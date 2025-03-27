<?php

$config = require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');

    $ftp_server = $config['ftp_server'];
    $ftp_username = $config['ftp_user'];
    $ftp_password = $config['ftp_pass'];
    $ftp_url = $config['ftp_url'];
    $ftp_directory = $config['ftp_promos_dir'];



    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];

        // Obtener el nombre del archivo original
        $originalFileName = $_FILES['file']['name'];

        // Obtener el nombre del archivo
        $fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
        
        // Obtener la extensión del archivo
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // El nombre está conformado por el nombre original y el timestamp de subida.
        // Ejemplo: imatgePromoProva.png -> imatgePromoProva-1737465157.png
        $fileName = $fileNameWithoutExtension . '-' . time() . '.' . $fileExtension; 

        // Carpeta donde se va a subir la imagen
        $destinationFilePath = $ftp_directory . '/' . $fileName;

        // Obtener URL para visualizar la imagen
        //$fileURL = $ftp_url . '/' . $destinationFilePath;

        // Conectar al servidor FTP
        $ftp_conn = ftp_connect($ftp_server);
        
        if ($ftp_conn) {
            $login = ftp_login($ftp_conn, $ftp_username, $ftp_password);
            if ($login) {
                // Habilitar modo pasivo
                ftp_pasv($ftp_conn, true);

                // Subir el archivo al servidor FTP
                if (ftp_put($ftp_conn, $destinationFilePath, $fileTmpPath, FTP_BINARY)) {
                    $response = [
                        'success' => true,
                        'fileName' => $fileName
                    ];
                } else {
                    $response = ['success' => false, 'error' => 'Error al subir el archivo al servidor FTP'];
                }
            } else {
                $response = ['success' => false, 'error' => 'Error al iniciar sesión en el servidor FTP'];
            }
            ftp_close($ftp_conn);
        } else {
            $response = ['success' => false, 'error' => 'Error al conectar con el servidor FTP'];
        }
    } else {
        $response = ['success' => false, 'error' => 'Error al cargar el archivo'];
    }

    echo json_encode($response);
}
?>
