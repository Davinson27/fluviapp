<?php
// =======================================================
// Controlador Base con Motor de Vistas y Redirección
// =======================================================

abstract class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("Error: La vista <code>" . htmlspecialchars($view) . "</code> no fue encontrada.");
        }

        require ROOT_PATH . '/views/layouts/header.php';
        require ROOT_PATH . '/views/layouts/sidebar.php';
        require $viewFile;
        require ROOT_PATH . '/views/layouts/footer.php';
    }

    protected function renderSingle(string $view, array $data = []): void {
        extract($data);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("Error: La vista no fue encontrada.");
        }

        require $viewFile;
    }

    protected function redirect(string $path): void {
        header("Location: " . BASE_URL . $path);
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function userErrorMessage(Throwable $e, string $fallback): string {
        return APP_DEBUG ? ($fallback . ' ' . $e->getMessage()) : $fallback;
    }

    protected function sanitizePago(string $metodo): string {
        return in_array($metodo, METODOS_PAGO, true) ? $metodo : 'efectivo';
    }

    protected function sanitizeRol(string $rol, string $default = 'taquilla'): string {
        return in_array($rol, ROLES_SISTEMA, true) ? $rol : $default;
    }
}
