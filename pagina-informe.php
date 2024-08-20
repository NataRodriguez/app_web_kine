<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "cetvirtu_app_web_dev";
$password = "A0uEouZnFf*@";
$dbname = "cetvirtu_appweb_kine";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Cargar los clientes para el formulario de búsqueda
$clientes = [];
$sql_clientes = "SELECT id, nombre FROM cliente";
$result_clientes = $conn->query($sql_clientes);
if ($result_clientes->num_rows > 0) {
    while ($row = $result_clientes->fetch_assoc()) {
        $clientes[] = $row;
    }
}

// Variables para los filtros
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
$cliente_id = isset($_POST['cliente_id']) ? $_POST['cliente_id'] : '';

// Solo ejecutar la consulta si se ha enviado el formulario
$informes = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql_informes = "SELECT e.id, e.fecha, c.nombre 
                     FROM examen e 
                     JOIN cliente c ON e.cliente_id = c.id 
                     WHERE 1=1";

    if ($fecha_inicio && $fecha_fin) {
        $sql_informes .= " AND e.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }

    if ($cliente_id) {
        $sql_informes .= " AND c.id = $cliente_id";
    }

    $sql_informes .= " ORDER BY e.fecha DESC";
    $result_informes = $conn->query($sql_informes);

    if ($result_informes->num_rows > 0) {
        while ($row = $result_informes->fetch_assoc()) {
            $informes[] = $row;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>

</head>
<body>
    <div class="header">
        <nav>
            <ul>
                <li><a href="pagina.html">Inicio</a></li>
                <li><a href="pagina2.html">Pacientes</a></li>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="ver_usuarios.php">Ver usuarios</a></li>
                <li><a href="cerrar.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
    <div class="welcome-container">
        <h2>Informe de Exámenes</h2>
        <form method="POST" action="pagina-informe.php">
            <div>
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
            </div>
            <div>
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
            </div>
            <div>
                <label for="cliente_id">Cliente:</label>
                <select id="cliente_id" name="cliente_id">
                    <option value="">Todos los clientes</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>" <?php if ($cliente_id == $cliente['id']) echo 'selected'; ?>>
                            <?php echo $cliente['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit">Buscar</button>
            </div>
        </form>

        <?php if (!empty($informes)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Fecha del Examen</th>
                        <th>Cliente</th>
                        <th>Ver Informe</th>
                        <th>Generar PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($informes as $informe): ?>
                        <tr>
                            <td><?php echo date('d-m-Y', strtotime($informe['fecha'])); ?></td>
                            <td><?php echo $informe['nombre']; ?></td>
                            <td><button onclick="verInforme(<?php echo $informe['id']; ?>)">Ver Informe</button></td>
                            <td><button onclick="generarPDF(<?php echo $informe['id']; ?>)">Generar PDF</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <p>No se encontraron informes para los criterios de búsqueda especificados.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Modal para mostrar el informe -->
    <div id="modalInforme">
        <div id="modalContent">
            <span class="closeModal" onclick="cerrarModal()">&times;</span>
            <div id="contenidoInforme"></div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        function verInforme(examen_id) {
            fetch('ver-informe.php?examen_id=' + examen_id)
                .then(response => response.json())
                .then(data => {
                    let content = '<table border="1"><thead><tr><th>Examen</th><th>Fecha de Carga</th><th>Resultado</th><th>Estado</th></tr></thead><tbody>';
                    data.forEach(row => {
                        content += `<tr><td>${row.examen}</td><td>${row.fecha_carga}</td><td>${row.resultado}</td><td>${row.estado}</td></tr>`;
                    });
                    content += '</tbody></table>';
                    document.getElementById('contenidoInforme').innerHTML = content;
                    document.getElementById('modalInforme').style.display = 'block';
                });
        }

        function cerrarModal() {
            document.getElementById('modalInforme').style.display = 'none';
        }

        function generarPDF(examen_id) {
    fetch('ver-informe.php?examen_id=' + examen_id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error en la respuesta:', data.error);
                alert('Hubo un problema al generar el PDF: ' + data.error);
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            if (data.length > 0) {
                const paciente = data[0];
                
                // Información del paciente
                const patientName = paciente.cliente_nombre;
                const patientRut = paciente.cliente_rut;
                const examDate = new Date().toLocaleDateString(); // Fecha actual o puedes usar la fecha del examen
                const patientAge = paciente.cliente_edad;
                const gender = paciente.cliente_genero;

                // Agregar datos del paciente al PDF
                doc.text(`Nombre del Paciente: ${patientName}`, 10, 10);
                doc.text(`RUT del Paciente: ${patientRut}`, 10, 20);
                doc.text(`Género: ${gender}`, 10, 30);
                doc.text(`Fecha del Examen: ${examDate}`, 10, 40);
                doc.text(`Edad del Paciente: ${patientAge}`, 10, 50);

                // Obtener los datos del examen
                const tableData = data.map(row => [row.examen, row.resultado, row.estado]);

                // Generar la tabla en el PDF
                doc.autoTable({
                    head: [['Examen', 'Resultado', 'Estado']],
                    body: tableData,
                    startY: 60
                });

                // Guardar el PDF
                doc.save(`examen-${patientName}-${examDate}.pdf`);
            } else {
                alert('No se encontraron detalles para este informe.');
            }
        })
        .catch(error => {
            console.error('Error al generar el PDF:', error);
            alert('Hubo un problema al generar el PDF: ' + error.message);
        });
}

    </script>
</body>
</html>
