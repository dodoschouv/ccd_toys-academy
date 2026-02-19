<?php

declare(strict_types=1);

namespace ToysAcademy\Application;

use ToysAcademy\Application\Port\ArticleRepository;

final class DeleteArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(string $id): void
    {
        if ($this->articleRepository->getById($id) === null) {
            throw new \InvalidArgumentException('Article non trouvé');
        }
        $this->articleRepository->delete($id);
    }
}
