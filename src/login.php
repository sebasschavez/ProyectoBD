<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = limpiar_entrada($_POST['usuario']);
    $contrasena = $_POST['contrasena'];
    
    $sql = "SELECT * FROM Usuario WHERE Nombre_Usuario = '$usuario' AND Activo = 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($contrasena, $user['Contrasena'])) {
            $_SESSION['usuario_id'] = $user['ID_Usuario'];
            $_SESSION['nombre_usuario'] = $user['Nombre_Usuario'];
            $_SESSION['rol'] = $user['Rol'];
            $_SESSION['id_empleado'] = $user['ID_Empleado'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tienda Don Manolo</title>
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

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
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
            margin-bottom: 25px;
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

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .input-group input:focus {
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

        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
            font-size: 0.9em;
        }

        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-login:active {
            transform: translateY(0);
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

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .logo h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🏪</h1>
            <h1>Tienda Don Manolo</h1>
            <p>Sistema de Gestión</p>
        </div>

        <?php if ($error): ?>
        <div class="error">
            ⚠️ <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input type="text" name="usuario" required autofocus placeholder="Ingresa tu usuario">
                </div>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="contrasena" required placeholder="Ingresa tu contraseña">
                </div>
            </div>

            <button type="submit" class="btn-login">
                🚀 Iniciar Sesión
            </button>
        </form>

        <div class="footer-text">
            ¿No tienes cuenta? <a href="registro.php">Registrarse</a>
        </div>
    </div>
</body>
</html>