<?php
$db_host = "mysql_db";      
$db_user = "admin";
$db_pass = "admin";
$db_name = "tienda_don_manolo";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function limpiar_entrada($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

function formatear_fecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}


function formatear_dinero($cantidad) {
    return '$' . number_format($cantidad, 2);
}
?>