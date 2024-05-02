<?php
include 'modals.php';
?>
<div id="form-instalacion">
    <div class="panel ps-3">
        <div id="inicioOrden">
            <div class="panel-heading fijo z-2">
                <div class="h4 titulo-lista text-start" id="contenedor-titulo-form-instalacion"> Formulario de servicio </div>
            </div>
            <div class="panel-body div-form p-5 border border-secondary-subtle">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end mb-4">
                        <label class="fw-bold text-danger small">* Campo obligatorio</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center py-2">
                        <div class="col-4 mb-2" for="folio-servicio"><label class="label-form text-right">Folio </label> <label class="mark-required text-danger fw-bold">*</label></div>
                        <div class="form-group col-8">
                            <select class="form-select text-center input-form" id="folio-servicio" name="folio-servicio">
                                <option value="" id="option-default-folio">- - - - -</option>
                                <optgroup id="folioinstalacion" class="contenedor-folios text-start"> </optgroup>
                            </select>
                            <div id="folio-servicio-errors"></div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center py-2">
                        <div class="col-4"><label class="label-form text-right" for="fecha-servicio">Fecha de servicio</label> <label class="mark-required text-danger fw-bold">*</label></div>

                        <div class="col-8 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form z-1" id="fecha-servicio" name="fecha-servicio" type="date" />
                                <!--<div class="input-group-text"><span class="text-body-tertiary far fa-calendar"></span></div>-->
                            </div>
                            <div id="fecha-servicio-errors">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center py-2">
                        <div class="col-4"><label class="label-form text-right" for="hora-servicio">Hora de servicio </label> <label class="mark-required text-danger fw-bold">*</label></div>
                        <div class="col-8 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form z-1" id="hora-servicio" name="hora-servicio" type="time" />
                                <!--<div class="input-group-text"><span class="text-body-tertiary far fa-clock"></span></div>-->
                            </div>
                            <div id="hora-servicio-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center py-2">
                        <div class="col-4"><label class="label-form text-right" for="nom-cliente">Nombre del cliente </label> <label class="mark-required text-danger fw-bold">*</label></div>
                        <div class="col-8 form-group">
                            <div class="input-group">
                                <input type="hidden" class="form-control" id="id-cliente">
                                <input class="form-control text-center input-form z-1" id="nombre-cliente" name="nombre-cliente" placeholder="Nombre del cliente" type="text" oninput="aucompletarClienteIns()" />
                                <!--<div class="input-group-text"><span class="text-body-tertiary far fa-user"></span></div>-->
                            </div>
                            <div id="nom-cliente-errors">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center py-2">
                        <div class="col-4"><label class="label-form text-right" for="plataforma">Plataforma </label> <label class="mark-required text-danger fw-bold">*</label></div>
                        <div class="col-8 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form z-1" id="plataforma" name="plataforma" placeholder="Plataforma" type="text" />
                                <!--<div class="input-group-text"><span class="text-body-tertiary fas fa-chalkboard"></span></div>-->
                            </div>
                            <div id="plataforma-errors">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="datos-vehiculo" class="mt-5">
                    <div class="fs-6 label-sub text-start" id="contenedor-datos-vehiculo"> Datos del Vehículo <!--<span class="lnr lnr-bus icon-size"></span>--></div>
                    <div class="row py-2">
                        <div class="col-md-6 form-group">
                            <fieldset>
                                <div class="row">
                                    <label class="label-form col-4 text-right" for="tipo-unidad">Tipo </label>
                                    <div class="col-4">
                                        <label class="d-flex align-items-center justify-content-center label-radio">
                                            <input type="radio" class="input-radio-sm me-2" id="tipo1" name="tipo-unidad" value="1" checked="" onclick="checkTipo();"> <span class="fw-semibold text-primary-emphasis"> Vehículo</span>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="d-flex align-items-center justify-content-center label-radio">
                                            <input type="radio" class="input-radio-sm me-2" id="tipo2" name="tipo-unidad" value="2" onclick="checkTipo();"> <span class="fw-semibold text-primary-emphasis"> Caja</span>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="marca-unidad">Marca </label>  <label id="label-marca" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="marca-unidad" name="marca-unidad" type="text" placeholder="Marca de la unidad" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary fas fa-tag"></span></div>-->
                                </div>
                                <div id="marca-unidad-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="modelo-unidad">Modelo </label> <label id="label-modelo" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="modelo-unidad" name="modelo-unidad" type="text" placeholder="Modelo de la unidad" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary fas fa-tag"></span></div>-->
                                </div>
                                <div id="modelo-unidad-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="anho-unidad">Año </label> <label id="label-anho" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="anho-unidad" name="anho-unidad" type="text" placeholder="Año unidad" oninput="validarNum(this);" maxlength="4" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary far fa-calendar"></span></div>-->
                                </div>
                                <div id="anho-unidad-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="color-unidad">Color </label> <label id="label-color" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="color-unidad" name="color-unidad" type="text" placeholder="Color de la unidad" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary fas fa-edit"></span></div>-->
                                </div>
                                <div id="color-unidad-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="serie-unidad">Serie </label> <label id="label-serie" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="serie-unidad" name="serie-unidad" type="text" placeholder="Serie de la unidad" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary fas fa-barcode"></span></div>-->
                                </div>
                                <div id="serie-unidad-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="num-economico">No. Económico </label> <label id="label-economico" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="num-economico" name="num-economico" type="text" placeholder="No. económico de la unidad" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                </div>
                                <div id="num-economico-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="km-unidad" >Kilometraje </label> <label id="label-kilometraje" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="km-unidad" name="km-unidad" type="text" placeholder="Kilometraje de la unidad" oninput="validarNum(this)" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                </div>
                                <div id="km-unidad-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center py-2">
                            <div class="col-4"><label class="label-form text-right" for="placas-unidad" >Placas </label> <label id="label-placas" class='mark-required text-danger fw-bold'>*</label></div>
                            <div class="col-8 form-group">
                                <div class="input-group">
                                    <input class="form-control text-center input-form z-1" id="placas-unidad" name="placas-unidad" type="text" placeholder="Placas de la unidad (sin guiones ni espacios)" oninput="validarPlacas(this);" />
                                    <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                </div>
                                <div id="placas-unidad-errors"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="datos-servicio" class="mt-5">
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <div class="fs-6 label-sub text-start">Tipo de servicio <label class=' fs-5 mark-required text-danger fw-bold'>*</label><!--<span class="lnr lnr-tablet icon-size">--> </div>
                            <div class="form-group row">
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv1" value="1" name="tservicio-vehiculo"> Instalación</div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv2" value="2" name="tservicio-vehiculo"> Reubicación</div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv3" value="3" name="tservicio-vehiculo"> Reposición (garantia)</div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv4" value="4" name="tservicio-vehiculo"> Retiro</div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv5" value="5" name="tservicio-vehiculo"> Revisión</div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv6" value="6" name="tservicio-vehiculo"> Cambio de unidad</div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv8" value="8" name="tservicio-vehiculo" onclick="hideInput()"> Cambio de equipo</div>
                                <div id="div-imei" style="display: none;" class="toHide">
                                    <input class="form-control text-center input-form z-1 mt-3 mb-3" id="modelo-anterior" name="modelo-anterior" type="text" placeholder="Modelo anterior" oninput="aucompletarModeloGPS2()">
                                    <div id="modelo-anterior-errors"></div>
                                    <input class="form-control text-center input-form z-1 mt-3 mb-3" id="imei-anterior" name="imei-anterior" type="text" placeholder="IMEI anterior" maxlength="15" oninput="validarNum(this)">
                                    <div id="imei-anterior-errors"></div>
                                </div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv9" value="9" name="tservicio-vehiculo" onclick="hideInput()"> Cambio de SIM</div>
                                <div id="div-sim" style="display: none;" class="toHide mt-3 "><input class="form-control text-center input-form z-1" id="tel-anterior" name="tel-anterior" type="text" placeholder="Teléfono anterior">
                                    <div id="tel-anterior-errors"></div>
                                </div>
                                <div class="label-radio d-flex align-items-center col-md-6 py-3"><input class="input-check-sm me-2" type="checkbox" id="chserv7" value="7" name="tservicio-vehiculo"> Otros</div>
                                <div class="col-12 py-4">
                                    <input class="form-control text-center input-form z-1" id="otros-tservicio" name="otros-tservicio" type="text" placeholder="Otros (Específica)">
                                </div>
                                <div id="tservicio-vehiculo-errors">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="fs-6 label-sub text-start">Datos del equipo GPS <!--<span class="lnr lnr-tablet icon-size">--></div>
                            <div class="row">
                                <div class="col-md-12 d-flex align-items-center py-2">
                                    <div class="col-4"><label class="label-form text-right">Modelo de GPS </label> <label class="mark-required text-danger fw-bold">*</label></div>
                                    <div class="col-8 form-group">
                                        <div class="input-group">
                                            <input type="hidden" id="existeModelo" value="">
                                            <input class="form-control text-center input-form z-1" id="modelo-gps" name="modelo-gps" type="text" placeholder="Modelo de gps" oninput="aucompletarModeloGPS();" />
                                            <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                        </div>
                                        <div id="modelo-gps-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex align-items-center py-2">
                                    <div class="col-4"><label class="label-form text-right" for="imei-gps">IMEI </label> <label class="mark-required text-danger fw-bold">*</label></div>
                                    <div class="col-8 form-group">
                                        <div class="input-group">
                                            <input class="form-control text-center input-form z-1" id="imei-gps" name="imei-gps" type="text" placeholder="IMEI del GPS" oninput="validarNum(this);" maxlength="15" />
                                            <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                        </div>
                                        <div id="imei-gps-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex align-items-center py-2">
                                    <div class="col-4"><label class="label-form text-right" for="num-telefono">No. Teléfonico </label> <label class="mark-required text-danger fw-bold">*</label></div>
                                    <div class="col-8 form-group">
                                        <div class="input-group">
                                            <input class="form-control text-center input-form z-1" id="num-telefono" name="num-telefono" type="text" placeholder="Numero de teléfono" oninput="validarNum(this)" maxlength="10" min="7"/>
                                            <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                        </div>
                                        <div id="num-telefono-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex align-items-center py-2">
                                    <div class="col-4"><label class="label-form text-right" for="tipo-comprobante">Instalador </label> <label class="mark-required text-danger fw-bold">*</label></div>
                                    <div class="col-8 form-group">
                                        <div class="input-group">
                                            <select class="form-select text-center input-form" id="nombre-instalador" name="nombre-instalador">
                                                <option value="" id="option-default-nombre-instalador">- - - -</option>
                                                <optgroup id="instaladores" class="contenedor-instaladores text-start"> </optgroup>
                                            </select>
                                            <!--<div class="input-group-text"><span class="text-body-tertiary far fa-list-alt"></span></div>-->
                                        </div>
                                        <div id="nombre-instalador-errors"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div id="datos-accesorios" class="mt-5">
                    <div class="row">
                        <div class="fs-6 label-sub text-start">Accesorios a instalar <label class=' fs-5 mark-required text-danger fw-bold'>*</label> <!--<span class="fas fa-wrench">--></div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <label class="label-form"></label>
                                <div class="form-group row">
                                    <div id="chacc1-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc1" value="1" name="accesorio-gps" /> Botón de pánico</div>
                                    <div id="chacc2-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc2" value="2" name="accesorio-gps" /> Bocina</div>
                                    <div id="chacc3-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc3" value="3" name="accesorio-gps" /> Micrófono</div>
                                    <div id="chacc4-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc4" value="4" name="accesorio-gps" /> Corte de corriente/ combustible</div>
                                    <div id="chacc5-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc5" value="5" name="accesorio-gps" /> Sensor de gasolina</div>
                                    <div id="chacc6-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc6" value="6" name="accesorio-gps" /> Sensores de puertas</div>
                                    <div id="chacc7-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc8" value="8" name="accesorio-gps" /> Cámara</div>
                                    <div id="chacc8-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc9" value="9" name="accesorio-gps" /> Chapa magnética</div>
                                    <div id="chacc9-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc10" value="10" name="accesorio-gps" /> Solo GPS</div>
                                    <div id="chacc10-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc11" value="11" name="accesorio-gps" /> Solo revisión</div>
                                    <div id="chacc11-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc13" value="13" name="accesorio-gps" /> Claxón</div>
                                    <div id="chacc12-div" class="label-radio d-flex align-items-center col-md-6"><input class="input-check-sm me-2" type="checkbox" id="chacc12" value="12" name="accesorio-gps" /> Ninguno</div>
                                    <div id="accesorio-gps-errors">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="datos-instaladores" class="mt-5">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="fs-6 label-sub text-start"> Asignar Instalador(es) <label class=' fs-5 mark-required text-danger fw-bold'>*</label> <!--<span class="fas fa-list-alt"></span>--></div>
                            <div class="label-form fw-semibold text-primary-emphasis">Seleccionar instalador(es) a cargo:</div>
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group py-3 row" id="asignarInstalador">

                                </div>
                                <div id="asigna-instalador-errors"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12 text-end" id="btns">
                        <input type="hidden" id="cve_orden" name="cve_orden" value="0">
                        <input type="hidden" id="cve_persona_creo" name="cve_persona_creo" value="0">
                        <input type="hidden" id="cve_persona_edita" name="cve_persona_edita" value="0">
                        <button class="button-form btn btn-danger " onclick="loadView('listainstalacion');">Cancelar <span class="fas fa-times"></span></button> &nbsp;
                        <button class="button-form btn btn-primary " onclick="insertarInstalacion();" id="btn-form-instalacion">Guardar <span class="fas fa-save"></span></button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="js/scriptinstalacion.js"></script>