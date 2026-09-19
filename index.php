<?php
// =======================================================
// Front Controller & Enrutador Principal - FluviApp
// =======================================================

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Helpers/AuthHelper.php';

// Auto-migración transparente v2.0 si la tabla pagos_transacciones no existe aún
try {
    $dbCheck = Database::getConnection();
    $stmtCheck = $dbCheck->query("SHOW TABLES LIKE 'pagos_transacciones'");
    if (!$stmtCheck->fetch()) {
        $rawMigrate = file_get_contents(__DIR__ . '/database/migration_v2.sql');
        if (!empty($rawMigrate)) {
            $cleanMigrate = preg_replace('/^\s*USE\s+`?[a-zA-Z0-9_-]+`?\s*;/mi', '', $rawMigrate);
            $dbCheck->exec($cleanMigrate);
        }
    }
} catch (Throwable $e) {
    // Continuar ejecución normalmente si no hay conexión o ya está migrado
}

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

SessionHelper::init();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !str_starts_with($path, '/api/')) {
    SessionHelper::requireCsrf();
}

// Enrutamiento
switch ($path) {
    // Landing Page Pública (Servicios, Búsqueda, Flota y Tracking)
    case '/':
    case '/landing':
        require_once __DIR__ . '/app/Controllers/LandingController.php';
        (new LandingController())->index();
        break;

    // APIs Públicas JSON para Mapa y Filtros Dinámicos
    case '/api/muelles':
        require_once __DIR__ . '/app/Controllers/LandingController.php';
        (new LandingController())->apiMuelles();
        break;

    case '/api/rutas':
        require_once __DIR__ . '/app/Controllers/LandingController.php';
        (new LandingController())->apiRutas();
        break;

    case '/api/rios':
        require_once __DIR__ . '/app/Controllers/LandingController.php';
        (new LandingController())->apiRios();
        break;

    case '/api/chatbot/consultar':
        require_once __DIR__ . '/app/Controllers/LandingController.php';
        (new LandingController())->apiChatbot();
        break;

    // Pasarela de Pagos Wompi (v2.0)
    case '/cliente/iniciar-pago-wompi':
        require_once __DIR__ . '/app/Controllers/PagosController.php';
        (new PagosController())->iniciarPago();
        break;

    case '/api/pagos/webhook-wompi':
        require_once __DIR__ . '/app/Controllers/PagosController.php';
        (new PagosController())->webhook();
        break;

    case '/cliente/simular-aprobacion-wompi':
        require_once __DIR__ . '/app/Controllers/PagosController.php';
        (new PagosController())->simularAprobacion();
        break;

    case '/cliente/pago-resultado':
        require_once __DIR__ . '/app/Controllers/PagosController.php';
        (new PagosController())->resultado();
        break;

    // Validación QR y Check-in en Muelle (v2.0)
    case '/operaciones/checkin':
        require_once __DIR__ . '/app/Controllers/CheckinController.php';
        (new CheckinController())->checkin();
        break;

    case '/api/checkin/validar':
        require_once __DIR__ . '/app/Controllers/CheckinController.php';
        (new CheckinController())->validarBoleto();
        break;

    case '/operaciones/entrega-carga':
        require_once __DIR__ . '/app/Controllers/CheckinController.php';
        (new CheckinController())->entregaCarga();
        break;

    case '/api/carga/confirmar-entrega':
        require_once __DIR__ . '/app/Controllers/CheckinController.php';
        (new CheckinController())->confirmarEntregaCarga();
        break;

    // Rastreo Fluvial en Vivo (v2.0)
    case '/capitan/navegacion':
        require_once __DIR__ . '/app/Controllers/TrackingController.php';
        (new TrackingController())->capitanNavegacion();
        break;

    case '/api/tracking/actualizar':
        require_once __DIR__ . '/app/Controllers/TrackingController.php';
        (new TrackingController())->actualizarPosicion();
        break;

    case '/api/tracking/posicion-viaje':
        require_once __DIR__ . '/app/Controllers/TrackingController.php';
        (new TrackingController())->posicionViaje();
        break;

    case '/tracking/viaje':
        require_once __DIR__ . '/app/Controllers/TrackingController.php';
        (new TrackingController())->viajeEnVivo();
        break;

    // Gestión Integral de Flota, Mantenimiento y Combustible (v2.0)
    case '/flota':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->index();
        break;

    case '/flota/mantenimiento':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->mantenimiento();
        break;

    case '/flota/mantenimiento/guardar':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->guardarMantenimiento();
        break;

    case '/flota/combustible':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->combustible();
        break;

    case '/flota/combustible/guardar':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->guardarCombustible();
        break;

    case '/flota/documentos':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->documentos();
        break;

    case '/flota/documentos/guardar':
        require_once __DIR__ . '/app/Controllers/FlotaController.php';
        (new FlotaController())->guardarDocumento();
        break;

    // Auth
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

    // Perfil de Usuario (tanto Administradores como Clientes / Pasajeros)
    case '/perfil':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        (new AuthController())->perfil();
        break;

    case '/perfil/actualizar':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        (new AuthController())->actualizarPerfil();
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

    case '/cliente/reportar-incidencia':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->reportarIncidencia();
        break;

    case '/api/notificaciones/marcar-leidas':
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/app/Helpers/AuthHelper.php';
        require_once __DIR__ . '/app/Models/Notificacion.php';
        $user = AuthHelper::user();
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'No autenticado']);
            exit;
        }
        $notifModel = new Notificacion();
        $notifModel->marcarTodasLeidas((int)$user['id']);
        echo json_encode(['success' => true]);
        exit;

    case '/cliente/factura-boleto':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->facturaBoleto();
        break;

    case '/cliente/factura-encomienda':
        require_once __DIR__ . '/app/Controllers/ClienteController.php';
        (new ClienteController())->facturaEncomienda();
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

    case '/embarcaciones/editar':
        require_once __DIR__ . '/app/Controllers/EmbarcacionesController.php';
        (new EmbarcacionesController())->edit();
        break;

    case '/embarcaciones/actualizar':
        require_once __DIR__ . '/app/Controllers/EmbarcacionesController.php';
        (new EmbarcacionesController())->update();
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

    case '/muelles/editar':
        require_once __DIR__ . '/app/Controllers/MuellesController.php';
        (new MuellesController())->edit();
        break;

    case '/muelles/actualizar':
        require_once __DIR__ . '/app/Controllers/MuellesController.php';
        (new MuellesController())->update();
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

    case '/boletos/factura':
        require_once __DIR__ . '/app/Controllers/BoletosController.php';
        (new BoletosController())->factura();
        break;

    // Cargas / Encomiendas
    case '/cargas':
        require_once __DIR__ . '/app/Controllers/CargasController.php';
        (new CargasController())->index();
        break;

    case '/cargas/factura':
        require_once __DIR__ . '/app/Controllers/CargasController.php';
        (new CargasController())->factura();
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
