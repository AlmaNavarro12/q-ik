<?php
include("modals.php");
?>
<div class="form-horizontal ps-3 fijo z-1">
    <div>
        <div class="titulo-lista">Listado de corte de cajas </div>
    </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-corte"
                placeholder="Buscar corte (Fecha u hora)" oninput="buscarCorte()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarCorte()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>


<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-cortes">
        <thead class="sin-paddding">
            <tr class='align-middle'>
                <th>Fecha / Hora </th>
                <th>Usuario </th>
                <th>Supervisor </th>
                <th>Fondo inicio </th>
                <th>Entradas </th>
                <th>Salidas </th>
                <th>Ganancias</th>
                <th>Opción</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptventa.js"></script>