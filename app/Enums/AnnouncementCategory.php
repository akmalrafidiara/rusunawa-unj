<?php

namespace App\Enums;

enum AnnouncementCategory: string
{
    case Important = 'important';
    case Appeal = 'appeal';
    case Maintenance = 'maintenance';
    case LostAndFound = 'lost_and_found';
    case General = 'general'; // Kategori tambahan

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Important => 'Penting',
            self::Appeal => 'Himbauan',
            self::Maintenance => 'Perawatan',
            self::LostAndFound => 'Kehilangan & Penemuan',
            self::General => 'Informasi Umum',
        };
    }

    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }

    public static function toArray(): array
    {
        return self::values();
    }

    public static function toArrayLabel(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }

    // Anda bisa menambahkan metode color() di sini juga jika diperlukan
    public function color(): array
    {
        return match ($this) {
            self::Important => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
            self::Appeal => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
            self::Maintenance => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            self::LostAndFound => ['bg-purple-100', 'text-purple-800', 'dark:bg-purple-900/30', 'dark:text-purple-400'],
            self::General => ['bg-gray-100', 'text-gray-800', 'dark:bg-gray-900/30', 'dark:text-gray-400'],
        };
    }
}