<div class="col-md-12 m-0 p-0">
    <div class="titulo-lista">Configuración </div>
</div>

<form onsubmit="return false;">
    <div class="col-12">
        <div class="row row-cols-1 col-12 row-cols-md-1 row-cols-lg-5 g-1 gx-4 mx-auto">
            <div class='col-md-3 col-12 mw-100' id="div-folio-conf" hidden="">
                <button id="btn-folio-conf" class='button-config'> <span class='lnr lnr-book icon-size'></span> Folios </button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-comision-conf" hidden="">
                <button id="btn-comision-conf" class='button-config'><span class='fas fa-dollar-sign icon-size'></span> Comisiones</button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-encabezado-conf" hidden="">
                <button id="btn-encabezado-conf" class='button-config'><span class='lnr lnr-file-empty icon-size'></span> Encabezados</button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-correo-conf" hidden="">
                <button id="btn-correo-conf" class='button-config'><span class='lnr lnr-envelope icon-size'></span> Correo</button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-tablas" hidden="">
                <button id="btn-tablas" class='button-config'><span class='lnr lnr-database icon-size'></span> Importar tablas</button>
            </div>
        </div>
    </div>
</form>

<!--
    
<form onsubmit="return false;">
    <div class="text-center col-12">
        <div class="row row-cols-1 col-12 row-cols-md-1 row-cols-lg-4 g-1 gx-4">
            <div class='col-md-3 col-12 mw-100' id="div-folio-conf" hidden="">
                <button id="btn-folio-conf" class='button-config'>Folios <span class='lnr lnr-book icon-size'></span></button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-comision-conf" hidden="">
                <button id="btn-comision-conf" class='button-config'>Comisiones <span class='fas fa-dollar-sign icon-size'></span></button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-encabezado-conf" hidden="">
                <button id="btn-encabezado-conf" class='button-config'>Encabezados <span class='lnr lnr-file-empty icon-size'></span></button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-correo-conf" hidden="">
                <button id="btn-correo-conf" class='button-config'>Correo <span class='lnr lnr-envelope icon-size'></span></button>
            </div>
            <div class='col-md-3 col-12 mw-100' id="div-tablas" hidden="">
                <button id="btn-tablas" class='button-config'>Importar tablas <span class='lnr lnr-database icon-size'></span></button>
            </div>
        </div>
    </div>
</form>
-->

<div class="div-form mw-100 bg-light mx-2 py-4 px-5 mt-4 border border-secondary-subtle" id="view-config">
</div>
<br />
<script type="text/javascript" src="js/scriptconfig.js"></script>
<script>
    window.addEventListener('resize', resposiveConfig);
    resposiveConfig();
</script>
