<?php
include("modals.php");
?>
<div class="form-horizontal ps-3 fijo z-2">
<div><div class="titulo-lista">Comunicados </div> </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis"  id="buscar-comunicado" placeholder="Buscar comunicado (Asunto)" oninput="buscarComunicados()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarComunicados()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 py-1 text-end px-1" id="btn-crear">
        </div>
    </div>
</div>
<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle">
    <table class="table table-hover table-condensed table-responsive table-row table-head" id="body-lista-comunicado">
        <thead class="sin-paddding" >
            <tr>
            <th></th>
                <th >Fecha y Hora de Creación</th>
                <th>Asunto </th>
                <th class="col-md-2">Archivos Adjuntos</th>
                <th>Opción</th>
            </tr>
        </thead>
    </table> 
</div>
<script type="text/javascript" src="js/scriptcomunicado.js"></script>