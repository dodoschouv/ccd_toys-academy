<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Persistence;

use PDO;
use ToysAcademy\Application\Port\CampaignRepository;
use ToysAcademy\Domain\Campaign;

final class PdoCampaignRepository implements CampaignRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /** @return Campaign[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM campaign ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->rowToCampaign($row);
        }
        return $result;
    }

    public function getById(int $id): ?Campaign
    {
        $stmt = $this->pdo->prepare('SELECT * FROM campaign WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToCampaign($row) : null;
    }

    public function save(Campaign $campaign): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO campaign (max_weight_per_box) VALUES (?)');
        $stmt->execute([$campaign->maxWeightPerBox]);
        return (int) $this->pdo->lastInsertId();
    }

    private function rowToCampaign(array $row): Campaign
    {
        return new Campaign(
            id: (int) $row['id'],
            maxWeightPerBox: (int) $row['max_weight_per_box'],
            createdAt: $row['created_at'] ?? null,
        );
    }
}
