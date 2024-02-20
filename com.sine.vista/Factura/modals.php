<div class="modal fade bs-example-modal-lg" id="pagosfactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Pagos de Factura</h4>
            <div class="modal-body">
                <div id="loader" style="position: absolute;	text-align: center;	top: 55px;	width: 100%;display:none;"></div><!-- Carga gif animado -->
                <div id="datosproducto" class="outer_div" >
                    <div class="row">
                        <table id="pagostabla" class="table tab-hover table-condensed table-responsive table-row thead-mod">

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="enviarmail" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Seleccionar correo(s): </h4>
            <div class="modal-body">
                <div id="datosproducto" class="outer_div" >
                    <input id="idfacturaenvio" name="idfacturaenvio" type="hidden"/>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="correo1">Correo informacion</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form mb-3" id="correo1" name="nombre_empresa" placeholder="Correo de Informacion" type="text"/>
                                <div class="input-group-addon ms-2" ><input class="input-check" id="chcorreo1" name="chcorreo1" type="checkbox"/></div>
                            </div>
                            <div id="correo1-errors">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="correo2">Correo facturacion</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo2" name="nombre_empresa" placeholder="Correo de Facturacion" type="text"/>
                                <div class="input-group-addon ms-2"><input class="input-check" id="chcorreo2" name="chcorreo2" type="checkbox" checked/></div>
                            </div>
                            <div id="correo2-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="correo3">Correo Gerencia</label>
                            <div class="input-group">
                                <input class="form-control text-center input-for mb-3" id="correo3" name="nombre_empresa" placeholder="Correo de Facturacion" type="text"/>
                                <div class="input-group-addon ms-2"><input class="input-check" id="chcorreo3" name="chcorreo3" type="checkbox"/></div>
                            </div>
                            <div id="correo3-errors">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="correo4">Correo Alternativo 1</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo4" name="correo4" placeholder="Correo Alternativo 1" type="text"/>
                                <div class="input-group-addon ms-2"><input class="input-check" id="chcorreo4" name="chcorreo4" type="checkbox"/></div>
                            </div>
                            <div id="correo4-errors">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="correo5">Correo Alternativo 2</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form mb-3" id="correo5" name="correo5" placeholder="Correo Alternativo 2" type="text"/>
                                <div class="input-group-addon ms-2"><input class="input-check" id="chcorreo5" name="chcorreo5" type="checkbox"/></div>
                            </div>
                            <div id="correo5-errors">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-2" for="correo6">Correo Alternativo 3</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo6" name="correo6" placeholder="Correo Alternativo 3" type="text"/>
                                <div class="input-group-addon ms-3"><input class="input-check" id="chcorreo6" name="chcorreo5" type="checkbox"/></div>
                            </div>
                            <div id="correo6-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right" id="btn">
                            <button class="button-modal" onclick="enviarfactura()" id="btn-pago">Enviar <span class="glyphicon glyphicon-envelope"></span></button>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modalcancelar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Motivo de la cancelacion:</h4>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <select class="form-control text-center input-form" id="motivo-cancelacion" name="motivo-cancelacion" onchange="checkCancelacion();">
                            <option value="" id="option-default-motivo">- - - -</option>
                            <optgroup id="motivos" class="contenedor-motivos text-left"> </optgroup>
                        </select>
                        <div id="motivo-cancelacion-errors"></div>
                    </div>
                </div>
                <div id="div-reemplazo" hidden>
                    <div class="row">
                        <label class="label-form text-right" for="uuid-reemplazo">Folio Fiscal de reemplazo:</label>
                        <div class="form-group">
                            <input type="text" class="form-control cfdi input-form" id="uuid-reemplazo" placeholder="00000000-0000-0000-0000-000000000000">
                            <div id="uuid-reemplazo-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right" id="btn">
                        <button class="button-modal" onclick="cancelarTimbre()" id="btn-cancelar">Cancelar Timbre <span class="glyphicon glyphicon-bell"></span></button>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-stcfdi" tabindex="-1" aria-labelledby="modal-stcfdi-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-stcfdi-label">Status del CFDI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <label class="label-sub" for="cod-status">Codigo Status:</label>
                    <label class="label-form" id="cod-status"></label>
                </div>
                
                <div class="row">
                    <label class="label-sub" for="estado-cfdi">Estado:</label>
                    <label class="label-form" id="estado-cfdi"></label>
                </div>
                
                <div class="row">
                    <label class="label-sub" for="cfdi-cancelable">Cancelable:</label>
                    <label class="label-form" id="cfdi-cancelable"></label>
                </div>
                
                <div class="row">
                    <label class="label-sub" for="estado-cancelacion">Estado Cancelacion:</label>
                    <label class="label-form" id="estado-cancelacion"></label>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-end" id="div-reset">
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>



<!--<div class="modal fade bs-example-modal-lg" id="enviarwp" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Enviar Whatsapp a: </h4>
            <div class="modal-body">
                <input id="idfacturawp" name="idfacturawp" type="hidden"/>
                <div class="row">
                    <div class="col-md-4">
                        <label class="label-form text-right" for="option-cod">Codigo pais</label>
                        <div class="form-group">
                            <select class="form-control text-center input-form" id="option-cod" name="option-cod">
                                <option value="521" id="cod52">Mexico +52</option>
                                <option value="1" id="cod1">EU +1</option>
                            </select>
                            <div id="option-cod-errors">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="label-form text-right" for="wpnumber">Numero</label>
                        <div class="form-group">
                            <input class="form-control text-center input-form" id="wpnumber" name="wpnumber" placeholder="Numero de Whatsapp" type="text"/>
                            <div id="wpnumber-errors">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right" id="btn">
                        <button class="button-modal" onclick="sendWhatsapp()" id="btn-wp">Enviar <span class="fab fa-whatsapp"></span></button>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>-->