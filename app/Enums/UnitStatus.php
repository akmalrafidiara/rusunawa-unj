<?php

namespace App\Enums;

enum UnitStatus: string
{
    case AVAILABLE = 'available';
    case NOT_AVAILABLE = 'not_available';
    case OCCUPIED = 'occupied';
    case UNDER_MAINTENANCE = 'under_maintenance';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::NOT_AVAILABLE => 'Tidak Tersedia',
            self::OCCUPIED => 'Terisi',
            self::UNDER_MAINTENANCE => 'Dalam Perawatan',
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
        return [
            self::AVAILABLE->value,
            self::NOT_AVAILABLE->value,
            self::OCCUPIED->value,
            self::UNDER_MAINTENANCE->value,
        ];
    }

    public static function toArrayLabel(): array
    {
        return [
            self::AVAILABLE->value => self::AVAILABLE->label(),
            self::NOT_AVAILABLE->value => self::NOT_AVAILABLE->label(),
            self::OCCUPIED->value => self::OCCUPIED->label(),
            self::UNDER_MAINTENANCE->value => self::UNDER_MAINTENANCE->label(),
        ];
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
            self::AVAILABLE => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::NOT_AVAILABLE => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
            self::OCCUPIED => ['bg-violet-100', 'text-violet-800', 'dark:bg-violet-900/30', 'dark:text-violet-400'],
            self::UNDER_MAINTENANCE => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
        };
    }
}
