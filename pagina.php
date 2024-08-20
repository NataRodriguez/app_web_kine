<?php
session_start();

// Verificar si la variable de sesión está definida
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirigir al index
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="header">
        <nav>
            <ul>
                <li><a href="pagina.php">Inicio</a></li>
                <li><a href="pagina2.php">Pacientes</a></li>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="ver_usuarios.php">Ver usuarios</a></li>
                <li><a href="pagina-informe.php">Ver Informes</a></li>
                <li><a href="cerrar.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
    <div class="welcome-container">
        <h2>Bienvenido</h2>
        <div class="row form-group">
            <div class="column">
                <label for="gender">Género:</label>
                <select id="gender" name="gender" required>
                    <option value="Hombre">Hombre</option>
                    <option value="Mujer">Mujer</option>
                </select>
            </div>
            <div class="column">
                <label for="ageRangeSelector">Selecciona un rango de edad:</label>
                <select id="ageRangeSelector"></select>
            </div>
        </div>
        <div class="table-container">
            <form id="examForm">
                <table>
                    <thead>
                        <tr>
                            <th>Examen</th>
                            <th>Valor Mínimo</th>
                            <th>Valor Máximo</th>
                        </tr>
                    </thead>
                    <tbody id="examTableBody">
                    </tbody>
                </table>
                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
