<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\SaveSubscriber;

final class SubscriberController
{
    public function __construct(
        private SaveSubscriber $saveSubscriber,
    ) {
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody() ?? [];
        $preferences = $body['preferences'] ?? [];
        if (!is_array($preferences)) {
            $preferences = [];
        }
        try {
            ($this->saveSubscriber)(
                lastName: (string) ($body['last_name'] ?? ''),
                firstName: (string) ($body['first_name'] ?? ''),
                email: (string) ($body['email'] ?? ''),
                childAgeRange: (string) ($body['child_age_range'] ?? ''),
                preferences: array_values($preferences),
            );
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
