<?php

require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Permiso.php';
require_once '../com.sine.controlador/ControladorUsuario.php';
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cu = new ControladorUsuario();
    switch ($transaccion) {
        case 'insertarusuario':
            !$insertado = $cu->nuevoUsuario(obtenerDatosUsuario());
            break;
        case 'filtrarusuario':
            $insertado = $cu->listaServiciosHistorial($_POST['US'], $_POST['numreg'], $_POST['pag']);
            break;
        case 'editarusuario':
            $insertado = $cu->getDatosUsuario($_POST['idusuario']);
            break;
        case 'actualizarusuario':
            $u = obtenerDatosUsuario();
            $u->setIdUsuario($_POST['idusuario']);
            $u->setImgactualizar($_POST['imgactualizar']);
            $u->setNameImg($_POST['nameimg']);
            $insertado = $cu->nuevoUsuario($u);
            break;
        case 'eliminarusuario':
            $insertado = $cu->quitarUsuario($_POST['idusuario']);
            break;
        case 'gettipousuario':
            $insertado = $cu->getTipoLogin();
            break;
        case 'crearimg':
            crearImagen();
            break;
        case 'actualizarimg':
            $u = new Usuario();
            $u->setIdUsuario($_POST['idusuario']);
            $u->setImg($_POST['img']);
            $u->setImgactualizar($_POST['imgactualizar']);
            $insertado = $cu->actualizarImgPerfil($u);
            break;
        case 'insertarpermisos':
            $insertado = $cu->insertarPermisosList(obtenerDatosPermisos());
            break;
        case 'actualizarpermisos':
            $insertado = $cu->checkAccion(obtenerDatosPermisos());
            break;
        case 'asignarpermiso':
            $insertado = $cu->checkPermisos($_POST['idusuario']);
            break;
        case 'eliminarimgtmp':
            $datos = $cu->eliminarImgTmp($_POST['imgtmp']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
    }

    if (isset($insertado)) {
        echo $insertado ? $insertado : "0Error: No se pudo realizar la operación";
    }
}

function obtenerDatosUsuario()
{
    $u = new Usuario();
    $u->setNombre($_POST['nombre']);
    $u->setApellidoPaterno($_POST['apellidopaterno']);
    $u->setApellidoMaterno($_POST['apellidomaterno']);
    $u->setUsuario($_POST['usuario']);
    $u->setContrasena(sha1($_POST['password']));
    $u->setCorreo($_POST['correo']);
    $u->setCelular($_POST['celular']);
    $u->setTelefono($_POST['telefono']);
    $u->setEstatus("activo");
    $u->setTipo($_POST['tipo']);
    $u->setImg($_POST["img"]);
    return $u;
}

function obtenerDatosPermisos()
{
    $p = new Permiso();
    $p->setIdUsuario($_POST['idusuario']);
    $p->setAccion($_POST['accion']);

    $p->setFacturas($_POST['facturas']);
    $p->setCrearfactura($_POST['crearfactura']);
    $p->setEditarfactura($_POST['editarfactura']);
    $p->setEliminarfactura($_POST['eliminarfactura']);
    $p->setListafactura($_POST['listafactura']);
    $p->setTimbrarFactura($_POST['timbrarfactura']);

    $p->setPago($_POST['pago']);
    $p->setCrearpago($_POST['crearpago']);
    $p->setEditarpago($_POST['editarpago']);
    $p->setEliminarpago($_POST['eliminarpago']);
    $p->setListapago($_POST['listapago']);
    $p->setTimbrarPago($_POST['timbrarpago']);

    $p->setNomina($_POST['nomina']);
    $p->setListaempleado($_POST['listaempleado']);
    $p->setCrearempleado($_POST['crearempleado']);
    $p->setEditarempleado($_POST['editarempleado']);
    $p->setEliminarempleado($_POST['eliminarempleado']);
    $p->setListanomina($_POST['listanomina']);
    $p->setCrearnomina($_POST['crearnomina']);
    $p->setEditarnomina($_POST['editarnomina']);
    $p->setEliminarnomina($_POST['eliminarnomina']);
    $p->setListanomina($_POST['listanomina']);
    $p->setTimbrarNomina($_POST['timbrarnomina']);
    
    $p->setCartaporte($_POST['cartaporte']);
    $p->setListaubicacion($_POST['listaubicacion']);
    $p->setCrearubicacion($_POST['crearubicacion']);
    $p->setEditarubicacion($_POST['editarubicacion']);
    $p->setEliminarubicacion($_POST['eliminarubicacion']);
    $p->setListatransporte($_POST['listatransporte']);
    $p->setCreartransporte($_POST['creartransporte']);
    $p->setEditartransporte($_POST['editartransporte']);
    $p->setEliminartransporte($_POST['eliminartransporte']);
    $p->setListaremolque($_POST['listaremolque']);
    $p->setCrearremolque($_POST['crearremolque']);
    $p->setEditarremolque($_POST['editarremolque']);
    $p->setEliminarremolque($_POST['eliminarremolque']);
    $p->setListaoperador($_POST['listaoperador']);
    $p->setCrearoperador($_POST['crearoperador']);
    $p->setEditaroperador($_POST['editaroperador']);
    $p->setEliminaroperador($_POST['eliminaroperador']);
    $p->setCrearcarta($_POST['crearcarta']);
    $p->setEditarcarta($_POST['editarcarta']);
    $p->setEliminarcarta($_POST['eliminarcarta']);
    $p->setListacarta($_POST['listacarta']);
    $p->setTimbrarCarta($_POST['timbrarcarta']);
    
    $p->setCotizacion($_POST['cotizacion']);
    $p->setCrearcotizacion($_POST['crearcotizacion']);
    $p->setEditarcot($_POST['editarcotizacion']);
    $p->setEliminarcot($_POST['eliminarcotizacion']);
    $p->setListacotizacion($_POST['listacotizacion']);
    $p->setAnticipo($_POST['anticipo']);

    $p->setCliente($_POST['cliente']);
    $p->setCrearcliente($_POST['crearcliente']);
    $p->setEditarcliente($_POST['editarcliente']);
    $p->setEliminarcliente($_POST['eliminarcliente']);
    $p->setListacliente($_POST['listacliente']);

    $p->setComunicado($_POST['comunicado']);
    $p->setCrearcomunicado($_POST['crearcomunicado']);
    $p->setEditarcomunicado($_POST['editarcomunicado']);
    $p->setEliminarcomunicado($_POST['eliminarcomunicado']);
    $p->setListacomunicado($_POST['listacomunicado']);

    $p->setProducto($_POST['producto']);
    $p->setCrearproducto($_POST['crearproducto']);
    $p->setEditarproducto($_POST['editarproducto']);
    $p->setEliminarproducto($_POST['eliminarproducto']);
    $p->setListaproducto($_POST['listaproducto']);

    $p->setProveedor($_POST['proveedor']);
    $p->setCrearproveedor($_POST['crearproveedor']);
    $p->setEditarproveedor($_POST['editarproveedor']);
    $p->setEliminarproveedor($_POST['eliminarproveedor']);
    $p->setListaproveedor($_POST['listaproveedor']);

    $p->setImpuesto($_POST['impuesto']);
    $p->setCrearimpuesto($_POST['crearimpuesto']);
    $p->setEditarimpuesto($_POST['editarimpuesto']);
    $p->setEliminarimpuesto($_POST['eliminarimpuesto']);
    $p->setListaimpuesto($_POST['listaimpuesto']);

    $p->setDatosfacturacion($_POST['datosfacturacion']);
    $p->setCreardatos($_POST['creardatos']);
    $p->setEditardatos($_POST['editardatos']);
    $p->setListadatos($_POST['listadatos']);
    $p->setEliminarDatos($_POST['eliminardatos']);
    $p->setDescargarDatos($_POST['descargardatos']);

    $p->setContrato($_POST['contrato']);
    $p->setCrearcontrato($_POST['crearcontrato']);
    $p->setEditarcontrato($_POST['editarcontrato']);
    $p->setEliminarcontrato($_POST['eliminarcontrato']);
    $p->setListacontrato($_POST['listacontrato']);

    $p->setUsuarios($_POST['usuarios']);
    $p->setCrearusuario($_POST['crearusuario']);
    $p->setListausuario($_POST['listausuario']);
    $p->setEliminarusuario($_POST['eliminarusuario']);
    $p->setAsignarpermisos($_POST['asignarpermiso']);

    $p->setReporte($_POST['reporte']);
    $p->setReportefactura($_POST['reportefactura']);
    $p->setReportepago($_POST['reportepago']);
    $p->setReportegrafica($_POST['reportegrafica']);
    $p->setReporteiva($_POST['reporteiva']);
    $p->setDatosiva($_POST['datosiva']);
    $p->setReporteventas($_POST['reporteventas']);

    $p->setConfiguracion($_POST['configuracion']);
    $p->setAddfolio($_POST['addfolio']);
    $p->setListafolio($_POST['listafolio']);
    $p->setEditfolio($_POST['editarfolio']);
    $p->setEliminarfolio($_POST['eliminarfolio']);
    $p->setAddcomision($_POST['addcomision']);
    $p->setEncabezados($_POST['encabezados']);
    $p->setConfcorreo($_POST['confcorreo']);
    $p->setImportar($_POST['importar']);
    
    $p->setVentas($_POST['ventas']);
    $p->setCrearVenta($_POST['crearventa']);
    $p->setCancelarVenta($_POST['cancelarventa']);
    $p->setExportarVenta($_POST['exportarventa']);
    $p->setListaVenta($_POST['listaventa']);
    return $p;
}

function crearImagen()
{
    $sn = substr('David', 0, 1);
    $hoy = date("Y-m-d\TH:i:s");
    $imgPath = '../temporal/usuarios/' . $sn . $hoy . '.png';

    $im = imagecreatetruecolor(20, 20) or die("Cannot Initialize new GD image stream");
    $color_fondo = imagecolorallocate($im, 9, 9, 107);
    $color_texto = imagecolorallocate($im, 255, 255, 255);
    imagestring($im, 5, 6, 2.8, "$sn", $color_texto);

    header("Content-Type: image/png");
    imagepng($im, $imgPath);
    imagedestroy($im);

    echo basename($imgPath);
}