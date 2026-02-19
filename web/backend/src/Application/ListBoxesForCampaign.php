<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Application\Port\BoxRepository;
use ToysAcademy\Application\Port\CampaignRepository;
use ToysAcademy\Application\Port\SubscriberRepository;

/**
 * Liste les box composées d'une campagne (admin) : articles par box, score, poids, prix.
 *
 * @return array<int, array{id: int, campaign_id: int, subscriber_id: string, subscriber: array{first_name: string, last_name: string, email: string}, status: string, score: int, total_weight: int, total_price: int, validated_at: ?string, created_at: ?string, articles: array<int, array{id: string, designation: string, category: string, age_range: string, state: string, price: int, weight: int}>}>
 */
final class ListBoxesForCampaign
{
    public function __construct(
        private CampaignRepository $campaignRepository,
        private BoxRepository $boxRepository,
        private ArticleRepository $articleRepository,
        private SubscriberRepository $subscriberRepository,
    ) {
    }

    public function __invoke(int $campaignId): array
    {
        $campaign = $this->campaignRepository->getById($campaignId);
        if ($campaign === null) {
            throw new \RuntimeException('Campagne introuvable', 404);
        }

        $boxes = $this->boxRepository->findByCampaign($campaignId);
        $result = [];

        foreach ($boxes as $box) {
            $subscriber = $this->subscriberRepository->getById($box->subscriberId);
            $articleIds = $this->boxRepository->getArticleIdsByBoxId($box->id);
            $articles = [];
            foreach ($articleIds as $articleId) {
                $article = $this->articleRepository->getById($articleId);
                if ($article !== null) {
                    $articles[] = [
                        'id' => $article->id,
                        'designation' => $article->designation,
                        'category' => $article->category,
                        'age_range' => $article->ageRange,
                        'state' => $article->state,
                        'price' => $article->price,
                        'weight' => $article->weight,
                    ];
                }
            }

            $result[] = [
                'id' => $box->id,
                'campaign_id' => $box->campaignId,
                'subscriber_id' => $box->subscriberId,
                'subscriber' => $subscriber ? [
                    'first_name' => $subscriber->firstName,
                    'last_name' => $subscriber->lastName,
                    'email' => $subscriber->email,
                ] : ['first_name' => '', 'last_name' => '', 'email' => ''],
                'status' => $box->status,
                'score' => $box->score,
                'total_weight' => $box->totalWeight,
                'total_price' => $box->totalPrice,
                'validated_at' => $box->validatedAt,
                'created_at' => $box->createdAt,
                'articles' => $articles,
            ];
        }

        return $result;
    }
}
