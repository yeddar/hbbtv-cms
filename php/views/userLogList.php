<?php
include dirname(__DIR__) . '/views/navbar.php';
require_once dirname(__DIR__) . '/controllers/UserController.php';
$config = require dirname(__DIR__, 2) . '/config.php';

// Obtener los registros de logs (10 por página, por ejemplo)
$userLogs = UserController::getUserLogs(0, 20);
?>
<!DOCTYPE html>
<html lang="es">
<?php include dirname(__DIR__, 2) . '/php/views/header.php'; ?>
<body>
<div class="container-fluid">
    <div class="list-group">
        <?php if (!empty($userLogs)): ?>
            <center>
                <h4>Mostrant els últims <?= count($userLogs) ?> registres.</h4>
            </center>
            
            <?php foreach ($userLogs as $log): ?>
                <?php 
                $logDatetime = $log['datetime'];
                $logUsername = $log['username'];
                $logAction = $log['action'];
                $logPromo = $log['promo'];

                $action = "";
                switch($logAction) {
                    case 'add':
                        $action = 'Afegit';
                        break;
                    case 'del':
                        $action = 'Esborrat';
                        break;
                    case 'update':
                        $action = 'Actualitzat';
                        break;
                    default:
                        $action = 'Acció desconeguda';
                } 
                
                // Obtengo JSON promo
                $promo_arr = json_decode($logPromo, true); // Array Asociativo
                
                $promoID = $promo_arr['id'];
                $promoImage = $promo_arr['image'];
                $promoName = $promo_arr['name'];
                $promoTitle = $promo_arr['title'];
                $promoSubtitle = $promo_arr['subtitle'];
                
                $promoCreateTime = $promo_arr['createTime'] ?? "";
                $_promoCreateDateTime = new DateTime($promoCreateTime);
                $promoCreateDateTime = $_promoCreateDateTime -> format("d/m/Y - H:i");

   
                $_promoDateTime = new DateTime($logDatetime);
                $promoDate = $_promoDateTime -> format("d/m/Y");
                $promoTime = $_promoDateTime -> format("H:i");

                // Obtengo URL de la imagen para mostrarla
                $promoImageURL = $config['image_url'] . '/' . $promo_arr['image'];
                
                ?>


                <div class="list-group-item log-container">

                    <div class="row">

                        <div class="promo-form col-md-6">
                            <div class="mb-4 log-<?=htmlspecialchars($logAction)?>">
                                <h4> <?= htmlspecialchars($action) ?> el dia <?= htmlspecialchars($promoDate) ?> a les <?= htmlspecialchars($promoTime) ?> h per <?= htmlspecialchars($logUsername) ?></h4>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text"> Nom </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    value="<?= htmlspecialchars($promoName) ?>" 
                                    disabled
                                >
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text"> Títol </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    value="<?= htmlspecialchars($promoTitle) ?>" 
                                    disabled
                                >
                            </div>


                            <div class="input-group mb-5">
                                <span class="input-group-text"> Subtítol </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    value="<?= htmlspecialchars($promoSubtitle) ?>" 
                                    disabled
                                >
                            </div>
    
                        </div>
                        <div class="promo-form col-md-6" style="align-self: center;">
                            <div class="mb-5 promo-image-container">
                                <img src="<?= htmlspecialchars($promoImageURL) ?>" class="img-fluid">
                            </div> 
                        </div>

                    </div>
                
                </div>

                
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay logs disponibles.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
