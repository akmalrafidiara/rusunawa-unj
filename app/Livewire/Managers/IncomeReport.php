<?php

namespace App\Livewire\Managers;

use Livewire\Component;
use App\Models\Invoice; // Assuming invoices are the source of income
use Carbon\Carbon;
use Livewire\WithPagination;

class IncomeReport extends Component
{
    use WithPagination;

    // Filters
    public $filterType = 'all_time'; // all_time, daily, monthly, yearly, custom
    public $startDate = null;
    public $endDate = null;
    public $occupantFilter = '';
    public $contractFilter = '';

    // Data for summary cards
    public $totalRevenue = 0;
    public $averageDailyRevenue = 0;
    public $averageMonthlyRevenue = 0;
    public $averagePerInvoice = 0;

    // Data for chart
    public $chartLabels = [];
    public $chartData = [];

    // Data for the recent transactions table
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    // Options for dropdowns
    public $occupantOptions = []; // Will be populated from Occupant model
    public $contractOptions = []; // Will be populated from Contract model

    protected $queryString = [
        'filterType' => ['except' => 'all_time'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'occupantFilter' => ['except' => ''],
        'contractFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Populate options for filters if needed (e.g., from database)
        // For simplicity, we'll leave them empty for now or mock them.
        // In a real application, you'd fetch them from your models.
        $this->occupantOptions = \App\Models\Occupant::select('id', 'full_name')->get()->map(fn($o) => ['value' => $o->id, 'label' => $o->full_name])->toArray();
        $this->contractOptions = \App\Models\Contract::select('id', 'contract_code')->get()->map(fn($c) => ['value' => $c->id, 'label' => $c->contract_code])->toArray();

        $this->applyFilters();
    }

    public function updated($propertyName)
    {
        // Automatically re-apply filters when filter properties change
        if (in_array($propertyName, ['filterType', 'startDate', 'endDate', 'occupantFilter', 'contractFilter'])) {
            $this->resetPage(); // Reset pagination when filters change
            $this->applyFilters();
        }
    }

    public function applyFilters()
    {
        $this->generateReportData();
        $this->generateChartData();
    }

    private function generateReportData()
    {
        $query = Invoice::where('status', \App\Enums\InvoiceStatus::PAID);

        // Apply date filters based on filterType
        if ($this->filterType === 'daily') {
            $query->whereDate('paid_at', Carbon::today());
        } elseif ($this->filterType === 'monthly') {
            $query->whereMonth('paid_at', Carbon::today()->month)
                  ->whereYear('paid_at', Carbon::today()->year);
        } elseif ($this->filterType === 'yearly') {
            $query->whereYear('paid_at', Carbon::today()->year);
        } elseif ($this->filterType === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('paid_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()]);
        }

        // Apply other filters
        if ($this->occupantFilter) {
            $query->whereHas('contract.occupants', function ($q) {
                $q->where('occupants.id', $this->occupantFilter);
            });
        }

        if ($this->contractFilter) {
            $query->where('contract_id', $this->contractFilter);
        }

        $paidInvoices = $query->get();

        $this->totalRevenue = $paidInvoices->sum('amount');
        $this->averagePerInvoice = $paidInvoices->count() > 0 ? $paidInvoices->avg('amount') : 0;

        // Calculate average daily/monthly revenue based on the overall period of paid invoices
        if ($paidInvoices->isNotEmpty()) {
            $firstPaidDate = $paidInvoices->min('paid_at');
            $lastPaidDate = $paidInvoices->max('paid_at');

            $diffInDays = $firstPaidDate->diffInDays($lastPaidDate) + 1; // Add 1 to include both start and end day
            $diffInMonths = $firstPaidDate->diffInMonths($lastPaidDate) + 1; // Add 1 for simplicity

            $this->averageDailyRevenue = $diffInDays > 0 ? $this->totalRevenue / $diffInDays : $this->totalRevenue;
            $this->averageMonthlyRevenue = $diffInMonths > 0 ? $this->totalRevenue / $diffInMonths : $this->totalRevenue;
        } else {
            $this->averageDailyRevenue = 0;
            $this->averageMonthlyRevenue = 0;
        }
    }

    private function generateChartData()
    {
        $this->chartLabels = [];
        $this->chartData = [];

        $dataRangeStart = null;
        $dataRangeEnd = null;

        $paidQuery = Invoice::where('status', \App\Enums\InvoiceStatus::PAID);

        // Apply filters to determine data range for chart
        if ($this->filterType === 'daily') {
            $dataRangeStart = Carbon::today()->subDays(6); // Last 7 days
            $dataRangeEnd = Carbon::today();
        } elseif ($this->filterType === 'monthly') {
            $dataRangeStart = Carbon::today()->startOfYear(); // Current year, month by month
            $dataRangeEnd = Carbon::today();
        } elseif ($this->filterType === 'yearly') {
            $dataRangeStart = Carbon::today()->subYears(4)->startOfYear(); // Last 5 years
            $dataRangeEnd = Carbon::today();
        } elseif ($this->filterType === 'custom' && $this->startDate && $this->endDate) {
            $dataRangeStart = Carbon::parse($this->startDate);
            $dataRangeEnd = Carbon::parse($this->endDate);
        } else { // All Time or if custom dates are not set
            $firstInvoice = Invoice::orderBy('paid_at', 'asc')->first();
            $dataRangeStart = $firstInvoice ? Carbon::parse($firstInvoice->paid_at) : Carbon::now()->subYear();
            $dataRangeEnd = Carbon::now();
        }

        if (!$dataRangeStart || !$dataRangeEnd) {
            return; // No data range, no chart
        }

        $paidQuery->whereBetween('paid_at', [$dataRangeStart->startOfDay(), $dataRangeEnd->endOfDay()]);

        if ($this->occupantFilter) {
            $paidQuery->whereHas('contract.occupants', function ($q) {
                $q->where('occupants.id', $this->occupantFilter);
            });
        }

        if ($this->contractFilter) {
            $paidQuery->where('contract_id', $this->contractFilter);
        }

        $invoices = $paidQuery->get();

        $groupedData = collect();

        if ($this->filterType === 'daily' || ($this->filterType === 'custom' && $dataRangeStart->diffInDays($dataRangeEnd) < 30)) {
            // Group by day for daily view or short custom ranges
            $period = Carbon::parse($dataRangeStart)->toPeriod($dataRangeEnd);
            foreach ($period as $date) {
                $this->chartLabels[] = $date->format('d M'); // e.g., 01 Jan
                $sumForDay = $invoices->whereBetween('paid_at', [$date->startOfDay(), $date->endOfDay()])->sum('amount');
                $this->chartData[] = $sumForDay;
            }
        } elseif ($this->filterType === 'monthly' || ($this->filterType === 'custom' && $dataRangeStart->diffInMonths($dataRangeEnd) < 12)) {
            // Group by month for monthly view or medium custom ranges
            $period = Carbon::parse($dataRangeStart)->startOfMonth()->toPeriod($dataRangeEnd->endOfMonth(), '1 month');
            foreach ($period as $month) {
                $this->chartLabels[] = $month->translatedFormat('M Y'); // e.g., Jan 2025
                $sumForMonth = $invoices->whereBetween('paid_at', [$month->startOfMonth(), $month->endOfMonth()])->sum('amount');
                $this->chartData[] = $sumForMonth;
            }
        } else { // Group by year for yearly view or long custom ranges / all_time
            $period = Carbon::parse($dataRangeStart)->startOfYear()->toPeriod($dataRangeEnd->endOfYear(), '1 year');
            foreach ($period as $year) {
                $this->chartLabels[] = $year->format('Y'); // e.g., 2025
                $sumForYear = $invoices->whereBetween('paid_at', [$year->startOfYear(), $year->endOfYear()])->sum('amount');
                $this->chartData[] = $sumForYear;
            }
        }
        
        // Ensure that chartLabels and chartData are arrays
        $this->chartLabels = array_values($this->chartLabels);
        $this->chartData = array_values($this->chartData);

        // Dispatch a browser event to update the chart in the Blade view
        $this->dispatch('updateChart', labels: $this->chartLabels, data: $this->chartData);
    }

    public function render()
    {
        $recentInvoices = Invoice::where('status', \App\Enums\InvoiceStatus::PAID)
            ->when($this->occupantFilter, function ($query) {
                $query->whereHas('contract.occupants', function ($q) {
                    $q->where('occupants.id', $this->occupantFilter);
                });
            })
            ->when($this->contractFilter, function ($query) {
                $query->where('contract_id', $this->contractFilter);
            })
            ->with(['contract.occupants', 'contract.unit.unitCluster'])
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage);

        return view('livewire.managers.oprations.income-reports.index', [
            'recentInvoices' => $recentInvoices,
        ]);
    }
}
