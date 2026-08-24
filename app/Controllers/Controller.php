<?php
// =======================================================
// Controlador Base con Motor de Vistas y Redirección
// =======================================================

abstract class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("Error: La vista <code>{$view}</code> no fue encontrada en <code>{$viewFile}</code>.");
        }

        require_once ROOT_PATH . '/views/layouts/header.php';
        require_once ROOT_PATH . '/views/layouts/sidebar.php';
        require_once $viewFile;
        require_once ROOT_PATH . '/views/layouts/footer.php';
    }

    protected function renderSingle(string $view, array $data = []): void {
        extract($data);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("Error: La vista <code>{$view}</code> no fue encontrada.");
        }

        require_once $viewFile;
    }

    protected function redirect(string $path): void {
        header("Location: " . BASE_URL . $path);
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_type($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
