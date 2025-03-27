<?php

require_once dirname(__DIR__, 1) .'/models/User.php';
require_once dirname(__DIR__, 1) .'/models/UserLog.php';
require_once dirname(__DIR__, 2) . '/db/conn.php';


class UserController {
    private static $pdo;

    public function __construct($pdo) {
        self::$pdo = $pdo;
    }

    public static function getPdo() {
        return self::$pdo;
    }

    public function showLoginForm($errorMessage = '') {
        require dirname(__DIR__, 1) . '/views/loginForm.php';
    }

    public function showCreateuserForm() {
        require dirname(__DIR__, 1) . '/views/userRegistration.php';
    }

    public function showUserLogs() {
        require dirname(__DIR__, 1) . '/views/userLogList.php';
    }

    
    public function authenticateUser() {
        // Iniciar la sesión para autenticar el usuario
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si los datos se enviaron mediante POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                // Llamar al método de autenticación del modelo User
                $new_user = User::authenticate(self::getPdo(), $username, $password);

                if ($new_user) {                    
                    // Configura las variables de la sesión
                    $_SESSION['user_id'] = $new_user->id;
                    $_SESSION['username'] = $new_user->username;

                    // Redirigir a la vista principal
                    header('Location: index.php');
                    exit();

                } else {
                    // Redirigir a login con un mensaje de error
                    $this->showLoginForm("Error d'autenticació: usuari o contrasenya incorrectes.");
                }

            } catch (Exception $e) {
                $this->showLoginForm($e->getMessage());
            }
        } else {
            // Si no es una solicitud POST, mostrar el formulario de login
            $this->showLoginForm();
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }   
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }

    // Listar todos los usuarios
    public function listUsers() {

        header('Content-Type: application/json');

        try {
            $users = User::listUsers(self::getPdo());

            $userArray = array_map(function ($user) {
                return [
                    'username' => $user->username,
                    'pass' => $user->pass
                ];
            }, $users);

            echo json_encode(['success' => true, 'data' => $userArray]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Crear un usuario
    public function createUser() {
        header('Content-Type: application/json');

        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'Error en procesar les dades']);
            return;
        }

        try {
            $user = new User(0, $username, $password);
            $result = User::createUser(self::getPdo(), $user);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Usuari creat amb èxit']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No s\'ha pogut crear l\'usuari']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Actualizar un usuario
    public function updateUser() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $_GET['username'] ?? null;

        if (!$input || !$username) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos o username faltante']);
            return;
        }

        try {
            $result = User::updateUser(self::getPdo(), $username, $input);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado con éxito']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Eliminar un usuario
    public function deleteUser() {
        header('Content-Type: application/json');
        $username = $_GET['username'] ?? null;

        if (!$username) {
            echo json_encode(['success' => false, 'message' => 'Username faltante']);
            return;
        }

        try {
            $result = User::deleteUser(self::getPdo(), $username);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado con éxito']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public static function getPromoLastUpdateInfo(int $id_promo) {
        return UserLog::getLastUpdateLogByIdPromo(self::getPdo(), $id_promo);
    }

    public static function getUserLogs($offset, $numElems) {
        return UserLog::listUserLogs(self::getPdo(), $offset, $numElems);
    }



    // Insertar logs
    public function insertLog(){
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUser = $_SESSION['user_id'] ?? null;

        if(!$idUser){
            echo json_encode(['success' => false, 'message' => 'No s\'ha pogut registrar l\'acció. Error en obtenir informació de l\'usuari.']);
            return;
        }

        $promo = $_POST['promo'];
        $action = $_POST['action'];


        if(!isset($promo) || !isset($action)){
            echo json_encode(['success' => false, 'message' => 'No s\'ha pogut registrar l\'acció. Error en la consulta.']);
            return;
        }

        // Decodificación del JSON
        $promo_arr = json_decode($promo, true);

        // Verificación de la estructura del JSON
        if (!is_array($promo_arr) || !isset($promo_arr['id'])) {
            echo json_encode(['success' => false, 'message' => 'No s\'ha pogut registrar l\'acció. Error en rebre els paràmetres.']);
            return;
        }

        $idPromo = $promo_arr['id'];

        try {
            
            $userLog = new UserLog($idUser, $action, $promo, $idPromo);
            $result = UserLog::createLog(self::getPdo(), $userLog);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Acció registrada amb èxit']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No s\'ha pogut registrar l\'acció']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }


    }
}
