<div><div class="titulo-lista">Productos </div> </div>
<form class="form-horizontal ps-3" onsubmit="return false;">
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-proveedor"
                placeholder="Buscar producto (C&oacute;digo o nombre del producto)" oninput="buscarPago()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarPago()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-4 py-1 d-flex justify-content-end" id="btn-crear">
        </div>
    </div>
</form>
