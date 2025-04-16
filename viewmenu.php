<?php
session_start();

// Cambia esta línea
require 'config/database.php';
// Por esta:
$db = require 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener productos de la base de datos
try {
    $query = "SELECT * FROM productos WHERE activo = 1 ORDER BY id_categoria, nombre";
    $stmt = $db->query($query); // Ahora $db es un objeto PDO válido
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar por categorías
    $categorias = [
        1 => ['nombre' => 'Pizzas Clásicas', 'desc' => 'Nuestras especialidades de la casa'],
        2 => ['nombre' => 'Bebidas', 'desc' => 'Acompaña tu pizza con una refrescante bebida'],
        3 => ['nombre' => 'Postres', 'desc' => 'Deliciosos postres para terminar tu comida'],
        4 => ['nombre' => 'Entrantes', 'desc' => 'Aperitivos para empezar']
    ];
    
} catch (PDOException $e) {
    // Manejar error de base de datos
    die("Error al cargar el menú: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pizzería - Menú</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="indexnew.php">
          <span class="flaticon-pizza-1 mr-1"></span>Pizzería<br /><small>Deliciosa</small>
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-toggle="collapse"
          data-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a href="indexnew.php" class="nav-link">Inicio</a>
            </li>
            <li class="nav-item active">
              <a href="viewmenu.php" class="nav-link">Menú</a>
            </li>
            <li class="nav-item">
              <a href="ordernew.php" class="nav-link">Carrito de compras</a>
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

    <section class="jumbotron text-center bg-primary text-white">
      <div class="container">
        <h1 class="display-4">Menú</h1>
        <p class="lead">Explora nuestra selección de pizzas artesanales</p>
      </div>
    </section>

    <section class="container py-5">
      <div class="row">
        <?php foreach ($categorias as $id_cat => $categoria): ?>
          <?php 
          // Filtrar productos por categoría
          $cat_products = array_filter($productos, function($p) use ($id_cat) {
              return $p['id_categoria'] == $id_cat;
          });
          
          // Mostrar sección solo si hay productos en la categoría
          if (!empty($cat_products)): ?>
          <div class="col-md-12 mb-4">
            <div class="card">
              <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($categoria['nombre']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($categoria['desc']) ?></p>
                
                <div class="row mt-4">
                  <?php foreach ($cat_products as $producto): ?>
                  <div class="col-md-6 mb-4">
                    <div class="media">
                      <?php if (!empty($producto['imagen'])): ?>
                        <img src="<?= htmlspecialchars($producto['imagen']) ?>" class="mr-3 rounded" width="100" height="100" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                      <?php else: ?>
                        <div class="mr-3 rounded bg-secondary d-flex align-items-center justify-content-center" style="width:100px;height:100px;">
                          <i class="fas fa-pizza-slice fa-2x text-white"></i>
                        </div>
                      <?php endif; ?>
                      <div class="media-body">
                        <h5 class="mt-0">
                          <?= htmlspecialchars($producto['nombre']) ?> 
                          <span class="badge bg-success float-right">$<?= number_format($producto['precio'], 2) ?></span>
                        </h5>
                        <?php if (!empty($producto['descripcion'])): ?>
                          <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($producto['ingredientes'])): ?>
                          <small class="text-muted">Ingredientes: <?= htmlspecialchars($producto['ingredientes']) ?></small>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-primary add-to-cart mt-2" 
                                data-id="<?= $producto['id_producto'] ?>" 
                                data-name="<?= htmlspecialchars($producto['nombre']) ?>" 
                                data-price="<?= $producto['precio'] ?>">
                          <i class="fas fa-plus"></i> Agregar
                        </button>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>

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

    <footer class="bg-dark text-white py-4">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h5>Horario</h5>
            <p>Lunes a Viernes: 11am - 10pm</p>
            <p>Fin de Semana: 12pm - 11pm</p>
          </div>
          <div class="col-md-4">
            <h5>Contacto</h5>
            <p><i class="fas fa-phone"></i> (123) 456-7890</p>
            <p><i class="fas fa-envelope"></i> info@pizzeria.com</p>
          </div>
          <div class="col-md-4">
            <h5>Síguenos</h5>
            <a href="#" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
          </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
          <p class="mb-0">&copy; <?= date('Y') ?> Pizzería Deliciosa. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>

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