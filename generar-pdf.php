require('fpdf/fpdf.php'); // Asegúrate de tener la librería FPDF correctamente instalada y configurada

$servername = "localhost";
$username = "cetvirtu_app_web_dev";
$password = "A0uEouZnFf*@";
$dbname = "cetvirtu_appweb_kine";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$examen_id = isset($_GET['examen_id']) ? intval($_GET['examen_id']) : 0;
$detalles = [];

if ($examen_id) {
    $sql = "SELECT r.examen, r.fecha_carga, r.resultado, r.estado 
            FROM resultado_examen r 
            WHERE r.examen_id = $examen_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
    }
}

$conn->close();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Informe de Examen', 0, 1, 'C');
$pdf->Ln(10);

if (!empty($detalles)) {
    $pdf->SetFont('Arial', '', 12);
    foreach ($detalles as $detalle) {
        $pdf->Cell(0, 10, 'Examen: ' . $detalle['examen'], 0, 1);
        $pdf->Cell(0, 10, 'Fecha de Carga: ' . date('d-m-Y H:i:s', strtotime($detalle['fecha_carga'])), 0, 1);
        $pdf->Cell(0, 10, 'Resultado: ' . $detalle['resultado'], 0, 1);
        $pdf->Cell(0, 10, 'Estado: ' . $detalle['estado'], 0, 1);
        $pdf->Ln(10);
    }
} else {
    $pdf->Cell(0, 10, 'No se encontraron detalles para este informe.', 0, 1);
}

$pdf->Output('D', 'informe_examen_' . $examen_id . '.pdf');
