<?php

namespace App\Enums;

enum RoleUser: string
{
    case ADMIN = 'admin';
    case HEAD_OF_RUSUNAWA = 'head_of_rusunawa';
    case STAFF_OF_RUSUNAWA = 'staff_of_rusunawa';

    public static function values(): array
    {
        return [
            self::ADMIN->value,
            self::HEAD_OF_RUSUNAWA->value,
            self::STAFF_OF_RUSUNAWA->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::HEAD_OF_RUSUNAWA => 'Kepala Rusunawa',
            self::STAFF_OF_RUSUNAWA => 'Staf Rusunawa',
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
            self::ADMIN->value,
            self::HEAD_OF_RUSUNAWA->value,
            self::STAFF_OF_RUSUNAWA->value,
        ];
    }

    public static function toArrayLabel(): array
    {
        return [
            self::ADMIN->value => self::ADMIN->label(),
            self::HEAD_OF_RUSUNAWA->value => self::HEAD_OF_RUSUNAWA->label(),
            self::STAFF_OF_RUSUNAWA->value => self::STAFF_OF_RUSUNAWA->label(),
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
            self::ADMIN => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
            self::HEAD_OF_RUSUNAWA => ['bg-purple-100', 'text-purple-800', 'dark:bg-purple-900/30', 'dark:text-purple-400'],
            self::STAFF_OF_RUSUNAWA => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
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
