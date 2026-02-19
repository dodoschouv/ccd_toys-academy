<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Domain\Subscriber;

final class ListSubscribers
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
    ) {
    }

    /** @return Subscriber[] */
    public function __invoke(): array
    {
        return $this->subscriberRepository->findAll();
    }
}
