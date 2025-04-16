<?php
header('Content-Type: application/json');
session_start();
$db = require 'config/database.php';

// Habilitar reporte de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener datos JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar datos recibidos
file_put_contents('payment_log.txt', date('Y-m-d H:i:s')."\n".print_r($data, true)."\n\n", FILE_APPEND);

try {
    // Validación básica
    if (!$data) {
        throw new Exception("No se recibieron datos del pedido");
    }
    
    if (empty($data['cart']) || empty($data['deliveryInfo'])) {
        throw new Exception("Datos incompletos del pedido");
    }

    $db->beginTransaction();

    // 1. Determinar método de pago
    $metodoPago = ($data['payment']['method'] === 'creditCard') ? 1 : 2;

    // 2. Generar código único para el pedido
    $codigoPedido = 'PED-' . strtoupper(uniqid());

    // 3. Insertar pedido principal
    $stmtPedido = $db->prepare("INSERT INTO pedidos (
        codigo_pedido,
        id_usuario,
        estado,
        direccion_entrega,
        telefono_contacto,
        id_metodo_pago,
        subtotal,
        costo_envio,
        total,
        notas
    ) VALUES (?, ?, 'pendiente', ?, ?, ?, ?, ?, ?, ?)");

    $stmtPedido->execute([
        $codigoPedido,
        $_SESSION['user_id'],
        $data['deliveryInfo']['address'],
        $data['deliveryInfo']['phone'],
        $metodoPago,
        $data['amounts']['subtotal'],
        $data['amounts']['delivery'],
        $data['amounts']['total'],
        $data['deliveryInfo']['notes'] ?? null
    ]);

    $idPedido = $db->lastInsertId();

    // 4. Insertar detalles del pedido
    foreach ($data['cart'] as $item) {
        $stmtDetalle = $db->prepare("INSERT INTO detalles_pedido (
            id_pedido,
            id_producto,
            cantidad,
            precio_unitario,
            subtotal,
            notas
        ) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmtDetalle->execute([
            $idPedido,
            $item['id_producto'],
            $item['quantity'],
            $item['price'],
            $item['price'] * $item['quantity'],
            $item['notes'] ?? null
        ]);
    }

    $db->commit();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'orderId' => $idPedido,
        'codigoPedido' => $codigoPedido
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    // Registrar error en logs
    error_log("Error en processar_pago.php: " . $e->getMessage());
    file_put_contents('payment_errors.log', date('Y-m-d H:i:s')." - ".$e->getMessage()."\n", FILE_APPEND);
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el pago. Intente nuevamente.',
        'error' => $e->getMessage()
    ]);
}
?>