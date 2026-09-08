<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\UnsupportedLocaleException;

enum Locale: string
{
    case Spanish = 'es';
    case English = 'en';
    case German = 'de';
    case French = 'fr';

    public const self DEFAULT = self::Spanish;

    public static function fromString(string $value): self
    {
        return self::tryFrom(self::normalise($value)) ?? throw new UnsupportedLocaleException();
    }

    public static function fromStringOrDefault(?string $value): self
    {
        return $value === null ? self::DEFAULT : self::tryFrom(self::normalise($value)) ?? self::DEFAULT;
    }

    private static function normalise(string $value): string
    {
        $language = preg_split('/[-_]/', trim($value))[0] ?? '';

        return strtolower($language);
    }

    public function displayName(): string
    {
        return match ($this) {
            self::Spanish => 'Español',
            self::English => 'English',
            self::German => 'Deutsch',
            self::French => 'Français'
        };
    }

    public static function all(): array
    {
        return self::cases();
    }
}
