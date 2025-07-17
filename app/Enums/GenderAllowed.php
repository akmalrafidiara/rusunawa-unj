<?php

namespace App\Enums;

enum GenderAllowed: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case GENERAL = 'general';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function withoutGeneralValues(): array
    {
        return array_column(array_filter(self::cases(), fn($case) => $case !== self::GENERAL), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Laki-laki',
            self::FEMALE => 'Perempuan',
            self::GENERAL => 'Umum',
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
            self::MALE->value,
            self::FEMALE->value,
            self::GENERAL->value,
        ];
    }

    public static function toArrayLabel(): array
    {
        return [
            self::MALE->value => self::MALE->label(),
            self::FEMALE->value => self::FEMALE->label(),
            self::GENERAL->value => self::GENERAL->label(),
        ];
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }

    public static function optionsWithoutGeneral(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], array_filter(self::cases(), fn($case) => $case !== self::GENERAL));
    }

    public function color(): array
    {
        return match ($this) {
            self::MALE => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            self::FEMALE => ['bg-pink-100', 'text-pink-800', 'dark:bg-pink-900/30', 'dark:text-pink-400'],
            self::GENERAL => ['bg-teal-100', 'text-teal-800', 'dark:bg-teal-900/30', 'dark:text-teal-400'],
        };
    }
}
