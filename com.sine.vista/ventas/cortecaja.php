<?php
include("modals.php");
?>
<div id="form-factura" style="height: 100%;">
    <div class="col-md-12"><div class="titulo-lista" id="contenedor-titulo-form-factura">Corte de Caja </div> </div>
    <div id="div-space">
    </div>
    <div class="div-form p-5 border border-secondary-subtle">
        <div class="row" id="not-timbre">
        </div>
        <div class="row">
            <div class="col-md-4 py-1" id="div-usuario">
                <label class="label-form text-right" for="usuario-corte">Usuario</label> <label
                class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-control text-center input-form" id="usuario-corte" name="usuario-corte" onchange="corteCaja();">
                        <option value="" id="option-default-usuario">- - - -</option>
                        <optgroup id="usuarios" class="contenedor-usuarios text-left"> </optgroup>
                    </select>
                    <div id="usuario-corte-errors"></div>
                </div>
            </div>
            <div class="col-md-4 py-1" id="div-fecha">
                <label class="label-form text-right" for="fecha-corte">Fecha</label> <label
                class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control input-form text-center" id="fecha-corte" name="fecha-corte" type="date" onchange="corteCaja();"/>
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

        <div class="row" id="div-corte-caja">
            <table class="table tab-hover table-condensed table-responsive">
                <tbody>
                    <tr class="align-middle text-center">
                        <td class='col-6'>
                            <label class="label-sub-tit fw-semibold fs-5 mt-4">VENTAS TOTALES: </label> <label class="label-sub-tit fw-semibold fs-5 mt-4" id="lbl-ventas">$0.00</label>
                        </td>
                        <td class='col-6'>
                            <label class="label-sub-tit fw-semibold fs-5 mt-4">GANANCIAS: </label> <label class="label-sub-tit fw-semibold fs-5 mt-4" id="lbl-ganancia">$0.00</label>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>
                            <label class="label-sub-tit fw-semibold fs-5">Entradas de Efectivo </label>
                        </td>
                        <td>
                            <label class="label-sub-tit fw-semibold fs-5">Dinero en caja </label>
                        </td>
                    </tr>

                    <tr class="text-center">
                        <td id="tab-entradas" class="scrollsmall">
                        </td>
                        <td id="tab-caja" class="scrollsmall">
                        </td>
                    </tr>

                    <tr class="text-center">
                        <td>
                            <label class="label-sub-tit fw-semibold fs-5">Salidas de Efectivo </label>
                        </td>
                        <td>

                        </td>
                    </tr>

                    <tr class="text-center">
                        <td id="tab-salidas" class="scrollsmall">

                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-12 text-end" id="btns"> 
                <button class="button-modal" onclick="loadView('puntodeventa')" >
                    <i class="fas fa-arrow-left"></i>
                    Regresar
                </button>
            </div>	
        </div>
    </div>
</div>
<script src="js/scriptventa.js"></script>