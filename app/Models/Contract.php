<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Enums\KeyStatus;
use App\Enums\PricingBasis;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Contract extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait;

    protected $fillable = [
        'contract_code',
        'contract_pic',
        'unit_id',
        'occupant_type_id',
        'start_date',
        'end_date',
        'pricing_basis',
        'total_price',
        'expired_date',
        'key_status',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'expired_date' => 'datetime',
        'pricing_basis' => PricingBasis::class,
        'key_status' => KeyStatus::class,
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
        return $this->belongsTo(Occupant::class, 'contract_pic');
    }

    public function occupantType()
    {
        return $this->belongsTo(OccupantType::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
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

    public static function generateAutoContractCode()
    {
        $prefix = strtoupper(Carbon::now()->format('D'));
        do {
            $randomPart = strtoupper(Str::random(5));
            $generatedCode = $prefix . $randomPart;
        } while (self::where('contract_code', $generatedCode)->exists());

        return $generatedCode;
    }
}
