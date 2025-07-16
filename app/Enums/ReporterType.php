<?php

namespace App\Enums;

enum ReporterType: string
{
    case ROOM = 'room'; // Melalui PIC kontrak
    case INDIVIDUAL = 'individual'; // Penghuni individual

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ROOM => 'Kamar (PIC)',
            self::INDIVIDUAL => 'Penghuni Individual',
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