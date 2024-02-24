<?php

if (isset($_GET['transaccion'])) {
    $transaccion = $_GET['transaccion'];
    include_once '../com.sine.controlador/ControladorUsuario.php';
    include_once '../com.sine.controlador/ControladorAuto.php';

    $cp = new ControladorAuto();

    switch ($transaccion) {
        case 'nombrecliente':
            echo json_encode($cp->getCoincidenciasBusquedaCliente($_GET['term']));
            break;
        /*
        case 'nomcliente':
            echo json_encode($cp->getCoincidenciasBusquedaCliente2($_GET['term']));
            break;
        case 'emailcliente':
            echo json_encode($cp->getCoincidenciasBusquedaMail($_GET['term']));
            break;
        case 'catfiscal':
            echo json_encode($cp->getCoincidenciasCatalogoFiscal($_GET['term']));
            break;
        case 'datosfiscales':
            echo json_encode($cp->getCoincidenciasCliente($_GET['term']));
            break;*/
        case 'facturas':
            echo json_encode($cp->getCoincidenciasFacturas($_GET['term'], $_GET['iddatos']));
            break;
        /*case 'catunidad':
            echo json_encode($cp->getCoincidenciasCatalogoUnidad($_GET['term']));
            break; 
        case 'foliocotizaion':
            echo json_encode($cp->getCoincidenciasFolioCotizacion($_GET['term']));
            break;
        case 'direccion':
            echo json_encode($cp->getCoincidenciasDireccion($_GET['term']));
            break;
        case 'regimenfiscal':
            echo json_encode($cp->getCoincidenciasRegimen($_GET['term']));
            break;
        case 'claveregimen':
            echo json_encode($cp->getCoincidenciasClvRegimen($_GET['term']));
            break;
        case 'empleado':
            echo json_encode($cp->getCoincidenciasEmpleado($_GET['term']));
            break;
        case 'localidad':
            echo json_encode($cp->getCoincidenciasLocalidad($_GET['term']));
            break;
        case 'tipopermiso':
            echo json_encode($cp->getCoincidenciasTipoPermiso($_GET['term']));
            break;
        case 'conftransporte':
            echo json_encode($cp->getCoincidenciasConfigTransporte($_GET['term']));
            break;
        case 'subtiporemolque':
            echo json_encode($cp->getCoincidenciasTipoRemolque($_GET['term']));
            break;
        case 'mercancia':
            $b = $_GET['b'];
            $result = "";
            if ($b == '1') {
                $result = json_encode($cp->getCoincidenciasProducto($_GET['term']));
            } else if ($b == '2') {
                $result = json_encode($cp->getCoincidenciasCatalogoFiscal($_GET['term']));
            }
            echo $result;
            break;
        case 'peligro':
            echo json_encode($cp->getCoincidenciasMaterialPeligroso($_GET['term']));;
            break;
        case 'embalaje':
            echo json_encode($cp->getCoincidenciasEmbalaje($_GET['term']));;
            break;
        case 'unitcarta':
            echo json_encode($cp->getCoincidenciasUnidadCarta($_GET['term']));
            break;
        case 'vehiculo':
            echo json_encode($cp->getCoincidenciasVehiculo($_GET['term']));
            break;
        case 'remolque':
            echo json_encode($cp->getCoincidenciasRemolque($_GET['term']));
            break;
        case 'ubicacion':
            $b = $_GET['b'];
            echo json_encode($cp->getCoincidenciasUbicacion($_GET['term'], $b));
            break;
        case 'operador':
            echo json_encode($cp->getCoincidenciasOperador($_GET['term']));
            break;
        default:
            break; */
    }
} else {
    header("Location: ../");
}
