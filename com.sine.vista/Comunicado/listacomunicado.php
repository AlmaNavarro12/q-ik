<?php
include("modals.php");
?>
<div class="col-md-12"><div class="titulo-lista">Comunicados </div> </div>
<form class="form-inline" onsubmit="return false;">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <input type="text" class="form-control input-search" id="buscar-comunicado" placeholder="Buscar comunicado (Asunto)" oninput="buscarComunicados()">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select class="form-select input-search w-100 p-2" id="num-reg" name="num-reg" onchange="buscarComunicados()">
                    <option value="10">--</option>
                    <option value="2">2</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <div class="form-group">
                <button type="button" class="btn btn-custom" id="btn-crear"></button>
            </div>
        </div>
    </div>
</form>

<div class="scrollX div-bordered">
    <table class="table table-hover table-condensed table-responsive table-row table-head" id="body-lista-comunicado">
        <thead class="sin-paddding" >
            <tr>
            <th></th>
                <th >Fecha y Hora de Creacion</th>
                <th>Asunto </th>
                <th class="col-md-2">Archivos Adjuntos</th>
                <th><span class=" fas fa-ellipsis-v"></span></th>
            </tr>
        </thead>
    </table> 
</div>
<br/>
<script type="text/javascript" src="js/scriptempresa.js"></script>