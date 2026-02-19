<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\ValidateBox;

final class BoxController
{
    public function __construct(
        private ValidateBox $validateBox,
    ) {
    }

    public function validate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $boxId = (int) ($args['id'] ?? 0);
        if ($boxId <= 0) {
            $response->getBody()->write(json_encode(['error' => 'Box invalide']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            ($this->validateBox)($boxId);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 500;
            $statusCode = match ($code) {
                404 => 404,
                400 => 400,
                default => 500,
            };
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
        }

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
