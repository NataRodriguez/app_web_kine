<!DOCTYPE html>
<html>
<head>
    <title>Kineintegra</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main-box">
        <img src="user.png" class="avatar" alt="avatar">
        <h1>Kineintegra</h1>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']); // Limpiar el mensaje de error después de mostrarlo
        }
        ?>
        <form id="form-login" action="script.php" method="POST">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" placeholder="Nombre de usuario" id="usuario" required>
            <label for="password">Contraseña</label>
            <input type="password" name="password" placeholder="Contraseña" id="password" required>
            <input type="submit" value="Entrar">
        </form>
    </div>
    <script src="js/login.js"></script>
</body>
</html>
