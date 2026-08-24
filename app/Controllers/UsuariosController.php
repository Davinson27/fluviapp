<?php
// =======================================================
// Controlador: UsuariosController (Gestión de Usuarios y Roles)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Usuario.php';

class UsuariosController extends Controller {
    private Usuario $usuarioModel;

    public function __construct() {
        AuthHelper::requireRoles(['admin']);
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        $usuarios = $this->usuarioModel->all('id ASC');
        $this->render('usuarios/index', [
            'pageTitle' => 'Gestión de Usuarios y Permisos - ' . APP_NAME,
            'usuarios'  => $usuarios
        ]);
    }

    public function create(): void {
        $this->render('usuarios/create', [
            'pageTitle' => 'Registrar Nuevo Usuario - ' . APP_NAME
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? 'taquilla';
        $estado = $_POST['estado'] ?? 'activo';
        $telefono = trim($_POST['telefono'] ?? '');

        if (empty($nombre) || empty($email) || empty($password)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los campos obligatorios.');
            $this->redirect('/usuarios/crear');
        }

        if ($this->usuarioModel->emailExists($email)) {
            SessionHelper::setFlash('danger', 'El correo electrónico ya se encuentra registrado por otro usuario.');
            $this->redirect('/usuarios/crear');
        }

        try {
            $this->usuarioModel->create([
                'nombre'   => $nombre,
                'email'    => $email,
                'password' => $password,
                'rol'      => $rol,
                'estado'   => $estado,
                'telefono' => $telefono
            ]);
            SessionHelper::setFlash('success', 'Usuario creado exitosamente.');
            $this->redirect('/usuarios');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al crear el usuario: ' . $e->getMessage());
            $this->redirect('/usuarios/crear');
        }
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            SessionHelper::setFlash('danger', 'El usuario solicitado no existe.');
            $this->redirect('/usuarios');
        }

        $this->render('usuarios/edit', [
            'pageTitle' => 'Editar Usuario - ' . APP_NAME,
            'usuario'   => $usuario
        ]);
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }

        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? 'taquilla';
        $estado = $_POST['estado'] ?? 'activo';
        $telefono = trim($_POST['telefono'] ?? '');

        if ($id <= 0 || empty($nombre) || empty($email)) {
            SessionHelper::setFlash('danger', 'Datos incompletos para actualizar el usuario.');
            $this->redirect('/usuarios');
        }

        if ($this->usuarioModel->emailExists($email, $id)) {
            SessionHelper::setFlash('danger', 'El correo electrónico ya está registrado en otra cuenta.');
            $this->redirect('/usuarios/editar?id=' . $id);
        }

        try {
            $updateData = [
                'nombre'   => $nombre,
                'email'    => $email,
                'rol'      => $rol,
                'estado'   => $estado,
                'telefono' => $telefono
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            $this->usuarioModel->update($id, $updateData);
            SessionHelper::setFlash('success', 'Usuario actualizado correctamente.');
            $this->redirect('/usuarios');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al actualizar el usuario: ' . $e->getMessage());
            $this->redirect('/usuarios/editar?id=' . $id);
        }
    }

    public function delete(): void {
        $id = (int)($_POST['id'] ?? 0);
        $currentUser = AuthHelper::user();

        if ($id === (int)$currentUser['id']) {
            SessionHelper::setFlash('danger', 'No puedes eliminar tu propia cuenta de usuario activa.');
            $this->redirect('/usuarios');
        }

        if ($id > 0) {
            $this->usuarioModel->delete($id);
            SessionHelper::setFlash('success', 'Usuario eliminado correctamente.');
        }

        $this->redirect('/usuarios');
    }
}
