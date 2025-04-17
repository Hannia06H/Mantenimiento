<?php
session_start();
require 'config/database.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pizzería Deliciosa - Inicio</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .hero-section {
            background: url('images/bg_1.jpg') no-repeat center center;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            
        }
        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
        }
        .pizza-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .pizza-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .special-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
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
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a href="indexnew.php" class="nav-link">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a href="viewmenu.php" class="nav-link">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a href="ordernew.php" class="nav-link">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a href="historial.php" class="nav-link">Historial</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="login.php" class="nav-link">Iniciar Sesión</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content text-center">
            <h1 class="display-4 mb-4">Deliciosas Pizzas </h1>
            <p class="lead mb-5">Selecciona tus favoritas y ordénalas en línea </p>
            <a href="viewmenu.php" class="btn btn-primary btn-lg px-5 py-3">
                <i class="fas fa-utensils mr-2"></i> Ver Menú
            </a>
        </div>
    </section>

    <!-- Special Offers -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 text-dark" >Catálogo de Pizzas</h2>
                <p class="lead text-muted">Selecciona las pizzas que deseas ordenar</p>
            </div>
            <div class="row">
                <!-- Pizza 1 -->
                <div class="col-md-4">
                    <div class="card pizza-card h-100">
                        <div class="special-badge">Más vendida</div>
                        <img src="images/pizza-1.jpg" class="card-img-top" alt="Pizza Italiana">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Italiana Clásica</h5>
                            <p class="card-text">Salsa de tomate, mozzarella fresca, albahaca y aceite de oliva virgen extra.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 text-primary mb-0">$12.99</span>
                                <button class="btn btn-sm btn-outline-primary add-to-cart" data-id="1" data-name="Pizza Italiana" data-price="12.99">
                                    <i class="fas fa-plus"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pizza 2 -->
                <div class="col-md-4">
                    <div class="card pizza-card h-100">
                        <div class="special-badge">Novedad</div>
                        <img src="images/pizza-5.jpg" class="card-img-top" alt="Pizza BBQ">
                        <div class="card-body">
                            <h5 class="card-title text-dark">BBQ Especial</h5>
                            <p class="card-text">Pollo, salsa BBQ casera, cebolla caramelizada y mezcla de quesos.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 text-primary mb-0">$15.99</span>
                                <button class="btn btn-sm btn-outline-primary add-to-cart" data-id="5" data-name="Pizza BBQ" data-price="15.99">
                                    <i class="fas fa-plus"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pizza 3 -->
                <div class="col-md-4">
                    <div class="card pizza-card h-100">
                        <div class="special-badge text-dark">Recomendada</div>
                        <img src="images/pizza-7.jpg" class="card-img-top" alt="Pizza 4 Quesos">
                        <div class="card-body">
                            <h5 class="card-title">Cuatro Quesos</h5>
                            <p class="card-text">Mezcla exclusiva de mozzarella, gorgonzola, parmesano y fontina.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 text-primary mb-0">$14.99</span>
                                <button class="btn btn-sm btn-outline-primary add-to-cart" data-id="7" data-name="Pizza Cuatro Quesos" data-price="14.99">
                                    <i class="fas fa-plus"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 mb-4">¿Listo para ordenar?</h2>
            <p class="lead mb-5">Disfruta de nuestras pizzas recién horneadas en la comodidad de tu hogar</p>
            <a href="viewmenu.php" class="btn btn-light btn-lg px-5 py-3 mr-3">
                <i class="fas fa-list mr-2"></i> Ver Menú Completo
            </a>
            <a href="ordernew.php" class="btn btn-outline-light btn-lg px-5 py-3">
                <i class="fas fa-shopping-cart mr-2"></i> Ver Carrito
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Horario</h5>
                    <ul class="list-unstyled">
                        <li>Lunes a Viernes: 11am - 10pm</li>
                        <li>Sábado y Domingo: 12pm - 11pm</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Av. Principal 123, Ciudad</li>
                        <li><i class="fas fa-phone mr-2"></i> (555) 123-4567</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@pizzeriadeliciosa.com</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <a href="#" class="text-white mr-3"><i class="fab fa-facebook-f fa-2x"></i></a>
                    <a href="#" class="text-white mr-3"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-twitter fa-2x"></i></a>
                </div>
            </div>
            <hr class="bg-light my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Pizzería Deliciosa. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Mini carrito flotante -->
    <div class="fixed-bottom d-flex justify-content-end p-3">
        <div class="card shadow-lg" style="width: 300px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Mi Pedido <span class="badge bg-light text-dark" id="cart-count">0</span></h5>
            </div>
            <div class="card-body p-2" id="cart-items" style="max-height: 300px; overflow-y: auto;">
                <p class="text-muted text-center">No hay items en tu pedido</p>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total:</strong>
                    <span id="cart-total">$0.00</span>
                </div>
                <a href="ordernew.php" class="btn btn-success btn-block">Finalizar Pedido</a>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let cart = [];
            
            // Cargar carrito desde localStorage al iniciar
            const savedCart = localStorage.getItem('pizzaCart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCart();
            }
            
            // Agregar al carrito
            $('.add-to-cart').click(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price'));
                
                // Verificar si ya existe en el carrito
                const existingItem = cart.find(item => item.id === id);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({
                        id: id,
                        name: name,
                        price: price,
                        quantity: 1
                    });
                }
                
                updateCart();
                
                // Mostrar notificación
                const toast = $(`
                    <div class="toast show position-fixed" style="bottom: 100px; right: 20px; z-index: 9999;">
                        <div class="toast-header bg-success text-white">
                            <strong class="me-auto">¡Agregado!</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            ${name} agregado al pedido
                        </div>
                    </div>
                `);
                
                $('body').append(toast);
                
                // Eliminar la notificación después de 3 segundos
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
            
            // Actualizar el carrito
            function updateCart() {
                const $cartItems = $('#cart-items');
                const $cartTotal = $('#cart-total');
                const $cartCount = $('#cart-count');
                
                $cartItems.empty();
                
                if (cart.length === 0) {
                    $cartItems.append('<p class="text-muted text-center">No hay items en tu pedido</p>');
                    $cartTotal.text('$0.00');
                    $cartCount.text('0');
                    return;
                }
                
                let total = 0;
                let itemCount = 0;
                
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    itemCount += item.quantity;
                    
                    $cartItems.append(`
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="d-block">${item.name}</span>
                                <small class="text-muted">$${item.price.toFixed(2)} x ${item.quantity}</small>
                            </div>
                            <div>
                                <span class="d-block">$${itemTotal.toFixed(2)}</span>
                                <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `);
                });
                
                $cartTotal.text('$' + total.toFixed(2));
                $cartCount.text(itemCount);
                
                // Guardar en localStorage
                localStorage.setItem('pizzaCart', JSON.stringify(cart));
            }
            
            // Eliminar item del carrito
            $(document).on('click', '.remove-item', function() {
                const index = $(this).data('index');
                cart.splice(index, 1);
                updateCart();
            });
        });
    </script>
</body>
</html>
