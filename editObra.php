<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xtz";

$conn = new mysqli($servername, $username, $password, $dbname);

// Obtener el ID de la obra a editar
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    die("ID de obra no proporcionado.");
}

// Consulta SQL para obtener los datos de la obra con el ID proporcionado
$sql = "SELECT obras.id, obras.nombre, obras.nombre_constructora, obras.descripcion,
        (SELECT COUNT(*) FROM trabajadores WHERE trabajadores.obra = obras.nombre) AS trabajadores_asociados
        FROM obras
        WHERE obras.id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparando la consulta: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $obra = $result->fetch_assoc();

    // Actualizar el campo trabajadores_asociados
    $trabajadores_asociados = $obra['trabajadores_asociados'];

    // Actualizar el campo trabajadores_asociados en la base de datos
    $update_sql = "UPDATE obras SET trabajadores_asociados = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $trabajadores_asociados, $id);
    $update_stmt->execute();
    $update_stmt->close();

    // Devolver los datos en formato JSON
    echo json_encode($obra);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>