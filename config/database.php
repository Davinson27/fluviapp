<?php
// =======================================================
// Conexión a Base de Datos MySQL (PDO Singleton)
// Credenciales: .env si existe; por defecto XAMPP (root sin clave)
// =======================================================

if (!function_exists('env')) {
    require_once __DIR__ . '/env.php';
    fluviapp_load_env(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', env_bool('APP_DEBUG', true));
}

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $name = env('DB_NAME', 'fluviapp');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                $detalle = APP_DEBUG
                    ? '<p><strong>Detalle:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>'
                    : '<p>Intente más tarde o contacte al administrador.</p>';
                http_response_code(500);
                die("<div style='font-family:sans-serif;padding:20px;background:#fee2e2;color:#991b1b;border-radius:8px;'>
                    <h3>Error de conexión a la Base de Datos FluviApp</h3>
                    <p>Verifique que MySQL en XAMPP esté iniciado y que la base de datos <code>fluviapp</code> exista.</p>
                    {$detalle}
                </div>");
            }
        }
        return self::$instance;
    }
}
