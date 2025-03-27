<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Importa Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/buttons.css">

    <!-- Importo Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


    <title>  Promocions HbbTV - Iniciar Sessió</title>


</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card p-4 shadow-lg">
                <h2 class="title text-center"> Promocions HbbTV</h2>

                <form action="?action=login" method="POST" class="mt-4">
                    <div class="mt-2 mb-3">
                        <label for="username" class="form-label">Usuari:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-5">
                        <label for="password" class="form-label">Contrasenya:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
          
                        <button type="submit" class="btn custom-btn btn-secondary">Accedix</button>
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