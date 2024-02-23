<div><div class="titulo-lista">Productos </div> </div>
<div class="form-horizontal ps-3">
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-producto"
                placeholder="Buscar producto (C&oacute;digo o nombre del producto)" oninput="buscarProducto()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarProducto()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 py-1 text-end" id="btn-crear">
        </div>
    </div>
</div>

<div class="scrollX div-form mw-100 bg-light mx-3 mt-3 border border-secondary-subtle">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-productos-altas">
        <thead class="p-0">
            <tr>
                <th class="col-auto">Empresa</th>
                <th class="col-auto">Representante</th>
                <th class="col-auto">Tel&eacute;fono</th>
                <th class="col-auto">Correo</th>
                <th class="col-auto">Banco</th>
                <th class="col-auto">Sucursal</th>
                <th class="col-auto">No. de Cuenta</th>
                <th class="col-auto">Clabe Interbancaria</th>
                <th class="col-auto">Opci&oacute;n</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptproducto.js" ></script>