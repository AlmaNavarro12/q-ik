<?php
include("buscarProductos.php");
?>
<div id="form-factura" style="height: 100%;">
    <div class="col-md-12">
        <div class="titulo-lista" id="contenedor-titulo-form-factura">Nueva factura </div>
    </div>
    <div class="div-form  p-5 border border-secondary-subtle">
        <div class="row" id="not-timbre">

        </div>
        <div class="row">
            <div class="col-md-8">
                <label class="label-sub">Datos del Emisor</label>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right mb-2" for="fecha-creacion">Fecha de creaci&oacute;n</label>
                <div class=" form-group">
                    <input class="input-form text-center form-control mb-3" disabled id="fecha-creacion" name="fecha-creacion" placeholder="Fecha Actual" type="text" />
                    <div id="fecha-creacion-errors">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2" for="folio">Folio</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="folio" name="folio">
                        <option value="" id="option-default-folio">- - - - -</option>
                        <optgroup id="foliofactura" class="contenedor-folios text-start"> </optgroup>
                    </select>
                    <div id="folio-errors"></div>
                </div>
            </div>
            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2" for="datos-facturacion">Datos de facturaci&oacute;n</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="datos-facturacion" name="datos-facturacion" onchange="loadDatosFactura();">
                        <option value="" id="option-default-datos">- - - -</option>
                        <optgroup id="datosfacturar" class="contenedor-datos text-start"> </optgroup>
                    </select>
                    <div id="datos-facturacion-errors"></div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="label-form text-right mb-2" for="rfc-emisor">RFC</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="rfc-emisor" name="rfc-emisor" placeholder="RFC Emisor" type="text" />
                    <div id="rfc-emisor-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="label-form text-right mt-3 mb-2" for="razon-emisor">Raz&oacute;n social</label>
                <div class="form-group">
                    <input class="input-form text-center form-control mb-3 " disabled id="razon-emisor" name="razon-emisor" placeholder="Razon Social Emisor" type="text" />
                    <div id="razon-emisor-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right  mt-3 mb-2" for="regimen-emisor">R&eacute;gimen fiscal</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="regimen-emisor" name="regimen-emisor" placeholder="R&eacute;gimen fiscal" type="text" />
                    <div id="regimen-emisor-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right mt-3 mb-2" for="cp-emisor">C&oacute;digo postal</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="cp-emisor" name="cp-emisor" placeholder="C&oacute;digo postal" type="text" />
                    <div id="cp-emisor-errors"></div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label class="label-sub">Datos del Receptor</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-end mb-2">
                <label class="fw-bold text-danger small">* Campo obligatorio</label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 py-1">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right mb-2" for="nombre-cliente">Cliente</label><label class="mark-required text-danger fw-bold">*</label> <span class="small text-primary-emphasis fas fa-question-circle"></span>
                    <span class="tiptext">Puede realizar la b&uacute;squeda por Nombre, Apellidos, Empresa o RFC de un cliente que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realiz&oacute; registro puede dejar este campo en blanco e ingresar los datos necesarios.</span>
                </div>
                <div class="form-group">
                    <input type="hidden" id="id-cliente" />
                    <input type="text" class="form-control input-form mb-3" id="nombre-cliente" placeholder="Buscar cliente (Nombre, Empresa o RFC cliente)" oninput="autocompletarCliente()" />
                    <div id="nombre-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2" for="rfc-cliente">RFC Cliente</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="rfc-cliente" placeholder="RFC del cliente" onblur="getClientebyRFC();" />
                    <div id="rfc-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2" for="razon-cliente">Raz&oacute;n social del cliente</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="razon-cliente" placeholder="Raz&oacute;n social del cliente" />
                    <div id="razon-cliente-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2 " for="regfiscal-cliente">R&eacute;gimen fiscal del cliente</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form mb-3" id="regfiscal-cliente" placeholder="R&eacute;gimen fiscal del cliente" oninput="aucompletarRegimen();" />
                    <div id="regfiscal-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2" for="direccion-cliente">Direcci&oacute;n del cliente</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="direccion-cliente" placeholder="Direcci&oacute;n del cliente" />
                    <div id="direccion-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-1">
                <label class="label-form text-right mb-2 " for="cp-cliente">C&oacute;digo postal del cliente</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="cp-cliente" placeholder="C&oacute;digo postal del cliente" />
                    <div id="cp-cliente-errors"></div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="label-sub">Datos de Factura</label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 py-2">
                <label class="label-form text-right mb-2" for="tipo-comprobante">Tipo comprobante</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="tipo-comprobante" name="tipo-comprobante" onchange="checkFolios();">
                        <option value="" id="option-default-tipo-comprobante">- - - -</option>
                        <optgroup id="tipo-comprobante" class="contenedor-tipo-comprobante text-start"> </optgroup>
                    </select>
                    <div id="tipo-comprobante-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right mb-2" for="id-metodo-pago">M&eacute;todo de pago</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-metodo-pago" name="id-metodo-pago" onchange="checkMetodopago();">
                        <option value="" id="option-default-metodo-pago">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-metodo-pago text-start"> </optgroup>
                    </select>
                    <div id="id-metodo-pago-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right mb-2" for="id-forma-pago">Forma de pago</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-forma-pago" name="id-forma-pago">
                        <option value="" id="option-default-forma-pago">- - - -</option>
                        <optgroup id="forma-pago" class="contenedor-forma-pago text-start"> </optgroup>
                    </select>
                    <div id="id-forma-pago-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 py-2">
                <label class="label-form text-right mb-2" for="id-moneda">Moneda</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form mb-3" id="id-moneda" name="id-moneda" onchange="getTipoCambioSinTag()">
                        <option value="" id="option-default-moneda">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-moneda text-start"> </optgroup>
                    </select>
                    <div id="id-moneda-errors"></div>
                </div>
            </div>

            <div class="col-md-2 py-2">
                <label class="label-form text-right mb-2" for="tipo-cambio">Tipo de cambio</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="tipo-cambio" placeholder="Tipo de cambio" disabled="">
                    <div id="tipo-cambio-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right mb-2" for="id-uso">Uso CFDI</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-uso" name="id-uso">
                        <option value="" id="option-default-uso">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-uso text-start"> </optgroup>
                    </select>
                    <div id="id-uso-errors"></div>
                </div>
            </div>

            <div class="col-md-4 py-2">
                <label class="label-form text-right mb-2" for="chfirma">Firmar</label>
                <div class="form-group">
                    <input class="input-check" id="chfirma" name="chfirma" type="checkbox" />
                    <div id="chfirma-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right mb-2" for="periodicidad-factura">Periodicidad </label> <span class="fas fa-question-circle small text-primary-emphasis"></span>
                    <span class="tiptext">Los datos de periodicidad, Meses y A&ntilde;o pertenecen a los datos de informacion global, estos datos solo son necesarios al crear una factura para el publico en general.</span>
                </div>
                <div class="form-group">
                    <select class="form-select text-center input-form mb-3" id="periodicidad-factura" name="periodicidad-factura">
                        <option value="" id="option-default-periodicidad-factura">- - - -</option>
                        <optgroup id="tipo-comprobante" class="contenedor-pglobal text-start"> </optgroup>
                    </select>
                    <div id="periodicidad-factura-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right mb-2" for="mes-periodo">Mes periodicidad</label> <span class="fas fa-question-circle small text-primary-emphasis"></span>
                    <span class="tiptext">Se debe registrar la clave del mes o los meses al que corresponde la información de las operaciones celebradas con el público en general</span>
                </div>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="mes-periodo" name="mes-periodo">
                        <option value="" id="option-default-mes-periodo">- - - -</option>
                        <optgroup id="periodo-mes" class="contenedor-mes text-start"> </optgroup>
                    </select>
                    <div id="mes-periodo-errors"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right mb-2" for="anho-periodo">A&ntilde;o periodicidad</label> <span class="fas fa-question-circle small text-primary-emphasis"></span>
                    <span class="tiptext">Se debe registrar el año al que corresponde la información del comprobante global. El valor registrado debe ser igual al año en curso o al año inmediato anterior considerando el registrado en la Fecha de emisión del comprobante.</span>
                </div>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="anho-periodo" name="anho-periodo">
                        <option value="" id="option-default-anho-periodo">- - - -</option>
                        <optgroup id="periodo-anho" class="contenedor-ano text-start"> </optgroup>
                    </select>
                    <div id="anho-periodo-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <a href="#cfdirel" class="label-sub text-decoration-none" data-bs-toggle="collapse">Agregar CFDIS Relacionados <span class="far fa-caret-square-down"></span></a>
                <div class="collapse" id="cfdirel">
                    <table class="table table-hover table-condensed table-responsive table-row table-head">
                        <thead>
                            <tr>
                                <th class='text-center'>Folio <span class="fas fa-sort-alpha-down"></span></th>
                                <th class='text-center'>CFDI <span class="fas fa-sort-alpha-down"></span></th>
                                <th class='text-center'>Tipo de relación <span class="fas fa-sort-alpha-down"></span></th>
                                <th class='text-center'>Agregar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-md-2">
                                    <input type="text" class="form-control cfdi input-form" id="folio-relacion" oninput="aucompletarFacturaTimbrada()" placeholder="Folio">
                                    <input type="hidden" id="type-rel" value="">
                                    <input type="hidden" id="idfactura-rel" value="">
                                </td>
                                <td><input type="text" class="form-control cfdi input-form" id="cfdi-rel" placeholder="00000000-0000-0000-0000-000000000000"></td>
                                <td class="col-md-5">
                                    <select class="form-select text-center input-form" id="tipo-relacion" name="tipo-relacion">
                                        <option value="" id="option-default-tipo-relacion">- - - -</option>
                                        <optgroup id="relacion" class="contenedor-relacion text-start"> </optgroup>
                                    </select>
                                </td>
                                <td class="text-center"><button id="btn-agregar-cfdi" class="btn button-list" onclick="addCFDI();"><span class="fas fa-plus"></span></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="border border-secondary-emphasis shadow-sm pt-3 pb-3 px-5 rounded" id="tablaresultados" style="display:none;">
                    <table class="table table-primary table-responsive table-row table-head" id="body-lista-cfdi"></table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="idticket" id="idticket">
        <div class="row mt-5">
            <div class="col-md-4">
                <label class="label-sub">Conceptos</label>
            </div>
            <div class="col-md-8" id="btnprod" style="text-align: right !important;">
                <button id="btn-nuevo-producto" type="button" class="button-modal" data-bs-toggle="modal" data-bs-target="#nuevo-producto" onclick="setCamposProducto();">
                    <span class="fas fa-plus"></span> Nuevo producto
                </button>
                <button id="btn-agregar-productos" type="button" class="button-modal" data-bs-toggle="modal" data-bs-target="#myModal">
                    Agregar conceptos <span class="fas fa-search"></span>
                </button>
            </div>
        </div>
        
        <div class="row scrollX" style="max-width: 100%;">
            <table id="resultados" class="table tab-hover table-condensed table-responsive table-row table-head mt-3">
            </table>
        </div>
        <div class="row">
            <div class="d-flex justify-content-end mt-3">
                <button class="button-form btn btn-danger me-2 " onclick="cancelarFactura()">Cancelar <span class="fas fa-times"></span></button> &nbsp;
                <button class="button-form btn btn-primary " onclick="gestionarFactura()" id="btn-form-factura">Guardar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
</div>
    <script src="js/scriptfactura.js"></script>
    <script>
        $(document).ready(function() {
            var cleave = new Cleave('.cfdi', {
                delimiter: '-',
                blocks: [8, 4, 4, 4, 12]
            });

        });
    </script>