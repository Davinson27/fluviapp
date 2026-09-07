<?php
// =======================================================
// Controlador: AuthController (Login, Logout)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Usuario.php';
require_once ROOT_PATH . '/app/Models/Muelle.php';

class AuthController extends Controller {
    private Usuario $usuarioModel;
    private Muelle $muelleModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->muelleModel = new Muelle();
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
        $departamentos = $this->muelleModel->getDepartamentos();
        $this->renderSingle('auth/registro', [
            'pageTitle'     => 'Crear Cuenta de Pasajero - ' . APP_NAME,
            'departamentos' => $departamentos
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
        $departamento = trim($_POST['departamento'] ?? '');
        $genero = in_array($_POST['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $_POST['genero'] : 'masculino';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($nombre) || empty($documento) || empty($email) || empty($password) || empty($departamento)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los campos obligatorios, incluyendo su departamento.');
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
                    $filename = 'user_reg_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $fotoPath = 'uploads/perfiles/' . $filename;
                    }
                }
            }

            $userId = $this->usuarioModel->create([
                'nombre'       => $nombre,
                'email'        => $email,
                'documento'    => $documento,
                'password'     => $password,
                'rol'          => 'cliente',
                'estado'       => 'activo',
                'telefono'     => $telefono,
                'departamento' => $departamento,
                'genero'       => $genero,
                'foto'         => $fotoPath
            ]);

            $newUser = $this->usuarioModel->find($userId);
            AuthHelper::login($newUser);

            SessionHelper::setFlash('success', '¡Cuenta creada con éxito! Bienvenido a FluviApp. Rutas activas configuradas para ' . htmlspecialchars($departamento) . '.');
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

    public function perfil(): void {
        AuthHelper::requireAuth();
        $userSession = AuthHelper::user();
        $usuario = $this->usuarioModel->find((int)$userSession['id']);

        if (!$usuario) {
            SessionHelper::setFlash('danger', 'Usuario no encontrado.');
            $this->redirect(AuthHelper::isCliente() ? '/portal' : '/dashboard');
        }

        $this->render('perfil/index', [
            'pageTitle' => 'Mi Perfil de Usuario - ' . APP_NAME,
            'usuario'   => $usuario
        ]);
    }

    public function actualizarPerfil(): void {
        AuthHelper::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/perfil');
        }

        $userSession = AuthHelper::user();
        $userId = (int)$userSession['id'];
        $usuario = $this->usuarioModel->find($userId);

        if (!$usuario) {
            SessionHelper::setFlash('danger', 'Usuario no encontrado.');
            $this->redirect('/login');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $telefono = trim($_POST['telefono'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $genero = in_array($_POST['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $_POST['genero'] : 'masculino';
        $password = $_POST['password'] ?? '';
        $eliminarFoto = !empty($_POST['eliminar_foto']);

        if (empty($nombre) || empty($email)) {
            SessionHelper::setFlash('danger', 'El nombre y correo electrónico son obligatorios.');
            $this->redirect('/perfil');
        }

        if ($this->usuarioModel->emailExists($email, $userId)) {
            SessionHelper::setFlash('danger', 'El correo ingresado ya pertenece a otra cuenta.');
            $this->redirect('/perfil');
        }

        $fotoPath = $usuario['foto'] ?? null;

        // Procesar eliminación de foto
        if ($eliminarFoto && !empty($fotoPath)) {
            $fullOldPath = ROOT_PATH . '/public/' . ltrim($fotoPath, '/');
            if (file_exists($fullOldPath)) {
                @unlink($fullOldPath);
            }
            $fotoPath = null;
        }

        // Procesar subida de nueva foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $maxBytes = 5 * 1024 * 1024; // 5 MB
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if ($file['size'] > $maxBytes) {
                SessionHelper::setFlash('danger', 'La imagen no debe superar los 5 MB de tamaño.');
                $this->redirect('/perfil');
            }

            if (!in_array($mimeType, $allowedTypes)) {
                SessionHelper::setFlash('danger', 'Formato de imagen no permitido. Solo se aceptan JPG, PNG, WEBP o GIF.');
                $this->redirect('/perfil');
            }

            $ext = match($mimeType) {
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

            $filename = 'user_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destination = $uploadDir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Eliminar foto anterior si existía
                if (!empty($usuario['foto'])) {
                    $oldPath = ROOT_PATH . '/public/' . ltrim($usuario['foto'], '/');
                    if (file_exists($oldPath) && realpath($oldPath) !== realpath($destination)) {
                        @unlink($oldPath);
                    }
                }
                $fotoPath = 'uploads/perfiles/' . $filename;
            } else {
                SessionHelper::setFlash('warning', 'No se pudo guardar la fotografía en el servidor.');
            }
        }

        $dataToUpdate = [
            'nombre'    => $nombre,
            'email'     => $email,
            'telefono'  => $telefono,
            'documento' => $documento,
            'genero'    => $genero,
            'foto'      => $fotoPath
        ];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                SessionHelper::setFlash('danger', 'La nueva contraseña debe tener mínimo 6 caracteres.');
                $this->redirect('/perfil');
            }
            $dataToUpdate['password'] = $password;
        }

        $ok = $this->usuarioModel->updatePerfil($userId, $dataToUpdate);

        if ($ok) {
            // Actualizar la sesión activa de inmediato
            AuthHelper::updateUserSession([
                'nombre'   => $nombre,
                'email'    => $email,
                'telefono' => $telefono,
                'genero'   => $genero,
                'foto'     => $fotoPath
            ]);
            SessionHelper::setFlash('success', '¡Perfil y fotografía actualizados exitosamente!');
        } else {
            SessionHelper::setFlash('danger', 'Error al guardar los cambios en la base de datos.');
        }

        $this->redirect('/perfil');
    }
}
