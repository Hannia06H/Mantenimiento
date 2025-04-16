<?php
// Iniciar sesión y verificar autenticación
session_start();
$db = require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pizzería - Mi Pedido</title>
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
        .pizza-item {
            transition: all 0.3s ease;
        }
        .pizza-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .order-summary {
            position: sticky;
            top: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
        }
        .btn-primary {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
        }
        .btn-primary:hover {
            background-color: #ff5252;
            border-color: #ff5252;
        }
        .color {
            background-color: #f8b500;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
            display: none;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
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
                    <li class="nav-item active">
                        <a href="ordernew.php" class="nav-link">Carrito</a>
                    </li>
                    <li class="nav-item">
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
        <div class="row">
            <!-- Resumen del Pedido -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header color text-white">
                        <h4 class="mb-0"><i class="fas fa-shopping-cart mr-2"></i>Tu Pedido</h4>
                    </div>
                    <div class="card-body">
                        <div id="cart-items-container">
                            <div class="text-center py-4" id="empty-cart-message">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Cargando tu pedido...</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de entrega -->
                <form id="delivery-form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo*</label>
                        <input type="text" class="form-control" id="name" required>
                        <div class="invalid-feedback">Por favor ingresa tu nombre completo</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono*</label>
                        <input type="tel" class="form-control" id="phone" required>
                        <div class="invalid-feedback">Por favor ingresa tu teléfono</div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección*</label>
                        <input type="text" class="form-control" id="address" required>
                        <div class="invalid-feedback">Por favor ingresa tu dirección</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Instrucciones adicionales</label>
                        <textarea class="form-control" id="notes" rows="3"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" id="proceed-to-payment">
                        <i class="fas fa-credit-card mr-2"></i>Continuar al Pago
                    </button>
                </form>
            </div>

            <!-- Resumen de Compra -->
            <div class="col-lg-4">
                <div class="card order-summary">
                    <div class="card-header color text-white">
                        <h4 class="mb-0"><i class="fas fa-receipt mr-2"></i>Resumen</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span id="delivery-fee">$2.50</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total">$2.50</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Pizzería Deliciosa. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar carrito desde localStorage
            function loadCart() {
                const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
                const cartContainer = $('#cart-items-container');
                
                if (cart.length === 0) {
                    cartContainer.html(`
                        <div class="text-center py-4" id="empty-cart-message">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tu carrito está vacío</h5>
                            <a href="viewmenu.php" class="btn btn-primary mt-3">Ir al Menú</a>
                        </div>
                    `);
                    $('#subtotal').text('$0.00');
                    $('#total').text('$2.50');
                    return;
                }

                let itemsHtml = '';
                let subtotal = 0;
                
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    itemsHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-3 pizza-item p-3 border rounded">
                            <div class="d-flex align-items-center">
                                <img src="images/pizza-${item.id}.jpg" alt="${item.name}" class="rounded mr-3" width="60" height="60">
                                <div>
                                    <h6 class="mb-1">${item.name}</h6>
                                    <small class="text-muted">$${item.price.toFixed(2)} c/u</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="input-group quantity-selector" style="width: 120px;">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-secondary decrease-quantity" type="button" data-index="${index}">-</button>
                                    </div>
                                    <input type="text" class="form-control text-center quantity-input" value="${item.quantity}" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary increase-quantity" type="button" data-index="${index}">+</button>
                                    </div>
                                </div>
                                <span class="ml-3 font-weight-bold" style="width: 70px; text-align: right;">$${itemTotal.toFixed(2)}</span>
                                <button class="btn btn-sm btn-outline-danger ml-2 remove-item" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                cartContainer.html(itemsHtml);
                $('#subtotal').text('$' + subtotal.toFixed(2));
                $('#total').text('$' + (subtotal + 2.5).toFixed(2));
            }

            // Cargar el carrito al iniciar
            loadCart();

            // Eventos para modificar cantidades
            $(document).on('click', '.increase-quantity', function() {
                const index = $(this).data('index');
                const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
                cart[index].quantity++;
                localStorage.setItem('pizzaCart', JSON.stringify(cart));
                loadCart();
            });
            
            $(document).on('click', '.decrease-quantity', function() {
                const index = $(this).data('index');
                const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                } else {
                    cart.splice(index, 1);
                }
                localStorage.setItem('pizzaCart', JSON.stringify(cart));
                loadCart();
            });
            
            $(document).on('click', '.remove-item', function() {
                const index = $(this).data('index');
                const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
                cart.splice(index, 1);
                localStorage.setItem('pizzaCart', JSON.stringify(cart));
                loadCart();
            });

            // Validación de campos en tiempo real
            $('#delivery-form input').on('input', function() {
                if ($(this).val().trim() === '' && $(this).prop('required')) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Procesar pago
            $('#proceed-to-payment').click(function() {
                // Validar campos obligatorios
                let isValid = true;
                $('#delivery-form [required]').each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    alert('Por favor completa todos los campos obligatorios');
                    return;
                }

                const cart = JSON.parse(localStorage.getItem('pizzaCart')) || [];
                if (cart.length === 0) {
                    alert('Tu carrito está vacío. Agrega algunos productos primero.');
                    return;
                }

                // Guardar datos de entrega
                const deliveryInfo = {
                    name: $('#name').val().trim(),
                    phone: $('#phone').val().trim(),
                    address: $('#address').val().trim(),
                    notes: $('#notes').val().trim()
                };
                
                localStorage.setItem('deliveryInfo', JSON.stringify(deliveryInfo));

                // Redirigir a payment.php
                window.location.href = 'payment.php';
            });
        });
    </script>
</body>
</html>