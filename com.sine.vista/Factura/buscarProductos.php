<!--MODAL PARA BUSCAR PRODUCTOS EN FACTURAS-->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
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
                        <div class="col-sm-7">
                            <input type="text" class="form-control input-search" id="buscar-producto" placeholder="Buscar Productos (Codigo, Nombre, Clv o Descripcion Fiscal del producto)" title="Buscar Productos (Codigo, Nombre, Clv o Descripcion Fiscal del producto)" oninput="buscarProducto()">
                        </div>
                        <div class="col-sm-2">
                            <select class="form-select input-search" id="num-reg" name="num-reg" onchange="buscarProducto()">
                                <option value="10" >10</option>
                                <option value="15" >15</option>
                                <option value="20" >20</option>
                                <option value="30" >30</option>
                                <option value="50" >50</option>
                                <option value="100" >100</option>
                                <option value="200" >200</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="row scroll-modal scrollX mt-3" id="datosproducto" >
                    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-productos-factura">

                    </table>
                </div>
                <div class="row" id="pagination">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cantidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Editar Cantidad</h4>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="idcant">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="label-form text-right mb-2" for="fecha-creacion">Cantidad</label>
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="cantidad-producto" name="cantidad-producto" placeholder="Cantidad" type="number"/>
                            <span class='input-group-btn'>
                                <button type='button' class='button-modal' data-type='plus' onclick="modificarCantidad();">
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

<div class="modal fade" id="modal-observaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Editar Observaciones de Producto</h4>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="idtmp">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="label-form text-right mb-2" for="observaciones-producto">Observaciones</label>
                        <!-- <textarea rows="5" cols="60" id="observaciones-producto" class="form-control input-form" placeholder="Observaciones sobre el Producto" maxlength="400" ></textarea>-->
                        <textarea rows="5" cols="60" id="observaciones-producto" class="form-control input-form mb-2" placeholder="Observaciones sobre el Producto" maxlength="10000" ></textarea>
                        <div id="observaciones-producto-errors">
                    </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="d-flex justify-content-end mt-3" > 
                        <button class="button-modal" onclick="agregarObservaciones();" id="btn-observaciones">Agregar <span class="fas fa-pencil-alt"></span></button>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-lg" id="nuevo-producto" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <h4 class="titulo-modal" id="label-nuevo-producto">Agregar nuevo producto</h4>
            <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      
            <div class="modal-body">
                <form id="form-producto" onsubmit="return false;">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right" for="codigo-producto">Codigo Producto</label> <label class="mark-required text-right">*</label>
                            <input class="form-control text-center input-form mb-3" id="codigo-producto" name="codigo-producto" placeholder="Codigo unico de producto" type="text" maxlength="30"  pattern="[a-zA-Z0-9\s]*"  />
                            <div id="codigo-producto-errors">
                            </div>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right" for="producto">Nombre</label> <label class="mark-required text-right">*</label>
                            <input class="form-control text-center input-form" id="producto" name="producto" placeholder="Producto" type="text"/>
                            <div id="producto-errors">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="descripcion">Descripcion</label>
                            <input class="form-control text-center input-form mb-3" id="descripcion" name="descripcion" placeholder="Descripcion" type="text"/>
                            <div id="descripcion-errors">
                            </div>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="clave-fiscal">Clave Fiscal</label> <label class="mark-required text-right">*</label>
                            <input class="form-control text-center input-form" id="clave-fiscal" name="clave-fiscal" placeholder="Clave del producto o servicio" type="text" oninput="aucompletarCatalogo();"/>
                            <div id="clave-fiscal-errors">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" >
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="tipo">Tipo</label> <label class="mark-required text-right">*</label>
                            <select class="form-select w-100 p-2 mb-3" id="tipo" name="tipo" onchange="addinventario()">
                                <option value="" id="option-default-tipo">- - - -</option>
                                <option class="option-list" value="1"   >Producto</option>
                                <option class="option-list" value="2"   >Servicio</option>
                            </select>
                            <div id="tipo-errors">
                            </div>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="clave-unidad">Clave Unidad</label> <label class="mark-required text-right">*</label>
                            <input class="form-control text-center input-form" id="clave-unidad" name="clave-unidad" placeholder="Clave de unidad de venta" type="text" oninput="aucompletarUnidad();"/>
                            <div id="clave-unidad-errors">
                            </div>
                        </div>
                        </div>
                    
                    <div class="row " id="inventario" >
                        <div class="col-md-4 form-group">
                            <label class="label-form text-right mb-2" for="chinventario">Activar inventario?</label>
                            <div class="input-group">
                                <input class="input-check" id="chinventario" name="chinventario" type="checkbox" onchange="addinventario()" />
                            </div>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label class="label-form text-right mb-2" for="cantidad">Cantidad</label>
                            <input class="form-control text-center input-form" id="cantidad" disabled name="producto" placeholder="Producto" type="number" value="0" />
                            <div id="cantidad-errors">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="pcompra">Precio de Compra</label>
                            <input class="form-control text-center input-form mb-3" id="pcompra" name="pcompra" placeholder="Precio de Compra" type="number" oninput="calcularGanancia()"/>
                            <div id="pcompra-errors">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="porganancia">Porcentaje Ganancia</label>
                            <input class="form-control text-center input-form" id="porganancia" name="porganancia" placeholder="Porcentaje de Ganancia" value="0" type="number" oninput="calcularGanancia()"/>
                            <div id="porganancia-errors">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="ganancia">Importe Ganancia</label>
                            <input class="form-control text-center input-form mb-3" id="ganancia" name="ganancia" placeholder="Importe Ganancia" type="text" disabled value='0'/>
                            <div id="ganancia-errors">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="label-form text-right" for="pventa">Precio de Venta</label> <label class="mark-required text-right">*</label>
                            <input class="form-control text-center input-form" id="pventa" name="pventa" placeholder="Precio de Venta" type="number" />
                            <div id="pventa-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="id-proveedor">Proveedor</label>
                            <select class="form-select w-100 p-2 mb-3" id="id-proveedor" name="id-proveedor">
                                <option value="" id="option-default-proveedor">- - - -</option>
                                <optgroup class="contenedor-proveedores text-left"> </optgroup>
                            </select>
                            <div id="id-proveedor-errors">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="button-file text-right" for="imagen"><span  class="far fa-image"></span> Imagen del Producto</label>
                            <div class="form-group">
                                <input class="form-control text-center upload"  id="imagen" name="imagen"  type="file" onchange="cargarImgProducto();"/>
                                <input id="filename" name="filename"  type="hidden" />
                                <input id="imgactualizar" name="imgactualizar"  type="hidden"/>
                                <div id="imagen-errors">
                                </div>
                            </div>
                        </div>
                        <div class="text-left col-md-3" id="muestraimagen" >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="d-flex justify-content-end mt-3">
                            <button class="button-modal" onclick="insertarProductoFactura()" id="btn-form-producto-factura">Guardar <span class="fas fa-save"></span></button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>      

<div class="modal fade bs-example-modal-lg" id="editar-producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Editar Producto en Factura</h4>
            <div class="modal-body">
                <form id="form-producto-editar" onsubmit="return false;">
                    <div class="row scrollX">
                        <div class="col-md-12">
                            <table class="table tab-hover table-condensed table-responsive table-row table-head">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <input id="editar-idtmp" name="editar-idtmp" type="hidden"/>
                                            <label class="label-form text-right mb-2" for="descripcion-mano">Descripcion</label>
                                            <input class="form-control text-center input-form" id="editar-descripcion" name="editar-descripcion" placeholder="Descripcion" type="text"/>
                                            <div id="editar-descripcion-errors">
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <label class="label-form text-right mb-2" for="editar-cfiscal">Clave Fiscal</label>
                                            <input class='form-control text-center input-form' id='editar-cfiscal' name='editar-cfiscal' placeholder='Clave Fiscal del producto' type='text' oninput="autocompletarCFiscal()"/>
                                            <div id="editar-cfiscal-errors"></div>
                                        </td>
                                        <td colspan="2">
                                            <label class="label-form text-right mb-2" for="editar-cunidad">Clave Unidad</label>
                                            <input class='form-control text-center input-form' id='editar-cunidad' name='editar-cunidad' placeholder='Clave de la unidad del producto' type='text' oninput="autocompletarCUnidad()"/>
                                            <div id="editar-cunidad-errors"></div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="cant-obra">Cantidad</label>
                                            <input class="form-control text-center input-form" id="editar-cantidad" name="editar-cantidad" placeholder="Cantidad" type="number" oninput="calcularImporteEditar();"/>
                                            <div id="editar-cantidad-errors">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="label-form text-right mb-2" for="precio-venta">Precio de Venta</label>
                                            <input class="form-control text-center input-form" id="editar-precio" name="editar-precio" placeholder="Precio" type="number" oninput="calcularImporteEditar();"/>
                                            <div id="editar-precio-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="importe-obra">Importe</label>
                                            <input class="form-control text-center input-form" id="editar-totuni" name="editar-totuni" placeholder="Precio" type="text" disabled/>
                                            <div id="editar-totuni-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="por-descuento">% descuento</label>
                                            <input class="form-control text-center input-form" id="editar-descuento" name="editar-descuento" placeholder="Descuento" type="number" oninput="calcularDescuentoEditar();"/>
                                            <div id="editar-descuento-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="por-descuento">Importe descuento</label>
                                            <input class="form-control text-center input-form" id="editar-impdesc" name="editar-impdesc" placeholder="Descuento" type="text" disabled/>
                                            <div id="editar-impdesc-errors">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="traslados">Traslados</label>
                                            <div class='input-group'>
                                                <div class='dropdown'>
                                                    <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown'>Traslados <span class='caret'></span></button>
                                                    <ul class='dropdown-menu' id="editar-traslados">
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="retencion">Retenciones</label>
                                            <div class='input-group'>
                                                <div class='dropdown'>
                                                    <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown' >Retenciones <span class='caret'></span></button>
                                                    <ul class='dropdown-menu' id="editar-retencion">
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="label-form text-right mb-2" for="total-obra">Total</label>
                                            <input class="form-control text-center input-form" id="editar-total" name="editar-total" placeholder="Precio de Compra" type="text" oninput="calcularGanancia()" disabled/>
                                            <div id="editar-total-errors">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <label class="label-form text-right mb-2" for="editar-observaciones">Observaciones</label>
                                            <textarea rows="5" cols="60" id="editar-observaciones" class="form-control input-form" placeholder="Observaciones sobre el Producto" maxlength="120" ></textarea>
                                            <div id="editar-observaciones-errors">
                                            </div>
                                        </td>
                                        <td></td>
                                        <td colspan="2">
                                            <button class="button-modal" onclick="actualizarConceptoFactura()" id="btn-form-producto-editar">Guardar <span class="glyphicon fas fa-save"></span></button>
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