<?php
session_start();
require 'config/database.php';

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obtiene el historial de pedidos del usuario
$stmt = $pdo->prepare("
    SELECT p.*, 
           GROUP_CONCAT(CONCAT(dp.cantidad, 'x ', pr.nombre, ' ($', dp.precio_unitario, ')')) AS items,
           SUM(dp.cantidad * dp.precio_unitario) AS total
    FROM pedidos p
    JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
    JOIN productos pr ON dp.id_producto = pr.id_producto
    WHERE p.id_usuario = ?
    GROUP BY p.id_pedido
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([$_SESSION['user_id']]);
$pedidos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pizzería - Historial de Pedidos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .order-card {
            border-radius: 10px;
            transition: all 0.3s;
            overflow: hidden;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .status-preparando {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-camino {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-entregado {
            background-color: #d4edda;
            color: #155724;
        }
        .item-list {
            max-height: 150px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="indexnew.php">
                <span class="fas fa-pizza-slice mr-2"></span>Pizzería <small>Deliciosa</small>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="indexnew.php" class="nav-link">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a href="viewmenu.php" class="nav-link">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a href="ordernew.php" class="nav-link">Carrito</a>
                    </li>
                    <li class="nav-item active">
                        <a href="historial.php" class="nav-link">Historial</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="font-weight-bold"><i class="fas fa-history mr-2"></i>Historial de Pedidos</h2>
                <p class="text-muted">Tus pedidos recientes</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php if (empty($pedidos)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes pedidos recientes</h5>
                        <a href="viewmenu.php" class="btn btn-primary mt-3">Ordenar Ahora</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="card mb-3 order-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Pedido #<?= $pedido['id_pedido'] ?></h5>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                                </div>
                                <span class="status-badge <?= 
                                    $pedido['estado'] == 'preparando' ? 'status-preparando' : 
                                    ($pedido['estado'] == 'en camino' ? 'status-camino' : 'status-entregado') 
                                ?>">
                                    <?= ucfirst($pedido['estado']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Resumen:</h6>
                                        <p><?= count(explode(',', $pedido['items'])) ?> items - Total: <strong>$<?= number_format($pedido['total'], 2) ?></strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Items:</h6>
                                        <div class="item-list">
                                            <?php foreach (explode(',', $pedido['items']) as $item): ?>
                                                <p class="mb-1"><?= $item ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
