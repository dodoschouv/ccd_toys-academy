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

    /** @return Article[] */
    public function __invoke(): array
    {
        return $this->articleRepository->findAll();
    }
}
