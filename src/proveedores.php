<?php
require_once 'config.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = limpiar_entrada($_POST['nombre']);
    $email = limpiar_entrada($_POST['email']);
    $telefono = limpiar_entrada($_POST['telefono']);
    
    $sql = "INSERT INTO Proveedor (Nombre_Empresa, Email, Telefono) VALUES ('$nombre', '$email', '$telefono')";
    
    if ($conn->query($sql)) {
        $mensaje = 'Proveedor agregado exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al agregar proveedor: ' . $conn->error;
        $tipo_mensaje = 'error';
    }
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    if ($conn->query("DELETE FROM Proveedor WHERE ID_Proveedor = $id")) {
        $mensaje = 'Proveedor eliminado exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar proveedor';
        $tipo_mensaje = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asignar'])) {
    $id_proveedor = intval($_POST['proveedor']);
    $id_producto = intval($_POST['producto']);
    $costo = floatval($_POST['costo']);
    
    $sql = "INSERT INTO Suministra (ID_Proveedor, ID_Producto, Costo_de_Compra) 
            VALUES ($id_proveedor, $id_producto, $costo)
            ON DUPLICATE KEY UPDATE Costo_de_Compra = $costo";
    
    if ($conn->query($sql)) {
        $mensaje = 'Producto asignado al proveedor exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al asignar producto: ' . $conn->error;
        $tipo_mensaje = 'error';
    }
}

$proveedores = $conn->query("SELECT * FROM Proveedor ORDER BY Nombre_Empresa");

$productos = $conn->query("SELECT * FROM Producto ORDER BY Nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - Tienda Don Manolo</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        nav {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        nav a {
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 600;
        }

        nav a:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .mensaje {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        input, select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        button, .btn {
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s;
        }

        button:hover, .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-info {
            background: #17a2b8;
        }

        .btn-info:hover {
            background: #138496;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            color: #333;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .proveedor-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .proveedor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .proveedor-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .productos-list {
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            nav {
                flex-direction: column;
            }

            .proveedor-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🚚 Gestión de Proveedores</h1>
            <nav>
                <a href="index.php">📊 Inicio</a>
                <a href="productos.php">📦 Productos</a>
                <a href="ventas.php">💰 Ventas</a>
                <a href="proveedores.php">🚚 Proveedores</a>
            </nav>
        </header>

        <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <h2>➕ Agregar Nuevo Proveedor</h2>
            <form method="POST" style="margin-top: 20px;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre de la Empresa *</label>
                        <input type="text" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" placeholder="5512345678">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="contacto@empresa.com">
                </div>

                <button type="submit" name="agregar" style="margin-top: 15px;">
                    ➕ Agregar Proveedor
                </button>
            </form>
        </div>

        <div class="card">
            <h2>🔗 Asignar Producto a Proveedor</h2>
            <form method="POST" style="margin-top: 20px;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Proveedor *</label>
                        <select name="proveedor" required>
                            <option value="">Seleccione un proveedor...</option>
                            <?php 
                            $proveedores_temp = $conn->query("SELECT * FROM Proveedor ORDER BY Nombre_Empresa");
                            while ($prov = $proveedores_temp->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $prov['ID_Proveedor']; ?>">
                                <?php echo $prov['Nombre_Empresa']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Producto *</label>
                        <select name="producto" required>
                            <option value="">Seleccione un producto...</option>
                            <?php 
                            $productos_temp = $conn->query("SELECT * FROM Producto ORDER BY Nombre");
                            while ($prod = $productos_temp->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $prod['ID_Producto']; ?>">
                                <?php echo $prod['Nombre']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Costo de Compra *</label>
                        <input type="number" name="costo" step="0.01" min="0" required>
                    </div>
                </div>

                <button type="submit" name="asignar" style="margin-top: 15px;">
                    🔗 Asignar Producto
                </button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Lista de Proveedores</h2>
            
            <?php 
            $proveedores = $conn->query("SELECT * FROM Proveedor ORDER BY Nombre_Empresa");
            while ($prov = $proveedores->fetch_assoc()): 
                $id_prov = $prov['ID_Proveedor'];
                $productos_prov = $conn->query("
                    SELECT p.*, s.Costo_de_Compra 
                    FROM Producto p
                    JOIN Suministra s ON p.ID_Producto = s.ID_Producto
                    WHERE s.ID_Proveedor = $id_prov
                    ORDER BY p.Nombre
                ");
            ?>
            
            <div class="proveedor-card">
                <div class="proveedor-header">
                    <h3 style="color: #667eea;">🏢 <?php echo $prov['Nombre_Empresa']; ?></h3>
                    <a href="?eliminar=<?php echo $prov['ID_Proveedor']; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('¿Eliminar este proveedor?')"
                       style="padding: 8px 16px; font-size: 0.9em; text-decoration: none;">
                        🗑️ Eliminar
                    </a>
                </div>

                <div class="proveedor-info">
                    <div class="info-item">
                        <strong>📞 Teléfono:</strong>
                        <span><?php echo $prov['Telefono'] ?: 'No registrado'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>📧 Email:</strong>
                        <span><?php echo $prov['Email'] ?: 'No registrado'; ?></span>
                    </div>
                </div>

                <?php if ($productos_prov->num_rows > 0): ?>
                <div class="productos-list">
                    <strong>📦 Productos que suministra:</strong>
                    <table style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio de Venta</th>
                                <th>Costo de Compra</th>
                                <th>Margen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($prod = $productos_prov->fetch_assoc()): 
                                $margen = $prod['Precio_Venta'] - $prod['Costo_de_Compra'];
                                $porcentaje = ($margen / $prod['Costo_de_Compra']) * 100;
                            ?>
                            <tr>
                                <td><?php echo $prod['Nombre']; ?></td>
                                <td><?php echo formatear_dinero($prod['Precio_Venta']); ?></td>
                                <td><?php echo formatear_dinero($prod['Costo_de_Compra']); ?></td>
                                <td>
                                    <strong style="color: #28a745;">
                                        <?php echo formatear_dinero($margen); ?>
                                    </strong>
                                    <small>(<?php echo number_format($porcentaje, 1); ?>%)</small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div style="padding: 15px; text-align: center; color: #666;">
                    Este proveedor aún no tiene productos asignados
                </div>
                <?php endif; ?>
            </div>

            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>