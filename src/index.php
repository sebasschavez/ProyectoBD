<?php
require_once 'auth.php';
require_once 'bootstrap.php';

use App\Models\Producto;
use App\Models\Venta;

// Obtener estadísticas usando Eloquent
$stats_productos = Producto::count();
$stats_stock_bajo = Producto::stockBajo()->count();

// Estadísticas de ventas de hoy
$ventas_hoy = Venta::hoy()->get();
$stats_ventas_hoy = [
    'total' => $ventas_hoy->count(),
    'monto' => $ventas_hoy->sum('Total')
];

// Productos con stock bajo
$productos_bajo_stock = Producto::stockBajo()
    ->orderBy('Cantidad_Stock', 'asc')
    ->limit(5)
    ->get();

// Últimas ventas con relación de empleado
$ultimas_ventas = Venta::with('empleado')
    ->orderBy('Venta_Fecha', 'desc')
    ->limit(5)
    ->get();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Don Manolo - Sistema de Gestión</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            flex: 1;
        }

        h1 {
            color: #667eea;
            font-size: 2.5em;
        }

        .subtitle {
            color: #666;
            font-size: 0.9em;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .user-info .username {
            font-weight: 600;
            color: #333;
        }

        .user-info .role {
            padding: 5px 12px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 0.85em;
        }

        nav {
            display: flex;
            gap: 15px;
            width: 100%;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-logout {
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #c82333;
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
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            color: #333;
            font-weight: 600;
        }

        td {
            padding: 12px;
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

        .badge-danger {
            background: #fee;
            color: #c33;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            header {
                flex-direction: column;
                text-align: center;
            }

            nav {
                flex-direction: column;
                width: 100%;
            }

            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-left">
                <h1>🏪 Tienda Don Manolo</h1>
                <p class="subtitle">Sistema de Gestión de Inventario y Ventas</p>
            </div>
            
            <div class="user-info">
                <div>
                    <div class="username">👤 <?php echo obtener_nombre_usuario(); ?></div>
                    <span class="role"><?php echo $_SESSION['rol']; ?></span>
                </div>
                <a href="logout.php" class="btn-logout" onclick="return confirm('¿Cerrar sesión?')">🚪 Salir</a>
            </div>

            <nav>
                <a href="index.php">📊 Inicio</a>
                <a href="productos.php">📦 Productos</a>
                <a href="ventas.php">💰 Ventas</a>
                <a href="proveedores.php">🚚 Proveedores</a>
            </nav>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?php echo $stats_productos; ?></div>
                <div class="stat-label">Productos en inventario</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-number"><?php echo $stats_stock_bajo; ?></div>
                <div class="stat-label">Productos con stock bajo</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number"><?php echo $stats_ventas_hoy['total']; ?></div>
                <div class="stat-label">Ventas hoy</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💵</div>
                <div class="stat-number"><?php echo formatear_dinero($stats_ventas_hoy['monto']); ?></div>
                <div class="stat-label">Ingresos de hoy</div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <h2>⚠️ Productos con Stock Bajo</h2>
                <?php if ($stats_stock_bajo > 0): ?>
                    <div class="alert alert-warning">
                        <strong>¡Atención!</strong> Hay <?php echo $stats_stock_bajo; ?> productos con menos de 10 unidades.
                    </div>
                <?php endif; ?>
                
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_bajo_stock as $producto): ?>
                        <tr>
                            <td><?php echo $producto->Nombre; ?></td>
                            <td><strong><?php echo $producto->Cantidad_Stock; ?></strong></td>
                            <td>
                                <?php if ($producto->Cantidad_Stock < 5): ?>
                                    <span class="badge badge-danger">Crítico</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Bajo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($productos_bajo_stock->count() == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #28a745;">
                                ✓ Todos los productos tienen stock suficiente
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>🛒 Últimas Ventas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Vendedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_ventas as $venta): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($venta->ID_Venta, 4, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo $venta->fecha_formateada; ?></td>
                            <td><strong><?php echo $venta->total_formateado; ?></strong></td>
                            <td><?php echo $venta->empleado->nombre_completo; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($ultimas_ventas->count() == 0): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666;">
                                No hay ventas registradas
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>