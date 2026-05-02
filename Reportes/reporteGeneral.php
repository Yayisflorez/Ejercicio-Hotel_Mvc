<?php
/**
 * reporteGeneral.php
 * Genera un reporte en Excel de todas las reservas del usuario actual.
 * Utiliza la librería SimpleXLSXGen ubicada en LIB.
 */

session_start();

// Validar sesión
if (!isset($_SESSION['usuario']['id'])) {
    die("Error: Debes iniciar sesión para generar el reporte.");
}

require_once '../model/Reserva.php';
require_once '../LIB/SimpleXLSXGen.php';
require_once '../model/conexion.php';

use Shuchkin\SimpleXLSXGen;

$id_user = $_SESSION['usuario']['id'];

// Obtener datos detallados del usuario
$con = new conexion();
$con->conectar();
$sql_u = "SELECT u.*, t.tipo AS tipo_documento_nombre 
          FROM usuarios u 
          LEFT JOIN tipos_documento t ON u.tipo_documento_id = t.id 
          WHERE u.id = $id_user";
$res_u = $con->query($sql_u);
$usuario = $res_u->fetch_assoc();
$con->cerrar();

if (!$usuario) {
    die("Error al obtener datos del usuario.");
}

$nombre_completo = $usuario['nombre'] . ' ' . $usuario['apellido'];

// Obtener las reservas del usuario
$reservas = Reserva::obtenerReservasPorUsuario($id_user);

if (empty($reservas)) {
    die("No tienes reservas registradas para generar el reporte.");
}

// Estilos para SimpleXLSXGen
// Navy: #0D1B2A | Gold: #C9A96E | White: #FFFFFF
$style_title = '<style bgcolor="#0D1B2A" color="#C9A96E" font-size="16"><b><center>';
$style_subtitle = '<style bgcolor="#C9A96E" color="#0D1B2A" font-size="12"><b><center>';
$style_info_header = '<b><center>';
$style_info_val = '<center>';
$style_table_header = '<style bgcolor="#0D1B2A" color="#C9A96E" font-size="12"><b><center>';
$style_total_label = '<style bgcolor="#C9A96E" color="#0D1B2A"><b><right>';
$style_total_val = '<style bgcolor="#C9A96E" color="#0D1B2A"><b><right>';
$style_footer = '<style bgcolor="#C9A96E" color="#0D1B2A"><b><center>';
$cell_money = '<right>';

// Preparar los datos para el Excel (8 columnas: A-H)
$data = [
    // Fila 1: Título Principal
    [
        $style_title . 'HOTEL VIÑA DEL MAR - REPORTE GENERAL DE RESERVAS</center></b>', 
        null, null, null, null, null, null, null
    ],
    // Fila 2: Subtítulo
    [
        $style_subtitle . 'Área Personal - Historial de Estadía</center></b>',
        null, null, null, null, null, null, null
    ],
    // Fila 3: Encabezados de información del usuario
    [
        $style_info_header . 'Usuario</center></b>',
        $style_info_header . 'Correo electronico</center></b>',
        $style_info_header . 'Tipo de documeto</center></b>',
        $style_info_header . 'N° de documeto</center></b>',
        $style_info_header . 'Telefono</center></b>',
        $style_info_header . 'Fecha de generación</center></b>',
        $style_info_header . 'Hora</center></b>',
        $style_info_header . 'Total de reservas</center></b>'
    ],
    // Fila 4: Valores de información del usuario
    [
        $style_info_val . $nombre_completo . '</center>',
        $style_info_val . $usuario['email'] . '</center>',
        $style_info_val . $usuario['tipo_documento_nombre'] . '</center>',
        $style_info_val . $usuario['documento'] . '</center>',
        $style_info_val . $usuario['telefono'] . '</center>',
        $style_info_val . date('d/m/Y') . '</center>',
        $style_info_val . date('H:i:s') . '</center>',
        $style_info_val . count($reservas) . '</center>'
    ],
    // Fila 5: Espaciador
    [null, null, null, null, null, null, null, null],
    // Fila 6: Encabezados de la tabla (Sin ID Reserva)
    [
        $style_table_header . 'Habitación</center></b>',
        $style_table_header . 'Tipo</center></b>',
        $style_table_header . 'Entrada</center></b>',
        $style_table_header . 'Salida</center></b>',
        $style_table_header . 'Noches</center></b>',
        $style_table_header . 'Personas</center></b>',
        $style_table_header . 'Método de Pago</center></b>',
        $style_table_header . 'Total</center></b>'
    ]
];

$total_general = 0;

foreach ($reservas as $r) {
    $data[] = [
        'Habitación ' . $r['habitacion'],
        $r['tipo'],
        $style_info_val . $r['entrada'] . '</center>',
        $style_info_val . $r['salida'] . '</center>',
        $style_info_val . $r['noches'] . '</center>',
        $style_info_val . $r['personas'] . '</center>',
        $r['pago'],
        $cell_money . '$' . number_format($r['total'], 0, ',', '.') . '</right>'
    ];
    $total_general += $r['total'];
}

// Fila de Total General
$data[] = [
    null, null, null, null, null, null,
    $style_total_label . 'TOTAL GENERAL:</right></b>',
    $style_total_val . '$' . number_format($total_general, 0, ',', '.') . '</right></b>'
];

// Fila de Espacio
$data[] = [null, null, null, null, null, null, null, null];

// Fila de Pie de página
$data[] = [
    $style_footer . '¡Gracias por elegir la elegancia y confort de Hotel Viña del Mar!</center></b>',
    null, null, null, null, null, null, null
];

// Generar y descargar el archivo
$xlsx = SimpleXLSXGen::fromArray($data);

// Fusiones de celdas
$xlsx->mergeCells('A1:H1'); // Título
$xlsx->mergeCells('A2:H2'); // Subtítulo
$last_row = count($data);
$xlsx->mergeCells('A' . $last_row . ':H' . $last_row); // Footer

// Ajustar anchos de columna
$xlsx->setColWidth(1, 20); // Habitación
$xlsx->setColWidth(2, 15); // Tipo
$xlsx->setColWidth(3, 20); // Entrada
$xlsx->setColWidth(4, 20); // Salida
$xlsx->setColWidth(5, 10); // Noches
$xlsx->setColWidth(6, 10); // Personas
$xlsx->setColWidth(7, 20); // Método de Pago
$xlsx->setColWidth(8, 20); // Total

$xlsx->downloadAs('Reporte_General_Reservas_' . date('Ymd_His') . '.xlsx');
exit;
