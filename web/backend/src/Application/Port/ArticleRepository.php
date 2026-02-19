<?php

declare(strict_types=1);

namespace ToysAcademy\Application\Port;

use ToysAcademy\Domain\Article;

interface ArticleRepository
{
    /** @return Article[] */
    public function findAll(): array;

    /**
     * @return array{items: Article[], total: int}
     */
    public function findPaginated(int $page, int $perPage, ?string $category = null, ?string $ageRange = null, ?string $state = null): array;

    public function getById(string $id): ?Article;

    public function save(Article $article): void;

    public function update(Article $article): void;

    public function delete(string $id): void;
}
