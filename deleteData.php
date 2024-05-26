<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xtz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$option = isset($_GET['option']) ? $_GET['option'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($option === 'trabajadores') {
    // Obtener el nombre de la obra del trabajador a eliminar
    $get_obra_sql = "SELECT obra FROM trabajadores WHERE id = ?";
    $get_obra_stmt = $conn->prepare($get_obra_sql);
    $get_obra_stmt->bind_param("i", $id);
    $get_obra_stmt->execute();
    $get_obra_result = $get_obra_stmt->get_result();
    $obra_data = $get_obra_result->fetch_assoc();
    $obra = $obra_data['obra'];
    $get_obra_stmt->close();

    // Eliminar el trabajador
    $sql = "DELETE FROM trabajadores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Actualizar el campo trabajadores_asociados en la tabla obras
        $update_trabajadores_asociados_sql = "UPDATE obras SET trabajadores_asociados = (SELECT COUNT(*) FROM trabajadores WHERE obra = ?) WHERE nombre = ?";
        $update_stmt = $conn->prepare($update_trabajadores_asociados_sql);
        $update_stmt->bind_param("ss", $obra, $obra);
        $update_stmt->execute();
        $update_stmt->close();

        echo "Registro eliminado exitosamente";
    } else {
        echo "Error eliminando registro: " . $stmt->error;
    }

    $stmt->close();
} elseif ($option === 'constructoras') {
    $sql = "DELETE FROM constructoras WHERE ID = ?";
} elseif ($option === 'obras') {
    $sql = "DELETE FROM obras WHERE ID = ?";
} else {
    echo "Opción no válida";
    $conn->close();
    exit();
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Registro eliminado exitosamente";
    }
} else {
    echo "Error al eliminar el registro: " . $stmt->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
