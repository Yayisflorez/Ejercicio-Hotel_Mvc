<?php
require_once 'model/Reserva.php';

class ReservasController {
    public static function obtenerReservasPorUsuario($id_user) {
        return Reserva::obtenerReservasPorUsuario($id_user);
    }

    public static function guardarReserva($data) {
        return Reserva::guardarReserva($data);
    }

    public static function actualizarReserva($data) {
        return Reserva::actualizarReserva($data);
    }

    public static function reservarHabitacion() {
        if (!isset($_SESSION['usuario']['id'])) {
            header('Location: index.php?action=getFormLoginUser');
            exit;
        }
        $id_user = $_SESSION['usuario']['id'];
        $id_habitacion = $_POST['id_habitacion'];
        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_final = $_POST['fecha_final'] ?? date('Y-m-d', strtotime('+1 day'));
        $num_personas = $_POST['num_personas'] ?? 1;
        $estado = 1;
        $precio = $_POST['precio'] ?? 0;
        $id_metodo_pago = $_POST['id_metodo_pago'] ?? 1;
        $data = [
            'id_user' => $id_user,
            'id_habitacion' => $id_habitacion,
            'fecha_inicio' => $fecha_inicio,
            'fecha_final' => $fecha_final,
            'num_personas' => $num_personas,
            'estado' => $estado,
            'precio' => $precio,
            'id_metodo_pago' => $id_metodo_pago
        ];
        $ok = self::guardarReserva($data);
        if ($ok) {
            $_SESSION['success'] = 'Reserva realizada correctamente';
        } else {
            $_SESSION['errors']['reserva'] = 'Error al guardar la reserva';
        }
        header('Location: index.php?action=getFormInicioExitosoReservas');
        exit;
    }

    public static function actualizarReservaAjax() {
        if (!isset($_SESSION['usuario']['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
            exit;
        }
        $id = $_POST['reserva_id'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $personas = $_POST['personas'];
        $id_habitacion = $_POST['id_habitacion'];
        $precio = $_POST['precio'];
        $pago_name = $_POST['pago_edit'] ?? 'nequi';

        // Mapeo simple de nombres a IDs (ajustar según tu tabla metodos_pago)
        $pago_map = ['nequi' => 1, 'daviplata' => 2, 'bancolombia' => 3];
        $id_metodo_pago = $pago_map[$pago_name] ?? 1;

        $data = [
            'id' => $id,
            'id_habitacion' => $id_habitacion,
            'precio' => $precio,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'personas' => $personas,
            'id_metodo_pago' => $id_metodo_pago
        ];
        $ok = self::actualizarReserva($data);
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        exit;
    }

    public static function eliminarReservaAjax() {
        if (!isset($_SESSION['usuario']['id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        $id = $_POST['id_reserva'];
        $ok = Reserva::eliminarReserva($id);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Reserva eliminada' : 'Error al eliminar']);
        exit;
    }

    public static function descargarPdfReserva() {
        if (!isset($_SESSION['usuario']['id'])) {
            header('Location: index.php?action=getFormLoginUser');
            exit;
        }

        if (!isset($_GET['id'])) {
            echo "ID de reserva no proporcionado.";
            exit;
        }

        $id_reserva = $_GET['id'];
        $id_user = $_SESSION['usuario']['id'];
        
        $reservas = self::obtenerReservasPorUsuario($id_user);
        $reserva = null;
        foreach ($reservas as $r) {
            if ($r['id'] == $id_reserva) {
                $reserva = $r;
                break;
            }
        }

        if (!$reserva) {
            echo "Reserva no encontrada o no pertenece al usuario.";
            exit;
        }

        require_once 'LIB/fpdf.php';

        $pdf = new FPDF();
        // Deshabilitar salto de página automático para forzar todo en una página
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        
        // Colors
        // Navy = 13, 27, 42
        // Gold = 201, 169, 110
        // White = 255, 255, 255

        // Header Background (Navy)
        $pdf->SetFillColor(13, 27, 42);
        $pdf->Rect(0, 0, 210, 40, 'F');

        // Hotel Name (Gold)
        $pdf->SetTextColor(201, 169, 110);
        $pdf->SetFont('Times', 'B', 24);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, utf8_decode('HOTEL VIÑA DEL MAR'), 0, 1, 'C');
        
        // Subtitle (White)
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Times', 'I', 14);
        $pdf->Cell(0, 10, utf8_decode('Comprobante de Reserva Oficial'), 0, 1, 'C');
        
        $pdf->Ln(15);

        // Reset Text Color to Navy for content
        $pdf->SetTextColor(13, 27, 42);
        
        // Block 1: Detalles de la Reserva
        $pdf->SetFont('Times', 'B', 16);
        $pdf->SetDrawColor(201, 169, 110); // Gold line
        $pdf->SetLineWidth(0.5);
        $pdf->Cell(0, 10, utf8_decode('Detalles de la Reserva'), 'B', 1, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(50, 8, utf8_decode('Código de Reserva:'), 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, $reserva['id'], 0, 1);
        
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(50, 8, utf8_decode('Fecha de Creación:'), 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, $reserva['created_at'], 0, 1);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(50, 8, utf8_decode('Huéspedes:'), 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, $reserva['personas'] . ' persona(s)', 0, 1);
        
        $pdf->Ln(5);
        
        // Block 2: Información de Estadía
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

        // Block 3: Pago
        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('Información de Pago'), 'B', 1, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(50, 8, utf8_decode('Método de Pago:'), 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, utf8_decode(ucfirst($reserva['pago'])), 0, 1);

        // Total Box
        $pdf->Ln(5);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetDrawColor(201, 169, 110);
        $pdf->SetLineWidth(1);
        $pdf->Rect(10, $pdf->GetY(), 190, 20, 'DF');

        $pdf->SetY($pdf->GetY() + 5);
        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(95, 10, utf8_decode('TOTAL A PAGAR: '), 0, 0, 'R');
        
        $pdf->SetTextColor(201, 169, 110); // Gold for total
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(95, 10, '$' . number_format($reserva['total'], 0, ',', '.'), 0, 1, 'L');

        // Footer
        $pdf->SetY(-35); // Posicionar a 35mm del final de la primera (y única) página
        $pdf->SetTextColor(13, 27, 42);
        $pdf->SetFont('Times', 'I', 12);
        $pdf->Cell(0, 10, utf8_decode('¡Gracias por elegir la elegancia y confort de Hotel Viña del Mar!'), 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, utf8_decode('reservas@hotelvinadelmar.cl  |  +56 32 000 0000'), 0, 1, 'C');
        $pdf->Cell(0, 6, utf8_decode('Av. San Martín 199, Viña del Mar, Chile'), 0, 1, 'C');

        $pdf->Output('D', 'Reserva-' . $reserva['id'] . '.pdf');
        exit;
    }
}
?>