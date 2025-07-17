<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_code',
        'unit_id',
        'occupant_id',
        'occupant_type_id',
        'start_date',
        'end_date',
        'pricing_basis',
        'total_price',
        'expired_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expired_date' => 'datetime',
        'status' => ContractStatus::class,
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function occupants()
    {
        return $this->belongsToMany(Occupant::class, 'contract_occupant');
    }

    public function pic()
    {
        return $this->belongsToMany(Occupant::class, 'contract_occupant')
                ->wherePivot('is_pic', true)
                ->withTimestamps();
    }

    public function occupantType()
    {
        return $this->belongsTo(OccupantType::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public static function generateContractCode($unitCluster, $occupantType, $pricingBasis): string
    {
        $clusterChar = substr($unitCluster->name, -1);
        $typeChar = substr($occupantType->name, 0, 1);
        $parts = explode('_', $pricingBasis);
        $basisChar = isset($parts[1]) ? substr($parts[1], 0, 1) : 'X';
        $prefix = strtoupper($clusterChar . $typeChar . $basisChar);
        do {
            $randomPart = strtoupper(Str::random(5));
            $generatedCode = $prefix . $randomPart;
        } while (self::where('contract_code', $generatedCode)->exists());

        return $generatedCode;
    }
}
