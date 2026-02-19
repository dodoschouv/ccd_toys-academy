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

    /** @return Box[] Box validées pour cet abonné, par date de validation décroissante */
    public function findValidatedBySubscriberId(string $subscriberId): array;

    public function getById(int $id): ?Box;

    /** @return string[] Liste des IDs d'articles dans la box */
    public function getArticleIdsByBoxId(int $boxId): array;

    /**
     * Vérifie si un article est présent dans au moins une box validée.
     * @return bool true si l'article est dans une box validée, false sinon
     */
    public function isArticleInValidatedBox(string $articleId): bool;

    /**
     * Met à jour le statut d'une box et sa date de validation.
     */
    public function updateStatus(int $boxId, string $status, ?string $validatedAt = null): void;

    /**
     * Vérifie si un article est présent dans une box validée autre que celle spécifiée.
     * @return bool true si l'article est dans une autre box validée, false sinon
     */
    public function isArticleInAnotherValidatedBox(string $articleId, int $excludeBoxId): bool;
}
