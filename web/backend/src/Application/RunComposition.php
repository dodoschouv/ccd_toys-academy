<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Application\Port\BoxRepository;
use ToysAcademy\Application\Port\CampaignRepository;
use ToysAcademy\Application\Port\OptimisationService;
use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Domain\Box;

final class RunComposition
{
    public function __construct(
        private CampaignRepository $campaignRepository,
        private ArticleRepository $articleRepository,
        private SubscriberRepository $subscriberRepository,
        private BoxRepository $boxRepository,
        private OptimisationService $optimisationService,
    ) {
    }

    /**
     * @return array{score: int, boxes_count: int}
     * @throws \RuntimeException
     */
    public function run(int $campaignId): array
    {
        $campaign = $this->campaignRepository->getById($campaignId);
        if ($campaign === null) {
            throw new \RuntimeException('Campagne introuvable', 404);
        }

        $articles = $this->articleRepository->findAll();
        $subscribers = $this->subscriberRepository->findAll();
        if ($articles === [] || $subscribers === []) {
            throw new \RuntimeException('Aucun article ou abonné pour lancer la composition', 400);
        }

        $csvInput = $this->buildCsvInput($articles, $subscribers, $campaign->maxWeightPerBox);
        $csvOutput = $this->optimisationService->compute($csvInput);
        $parsed = $this->parseCsvOutput($csvOutput);

        $this->boxRepository->deleteDraftByCampaign($campaignId);

        $score = $parsed['score'];
        $byPrenom = $parsed['by_prenom'];

        foreach ($byPrenom as $prenom => $lines) {
            $subscriber = $this->subscriberRepository->findByFirstName($prenom);
            if ($subscriber === null) {
                continue;
            }
            $totalWeight = 0;
            $totalPrice = 0;
            $articleIds = [];
            foreach ($lines as $line) {
                $articleId = $line['article_id'];
                $article = $this->articleRepository->getById($articleId);
                if ($article !== null) {
                    $totalWeight += $article->weight;
                    $totalPrice += $article->price;
                    $articleIds[] = $articleId;
                }
            }
            $box = new Box(
                id: 0,
                campaignId: $campaignId,
                subscriberId: $subscriber->id,
                status: Box::STATUS_DRAFT,
                score: $score,
                totalWeight: $totalWeight,
                totalPrice: $totalPrice,
                validatedAt: null,
                createdAt: null,
            );
            $boxId = $this->boxRepository->save($box);
            foreach ($articleIds as $aid) {
                $this->boxRepository->addArticleToBox($boxId, $aid);
            }
        }

        return [
            'score' => $score,
            'boxes_count' => count($byPrenom),
        ];
    }

    private function buildCsvInput(array $articles, array $subscribers, int $maxWeightPerBox): string
    {
        $lines = [];
        $lines[] = 'articles';
        foreach ($articles as $a) {
            $lines[] = sprintf(
                '%s;%s;%s;%s;%s;%d;%d',
                $a->id,
                $a->designation,
                $a->category,
                $a->ageRange,
                $a->state,
                $a->price,
                $a->weight
            );
        }
        $lines[] = '';
        $lines[] = 'abonnes';
        foreach ($subscribers as $s) {
            $prefs = implode(',', $s->preferences);
            $lines[] = sprintf('%s;%s;%s;%s', $s->id, $s->firstName, $s->childAgeRange, $prefs);
        }
        $lines[] = '';
        $lines[] = 'parametres';
        $lines[] = (string) $maxWeightPerBox;
        return implode("\n", $lines);
    }

    /**
     * @return array{score: int, by_prenom: array<string, list<array{article_id: string, category: string, age: string, state: string}>>}
     */
    private function parseCsvOutput(string $csv): array
    {
        $csv = trim($csv);
        $lines = preg_split('/\r?\n/', $csv);
        if ($lines === false || $lines === []) {
            throw new \RuntimeException('Réponse optimisation vide ou invalide');
        }
        $scoreLine = $lines[0];
        if (!preg_match('/^\d+$/', trim($scoreLine))) {
            throw new \RuntimeException('Format de réponse optimisation invalide (score attendu en première ligne)');
        }
        $score = (int) trim($scoreLine);
        $byPrenom = [];
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line === '') {
                continue;
            }
            $cols = explode(';', $line);
            if (count($cols) < 5) {
                continue;
            }
            $prenom = trim($cols[0]);
            $articleId = trim($cols[1]);
            $category = trim($cols[2]);
            $age = trim($cols[3]);
            $state = trim($cols[4]);
            if (!isset($byPrenom[$prenom])) {
                $byPrenom[$prenom] = [];
            }
            $byPrenom[$prenom][] = [
                'article_id' => $articleId,
                'category' => $category,
                'age' => $age,
                'state' => $state,
            ];
        }
        return ['score' => $score, 'by_prenom' => $byPrenom];
    }
}
