<?php

declare(strict_types=1);

namespace ToysAcademy\Domain;

final class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $firstName,
        public string $lastName,
        public ?string $passwordHash = null,
        public string $role = 'subscriber',
        public ?string $subscriberId = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {
    }
}
