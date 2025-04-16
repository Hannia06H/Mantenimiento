<?php
// Iniciar sesión y verificar autenticación
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pizzería - Finalizar Pago</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .payment-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
        }
        .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
        }
        .payment-method.active {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .summary-item {
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

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
                    <li class="nav-item">
                        <a href="history.php" class="nav-link">Historial</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container py-5">
        <div class="row">
            <!-- Resumen del Pedido -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="card payment-card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-receipt me-2"></i>Resumen del Pedido</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Detalles de Entrega</h5>
                        <div class="mb-4">
                            <p class="mb-1"><strong>Dirección:</strong> <span id="delivery-address"></span></p>
                            <p class="mb-1"><strong>Teléfono:</strong> <span id="delivery-phone"></span></p>
                            <p class="mb-1"><strong>Instrucciones:</strong> <span id="delivery-notes"></span></p>
                            <a href="ordernew.php" class="btn btn-sm btn-outline-primary mt-2">Editar</a>
                        </div>
                        
                        <h5 class="mb-3">Items del Pedido</h5>
                        <div class="mb-3" id="order-items-summary">
                            <!-- Los items se cargarán con JavaScript -->
                        </div>
                        
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="summary-subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span id="summary-delivery">$2.50</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="summary-total">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Métodos de Pago -->
            <div class="col-lg-7">
                <div class="card payment-card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Método de Pago</h4>
                    </div>
                    <div class="card-body">
                        <!-- Opciones de Pago -->
                        <div class="mb-4">
                            <h5 class="mb-3">Selecciona método de pago</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="payment-method p-3 active" data-method="card" id="card-method">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" checked>
                                            <label class="form-check-label" for="creditCard">
                                                <i class="far fa-credit-card fa-2x me-2"></i> Tarjeta de Crédito/Débito
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-method p-3" data-method="cash" id="cash-method">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="cashOnDelivery">
                                            <label class="form-check-label" for="cashOnDelivery">
                                                <i class="fas fa-money-bill-wave fa-2x me-2"></i> Efectivo al entregar
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulario de Tarjeta -->
                        <div id="card-payment-form">
                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Número de Tarjeta</label>
                                <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
                                <small id="card-type" class="text-muted"></small>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="cardExpiry" class="form-label">Fecha de Expiración</label>
                                    <input type="text" class="form-control" id="cardExpiry" placeholder="MM/AA" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cardCvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cardCvv" placeholder="123" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="cardName" class="form-label">Nombre en la Tarjeta</label>
                                <input type="text" class="form-control" id="cardName" placeholder="Nombre como aparece en la tarjeta" required>
                            </div>
                        </div>
                        
                        <!-- Información para pago en efectivo -->
                        <div id="cash-payment-info" class="d-none">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i>Pago en Efectivo</h5>
                                <p class="mb-0">Por favor ten el monto exacto listo para cuando llegue el repartidor. El total a pagar es: <strong id="cash-total">$0.00</strong></p>
                            </div>
                        </div>
                        
                        <!-- Términos y Condiciones -->
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck">Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Términos y Condiciones</a></label>
                        </div>
                        
                        <!-- Botón de Confirmación -->
                        <button class="btn btn-primary btn-lg w-100 mt-3" id="confirm-payment">
                            <i class="fas fa-lock me-2"></i> Confirmar Pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2023 Pizzería Deliciosa. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Modal Términos y Condiciones -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Términos y Condiciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Aquí van los términos y condiciones de tu pizzería...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Pago -->
    <div class="modal fade" id="paymentConfirmation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">¡Pedido Confirmado!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tu pedido ha sido procesado exitosamente.</p>
                    <p><strong>Número de pedido:</strong> <span id="order-number"></span></p>
                    <p><strong>Dirección de entrega:</strong> <span id="delivery-details-modal"></span></p>
                    <p><span id="delivery-phone-modal"></span></p>
                    <p><strong>Tiempo estimado de entrega:</strong> <span id="delivery-eta"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script personalizado -->
    <script>
    $(document).ready(function() {
        // 1. Cargar datos del pedido desde localStorage
        function loadOrderData() {
            const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
            const deliveryInfo = JSON.parse(localStorage.getItem('deliveryInfo')) || {};
            
            console.log('Datos del carrito:', cart);
            console.log('Datos de entrega:', deliveryInfo);

            if (cart.length === 0 || !deliveryInfo.address || !deliveryInfo.phone) {
                alert('No se encontró información completa del pedido. Serás redirigido al carrito.');
                window.location.href = 'ordernew.php';
                return false;
            }

            $('#delivery-address').text(deliveryInfo.address);
            $('#delivery-phone').text(deliveryInfo.phone);
            $('#delivery-notes').text(deliveryInfo.notes || 'Ninguna');
            
            let subtotal = 0;
            let itemsHtml = '';
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                itemsHtml += `
                    <div class="d-flex justify-content-between summary-item py-2">
                        <span>${item.name} x${item.quantity}</span>
                        <span>$${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            const deliveryFee = 2.50;
            const total = subtotal + deliveryFee;
            
            $('#order-items-summary').html(itemsHtml);
            $('#summary-subtotal').text('$' + subtotal.toFixed(2));
            $('#summary-total').text('$' + total.toFixed(2));
            $('#cash-total').text('$' + total.toFixed(2));
            
            return true;
        }

        // Cargar datos al iniciar
        if (!loadOrderData()) {
            return;
        }

        // 2. Manejo de métodos de pago
        $('.payment-method').click(function() {
            $('.payment-method').removeClass('active');
            $(this).addClass('active');
            $('input[name="paymentMethod"]').prop('checked', false);
            $(this).find('input').prop('checked', true);
            
            if ($(this).data('method') === 'cash') {
                $('#card-payment-form').addClass('d-none');
                $('#cash-payment-info').removeClass('d-none');
            } else {
                $('#card-payment-form').removeClass('d-none');
                $('#cash-payment-info').addClass('d-none');
            }
        });

        // 3. Formatear y validar tarjeta
        $('#cardNumber').on('input', function() {
            let value = $(this).val().replace(/\D/g, '').substring(0, 16);
            if (value.length > 0) {
                value = value.match(/.{1,4}/g).join(' ');
            }
            $(this).val(value);
            detectCardType(value.replace(/\s/g, ''));
        });

        function detectCardType(number) {
            let cardType = '';
            if (/^4/.test(number)) {
                cardType = '<i class="fab fa-cc-visa me-1"></i> Visa';
            } else if (/^5[1-5]/.test(number)) {
                cardType = '<i class="fab fa-cc-mastercard me-1"></i> Mastercard';
            } else if (/^3[47]/.test(number)) {
                cardType = '<i class="fab fa-cc-amex me-1"></i> American Express';
            } else if (/^6(?:011|5)/.test(number)) {
                cardType = '<i class="fab fa-cc-discover me-1"></i> Discover';
            }
            $('#card-type').html(cardType);
        }

        $('#cardExpiry').on('input', function() {
            let value = $(this).val().replace(/\D/g, '').substring(0, 4);
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            $(this).val(value);
        });

        $('#cardCvv').on('input', function() {
            $(this).val($(this).val().replace(/\D/g, '').substring(0, 4));
        });

        $('#cardName').on('input', function() {
            $(this).val($(this).val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, ''));
        });

        // 4. Procesar pago
        $('#confirm-payment').click(function() {
            if (!$('#termsCheck').prop('checked')) {
                alert('Por favor acepta los Términos y Condiciones');
                return;
            }

            const paymentMethod = $('input[name="paymentMethod"]:checked').attr('id');
            const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
            const deliveryInfo = JSON.parse(localStorage.getItem('deliveryInfo')) || {};
            
            if (cart.length === 0 || !deliveryInfo.address || !deliveryInfo.phone) {
                alert('Información de pedido incompleta');
                return;
            }

            if (paymentMethod === 'creditCard') {
                const cardValidation = validateCard();
                if (!cardValidation.valid) {
                    alert(cardValidation.message);
                    return;
                }
            }

            $('#loading-overlay').fadeIn();
            
            const orderData = {
                cart: cart.map(item => ({
                    id_producto: item.id,
                    name: item.name,
                    quantity: item.quantity,
                    price: item.price,
                    notes: item.notes || null
                })),
                deliveryInfo: {
                    address: deliveryInfo.address,
                    phone: deliveryInfo.phone,
                    notes: deliveryInfo.notes || null
                },
                payment: {
                    method: paymentMethod
                },
                amounts: {
                    subtotal: parseFloat($('#summary-subtotal').text().replace('$', '')),
                    delivery: 2.50,
                    total: parseFloat($('#summary-total').text().replace('$', ''))
                },
                userId: <?php echo $_SESSION['user_id'] ?? 'null'; ?>
            };

            console.log('Enviando datos:', orderData);

            $.ajax({
                url: 'procesar_pago.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(orderData),
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta:', response);
                    if (response?.success) {
                        localStorage.removeItem('pizzaCart');
                        if (response.orderId) {
                            window.location.href = 'confirmacion.php?id=' + response.orderId;
                        } else {
                            showOrderConfirmation({
                                id: 'temp-' + Date.now(),
                                address: deliveryInfo.address,
                                phone: deliveryInfo.phone
                            });
                        }
                    } else {
                        alert(response?.message || 'Error desconocido');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Error al procesar el pago. Intente nuevamente.');
                },
                complete: function() {
                    $('#loading-overlay').fadeOut();
                }
            });
        });

        function validateCard() {
            const cardNumber = $('#cardNumber').val().replace(/\s+/g, '');
            const cardExpiry = $('#cardExpiry').val();
            const cardCvv = $('#cardCvv').val();
            const cardName = $('#cardName').val();
            
            if (!/^\d{16}$/.test(cardNumber)) {
                $('#cardNumber').addClass('is-invalid');
                return { valid: false, message: 'Número de tarjeta inválido (16 dígitos)' };
            }
            
            if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
                $('#cardExpiry').addClass('is-invalid');
                return { valid: false, message: 'Fecha inválida (MM/AA)' };
            }
            
            const [month, year] = cardExpiry.split('/');
            const currentYear = new Date().getFullYear() % 100;
            const currentMonth = new Date().getMonth() + 1;
            
            if (parseInt(year) < currentYear || (parseInt(year) === currentYear && parseInt(month) < currentMonth)) {
                $('#cardExpiry').addClass('is-invalid');
                return { valid: false, message: 'Tarjeta expirada' };
            }
            
            if (!/^\d{3,4}$/.test(cardCvv)) {
                $('#cardCvv').addClass('is-invalid');
                return { valid: false, message: 'CVV inválido (3-4 dígitos)' };
            }
            
            if (cardName.trim().length < 3) {
                $('#cardName').addClass('is-invalid');
                return { valid: false, message: 'Nombre inválido' };
            }
            
            $('#cardNumber, #cardExpiry, #cardCvv, #cardName').removeClass('is-invalid');
            return { valid: true };
        }

        function showOrderConfirmation(order) {
            $('#order-number').text(order.id);
            $('#delivery-details-modal').text(order.address);
            $('#delivery-phone-modal').text('Teléfono: ' + order.phone);
            $('#delivery-eta').text(Math.floor(Math.random() * 16) + 30 + ' minutos');
            
            const modal = new bootstrap.Modal(document.getElementById('paymentConfirmation'));
            modal.show();
            
            $('#paymentConfirmation').on('hidden.bs.modal', function() {
                window.location.href = 'history.php';
            });
        }

        // Inicializar tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
</html>