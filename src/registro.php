<?php
session_start();
require_once 'config.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = limpiar_entrada($_POST['usuario']);
    $contrasena = $_POST['contrasena'];
    $confirmar = $_POST['confirmar_contrasena'];
    $id_empleado = !empty($_POST['empleado']) ? intval($_POST['empleado']) : null;
    
    if (strlen($usuario) < 4) {
        $mensaje = 'El usuario debe tener al menos 4 caracteres';
        $tipo_mensaje = 'error';
    } elseif (strlen($contrasena) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres';
        $tipo_mensaje = 'error';
    } elseif ($contrasena !== $confirmar) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipo_mensaje = 'error';
    } else {
        $check = $conn->query("SELECT ID_Usuario FROM Usuario WHERE Nombre_Usuario = '$usuario'");
        if ($check->num_rows > 0) {
            $mensaje = 'El nombre de usuario ya está en uso';
            $tipo_mensaje = 'error';
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO Usuario (Nombre_Usuario, Contrasena, ID_Empleado, Rol) 
                    VALUES ('$usuario', '$hash', " . ($id_empleado ? $id_empleado : "NULL") . ", 'vendedor')";
            
            if ($conn->query($sql)) {
                $mensaje = 'Usuario registrado exitosamente. Ya puedes iniciar sesión.';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al registrar usuario: ' . $conn->error;
                $tipo_mensaje = 'error';
            }
        }
    }
}

$empleados = $conn->query("SELECT * FROM Empleado ORDER BY Nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Tienda Don Manolo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 5px;
        }

        .logo p {
            color: #666;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }

        .input-group {
            position: relative;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
        }

        .mensaje {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .mensaje.error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.85em;
        }

        .footer-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        .help-text {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }

            .logo h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>🏪</h1>
            <h1>Registro</h1>
            <p>Crea tu cuenta en Tienda Don Manolo</p>
        </div>

        <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $tipo_mensaje; ?>">
            <?php echo $tipo_mensaje === 'success' ? '✅' : '⚠️'; ?> <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre de Usuario *</label>
                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input type="text" name="usuario" required minlength="4" placeholder="Mínimo 4 caracteres">
                </div>
                <div class="help-text">Este será tu nombre para iniciar sesión</div>
            </div>

            <div class="form-group">
                <label>Contraseña *</label>
                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="contrasena" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña *</label>
                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="confirmar_contrasena" required minlength="6" placeholder="Repite tu contraseña">
                </div>
            </div>

            <div class="form-group">
                <label>Asociar con Empleado (Opcional)</label>
                <div class="input-group">
                    <span class="input-icon">👔</span>
                    <select name="empleado">
                        <option value="">Sin asociar</option>
                        <?php while ($emp = $empleados->fetch_assoc()): ?>
                        <option value="<?php echo $emp['ID_Empleado']; ?>">
                            <?php echo $emp['Nombre'] . ' ' . $emp['Apellido']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="help-text">Si eres empleado, selecciona tu nombre</div>
            </div>

            <button type="submit" class="btn-register">
                ✨ Crear Cuenta
            </button>
        </form>

        <div class="footer-text">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>