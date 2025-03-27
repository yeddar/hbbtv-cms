<div class="list-group-item promo-container">
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
        <div class="promo-form col-md-7">

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
                    aria-describedby="basic-addon-<?= $idPromo ?>"
                    oninput="handleFieldChange(<?= $idPromo ?>)"
                    disabled
                >
            </div>


            <div class="input-group mb-5">
                <span class="input-group-text" id="basic-addon-<?= $idPromo ?>"> Subtítol </span>
                <input 
                    type="text" 
                    id="input-subtitle-<?= $idPromo ?>" 
                    class="form-control" 
                    value="<?= htmlspecialchars($subtitlePromo) ?>" 
                    aria-describedby="basic-addon-<?= $idPromo ?>"
                    oninput="handleFieldChange(<?= $idPromo ?>)"
                    disabled
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

        <div class="promo-preview col-md-5">
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
        <small class="text-muted">
            Última modificació: <?= htmlspecialchars($formattedUpdateDate) ?> a les <?=htmlspecialchars($formattedUpdateTime)?> per <strong><?=htmlspecialchars($userLastUpdate)?></strong>
            </small>
        </small>
    </div>
</div>