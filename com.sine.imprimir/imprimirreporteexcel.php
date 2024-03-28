<?php

$fechainicio = $_GET['fechainicio'];
$fechafin = $_GET['fechafin'];
$idcliente = $_GET['idcliente'];
$estado = $_GET['estado'];
$datosfacturacion = $_GET['datos'];
$tipofactura = $_GET['tipo'];

$monedaC = 1;
if ($_GET['moneda'] != "") {
    $monedaC = $_GET['moneda'];
}

require_once '../com.sine.controlador/ControladorReportes.php';
require_once '../com.sine.controlador/ControladorConfiguracion.php';
require_once '../com.sine.modelo/Reportes.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('6');
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
$r->setIdcliente($idcliente);
$r->setEstado($estado);
$r->setDatos($datosfacturacion);
$r->setTipo($tipofactura);

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


$divFI = explode("-", $fechainicio);
$mI = $controlador_reporte->translateMonth($divFI[1]);
$fechainicio2 = $divFI[2] . '/' . $mI . '/' . $divFI[0];

$divFF = explode("-", $fechafin);
$mF = $controlador_reporte->translateMonth($divFF[1]);
$fechafin2 = $divFF[2] . '/' . $mF . '/' . $divFF[0];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Reporte');
$sheet->mergeCells('A2:H2');
$sheet->getStyle('A2:A3')->getFont()->setSize(14);
$sheet->getStyle('A2:A3')->getFont()->setBold(true);
$sheet->getStyle('A2:A3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbc[1]);
$sheet->getStyle('A2:A3')->getFont()->getColor()->setARGB($rgbt[1]);
$sheet->getStyle('A2:A3')->getAlignment()->setWrapText(true);
$sheet->getRowDimension('2')->setRowHeight(75);
$sheet->getStyle('A2:A3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->setCellValue('A2', $titulo . "\r" . $pagina . " " . $correo . " " . $telefono1 . " " . $telefono2);

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath("../img/logo/$logo[0]");
$drawing->setCoordinates('I2');
$drawing->setHeight(100);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$sheet->mergeCells('A3:I3');
$sheet->setCellValue('A3', 'Facturas generadas en el periodo : ' . $fechainicio2 . ' al ' . $fechafin2);

$sheet->getStyle('A5:V5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbht[1]);
$sheet->getStyle('A5:V5')->getFont()->getColor()->setARGB($rgbtt[1]);
$sheet->getStyle('A5:V5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A5:V5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A5:V5')->getAlignment()->setWrapText(true);
$sheet->getStyle('A5:V5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A5:V5')->getFont()->setSize(12);
$sheet->getStyle('A5:V5')->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(21);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(30);
$sheet->getColumnDimension('E')->setWidth(30);
$sheet->getColumnDimension('F')->setWidth(18);
$sheet->getColumnDimension('G')->setWidth(13);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(30);
$sheet->getColumnDimension('J')->setWidth(30);
$sheet->getColumnDimension('K')->setWidth(13);
$sheet->getColumnDimension('L')->setWidth(20);
$sheet->getColumnDimension('M')->setWidth(15);
$sheet->getColumnDimension('N')->setWidth(15);
$sheet->getColumnDimension('O')->setWidth(18);
$sheet->getColumnDimension('P')->setWidth(18);
$sheet->getColumnDimension('Q')->setWidth(18);
$sheet->getColumnDimension('R')->setWidth(18);
$sheet->getColumnDimension('S')->setWidth(18);
$sheet->getColumnDimension('T')->setWidth(10);
$sheet->getColumnDimension('U')->setWidth(10);
$sheet->getColumnDimension('V')->setWidth(10);


$sheet->setCellValue('A5', 'Folio Factura');
$sheet->setCellValue('B5', 'Folio Fiscal');
$sheet->setCellValue('C5', 'Fecha Creacion');
$sheet->setCellValue('D5', 'Emisor');
$sheet->setCellValue('E5', 'Cliente');
$sheet->setCellValue('F5', 'RFC');
$sheet->setCellValue('G5', 'Tipo');
$sheet->setCellValue('H5', 'Estado');
$sheet->setCellValue('I5', 'Primer Concepto');
$sheet->setCellValue('J5', 'Metodo pago');
$sheet->setCellValue('K5', 'Forma pago');
$sheet->setCellValue('L5', 'Observaciones');
$sheet->setCellValue('M5', 'Cantidad');
$sheet->setCellValue('N5', 'Clave fiscal');
$sheet->setCellValue('O5', 'Precio');
$sheet->setCellValue('P5', 'Importe');
$sheet->setCellValue('Q5', 'Subtotal');
$sheet->setCellValue('R5', 'Traslados');
$sheet->setCellValue('S5', 'Retenciones');
$sheet->setCellValue('T5', 'Descuentos');
$sheet->setCellValue('U5', 'Total');
$sheet->setCellValue('V5', 'Moneda');

$subtotalperiodo = 0;
$ivaperiodo = 0;
$retperiodo = 0;
$descperiodo = 0;
$totalPeriodo = 0;

$row = 6; //<--Variable que autoincrementa segun el numero de filas que genere la consulta, su valor es el numero de celda en que comenzara a mostrar los resultados
if ($tipofactura == "" || $tipofactura == '1' || $tipofactura == '3') {
    $datosreporte = $controlador_reporte->getReporteFactura($r);
    foreach ($datosreporte as $reporteactual) {
        $idfactura = $reporteactual['iddatos_factura'];
        $folio = $reporteactual['letra'] . $reporteactual['folio_interno_fac'];
        $fecha = $reporteactual['fecha_creacion'];
        $divf = explode("-", $fecha);
        $mes = $controlador_reporte->translateMonth($divf[1]);
        $fechaemision = "$divf[2]/$mes/$divf[0]";
        $emisor = $reporteactual['factura_rzsocial'];
        $nombre_cliente = $reporteactual['rzreceptor'];
        $rfc = $reporteactual['rfcreceptor'];
        $estado = $reporteactual['status_pago'];
        $subtotal = $reporteactual['subtotal'];
        $iva = $reporteactual['subtotaliva'];
        $ret = $reporteactual['subtotalret'];
        $totaldescuentos = $reporteactual['totaldescuentos'];
        $total = $reporteactual['totalfactura'];
        $uuid = $reporteactual['uuid'];
        $tcambio = $reporteactual['tcambio'];
        $monedaF = $reporteactual['id_moneda'];
         $divm = explode('-', $reporteactual['moneda']);
        $cmoneda = $divm[0];
        $tagfactura = $reporteactual['tagfactura'];
        //n
        $metodopago = $reporteactual['metodo_pago'];
        $formapago = $reporteactual['forma_pago'];

        switch ($estado) {
            case '1':
                $estadoF = "Pagada";
                $colorE = "5cb85c";
                break;
            case '2':
                $estadoF = "Pendiente";
                $colorE = "d9534f";
                break;
            case '3':
                $estadoF = "Cancelada";
                $colorE = "f0ad4e";
                break;
            case '4':
                $estadoF = "Pago Parcial";
                $colorE = "5bc0de";
                break;
            default:
                $estadoF = "";

                break;
        }

        $sheet->getStyle('A' . $row . ':V' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A' . $row . ':V' . $row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setSize(12);
        $sheet->getStyle('A' . $row . ':V' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $row . ':V' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB($rgbt[1]);
        $sheet->getStyle('H' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorE);
        $sheet->getStyle('M' . $row . ':S' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        $trasreg = 0;
        $subtotalperiodo += $subtotal;
        $diviva = explode("<impuesto>", $iva);
        foreach ($diviva as $ivan) {
            $traslados = $ivan;
            $divt = explode("-", $traslados);
            $trasreg += $controlador_reporte->totalDivisa($divt[0], $tcambio, $monedaC, $monedaF);
            $ivaperiodo += $controlador_reporte->totalDivisa($divt[0], $tcambio, $monedaC, $monedaF);
        }

        $retreg = 0;
        $divret = explode("<impuesto>", $ret);
        foreach ($divret as $retn) {
            $retenciones = $retn;
            $divr = explode("-", $retenciones);
            $retreg += $controlador_reporte->totalDivisa($divr[0], $tcambio, $monedaC, $monedaF);
            $retperiodo += $controlador_reporte->totalDivisa($divr[0], $tcambio, $monedaC, $monedaF);
        }

        $sheet->setCellValue('A' . $row, $folio);
        $sheet->setCellValue('B' . $row, $uuid);
        $sheet->setCellValue('C' . $row, $fechaemision);
        $sheet->setCellValue('D' . $row, $emisor);
        $sheet->setCellValue('E' . $row, $nombre_cliente);
        $sheet->setCellValue('F' . $row, $rfc);
        $sheet->setCellValue('G' . $row, "Factura");

        $sheet->setCellValue('H' . $row, $estadoF);
       

        $detallefactura = $controlador_reporte->getPrimerConcepto($tagfactura);
        foreach ($detallefactura as $primero) {
            $descripcion = $primero['factura_producto'];
            $cantidad = $primero['cantidad'];
            $clvfiscal = $primero['clave_fiscal'];
            $precio = $primero['precio'];
            $importe = $primero['totaldescuento'];
            $observacionesprod = $primero['observacionesproducto'];
            $obser = str_replace("<ent>", "\n", $observacionesprod);

            $sheet->setCellValue('I' . $row, $descripcion);
            $sheet->setCellValue('J' . $row, $metodopago);
            $sheet->setCellValue('K' . $row, $formapago);
            $sheet->setCellValue('L' . $row, $obser);
            $sheet->setCellValue('M' . $row, $cantidad);
            $sheet->setCellValue('N' . $row, $clvfiscal);
            $sheet->setCellValue('O' . $row, $precio);
            $sheet->setCellValue('P' . $row, $importe);
        }

        $sheet->setCellValue('Q' . $row, number_format($subtotal, 2, '.', ''));
        $sheet->setCellValue('R' . $row, number_format($trasreg, 2, '.', ''));
        $sheet->setCellValue('S' . $row, number_format($retreg, 2, '.', ''));
        $sheet->setCellValue('T' . $row, number_format($totaldescuentos, 2, '.', ''));
        $sheet->setCellValue('U' . $row, number_format($total, 2, '.', ''));
        $sheet->setCellValue('V' . $row, $cmoneda);


        $descperiodo += $controlador_reporte->totalDivisa($totaldescuentos, $tcambio, $monedaC, $monedaF);
        $totalPeriodo += $controlador_reporte->totalDivisa($total, $tcambio, $monedaC, $monedaF);

        $row++; //<--Incremento de la variable row por cada fila generada
    }
}

if ($tipofactura == '2' || $tipofactura == '3') {
    $rowT = $row - 1;

    $sheet->getColumnDimension('U')->setWidth(30);
    $sheet->getColumnDimension('V')->setWidth(30);
    $sheet->getColumnDimension('W')->setWidth(13);
    $sheet->getColumnDimension('X')->setWidth(15);
    $sheet->getColumnDimension('Y')->setWidth(30);
    $sheet->getColumnDimension('Z')->setWidth(30);
    $sheet->getColumnDimension('AA')->setWidth(20);
    $sheet->getColumnDimension('AB')->setWidth(15);
    $sheet->getColumnDimension('AC')->setWidth(18);
    $sheet->getColumnDimension('AD')->setWidth(18);
    $sheet->getColumnDimension('AE')->setWidth(30);
    $sheet->getColumnDimension('AF')->setWidth(30);
    $sheet->getColumnDimension('AG')->setWidth(20);
    $sheet->getColumnDimension('AH')->setWidth(15);
    $sheet->getColumnDimension('AI')->setWidth(18);
    $sheet->getColumnDimension('AJ')->setWidth(18);
    $sheet->getColumnDimension('AK')->setWidth(30);
    $sheet->getColumnDimension('AL')->setWidth(30);
    $sheet->getColumnDimension('AM')->setWidth(20);
    $sheet->getColumnDimension('AN')->setWidth(20);
    $sheet->getColumnDimension('AO')->setWidth(30);
    $sheet->getColumnDimension('AP')->setWidth(20);
    $sheet->getColumnDimension('AQ')->setWidth(30);
    $sheet->getColumnDimension('AR')->setWidth(13);
    $sheet->getColumnDimension('AS')->setWidth(13);
    $sheet->getColumnDimension('AT')->setWidth(30);
    $sheet->getColumnDimension('AU')->setWidth(25);
    $sheet->getColumnDimension('AV')->setWidth(20);
    $sheet->getColumnDimension('AW')->setWidth(25);
    $sheet->getColumnDimension('AX')->setWidth(13);
    $sheet->getColumnDimension('AY')->setWidth(25);
    $sheet->getColumnDimension('AZ')->setWidth(13);
    $sheet->getColumnDimension('BA')->setWidth(25);
    $sheet->getColumnDimension('BB')->setWidth(13);


    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbht[1]);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getFont()->getColor()->setARGB($rgbtt[1]);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getAlignment()->setWrapText(true);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getFont()->setSize(16);
    $sheet->getStyle('U' . ($rowT - 1) . ':BB' . ($rowT - 1))->getFont()->setBold(true);

    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($rgbht[1]);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getFont()->getColor()->setARGB($rgbtt[1]);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getAlignment()->setWrapText(true);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getFont()->setSize(12);
    $sheet->getStyle('U' . ($rowT) . ':BB' . ($rowT))->getFont()->setBold(true);

    $sheet->setCellValue('U' . ($rowT - 1), 'Datos de Carta Porte');
    $sheet->setCellValue('U' . $rowT, 'Primer Mercancia');
    $sheet->setCellValue('V' . $rowT, 'Clave Fiscal');
    $sheet->setCellValue('W' . $rowT, 'Cantidad');
    $sheet->setCellValue('X' . $rowT, 'Peso en Kg');
    $sheet->setCellValue('Y' . $rowT, 'Material Peligroso');
    $sheet->setCellValue('Z' . $rowT, 'Primer Origen');
    $sheet->setCellValue('AA' . $rowT, 'RFC');
    $sheet->setCellValue('AB' . $rowT, 'Tipo');
    $sheet->setCellValue('AC' . $rowT, 'Distancia');
    $sheet->setCellValue('AD' . $rowT, 'Fecha Salida');
    $sheet->setCellValue('AE' . $rowT, 'Direccion');
    $sheet->setCellValue('AF' . $rowT, 'Primer Destino');
    $sheet->setCellValue('AG' . $rowT, 'RFC');
    $sheet->setCellValue('AH' . $rowT, 'Tipo');
    $sheet->setCellValue('AI' . $rowT, 'Distancia');
    $sheet->setCellValue('AJ' . $rowT, 'Fecha Llegada');
    $sheet->setCellValue('AK' . $rowT, 'Direccion');
    $sheet->setCellValue('AL' . $rowT, 'Operador');
    $sheet->setCellValue('AM' . $rowT, 'RFC');
    $sheet->setCellValue('AN' . $rowT, 'Num Licencia');
    $sheet->setCellValue('AO' . $rowT, 'Direccion');
    $sheet->setCellValue('AP' . $rowT, 'Autotransporte Federal');
    $sheet->setCellValue('AQ' . $rowT, "Tipo Permiso");
    $sheet->setCellValue('AR' . $rowT, "Año Vehiculo");
    $sheet->setCellValue('AS' . $rowT, "Placa");
    $sheet->setCellValue('AT' . $rowT, "Tipo Transporte");
    $sheet->setCellValue('AU' . $rowT, "Nombre Aseguradora");
    $sheet->setCellValue('AV' . $rowT, "Num Poliza");
    $sheet->setCellValue('AW' . $rowT, "Tipo Remolque1");
    $sheet->setCellValue('AX' . $rowT, "Placa Remolque1");
    $sheet->setCellValue('AY' . $rowT, "Tipo Remolque2");
    $sheet->setCellValue('AZ' . $rowT, "Placa Remolque2");
    $sheet->setCellValue('BA' . $rowT, "Tipo Remolque3");
    $sheet->setCellValue('BB' . $rowT, "Placa Remolque3");

    $datosreporte = $controlador_reporte->getReporteCarta($r);
    foreach ($datosreporte as $reporteactual) {
        $folio = $reporteactual['letra'] . $reporteactual['foliocarta'];
        $fecha = $reporteactual['fecha_creacion'];
        $divf = explode("-", $fecha);
        $mes = $controlador_reporte->translateMonth($divf[1]);
        $fechaemision = "$divf[2]/$mes/$divf[0]";
        $emisor = $reporteactual['factura_rzsocial'];
        $nombre_cliente = $reporteactual['rzreceptor'];
        $rfc = $reporteactual['rfcreceptor'];
        $estado = $reporteactual['status_pago'];
        $subtotal = $reporteactual['subtotal'];
        $iva = $reporteactual['subtotaliva'];
        $ret = $reporteactual['subtotalret'];
        $totaldescuentos = $reporteactual['totaldescuentos'];
        $total = $reporteactual['totalfactura'];
        $uuid = $reporteactual['uuid'];
        $tcambio = $reporteactual['tcambio'];
        $monedaF = $reporteactual['id_moneda'];
        $cmoneda = $reporteactual['c_moneda'];
        $tag = $reporteactual['tagfactura'];

        $datoscarta = $controlador_reporte->getDatosCarta($tag);
        foreach ($datoscarta as $cartaactual) {
            $nombrevehiculo = $cartaactual['nombrevehiculo'];
            $numpermiso = $cartaactual['carta_numpermiso'];
            $tipopermiso = $cartaactual['carta_tipopermiso'];
            $conftransporte = $cartaactual['carta_conftransporte'];
            $anhomod = $cartaactual['carta_anhomod'];
            $placavehiculo = $cartaactual['carta_placa'];
            $segurocivil = $cartaactual['carta_segurocivil'];
            $polizacivil = $cartaactual['carta_polizaseguro'];
            $nmremolque1 = $cartaactual['carta_nmremolque1'];
            $tiporemolque1 = $cartaactual['carta_tiporemolque1'];
            $placaremolque1 = $cartaactual['carta_placaremolque1'];
            $nmremolque2 = $cartaactual['carta_nmremolque2'];
            $tiporemolque2 = $cartaactual['carta_tiporemolque2'];
            $placaremolque2 = $cartaactual['carta_placaremolque2'];
            $nmremolque3 = $cartaactual['carta_nmremolque3'];
            $tiporemolque3 = $cartaactual['carta_tiporemolque3'];
            $placaremolque3 = $cartaactual['carta_placaremolque3'];
        }
        
        switch ($estado) {
            case '1':
                $estadoF = "Pagada";
                $colorE = "5cb85c";
                break;
            case '2':
                $estadoF = "Pendiente";
                $colorE = "d9534f";
                break;
            case '3':
                $estadoF = "Cancelada";
                $colorE = "f0ad4e";
                break;
            case '4':
                $estadoF = "Pago Parcial";
                $colorE = "5bc0de";
                break;
            default:
                $estadoF = "";

                break;
        }

        $sheet->getStyle('A' . $row . ':BB' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A' . $row . ':BB' . $row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $row . ':BB' . $row)->getFont()->setSize(12);
        $sheet->getStyle('H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB($rgbtt[1]);
        $sheet->getStyle('H' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorE);
        $sheet->getStyle('M' . $row . ':S' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle('A' . $row . ':BB' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $row . ':BB' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);        

        $trasreg = 0;
        $diviva = explode("<impuesto>", $iva);
        foreach ($diviva as $ivan) {
            $traslados = $ivan;
            $divt = explode("-", $traslados);
            $trasreg += $controlador_reporte->totalDivisa($divt[0], $tcambio, $monedaC, $monedaF);
            $ivaperiodo += $controlador_reporte->totalDivisa($divt[0], $tcambio, $monedaC, $monedaF);
        }

        $retreg = 0;
        $divret = explode("<impuesto>", $ret);
        foreach ($divret as $retn) {
            $retenciones = $retn;
            $divr = explode("-", $retenciones);
            $retreg += $controlador_reporte->totalDivisa($divr[0], $tcambio, $monedaC, $monedaF);
            $retperiodo += $controlador_reporte->totalDivisa($divr[0], $tcambio, $monedaC, $monedaF);
        }

        $sheet->setCellValue('A' . $row, $folio);
        $sheet->setCellValue('B' . $row, $uuid);
        $sheet->setCellValue('C' . $row, $fechaemision);
        $sheet->setCellValue('D' . $row, $emisor);
        $sheet->setCellValue('E' . $row, $nombre_cliente);
        $sheet->setCellValue('F' . $row, $rfc);
        $sheet->setCellValue('G' . $row, "Carta");
        $sheet->setCellValue('H' . $row, $estadoF);

        $detallefactura = $controlador_reporte->getPrimerConceptoCarta($tag);
        foreach ($detallefactura as $primero) {
            $descripcion = $primero['carta_producto'];
            $cantidad = $primero['cantidad'];
            $clvfiscal = $primero['clave_fiscal'];
            $precio = $primero['precio'];
            $importe = $primero['totaldescuento'];
            $observacionesprod = $primero['observacionesproducto'];
            $obser = str_replace("<ent>", "\n", $observacionesprod);

            $sheet->setCellValue('I' . $row, $descripcion);
            $sheet->setCellValue('J' . $row, $obser);
            $sheet->setCellValue('K' . $row, $cantidad);
            $sheet->setCellValue('L' . $row, $clvfiscal);
            $sheet->setCellValue('M' . $row, $precio);
            $sheet->setCellValue('N' . $row, $importe);
        }

        $sheet->setCellValue('O' . $row, number_format($subtotal, 2, '.', ''));
        $sheet->setCellValue('P' . $row, number_format($trasreg, 2, '.', ''));
        $sheet->setCellValue('Q' . $row, number_format($retreg, 2, '.', ''));
        $sheet->setCellValue('R' . $row, number_format($totaldescuentos, 2, '.', ''));
        $sheet->setCellValue('S' . $row, number_format($total, 2, '.', ''));
        $sheet->setCellValue('T' . $row, $cmoneda);

        $mercancias = $controlador_reporte->getPrimerMercancia($tag);
        foreach ($mercancias as $mercactual) {
            $clavemercancia = $mercactual['clave_mercanca'];
            $descripcionmercancia = $mercactual['descripcion_mercancia'];
            $cantmercancia = $mercactual['cant_mercancia'];
            $unitmercancia = $mercactual['unidad_mercancia'];
            $peso = $mercactual['peso_mercancia'];
            $peligro = $mercactual['peligro'];
            $clvmaterial = $mercactual['clvmaterial'];
            $embalaje = $mercactual['embalaje'];


            $sheet->setCellValue('U' . $row, $descripcionmercancia);
            $sheet->setCellValue('V' . $row, $clavemercancia);
            $sheet->setCellValue('W' . $row, $cantmercancia);
            $sheet->setCellValue('X' . $row, $peso);
            $sheet->setCellValue('Y' . $row, $clvmaterial);
        }

        $origenes = $controlador_reporte->getPrimerUbicacion($tag, '1');
        foreach ($origenes as $oractual) {
            $idubicacion = $oractual['ubicacion_id'];
            $remitente = $oractual['ubicacion_nombre'];
            $rfcorigen = $oractual['ubicacion_rfc'];
            $ubicacion_tipo = $oractual['ubicacion_tipo'];
            $distancia = $oractual['ubicacion_distancia'];
            $fechallegada = $oractual['fechallegada'];
            $codporigen = $oractual['ubicacion_codpostal'];
            $estado = $oractual['estado'];
            $ordireccion = $oractual['direccion'];
            $idmunicipio = $oractual['idmunicipio'];
            $municipio = $controlador_reporte->getMunicipioAux($idmunicipio);
            
            $dirubicacion = "$ordireccion CP. $codporigen, $municipio $estado";

            $sheet->setCellValue('Z' . $row, $remitente);
            $sheet->setCellValue('AA' . $row, $rfcorigen);
            $sheet->setCellValue('AB' . $row, "Origen");
            $sheet->setCellValue('AC' . $row, $distancia);
            $sheet->setCellValue('AD' . $row, $fechallegada);
            $sheet->setCellValue('AE' . $row, $dirubicacion);
        }

        $destinos = $controlador_reporte->getPrimerUbicacion($tag, '2');
        foreach ($destinos as $desactual) {
            $idubicacion = $desactual['ubicacion_id'];
            $destino = $desactual['ubicacion_nombre'];
            $rfcdestino = $desactual['ubicacion_rfc'];
            $ubicacion_tipo = $desactual['ubicacion_tipo'];
            $distancia = $desactual['ubicacion_distancia'];
            $fechallegada = $desactual['fechallegada'];
            $codpdestino = $desactual['ubicacion_codpostal'];
            $estado = $desactual['estado'];
            $desdireccion = $desactual['direccion'];
            $idmunicipio = $desactual['idmunicipio'];
            $municipio = $controlador_reporte->getMunicipioAux($idmunicipio);
            
            $dirubicacion = "$desdireccion CP. $codpdestino, $municipio $estado";

            $sheet->setCellValue('AF' . $row, $destino);
            $sheet->setCellValue('AG' . $row, $rfcdestino);
            $sheet->setCellValue('AH' . $row, "Destino");
            $sheet->setCellValue('AI' . $row, $distancia);
            $sheet->setCellValue('AJ' . $row, $fechallegada);
            $sheet->setCellValue('AK' . $row, $dirubicacion);
        }

        $operadores = $controlador_reporte->getPrimerOperador($tag);
        foreach ($operadores as $opactual) {
            $idoperador = $opactual['operador_id'];
            $operador = $opactual['operador_nombre'];
            $rfcoperador = $opactual['operador_rfc'];
            $numlicencia = $opactual['operador_numlic'];
            $idestado = $opactual['operador_idestado'];
            $estado = $opactual['estado'];
            $calle = $opactual['operador_calle'];
            $cpoperador = $opactual['operador_cp'];

            if ($calle != "") {
                $calle = "$calle, ";
            }
            $diroperador = $calle . $cpoperador . " " . $estado;

            $sheet->setCellValue('AL' . $row, $operador);
            $sheet->setCellValue('AM' . $row, $rfcoperador);
            $sheet->setCellValue('AN' . $row, $numlicencia);
            $sheet->setCellValue('AO' . $row, $diroperador);
        }

        $sheet->setCellValue('AP' . ($row), $numpermiso);
        $sheet->setCellValue('AQ' . ($row), $tipopermiso);
        $sheet->setCellValue('AR' . ($row), $anhomod);
        $sheet->setCellValue('AS' . ($row), $placavehiculo);
        $sheet->setCellValue('AT' . ($row), $conftransporte);
        $sheet->setCellValue('AU' . ($row), $segurocivil);
        $sheet->setCellValue('AV' . ($row), $polizacivil);
        if ($placaremolque1 != "") {
            $sheet->setCellValue('AW' . ($row), $tiporemolque1);
            $sheet->setCellValue('AX' . ($row), $placaremolque1);
        }

        if ($placaremolque2 != "") {
            $sheet->setCellValue('AY' . ($row), $tiporemolque2);
            $sheet->setCellValue('AZ' . ($row), $placaremolque2);
        }

        if ($placaremolque3 != "") {
            $sheet->setCellValue('BA' . ($row), $tiporemolque3);
            $sheet->setCellValue('BB' . ($row), $placaremolque3);
        }

        $subtotalperiodo += $subtotal;

        $descperiodo += $controlador_reporte->totalDivisa($totaldescuentos, $tcambio, $monedaC, $monedaF);
        $totalPeriodo += $controlador_reporte->totalDivisa($total, $tcambio, $monedaC, $monedaF);

        $row++; //<--Incremento de la variable row por cada fila generada
    }
}

$row2 = $row;
$sheet->mergeCells('M' . $row . ':N' . $row2);
$sheet->setCellValue('M' . ($row), 'Totales en ' . $cmoneda . ':');
$sheet->setCellValue('O' . ($row), number_format($subtotalperiodo, 2, '.', ''));
$sheet->setCellValue('P' . ($row), number_format($ivaperiodo, 2, '.', ''));
$sheet->setCellValue('Q' . ($row), number_format($retperiodo, 2, '.', ''));
$sheet->setCellValue('R' . ($row), number_format($descperiodo, 2, '.', ''));
$sheet->setCellValue('S' . ($row), number_format($totalPeriodo, 2, '.', ''));

$sheet->getStyle('M' . $row . ':S' . $row2)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$sheet->getStyle('M' . $row . ':S' . $row2)->getAlignment()->setWrapText(true);
$sheet->getStyle('M' . $row . ':S' . $row2)->getFont()->setSize(12);
$sheet->getStyle('M' . $row . ':S' . $row2)->getFont()->setBold(true);
$sheet->getStyle('M' . $row . ':S' . $row2)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('M' . $row . ':S' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('O' . $row . ':S' . $row2)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

/*$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
$objWriter->save('../reporteExcel/reporte_' . $fechainicio . '_' . $fechafin . '.xlsx');*/
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="reporte_' . $fechainicio . '_' . $fechafin . '.xlsx"');
$writer->save('php://output');
exit();
?>