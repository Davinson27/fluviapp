<?php
// =======================================================
// Helper de Autenticación y Autorización por Roles
// =======================================================

class AuthHelper {
    public static function check(): bool {
        SessionHelper::init();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        SessionHelper::init();
        if (self::check()) {
            return [
                'id'       => $_SESSION['user_id'],
                'nombre'   => $_SESSION['user_name'] ?? '',
                'email'    => $_SESSION['user_email'] ?? '',
                'rol'      => $_SESSION['user_rol'] ?? 'taquilla',
                'telefono' => $_SESSION['user_telefono'] ?? ''
            ];
        }
        return null;
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            SessionHelper::setFlash('warning', 'Debe iniciar sesión para acceder al sistema.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireRoles(array $allowedRoles): void {
        self::requireAuth();
        $user = self::user();
        if (!$user || !in_array($user['rol'], $allowedRoles)) {
            SessionHelper::setFlash('danger', 'No tiene permisos suficientes para acceder a este módulo.');
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    public static function login(array $userData): void {
        SessionHelper::init();
        session_regenerate_id(true);
        $_SESSION['user_id']       = $userData['id'];
        $_SESSION['user_name']     = $userData['nombre'];
        $_SESSION['user_email']    = $userData['email'];
        $_SESSION['user_rol']      = $userData['rol'];
        $_SESSION['user_telefono'] = $userData['telefono'] ?? '';
    }

    public static function logout(): void {
        SessionHelper::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
