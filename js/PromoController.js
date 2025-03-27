document.addEventListener("DOMContentLoaded", function () {
    loadActivePromo(); // Cargo la promo activa
    loadList(); // Cargo listado de promos
});

function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertContainer.appendChild(alert);

    // Automatically close the alert after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }, 5000);
}

// De momento esta función no se usa
function showAlertInCard(message, idPromo) {
    const errorContainer = document.getElementById('errorMessageContainer-' + idPromo);
    const errorMessage = document.createElement('small');
    errorMessage.classList.add('text-danger');

    errorMessage.innerText = message;
    errorContainer.hidden = false; // Mostrar el mensaje
    
}

function deletePromo(idPromo) {

    // Muestro el modal de confirmación
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    confirmDeleteModal.show();

    // Si se pulsa en el botón eliminar del alert, se realiza la llamada correspondiente.
    document.getElementById('confirmDeleteButton').addEventListener('click', async function() {

        try {
            const response = await fetch(`index.php?action=deletePromo&idPromo=${idPromo}`);
            const data = await response.json();
            if(data.success) {
                await sendLog("del", data.promo);
                location.reload(); // Recargar la página si la consulta tiene éxito
            } else {
                throw new Error("Error al borrar la promoció: ", data.message);
            }  

        } catch (error) {
            console.error(error);
            showAlert(`Error al borrar la promoció: ${error}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
        }
    });
}



async function uploadImagePromo(inputField, idPromo) {
    const files = inputField.files;
    var imageName = null;

    if(files.length > 0) {
        
        const file = files[0] // Obtengo el archivo de imagen
    
        // Prepara la subida del archivo
        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch('index.php?action=uploadImage', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }

            // Realizo la consulta y espero a recibir un JSON como respusta.
            const resultJson = await response.json();

            // La imagen se valida también en el servidor.
            // Una vez validada, se obtiene la URL donde se ha subido.
            if(resultJson.success) {
                imageName = resultJson.fileName;
            } else {
                // Cualquer error en la validación en el servidor generará un mensaje de error.
                console.log(`Error al penjar la imatge: ${resultJson.error}`, idPromo);  
            }  

        } catch (error) {
            // Error de conexión 
            console.log(error);
            showAlert(`Error al penjar la imatge: ${error}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
        }
    }    
    

    return imageName;
}

async function loadPromoAddForm() {
    const openIcon = document.getElementById('open-addNewPromo-icon');
    const closeIcon = document.getElementById('close-addNewPromo-icon');
    const addPromoContainer = document.getElementById('add-promo-container');

    const isVisible = addPromoContainer.classList.contains('visible');

    if (isVisible) { // El formulario se está mostrando, por lo que hay que cerrarlo
        // Muestro el botón para abrir el formulario y oculto el botón para cerrarlo.
        openIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');

        // Ocultar contenedor del formulario
        addPromoContainer.classList.remove('visible');
    } else { // El formulario está oculto por lo que hay que mostrarlo
        // Oculto el botón para abrir formulario y muestro el botón para cerrar el formulario
        openIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
        
        // Mostrar círculo de progreso mientras se carga el formulario
        addPromoContainer.innerHTML = `
        <div class="progress-circle-wrapper">
            <div class="progress-circle"></div>
        </div>
        `;

        
        try {
            const response = await fetch('index.php?action=addPromo');
            if(!response.ok) throw new Error("Error al carregar el formulari per a afegir una nova promoció");
            const data = await response.text();
            addPromoContainer.innerHTML = data;
            addPromoContainer.classList.add('visible');

            // Scroll al contenedor
            //addPromoContainer.scrollIntoView();
        } catch (error) {
            addPromoContainer.innerHTML = '';
            console.error(error);
            showAlert(`No s\'ha pogut carregar el formulari. Contacta amb suportmultimedia@cvmc.es`, 'danger');
            
        }
    }
}


async function copyPromo(idPromo) {
    const openIcon = document.getElementById('open-addNewPromo-icon');
    const closeIcon = document.getElementById('close-addNewPromo-icon');
    const addPromoContainer = document.getElementById('add-promo-container');

    const isVisible = addPromoContainer.classList.contains('visible');

    if (!isVisible) { // El formulario está oculto por lo que hay que mostrarlo
        // Oculto el botón para abrir formulario y muestro el botón para cerrar el formulario
        openIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
        addPromoContainer.classList.add('visible');   
    }

    try {
        const response = await fetch(`index.php?action=copyPromo&idPromo=${idPromo}`);
        if(!response.ok) throw new Error("Error al carregar el formulari per a copiar la promoció");
        const data = await response.text();
        addPromoContainer.innerHTML = data;
        
        
        // Desplazar scroll al container
        addPromoContainer.scrollIntoView();

    } catch (error) {
        addPromoContainer.innerHTML = '';
        console.error(error);
        showAlert(`No s\'ha pogut carregar el formulari. Contacta amb suportmultimedia@cvmc.es`, 'danger');
    }
      
}


function previewPromo(idPromo) {

    // Desactivar botón de previsualización una vez pulsado
    const previewButton = document.getElementById(`preview-button-${idPromo}`);
    previewButton.disabled = true;

    // Obtener los valores de los campos opcionales
    var title = document.getElementById(`input-title-${idPromo}`);
    var subtitle = document.getElementById(`input-subtitle-${idPromo}`);

    // Asignar valores de los campos solo si no están deshabilitados
    title = title.disabled ? '' : title.value;
    subtitle = subtitle.disabled ? '' : subtitle.value;

    const imageField = document.getElementById(`image-${idPromo}`);
    const imageURL = imageField ? imageField.src : '';

    // Construir la nueva URL para el iframe
    const iframe = document.getElementById(`ganxo-iframe-${idPromo}`);
    const newURL = `/php/views/ganxo/ganxoPreview.php?imageURL=${encodeURIComponent(imageURL)}&title=${encodeURIComponent(title)}&subtitle=${encodeURIComponent(subtitle)}`;

    // Actualizar el src del iframe
    iframe.src = newURL;

    // Habilitar el botón de guardado
    const saveButton = document.getElementById(`save-button-${idPromo}`);
    saveButton.disabled = false;
}

function handleFieldChange(idPromo) {
    const previewButton = document.getElementById(`preview-button-${idPromo}`);
    const saveButton = document.getElementById(`save-button-${idPromo}`);
    previewButton.disabled = false;
    saveButton.disabled = true;
}



// Función para gestionar la búsqueda de promociones
function handleSearchPromoInput() {
    loadList(); // Llamo a la función para mostrar el listado con el filtro por nombre.
}

function handleSelectPromoStatus() {
    // Borrar campo de búsqueda
    const searchPromoField = document.getElementById('input-search-promo');
    if (searchPromoField) searchPromoField.value = '';

    loadList();

}

function validateImage(input, idPromo) {
    // Mensaje de error
    const errorMessage = document.createElement('small');
    errorMessage.classList.add('text-danger');

    input.nextElementSibling?.remove(); // Eliminar mensajes de error previos
    input.insertAdjacentElement('afterend', errorMessage);

    // Obtener la imagen
    const image = input.files[0];


    // Comprobar que el nombre no contiene espacions ni símbolos
    const fileName = image.name;
    const validNamePattern = /^[a-zA-Z0-9_-]+\.[a-zA-Z0-9]+$/;

    if (!validNamePattern.test(fileName)) {
        errorMessage.textContent = 'El nom de la imatge només pot contindre lletres, números, guions baixos o guions i NO por contindre espais.';
        return false;
    }

    // Comprobar que la imagen sea de tipo PNG
    const fileType = image.type;
    if (fileType !== 'image/png') {
        errorMessage.textContent = 'La imatge ha de ser de tipus PNG';
        input.value = ''; // Reestablezco el campo
        return false;
    }

    // Comprobar que el tamaño sea inferior al especificado.
    const maxSize = 1000 * 300; // 300KB
    if (image.size > maxSize) {
        errorMessage.textContent = 'La imatge supera el tamany permés: 300KB';
        input.value = ''; // Reestablezco el campo
        return false;
    }

    // Comprobar que las dimensiones de la imagen estén dentro de las restricciones
    const minWidth = 500, maxWidth = 650;
    const minHeight = 230, maxHeight = 900;

    const limitedHeight = 700;

    return new Promise((resolve) => {
        const img = new Image();
        img.onload = function () {
            let aspectRatio = img.width/img.height;
            console.log("aspect ratio: " + aspectRatio);
            // Validar dimensiones de ancho y alto
            if (img.width < minWidth || img.width > maxWidth) {
                errorMessage.textContent = `L'ample de la imatge ha d'estar entre ${minWidth} i ${maxWidth} pixels.`;
                reject(new Error('Dimensiones de ancho inválidas'));
            } 
            
            if (img.height < minHeight || img.height > maxHeight) {
                errorMessage.textContent = `L'altura de la imatge ha d'estar entre ${minHeight} i ${maxHeight} pixels.`;
                reject(new Error('Dimensiones de alto inválidas'));
            } 

            if (aspectRatio < 0.73) {
                errorMessage.textContent = `La imatge és massa alta en comparació amb la seua amplària. L'ample recomanat és 650px, amb una altura mai superior a 900px.`;
                reject(new Error('Relación de aspecto inválida'));
            } 

            // Manejar relación de aspecto limitada
            const checkButton = document.getElementById(`input-check-${idPromo}`);
            const titleField = document.getElementById('input-title-' + idPromo);
            const subtitleField = document.getElementById('input-subtitle-' + idPromo);

            if (aspectRatio < 0.92) {
            //if (img.height > limitedHeight) {
                // La imagen es demasiado alta para poder contener texto adicional.
                errorMessage.classList.remove('text-danger');
                errorMessage.classList.add('text-warning');
                errorMessage.textContent = `L'altura és massa gran per a permetre afegir-hi text a través dels camps de títol i subtítol.`;

                if(checkButton) {
                    checkButton.disabled = true;
                    //checkButton.checked = false;
                }

                if (titleField) {
                    titleField.disabled = true;
                    //titleField.value = '';
                } 
                if (subtitleField) {
                    subtitleField.disabled = true;
                    //subtitleField.value = '';
                } 
            } else {
                // Restaurar si la altura está dentro de los límites
                if (checkButton) {
                    checkButton.disabled = false;
                    if (checkButton.checked) {
                        if (titleField) titleField.disabled = false;
                        if (subtitleField) subtitleField.disabled = false;
                    } else {
                        if (titleField) titleField.disabled = true;
                        if (subtitleField) subtitleField.disabled = true;
                    }
                } else {
                    if (titleField) titleField.disabled = false;
                    if (subtitleField) subtitleField.disabled = false;
                }

                

                errorMessage.classList.remove('text-warning');
                errorMessage.classList.add('text-danger');
            }

            // Resolver si el tamaño es válido
            resolve(true);
        };
        img.src = URL.createObjectURL(image);
    })
    .then(() => { // La imagen se ha validado
        // Previsualizar la imagen 
        const imageField = document.getElementById(`image-${idPromo}`);
        if (imageField) {
            imageField.src = URL.createObjectURL(image); // Cambia la imagen por la cargada
        } else {
            console.error(`No se encontró el campo de imagen con id 'image-${idPromo}'`);
        }

        // Activar botón de previsualización
        const previewField = document.getElementById(`preview-button-${idPromo}`);
        const saveButton = document.getElementById(`save-button-${idPromo}`);
        previewField.disabled = false;
        saveButton.disabled = true;

    })
    .catch((error) => {
        console.error(error.message);
        showAlert(`Error en la base de dades: ${error.message}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
    });
}

function toggleTextFields(checkbox, idPromo) {
    const titleField = document.getElementById(`input-title-${idPromo}`);
    const subtitleField = document.getElementById(`input-subtitle-${idPromo}`);

    // Activa o desactiva los campos según el estado del checkbox
    titleField.disabled = !checkbox.checked;
    subtitleField.disabled = !checkbox.checked;

    // per a activar el botó de previsualització
    handleFieldChange(idPromo);
}

function isValidFileName(fileName) {
    const regex = /^[a-zA-Z0-9_-]+-\d+\.png$/;
    return regex.test(fileName);
}

async function validateAndSavePromo(idPromo, isLivePromo=false) {
    // Deshabilitar el botón de guardado
    const saveButton = document.getElementById(`save-button-${idPromo}`);
    saveButton.disabled = true;

    // Obtengo los campos del formulario
    const name = document.getElementById(`input-name-${idPromo}`);
    const imageInput = document.getElementById("input-image-" + idPromo);
    const previewedImageField = document.getElementById(`image-${idPromo}`);

    // Fecha y hora de publicación
    const dateField = document.getElementById(`date-field-${idPromo}`);
    const date = dateField ? dateField.value : ''; 

    const timeField = document.getElementById(`time-field-${idPromo}`)
    const time = timeField ? timeField.value : '';

    // Obtener los valores de los campos opcionales
    var title = document.getElementById(`input-title-${idPromo}`);
    var subtitle = document.getElementById(`input-subtitle-${idPromo}`);


    // Asignar valores de los campos solo si no están deshabilitados
    title = title.disabled ? '' : title.value;
    subtitle = subtitle.disabled ? '' : subtitle.value;


    // Preparlo los datos para hacer la consulta
    var formData = new FormData();


    if(dateField && timeField) { // Solo si los campos fecha y hora existen
        // Comprobar que los campos no están vacíos
        if ( !(date) || !(time) ) {
            console.error('Data o hora no proporcionades');
            alert('Data o hora no proporcionades');
            return;
        }

        // Comprobar que la fecha y hora elegida de publicación no haya vencido.
        const now = new Date();
        const selectedDate = new Date(`${date}T${time}`);

        if(selectedDate < now) {
            console.error('La data i hora seleccionades han vençut. Per favor, selecciona una data i hora futures.');
            alert('La data i hora seleccionades han vençut. Per favor, selecciona una data i hora futures.');
            return;
        }

        // Formato esperado para fechas por la base de datos (YYYY-DD-MM HH:ii:ss)
        const [year, month, day] = date.split('-');
        const datetimeString = `${year}-${day}-${month} ${time}:00`;

        // Para la edición de una promoción activa no se estaría enviando la fecha de publicación ya que no existen los campos en el formulario.
        formData.append('publishDate', datetimeString);
    } 


    var imageName = null;
  
    // Si hay imagen cargada en el campo, se intenta subir al servidor FTP.
    if (imageInput.files.length > 0) {
        imageName = await uploadImagePromo(imageInput, idPromo);
    } else {
        // No hay imagen cargada en el campo
        try {
            // Intento guardarme la URL de la imagen previsualizada
            const url = previewedImageField ? previewedImageField.src: '';
            if (url) {
                new URL(url); // Lanzará excepción si la URL de la imagen no es válida (sin imagen o imagen por defecto)
                imageName = url.split('/').at(-1); // Obtener el nombre de la imagen a través de la URL
            } else {
                throw new Error("No s'ha trobat una imtge vàlida");
            }
        } catch(error) {
            console.error('Error al obtindre la imatge:', error);
            showAlert(`Error: ${error}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
            return;
        }
    }
    // Comprueba que el nombre de la imagen contiene un formato correcto.
    if(imageName && isValidFileName(imageName)) { // imagen válida
        formData.append('image', imageName); // Se modificará la imagen
   
    } else { // Si la imagen no es válida o ha habido un error, no se sustituye la imagen.
        imageInput.value = ''; // Se resetea el campo de la imagen
        // Lanzar el error correspondiente
        console.error('Ha ocorregut un error en guardar la imatge. Nom imatge:', imageName);
        showAlert(`Ha ocorregut un error en guardar la imatge. Contacta amb suportmultimedia@cvmc.es`, 'danger');
        return;
        
    }

    // Guardo el resto de campos
    // Si los campos title y/o subtitle están vacíos, serán también sustituidos en la base de datos.
    formData.append('idPromo', idPromo);
    formData.append('title', title);
    formData.append('subtitle', subtitle);

    // Comprobar si se está creando o actualizando la promo
    if (idPromo == 0) { // La promo con ID=0 corresponde a una promo nueva (ya sea nueva de trinca o una copia)
        if (!name.value) {
            console.error('El nom de la promoció és un camp obligatori!');
            alert('El nom de la promoció és un camp obligatori!');
            return;
        }
        formData.append('name', name.value);

        try {
            const response = await fetch('index.php?action=createPromo', {
                method: 'POST',
                body: formData
            });
    
            const data = await response.json(); // Parsear la respuesta como JSON
    
            if (data.success) {
                await sendLog("add", data.promo);
                location.reload(); // Recargar la página una vez se ha añadido la promo
    
            } else {
                console.error(data.message);
                showAlert(`Error en la base de dades: ${data.message}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert(`Error en la base de dades: ${error}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
        }

    } else {
        // Se actualiza una promo que ya existe

        try {
            const response = await fetch(`index.php?action=updatePromo`, {
                method: 'POST',
                body: formData
            });
    
            const data = await response.json(); // Parsear la respuesta como JSON
            if (data.success) {
                //location.reload(); // Si la promo se ha actualizado, previsualiza los cambios.
                showAlert(`Promoció actualitzada amb éxit!`, 'success');
                await sendLog("update", data.promo);

    
            } else {
                console.error(data.message);
                showAlert(`Error en la base de dades: ${data.message}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
            } 
        } catch (error) {
            console.error('Error:', error);
            showAlert(`Error en la base de dades: ${error}. Contacta amb suportmultimedia@cvmc.es`, 'danger');
        }
    } 
}

async function sendLog(action, promo) {
    var logData = new FormData();
    const promo_str = JSON.stringify(promo);
    console.log(promo_str);
    logData.append("action", action);
    logData.append("promo", promo_str);

    try {
        const response = await fetch('index.php?action=insertLog', {
            method: 'POST',
            body: logData
        });

        const data = await response.json(); 
        console.log(data.message);
        
    } catch {
        console.error("Error al generar log");
    }
}

async function loadList(offset=0, filterByName='%') {

    // Obtengo el contendor donde se volcará el listado
    const promosListContainer = document.getElementById('list-promo-container');
    
    if(offset == 0) promosListContainer.innerHTML = '';

    // Mostrar el spinner
    const spinner = document.getElementById('spinner');
    if (spinner) spinner.classList.remove('hidden-spinner');


    // Primero obtengo el estado seleccionado
    const statusValue = document.getElementById('select-load-promos').value;


    // Obtengo el valor del filtro
    const searchFilterValue = document.getElementById('input-search-promo');

    if(searchFilterValue && searchFilterValue.value != '') {
        filterByName = searchFilterValue.value;
    }
    
    try {
        const response = await fetch(`index.php?action=getPromos&status=${statusValue}&offset=${offset}&filterByName=${filterByName}`);
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(errorText);
        }
         
        const data = await response.text(); // Parsear la respuesta como JSON

        if(offset == 0) promosListContainer.innerHTML = data;
        else promosListContainer.innerHTML += data;
        
    } catch (error) {
        promosListContainer.innerHTML = '';
        console.error('Error:', error);
        showAlert(`${error}`, 'danger');

    } finally {
        // Ocultar el spinner después de completar la carga
        if (spinner) spinner.classList.add('hidden-spinner');
    }
    
}

async function loadActivePromo() {

    // Obtengo el contendor donde se volcará el listado
    const activePromoContainer = document.getElementById('live-promo-container');
    activePromoContainer.innerHTML = '';

    const status = 'active';
    
    try {
        const response = await fetch(`index.php?action=getPromos&status=${status}`);
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(errorText);
        }
         
        const data = await response.text(); // Parsear la respuesta como JSON
        activePromoContainer.innerHTML = data;
        
    } catch (error) {
        activePromoContainer.innerHTML = '';
        console.error('Error:', error);
        showAlert(`${error}`, 'danger');

    }
    
}


async function loadMore(offset) {
    var divLoadMore = document.getElementById(`load-more-${offset}`);

    // Asegurar que el botón de carga se mantiene visible hasta que termine el proceso
    divLoadMore.innerHTML = `<div id="spinner" class="hidden"><div class="loader"></div></div>`;

    try {
        await loadList(offset); // Espera a que la función termine antes de ocultar
    } finally {
        divLoadMore = document.getElementById(`load-more-${offset}`);
        divLoadMore.innerHTML = ''; // Limpia el contenido
        divLoadMore.hidden = true; // Oculta el contenedor
    }
}