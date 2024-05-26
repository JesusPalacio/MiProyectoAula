<?php
// Código PHP para procesar el inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conexión a la base de datos
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "xtz";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta para verificar el usuario y la contraseña (distingue mayúsculas y minúsculas)
    $sql = "SELECT * FROM login WHERE BINARY usuario = '$username' AND BINARY contraseña = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Inicio de sesión exitoso, redirigir a la página de inicio
        header("Location: index.html");
        exit();
    } else {
        echo "<p style='color: red; text-align: center;'>Usuario o contraseña incorrectos</p>";
    }

    $conn->close();
}
?>