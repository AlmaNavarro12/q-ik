<?php
require_once '../com.sine.modelo/Cliente.php';
require_once '../com.sine.controlador/ControladorCliente.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cc = new ControladorCliente();

    switch ($transaccion) {
        case 'insertarcliente':
            $insertado = $cc->nuevoCliente(obtenerDatosCliente());
            echo $insertado ? "Registro insertado" : "0Error: no insertó el registro";
            break;
        case 'editarcliente':
            $idcliente = $_POST['idcliente'];
            $datos = $cc->getDatosCliente($idcliente);
            echo $datos != "" ? $datos : "0No se han encontrado datos";
            break;
        case 'actualizarcliente':
            $idcliente = obtenerDatosCliente();
            $idcliente->setIdCliente($_POST['idcliente']);
            $actualizado = $cc->modificarCliente($idcliente);
            echo $actualizado ? "Registro actualizado" : "0Error: no actualizó el registro";
            break;
        case 'eliminarcliente':
            $idcliente = $_POST['idcliente'];
            $eliminado = $cc->quitarCliente($idcliente);
            echo $eliminado ? "Registro eliminado" : "0No se han encontrado datos";
            break;
        case 'filtrarcliente':
            $REF = $_POST['REF'];
            $pag = $_POST['pag'];
            $numreg = $_POST['numreg'];
            $datos = $cc->listaClientesHistorial($REF, $pag, $numreg);
            echo $datos != "" ? $datos : "0Ha ocurrido un error";
            break;
    }
}

function obtenerDatosCliente(){
    $c = new Cliente();
    $c->setNombre($_POST['nombre']);
    $c->setApellidoPaterno($_POST['apellidopaterno']);
    $c->setApellidoMaterno($_POST['apellidomaterno']);
    $c->setNombre_empresa($_POST['nombre_empresa']);
    $c->setCorreoinfo($_POST['correoinfo']);
    $c->setCorreo_fact($_POST['correo_fact']);
    $c->setCorreo_gerencia($_POST['correo_gerencia']);
    $c->setCorreoalt1($_POST['correoalt1']);
    $c->setCorreoalt2($_POST['correoalt2']);
    $c->setCorreoalt3($_POST['correoalt3']);
    $c->setTelefono($_POST['telefono']);
    $c->setRFC($_POST['rfc']);
    $c->setRazon($_POST['razon']);
    $c->setRegimen($_POST['regimenfiscal']);
    $c->setCalle($_POST['calle']);
    $c->setNum_interior($_POST['interior']);
    $c->setNum_exterior($_POST['exterior']);
    $c->setLocalidad($_POST['localidad']);
    $c->setEstado($_POST['estado']);
    $c->setNombreEstado($_POST['nombreestado']);
    $c->setMunicipio($_POST['municipio']);
    $c->setNombreMunicipio($_POST['nombremunicipio']);
    $c->setCodigo_postal($_POST['postal']);
    $c->setIdBanco($_POST['idbanco']);
    $c->setCuenta($_POST['cuenta']);
    $c->setClabe($_POST['clabe']);
    $c->setIdBanco1($_POST['idbanco1']);
    $c->setCuenta1($_POST['cuenta1']);
    $c->setClabe1($_POST['clabe1']);
    $c->setIdBanco2($_POST['idbanco2']);
    $c->setCuenta2($_POST['cuenta2']);
    $c->setClabe2($_POST['clabe2']);
    $c->setIdBanco3($_POST['idbanco3']);
    $c->setCuenta3($_POST['cuenta3']);
    $c->setClabe3($_POST['clabe3']);
    $c->setNombreBanco1($_POST['nombrebanco1']);
    $c->setNombreBanco2($_POST['nombrebanco2']);
    $c->setNombreBanco3($_POST['nombrebanco3']);
    $c->setNombreBanco4($_POST['nombrebanco4']);
    return $c;
}
