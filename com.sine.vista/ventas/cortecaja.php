<?php
include("modals.php");
?>
<div id="form-factura" class="mb-3" >
    <div class="col-md-12">
        <div class="titulo-lista" id="contenedor-titulo-form-factura">Corte de caja </div>
    </div>
    <div id="div-space">
    </div>
    <div class="div-form p-5 border border-secondary-subtle">
        <div class="row" id="not-timbre">
        </div>
        <div class="row">
            <div class="col-md-4 py-1" id="div-usuario">
                <label class="label-form text-right" for="usuario-corte">Usuario</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="usuario-corte" name="usuario-corte" onchange="corteCaja();">
                        <option value="" id="option-default-usuario">- - - -</option>
                        <optgroup id="usuarios" class="contenedor-usuarios text-start"> </optgroup>
                    </select>
                    <div id="usuario-corte-errors"></div>
                </div>
            </div>
            <div class="col-md-4 py-1" id="div-fecha">
                <label class="label-form text-right" for="fecha-corte">Fecha</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control input-form text-center" id="fecha-corte" name="fecha-corte" type="date" onchange="corteCaja();" />
                    <div id="fecha-corte-errors"></div>
                </div>
            </div>
            <div class="col-md-4 py-1">
                <label class="label-space"></label>
                <div class="form-group">
                    <button id="btn-print-corte" type="button" class="button-modal col-12" onclick="imprimirCorteCaja();">
                        <span class="fas fa-file"></span> Imprimir Corte
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 text-center">
                <label class="label-sub-tit fw-semibold fs-5 mt-4">VENTAS TOTALES: </label> <label class="label-sub-tit fw-semibold fs-5 mt-4" id="lbl-ventas">$0.00</label>
            </div>
            <div class="col-6 text-center">
                <label class="label-sub-tit fw-semibold fs-5 mt-4">GANANCIAS: </label> <label class="label-sub-tit fw-semibold fs-5 mt-4" id="lbl-ganancia">$0.00</label>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mh-100">
            <div class="col">
                <div class="card shadow border border-light" style="height: 100%;">
                    <div class="card-body text-center">
                        <label class="label-sub-tit fw-semibold fs-5 text-uppercase text-center">Entradas de efectivo </label>
                        <div id="tab-entradas">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow border border-light" style="height: 100%;">
                    <div class="card-body text-center">
                        <label class="label-sub-tit fw-semibold fs-5 text-uppercase text-center">Salidas de efectivo </label>
                        <div id="tab-salidas">
                        </div>
                    </div>
                </div>
            </div>
        <div class="col">
                <div class="card shadow border border-light" style="height: 100%;">
                    <div class="card-body text-center">
                        <label class="label-sub-tit fw-semibold fs-5 text-uppercase text-center">Dinero en caja </label>
                        <div id="tab-caja">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-end" id="btns">
                <button class="button-modal" onclick="">
                    <i class="fas fa-save"></i>
                    Guardar registro
                </button>
            </div>
        </div>
    </div>
</div>
<script src="js/scriptventa.js"></script>