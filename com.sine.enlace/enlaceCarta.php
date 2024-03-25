<?php

require_once '../com.sine.modelo/CartaPorte.php';
require_once '../com.sine.modelo/Pago.php';
require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.modelo/TMPMercancia.php';
require_once '../com.sine.modelo/TMPUbicacion.php';
require_once '../com.sine.modelo/TMPOperador.php';
require_once '../com.sine.modelo/TMPCFDI.php';
require_once '../com.sine.controlador/ControladorCarta.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';

Session::start();
$cc = new ControladorCarta();


if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
            //-----------------------------------MERCANCIA
        case 'agregarmercancia':
            $insertado = $cc->agregarMercancia(obtenerDatosMercancia());
            echo $insertado ? $insertado : "0Error: No se insertó el registro.";
            break;
        case 'tablamercancia':
            $tabla = $cc->tablaMercancia(session_id(), $_POST['uuid']);
            echo $tabla;
            break;
        case 'incredmercancia':
            $eliminado = $cc->incrementarMercancia($_POST['idtmp'], $_POST['flag']);
            echo $eliminado ? $eliminado : "0No se han encontrado datos.";
            break;
        case 'getcantmercancia':
            $consultado = $cc->getCantTMPMercancia($_POST['idtmp']);
            echo $consultado ? $consultado : "0No se han encontrado datos.";
            break;
        case 'modcantmercancia':
            $consultado = $cc->modificarCantMercancia($_POST['idtmp'], $_POST['cant']);
            echo $consultado ? $consultado : "0No se han encontrado datos.";
            break;
        case 'editarmercancia':
            $datos = $cc->getDatosMercancia($_POST['idtmp']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarmercancia':
            $m = obtenerDatosMercancia();
            $m->setTmpid($tid);
            $datos = $cc->actualizarMercancia($m);
            echo $datos != "" ? $datos : "0No se actualizó el registro.";
            break;
        case 'eliminarmercancia':
            $eliminado = $cc->eliminarMercancia($_POST['tid']);
            echo $eliminado ? $eliminado : "0No se han encontrado datos.";
            break;
        //---------------------------------------------UBICACION
        case 'agregarubicacion':
        $insertado = $cc->agregarUbicacion(obtenerDatosUbicacion());
        echo $insertado ? $insertado : "0Error: No se insertó el registro.";
        break;

    }
}

function obtenerDatosMercancia() {
    $m = new TMPMercancia();
    $m->setCondicional($_POST['condicional']);
    $m->setClvprod($_POST['clvprod']);
    $m->setDescripcion($_POST['descripcion']);
    $m->setCantidad($_POST['cantidad']);
    $m->setUnidad($_POST['unidad']);
    $m->setPeso($_POST['peso']);
    $m->setPeligro($_POST['peligro']);
    $clvmaterial = ($_POST['clvmaterial'] === 'null') ? '' : $_POST['clvmaterial'];
    $embalaje = ($_POST['embalaje'] === 'null') ? '' : $_POST['embalaje'];
    $m->setClvmaterial($clvmaterial);
    $m->setEmbalaje($embalaje);
    $m->setSid(session_id());
    return $m;
}

function obtenerDatosUbicacion() {
    $u = new TMPUbicacion();
    $search = array('-', ' ', '.', '/', ',', '_');
    $u->setTmpidubicacion($_POST['idu']);
    $u->setNombre($_POST['nombre']);
    $u->setRfc($_POST['rfc']);
    $u->setTipo($_POST['tipo']);
    $u->setDireccion($_POST['direccion']);
    $u->setEstado($_POST['idestado']);
    $u->setIdmunicipio($_POST['idmunicipio']);
    $u->setCodpos(str_replace($search, "", $_POST['cp']));
    $u->setDistancia($_POST['distancia']);
    $u->setFecha($_POST['fecha']);
    $u->setHora($_POST['hora']);
    $u->setSid(session_id());
    return $u;
}
