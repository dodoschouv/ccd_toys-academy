<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final readonly class ArticleState
{
    public const N = 'N';  // Neuf
    public const TB = 'TB'; // Très bon état
    public const B = 'B';  // Bon état

    /** @return array<string, string> code => label */
    public static function all(): array
    {
        return [
            self::N => 'Neuf',
            self::TB => 'Très bon état',
            self::B => 'Bon état',
        ];
    }

    public static function isValid(string $code): bool
    {
        return array_key_exists($code, self::all());
    }
}
