
<div class="form-horizontal ps-3 fijo z-1">
<div>
    <div class="titulo-lista">Impuestos </div>
</div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-impuesto" placeholder="Buscar impuesto (Nombre o porcentaje)" oninput="buscarImpuesto()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarImpuesto()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 text-end px-1 py-1" id="btn-crear">
        </div>
    </div>
</div>
<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-impuesto">
        <thead class="p-0">
            <tr>
                <th class="col-auto">Nombre</th>
                <th class="col-auto">Tipo</th>
                <th class="col-auto">Impuesto</th>
                <th class="col-auto">Factor</th>
                <th class="col-auto">Porcentaje</th>
                <th class="col-auto">Opci&oacute;n</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptimpuesto.js"></script>