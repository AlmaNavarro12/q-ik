<div class="modal fade bs-example-modal-lg" id="lista-anticipo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Lista de Anticipos</h4>
            <div class="modal-body">
                <div class="scrollX">
                    <table id="body-lista-anticipo" class="table tab-hover table-condensed table-responsive table-row thead-mod">
                        <thead class="sin-paddding">
                            <tr>
                                <th>Fecha de Creacion </th>
                                <th>Monto</th>
                                <th>Restante</th>
                                <th>Num Autorizacion</th>
                                <th>Fecha Transaccion</th>
                                <th>Comprobante</th>
                                <th>Opciones<span class="glyphicon glyphicon-option-vertical"></span></th>
                            </tr>
                        </thead>
                        <tbody id="body-lista-anticipo">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="anticipos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Agregar Anticipo</h4>
            <div class="modal-body">
                <form id="form-anticipo" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-6">
                            <input class="form-control text-center input-sm" id="id-cotizacion" name="id-cotizacion" type="hidden"/>
                        </div>
                        <div class="col-md-6">
                            <label class="label-form text-right" for="fecha-creacion">Fecha creacion</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="fecha-creacion" name="fecha-creacion" placeholder="Fecha de Creacion" type="text" disabled/>
                                <div id="fecha-creacion-errors">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <input id="editar-idtmp" name="editar-idtmp" type="hidden"/>
                        <div class="col-md-6">
                            <label class="label-form text-right" for="monto-anticipo">Anticipo</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="total-cotizacion" name="total-cotizacion" type="hidden"/>
                                <input class="form-control text-center input-form" id="monto-anticipo" name="monto-anticipo" placeholder="Anticipo" value="0" type="number" oninput="transcribirAnticipo();"/>
                                <div id="monto-anticipo-errors">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="label-form text-right" for="restante-anticipo">Restante</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="restante-anticipo" name="restante-anticipo" placeholder="Restante" value="0" type="text" disabled/>
                                <div id="restante-anticipo-errors">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="label-form text-right" for="no-autorizacion">N° Autorizacion</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="no-autorizacion" name="no-autorizacion" placeholder="Numero de Autorizacion" type="text"/>
                                <div id="no-autorizacion-errors">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="label-form text-right" for="fecha-anticipo">Fecha Anticipo</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="fecha-anticipo" name="fecha-anticipo" type="date"/>
                                <div id="fecha-anticipo-errors">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-3">
                            <label class="label-form button-file text-right" for="imagen"><span class="glyphicon glyphicon-picture" ></span> Agregar Comprobante</label>
                            <div class="form-group">
                                <input class="form-control input-form" id="imagen" name="imagen" type="file" onchange="cargarImgAnticipo();"/>
                                <input id="filename" name="filename"  type="hidden" />
                                <input id="imgactualizar" name="imgactualizar"  type="hidden"/>
                            </div>
                        </div>
                        <div class="text-left col-md-3" id="btn-anticipo" >
                        </div>

                        <div class="col-md-6">
                            <label class="label-form text-right" for="lugar-emision">Lugar de Emision</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="lugar-emision" name="lugar-emision" placeholder="Municipio y/o estado de Emision" type="text"/>
                                <div id="monto-anticipo-errors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="label-form text-right" for="producto">Texto</label>
                            <div class="form-group">
                                <textarea rows="5" cols="60" id="mensaje-anticipo" class="form-control input-form" placeholder="Escrito de anticipo" ></textarea>
                            </div>
                            <div id="mensaje-anticipo-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row text-right">
                        <button class="button-modal" onclick="insertarAnticipo()" id="btn-form-anticipo">Guardar <span class="glyphicon glyphicon-floppy-disk"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--MDDAL PARA ENVIAR CORREO-->
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
                    <input id="idcotizacionenvio" name="idcotizacionenvio" type="hidden"/>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="correo1">Correo No.1</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo1" name="nombre_empresa" placeholder="Correo de Informacion" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo1" name="nombre_empresa" type="checkbox"/></div>
                            </div>
                            <div id=correo1-errors">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="correo2">Correo No.2</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo2" name="nombre_empresa" placeholder="Correo de Facturacion" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo2" name="nombre_empresa" type="checkbox" checked/></div>
                            </div>
                            <div id="correo2-errors">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="correo3">Correo No.3</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo3" name="nombre_empresa" placeholder="Correo de Facturacion" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreo3" name="nombre_empresa" type="checkbox"/></div>
                            </div>
                            <div id="correo3-errors">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end" id="btn">
                            <button class="button-modal" onclick="enviarfactura()" id="btn-send">Enviar <span class="fas fa-envelope"></span></button>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>