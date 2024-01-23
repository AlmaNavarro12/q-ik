<?php

require_once '../com.sine.modelo/Configuracion.php';
require_once '../com.sine.modelo/Folios.php';
require_once '../com.sine.controlador/ControladorConfiguracion.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $c = new Configuracion();
    $cc = new ControladorConfiguracion();
    //-------------------------------------- FOLIO
    switch ($transaccion) {
        case 'insertarfolio':
            $insertado = $cc->valFolio(obtenerDatosFolio());
            break;
        case 'listafolios':
            $insertado = $cc->listaFolios($_POST['pag'], $_POST['REF'], $_POST['numreg']);
            break;
        case 'editarfolio':
            $insertado = $cc->getDatosFolio($_POST['idfolio']);
            break;
        case 'actualizarfolio':
            $f = obtenerDatosFolio();
            $f->setIdfolio($_POST['idfolio']);
            $f->setActualizarinicio($_POST['inicio']);
            $insertado = $cc->valFolioActualizar($f);
            break;
        case 'eliminarfolio':
            $insertado = $cc->eliminarFolio($_POST['idfolio']);
            break;
            //--------------------------------TABLAS
        case 'loadexcel':
            $fnm = $_POST['fnm'];
            $tabla = $_POST['tabla'];
            echo ($datos = $cc->importTable($fnm, $tabla)) != "" ? $datos : "0No hay datos registrados.";
            break;
    }

    if (isset($insertado)) {
        echo $insertado ? $insertado : "0Error: No se pudo realizar la operación";
    }
}

function obtenerDatosFolio(){
    $f = new Folio();
    $f->setSerie($_POST['serie']);
    $f->setLetra($_POST['letra']);
    $f->setNuminicio($_POST['folio']);
    $f->setUsofolio($_POST['usofolio']);
    return $f;
}
