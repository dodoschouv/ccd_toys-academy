<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Domain\Article;
use ToysAcademy\Domain\AgeRange;
use ToysAcademy\Domain\ArticleCategory;
use ToysAcademy\Domain\ArticleState;

final class UpdateArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(
        string $id,
        string $designation,
        string $category,
        string $ageRange,
        string $state,
        int $price,
        int $weight,
        ?string $barcode = null,
    ): void {
        if (!ArticleCategory::isValid($category)) {
            throw new \InvalidArgumentException('Catégorie invalide');
        }
        if (!AgeRange::isValid($ageRange)) {
            throw new \InvalidArgumentException('Tranche d\'âge invalide');
        }
        if (!ArticleState::isValid($state)) {
            throw new \InvalidArgumentException('État invalide');
        }
        if ($price < 0 || $weight < 0) {
            throw new \InvalidArgumentException('Prix et poids doivent être positifs');
        }
        $existing = $this->articleRepository->getById($id);
        if ($existing === null) {
            throw new \InvalidArgumentException('Article non trouvé');
        }
        $article = new Article(
            id: $id,
            designation: trim($designation),
            category: $category,
            ageRange: $ageRange,
            state: $state,
            price: $price,
            weight: $weight,
            barcode: $barcode !== null && $barcode !== '' ? trim($barcode) : null,
            createdAt: $existing->createdAt,
        );
        $this->articleRepository->update($article);
    }
}
