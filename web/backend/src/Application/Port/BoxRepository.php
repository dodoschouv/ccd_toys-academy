<?php

declare(strict_types=1);

namespace ToysAcademy\Application\Port;

use ToysAcademy\Domain\Box;

interface BoxRepository
{
    public function deleteDraftByCampaign(int $campaignId): void;

    public function save(Box $box): int;

    public function addArticleToBox(int $boxId, string $articleId): void;

    /** @return Box[] */
    public function findByCampaign(int $campaignId): array;

    public function getById(int $id): ?Box;
}
