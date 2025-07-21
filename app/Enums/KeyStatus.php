<?php

namespace App\Enums;

enum KeyStatus: string
{
    // --- 1. Definisi Kasus ---
    case PENDING_HANDOVER = 'pending_handover';
    case HANDED_OVER = 'handed_over';
    case RETURNED = 'returned';
    case LOST = 'lost';

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
            self::PENDING_HANDOVER => 'Belum Diserahkan',
            self::HANDED_OVER      => 'Sudah Diserahkan',
            self::RETURNED         => 'Sudah Dikembalikan',
            self::LOST             => 'Hilang',
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
            self::PENDING_HANDOVER => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'], // Kuning untuk status menunggu
            self::HANDED_OVER      => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],   // Hijau untuk status sukses/aktif
            self::RETURNED         => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],     // Biru untuk status selesai/netral
            self::LOST             => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],         // Merah untuk status masalah/hilang
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
