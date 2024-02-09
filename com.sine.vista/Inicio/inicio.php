<div class="col-md-12"><div class="titulo-inicio">Inicio </div> </div>

<div class="row text-right" style="padding-bottom: 10px;">
    <a class="btn button-inicio" href="../Registro/comprar.php">Comprar Timbres <span class="glyphicon glyphicon-credit-card"></span></a>
</div>
<div class="row" style="padding-bottom: 5px;">
    <label class="label-index col-md-2 text-center">Timbres<br>Disponibles:</label>
    <label class="label-data col-md-2 text-center" id="contenedor-timbres" ></label>
    <label class="col-md-1"></label>
    <label class="label-index col-md-2 text-center">Timbres<br>Utilizados:</label>
    <label class="label-data col-md-2 text-center" id="contenedor-usados" ></label>
    <label class="col-md-1"></label>
    <label class="label-index col-md-2 text-center">Plan de<br>Facturacion:</label>
    <label class="label-data col-md-2 text-center" id="contenedor-plan" ></label>
</div>


<div class="div-form">
    <div class="col-md-12">
        <label class="sub-titulo" id="contenedor-titulo-facturas-emitidas"> Facturas emitidas en</label>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <select class="form-control text-center input-form" id="opciones-ano" name="opciones-ano" onchange="buscarGrafica()">
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
<br/>
<script type="text/javascript" src="js/scriptinicio.js"></script>
<script>
                    window.addEventListener('resize', resetDiv);
                    resetDiv();
</script>
