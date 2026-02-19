<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final class Subscriber
{
    public function __construct(
        public string $id,
        public string $lastName,
        public string $firstName,
        public string $email,
        public string $childAgeRange,
        public array $preferences,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {
    }
}
