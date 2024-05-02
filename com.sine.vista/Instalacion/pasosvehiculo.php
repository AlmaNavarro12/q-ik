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
                    <div class="col-md-8 col-sm-12 d-flex align-items-center pe-5">
                        <div class="progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 33px;">
                            <div class="progress-bar" style="width: 100%;">100%</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 d-flex justify-content-center mb-0">
                        <ul class="pagination pagination-sm">
                            <li id="li-prev" class="page-item disabled"><button class="page-link" id="btn-prev" aria-label="Anterior" onclick="prev()"><span aria-hidden="true">«</span></button></li>
                            <li id="li-1" class="active page-item"><button class="page-link" id="btn-1" onclick="goStep(1)">1</button></li>
                            <li id="li-2" class="page-item disabled"><button class="page-link" id="btn-2" onclick="goStep(2)">2</button></li>
                            <li id="li-3" class="page-item disabled"><button class="page-link" id="btn-3" onclick="goStep(3)">3</button></li>
                            <li id="li-4" class="page-item disabled"><button class="page-link" id="btn-4" onclick="goStep(4)">4</button></li>
                            <li id="li-5" class="page-item disabled"><button class="page-link" id="btn-5" onclick="goStep(5)">5</button></li>
                            <li id="li-6" class="page-item disabled"><button class="page-link" id="btn-6" onclick="goStep(6)">6</button></li>
                            <li id="li-7" class="page-item disabled"><button class="page-link" id="btn-7" onclick="goStep(7)">7</button></li>
                            <li id="li-8" class="page-item disabled"><button class="page-link" id="btn-8" onclick="goStep(8)">8</button></li>
                            <li id="li-9" class="page-item disabled"><button class="page-link" id="btn-9" onclick="goStep(9)">9</button></li>
                            <li id="li-10" class="page-item disabled"><button class="page-link" id="btn-10" onclick="goStep(10)">10</button></li>
                            <li id="li-11" class="page-item disabled"><button class="page-link" id="btn-11" onclick="goStep(11)">11</button></li>
                            <li id="li-next"><button class="page-link" id="btn-next" aria-label="Siguiente" onclick="next()"><span aria-hidden="true">»</span></button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="form-vehiculo" class="div-form py-5 px-4 ms- border border-secondary-subtle">
            <div class="row">
                <div class="col-md-6" id="img-step">
                    <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Marca los daños detectados en el vehículo <br> (imagen de referencia).</label>
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <img src="img/carro1.png" style="max-width: 90%;" />
                    </div>
                </div>
                <div class="col-md-6">
                    <!--PASO 1. FOTOS DE INICIO-->
                    <fieldset id="step-1" class="pe-4">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.1: Subir las fotos de inicio.</h2><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12 form-group">
                                <label class="button-file col-md-12 col-sm-12 text-right text-uppercase" id="label-frenteimg" for="frenteimg">Frente del Vehiculo <span class="fas fa-plus"></span></label>
                                <input id="frentepic" name="frentepic" type="hidden" />
                                <input id="frenteactualizar" name="frenteactualizar" type="hidden" />
                                <input class="form-control text-center upload" id="frenteimg" name="frenteimg" type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgFrente(1);" hidden />
                                <div id="imgfrente" class="py-3 col-12 d-flex align-items-center"></div>
                                <div id="frentepic-errors"></div>
                            </div>
                            <div class="col-md-6 col-sm-12 form-group">
                                <label class="button-file col-md-12 col-sm-12 text-right text-uppercase" id="label-vinimg" for="vinimg">No. de serie o VIN <span class="fas fa-plus"></span></label>
                                <input id="vinpic" name="vinpic" type="hidden" />
                                <input id="vinpicactualizar" name="vinpicactualizar" type="hidden" />
                                <input class="form-control text-center upload" id="vinimg" name="vinimg" type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgVin();" hidden />
                                <div id="imgvin" class="py-3 col-12 d-flex align-items-center"></div>
                                <div id="vinimg-errors"></div>
                            </div>
                        </div>
                    </fieldset>

                    <!--PASO 2. DAÑO DEL VEHICULO-->
                    <fieldset id="step-2" style="display: none">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.2: Daños en el vehículo.</h2><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Marca las zonas donde se presenten daños en el vehiculo.</label>
                            </div>
                            <input type="hidden" id="hidpaso" value="">
                            <input type="hidden" id="hidcheck" value="">
                            <div class="col-md-12">
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech1-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch1" value="1" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(1, 2, 'ch1'); quitacheck('ch9');" /> Parachoques delantero
                                        <input id="evidencia1-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(1,2,'evidencia1-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia1-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech2-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch2" value="2" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(2, 2, 'ch2'); quitacheck('ch9');" /> Parachoques trasero
                                        <input id="evidencia2-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(2,2,'evidencia2-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia2-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech3-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch3" value="3" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(3, 2, 'ch3'); quitacheck('ch9');" /> Lateral izquierdo
                                        <input id="evidencia3-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(3,2,'evidencia3-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia3-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech4-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch4" value="4" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(4, 2, 'ch4'); quitacheck('ch9');" /> Lateral derecho
                                        <input id="evidencia4-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(4,2,'evidencia4-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia4-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech5-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch5" value="5" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(5, 2, 'ch5'); quitacheck('ch9');" /> Parabrisas
                                        <input id="evidencia5-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(5,2,'evidencia5-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia5-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech6-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch6" value="6" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(6, 2, 'ch6'); quitacheck('ch9');" /> Cajuela
                                        <input id="evidencia6-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(6,2,'evidencia6-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia6-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech7-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch7" value="7" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(7, 2, 'ch7'); quitacheck('ch9');" /> Cofre
                                        <input id="evidencia7-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(7,2,'evidencia7-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia7-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 row">
                                    <div class="d-flex align-items-center py-2 col-11">
                                        <input id="filech8-2" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="ch8" value="8" name="danhos-vehiculo" onchange="saveTMP(); validaCheck(8, 2, 'ch8'); quitacheck('ch9');" /> Techo
                                        <input id="evidencia8-2" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(8,2,'evidencia8-2');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia8-2"></div>
                                </div>
                                <div class="col-md-11 col-sm-12 d-flex align-items-center py-2">
                                    <input class="input-check me-3" type="checkbox" id="ch9" value="9" name="danhos-vehiculo" onchange="quitaseleccion('danhos-vehiculo', 'ch9'); next();" /> Sin daños
                                </div>
                                <div id="danhos-vehiculo-errors"></div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!--PASO 3. CONDICIONES DE MOLDADURAS-->
                <fieldset id="step-3" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.3: Moldaduras del vehículo.</h2>
                        </div>
                    </div>
                    <div class="col-12 ps-4 mt-4">
                        <div class="col-md-12 col-sm-12 mb-3">
                            <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Selecciona las condiciones de molduras del vehículo</label>
                        </div>
                        <div class="row ps-3">
                            <div class="col-6 py-2">
                                <div class="row">
                                    <div class="d-flex align-items-center row col-11">
                                        <input id="filech1-3" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="chmol1" value="1" name="molduras-vehiculo" onchange="saveTMP(); validaCheck(1, 3, 'chmol1'); quitacheck('chmol3');" /> Molduras dañadas <br> (Rotas, maltratadas, marcadas, ralladas, etc).
                                        <input id="evidencia1-3" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(1,3,'evidencia1-3');" />
                                    </div>
                                    <div class="col-md-1 text-end" id="img-evidencia1-3"></div>
                                </div>
                            </div>
                            <div class="col-6 py-2">
                                <div class="row">
                                    <div class="d-flex align-items-center row col-11">
                                        <input id="filech2-3" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="chmol2" value="2" name="molduras-vehiculo" onchange="saveTMP(); validaCheck(2, 3, 'chmol2'); quitacheck('chmol3');" /> Tornillos, grapas o pijas faltantes.
                                        <input id="evidencia2-3" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(2,3,'evidencia2-3');" />
                                    </div>
                                    <div class=" col-md-1 d-flex justify-content-end" id="img-evidencia2-3"></div>
                                </div>
                            </div>
                            <div class="col-6 py-2 d-flex align-items-center">
                                <div class="row">
                                    <input class="input-check me-3 col-12" type="checkbox" id="chmol3" value="3" name="molduras-vehiculo" onchange="quitaseleccion('molduras-vehiculo', 'chmol3'); next();" /> Sin observación.
                                </div>
                            </div>
                            <div class="col-6 py-2">
                                <div class="row">
                                    <div class="d-flex align-items-center row col-11">
                                        <input id="filech4-3" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="chmol4" value="4" name="molduras-vehiculo" onchange="saveTMP(); validaCheck(4, 3, 'chmol4'); quitacheck('chmol3');" /> Otros
                                        <input id="evidencia4-3" hidden accept=".png, .jpg, .jpeg" type="file" onchange="cargarImgInstalacion(4,3,'evidencia4-3');" />
                                    </div>
                                    <div class=" col-md-1 d-flex justify-content-end" id="img-evidencia4-3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 py-2">
                            <input class="form-control text-center input-form" id="otros-molduras" name="otros-molduras" type="text" placeholder="Otros (específica)" onchange="saveTMP();" />
                            <div id="otros-molduras-errors"></div>
                        </div>
                        <div id="molduras-vehiculo-errors"></div>
                    </div>
                </fieldset>

                <!--PASO 4. TABLERO INICIAL-->
                <fieldset id="step-4" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.4: Tablero Inicial.</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 form-group mt-4">
                            <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" id="label-tabiimg" for="tabiimg">Tablero inicial <span class="fas fa-plus"></span></label>
                            <input id="tabipic" name="tabipic" type="hidden" />
                            <input id="tabipicactualizar" name="tabipicactualizar" type="hidden" />
                            <input class="form-control text-center upload" id="tabiimg" name="tabiimg" hidden type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgTableroInicial();" />
                            <div id="imgtabi" class="col-6 mt-3"></div>
                            <div id="tabiimg-errors"></div>
                        </div>
                    </div>
                    <div class="row mt-4 mb-3">
                        <div class="col-md-12 col-sm-12">
                            <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Condiciones sobre el tablero del vehículo</label>
                            <div class="form-group row">
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chtab1" value="1" name="tablero-vehiculo" onchange="saveTMP(); quitacheck('chtab5');" /> Testigos encendidos (Motor, servicio, aceite, temperatura, etc.)</div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chtab2" value="2" name="tablero-vehiculo" onchange="saveTMP(); quitacheck('chtab5');" /> No enciende tablero.</div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chtab3" value="3" name="tablero-vehiculo" onchange="saveTMP(); quitacheck('chtab5');" /> No marca gasolina, RPM, KM, temperatura, etc.</div>
                                <div class="col-md-6 col-sm-12 py-2">
                                    <div class="d-flex align-items-center">
                                        <input id="filech4-4" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="chtab4" value="4" name="tablero-vehiculo" onchange="saveTMP(); validaCheck(4, 4, 'chtab4')" /> Arnés o contra arnés dañado del clúster de instrumentos.
                                        <input id="evidencia4-4" type="file" accept=".png, .jpg, .jpeg" hidden onchange="cargarImgInstalacion(4,4,'evidencia4-4');" />
                                    </div>
                                    <div id="img-evidencia4-4"></div>
                                </div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chtab5" value="5" name="tablero-vehiculo" onchange="quitaseleccion('tablero-vehiculo', 'chtab5'); next();" /> Sin observación.</div>
                                <div class="col-md-6 col-sm-12 py-2">
                                    <div class="d-flex align-items-center ">
                                        <input id="filech6-4" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="chtab6" value="6" name="tablero-vehiculo" onchange="saveTMP(); validaCheck(6, 4, 'chtab6'); quitacheck('chtab5');" /> Otros.
                                        <input id="evidencia6-4" type="file" accept=".png, .jpg, .jpeg" hidden onchange="cargarImgInstalacion(6,4,'evidencia6-4');" />
                                    </div>
                                    <div id="img-evidencia6-4"></div>
                                </div>
                                <div class="mt-3">
                                    <input class="form-control text-center input-form" id="otros-tablero" name="otros-tablero" type="text" placeholder="Otros (específica)" onchange="saveTMP();" />
                                </div>
                                <div id="otros-tablero-errors"></div>
                                <div id="tablero-vehiculo-errors"></div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!--PASO 5. CABLEADO INICIAL-->
                <fieldset id="step-5" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.5: Cableado Inicial.</h2>
                        </div>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class="col-md-12 col-sm-12 form-group">
                            <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" id="label-beforeIns" for="beforeIns">Cableado Antes de Instalación / Revisión <span class="fas fa-plus"></span></label>
                            <input id="tabbefore" name="tabbefore" type="hidden" />
                            <input id="tabbeforeactualizar" name="tabbeforeactualizar" type="hidden" />
                            <input class="form-control text-center upload" id="beforeIns" name="beforeIns" hidden type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgAntesInst();" />
                            <div id="imgbeforeIns" class="mt-3 col-6"></div>
                            <div id="beforeIns-errors"></div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12">
                            <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Condiciones del cableado</label>
                            <div class="form-group row">
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chcab1" value="1" name="cableado-vehiculo" onchange="saveTMP(); quitacheck('chcab4');" /> Cables sueltos.</div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chcab2" value="2" name="cableado-vehiculo" onchange="saveTMP(); quitacheck('chcab4');" /> Cables sin aislamiento.</div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chcab3" value="3" name="cableado-vehiculo" onchange="saveTMP(); quitacheck('chcab4');" /> Empalme de cables excesivo.</div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chcab4" value="4" name="cableado-vehiculo" onchange="quitaseleccion('cableado-vehiculo', 'chcab4'); next();" /> Sin observación.</div>
                                <div class="d-flex align-items-center col-md-6 col-sm-12 py-2"><input class="input-check me-3" type="checkbox" id="chcab5" value="5" name="cableado-vehiculo" onchange="saveTMP(); quitacheck('chcab4');" /> Otros.</div>
                                <div class="mt-4 px-1">
                                    <input class="form-control text-center input-form" id="otros-cableado" name="otros-cableado" type="text" placeholder="Otros (específica)" onchange="saveTMP();" />
                                </div>
                                <div id="otros-cableado-errors"></div>
                                <div id="cableado-vehiculo-errors"></div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!--PASO 6. CORTA CORRIENTE-->
                <fieldset id="step-6" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.6: Corta corriente.</h2>
                        </div>
                    </div>
                    <div class="row mt-4 mb-3">
                        <div class="col-md-12 col-sm-12">
                            <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">El Vehículo cuenta "Ya" con sistema de corta corriente.</label>
                            <div class="form-group row">
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chtcor1" value="1" name="tcorriente-vehiculo" onchange="saveTMP(); quitacheck('chtcor4');" /> Alarma con corta corriente activo.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chtcor2" value="2" name="tcorriente-vehiculo" onchange="saveTMP(); quitacheck('chtcor4');" /> GPS con corta corriente activo.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chtcor3" value="3" name="tcorriente-vehiculo" onchange="saveTMP(); quitacheck('chtcor4');" /> Switch corta corriente.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chtcor4" value="4" name="tcorriente-vehiculo" onchange="quitaseleccion('tcorriente-vehiculo', 'chtcor4'); next();" /> NO CUENTA.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chtcor5" value="5" name="tcorriente-vehiculo" onchange="saveTMP(); quitacheck('chtcor4');" /> Otros.</div>
                                <div class="mt-3">
                                    <input class="form-control text-center input-form" id="otros-ccorriente" name="otros-ccorriente" type="text" placeholder="Otros (específica)" onchange="saveTMP();" />
                                </div>
                                <div id="otros-ccorriente-errors"></div>
                                <div id="tcorriente-vehiculo-errors"></div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!--PASO 7. EQUIPO GPS-->
                <fieldset id="step-7" style="display: none">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.7: Datos del equipo GPS.</h2>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row mt-4 mb-5">
                            <div class="col-6 py-2">
                                <label class="control-label col-md-12 text-right">Modelo de GPS</label>
                                <div class="col-md-12 form-group">
                                    <div class="input-group">
                                        <input type="hidden" id="existeModelo" value="">
                                        <input class="form-control text-center input-form" id="modelo-gps-step" name="modelo-gps-step" type="text" placeholder="Modelo de gps" oninput="aucompletarModeloGPSStep();" onchange="saveTMP();" disabled />
                                    </div>
                                    <div id="modelo-gps-errors"></div>
                                </div>
                            </div>
                            <div class="col-6 py-2">
                                <label class="control-label col-md-12 text-right" for="imei-gps">IMEI</label>
                                <div class="col-md-12 form-group">
                                    <div class="input-group">
                                        <input class="form-control text-center input-form" id="imei-gps-step" name="imei-gps-step" type="text" placeholder="IMEI del GPS" onchange="saveTMP();" disabled />
                                    </div>
                                    <div id="imei-gps-errors"></div>
                                </div>
                            </div>
                            <div class="col-6 py-2">
                                <label class="control-label col-md-12 text-right" for="num-telefono">Número telefónico</label>
                                <div class="col-md-12 form-group">
                                    <div class="input-group">
                                        <input class="form-control text-center input-form" id="num-telefono-step" name="num-telefono-step" type="text" placeholder="Número de teléfono" onchange="saveTMP();" disabled />
                                    </div>
                                    <div id="num-telefono-errors"></div>
                                </div>
                            </div>
                            <div class="col-6 py-2">
                                <label class="control-label col-md-12 text-right" for="tipo-comprobante">Nombre del instalador</label>
                                <div class="col-md-12 form-group">
                                    <div class="input-group">
                                        <input class="form-control text-center input-form" id="nombre-instalador-step" name="nombre-instalador-step" placeholder="Nombre del instalador" onchange="saveTMP();" disabled />
                                    </div>
                                    <div id="nombre-instalador-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!--PASO 8. ACCESORIOS-->
                <fieldset id="step-8" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.8: Accesorios a instalar.</h2>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12">
                            <label class="control-label"></label>
                            <div class="form-group row">
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc1-step" value="1" name="accesorio-gps-step" /> Botón de pánico.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc2-step" value="2" name="accesorio-gps-step" /> Bocina.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc3-step" value="3" name="accesorio-gps-step" /> Micrófono.</div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="d-flex align-items-center py-2 col-12"><input class="input-check me-3" type="checkbox" onchange="verifyTipoCorte();" id="chacc4-step" value="4" name="accesorio-gps-step" /> Corte de corriente/ combustible.</div>
                                    <div id="container-tipo-corte" class="col-12 collapse mt-2 mb-2">
                                        <div class="control-label col-md-12" for="tipo-corte">Tipo de corte</div>
                                        <div class="col-md-12 form-group">
                                            <div class="input-group">
                                                <select class="form-select text-center input-form" id="tipo-corte" name="tipo-corte" onchange="saveTMP();">
                                                    <option value="">- - - -</option>
                                                    <option class="text-start" value="Corte a marcha">Corte a marcha</option>
                                                    <option class="text-start" value="Corte a ignición">Corte a ignición</option>
                                                    <option class="text-start" value="Corte a combustible">Corte a combustible</option>
                                                </select>
                                                <div class="input-group-addon"><span class="glyphicon glyphicon-list-alt"></span></div>
                                            </div>
                                            <div id="tipo-corte-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc5-step" value="5" name="accesorio-gps-step" /> Sensor de gasolina.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc6-step" value="6" name="accesorio-gps-step" /> Sensores de puertas.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc8-step" value="8" name="accesorio-gps-step" /> Cámara.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc9-step" value="9" name="accesorio-gps-step" /> Chapa magnética.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc10-step" value="10" name="accesorio-gps-step" /> Solo GPS.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc11-step" value="11" name="accesorio-gps-step" /> Solo revisión.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="saveTMP();" id="chacc13-step" value="13" name="accesorio-gps-step" /> Claxón.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" onchange="next(); saveTMP();" id="chacc12-step" value="12" name="accesorio-gps-step" /> Ninguno.</div>
                                <div id="accesorio-gps-step-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <label class="control-label col-md-12 text-start text-muted fw-semibold" for="observaciones">Observaciones</label>
                        <div class="form-group col-md-12">
                            <div class="input-group">
                                <textarea rows="5" cols="60" id="observaciones-step" class="form-control" placeholder="Observaciones sobre la instalación" onchange="saveTMP();"></textarea>
                                <div class="input-group-addon"></div>
                            </div>
                        </div>
                        <div id="observaciones-step-errors"></div>
                    </div>

                </fieldset>

                <!--PASO 9. CABLEADO FINAL-->
                <fieldset id="step-9" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.9: Cableado final.</h2>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12 form-group">
                            <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" id="label-afterIns" for="afterIns">Cableado Después de Intalación / Final <span class="fas fa-plus"></span></label>
                            <input id="tabafter" name="tabafter" type="hidden" />
                            <input id="tabafteractualizar" name="tabafteractualizar" type="hidden" />
                            <input class="form-control text-center upload" id="afterIns" name="afterIns" hidden type="file" accept=".png, .jpg, .jpeg" onchange="cargarImgDespuesInst();" />
                            <div id="imgafterIns" class="col-6 mt-3"></div>
                            <div id="afterIns-errors"></div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12">
                            <label class="label-form text-muted small fw-semibold lh-base mb-4 text-start">Lista de instalación</label>
                            <div class="form-group row">
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chins1" value="1" name="instalacion-list" onchange="saveTMP();" /> GPS fijo.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chins2" value="2" name="instalacion-list" onchange="saveTMP();" /> Arnés protegido.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chins3" value="3" name="instalacion-list" onchange="saveTMP();" /> Corte de corriente/combustible protegido.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chins4" value="4" name="instalacion-list" onchange="saveTMP();" /> Conexiones del arnés al GPS conectadas y protegidas.</div>
                                <div class="d-flex align-items-center py-2 col-md-6 col-sm-12"><input class="input-check me-3" type="checkbox" id="chins5" value="5" name="instalacion-list" onchange="saveTMP();" /> Accesorios bien sujetados.</div>
                                <div class="col-md-6 col-sm-12 py-2">
                                    <div class="col-12 row">
                                        <div class="d-flex align-items-center col-10">
                                            <input id="filech6-10" type="hidden" val=""/>
                                            <input class="input-check me-3" type="checkbox" id="chins6" value="6" name="instalacion-list" onchange="saveTMP(); validaCheckVideo(6, 10, 'chins6')" /> Apagado exitoso.
                                            <input id="evidencia6-10" type="file" hidden accept="video/*" onchange="cargarVidInstalacion(6,10,'evidencia6-10');" />
                                        </div>
                                        <div id="img-evidencia6-10" class="col-1 text-center"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 py-2">
                                    <div class="col-12 row">
                                        <div class="d-flex align-items-center col-11">
                                            <input id="filech7-10" type="hidden" val="" />
                                            <input class="input-check me-3" type="checkbox" id="chins7" value="7" name="instalacion-list" onchange="saveTMP(); validaCheckVideo(7, 10, 'chins7')" /> Desbloqueo exitoso.
                                            <input id="evidencia7-10" type="file" hidden accept="video/*" onchange="cargarVidInstalacion(7,10,'evidencia7-10');" />
                                        </div>
                                        <div id="img-evidencia7-10" class="col-1 text-center"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 py-2">
                                    <div class="col-12 row">
                                        <div class="d-flex align-items-center col-10">
                                            <input id="filech9-10" type="hidden" val="" />
                                            <input class="input-check me-3" type="checkbox" id="chins9" value="9" name="instalacion-list" onchange="saveTMP(); validaCheckVideo(9, 10, 'chins9')" /> Claxón exitoso.
                                            <input id="evidencia9-10" type="file" hidden accept="video/*" onchange="cargarVidInstalacion(9,10,'evidencia9-10');" />
                                        </div>
                                        <div id="img-evidencia9-10" class="col-1 text-center"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 py-2">
                                    <label class="d-flex align-items-center">
                                        <input class="input-check me-3" type="checkbox" id="chins10" value="10" name="instalacion-list" onchange="verificaCheckBtnPanic();" /> Botón de pánico.
                                    </label>
                                    <div id="complementoBtnPanico" class="row collapse" style="margin-top: 2rem;">
                                        <div class="d-flex align-items-center py-2 form-group">
                                            <input id="filech11-10" type="hidden" val="0" />
                                            <label class="button-file col-md-10 col-xs-10 text-right me-2 text-uppercase" for="evidencia11-10">Foto lugar donde quedó <span class="fas fa-plus"></span></label>
                                            <input id="evidencia11-10" type="file" hidden accept=".png, .jpg, .jpeg" onchange="cargarImgInstalacion(11, 10,'evidencia11-10');" />
                                            <div id="img-evidencia11-10" class="text-center col-2"></div>
                                        </div>
                                        <div class="d-flex align-items-center py-2  form-group">
                                            <input id="filech12-10" type="hidden" val="0" />
                                            <label class="button-file col-md-10 col-xs-10 text-right me-2 text-uppercase" for="evidencia12-10">Foto notificación <span class="fas fa-plus"></span></label>
                                            <input id="evidencia12-10" type="file" hidden accept=".png, .jpg, .jpeg" onchange="cargarImgInstalacion(12, 10,'evidencia12-10');" />
                                            <div id="img-evidencia12-10" class="text-center col-2"></div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label for="descUbicacion" class="text-start text-muted fw-semibold small">Descripión de ubicación</label>
                                            <div class="form-group col-md-12">
                                                <div class="input-group">
                                                    <textarea rows="5" cols="60" id="descUbicacion" class="form-control" placeholder="Dónde quedó instalado el botón de pánico" onchange="saveTMP();"></textarea>
                                                </div>
                                            </div>
                                            <div class="descUbicacion-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 py-2 ">
                                    <div class="row col-12">
                                    <div class="d-flex align-items-center col-10">
                                        <input id="filech13-10" type="hidden" val="0" />
                                        <input class="input-check me-3" type="checkbox" id="chins13" value="13" name="instalacion-list" onchange="saveTMP(); validaCheck(13, 10, 'chins13')" /> Cámaras.
                                        <input id="evidencia13-10" type="file" hidden accept=".png, .jpg, .jpeg" onchange="cargarImgInstalacion(13, 10,'evidencia13-10');" />
                                    </div>
                                    <div id="img-evidencia13-10" class="col-1 text-center"></div>
                                    </div>
                                </div>
                                <div id="instalacion-list-errors"></div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!--PASO 10. EVIDENCIAS FINALES-->
                <fieldset id="step-10" style="display: none">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Paso No.10: Evidencias finales.</h2>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="col-md-12 col-sm-12 form-group">
                            <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" id="label-tabfimg" for="tabfimg">Tablero Final <span class="fas fa-plus"></span></label>
                            <input id="tabfpic" name="tabfpic" type="hidden" />
                            <input id="tabfpicactualizar" name="tabfpicactualizar" type="hidden" />
                            <input class="form-control text-center upload" id="tabfimg" name="tabfimg" type="file" accept=".png, .jpg, .jpeg" hidden onchange="cargarImgTableroFinal();" />
                            <div id="imgtabf" class="col-7 mt-3 mb-3"></div>
                            <div id="tabfimg-errors"></div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="col-md-12 col-sm-12 form-group text-right">
                                    <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" onclick="showModalTitIMG()">Otras fotos<span class="fas fa-plus"></span></label>
                                    <input id="filename" name="filename" type="hidden" />
                                    <input class="form-control text-center upload" id="imagen" name="imagen[]" accept=".png, .jpg, .jpeg" type="file" hidden onchange="cargarImgInstalacion();" />
                                </div>
                                <div class="col-md-12 col-sm-12 form-group mt-2" style="min-height: 5rem;">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                                        <tbody id="img-table">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12 col-sm-12 form-group text-right">
                                    <label class="button-file col-md-12 col-sm-12 text-right text-uppercase fw-semibold" for="video">Otros Videos <span class="fas fa-plus"></span></label>
                                    <input id="nvideos" name="nvideos" type="hidden" />
                                    <input id="vidname" name="vidname" type="hidden" />
                                    <input class="form-control text-center upload" id="video" name="video" accept="video/*" type="file" hidden onchange="cargarVidInstalacion();" onblur="resetHideChPaso();" />
                                </div>
                                <div class="col-md-12 col-sm-12 form-group mt-2">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                                        <tbody id="vid-table">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <label class="label-form text-muted small fw-semibold lh-base mb-1 text-start" for="observaciones-gral">Observaciones generales</label>
                            <div class="form-group col-md-12">
                                <div class="input-group">
                                    <textarea rows="5" cols="60" id="observaciones-gral" class="form-control" placeholder="Observaciones sobre la instalación" onchange="saveTMP();"></textarea>
                                    <div class="input-group-addon"></div>
                                </div>
                            </div>
                            <div class="observaciones-gral-errors"></div>
                        </div>
                    </div>
                </fieldset>

                <!--PASO 11. VISTA PREVIA-->
                <fieldset id="step-11" style="display: none">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-center titulo-lista fs-5 fw-semibold text-uppercase">Vista previa</h2>
                        </div>
                    </div>
                    <div id="resume">
                    </div>
                    <input type="hidden" id="firma-actual" value="" />
                    <div class="row col-4" id="div-firma"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" style="text-align: justify;" for="firma-canvas">El cliente acepta lo reflejado en este documento, y también acepta que la empresa que proporcionó el servicio de instalación, así como sus empleados, no se hacen responsables por daños causados antes, durante o después de la instalación. Para conservar la garantía del sistema, es muy importante evitar que éste sea manipulado o alterado por personal ajeno a la empresa y esto incluye a técnicos de agencias de vehículos nuevos. La empresa no responderá por ningún tipo de daño si el dispositivo o su instalación fueron previamente intervenidos por terceros. La empresa no se hace responsable por la pérdida o robo de objetos de valor personal dentro del vehículo.</label>
                        </div>
                        <br>
                        <div class="col-md-6">
                            <label class="label-form text-muted small fw-semibold lh-base mb-1 text-start" for="firma-canvas">Firma</label>
                            <div class="form-group col-12 d-flex justify-content-center">
                                <canvas id="firma-canvas" width="450px" height="250px" class="sign-canvas2 border border-secondary-subtle shadow-sm" style="touch-action: none;"></canvas>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm button-file text-uppercase fw-semibold" id="clear">Limpiar <span class="fas fa-eraser"></span></button>
                                <button class="btn btn-sm button-file text-uppercase fw-semibold" id="undo">Borrar <span class="fas fa-backward"></span></button>
                                <button class="btn btn-sm button-file text-uppercase fw-semibold" onclick="saveTMP(123)" id="save">Guardar firma <span class="fas fa-save"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-12 mt-3">
                            <button id="btn-finalizar" type="button" class="col-12 btn btn-success text-uppercase fw-semibold" onclick="finalizarOrden()"> Finalizar <span class="fas fa-save"></span></button>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>