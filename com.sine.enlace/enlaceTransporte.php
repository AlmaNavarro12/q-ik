<?php

require_once '../com.sine.modelo/Transporte.php';
require_once '../com.sine.modelo/Remolque.php';
require_once '../com.sine.controlador/ControladorTransporte.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    
    $t = new Transporte();
    $ct = new ControladorTransporte();

    switch ($transaccion) {
        case 'insertartransporte':
            $insertado = $ct->nuevoTransporte(obtenerDatosTransporte());
            echo $insertado ? "1Transporte insertado." : "0Error: No se insertó el registro.";
            break;
        case 'filtrartransporte':
            $datos = $ct->listaTransportesHistorial($_POST['pag'], $_POST['REF'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'editartransporte':
            $tid = $_POST['tid'];
            $datos = $ct->getDatosTransporte($_POST['tid']);
            echo $datos != "" ? $datos : "0No se han encontrados datos.";
            break;
        case 'actualizartransporte':
            $t = obtenerDatosTransporte();
            $t->setIdtransporte($_POST['tid']);
            $datos = $ct->modificarTransporte($t);
            echo $datos != "" ? $datos : "0No se actualizó el registro.";
            break;
        case 'eliminartransporte':
            $eliminado = $ct->eliminarTransporte($_POST['tid']);
            echo $eliminado ? "1Transporte eliminado." : "0Error: No se eliminó el registro.";
            break;
        //---------------------------REMOLQUE
        case 'insertarremolque':
            $insertado = $ct->nuevoRemolque(obtenerDatosRemolque());
            echo $insertado ? "1Remolque insertado." : "0Error: No se insertó el registro.";
            break;
        case 'filtrarremolque':
            $datos = $ct->listaRemolquesHistorial($_POST['pag'], $_POST['REF'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'editarremolque':
            $datos = $ct->getDatosRemolque($_POST['rid']);
            echo $datos != "" ? $datos : "0No se han encontrados datos.";
            break;
        case 'actualizarremolque':
            $r = obtenerDatosRemolque();
            $r->setIdremolque($_POST['rid']);
            $datos = $ct->modificarRemolque($r);
            echo $datos != "" ? $datos : "0No se han encontrados datos.";
            break;
        case 'eliminarremolque':
            $eliminado = $ct->eliminarRemolque($_POST['rid']);
            echo $eliminado ? "1Remolque eliminado." : "0Error: No se eliminó el registro.";
            break;
    }
}

function obtenerDatosTransporte() {
    $t = new Transporte();
    $t->setNombre($_POST['nombre']);
    $t->setNumpermiso($_POST['numpermiso']);
    $t->setTipopermiso($_POST['tipopermiso']);
    $t->setConftransporte($_POST['conftransporte']);
    $t->setAnhomodelo($_POST['anho']);

    $placa = strtoupper(str_replace(['-', ' ', '.', '/', ','], '', $_POST['placa']));
    $t->setPlacavehiculo($placa);

    $t->setSegurorc($_POST['segurorc']);
    $t->setPolizarc($_POST['polizarc']);
    $t->setSeguroma($_POST['seguroma']);
    $t->setPolizama($_POST['polizama']);
    $t->setSegurocg($_POST['segurocg']);
    $t->setPolizacg($_POST['polizacg']);
    return $t;
}

function obtenerDatosRemolque() {
    $r = new Remolque();
    $r->setNombre($_POST['nombre']);
    $r->setTiporemolque($_POST['tiporemolque']);
    $placa = strtoupper(str_replace(['-', ' ', '.'], '', $_POST['placa']));
    $r->setPlacaremolque($placa);

    return $r;
}
