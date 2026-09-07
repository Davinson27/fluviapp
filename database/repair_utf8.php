<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$pdo = Database::getConnection();
$pdo->exec("SET NAMES utf8mb4");

// Reparar departamentos
$pdo->exec("UPDATE muelles SET departamento = 'Bolívar' WHERE departamento LIKE 'Bol%var%' OR departamento LIKE 'Bol%'");
$pdo->exec("UPDATE muelles SET departamento = 'Atlántico' WHERE departamento LIKE 'Atl%ntico'");
$pdo->exec("UPDATE muelles SET departamento = 'Boyacá' WHERE departamento LIKE 'Boyac%'");
$pdo->exec("UPDATE muelles SET departamento = 'Caquetá' WHERE departamento LIKE 'Caquet%'");
$pdo->exec("UPDATE muelles SET departamento = 'Chocó' WHERE departamento LIKE 'Choc%'");
$pdo->exec("UPDATE muelles SET departamento = 'Córdoba' WHERE departamento LIKE 'C%rdoba'");
$pdo->exec("UPDATE muelles SET departamento = 'Guainía' WHERE departamento LIKE 'Guain%a'");
$pdo->exec("UPDATE muelles SET departamento = 'Nariño' WHERE departamento LIKE 'Nari%o'");
$pdo->exec("UPDATE muelles SET departamento = 'Vaupés' WHERE departamento LIKE 'Vaup%s'");

// Reparar nombres de ríos en muelles
$pdo->exec("UPDATE muelles SET rio = 'Río Magdalena' WHERE rio LIKE 'R%o Magdalena'");
$pdo->exec("UPDATE muelles SET rio = 'Río Arauca' WHERE rio LIKE 'R%o Arauca'");
$pdo->exec("UPDATE muelles SET rio = 'Río Orteguaza' WHERE rio LIKE 'R%o Orteguaza'");
$pdo->exec("UPDATE muelles SET rio = 'Río Caquetá' WHERE rio LIKE 'R%o Caquet%'");
$pdo->exec("UPDATE muelles SET rio = 'Río Atrato' WHERE rio LIKE 'R%o Atrato'");
$pdo->exec("UPDATE muelles SET rio = 'Río Sinú' WHERE rio LIKE 'R%o Sin%'");
$pdo->exec("UPDATE muelles SET rio = 'Río Inírida' WHERE rio LIKE 'R%o In%rida'");
$pdo->exec("UPDATE muelles SET rio = 'Río Telembí' WHERE rio LIKE 'R%o Telemb%'");
$pdo->exec("UPDATE muelles SET rio = 'Río Vaupés' WHERE rio LIKE 'R%o Vaup%s'");
$pdo->exec("UPDATE muelles SET rio = 'Río Nechí' WHERE rio LIKE 'R%o Nech%'");
$pdo->exec("UPDATE muelles SET rio = 'Río Caguán' WHERE rio LIKE 'R%o Cagu%n'");
$pdo->exec("UPDATE muelles SET rio = 'Ciénaga de Zapatosa' WHERE rio LIKE 'Ci%naga de Zapatosa'");
$pdo->exec("UPDATE muelles SET rio = 'Ciénaga de Ayapel' WHERE rio LIKE 'Ci%naga de Ayapel'");
$pdo->exec("UPDATE muelles SET rio = 'Puerto Nariño' WHERE nombre LIKE 'Puerto Nari%o'");

// Reparar nombres específicos de muelles
$pdo->exec("UPDATE muelles SET nombre = 'Terminal Fluvial Puerto Berrío' WHERE id = 12");
$pdo->exec("UPDATE muelles SET nombre = 'Puerto Vigía del Fuerte' WHERE id = 15");
$pdo->exec("UPDATE muelles SET nombre = 'Terminal Fluvial Magangué' WHERE id = 19");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Puerto Boyacá' WHERE id = 23");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Timbiquí' WHERE id = 29");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Bojayá' WHERE id = 33");
$pdo->exec("UPDATE muelles SET nombre = 'Terminal Fluvial Quibdó' WHERE id = 32");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Turístico Montería' WHERE id = 37");
$pdo->exec("UPDATE muelles SET nombre = 'Puerto Fluvial Inírida' WHERE id = 42");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle San José del Guaviare' WHERE id = 43");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle San Benito Abad' WHERE id = 60");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Principal Mitú' WHERE id = 65");
$pdo->exec("UPDATE muelles SET nombre = 'Terminal Puerto Carreño' WHERE id = 66");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Santa Rosalía' WHERE id = 67");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Yondó Antioquia' WHERE id = 71");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle El Bagre Antioquia' WHERE id = 72");
$pdo->exec("UPDATE muelles SET nombre = 'Puerto Zambrano Bolívar' WHERE id = 74");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Pinillos Bolívar' WHERE id = 75");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Cartagena del Chairá' WHERE id = 77");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Medio Atrato Chocó' WHERE id = 79");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Carmen del Darién Chocó' WHERE id = 80");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Mapiripán Meta' WHERE id = 86");
$pdo->exec("UPDATE muelles SET nombre = 'Muelle Carurú Vaupés' WHERE id = 89");

// Reparar nombres de rios
$pdo->exec("UPDATE rios SET nombre = 'Río Magdalena' WHERE id = 1");
$pdo->exec("UPDATE rios SET nombre = 'Río Cauca' WHERE id = 2");
$pdo->exec("UPDATE rios SET nombre = 'Río Atrato' WHERE id = 3");
$pdo->exec("UPDATE rios SET nombre = 'Río Meta' WHERE id = 4");
$pdo->exec("UPDATE rios SET nombre = 'Río Guaviare' WHERE id = 5");
$pdo->exec("UPDATE rios SET nombre = 'Río Putumayo' WHERE id = 6");
$pdo->exec("UPDATE rios SET nombre = 'Río Amazonas' WHERE id = 7");
$pdo->exec("UPDATE rios SET nombre = 'Río Orinoco' WHERE id = 8");
$pdo->exec("UPDATE rios SET nombre = 'Río San Juan' WHERE id = 9");
$pdo->exec("UPDATE rios SET nombre = 'Río Sinú' WHERE id = 10");
$pdo->exec("UPDATE rios SET nombre = 'Río Arauca' WHERE id = 11");
$pdo->exec("UPDATE rios SET nombre = 'Río Vaupés' WHERE id = 12");
$pdo->exec("UPDATE rios SET nombre = 'Río Caquetá' WHERE id = 13");
$pdo->exec("UPDATE rios SET nombre = 'Río Inírida' WHERE id = 14");
$pdo->exec("UPDATE rios SET nombre = 'Río Negro (Guainía)' WHERE id = 15");
$pdo->exec("UPDATE rios SET nombre = 'Río Baudó' WHERE id = 16");
$pdo->exec("UPDATE rios SET nombre = 'Río Patía' WHERE id = 17");
$pdo->exec("UPDATE rios SET nombre = 'Río San Jorge' WHERE id = 18");
$pdo->exec("UPDATE rios SET nombre = 'Río Cesar' WHERE id = 19");
$pdo->exec("UPDATE rios SET nombre = 'Río Sogamoso' WHERE id = 20");

// Limpiar muelles 1 y 2 duplicados con 19 y 20 si es necesario
$stmt = $pdo->query("SELECT COUNT(*) FROM muelles WHERE estado = 'activo'");
echo "Total muelles activos: " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->query("SELECT DISTINCT departamento FROM muelles ORDER BY departamento");
$deps = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Total departamentos únicos: " . count($deps) . "\n";
print_r($deps);
