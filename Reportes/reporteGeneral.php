<?php
if (!isset($reservas) || !isset($usuario)) {
    die("Error: Faltan datos para generar el reporte.");
}

require_once 'LIB/SPREADSHEET/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Mis Reservas');

// Colores Corporativos
$navy = '0D1B2A';
$gold = 'C9A96E';
$darkText = '000000';
$whiteText = 'FFFFFF';

// --- 1. ENCABEZADOS PRINCIPALES ---
// Fila 1: Título Principal
$sheet->setCellValue('A1', '✦ HOTEL VIÑA DEL MAR');
$sheet->mergeCells('A1:H1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 18,
        'color' => ['argb' => 'FF' . $gold],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF' . $navy],
    ],
]);
$sheet->getRowDimension(1)->setRowHeight(35);

// Fila 2: Subtítulo
$sheet->setCellValue('A2', 'REPORTE GENERAL DE RESERVAS');
$sheet->mergeCells('A2:H2');
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['argb' => 'FF' . $darkText],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF' . $gold],
    ],
]);
$sheet->getRowDimension(2)->setRowHeight(22);

// --- 2. SECCIÓN: INFORMACIÓN DEL CLIENTE ---
// Fila 4: Título Sección Cliente
$sheet->setCellValue('A4', 'Información del Cliente');
$sheet->mergeCells('A4:H4');
$sheet->getStyle('A4')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['argb' => 'FF' . $navy],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);
$sheet->getRowDimension(4)->setRowHeight(25);

// Fila 5: Encabezados Info Cliente
$infoHeaders = [
    'A' => 'Usuario',
    'B' => 'Correo electronico',
    'C' => 'Tipo de documeto',
    'D' => 'N° de documeto',
    'E' => 'Telefono',
    'F' => 'Fecha de generación',
    'G' => 'Hora',
    'H' => 'Total de reservas'
];

foreach ($infoHeaders as $col => $text) {
    $sheet->setCellValue($col . '5', $text);
}

$sheet->getStyle('A5:H5')->applyFromArray([
    'font' => ['bold' => true, 'size' => 10],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FFDDDDDD'],
        ],
    ]
]);

// Fila 6: Valores Info Cliente
date_default_timezone_set('America/Bogota');
$sheet->setCellValue('A6', $usuario['nombre'] . ' ' . $usuario['apellido']);
$sheet->setCellValue('B6', $usuario['email']);
$sheet->setCellValue('C6', $usuario['tipo_documento']);
$sheet->setCellValue('D6', $usuario['documento']);
$sheet->setCellValue('E6', $usuario['telefono']);
$sheet->setCellValue('F6', date('d/m/Y'));
$sheet->setCellValue('G6', date('H:i:s'));
$sheet->setCellValue('H6', count($reservas));

$sheet->getStyle('A6:H6')->applyFromArray([
    'font' => ['size' => 10],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FFDDDDDD'],
        ],
    ]
]);

// --- 3. SECCIÓN: DETALLE DE RESERVAS ---
// Fila 8: Título Sección Reservas
$sheet->setCellValue('A8', 'Detalle de Reservas');
$sheet->mergeCells('A8:H8');
$sheet->getStyle('A8')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['argb' => 'FF' . $navy],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);
$sheet->getRowDimension(8)->setRowHeight(25);

// Fila 9: Encabezados de Tabla
$tableHeaders = [
    'A' => 'Habitación',
    'B' => 'Tipo',
    'C' => 'Entrada',
    'D' => 'Salida',
    'E' => 'Noches',
    'F' => 'Personas',
    'G' => 'Método de Pago',
    'H' => 'Total'
];

foreach ($tableHeaders as $col => $text) {
    $sheet->setCellValue($col . '9', $text);
}

$sheet->getStyle('A9:H9')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FF' . $gold],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF' . $navy],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ]
]);

// Filas de Datos
$row = 10;
$totalGeneral = 0;

if (!empty($reservas)) {
    foreach ($reservas as $r) {
        $nombreHabitacion = stripos($r['habitacion'], 'Habitación') !== false 
            ? $r['habitacion'] 
            : $r['habitacion'];
        $sheet->setCellValue('A' . $row, $nombreHabitacion);
        
        $sheet->setCellValue('B' . $row, $r['tipo']);
        $sheet->setCellValue('C' . $row, $r['entrada']);
        $sheet->setCellValue('D' . $row, $r['salida']);
        $sheet->setCellValue('E' . $row, $r['noches']);
        $sheet->setCellValue('F' . $row, $r['personas']);
        
        $metodoPago = isset($r['pago']) ? ucfirst($r['pago']) : 'N/A';
        $sheet->setCellValue('G' . $row, $metodoPago);
        
        $totalGeneral += $r['total'];
        $sheet->setCellValue('H' . $row, '$' . number_format($r['total'], 2, ',', '.'));
        
        // Formato para la fila
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ]
        ]);
        $row++;
    }
} else {
    $sheet->setCellValue('A' . $row, 'No hay reservas registradas.');
    $sheet->mergeCells('A' . $row . ':H' . $row);
    $row++;
}

// Fila de TOTAL GENERAL
$sheet->setCellValue('G' . $row, 'TOTAL GENERAL:');
$sheet->setCellValue('H' . $row, '$' . number_format($totalGeneral, 2, ',', '.'));

$sheet->getStyle('G' . $row . ':H' . $row)->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FF' . $darkText],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF' . $gold],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ]
]);
$sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// --- 4. PIE DE PÁGINA ---
$row += 2;
$sheet->setCellValue('A' . $row, '¡Gracias por elegir la elegancia y confort de Hotel Viña del Mar!');
$sheet->mergeCells('A' . $row . ':H' . $row);
$sheet->getStyle('A' . $row)->applyFromArray([
    'font' => [
        'size' => 11,
        'bold' => true,
        'color' => ['argb' => 'FF' . $darkText],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF' . $gold],
    ],
]);

// --- 5. AJUSTAR ANCHOS DE COLUMNA ---
$sheet->getColumnDimension('A')->setWidth(25);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(18);
$sheet->getColumnDimension('H')->setWidth(22);

// --- 6. SALIDA ---
if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Reservas_General.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
