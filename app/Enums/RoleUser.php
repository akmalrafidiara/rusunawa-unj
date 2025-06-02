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

    public function toString(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::HEAD_OF_RUSUNAWA => 'kepala rusunawa',
            self::STAFF_OF_RUSUNAWA => 'staff rusunawa',
            self::OCCUPANT => 'penghuni',
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
            self::ADMIN->value => self::ADMIN->toString(),
            self::HEAD_OF_RUSUNAWA->value => self::HEAD_OF_RUSUNAWA->toString(),
            self::STAFF_OF_RUSUNAWA->value => self::STAFF_OF_RUSUNAWA->toString(),
            self::OCCUPANT->value => self::OCCUPANT->toString(),
        ];
    }
}
