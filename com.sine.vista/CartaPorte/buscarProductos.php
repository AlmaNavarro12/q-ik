<!--BUSCAR PRODUCTO-->
<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Buscar producto:
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" onsubmit="return false;">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control inp-mod" id="buscar-producto" placeholder="Buscar Productos (Código, nombre, clave o descripción fiscal del producto)" oninput="buscarProducto()">
                        </div>
                        <div class="col-sm-2">
                            <select class="form-select inp-mod" id="num-reg" name="num-reg" onchange="buscarProducto()">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="scrollX div-form mw-100">
                    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-productos-factura">
                    </table>
                </div>
                <div class="row fw-semibold mt-3 text-uppercase" style="color: #17177C;" id="pagination">
                </div>
            </div>
        </div>
    </div>
</div>


<!--EDITAR CANTIDAD-->
<div class="modal fade" id="modal-cantidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Editar cantidad
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="idcant">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="label-form text-right" for="fecha-creacion">Cantidad</label>
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="cantidad-producto" name="cantidad-producto" placeholder="Cantidad" type="number" />
                            <span class='input-group-btn'>
                                <button type='button' class='button-modal' data-type='plus' id="btn-modificar-cant">
                                    <span class='fas fa-plus'></span>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div id="cantidad-producto-errors">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--OBSERVACIONES DEL PRODUCTO-->
<div class="modal fade" id="modal-observaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Editar observaciones del producto
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="idtmp">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="label-form text-right" for="observaciones-producto">Observaciones</label>
                        <textarea rows="5" cols="60" id="observaciones-producto" class="form-control input-form" placeholder="Observaciones sobre el producto" maxlength="400"></textarea>
                    </div>
                    <div id="observaciones-producto-errors">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <button class="button-modal" onclick="agregarObservaciones();" id="btn-observaciones">Agregar <span class="fas fa-pencil"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--AGREGAR NUEVO PRODUCTO-->
<div class="modal fade bs-example-modal-lg" id="nuevo-producto" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Agregar nuevo producto
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="limpiarCampos();" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-producto" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="codigo-producto">Código producto</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="form-control text-center input-form" id="codigo-producto" name="codigo-producto" placeholder="Código único de producto" type="text" maxlength="30" />
                            <div id="codigo-producto-errors">
                            </div>
                        </div>
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="producto">Nombre</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="form-control text-center input-form" id="producto" name="producto" placeholder="Producto" type="text" />
                            <div id="producto-errors">
                            </div>
                        </div>
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="descripcion">Descripción</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                            <input class="form-control text-center input-form" id="descripcion" name="descripcion" placeholder="Descripción" type="text" />
                            <div id="descripcion-errors">
                            </div>
                        </div>
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="clave-fiscal">Clave fiscal</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="form-control text-center input-form" id="clave-fiscal" name="clave-fiscal" placeholder="Clave del producto o servicio" type="text" oninput="aucompletarCatalogo();" />
                            <div id="clave-fiscal-errors">
                            </div>
                        </div>
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="tipo">Tipo</label> <label class="mark-required text-danger fw-bold">*</label>
                            <select class="form-select text-center input-form" id="tipo" name="tipo" onchange="addinventario()">
                                <option value="" id="option-default-tipo">- - - -</option>
                                <option class="text-start" value="1" id="tipo1">Producto</option>
                                <option class="text-start" value="2" id="tipo2">Servicio</option>
                            </select>
                            <div id="tipo-errors">
                            </div>
                        </div>
                        <div class="col-md-4 py-2">
                            <div id="inventarios" class="col-12 py-2" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 py-2">
                                        <div class="form-group d-flex aling-items-center col-sm-12  mt-3">
                                            <input class="input-check mt-1" checked="" id="chinventario" name="chinventario" type="checkbox" onchange="addinventario()" />
                                            <label class="label-form ms-2" for="chinventario" id="labelinventario">¿Activar inventario?</label>
                                            <div id="chinventario-errors">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="label-form" for="cantidad">Cantidad</label>
                                        <label class="mark-required text-danger fw-bold"></label>
                                        <div class="form-group">
                                            <input class="form-control text-center input-form col-12" id="cantidad" disabled name="producto" placeholder="Producto" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                                            <div id="cantidad-errors">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="clave-unidad">Clave unidad</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="form-control text-center input-form" id="clave-unidad" name="clave-unidad" placeholder="Clave de unidad de venta" type="text" oninput="aucompletarUnidad();" />
                            <div id="clave-unidad-errors">
                            </div>
                        </div>
                        <div class="col-md-2 py-2 form-group">
                            <label class="label-form text-right" for="pcompra">Precio de compra</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                            <input class="form-control text-center input-form" id="pcompra" name="pcompra" placeholder="Precio de compra" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); calcularGanancia();" />
                            <div id="pcompra-errors">
                            </div>
                        </div>
                        <div class="col-md-2 py-2 form-group">
                            <label class="label-form text-right" for="porganancia">Porcentaje ganancia</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                            <input class="form-control text-center input-form" id="porganancia" name="porganancia" placeholder="Porcentaje de Ganancia" value="0" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); calcularGanancia();" />
                            <div id="porganancia-errors">
                            </div>
                        </div>
                        <div class="col-md-2 py-2 form-group">
                            <label class="label-form text-right" for="ganancia">Importe ganancia</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                            <input class="form-control text-center input-form" id="ganancia" name="ganancia" placeholder="Importe Ganancia" type="text" disabled value='0' />
                            <div id="ganancia-errors">
                            </div>
                        </div>
                        <div class="col-md-2 py-2 form-group">
                            <label class="label-form text-right" for="pventa">Precio de venta</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="form-control text-center input-form" id="pventa" name="pventa" placeholder="Precio de venta" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                            <div id="pventa-errors">
                            </div>
                        </div>
                        <div>
                            <label class="label-form">Impuestos aplicables</label>
                            <div id="imp-apli" class="row"></div>
                        </div>
                        <div class="row mt-2 mb-2">
                            <div id="input-imp-apli" class="row col-12">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="mark-required text-danger fw-bold mb-1">&nbsp;</label><br>
                            <label class="button-file text-right col-12" for="imagen"><span class="fas fa-image"></span> Imagen del producto</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                            <div class="form-group">
                                <input class="form-control text-center upload" id="imagen" name="imagen" type="file" onchange="cargarImgProducto();" hidden accept=".jpg, .png, .jgep" />
                                <input id="filename" name="filename" type="hidden" />
                                <input id="imgactualizar" name="imgactualizar" type="hidden" />
                                <div id="imagen-errors">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 py-2 form-group">
                            <label class="label-form text-right" for="id-proveedor">Proveedor</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                            <select class="form-select text-center input-form" id="id-proveedor" name="id-proveedor">
                                <option value="" id="option-default-proveedor">- - - -</option>
                                <optgroup class="contenedor-proveedores text-start"> </optgroup>
                            </select>
                            <div id="id-proveedor-errors">
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class='col-md-6'>
                                <div class="col-md-12 mt-3 border rounded-2 border-secondary-subtle shadow position-relative" id="imagenproducto" style="display: none;">
                                    <div class="col-md-3 d-flex justify-content-center aling-items-center" id="muestraimagenproducto">

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="button-modal" onclick="insertarProductoCarta()" id="btn-form-producto-factura">Guardar producto <span class="fas fa-save"></span></button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

    </div>
</div>
</div>


<div class="modal fade bs-example-modal-lg" id="editar-producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta-editar">
                    Editar producto en factura
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-producto-editar" onsubmit="return false;">
                    <div class="row scrollX">
                        <div class="col-md-12">
                            <table class="table tab-hover table-condensed table-responsive table-row table-head">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <input id="editar-idtmp" name="editar-idtmp" type="hidden" />
                                            <label class="label-form text-right mb-2" for="descripcion-mano">Descripción</label>
                                            <input class="form-control text-center input-form" id="editar-descripcion" name="editar-descripcion" placeholder="Descripcion" type="text" />
                                            <div id="editar-descripcion-errors">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label class="label-form text-right mb-2" for="editar-cfiscal">Clave fiscal</label>
                                            <input class='form-control text-center input-form' id='editar-cfiscal' name='editar-cfiscal' placeholder='Clave Fiscal del producto' type='text' oninput="autocompletarCFiscal()" />
                                            <div id="editar-cfiscal-errors"></div>
                                        </td>
                                        <td colspan="2">
                                            <label class="label-form text-right mb-2" for="editar-cunidad">Clave unidad</label>
                                            <input class='form-control text-center input-form' id='editar-cunidad' name='editar-cunidad' placeholder='Clave de la unidad del producto' type='text' oninput="autocompletarCUnidad()" />
                                            <div id="editar-cunidad-errors"></div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="cant-obra">Cantidad</label>
                                            <input class="form-control text-center input-form" id="editar-cantidad" name="editar-cantidad" placeholder="Cantidad" type="number" oninput="calcularImporteEditar();" />
                                            <div id="editar-cantidad-errors">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="label-form text-right mb-2" for="precio-venta">Precio de venta</label>
                                            <input class="form-control text-center input-form" id="editar-precio" name="editar-precio" placeholder="Precio" type="number" oninput="calcularImporteEditar();" />
                                            <div id="editar-precio-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="importe-obra">Importe</label>
                                            <input class="form-control text-center input-form" id="editar-totuni" name="editar-totuni" placeholder="Precio" type="text" disabled />
                                            <div id="editar-totuni-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="por-descuento">Descuento %</label>
                                            <input class="form-control text-center input-form" id="editar-descuento" name="editar-descuento" placeholder="Descuento" type="number" oninput="calcularDescuentoEditar();" />
                                            <div id="editar-descuento-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="por-descuento">Importe descuento</label>
                                            <input class="form-control text-center input-form" id="editar-impdesc" name="editar-impdesc" placeholder="Descuento" type="text" disabled />
                                            <div id="editar-impdesc-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="traslados">Traslados</label>
                                            <div class='input-group'>
                                                <div class='dropdown'>
                                                    <button type='button' class='input-form dropdown-toggle' data-bs-toggle='dropdown' data-bs-auto-close="false">Traslados <span class='caret'></span></button>
                                                    <ul class='dropdown-menu' id="editar-traslados">
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="retencion">Retenciones</label>
                                            <div class='input-group'>
                                                <div class='dropdown'>
                                                    <button type='button' class='input-form dropdown-toggle' data-bs-toggle='dropdown' data-bs-auto-close="false">Retenciones <span class='caret'></span></button>
                                                    <ul class='dropdown-menu' id="editar-retencion">
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="total-obra">Total</label>
                                            <input class="form-control text-center input-form" id="editar-total" name="editar-total" placeholder="Precio de Compra" type="text" oninput="calcularGanancia()" disabled />
                                            <div id="editar-total-errors">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <label class="label-form text-right mb-2" for="editar-observaciones">Observaciones</label>
                                            <textarea rows="9" cols="60" id="editar-observaciones" class="form-control input-form" placeholder="Observaciones sobre el producto" maxlength="120"></textarea>
                                            <div id="editar-observaciones-errors">
                                            </div>
                                        </td>
                                        <td></td>
                                        <td colspan="2">
                                            <button class="button-modal col-12" onclick="actualizarConceptoFactura()" id="btn-form-producto-editar">Guardar <span class="fas fa-save"></span></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>