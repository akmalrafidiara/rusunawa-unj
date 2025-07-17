<?php

namespace App\Enums;

enum OccupantStatus: string
{
    // --- 1. Definisi Kasus ---
    case PENDING_VERIFICATION = 'pending_verification';
    case VERIFIED = 'verified';
    case INACTIVE = 'inactive';
    case REJECTED = 'rejected';

    /**
     * Mengembalikan semua nilai 'value' dari enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Mengembalikan label yang ramah untuk dibaca manusia.
     */
    public function label(): string
    {
        // --- 2. Label untuk Setiap Status ---
        return match ($this) {
            self::PENDING_VERIFICATION => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::INACTIVE => 'Tidak Aktif',
            self::REJECTED => 'Ditolak',
        };
    }

    /**
     * Mencari enum case dari nilai string.
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }

    /**
     * Mengembalikan kelas warna Tailwind CSS untuk badge.
     */
    public function color(): array
    {
        // --- 3. Warna untuk Setiap Status ---
        return match ($this) {
            self::PENDING_VERIFICATION => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            self::VERIFIED => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::INACTIVE => ['bg-gray-100', 'text-gray-800', 'dark:bg-gray-900/30', 'dark:text-gray-400'],
            self::REJECTED => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
        };
    }

    /**
     * Helper untuk mendapatkan semua opsi dalam format yang ramah untuk dropdown.
     */
    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }
}
