<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function createUser(string $firstName, string $lastName, string $email, string $password, string $role = 'student'): int
    {
        return $this->insert([
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => $role,
        ]);
    }

    public function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }

    public function fullName(array $user): string
    {
        return trim($user['first_name'] . ' ' . $user['last_name']);
    }
}
