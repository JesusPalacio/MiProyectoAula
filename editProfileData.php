<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xtz";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recuperar datos del formulario
$nombre_empresa = $_POST['nombre_empresa'];
$direccion = $_POST['direccion'];
$correo_empresa = $_POST['correo_empresa'];
$telefono = $_POST['telefono'];
$descripcion = $_POST['descripcion'];
$uploadedFile = '';

// Verificar y procesar el archivo subido
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['archivo']['tmp_name'];
    $fileName = $_FILES['archivo']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Define la nueva ruta para el archivo
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $uploadFileDir = 'uploads/';
    
    // Verificar y crear el directorio si no existe
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
    }

    $dest_path = $uploadFileDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        $uploadedFile = $dest_path;
    } else {
        die("Hubo un error al subir el archivo. Inténtalo de nuevo.");
    }
}

// Consulta SQL para actualizar los datos del perfil
$sql = "UPDATE empresa 
        SET direccion = '$direccion', 
            correo_empresa = '$correo_empresa', 
            telefono = '$telefono', 
            descripcion = '$descripcion'";

// Si se ha subido un archivo, actualizar también el campo 'archivo'
if ($uploadedFile != '') {
    $sql .= ", archivo = '$uploadedFile'";
}

$sql .= " WHERE nombre_empresa = '$nombre_empresa'";

if ($conn->query($sql) === TRUE) {
    echo "Perfil actualizado correctamente";
} else {
    echo "Error al actualizar el perfil: " . $conn->error;
}

// Cerrar conexión
$conn->close();
?>
