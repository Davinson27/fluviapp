<?php
// =======================================================
// Suite de Pruebas de Verificación: FluviApp Versión 2.0
// =======================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Helpers/QrCodeHelper.php';
require_once __DIR__ . '/../app/Services/PaymentService.php';
require_once __DIR__ . '/../app/Models/Asiento.php';
require_once __DIR__ . '/../app/Models/Carga.php';
require_once __DIR__ . '/../app/Models/Boleto.php';
require_once __DIR__ . '/../app/Models/Mantenimiento.php';
require_once __DIR__ . '/../app/Models/Combustible.php';
require_once __DIR__ . '/../app/Models/DocumentoEmbarcacion.php';

echo "====================================================\n";
echo "   INICIANDO PRUEBAS DE VERIFICACIÓN FLUVIAPP v2.0  \n";
echo "====================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest(string $testName, bool $condition, string $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$testName}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$testName} - {$details}\n";
        $failed++;
    }
}

// 1. Verificación de Versión del Sistema
echo "1. Configuración y Versión:\n";
assertTest("APP_VERSION es 2.0.0", defined('APP_VERSION') && APP_VERSION === '2.0.0');
assertTest("WOMPI_MODE definido", defined('WOMPI_MODE'));
assertTest("WOMPI_PUBLIC_KEY definido", defined('WOMPI_PUBLIC_KEY') && !empty(WOMPI_PUBLIC_KEY));

// 2. Verificación de QrCodeHelper
echo "\n2. QrCodeHelper (Generación y Criptografía QR):\n";
$token = QrCodeHelper::generateToken('FLV-BOL', 1, 'BOL-2026-TEST');
assertTest("Token QR generado con SHA-256 (longitud 64)", strlen($token) === 64);

$payload = QrCodeHelper::buildPayload('BOL', 'BOL-TEST', $token);
assertTest("Payload QR con formato estándar FLV|BOL|...", str_starts_with($payload, 'FLV|BOL|BOL-TEST|'));

$svg = QrCodeHelper::renderSvg($payload, 200);
assertTest("Generación de SVG vectorial autónomo", str_contains($svg, '<svg') && str_contains($svg, '</svg>'));

$base64 = QrCodeHelper::getBase64Svg($payload);
assertTest("Generación de Data-URI base64 para <img>", str_starts_with($base64, 'data:image/svg+xml;base64,'));

// 3. Verificación de PaymentService (Wompi)
echo "\n3. PaymentService (Pasarela Wompi):\n";
$payService = new PaymentService();
$ref = PaymentService::generarReferencia('BOL');
assertTest("Referencia de pago generada con prefijo FLV-BOL-", str_starts_with($ref, 'FLV-BOL-'));

$montoCentavos = 5000000;
$firma = $payService->calcularFirmaIntegridad($ref, $montoCentavos, 'COP');
$esperada = hash('sha256', $ref . $montoCentavos . 'COP' . WOMPI_INTEGRITY_SECRET);
assertTest("Cálculo de firma SHA-256 de Wompi coincide exactamente", $firma === $esperada);

// 4. Verificación de Asiento (Seat Map)
echo "\n4. Asiento (Mapa Visual Interactivo):\n";
$asientoModel = new Asiento();
// Probar con un viaje existente en BD
$db = Database::getConnection();
$stmtViaje = $db->query("SELECT id FROM viajes LIMIT 1");
$vRow = $stmtViaje->fetch();
if ($vRow) {
    $mapa = $asientoModel->getMapaAsientosPorViaje((int)$vRow['id']);
    assertTest("Mapa de asientos generado para viaje #{$vRow['id']}", !empty($mapa['filas']));
    assertTest("Estructura de filas contiene lados 'izquierda' y 'derecha'", isset($mapa['filas'][0]['izquierda']) && isset($mapa['filas'][0]['derecha']));
} else {
    echo "  [SKIP] No hay viajes en BD para probar getMapaAsientosPorViaje\n";
}

// 5. Verificación de Cubicaje de Carga
echo "\n5. Cubicaje y Peso Volumétrico:\n";
$largo = 50; $ancho = 40; $alto = 30; // 60.000 cm3 / 5000 = 12 kg
$pesoVol = round(($largo * $ancho * $alto) / 5000.0, 2);
assertTest("Fórmula de cubicaje (50x40x30 / 5000) = 12.0 kg", $pesoVol === 12.0);

// 6. Verificación de Semáforo de Documentos DIMAR
echo "\n6. Semáforo Documental de Flota:\n";
$docModel = new DocumentoEmbarcacion();
$resumen = $docModel->getAlertasResumen();
assertTest("Consulta de semáforo documental DIMAR ejecutada correctamente", isset($resumen['total_documentos']));

// 7. Verificación de Tablas y Columnas en Base de Datos
echo "\n7. Integridad de Base de Datos v2.0:\n";
$tables = ['pagos_transacciones', 'viajes_telemetria', 'embarcaciones_mantenimiento', 'embarcaciones_combustible', 'embarcaciones_documentos'];
foreach ($tables as $t) {
    $stmtT = $db->query("SHOW TABLES LIKE '{$t}'");
    assertTest("Tabla '{$t}' existe en la base de datos", $stmtT->rowCount() > 0);
}

$stmtColBol = $db->query("SHOW COLUMNS FROM boletos LIKE 'codigo_qr_token'");
assertTest("Columna 'codigo_qr_token' existe en tabla boletos", $stmtColBol->rowCount() > 0);

$stmtColCarga = $db->query("SHOW COLUMNS FROM cargas_encomiendas LIKE 'peso_volumetrico_kg'");
assertTest("Columna 'peso_volumetrico_kg' existe en tabla cargas_encomiendas", $stmtColCarga->rowCount() > 0);

echo "\n====================================================\n";
echo "   RESUMEN: {$passed} PASADAS, {$failed} FALLIDAS\n";
echo "====================================================\n";

if ($failed > 0) {
    exit(1);
}
