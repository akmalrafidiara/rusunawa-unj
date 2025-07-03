<?php

namespace App\Enums;

enum MaintenanceScheduleStatus: string
{
    case SCHEDULED = 'scheduled';
    case UPCOMING = 'upcoming';
    case OVERDUE = 'overdue';
    case POSTPONED = 'postponed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Terjadwal',
            self::UPCOMING => 'Akan Datang',
            self::OVERDUE => 'Terlambat',
            self::POSTPONED => 'Ditunda',
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
            self::UPCOMING => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::OVERDUE => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
            self::POSTPONED => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
        };
    }
}