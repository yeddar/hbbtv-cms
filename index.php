<?php 
    require_once __DIR__ . '/db/conn.php';
    require_once __DIR__ . '/php/controllers/PromoController.php';
    require_once __DIR__ . '/php/controllers/UserController.php';

    // Declaro las variables globales para la conexión con las BBDD.
    global $pdo_mss;
    global $pdo_sqlite;

    // Creo instancias de los controladores y le asigno a cada una su PDO correspondiente.
    global $promoController, $userController;
    $promoController = new PromoController($pdo_mss);
    $userController = new UserController($pdo_sqlite);

    // Detectar método HTTP y acción
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? null;
  
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verifica si el usuario está logueado, excepto para login y creación de usuario
    if (!isset($_SESSION['user_id']) && !in_array($action, ['login', 'createUser'])) {
        header("Location: ?action=login");
        exit();
    }


    // Mantenimiento
    /*
    if(isset($_SESSION['user_id']) && $_SESSION['username'] != 'admin') {
        include 'maintenance.php';
        exit();
    }
    
    */

    // Manejo de rutas según método HTTP
    switch ($method) {
        case 'GET':
            if ($action === 'logout') {
                $userController->logout();
                exit();
            } elseif ($action === 'addPromo') {
                $promoController->showAddPromo();
                exit();
            } elseif ($action === 'copyPromo') {
                $promoController->copyPromo();
                exit();
            } elseif ($action === 'deletePromo') {
                $promoController->deletePromo();
                exit();
            } elseif ($action === 'getPromos') {
                $promoController->getPromos();
                exit();
            } elseif ($action === 'createUser') {
                $userController->showCreateUserForm();
                exit();
            } elseif ($action === 'showUserLogs') {
                $userController->showUserLogs();
                exit();
            } elseif ($action === 'login') {
                $userController->showLoginForm();
                exit();
            }
            break;

        case 'POST':
            if ($action === 'createUser') {
                $userController->createUser();
                exit();
            } elseif ($action === 'login') {
                $userController->authenticateUser();
                exit();
            } elseif ($action === 'uploadImage') {
                $promoController->uploadImagePromo();
                exit();
            } elseif ($action === 'updatePromo') {
                $promoController->updatePromo();
                exit();
            } elseif ($action === 'createPromo') {
                $promoController->createPromo();
                exit();
            } elseif ($action === 'insertLog') {
                $userController->insertLog();
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
                exit();
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
    }

?>
<!DOCTYPE html>
<html lang="es">
<?php include __DIR__ . '/php/views/header.php'; ?>
<body>
    <div class="container-fluid main-container">
        <?php include __DIR__ . '/php/views/navbar.php'; ?>

        <div id="inicio" class="contenido contenido-activo">
            <div class="main-view-container">
                <div class="spacer"></div>
                <?= $promoController->showMainView()?>
                <div class="spacer"></div>
            </div>  
        </div>  
    </div>

    <!-- Modal de borrado -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminació</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    Confirma per a eliminar la promoció
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel·la</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Elimina</button>
                </div>
            </div>
        </div>
    </div>



   <!-- Alert container -->
   <div id="alert-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"></div>

</body>
</html>