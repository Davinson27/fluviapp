<?php
// =======================================================
// Front Controller & Enrutador Principal - FluviApp
// =======================================================

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Helpers/AuthHelper.php';

// Obtener ruta solicitada
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = dirname($scriptName);

// Eliminar el directorio base de la URI
$path = parse_url($requestUri, PHP_URL_PATH);
if ($baseDir !== '/' && strpos($path, $baseDir) === 0) {
    $path = substr($path, strlen($baseDir));
}
$path = '/' . trim($path, '/');

// Enrutamiento
switch ($path) {
    // Auth
    case '/':
    case '/login':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;

    case '/logout':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    // Registro de Clientes (público)
    case '/registro':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;

    // Dashboard
    case '/dashboard':
        require_once __DIR__ . '/app/Controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    // Portal de Pasajeros / Clientes
    case '/portal':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->portal();
        break;

    case '/cliente/comprar':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->comprar();
        break;

    case '/cliente/procesar-compra':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->procesarCompra();
        break;

    case '/cliente/mis-boletos':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->misBoletos();
        break;

    case '/cliente/ver-ruta':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->verRuta();
        break;

    case '/cliente/enviar-encomienda':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->encomiendaCrear();
        break;

    case '/cliente/guardar-encomienda':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->guardarEncomienda();
        break;

    case '/cliente/mis-encomiendas':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->misEncomiendas();
        break;

    // Embarcaciones
    case '/embarcaciones':
        require_once __DIR__ . '/app/Controllers/EmbarcacionesController.php';
        (new EmbarcacionesController())->index();
        break;

    case '/embarcaciones/crear':
        require_once __DIR__ . '/app/Controllers/EmbarcacionesController.php';
        (new EmbarcacionesController())->create();
        break;

    case '/embarcaciones/guardar':
        require_once __DIR__ . '/app/Controllers/EmbarcacionesController.php';
        (new EmbarcacionesController())->store();
        break;

    case '/embarcaciones/eliminar':
        require_once __DIR__ . '/app/Controllers/EmbarcacionesController.php';
        (new EmbarcacionesController())->delete();
        break;

    // Muelles
    case '/muelles':
        require_once __DIR__ . '/app/Controllers/MuellesController.php';
        (new MuellesController())->index();
        break;

    case '/muelles/guardar':
        require_once __DIR__ . '/app/Controllers/MuellesController.php';
        (new MuellesController())->store();
        break;

    case '/muelles/eliminar':
        require_once __DIR__ . '/app/Controllers/MuellesController.php';
        (new MuellesController())->delete();
        break;

    // Rutas
    case '/rutas':
        require_once __DIR__ . '/app/Controllers/RutasController.php';
        (new RutasController())->index();
        break;

    case '/rutas/guardar':
        require_once __DIR__ . '/app/Controllers/RutasController.php';
        (new RutasController())->store();
        break;

    case '/rutas/editar-tarifa':
        require_once __DIR__ . '/app/Controllers/RutasController.php';
        (new RutasController())->actualizarTarifa();
        break;

    case '/rutas/eliminar':
        require_once __DIR__ . '/app/Controllers/RutasController.php';
        (new RutasController())->delete();
        break;

    // Viajes
    case '/viajes':
        require_once __DIR__ . '/app/Controllers/ViajesController.php';
        (new ViajesController())->index();
        break;

    case '/viajes/crear':
        require_once __DIR__ . '/app/Controllers/ViajesController.php';
        (new ViajesController())->create();
        break;

    case '/viajes/guardar':
        require_once __DIR__ . '/app/Controllers/ViajesController.php';
        (new ViajesController())->store();
        break;

    case '/viajes/cambiar-estado':
        require_once __DIR__ . '/app/Controllers/ViajesController.php';
        (new ViajesController())->cambiarEstado();
        break;

    case '/viajes/editar-precio':
        require_once __DIR__ . '/app/Controllers/ViajesController.php';
        (new ViajesController())->actualizarPrecio();
        break;

    case '/viajes/manifiesto':
        require_once __DIR__ . '/app/Controllers/ViajesController.php';
        (new ViajesController())->manifiesto();
        break;

    // Boletos
    case '/boletos':
        require_once __DIR__ . '/app/Controllers/BoletosController.php';
        (new BoletosController())->index();
        break;

    case '/boletos/crear':
        require_once __DIR__ . '/app/Controllers/BoletosController.php';
        (new BoletosController())->create();
        break;

    case '/boletos/guardar':
        require_once __DIR__ . '/app/Controllers/BoletosController.php';
        (new BoletosController())->store();
        break;

    case '/boletos/ticket':
        require_once __DIR__ . '/app/Controllers/BoletosController.php';
        (new BoletosController())->ticket();
        break;

    // Cargas / Encomiendas
    case '/cargas':
        require_once __DIR__ . '/app/Controllers/CargasController.php';
        (new CargasController())->index();
        break;

    case '/cargas/crear':
        require_once __DIR__ . '/app/Controllers/CargasController.php';
        (new CargasController())->create();
        break;

    case '/cargas/guardar':
        require_once __DIR__ . '/app/Controllers/CargasController.php';
        (new CargasController())->store();
        break;

    case '/cargas/cambiar-estado':
        require_once __DIR__ . '/app/Controllers/CargasController.php';
        (new CargasController())->cambiarEstado();
        break;

    // Reportes
    case '/reportes':
        require_once __DIR__ . '/app/Controllers/ReportesController.php';
        (new ReportesController())->index();
        break;

    // Usuarios y Roles (Solo Administrador)
    case '/usuarios':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        (new UsuariosController())->index();
        break;

    case '/usuarios/crear':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        (new UsuariosController())->create();
        break;

    case '/usuarios/guardar':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        (new UsuariosController())->store();
        break;

    case '/usuarios/editar':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        (new UsuariosController())->edit();
        break;

    case '/usuarios/actualizar':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        (new UsuariosController())->update();
        break;

    case '/usuarios/eliminar':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        (new UsuariosController())->delete();
        break;

    default:
        http_response_code(404);
        echo "<div style='font-family:sans-serif;text-align:center;padding:50px;'>
            <h1>404 - Página no encontrada</h1>
            <p>La ruta <code>" . htmlspecialchars($path) . "</code> no existe en " . APP_NAME . ".</p>
            <a href='" . BASE_URL . "/dashboard' style='color:#0284c7;text-decoration:none;font-weight:bold;'>&larr; Volver al Dashboard</a>
        </div>";
        break;
}
