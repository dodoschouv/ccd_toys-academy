<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final class Article
{
    public function __construct(
        public string $id,
        public string $designation,
        public string $category,
        public string $ageRange,
        public string $state,
        public int $price,
        public int $weight,
        public ?string $barcode = null,
        public ?string $createdAt = null,
    ) {
    }
}
