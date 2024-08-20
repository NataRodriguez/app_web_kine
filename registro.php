<?php
session_start();

// Verificar si el usuario está logueado y si es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    // Si no está logueado o no es administrador, redirigir a la página de login
    header("Location: pagina.html");
    exit();
}

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
    $rut = $_POST['rut'];
    $password = md5($_POST['password']); // Encriptar la contraseña usando MD5
    $tipo_usuario = $_POST['tipo_usuario'];

    $sql = "INSERT INTO usuario (rut, clave, tipo_usuario) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $rut, $password, $tipo_usuario);

    if ($stmt->execute()) {
        $mensaje = "Usuario registrado exitosamente.";
    } else {
        $mensaje = "Error al registrar el usuario: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Usuario - Kineintegra</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="header">
        <nav>
            <ul>
                <li><a href="pagina.html">Inicio</a></li>
                <li><a href="pagina2.html">Pacientes</a></li>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="ver_usuarios.php">Ver usuarios</a></li>
                <li><a href="pagina-informe.php">Ver Informes</a></li>
                <li><a href="cerrar.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
    <div class="welcome-container">        
        <?php
        if (isset($mensaje)) {
            echo "<div class='message'>$mensaje</div>";
        }
        ?>

        <form action="registro.php" method="POST">
            <div class="row form-group">
                <div class="column">
                    <label for="rut">RUT:</label>
                    <input type="text" name="rut" id="rut" placeholder="RUT del usuario" required>
                </div>
                <div class="column">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password" placeholder="Contraseña" required>
                </div>
            </div>
            <div class="row form-group">
                <div class="column">
                    <label for="tipo_usuario">Tipo de Usuario:</label>
                    <select name="tipo_usuario" id="tipo_usuario" required>
                        <option value="1">Administrador</option>
                        <option value="2">Usuario Primario</option>
                        <option value="3">Cliente</option>
                    </select>
                </div>
            </div>
            <button type="submit">Registrar</button>
        </form>       
    </div>
    <script src="js/script.js"></script>
</body>
</html>

