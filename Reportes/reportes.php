<?php
if (!isset($reserva)) {
    die("Error: No se ha proporcionado la información de la reserva para el reporte.");
}

require_once 'LIB/fpdf.php';

$pdf = new FPDF();
// Establecer el título del documento (cambia el nombre de la pestaña en el navegador)
$pdf->SetTitle(utf8_decode('Reporte Reserva #' . $reserva['id']));

// Deshabilitar salto de página automático para asegurar que todo quepa en una sola hoja (según diseño previo)
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// Colores Corporativos
// Navy: 13, 27, 42 | Gold: 201, 169, 110 | White: 255, 255, 255

// Encabezado (Fondo Azul Marino)
$pdf->SetFillColor(13, 27, 42);
$pdf->Rect(0, 0, 210, 40, 'F');

// Nombre del Hotel (Dorado)
$pdf->SetTextColor(201, 169, 110);
$pdf->SetFont('Times', 'B', 24);
$pdf->SetY(12);
$pdf->Cell(0, 10, utf8_decode('HOTEL VIÑA DEL MAR'), 0, 1, 'C');

// Subtítulo (Blanco)
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Times', 'I', 14);
$pdf->Cell(0, 10, utf8_decode('Comprobante de Reserva Oficial'), 0, 1, 'C');

$pdf->Ln(15);

// Resetear color de texto a Azul Marino para el contenido
$pdf->SetTextColor(13, 27, 42);

// Bloque 0: Información del Cliente
$pdf->SetFont('Times', 'B', 16);
$pdf->SetDrawColor(201, 169, 110); // Línea dorada
$pdf->SetLineWidth(0.5);
$pdf->Cell(0, 10, utf8_decode('Información del Cliente'), 'B', 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Nombre Completo:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode($usuario['nombre'] . ' ' . $usuario['apellido']), 0, 1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode($usuario['tipo_documento'] . ':'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $usuario['documento'], 0, 1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Correo Electrónico:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode($usuario['email']), 0, 1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Celular:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $usuario['telefono'], 0, 1);

$pdf->Ln(5);

// Bloque 1: Detalles de la Reserva
$pdf->SetFont('Times', 'B', 16);
$pdf->SetDrawColor(201, 169, 110); // Línea dorada
$pdf->SetLineWidth(0.5);
$pdf->Cell(0, 10, utf8_decode('Detalles de la Reserva'), 'B', 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Fecha de Creación:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $reserva['created_at'], 0, 1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Huéspedes:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $reserva['personas'] . ' persona(s)', 0, 1);

$pdf->Ln(5);

// Bloque 2: Información de Estadía
$pdf->SetFont('Times', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Información de Estadía'), 'B', 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Habitación:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode($reserva['habitacion'] . ' (' . $reserva['tipo'] . ')'), 0, 1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Descripción:'), 0, 0);
$pdf->SetFont('Arial', 'I', 11);
$pdf->MultiCell(0, 8, utf8_decode($reserva['descripcion'] ?? 'Sin descripción.'));

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Fecha de Entrada:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $reserva['entrada'], 0, 1);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Fecha de Salida:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $reserva['salida'] . ' (' . $reserva['noches'] . ' noches)', 0, 1);

$pdf->Ln(5);

// Bloque 3: Información de Pago
$pdf->SetFont('Times', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Información de Pago'), 'B', 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(50, 8, utf8_decode('Método de Pago:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode(ucfirst($reserva['pago'])), 0, 1);

// Caja de Total
$pdf->Ln(5);
$pdf->SetFillColor(245, 245, 245);
$pdf->SetDrawColor(201, 169, 110);
$pdf->SetLineWidth(1);
$pdf->Rect(10, $pdf->GetY(), 190, 20, 'DF');

$pdf->SetY($pdf->GetY() + 5);
$pdf->SetFont('Times', 'B', 16);
$pdf->Cell(95, 10, utf8_decode('TOTAL A PAGAR: '), 0, 0, 'R');

$pdf->SetTextColor(201, 169, 110); // Dorado para el total
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(95, 10, '$' . number_format($reserva['total'], 0, ',', '.'), 0, 1, 'L');

// Pie de página
$pdf->SetY(-35); 
$pdf->SetTextColor(13, 27, 42);
$pdf->SetFont('Times', 'I', 12);
$pdf->Cell(0, 10, utf8_decode('¡Gracias por elegir la elegancia y confort de Hotel Viña del Mar!'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, utf8_decode('reservas@hotelvinadelmar.cl  |  +56 32 000 0000'), 0, 1, 'C');
$pdf->Cell(0, 6, utf8_decode('Av. San Martín 199, Viña del Mar, Chile'), 0, 1, 'C');

// Salida del PDF (I para mostrar en el navegador)
$pdf->Output('I', 'Reserva-' . $reserva['id'] . '.pdf');
exit;
