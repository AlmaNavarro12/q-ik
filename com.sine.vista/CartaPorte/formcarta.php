<?php
include("buscarProductos.php");
?>
<form action="com.sine.enlace/enlacecarta.php" onsubmit="return false;" id="form-carta">
    <div class="col-md-12"><div class="titulo-lista" id="contenedor-titulo-form-carta">Nueva Factura complemento Carta Porte </div> </div>
    <div id="div-space">
    </div>

    <div id="div-tab">
        <div class="row" id="menu-button">
            <div class='col-md-12' id="div-folio-conf">
                <button id="tab-factura" class='button-tab tab-active' data-tab="div-factura" name="tab">Factura <span class='lnr lnr-book icon-size'></span></button>
                <button id="tab-carta" class='button-tab' data-tab="div-carta" name="tab">Carta Porte <span class='glyphicon glyphicon-file icon-size'></span></button>
            </div>
        </div>
    </div>

    <div class="div-form" id="div-factura">
        <div class="row not-timbre">

        </div>
        <div class="row">
            <div class="col-md-8">
                <label class="label-sub">Datos del Emisor</label>
            </div>
            <div class="col-md-4">
                <label class="label-form text-right" for="fecha-creacion">Fecha de creacion</label>
                <div class=" form-group">
                    <div>
                        <input class="input-form text-center form-control" disabled id="fecha-creacion" name="fecha-creacion" placeholder="Fecha Actual" type="text"/>
                    </div>
                    <div id="fecha-creacion-errors">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="label-form text-right" for="folio">Folio</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="folio" name="folio">
                        <option value="" id="option-default-folio">- - - -</option>
                        <optgroup id="foliofactura" class="contenedor-folios text-left"> </optgroup>
                    </select>
                    <div id="folio-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="datos-facturacion">Datos de Facturacion</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="datos-facturacion" name="datos-facturacion" onchange="loadFolioCarta();">
                        <option value="" id="option-default-datos">- - - -</option>
                        <optgroup id="datosfacturar" class="contenedor-datos text-left"> </optgroup>
                    </select>
                    <div id="datos-facturacion-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="rfc-emisor">RFC</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="rfc-emisor" name="rfc-emisor" placeholder="RFC Emisor" type="text"/>
                    <div id="rfc-emisor-errors"></div>
                </div>
            </div>


        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="label-form text-right" for="razon-emisor">Razon Social</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="razon-emisor" name="razon-emisor" placeholder="Razon Social Emisor" type="text"/>
                    <div id="razon-emisor-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="regimen-emisor">Regimen Fiscal</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="regimen-emisor" name="regimen-emisor" placeholder="Regimen fiscal" type="text"/>
                    <div id="regimen-emisor-errors"></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <label class="label-form text-right" for="cp-emisor">Codigo Postal</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="cp-emisor" name="cp-emisor" placeholder="Codigo Postal" type="text"/>
                    <div id="cp-emisor-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="label-sub">Datos del Receptor</label>
            </div>
            <div class="col-md-6 text-right">
                <label class="mark-required text-right">*</label> <label class="label-required text-right"> Campo Obligatorio</label>
            </div>
        </div>        

        <div class="row">
            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right" for="nombre-cliente">Cliente</label> <span class="glyphicon glyphicon-question-sign"></span>
                    <span class="tiptext">Puede realizar la busqueda por Nombre, Apellidos, Empresa o RFC de un cliente que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro puede dejar este campo en blanco e ingresar los datos necesarios.</span>
                </div>
                <div class="form-group">
                    <input type="hidden" id="id-cliente"/>
                    <input type="text" class="form-control input-form" id="nombre-cliente" placeholder="Buscar cliente (Nombre, Empresa o RFC cliente)" oninput="autocompletarCliente()"/>
                    <div id="nombre-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="rfc-cliente">RFC Cliente</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="rfc-cliente" placeholder="RFC del cliente" onblur="getClientebyRFC();"/>
                    <div id="rfc-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="razon-cliente">Razon Social del Cliente</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="razon-cliente" placeholder="Razon social del cliente"/>
                    <div id="razon-cliente-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="label-form text-right" for="regfiscal-cliente">Regimen Fiscal del cliente</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="regfiscal-cliente" placeholder="Registro fiscal del cliente" oninput="aucompletarRegimen();"/>
                    <div id="regfiscal-cliente-errors"></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <label class="label-form text-right" for="direccion-cliente">Direccion del Cliente</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="direccion-cliente" placeholder="Direccion del cliente"/>
                    <div id="direccion-cliente-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="cp-cliente">Codigo Postal del Cliente</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="cp-cliente" placeholder="Codigo Postal del cliente"/>
                    <div id="cp-cliente-errors"></div>
                </div>
            </div>            
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="label-sub">Datos de Factura</label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="label-form text-right" for="tipo-comprobante">Tipo Comprobante</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="tipo-comprobante" name="tipo-comprobante" >
                        <option value="" id="option-default-tipo-comprobante">- - - -</option>
                        <optgroup id="tipo-comprobante" class="contenedor-tipo-comprobante text-left"> </optgroup>
                    </select>
                    <div id="tipo-comprobante-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="id-metodo-pago">Metodo de Pago</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="id-metodo-pago" name="id-metodo-pago" onchange="checkMetodopago();" >
                        <option value="" id="option-default-metodo-pago">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-metodo-pago text-left"> </optgroup>
                    </select>
                    <div id="id-metodo-pago-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="id-forma-pago">Forma de Pago</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <div>
                        <select class="form-control text-center input-form" id="id-forma-pago" name="id-forma-pago">
                            <option value="" id="option-default-forma-pago">- - - -</option>
                            <optgroup id="forma-pago" class="contenedor-forma-pago text-left"> </optgroup>
                        </select>
                    </div>
                    <div id="id-forma-pago-errors"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label class="label-form text-right" for="id-moneda">Moneda</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <div>
                        <select class="form-control text-center input-form" id="id-moneda" name="id-moneda" onchange="getTipoCambio()">
                            <option value="" id="option-default-moneda">- - - -</option>
                            <optgroup id="metodo-pago" class="contenedor-moneda text-left"> </optgroup>
                        </select>
                    </div>
                    <div id="id-moneda-errors"></div>
                </div>
            </div>

            <div class="col-md-2">
                <label class="label-form text-right" for="tipo-cambio">Tipo de Cambio</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="tipo-cambio" placeholder="Tipo de cambio de Moneda" disabled="">
                    <div id="tipo-cambio-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="id-uso">Uso CFDI</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="id-uso" name="id-uso" >
                        <option value="" id="option-default-uso">- - - -</option>
                        <optgroup id="metodo-pago" class="contenedor-uso text-left"> </optgroup>
                    </select>
                    <div id="id-uso-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-form text-right" for="chfirma">Firmar?</label>
                <div class="form-group">
                    <input class="input-check" id="chfirma" name="chfirma" type="checkbox"/>
                    <div id="chfirma-errors"></div>
                </div> 
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right" for="periodicidad-factura">Periodicidad </label> <span class="glyphicon glyphicon-question-sign"></span>
                    <span class="tiptext">Los datos de periodicidad, Meses y A&ntilde;o pertenecen a los datos de informacion global, estos datos solo son necesarios al crear una factura para el publico en general.</span>
                </div>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="periodicidad-factura" name="periodicidad-factura">
                        <option value="" id="option-default-periodicidad-factura">- - - -</option>
                        <optgroup id="tipo-comprobante" class="contenedor-pglobal text-left"> </optgroup>
                    </select>
                    <div id="periodicidad-factura-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right" for="mes-periodo">Mes Periodicidad</label> <span class="glyphicon glyphicon-question-sign"></span>
                    <span class="tiptext">Se debe registrar la clave del mes o los meses al que corresponde la información de las operaciones celebradas con el público en general</span>
                </div>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="mes-periodo" name="mes-periodo">
                        <option value="" id="option-default-mes-periodo">- - - -</option>
                        <optgroup id="periodo-mes" class="contenedor-meses text-left"> </optgroup>
                    </select>
                    <div id="mes-periodo-errors"></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="new-tooltip icon tip">
                    <label class="label-form text-right" for="anho-periodo">A&ntilde;o Periodicidad</label> <span class="glyphicon glyphicon-question-sign"></span>
                    <span class="tiptext">Se debe registrar el año al que corresponde la información del comprobante global. El valor registrado debe ser igual al año en curso o al año inmediato anterior considerando el registrado en la Fecha de emisión del comprobante.</span>
                </div>
                
                <div class="form-group">
                    <select class="form-control text-center input-form" id="anho-periodo" name="anho-periodo">
                        <option value="" id="option-default-anho-periodo">- - - -</option>
                        <optgroup id="periodo-anho" class="contenedor-ano text-left"> </optgroup>
                    </select>
                    <div id="anho-periodo-errors"></div>
                </div>
            </div>
        </div>
		<!-- AGREGAR CFDIS RELACIONADOS -->
		<div class="row">
            <div class="col-md-12">
                <a href="#cfdirel" data-toggle='collapse' class="label-sub">Agregar CFDIS Relacionados <span class="glyphicon glyphicon-collapse-down"></span></a>
                <div id="cfdirel" class="panel-collapse collapse">
                    <table class="table tab-hover table-condensed table-responsive table-row table-head">
                        <thead>
                            <tr>
                                <th>Tipo de Relacion <span class="glyphicon glyphicon-sort-by-alphabet"></span></th>
                                <th>CFDI <span class="glyphicon glyphicon-sort-by-alphabet"></span></th>
                                <th>Agregar <span class="glyphicon glyphicon-plus"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-md-5">
                                    <select class="form-control text-center input-form" id="tipo-relacion" name="tipo-relacion" >
                                        <option value="" id="option-default-tipo-relacion">- - - -</option>
                                        <optgroup id="relacion" class="contenedor-relacion text-left"> </optgroup>
                                        
                                    </select>
                                </td>
                                <td><input type="text" class="form-control cfdi input-form" id="cfdi-rel" placeholder="00000000-0000-0000-0000-000000000000"></td>
                                <td class="text-center"><button id="btn-agregar-cfdi" class='btn button-list' onclick='addCFDI();'><span class='glyphicon glyphicon-plus'></span> </button></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-cfdi">

                    </table>
                </div>
            </div>
        </div>
		<!-- FIN AGREGAR CFDIS RELACIONADOS -->

        <div class="row">
            <div class="col-md-4">
                <label class="label-sub">Conceptos</label>
            </div>
            <div class="col-md-8 text-right" id="btnprod">
                <button id="btn-nuevo-producto" type="button" class="button-agregar" data-toggle="modal" data-target="#nuevo-producto" onclick="setCamposProducto();">
                    <span class="glyphicon glyphicon-plus"></span> Nuevo Producto
                </button>
                <button id="btn-agregar-productos" type="button" class="button-agregar" data-toggle="modal" data-target="#myModal">
                    Agregar Conceptos <span class="glyphicon glyphicon-search "></span>
                </button>
            </div>
        </div>

        <div class="row scrollX">
            <table id="resultados" class="table tab-hover table-condensed table-responsive table-row table-head">

            </table>
        </div>
        <div class="row">
            <div class="col-md-12 text-right" id="btns"> 
                <button class="button-form btn-danger " onclick="cancelarCarta();" >Cancelar <span class="glyphicon glyphicon-remove"></span></button> &nbsp;
                <button class="button-form btn-info next-prev" data-nav="top" data-tab="carta" id="btn-form-next1">Siguiente <span class="glyphicon glyphicon-forward"></span></button>
            </div>	
        </div>
    </div>

    <div class="div-form" id="div-carta" hidden>
        <div class="row not-timbre">

        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                <label class="mark-required text-right">*</label> <label class="label-required text-right"> Campo Obligatorio</label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label class="label-form text-right" for="version-carta">Version</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="version-carta" name="version-carta" placeholder="Version Carta Porte" value="3.0" type="text"/>
                    <div id="version-carta-errors">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="label-form text-right" for="transporte-internacional">Transporte Internacional</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="transporte-internacional" name="transporte-internacional" placeholder="Transporte Internacional" value="No" type="text"/>
                    <div id="transporte-internacional-errors">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="label-form text-right" for="clave-transporte">Clave de Transporte</label>
                <div class="form-group">
                    <input class="input-form text-center form-control" disabled id="clave-transporte" name="clave-transporte" placeholder="Clave de Transporte" value="01-Autotransporte Federal" type="text"/>
                    <div id="clave-transporte-errors">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="label-form text-right" for="tipo-movimiento">Entrada/Salida Mercancia</label> <label class="mark-required text-right">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="tipo-movimiento" name="tipo-movimiento" >
                        <option value="" id="option-default-tipo-movimiento">- - - -</option>
                        <optgroup id="movimientos" class="contenedor-movimiento text-left"> 
                            <option value="1" id="mov1">Entrada</option>
                            <option value="2" id="mov2">Salida</option>
                        </optgroup>
                    </select>
                    <div id="tipo-movimiento-errors">
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="menu-button">
            <div class='col-md-12'>
                <button id="tab-mercancia" class='sub-button-tab sub-tab-active' data-tab="mercancia" name="tab">Mercancia <span class='glyphicon glyphicon-list icon-size'></span></button>
                <button id="tab-transporte" class='sub-button-tab' data-tab="transporte" name="tab">Transporte <span class='fa fa-truck icon-size'></span></button>
                <button id="tab-ubicacion" class='sub-button-tab' data-tab="ubicacion" name="tab">Ubicaciones <span class='glyphicon glyphicon-map-marker icon-size'></span></button>
                <button id="tab-operador" class='sub-button-tab' data-tab="operador" name="tab">Operadores <span class='fa fa-user icon-size'></span></button>
                <button id="tab-evidencia" class='sub-button-tab' data-tab="evidencia" name="tab">Evidencias <span class='fa fa-file icon-size'></span></button>
            </div>
        </div>
        <br/>

        <div id="sub-mercancia" class="sub-div">
            <div class="row">
                <div class="col-md-12">
                    <label class="label-sub">Mercancia</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <label class="label-form text-right" for="titulo">Buscar en: </label>
                        <div class="form-group">
                            <label class="label-radio">
                                <input class="input-radio" type="radio" id="busqueda1" name="busqueda" value="1" checked=""> Producto ya registrado
                            </label>
                            <label class="label-radio">
                                <input class="input-radio" type="radio" id="busqueda2" name="busqueda" value="2"> Producto sin registrar
                            </label>
                        </div>

                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="clv-producto">Clv/Nombre mercancia <span class="glyphicon glyphicon-question-sign"></span></label>
                        <span class="tiptext">Puede realizar la busqueda por Clave o Descripcion Fiscal de un producto que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del producto puede seleccionar la opcion "Producto sin registrar" y se realizara la busqueda en el catalogo de productos del SAT</span>
                    </div>
                    <div class="form-group">
                        <input id="peligro-mercancia" name='peligro-mercancia' type='hidden'/>
                        <input class='form-control text-center input-form' id="clv-producto" name='clv-producto' placeholder='Buscar Producto' type='text' oninput="autocompletarMercancia();"/>
                        <div id="clv-producto-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="descripcion-mercancia">Descripcion </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="descripcion-mercancia" name="descripcion-mercancia" placeholder="Descripcion de la mercancia"/>
                        <div id="descripcion-mercancia-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="cantidad-mercancia">Cantidad </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='cantidad-mercancia' name='cantidad-mercancia' placeholder='Cantidad a enviar' type='number' step="any" value="1"/>
                        <div id="cantidad-mercancia-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="unidad-mercancia">Clv Unidad </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='unidad-mercancia' name='unidad-mercancia' placeholder='Clave unidad de transporte' type='text' oninput="aucompletarUnitMercancia();"/>
                        <div id="unidad-mercancia-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="peso-mercancia">Peso en Kg </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='peso-mercancia' name='peso-mercancia' placeholder='Peso de la mercancia a transportar' type='number' step="any" value="0"/>
                        <div id="peso-mercancia-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="material-peligroso">Material Peligroso </label>
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="material-peligroso" name="material-peligroso" disabled>
                            <option value="" id="option-default-material-peligroso">- - - -</option>
                            <optgroup id="peligros" class="contenedor-peligro text-left"> 
                                <option value="0" id="peligro0">No</option>
                                <option value="1" id="peligro1">Si</option>
                            </optgroup>
                        </select>
                        <div id="material-peligroso-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="clv-peligro">Clv Material Peligroso </label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='clv-peligro' name='clv-peligro' placeholder='Clave Material Peligroso' type='text' oninput="autocompletarMaterialPeligroso();" disabled/>
                        <div id="clv-peligro-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="clv-embalaje">Embalaje </label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='clv-embalaje' name='clv-embalaje' placeholder='Clave embalaje de la mercancia' type='text' oninput="autocompletarEmbalaje();" disabled/>
                        <div id="clv-embalaje-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="btn-agregar-mercancia">Agregar </label>
                    <div class="form-group">
                        <button title="Agregar mercancia" id="btn-agregar-mercancia" class='button-add-prod' onclick='agregarMercancia();'><span class='glyphicon glyphicon-plus'></span></button>
                    </div>
                </div>
            </div>

            <div class="row scroll-table">
                <table id="resultmercancia" class="table tab-hover table-condensed table-responsive table-row table-head">

                </table>
            </div>
        </div>

        <div id="sub-transporte" class="sub-div" hidden>
            <div class="row">
                <div class="col-md-12">
                    <label class="label-sub">Autotransporte Federal</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="nombre-vehiculo">Nombre Vehiculo</label> <span class="glyphicon glyphicon-question-sign" title="Puede realizar la busqueda por nombre o placa de un vehiculo que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del vehiculo puede dejar este campo en blanco e ingresar los datos del vehiculo."></span>
                        <span class="tiptext">Puede realizar la busqueda por nombre o placa de un vehiculo que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del vehiculo puede dejar este campo en blanco e ingresar los datos del vehiculo.</span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="" id="id-vehiculo" name="id-vehiculo"/>
                        <input type="text" class="form-control input-form" id="nombre-vehiculo" name="nombre-vehiculo" placeholder="Buscar vehiculo" oninput="autocompletarVehiculo();" title="Puede realizar la busqueda por nombre o placa de un vehiculo que ya haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del vehiculo puede dejar este campo en blanco e ingresar los datos del vehiculo."/>
                        <div id="nombre-vehiculo-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="num-permiso">Numero Permiso </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="num-permiso" placeholder="Numero de Permiso otorgado por la SCT"/>
                        <div id="num-permiso-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="tipo-permiso">Tipo permiso</label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="tipo-permiso" placeholder="Tipo de Permiso otorgado por la SCT" oninput="aucompletarPermiso();"/>
                        <div id="tipo-permiso-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="conf-transporte">Tipo Autotransporte </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="conf-transporte" placeholder="Clave Tipo de Vehiculo" oninput="aucompletarConfigTransporte();"/>
                        <div id="calle-destino-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="anho-modelo">A&ntilde;o Modelo Vehiculo</label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="anho-modelo" placeholder="Año del modelo del Vehiculo"/>
                        <div id="anho-modelo-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="placa-vehiculo">Placa Vehiculo</label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="placa-vehiculo" placeholder="Placa del Vehiculo (sin espacios ni guiones)" onblur="checkVehiculo();"/>
                        <div id="placa-vehiculo-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="seguro-respcivil">Nombre Aseguradora Resp. Civil</label><label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="seguro-respcivil" placeholder="Aseguradora que cubre riesgos por responsabilidad civil"/>
                        <div id="seguro-respcivil-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="poliza-respcivil">Numero de Poliza</label><label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="poliza-respcivil" placeholder="Numero de poliza de seguro por responsabilidad civil"/>
                        <div id="poliza-respcivil-errors"></div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="peso-vehiculo">Peso Vehicular</label><label class="mark-required text-right">*</label> <span class="glyphicon glyphicon-question-sign" ></span>
                        <span class="tiptext">Es el peso del vehiculo en toneladas (t).</span>
                    </div>
                    <div class="form-group">
                        <!--<input type="float" class="form-control input-form" id="peso-vehiculo" onchange="obtenerPesoBrutoVehicular()"/>-->
                        <input type="text" onkeypress="return filterFloat(event,this);" class="form-control input-form" id="peso-vehiculo" onchange="obtenerPesoBrutoVehicular()"/>
                        <div id="peso-vehiculo-errors"></div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="peso-bruto">Peso Bruto</label><label class="mark-required text-right">*</label> <span class="glyphicon glyphicon-question-sign" ></span>
                        <span class="tiptext">Es la suma del peso vehicular y el peso de la carga, en el caso de vehículos de carga de acuerdo a la NOMSCT-012-2017.</span>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control input-form" id="peso-bruto" value="0" disabled/>
                        <div id="peso-bruto-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="seguro-medambiente">Nombre Aseguradora Medio Ambiente</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="seguro-medambiente" placeholder="Solo en transporte de Materiales Peligrosos"/>
                        <div id="seguro-medambiente-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="poliza-medambiente">Numero de Poliza</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="poliza-medambiente" placeholder="Numero de poliza de seguro"/>
                        <div id="poliza-medambiente-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label class="label-form">Remolques</label>  
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="nombre-remolque1">Nombre remolque #1</label> <span class="glyphicon glyphicon-question-sign" ></span>
                        <span class="tiptext">Puede realizar la busqueda por nombre o placa de un remolque que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del remolque puede dejar este campo en blanco e ingresar sus datos.</span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="" id="id-remolque1" name="id-remolque1"/>
                        <input type="text" class="form-control input-form" id="nombre-remolque1" placeholder="Buscar remolque" oninput="autocompletarRemolque1()"/>
                        <div id="nombre-vehiculo1-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="tipo-remolque1">Tipo Remolque #1</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="tipo-remolque1" placeholder="Clave del tipo de Remolque" oninput="aucompletarTipoRemolque()"/>
                        <div id="tipo-remolque1-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="placa-remolque1">Placa Remolque #1</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="placa-remolque1" placeholder="Placa del remolque (sin espacios ni guiones)" onblur="checkRemolque1();"/>
                        <div id="placa-remolque1-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="nombre-remolque2">Nombre remolque #2</label> <span class="glyphicon glyphicon-question-sign"></span>
                        <span class="tiptext">Puede realizar la busqueda por nombre o placa de un remolque que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del remolque puede dejar este campo en blanco e ingresar sus datos.</span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="" id="id-remolque2" name="id-remolque2"/>
                        <input type="text" class="form-control input-form" id="nombre-remolque2" placeholder="Buscar remolque" oninput="autocompletarRemolque2()" />
                        <div id="nombre-vehiculo2-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="tipo-remolque2">Tipo Remolque #2</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="tipo-remolque2" placeholder="Clave del tipo de Remolque" oninput="aucompletarTipoRemolque()"/>
                        <div id="tipo-remolque2-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="placa-remolque2">Placa Remolque #2</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="placa-remolque2" placeholder="Placa del remolque (sin espacios ni guiones)" onblur="checkRemolque2();"/>
                        <div id="placa-remolque2-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="nombre-remolque3">Nombre remolque #3</label> <span class="glyphicon glyphicon-question-sign"></span>
                        <span class="tiptext">Puede realizar la busqueda por nombre o placa de un remolque que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del remolque puede dejar este campo en blanco e ingresar sus datos.</span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="" id="id-remolque3" name="id-remolque3"/>
                        <input type="text" class="form-control input-form" id="nombre-remolque3" placeholder="Buscar remolque" oninput="autocompletarRemolque3()"/>
                        <div id="nombre-vehiculo3-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="tipo-remolque3">Tipo Remolque #3</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="tipo-remolque3" placeholder="Clave del tipo de Remolque" oninput="aucompletarTipoRemolque()"/>
                        <div id="tipo-remolque3-errors"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="label-form text-right" for="placa-remolque3">Placa Remolque #3</label>
                    <div class="form-group">
                        <input type="text" class="form-control input-form" id="placa-remolque3" placeholder="Placa del remolque (sin espacios ni guiones)" onblur="checkRemolque3();"/>
                        <div id="placa-remolque3-errors"></div>
                    </div>
                </div>
            </div>
            <br/><br/>
        </div>

        <div id="sub-ubicacion" class="sub-div" hidden>
            <div class="row">
                <div class="col-md-12">
                    <label class="label-sub">Ubicaciones</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <label class="label-form text-right" for="titulo">Buscar: </label>
                        <div class="form-group">
                            <label class="label-radio">
                                <input class="input-radio" type="radio" id="findubicacion1" name="findubicacion" value="1" checked=""> Origenes
                            </label>
                            <label class="label-radio">
                                <input class="input-radio" type="radio" id="findubicacion2" name="findubicacion" value="2"> Destinos
                            </label>
                        </div>

                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="nombre-ubicacion">Nombre Ubicacion <span class="glyphicon glyphicon-question-sign"></span></label>
                        <span class="tiptext">Puede realizar la busqueda por nombre, RFC o estado de una ubicacion que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro de la ubicacion puede dejar este campo en blanco e ingresar los datos necesarios.</span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="" id="id-ubicacion" name='id-ubicacion'/>
                        <input class='form-control text-center input-form' id="nombre-ubicacion" name='nombre-ubicacion' placeholder='Buscar Ubicacion' type='text' oninput="autocompletarUbicacion();"/>
                        <div id="nombre-ubicacion-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="rfc-ubicacion">RFC </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id="rfc-ubicacion" name='rfc-ubicacion' placeholder='RFC de la ubicacion' type='text' onblur="valRFCUbicacion()"/>
                        <div id="rfc-ubicacion-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="tipo-ubicacion">Tipo </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="tipo-ubicacion" name="tipo-ubicacion" onchange="labelUbicacion();">
                            <option value="" id="option-default-ubicacion">- - - -</option>
                            <optgroup id="tipos" class="contenedor-tipo text-left">
                                <option value="1" id="ubicacion1">Origen</option>
                                <option value="2" id="ubicacion2">Destino</option>
                            </optgroup>
                        </select>
                        <div id="tipo-ubicacion-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="direccion-ubicacion">Direccion </label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id="direccion-ubicacion" name='direccion-ubicacion' placeholder='Calle, N° Int, N° Ext, Colonia' type='text'/>
                        <div id="direccion-ubicacion-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="label-form text-right" for="cp-ubicacion">Cod Postal </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id="cp-ubicacion" name='cp-ubicacion' placeholder='Codigo Postal de la ubicacion' type='text' onblur="getEstadoUbicacion();"/>
                        <div id="cp-ubicacion-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="label-form text-right" for="id-estado">Estado</label><label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="id-estado" name="id-estado" onchange="loadMunicipioUbicacion()">
                            <option value="" id="option-default-estado">- - - -</option>
                            <optgroup id="estados-ubicacion" class="contenedor-estado text-left"> </optgroup>
                        </select>
                        <div id="id-estado-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="id-municipio">Municipio</label>
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="id-municipio" name="id-municipio">
                            <option value="" id="option-default-municipio">- - - -</option>
                            <optgroup id="municipios" class="contenedor-municipio text-left"> </optgroup>
                        </select>
                        <div id="id-municipio-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="label-form text-right" for="distancia-ubicacion">Distancia (Km) </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='distancia-ubicacion' name='distancia-ubicacion' placeholder='Distancia a recorrer' type='number' step="any" value="0"/>
                        <div id="distancia-ubicacion-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="label-form text-right" for="fecha-llegada" id="fecha-label">Fecha Llegada</label><label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='fecha-llegada' name='fecha-llegada' type='date'/>
                        <div id="fecha-llegada-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="label-form text-right" for="hora-llegada" id="hora-label">Hora de salida </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='hora-llegada' name='hora-llegada' type='time'/>
                        <div id="hora-llegada-errors"></div>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="label-form text-right" for="distancia-ubicacion">Agregar </label>
                    <div class="form-group">
                        <button title="Agregar ubicacion" id="btn-agregar-ubicacion" class='button-add-prod' onclick='agregarUbicacion();'><span class='glyphicon glyphicon-plus'></span></button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="scroll-table">
                        <table id="resultubicacion" class="table tab-hover table-condensed table-responsive table-row table-head">

                        </table>  
                    </div>
                </div>
            </div>
        </div>

        <div id="sub-operador" class="sub-div" hidden>
            <div class="row">
                <div class="col-md-12">
                    <label class="label-sub">Operadores</label>
                </div>
            </div>

            <div class="row">
                <input type="hidden" value="" id="flag-operador" name='flag-operador'/>
                <div class="col-md-4">
                    <div class="new-tooltip icon tip">
                        <label class="label-form text-right" for="nombre-operador">Nombre Operador <span class="glyphicon glyphicon-question-sign"></span></label>
                        <span class="tiptext">Puede realizar la busqueda por nombre o RFC de un operador que haya registrado previamente y el sistema cargara los datos de forma automatica, si no realizo registro del operador puede dejar este campo en blanco e ingresar los datos.</span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="" id="id-operador" name='id-operador'/>
                        <input class='form-control text-center input-form' id="nombre-operador" name='nombre-operador' placeholder='Buscar Operador' type='text' oninput="autocompletarOperador();"/>
                        <div id="nombre-operador-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="rfc-operador">RFC Operador</label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id="rfc-operador" name='rfc-operador' placeholder='RFC del operador' type='text' onblur="checkOperador(); valRFCOperador();"/>
                        <div id="rfc-operador-errors"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="label-form text-right" for="num-licencia">Num de Licencia </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id="num-licencia" name='num-licencia' placeholder='Numero de licenia del operador' type='text' oninput="autocompletarOperador();"/>
                        <div id="num-licencia-errors"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="label-form text-right" for="direccion-operador">Direccion Operador</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='direccion-operador' name='direccion-operador' placeholder='Calle domicilio' type='text'/>
                        <div id="direccion-operador-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="label-form text-right" for="estado-operador">Estado</label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="estado-operador" name="estado-operador" onchange="loadMunicipioOperador()">
                            <option value="" id="option-default-estado">- - - -</option>
                            <optgroup id="estados-operador" class="contenedor-estado text-left"> </optgroup>
                        </select>
                        <div id="estado-operador-errors"></div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="label-form text-right" for="municipio-operador">Municipio</label>
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="municipio-operador" name="municipio-operador">
                            <option value="" id="default-municipio-operador">- - - -</option>
                            <optgroup id="municipios-operador" class="contenedor-municipio-op text-left"> </optgroup>
                        </select>
                        <div id="municipio-operador-errors"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="label-form text-right" for="cp-operador">Cod Postal del operador </label> <label class="mark-required text-right">*</label>
                    <div class="form-group">
                        <input class='form-control text-center input-form' id='cp-operador' name='cp-operador' placeholder='Codigo Postal domicilio' type='text' onblur="getEstadoOperador();"/>
                        <div id="cp-operador-errors"></div>
                    </div>
                </div>

                <div class="col-md-1">
                    <label class="label-form text-right" for="btn-agregar-operador">Agregar </label>
                    <div class="form-group">
                        <button title="Agregar ubicacion" id="btn-agregar-operador" class='button-add-prod' onclick='agregarOperador();'><span class='glyphicon glyphicon-plus'></span></button>
                    </div>
                </div>
            </div>

            <div class="row scroll-table">
                <table id="result-operador" class="table tab-hover table-condensed table-responsive table-row table-head">

                </table>
            </div>
        </div>

        <div id="sub-evidencia" class="sub-div" hidden>
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="label-sub text-right" for="observaciones-carta">Observaciones</label>
                    <textarea rows="5" cols="60" id="observaciones-carta" name="observaciones-carta" class="form-control input-form" placeholder="Observaciones sobre el servicio"></textarea>
                    <div id="observaciones-carta-errors">
                    </div>
                </div>

            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <label class="label-sub">Evidencias (Opcional)</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="button-file text-right" for="img-evidencia"><span class="glyphicon glyphicon-picture" ></span> Agregar Imagenes o archivos</label>
                    <div class="form-group">
                        <input class="form-control text-center upload"  id="img-evidencia" name="img-evidencia[]"  type="file" onchange="cargarImgEvidencia();" multiple/>
                        <input id="nm-evidencia" name="nm-evidencia" type="hidden"/>
                        <div id="img-evidencia-errors">
                        </div>
                    </div>
                </div>
                <div class="col-md-8 scroll-table">
                    <table  class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                        <tbody id="img-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br/><br/>
        <div class="row">
            <div class="col-md-12 text-right" id="btns"> 
                <button class="button-form btn-danger " onclick="cancelarCarta()" >Cancelar <span class="glyphicon glyphicon-remove"></span></button> &nbsp;
                <button class="button-form btn-info next-prev" data-nav="top" data-tab="factura" id="btn-form-prev"><span class="glyphicon glyphicon-backward"></span> Anterior</button>&nbsp;
                <button class="button-form btn-info next-prev" data-nav="sub" data-tab="transporte" id="btn-form-next">Siguiente <span class="glyphicon glyphicon-forward"></span></button>&nbsp;
                <button class="button-form btn-primary " onclick="insertarFacturaCarta()" id="btn-form-carta">Guardar <span class="glyphicon glyphicon-floppy-disk"></span></button>
            </div>	
        </div>
    </div>
</form>
<br/>
<script src="js/scriptcarta.js"></script>
<script>
                    resetDiv();
</script>
