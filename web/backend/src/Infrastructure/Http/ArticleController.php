<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\ListArticles;

final class ArticleController
{
    public function __construct(
        private ListArticles $listArticles,
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $articles = ($this->listArticles)();
        $data = array_map(fn ($a) => [
            'id' => $a->id,
            'designation' => $a->designation,
            'category' => $a->category,
            'age_range' => $a->ageRange,
            'state' => $a->state,
            'price' => $a->price,
            'weight' => $a->weight,
            'barcode' => $a->barcode,
        ], $articles);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
