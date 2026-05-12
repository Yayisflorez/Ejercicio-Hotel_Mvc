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

    public static function obtenerPorId($id) {
        $conexion = new Conexion();
        $conexion->conectar();
        $sql = "SELECT h.*, c.nombre AS categoria_nombre 
                FROM habitaciones h 
                JOIN categorias c ON h.id_categoria = c.id 
                WHERE h.id = ?";
        $stmt = $conexion->getConexion()->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $habitacion = $result->fetch_assoc();
        $stmt->close();
        $conexion->cerrar();
        return $habitacion;
    }
}
/* ajax */
if (isset($_GET['action']) && $_GET['action'] == 'getHabitacionesByCategoria') {

    header('Content-Type: application/json; charset=utf-8');

    require_once "conexion.php";

    $categoria = $_GET['categoria'] ?? '';

    $conn = new conexion();
    $conn->conectar();
    $conn = $conn->getConexion();

    $habitaciones = [];

    try {

        $sql = "SELECT 
                    h.id,
                    h.num_habitacion AS nombre,
                    h.descripcion,
                    h.precio,
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