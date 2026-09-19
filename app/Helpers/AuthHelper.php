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
                'id'           => $_SESSION['user_id'],
                'nombre'       => $_SESSION['user_name'] ?? '',
                'email'        => $_SESSION['user_email'] ?? '',
                'documento'    => $_SESSION['user_documento'] ?? '',
                'rol'          => $_SESSION['user_rol'] ?? 'cliente',
                'telefono'     => $_SESSION['user_telefono'] ?? '',
                'departamento' => $_SESSION['user_departamento'] ?? '',
                'genero'       => $_SESSION['user_genero'] ?? 'masculino',
                'foto'         => $_SESSION['user_foto'] ?? null
            ];
        }
        return null;
    }

    public static function isCliente(): bool {
        $u = self::user();
        return ($u['rol'] ?? '') === 'cliente';
    }

    public static function isGeneralAdmin(): bool {
        $u = self::user();
        return $u !== null && $u['rol'] === 'admin' && empty($u['departamento']);
    }

    public static function isDepartmentalAdmin(): bool {
        $u = self::user();
        return $u !== null && $u['rol'] === 'admin' && !empty($u['departamento']);
    }

    public static function getUserDepartment(): ?string {
        $u = self::user();
        return (!empty($u['departamento'])) ? $u['departamento'] : null;
    }

    public static function getDepartmentFilter(): ?string {
        $u = self::user();
        if (!$u) return null;
        // Administrador General sin departamento asignado tiene alcance nacional completo
        if ($u['rol'] === 'admin' && empty($u['departamento'])) {
            return null;
        }
        // Administradores departamentales, operadores, taquillas y clientes con departamento asignado
        if (!empty($u['departamento'])) {
            return $u['departamento'];
        }
        return null;
    }

    public static function isStaff(): bool {
        $u = self::user();
        return $u !== null && in_array($u['rol'], ROLES_STAFF, true);
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
        if (!$user || !in_array($user['rol'], $allowedRoles, true)) {
            SessionHelper::setFlash('danger', 'No tiene permisos suficientes para acceder a este módulo.');
            $dest = self::isCliente() ? '/portal' : '/dashboard';
            header('Location: ' . BASE_URL . $dest);
            exit;
        }
    }

    public static function requireRole(string|array $roleOrRoles): void {
        $roles = is_array($roleOrRoles) ? $roleOrRoles : [$roleOrRoles];
        self::requireRoles($roles);
    }

    public static function requireStaff(): void {
        self::requireRoles(ROLES_STAFF);
    }

    public static function requireCliente(): void {
        self::requireRoles(['cliente']);
    }

    public static function login(array $userData): void {
        SessionHelper::init();
        session_regenerate_id(true);
        $_SESSION['user_id']           = $userData['id'];
        $_SESSION['user_name']         = $userData['nombre'];
        $_SESSION['user_email']        = $userData['email'];
        $_SESSION['user_documento']    = $userData['documento'] ?? '';
        $_SESSION['user_rol']          = $userData['rol'];
        $_SESSION['user_telefono']     = $userData['telefono'] ?? '';
        $_SESSION['user_departamento'] = $userData['departamento'] ?? '';
        $_SESSION['user_genero']       = $userData['genero'] ?? 'masculino';
        $_SESSION['user_foto']         = $userData['foto'] ?? null;
    }

    public static function updateUserSession(array $updatedFields): void {
        SessionHelper::init();
        if (isset($updatedFields['nombre'])) $_SESSION['user_name'] = $updatedFields['nombre'];
        if (isset($updatedFields['email'])) $_SESSION['user_email'] = $updatedFields['email'];
        if (isset($updatedFields['telefono'])) $_SESSION['user_telefono'] = $updatedFields['telefono'];
        if (isset($updatedFields['departamento'])) $_SESSION['user_departamento'] = $updatedFields['departamento'];
        if (isset($updatedFields['genero'])) $_SESSION['user_genero'] = $updatedFields['genero'];
        if (array_key_exists('foto', $updatedFields)) $_SESSION['user_foto'] = $updatedFields['foto'];
    }

    public static function logout(): void {
        SessionHelper::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"] ?? '', $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
