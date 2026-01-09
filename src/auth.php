<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

function es_admin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function es_vendedor() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'vendedor';
}

function obtener_nombre_usuario() {
    return isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Usuario';
}

function cerrar_sesion() {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>