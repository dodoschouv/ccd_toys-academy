<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Persistence;

use PDO;
use ToysAcademy\Application\Port\BoxRepository;
use ToysAcademy\Domain\Box;

final class PdoBoxRepository implements BoxRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function deleteDraftByCampaign(int $campaignId): void
    {
        $this->pdo->prepare('DELETE FROM box WHERE campaign_id = ? AND status = ?')
            ->execute([$campaignId, Box::STATUS_DRAFT]);
    }

    public function save(Box $box): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO box (campaign_id, subscriber_id, status, score, total_weight, total_price, validated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $box->campaignId,
            $box->subscriberId,
            $box->status,
            $box->score,
            $box->totalWeight,
            $box->totalPrice,
            $box->validatedAt,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function addArticleToBox(int $boxId, string $articleId): void
    {
        $this->pdo->prepare('INSERT INTO box_article (box_id, article_id) VALUES (?, ?)')
            ->execute([$boxId, $articleId]);
    }

    /** @return Box[] */
    public function findByCampaign(int $campaignId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM box WHERE campaign_id = ? ORDER BY id');
        $stmt->execute([$campaignId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->rowToBox($row);
        }
        return $result;
    }

    /** @return Box[] */
    public function findValidatedBySubscriberId(string $subscriberId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM box WHERE subscriber_id = ? AND status = ? ORDER BY validated_at DESC, id DESC'
        );
        $stmt->execute([$subscriberId, Box::STATUS_VALIDATED]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->rowToBox($row);
        }
        return $result;
    }

    public function getById(int $id): ?Box
    {
        $stmt = $this->pdo->prepare('SELECT * FROM box WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToBox($row) : null;
    }

    /** @return string[] */
    public function getArticleIdsByBoxId(int $boxId): array
    {
        $stmt = $this->pdo->prepare('SELECT article_id FROM box_article WHERE box_id = ? ORDER BY article_id');
        $stmt->execute([$boxId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function rowToBox(array $row): Box
    {
        return new Box(
            id: (int) $row['id'],
            campaignId: (int) $row['campaign_id'],
            subscriberId: $row['subscriber_id'],
            status: $row['status'],
            score: (int) $row['score'],
            totalWeight: (int) $row['total_weight'],
            totalPrice: (int) $row['total_price'],
            validatedAt: $row['validated_at'] ?? null,
            createdAt: $row['created_at'] ?? null,
        );
    }
}
