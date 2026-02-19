<?php

declare(strict_types=1);

namespace ToysAcademy\Application\Port;

use ToysAcademy\Domain\User;

interface UserRepository
{
    public function getById(int $id): ?User;

    public function getByEmail(string $email): ?User;

    public function save(User $user): void;
}
