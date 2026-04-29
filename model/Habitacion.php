<?php
require_once 'conexion.php';

class Habitacion {
    public static function obtenerHabitaciones() {
        $conexion = new Conexion();
        $conexion->conectar();
        $sql = "SELECT h.*, c.nombre AS categoria_nombre FROM habitaciones h JOIN categorias c ON h.id_categoria = c.id";
        $result = $conexion->query($sql);
        $habitaciones = [];
        while ($row = $result->fetch_assoc()) {
            $habitaciones[] = $row;
        }
        $conexion->cerrar();
        return $habitaciones;
    }
}
/* ajax */
if (isset($_GET['action']) && $_GET['action'] == 'getHabitacionesByCategoria') {

    header('Content-Type: application/json; charset=utf-8');

    require_once "conexion.php";

    $categoria = $_GET['categoria'] ?? '';

    $conn = Conexion::conectar();

    $habitaciones = [];

    try {

        $sql = "SELECT 
                    h.id,
                    h.nombre,
                    h.descripcion,
                    h.precio,
                    h.img,
                    h.max_personas
                FROM habitaciones h
                INNER JOIN categorias c ON h.id_categoria = c.id
                WHERE c.nombre = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $categoria);

        $stmt->execute();

        $resultado = $stmt->get_result();

        while ($fila = $resultado->fetch_assoc()) {
            $habitaciones[] = $fila;
        }

        echo json_encode([
            'ok' => true,
            'data' => $habitaciones
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'ok' => false,
            'message' => 'Error al consultar habitaciones',
            'data' => []
        ]);
    }

    exit;
}

?>