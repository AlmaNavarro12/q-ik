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
    $f = new Factura();
    $cf = new ControladorFactura();

    switch ($transaccion) {
        case 'insertarfactura':
            $datosFactura = obtenerdatosFactura();
            $insertado = $cf->nuevoFactura($datosFactura);
            echo $insertado;    
            break;
        case 'addcfdi':
                $t = new TMPCFDI();
                $rel = $_POST['rel'];
                $cfdi = $_POST['cfdi'];
                $id = $_POST['id'];
                $folio = $_POST['folio'];
                $type = $_POST['type'];
                $descripcion = $_POST['descripcion'];
                $tcomp = $_POST['tcomp'];
                $sessionid = session_id();
    
                $t->setTiporel($rel);
                $t->setUuid($cfdi);
                $t->setIdDoc($id);
                $t->setFolioDoc($folio);
                $t->setType($type);
                $t->setDescripcion($descripcion);
                $t->setSessionid($sessionid);
                $t->setTipoComprobante($tcomp);
                $insertado = $cf->agregarCFDI($t);
                if ($insertado) {
                    echo $insertado;
                } else {
                    echo "0Error: no inserto el registro ";
                }
                break;
        case 'eliminarcfdi':
            $t = new TMPCFDI();
            $idtmp = $_POST['idtmp'];
            $sessionid = session_id();

            $insertado = $cf->eliminarCFDI($idtmp, $sessionid);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'editarfactura':
            $idFactura = $_POST['idFactura'];
            $datos = $cf->getDatosFactura($idFactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarpago':
            $idpago = $_POST['idpago'];
            $datos = $cf->getDatosPago($idpago);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarconcepto':
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
            $datos = $cf->getTipoCambio();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "No se han econtrado datos";
            }
            break;
        case 'getdatospago':
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getDatosFacPago($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarestado':
            $idfactura = $_POST['idfactura'];
            $datos = $cf->estadoFactura($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se guardo el registro";
            }
            break;
        case 'actualizarFactura':
                $f = obtenerDatosFactura();
                $f->setIddatos_factura($_POST['idfactura']); 
                $actualizado = $cf->modificarFactura($f);
                echo $actualizado ? $actualizado : "0Error: no se actualizó la factura";
                break;
        case 'eliminarfactura':
            $idfactura = $_POST['idfactura'];
            $eliminado = $cf->eliminarFactura($idfactura);
            if ($eliminado) {
                echo "1Registro eliminado";
            } else {
                echo "No se econtro datos";
            }
            break;
        case 'emisor':
            $iddatos = $_POST['iddatos'];
            $folio = $cf->getDatosEmisor($iddatos);
            if ($folio) {
                echo $folio;
            } else {
                echo "No se han econtrado datos";
            }
            break;
        case 'fecha':
            $fecha = $cf->getFecha();
            if ($fecha) {
                echo $fecha;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'filtrarfolio':
            $pag = $_POST['pag'];
            $REF = $_POST['REF'];
            $numreg = $_POST['numreg'];
            $datos = $cf->listaServiciosHistorial($pag, $REF, $numreg);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ha ocurrido un error en filtrar datos";
            }
            break;
        case 'agregarProducto':
            $datos = "";
            $t = new TMP();
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
                
                echo $datos;
            } else {
                echo "0Ha ocurrido un error en agregar un producto";
            }
            break;
        case 'chivatmp':
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
                echo "0Ha ocurrido un error en agregar observaciones";
            }
            break;
        case 'cfdisrelacionados':
            $datos = "";
            $f = new Factura();
            $tag = $_POST['tag'];
            $uuid = $_POST['uuid'];
            $sid = session_id();
            $datos = $cf->cfdisRelacionados($tag, $sid, $uuid);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ha ocurrido un error en recuperar los CFDIS relacionados.";
            }
            break;
        case 'prodfactura':
            $datos = "";
            $f = new Factura();
            $tag = $_POST['tag'];
            $sessionid = session_id();
            $datos = $cf->productosFactura($tag, $sessionid);
            if ($datos != "") {
                echo "datos";
            } else {
                echo "0No hay productos registrados en esta factura.";
            }
            break;
        case 'eliminar':
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
        case 'incrementar':
            $idtmp = $_POST['idtmp'];
            $eliminado = $cf->incrementarProducto($idtmp);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "incrementado";
            }
            break;
        case 'reducir':
            $idtmp = $_POST['idtmp'];
            $eliminado = $cf->reducirProducto($idtmp);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "red";
            }
            break;
        case 'modificartmp':
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
            $sessionid = session_id();
            $eliminado = $cf->cancelar($sessionid);
            if ($eliminado) {
                echo "1Registro eliminado";
            } else {
                echo "factura cancelada";
            }
            break;
        case 'pagosfactura':
            $idfactura = $_POST['idfactura'];
            $estado = $_POST['estado'];
            $eliminado = $cf->tablaPagosReg($idfactura, $estado);
            if ($eliminado) {
                echo $eliminado;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'xml':
            $idfactura = $_POST['idfactura'];
            $cadena = $cf->checkSaldo($idfactura);
            if ($cadena != "") {
                echo $cadena;
            } else {
                echo "no tienes saldo";
            }
            break;
        case 'cancelartimbre':
            $f = new Factura();
            $idfactura = $_POST['idfactura'];
            $motivo = $_POST['motivo'];
            $reemplazo = $_POST['reemplazo'];
            $cadena = $cf->cancelarTimbre($idfactura, $motivo, $reemplazo);
            if ($cadena != "") {
                echo 'timbre cancelado';
            } else {
                echo "error al cancelar el timbrado";
            }
            break;
        case 'filtrarproducto':
            $NOM = $_POST['NOM'];
            $pag = $_POST['pag'];
            $numreg = $_POST['numreg'];
            $datos = $cf->listaProductosHistorial($NOM, $pag, $numreg);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ha ocurrido un error en filtrado";
            }
            break;
        case 'mail2':
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
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getTelefono($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Boton Activo";
            }
            break;
        case 'getcorreos':
            $idfactura = $_POST['idfactura'];
            $datos = $cf->getCorreo($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se encontraron correos registrados";
            }
            break;
        case 'tablatmp':
            $sid = session_id();
            $uuid = $_POST['uuid'];
            $tcomprobante = $_POST['tcomprobante'];
            $tabla = $cf->tablaProd($sid, $uuid, $tcomprobante );
            echo $tabla;
            break;
        case 'diashorario':
            $primer = $_POST['primer'];
            $ultimo = $_POST['ultimo'];
            $tabla = $cf->actualizarDiasHorario($primer, $ultimo);
            echo $tabla;
            break;
        case 'loadcliente':
            $idcliente = $_POST['idcliente'];
            $datos = $cf->getClienteExportar($idcliente);
            echo $datos;
            break;
        case 'statuscfdi':
            $idfactura = $_POST['fid'];
            $datos = $cf->checkStatusCFDI($idfactura);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Boton Activo";
            }
            break;
        //nuevos cAMBIOS
        case 'cargarUUID':
            $cf = new ControladorFactura();
            $id = $_POST['id'];
            $type = $_POST['type'];
            $folio = $_POST['a'];

            $data = $cf->getUUIDRel($id, $type, $folio);
            echo $data;
            break;
        case 'asignarmonto':
            $cf = new ControladorFactura();

            $total = $_POST['total'];
            $id_egreso = $_POST['id_egreso'];
            $id_prod = $_POST['id_prod'];
            
            $asignado = $cf->asignarMontoCfdiRel($id_egreso, $total, $id_prod);
            echo $asignado;
            break;
        case 'cfdiEgreso':
            $cf = new ControladorFactura();
            $tag = $_POST['tag'];
            $sid = session_id();
            echo $egresos = $cf->cfdiEgreso($tag, $sid);
            break;
        default:
            break;
    }
}

function obtenerdatosFactura(){
    $f = new Factura();
    $sessionid = session_id(); 
    $f->setSessionid($sessionid);

    $search = array('-', ' ', '.', '/', ',', '_');
    $codpostal = str_replace($search, "", $_POST['codpostal']);
    
    $f->setFolio($_POST['folio']);
    $f->setIdcliente($_POST['idcliente']);
    $f->setCliente($_POST['cliente']);
    $f->setRfccliente($_POST['rfccliente']);
    $f->setRzcliente($_POST['razoncliente']); 
    $f->setRegfiscalcliente($_POST['regfiscal']);
    $f->setDircliente($_POST['dircliente']);
    $f->setCodpostal($codpostal);
    $f->setIdformapago($_POST['idformapago']);
    $f->setIdmetodopago($_POST['idmetodopago']);
    $f->setIdmoneda($_POST['idmoneda']);
    $f->setTcambio($_POST['tcambio']);
    $f->setIdusocfdi($_POST['iduso']);
    $f->setTipocomprobante($_POST['tipocomprobante']);
    $f->setIddatosfacturacion($_POST['iddatosF']);
    $f->setPeriodicidad($_POST['periodicidad']);
    $f->setMesperiodo($_POST['mesperiodo']);
    $f->setAnoperiodo($_POST['anhoperiodo']);
    $f->setTag($_POST['tag'] ?? ''); //n
    $f->setChfirmar($_POST['chfirma']);
    $f->setCfdisrel($_POST['cfdis']);
    $f->setIdcotizacion($_POST['idcotizacion']);
    //NUEVOS
    $f->setNombremoneda($_POST['nombremoneda']);
    $f->setNombremetodo($_POST['nombremetodo']);
    $f->setNombrecomprobante($_POST['nombrecomprobante']);
    $f->setNombrepago($_POST['nombrepago']);
    $f->setNombrecfdi($_POST['uso']);
    return $f; 
}