<!--PAGOS DE FACTURAS-->
<div class="modal fade bs-example-modal-lg" id="pagosfactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Pagos de la factura
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loader" style="position: absolute;	text-align: center;	top: 55px;	width: 100%;display:none;"></div><!-- Carga gif animado -->
                <div id="datosproducto" class="outer_div" >
                    <div class="row">
                        <table id="pagostabla" class="table table-hover table-condensed table-responsive table-row table-head">

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--ENVIAR CARTA PORTE POR CORREO-->
<div class="modal fade bs-example-modal-lg" id="enviarmail" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Seleccionar correo electr&oacute;nico:
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="datosproducto" class="outer_div" >
                    <div class="row">
                        <div class="col-md-6 py-2 form-group">
                            <label class="label-form text-right mb-2" for="correo1">Correo información</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo1" name="nombre_empresa" placeholder="Correo de Informacion" type="text"/>
                                <div class="input-group-text" ><input class="input-check" id="chcorreo1" name="chcorreo1" type="checkbox"/></div>
                            </div>
                            <div id="correo1-errors">
                            </div>
                        </div>

                        <div class="col-md-6 py-2 form-group">
                            <label class="label-form text-right mb-2" for="correo2">Correo facturación</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo2" name="nombre_empresa" placeholder="Correo de Facturacion" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo2" name="chcorreo2" type="checkbox" checked/></div>
                            </div>
                            <div id="correo2-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 py-2 form-group">
                            <label class="label-form text-right mb-2" for="correo3">Correo gerencia</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo3" name="nombre_empresa" placeholder="Correo de Facturacion" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo3" name="chcorreo3" type="checkbox"/></div>
                            </div>
                            <div id="correo3-errors">
                            </div>
                        </div>

                        <div class="col-md-6 py-2 form-group">
                            <label class="label-form text-right mb-2" for="correo4">Correo alternativo 1</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo4" name="correo4" placeholder="Correo Alternativo 1" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo4" name="chcorreo4" type="checkbox"/></div>
                            </div>
                            <div id="correo4-errors">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 py-2 form-group">
                            <label class="label-form text-right mb-2" for="correo5">Correo alternativo 2</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo5" name="correo5" placeholder="Correo Alternativo 2" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo5" name="chcorreo5" type="checkbox"/></div>
                            </div>
                            <div id="correo5-errors">
                            </div>
                        </div>

                        <div class="col-md-6 py-2 form-group">
                            <label class="label-form text-right mb-2" for="correo6">Correo alternativo 3</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo6" name="correo6" placeholder="Correo Alternativo 3" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo6" name="chcorreo5" type="checkbox"/></div>
                            </div>
                            <div id="correo6-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-end" id="btn">
                            <button class="button-modal" onclick="enviarCarta()" id="btn-enviar-carta">Enviar <span class="fas fa-envelope"></span></button>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MOTIVO DE CANCELACION-->
<div class="modal fade bs-example-modal-lg" id="modalcancelar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Motivo de la cancelaci&oacute;n
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-3">
                    <div class="form-group">
                    <label class="label-form mb-1" for="uuid-reemplazo">Selecciona un motivo de cancelación:</label>
                        <select class="form-select text-center input-form" id="motivo-cancelacion" name="motivo-cancelacion" onchange="checkCancelacion();">
                            <option value="" id="option-default-motivo">- - - -</option>
                            <optgroup id="motivos" class="contenedor-motivos text-start"> </optgroup>
                        </select>
                        <div id="motivo-cancelacion-errors"></div>
                    </div>
                </div>
                <div id="div-reemplazo" style="display: none;">
                    <div class="row mt-3">
                        <label class="label-form text-right mb-1" for="uuid-reemplazo">Folio fiscal de reemplazo:</label>
                        <div class="form-group">
                            <input type="text" class="form-control cfdi input-form" id="uuid-reemplazo" placeholder="00000000-0000-0000-0000-000000000000">
                            <div id="uuid-reemplazo-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 mb-5">
                    <div class="col-md-12 text-end" id="btn">
                        <button class="button-modal" onclick="cancelarTimbre()" id="btn-cancelar">Cancelar timbre <span class="fas fa-bell"></span></button>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>

<!--ESTADO DEL CFDI-->
<div class="modal fade" id="modal-stcfdi" tabindex="-1" aria-labelledby="modal-stcfdi-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                   Estado del CFDI
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row">
                    <span class="label-sub py-0 my-0" for="cod-status">Código de estado: </span>
                    <span class="label-form" id="cod-status"></span>
                </div>
                
                <div class="row">
                    <span class="label-sub py-0 my-0 mt-3" for="estado-cfdi">Estado: </span>
                    <span class="label-form" id="estado-cfdi"></span>
                </div>
                
                <div class="row">
                    <span class="label-sub py-0 my-0 mt-3" for="cfdi-cancelable">Cancelable: </span>
                    <span class="label-form" id="cfdi-cancelable"></span>
                </div>
                
                <div class="row">
                    <span class="label-sub py-0 my-0 mt-3" for="estado-cancelacion">Estado cancelación: </span>
                    <span class="label-form" id="estado-cancelacion"></span>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-right" id="div-reset">
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA VER ARCHIVOS-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-evidencia"  tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="label-nuevo-producto">Evidencias adjuntas</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="contenido">
                    <div class="col-md-4">
                        <div class="col-12">
                            <table class="table table-responsive table-secondary border border-start-0 border-dark" id="tabla-evidencias">
                            </table> 
                        </div>
                    </div>
                    <div class="col-md-8 d-flex justify-content-center" id="fotito">
                        <embed src="" class="col-12 mx-auto" type="application/pdf"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>