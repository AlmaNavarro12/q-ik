<?php
$fechainicio = $_GET['fechainicio'];
$fechafin = $_GET['fechafin'];
$clienteB = $_GET['idcliente'];
$datos = $_GET['datos'];

require_once '../com.sine.controlador/ControladorReportes.php';
require_once '../com.sine.controlador/ControladorConfiguracion.php';
require_once '../com.sine.modelo/Reportes.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('7');
foreach ($encabezado as $actual) {
    $titulo = $actual['tituloencabezado'];
    $colortitulo = $actual['colortitulo'];
    $rgbt = explode("#", $colortitulo);
    $pagina = $actual['pagina'];
    $correo = $actual['correo'];
    $telefono1 = $actual['telefono1'];
    $telefono2 = $actual['telefono2'];
    
    $colorcuadro = $actual['colorceltitulo'];
    $rgbc = explode("#", $colorcuadro);
    
    $colorhtabla = $actual['colorhtabla'];
    $rgbht = explode("#", $colorhtabla);
    $colortittabla = $actual['colortittabla'];
    $rgbtt = explode("#", $colortittabla);
    
    $imglogo = $actual['imglogo'];
    $logo = explode("/", $imglogo);
}

if ($correo != "") {
    $correo = "Email: $correo";
}
if ($telefono2 != "") {
    $telefono2 = " & $telefono2";
}

$controlador_reporte = new ControladorReportes();
$r = new Reportes();
$r->setFechainicio($fechainicio);
$r->setFechafin($fechafin);
$r->setIdcliente($clienteB);
$r->setDatos($datos);

$monedaC = 1;
if ($_GET['moneda'] != "") {
    $monedaC = $_GET['moneda'];
}

function getNombreMoneda($idMoneda)
{
    switch ($idMoneda) {
        case 1:
            return "MXN"; // Peso Mexicano
        case 2:
            return "USD"; // Dólar Estadounidense
        case 3:
            return "EUR"; // Euro
        default:
            return "Desconocido";
    }
}
$cmonedaT = getNombreMoneda($monedaC);

$divFI = explode("-", $fechainicio);
$mI = $controlador_reporte->translateMonth($divFI[1]);
$fechainicio2 = $divFI[2] . '/' . $mI . '/' . $divFI[0];

$divFF = explode("-", $fechafin);
$mF = $controlador_reporte->translateMonth($divFF[1]);
$fechafin2 = $divFF[2] . '/' . $mF . '/' . $divFF[0];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Reporte');

$sheet->mergeCells('A2:G2');
$sheet->getStyle('A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbc[1]);
$sheet->getStyle('A2')->getFont()->setSize(14);
$sheet->getStyle('A2')->getFont()->setBold(true);

$sheet->getStyle('A2')->getFont()->getColor()->setARGB($rgbt[1]);
$sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

$sheet->getRowDimension('2')->setRowHeight(75);
$sheet->setCellValue('A2', iconv("utf-8", "windows-1252",$titulo."\r".$pagina." ".$correo." ".$telefono1." ".$telefono2));
$sheet->getStyle('A2')->getAlignment()->setWrapText(true);
$sheet->mergeCells('A3:H3');
$sheet->getStyle('A3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbc[1]);
$sheet->getStyle('A3')->getFont()->getColor()->setARGB($rgbt[1]);
$sheet->getStyle('A3')->getFont()->setSize(14);
$sheet->getStyle('A3')->getFont()->setBold(true);
$sheet->setCellValue('A3', 'Pagos generados en el periodo : ' . $fechainicio2 . ' al ' . $fechafin2);

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath("../img/logo/$logo[0]");
$drawing->setCoordinates('H2');
$drawing->setHeight(100);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$sheet->getStyle('A5:J5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbht[1]);
$sheet->getStyle('A5:J5')->getFont()->getColor()->setARGB($rgbtt[1]);
$sheet->getStyle('A5:J5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A5:J5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A5:J5')->getAlignment()->setWrapText(true);
$sheet->getStyle('A5:J5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A5:J5')->getFont()->setSize(12);
$sheet->getStyle('A5:J5')->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(22);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(30);
$sheet->getColumnDimension('E')->setWidth(30);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(23);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(10);

$sheet->setCellValue('A5', 'Folio Pago');
$sheet->setCellValue('B5', 'Folio Fiscal');
$sheet->setCellValue('C5', 'Facturas Pagadas');
$sheet->setCellValue('D5', 'Emisor');
$sheet->setCellValue('E5', 'Nombre Cliente');
$sheet->setCellValue('F5', 'RFC');
$sheet->setCellValue('G5', 'Fecha de Pago');
$sheet->setCellValue('H5', 'Forma de Pago');
$sheet->setCellValue('I5', 'Total');
$sheet->setCellValue('J5', 'Moneda');

$totalpagado = 0;

$row = 6; //<--Variable que autoincrementa segun el numero de filas que genere la consulta, su valor es el numero de celda en que comenzara a mostrar los resultados
$datosreporte = $controlador_reporte->getReportePagos($r);
foreach ($datosreporte as $reporteactual) {
    $idpago = $reporteactual['idpago'];
    $folio = $reporteactual['letra'].$reporteactual['foliopago'];
    $uuid = $reporteactual['uuidpago'];
    $fechapago = $reporteactual['fechacreacion'];
    $horapago = $reporteactual['hora_creacion'];
    $emisor = $reporteactual['emisor'];
    $nombre_cliente = $reporteactual['cliente'];
    $rfc = $reporteactual['rfc'];
    $total = $reporteactual['totalpagado'];
    $tcambio = $reporteactual['tcambiodoc'];
    $monedaF = $reporteactual['idmonedadoc'];
    $cmoneda = $reporteactual['nombre_moneda'];
    $formapago = $reporteactual['nombre_forma_pago'];

    
    $divFP = explode("-", $fechapago);
    $mes = $controlador_reporte->translateMonth($divFP[1]);
    $fechapago = $divFP[2] . '/' . $mes . "/" . $divFP[0];
    $cfdis = $controlador_reporte->getCfdisPDF($idpago);

    $totalpagado += $controlador_reporte->totalDivisa($total, $tcambio, $monedaC, $monedaF);

    $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setWrapText(true);
    $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setSize(12);
    $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

    $sheet->setCellValue('A' . $row, $folio);
    $sheet->setCellValue('B' . $row, $uuid);
    $sheet->setCellValue('C' . $row, $cfdis);
    $sheet->setCellValue('D' . $row, $emisor);
    $sheet->setCellValue('E' . $row, $nombre_cliente);
    $sheet->setCellValue('F' . $row, $rfc);
    $sheet->setCellValue('G' . $row, $fechapago." ".$horapago);
    $sheet->setCellValue('H' . $row, $formapago);
    $sheet->setCellValue('I' . $row, number_format($total, 2, '.', ''));
    $sheet->setCellValue('J' . $row, iconv("utf-8", "windows-1252",$cmoneda));

    $row++; //<--Incremento de la variable row por cada fila generada
}

$sheet->getStyle('G' . $row . ':I' . ($row))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$sheet->getStyle('G' . $row . ':I' . ($row))->getAlignment()->setWrapText(true);
$sheet->getStyle('G' . $row . ':I' . ($row))->getFont()->setSize(12);
$sheet->getStyle('G' . $row . ':I' . ($row))->getFont()->setBold(true);
$sheet->getStyle('G' . $row . ':I' . ($row))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('I' . ($row))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$sheet->mergeCells('G'.$row.':H'.$row);

$sheet->setCellValue('G' . ($row), 'Total del Periodo en '.$cmonedaT);
$sheet->setCellValue('I' . ($row), number_format($totalpagado, 2, '.', ''));


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="reporte_' . $fechainicio . '_' . $fechafin . '.xlsx"');
$writer->save('php://output');
exit();
?>