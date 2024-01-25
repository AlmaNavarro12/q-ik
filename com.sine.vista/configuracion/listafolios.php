<div><div class="label-sub">Folios </div> </div>
<form onsubmit="return false;">
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-folio" placeholder="Buscar folio por serie o letra" oninput="loadListaFolio()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="loadListaFolio()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 py-1 d-flex justify-content-end px-0" id="btn-crear-folio">
        </div>
    </div>
</form>

<div class="scrollX table-responsive">
    <table class="table table-hover table-row table-head" id="body-lista-folios">
        <thead class="p-0">
            <tr>
            <th class="col-2">Serie</th>
            <th class="col-2">Letra</th>
            <th class="col-2">N° Inicio</th>
            <th class="col-2">Folio Actual</th>
            <th class="col-2">Uso del folio</th>
            <th class="col-2 text-center">Opción <span class='fas fa-ellipsis-v text-muted'></span></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript" src="js/scriptconfig.js"></script>


