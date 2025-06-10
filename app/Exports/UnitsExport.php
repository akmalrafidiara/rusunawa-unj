<?php

namespace App\Exports;

use App\Enums\GenderAllowed;
use App\Enums\UnitStatus;
use App\Models\Unit as UnitModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class UnitsExport implements FromCollection
{
    protected $search;
    protected $genderAllowedFilter;
    protected $statusFilter;
    protected $unitTypeFilter;
    protected $unitClusterFilter;
    protected $orderBy;
    protected $sort;

    public function __construct(
        $search = null,
        $genderAllowedFilter = null,
        $statusFilter = null,
        $unitTypeFilter = null,
        $unitClusterFilter = null,
        $orderBy = 'id',
        $sort = 'asc'
    ) {
        $this->search = $search;
        $this->genderAllowedFilter = $genderAllowedFilter;
        $this->statusFilter = $statusFilter;
        $this->unitTypeFilter = $unitTypeFilter;
        $this->unitClusterFilter = $unitClusterFilter;
        $this->orderBy = $orderBy;
        $this->sort = $sort;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $units = UnitModel::query()
            ->when($this->search, fn($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->when($this->genderAllowedFilter, fn($q) => $q->where("gender_allowed", $this->genderAllowedFilter))
            ->when($this->statusFilter, fn($q) => $q->where("status", $this->statusFilter))
            ->when($this->unitTypeFilter, fn($q) => $q->where("unit_type_id", $this->unitTypeFilter))
            ->when($this->unitClusterFilter, fn($q) => $q->where("unit_cluster_id", $this->unitClusterFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->with(['unitType', 'unitCluster'])
            ->get();

        // Map enums and relations
        $units = $units->map(function ($unit) {
            return [
                'room_number' => $unit->room_number,
                'capacity' => $unit->capacity,
                'virtual_account_number' => (string) $unit->virtual_account_number,
                'gender_allowed' => GenderAllowed::from($unit->gender_allowed)->label(),
                'status' => UnitStatus::from($unit->status)->label(),
                'unit_type_id' => $unit->unitType ? $unit->unitType->name : '',
                'unit_cluster_id' => $unit->unitCluster ? $unit->unitCluster->name : '',
            ];
        });

        // Add table header as the first row
        $header = collect([[
            'room_number' => 'No Kamar',
            'capacity' => 'Kapasitas',
            'virtual_account_number' => 'No VA',
            'gender_allowed' => 'Peruntukan',
            'status' => 'Status',
            'unit_type_id' => 'Unit Type',
            'unit_cluster_id' => 'Unit Cluster'
        ]]);

        return $header->concat($units);
    }
}
