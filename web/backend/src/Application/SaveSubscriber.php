<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Domain\AgeRange;
use ToysAcademy\Domain\ArticleCategory;
use ToysAcademy\Domain\Subscriber;

final class SaveSubscriber
{
    private const PREFERENCES_COUNT = 6;

    public function __construct(
        private SubscriberRepository $subscriberRepository,
    ) {
    }

    /**
     * @param array<int, string> $preferences Liste de 1 à 6 codes catégories (ordre de préférence)
     */
    public function __invoke(
        string $lastName,
        string $firstName,
        string $email,
        string $childAgeRange,
        array $preferences,
    ): void {
        if (!AgeRange::isValid($childAgeRange)) {
            throw new \InvalidArgumentException('Tranche d\'âge invalide');
        }
        $email = trim($email);
        if ($email === '') {
            throw new \InvalidArgumentException('Email requis');
        }
        $normalized = $this->normalizePreferences($preferences);
        $existing = $this->subscriberRepository->findByEmail($email);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        if ($existing !== null) {
            $subscriber = new Subscriber(
                id: $existing->id,
                lastName: trim($lastName),
                firstName: trim($firstName),
                email: $email,
                childAgeRange: $childAgeRange,
                preferences: $normalized,
                createdAt: $existing->createdAt,
                updatedAt: $now,
            );
            $this->subscriberRepository->update($subscriber);
        } else {
            $subscriber = new Subscriber(
                id: $this->generateId(),
                lastName: trim($lastName),
                firstName: trim($firstName),
                email: $email,
                childAgeRange: $childAgeRange,
                preferences: $normalized,
                createdAt: $now,
                updatedAt: $now,
            );
            $this->subscriberRepository->save($subscriber);
        }
    }

    /** @return array<int, string> */
    private function normalizePreferences(array $preferences): array
    {
        $valid = [];
        foreach ($preferences as $code) {
            if (ArticleCategory::isValid((string) $code)) {
                $valid[] = (string) $code;
            }
        }
        if ($valid === []) {
            $valid = [ArticleCategory::SOC];
        }
        while (count($valid) < self::PREFERENCES_COUNT) {
            $valid[] = $valid[count($valid) - 1];
        }
        return array_slice($valid, 0, self::PREFERENCES_COUNT);
    }

    private function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
