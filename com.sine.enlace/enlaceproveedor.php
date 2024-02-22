<?php

require_once '../com.sine.modelo/Proveedor.php';
require_once '../com.sine.controlador/ControladorProveedor.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    
    $cp = new ControladorProveedor();

    switch ($transaccion) {
        case 'insertarproveedor':
            $insertado = $cp->checarProveedor(obtenerDatosProveedor());
            echo $insertado ? "Proveedor insertado" : "0Error: No se insertó el registro.";
            break;
        case 'listaproveedor':
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'editarproveedor':
            $datos = $cp->getDatosProveedor( $_POST['idproveedor']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarproveedor':
            $p = obtenerDatosProveedor();
            $p->setId_proveedor($_POST['idproveedor']);
            $actualizado = $cp->checarProveedor($p);
            echo $actualizado ? "Proveedor actualizado." : "0Error: No se actualizó el registro.";
            break;
        case 'eliminarproveedor':
            $eliminado = $cp->quitarProveedor($_POST['idproveedor']);
            echo $eliminado ? "Registro eliminado" : "0No se han encontrado datos.";
            break;
        case 'filtrarproveedor':
            $datos = $cp->listaServiciosHistorial($_POST['REF'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
    }
}

function obtenerDatosProveedor() {
    $p = new Proveedor();
    $p->setEmpresa($_POST['empresa']);
    $p->setRepresentante($_POST['representante']);
    $p->setTelefono($_POST['telefono']);
    $p->setEmail($_POST['correo']);
    $p->setNum_cuenta($_POST['cuenta']);
    $p->setClave_interbancaria($_POST['clabe']);
    $p->setId_banco($_POST['idbanco']);
    $p->setNombre_banco($_POST['banco']);
    $p->setSucursal($_POST['sucursal']);
    $p->setRfc($_POST['rfc']);
    $p->setRazon($_POST['razon']);
    return $p;
}