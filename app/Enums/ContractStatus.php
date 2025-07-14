<?php

namespace App\Enums;

enum ContractStatus: string
{
    // --- 1. Definisi Kasus ---
    case PENDING_PAYMENT = 'pending_payment';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

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
            self::PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::ACTIVE => 'Aktif',
            self::EXPIRED => 'Kedaluwarsa',
            self::CANCELLED => 'Dibatalkan',
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
            self::PENDING_PAYMENT => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
            self::ACTIVE => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::EXPIRED => ['bg-gray-100', 'text-gray-800', 'dark:bg-gray-900/30', 'dark:text-gray-400'],
            self::CANCELLED => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
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
