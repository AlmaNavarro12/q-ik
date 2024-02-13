<?php
if (isset($_SESSION[sha1('paquete')])) {
    echo "<script>var paquete = '" . $_SESSION[sha1('paquete')] . "';</script>";
} else {
    echo "<script>var paquete = '1';</script>";
}?>

<div class=" p-0 m-0">
    <div class="p-0 m-0 fijo z-1">
        <div class="row pt-0 mt-0">
            <div class="col-md-6">
                <div class="titulo-lista">Inicio</div>
            </div>
            <div class="col-md-6 text-end pe-4">
                <a class="btn button-inicio" href="../Registro/comprar.php">Comprar Timbres <span class="fas fa-credit-card"></span></a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row py-1 d-flex justify-content-between flex-wrap g-1">
            <div class="col-md-3 py-1 boton-largo row ps-0">
                <label class="label-index text-start col-11 py-1">Timbres Disponibles:</label>
                <label class="boton-azul text-center col-4 pb-3" id="contenedor-timbres"></label>
            </div>
            <div class="col-md-3 py-1 boton-largo row ps-0">
                <label class="label-index text-start col-9 py-1">Timbres Utilizados:</label>
                <label class="boton-azul text-center col-4 pb-3" id="contenedor-usados"></label>
            </div>
            <div class="col-md-5 py-1 boton-largo row ps-0">
                <label class="label-index text-start col-7 py-1">Plan de Facturación:</label>
                <label class="boton-azul text-center col-5" id="contenedor-plan">Paquete Básico</label>
            </div>
        </div>
        <div class="div-form px-5 py-4 mt-2">
            <div class="col-md-12">
                <label class="titulo-lista fw-light fs-4" id="contenedor-titulo-facturas-emitidas"> Facturas emitidas en</label>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="select-control text-center input-form" id="opciones-ano" name="opciones-ano" onchange="buscarGrafica()">
                            <option value="" id="option-default-opciones-ano">A&ntilde;o de emisi&oacute;n</option>
                            <optgroup id="ano" class="contenedor-ano text-left"> </optgroup>
                        </select>
                        <div class="opciones-ano-errors">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4" id="chart-div">
                <div class="col-md-12">
                    <canvas id='chart1' style='height:100px;width: 300px;'></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/scriptinicio.js"></script>