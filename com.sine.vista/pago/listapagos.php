<?php
include("modals.php");
?>
<div><div class="titulo-lista">Pagos </div> </div>
<form class="form-horizontal ps-3" onsubmit="return false;">
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-pago"
                placeholder="Buscar pagos (Folio, Emisor o Receptor)" oninput="buscarPago()">
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
<div class="scrollX div-form mw-100 bg-light mx-3 mt-3 border border-secondary-subtle">
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-pagos">
        <thead class="p-0">
            <tr>
                <th class="col-auto">No. Folio </th>
                <th class="col-auto">Creaci&oacute;n </th>
                <th class="col-auto">Emisor </th>
                <th class="col-auto">Receptor </th>
                <th class="col-auto">Fecha / Hora de pago </th>
                <th class="col-auto">Total </th>
                <th class="col-auto">Opci&oacute;n</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptpago.js"></script>
<script>
    $(document).ready(function() {
        var cleave = new Cleave('.cfdi', {
            delimiter: '-',
            blocks: [8, 4, 4, 4, 12]
        });

    });
</script>