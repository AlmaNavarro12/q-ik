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
        case 'opcionesfacturacion':
            $datos = $co->opcionesDatFacturacion($_POST['id']);
            break;
        case 'opcionesproveedor':
            $datos = $co->opcionesProveedor($_POST['idprov']);
            break;
        case 'anoglobal':
            $datos = $co->opcionesAnoGlobal();
            break;
        case 'opcionesano':
            $datos = $co->opcionesAno();
            break;
        case 'opcionesusuario':
            $datos = $co->opcionesUsuario();
            break;
        case 'opcionesfolio':
            $datos = $co->opcionesFolios($_POST['id'], $_POST['serie'], $_POST['folio']);
            break;
        case 'correolist':
            $datos = $co->opcionesCorreoList();
            break;
        case 'opcionescliente':
            $datos = $co->opcionesCliente();
            break;
        
        
        /*
            case 'opcionesmotivo':
            $datos = $co->opcionesMotivo(); CATSAT
            break;*/
        
        /*case 'opcionesmpago':
            $datos = $co->opcionesMetodoPago($_POST['selected']); CATSAT
            break;
        case 'opcionesformapago':
            $datos = $co->opcionesFormaPago('', $_POST['selected']); CATSAT
            break;
        case 'opcionesformapago2':
            $condicion = "where c_pago !='99'";
            $datos = $co->opcionesFormaPago($condicion, $_POST['selected']); CATSAT
            break;
       
        case 'opcionesusocfdi':
            $datos = $co->opcionesUsoCFDI($_POST['iduso']); CATSAT
            break;
        case 'opcionescomprobante':
            $datos = $co->opcionesComprobante($_POST['id']);
            break;*/
        
        /*case 'opcionesregimen':
            $datos = $co->opcionesRegimen($_POST['idregimen']); CATSAT
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
            $datos = $co->opcionesEstadoCP($_POST['cp']); CATSAT
            break;
        case 'opcionesestado':
            $datos = $co->opcionesEstadoClv($_POST['idestado']); CATSAT
            break;
        case 'opcionesmunicipio':
            $datos = $co->opcionesMunicipioByEstado($_POST['idestado'], $_POST['idmunicipio']); CATSAT
            break;
        case 'opcionesbanco':
            $datos = $co->opcionesBanco($_POST['idbanco']); -->CONTROLADORBANCO
            break;
        case 'addopcionesbanco':
            $datos = $co->addopcionesBanco($_POST['a'], $_POST['idbanco']); CATSAT
            break;
        case 'opcionesriesgo':
            $datos = $co->opcionesRiesgo($_POST['idriesgo']);
            break;
        case 'opcionesvendedor':
            $datos = $co->opcionesVendedor();
            break;
        */
        /*case 'opcionesimpuestos':
            $datos = $co->opcionesImpuestos($_POST['t']); 
            break;
        
        case 'periodoglobal':
            $datos = $co->opcionesPeriodoGlobal($_POST['id']); CATSAT
            break;
        case 'opcionesmeses':
            $datos = $co->opcionesMesesPeriodo($_POST['id']); CATSAT
            break;
            case 'opcionesrelacion':
            $datos = $co->opcionesTipoRelacion(); CATSAT
            break;
        */
        default:
            break;
    }
    echo $datos;
}