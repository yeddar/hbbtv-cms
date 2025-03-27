<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$admin_username = "admin"; 
if ($_SESSION['username'] !== $admin_username) {
    echo "No tienes permiso para acceder a esta página.";
    exit();
}

include __DIR__ . '../../views/navbar.php';

?>


<!DOCTYPE html>
<html lang="es">
<?php include dirname(__DIR__, 2) . '/php/views/header.php'; ?>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-3">
            <div class="card p-4 shadow-lg">

                <h2 class="title text-center">Promocions HbbTV</h2>
                <h3 class="subtitle mt-3">Nou usuari</h3>

                <form class="mt-4" id="register-form" action="?action=createUser" method="POST" onsubmit="validateAndCreateUser(event)">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuari:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-5">
                        <label for="password" class="form-label">Contrasenya:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
          
                        <button type="submit" class="btn custom-btn btn-secondary"> Crear usuari </button>
                    </div>
                </form>

                <!-- Mostrar mensaje de error si lo hay -->
                <?php if (!empty($errorMessage)): ?>
                    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>