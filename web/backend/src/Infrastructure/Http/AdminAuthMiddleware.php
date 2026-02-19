<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use ToysAcademy\Application\Port\UserRepository;
use ToysAcademy\Domain\User;

final class AdminAuthMiddleware implements MiddlewareInterface
{
    private const JWT_SECRET_ENV = 'dev-secret-toys-academy-change-in-production-32bytes';

    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->resolveUserFromToken($request);
        if ($user === null || $user->role !== 'admin') {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Accès réservé aux administrateurs']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
        return $handler->handle($request);
    }

    private function resolveUserFromToken(ServerRequestInterface $request): ?User
    {
        $auth = $request->getHeaderLine('Authorization');
        if ($auth === '' || !str_starts_with(strtolower($auth), 'bearer ')) {
            return null;
        }
        $token = trim(substr($auth, 7));
        if ($token === '') {
            return null;
        }
        $secret = $_ENV['JWT_SECRET'] ?? self::JWT_SECRET_ENV;
        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Throwable) {
            return null;
        }
        $userId = (int) $decoded->sub;
        return $this->userRepository->getById($userId);
    }
}
