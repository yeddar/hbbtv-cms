<?php 
$config = require 'config.php';
require_once dirname(__DIR__, 1) . '/controllers/UserController.php';

if (!empty($promos)) {
    for ($i = 0; $i < count($promos); $i++) {  
        $idPromo = $promos[$i]->id;
        $namePromo = $promos[$i]->name;
        $titlePromo = $promos[$i]->title;
        $subtitlePromo = $promos[$i]->subtitle;
        $imagePromo = $promos[$i]->image;


        // Chapuza pensada por Guillem Frasquet:
        // Debido a la redirección que se hizo de hbbtv a hbbtvpre y para acceder a pro se usa hbbtvpro. A nivel de la red interna
        $imagePromoURL = $config['image_url'] . '/' . $imagePromo;

        // Formatear fechas de publicación
        $publishDateTimePromo = new DateTime($promos[$i]->publish_date);
        $formattedPublishDatePromo = $publishDateTimePromo -> format("Y-m-d");
        $formattedPublishTimePromo = $publishDateTimePromo -> format('H:i');

        // Obtener intervalo de publicación
        $startPublished = $publishDateTimePromo->format('d/m/Y - H:i');
        $endPublished = '';

        if($status == 'scheduled') {
            if(($i + 1) < count($promos)) { // Si hay siguiente elemento
                $publishDateTimeNextPromo = new Datetime($promos[$i + 1]->publish_date);
                $endPublished = $publishDateTimeNextPromo->format('d/m/Y - H:i');
            }
        } else if($status == 'archived') {
            if($i > 0) { // Si no es el primer elemento
                $publishDateTimePrevPromo = new Datetime($promos[$i - 1]->publish_date);
                $endPublished = $publishDateTimePrevPromo->format('d/m/Y - H:i');
            } else if($livePromo) {
                // Será el primer elemento.
                // Obtener fecha de la que está activa
                $publishDateTimeLivePromo = new DateTime($livePromo->publish_date);
                $endPublished = $publishDateTimeLivePromo->format('d/m/Y - H:i');
            }
        }
        
        
        // Formatear fechas de última modificación
        $updateDateTimePromo = new DateTime($promos[$i]->update_time);
        $formattedUpdateDate = $updateDateTimePromo -> format('d/m/Y');
        $formattedUpdateTime = $updateDateTimePromo -> format('H:i');

        // Lógica para deshabilitar los campos titol y subtitol cuando la imagen supera las dimensiones especificadas.
        // Comprobar las dimensiones de la imagen
        $isOverDim = false;
        
        try {
            $tempImage = tempnam(sys_get_temp_dir(), 'img');
        
            $imageData = @file_get_contents($imagePromoURL);
            if ($imageData === false) {
                throw new Exception("No se pudo obtener la imagen desde la URL " . $imagePromoURL);
            }
        
            file_put_contents($tempImage, $imageData);
        
            $imageSize = @getimagesize($tempImage);
            if ($imageSize && $imageSize[1] > 700) {
                $isOverDim = true;
            }
        } catch (Exception $e) {
            echo "Error al obtener la imagen: " . $e->getMessage();
        }

        // Obtener usuario que hizo la última modificación
        $lastUpdate = UserController::getPromoLastUpdateInfo($idPromo);
        $userLastUpdate = "usuari";
        if ($lastUpdate) {
            $userLastUpdate = $lastUpdate['username'];
        }

        // Cargar la plantilla correspondiente
        if($status == 'scheduled') {
            // Cargar la lista para scheduledPromos
            require 'scheduledPromo.php';
        } 
        else if ($status == 'archived') {
            // Cargar la lista para archivedPromos
            require 'archivedPromo.php';
        }
        
    }

    $newOffset = $offset + count($promos);
    echo '<div id="load-more-'. $newOffset .'" class="load-more-container">';
        echo '<button class="load-more-button" onclick="loadMore(' . $newOffset . ')"><i class="fa-solid fa-circle-chevron-down fa-2xl"></i></button>';
    echo '</div>';
    
    

} else {
    echo '<div class="list-group-item promo-container">';
    echo '<p class="text-muted promo-message-p">No hi ha promocions disponibles per a mostrar.</p>';
    echo '</div>';
}
        
?>