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

    /** @return array{items: Article[], total: int} */
    public function findPaginated(int $page, int $perPage, ?string $category = null, ?string $ageRange = null, ?string $state = null): array
    {
        $where = [];
        $params = [];
        if ($category !== null && $category !== '') {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        if ($ageRange !== null && $ageRange !== '') {
            $where[] = 'age_range = ?';
            $params[] = $ageRange;
        }
        if ($state !== null && $state !== '') {
            $where[] = 'state = ?';
            $params[] = $state;
        }
        $whereClause = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM article' . $whereClause);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = 'SELECT * FROM article' . $whereClause . ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $stmt = $this->pdo->prepare($sql);
        $i = 1;
        foreach ($params as $p) {
            $stmt->bindValue($i++, $p);
        }
        $offset = ($page - 1) * $perPage;
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $items = [];
        foreach ($rows as $row) {
            $items[] = $this->rowToArticle($row);
        }
        return ['items' => $items, 'total' => $total];
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

    public function update(Article $article): void
    {
        $sql = 'UPDATE article SET designation = ?, category = ?, age_range = ?, state = ?, price = ?, weight = ?, barcode = ?
                WHERE id = ?';
        $this->pdo->prepare($sql)->execute([
            $article->designation,
            $article->category,
            $article->ageRange,
            $article->state,
            $article->price,
            $article->weight,
            $article->barcode,
            $article->id,
        ]);
    }

    public function delete(string $id): void
    {
        $this->pdo->prepare('DELETE FROM article WHERE id = ?')->execute([$id]);
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
