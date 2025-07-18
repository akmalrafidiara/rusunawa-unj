<?php

namespace App\Enums;

enum PaymentStatus: string
{
    // --- 1. Definisi Kasus ---
    case PENDING_VERIFICATION = 'pending_verification';
    case APPROVED = 'approved';
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
            self::PENDING_VERIFICATION    => 'Menunggu Verifikasi',
            self::APPROVED      => 'Diterima',
            self::REJECTED   => 'Ditolak',
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
            self::PENDING_VERIFICATION    => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
            self::APPROVED      => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::REJECTED   => ['bg-orange-100', 'text-orange-800', 'dark:bg-orange-900/30', 'dark:text-orange-400'],
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
