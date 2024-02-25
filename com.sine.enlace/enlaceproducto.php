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
        case 'editarproducto':
            $datos = $cp->datosProductos($_POST['idproducto']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'eliminarproducto':
            $eliminado = $cp->quitarProducto($_POST['idproducto']);
            echo $eliminado ? "Producto eliminado." : "0Error: No se pudo realizar la operación.";
            break;
        case 'actualizarproducto':
            $p = obtenerDatosProducto();
            $p->setIdProducto($_POST['idproducto']);
            $p->setImgactualizar($_POST['imgactualizar']);
            $p->setNameImg($_POST['nameimg']);
            $actualizado = $cp->valCodigoActualizar($p);
            echo $actualizado ? "Producto actualizado." : "0Error: No se pudo realizar la operación.";
            break;
    
    }
}

function obtenerDatosProducto()
{
    $p = new Producto();
    $p->setCodproducto($_POST['codproducto']);
    $p->setProducto($_POST['producto']);
    $divide = explode("-", $_POST['unidad']);
    $p->setClvunidad($divide[0]);
    $p->setDescripcionunidad($divide[1]);
    $divide2 = explode("-", $_POST['clavefiscal']);
    $p->setClavefiscal($divide2[0]);
    $p->setDescripcionfiscal($divide2[1]);
    $p->setDescripcion($_POST['descripcion']);
    $p->setPrecio_compra($_POST['pcompra'] ?? '0');
    $p->setPorcentaje($_POST['porcentaje'] ?? '0');
    $p->setGanancia($_POST['ganancia'] ?? '0');
    $p->setPrecio_venta($_POST['pventa'] ?? '0');
    $p->setTipo($_POST['tipo']);
    $p->setIdproveedor($_POST['idproveedor'] ?? '0');
    $p->setChinventario($_POST['chinventario'] ?? 0);
    $p->setCantidad($_POST['cantidad'] ?? '0');
    $p->setImagen($_POST["imagen"]);
    $p->setInsert($_POST['insert']);
    $p->setTaxes($_POST['imp_apl']);
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