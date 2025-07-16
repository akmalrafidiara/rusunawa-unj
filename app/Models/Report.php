<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Enums\ReportStatus;
use App\Enums\ReporterType;
use Carbon\Carbon;

// Pastikan model yang dibutuhkan diimpor
use App\Models\Contract;
use App\Models\Unit;
use App\Models\UnitCluster;


class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'contract_id',
        'reporter_type',
        'reporter_id',
        'subject',
        'description',
        'status',
        'current_handler_id',
        'completion_deadline',
    ];

    protected $casts = [
        'status' => ReportStatus::class,
        'reporter_type' => ReporterType::class,
        'completion_deadline' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            // Memuat kontrak beserta unit dan unitCluster-nya secara eksplisit
            // untuk memastikan data unit_number dan cluster tersedia saat membuat unique_id.
            $contract = Contract::with('unit.unitCluster')->find($report->contract_id);

            $unitCodePart = 'XXXX'; // Default jika tidak ditemukan

            if ($contract && $contract->unit && $contract->unit->unitCluster) {
                $roomNumber = $contract->unit->room_number;
                $clusterName = $contract->unit->unitCluster->name; // Contoh: "Tower A"

                // Ekstrak kode dari nama cluster (misalnya, 'A' dari 'Tower A')
                $clusterCode = '';
                $parts = explode(' ', $clusterName);
                if (!empty($parts)) {
                    // Ambil huruf pertama dari kata terakhir (misal: 'A' dari 'Tower A')
                    $clusterCode = Str::upper(substr(end($parts), 0, 1));
                }

                $unitCodePart = "{$roomNumber}{$clusterCode}"; // Contoh: 471A
            }

            $datePart = Carbon::now()->format('Ymd'); // Tanggal saat ini dalam format YYYYMMDD
            $randomSuffix = Str::upper(Str::random(4)); // String acak 4 karakter untuk bagian 'ID'

            $report->unique_id = "PG-{$unitCodePart}-{$datePart}-{$randomSuffix}";
        });
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function reporter()
    {
        return $this->belongsTo(Occupant::class, 'reporter_id');
    }

    public function currentHandler()
    {
        return $this->belongsTo(User::class, 'current_handler_id');
    }

    public function logs()
    {
        return $this->hasMany(ReportLog::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}