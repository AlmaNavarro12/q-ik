<?php

require_once '../com.sine.modelo/Producto.php';
require_once '../com.sine.controlador/ControladorProducto.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cp = new ControladorProducto();

    switch ($transaccion) {
        case 'insertarproducto':
            $insertado = $cp->validarCodigo(obtenerDatosProducto());
            echo $insertado ? $insertado : "0Error: No se pudo realizar la operación.";
            break;
        case 'filtrarproducto':
            $datos = $cp->listaProductosHistorial($_POST['NOM'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'taxesproductos':
            $datos = $cp->listaProductosTaxes($_POST['taxes']);
            header("Content-type: application/json; charset=utf-8");
            echo json_encode($datos);
            break;
        case 'eliminarimgtmp':
            $datos = $cp->eliminarImgTmp($_POST['imgtmp']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'desactivarinventario':
            
            $inventario = $cp->estadoInventario(obtenerDatosInventario());
            echo $insertado ? "Inventario desactivado." : "0Error: No se pudo realizar la operación.";
            break;
        case 'activarinventario':
            $inventario = $cp->estadoInventario(obtenerDatosInventario());
            $p = obtenerDatosInventario();
            echo $inventario ? "Inventario activado con ". $p->getCantidad() . " productos." : "0Error: No se pudo realizar la operación.";
            break;
    }
}

function obtenerDatosProducto()
{
    $p = new Producto();
    $p->setCodproducto($_POST['codproducto']);
    $p->setProducto($_POST['producto']);
    $p->setClvunidad($_POST['unidad']);
    $p->setDescripcion($_POST['descripcion']);
    $p->setPrecio_compra($_POST['pcompra'] ?? '0');
    $p->setPorcentaje($_POST['porcentaje'] ?? '0');
    $p->setGanancia($_POST['ganancia'] ?? '0');
    $p->setPrecio_venta($_POST['pventa'] ?? '0');
    $p->setTipo($_POST['tipo']);
    $p->setClavefiscal($_POST['clavefiscal']);
    $p->setIdproveedor($_POST['idproveedor'] ?? '0');
    $p->setChinventario($_POST['chinventario'] ?? 0);
    $p->setCantidad($_POST['cantidad'] ?? '0');
    $p->setImagen($_POST["imagen"]);
    $p->setInsert($_POST['insert']);

    $taxes = [];
    if (isset($_POST['imp_apl']) && is_array($_POST['imp_apl'])) {
        $taxes = $_POST['imp_apl'];
    }
    $p->setTaxes($taxes);

    return $p;
}

function obtenerDatosInventario()
{
    $p = new Producto();
    $p->setIdProducto($_POST['idproducto']);
    $p->setCantidad($_POST['cantidad']);
    $p->setChinventario($_POST['estado']);
    return $p;
}