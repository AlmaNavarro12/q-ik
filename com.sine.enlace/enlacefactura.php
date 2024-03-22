<?php
require_once '../com.sine.modelo/Factura.php';
require_once '../com.sine.modelo/Pago.php';
require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.modelo/TMPCFDI.php';
require_once '../com.sine.controlador/ControladorFactura.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';
Session::start();
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];

    switch ($transaccion) {
        case 'insertarfactura':
            $f = new Factura();
            $cf = new ControladorFactura();
            $folio = $_POST['folio'];
            $idcliente = $_POST['idcliente'];
            $cliente = $_POST['cliente'];
            $rfccliente = $_POST['rfccliente'];
            $razoncliente = $_POST['razoncliente'];
            $regfiscal = $_POST['regfiscal'];
            $dircliente = $_POST['dircliente'];
            $codpostal = $_POST['codpostal'];
            $idformapago = $_POST['idformapago'];
            $idmetodopago = $_POST['idmetodopago'];
            $idmoneda = $_POST['idmoneda'];
            $tcambio = $_POST['tcambio'];
            $idusocfdi = $_POST['iduso'];
            $tipocomprobante = $_POST['tipocomprobante'];
            $iddatosfacturacion = $_POST['iddatosF'];
            $chfirma = $_POST['chfirma'];
            $cfdis = $_POST['cfdis'];
            $periodicidad = $_POST['periodicidad'];
            $mesperiodo = $_POST['mesperiodo'];
            $anhoperiodo = $_POST['anhoperiodo'];
            $idcotizacion = $_POST['idcotizacion'];
            $sessionid = session_id();

            $search = array('-', ' ', '.', '/', ',', '_');
            $codpostal = str_replace($search, "", $codpostal);

            $f->setFolio($folio);
            $f->setIdcliente($idcliente);
            $f->setCliente($cliente);
            $f->setRfccliente($rfccliente);
            $f->setRzcliente($razoncliente);
            $f->setRegfiscalcliente($regfiscal);
            $f->setDircliente($dircliente);
            $f->setCodpostal($codpostal);
            $f->setIdformapago($idformapago);
            $f->setIdmetodopago($idmetodopago);
            $f->setIdmoneda($idmoneda);
            $f->setTcambio($tcambio);
            $f->setIdusocfdi($idusocfdi);
            $f->setSessionid($sessionid);
            $f->setTipocomprobante($tipocomprobante);
            $f->setIddatosfacturacion($iddatosfacturacion);
            $f->setPeriodicidad($periodicidad);
            $f->setMesperiodo($mesperiodo);
            $f->setAnoperiodo($anhoperiodo);
            $f->setChfirmar($chfirma);
            $f->setCfdisrel($cfdis);
            $f->setIdcotizacion($idcotizacion);

            $insertado = $cf->nuevoFactura($f);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'addcfdi':
            $t = new TMPCFDI();
            $cf = new ControladorFactura();
            $rel = $_POST['rel'];
            $cfdi = $_POST['cfdi'];
            $sessionid = session_id();

            $t->setTiporel($rel);
            $t->setUuid($cfdi);
            $t->setSessionid($sessionid);
            $insertado = $cf->agregarCFDI($t);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
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
        case 'agregarPago':
            $t = new TMPPago();
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $idcliente = $_POST['idcliente'];
            $total = $_POST['total'];
            $monto = $_POST['monto'];
            $banco = $_POST['banco'];
            $numtransaccion = $_POST['numtrans'];
            $divide = explode("<corte>", $banco);
            $idbanco = $divide[0];
            $cuenta = $divide[1];
            $parcialidad = $_POST['parcialidad'];
            $sessionid = session_id();
            $t->setIdfactura($idfactura);
            $t->setIdcliente($idcliente);
            $t->setTotalfactura($total);
            $t->setMonto($monto);
            $t->setNumtransaccion($numtransaccion);
            $t->setIdbanco($idbanco);
            $t->setCuenta($cuenta);
            $t->setParcialidad($parcialidad);
            $t->setSessionid($sessionid);
            $insertado = $cf->agregarPago($t);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'insertarproducto':
            $p = new Producto();
            $cp = new ControladorFactura();
            $sessionid = session_id();
            $codproducto = $_POST['codproducto'];
            $producto = $_POST['producto'];
            $tipop = $_POST['tipo'];
            $unidad = $_POST['unidad'];
            $divide = explode("-", $unidad);
            $clvunidad = $divide[0];
            $descripcionunidad = $divide[1];
            $descripcion = $_POST['descripcion'];
            $pcompra = $_POST['pcompra'];
            $porcentaje = $_POST['porcentaje'];
            $ganancia = $_POST['ganancia'];
            $pventa = $_POST['pventa'];
            $clavefiscal = $_POST['clavefiscal'];
            $divide2 = explode("-", $clavefiscal);
            $clvfiscal = $divide2[0];
            $descripcionfiscal = $divide2[1];
            $idproveedor = $_POST['idproveedor'];
            $chinventario = $_POST['chinventario'];
            $cantidad = $_POST['cantidad'];
            $imagen = $_POST['imagen'];

            $p->setCodproducto($codproducto);
            $p->setProducto($producto);
            $p->setClvunidad($clvunidad);
            $p->setDescripcionunidad($descripcionunidad);
            $p->setDescripcion($descripcion);
            $p->setPrecio_compra($pcompra);
            $p->setPorcentaje($porcentaje);
            $p->setGanancia($ganancia);
            $p->setPrecio_venta($pventa);
            $p->setTipo($tipop);
            $p->setClavefiscal($clvfiscal);
            $p->setDescripcionfiscal($descripcionfiscal);
            $p->setIdproveedor($idproveedor);
            $p->setChinventario($chinventario);
            $p->setCantidad($cantidad);
            $p->setImagen($imagen);

            $insertado = $cp->validarCodigo($p, $sessionid);
            if ($insertado) {
                echo "Registro insertado";
            } else {
                echo "No registro producto";
            }
            break;
        case 'insertarpago':
            $p = new Pago();
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $idcliente = $_POST['idcliente'];
            $idmetodopago = $_POST['idmetodopago'];
            $idformapago = $_POST['idformapago'];
            $monto = $_POST['monto'];
            $total = $_POST['total'];
            $restante = $_POST['restante'];
            $banco = $_POST['banco'];
            $divide = explode("<corte>", $banco);
            $idbanco = $divide[0];
            $cuenta = $divide[1];
            $numt = $_POST['numt'];

            $p->setIdfactura($idfactura);
            $p->setIdcliente($idcliente);
            $p->setIdmetodopago($idmetodopago);
            $p->setIdformapago($idformapago);
            $p->setMonto($monto);
            $p->setTotalfactura($total);
            $p->setRestante($restante);
            $p->setIdbanco($idbanco);
            $p->setCuenta($cuenta);
            $p->setNotransaccion($numt);
            $insertado = $cf->validarPago($p);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'actualizarpago':
            $p = new Pago();
            $cf = new ControladorFactura();

            $idpago = $_POST['idpago'];
            $idfactura = $_POST['idfactura'];
            $idcliente = $_POST['idcliente'];
            $idmetodopago = $_POST['idmetodopago'];
            $monto = $_POST['monto'];
            $total = $_POST['total'];
            $restante = $_POST['restante'];
            $idformapago = $_POST['idformapago'];
            $banco = $_POST['banco'];
            $divide = explode("<corte>", $banco);
            $idbanco = $divide[0];
            $cuenta = $divide[1];
            $numt = $_POST['numt'];

            $p->setIdpago($idpago);
            $p->setIdfactura($idfactura);
            $p->setIdcliente($idcliente);
            $p->setIdmetodopago($idmetodopago);
            $p->setMonto($monto);
            $p->setTotalfactura($total);
            $p->setRestante($restante);
            $p->setIdformapago($idformapago);
            $p->setIdbanco($idbanco);
            $p->setCuenta($cuenta);
            $p->setNotransaccion($numt);
            $insertado = $cf->validarActualizarPago($p);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'listafactura':
            $cc = new ControladorFactura();
            $datos = $cc->listaFactura();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'editarfactura':
            $cf = new ControladorFactura();
            $idFactura = $_POST['idFactura'];
            $datos = $cf->getDatosFactura($idFactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarpago':
            $cf = new ControladorFactura();
            $idpago = $_POST['idpago'];
            $datos = $cf->getDatosPago($idpago);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarconcepto':
            $cf = new ControladorFactura();
            $idtmp = $_POST['idtmp'];
            $datos = $cf->getDatosTMP($idtmp);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'actualizarconcepto':
            $datos = "";
            $t = new TMP();
            $cf = new ControladorFactura();

            $sessionid = session_id();
            $idtmp = $_POST['idtmp'];
            $descripcion = $_POST['descripcion'];
            $clvfiscal = $_POST['clvfiscal'];
            $clvunidad = $_POST['clvunidad'];
            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];
            $totalunitario = $_POST['totalunitario'];
            $descuento = $_POST['descuento'];
            $impdescuento = $_POST['impdescuento'];
            $total = $_POST['total'];
            $observaciones = $_POST['observaciones'];
            $idtraslados = $_POST['idtraslados'];
            $idretencion = $_POST['idretencion'];

            $t->setIdtmp($idtmp);
            $t->setDescripciontmp($descripcion);
            $t->setCfiscaltmp($clvfiscal);
            $t->setCunidadtmp($clvunidad);
            $t->setCantidadtmp($cantidad);
            $t->setPreciotmp($precio);
            $t->setImportetmp($totalunitario);
            $t->setDescuento($descuento);
            $t->setImpdescuento($impdescuento);
            $t->setImptotal($total);
            $t->setObservacionestmp($observaciones);
            $t->setSessionid($sessionid);
            $t->setIdtraslados($idtraslados);
            $t->setIdretencion($idretencion);

            $datos = $cf->checkConcepto($t);
            if ($datos != "") {
                echo 'datos';
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'tipocambio':
            $cf = new ControladorFactura();
            $datos = $cf->getTipoCambio();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "No se han econtrado datos";
            }
            break;
        case 'getdatospago':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getDatosFacPago($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'getdatospago2':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getDatosFacPago2($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarestado':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $datos = $cf->estadoFactura($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se guardo el registro";
            }
            break;
        case 'actualizarFactura':
            $f = new Factura();
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $folio = $_POST['folio'];
            $idcliente = $_POST['idcliente'];
            $cliente = $_POST['cliente'];
            $rfccliente = $_POST['rfccliente'];
            $razoncliente = $_POST['razoncliente'];
            $regfiscal = $_POST['regfiscal'];
            $dircliente = $_POST['dircliente'];
            $codpostal = $_POST['codpostal'];
            $idformapago = $_POST['idformapago'];
            $idmetodopago = $_POST['idmetodopago'];
            $idmoneda = $_POST['idmoneda'];
            $tcambio = $_POST['tcambio'];
            $idusocfdi = $_POST['iduso'];
            $tipocomprobante = $_POST['tipocomprobante'];
            $iddatosfacturacion = $_POST['iddatosF'];
            $chfirmar = $_POST['chfirmar'];
            $cfdis = $_POST['cfdis'];
            $periodicidad = $_POST['periodicidad'];
            $mesperiodo = $_POST['mesperiodo'];
            $anhoperiodo = $_POST['anhoperiodo'];
            $tag = $_POST['tag'];
            $sessionid = session_id();

            $search = array('-', ' ', '.', '/', ',', '_');
            $codpostal = str_replace($search, "", $codpostal);

            $f->setIddatos_factura($idfactura);
            $f->setFolio($folio);
            $f->setIdcliente($idcliente);
            $f->setCliente($cliente);
            $f->setRfccliente($rfccliente);
            $f->setRzcliente($razoncliente);
            $f->setRegfiscalcliente($regfiscal);
            $f->setDircliente($dircliente);
            $f->setCodpostal($codpostal);
            $f->setIdformapago($idformapago);
            $f->setIdmetodopago($idmetodopago);
            $f->setIdmoneda($idmoneda);
            $f->setTcambio($tcambio);
            $f->setIdusocfdi($idusocfdi);
            $f->setSessionid($sessionid);
            $f->setTipocomprobante($tipocomprobante);
            $f->setIddatosfacturacion($iddatosfacturacion);
            $f->setChfirmar($chfirmar);
            $f->setCfdisrel($cfdis);
            $f->setPeriodicidad($periodicidad);
            $f->setMesperiodo($mesperiodo);
            $f->setAnoperiodo($anhoperiodo);
            $f->setTag($tag);

            $insertado = $cf->modificarFactura($f);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'eliminarfactura':
            $ca = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $eliminado = $ca->eliminarFactura($idfactura);
            if ($eliminado) {
                echo "1Registro eliminado";
            } else {
                echo "No se econtro datos";
            }
            break;
        case 'emisor':
            $ca = new ControladorFactura();
            $iddatos = $_POST['iddatos'];
            $folio = $ca->getDatosEmisor($iddatos);
            if ($folio) {
                echo $folio;
            } else {
                echo "No se han econtrado datos";
            }
            break;
        case 'foliopago':
            $ca = new ControladorFactura();
            $folio = $ca->getFolioPago();
            if ($folio) {
                echo $folio;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'fecha':
            $ca = new ControladorFactura();
            $fecha = $ca->getFecha();
            if ($fecha) {
                echo $fecha;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'documento':
            $ca = new ControladorCarta();
            $documento = $ca->getDocumento();
            if ($documento) {
                echo $documento;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'filtrarfolio':
            $pag = $_POST['pag'];
            $REF = $_POST['REF'];
            $numreg = $_POST['numreg'];
            $cs = new ControladorFactura();
            $datos = $cs->listaServiciosHistorial($pag, $REF, $numreg);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;

        case 'listaproductos':
            $datos = "";
            $cp = new ControladorFactura();
            $datos = $cp->listaProductos();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay productos registrados";
            }
            break;
        case 'listaproductos2':
            $datos = "";
            $cp = new ControladorFactura();
            $datos = $cp->listaProductos2();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'agregarProducto':
            $datos = "";
            $t = new TMP();
            $cf = new ControladorFactura();
            $sessionid = session_id();
            $idproductotmp = $_POST['idproducto'];
            $descripcion = $_POST['descripcion'];
            $cantidadtmp = $_POST['cantidad'];
            $pventatmp = $_POST['pventa'];
            $importetmp = $_POST['importe'];
            $descuento = $_POST['descuento'];
            $impdescuento = $_POST['impdescuento'];
            $total = $_POST['total'];
            $idtraslados = $_POST['idtraslados'];
            $idretencion = $_POST['idretencion'];

            $t->setSessionid($sessionid);
            $t->setIdproductotmp($idproductotmp);
            $t->setDescripciontmp($descripcion);
            $t->setCantidadtmp($cantidadtmp);
            $t->setPreciotmp($pventatmp);
            $t->setImportetmp($importetmp);
            $t->setDescuento($descuento);
            $t->setImpdescuento($impdescuento);
            $t->setImptotal($total);
            $t->setIdtraslados($idtraslados);
            $t->setIdretencion($idretencion);

            $datos = $cf->checkInventario($t);
            if ($datos != "") {
                echo json_encode($datos); //541
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'chivatmp':
            $cf = new ControladorFactura();
            $idtmp = $_POST['idtmp'];
            $traslados = $_POST['traslados'];
            $retenciones = $_POST['retenciones'];
            $eliminado = $cf->modificarChIva($idtmp, $traslados, $retenciones);
            if ($eliminado) {
                echo '$eliminado';
            } else {
                echo "No se encontro datos";
            }
            break;
        case 'agregarobservaciones':
            $datos = "";
            $t = new TMP();
            $cf = new ControladorFactura();
            $sessionid = session_id();
            $idtmp = $_POST['idtmp'];
            $observacionestmp = $_POST['observaciones'];
            $uuid = $_POST['uuid'];

            $t->setSessionid($sessionid);
            $t->setIdtmp($idtmp);
            $t->setObservacionestmp($observacionestmp);
            $t->setUuid($uuid);
            $datos = $cf->agregarObservaciones($t);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'cfdisrelacionados':
            $datos = "";
            $f = new Factura();
            $cf = new ControladorFactura();
            $tag = $_POST['tag'];
            $uuid = $_POST['uuid'];
            $sid = session_id();
            $datos = $cf->cfdisRelacionados($tag, $sid, $uuid);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'prodfactura':
            $datos = "";
            $f = new Factura();
            $cf = new ControladorFactura();
            $tag = $_POST['tag'];
            $sessionid = session_id();
            $datos = $cf->productosFactura($tag, $sessionid);
            if ($datos != "") {
                echo "datos";
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'eliminar':
            $cf = new ControladorFactura();
            $idtemp = $_POST['idtemp'];
            $cantidad = $_POST['cantidad'];
            $idproducto = $_POST['idproducto'];
            $sessionid = session_id();
            $eliminado = $cf->eliminar($idtemp, $sessionid, $cantidad, $idproducto);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "No se encontro datos";
            }
            break;
        case 'eliminarpago':
            $cf = new ControladorFactura();
            $idtemp = $_POST['idtemp'];
            $sessionid = session_id();
            $eliminado = $cf->eliminarPago($idtemp, $sessionid);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'incrementar':
            $cf = new ControladorFactura();
            $idtmp = $_POST['idtmp'];
            $eliminado = $cf->incrementarProducto($idtmp);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "incrementado";
            }
            break;
        case 'reducir':
            $cf = new ControladorFactura();
            $idtmp = $_POST['idtmp'];
            $eliminado = $cf->reducirProducto($idtmp);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "red";
            }
            break;
        case 'modificartmp':
            $cf = new ControladorFactura();
            $idtmp = $_POST['idtmp'];
            $cant = $_POST['cant'];
            $eliminado = $cf->modificarCantidad($idtmp, $cant);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "inc";
            }
            break;
        case 'cancelar':
            $cf = new ControladorFactura();
            $sessionid = session_id();
            $eliminado = $cf->cancelar($sessionid);
            if ($eliminado) {
                echo "1Registro eliminado";
            } else {
                echo "factura cancelada";
            }
            break;
        case 'loadpagos':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $sessionid = session_id();
            $eliminado = $cf->loadPagos($idfactura, $sessionid);
            if ($eliminado) {
                echo "$eliminado";
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'pagosfactura':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $estado = $_POST['estado'];
            $eliminado = $cf->tablaPagosReg($idfactura, $estado);
            if ($eliminado) {
                echo "$eliminado";
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'resetpagos':
            $cf = new ControladorFactura();
            $sessionid = session_id();
            $eliminado = $cf->resetPagos($sessionid);
            if ($eliminado) {
                echo "$eliminado";
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'imprimirFactura':
            $cf = new ControladorFactura();
            $f = new Factura();
            $idfactura = $_POST['idfactura'];
            $datos = $cf->imprimirFactura($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'xml':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $cadena = $cf->checkSaldo($idfactura);
            if ($cadena != "") {
                echo $cadena;
            } else {
                echo "no tienes saldo";
            }
            break;
        case 'modestado':
            $cf = new ControladorFactura();
            $f = new Factura();
            $idfactura = $_POST['idfactura'];
            $cadena = $cf->modEstado($idfactura);
            if ($cadena != "") {
                echo $cadena;
            } else {
                echo "";
            }
            break;
        case 'cancelartimbre':
            $cf = new ControladorFactura();
            $f = new Factura();
            $idfactura = $_POST['idfactura'];
            $motivo = $_POST['motivo'];
            $reemplazo = $_POST['reemplazo'];
            $cadena = $cf->cancelarTimbre($idfactura, $motivo, $reemplazo);
            if ($cadena != "") {
                echo $cadena;
            } else {
                echo "";
            }
            break;
        case 'filtrarproducto':
            $cf = new ControladorFactura();
            $NOM = $_POST['NOM'];
            $pag = $_POST['pag'];
            $numreg = $_POST['numreg'];
            $datos = $cf->listaProductosHistorial($NOM, $pag, $numreg);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'refactura':
            $cf = new ControladorFactura();
            $datos = $cf->Contratosfacturar();
            if ($datos != "") {
                echo $datos;
            } else {
                echo $datos;
            }
            break;
        case 'mail2':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $chcorreo1 = $_POST['chcorreo1'];
            $chcorreo2 = $_POST['chcorreo2'];
            $chcorreo3 = $_POST['chcorreo3'];
            $chcorreo4 = $_POST['chcorreo4'];
            $chcorreo5 = $_POST['chcorreo5'];
            $chcorreo6 = $_POST['chcorreo6'];

            $datos = $cf->mail($idfactura, $chcorreo1, $chcorreo2, $chcorreo3, $chcorreo4, $chcorreo5, $chcorreo6);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No Hay Facturas activas";
            }
            break;
        case 'gettelefono':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getTelefono($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Boton Activo";
            }
            break;
        case 'getcorreos':
            $cf = new ControladorFactura();
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getCorreo($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se encontraron correos registrados";
            }
            break;
        case 'tests':
            $cf = new ControladorFactura();
            $datos = $cf->checkStatusCFDI();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Boton Activo";
            }
            break;
        case 'tablatmp':
            $cf = new ControladorFactura();
            $sid = session_id();
            $uuid = $_POST['uuid'];
            $tabla = $cf->tablaProd($sid, $uuid);
            echo $tabla;
            break;
        case 'diashorario':
            $cf = new ControladorFactura();
            $primer = $_POST['primer'];
            $ultimo = $_POST['ultimo'];
            $tabla = $cf->actualizarDiasHorario($primer, $ultimo);
            echo $tabla;
            break;
        case 'loadcliente':
            $cf = new ControladorFactura();
            $idcliente = $_POST['idcliente'];

            $datos = $cf->getClienteExportar($idcliente);
            echo $datos;
            break;
        case 'statuscfdi':
            $cf = new ControladorFactura();
            $idfactura = $_POST['fid'];
            $datos = $cf->checkStatusCFDI($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Boton Activo";
            }
            break;
        default:
            break;
    }
}
