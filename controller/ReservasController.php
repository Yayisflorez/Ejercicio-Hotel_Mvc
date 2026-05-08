<?php
require_once 'model/Reserva.php';
require_once 'model/Usuario.php';

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

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($id_user);

        include 'Reportes/reportes.php';
        exit;
    }

    public static function descargarExcelReservas() {
        if (!isset($_SESSION['usuario']['id'])) {
            header('Location: index.php?action=getFormLoginUser');
            exit;
        }

        $id_user = $_SESSION['usuario']['id'];
        
        $reservas = self::obtenerReservasPorUsuario($id_user);

        if (empty($reservas)) {
            echo "No hay reservas para generar el reporte.";
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($id_user);

        include 'Reportes/reporteGeneral.php';
        exit;
    }
}
?>