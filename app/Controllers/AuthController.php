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
            if (AuthHelper::isCliente()) {
                $this->redirect('/portal');
            } else {
                $this->redirect('/dashboard');
            }
        }
        $this->renderSingle('auth/login', [
            'pageTitle' => 'Iniciar Sesión - ' . APP_NAME
        ]);
    }

    public function showRegister(): void {
        if (AuthHelper::check()) {
            $this->redirect('/portal');
        }
        $this->renderSingle('auth/registro', [
            'pageTitle' => 'Crear Cuenta de Pasajero - ' . APP_NAME
        ]);
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/registro');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($nombre) || empty($documento) || empty($email) || empty($password)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los campos obligatorios.');
            $this->redirect('/registro');
        }

        if ($password !== $passwordConfirm) {
            SessionHelper::setFlash('danger', 'Las contraseñas ingresadas no coinciden.');
            $this->redirect('/registro');
        }

        if (strlen($password) < 6) {
            SessionHelper::setFlash('danger', 'La contraseña debe tener al menos 6 caracteres.');
            $this->redirect('/registro');
        }

        if ($this->usuarioModel->emailExists($email)) {
            SessionHelper::setFlash('danger', 'El correo electrónico ya se encuentra registrado. Inicie sesión.');
            $this->redirect('/login');
        }

        try {
            $userId = $this->usuarioModel->create([
                'nombre'    => $nombre,
                'email'     => $email,
                'documento' => $documento,
                'password'  => $password,
                'rol'       => 'cliente',
                'estado'    => 'activo',
                'telefono'  => $telefono
            ]);

            $newUser = $this->usuarioModel->find($userId);
            AuthHelper::login($newUser);

            SessionHelper::setFlash('success', '¡Cuenta creada con éxito! Bienvenido a FluviApp, ' . htmlspecialchars($nombre) . '.');
            $this->redirect('/portal');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al registrar la cuenta: ' . $e->getMessage());
            $this->redirect('/registro');
        }
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
        SessionHelper::setFlash('success', '¡Bienvenido a FluviApp, ' . htmlspecialchars($user['nombre']) . '!');

        if ($user['rol'] === 'cliente') {
            $this->redirect('/portal');
        } else {
            $this->redirect('/dashboard');
        }
    }

    public function logout(): void {
        AuthHelper::logout();
        SessionHelper::setFlash('info', 'Ha cerrado sesión correctamente.');
        $this->redirect('/login');
    }
}
