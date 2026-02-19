<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Persistence;

use PDO;
use ToysAcademy\Application\Port\UserRepository;
use ToysAcademy\Domain\User;

final class PdoUserRepository implements UserRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToUser($row) : null;
    }

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToUser($row) : null;
    }

    public function save(User $user): void
    {
        if ($user->id === 0) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO user (email, first_name, last_name, password_hash, role, subscriber_id) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $user->email,
                $user->firstName,
                $user->lastName,
                $user->passwordHash ?? null,
                $user->role,
                $user->subscriberId,
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE user SET first_name = ?, last_name = ?, password_hash = ?, role = ?, subscriber_id = ? WHERE id = ?'
            );
            $stmt->execute([
                $user->firstName,
                $user->lastName,
                $user->passwordHash ?? null,
                $user->role,
                $user->subscriberId,
                $user->id,
            ]);
        }
    }

    private function rowToUser(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            email: $row['email'],
            firstName: $row['first_name'],
            lastName: $row['last_name'],
            passwordHash: $row['password_hash'] ?? null,
            role: $row['role'] ?? 'subscriber',
            subscriberId: $row['subscriber_id'] ?? null,
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
        );
    }
}
