<?php
include("modals.php");
?>
<div class="form-horizontal ps-3 fijo z-2">
    <div>
        <div class="titulo-lista">Cotizaciones </div>
    </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-cotizacion" placeholder="Buscar cotización (Folio o nombre cliente)" oninput="filtrarCotizacion()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="filtrarCotizacion()">
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
    <table class="table table-hover table-condensed table-responsive table-row table-head" id="body-lista-cotizacion">
        <thead class="sin-paddding">
            <tr>
                <th>No. Folio </th>
                <th>Fecha de Creación </th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Total </th>
                <th>Opción</th>
            </tr>
        </thead>
        <tbody >
        </tbody>
    </table>
</div>
<script type="text/javascript" src="js/scriptcotizacion.js"></script>




