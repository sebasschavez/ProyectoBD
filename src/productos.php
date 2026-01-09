<?php
require_once 'config.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = limpiar_entrada($_POST['nombre']);
    $descripcion = limpiar_entrada($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $cantidad = intval($_POST['cantidad']);
    $tipo = limpiar_entrada($_POST['tipo']);
    
    $sql = "INSERT INTO Producto (Nombre, Descripcion, Precio_Venta, Cantidad_Stock, Tipo_Producto";
    $values = "VALUES ('$nombre', '$descripcion', $precio, $cantidad, '$tipo'";
    
    if ($tipo == 'Perecedero') {
        $fecha_cad = limpiar_entrada($_POST['fecha_caducidad']);
        $refrig = isset($_POST['refrigeracion']) ? 1 : 0;
        $sql .= ", Fecha_Caducidad, Requiere_Refrigeracion";
        $values .= ", '$fecha_cad', $refrig";
    } else {
        $marca = limpiar_entrada($_POST['marca']);
        $contenido = limpiar_entrada($_POST['contenido']);
        $sql .= ", Marca, Contenido_Neto";
        $values .= ", '$marca', '$contenido'";
    }
    
    $sql .= ") " . $values . ")";
    
    if ($conn->query($sql)) {
        $mensaje = 'Producto agregado exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al agregar producto: ' . $conn->error;
        $tipo_mensaje = 'error';
    }
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    if ($conn->query("DELETE FROM Producto WHERE ID_Producto = $id")) {
        $mensaje = 'Producto eliminado exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar producto';
        $tipo_mensaje = 'error';
    }
}

$filtro = isset($_GET['filtro']) ? limpiar_entrada($_GET['filtro']) : '';
$tipo_filtro = isset($_GET['tipo']) ? limpiar_entrada($_GET['tipo']) : '';

$sql = "SELECT * FROM Producto WHERE 1=1";
if ($filtro) {
    $sql .= " AND (Nombre LIKE '%$filtro%' OR Descripcion LIKE '%$filtro%')";
}
if ($tipo_filtro) {
    $sql .= " AND Tipo_Producto = '$tipo_filtro'";
}
$sql .= " ORDER BY Nombre ASC";

$productos = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Tienda Don Manolo</title>
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

        .filtros {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        input, select, textarea {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .filtros input {
            flex: 1;
            min-width: 250px;
        }

        .filtros select {
            min-width: 150px;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
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
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-perecedero {
            background: #fff3cd;
            color: #856404;
        }

        .badge-abarrote {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-bajo {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-ok {
            background: #d4edda;
            color: #155724;
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input {
            width: auto;
        }

        #campos_tipo {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            nav {
                flex-direction: column;
            }

            table {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Gestión de Productos</h1>
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
            <h2>➕ Agregar Nuevo Producto</h2>
            <form method="POST" style="margin-top: 20px;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre del Producto *</label>
                        <input type="text" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Producto *</label>
                        <select name="tipo" id="tipo_producto" required onchange="mostrarCamposTipo()">
                            <option value="">Seleccione...</option>
                            <option value="Perecedero">Perecedero</option>
                            <option value="Abarrote">Abarrote</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Precio de Venta *</label>
                        <input type="number" name="precio" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Cantidad en Stock *</label>
                        <input type="number" name="cantidad" min="0" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3"></textarea>
                </div>

                <div id="campos_tipo" style="display: none;">
                    <div id="campos_perecedero" style="display: none;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Fecha de Caducidad</label>
                                <input type="date" name="fecha_caducidad">
                            </div>
                            <div class="form-group checkbox-group">
                                <input type="checkbox" name="refrigeracion" id="refrigeracion">
                                <label for="refrigeracion">¿Requiere Refrigeración?</label>
                            </div>
                        </div>
                    </div>

                    <div id="campos_abarrote" style="display: none;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Marca</label>
                                <input type="text" name="marca">
                            </div>
                            <div class="form-group">
                                <label>Contenido Neto</label>
                                <input type="text" name="contenido" placeholder="Ej: 1kg, 500ml">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" name="agregar" style="margin-top: 20px;">
                    ➕ Agregar Producto
                </button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Lista de Productos</h2>
            <form method="GET" class="filtros">
                <input type="text" name="filtro" placeholder="Buscar producto..." 
                       value="<?php echo htmlspecialchars($filtro); ?>">
                <select name="tipo">
                    <option value="">Todos los tipos</option>
                    <option value="Perecedero" <?php echo $tipo_filtro == 'Perecedero' ? 'selected' : ''; ?>>Perecedero</option>
                    <option value="Abarrote" <?php echo $tipo_filtro == 'Abarrote' ? 'selected' : ''; ?>>Abarrote</option>
                </select>
                <button type="submit">🔍 Buscar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($prod = $productos->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $prod['ID_Producto']; ?></strong></td>
                        <td>
                            <strong><?php echo $prod['Nombre']; ?></strong><br>
                            <small style="color: #666;"><?php echo $prod['Descripcion']; ?></small>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($prod['Tipo_Producto']); ?>">
                                <?php echo $prod['Tipo_Producto']; ?>
                            </span>
                        </td>
                        <td><strong><?php echo formatear_dinero($prod['Precio_Venta']); ?></strong></td>
                        <td><strong><?php echo $prod['Cantidad_Stock']; ?></strong></td>
                        <td>
                            <?php if ($prod['Cantidad_Stock'] < 10): ?>
                                <span class="badge badge-bajo">Stock Bajo</span>
                            <?php else: ?>
                                <span class="badge badge-ok">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?eliminar=<?php echo $prod['ID_Producto']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('¿Eliminar este producto?')"
                               style="padding: 8px 16px; font-size: 0.9em; text-decoration: none; display: inline-block;">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function mostrarCamposTipo() {
            const tipo = document.getElementById('tipo_producto').value;
            const camposTipo = document.getElementById('campos_tipo');
            const camposPer = document.getElementById('campos_perecedero');
            const camposAba = document.getElementById('campos_abarrote');

            camposTipo.style.display = 'none';
            camposPer.style.display = 'none';
            camposAba.style.display = 'none';

            if (tipo === 'Perecedero') {
                camposTipo.style.display = 'block';
                camposPer.style.display = 'block';
            } else if (tipo === 'Abarrote') {
                camposTipo.style.display = 'block';
                camposAba.style.display = 'block';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>