<?php

namespace App\Livewire\Managers\ReportsAndComplaints;

use App\Enums\ReportStatus;
use App\Enums\RoleUser;
use App\Models\Report;
use App\Models\UnitCluster;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReportList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $tab = 'aktif';
    // Hapus clusterFilter karena tidak lagi digunakan di UI
    // public $clusterFilter = '';

    public $statusOptions = [];
    public $clusterOptions = []; // Tetap ada untuk penggunaan internal jika diperlukan

    public $is_admin_user;
    public $is_head_of_rusunawa_user;
    public $is_staff_of_rusunawa_user;
    public $user_cluster_ids = [];

    public $selectedReportId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        // 'clusterFilter' => ['except' => ''], // Hapus dari query string
        'page' => ['except' => 1],
        'selectedReportId' => ['except' => null],
    ];

    protected $listeners = ['refreshReports' => '$refresh'];

    /**
     * FUNGSI BARU: Lifecycle hook yang berjalan saat properti diperbarui.
     * Fungsinya untuk membersihkan detail laporan jika hasil filter/pencarian kosong.
     */
    public function updated($propertyName)
    {
        // Periksa hanya jika properti search atau statusFilter yang berubah
        if (in_array($propertyName, ['search', 'statusFilter', 'tab'])) { // Tambahkan 'tab'
            $this->resetPage(); // Reset halaman ke 1 setiap kali ada filter/pencarian baru

            // Hitung jumlah laporan berdasarkan kriteria baru
            $reportCount = $this->buildReportQuery()->count();

            // Jika tidak ada laporan ditemukan, kosongkan pilihan
            if ($reportCount === 0) {
                $this->selectedReportId = null;
                $this->dispatch('reportSelected', reportId: null);
            }
        }
    }


    public function mount()
    {
        // Opsi filter status sekarang dimodifikasi untuk mencerminkan alur baru
        $this->statusOptions = [
            ['value' => ReportStatus::DISPOSED_TO_ADMIN->value, 'label' => 'Disposisi Admin'],
            ['value' => ReportStatus::DISPOSED_TO_RUSUNAWA->value, 'label' => 'Dikembalikan ke Rusunawa'],
            // Tambahkan status lain yang relevan jika perlu
            ['value' => ReportStatus::IN_PROCESS->value, 'label' => 'Sedang Diproses'],
            ['value' => ReportStatus::COMPLETED->value, 'label' => 'Selesai'],
            ['value' => ReportStatus::CONFIRMED_COMPLETED->value, 'label' => 'Dikonfirmasi Selesai'],
        ];

        $this->clusterOptions = UnitCluster::all()->map(fn($cluster) => ['value' => $cluster->id, 'label' => $cluster->name])->toArray();

        $this->is_admin_user = Auth::user()->hasRole(RoleUser::ADMIN->value);
        $this->is_head_of_rusunawa_user = Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value);
        $this->is_staff_of_rusunawa_user = Auth::user()->hasRole(RoleUser::STAFF_OF_RUSUNAWA->value);

        // Hanya ambil cluster IDs jika pengguna adalah staf rusunawa
        if ($this->is_staff_of_rusunawa_user) {
            $this->user_cluster_ids = Auth::user()->unitClusters->pluck('id')->toArray();
        }
        // Kepala Rusunawa tidak perlu difilter berdasarkan cluster, jadi user_cluster_ids dibiarkan kosong
    }

    public function render()
    {
        $reports = $this->buildReportQuery()->paginate(10);

        // Jika laporan yang dipilih tidak lagi ada dalam daftar yang difilter, kosongkan pilihan
        if ($this->selectedReportId && !$reports->contains('id', $this->selectedReportId)) {
             $this->selectedReportId = null;
             $this->dispatch('reportSelected', reportId: null);
        }

        return view('livewire.managers.oprations.reports-and-complaints.report-list.index', compact('reports'));
    }

    private function buildReportQuery()
    {
        return Report::query()
            ->with(['reporter', 'contract.unit.unitCluster', 'currentHandler'])
            ->when($this->tab === 'aktif', function ($query) {
                $query->whereIn('status', [
                    ReportStatus::REPORT_RECEIVED,
                    ReportStatus::IN_PROCESS,
                    ReportStatus::DISPOSED_TO_RUSUNAWA,
                    ReportStatus::DISPOSED_TO_ADMIN,
                ]);
            })
            ->when($this->tab === 'selesai', function ($query) {
                $query->whereIn('status', [
                    ReportStatus::COMPLETED,
                    ReportStatus::CONFIRMED_COMPLETED,
                ]);
            })
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('unique_id', 'like', $searchTerm) // Cari berdasarkan ID Laporan
                      ->orWhere('subject', 'like', $searchTerm) // Cari berdasarkan Subjek
                      ->orWhereHas('contract.unit', function ($unitQuery) use ($searchTerm) {
                          $unitQuery->where('room_number', 'like', $searchTerm) // Cari berdasarkan Unit Kamar
                                    ->orWhere('room_number', 'like', '%' . preg_replace('/^(kamar\s*)/i', '', $this->search) . '%'); // Cari dengan kata "kamar" di depan
                      });
                });
            })
            // --- END MODIFIKASI PENCARIAN ---
            ->when($this->statusFilter, function ($query) {
                // Logika filter berdasarkan status yang ditampilkan, bukan status mentah
                $query->where(function($q) {
                    if ($this->statusFilter === ReportStatus::DISPOSED_TO_ADMIN->value) {
                         $q->where('status', ReportStatus::DISPOSED_TO_ADMIN)
                           ->orWhere(function($sub) {
                               $sub->where('status', ReportStatus::IN_PROCESS)
                                   ->whereHas('currentHandler', fn($h) => $h->whereHas('roles', fn($r) => $r->where('name', RoleUser::ADMIN->value)));
                           });
                    } elseif ($this->statusFilter === ReportStatus::DISPOSED_TO_RUSUNAWA->value) {
                        $q->where('status', ReportStatus::DISPOSED_TO_RUSUNAWA)
                          ->orWhere(function($sub) {
                               $sub->where('status', ReportStatus::IN_PROCESS)
                                   ->whereHas('currentHandler', fn($h) => $h->whereHas('roles', fn($r) => $r->where('name', '!=', RoleUser::ADMIN->value)));
                           });
                    } else {
                        $q->where('status', $this->statusFilter);
                    }
                });
            })
            // Logika otorisasi berdasarkan role
            ->when($this->is_admin_user, function ($query) {
                // Admin hanya bisa melihat laporan yang didisposisikan kepadanya
                 $query->where(function($q) {
                    $q->where('status', ReportStatus::DISPOSED_TO_ADMIN)
                      ->orWhereHas('logs', fn($log) => $log->where('new_status', ReportStatus::DISPOSED_TO_ADMIN->value));
                 });
            })
            // NEW: If staff of rusunawa and no clusters assigned, return no reports
            // This condition explicitly applies only to staff, not head of rusunawa
            ->when($this->is_staff_of_rusunawa_user && empty($this->user_cluster_ids), function ($query) {
                $query->whereRaw('0 = 1'); // This will ensure no results are returned
            })
            // Existing: Filter for staff based on assigned unit clusters
            // This condition explicitly applies only to staff, not head of rusunawa
            ->when($this->is_staff_of_rusunawa_user && !empty($this->user_cluster_ids), function ($query) {
                $query->whereHas('contract.unit.unitCluster', fn($q) => $q->whereIn('unit_clusters.id', $this->user_cluster_ids));
            })
            // Kepala Rusunawa tidak memiliki filter cluster, jadi tidak ada 'when' clause tambahan untuk mereka di sini.
            ->orderBy('created_at', 'desc');
    }


    public function selectReport($reportId)
    {
        $this->selectedReportId = $reportId;
        $this->dispatch('reportSelected', reportId: $reportId);
    }

    /**
     * FUNGSI BARU: Menentukan status yang harus ditampilkan di UI
     * berdasarkan role pengguna dan riwayat laporan.
     */
    public function getReportDisplayStatus(Report $report): ReportStatus
    {
        // Jika user adalah Admin
        if ($this->is_admin_user) {
            // Jika status asli adalah 'dikembalikan_ke_rusunawa', tampilkan itu.
            if ($report->status === ReportStatus::DISPOSED_TO_RUSUNAWA) {
                return ReportStatus::DISPOSED_TO_RUSUNAWA;
            }
            // Jika status asli 'diproses' dan ditangani oleh non-admin, tampilkan 'dikembalikan_ke_rusunawa'
            if ($report->status === ReportStatus::IN_PROCESS && $report->currentHandler && !$report->currentHandler->hasRole(RoleUser::ADMIN->value)) {
                return ReportStatus::DISPOSED_TO_RUSUNAWA;
            }
        }

        // Jika user adalah Kepala atau Staff Rusunawa
        if ($this->is_head_of_rusunawa_user || $this->is_staff_of_rusunawa_user) {
            // Jika status asli adalah 'disposisi_ke_admin', tampilkan itu
            if ($report->status === ReportStatus::DISPOSED_TO_ADMIN) {
                return ReportStatus::DISPOSED_TO_ADMIN;
            }
            // Jika status asli 'diproses' dan ditangani oleh admin, tampilkan 'disposisi_ke_admin'
            if ($report->status === ReportStatus::IN_PROCESS && $report->currentHandler && $report->currentHandler->hasRole(RoleUser::ADMIN->value)) {
                return ReportStatus::DISPOSED_TO_ADMIN;
            }
        }

        // Untuk semua kasus lain, kembalikan status asli dari laporan
        return $report->status;
    }
}