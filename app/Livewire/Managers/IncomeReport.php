<?php

namespace App\Livewire\Managers;

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

    // Properti Tabel
    public $perPage = 10;
    public array $occupantOptions = [];
    public array $contractOptions = [];

    /**
     * Inisialisasi komponen.
     */
    public function mount(): void
    {
        $this->setDefaultDates();
        $this->loadFilterOptions();
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
        return Invoice::query()
            ->where('status', 'paid')
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween('paid_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);
            })
            ->when($this->occupantFilter, fn($q) => $q->whereHas('contract.occupants', fn($q2) => $q2->where('occupants.id', $this->occupantFilter)))
            ->when($this->contractFilter, fn($q) => $q->where('contract_id', $this->contractFilter))
            ->latest('paid_at');
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

        $firstDate = Carbon::parse($invoices->min('paid_at'));
        $lastDate = Carbon::parse($invoices->max('paid_at'));

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
        $data = collect([
            'labels' => [],
            'datasets' => [['data' => []]],
        ]);

        if ($invoices->isNotEmpty()) {
            $data = $invoices->groupBy(fn($inv) => Carbon::parse($inv->paid_at)->format('d M'))
                             ->map(fn($group) => $group->sum('amount'));
        }

        $this->chartData = [
            'labels' => $data->keys()->toArray(),
            'datasets' => [['data' => $data->values()->toArray()]],
        ];

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
        $this->occupantOptions = Occupant::orderBy('full_name')->get()->map(fn($o) => ['value' => $o->id, 'label' => $o->full_name])->toArray();
        $this->contractOptions = Contract::orderBy('contract_code')->get()->map(fn($c) => ['value' => $c->id, 'label' => $c->contract_code])->toArray();
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
}