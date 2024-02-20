<?php

if (isset($_GET['f'])) {
    require_once '../com.sine.controlador/ControladorFactura.php';
    setlocale(LC_MONETARY, 'es_MX.UTF-8');

    $cf = new ControladorFactura();

    $id_factura = intval($_GET['f']);
    $t = $_GET['t'];

    $cfdi = "";
    $facturas = $cf->getXMLImprimir($id_factura);
    foreach ($facturas as $facturaactual) {
        $folio = $facturaactual['letra'] . $facturaactual['folio_interno_fac'];
        $uuid = $facturaactual['uuid'];
        $rfc = $facturaactual['rfc'];
        if ($t == 'a') {
            $cfdi = $facturaactual['cfdistring'];
        } else if ($t == 'c') {
            $cfdi = $facturaactual['cfdicancel'];
        }
    }

    if ($cfdi != "") {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="' . $folio . '_' . $rfc . '_' . $uuid . '.xml"');

        echo $cfdi;
        exit();
//$xml2->save($doc);
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_GET['p'])) {
    require_once '../com.sine.controlador/ControladorPago.php';
    setlocale(LC_MONETARY, 'es_MX.UTF-8');

    $cf = new ControladorPago();

    $idpago = intval($_GET['p']);
    $t = $_GET['t'];

    $cfdi = "";
    $pagos = $cf->getXMLInfo($idpago);
    foreach ($pagos as $pagoactual) {
        $foliopago = $pagoactual['letra'] . $pagoactual['foliopago'];
        $uuid = $pagoactual['uuidpago'];
        $rfc = $pagoactual['rfcemisor'];
        if ($t == 'a') {
            $cfdi = $pagoactual['cfdipago'];
        } else if ($t == 'c') {
            $cfdi = $pagoactual['cfdicancel'];
        }
    }

    if ($cfdi != "") {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="' . $foliopago . '_' . $rfc . '_' . $uuid . '.xml"');

        echo $cfdi;
        exit();
//$xml2->save($doc);
    } else {
        echo "404 No se encontro el archivo";
    }
} if (isset($_GET['c'])) {
    require_once '../com.sine.controlador/ControladorCarta.php';
    setlocale(LC_MONETARY, 'es_MX.UTF-8');

    $cc = new ControladorCarta();

    $id_factura = intval($_GET['c']);
    $t = $_GET['t'];

    $cfdi = "";
    $facturas = $cc->getXMLImprimir($id_factura);
    foreach ($facturas as $facturaactual) {
        $folio = $facturaactual['letra'] . $facturaactual['foliocarta'];
        $uuid = $facturaactual['uuid'];
        $rfc = $facturaactual['rfc'];
        if ($t == 'a') {
            $cfdi = $facturaactual['cfdistring'];
        } else if ($t == 'c') {
            $cfdi = $facturaactual['cfdicancel'];
        }
    }

    if ($cfdi != "") {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="' . $folio . '_' . $rfc . '_' . $uuid . '.xml"');

        echo $cfdi;
        exit();
//$xml2->save($doc);
    } else {
        echo "404 No se encontro el archivo";
    }
} else {
    echo "Error no se encontro el archivo";
}

