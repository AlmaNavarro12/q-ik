<?php

require_once '../com.sine.controlador/ControladorOpcion.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $co = new ControladorOpcion();
    $datos = '';

    switch ($transaccion) {
        case 'opcionesbancocliente':
            $datos = $co->opcionesBancobyCliente($_POST['idcliente']);
            break;
        case 'opcionesbeneficiario':
            $datos = $co->opcionesBeneficiario($_POST['iddatos']);
            break;
        /*case 'opcionescliente':
            $datos = $co->opcionesCliente();
            break;*/
        case 'opcionesfacturacion':
            $datos = $co->opcionesDatFacturacion($_POST['id']);
            break;
        /*case 'opcionesmpago':
            $datos = $co->opcionesMetodoPago($_POST['selected']);
            break;
        case 'opcionesformapago':
            $datos = $co->opcionesFormaPago('', $_POST['selected']);
            break;
        case 'opcionesformapago2':
            $condicion = "where c_pago !='99'";
            $datos = $co->opcionesFormaPago($condicion, $_POST['selected']);
            break;
       
        case 'opcionesusocfdi':
            $datos = $co->opcionesUsoCFDI($_POST['iduso']);
            break;
        case 'opcionescomprobante':
            $datos = $co->opcionesComprobante($_POST['id']);
            break;
        case 'opcionesproveedor':
            $datos = $co->opcionesProveedor($_POST['idprov']);
            break;
        case 'opcionesregimen':
            $datos = $co->opcionesRegimen($_POST['idregimen']);
            break;
        case 'opcionesperiodicidad':
            $datos = $co->opcionesPeriodicidad($_POST['idper']);
            break;
        case 'opcionesjornada':
            $datos = $co->opcionesJornada($_POST['idjor']);
            break;
        case 'opcionescontrato':
            $datos = $co->opcionesContrato($_POST['idcontrato']);
            break;
        case 'buscarcp':
            $datos = $co->opcionesEstadoCP($_POST['cp']);
            break;
        case 'opcionesestado':
            $datos = $co->opcionesEstadoClv($_POST['idestado']);
            break;
        case 'opcionesmunicipio':
            $datos = $co->opcionesMunicipioByEstado($_POST['idestado'], $_POST['idmunicipio']);
            break;
        case 'opcionesbanco':
            $datos = $co->opcionesBanco($_POST['idbanco']);
            break;
        case 'addopcionesbanco':
            $datos = $co->addopcionesBanco($_POST['a'], $_POST['idbanco']);
            break;
        case 'opcionesriesgo':
            $datos = $co->opcionesRiesgo($_POST['idriesgo']);
            break;
        case 'opcionesvendedor':
            $datos = $co->opcionesVendedor();
            break;
        */case 'opcionesano':
            $datos = $co->opcionesAno();
            break;
        case 'opcionesusuario':
            $datos = $co->opcionesUsuario();
            break;
        case 'opcionesfolio':
            $datos = $co->opcionesFolios($_POST['id'], $_POST['serie'], $_POST['folio']);
            break;
        /*case 'correolist':
            $datos = $co->opcionesCorreoList();
            break;*/
        case 'opcionesmotivo':
            $datos = $co->opcionesMotivo();
            break;
        /*case 'opcionesimpuestos':
            $datos = $co->opcionesImpuestos($_POST['t']);
            break;
        case 'opcionesrelacion':
            $datos = $co->opcionesTipoRelacion();
            break;
        case 'periodoglobal':
            $datos = $co->opcionesPeriodoGlobal($_POST['id']);
            break;
        case 'opcionesmeses':
            $datos = $co->opcionesMesesPeriodo($_POST['id']);
            break;
        case 'anoglobal':
            $datos = $co->opcionesAnoGlobal();
            break;*/
        default:
            break;
    }
    echo $datos;
}