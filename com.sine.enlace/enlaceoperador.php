<?php

require_once '../com.sine.modelo/Operador.php';
require_once '../com.sine.controlador/ControladorOperador.php';
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    
    $o = new Operador();
    $co = new ControladorOperador();

    switch ($transaccion) {
        case 'insertaroperador':
            $insertado = $co->nuevoOperador(obtenerDatosOperador());
            echo !$insertado ? "1Operador insertado." : "0Error: No se insertó el registro.";
            break;
        case 'filtraroperador':
            $datos = $co->listaOperadoresHistorial($_POST['REF'], $_POST['numreg'], $_POST['pag']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'editaroperador':
            $datos = $co->getDatosOperador($_POST['id']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizaroperador':
            $o = obtenerDatosOperador();
            $o->setIdoperador($_POST['oid']);
            $actualizado = $co->modificarUsuario($o);
            echo $actualizado ? "1Operador actualizado." : "0Error: No se actualizó el registro.";
            break;
        case 'eliminaroperador':
            $eliminado = $co->quitarOperador($_POST['idoperador']);
            echo $eliminado ? "1Operador eliminado." : "0Error: No se eliminó el registro.";
            break;
    }
}

function obtenerDatosOperador() {
    $o = new Operador();
    $o->setNombre($_POST['nombre']);
    $o->setApaterno($_POST['apaterno']);
    $o->setAmaterno($_POST['amaterno']);
    $o->setNumlicencia($_POST['numlicencia']);
    $o->setRfc($_POST['rfc']);
    $o->setEmpresa($_POST['empresa']);
    $o->setIdestado($_POST['idestado']);
    $o->setNombreEstado($_POST['nombreestado']);
    $o->setIdmunicipio($_POST['idmunicipio']);
    $o->setNombreMunicipio($_POST['nombremunicipio']);
    $o->setCalle($_POST['calle']);
    $o->setCodpostal($_POST['cp']);
    return $o;
}