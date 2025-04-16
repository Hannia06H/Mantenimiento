<?php
header('Content-Type: application/json');

// Obtener el contenido JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar datos básicos
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Procesar el pedido (ejemplo simplificado)
try {
    // Aquí iría tu lógica para guardar en la base de datos
    
    $response = [
        'success' => true,
        'orderId' => 'PZ-' . uniqid(),
        'message' => 'Pedido procesado correctamente'
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}