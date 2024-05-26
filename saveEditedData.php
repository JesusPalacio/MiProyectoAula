<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xtz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

$id = isset($_POST['id']) ? $_POST['id'] : null;
$option = isset($_POST['option']) ? $_POST['option'] : null;

if ($option === 'trabajadores') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $tipo_documento = isset($_POST['tipo_documento']) ? $_POST['tipo_documento'] : null;
    $numero_documento = isset($_POST['numero_documento']) ? $_POST['numero_documento'] : null;
    $ocupacion = isset($_POST['ocupacion']) ? $_POST['ocupacion'] : null;
    $activo = isset($_POST['activo']) ? $_POST['activo'] : 'No';
    $obra = isset($_POST['obra']) ? $_POST['obra'] : null;

    // Obtener el nombre de la obra anterior del trabajador
    $get_obra_anterior_sql = "SELECT obra FROM trabajadores WHERE id = ?";
    $get_obra_anterior_stmt = $conn->prepare($get_obra_anterior_sql);
    $get_obra_anterior_stmt->bind_param("i", $id);
    $get_obra_anterior_stmt->execute();
    $get_obra_anterior_result = $get_obra_anterior_stmt->get_result();
    $obra_anterior_data = $get_obra_anterior_result->fetch_assoc();
    $obra_anterior = $obra_anterior_data['obra'];
    $get_obra_anterior_stmt->close();

    $sql = "UPDATE trabajadores SET nombre=?, tipo_documento=?, numero_documento=?, ocupacion=?, activo=?, obra=?, archivo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparando la consulta: " . $conn->error);
    }
    $stmt->bind_param('sssssssi', $nombre, $tipo_documento, $numero_documento, $ocupacion, $activo, $obra, $uploadedFile,$id);
    $stmt->execute();

    // Si se ha subido un archivo, actualizar también el campo 'archivo'
    if ($uploadedFile != '') {
        $sql .= ", archivo = '$uploadedFile'";
    }

    // Actualizar el campo trabajadores_asociados en la tabla obras
    $update_trabajadores_asociados_sql = "UPDATE obras SET trabajadores_asociados = (SELECT COUNT(*) FROM trabajadores WHERE obra = ?) WHERE nombre = ?";
    $update_stmt1 = $conn->prepare($update_trabajadores_asociados_sql);
    $update_stmt1->bind_param("ss", $obra, $obra);
    $update_stmt1->execute();

    if ($obra_anterior !== $obra) {
        // Si la obra cambió, actualizar también la obra anterior
        $update_stmt2 = $conn->prepare($update_trabajadores_asociados_sql);
        $update_stmt2->bind_param("ss", $obra_anterior, $obra_anterior);
        $update_stmt2->execute();
        $update_stmt2->close();
    }

} elseif ($option === 'constructoras') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $nit = isset($_POST['nit']) ? $_POST['nit'] : null;
    $nombre_contacto = isset($_POST['nombre_contacto']) ? $_POST['nombre_contacto'] : null;
    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
    $tipo_contrato = isset($_POST['tipo_contrato']) ? $_POST['tipo_contrato'] : null;

    $sql = "UPDATE constructoras SET nombre=?, nit=?, nombre_contacto=?, correo=?, tipo_contrato=?, archivo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparando la consulta: " . $conn->error);
    }

    $stmt->bind_param('ssssssi', $nombre, $nit, $nombre_contacto, $correo, $tipo_contrato, $uploadedFile, $id);


    // Si se ha subido un archivo, actualizar también el campo 'archivo'
    if ($uploadedFile != '') {
        $sql .= ", archivo = '$uploadedFile'";
    }

} elseif ($option === 'obras') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $nombre_constructora = isset($_POST['nombre_constructora']) ? $_POST['nombre_constructora'] : null;
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;

    $sql = "UPDATE obras SET nombre=?, nombre_constructora=?, descripcion=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparando la consulta: " . $conn->error);
    }
    $stmt->bind_param('sssi', $nombre, $nombre_constructora, $descripcion, $id);
} else {
    die("Opción no válida.");
}

if ($stmt->execute()) {
    echo "Registro actualizado con éxito";
} else {
    echo "Error actualizando registro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>