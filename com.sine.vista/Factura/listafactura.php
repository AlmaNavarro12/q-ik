<?php
include("modals.php");
?>
<div class="form-horizontal ps-3 fijo z-2">
<div><div class="titulo-lista">Facturas </div> </div>
    <div class="row col-12 p-0">
        <div class="col-sm-6 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis"  id="buscar-factura" placeholder="Buscar facturas (Folio, emisor o cliente)" oninput="buscarFactura()">
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarFactura()">
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
    <table class="table tab-hover table-condensed table-responsive table-row table-head" id="body-lista-factura">
        <thead class="p-0">
            <tr>
                <th></th>
                <th class="col-auto">No.Folio </th>
                <th class="col-auto">Fecha de creación </th>
                <th class="col-auto">Emisor</th>
                <th class="col-auto">Cliente</th>
                <th class="col-auto">Estado </th>
                <th class="col-auto">Subtotal </th>
                <th class="col-auto">Traslados </th>
                <th class="col-auto">Retenciones </th>
                <th class="col-auto">Total </th>
                <th class="col-auto">Opción</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptfactura.js"></script>
<script>
    $(document).ready(function() {
        var cleave = new Cleave('.cfdi', {
            delimiter: '-',
            blocks: [8, 4, 4, 4, 12]
        });

    });
</script>