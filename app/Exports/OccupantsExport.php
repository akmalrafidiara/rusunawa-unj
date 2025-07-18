<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Gunakan ini untuk header yang lebih bersih
use Illuminate\Support\Collection;

// Ganti FromCollection menjadi WithHeadings untuk manajemen header yang lebih baik
class OccupantsExport  implements FromCollection, WithHeadings
{
    protected Collection $occupants;

    /**
     * Terima koleksi data yang sudah di-query sebelumnya.
     *
     * @param Collection $occupants
     */
    public function __construct(Collection $occupants)
    {
        $this->occupants = $occupants;
    }

    /**
     * Definisikan baris header untuk Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'No WA',
            'Jenis Kelamin',
            'Status Verifikasi',
            'Apakah Mahasiswa?',
            'NIM',
            'Fakultas',
            'Program Studi',
            'Tahun Angkatan',
            'Kontrak Aktif',
            'Catatan',
        ];
    }

    /**
     * Fungsi ini sekarang hanya memformat dan mengembalikan data yang sudah ada.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->occupants->map(function ($occupant) {
            return [
                'full_name' => $occupant->full_name,
                'email' => $occupant->email,
                'whatsapp_number' => 'https://wa.me/' . $occupant->whatsapp_number,
                'gender' => $occupant->gender->label(),
                'status' => $occupant->status->label(),
                'is_student' => $occupant->is_student ? 'Ya' : 'Tidak',
                'student_id' => $occupant->student_id,
                'faculty' => $occupant->faculty,
                'study_program' => $occupant->study_program,
                'class_year' => $occupant->class_year,
                'contracts' => $occupant->contracts->map(function ($contract) {
                    return $contract->contract_code . ' - ' . $contract->unit->unitCluster->name . ' | ' . $contract->unit->room_number;
                })->implode(', '),
                'notes' => $occupant->notes,
            ];
        });
    }
}