

<div class="container-fluid">
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="list-group">
        <!-- Promoción activa -->
        <div id="live-promo-container" class="list-group-item live-promo-container">

        </div>
    </div>
    <div class="spacer"></div>
    
    <div class="list-group-item add-promo-button-container">
        <button class="add-promo-button" id="open-addNewPromo-icon" onclick="loadPromoAddForm()" title="Afegix una nova promoció"><i class="fa-solid fa-circle-plus fa-2xl"></i></button>
        <button class="add-promo-button hidden" id="close-addNewPromo-icon" onclick="loadPromoAddForm()" title="Descarta els canvis"><i class="fa-solid fa-circle-xmark fa-2xl"></i></button>
        <!-- Formulario para añadir una nueva promoción controlado por PromoController.js -->
        <div id="add-promo-container" class="hidden"></div>
    </div>
    <div class="spacer"></div>
        
    <div class="programming-promo-container list-group-item">
        <div class="header-promo-buttons">
            <select id="select-load-promos" class="select-status-promo" onchange="handleSelectPromoStatus()">
                <option value="scheduled">PROGRAMADES</option>
                <option value="archived">ARXIVADES</option>
            </select>

            <div class="input-group search-promo-field" >
                <input 
                    type="text" 
                    id="input-search-promo" 
                    class="form-control" 
                    style="height: 100%; border:0;"
                    placeholder="Busca per nom de la promoció" 
                    maxlength="15"
                    onchange="handleSearchPromoInput()"
                >
                <span class="input-group-text" style="background-color: #fff3cd; height: 100%; border:0; border-left:2px solid #8d8d8dbf;">
                <button 
                    id="search-promo" 
                    class="btn"
                    onclick="handleSearchPromoInput()"
                    ><i class="fa-solid fa-magnifying-glass" style="font-size: 22px; color: #6e7174;"></i>
                </button>
                </span>
            </div> 
        </div>
        

        <div class="list-group">
            <!-- Spinner para indicar la carga del listado de promociones -->
            <div id="spinner" class="hidden"><div class="loader"></div></div>

            <!-- Listado de promociones programadas y archivadas -->
            <div id="list-promo-container"></div>
        </div>
    </div>
</div>





