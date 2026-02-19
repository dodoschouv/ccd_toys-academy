<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\BoxRepository;
use ToysAcademy\Domain\Box;

final class ValidateBox
{
    public function __construct(
        private BoxRepository $boxRepository,
    ) {
    }

    public function __invoke(int $boxId): void
    {
        $box = $this->boxRepository->getById($boxId);
        if ($box === null) {
            throw new \RuntimeException('Box introuvable', 404);
        }

        if ($box->status === Box::STATUS_VALIDATED) {
            throw new \RuntimeException('Cette box est déjà validée', 400);
        }

        // Vérifier que les articles de cette box ne sont pas déjà dans une autre box validée
        $articleIds = $this->boxRepository->getArticleIdsByBoxId($boxId);
        foreach ($articleIds as $articleId) {
            if ($this->boxRepository->isArticleInAnotherValidatedBox($articleId, $boxId)) {
                throw new \RuntimeException(
                    sprintf('L\'article %s est déjà présent dans une autre box validée', $articleId),
                    400
                );
            }
        }

        // Mettre à jour le statut de la box
        $validatedAt = date('Y-m-d H:i:s');
        $this->boxRepository->updateStatus($boxId, Box::STATUS_VALIDATED, $validatedAt);
    }
}
