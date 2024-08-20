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
    <title>Nueva Página de Exámenes</title>
    <!-- Incluye jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- Incluye jsPDF-AutoTable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>
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
        <h2>Registrar Examen del Paciente</h2>
        <form id="examForm">          
            <div class="row form-group">
                <div class="column">
                    <label for="patientName">Nombre del Paciente:</label>
                    <input type="text" id="patientName" name="patientName" required>
                </div>
                <div class="column">
                    <label for="patientRut">RUT del Paciente:</label>
                    <input type="text" id="patientRut" name="patientRut" required>
                </div>
            </div>
            <div class="row form-group">
                <div class="column">
                    <label for="examDate">Fecha del Examen:</label>
                    <input type="date" id="examDate" name="examDate" required>
                </div>
                <div class="column">
                    <label for="patientAge">Edad del Paciente:</label>
                    <input type="number" id="patientAge" name="patientAge" required>
                </div>
            </div>
            <div class="row form-group">
                <div class="column">
                    <label for="gender">Género:</label>
                    <select id="gender" name="gender" required>
                        <option value="Hombre">Hombre</option>
                        <option value="Mujer">Mujer</option>
                    </select>
                </div>
            </div>
        
            <table id="examTable">
                <thead>
                    <tr>
                        <th>Examen</th>
                        <th>Resultado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="examTableBody">
                    <!-- Aquí se llenará la tabla con los exámenes -->
                </tbody>
            </table>
        
            <button type="submit">Guardar</button>
        </form>
        
        <button type="button" onclick="generatePDF()">Guardar como PDF</button>
    </div>
    <script src="js/script2.js"></script>
    <script>
        document.getElementById('examForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir que el formulario se envíe de la manera tradicional
            
            var formData = new FormData(this);

            fetch('guardar_examen.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    alert('Datos guardados exitosamente.');
                } else {
                    alert('Error al guardar los datos.');
                }
            })
            .catch(error => {
                alert('Error en la solicitud: ' + error.message);
            });
        });
    </script>
</body>
</html>