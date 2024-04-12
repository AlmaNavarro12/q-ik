<div id="form-ubicacion">
    <div class="col-md-12 fijo z-2">
        <div class="titulo-lista" id="contenedor-titulo-form-ubicacion">Nueva ubicaci&oacute;n </div>
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
            <div class="col-md-6">
                <label class="label-form text-right" for="tipo-ubicacion">Tipo ubicaci&oacute;n </label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="tipo-ubicacion" name="tipo-ubicacion">
                        <option value="" id="option-default-ubicacion">- - - -</option>
                        <optgroup id="tipos" class="contenedor-tipo text-start">
                            <option value="1" id="ubicacion1">Origen</option>
                            <option value="2" id="ubicacion2">Destino</option>
                        </optgroup>
                    </select>
                    <div id="tipo-ubicacion-errors"></div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="label-form text-right" for="nombre-destino">Nombre</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="nombre-destino" placeholder="Nombre/referencia de la empresa/lugar" oninput="validarLetNum(this)"/>
                    <div id="nombre-destino-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="new-tooltip icon tip col-md-2 text-center">
                <div class="new-tooltip icon tip me-2">
                    <span class="fas fa-question-circle small text-primary-emphasis"></span>
                    <span class="tiptext">Se abrirá una ventana directamente a la herramienta de validación del SAT para
                        verificar el RFC que se ingresa.</span>
                </div>
                <label class="label-sub click-label" style="cursor: pointer;" onclick="validarRFC();">Validar RFC <span class="fas fa-pencil-alt"></span></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="rfc-destino">RFC</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="rfc-destino" placeholder="RFC de la empresa/lugar" maxlength="13" />
                    <div id="rfc-destino-errors"></div>
                </div>
            </div>

            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="calle-destino">Calle </label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="calle-destino" placeholder="Calle de la empresa/lugar" />
                    <div id="calle-destino-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 py-2">
                <label class="label-form text-right" for="num-exterior">N&uacute;mero exterior</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="num-exterior" placeholder="N&uacute;mero exterior de la empresa/lugar" />
                    <div id="num-exterior-errors"></div>
                </div>
            </div>
            <div class="col-md-3 py-2">
                <label class="label-form text-right" for="num-interior">N&uacute;mero interior</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="num-interior" placeholder="N&uacute;mero exterior de la empresa/lugar" />
                    <div id="num-interior-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2">
            <div class="new-tooltip icon tip me-2">
                    <span class="fas fa-question-circle small text-primary-emphasis"></span>
                    <span class="tiptext">Al ingresar el código postal se selecciona el estado y municipio correspondiente. Si no es el estado o municipio deseados puedes seleccionarlos del listado manualmente.</span>
                </div>
                <label class="label-form text-right" for="codigo_postal">C&oacute;digo postal</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="codigo_postal" name="codigo_postal" placeholder="C&oacute;digo postal de la empresa/lugar" maxlength="5" minlength="5" onblur="getEstadoMunicipioByCodP();" oninput="validarNum(this)" />
                    <div id="codigo_postal-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="referencia">Referencia</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="referencia" placeholder="Referencia de localizaci&oacute;n de la empresa/lugar" />
                    <div id="referencia-errors"></div>
                </div>
            </div>
            <div class="col-md-6 py-2 py-2">
                <label class="label-form text-right" for="id-estado">Estado</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-estado" name="id-estado" onchange="loadOpcionesMunicipio();">
                        <option value="" id="option-default-estado">- - - -</option>
                        <optgroup id="estados" class="contenedor-estado text-start"> </optgroup>
                    </select>
                    <div id="id-estado-errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right" for="id-municipio">Municipio</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="id-municipio" name="municipio">
                        <option value="" id="option-default-municipio">- - - -</option>
                        <optgroup id="municipios" class="contenedor-municipio text-start"> </optgroup>
                    </select>
                    <div id="id-estado-errors"></div>
                </div>
            </div>
            <div class="col-md-3 py-2">
                <label class="label-form text-right" for="clv-localidad">Localidad</label><label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="clv-localidad" placeholder="Clave localidad de la empresa/lugar" oninput="aucompletarLocalidad();" />
                    <div id="clv-localidad-errors"></div>
                </div>
            </div>
            <div class="col-md-3 py-2">
                <label class="label-form text-right" for="clv-colonia">Colonia</label> <label class="mark-required text-danger fw-bold">&nbsp;</label>
                <div class="form-group">
                    <input type="text" class="form-control input-form" id="clv-colonia" placeholder="Colonia de la empresa/lugar" />
                    <div id="clv-colonia-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-end" id="btns">
                <button class="button-form btn btn-danger" onclick="loadView('listadireccion')">Cancelar <span class="fas fa-times"></span></button> &nbsp;
                <button class="button-form btn btn-primary" onclick="insertarUbicacion()" id="btn-form-ubicacion">Guardar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
</div>
<script src="js/scriptubicacion.js"></script>