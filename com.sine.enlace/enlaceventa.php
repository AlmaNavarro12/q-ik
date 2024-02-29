<?php
require_once '../com.sine.modelo/Venta.php';
require_once '../com.sine.controlador/ControladorVenta.php';
require_once '../com.sine.modelo/Session.php';
Session::start();

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cv = new ControladorVenta();
    $v = new Venta();

    switch ($transaccion) {
        case 'newventa':
            $insertado = $cv->loadNewTicket($_POST['ticket']);
            break;
        case 'checkfondo':
            $insertado = $cv->checkDineroCaja();
            break;
        case 'agregarproducto':
            $insertado = $cv->agregarProducto($_POST['producto'], $_POST['tab'], session_id());
            break;
        case 'tablatmp':
            $insertado = $cv->tablaTicket($_POST['tab'], session_id());
            break;
        case 'registrarmovimiento':
            $v->setTipomov($_POST['tipo']);
            $v->setMontomov($_POST['cantidad']);
            $v->setConceptomov($_POST['concepto']);
            $insertado = $cv->insertarmovEfectivo($v);
            break;
        case 'incrementar':
            $insertado = $cv->incrementarProducto($_POST['idtmp']);
            break;
        case 'eliminarprod':
            $insertado = $cv->eliminarProducto($_POST['tid']);
            break;
        case 'modificartmp':
            $insertado = $cv->modificarCantidad($_POST['idtmp'], $_POST['cant'], $_POST['precio']);
            break;
        case 'reducir':
            $insertado = $cv->reducirProducto($_POST['idtmp']);
            break;
        case 'fondoinicial':
            $insertado = $cv->insertarMontoInicial($_POST['monto']);
            break;
        case 'totalticket':
            $insertado = $cv->getTotalTicket($_POST['tab'], session_id()); //MANDAR ALERTA DE XTOTAL
            break;
        case 'validaproductos':
            $datos = $cv->validaProductos($_POST['tab']);
            header('Content-Type: application/json');
            echo json_encode($datos, JSON_FORCE_OBJECT);
            break;
        case 'insertarticket':
            $insertado = $cv->insertarTicket(obtenerDatosTicket());
            break;
        case 'listaventa':
            $insertado = $cv->listaServiciosHistorial($_POST['pag'], $_POST['REF'], $_POST['numreg'], $_POST['usuario']);
            break;
            /*
        case 'agregarproductobusqueda':
            $insertado = $cv->agregarProductoBusqueda($_POST['producto'], $_POST['tab'], session_id(), $_POST['cantidad']);
            break;
        case 'chivatmp':
            $insertado = $cv->modificarChIva($_POST['idtmp'], $_POST['traslados'], $_POST['retenciones']);
            break;
        case 'cancelar':
            $insertado = $cv->delAllTickets(session_id()); //MNDAR ALERTA DE ELIMINADO EN EL JS
            break;
        case 'borrarticket':
            $insertado = $cv->cerrarTicket($_POST['tab'], session_id());
            break;
        
        case 'exportarticket':
            $insertado = $cv->exportarProductos($_POST['id'], session_id());
            break;
        case 'cortecaja':
            $insertado = $cv->getCorteCaja($_POST['user'], $_POST['fecha']);
            break;
        case 'asignatagcotizacion':
            $insertado = $cv->asignarTAG($_POST['tab'], $_POST['sid']);
            break;
        case 'cancelarTicked':
            $insertado = $cv->cancelarTicked($_POST['id']);
            break;
        case 'checkPersisionNewProduct':
            $bandera = $cv->verificarF5();
            echo $bandera == 1 ? "1Permiso concedido" : "0No tienes los permisos";
            break;
        case 'checkPrecio':
            $datos = $cv->checkPrecio($_POST['producto']);
            header('Content-Type: application/json');
            echo json_encode($datos, JSON_FORCE_OBJECT);
            break;
        case 'validaproductos':
            $datos = $cv->validaProductos($_POST['tab']);
            header('Content-Type: application/json');
            echo json_encode($datos, JSON_FORCE_OBJECT);
            break;*/
    }

    if (isset($insertado)) {
        echo $insertado ? $insertado : "0Error: No se pudo realizar la operacion";
    }
}

function obtenerDatosTicket()
{
    $v = new Venta();
    $v->setTagventa($_POST['tab']);
    $v->setTotalventa($_POST['total']);
    $v->setFormapago($_POST['fmpago']);
    $v->setMontopagado($_POST['pagado']);
    $v->setReferencia($_POST['referencia']);
    $v->setSid(session_id());
    $v->setDescuento($_POST['descuento']);
    $v->setPercentDescuento($_POST['percent_descuento']);
    return $v;
}