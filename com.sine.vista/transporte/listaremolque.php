<div class="form-horizontal ps-3 fijo z-2">
    <div>
        <div class="titulo-lista">Remolques </div>
    </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-placa" placeholder="Buscar remolque (Nombre o Placa)" oninput="buscarRemolque()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarRemolque()">
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
    <table class="table table-hover table-condensed table-responsive table-row table-head" id="body-lista-remolque">
        <thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>Nombre</th>
                <th class='text-center'>Placa</th>
                <th class='text-center'>Tipo Remolque</th>
                <th class='text-center'>Opci&oacute;n </th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scripttransporte.js"></script>