<?php
$servername = "localhost";
$username = "cetvirtu_app_web_dev";
$password = "A0uEouZnFf*@";
$dbname = "cetvirtu_appweb_kine";

header('Content-Type: application/json'); // Asegura que se devuelva JSON

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

$examen_id = isset($_GET['examen_id']) ? intval($_GET['examen_id']) : 0;
$detalles = [];

if ($examen_id) {
    $sql = "SELECT r.examen, r.fecha_carga, r.resultado, r.estado, c.nombre as cliente_nombre, c.rut as cliente_rut, c.edad as cliente_edad, c.genero as cliente_genero
            FROM resultado_examen r
            JOIN examen e ON r.examen_id = e.id
            JOIN cliente c ON e.cliente_id = c.id
            WHERE r.examen_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $examen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $detalles[] = [
                'examen' => $row['examen'],
                'fecha_carga' => date('d-m-Y H:i:s', strtotime($row['fecha_carga'])),
                'resultado' => $row['resultado'],
                'estado' => $row['estado'],
                'cliente_nombre' => $row['cliente_nombre'],
                'cliente_rut' => $row['cliente_rut'],
                'cliente_edad' => $row['cliente_edad'],
                'cliente_genero' => $row['cliente_genero']
            ];
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
        exit();
    }
} else {
    echo json_encode(['error' => 'ID de examen no proporcionado o inválido']);
    exit();
}

$conn->close();

echo json_encode($detalles);
