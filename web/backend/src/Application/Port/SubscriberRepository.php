<?php

declare(strict_types=1);

namespace ToysAcademy\Application\Port;

use ToysAcademy\Domain\Subscriber;

interface SubscriberRepository
{
    /** @return Subscriber[] */
    public function findAll(): array;

    public function getById(string $id): ?Subscriber;

    public function findByEmail(string $email): ?Subscriber;

    public function findByFirstName(string $firstName): ?Subscriber;

    public function save(Subscriber $subscriber): void;

    public function update(Subscriber $subscriber): void;
}
