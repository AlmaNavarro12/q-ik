<?php
require_once '../com.sine.modelo/Cotizacion.php';
require_once '../com.sine.modelo/TMPCotizacion.php';
require_once '../com.sine.controlador/ControladorCotizacion.php';
require_once '../com.sine.controlador/ControladorFactura.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Producto.php';
require_once '../com.sine.modelo/Anticipo.php';

Session::start();

$c = new Cotizacion();
$t = new TMPCotizacion();
$cc = new ControladorCotizacion();
$cf = new ControladorFactura();

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
        case 'filtrarcotizacion':
            $datos = $cc->listaServiciosHistorial($_POST['REF'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'insertarcotizacion':
            $insertado = $cc->nuevaCotizacion(obtenerDatosCotizacion());
            echo $insertado ? $insertado : "0No se insertó el registro.";
            break;
        case 'actualizarcotizacion':
            $co = obtenerDatosCotizacion();
            $co->setIddatos_cotizacion($_POST['idcotizacion']);
            $co->setTag($_POST['tag']);
            $insertado = $cc->actualizarCotizacion($co);
            echo $insertado ? $insertado : "0No se actualizó el registro.";
            break;
        case 'editarcotizacion':
            $datos = $cc->getDatosCotizacion($_POST['idcotizacion']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'eliminarcotizacion':
            $eliminado = $cc->eliminarCotizacion($_POST['idcotizacion']);
            echo $eliminado ? "Cotización eliminada correctamente. " : "0No se ha eliminado el dato.";
            break;
        case 'prodcotizacion':
            $datos = $cc->productosCotizacion($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0Esta cotización no tiene conceptos.";
            break;
        case 'incrementar':
            $incrementar = $cc->incrementarProducto($_POST['idtmp']);
            echo $incrementar ? $incrementar : "0No se han encontrado datos.";
            break;
        case 'reducir':
            $reducir = $cc->reducirProducto($_POST['idtmp']);
            echo $reducir ? $reducir : "0No se han encontrado datos.";
            break;
        case 'modificartmp':
            $modificado = $cc->modificarCantidad($_POST['idtmp'], $_POST['cant']);
            echo $modificado ? $modificado : "0No se han encontrado datos.";
            break;
        case 'getcorreos':
            $datos = $cc->getCorreo($_POST['idcotizacion']);
            echo $datos != "" ? $datos : "0No se han encontrado correos registrados.";
            break;
        //---------------------------------------ANTICIPO
        case 'datosanticipo':
            $datos = $cc->getCotizacionAnticipo($_POST['idcotizacion']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'insertaranticipo':
            $insertado = $cc->nuevoAnticipo(obtenerDatosAnticipo());
            echo $insertado ? $insertado : "0No se insertó el registro.";
            break;
        case 'listaanticipos':
            $datos = $cc->listaAnticipo($_POST['idcotizacion']);
            echo $datos != "" ? $datos : "0No hay productos registrados.";
            break;
        case 'editaranticipo':
            $datos = $cc->getDatosAnticipo($_POST['idanticipo']);
            echo $datos != "" ? $datos : "0No hay productos registrados.";
            break;
        case 'actualizaranticipo':
            $a = obtenerDatosAnticipo();
            $a->setIdanticipo($_POST['idanticipo']);
            $datos = $cc->actualizarAnticipo($a);
            echo $datos != "" ? $datos : "0No se actualizó el registro.";
            break;
        case 'eliminaranticipo':
            $eliminado = $cc->eliminarAnticipo($_POST['idanticipo'], $_POST['idcotizacion']);
            echo $eliminado ? "Anticipo eliminado correctamente. " : "0No se ha eliminado el dato.";
            break;
        case 'transcribir':
            $datos = $cc->transcribirCantidad($_POST['idcot'], $_POST['cant']);
            echo $datos != "" ? $datos : "0Error al transcribir el mensaje.";
            break;
        case 'editarconcepto':
            $datos = $cc->getDatosTMP($_POST['idtmp']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'cancelar':
            $eliminado = $cc->cancelar(session_id());
            echo $eliminado ? "Registro eliminado correctamente. " : "0No se ha eliminado el dato.";
            break;
        case 'eliminar':
            $eliminado = $cc->eliminar($_POST['idtemp']);
            echo $eliminado ? "Dato eliminado correctamente. " : "0No se ha eliminado el dato.";
            break;
        case 'filtrarproducto':
            $datos = $cc->listaProductosHistorial($_POST['NOM'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'emisor':
            $datos = $cc->getDatosEmisor($_POST['iddatos']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'documento':
            $documento = $cc->getDocumento();
            echo $documento != "" ? $documento : "0No se han encontrado datos.";
            break;
        case 'agregarmanoobra':
            $datos = $cc->agregar(obtenerDatosTmpCotizacion(), '0');
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'tablatmp':
            $datos = $cc->tablaProd(session_id());
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'chivatmp':
            $datos = $cc->modificarChIva($_POST['idtmp'], $_POST['traslados'], $_POST['retenciones']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'agregarobservaciones':
            $t->setSessionid(session_id());
            $t->setIdtmp($_POST['idtmp']);
            $t->setObservacionestmp($_POST['observaciones']);
            $datos = $cc->agregarObservaciones($t);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'agregarProducto':
            $datos = $cc->checkInventario(obtenerDatosTmpCotizacion());
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarconcepto':
            $co = obtenerDatosTmpCotizacion();
            $co->setIdtmp($_POST['idtmp']);
            $datos = $cc->checkConcepto($co);
            echo $datos != "" ? $datos : "0No se ha actualizado el concepto.";
            break;
        //-----------------------------------------COBRAR
        case 'cobrar':
            $datos = $cc->cobrarCotizacion($_POST['idcotizacion'], session_id());
            echo $datos != "" ? $datos : "0No se han encontrado productos a cobrar.";
            break;
        case 'validarcotizacion':
            $datos = $cc->validarExistenciaFacturaCotizacion($_POST['idcotizacion'], $_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No se han podido exportar la cotización.";
            break;
        case 'exportarproducto':
            $datos = $cc->exportarprodCotizacion($_POST['tag'], session_id());
            echo $datos != "" ? $datos : "0No se han encontrado productos a exportar.";
            break;
        case 'actualizarprecios':
            $actualizar = $cc->actualizarPrecios($_POST['idcotizacion']);
            echo $actualizar != "" ? $actualizar : "0No se han encontrado datos.";
            break;
    }
}

function obtenerDatosCotizacion()
{
    $c = new Cotizacion();
    $c->setFolio($_POST['folio']);
    $c->setFecha_creacion($_POST['fecha_creacion']);
    $c->setIdCliente($_POST['idcliente']);
    $c->setNombreCliente($_POST['nombrecliente']);
    $c->setEmailCliente($_POST['correocliente']);
    $c->setEmailCliente2($_POST['correocliente2']);
    $c->setEmailCliente3($_POST['correocliente3']);
    $c->setTipoComprobante($_POST['tipocomprobante']);
    $c->setIdFormaPago($_POST['idformapago']);
    $c->setIdMetodoPago($_POST['idmetodopago']);
    $c->setIdMoneda($_POST['idmoneda']);
    $c->setIdUsoCfdi($_POST['iduso']);
    $c->setIddatosfacturacion($_POST['datosfac']);
    $c->setObservaciones($_POST['observaciones']);
    $c->setChFirmar($_POST['chfirmar']);
    $c->setSessionid(session_id());
    return $c;
}

function obtenerDatosAnticipo()
{
    $a = new Anticipo();
    $a->setIdcotizacion($_POST['idcotizacion']);
    $a->setMonto($_POST['anticipo']);
    $a->setRestante($_POST['restante']);
    $a->setAutorizacion($_POST['autorizacion']);
    $a->setFecha($_POST['fecha']);
    $a->setImg($_POST["img"]);
    $a->setMensaje($_POST['mensaje']);
    $a->setEmision($_POST['emision']);
    return $a;
}

function obtenerDatosTmpCotizacion()
{
    $t = new TMPCotizacion();
    $t->setSessionid(session_id());
    $t->setIdproductotmp($_POST['idproducto'] ?? '');
    $t->setDescripciontmp($_POST['descripcion']);
    $t->setClvfiscal($_POST['clvfiscal'] ?? '');
    $t->setClvunidad($_POST['clvunidad'] ?? '');
    $t->setCantidadtmp($_POST['cantidad']);
    $t->setPreciotmp($_POST['pventa']);
    $t->setImportetmp($_POST['importe']);
    $t->setDescuento($_POST['descuento']);
    $t->setImpdescuento($_POST['impdescuento']);
    $t->setImptotal($_POST['total']);
    $t->setIdtraslados($_POST['idtraslados']);
    $t->setIdretencion($_POST['idretencion']);
    return $t;
}
