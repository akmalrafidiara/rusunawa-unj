<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitRate extends Model
{
    protected $table = 'rates';

    protected $fillable = [
        'price',
        'occupant_type',
        'pricing_bases'
    ];

    public function setPricingBasesAttribute($value)
    {
        $allowed = ['per_night', 'per_month'];
        if (!in_array($value, $allowed)) {
            throw new \InvalidArgumentException("Invalid pricing_bases value.");
        }
        $this->attributes['pricing_bases'] = $value;
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_rate', 'unit_rate_id', 'unit_id');
    }
}