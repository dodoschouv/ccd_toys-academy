<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * W18 — Historique global (admin) : campagnes avec synthèse (nb box validées, nb articles distribués, score moyen).
 */
final class HistoryController
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sql = "
            SELECT
                c.id,
                c.max_weight_per_box,
                c.created_at,
                COUNT(DISTINCT b.id) AS boxes_count,
                COALESCE(ROUND(AVG(CASE WHEN b.status = 'validated' THEN b.score END), 1), 0) AS average_score,
                (SELECT COUNT(*) FROM box_article ba
                 INNER JOIN box b2 ON b2.id = ba.box_id
                 WHERE b2.campaign_id = c.id AND b2.status = 'validated') AS articles_count
            FROM campaign c
            LEFT JOIN box b ON b.campaign_id = c.id AND b.status = 'validated'
            GROUP BY c.id, c.max_weight_per_box, c.created_at
            ORDER BY c.created_at DESC
        ";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => (int) $row['id'],
                'max_weight_per_box' => (int) $row['max_weight_per_box'],
                'created_at' => $row['created_at'],
                'boxes_count' => (int) $row['boxes_count'],
                'articles_count' => (int) $row['articles_count'],
                'average_score' => (float) $row['average_score'],
            ];
        }
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
