<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Domain\Article;

final class ListArticles
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    /**
     * @return array{items: Article[], total: int}
     */
    public function __invoke(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        return $this->articleRepository->findPaginated($page, $perPage);
    }
}
