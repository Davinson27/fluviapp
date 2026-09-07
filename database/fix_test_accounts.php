<?php
// =========================================================
// Fix: Actualizar cuentas de prueba con documento y claves correctas
// =========================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();

echo "=== Arreglando cuentas de prueba de FluviApp ===\n\n";

// Definir cuentas de prueba con datos completos
$cuentas = [
    [
        'email'     => 'admin@fluviapp.com',
        'nombre'    => 'Davinson Mosquera',
        'documento' => '1234567890',
        'password'  => 'admin123',
        'rol'       => 'admin',
        'telefono'  => '3001234567',
        'estado'    => 'activo'
    ],
    [
        'email'     => 'operador@fluviapp.com',
        'nombre'    => 'Carlos Operador Muelle',
        'documento' => '1234567891',
        'password'  => 'admin123',
        'rol'       => 'operador',
        'telefono'  => '3001234568',
        'estado'    => 'activo'
    ],
    [
        'email'     => 'taquilla@fluviapp.com',
        'nombre'    => 'María Taquilla 1',
        'documento' => '1234567892',
        'password'  => 'admin123',
        'rol'       => 'taquilla',
        'telefono'  => '3001234569',
        'estado'    => 'activo'
    ],
    [
        'email'     => 'capitan@fluviapp.com',
        'nombre'    => 'Capitán Juan Navas',
        'documento' => '1234567893',
        'password'  => 'admin123',
        'rol'       => 'capitan',
        'telefono'  => '3001234570',
        'estado'    => 'activo'
    ],
    [
        'email'     => 'cliente@fluviapp.com',
        'nombre'    => 'Carlos Pasajero Fluvial',
        'documento' => '1098765432',
        'password'  => 'cliente123',
        'rol'       => 'cliente',
        'telefono'  => '3109876543',
        'estado'    => 'activo'
    ],
];

$stmtUpdate = $db->prepare("
    UPDATE usuarios 
    SET nombre = :nombre, 
        documento = :documento, 
        password = :password, 
        rol = :rol, 
        telefono = :telefono, 
        estado = :estado
    WHERE email = :email
");

$stmtInsert = $db->prepare("
    INSERT INTO usuarios (nombre, email, documento, password, rol, telefono, estado)
    VALUES (:nombre, :email, :documento, :password, :rol, :telefono, :estado)
");

$stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");

foreach ($cuentas as $c) {
    $hash = password_hash($c['password'], PASSWORD_DEFAULT);
    
    $stmtCheck->execute(['email' => $c['email']]);
    $exists = $stmtCheck->fetch();
    
    if ($exists) {
        $stmtUpdate->execute([
            'nombre'    => $c['nombre'],
            'documento' => $c['documento'],
            'password'  => $hash,
            'rol'       => $c['rol'],
            'telefono'  => $c['telefono'],
            'estado'    => $c['estado'],
            'email'     => $c['email']
        ]);
        echo "✓ Actualizada: {$c['email']} ({$c['rol']}) -> clave: {$c['password']}\n";
    } else {
        $stmtInsert->execute([
            'nombre'    => $c['nombre'],
            'email'     => $c['email'],
            'documento' => $c['documento'],
            'password'  => $hash,
            'rol'       => $c['rol'],
            'telefono'  => $c['telefono'],
            'estado'    => $c['estado']
        ]);
        echo "✓ Creada: {$c['email']} ({$c['rol']}) -> clave: {$c['password']}\n";
    }
}

echo "\n=== Verificación final ===\n";
$stmt = $db->query('SELECT id, nombre, email, documento, rol, estado, telefono FROM usuarios ORDER BY id');
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($todos as $u) {
    echo "  [{$u['id']}] {$u['email']} | {$u['rol']} | doc:{$u['documento']} | tel:{$u['telefono']} | {$u['estado']}\n";
}

echo "\n✅ Cuentas de prueba listas.\n";
echo "\nCredenciales:\n";
echo "  admin@fluviapp.com     -> clave: admin123    (Administrador)\n";
echo "  operador@fluviapp.com  -> clave: admin123    (Operador)\n";
echo "  taquilla@fluviapp.com  -> clave: admin123    (Taquillero)\n";
echo "  capitan@fluviapp.com   -> clave: admin123    (Capitán)\n";
echo "  cliente@fluviapp.com   -> clave: cliente123  (Pasajero/Cliente)\n";
