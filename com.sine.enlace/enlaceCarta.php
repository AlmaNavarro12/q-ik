<?php

require_once '../com.sine.modelo/CartaPorte.php';
require_once '../com.sine.modelo/Pago.php';
require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.modelo/TMPMercancia.php';
require_once '../com.sine.modelo/TMPUbicacion.php';
require_once '../com.sine.modelo/TMPOperador.php';
require_once '../com.sine.modelo/TMPCFDI.php';
require_once '../com.sine.controlador/ControladorCarta.php';
require_once '../com.sine.controlador/ControladorFactura.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';

Session::start();
$cc = new ControladorCarta();
$cf = new ControladorFactura();

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
        //---------------------------------------CARTA PORTE
        case 'filtrarfolio':
            $datos = $cc->listaServiciosHistorial($_POST['pag'], $_POST['REF'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
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
            $m->setTmpid($_POST['tid']);
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
        case 'cancelar':
            $eliminado = $cc->cancelar(session_id());
            echo $eliminado ? $eliminado : "0No se han encontrado datos.";
            break;
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
            $insertado = $cf->eliminarCFDI($_POST['idtmp'], session_id());
            echo $insertado ? $insertado : "0Error: No se insertó el registro.";
            break;
        case 'addcfdi':
            $t = new TMPCFDI();
            $t->setTiporel($_POST['rel']);
            $t->setUuid($_POST['cfdi']);
            $t->setDescripcion($_POST['descripcion']);
            $t->setSessionid(session_id());
            $insertado = $cc->agregarCFDI($t);
            echo $insertado ? $insertado : "0Error: No se insertó el registro.";
            break;
        case 'nuevosdatos':
            $datos = $cc->checkCarta($_POST['tag'], $_POST['type'], $_POST['num'] ?? '');
            echo $datos;
            break;
            //----------------------------------------RELACION CON LOS OTROS MODULOS
        case 'valvehiculo':
            $placa = strtoupper(str_replace(['-', ' ', '.', '/', ',', '_'], "", $_POST['placa']));
            echo $cc->checkVehiculo($placa);
            break;
        case 'valremolque':
            $placa = strtoupper(str_replace(['-', ' ', '.', '/', ',', '_'], "", $_POST['placa']));
            echo $cc->checkRemolque($placa);
            break;
        case 'valoperador':
            $datos = $cc->checkOperador($_POST['rfc']);
            echo $datos;
            break;
        case 'prodfactura':
            $datos = $cc->productosFactura($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No se han productos para esta factura.";
            break;
        case 'mercanciacarta':
            $datos = $cc->mercanciaCarta($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No se han encontrado los datos de mercancía para esta factura.";
            break;
        case 'ubicacioncarta':
            $datos = $cc->ubicacionCarta($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No se han encontrado datos de ubicación para esta factura.";
            break;
        case 'operadorcarta':
            $datos = $cc->operadorCarta($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No se han encontrado datos de operador para esta factura.";
            break;
        case 'doccarta':
            $datos = $cc->documentoCarta($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No hay evidencias agregadas datos.";
            break;
        //----------------------------------------------EVIDENCIAS
        case 'tablaimg':
            $insertado = $cc->tablaEvidencias($_POST['id']);
            echo $insertado != "" ? $insertado : "0No se han encontrado evidencias.";
            break;
        //----------------------------------------------PAGOS
        case 'getdatospago':
            $datos = $cc->getDatosFacPago($_POST['idcarta']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'tablaimgs':
            $datos = $cc->tablaIMG(session_id());
            echo  $datos;
            break;
        case 'eliminarimg':
            $resultado = $cc->eliminarIMG($_POST['idtmp']);
            echo 'Archivo eliminado.';
            break;
        case 'pagosfactura':
            $datos = $cc->tablaPagosReg($_POST['idfactura'], $_POST['estado']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        //-------------------------------------------GET
        case 'getcliente':
            $datos = $cc->checkCliente($_POST['rfc']);
            echo $datos;
            break;
        case 'getcorreos':
            $datos = $cc->getCorreo($_POST['idfactura']);
            echo $datos;
            break;
        //-------------------------------------------TIMBRADO
        case 'xml':
            $cadena = $cc->checkSaldo($_POST['id']);
            echo $cadena != "" ? $cadena : "0No se han encontrado correos.";
            break;
        case 'statuscfdi':
            $datos = $cc->checkStatusCFDI($_POST['fid']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'cancelartimbre':
            $cadena = $cc->cancelarTimbre($_POST['idfactura'], $_POST['motivo'], $_POST['reemplazo']);
            echo $cadena;
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
    $u->setNombreEstado($_POST['nombreestado']);
    $u->setIdmunicipio($_POST['idmunicipio']);
    $u->setNombreMunicipio($_POST['nombremunicipio']);
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
    $o->setNombreEstado($_POST['nombreestado']);
    $o->setCalle($_POST['direccion']);
    $search = array('-', ' ', '.', '/', ',', '_');
    $codpostal = str_replace($search, "", $_POST['codpostal']);
    $o->setCodpostal($codpostal);
    $o->setIdmunicipio($_POST['idmunicipio']);
    $o->setNombreMunicipio($_POST['nombremunicipio']);
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
    $cp->setNombreComprobante($_POST['nombre_comprobante']);
    $cp->setNombreMetodo($_POST['nombre_metodo']);
    $cp->setNombreForma($_POST['nombre_forma']);
    $cp->setNombreMoneda($_POST['nombre_moneda']);
    $cp->setNombrecfdi($_POST['nombre_cdfi']);
    return $cp;
}