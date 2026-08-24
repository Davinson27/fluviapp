<?php
// =======================================================
// Controlador: AuthController (Login, Logout)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Usuario.php';

class AuthController extends Controller {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function showLogin(): void {
        if (AuthHelper::check()) {
            $this->redirect('/dashboard');
        }
        $this->renderSingle('auth/login', [
            'pageTitle' => 'Iniciar Sesión - ' . APP_NAME
        ]);
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            SessionHelper::setFlash('danger', 'Por favor ingrese su correo y contraseña.');
            $this->redirect('/login');
        }

        $user = $this->usuarioModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            SessionHelper::setFlash('danger', 'Credenciales incorrectas. Verifique su correo y contraseña.');
            $this->redirect('/login');
        }

        if ($user['estado'] !== 'activo') {
            SessionHelper::setFlash('warning', 'Su cuenta de usuario se encuentra inactiva. Contacte al administrador.');
            $this->redirect('/login');
        }

        AuthHelper::login($user);
        SessionHelper::setFlash('success', '¡Bienvenido al sistema FluviApp, ' . htmlspecialchars($user['nombre']) . '!');
        $this->redirect('/dashboard');
    }

    public function logout(): void {
        AuthHelper::logout();
        SessionHelper::setFlash('info', 'Ha cerrado sesión correctamente.');
        $this->redirect('/login');
    }
}
