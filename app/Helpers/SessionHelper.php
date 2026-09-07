<?php
// =======================================================
// Helper de Sesión, Alertas Flash y Seguridad CSRF
// =======================================================

class SessionHelper {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function setFlash(string $type, string $message): void {
        self::init();
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    public static function getFlash(): ?array {
        self::init();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public static function generateCsrfToken(): string {
        self::init();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string {
        $token = htmlspecialchars(self::generateCsrfToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $token . '">';
    }

    public static function validateCsrfToken(?string $token): bool {
        self::init();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function requireCsrf(): void {
        $token = $_POST['_csrf'] ?? '';
        if (!empty($token)) {
            if (!self::validateCsrfToken($token)) {
                http_response_code(403);
                self::setFlash('danger', 'Solicitud rechazada por seguridad. Recargue la página e intente de nuevo.');
                $fallback = AuthHelper::check()
                    ? (AuthHelper::isCliente() ? '/portal' : '/dashboard')
                    : '/login';
                $ref = $_SERVER['HTTP_REFERER'] ?? '';
                if ($ref !== '' && str_starts_with($ref, BASE_URL)) {
                    header('Location: ' . $ref);
                    exit;
                }
                header('Location: ' . BASE_URL . $fallback);
                exit;
            }
        }
    }

    public static function loginAttemptsExceeded(int $max = 8, int $windowSeconds = 900): bool {
        self::init();
        $now = time();
        $attempts = $_SESSION['login_attempts'] ?? [];
        $attempts = array_values(array_filter($attempts, fn($t) => ($now - (int)$t) < $windowSeconds));
        $_SESSION['login_attempts'] = $attempts;
        return count($attempts) >= $max;
    }

    public static function recordLoginAttempt(): void {
        self::init();
        $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? [];
        $_SESSION['login_attempts'][] = time();
    }

    public static function clearLoginAttempts(): void {
        self::init();
        unset($_SESSION['login_attempts']);
    }
}
