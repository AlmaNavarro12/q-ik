<div id="form-transporte">
    <div class="col-md-12 fijo z-2">
        <div class="titulo-lista" id="contenedor-titulo-form-transporte">Nuevo transporte </div>
    </div>
    <div id="div-space">
    </div>
    <div class="div-form p-5 border border-secondary-subtle">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-end mb-2">
                <label class="fw-bold text-danger small">* Campo obligatorio</label>
            </div>
        </div>
        <label class="label-sub">Identificaci&oacute;n Vehicular</label>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="nombre-unidad">Nombre veh&iacute;culo </label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control
                     input-form" id="nombre-unidad" placeholder="Nombre de identificacion del Veh&iacute;culo" />
                    <div id="nombre-unidad-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="num-permiso">N&uacute;mero permiso </label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="num-permiso" placeholder="N&uacute;mero de permiso otorgado por la SCT" oninput="validarNum(this)" />
                    <div id="num-permiso-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="tipo-permiso">Tipo permiso</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="tipo-permiso" placeholder="Tipo de permiso otorgado por la SCT" oninput="aucompletarPermiso();" />
                    <div id="tipo-permiso-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="conf-transporte">Tipo autotransporte </label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="conf-transporte" placeholder="Clave tipo de veh&iacute;culo" oninput="aucompletarConfigTransporte();" />
                    <div id="calle-destino-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="anho-modelo">A&ntilde;o modelo veh&iacute;culo</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="anho-modelo" placeholder="Año del modelo del veh&iacute;culo" maxlength="4" oninput="validarNum(this)"/>
                    <div id="anho-modelo-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="placa-vehiculo">Placa veh&iacute;culo</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="placa-vehiculo" placeholder="Placa del veh&iacute;culo (sin espacios ni guiones)" />
                    <div id="placa-vehiculo-errors"></div>
                </div>
            </div>
        </div>
        <label class="label-sub mt-4">Seguros</label>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="seguro-respcivil">Nombre aseguradora responsabilidad civil</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="seguro-respcivil" placeholder="Aseguradora que cubre riesgos por responsabilidad civil" />
                    <div id="seguro-respcivil-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="poliza-respcivil">N&uacute;mero de poliza</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="poliza-respcivil" placeholder="N&uacute;mero de poliza de seguro por responsabilidad civil" />
                    <div id="poliza-respcivil-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="seguro-medioambiente">Nombre aseguradora medio ambiente</label><label class="mark-required text-right">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="seguro-medioambiente" placeholder="Aseguradora que cubre daños al medio ambiente" />
                    <div id="seguro-medioambiente-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="poliza-medioambiente">N&uacute;mero de poliza</label><label class="mark-required text-right">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="poliza-medioambiente" placeholder="N&uacute;mero de poliza de seguro por daños al medio ambiente" />
                    <div id="poliza-medioambiente-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="seguro-carga">Nombre aseguradora carga</label><label class="mark-required text-right">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="seguro-carga" placeholder="Aseguradora que cubre riesgos de la carga" />
                    <div id="seguro-carga-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="poliza-carga">N&uacute;mero de poliza</label> <label class="mark-required text-right">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="poliza-carga" placeholder="N&uacute;mero de poliza de seguro por riesgo de la carga" />
                    <div id="poliza-carga-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-end" id="btns">
                <button class="button-form btn btn-danger" onclick="loadView('listatransporte')">Cancelar <span class="fas fa-times"></span></button> &nbsp;
                <button class="button-form btn btn-primary" onclick="insertarTransporte()" id="btn-form-transporte">Guardar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
</div>
<script src="js/scripttransporte.js"></script>