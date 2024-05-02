<?php
include("buscarProductos.php");
?>
<div id="form-cotizacion">
    <div class="col-md-12">
        <div class="titulo-lista" id="contenedor-titulo-form-cotizacion">Nueva cotización </div>
    </div>
    <div id="div-space">
    </div>
    <div class="div-form  p-5 border border-secondary-subtle">
    <div class="col-md-12">
            <div class="col-md-12 d-flex justify-content-end mb-2">
                <label class="fw-bold text-danger small">* Campo obligatorio</label>
            </div>
        </div>
        <input type="hidden" name="transaccion" id="transaccion" value="" />
        <div class="row">
            <label class="control-label col-md-8 text-right">
            </label>
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="fecha-creacion">Fecha de creación</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="form-control text-center input-form" disabled id="fecha-creacion" name="fecha-creacion" placeholder="Fecha Actual" type="text" />
                    <div id="fecha-creacion-errors">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="folio-cotizacion">Folio</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="folio-cotizacion" name="folio-cotizacion">
                        <option value="" id="option-default-folio">- - - -</option>
                        <optgroup id="foliofactura" class="contenedor-folios text-start"> </optgroup>
                    </select>
                    <div id="folio-cotizacion-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="datos-facturacion">Datos de facturacion</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="datos-facturacion" name="datos-facturacion" onchange="autocompletarEmisor();">
                        <option value="" id="option-default-datos">- - - -</option>
                        <optgroup id="datosfacturar" class="contenedor-datos text-start"> </optgroup>
                    </select>
                    <div id="datos-facturacion-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="rfc-emisor">RFC</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="rfc-emisor" name="rfc-emisor" placeholder="RFC Emisor" type="text" />
                    <div id="rfc-emisor-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="razon-emisor">Raz&oacute;n cocial</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="razon-emisor" name="razon-emisor" placeholder="Raz&oacute;n social emisor" type="text" />
                    <div id="razon-emisor-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="regimen-emisor">R&eacute;gimen fiscal</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="regimen-emisor" name="regimen-emisor" placeholder="R&eacute;gimen fiscal" type="text" />
                    <div id="regimen-emisor-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="cp-emisor">C&oacute;digo postal</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="cp-emisor" name="cp-emisor" placeholder="C&oacute;digo postal" type="text" />
                    <div id="cp-emisor-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-6 text-start">
                <label class="label-sub" for="fecha-creacion">Datos de Cotizaci&oacute;n</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="nombre-cliente">Nombre del cliente</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control text-center input-form" id="id-cliente" name="id-cliente" type="hidden" value="0" />
                    <input class="form-control text-center input-form" id="nombre-cliente" name="nombre-cliente" placeholder="Nombre del cliente" type="text" oninput="aucompletarCliente()" />
                    <div id="nombre-cliente-errors">
                    </div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="email-cliente1">Correo de envio</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control text-center input-form correo-cotizacion" id="email-cliente1" name="email-cliente1" placeholder="Correo del cliente" type="text" oninput="aucompletarCorreo()" />
                    <div id="email-cliente2-errors">
                    </div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="email-cliente2">Correo adicional de envio</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="form-control text-center input-form correo-cotizacion" id="email-cliente2" name="email-cliente2" placeholder="Correo del cliente" type="text" oninput="aucompletarCorreo()" />
                    <div id="email-cliente2-errors">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="email-cliente3">Correo adicional de envio No.2</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="form-control text-center input-form correo-cotizacion" id="email-cliente3" name="email-cliente3" placeholder="Correo del cliente" type="text" />
                    <div id="email-cliente3-errors">
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="tipo-comprobante">Tipo comprobante</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="tipo-comprobante" name="tipo-comprobante">
                        <option value="" id="option-default-tipo-comprobante">- - - -</option>
                        <optgroup id="tipo-comprobante" class="contenedor-tipo-comprobante text-start"> </optgroup>
                    </select>
                    <div id="tipo-comprobante-errors"></div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="id-metodo-pago">M&eacute;todo de pago</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-metodo-pago" name="id-metodo-pago" onchange="checkMetodopago();">
                        <option value="" id="option-default-metodo-pago">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-metodo-pago text-start"> </optgroup>
                    </select>
                    <div id="id-metodo-pago-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-md-4 py-2">
                <label class="label-form text-right" for="id-forma-pago">Forma de pago</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class=" form-group">
                    <select class="form-select text-center input-form" id="id-forma-pago" name="id-forma-pago">
                        <option value="" id="option-default-forma-pago">- - - -</option>
                        <optgroup id="forma-pago" class="contenedor-forma-pago text-start"> </optgroup>
                    </select>
                    <div id="id-forma-pago-errors"></div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="id-moneda">Moneda</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-moneda" name="id-moneda">
                        <option value="" id="option-default-moneda">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-moneda text-start"> </optgroup>
                    </select>
                    <div id="id-moneda-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="id-uso">Uso CFDI</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-uso" name="id-uso">
                        <option value="" id="option-default-uso">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-uso text-start"> </optgroup>
                    </select>
                    <div id="id-uso-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="observaciones">Observaciones</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <textarea rows="3" id="observaciones" class="form-control input-form" placeholder="Observaciones sobre la cotizaci&oacute;n" style="height: 80px;"></textarea>
                </div>
                <div id="observaciones-errors">
                </div>
            </div>

            <div class="col-md-2 py-2">
                <label class="label-form text-right mb-2" for="chfirma">Firmar</label>
                <div class="form-group">
                    <input class="input-check" id="chfirma" name="chfirma" type="checkbox" />
                    <div id="chfirma-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 py-2">
                <label class="label-form text-right" for="fecha-creacion">Realiz&oacute;:</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input class="form-control text-center input-form" disabled id="realizo" name="realizo" placeholder="Usuario que realiz&oacute;" type="text" />
                    <div id="realizo-errors">
                    </div>
                </div>
            </div>
        </div>
        <!--FECHA DEL 9 DE ABRIL 2024- POSIBLE CAMBIOS
            <div class="row scrollX">
            <div class="col-md-12">
                <a href="#cfdirel" data-bs-toggle='collapse' class="label-sub click-row text-decoration-none">Agregar mano de obra/ otros conceptos <span class="fas fa-chevron-down"></span></a>
                <div id="cfdirel" class="panel-collapse collapse">
                    <table class="table table-hover table-condensed table-responsive table-row table-head">
                        <tbody>
                            <tr class='align-middle'>
                                <td colspan="2">
                                    <label class="label-form text-right mb-1" for="descripcion-mano">Descripci&oacute;n</label>
                                    <input class='form-control text-center input-form col-12' id="descripcion-mano" name='descripcion-mano' placeholder='Descripci&oacute;n del concepto' type='text' value="Mano de obra" />
                                    <div id="descripcion-mano-errors"></div>
                                </td>
                                <td colspan="2">
                                    <label class="label-form text-right mb-1" for="clave-fiscal">Clave fiscal</label>
                                    <input class='form-control text-center input-form col-12' id="clave-fiscal" name="clave-fiscal" placeholder='Clave fiscal del producto' type='text' value="01010101-No existe en el catálogo" oninput="aucompletarCatalogo()" />
                                    <div id="clave-fiscal-errors"></div>
                                </td>
                                <td colspan="2">
                                    <label class="label-form text-right mb-1" for="clave-unidad">Clave unidad</label>
                                    <input class='form-control text-center input-form col-12' id='clave-unidad' name='clave-unidad' placeholder='Clave de la unidad del producto' type='text' value="E48-Unidad de servicio" oninput="aucompletarUnidad()" />
                                    <div id="clave-unidad-errors"></div>
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="cant-obra">Cantidad</label>
                                    <input class='form-control text-center input-form col-12' id="cant-obra" name='cant-obra' placeholder='Cantidad' type='number' value="1" oninput="calcularImporteObra()" />
                                    <div id="cant-obra-errors"></div>
                                </td>
                            </tr>
                            <tr class='align-middle'>
                                <td>
                                    <label class="label-form text-right mb-1" for="precio-venta">Precio de venta</label>
                                    <input class='form-control text-center input-form' id='precio-venta' name='total-factura' placeholder='Precio del concepto' type='number' step="any" value="0" oninput="calcularImporteObra()" />
                                    <div id="precio-venta-errors"></div>
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="importe-obra">Importe</label>
                                    <input class='form-control text-center input-form' id='importe-obra' disabled name='importe-obra' placeholder='Importe del Concepto' type='number' step="any" value="0" />
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="por-descuento">Descuento %</label>
                                    <input class='form-control text-center input-form' id='por-descuento' name='por-descuento' placeholder='Porcentaje de descuento' type='number' step="any" value="0" oninput="calcularDescuentoObra()" />
                                    <input class='form-control text-center input-form' id='importe-descuento' name='importe-descuento' type='hidden' />
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="traslados">Traslados</label>
                                    <div class='input-group'>
                                        <div class='dropdown'>
                                            <button type='button' class='button-impuesto btn dropdown-toggle' data-bs-toggle='dropdown'>Traslados <span class='caret'></span></button>
                                            <ul class='dropdown-menu' id="traslados-option">
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="retencion">Retenciones</label>
                                    <div class='input-group'>
                                        <div class='dropdown'>
                                            <button type='button' class='button-impuesto btn dropdown-toggle' data-bs-toggle='dropdown'>Retenciones <span class='caret'></span></button>
                                            <ul class='dropdown-menu' id="retencion-option">
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="total-obra">Total</label>
                                    <input class='form-control text-center input-form' id='total-obra' name='total-obra' placeholder='Total a pagar' type='number' step="any" disabled value="0" />
                                </td>
                                <td>
                                    <label class="label-form text-right mb-1" for="btn-agregar-concepto">Agregar</label><br />
                                    <button id="btn-agregar-concepto" class='btn button-list-add col-12' onclick='agregarManoObra();'><span class='fas fa-plus'></span></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>-->
        <div class="row mt-5">
            <div class="col-md-4 py-2">
                <label class="label-sub" for="fecha-creacion">Conceptos</label>
            </div>
            <div class="col-md-8 text-end">
                <button id="btn-nuevo-productos" type="button" class="button-modal" data-bs-toggle="modal" data-bs-target="#nuevo-producto" onclick="setCamposProducto();">
                    <span class="fas fa-plus"></span> Nuevo producto / servicio
                </button>
                <button id="btn-agregar-productos" type="button" class="button-modal" data-bs-toggle="modal" data-bs-target="#myModal">
                    Agregar conceptos <span class="fas fa-search"></span>
                </button>

            </div>
        </div>
        <div class="row scrollX">
            <table id="resultados" class="table table-hover table-condensed table-responsive table-row table-head">
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-end" id="btns">
                <button class="button-form btn btn-danger" onclick="cancelarCotizacion();">Cancelar <span class="fas fa-times"></span></button> &nbsp;
                <button class="button-form btn btn-primary" onclick="insertarCotizacion();" id="btn-form-cotizacion">Guardar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
</div>
<script src="js/scriptcotizacion.js"></script>