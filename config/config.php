<?php
// =======================================================
// Configuración Global - FluviApp
// =======================================================

require_once __DIR__ . '/env.php';
fluviapp_load_env(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

define('APP_ENV', env('APP_ENV', 'local'));
define('APP_DEBUG', env_bool('APP_DEBUG', APP_ENV !== 'production'));

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
}

date_default_timezone_set('America/Bogota');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-XSS-Protection: 0');
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
}

define('APP_NAME', 'FluviApp');
define('APP_TAGLINE', 'Sistema de Gestión y Operaciones Fluviales');
define('APP_VERSION', '1.0.0');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($scriptDir, '/');

define('BASE_URL', $protocol . $host . $basePath);
define('ROOT_PATH', dirname(__DIR__));

define('METODOS_PAGO', ['efectivo', 'transferencia', 'tarjeta']);
define('ROLES_STAFF', ['admin', 'operador', 'taquilla', 'capitan']);
define('ROLES_SISTEMA', ['admin', 'operador', 'taquilla', 'capitan', 'cliente']);
