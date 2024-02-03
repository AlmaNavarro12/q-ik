<?php
require_once '../Enrutador.php';

if (isset($_POST['transaccion'], $_POST['view'])) {
    $transaccion = $_POST['transaccion'];
    $vista = $_POST['view'];

    if (!empty($transaccion) && !empty($vista)) {
        $e = new Enrutador();

        switch ($vista) {
            case 'paginicio':
            case 'notificacion':
            case 'comprar':
            case 'nuevousuario':
            case 'listasuarioaltas':
            case 'asignarpermisos':
            case 'categoria':
            case 'listacategoria':
            case 'nuevoproducto':
            case 'listaproductoaltas':
            case 'valrfc':
            case 'nuevocliente':
            case 'listaclientealtas':
            case 'comunicado':
            case 'listacomunicado':
            case 'cfdi':
            case 'datosempresa':
            case 'nuevocontrato':
            case 'registrarpago':
            case 'pago':
            case 'listapago':
            case 'factura':
            case 'listafactura':
            case 'cotizacion':
            case 'listacotizacion':
            case 'instalacion':
            case 'listainstalacion':
            case 'listacontrato':
            case 'listacontratos':
            case 'listaprecios':
            case 'listaempresa':
            case 'listacfdi':
            case 'nuevoproveedor':
            case 'listaproveedor':
            case 'forminventario':
            case 'listainventario':
            case 'impuesto':
            case 'listaimpuesto':
            case 'reportefactura':
            case 'reportepago':
            case 'reportegrafica':
            case 'reportesat':
            case 'reporteventas':
            case 'datosiva':
            case 'config':
            case 'encabezado':
            case 'correo':
            case 'folio':
            case 'listafolio':
            case 'comision':
            case 'listafiel':
            case 'nuevafiel':
            case 'listadescsolicitud':
            case 'descsolicitud':
            case 'empleado':
            case 'listaempleado':
            case 'nomina':
            case 'listanomina':
            case 'direccion':
            case 'listadireccion':
            case 'transporte':
            case 'listatransporte':
            case 'remolque':
            case 'listaremolque':
            case 'operador':
            case 'listaoperador':
            case 'listacarta':
            case 'carta':
            case 'tablas':
            case 'puntosdeventa':
                $datos = $e->cargarVista($vista);
                break;
            default:
                echo "0Recurso no disponible.";
                break;
        }
    } else {
        echo "0Recurso no disponible.";
    }
} else {
    echo "0Recurso no disponible.";
}