<?php

namespace App\Enums;

enum RoleUser: string
{
    case ADMIN = 'admin';
    case HEAD_OF_RUSUNAWA = 'head_of_rusunawa';
    case STAFF_OF_RUSUNAWA = 'staff_of_rusunawa';
    case OCCUPANT = 'occupant';

    public static function values(): array
    {
        return [
            self::ADMIN->value,
            self::HEAD_OF_RUSUNAWA->value,
            self::STAFF_OF_RUSUNAWA->value,
            self::OCCUPANT->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::HEAD_OF_RUSUNAWA => 'Kepala Rusunawa',
            self::STAFF_OF_RUSUNAWA => 'Staf Rusunawa',
            self::OCCUPANT => 'Penghuni',
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
            self::OCCUPANT->value,
        ];
    }

    public static function toArrayLabel(): array
    {
        return [
            self::ADMIN->value => self::ADMIN->label(),
            self::HEAD_OF_RUSUNAWA->value => self::HEAD_OF_RUSUNAWA->label(),
            self::STAFF_OF_RUSUNAWA->value => self::STAFF_OF_RUSUNAWA->label(),
            self::OCCUPANT->value => self::OCCUPANT->label(),
        ];
    }
}
