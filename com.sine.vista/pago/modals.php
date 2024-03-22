<!---ENVIAR PAGO VIA EMAIL-->
<div class="modal fade" id="enviarrecibo" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Seleccionar correo electr&oacute;nico:
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="idreciboenvio" name="nombre_empresa" type="hidden"/>
                <div class="row">
                    <div class="col-md-6 form-group py-2">
                        <label class="label-form mb-1" for="apellido-materno">Correo informaci&oacute;n</label>
                        <div class="input-group">
                            <input class="form-control input-form" id="correo1" name="nombre_empresa" placeholder="Correo de Informaci&oacute;n" type="text"/>
                            <div class="input-group-text"><input class="input-check" id="chcorreo1" name="nombre_empresa" type="checkbox"/></div>
                        </div>
                        <div id="correo1-errors">
                        </div>
                    </div>

                    <div class="col-md-6 form-group py-2">
                        <label class="label-form mb-1" for="apellido-materno">Correo facturaci&oacute;n</label>
                        <div class="input-group">
                            <input class="form-control input-form" id="correo2" name="nombre_empresa" placeholder="Correo de Facturaci&oacute;n" type="text"/>
                            <div class="input-group-text"><input class="input-check" id="chcorreo2" name="nombre_empresa" type="checkbox" checked/></div>
                        </div>
                        <div id="correo2-errors">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group py-2">
                        <label class="label-form mb-1" for="apellido-materno">Correo Gerencia</label>
                        <div class="input-group">
                            <input class="form-control input-form" id="correo3" name="nombre_empresa" placeholder="Correo de Facturaci&oacute;n" type="text"/>
                            <div class="input-group-text"><input class="input-check" id="chcorreo3" name="nombre_empresa" type="checkbox"/></div>
                        </div>
                        <div id="correo3-errors">
                        </div>
                    </div>

                    <div class="col-md-6 form-group py-2">
                        <label class="label-form mb-1" for="apellido-materno">Correo Alternativo 1</label>
                        <div class="input-group">
                            <input class="form-control input-form" id="correo4" name="correo4" placeholder="Correo Alternativo 1" type="text"/>
                            <div class="input-group-text"><input class="input-check" id="chcorreo4" name="nombre_empresa" type="checkbox"/></div>
                        </div>
                        <div id="correo4-errors">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group py-2">
                        <label class="label-form mb-1" for="apellido-materno">Correo Alternativo 2</label>
                        <div class="input-group">
                            <input class="form-control input-form" id="correo5" name="correo4" placeholder="Correo Alternativo 2" type="text"/>
                            <div class="input-group-text"><input class="input-check" id="chcorreo5" name="nombre_empresa" type="checkbox"/></div>
                        </div>
                        <div id="correo5-errors">
                        </div>
                    </div>

                    <div class="col-md-6 form-group py-2">
                        <label class="label-form mb-1" for="apellido-materno">Correo Alternativo 3</label>
                        <div class="input-group">
                            <input class="form-control input-form" id="correo6" name="correo6" placeholder="Correo Alternativo 3" type="text"/>
                            <div class="input-group-text"><input class="input-check" id="chcorreo6" name="nombre_empresa" type="checkbox"/></div>
                        </div>
                        <div id="correo6-errors">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-end" id="btn">
                        <button class="button-modal" onclick="enviarPago()" id="btn-pago">Enviar <span class="fas fa-envelope"></span></button>
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
                        <select class="form-select text-center input-form" id="motivo-cancelacion" name="motivo-cancelacion" onchange="checkCancelarPago();">
                            <option value="" id="option-default-motivo">- - - -</option>
                            <optgroup id="motivos" class="contenedor-motivos text-start"> </optgroup>
                        </select>
                        <div id="motivo-cancelacion-errors"></div>
                    </div>
                </div>
                <div id="div-reemplazo" style="display: none;">
                    <div class="row mt-3">
                        <label class="label-form mb-1" for="uuid-reemplazo">Folio fiscal de reemplazo:</label>
                        <div class="form-group">
                            <input type="text" class="form-control input-form cfdi input-form" id="uuid-reemplazo" placeholder="00000000-0000-0000-0000-000000000000">
                            <div id="uuid-reemplazo-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 mb-3">
                    <div class="col-md-12 text-end" id="btn">
                        <button class="button-modal" onclick="cancelarTimbrePago()" id="btn-cancelar">Cancelar timbre <span class="fas fa-bell"></span></button>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>

<!--CHECAR STATUS DEL CFDI-->
<div class="modal fade bs-example-modal-lg" id="modal-stcfdi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                   Status del CFDI
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <span class="label-sub py-0 my-0" for="cod-status">Codigo Status: </span>
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
                    <span class="label-sub py-0 my-0 mt-3" for="estado-cancelacion">Estado Cancelación: </span>
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