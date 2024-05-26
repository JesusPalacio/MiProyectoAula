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

$option = $_GET['option'];

// Consulta SQL para obtener los datos según la opción
$sql = "SELECT * FROM $option";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Array para almacenar los datos
    $data = array();
    while ($row = $result->fetch_assoc()) {
        // Agregar cada fila como un elemento al array
        $data[] = $row;
    }
    // Devolver los datos en formato JSON
    echo json_encode($data);
} else {
    echo json_encode([]);
}

$conn->close();
?>
