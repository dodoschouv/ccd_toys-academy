<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final class Box
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_VALIDATED = 'validated';

    public function __construct(
        public int $id,
        public int $campaignId,
        public string $subscriberId,
        public string $status,
        public int $score,
        public int $totalWeight,
        public int $totalPrice,
        public ?string $validatedAt = null,
        public ?string $createdAt = null,
    ) {
    }
}
