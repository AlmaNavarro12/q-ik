<?php

require_once '../com.sine.modelo/Ubicacion.php';
require_once '../com.sine.controlador/ControladorUbicacion.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    
    $u = new Ubicacion();
    $cu = new ControladorUbicacion();

    switch ($transaccion) {
        case 'insertarubicacion':
            $insertado = $cu->nuevaUbicacion(obtenerDatosUbicacion());
            echo $insertado ? "1Ubicación insertada." : "0Error: No se insertó el registro.";
            break;
        case 'filtrarubicacion':
            $datos = $cu->listaServiciosHistorial($_POST['pag'], $_POST['REF'], $_POST['tipo'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'editarubicacion':
            $datos = $cu->getDatosUbicacion($_POST['uid']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarubicacion':
            $u = obtenerDatosUbicacion();
            $u->setIdubicacion($_POST['uid']);
            $datos = $cu->modificarUbicacion($u);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'eliminarubicacion':
            $eliminado = $cu->eliminarUbicacion($_POST['uid']);
            echo $insertado ? "1Ubicación eliminada." : "0Error: No se eliminó el registro.";
            break;
    }
}

function obtenerDatosUbicacion() {
    $u = new Ubicacion();
    $u->setTipoubicacion($_POST['tipo']);
    $u->setNombre($_POST['nombre']);
    $u->setRfc($_POST['rfc']);
    $u->setCalle($_POST['calle']);
    $u->setNumext($_POST['numext']);
    $u->setNumint($_POST['numint']);
    $u->setCodigopostal($_POST['cp']);
    $u->setReferencia($_POST['referencia']);
    $u->setEstado($_POST['idestado']);
    $u->setNombreEstado($_POST['nombreestado']);
    $u->setMunicipio($_POST['idmunicipio']);
    $u->setNombreMunicipio($_POST['nombremunicipio']);
    $u->setLocalidad($_POST['localidad']);
    $u->setColonia($_POST['colonia']);
    return $u;
}