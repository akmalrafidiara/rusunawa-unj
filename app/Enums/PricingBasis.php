<?php

namespace App\Enums;

enum PricingBasis: string
{
    case PER_NIGHT = 'per_night';
    case PER_MONTH = 'per_month';

    public static function values(): array
    {
        return [
            self::PER_NIGHT->value,
            self::PER_MONTH->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::PER_NIGHT => 'Permalam',
            self::PER_MONTH => 'Perbulan',
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
            self::PER_NIGHT->value,
            self::PER_MONTH->value,
        ];
    }

    public static function toArrayLabel(): array
    {
        return [
            self::PER_NIGHT->value => self::PER_NIGHT->label(),
            self::PER_MONTH->value => self::PER_MONTH->label(),
        ];
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }

    public function color(): array
    {
        return match ($this) {
            self::PER_NIGHT => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            self::PER_MONTH => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
        };
    }

    // public static function options(): array
    // {
    //     $options = [];

    //     foreach (self::cases() as $case) {
    //         $options[$case->value] = $case->label();
    //     }

    //     return $options;
    // }
}
