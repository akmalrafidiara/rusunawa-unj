<?php

namespace App\Enums;

enum ReportStatus: string
{
    case REPORT_RECEIVED = 'report_received';
    case IN_PROCESS = 'in_process';
    case DISPOSED_TO_ADMIN = 'disposed_to_admin';
    case DISPOSED_TO_RUSUNAWA = 'disposed_to_rusunawa'; // Admin menyerahkan kembali ke rusunawa
    case COMPLETED = 'completed'; // Dinyatakan selesai oleh manager/staff/admin
    case CONFIRMED_COMPLETED = 'confirmed_completed'; // Konfirmasi oleh penghuni

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::REPORT_RECEIVED => 'Laporan Masuk',
            self::IN_PROCESS => 'Sedang Diproses',
            self::DISPOSED_TO_ADMIN => 'Disposisi ke Admin',
            self::DISPOSED_TO_RUSUNAWA => 'Dikembalikan ke Rusunawa',
            self::COMPLETED => 'Selesai (Menunggu Konfirmasi Penghuni)',
            self::CONFIRMED_COMPLETED => 'Dikonfirmasi Selesai',
        };
    }

    public function color(): array
    {
        return match ($this) {
            self::REPORT_RECEIVED => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
            self::IN_PROCESS => ['bg-indigo-100', 'text-indigo-800', 'dark:bg-indigo-900/30', 'dark:text-indigo-400'],
            self::DISPOSED_TO_ADMIN => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
            self::DISPOSED_TO_RUSUNAWA => ['bg-orange-100', 'text-orange-800', 'dark:bg-orange-900/30', 'dark:text-orange-400'],
            self::COMPLETED => ['bg-teal-100', 'text-teal-800', 'dark:bg-teal-900/30', 'dark:text-teal-400'],
            self::CONFIRMED_COMPLETED => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }
}