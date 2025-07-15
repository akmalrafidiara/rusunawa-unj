<?php

namespace App\Livewire\Frontend\Tenancy;

use App\Enums\PricingBasis;
use Livewire\Component;
use App\Models\UnitType;
use App\Models\OccupantType;

class UnitDetail extends Component
{
    public
        $unitType,
        $totalUnits,
        $price,
        $occupantType,
        $pricingBasis,
        $startDate,
        $endDate,
        $totalDays,
        $totalPrice;

    public $encryptedData;

    public function mount()
    {
        $unitTypeId = request()->query('type');
        $this->encryptedData = request()->query('ed');

        try {
            $data = $this->encryptedData ? decrypt($this->encryptedData) : [];
        } catch (\Exception $e) {
            $data = [];
        }

        $occupantTypeId = $data['occupantType'] ?? null;
        $pricingBasis = $data['pricingBasis'] ?? null;

        $this->unitType = UnitType::query()
            ->where('id', $unitTypeId)
            ->whereHas('unitPrices', function ($query) use ($occupantTypeId, $pricingBasis) {
                $query->where('occupant_type_id', $occupantTypeId)->where('pricing_basis', $pricingBasis);
            })
            ->withCount([
                'units as available_units_count' => function ($unitQuery) use ($occupantTypeId) {
                    $unitQuery->availableWithFilters(['occupantTypeId' => $occupantTypeId]);
                },
            ])
            ->with([
                'attachments',
                'unitPrices' => function ($query) use ($occupantTypeId, $pricingBasis) {
                    $query->where('occupant_type_id', $occupantTypeId)->where('pricing_basis', $pricingBasis);
                },
            ])
            ->first();

        $this->price = $this->unitType->unitPrices->first()->price ?? null;

        $this->occupantType = OccupantType::find($occupantTypeId);
        $this->pricingBasis = $this->unitType->unitPrices->first()->pricing_basis ?? null;
        $this->startDate = $data['startDate'] ?? null;
        $this->endDate = $data['endDate'] ?? null;
        $this->totalUnits = $this->unitType->available_units_count;
        $this->calculateTotalDays();
        $this->totalPrice = $this->pricingBasis === PricingBasis::PER_NIGHT ? $this->price * $this->totalDays : $this->price;
    }

    public function redirectToForm()
    {
        session()->forget('tenancy_data');
        session([
            'tenancy_data' => [
                'occupantType' => $this->occupantType->id ?? null,
                'pricingBasis' => $this->pricingBasis ?? null,
                'startDate' => $this->startDate ?? null,
                'endDate' => $this->endDate ?? null,
                'unitType' => $this->unitType->id ?? null,
                'price' => $this->price ?? null,
                'totalDays' => $this->totalDays ?? null,
                'totalPrice' => $this->totalPrice ?? null,
                'filterUrl' => route('tenancy.index', ['ed' => $this->encryptedData]),
                'detailUrl' => route('frontend.tenancy.unit.detail', [
                    'type' => $this->unitType->id,
                    'ed' => $this->encryptedData,
                ]),
            ],
        ]);

        return $this->redirect(route('frontend.tenancy.form'), navigate: true);
    }

    private function calculateTotalDays()
    {
        if ($this->startDate && $this->endDate) {
            $start = \Carbon\Carbon::parse($this->startDate);
            $end = \Carbon\Carbon::parse($this->endDate);
            $this->totalDays = $start->diffInDays($end);
        } else {
            $this->totalDays = null;
        }
    }

    public function render()
    {
        return view('livewire.frontend.tenancy.unit-detail');
    }
}
