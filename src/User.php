<?php
/** User entity + persistence. */
class User
{
    public const ROLE_LABELS = [
        'logistic'    => 'Logistic Officer',
        'coordinator' => 'Coordinator',
        'hod'         => 'Head of Department',
    ];

    public function __construct(
        public int $id,
        public string $fullName,
        public string $username,
        public string $email,
        public string $password,
        public string $role
    ) {}

    private static function fromRow(array $r): self
    {
        return new self((int) $r['id'], $r['full_name'], $r['username'], $r['email'], $r['password'], $r['role']);
    }

    public static function findByUsername(string $username): ?self
    {
        $row = Database::fetchOne('SELECT * FROM users WHERE username = ? LIMIT ?', [$username, 1]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findById(int $id): ?self
    {
        $row = Database::fetchOne('SELECT * FROM users WHERE id = ? LIMIT ?', [$id, 1]);
        return $row ? self::fromRow($row) : null;
    }

    /** Returns an error message, or null on success. */
    public static function changePassword(int $id, string $current, string $new, string $confirm): ?string
    {
        $user = self::findById($id);
        if (!$user || !password_verify($current, $user->password)) {
            return 'Your current password is incorrect.';
        }
        if (strlen($new) < 8) {
            return 'The new password must be at least 8 characters long.';
        }
        if (!preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            return 'Use at least one letter and one number in the new password.';
        }
        if ($new !== $confirm) {
            return 'The new password and its confirmation do not match.';
        }
        if (password_verify($new, $user->password)) {
            return 'Choose a password different from the current one.';
        }
        Database::run('UPDATE users SET password = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $id]);
        return null;
    }
}
