<?php

namespace App\Livewire\Frontend\Tenancy;

use App\Enums\PricingBasis;
use App\Models\OccupantType;
use App\Models\UnitRate as UnitRateModel;
use App\Models\UnitType;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;

class AvaibilityList extends Component
{
    public $unitTypes;

    public
        $occupantTypeId,
        $occupantType,
        $pricingBasis,
        $startDate,
        $endDate,
        $totalDays;

    public function mount()
    {
        $request = request()->query('ed');

        try {
            $data = $request ? decrypt($request) : [];

        } catch (\Exception $e) {
            $data = [];
        }

        if ($data) {
            $this->occupantTypeId = $data['occupantType'] ?? null;
            $this->occupantType = OccupantType::find($this->occupantTypeId)->name ?? null;
            $this->pricingBasis = $data['pricingBasis'] ?? null;
            $this->startDate = $data['startDate'] ?? null;
            $this->endDate = $data['endDate'] ?? null;
            $this->totalDays = $data['totalDays'] ?? null;
            $this->queryFilters();
        }
    }

    #[On('filtersApplied')]
    public function FiltersApplied($filters)
    {
        $this->occupantTypeId = $filters['occupantType'] ?? null;

        if (!empty($this->occupantTypeId)) {
            $this->occupantType = OccupantType::where('id', $filters['occupantType'])->first()->name;
        }

        $this->pricingBasis = $filters['pricingBasis'] ?? null;
        $this->startDate = $filters['startDate'] ?? null;
        $this->endDate = $filters['endDate'] ?? null;
        $this->totalDays = $filters['totalDays'] ?? null;

        $this->queryFilters();
    }

    public function render()
    {
        return view('livewire.frontend.tenancy.avaibility-list');
    }

    public function queryFilters()
    {
        $occupantTypeId = $this->occupantTypeId;
        $pricingBasis = $this->pricingBasis;
        $accessibleClusterIds = [];

        if ($occupantTypeId) {
            $occupantType = OccupantType::find($occupantTypeId);
            if ($occupantType) {
                $accessibleClusterIds = $occupantType->accessibleClusters()->pluck('id')->toArray();
            }
        }

        $this->unitTypes = UnitType::query()

            ->when($occupantTypeId, function ($query) use ($occupantTypeId, $pricingBasis, $accessibleClusterIds) {

                $query->whereHas('units', function ($unitQuery) use ($accessibleClusterIds) {
                    $unitQuery->where('status', 'available');

                    if (!empty($accessibleClusterIds)) {
                        $unitQuery->whereIn('unit_cluster_id', $accessibleClusterIds);
                    } else {
                        $unitQuery->whereRaw('1 = 0');
                    }
                })
                ->whereHas('unitPrices', function ($priceQuery) use ($occupantTypeId, $pricingBasis) {
                    $priceQuery->where('occupant_type_id', $occupantTypeId)
                            ->where('pricing_basis', $pricingBasis);
                });

            })
            ->withCount(['units as available_units_count' => function ($unitQuery) use ($occupantTypeId) {
                $unitQuery->availableWithFilters(['occupantTypeId' => $occupantTypeId]);
            }])
            ->with([
                'attachments',
                'unitPrices' => function ($priceQuery) use ($occupantTypeId, $pricingBasis) {
                    $priceQuery->where('occupant_type_id', $occupantTypeId)
                            ->where('pricing_basis', $pricingBasis);
                }
            ])
            ->get();

        LivewireAlert::title('Pencarian Berhasil')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function redirectDetailUnit($unitTypeId)
    {
        $unitType = UnitType::find($unitTypeId);

        if (!$unitType) {
            LivewireAlert::title('Unit Type Tidak Ditemukan')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $queryParams = [
            'occupantType' => $this->occupantTypeId,
            'pricingBasis' => $this->pricingBasis,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalDays' => $this->totalDays,
        ];

        return $this->redirect(route('frontend.tenancy.unit.detail', [
            'type' => $unitType,
            'ed' => encrypt($queryParams),
        ]), navigate: true);
    }
}
