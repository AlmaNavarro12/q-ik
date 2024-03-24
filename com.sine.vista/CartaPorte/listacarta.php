<?php
include("modals.php");
?>
<div class="form-horizontal ps-3 fijo z-2">
    <div>
        <div class="titulo-lista">Carta porte </div>
    </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-carta" placeholder="Buscar carta porte (Folio, emisor o nombre del cliente)" oninput="buscarCarta()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarCarta()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 py-1 px-1 text-end" id="btn-crear">
        </div>
    </div>
</div>
<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle pb-5">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-carta">
        <thead class="sin-paddding">
            <tr>
                <th></th>
                <th>No. Folio </th>
                <th>Fecha de Creaci&oacute;n </th>
                <th class="col-md-3">Emisor</th>
                <th>Cliente</th>
                <th>Estado </th>
                <th>Total </th>
                <th>Opci&oacute;n</th>
            </tr>
        </thead>
    </table>
</div>
<script src="js/scriptcarta.js"></script>