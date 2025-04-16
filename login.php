<?php
session_start();
require 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // CORREGIDO: Se usa 'password' y no 'contraseña'
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id_usuario'];
                    $_SESSION['user_name'] = $user['nombre'];
                    $_SESSION['user_email'] = $user['email'];

                    // Redirección a indexnew.php
                    header('Location: indexnew.php');
                    exit;
                } else {
                    $error = 'Contraseña incorrecta';
                }
            } else {
                $error = 'No existe un usuario con ese email';
            }
        } catch (PDOException $e) {
            $error = 'Error al iniciar sesión: ' . $e->getMessage();
            error_log("Error en login: " . $e->getMessage());
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pizzería - Iniciar Sesión</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    
    <style>
      body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
      }
      .login-section {
        background: url('images/bg_1.jpg') no-repeat center center;
        background-size: cover;
        min-height: 100vh;
        display: flex;
        align-items: center;
      }
      .login-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
      }
      .login-header {
        background: #343a40;
        color: white;
        padding: 20px;
        text-align: center;
      }
      .login-body {
        padding: 30px;
      }
      .btn-pizza {
        background: #dc3545;
        border: none;
        padding: 10px 25px;
        transition: all 0.3s;
      }
      .btn-pizza:hover {
        background: #bb2d3b;
      }
      .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
      }
    </style>
</head>
<body>
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
              <a href="register.php" class="nav-link">Registrarse</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <section class="login-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="login-card">
              <div class="login-header">
                <h3><i class="fas fa-pizza-slice me-2"></i> Iniciar Sesión</h3>
              </div>
              <div class="login-body">
                <?php if ($error): ?>
                  <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                  <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                      <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-lock"></i></span>
                      <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                  </div>
                  <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-pizza text-white">
                      <i class="fas fa-sign-in-alt me-2"></i> Ingresar
                    </button>
                  </div>
                </form>
                <hr>
                <div class="text-center">
                  <p class="mb-0">¿No tienes cuenta? <a href="register.php" class="text-decoration-none">Regístrate aquí</a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="bg-dark text-white py-4">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <h5>Horario de Atención</h5>
            <p>Lunes a Viernes: 11:00 AM - 10:00 PM</p>
            <p>Sábado y Domingo: 12:00 PM - 11:00 PM</p>
          </div>
          <div class="col-md-6">
            <h5>Contacto</h5>
            <p><i class="fas fa-phone"></i> (123) 456-7890</p>
            <p><i class="fas fa-envelope"></i> info@pizzeria.com</p>
            <p><i class="fas fa-map-marker-alt"></i> Calle Principal #123, Ciudad</p>
          </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
          <p class="mb-0">&copy; 2023 Pizzería Deliciosa. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>