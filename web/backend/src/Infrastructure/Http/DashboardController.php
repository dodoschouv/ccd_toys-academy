<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DashboardController
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function stats(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $stock = (int) $this->pdo->query('SELECT COUNT(*) FROM article')->fetchColumn();
        $subscribersCount = (int) $this->pdo->query('SELECT COUNT(*) FROM subscriber')->fetchColumn();
        $avgScoreRow = $this->pdo->query('SELECT COALESCE(AVG(score), 0) FROM box')->fetchColumn();
        $averageScore = round((float) $avgScoreRow, 1);

        $payload = [
            'stock' => $stock,
            'subscribers_count' => $subscribersCount,
            'average_score' => $averageScore,
        ];
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
