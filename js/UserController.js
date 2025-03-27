async function validateAndCreateUser(event) {

    // Evitar el envío del formulario por defecto
    event.preventDefault();

    const usernameField = document.getElementById('username');
    const passwordField = document.getElementById('password');

    if (!(usernameField) || !(passwordField)) {
        alert('Error al obtenir els camps del formulari');
        return;
    }

    // obtengo los valores de esos campos
    const username = usernameField.value.trim();
    const password = passwordField.value.trim();

    // Compruebo que todos los campos están rellenados
    if(!(username) || !(password)) {
        alert('Usuari o contrasenya no proporcionats');
        return;
    }

    // Validación adicional opcional
    if (password.length < 6) {
        alert('La contrasenya ha de tenir almenys 6 caràcters');
        return;
    }

    formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);

    try {
        const response = await fetch('/index.php?action=createUser', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Error en la resposta del servidor');
        }

        const data = await response.json();

        if(data.success) {
            console.log('Usuari creat amb éxit!');
            alert('Usuari creat amb éxit!');
            // Limpio el formulario 
            usernameField.value = '';
            passwordField.value = '';
        } else {
            alert(data.message);
        }

    } catch (error) {
        alert('Error en la comunicació amb el servidor: ' + error.message);
    }
        
}