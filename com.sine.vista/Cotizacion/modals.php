<!--MODAL PARA LISTADO DE ANTICIPOS-->
<div class="modal fade bs-example-modal-lg" id="lista-anticipo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Lista de anticipos
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="scrollX">
                    <table id="body-lista-anticipo" class="table table-hover table-condensed table-responsive table-row table-head border border-light">
                        <thead class="sin-paddding">
                            <tr>
                                <th>Fecha de Creacion </th>
                                <th>Monto</th>
                                <th>Restante</th>
                                <th>Num Autorizacion</th>
                                <th>Fecha Transaccion</th>
                                <th>Comprobante</th>
                                <th>Opción<sth>
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

<!--MODAL PARA REGISTRAR UN ANTICIPO-->
<div class="modal fade bs-example-modal-lg" id="anticipos" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Agregar anticipo
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="form-anticipo">
                    <div class="row">
                        <div class="col-md-6">
                            <input class="form-control text-center input-sm" id="id-cotizacion" name="id-cotizacion" type="hidden" />
                        </div>
                        <div class="col-md-6">
                            <label class="label-form text-right mb-1" for="fecha-creacion">Fecha creación</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="fecha-creacion" name="fecha-creacion" placeholder="Fecha de creación" type="text" disabled />
                                <div id="fecha-creacion-errors">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <input id="editar-idtmp" name="editar-idtmp" type="hidden" />
                        <div class="col-md-6 py-2">
                            <label class="label-form text-right mb-1" for="monto-anticipo">Anticipo</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="total-cotizacion" name="total-cotizacion" type="hidden" />
                                <input class="form-control text-center input-form" id="monto-anticipo" name="monto-anticipo" placeholder="Anticipo" value="0" type="number" oninput="transcribirAnticipo();" />
                                <div id="monto-anticipo-errors">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 py-2">
                            <label class="label-form text-right mb-1" for="restante-anticipo">Restante</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="restante-anticipo" name="restante-anticipo" placeholder="Restante" value="0" type="text" disabled />
                                <div id="restante-anticipo-errors">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 py-2">
                            <label class="label-form text-right mb-1" for="no-autorizacion">No. Autorización</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="no-autorizacion" name="no-autorizacion" placeholder="Número de autorización" type="text" />
                                <div id="no-autorizacion-errors">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 py-2">
                            <label class="label-form text-right mb-1" for="fecha-anticipo">Fecha anticipo</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="fecha-anticipo" name="fecha-anticipo" type="date" />
                                <div id="fecha-anticipo-errors">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 py-2">
                            <label class="label-form text-right mb-1" for="lugar-emision">Lugar de emisión</label>
                            <div class="form-group">
                                <input class="form-control text-center input-form" id="lugar-emision" name="lugar-emision" placeholder="Municipio y/o estado de emisión" type="text" />
                                <div id="monto-anticipo-errors">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 py-2">
                            <label class="label-form text-right mb-1" for="lugar-emision">&nbsp;</label>
                            <label class="button-file text-right mb-1 col-12 text-uppercase" for="imagen"><span class="fas fa-file"></span> Agregar comprobante</label>
                            <div class="form-group">
                                <input class="form-control input-form" id="imagen" name="imagen" type="file" hidden onchange="cargarImgAnticipo();" accept=".jpg, .png, .jprg, .gif, .jfif, .pdf, .docx, .xlsx, .pptx, .rar, .zip"/>
                                <input id="filename" name="filename" type="hidden" />
                                <input id="imgactualizar" name="imgactualizar" type="hidden" />
                            </div>
                        </div>
                        <div class="text-center col-md-3 pt-3 mt-3" id="btn-anticipo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 py-2">
                            <label class="label-form text-right mb-1" for="producto">Texto</label>
                            <div class="form-group">
                                <textarea rows="5" cols="60" id="mensaje-anticipo" class="form-control input-form" style="height: 100px;" placeholder="Escrito de anticipo"></textarea>
                            </div>
                            <div id="mensaje-anticipo-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row me-2 d-flex justify-content-end">
                        <button class="button-modal col-auto" onclick="insertarAnticipo()" id="btn-form-anticipo">Guardar <span class="fas fa-save"></span></button>
                    </div>
                </div>
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
                <div id="datosproducto" class="outer_div">
                    <input id="idcotizacionenvio" name="idcotizacionenvio" type="hidden" />
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="correo1">Correo No.1</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo1" name="nombre_empresa" placeholder="Correo de Informacion" type="text" />
                                <div class="input-group-text"><input class="input-check" id="chcorreo1" name="nombre_empresa" type="checkbox" /></div>
                            </div>
                            <div id=correo1-errors">
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="correo2">Correo No.2</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo2" name="nombre_empresa" placeholder="Correo de Facturacion" type="text" />
                                <div class="input-group-text"><input class="input-check" id="chcorreo2" name="nombre_empresa" type="checkbox" checked /></div>
                            </div>
                            <div id="correo2-errors">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="label-form text-right mb-1" for="correo3">Correo No.3</label>
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correo3" name="nombre_empresa" placeholder="Correo de Facturacion" type="text" />
                                <div class="input-group-text"><input class="input-check" id="chcorreo3" name="nombre_empresa" type="checkbox" /></div>
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

<!--VISUALIZACION DE IMÁGENES EN EL FORMULARIO-->
<div class="modal fade shadow-lg rounded rounded-5" id="archivos" name="archivos" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="label-nuevo-producto">Visualización de imágenes</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" onclick="cerrarArchivos()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fotito" class="d-flex justify-content-center">
                <embed src="" class="col-md-8"  type="application/pdf"/>
                </div>
            </div>
        </div>
    </div>
</div>

<!--VISUALIZACION DE ARCHIVOS EMBED EN EL FORMULARIO-->
<div class="modal fade shadow-lg rounded rounded-5" id="archivosotros" name="archivosotros" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="label-nuevo-producto">Visualización de archivos</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" onclick="cerrarArchivosOtros()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="archivo-visualizar" class="d-flex justify-content-center">
    

                <embed src="" class="col-md-8"  type="application/pdf"/>
                </div>
            </div>
        </div>
    </div>
</div>
