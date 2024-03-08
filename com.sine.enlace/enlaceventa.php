<?php
require_once '../com.sine.modelo/Venta.php';
require_once '../com.sine.modelo/CorteCaja.php';
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
            $insertado = $cv->getTotalTicket($_POST['tab'], session_id()); 
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
        case 'borrarticket':
            $insertado = $cv->cerrarTicket($_POST['tab'], session_id());
            break;
        case 'chivatmp':
            $insertado = $cv->modificarChIva($_POST['idtmp'], $_POST['traslados'], $_POST['retenciones']);
            break;
        case 'cancelarTicket':
            $insertado = $cv->cancelar(session_id());
            break;
        case 'cancelarTicked':
            $insertado = $cv->cancelarTicked($_POST['id']);
            break;
        case 'checkPrecio':
            $datos = $cv->checkPrecio($_POST['producto']);
            header('Content-Type: application/json');
            echo json_encode($datos, JSON_FORCE_OBJECT);
            break;
        case 'agregarproductobusqueda':
            $insertado = $cv->agregarProducto($_POST['producto'], $_POST['tab'], session_id(), $_POST['cantidad']);
            break;
        case 'cortecaja':
            $insertado = $cv->getCorteCaja($_POST['user']);
            break;
        case 'checkPersisionNewProduct':
            $bandera = $cv->verificarF5();
            echo $bandera == 1 ? "1Permiso concedido." : "0No tienes los permisos.";
            break;
        case 'validarsupervisor':
            $bandera = $cv->validarSupervisor($_POST['usuario'], $_POST['contrasena']);
            echo $bandera ? $bandera : "0Ha occurrido un error.";
            break;
        case 'insertarcorte':
            $insertado = $cv->insertarCorte(obtenerDatosCorte());
            echo $insertado != "" ? $insertado : "0Error: No se pudo realizar la operacion.";
            break;
        case 'filtrarcorte':
            $datos = $cv->listaCortesHistorial($_POST['REF'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'exportarticket':
            $insertado = $cv->exportarProductos($_POST['id'], session_id());
            break;
        case 'asignatagcotizacion':
            $insertado = $cv->asignarTAG($_POST['tab'], $_POST['sid']);
            break;
    }

    if (isset($insertado)) {
        echo $insertado ? $insertado : "0Error: No se pudo realizar la operacion.";
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

function obtenerDatosCorte(){
    $cc = new CorteCaja();
    $cc->setTotalventas($_POST["totalventas"]);
    $cc->setTotalentradas($_POST["totalentradas"]);
    $cc->setTotalsalidas($_POST["totalsalidas"]);
    $cc->setFondoinicio($_POST["fondoinicio"]);
    $cc->setUsuario($_POST["usuario"]);
    $cc->setTotalganancias($_POST["totalganancias"]);
    $cc->setIdsupervisor($_POST["idsupervisor"]); 
    $cc->setComentarios($_POST["comentarios"]); 
    $cc->setSobrantes($_POST["totalsobrantes"]); 
    $cc->setFaltantes($_POST["totalfaltantes"]); 
    return $cc;
}