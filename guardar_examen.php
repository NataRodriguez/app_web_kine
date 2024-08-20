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

// Cargar el JSON desde el archivo
$json_data = file_get_contents('datos/datos-sitio.json');
$data = json_decode($json_data, true);

function log_message($message) {
    $logfile = 'logfile.txt';
    file_put_contents($logfile, date("Y-m-d H:i:s") . " - " . $message . "\n", FILE_APPEND);
}

log_message("Inicio de proceso");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['patientName'];
    $rut = $_POST['patientRut'];
    $edad = $_POST['patientAge'];
    $fecha_examen = $_POST['examDate'] . ' ' . date('H:i:s'); // Agregar la hora actual
    $gender = $_POST['gender'];

    log_message("Datos recibidos - Nombre: $nombre, RUT: $rut, Edad: $edad, Fecha de Examen: $fecha_examen, Género: $gender");

    // Verificar si el RUT ya está registrado
    $sql_verificar = "SELECT id FROM cliente WHERE rut = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("s", $rut);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();

    if ($stmt_verificar->num_rows > 0) {
        $stmt_verificar->bind_result($cliente_id);
        $stmt_verificar->fetch();
        log_message("RUT encontrado, actualizando cliente ID: $cliente_id");

        $sql_actualizar = "UPDATE cliente SET nombre = ?, edad = ? WHERE id = ?";
        $stmt_actualizar = $conn->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("sii", $nombre, $edad, $cliente_id);
        $stmt_actualizar->execute();

        log_message("Cliente actualizado");
    } else {
        log_message("RUT no encontrado, insertando nuevo cliente");

        $sql_cliente = "INSERT INTO cliente (rut, nombre, edad, genero) VALUES (?, ?, ?, ?)";
        $stmt_cliente = $conn->prepare($sql_cliente);
        $stmt_cliente->bind_param("ssis", $rut, $nombre, $edad, $gender);
        $stmt_cliente->execute();

        $cliente_id = $stmt_cliente->insert_id;
        log_message("Nuevo cliente insertado con ID: $cliente_id");
    }

    // Insertar un nuevo examen en la tabla `examen`
    $sql_examen = "INSERT INTO examen (fecha, cliente_id) VALUES (?, ?)";
    $stmt_examen = $conn->prepare($sql_examen);
    $stmt_examen->bind_param("si", $fecha_examen, $cliente_id);
    $stmt_examen->execute();

    // Obtener el ID del examen insertado
    $examen_id = $stmt_examen->insert_id;
    log_message("Nuevo examen insertado con ID: $examen_id");

    // Calcular el rango de edad
    $ageRange = null;
    foreach ($data[$gender] as $range => $exams) {
        list($minAge, $maxAge) = explode('-', $range);
        if ($edad >= $minAge && $edad <= $maxAge) {
            $ageRange = $exams;
            break;
        }
    }

    // Procesar los resultados de los exámenes
    for ($index = 0; isset($_POST["result$index"]); $index++) {
        $examen = $_POST["examen_$index"];
        $resultado = $_POST["result$index"];
        $fecha_carga = date('Y-m-d H:i:s');

        if ($ageRange && isset($ageRange[$examen])) {
            $min = $ageRange[$examen]['min'];
            $max = $ageRange[$examen]['max'];

            if ($resultado < $min) {
                $estado = 'En riesgo';
            } elseif ($resultado > $max) {
                $estado = 'Excelente estado';
            } else {
                $estado = 'Normal';
            }
        } else {
            $estado = 'Desconocido';
        }

        log_message("Procesando resultado del examen: $examen, Resultado: $resultado, Estado: $estado");

        // Insertar el resultado del examen en la tabla `resultado_examen`
        $sql_resultado = "INSERT INTO resultado_examen (examen_id, examen, fecha_carga, resultado, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt_resultado = $conn->prepare($sql_resultado);
        $stmt_resultado->bind_param("issss", $examen_id, $examen, $fecha_carga, $resultado, $estado);
        $response = array();

        if ($stmt_resultado->execute()) {
            log_message("Resultado del examen $examen insertado correctamente");
            $response['status'] = 'ok';
        } else {
            log_message("Error al guardar el resultado del examen $examen: " . $stmt_resultado->error);
            $response['status'] = 'fail';
        }
    }

    log_message("Proceso finalizado");
    echo json_encode($response);
}

$conn->close();

?>