<?php

namespace App\Models;

use App\Enums\PricingBasis;
use Illuminate\Database\Eloquent\Model;

class UnitPrice extends Model
{
    protected $fillable = [
        'unit_type_id',
        'occupant_type_id',
        'pricing_basis',
        'price',
        'max_price',
        'notes',
    ];

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function occupantType()
    {
        return $this->belongsTo(OccupantType::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    protected $casts = [
        'pricing_basis' => PricingBasis::class,
    ];
}
