<?php

namespace App\Enums;

enum MaintenanceRecordType: string
{
    case ROUTINE = 'routine';
    case URGENT = 'urgent';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ROUTINE => 'Rutin',
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
}