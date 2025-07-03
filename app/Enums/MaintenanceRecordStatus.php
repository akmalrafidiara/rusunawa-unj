<?php

namespace App\Enums;

enum MaintenanceRecordStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED_ON_TIME = 'completed_on_time';
    case COMPLETED_LATE = 'completed_late';
    case COMPLETED_EARLY = 'completed_early';
    case POSTPONED = 'postponed';
    case URGENT = 'urgent';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Terjadwal',
            self::COMPLETED_ON_TIME => 'Selesai Tepat Waktu',
            self::COMPLETED_LATE => 'Selesai Terlambat',
            self::COMPLETED_EARLY => 'Selesai Lebih Awal',
            self::POSTPONED => 'Ditunda',
            self::URGENT => 'Urgen',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }

    public function color(): array
    {
        return match ($this) {
            self::SCHEDULED => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            self::COMPLETED_ON_TIME => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::COMPLETED_LATE => ['bg-orange-100', 'text-orange-800', 'dark:bg-orange-900/30', 'dark:text-orange-400'],
            self::COMPLETED_EARLY => ['bg-teal-100', 'text-teal-800', 'dark:bg-teal-900/30', 'dark:text-teal-400'],
            self::POSTPONED => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
            self::URGENT => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
        };
    }
}