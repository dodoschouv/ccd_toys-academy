<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Persistence;

use PDO;
use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Domain\ArticleCategory;
use ToysAcademy\Domain\Subscriber;

final class PdoSubscriberRepository implements SubscriberRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /** @return Subscriber[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM subscriber ORDER BY updated_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->rowToSubscriber($row);
        }
        return $result;
    }

    public function getById(string $id): ?Subscriber
    {
        $stmt = $this->pdo->prepare('SELECT * FROM subscriber WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToSubscriber($row) : null;
    }

    public function findByEmail(string $email): ?Subscriber
    {
        $stmt = $this->pdo->prepare('SELECT * FROM subscriber WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToSubscriber($row) : null;
    }

    public function save(Subscriber $subscriber): void
    {
        $prefs = $this->preferencesToColumns($subscriber->preferences);
        $sql = 'INSERT INTO subscriber (id, last_name, first_name, email, child_age_range, preference_1, preference_2, preference_3, preference_4, preference_5, preference_6)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->pdo->prepare($sql)->execute([
            $subscriber->id,
            $subscriber->lastName,
            $subscriber->firstName,
            $subscriber->email,
            $subscriber->childAgeRange,
            $prefs[0],
            $prefs[1],
            $prefs[2],
            $prefs[3],
            $prefs[4],
            $prefs[5],
        ]);
    }

    public function update(Subscriber $subscriber): void
    {
        $prefs = $this->preferencesToColumns($subscriber->preferences);
        $sql = 'UPDATE subscriber SET last_name = ?, first_name = ?, child_age_range = ?, preference_1 = ?, preference_2 = ?, preference_3 = ?, preference_4 = ?, preference_5 = ?, preference_6 = ?
                WHERE id = ?';
        $this->pdo->prepare($sql)->execute([
            $subscriber->lastName,
            $subscriber->firstName,
            $subscriber->childAgeRange,
            $prefs[0],
            $prefs[1],
            $prefs[2],
            $prefs[3],
            $prefs[4],
            $prefs[5],
            $subscriber->id,
        ]);
    }

    private function rowToSubscriber(array $row): Subscriber
    {
        $preferences = [
            $row['preference_1'],
            $row['preference_2'],
            $row['preference_3'],
            $row['preference_4'],
            $row['preference_5'],
            $row['preference_6'],
        ];
        return new Subscriber(
            id: $row['id'],
            lastName: $row['last_name'],
            firstName: $row['first_name'],
            email: $row['email'],
            childAgeRange: $row['child_age_range'],
            preferences: $preferences,
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
        );
    }

    /** @return array<int, string> */
    private function preferencesToColumns(array $preferences): array
    {
        $out = [];
        for ($i = 0; $i < 6; $i++) {
            $out[] = $preferences[$i] ?? ArticleCategory::SOC;
        }
        return $out;
    }
}
