<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Domain\AgeRange;
use ToysAcademy\Domain\ArticleCategory;
use ToysAcademy\Domain\ArticleState;

final class GetReferenceData
{
    /** @return array{categories: array<string, string>, age_ranges: array<string, string>, states: array<string, string>} */
    public function __invoke(): array
    {
        return [
            'categories' => ArticleCategory::all(),
            'age_ranges' => AgeRange::all(),
            'states' => ArticleState::all(),
        ];
    }
}
