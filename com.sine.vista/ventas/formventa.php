<?php
include("modals.php");
?>
<div class="col-md-12">
    <div class="titulo-lista" id="contenedor-titulo-form-venta">Nueva venta </div>
</div>
<div id="div-space">
</div>
<div class="div-form mt-3 p-5 border border-secondary-subtle">
    <div class="row" id="not-timbre">
    </div>
    <label class="label-sub">C&oacute;digo del producto</label>
    <div class="row">
        <div class="col-md-8 py-1">
            <input type="text" class="form-control input-search" id="buscar-producto" placeholder="Buscar producto (F10) Consultar precio (F11)" oninput="aucompletarProducto();">
            <div id="buscar-producto-errors"></div>
        </div>
        <div class="col-md-4 py-1">
            <div class="space-div"></div>
            <button id="btn-nuevo-producto" type="button" class="button-agregar col-12" onclick="agregarProducto();">
                <span class="fas fa-plus"></span> Agregar Producto (Enter)
            </button>
        </div>
    </div>
    <div class="row d-flex justify-content-between">
        <div class="col-12 col-md py-1">
            <button id="btn-nuevo-producto" type="button" class="button-ventas col-12" onclick="newVenta();">
                <span class="fas fa-plus"></span> Nuevo <br> ticket (F1)
            </button>
        </div>

        <div class="col-12 col-md py-1" id="div-entradas" hidden="">
            <button id="btn-entrada" type="button" class="button-ventas col-12" data-bs-toggle="modal" data-bs-target="#modal-entradas" onclick="setLabelIngreso(this)">
                <span class="fas fa-dollar-sign"></span> Registrar <br> Entrada (F2)
            </button>
        </div>

        <div class="col-12 col-md py-1" id="div-salidas" hidden="">
            <button id="btn-salida" type="button" class="button-ventas col-12" data-bs-toggle="modal" data-bs-target="#modal-entradas" onclick="setLabelIngreso(this)">
                <span class="fas fa-dollar-sign"></span> Registrar <br> Salida (F3)
            </button>
        </div>

        <div class="col-12 col-md py-1">
            <button id="btn-lista-ticket" type="button" class="button-ventas col-12" onclick="loadView('listaticket');">
                <span class="fas fa-list-alt"></span> Tickets <br> antiguos (F4)
            </button>
        </div>

        <div class="col-12 col-md py-1">
            <button id="btn-corte" type="button" class="button-ventas col-12" onclick="loadView('cortecaja');">
                <span class="fas fa-dollar-sign"></span> Corte de <br> caja (F9)
            </button>
        </div>
        <div class="col-12 col-md py-1">
            <button id="btn-form-factura" type="button" class="button-ventas col-12" style="background-color: #327AB7 !important; color: #fff !important;" onclick="setValoresCobrar();">
                <span class="fas fa-save"></span> Cobrar <br> ticket (F7)
            </button>
        </div>
    </div>
    <div class="col-12 scrollX mt-4" style="max-width: 100%;">
        <div class="col-md-12">
            <div id="tabs" class='tabs-div mt-3'>
            </div>
            <div id="tickets">
            </div>
        </div>
    </div>
</div>
<script src="js/scriptventa.js"></script>