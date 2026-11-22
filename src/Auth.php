<?php
/** Session, authentication, role guard, CSRF and flash messages. */
class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => !empty($_SERVER['HTTPS']),
            ]);
            session_start();
        }
    }

    public static function attempt(string $username, string $password): bool
    {
        $user = User::findByUsername(trim($username));
        // Always run password_verify so unknown usernames take similar time.
        $hash = $user ? $user->password : password_hash('dummy-password', PASSWORD_DEFAULT);
        $ok   = password_verify($password, $hash);
        if (!$user || !$ok) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'       => $user->id,
            'name'     => $user->fullName,
            'username' => $user->username,
            'role'     => $user->role,
        ];
        return true;
    }

    public static function check(): bool { return isset($_SESSION['user']); }
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function id(): int { return (int) ($_SESSION['user']['id'] ?? 0); }
    public static function role(): string { return $_SESSION['user']['role'] ?? ''; }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('login.php');
        }
    }

    /** Allow only the given roles; others are sent back to their dashboard. */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        if (!in_array(self::role(), $roles, true)) {
            self::flash('error', 'You do not have access to that page.');
            redirect('dashboard.php');
        }
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /* ---------- CSRF ---------- */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::csrfToken() . '">';
    }

    public static function verifyCsrf(): void
    {
        $sent = $_POST['csrf'] ?? '';
        if (!is_string($sent) || !hash_equals(self::csrfToken(), $sent)) {
            http_response_code(419);
            exit('Your session expired. Go back, refresh the page and try again.');
        }
    }

    /* ---------- Flash messages ---------- */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlash(): ?array
    {
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $f;
    }
}
