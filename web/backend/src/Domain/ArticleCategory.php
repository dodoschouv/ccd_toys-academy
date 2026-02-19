<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final readonly class ArticleCategory
{
    public const SOC = 'SOC'; // Jeux de société
    public const FIG = 'FIG'; // Figurines et poupées
    public const CON = 'CON'; // Jeux de construction
    public const EXT = 'EXT'; // Jeux d'extérieur
    public const EVL = 'EVL'; // Jeux d'éveil et éducatifs
    public const LIV = 'LIV'; // Livres jeunesse

    /** @return array<string, string> code => label */
    public static function all(): array
    {
        return [
            self::SOC => 'Jeux de société',
            self::FIG => 'Figurines et poupées',
            self::CON => 'Jeux de construction',
            self::EXT => 'Jeux d\'extérieur',
            self::EVL => 'Jeux d\'éveil et éducatifs',
            self::LIV => 'Livres jeunesse',
        ];
    }

    public static function isValid(string $code): bool
    {
        return array_key_exists($code, self::all());
    }
}
