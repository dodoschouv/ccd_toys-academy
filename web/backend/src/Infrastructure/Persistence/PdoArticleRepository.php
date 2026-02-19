<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Persistence;

use PDO;
use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Domain\Article;

final class PdoArticleRepository implements ArticleRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /** @return Article[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM article ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->rowToArticle($row);
        }
        return $result;
    }

    public function getById(string $id): ?Article
    {
        $stmt = $this->pdo->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToArticle($row) : null;
    }

    public function save(Article $article): void
    {
        $sql = 'INSERT INTO article (id, designation, category, age_range, state, price, weight, barcode)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $this->pdo->prepare($sql)->execute([
            $article->id,
            $article->designation,
            $article->category,
            $article->ageRange,
            $article->state,
            $article->price,
            $article->weight,
            $article->barcode,
        ]);
    }

    private function rowToArticle(array $row): Article
    {
        return new Article(
            id: $row['id'],
            designation: $row['designation'],
            category: $row['category'],
            ageRange: $row['age_range'],
            state: $row['state'],
            price: (int) $row['price'],
            weight: (int) $row['weight'],
            barcode: $row['barcode'] ?? null,
            createdAt: $row['created_at'] ?? null,
        );
    }
}
