<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xtz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$option = $_POST['option'];
$uploadedFile = '';

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['archivo']['tmp_name'];
    $fileName = $_FILES['archivo']['name'];
    $fileSize = $_FILES['archivo']['size'];
    $fileType = $_FILES['archivo']['type'];
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

    if(move_uploaded_file($fileTmpPath, $dest_path)) {
        $uploadedFile = $dest_path;
    } else {
        die("Hubo un error al subir el archivo. Inténtalo de nuevo.");
    }
}

if ($option === 'trabajadores') {
    // Verificar que todos los campos requeridos están presentes y no están vacíos
    if (!isset($_POST['nombre'], $_POST['tipo_documento'], $_POST['numero_documento'], $_POST['ocupacion'], $_POST['obra']) || 
        empty($_POST['nombre']) || empty($_POST['tipo_documento']) || empty($_POST['numero_documento']) || empty($_POST['ocupacion']) || empty($_POST['obra'])) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Procesar el formulario de trabajadores
    $nombre = $_POST['nombre'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $ocupacion = $_POST['ocupacion'];
    $activo = isset($_POST['activo']) && $_POST['activo'] === 'Si' ? 'Si' : 'No';
    $obra = $_POST['obra'];

    // Incluir la ruta del archivo en la base de datos
    $sql = "INSERT INTO trabajadores (nombre, tipo_documento, numero_documento, ocupacion, activo, obra, archivo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nombre, $tipo_documento, $numero_documento, $ocupacion, $activo, $obra, $uploadedFile);

    if ($stmt->execute()) {
        
        // Actualizar el campo trabajadores_asociados en la tabla obras
        $update_trabajadores_asociados_sql = "UPDATE obras SET trabajadores_asociados = (SELECT COUNT(*) FROM trabajadores WHERE obra = ?) WHERE nombre = ?";
        $update_stmt = $conn->prepare($update_trabajadores_asociados_sql);
        $update_stmt->bind_param("ss", $obra, $obra);
        $update_stmt->execute();
        $update_stmt->close();
        
        echo "Registro guardado exitosamente";
    } else {
        echo "Error guardando registro: " . $stmt->error;
    }

    $stmt->close();
} elseif ($option === 'constructoras') {
    // Verificar que todos los campos requeridos están presentes y no están vacíos
    if (!isset($_POST['nombre'], $_POST['nit'], $_POST['nombre_contacto'], $_POST['correo'], $_POST['tipo_contrato']) || 
        empty($_POST['nombre']) || empty($_POST['nit']) || empty($_POST['nombre_contacto']) || empty($_POST['correo']) || empty($_POST['tipo_contrato'])) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Procesar el formulario de constructoras
    $nombre = $_POST['nombre'];
    $nit = $_POST['nit'];
    $nombre_contacto = $_POST['nombre_contacto'];
    $correo = $_POST['correo'];
    $tipo_contrato = $_POST['tipo_contrato'];

    // Incluir la ruta del archivo en la base de datos
    $sql = "INSERT INTO constructoras (nombre, nit, nombre_contacto, correo, tipo_contrato, archivo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $nit, $nombre_contacto, $correo, $tipo_contrato, $uploadedFile);

    if ($stmt->execute()) {
        echo "Registro guardado exitosamente";
    } else {
        echo "Error guardando registro: " . $stmt->error;
    }

} elseif ($option === 'obras') {
    // Verificar que todos los campos requeridos están presentes y no están vacíos
    if (!isset($_POST['nombre'], $_POST['nombre_constructora'], $_POST['descripcion']) || 
        empty($_POST['nombre']) || empty($_POST['nombre_constructora']) || empty($_POST['descripcion'])) {
        die("Error: Todos los campos son obligatorios.");
    }

    $nombre = $_POST['nombre'];
    $nombre_constructora = $_POST['nombre_constructora'];
    $descripcion = $_POST['descripcion'];

    // Verificar si el nombre de la obra ya existe
    $check_nombre_sql = "SELECT COUNT(*) AS count FROM obras WHERE nombre = ?";
    $check_nombre_stmt = $conn->prepare($check_nombre_sql);
    $check_nombre_stmt->bind_param('s', $nombre);
    $check_nombre_stmt->execute();
    $result = $check_nombre_stmt->get_result();
    $nombre_data = $result->fetch_assoc();
    $nombre_count = $nombre_data['count'];
    $check_nombre_stmt->close();

    if ($nombre_count > 0) {
        echo "Nombre de obra ya existe";
        $conn->close();
        exit;
    }

    // Obtener el recuento de trabajadores asociados a la nueva obra
    $get_trabajadores_asociados_sql = "SELECT COUNT(*) AS trabajadores_asociados FROM trabajadores WHERE obra = ?";
    $get_trabajadores_asociados_stmt = $conn->prepare($get_trabajadores_asociados_sql);
    $get_trabajadores_asociados_stmt->bind_param('s', $nombre);
    $get_trabajadores_asociados_stmt->execute();
    $result = $get_trabajadores_asociados_stmt->get_result();
    $trabajadores_asociados_data = $result->fetch_assoc();
    $trabajadores_asociados = $trabajadores_asociados_data['trabajadores_asociados'];
    $get_trabajadores_asociados_stmt->close();

    $sql = "INSERT INTO obras (nombre, nombre_constructora, descripcion, trabajadores_asociados) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparando la consulta: " . $conn->error);
    }
    $stmt->bind_param('ssss', $nombre, $nombre_constructora, $descripcion, $trabajadores_asociados);

    if ($stmt->execute()) {
        echo "Registro guardado exitosamente";
    } else {
        echo "Error guardando registro: " . $stmt->error;
    }

    $stmt->close();
} else {
    die("Opción no válida.");
}

$conn->close();
?>