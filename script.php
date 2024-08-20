<?php
session_start();

$servername = "localhost";
$username = "cetvirtu_app_web_dev";
$password = "A0uEouZnFf*@";
$dbname = "cetvirtu_appweb_kine";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = md5($_POST['password']); // Encriptar la contraseña usando MD5

    $sql = "SELECT id, rut, clave, tipo_usuario FROM usuario WHERE rut = ? AND clave = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Inicio de sesión exitoso
        $stmt->bind_result($id, $rut, $clave, $tipo_usuario);
        $stmt->fetch();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['tipo_usuario'] = $tipo_usuario; // Guardar tipo de usuario en la sesión

        header("Location: https://kineintegra.com/cetvirtu/Aplicacion_web_Kineintegra/Login_registro/pagina.php");
        exit();
    } else {
        // Usuario o contraseña incorrectos
        $_SESSION['error'] = "Usuario o contraseña incorrectos";
        header("Location: index.php");
        exit();
    }
}

$conn->close();
?>
