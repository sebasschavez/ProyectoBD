<?php
// bootstrap.php - Archivo de inicialización de Eloquent ORM

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'mysql_db',
    'database'  => 'tienda_don_manolo',
    'username'  => 'admin',
    'password'  => 'admin',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// Hacer disponible Eloquent globalmente
$capsule->setAsGlobal();

// Iniciar Eloquent
$capsule->bootEloquent();

// Funciones auxiliares (mantener compatibilidad)
function formatear_fecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}

function formatear_dinero($cantidad) {
    return '$' . number_format($cantidad, 2);
}