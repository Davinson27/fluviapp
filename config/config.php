<?php
// =======================================================
// Configuración Global - FluviApp
// =======================================================

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zona horaria
date_default_timezone_set('America/Bogota');

// Constantes de la aplicación
define('APP_NAME', 'FluviApp');
define('APP_TAGLINE', 'Sistema de Gestión y Operaciones Fluviales');
define('APP_VERSION', '1.0.0');

// Detección automática de la URL Base
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptDir, '/');

define('BASE_URL', $protocol . $host . $basePath);
define('ROOT_PATH', dirname(__DIR__));
