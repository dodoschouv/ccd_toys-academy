<?php

declare(strict_types=1);

namespace ToysAcademy\Application\Port;

interface OptimisationService
{
    /**
     * Envoie le CSV d'entrée à la brique d'optimisation et retourne le CSV de sortie (score + lignes prénom;article_id;...).
     * @throws \RuntimeException en cas d'erreur HTTP ou d'optimisation
     */
    public function compute(string $csvInput): string;
}
