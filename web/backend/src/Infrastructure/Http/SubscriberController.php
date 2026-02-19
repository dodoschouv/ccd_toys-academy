<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\GetValidatedBoxesForSubscriberByEmail;
use ToysAcademy\Application\ListSubscribers;
use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Application\SaveSubscriber;
use ToysAcademy\Domain\Subscriber;

final class SubscriberController
{
    public function __construct(
        private SaveSubscriber $saveSubscriber,
        private ListSubscribers $listSubscribers,
        private SubscriberRepository $subscriberRepository,
        private GetValidatedBoxesForSubscriberByEmail $getValidatedBoxesForSubscriberByEmail,
    ) {
    }

    public function getByEmail(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $email = isset($params['email']) ? trim((string) $params['email']) : '';
        if ($email === '') {
            $response->getBody()->write(json_encode(['error' => 'Paramètre email requis']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $subscriber = $this->subscriberRepository->findByEmail($email);
        if ($subscriber === null) {
            $response->getBody()->write(json_encode(['error' => 'Aucun abonné avec cet email']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $data = [
            'id' => $subscriber->id,
            'first_name' => $subscriber->firstName,
            'last_name' => $subscriber->lastName,
            'email' => $subscriber->email,
            'child_age_range' => $subscriber->childAgeRange,
            'preferences' => $subscriber->preferences,
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $subscribers = ($this->listSubscribers)();
        $data = array_map(fn (Subscriber $s) => [
            'id' => $s->id,
            'first_name' => $s->firstName,
            'last_name' => $s->lastName,
            'email' => $s->email,
            'child_age_range' => $s->childAgeRange,
            'preferences' => $s->preferences,
        ], $subscribers);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
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

    /**
     * W9 — Consultation de sa box (validée) par l'abonné via email
     */
    public function getMyBox(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $email = isset($params['email']) ? trim((string) $params['email']) : '';
        try {
            $boxes = ($this->getValidatedBoxesForSubscriberByEmail)($email);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            if ($code === 400) {
                $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            throw $e;
        }
        $response->getBody()->write(json_encode($boxes));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
