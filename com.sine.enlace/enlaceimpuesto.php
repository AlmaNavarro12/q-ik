<?php

require_once '../com.sine.modelo/Impuesto.php';
require_once '../com.sine.controlador/ControladorImpuesto.php';
if (isset($_POST['transaccion'])) {

    $cf = new Impuesto();
    $ci = new ControladorImpuesto();
    $transaccion = $_POST['transaccion'];

    switch ($transaccion) {
        case 'insertarimpuesto':
            $insertado = $ci->checkImpuesto(obtenerDatosImpuesto());
            echo $insertado ? "Impuesto insertado." : "Error: No se insertó el impuesto.";
            break;
        case 'listaimpuesto':
            $datos = $ci->listaImpuesto($_POST['pag'], $_POST['REF'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'editarimpuesto':
            $datos = $ci->getDatosImpuesto($_POST['idimpuesto']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarimpuesto':
            $i = obtenerDatosImpuesto();
            $i->setIdimpuesto($_POST['idimpuesto']);
            $insertado = $ci->checkImpuesto($cf);
            echo $insertado ? "Impuesto actualizado." : "Error: No se actualizó el impuesto.";
            break;
        case 'eliminarimpuesto':
            $datos = $ci->eliminarImpuesto($_POST['idimpuesto']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'opcionestasa':
            $datos = $ci->getPorcentajes($_POST['tipo'], $_POST['impuesto'], $_POST['factor']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        default:
            break;
    }
}

function obtenerDatosImpuesto()
{
    $i = new Impuesto();
    $i->setNombre($_POST['nombre']);
    $i->setTipo($_POST['tipo']);
    $i->setImpuesto($_POST['impuesto']);
    $i->setFactor($_POST['factor']);
    $i->setTipoTasa($_POST['tipotasa']);
    $i->setTasa($_POST['tasa']);
    $i->setChuso($_POST['chuso']);
    return $i;
}