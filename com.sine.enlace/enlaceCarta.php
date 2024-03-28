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
        case 'tablaubicacion':
            $tabla = $cc->tablaUbicacion(session_id(), $_POST['uuid']);
            echo $tabla;
            break;
        case 'getdistanciatmp':
            $consultado = $cc->getDistanciaTMP($_POST['idtmp']);
            echo $consultado ? $consultado : "0No se han encontrado datos.";
            break;
        case 'moddistancia':
            $consultado = $cc->modificarDistancia($_POST['idtmp'], $_POST['cant']);
            echo $consultado ? $consultado : "0No se han encontrado datos.";
            break;
        case 'editarubicacion':
            $datos = $cc->getDatosUbicacion($_POST['idtmp']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarubicacion':
            $u = obtenerDatosUbicacion();
            $u->setTmpid($_POST['tid']);
            $datos = $cc->actualizarUbicacion($u);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'eliminarubicacion':
            $eliminado = $cc->eliminarUbicacion($_POST['tid']);
            echo $eliminado ? $eliminado : "0No se han encontrado datos.";
            break;
            //-------------------------------------------OPERADORES
        case 'agregaroperador':
            $insertado = $cc->agregarOperador(obtenerDatosOperador());
            echo $insertado ? $insertado : "0Error: No se insertó el registro.";
            break;
        case 'tablaoperador':
            $tabla = $cc->tablaOperador(session_id(), $_POST['uuid']);
            echo $tabla;
            break;
        case 'editaroperador':
            $datos = $cc->getDatosOperador($_POST['idtmp']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizaroperador':
            $o = obtenerDatosOperador();
            $o->setTmpid($_POST['tid']);
            $datos = $cc->actualizarOperador($o);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'eliminaroperador':
            $eliminado = $cc->eliminarOperador($_POST['tid']);
            echo $eliminado ? $eliminado : "0No se han encontrado datos.";
            break;
            //--------------------------------------CARTA PORTE
        case 'emisor':
            $folio = $cc->getDatosEmisor($_POST['iddatos']);
            echo $folio != "" ? $folio : "0No se han encontrado datos.";
            break;
        case 'insertarcarta':
            $insertado = $cc->nuevaFacturaCarta(obtenerDatosCartaPorte());
            echo $insertado ? $insertado : "0Error: No se insertó el registro.";
            break;
        case 'editarcarta':
            $datos = $cc->getEditarCarta($_POST['cid']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarcarta':
            $cp = obtenerDatosCartaPorte();
            $cp->setTag($_POST['tag']);
            $cp->setUuid($_POST['uuid']);
            $insertado = $cc->modificarCarta($cp);
            echo $insertado ? $insertado : "0Error: No se actualizó el registro.";
            break;
        case 'eliminarcarta':
            $eliminado = $cc->eliminarFactura($_POST['cid']);
            echo $eliminado != "" ? "1Registro eliminado." : "0No se han encontrado datos.";
            break;
        case 'eliminarcfdi':
            $t = new TMPCFDI();
            $cf = new ControladorFactura();
            $idtmp = $_POST['idtmp'];
            $sessionid = session_id();
            $insertado = $cf->eliminarCFDI($idtmp, $sessionid);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
    }
}

function obtenerDatosMercancia()
{
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

function obtenerDatosUbicacion()
{
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

function obtenerDatosOperador()
{
    $o = new TMPOperador();
    $o->setTmpidoperador($_POST['id']);
    $o->setTmpnombre($_POST['nombre']);
    $o->setTmprfc($_POST['rfc']);
    $o->setTmplic($_POST['licencia']);
    $o->setEstado($_POST['estado']);
    $o->setCalle($_POST['direccion']);
    $search = array('-', ' ', '.', '/', ',', '_');
    $codpostal = str_replace($search, "", $_POST['codpostal']);
    $o->setCodpostal($codpostal);
    $o->setIdmunicipio($_POST['idmunicipio']);
    $o->setSid(session_id());
    return $o;
}

function obtenerDatosCartaPorte()
{
    $cp = new CartaPorte();

    $search = array('-', ' ', '.', '/', ',', '_');

    $cp->setFolio($_POST['folio']);
    $cp->setIdcliente($_POST['idcliente']);
    $cp->setCliente($_POST['cliente']);
    $cp->setRfccliente($_POST['rfccliente']);
    $cp->setRzcliente($_POST['razoncliente']);
    $cp->setRegfiscalcliente($_POST['regfiscal']);
    $cp->setDircliente($_POST['dircliente']);
    $cp->setCodpostal(str_replace($search, "", $_POST['codpostal']));
    $cp->setIdformapago($_POST['idformapago']);
    $cp->setIdmetodopago($_POST['idmetodopago']);
    $cp->setIdmoneda($_POST['idmoneda']);
    $cp->setTcambio($_POST['tcambio']);
    $cp->setIdusocfdi($_POST['iduso']);
    $cp->setTipocomprobante($_POST['tipocomprobante']);
    $cp->setIddatosfacturacion($_POST['iddatosF']);
    $cp->setPeriodicidad($_POST['periodicidad']);
    $cp->setMesperiodo($_POST['mesperiodo']);
    $cp->setAnoperiodo($_POST['anhoperiodo']);
    $cp->setChfirmar($_POST['chfirma']);
    $cp->setTipomovimiento($_POST['tipomov']);
    $cp->setIdvehiculo($_POST['idvehiculo']);
    $cp->setNombrevehiculo($_POST['nombrevehiculo']);
    $cp->setNumpermiso(str_replace($search, "", $_POST['numpermiso']));
    $cp->setTipopermiso($_POST['tipopermiso']);
    $cp->setTipotransporte($_POST['tipotransporte']);
    $cp->setModelo($_POST['modelo']);
    $cp->setPlacavehiculo(strtoupper(str_replace($search, "", $_POST['placavehiculo'])));
    $cp->setSegurorespcivil($_POST['segurorespcivil']);
    $cp->setPolizarespcivil(str_replace($search, "", $_POST['polizarespcivil']));
    $cp->setIdremolque1($_POST['idremolque1']);
    $cp->setNombreremolque1($_POST['nombreremolque1']);
    $cp->setTiporemolque1($_POST['tiporemolque1']);
    $cp->setPlacaremolque1(strtoupper(str_replace($search, "", $_POST['placaremolque1'])));
    $cp->setIdremolque2($_POST['idremolque2']);
    $cp->setNombreremolque2($_POST['nombreremolque2']);
    $cp->setTiporemolque2($_POST['tiporemolque2']);
    $cp->setPlacaremolque2(strtoupper(str_replace($search, "", $_POST['placaremolque2'])));
    $cp->setIdremolque3($_POST['idremolque3']);
    $cp->setNombreremolque3($_POST['nombreremolque3']);
    $cp->setTiporemolque3($_POST['tiporemolque3']);
    $cp->setPlacaremolque3(strtoupper(str_replace($search, "", $_POST['placaremolque3'])));
    $cp->setSeguroambiente($_POST['seguroambiente']);
    $cp->setPolizaambiente(str_replace($search, "", $_POST['polizaambiente']));
    $cp->setObservaciones($_POST['observaciones']);
    $cp->setSessionid(session_id());
    $cp->setCFDISrel($_POST['cfdis']);
    $cp->setPesoMercancia($_POST['p_mercancia']);
    $cp->setPesoVehicular($_POST['p_vehiculo']);
    $cp->setPesoBruto($_POST['p_bruto']);
    return $cp;
}