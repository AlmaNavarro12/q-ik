<div class="row sticky-top mt-3 z-0" style="top: 50px; z-index: 1000;">
    <div class="col-md-6">
        <div class="titulo-lista">Inicio</div>
    </div>
    <div class="col-md-6 text-end pe-4">
        <a class="btn button-inicio" href="../Registro/comprar.php">Comprar Timbres <span class="fas fa-credit-card"></span></a>
    </div>
</div>
<div class="row py-1 px-3 d-flex justify-content-between flex-wrap">
    <div class="col-md-4 py-1 boton-largo">
        <label class="label-index text-start ps-2 fw-semibold text-muted py-2">Timbres Disponibles:</label>
        <label class="boton-azul text-center pt-2" id="contenedor-timbres">0</label>
    </div>
    <div class="col-md-3 py-1 boton-largo">
        <label class="label-index text-start ps-2 fw-semibold text-muted py-2">Timbres Utilizados:</label>
        <label class="boton-azul text-center pt-2" id="contenedor-usados">0</label>
    </div>
    <div class="col-md-4 py-1 boton-largo">
        <label class="label-index text-start ps-2 fw-semibold text-muted py-2">Plan de Facturación:</label>
        <label class="boton-azul text-center pt-2" id="contenedor-plan">Paquete Básico</label>
    </div>
</div>
<div class="div-form px-5 py-4">
    <div class="col-md-12">
        <label class="titulo-lista fw-light fs-4" id="contenedor-titulo-facturas-emitidas"> Facturas emitidas en</label>
    </div>
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="form-group">
                <select class="select-control text-center input-form" id="opciones-ano" name="opciones-ano" onchange="buscarGrafica()">
                    <option value="" id="option-default-opciones-ano">A&ntilde;o de emision</option>
                    <optgroup id="ano" class="contenedor-ano text-left"> </optgroup>
                </select>
                <div class="opciones-ano-errors">
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="chart-div">
        <div class="col-md-12">
            <canvas id='chart1' style='height:100px;width: 300px;'></canvas>
        </div>

    </div>
</div>
<br />
<script type="text/javascript" src="js/scriptinicio.js"></script>