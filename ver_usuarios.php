<?php
session_start();

// Verificar si el usuario está logueado y si es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    // Si no está logueado o no es administrador, redirigir a la página de login
    header("Location: index.php");
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

// Manejar la activación o desactivación de usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $estado_actual = $_POST['estado_actual'];
    $nuevo_estado = $estado_actual == 1 ? 0 : 1; // Cambiar el estado

    $sql = "UPDATE usuario SET activo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nuevo_estado, $usuario_id);

    if ($stmt->execute()) {
        $mensaje = "Estado del usuario actualizado exitosamente.";
    } else {
        $mensaje = "Error al actualizar el estado del usuario: " . $stmt->error;
    }
}

// Obtener la lista de usuarios
$sql = "SELECT id, rut, tipo_usuario, activo FROM usuario";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ver Usuarios - Kineintegra</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="header">
        <nav>
            <ul>
                <li><a href="pagina.html">Inicio</a></li>
                <li><a href="pagina2.html">Pacientes</a></li>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="ver_usuarios.php">Ver Usuarios</a></li>
            </ul>
        </nav>
    </div>
    <div class="welcome-container">
        <h2>Usuarios Registrados</h2>
        
        <?php
        if (isset($mensaje)) {
            echo "<div class='message'>$mensaje</div>";
        }
        ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>RUT</th>
                        <th>Tipo de Usuario</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $estado = $row['activo'] == 1 ? "Activo" : "Inactivo";
                            $boton_accion = $row['activo'] == 1 ? "Desactivar" : "Activar";
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['rut'] . "</td>";
                            echo "<td>" . ($row['tipo_usuario'] == 1 ? "Administrador" : "Usuario Primario") . "</td>";
                            echo "<td>" . $estado . "</td>";
                            echo "<td>
                                    <form action='ver_usuarios.php' method='POST'>
                                        <input type='hidden' name='usuario_id' value='" . $row['id'] . "'>
                                        <input type='hidden' name='estado_actual' value='" . $row['activo'] . "'>
                                        <button type='submit'>$boton_accion</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
