<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Pizzería - Registro</title>
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
      .register-section {
        background: url('images/bg_1.jpg') no-repeat center center;
        background-size: cover;
        min-height: 100vh;
        display: flex;
        align-items: center;
      }
      .register-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
      }
      .register-header {
        background: #dc3545;
        color: white;
        padding: 20px;
        text-align: center;
      }
      .register-body {
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
        transform: translateY(-2px);
      }
      .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
      }
      .terms-link {
        color: #dc3545;
        text-decoration: none;
        font-weight: 500;
      }
      .input-group-text {
        background-color: #e9ecef;
      }
      .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
      }
    </style>
  </head>
  <body>
    <?php
    // Incluir configuración de la base de datos
    require 'config/database.php';
    
    // Variables para mensajes de error
    $error = '';
    $success = '';
    
    // Procesar el formulario cuando se envía
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Obtener datos del formulario
      $nombre = trim($_POST['firstName']) . ' ' . trim($_POST['lastName']);
      $email = trim($_POST['email']);
      $telefono = trim($_POST['phone']);
      $password = $_POST['password'];
      $confirmPassword = $_POST['confirmPassword'];
        
        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($password) || empty($confirmPassword)) {
            $error = 'Por favor completa todos los campos obligatorios';
        } elseif (strlen($password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres';
        } elseif ($password !== $confirmPassword) {
            $error = 'Las contraseñas no coinciden';
        } elseif (!isset($_POST['termsCheck'])) {
            $error = 'Debes aceptar los términos y condiciones';
        } else {
            try {
                // Verificar si el email ya existe
                $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $error = 'Este correo electrónico ya está registrado';
                } else {
                    // Hash de la contraseña
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insertar nuevo usuario
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, direccion, created_at, updated_at)
                                          VALUES (?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([
                        $nombre,
                        $email,
                        $hashedPassword,
                        $telefono // Usamos el teléfono como dirección temporal
                    ]);
                    
                    $success = '¡Registro exitoso! Redirigiendo...';
                    
                    // Redirigir después de 2 segundos
                    header("Refresh: 2; url=login.php");
                }
            } catch (PDOException $e) {
                $error = 'Error en el registro: ' . $e->getMessage();
            }
        }
    }
    ?>

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
              <a href="login.php" class="nav-link">Iniciar Sesión</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <section class="register-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6">
            <div class="register-card">
              <div class="register-header">
                <h3><i class="fas fa-user-plus me-2"></i> Crear Cuenta</h3>
                <p class="mb-0">¡Únete a nuestra comunidad pizzera!</p>
              </div>
              <div class="register-body">
                <?php if ($error): ?>
                  <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                  <div class="alert alert-success mb-4"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="register.php">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="firstName" class="form-label">Nombre</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="lastName" class="form-label">Apellido</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                      <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label for="phone" class="form-label">Teléfono</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="password" class="form-label">Contraseña</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                      <small class="text-muted">Mínimo 8 caracteres</small>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="termsCheck" name="termsCheck" required>
                    <label class="form-check-label" for="termsCheck">Acepto los <a href="#" class="terms-link">Términos y Condiciones</a></label>
                  </div>
                  
                  <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-pizza text-white">
                      <i class="fas fa-check-circle me-2"></i> Registrarse
                    </button>
                  </div>
                  
                  <div class="text-center">
                    <p>¿Ya tienes cuenta? <a href="login.php" class="terms-link">Inicia Sesión</a></p>
                  </div>
                </form>
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
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar contraseña
        document.querySelector('.toggle-password').addEventListener('click', function() {
          const passwordInput = document.getElementById('password');
          const icon = this.querySelector('i');
          
          if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
          } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
          }
        });
      });
    </script>
  </body>
</html>