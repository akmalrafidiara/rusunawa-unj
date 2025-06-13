<?php

namespace App\Enums;

enum AnnouncementStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public static function values(): array
    {
        return [
            self::Draft->value,
            self::Published->value,
            self::Archived->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Diterbitkan',
            self::Archived => 'Diarsipkan',
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
            self::Draft->value,
            self::Published->value,
            self::Archived->value,
        ];
    }

    public static function toArrayLabel(): array
    {
        return [
            self::Draft->value => self::Draft->label(),
            self::Published->value => self::Published->label(),
            self::Archived->value => self::Archived->label(),
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
            self::Draft => ['bg-gray-100', 'text-gray-800', 'dark:bg-gray-900/30', 'dark:text-gray-400'],
            self::Published => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            self::Archived => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
        };
    }

    public function isActive(): bool
    {
        return $this === self::Published;
    }
}