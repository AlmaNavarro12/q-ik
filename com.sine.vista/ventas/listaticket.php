<?php
include("modals.php");
?>
<div class="form-horizontal ps-3 fijo z-1">
    <div class="col-md-12">
        <div class="titulo-lista">Tickets de Venta</div>
    </div>
    <div class="row col-12 p-0">
        <div class="col-sm-2 py-1 text-center">
            <select class="form-select input-search" id="usuarios" name="usuarios" onchange="filtrarVentas()">
                <option value="0" id="option-default-usuario"> Usuario </options>
                <optgroup id="usuarios" class="contenedor-usuarios text-start"> </optgroup>
            </select>
        </div>
        <div class="col-sm-4 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-ticket"
                placeholder="Buscar ticket (Folio, emisor o cliente)" oninput="buscarVentas()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-start col-12" id="num-reg" name="num-reg" onchange="buscarVentas()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 py-1 pe-1 text-end" id="btn-crear">
        </div>
    </div>
</div>


<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-ticket">
        <thead class="sin-paddding">
            <tr class='align-middle'>
                <th></th>
                <th>No.Folio </th>
                <th class='text-center'>Fecha de Creaci&oacute;n </th>
                <th class="col-md-3 text-center">Emisor</th>
                <th>Cliente</th>
                <th>Estado </th>
                <th>Subtotal </th>
                <th>Traslados </th>
                <th>Retenciones </th>
                <th>Total </th>
                <th>Opción</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptventa.js"></script>