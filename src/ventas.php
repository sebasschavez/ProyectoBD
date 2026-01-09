<?php
require_once 'config.php';

$mensaje = '';
$tipo_mensaje = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['realizar_venta'])) {
    $id_empleado = intval($_POST['empleado']);
    $productos_venta = $_POST['productos'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    
    if (empty($productos_venta)) {
        $mensaje = 'Debe agregar al menos un producto a la venta';
        $tipo_mensaje = 'error';
    } else {
        $conn->begin_transaction();
        
        try {
            $total = 0;
            foreach ($productos_venta as $key => $id_producto) {
                $cantidad = intval($cantidades[$key]);
                $result = $conn->query("SELECT Precio_Venta, Cantidad_Stock FROM Producto WHERE ID_Producto = $id_producto");
                $producto = $result->fetch_assoc();
                
                if ($producto['Cantidad_Stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente para el producto");
                }
                
                $total += $producto['Precio_Venta'] * $cantidad;
            }
            
            $conn->query("INSERT INTO Venta (ID_Empleado, Total, Venta_Fecha) VALUES ($id_empleado, $total, NOW())");
            $id_venta = $conn->insert_id;
            
            foreach ($productos_venta as $key => $id_producto) {
                $cantidad = intval($cantidades[$key]);
                $result = $conn->query("SELECT Precio_Venta FROM Producto WHERE ID_Producto = $id_producto");
                $producto = $result->fetch_assoc();
                $precio = $producto['Precio_Venta'];
                
                $conn->query("INSERT INTO Detalle_Venta (ID_Venta, ID_Producto, Cantidad_vendida, Precio_en_Venta) 
                             VALUES ($id_venta, $id_producto, $cantidad, $precio)");
                
                $conn->query("UPDATE Producto SET Cantidad_Stock = Cantidad_Stock - $cantidad WHERE ID_Producto = $id_producto");
            }
            
            $conn->commit();
            $mensaje = 'Venta realizada exitosamente. Folio: #' . str_pad($id_venta, 4, '0', STR_PAD_LEFT);
            $tipo_mensaje = 'success';
            
        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = 'Error al realizar la venta: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}

$empleados = $conn->query("SELECT * FROM Empleado ORDER BY Nombre");

$productos = $conn->query("SELECT * FROM Producto WHERE Cantidad_Stock > 0 ORDER BY Nombre");

$ventas_hoy = $conn->query("
    SELECT v.*, e.Nombre, e.Apellido,
           (SELECT COUNT(*) FROM Detalle_Venta WHERE ID_Venta = v.ID_Venta) as items
    FROM Venta v
    JOIN Empleado e ON v.ID_Empleado = e.ID_Empleado
    WHERE DATE(v.Venta_Fecha) = CURDATE()
    ORDER BY v.Venta_Fecha DESC
");

$stats = $conn->query("
    SELECT 
        COUNT(*) as total_ventas,
        IFNULL(SUM(Total), 0) as total_ingresos,
        IFNULL(AVG(Total), 0) as ticket_promedio
    FROM Venta 
    WHERE DATE(Venta_Fecha) = CURDATE()
")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Tienda Don Manolo</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        input, select {
            width: 100%;
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

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
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
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .producto-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 15px;
            align-items: center;
        }

        #carrito {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        #total-venta {
            font-size: 2em;
            color: #667eea;
            font-weight: bold;
            text-align: right;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .producto-item {
                grid-template-columns: 1fr;
            }
            
            nav {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>💰 Sistema de Ventas</h1>
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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_ventas']; ?></div>
                <div class="stat-label">Ventas realizadas hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo formatear_dinero($stats['total_ingresos']); ?></div>
                <div class="stat-label">Ingresos del día</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo formatear_dinero($stats['ticket_promedio']); ?></div>
                <div class="stat-label">Ticket promedio</div>
            </div>
        </div>

        <div class="card">
            <h2>🛒 Nueva Venta</h2>
            <form method="POST" id="form-venta">
                <div class="form-group">
                    <label>Vendedor *</label>
                    <select name="empleado" required>
                        <option value="">Seleccione un empleado...</option>
                        <?php while ($emp = $empleados->fetch_assoc()): ?>
                        <option value="<?php echo $emp['ID_Empleado']; ?>">
                            <?php echo $emp['Nombre'] . ' ' . $emp['Apellido']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Agregar Productos</label>
                    <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 10px;">
                        <select id="producto-select">
                            <option value="">Seleccione un producto...</option>
                            <?php while ($prod = $productos->fetch_assoc()): ?>
                            <option value="<?php echo $prod['ID_Producto']; ?>" 
                                    data-nombre="<?php echo htmlspecialchars($prod['Nombre']); ?>"
                                    data-precio="<?php echo $prod['Precio_Venta']; ?>"
                                    data-stock="<?php echo $prod['Cantidad_Stock']; ?>">
                                <?php echo $prod['Nombre']; ?> - <?php echo formatear_dinero($prod['Precio_Venta']); ?> 
                                (Stock: <?php echo $prod['Cantidad_Stock']; ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="number" id="cantidad-input" min="1" value="1" placeholder="Cantidad">
                        <button type="button" onclick="agregarProducto()" class="btn-secondary">
                            ➕ Agregar
                        </button>
                    </div>
                </div>

                <div id="carrito">
                    <h3>Carrito de Compra</h3>
                    <div id="lista-productos"></div>
                    <div id="total-venta">Total: $0.00</div>
                </div>

                <button type="submit" name="realizar_venta" style="margin-top: 20px;">
                    💳 Realizar Venta
                </button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Ventas de Hoy</h2>
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Hora</th>
                        <th>Vendedor</th>
                        <th>Items</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ventas_hoy->num_rows > 0): ?>
                        <?php while ($venta = $ventas_hoy->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($venta['ID_Venta'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo date('H:i', strtotime($venta['Venta_Fecha'])); ?></td>
                            <td><?php echo $venta['Nombre'] . ' ' . $venta['Apellido']; ?></td>
                            <td><?php echo $venta['items']; ?> productos</td>
                            <td><strong><?php echo formatear_dinero($venta['Total']); ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666;">
                                No hay ventas registradas hoy
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let carrito = [];
        let total = 0;

        function agregarProducto() {
            const select = document.getElementById('producto-select');
            const cantidad = parseInt(document.getElementById('cantidad-input').value);
            
            if (!select.value || cantidad < 1) {
                alert('Seleccione un producto y cantidad válida');
                return;
            }

            const option = select.options[select.selectedIndex];
            const id = option.value;
            const nombre = option.dataset.nombre;
            const precio = parseFloat(option.dataset.precio);
            const stock = parseInt(option.dataset.stock);

            if (cantidad > stock) {
                alert('Stock insuficiente. Disponible: ' + stock);
                return;
            }

            const existe = carrito.findIndex(item => item.id === id);
            if (existe !== -1) {
                carrito[existe].cantidad += cantidad;
            } else {
                carrito.push({ id, nombre, precio, cantidad, stock });
            }

            actualizarCarrito();
            document.getElementById('cantidad-input').value = 1;
        }

        function eliminarProducto(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
        }

        function actualizarCarrito() {
            const lista = document.getElementById('lista-productos');
            lista.innerHTML = '';
            total = 0;

            if (carrito.length === 0) {
                lista.innerHTML = '<p style="text-align: center; color: #666;">El carrito está vacío</p>';
            } else {
                carrito.forEach((item, index) => {
                    const subtotal = item.precio * item.cantidad;
                    total += subtotal;

                    const div = document.createElement('div');
                    div.className = 'producto-item';
                    div.innerHTML = `
                        <div>
                            <strong>${item.nombre}</strong><br>
                            <small>${item.cantidad} × $${item.precio.toFixed(2)}</small>
                        </div>
                        <div style="text-align: right;">
                            <strong>$${subtotal.toFixed(2)}</strong>
                        </div>
                        <button type="button" class="btn-danger" onclick="eliminarProducto(${index})" 
                                style="padding: 8px 16px;">🗑️</button>
                        <input type="hidden" name="productos[]" value="${item.id}">
                        <input type="hidden" name="cantidades[]" value="${item.cantidad}">
                    `;
                    lista.appendChild(div);
                });
            }

            document.getElementById('total-venta').textContent = 'Total: $' + total.toFixed(2);
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>