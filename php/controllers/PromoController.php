<?php
date_default_timezone_set('Europe/Madrid');

require_once dirname(__DIR__, 1) .'/models/Promo.php';
require_once dirname(__DIR__, 2) . '/db/conn.php';

class PromoController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function showMainView($errorMessage = '') {
        require dirname(__DIR__, 1) . '/views/mainView.php';
    }

    // Función para cargar la vista para añadir nueva promoción.
    // Por defecto se pasan los campos vacíos y si es una copia se especifican en la llamada.
    public function showAddPromo($windowTitle = "NOVA PROMOCIÓ", $imagePromoURL = "", $namePromo = "", $titlePromo = "", $subtitlePromo = "") {
        require dirname(__DIR__, 1) . '/views/promoAdd.php';
    }

    public function getPromos() {

        // Obtengo el tipo de listado
        $status = $_GET['status'] ?? null;
        $filterByName = $_GET['filterByName'] ?? "%"; // Por defecto el filtro es %, que actúa como un comodin para que no se aplique el filtro en caso de que no exista.
        $offset = $_GET['offset'] ?? 0; # Por defecto el offset es 0 .

        if (!$status) {
            http_response_code(400);
            echo "El parámetro 'status' es obligatorio";
            exit;
        }

        if (!in_array($status, ['active', 'scheduled', 'archived'])) {
            http_response_code(400);
            echo "El parámetro 'status' debe ser 'active', 'scheduled' o 'archived'";
            exit;
        }

        try {
            if ($status == 'active') {
                $livePromo = Promo::getLivePromo($this->pdo);
                $scheduledPromos = Promo::getPromos($this->pdo, 'scheduled');
                require dirname(__DIR__, 1) . '/views/livePromo.php';
            } else {
                $livePromo = Promo::getLivePromo($this->pdo);
                $promos = Promo::getPromos($this->pdo, $status, $offset, $filterByName);
                require dirname(__DIR__, 1) . '/views/promoList.php'; 
            }
            
        
        } catch (Exception $e) {
            http_response_code(500);
            echo $e;
        }

    }


    public function copyPromo() {
        header('Content-Type: application/json');

        // Intento obtener la id de la promo a copiar
        $idPromo = $_GET['idPromo'] ?? null;

        if(!$idPromo) { // Error al obtener el parámetro
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            return;
        }

        try {
            // Consulta la promo al modelo
            $promo = Promo::getPromoById($this->pdo, $idPromo);

            if($promo) { // Promo obtenida con éxito
                // Cargar la vista con la promoción
                $this->showAddPromo(
                    "COPIA LA PROMOCIÓ", 
                    $promo->image, 
                    $promo->name, 
                    $promo->title, 
                    $promo->subtitle);
            } else {
                $this->showAddPromo("Error al copiar la promoció");
                echo json_encode(['success' => false, 'message' => 'Error al copiar la promoció']);
            }
        } catch (Exception $e) {
            $this->showAddPromo($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al copiar la promoció']);
        }
        
    }


    public function deletePromo() {
        header('Content-Type: application/json');

        $idPromo = $_GET['idPromo'] ?? null;

        if(!$idPromo) {
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            return;
        } else {
            try {
                $promo = Promo::getPromoById($this->pdo, $idPromo);

                if (Promo::deletePromo($this->pdo, $idPromo)) {
                    echo json_encode(['success' => true, 'promo' => $promo]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al intentar borrar la promo']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al intentar borrar la promo.' . $e]);
            }
            
        }
    }
 

    // Manejar solicitud POST para crear una nueva promoción
    public function createPromo() {
        header('Content-Type: application/json');
        $name = $_POST['name'] ?? null;
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $publishDate = $_POST['publishDate'] ?? null;
        $image = $_POST['image'] ?? null;

        if (!$name || !$publishDate || !$image) {
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            return;
        }

        // Formato aceptado por SQL Server
        //$formatted_publish_date = date('Y-d-m H:i:s', strtotime($publishDate));

        // Fecha y hora actuales para update_time
        $currentDateTime = date('Y-d-m H:i:s');

        // Estado por defecto de la nueva tarea
        $status = 'scheduled';

        $promo = new Promo(null, $name, $image, $title, $subtitle, $publishDate, $currentDateTime, $currentDateTime, $status);
    
        try {
            $insertedPromo = Promo::createPromo($this->pdo, $promo);
    
            if ($promo) {
                echo json_encode([
                    'success' => true, 
                    'promo' => [
                        'id' => $insertedPromo->id,
                        'name' => $insertedPromo->name,
                        'title' => $insertedPromo->title,
                        'subtitle' => $insertedPromo->subtitle,
                        'publishDate' => $insertedPromo->publish_date,
                        'image' => $insertedPromo->image,
                        'createTime' => $insertedPromo->create_time,
                        'updateTime' => $insertedPromo->update_time,
                        'status' => $insertedPromo->status
                    ]
                ]); // Devuelvo la promoción añadida en la respuesta

            } else {
                echo json_encode(['success' => false, 'message' => 'Error al intentar actualizar los datos']);
            }
    
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function uploadImagePromo() {
        require dirname(__DIR__) . '/utils/uploadToFTP.php';
    }

    public function updatePromo() {
        header('Content-Type: application/json');

        $id = $_POST['idPromo'] ?? null;
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $publishDate = $_POST['publishDate'] ?? null;
        $image = $_POST['image'] ?? null;

        if (!$publishDate || !$id || !$image) {
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            return;
        }

        // Fecha y hora actuales para update_time
        $currentDateTime = date('Y-d-m H:i:s');
        $promo = new Promo($id, null, $image, $title, $subtitle, $publishDate, null, $currentDateTime, null);
    
        try {
            $updatedPromo = Promo::updatePromo($this->pdo, $promo);
    
            if ($updatedPromo) {
                echo json_encode([
                    'success' => true, 
                    'promo' => [
                        'id' => $updatedPromo->id,
                        'name' => $updatedPromo->name,
                        'title' => $updatedPromo->title,
                        'subtitle' => $updatedPromo->subtitle,
                        'publishDate' => $updatedPromo->publish_date,
                        'image' => $updatedPromo->image,
                        'createTime' => $updatedPromo->create_time,
                        'updateTime' => $updatedPromo->update_time,
                        'status' => $updatedPromo->status
                    ]
                ]); // Devuelvo la promoción añadida en la respuesta

            } else {
                echo json_encode(['success' => false, 'message' => 'Error al intentar actualizar los datos']);
            }
    
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }





  
}


?>