
        
<div class="main-title"><h1>Promocions Ganxo HbbTV</h1></div>


<!-- Barra de Navegación -->
<nav class="navbar navbar-expand-lg nav-container">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <p> Loguejat com a <b> <?= htmlspecialchars($_SESSION['username']); ?></b> </p>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <!-- Mostrar enlace para registrar un usuario solo si el usuario es "admin" -->
            <?php if ($_SESSION['username'] === 'admin'): ?>
                <a href="?" class="btn btn-header me-2">Inici</a>
                <a href="?action=showUserLogs" class="btn btn-header me-2">Logs</a>
                <a href="?action=createUser" class="btn btn-header me-2">Nou usuari</a>
            <?php endif; ?>

            <a href="?action=logout" class="btn btn-session-close">
                <i class="fa-solid fa-arrow-right-from-bracket" style="color: #c91818;"></i>
            </a>
        </div>
    </div>
</nav>

