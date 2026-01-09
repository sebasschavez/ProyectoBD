<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;}</style>";
echo "</head><body>";
echo "<h1>🔧 Diagnóstico de Conexión</h1>";

$db_host = 'sql301.infinityfree.com';  
$db_user = 'if0_40420856';              
$db_pass = 'aC0qlpQFP15BRV';                         
$db_name = 'if0_40420856_tienda_don_manolo';         

echo "<h2>📋 Datos de Configuración:</h2>";
echo "<p><strong>Host:</strong> $db_host</p>";
echo "<p><strong>Usuario:</strong> $db_user</p>";
echo "<p><strong>Base de datos:</strong> $db_name</p>";
echo "<p><strong>Password:</strong> " . (empty($db_pass) ? "⚠️ VACÍO" : "✓ Configurado") . "</p>";
echo "<hr>";

echo "<h2>🔌 Probando Conexión...</h2>";

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo "<p class='error'>❌ <strong>ERROR DE CONEXIÓN</strong></p>";
    echo "<p class='error'>Mensaje: " . $conn->connect_error . "</p>";
    echo "<p class='error'>Código: " . $conn->connect_errno . "</p>";
    
    echo "<h3>💡 Posibles soluciones:</h3><ul>";
    if ($conn->connect_errno == 1045) {
        echo "<li>Usuario o contraseña incorrectos</li>";
        echo "<li>Verifica en MySQL Databases</li>";
    } elseif ($conn->connect_errno == 1049) {
        echo "<li>La base de datos no existe</li>";
        echo "<li>Créala en MySQL Databases</li>";
    } elseif ($conn->connect_errno == 2002) {
        echo "<li>Host incorrecto</li>";
        echo "<li>Verifica el MySQL Hostname</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p class='success'>✅ <strong>CONEXIÓN EXITOSA!</strong></p>";
    
    $result = $conn->query("SHOW TABLES");
    
    if ($result && $result->num_rows > 0) {
        echo "<h2 class='success'>📦 Tablas encontradas:</h2><ul>";
        while ($row = $result->fetch_array()) {
            $tabla = $row[0];
            $count = $conn->query("SELECT COUNT(*) as total FROM `$tabla`")->fetch_assoc();
            echo "<li>✓ <strong>$tabla</strong> ({$count['total']} registros)</li>";
        }
        echo "</ul>";
        echo "<p class='success'>🎉 ¡Todo está listo! Ahora puedes usar index.php</p>";
    } else {
        echo "<h2 class='warning'>⚠️ Base de datos vacía</h2>";
        echo "<p>La conexión funciona pero no hay tablas.</p>";
        echo "<p><strong>Solución:</strong> Ve a phpMyAdmin y ejecuta el script SQL</p>";
    }
    
    $conn->close();
}

echo "</body></html>";
?>