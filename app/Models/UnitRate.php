<?php

namespace App\Models;

use App\Enums\PricingBasis;
use Illuminate\Database\Eloquent\Model;

class UnitRate extends Model
{
    protected $table = 'rates';

    protected $fillable = [
        'price',
        'occupant_type',
        'pricing_basis',
        'requires_verification',
    ];

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_rate', 'rate_id', 'unit_id');
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    protected $casts = [
        'pricing_basis' => PricingBasis::class,
        'requires_verification' => 'boolean',
    ];
}
