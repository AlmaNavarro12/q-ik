<div id="form-remolque">
    <div class="col-md-12 fijo z-2">
        <div class="titulo-lista" id="contenedor-titulo-form-remolque">Nuevo remolque </div>
    </div>
    <div id="div-space">
    </div>
    <div class="div-form p-5 border border-secondary-subtle">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-end mb-2">
                <label class="fw-bold text-danger small">* Campo obligatorio</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="nombre-remolque">Nombre remolque </label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="nombre-remolque" placeholder="Nombre de identificaci&oacute;n del veh&iacute;culo" />
                    <div id="nombre-unidad-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="tipo-remolque">Tipo remolque </label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="tipo-remolque" placeholder="Clave del tipo de remolque" oninput="aucompletarTipoRemolque()" />
                    <div id="tipo-remolque-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="placa-remolque">Placa remolque</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="placa-remolque" placeholder="Placa del remolque (sin espacios ni gu&iacute;ones)" oninput="validarLetNum(this);" />
                    <div id="placa-remolque-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-end" id="btns">
                <button class="button-form btn btn-danger" onclick="loadView('listaremolque')">Cancelar <span class="fas fa-times"></span></button> &nbsp;
                <button class="button-form btn btn-primary" onclick="insertarRemolque()" id="btn-form-remolque">Guardar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
</div>
<script src="js/scripttransporte.js"></script>