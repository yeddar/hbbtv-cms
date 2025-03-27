<?php
$idPromo = 0;
$isOverDim = false;

$config = require 'config.php';


if (empty($imagePromoURL)) {
    $imagePromoURL = '/php/views/ganxo/img/sense-imatge.png';

} else {
    // Chapuza pensada por Guillem Frasquet:
    // Debido a la redirección que se hizo de hbbtv a hbbtvpre y para acceder a pro se usa hbbtvpro. A nivel de la red interna
    $imagePromoURL = $config['image_url'] . '/'. $imagePromoURL;

    // Comprobar si la imagen supera las dimensiones para activar los campos de texto
    $tempImage = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($tempImage, file_get_contents($imagePromoURL));
    $imageSize = @getimagesize($tempImage);
    if ($imageSize && $imageSize[1] > 700) {
        $isOverDim = true;
    }
}

?>

<div class="promo-container">
    <div class="d-flex w-100 justify-content-between">
      
        <h3 class="window-title"><?= htmlspecialchars($windowTitle) ?></h3>
    
        <div>
            <button
                id="preview-button-<?= $idPromo ?>" 
                class="btn btn-outline-dark" 
                onclick="previewPromo(<?= $idPromo ?>)"
                disabled
                ><i class="fa-solid fa-eye"></i>
                
            </button>

            <i class="fa-solid fa-arrow-right fa-lg"></i>

            <button 
                id="save-button-<?= $idPromo ?>" 
                class="btn btn-outline-dark me-2" 
                onclick="validateAndSavePromo(<?= $idPromo ?>)"
                disabled
                ><i class="fa-solid fa-floppy-disk"></i>
            </button>
        </div>  
    </div>

    <div class="row mt-10">
        <div class="promo-form col-md-6">
            
           

            <div class="mb-5 promo-image-container">
                <img id="image-<?= $idPromo ?>" src="<?= htmlspecialchars($imagePromoURL) ?>" alt="Imagen de <?= htmlspecialchars($namePromo) ?>" class="img-fluid">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon-<?= $idPromo ?>"> Nom </span>
                <input 
                    type="text" 
                    id="input-name-<?= $idPromo ?>" 
                    class="form-control" 
                    placeholder="Nom de la promoció" 
                    value="<?= $namePromo ?>"
                    aria-describedby="basic-addon-<?= $idPromo ?>"
                    maxlength="30"
                    oninput="handleFieldChange(<?= $idPromo ?>)"
                >
            </div>

            <div class="mb-5">
                <input type="file" 
                    class="form-control" 
                    id="input-image-<?= $idPromo ?>" 
                    accept=".png" 
                    onchange="validateImage(this, <?= $idPromo ?>)">
                <small class="text-muted">Només es permeten imatges PNG de fins a 300KB.</small>
            </div>

            

            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    value="" 
                    id="input-check-<?= $idPromo ?>"
                    onchange="toggleTextFields(this, <?= $idPromo ?>)"
                    <?= (empty($titlePromo) && empty($subtitlePromo)) ? '' : 'checked' ?>
                    <?= $isOverDim ? 'disabled' : '' ?>
                    >
                <label class="form-check-label" for="flexCheckChecked">
                    Vull afegir text a més de la imatge
                </label>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon-<?= $idPromo ?>"> Títol </span>
                <input 
                    type="text" 
                    id="input-title-<?= $idPromo ?>" 
                    class="form-control" 
                    aria-describedby="basic-addon-<?= $idPromo ?>"
                    maxlength="40"
                    value="<?= $titlePromo ?>"
                    oninput="handleFieldChange(<?= $idPromo ?>)"
                    <?= (empty($titlePromo) && empty($subtitlePromo)) ? 'disabled' : '' ?>
                >
            </div>


            <div class="input-group mb-5">
                <span class="input-group-text" id="basic-addon-<?= $idPromo ?>"> Subtítol </span>
                <input 
                    type="text" 
                    id="input-subtitle-<?= $idPromo ?>" 
                    class="form-control" 
                    aria-describedby="basic-addon-<?= $idPromo ?>"
                    maxlength="50"
                    value="<?= $subtitlePromo ?>"
                    oninput="handleFieldChange(<?= $idPromo ?>)"
                    <?= (empty($titlePromo) && empty($subtitlePromo)) ? 'disabled' : '' ?>
                >
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
                        onchange="handleFieldChange(<?= $idPromo ?>)"
                        min="<?= date('Y-m-d') ?>"
                        required>
                </div>

                <div class="time-field">
                    <input 
                        class="form-control" 
                        type="time" 
                        id="time-field-<?= $idPromo ?>" 
                        name="hora" 
                        onchange="handleFieldChange(<?= $idPromo ?>)"
                        required>
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

</div>
        