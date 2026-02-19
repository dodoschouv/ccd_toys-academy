<?php

declare(strict_types=1);

namespace ToysAcademy\Application\Port;

use ToysAcademy\Domain\Campaign;

interface CampaignRepository
{
    /** @return Campaign[] */
    public function findAll(): array;

    public function getById(int $id): ?Campaign;

    public function save(Campaign $campaign): int;
}
