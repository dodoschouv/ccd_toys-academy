<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final readonly class AgeRange
{
    public const BB = 'BB'; // 0-3 ans (bébé)
    public const PE = 'PE'; // 3-6 ans (petit enfant)
    public const EN = 'EN'; // 6-10 ans (enfant)
    public const AD = 'AD'; // 10+ ans (adolescent)

    /** @return array<string, string> code => label */
    public static function all(): array
    {
        return [
            self::BB => '0-3 ans (bébé)',
            self::PE => '3-6 ans (petit enfant)',
            self::EN => '6-10 ans (enfant)',
            self::AD => '10+ ans (adolescent)',
        ];
    }

    public static function isValid(string $code): bool
    {
        return array_key_exists($code, self::all());
    }
}
