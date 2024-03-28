<?php

require_once '../com.sine.modelo/TMPPago.php';
require_once '../com.sine.controlador/ControladorPago.php';
require_once '../com.sine.controlador/ControladorFactura.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Pago.php';

$cp = new ControladorPago();
$cf = new ControladorFactura();
$t = new TMPPago();
Session::start();

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
        case 'listapagoaltas':
            $datos = "";
            $datos = $cp->listaServiciosHistorial($_POST['REF'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'emisor':
            $folio = $cp->getDatosEmisor($_POST['iddatos']);
            echo $folio ? $folio : "0No se han encontrado datos.";
            break;
        case 'loadtabla':
            $insertado = $cp->getTabla(session_id(), $_POST['tag'], $_POST['idmoneda'], $_POST['tcambio'], $_POST['uuid']);
            echo $insertado ? $insertado : "0Error: No se insertó el registro.";
            break;
        case 'nuevocomplemento':
            echo $datos = $cp->nuevoComplemento($_POST['comp']);
            break;
        case 'loadfactura':
            $datos = $cp->loadFactura($_POST['idfactura'], $_POST['idpago'], $_POST['type']);
            echo $datos != "" ? $datos : "0No se encontro la factura.";
            break;
        case 'agregarcfdi':
            $insertado = $cp->agregarPago(crearTMPPagoDesdePOST());
            echo $insertado ? $insertado : "0Error: No se insertó el registro. ";
            break;
        case 'eliminar':
            $eliminado = $cp->eliminar($_POST['idtemp']);
            echo $eliminado != "" ? $eliminado : "0No se han econtrado datos.";
            break;
        case 'borrarcomplemento':
            $insertado = $cp->cancelar(session_id(), $_POST['tab']);
            echo $insertado ? $insertado : "0Error al eliminar el complemento.";
            break;
        case 'insertarpago':
            $insertado = $cp->validarPago(obtenerDatosPago(), $_POST['objimpuesto']);
            echo $insertado ? $insertado : "0Error: no insertó el registro";
            break;
        case 'insertarcomplementos':
            $insertado = $cp->insertarComplemento(obtenerDatosComplementoPago());
            echo $insertado ? $insertado : "0Error al insertar el complemento.";
            break;
        case 'xml':
            $cadena = $cp->checkCancelados($_POST['idpago']);
            echo $cadena ? $cadena : "0Error.";
            break;
        case 'editarpago':
            $datos = $cp->getDatosPago($_POST['idpago']);
            echo $datos != "" ? $datos : "0No se han econtrado datos.";
            break;
        case 'complementos':
            $complementos = $cp->buildComplementos($_POST['tag'], session_id(), $_POST['uuid']);
            echo $complementos ? $complementos : '0No hay complementos en este pago.';
            break;
        case 'eliminarpago':
            $eliminado = $cp->eliminarPago($_POST['idpago']);
            echo $eliminado ? $eliminado : "0No se han encontrado datos";
            break;
        case 'cancelar':
            $eliminado = $cp->cancelar(session_id());
            echo $eliminado != "" ? $eliminado : "0No se han econtrado datos.";
            break;
        case 'pdf':
            $datos = $cp->mail($$_POST['idpago']);
            echo $datos != "" ? $datos : "0No hay pagos activos.";
            break;
        case 'mail':
            $datos = $cf->mail($_POST['idfactura']);
            echo $datos != "" ? $datos : "0No hay facturas activos.";
            break;
        case 'cancelartimbre':
            $eliminado = $cp->cancelarTimbre($_POST['idpago'], $_POST['motivo'],  $_POST['reemplazo']);
            echo $eliminado != "" ? $eliminado : "0No se han econtrado datos.";
            break;
        case 'actualizarpago':
            $p = obtenerDatosPago();
            $insertado = $cp->validarActualizarPago($p, $_POST['objimpuesto']);
            echo $insertado != "" ? $insertado : "0Error: No se inserto el registro.";
            break;
        case 'actualizarcomplementos':
            $insertado = $cp->actualizarComplemento(obtenerDatosComplementoPago());
            echo $insertado ? $insertado : "0Error.";
            break;
        case 'fecha':
            $fecha = $cf->getFecha();
            echo $fecha ? $fecha : "0No se han encontrado datos.";
            break;
        case 'documento':
            echo $cpp->getDocumento() ?: "0No se han encontrado datos.";
            break;
        case 'getcorreos':
            $datos = $cp->getCorreo($_POST['idfactura']);
            echo $datos != "" ? $datos : "0No se encontrarón correos registrados.";
            break;
        case 'expcomplementos':
            $complementos = $cp->exportarComplemento($_POST['idformapago'], $_POST['idmoneda'], $_POST['tcambio'], $_POST['idfactura'], $_POST['foliofactura'], session_id());
            echo $complementos != "" ? $complementos : "0Error en recuperar datos.";
            break;
        case 'statuscfdi':
            $datos = $cp->checkStatusCFDI($_POST['fid']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error al recuperar los datos.";
            break;
        case 'editarestado':
            $datos = $cp->estadoPago($_POST['idpago']);
            echo $datos != "" ? $datos : "0No hay clientes registrados.";
            break;
        case 'aplicarEgreso':
            $datos = $cp->aplicarEgreso($_POST['uuid'], $_POST['montoant_tmp'], $_POST['monto'],);
            echo $datos;
            break;
            /*case 'cfdipago':
            $datos = $cp->cfdisPago($_POST['idpago'], session_id());
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;*/
            /*case 'loaddatosfiscales':
            $datos = $cf->loadFiscales($_POST['idcarta']);
            echo $datos != "" ? $datos : "0No se han econtrado datos.";
            break;
        case 'tipocambio':
            $datos = $cp->updateTipoCambio();
            echo $datos != "" ? $datos : "0Error al obtener los datos.";
            break;*/
            /*case 'eliminarfactura':
            $eliminado = $cf->eliminarFactura($_POST['idfactura'], $_POST['folio']);
            echo $eliminado ? "1Registro eliminado" : "0No se han encontrado datos.";
            break;*/
            /*case 'filtrarfolio':
            $datos = $cf->listaServiciosHistorial($_POST['FO']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;*/
    }
}


function crearTMPPagoDesdePOST()
{
    $t = new TMPPago();
    $t->setTag($_POST['tag']);
    $t->setIdmoneda($_POST['idmoneda']);
    $t->setNombreMoneda($_POST['nombremoneda']);
    $t->setTcambio($_POST['tcambio']);
    $t->setType($_POST['type']);
    $t->setParcialidadtmp($_POST['parcialidad']);
    $t->setIdfacturatmp($_POST['idfactura']);

    $folio = $_POST['folio'];
    $t->setFoliotmp(explode("-", $folio)[1] ?? $folio);

    $t->setUuid($_POST['uuid']);
    $t->setTcamcfdi($_POST['tcamcfdi']);
    $t->setIdmonedacdfi($_POST['idmonedacdfi']);
    $t->setCmetodo($_POST['cmetodo']);
    $t->setMontopagotmp($_POST['montopago']);
    $t->setMontoanteriortmp($_POST['montoanterior']);
    $t->setMontoinsolutotmp($_POST['montorestante']);
    $t->setTotalfacturatmp($_POST['totalfactura']);
    $t->setSessionid(session_id());
    return $t;
}

function obtenerDatosPago()
{
    $p = new Pago();
    if(isset($_POST['tag'])) {
        $p->setTagpago($_POST['tag']);
    }
    if(isset($_POST['idpago'])) {
        $p->setIdpago($_POST['idpago']);
    }
    $p->setFoliopago($_POST['folio']);
    $p->setIdcliente($_POST['idcliente']);
    $p->setNombrecliente($_POST['cliente']);
    $p->setRfccliente($_POST['rfccliente']);
    $p->setRazoncliente($_POST['razoncliente']);
    $p->setRegfiscalcliente($_POST['regfiscalcliente']);

    $search = "/[^A-Za-z0-9]/";
    $p->setCodpostal(preg_replace($search, "", $_POST['codpostal']));
    $p->setPago_idfiscales($_POST['datosfiscales']);
    $p->setChfirmar($_POST['chfirma']);
    $p->setSessionid(session_id());
    return $p;
}

function obtenerDatosComplementoPago()
{
    $p = new Pago();
    $p->setTagpago($_POST['tag']);
    $p->setTagcomp($_POST['tagcomp']);
    $p->setOrden($_POST['orden']);
    $p->setPagoidformapago($_POST['idformapago']);
    $p->setCForma($_POST['cforma']);
    $p->setNombreForma($_POST['nombreforma']);
    $p->setPagoidmoneda($_POST['moneda']);
    $p->setNombreMoneda($_POST['nombremoneda']);
    $p->setTipocambio($_POST['tcambio']);
    $p->setFechapago($_POST['fechapago']);
    $p->setHorapago($_POST['horapago']);
    $p->setPago_idbanco($_POST['cuenta']);
    $p->setIdbancoB($_POST['beneficiario']);
    $p->setNooperacion($_POST['numtransaccion']);
    $p->setSessionid(session_id());
    return $p;
}
