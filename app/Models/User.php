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

    /**
     * Create a new (unverified) user with a hashed password.
     * Public self-registration always starts as unverified — call
     * setVerificationCode() right after this to issue the OTP code.
     */
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

    public function isEmailVerified(array $user): bool
    {
        return !empty($user['email_verified_at']);
    }

    /**
     * Issue (or reissue) a 6-digit verification code good for 15 minutes.
     * Returns the raw code to embed in the email.
     */
    public function setVerificationCode(int $userId): string
    {
        $code = (string) random_int(100000, 999999);

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET verification_token = :code,
                 verification_expires_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
             WHERE id = :id"
        );
        $stmt->execute(['code' => $code, 'id' => $userId]);

        return $code;
    }

    public static function normalizeVerificationCode(string $code): string
    {
        $clean = preg_replace('/\D+/', '', trim((string) $code));

        return is_string($clean) ? $clean : '';
    }

    public function findByEmailAndValidCode(string $email, string $code): array|false
    {
        $normalizedCode = self::normalizeVerificationCode($code);

        if ($normalizedCode === '' || !preg_match('/^\d{6}$/', $normalizedCode)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE email = :email
               AND verification_token = :code
               AND verification_expires_at >= NOW()
             LIMIT 1"
        );
        $stmt->execute(['email' => $email, 'code' => $normalizedCode]);
        return $stmt->fetch();
    }

    public function markEmailVerified(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET email_verified_at = NOW(),
                 verification_token = NULL,
                 verification_expires_at = NULL
             WHERE id = :id"
        );
        return $stmt->execute(['id' => $userId]);
    }

    public function fullName(array $user): string
    {
        return trim($user['first_name'] . ' ' . $user['last_name']);
    }
}
