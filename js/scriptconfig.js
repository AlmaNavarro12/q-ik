
function resposiveConfig() {
    if (window.innerWidth <= 1220) {
        $(".row-cols-lg-5").removeClass('row-cols-lg-5').addClass('row-cols-1');
        $(".col-md-3").removeClass('col-md-3').addClass('col-12 mw-100');
    } else {
        $(".row-cols-1").removeClass('row-cols-1').addClass('row-cols-lg-5');
        $(".col-12.mw-100").removeClass('col-12 mw-100').addClass('col-md-3');
    }
}

$(function () {
    $(".button-config").click(function () {
        $('.button-config').removeClass("conf-active");
        $(this).addClass("conf-active");
    });
});

function loadBtnConfig(view) {
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacepermiso.php",
        type: "POST",
        data: { transaccion: "loadbtn", view: view },
        success: function (datos) {
            var array = datos.split("</tr>");

            var elements = [
                { key: 'folio', divId: 'div-folio-conf', btnId: 'btn-folio-conf', view: 'listafolio' },
                { key: 'comision', divId: 'div-comision-conf', btnId: 'btn-comision-conf', view: 'comision' },
                { key: 'encabezado', divId: 'div-encabezado-conf', btnId: 'btn-encabezado-conf', view: 'encabezado' },
                { key: 'correo', divId: 'div-correo-conf', btnId: 'btn-correo-conf', view: 'correo' },
                { key: 'importar', divId: 'div-tablas', btnId: 'btn-tablas', view: 'tablas' }
            ];

            elements.forEach(function (element, index) {
                if (array[index] == '1') {
                    $("#" + element.divId).removeAttr('hidden');
                    $("#" + element.btnId).attr('onclick', "loadViewConfig('" + element.view + "');");
                }
            });
            cargandoHide();
        }
    });
}
function loadViewConfig(vista) {
    getViewConfig(vista);

    const config = {
        'correo': 300,
        'folio': 300,
        'listafolio': 400,
        'comision': 400,
        'tablas': 0  
    };

    const funciones = {
        'correo': 'opcionesCorreoList()',
        'folio': 'loadopcionesFolioDatos()',
        'listafolio': 'loadListaFolio(); loadBtnCrearConfig("folio")',
        'comision': 'loadOpcionesUsuario()',
        'tablas': ''  
    };

    const configuracion = config[vista] || 0;
    const fun = funciones[vista] || '';

    if (configuracion > 0) {
        window.setTimeout(fun, configuracion);
    }
}

function getViewConfig(view) {
    $.ajax({
        url: 'com.sine.enlace/enlaceenrutador.php',
        type: 'POST',
        data: {transaccion: "hola", view: view},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var resto = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(resto);
            } else {
                $("#view-config").html('');
                $("#view-config").html(datos);
            }
        }
    });
}

//-----------------------------FOLIO---------------------------
function loadListaFolio(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlaceconfig.php",
        type: "POST",
        data: {transaccion: "listafolios", pag: pag, REF: $("#buscar-folio").val(), numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-folios").html(datos);
            }
            cargandoHide();
        }
    });
}

function loadBtnCrearConfig(view) {
    $.ajax({
        url: "com.sine.enlace/enlacepermiso.php",
        type: "POST",
        data: {transaccion: "loadbtn", view: view},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            $("#btn-crear-folio").html(datos);
            cargandoHide();
        }
    });
}

function obtenerDatosFolio() {
    var serie = $("#serie").val();
    var letra = $("#folio-letra").val();
    var folio = $("#folio-inicio").val();
    var usofolio = $("input[name='chusofolio']:checked").map(function () { //obtener un array de valores
        return $(this).val();
    }).get().join("-"); // unir valores con un guion ("-").
    return {serie, letra, folio, usofolio};
}

function insertarFolio(idfolio = null) {
    var datosFolio = obtenerDatosFolio();
    if ((isnEmpty(datosFolio.serie, "serie") && isnEmpty(datosFolio.folio, "folio-inicio") && isnEmpty(datosFolio.usofolio, "btn-uso"))) {
    cargandoHide();
        cargandoShow();
        var transaccion = (idfolio == null) ? "insertarfolio" : "actualizarfolio";
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {
                transaccion: transaccion,
                idfolio: idfolio,
                serie: datosFolio.serie,
                letra: datosFolio.letra,
                folio: datosFolio.folio,
                usofolio: datosFolio.usofolio,
                inicio: datosFolio.folio
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);

                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    if (transaccion === "insertarfolio") {
                        alertify.success('Datos guardados.');
                    } else {
                        alertify.success('Folio actualizado.');
                    }
                    loadViewConfig('listafolio');
                }
                cargandoHide();
            }
        });
    }
}

function editarFolio(idfolio) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceconfig.php",
        type: "POST",
        data: {transaccion: "editarfolio", idfolio: idfolio},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadViewConfig('folio');
                window.setTimeout("setValoresEditarFolio('" + datos + "')", 500);
            }
        }
    });
}

function setValoresEditarFolio(datos) {
    changeText("#contenedor-titulo-form-folio", "Editar Folio");
    changeText("#btn-form-folio", "Guardar cambios <span class='far fa-save'></span></a>");

    var array = datos.split("</tr>");
    var idfolio = array[0];
    var serie = array[1];
    var letra = array[2];
    var numinicio = array[3];
    var uso = array[4];

    $("#btn-uso").dropdown('toggle');
    uso.split("-").forEach(function (item) {
        $("#chusofolio" + item).prop('checked', true);
        $("#chspan" + item).removeClass('far fa-square').addClass('far fa-check-square');
    });

    $("#serie").val(serie);
    $("#folio-letra").val(letra);
    $("#folio-inicio").val(numinicio);
    $("#uso-folio").val(uso);

    $("#form-folio").append("<input type='hidden' id='numinicio' name='numinicio' value='" + numinicio + "'/>");
    $("#btn-form-folio").attr("onclick", "insertarFolio(" + idfolio + ");");
    cargandoHide();
}

function eliminarFolio(idfolio) {
    alertify.confirm("¿Esta seguro que desea eliminar este folio?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "eliminarfolio", idfolio: idfolio},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Folio eliminado');
                    loadViewConfig('listafolio');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

//--------------------------------TABLAS
function loadFormato() {
    var tabla = $("#datos").val();
    var data = "";
    if (tabla == '1') {
        data = `<div class='container'>
        <div class='row align-items-start'>
            <div class='col-md-5'>
                <strong class='text-muted'>Formato del archivo:</strong>
                <br />21 Campos, sin titulos de tabla
                <ul class='list-unstyled'>
                    <li>Nombre</li>
                    <li>Apellido paterno</li>
                    <li>Apellido materno</li>
                    <li>Empresa</li>
                    <li>Email información (Opcional)</li>
                    <li>Email facturación</li>
                    <li>Email de gerencia (Opcional)</li>
                    <li>Teléfono</li>
                    <li>Banco (Opcional)</li>
                    <li>Cuenta (Opcional)</li>
                </ul>
            </div>
            <div class='col-md-7'>
                <ul class='list-unstyled'>
                    <li>Clabe (Opcional)</li>
                    <li>RFC</li>
                    <li>Razón Social</li>
                    <li>Régimen Cliente <br><small class='ps-3'>(Formato: (Clv régimen)-(Descripcién fiscal))</small></li>
                    <li>Calle (Opcional)</li>
                    <li>Número Interior (Opcional)</li>
                    <li>Número Exterior</li>
                    <li>Localidad o Colonia (Opcional)</li>
                    <li>Estado</li>
                    <li>Municipio</li>
                    <li>Código Postal</li>
                </ul>
            </div>
        </div>
        <button class='button-form btn btn-success col-md-7 col-12 float-end my-2' onclick='descargarEjemplo(1)'>Descargar ejemplo
            <span class='fas fa-download'></span></button>
    </div>`;
    } else if (tabla == '2') {
        data = `<div class="container">
        <div class="row align-items-start">
            <div class="col-md-5 col-sm-6">
                <strong class="text-muted">Formato del archivo:</strong>
                <br />14 Campos, sin titulos de tabla
                <ul class="list-unstyled">
                    <li>Código de producto</li>
                    <li>Nombre de producto</li>
                    <li>Clave fiscal de unidad <br><small class="ps-3">(Cátalogo SAT)</small></li>
                    <li>Descripción fiscal de unidad <br><small class="ps-3">(Cátalogo SAT)</small></li>
                    <li>Descripción de producto</li>
                    <li>Precio de compra</li>
                    <li>Porcentaje de ganancia</li>
                </ul>
            </div>
            <div class="col-md-7 col-sm-6">
                <ul class="list-unstyled">
                    <li>Importe de ganancia</li>
                    <li>Precio de venta</li>
                    <li>Tipo de producto <br><small class="ps-3">(Producto = '1' / Servicio='2')</small></li>
                    <li>Clave fiscal del producto <br><small class="ps-3">(Cátalogo del SAT)</small> </li>
                    <li>Descripción fiscal del producto <br><small class="ps-3">(Cátalogo del SAT)</small></li>
                    <li>¿Activar inventario? <br><small class="ps-3">(Si = '1' / No = '0')</small></li>
                    <li>Cantidad inventario</li>
                </ul>
            </div>
        </div>
        <button class='button-form btn btn-success col-md-7 col-12 float-end my-2'
            onclick='descargarEjemplo(2)'>Descargar ejemplo
            <span class='fas fa-download'></span></button>
    </div>`;
    }
    $("#data-tabla").html(data);
}

function descargarEjemplo(id) {
    cargandoHide();
    cargandoShow();
    if (id == '1') {
        window.open('./temporal/Ejemplo_cliente.xlsx');
    } else if (id == '2') {
        window.open('./temporal/Ejemplo_producto.xlsx');
    }
    cargandoHide();
}

function cargarArchivoTabla() {
    var formData = new FormData(document.getElementById("form-tabla"));
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: 'com.sine.enlace/cargarimg.php',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            var array = datos.split("<corte>");
            $("#muestraimagen").html(array[0]);
            $("#filename").val(array[1]);
            $("#imagen").val('');
            cargandoHide();
        }
    });
}

function loadArchivo() {
    var fnm = $("#filename").val();
    var tabla = $("#datos").val();

    if (isnEmpty(tabla, "datos") && isnEmpty(fnm, "imagen")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "loadexcel", fnm: fnm, tabla: tabla},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);

                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Datos guardados correctamente.');
                    loadViewConfig('tablas');
                }
                cargandoHide();
            }
        });
    }
}

//--------------------------------USUARIOS