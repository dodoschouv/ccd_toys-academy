<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final class Campaign
{
    public function __construct(
        public int $id,
        public int $maxWeightPerBox,
        public ?string $createdAt = null,
    ) {
    }
}
