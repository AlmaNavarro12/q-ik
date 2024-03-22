<div class="form-horizontal ps-3 fijo z-2">
<div><div class="titulo-lista">Datos de facturación </div> </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-empresa"
                placeholder="Buscar datos (Contribuyente o RFC)" oninput="buscarEmpresa()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarEmpresa()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 text-end py-1 px-1" id="btn-crear">
        </div>
    </div>
</div>
<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-empresa">
        <thead class="sin-paddding">
            <tr>
            <th></th>
                <th class="col-md-2">Contribuyente</th>
                <th>RFC </th>
                <th class="col-md-2">Razón social</th>
                <th>Dirección</th>
                <th>Régimen fiscal</th>
                <th>Opción</th>
            </tr>
        </thead>
    </table> 
</div>
<script type="text/javascript" src="js/scriptempresa.js"></script>