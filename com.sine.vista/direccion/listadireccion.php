<div class="form-horizontal ps-3 fijo z-2">
<div><div class="titulo-lista">Ubicaciones </div> </div>
    <div class="row col-12 p-0">
        <div class="col-sm-4 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-destino"
                placeholder="Buscar (Nombre, Estado o RFC)" oninput="buscarUbicacion()">
        </div>
        <div class="col-sm-2 py-1">
            <select title="Buscar ubicacion por Tipo" class="form-select input-search" id="tipo-reg" name="tipo-reg" onchange="buscarUbicacion();">
                <option value="">Tipo</option>
                <option value="1">Origen</option>
                <option value="2">Destino</option>
            </select>
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarUbicacion()">
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
<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle">
    <table class="table table-hover table-condensed table-responsive table-row table-head" id="body-lista-ubicacion">
        <thead class='sin-paddding'>
            <tr class="align-middle">
                <th class="text-center">Tipo</th>
                <th class="text-center">Nombre</th>
                <th class="text-center">RFC</th>
                <th class="text-center">Direcci&oacute;n</th>
                <th class="text-center">Opci&oacute;n</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptubicacion.js"></script>
