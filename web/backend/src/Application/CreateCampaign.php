<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\CampaignRepository;
use ToysAcademy\Domain\Campaign;

final class CreateCampaign
{
    public function __construct(
        private CampaignRepository $campaignRepository,
    ) {
    }

    public function __invoke(int $maxWeightPerBox): Campaign
    {
        if ($maxWeightPerBox <= 0) {
            throw new \InvalidArgumentException('Le poids max par box doit être strictement positif');
        }
        $campaign = new Campaign(
            id: 0,
            maxWeightPerBox: $maxWeightPerBox,
        );
        $newId = $this->campaignRepository->save($campaign);
        return new Campaign(
            id: $newId,
            maxWeightPerBox: $maxWeightPerBox,
            createdAt: (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        );
    }
}
