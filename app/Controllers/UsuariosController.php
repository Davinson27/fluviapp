<?php
// =======================================================
// Controlador: UsuariosController (Gestión de Usuarios y Roles)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Usuario.php';
require_once ROOT_PATH . '/app/Models/Muelle.php';

class UsuariosController extends Controller {
    private Usuario $usuarioModel;
    private Muelle $muelleModel;

    public function __construct() {
        AuthHelper::requireRoles(['admin']);
        $this->usuarioModel = new Usuario();
        $this->muelleModel = new Muelle();
    }

    public function index(): void {
        $isGeneralAdmin = AuthHelper::isGeneralAdmin();
        $deptScope = AuthHelper::getUserDepartment();

        if ($isGeneralAdmin) {
            $usuarios = $this->usuarioModel->all('id ASC');
        } else {
            // Administrador Departamental: sólo ve los usuarios asignados a su departamento
            $usuarios = $this->usuarioModel->allWithFilter($deptScope);
        }

        $this->render('usuarios/index', [
            'pageTitle'      => 'Gestión de Usuarios y Roles - ' . APP_NAME,
            'usuarios'       => $usuarios,
            'isGeneralAdmin' => $isGeneralAdmin,
            'deptScope'      => $deptScope
        ]);
    }

    public function create(): void {
        $isGeneralAdmin = AuthHelper::isGeneralAdmin();
        $deptScope = AuthHelper::getUserDepartment();

        if ($isGeneralAdmin) {
            $departamentos = $this->muelleModel->getDepartamentos();
        } else {
            $departamentos = [['departamento' => $deptScope]];
        }

        $this->render('usuarios/create', [
            'pageTitle'      => 'Registrar Nuevo Usuario - ' . APP_NAME,
            'departamentos'  => $departamentos,
            'isGeneralAdmin' => $isGeneralAdmin,
            'deptScope'      => $deptScope
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }

        $isGeneralAdmin = AuthHelper::isGeneralAdmin();
        $deptScope = AuthHelper::getUserDepartment();

        $nombre = trim($_POST['nombre'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? 'taquilla';
        $estado = $_POST['estado'] ?? 'activo';
        $telefono = trim($_POST['telefono'] ?? '');
        $genero = in_array($_POST['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $_POST['genero'] : 'masculino';

        // Si es General Admin, puede asignar cualquier departamento o dejarlo vacío para Administrador General Nacional
        if ($isGeneralAdmin) {
            $departamento = trim($_POST['departamento'] ?? '');
        } else {
            // Si es Admin Departamental, solo puede crear personal/usuarios para su propio departamento
            $departamento = $deptScope;
        }

        if (empty($nombre) || empty($email) || empty($password)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los campos obligatorios.');
            $this->redirect('/usuarios/crear');
        }

        if ($this->usuarioModel->emailExists($email)) {
            SessionHelper::setFlash('danger', 'El correo electrónico ya se encuentra registrado por otro usuario.');
            $this->redirect('/usuarios/crear');
        }

        try {
            $fotoPath = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['foto'];
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (in_array($mime, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
                    $ext = match($mime) {
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp',
                        'image/gif'  => 'gif',
                        default      => 'jpg'
                    };
                    $uploadDir = ROOT_PATH . '/public/uploads/perfiles';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0777, true);
                    }
                    $filename = 'user_new_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $fotoPath = 'uploads/perfiles/' . $filename;
                    }
                }
            }

            $this->usuarioModel->create([
                'nombre'       => $nombre,
                'email'        => $email,
                'password'     => $password,
                'rol'          => $rol,
                'estado'       => $estado,
                'telefono'     => $telefono,
                'departamento' => !empty($departamento) ? $departamento : null,
                'genero'       => $genero,
                'foto'         => $fotoPath
            ]);

            $msg = 'Usuario creado exitosamente.';
            if ($rol === 'admin') {
                $msg .= !empty($departamento) 
                    ? " Asignado como Administrador Departamental de {$departamento}."
                    : " Asignado como Administrador General Nacional.";
            }
            SessionHelper::setFlash('success', $msg);
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

        $isGeneralAdmin = AuthHelper::isGeneralAdmin();
        $deptScope = AuthHelper::getUserDepartment();

        // Si es admin departamental, solo puede editar usuarios de su departamento
        if (!$isGeneralAdmin && ($usuario['departamento'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para editar usuarios de otros departamentos.');
            $this->redirect('/usuarios');
        }

        if ($isGeneralAdmin) {
            $departamentos = $this->muelleModel->getDepartamentos();
        } else {
            $departamentos = [['departamento' => $deptScope]];
        }

        $this->render('usuarios/edit', [
            'pageTitle'      => 'Editar Usuario - ' . APP_NAME,
            'usuario'        => $usuario,
            'departamentos'  => $departamentos,
            'isGeneralAdmin' => $isGeneralAdmin,
            'deptScope'      => $deptScope
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
        $genero = in_array($_POST['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $_POST['genero'] : 'masculino';

        $isGeneralAdmin = AuthHelper::isGeneralAdmin();
        $deptScope = AuthHelper::getUserDepartment();

        $targetUser = $this->usuarioModel->find($id);
        if (!$targetUser) {
            SessionHelper::setFlash('danger', 'El usuario especificado no existe.');
            $this->redirect('/usuarios');
        }

        // Si es admin departamental, verificar que pertenezca a su jurisdicción
        if (!$isGeneralAdmin && ($targetUser['departamento'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para modificar usuarios de otros departamentos.');
            $this->redirect('/usuarios');
        }

        if ($isGeneralAdmin) {
            $departamento = trim($_POST['departamento'] ?? '');
        } else {
            $departamento = $deptScope;
        }

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
                'nombre'       => $nombre,
                'email'        => $email,
                'rol'          => $rol,
                'estado'       => $estado,
                'telefono'     => $telefono,
                'departamento' => !empty($departamento) ? $departamento : null,
                'genero'       => $genero
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            // Manejo de foto de perfil
            $fotoPath = $targetUser['foto'] ?? null;
            if (!empty($_POST['eliminar_foto']) && !empty($fotoPath)) {
                $fullOldPath = ROOT_PATH . '/public/' . ltrim($fotoPath, '/');
                if (file_exists($fullOldPath)) {
                    @unlink($fullOldPath);
                }
                $fotoPath = null;
                $updateData['foto'] = null;
            }

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['foto'];
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (in_array($mime, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
                    $ext = match($mime) {
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp',
                        'image/gif'  => 'gif',
                        default      => 'jpg'
                    };
                    $uploadDir = ROOT_PATH . '/public/uploads/perfiles';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0777, true);
                    }
                    $filename = 'user_' . $id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        if (!empty($targetUser['foto'])) {
                            $oldFile = ROOT_PATH . '/public/' . ltrim($targetUser['foto'], '/');
                            if (file_exists($oldFile) && realpath($oldFile) !== realpath($dest)) {
                                @unlink($oldFile);
                            }
                        }
                        $fotoPath = 'uploads/perfiles/' . $filename;
                        $updateData['foto'] = $fotoPath;
                    }
                }
            }

            $this->usuarioModel->update($id, $updateData);

            // Si el usuario actualizado es el usuario logueado actualmente, actualizar su sesión
            $currentUser = AuthHelper::user();
            if ($currentUser && (int)$currentUser['id'] === $id) {
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_rol'] = $rol;
                $_SESSION['user_departamento'] = !empty($departamento) ? $departamento : '';
                $_SESSION['user_genero'] = $genero;
                if (array_key_exists('foto', $updateData)) {
                    $_SESSION['user_foto'] = $updateData['foto'];
                }
            }

            $msg = 'Usuario actualizado correctamente.';
            if ($rol === 'admin') {
                $msg .= !empty($departamento)
                    ? " Asignado como Administrador Departamental de {$departamento}."
                    : " Asignado como Administrador General Nacional.";
            }
            SessionHelper::setFlash('success', $msg);
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

        $isGeneralAdmin = AuthHelper::isGeneralAdmin();
        $deptScope = AuthHelper::getUserDepartment();

        $targetUser = $this->usuarioModel->find($id);
        if (!$targetUser) {
            SessionHelper::setFlash('danger', 'El usuario no existe.');
            $this->redirect('/usuarios');
        }

        if (!$isGeneralAdmin && ($targetUser['departamento'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para eliminar usuarios de otros departamentos.');
            $this->redirect('/usuarios');
        }

        if ($id > 0) {
            $this->usuarioModel->delete($id);
            SessionHelper::setFlash('success', 'Usuario eliminado correctamente.');
        }

        $this->redirect('/usuarios');
    }
}
