<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/plugion/fpdf/fpdf.php';

if (!isset($_SESSION['user'])) {
    redirect('auth-2-sign-in.php');
}

$eventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;
if ($eventId <= 0) {
    redirect('eventos-autoridades.php');
}

$stmt = db()->prepare('SELECT titulo, fecha_inicio, fecha_fin, ubicacion, tipo FROM events WHERE id = ?');
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    redirect('eventos-autoridades.php');
}

$stmt = db()->prepare(
    'SELECT a.nombre, a.tipo, a.correo, a.telefono
     FROM authorities a
     INNER JOIN event_authorities ea ON ea.authority_id = a.id
     WHERE ea.event_id = ?
     ORDER BY a.nombre'
);
$stmt->execute([$eventId]);
$authorities = $stmt->fetchAll();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 16);
$pdf->Cell(0, 8, 'Listado de autoridades invitadas', 0, 1);

$pdf->SetFont('Helvetica', '', 11);
$pdf->Cell(0, 6, 'Evento: ' . $event['titulo'], 0, 1);
$pdf->Cell(0, 6, 'Fecha: ' . $event['fecha_inicio'] . ' - ' . $event['fecha_fin'], 0, 1);
$pdf->Cell(0, 6, 'Ubicacion: ' . $event['ubicacion'] . ' · ' . $event['tipo'], 0, 1);
$pdf->Ln(4);

$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(90, 7, 'Autoridad', 0, 0);
$pdf->Cell(50, 7, 'Tipo', 0, 0);
$pdf->Cell(0, 7, 'Contacto', 0, 1);
$pdf->Ln(1);

$pdf->SetFont('Helvetica', '', 10);
if (empty($authorities)) {
    $pdf->Cell(0, 6, 'No hay autoridades asignadas para este evento.', 0, 1);
} else {
    foreach ($authorities as $authority) {
        $contacto = trim(($authority['correo'] ?? '') . ' ' . ($authority['telefono'] ?? ''));
        $pdf->Cell(90, 6, $authority['nombre'], 0, 0);
        $pdf->Cell(50, 6, $authority['tipo'], 0, 0);
        $pdf->Cell(0, 6, $contacto !== '' ? $contacto : '-', 0, 1);
    }
}

$filename = 'autoridades-evento-' . $eventId . '.pdf';
$pdf->Output('I', $filename);
