<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Application\Port\UserRepository;
use ToysAcademy\Application\SaveSubscriber;
use ToysAcademy\Domain\User;

final class AuthController
{
    private const JWT_EXPIRY_DAYS = 7;

    public function __construct(
        private UserRepository $userRepository,
        private SubscriberRepository $subscriberRepository,
        private SaveSubscriber $saveSubscriber,
    ) {
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody() ?? [];
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($email === '' || $password === '') {
            $response->getBody()->write(json_encode(['error' => 'Email et mot de passe requis']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $user = $this->userRepository->getByEmail($email);
        if ($user === null || $user->passwordHash === null) {
            $response->getBody()->write(json_encode(['error' => 'Identifiants incorrects']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        if (!password_verify($password, $user->passwordHash)) {
            $response->getBody()->write(json_encode(['error' => 'Identifiants incorrects']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = $this->issueToken($user);
        $response->getBody()->write(json_encode([
            'token' => $token,
            'user' => $this->userToArray($user),
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody() ?? [];
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        $firstName = trim((string) ($body['first_name'] ?? ''));
        $lastName = trim((string) ($body['last_name'] ?? ''));
        $childAgeRange = (string) ($body['child_age_range'] ?? '');
        $preferences = $body['preferences'] ?? [];
        if (!is_array($preferences)) {
            $preferences = [];
        }

        if ($email === '' || $password === '') {
            $response->getBody()->write(json_encode(['error' => 'Email et mot de passe requis']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        if (strlen($password) < 6) {
            $response->getBody()->write(json_encode(['error' => 'Le mot de passe doit faire au moins 6 caractères']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        if ($firstName === '' || $lastName === '') {
            $response->getBody()->write(json_encode(['error' => 'Nom et prénom requis']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        if ($childAgeRange === '' || !in_array($childAgeRange, ['BB', 'PE', 'EN', 'AD'], true)) {
            $response->getBody()->write(json_encode(['error' => 'Choisissez une tranche d\'âge valide (enfant).']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $existingUser = $this->userRepository->getByEmail($email);
        if ($existingUser !== null) {
            $response->getBody()->write(json_encode(['error' => 'Un compte existe déjà avec cet email. Connectez-vous.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            ($this->saveSubscriber)($lastName, $firstName, $email, $childAgeRange, array_values($preferences));
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $subscriber = $this->subscriberRepository->findByEmail($email);
        if ($subscriber === null) {
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la création du profil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        try {
            $user = new User(
                id: 0,
                email: $email,
                firstName: $firstName,
                lastName: $lastName,
                passwordHash: password_hash($password, PASSWORD_DEFAULT),
                role: 'subscriber',
                subscriberId: $subscriber->id,
            );
            $this->userRepository->save($user);
        } catch (\Throwable $e) {
            error_log('AuthController::register user save failed: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la création du compte']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $savedUser = $this->userRepository->getByEmail($email);
        if ($savedUser === null) {
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la création du compte']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        try {
            $token = $this->issueToken($savedUser);
        } catch (\Throwable $e) {
            error_log('AuthController::register JWT issue failed: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Erreur serveur (JWT). Réessayez.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode([
            'token' => $token,
            'user' => $this->userToArray($savedUser),
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function me(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $this->resolveUserFromToken($request);
        if ($user === null) {
            $response->getBody()->write(json_encode(['error' => 'Non authentifié']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $data = $this->userToArray($user);
        if ($user->subscriberId !== null) {
            $subscriber = $this->subscriberRepository->getById($user->subscriberId);
            if ($subscriber !== null) {
                $data['subscriber'] = [
                    'id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'first_name' => $subscriber->firstName,
                    'last_name' => $subscriber->lastName,
                    'child_age_range' => $subscriber->childAgeRange,
                    'preferences' => $subscriber->preferences,
                ];
            }
        }
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function issueToken(User $user): string
    {
        // firebase/php-jwt v7 exige une clé HS256 d'au moins 32 octets
        $secret = $_ENV['JWT_SECRET'] ?? 'dev-secret-toys-academy-change-in-production-32bytes';
        $now = time();
        $payload = [
            'sub' => (string) $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'subscriber_id' => $user->subscriberId,
            'iat' => $now,
            'exp' => $now + (self::JWT_EXPIRY_DAYS * 86400),
        ];
        return JWT::encode($payload, $secret, 'HS256');
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
        $secret = $_ENV['JWT_SECRET'] ?? 'dev-secret-toys-academy-change-in-production-32bytes';
        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Throwable) {
            return null;
        }
        $userId = (int) $decoded->sub;
        return $this->userRepository->getById($userId);
    }

    private function userToArray(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'role' => $user->role,
            'subscriber_id' => $user->subscriberId,
        ];
    }
}
