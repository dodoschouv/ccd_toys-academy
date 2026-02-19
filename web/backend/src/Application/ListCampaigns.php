<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\CampaignRepository;
use ToysAcademy\Domain\Campaign;

final class ListCampaigns
{
    public function __construct(
        private CampaignRepository $campaignRepository,
    ) {
    }

    /** @return Campaign[] */
    public function __invoke(): array
    {
        return $this->campaignRepository->findAll();
    }
}
