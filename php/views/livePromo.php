<?php
$config = require 'config.php';
require_once dirname(__DIR__, 1) . '/controllers/UserController.php';

try {
    //$urlLivePromo = $config['ftp_url'] . '/currentPromo.json';
    $urlLivePromo = 'https://hbbtvpro.apuntmedia.es/currentPromo.json';

    /* // Desactivo la verificación del certificado
    $context = stream_context_create([
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ]); */

    $json = file_get_contents($urlLivePromo);

    $validated = false;

    if($json) {
        $data = json_decode($json, true);
        if($data) {
            $id = $data['Id'];
            $image = $data['Image'];
            $title = $data['Title'];
            $subtitle = $data['Subtitle'];

            if(!empty($livePromo)) {  
                if(
                    $livePromo->id == $id &&
                    $livePromo->image == $image &&
                    $livePromo->title == $title &&
                    $livePromo->subtitle == $subtitle
                
                ) $validated = true; 
            }   
        }
    }

} catch (Exception $e) {
    echo $e;
}

?>

<div class="promo-title"> PROMOCIÓ ACTIVA </div>
<div class="promo-container">
    <?php if (!empty($livePromo)): ?>
        <?php
            $idPromo = $livePromo->id;
            $namePromo = $livePromo->name;
            $titlePromo = $livePromo->title;
            $subtitlePromo = $livePromo->subtitle;
            $imagePromo = $livePromo->image;

            // Chapuza pensada por Guillem Frasquet:
            // Debido a la redirección que se hizo de hbbtv a hbbtvpre y para acceder a pro se usa hbbtvpro. A nivel de la red interna
            $imagePromoURL = $config['image_url'] . '/' . $imagePromo;
        
            // Formatear fechas de publicación
            $publishDateTimePromo = new DateTime($livePromo->publish_date);
            $formattedPublishDatePromo = $publishDateTimePromo -> format("Y-m-d");
            $formattedPublishTimePromo = $publishDateTimePromo -> format('H:i');


            // Obtener intervalo de publicación
            $startPublished = $publishDateTimePromo->format('d/m/Y - H:i');
            $endPublished = '';
            if(count($scheduledPromos) >= 1) { // Si hay siguiente elemento
                $publishDateTimeNextPromo = new Datetime($scheduledPromos[0]->publish_date);
                $endPublished = $publishDateTimeNextPromo->format('d/m/Y - H:i');
            } 
            
            // Formatear fechas de última modificación
            $updateDateTimePromo = new DateTime($livePromo->update_time);
            $formattedUpdateDate = $updateDateTimePromo -> format('d/m/Y');
            $formattedUpdateTime = $updateDateTimePromo -> format('H:i');

            // Obtener usuario que hizo la última modificación
            $lastUpdate = UserController::getPromoLastUpdateInfo($idPromo);
            $userLastUpdate = "usuari";
            if ($lastUpdate) {
                $userLastUpdate = $lastUpdate['username'];
            }

    
        ?>
        

        <div class="d-flex w-100 justify-content-between">
            
            <div class="chip-container">
                <small title="Publicació" class="chip chip-start-date me-1"><?= $startPublished ?></small>
                <?php if($endPublished): ?>
                    <small title="Despublicació" class="chip chip-end-date me-1"><?= $endPublished ?></small>
                <?php endif ?>
                <small title="ID" class="chip chip-id me-1"><?= $idPromo ?></small>
                <small title="Nom" class="chip chip-name me-1"><?= $namePromo ?></small>
            </div>
       
            <div>
                <button class="btn btn-outline-dark me-2"
                    onclick="copyPromo(<?= $idPromo ?>)"
                ><i class="fa-solid fa-copy"></i>
                </button>
            </div>

        </div>

        <div class="row mt-5">
            <div class="promo-form col-md-6">

                <div class="mb-3">
                    <?php if($validated): ?>
                        <i class="fa-solid fa-circle-check" style="color: #327858;">
                            ONLINE
                        </i>
                    <?php else: ?>
                        <i class="fa-solid fa-circle-exclamation" style="color: #b93131;">
                        ERROR
                        </i>
                        <p>La promoció no s'ha actualitzat correctament. <br>
                        <b>Per favor, informa a <a href="mailto:suportmultimedia@cvmc.es"> suportmultimedia@cvmc.es </a></b>
                        </p>
                    <?php endif ?>
                </div>

                <div class="mb-5 promo-image-container">
                    <img id="image-<?= $idPromo ?>" src="<?= htmlspecialchars($imagePromoURL) ?>" alt="Imagen de <?= htmlspecialchars($namePromo) ?>" class="img-fluid">
                </div>
                
        
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon-<?= $idPromo ?>"> Títol </span>
                    <input 
                        type="text" 
                        id="input-title-<?= $idPromo ?>" 
                        class="form-control" 
                        value="<?= htmlspecialchars($titlePromo) ?>" 
                        disabled>
                </div>


                <div class="input-group mb-5">
                    <span class="input-group-text" id="basic-addon-<?= $idPromo ?>"> Subtítol </span>
                    <input 
                        type="text" 
                        id="input-subtitle-<?= $idPromo ?>" 
                        class="form-control" 
                        value="<?= htmlspecialchars($subtitlePromo) ?>" 
                        disabled>
                </div>

                <div class="mb-1">
                    <p>Data i hora de publicació:</p>
                </div>
                <div class="input-group mb-3">
                              
                    <div class="date-field me-2">
                        <input 
                            class=" form-control" 
                            type="date" 
                            id="date-field-<?= $idPromo ?>" 
                            name="fecha" 
                            value="<?= htmlspecialchars($formattedPublishDatePromo) ?>" 
                            disabled>
                    </div>
                    <div class="time-field">
                        <input 
                            class="form-control" 
                            type="time" 
                            id="time-field-<?= $idPromo ?>" 
                            name="hora" 
                            value="<?= htmlspecialchars($formattedPublishTimePromo) ?>" 
                            disabled>
                    </div>
            
                </div>
            </div>

            <div class="promo-preview col-md-6">
                <iframe 
                    id="ganxo-iframe-<?= $idPromo ?>"
                    src="/php/views/ganxo/ganxoPreview.php?imageURL=<?= urlencode($imagePromoURL) ?>&title=<?= urlencode($titlePromo) ?>&subtitle=<?= urlencode($subtitlePromo) ?>" 
                    width="100%" 
                    height="600px" 
                    style="border: none;"
                    scrolling="no">
                </iframe>
            </div>
        </div>

        <div id="errorMessageContainer-<?= $idPromo ?>" class="mt-3" aria-hidden="true"></div>
        <div class="mt-3">
            <div class="row">
                <small class="text-muted">
                    Última modificació: <?= htmlspecialchars($formattedUpdateDate) ?> a les <?=htmlspecialchars($formattedUpdateTime)?> per <strong><?=htmlspecialchars($userLastUpdate)?></strong>
                    </small>
                </small>
            </div>
            
        </div>
    <?php endif; ?>
</div>