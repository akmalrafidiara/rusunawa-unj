<?php

namespace App\Livewire\Managers;

use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Exports\IncomeReportExport;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Occupant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class IncomeReport extends Component
{
    use WithPagination;

    // Properti Filter
    public $filterType = 'monthly';
    public $startDate;
    public $endDate;
    public $occupantFilter = '';
    public $contractFilter = '';

    // Properti Metrik & Ringkasan
    public $totalRevenue = 0;
    public $averageMonthlyRevenue = 0;
    public $averageDailyRevenue = 0;

    // Properti Grafik
    public ?array $chartData = null;
    public $chartKey = '';

    // Properti Tabel
    public $perPage = 10;
    public $occupants;
    public $contracts;
    public array $occupantOptions = [];
    public array $contractOptions = [];

    // Properti Modal
    public $showModal = false;
    public $selectedInvoice = null;

    /**
     * Inisialisasi komponen.
     */
    public function mount(): void
    {
        $this->occupants = Occupant::where('status', OccupantStatus::VERIFIED)->with('contracts')->get();
        $this->contracts = Contract::where('status', ContractStatus::ACTIVE)->with('unit', 'occupants')->get();
        $this->loadFilterOptions();
        $this->setDefaultDates();
        $this->prepareChartData();
    }

    /**
     * Merender view utama dan mempersiapkan data.
     */
    public function render()
    {
        $this->calculateMetrics();
        $this->prepareChartData();

        $invoicesQuery = $this->getInvoicesQuery();

        return view('livewire.managers.oprations.income-reports.index', [
            'recentInvoices' => $invoicesQuery->paginate($this->perPage),
        ]);
    }

    /**
     * Query dasar untuk mengambil invoice berdasarkan filter.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getInvoicesQuery()
    {
        $query = Invoice::query()
            ->with(['contract.occupants', 'contract.unit', 'contract.occupantType'])
            ->where('status', InvoiceStatus::PAID);

        // Filter berdasarkan tanggal
        if ($this->filterType !== 'all_time' && $this->startDate && $this->endDate) {
            $query->whereBetween('paid_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        // Filter berdasarkan penghuni
        if ($this->occupantFilter) {
            $query->whereHas('contract', function ($q) {
                $q->whereHas('occupants', function ($q2) {
                    $q2->where('occupants.id', $this->occupantFilter);
                });
            });
        }

        // Filter berdasarkan kontrak
        if ($this->contractFilter) {
            $query->where('contract_id', $this->contractFilter);
        }

        return $query->latest('paid_at');
    }

    /**
     * Menghitung semua metrik yang dibutuhkan oleh UI.
     */
    public function calculateMetrics(): void
    {
        $invoices = $this->getInvoicesQuery()->get();

        if ($invoices->isEmpty()) {
            $this->resetMetrics();
            return;
        }

        $this->totalRevenue = $invoices->sum('amount');

        $firstDate = null;
        $lastDate = null;

        if ($this->filterType !== 'all_time' && $this->startDate && $this->endDate) {
            $firstDate = Carbon::parse($this->startDate);
            // $lastDate = now()->min(Carbon::parse($this->endDate));
            $lastDate = Carbon::parse($this->endDate);
        } else {
            $firstDate = Carbon::parse($invoices->min('paid_at'));
            $lastDate = Carbon::parse($invoices->max('paid_at'));
        }

        $daysDifference = $firstDate->diffInDays($lastDate) + 1;
        $monthsDifference = $firstDate->diffInMonths($lastDate, true);

        $this->averageDailyRevenue = $daysDifference > 0 ? $this->totalRevenue / $daysDifference : 0;

        if ($monthsDifference < 1) {
            $this->averageMonthlyRevenue = $this->averageDailyRevenue * $firstDate->daysInMonth;
        } else {
            $this->averageMonthlyRevenue = $this->totalRevenue / $monthsDifference;
        }
    }

    /**
     * Menyiapkan data untuk Chart.js.
     */
    public function prepareChartData(): void
    {
        $invoices = $this->getInvoicesQuery()->get();

        if ($invoices->isEmpty()) {
            $this->chartData = [
                'labels' => [],
                'datasets' => [[
                    'label' => 'Pendapatan',
                    'data' => [],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ]],
            ];
        } else {
            // Determine grouping format based on filter type
            $groupBy = match($this->filterType) {
                'yearly' => 'M Y',
                'monthly' => 'W \W\e\e\k',
                'daily' => 'd M',
                default => 'M Y'
            };

            $data = $invoices->groupBy(function($invoice) use ($groupBy) {
                return Carbon::parse($invoice->paid_at)->format($groupBy);
            })->map(function($group) {
                return $group->sum('amount');
            })->sortKeys();

            $this->chartData = [
                'labels' => $data->keys()->toArray(),
                'datasets' => [[
                    'label' => 'Pendapatan',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ]],
            ];
        }

        // Update chart key to force re-render
        $this->chartKey = uniqid();

    // Dispatch a Livewire browser event (legacy helper used in this project)
    // so frontend listeners receive the data. This uses the project's
    // Livewire/Flux helper which expects `dispatch`.
    $this->dispatch('updateChart', $this->chartData);
    }

    /**
     * Reset semua filter ke nilai default.
     */
    public function resetFilters(): void
    {
        $this->reset('filterType', 'startDate', 'endDate', 'occupantFilter', 'contractFilter');
        $this->setDefaultDates();
        $this->resetPage();
    }

    /**
     * Atur ulang tanggal saat tipe filter berubah.
     */
    public function updatedFilterType(): void
    {
        $this->setDefaultDates();
        $this->resetPage();
        $this->prepareChartData();
    }

    /**
     * Hook yang dijalankan setiap kali ada properti yang diupdate.
     * Digunakan untuk mereset paginasi saat filter berubah.
     */
    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['startDate', 'endDate', 'occupantFilter', 'contractFilter', 'perPage'])) {
            $this->resetPage();
        }

        // Update chart when filter properties change
        if (in_array($propertyName, ['filterType', 'startDate', 'endDate', 'occupantFilter', 'contractFilter'])) {
            $this->prepareChartData();
        }
    }

    /**
     * Atur tanggal default berdasarkan tipe filter.
     */
    private function setDefaultDates(): void
    {
        $now = Carbon::now();
        switch ($this->filterType) {
            case 'daily':
                $this->startDate = $now->startOfDay()->toDateString();
                $this->endDate = $now->endOfDay()->toDateString();
                break;
            case 'monthly':
                $this->startDate = $now->startOfMonth()->toDateString();
                $this->endDate = $now->endOfMonth()->toDateString();
                break;
            case 'yearly':
                $this->startDate = $now->startOfYear()->toDateString();
                $this->endDate = $now->endOfYear()->toDateString();
                break;
            default: // all_time
                $this->startDate = Invoice::min('paid_at') ? Carbon::parse(Invoice::min('paid_at'))->toDateString() : $now->toDateString();
                $this->endDate = $now->toDateString();
                break;
        }
    }

    /**
     * Muat data untuk dropdown filter.
     */
    private function loadFilterOptions(): void
    {
        $this->occupantOptions = Occupant::where('status', OccupantStatus::VERIFIED)
            ->orderBy('full_name')
            ->get()
            ->map(fn($o) => ['value' => $o->id, 'label' => $o->full_name])
            ->toArray();

        $this->contractOptions = Contract::where('status', ContractStatus::ACTIVE)
            ->orderBy('contract_code')
            ->get()
            ->map(fn($c) => ['value' => $c->id, 'label' => $c->contract_code])
            ->toArray();
    }

    /**
     * Reset metrik jika tidak ada data.
     */
    private function resetMetrics(): void
    {
        $this->totalRevenue = 0;
        $this->averageDailyRevenue = 0;
        $this->averageMonthlyRevenue = 0;
    }

    /**
     * Ekspor data ke PDF.
     */
    public function exportPdf()
    {
        $invoices = $this->getInvoicesQuery()->get();
        $pdf = Pdf::loadView('exports.income-report-pdf', [
            'invoices' => $invoices,
            'totalIncome' => $invoices->sum('amount')
        ]);
        return response()->streamDownload(fn() => print($pdf->output()), 'laporan-pendapatan-'.now()->format('d-m-Y').'.pdf');
    }

    /**
     * Ekspor data ke Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new IncomeReportExport($this->getInvoicesQuery()->get()), 'laporan-pendapatan-'.now()->format('d-m-Y').'.xlsx');
    }

    /**
     * Melihat detail tagihan.
     */
    public function viewInvoiceDetails($invoiceId)
    {
        $this->selectedInvoice = Invoice::with(['contract.occupants', 'contract.unit', 'contract.occupantType'])
            ->findOrFail($invoiceId);
        $this->showModal = true;
    }

    /**
     * Menutup modal detail tagihan.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedInvoice = null;
    }
}
