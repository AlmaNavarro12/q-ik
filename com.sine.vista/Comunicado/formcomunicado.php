<?php
include("modals.php");
?>

<form action="#" onsubmit="return false;" id="form-comunicado" style="height: 100%;">
    <div class="col-md-12">
        <div class="titulo-lista" id="contenedor-titulo-form-comunicado">Nuevo comunicado </div>
    </div>
    <div class="div-form p-5 border border-secondary-subtle">
        <div class="row">
            <div class="col-md-6 py-2 form-group">
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right mb-2" for="fecha-creacion">Fecha y Hora de Creación</label>
                <div class="form-group">
                    <input id="tag" name="tag" type="hidden" />
                    <input class="form-control text-center input-form" id="fecha-creacion" name="fecha-creacion" placeholder="Fecha y Hora" type="text" disabled />
                    <div id="fecha-creacion-errors">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 d-flex justify-content-end mb-2">
                <label class="fw-bold text-danger small">* Campo obligatorio</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <label class="form-label mb-2" for="contactos-div">Contactos</label>
                <fieldset>
                    <div class="form-group col-md-12">
                        <label class='me-4'>
                            <input type="radio" id="typecom1" name="typecom" class="form-check-input border border-secondary" value="1" checked> <strong class='text-muted'>Enviar a todos los clientes</strong>
                        </label>
                        <label>
                            <input type="radio" id="typecom2" name="typecom" class="form-check-input border border-secondary" value="2"> <strong class='text-muted'>Seleccionar destinatarios</strong>
                        </label>
                    </div>
                </fieldset>
                <div class="scrollsmall boxcontactos form-group mb-3 pt-3" id="contactos-div" style="display: none;">
                </div>
            </div>
            <div id="contactos-div-errors"></div>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right mb-2 mt-2" for="asunto">Asunto</label> <label
                class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control text-center input-form mb-3" id="asunto" name="asunto" placeholder="Asunto del correo" type="text" />
                    <div id="asunto-errors">
                    </div>
                </div>
            </div>
            <div class="col-md-6 py-2">
                <label class="label-form text-right mb-2" for="emision">Emitido en</label> <label
                class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control text-center input-form mt-2" id="emision" name="emision" placeholder="Municipio y estado de emisión " type="text" />
                    <div id="emision-errors">
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label class="label-sub text-right" for="texto-comunicado">Texto</label> <label
                class="mark-required text-danger fw-bold">*</label>
        </div>
        <div class="row">
            <div class="col-md-6 py-2">
                <label class="label-form text-right mb-2" for="color-txt">Color</label>
                <div class="form-group">
                    <input class="form-control text-center input-form mb-2" id="color-txt" name="color-txt" type="color" value="#09096B" />
                    <div id="color-txt-errors">
                    </div>
                </div>
            </div>
            <div class="col-md-3 py-2">
                <label class="label-form text-center mb-2" for="size-txt">Tamaño</label>
                <div class="form-group">
                    <select class="form-select input-form text-center" aria-label="form-select-sm example" id="size-txt" name="size-txt">
                        <option value="" id="option-default-size">- - - -</option>
                        <option class="text-start" value="10-4">10</option>
                        <option class="text-start" value="11-4">11</option>
                        <option class="text-start" value="12-5">12</option>
                        <option class="text-start" value="13-5">13</option>
                        <option class="text-start" value="14-6">14</option>
                        <option class="text-start" value="15-6">15</option>
                        <option class="text-start" value="16-6">16</option>
                        <option class="text-start" value="17-7">17</option>
                        <option class="text-start" value="18-7">18</option>
                        <option class="text-start" value="19-8">19</option>
                        <option class="text-start" value="20-8">20</option>
                    </select>
                    <div id="size-txt-errors"></div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
        <div class="row">
            <div class="cool-md-6">
                <label class="label-form text-right mb-2 mt-2" for="color-txt">Redacte mensaje</label>
            </div>
        </div>
        <div>
            <textarea class="form-control input-form" id="texto-comunicado" style="height: 300"></textarea>
            <label for="floatingTextarea"></label>
            <div id="texto-comunicado-errors"></div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group ">
                <label class="label-space mb-3"></label>
                <div class="form-group">
                    <label class="button-file text-right text-uppercase " for="imagen"><span class="far fa-image"></span> Agregar Imágenes o archivos</label>
                    <input class="form-control text-center upload" id="imagen" name="imagen[]" type="file" onchange="cargarImgCom();" multiple style="display: none;"/>
                    <input id="filename" name="filename" type="hidden" style="display: none;" />
                    <div id="imagen-errors"></div>
                    <div class="scroll-table mt-3">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                            <tbody id="img-table">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-1">
                <label class="label-form text-right mb-2 mt-3" for="chfirmar">Firmar</label>
                <div class="form-group">
                    <input class="input-check" id="chfirmar" name="chfirmar" type="checkbox" onclick="disableDatos()" />
                </div>
            </div>
            <div class="col-md-4">
                <label class="label-form text-right mb-2 mt-3" for="datos-facturacion">Firmado por:</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="datos-facturacion" name="datos-facturacion" disabled>
                        <option value="" id="option-default-datos">- - - -</option>
                        <optgroup id="datosfacturar" class="contenedor-datos text-start"> </optgroup>
                    </select>
                    <div id="datos-facturacion-errors"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="d-flex justify-content-end mt-3">
                <button class="button-form btn btn-danger me-2 " onclick="loadView('listacomunicado');">Cancelar <span class="fas fa-times"></span></button>
                <button class="button-form btn btn-primary " onclick="gestionarComunicado()" id="btn-form-comunicado">Guardar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
<br>
</form>
<script src="js/scriptcomunicado.js"></script>