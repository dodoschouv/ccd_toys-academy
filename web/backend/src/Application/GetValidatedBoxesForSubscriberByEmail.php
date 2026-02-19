<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Application\Port\BoxRepository;
use ToysAcademy\Application\Port\SubscriberRepository;

/**
 * Consultation box abonné (W9) : retourne les box validées pour l'abonné identifié par email
 * Seules les box validées sont visibles
 *
 * @return list<array{id: int, campaign_id: int, status: string, score: int, total_weight: int, total_price: int, validated_at: ?string, created_at: ?string, articles: list<array{id: string, designation: string, category: string, age_range: string, state: string, price: int, weight: int}>}>
 */
final class GetValidatedBoxesForSubscriberByEmail
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private BoxRepository $boxRepository,
        private ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(string $email): array
    {
        $email = trim($email);
        if ($email === '') {
            throw new \RuntimeException('Email requis', 400);
        }

        $subscriber = $this->subscriberRepository->findByEmail($email);
        if ($subscriber === null) {
            throw new \RuntimeException('Aucun abonné avec cet email', 404);
        }

        $boxes = $this->boxRepository->findValidatedBySubscriberId($subscriber->id);
        $result = [];

        foreach ($boxes as $box) {
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
