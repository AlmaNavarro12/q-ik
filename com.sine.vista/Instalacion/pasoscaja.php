<?php
include 'modals.php';
?>
<div id="panel">
    <div class="ps-3">
        <div class="col-12 text-start">
            <h1 class="text-start titulo-lista">Progreso en el servicio: <span id="spn-cve-orden"></span></h1>
            <input type="hidden" id="cve_orden" name="cve_orden" value="0">
            <input type="hidden" id="tipo_unidad" name="tipo_unidad">
            <div class="col-12 row">
                <div class="col-12 row">
                    <div class="col-md-10 col-sm-12 d-flex align-items-center  pe-5">
                        <div class="progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 33px;">
                            <div class="progress-bar" style="width: 100%;">100%</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 d-flex justify-content-center  mb-0">
                        <ul class="pagination pagination-sm">
                            <li id="li-prev-caja" class="page-item disabled"><button class="page-link" id="btn-prev-caja" aria-label="Anterior" onclick="prev_caja()" disabled><span aria-hidden="true">&laquo;</span></button></li>
                            <li id="li-1-caja" class="active page-item"><button class="page-link" id="btn-1-caja" onclick="goStep_caja(1)">1</button></li>
                            <li id="li-2-caja" class="page-item disabled"><button class="page-link" id="btn-2-caja" onclick="goStep_caja(2)" disabled>2</button></li>
                            <li id="li-3-caja" class="page-item disabled"><button class="page-link" id="btn-3-caja" onclick="goStep_caja(3)" disabled>3</button></li>
                            <li id="li-4-caja" class="page-item disabled"><button class="page-link" id="btn-4-caja" onclick="goStep_caja(4)" disabled>4</button></li>
                            <li id="li-5-caja" class="page-item disabled"><button class="page-link" id="btn-5-caja" onclick="goStep_caja(5)" disabled>5</button></li>
                            <li id="li-6-caja" class="page-item disabled"><button class="page-link" id="btn-6-caja" onclick="goStep_caja(6)" disabled>6</button></li>
                            <li id="li-7-caja" class="page-item disabled"><button class="page-link" id="btn-7-caja" onclick="goStep_caja(7)" disabled>7</button></li>
                            <li id="li-next-caja"><button class="page-link" id="btn-next-caja" aria-label="Siguiente" onclick="next_caja()"><span aria-hidden="true">&raquo;</span></button></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="form-caja" class="div-form py-5 px-4 ms-3 border border-secondary-subtle">
        <!--PASO 1. FOTOS DE INICIO-->
        <fieldset id="step-1-caja">
            <div class="row">
                <div class="col-md-12 col-sm-12 mh-100">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.1: Subir las fotos de inicio.</h2>
                </div>
            </div>
            <div class="row mt-4 mb-5 pb-4">
                <div class="col-md-6 col-sm-12 form-group">
                    <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" id="label-frenteimg-caja" for="frenteimg-caja">Placas <span class="fas fa-plus"></span></label>
                    <input id="frentepic-caja" name="frentepic-caja" type="hidden" />
                    <input id="frenteactualizar-caja" name="frenteactualizar-caja" type="hidden" />
                    <input class="form-control text-center upload" id="frenteimg-caja" name="frenteimg-caja" type="file" accept=".png, .jpg, .jpeg" hidden onchange="cargarImgFrente();" />
                    <div id="imgfrente-caja" class="py-3 col-12 d-flex align-items-center"></div>
                    <div id="frentepic-caja-errors"></div>
                </div>
                <div class="col-md-6 col-sm-12 form-group" id="vinopcional">
                    <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" id="label-vinimg-caja" for="vinimg-caja">No. de serie o VIN <span class="fas fa-plus"></span></label>
                    <input id="vinpic-caja" name="vinpic-caja" type="hidden" />
                    <input id="vinpicactualizar-caja" name="vinpicactualizar-caja" type="hidden" />
                    <input class="form-control text-center upload" id="vinimg-caja" name="vinimg-caja" type="file" accept=".png, .jpg, .jpeg" hidden onchange="cargarImgVin();" />
                    <div id="imgvin-caja" class="py-3 col-12 d-flex align-items-center"></div>
                    <div id="vinimg-caja-errors"></div>
                </div>
            </div>
        </fieldset>

        <!--PASO 2. CABLEADO INICIAL-->
        <fieldset id="step-2-caja" style="display: none">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.2: Cableado Inicial</h2><br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 form-group">
                    <label class="button-file col-md-12 text-center text-uppercase fw-semibold" id="label-beforeIns-caja" for="beforeIns-caja">Cableado antes de instalación / revisión <span class="fas fa-plus"></span></label>
                    <input id="tabbefore-caja" name="tabbefore-caja" type="hidden" />
                    <input id="tabbeforeactualizar-caja" name="tabbeforeactualizar-caja" type="hidden" />
                    <input class="form-control text-center upload" id="beforeIns-caja" name="beforeIns-caja" hidden type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgAntesInst();" />
                    <div id="imgbeforeIns-caja" class="col-6 mt-3"></div>
                    <div id="beforeIns-caja-errors"></div>
                </div>
            </div>
            <div class="row mt-3 mb-3">
                <div class="col-md-12 col-sm-12">
                    <label class="label-form text-muted small fw-semibold lh-base mb-3 text-start">Condiciones del cableado</label>
                    <div class="form-group row">
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chcab1-caja" value="1" name="cableado-vehiculo-caja" onchange="saveTMP();" /> Cables sueltos.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chcab2-caja" value="2" name="cableado-vehiculo-caja" onchange="saveTMP();" /> Cables sin aislamiento.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chcab3-caja" value="3" name="cableado-vehiculo-caja" onchange="saveTMP();" /> Empalme de cables excesivo.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chcab4-caja" value="4" name="cableado-vehiculo-caja" onchange="next(); saveTMP();" /> Sin observación.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chcab5-caja" value="5" name="cableado-vehiculo-caja" onchange="saveTMP();" /> Otros.</div>
                        <input class="form-control text-center input-form mt-3" id="otros-cableado-caja" name="otros-cableado-caja" type="text" placeholder="Otros (especifíca)" onchange="saveTMP();" />
                        <div id="otros-cableado-caja-errors"></div>
                        <div id="cableado-vehiculo-caja-errors"></div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!--PASO 3. DATOS DEL EQUIPO GPS-->
        <fieldset id="step-3-caja" style="display: none">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.3: Datos del equipo GPS.</h2>
                </div>
            </div>
            <div class="row mt-3 mb-5">
                <div class="col-6 py-2">
                    <label class="control-label col-md-12 text-right">Modelo de GPS</label>
                    <div class="col-md-12 form-group">
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="modelo-gps-caja" name="modelo-gps-caja" type="text" placeholder="Modelo de gps" oninput="aucompletarModeloGPSCaja();" onchange="saveTMP();" disabled />
                            <div class="input-group-addon"><span class="glyphicon glyphicon-list-alt"></span></div>
                        </div>
                        <div id="modelo-gps-caja-errors"></div>
                    </div>
                </div>
                <div class="col-6 py-2">
                    <label class="control-label col-md-12 text-right" for="imei-gps">IMEI</label>
                    <div class="col-md-12 form-group">
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="imei-gps-caja" name="imei-gps-caja" type="text" placeholder="IMEI del GPS" onchange="saveTMP();" disabled />
                        </div>
                        <div id="imei-gps-caja-errors"></div>
                    </div>
                </div>
                <div class="col-6 py-2">
                    <label class="control-label col-md-12 text-right" for="num-telefono">Número telefónico</label>
                    <div class="col-md-12 form-group">
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="num-telefono-caja" name="num-telefono-caja" type="text" placeholder="Número de teléfono" onchange="saveTMP();" disabled />
                        </div>
                        <div id="num-telefono-caja-errors"></div>
                    </div>
                </div>
                <div class="col-6 py-2">
                    <label class="control-label col-md-12 text-right" for="tipo-comprobante">Nombre del instalador</label>
                    <div class="col-md-12 form-group">
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="nombre-instalador-caja" name="nombre-instalador-caja" placeholder="Nombre del instalador" onchange="saveTMP();" disabled />
                        </div>
                        <div id="nombre-instalador-caja-errors"></div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!--PASO 4. -->
        <fieldset id="step-4-caja" style="display: none">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.4: Accesorios a instalar.</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 col-sm-12">
                    <label class="control-label"></label>
                    <div class="form-group row">
                        <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-2" type="checkbox" onchange="saveTMP();" id="chacc6-caja" value="6" name="accesorio-gps-caja" /> Sensores de puertas.</div>
                        <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-2" type="checkbox" onchange="saveTMP();" id="chacc8-caja" value="8" name="accesorio-gps-caja" /> Cámara.</div>
                        <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-2" type="checkbox" onchange="saveTMP();" id="chacc9-caja" value="9" name="accesorio-gps-caja" /> Chapa magnética.</div>
                        <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-2" type="checkbox" onchange="saveTMP();" id="chacc10-caja" value="10" name="accesorio-gps-caja" /> Solo GPS</div>
                        <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-2" type="checkbox" onchange="saveTMP();" id="chacc11-caja" value="11" name="accesorio-gps-caja" /> Solo revisión.</div>
                        <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-2" type="checkbox" onchange="next(); saveTMP();" id="chacc12-caja" value="12" name="accesorio-gps-caja" /> Ninguno.</div>
                        <div id="accesorio-gps-caja-errors"></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3 mb-3">
                <label class="text-start text-muted small fw-semibold" for="observaciones-caja">Observaciones</label>
                <div class="form-group col-md-12">
                    <div class="input-group">
                        <textarea rows="5" cols="60" id="observaciones-caja" class="form-control" placeholder="Observaciones sobre la instalación" onchange="saveTMP();"></textarea>
                        <div class="input-group-addon"></div>
                    </div>
                </div>
                <div id="observaciones-caja-errors"></div>
            </div>
        </fieldset>

        <!--PASO 5. CABLEADO FINAL-->
        <fieldset id="step-5-caja" style="display: none">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.5: Cableado final.</h2>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12 col-sm-12 form-group">
                    <label class="button-file col-md-12 col-sm-12 text-right fw-semibold text-uppercase" id="label-afterIns-caja" for="afterIns-caja">Cableado Después de Intalación / Final <span class="fas fa-plus"></span></label>
                    <input id="tabafter-caja" name="tabafter-caja" type="hidden" />
                    <input id="tabafteractualizar-caja" name="tabafteractualizar-caja" type="hidden" />
                    <input class="form-control text-center upload" id="afterIns-caja" name="afterIns-caja" hidden type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgDespuesInst();" />
                    <div id="imgafterIns-caja" class="col-6 mt-3"></div>
                    <div id="afterIns-caja-errors"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 col-sm-12">
                    <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Lista de instalación</label>
                    <div class="form-group row">
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chins1-caja" value="1" name="instalacion-list-caja" onchange="saveTMP();" /> GPS fijo.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chins2-caja" value="2" name="instalacion-list-caja" onchange="saveTMP();" /> Arnés protegido.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chins4-caja" value="4" name="instalacion-list-caja" onchange="saveTMP();" /> Conexiones del arnés al GPS conectadas y protegidas.</div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-center py-2"><input class="input-check me-2" type="checkbox" id="chins5-caja" value="5" name="instalacion-list-caja" onchange="saveTMP();" /> Accesorios bien sujetados.</div>
                        <div class="col-md-6 col-sm-12 py-2 row">
                            <div class="d-flex align-items-center col-10">
                                <input id="filech13-10-caja" type="hidden" val="0" />
                                <input class="input-check me-2" type="checkbox" id="chins13-caja" value="13" name="instalacion-list-caja" onchange="saveTMP(); validaCheck(13, '10-caja', 'chins13-caja')" /> Cámaras.
                                <input id="evidencia13-10-caja" type="file" accept=".png, .jpg, .jpeg" hidden onchange="cargarImgInstalacion(13, 10,'evidencia13-10');" />
                            </div>
                            <div id="img-evidencia13-10-caja" class="col-1 text-center"></div>
                            <div id="instalacion-list-caja-errors"></div>
                        </div>
                    </div>
                </div>
        </fieldset>

        <!--PASO 6. EVIDENCIAS FINALES-->
        <fieldset id="step-6-caja" style="display: none">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.6: Evidencias finales.</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    <div class="col-md-12 col-sm-12 form-group text-right">
                        <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" onclick="showModalTitIMG()">Otras Fotos <span class="fas fa-plus"></span></label>
                        <input id="filename-caja" name="filename-caja" type="hidden" />
                        <input class="form-control text-center upload" id="imagen-caja" name="imagen-caja[]" hidden type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgInstalacion();" />
                    </div>
                    <div class="col-md-12 col-sm-12 form-group mt-2">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                            <tbody id="img-table-caja">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-6">
                    <div class="col-md-12 col-sm-12 form-group text-right">
                        <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" for="video-caja">Otros Videos <span class="fas fa-plus"></span></label>
                        <input id="nvideos-caja" name="nvideos-caja" type="hidden" />
                        <input id="vidname-caja" name="vidname-caja" type="hidden" />
                        <input class="form-control text-center upload" id="video-caja" name="video-caja" hidden accept="video/*" type="file" onchange="cargarVidInstalacion();" onblur="resetHideChPaso();" />
                    </div>
                    <div class="col-md-12 col-sm-12 form-group mt-2">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                            <tbody id="vid-table-caja">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <label class="label-form text-muted small fw-semibold lh-base mb-1 text-start" for="observaciones-gral-caja">Observaciones generales</label>
                <div class="form-group col-md-12">
                    <div class="input-group">
                        <textarea rows="5" cols="60" id="observaciones-gral-caja" class="form-control" placeholder="Observaciones sobre la instalación" onchange="saveTMP();"></textarea>
                        <div class="input-group-addon"></div>
                    </div>
                </div>
                <div id="observaciones-gral-caja-errors"></div>
            </div>
        </fieldset>

        <!--PASO 7. VISTA PREVIA-->
        <fieldset id="step-7-caja" style="display: none">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Vista previa</h2>
                </div>
            </div>
            <div id="resume-caja">

            </div>
            <br>
            <input type="hidden" id="firma-actual-caja" value="" />
            <div class="row col-4" id="div-firma-caja"></div>
            <div class="row">
                <div class="col-md-6">
                    <label for="certificado-csd-caja" class="control-label" style="text-align: justify;">El cliente acepta lo reflejado en este documento, y también acepta que la empresa que proporcionó el servicio de instalación, así como sus empleados, no se hacen responsables por daños causados antes, durante o después de la instalación. Para conservar la garantía del sistema, es muy importante evitar que éste sea manipulado o alterado por personal ajeno a la empresa y esto incluye a técnicos de agencias de vehículos nuevos. La empresa no responderá por ningún tipo de daño si el dispositivo o su instalación fueron previamente intervenidos por terceros. La empresa no se hace responsable por la pérdida o robo de objetos de valor personal dentro del vehículo.</label>
                </div>
                <br>
                <div class="col-md-6">
                    <label class="label-form text-muted small fw-semibold lh-base mb-1 text-start" for="certificado-csd-caja">Firma</label>
                    <div class="form-group">
                        <canvas id="firma-canvas-caja" width="455px" height="250px" class="sign-canvas2 border border-secondary-subtle shadow-sm" style="touch-action: none;"></canvas>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-sm button-file text-uppercase fw-semibold" id="clear-caja">Limpiar <span class="fas fa-eraser"></span></button>
                        <button class="btn btn-sm button-file text-uppercase fw-semibold" id="undo-caja">Borrar <span class="fas fa-backward"></span></button>
                        <button class="btn btn-sm button-file text-uppercase fw-semibold" onclick="saveTMP(123)" id="save">Guardar firma <span class="fas fa-save"></span></button>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 col-sm-12 mt-3">
                    <button id="btn-finalizar" type="button" class="col-12 btn btn-success text-uppercase fw-semibold" onclick="finalizarOrden()"> Finalizar <span class="fas fa-save"></span></button>
                </div>
            </div>
        </fieldset>
    </div>
</div>