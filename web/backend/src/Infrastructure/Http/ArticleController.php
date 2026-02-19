<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\CreateArticle;
use ToysAcademy\Application\DeleteArticle;
use ToysAcademy\Application\ListArticles;
use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Application\UpdateArticle;
use ToysAcademy\Domain\Article;

final class ArticleController
{
    public function __construct(
        private ListArticles $listArticles,
        private ArticleRepository $articleRepository,
        private CreateArticle $createArticle,
        private UpdateArticle $updateArticle,
        private DeleteArticle $deleteArticle,
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 10;
        $category = isset($params['category']) && (string) $params['category'] !== '' ? (string) $params['category'] : null;
        $ageRange = isset($params['age_range']) && (string) $params['age_range'] !== '' ? (string) $params['age_range'] : null;
        $state = isset($params['state']) && (string) $params['state'] !== '' ? (string) $params['state'] : null;

        $result = ($this->listArticles)($page, $perPage, $category, $ageRange, $state);
        $data = [
            'data' => array_map(fn ($a) => $this->articleToArray($a), $result['items']),
            'total' => $result['total'],
            'page' => $page,
            'per_page' => $perPage,
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $article = $this->articleRepository->getById($args['id']);
        if ($article === null) {
            $response->getBody()->write(json_encode(['error' => 'Article non trouvé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($this->articleToArray($article)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody() ?? [];
        try {
            ($this->createArticle)(
                id: (string) ($body['id'] ?? ''),
                designation: (string) ($body['designation'] ?? ''),
                category: (string) ($body['category'] ?? ''),
                ageRange: (string) ($body['age_range'] ?? ''),
                state: (string) ($body['state'] ?? ''),
                price: (int) ($body['price'] ?? 0),
                weight: (int) ($body['weight'] ?? 0),
                barcode: isset($body['barcode']) && $body['barcode'] !== '' ? (string) $body['barcode'] : null,
            );
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody() ?? [];
        try {
            ($this->updateArticle)(
                id: $args['id'],
                designation: (string) ($body['designation'] ?? ''),
                category: (string) ($body['category'] ?? ''),
                ageRange: (string) ($body['age_range'] ?? ''),
                state: (string) ($body['state'] ?? ''),
                price: (int) ($body['price'] ?? 0),
                weight: (int) ($body['weight'] ?? 0),
                barcode: isset($body['barcode']) && $body['barcode'] !== '' ? (string) $body['barcode'] : null,
            );
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            ($this->deleteArticle)($args['id']);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $response->withStatus(204);
    }

    private function articleToArray(Article $a): array
    {
        return [
            'id' => $a->id,
            'designation' => $a->designation,
            'category' => $a->category,
            'age_range' => $a->ageRange,
            'state' => $a->state,
            'price' => $a->price,
            'weight' => $a->weight,
            'barcode' => $a->barcode,
        ];
    }
}
