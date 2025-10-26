<?php

namespace App\Livewire\Managers;

use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Enums\UnitStatus;
use App\Enums\ReportStatus;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\Unit;
use App\Models\Report;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceRecord;
use App\Models\Announcement;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Overview extends Component
{
    // Statistics properties
    public $totalUnits;
    public $occupiedUnits;
    public $availableUnits;
    public $maintenanceUnits;
    public $occupancyRate;

    public $totalOccupants;
    public $activeOccupants;
    public $pendingOccupants;

    public $totalContracts;
    public $activeContracts;
    public $expiredContracts;
    public $expiringContractsThisMonth;

    public $totalInvoices;
    public $paidInvoices;
    public $unpaidInvoices;
    public $overdueInvoices;
    public $totalRevenue;
    public $monthlyRevenue;

    public $pendingReports;
    public $resolvedReports;
    public $totalReports;

    public $upcomingMaintenance;
    public $completedMaintenanceThisMonth;

    public $recentAnnouncements;

    // Chart data
    public $monthlyRevenueChart;
    public $occupancyChart;
    public $contractStatusChart;

    public function mount()
    {
        $this->loadStatistics();
        $this->loadChartData();
        $this->refreshData();
    }

    public function loadStatistics()
    {
        // Unit Statistics
        $this->totalUnits = Unit::count();
        $this->occupiedUnits = Unit::where('status', UnitStatus::OCCUPIED)->count();
        $this->availableUnits = Unit::where('status', UnitStatus::AVAILABLE)->count();
        $this->maintenanceUnits = Unit::where('status', UnitStatus::UNDER_MAINTENANCE)->count();
        $this->occupancyRate = $this->totalUnits > 0 ?
            round(($this->occupiedUnits / $this->totalUnits) * 100, 1) : 0;

        // Occupant Statistics
        $this->totalOccupants = Occupant::count();
        $this->activeOccupants = Occupant::where('status', OccupantStatus::VERIFIED)->count();
        $this->pendingOccupants = Occupant::where('status', OccupantStatus::PENDING_VERIFICATION)->count();

        // Contract Statistics
        $this->totalContracts = Contract::count();
        $this->activeContracts = Contract::where('status', ContractStatus::ACTIVE)->count();
        $this->expiredContracts = Contract::where('status', ContractStatus::EXPIRED)->count();
        $this->expiringContractsThisMonth = Contract::where('status', ContractStatus::ACTIVE)
            ->whereMonth('end_date', Carbon::now()->month)
            ->whereYear('end_date', Carbon::now()->year)
            ->count();

        // Invoice Statistics
        $this->totalInvoices = Invoice::count();
        $this->paidInvoices = Invoice::where('status', InvoiceStatus::PAID)->count();
        $this->unpaidInvoices = Invoice::where('status', InvoiceStatus::UNPAID)->count();
        $this->overdueInvoices = Invoice::where('status', InvoiceStatus::UNPAID)
            ->where('due_at', '<', Carbon::now())
            ->count();

        $this->totalRevenue = Invoice::where('status', InvoiceStatus::PAID)->sum('amount');
        $this->monthlyRevenue = Invoice::where('status', InvoiceStatus::PAID)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');

        // Report Statistics
        $this->totalReports = Report::count();
        $this->pendingReports = Report::whereIn('status', [
            ReportStatus::REPORT_RECEIVED,
            ReportStatus::IN_PROCESS
        ])->count();
        $this->resolvedReports = Report::where('status', ReportStatus::CONFIRMED_COMPLETED)->count();

        // Maintenance Statistics
        $this->upcomingMaintenance = MaintenanceSchedule::where('next_due_date', '>=', Carbon::now())
            ->where('next_due_date', '<=', Carbon::now()->addDays(7))
            ->count();

        $this->completedMaintenanceThisMonth = MaintenanceRecord::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Recent Announcements
        $this->recentAnnouncements = Announcement::latest()
            ->take(5)
            ->get() ?? collect();
    }

    public function loadChartData()
    {
        // Monthly Revenue Chart (Last 6 months)
        $this->monthlyRevenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Invoice::where('status', InvoiceStatus::PAID)
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount') ?? 0;

            $this->monthlyRevenueChart[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Occupancy Chart (Last 12 months)
        $this->occupancyChart = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $totalUnits = Unit::whereDate('created_at', '<=', $date->endOfMonth())->count();
            $occupiedUnits = Contract::where('status', ContractStatus::ACTIVE)
                ->whereDate('start_date', '<=', $date->endOfMonth())
                ->whereDate('end_date', '>=', $date->startOfMonth())
                ->count();

            $rate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

            $this->occupancyChart[] = [
                'month' => $date->format('M Y'),
                'rate' => $rate
            ];
        }

        // Contract Status Chart
        $this->contractStatusChart = [
            ['status' => 'Active', 'count' => $this->activeContracts ?? 0],
            ['status' => 'Expired', 'count' => $this->expiredContracts ?? 0],
            ['status' => 'Pending Payment', 'count' => Contract::where('status', ContractStatus::PENDING_PAYMENT)->count() ?? 0],
        ];
    }

    public function refreshData()
    {
        $this->loadStatistics();
        $this->loadChartData();

        $this->dispatch('refresh-charts');

        session()->flash('message', 'Data refreshed successfully!');
    }

    public function render()
    {
        $this->refreshData();
        return view('livewire.managers.overview.index');
    }
}
