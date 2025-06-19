<?php

namespace App\Enums;

enum EmergencyContactRole: string
{
    case Internal = 'internal';
    case External = 'external';

    /**
     * Get all raw values of the enum cases.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get a human-readable label for the enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Internal',
            self::External => 'Eksternal',
        };
    }

    /**
     * Create an enum instance from a given string value.
     *
     * @param string $value
     * @return self|null
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }

    /**
     * Get an array of all enum values.
     * Alias for values().
     *
     * @return array<string>
     */
    public static function toArray(): array
    {
        return self::values();
    }

    /**
     * Get an associative array of enum values and their labels.
     *
     * @return array<string, string>
     */
    public static function toArrayLabel(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();
            return $carry;
        }, []);
    }

    /**
     * Get an array of options suitable for select inputs (value-label pairs).
     *
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }

    /**
     * Get Tailwind CSS classes for styling based on the role.
     *
     * @return array<string>
     */
    public function color(): array
    {
        return match ($this) {
            self::Internal => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            self::External => ['bg-purple-100', 'text-purple-800', 'dark:bg-purple-900/30', 'dark:text-purple-400'],
        };
    }

    /**
     * Check if the emergency contact role is internal.
     *
     * @return bool
     */
    public function isInternal(): bool
    {
        return $this === self::Internal;
    }

    /**
     * Check if the emergency contact role is external.
     *
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this === self::External;
    }
}