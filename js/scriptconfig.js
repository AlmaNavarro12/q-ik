
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
        data: { transaccion: "hola", view: view },
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
        data: { transaccion: "listafolios", pag: pag, REF: $("#buscar-folio").val(), numreg: $("#num-reg").val() },
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
        data: { transaccion: "loadbtn", view: view },
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
    return { serie, letra, folio, usofolio };
}

function insertarFolio(idfolio = null) {
    var datosFolio = obtenerDatosFolio();
    if (isnEmpty(datosFolio.serie, "serie") && isNumber(datosFolio.folio, "folio-inicio") && isnEmpty(datosFolio.usofolio, "btn-uso")) {
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
        data: { transaccion: "editarfolio", idfolio: idfolio },
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
                console.log(datos);
            }
        }
    });
}

function setValoresEditarFolio(datos) {
    changeText("#contenedor-titulo-form-folio", "Editar Folio");
    changeText("#btn-form-folio", "Guardar cambios <span class='fas fa-save'></span></a>");

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
    alertify.confirm("¿Estás seguro que deseas eliminar este folio?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: { transaccion: "eliminarfolio", idfolio: idfolio },
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
    }).set({ title: "Q-ik" });
}

//--------------------------------COMISION
function loaddatosUsuario() {
    var idusuario = $("#id-usuario").val();
    $.ajax({
        url: 'com.sine.enlace/enlaceconfig.php',
        type: 'POST',
        data: { transaccion: 'datosusuario', idusuario: idusuario },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                setValoresUsuarioComision(datos);
            }
        }
    });
}

function setValoresUsuarioComision(datos) {
    var array = datos.split("</tr>");
    var tipo = array[0];
    var t = (tipo == '1') ? "Administrador" : "Vendedor";
    $("#tipo-usuario").val(t);

    var check = array[1];
    if (check != '0') {
        var [idcomision, idusu, porcentaje, calculo] = array.slice(2);
        $("#btn-form-quicom").html("Eliminar comisión <span class='fas fa-times'></span>").attr('onclick', `quitarComision(${idcomision})`).removeClass('visually-hidden');
        $("#porcentaje-comision").val(porcentaje);
        $(`#calculo${calculo}`).prop('checked', true);
        $("#btn-form-comision").html("Actualizar comisión <span class='fas fa-save'></span>").attr('onclick', `insertarComision(${idcomision})`);
    } else {
        $("#btn-form-quicom").addClass('visually-hidden'); // Oculta el botón de eliminar comisión
        $("#porcentaje-comision").val('');
        $("#calculo1").prop('checked', true);
        $("#btn-form-comision").html("Guardar <span class='fas fa-save'></span>").attr('onclick', 'insertarComision()');
    }
}

function insertarComision(idcomision = null) {
    var idusuario = $("#id-usuario").val();
    var porcentaje = $("#porcentaje-comision").val();
    var chcalculo = $("input[name=calculo]:checked").val();
    var chcom = ($("#chcom").prop('checked')) ? 1 : 0;

    if (isnEmpty(idusuario, "id-usuario") && isPorcentaje(porcentaje, "porcentaje-comision")) {
        cargandoHide();
        cargandoShow();
        var transaccion = (idcomision == null) ? "insertarcomision" : "actualizarcomision";
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {
                transaccion: transaccion,
                idcomision: idcomision,
                idusuario: idusuario,
                porcentaje: porcentaje,
                chcalculo: chcalculo,
                chcom: chcom
            },
            success: function(datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    cargandoHide();
                    alertify.error(res);
                } else {
                    cargandoHide();
                    var mensaje = (transaccion == "insertarcomision") ? 'registrada.' : 'actualizada.';
                    alertify.success('Comisión ' + mensaje);
                    loadViewConfig('comision');
                }
            }
        });
    }
}

function quitarComision(idcomision){
    alertify.confirm("¿Estás seguro que quieres eliminar la comisión de este usuario?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "quitarcomision", idcomision: idcomision},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                }
                else {
                    cargandoHide();
                    alertify.success("Comisión eliminada.");
                    loadViewConfig('comision');
                }
            }
        });
    }).set({title: "Q-ik"});
}

//--------------------------------CORREO
function loadMailConfig() {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceconfig.php",
        type: "POST",
        data: {transaccion: "loadmail", idcorreo: $("#id-correo").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                changeText("#btn-form-correo", "Guardar <span class='fas fa-save'></span>");
                $("#btn-form-correo").attr('onclick', 'insertarCorreo()');
                cargandoHide();
            } else {
                setValoresCorreo(datos);
                cargandoHide();
            }
        }
    });
}

function setValoresCorreo(datos) {
    changeText("#btn-form-correo", "Guardar cambios <span class='fas fa-save'></span>");
    var array = datos.split("</tr>");

    var fields = ["#correo-uso", "#pass", "#remitente", "#correo-remitente", "#host-correo", "#puerto-acceso", "#seguridad"];
    var chusos = [array[7], array[8], array[9], array[10], array[11], array[12]];

    fields.forEach(function(field, index) {
        $(field).val(array[index]);
    });

    chusos.forEach(function(valor, index) {
        $("#chuso" + (index + 1)).prop('checked', valor == 1);
        
        var checkboxId = "#chuso" + (index + 1);
        var checkboxSpanId = "#chspan" + (index + 1);
        if (valor == 1) {
            $(checkboxId).prop('checked', true);
            $(checkboxSpanId).removeClass('far fa-square').addClass('far fa-check-square');
        } else {
            $(checkboxId).prop('checked', false);
            $(checkboxSpanId).removeClass('far fa-check-square').addClass('far fa-square');
        }
    });

    $("#btn-form-correo").attr('onclick', 'actualizarCorreo()');
}

function insertarCorreo() {
    var correo = $("#correo-uso").val();
    var pass = $("#pass").val();
    var remitente = $("#remitente").val();
    var mailremitente = $("#correo-remitente").val();  // Agregado
    var host = $("#host-correo").val();
    var puerto = $("#puerto-acceso").val();
    var seguridad = $("#seguridad").val();
    var chuso1 = 0;
    var chuso2 = 0;
    var chuso3 = 0;
    var chuso4 = 0;
    var chuso5 = 0;
    var chuso6 = 0;

    if ($("#chuso1").prop('checked')) {
        chuso1 = 1;
    }

    if ($("#chuso2").prop('checked')) {
        chuso2 = 1;
    }

    if ($("#chuso3").prop('checked')) {
        chuso3 = 1;
    }

    if ($("#chuso4").prop('checked')) {
        chuso4 = 1;
    }

    if ($("#chuso5").prop('checked')) {
        chuso5 = 1;
    }
    
    if ($("#chuso6").prop('checked')) {
        chuso6 = 1;
    }

    if (isEmail(correo, "correo-uso") && isnEmpty(pass, "pass") && isnEmpty(remitente, "remitente") && isnEmpty(host, "host-correo") && isnEmpty(puerto, "puerto-acceso") && isnEmpty(seguridad, "seguridad")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "insertarcorreo", correo: correo, pass: pass, remitente: remitente, mailremitente: mailremitente, host: host, puerto: puerto, seguridad: seguridad, chuso1: chuso1, chuso2: chuso2, chuso3: chuso3, chuso4: chuso4, chuso5: chuso5, chuso6:chuso6},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    cargandoHide();
                    alertify.error(res);
                } else {
                    cargandoHide();
                    //alert(datos);
                    alertify.success('correo insertado');
                    loadViewConfig('correo');
                }
            }
        });
    }
}

function actualizarCorreo() {
    var idcorreo = $("#id-correo").val();
    var correo = $("#correo-uso").val();
    var pass = $("#pass").val();
    var remitente = $("#remitente").val();
    var mailremitente = $("#correo-remitente").val();
    var host = $("#host-correo").val();
    var puerto = $("#puerto-acceso").val();
    var seguridad = $("#seguridad").val();
    var chuso1 = 0;
    var chuso2 = 0;
    var chuso3 = 0;
    var chuso4 = 0;
    var chuso5 = 0;

    if ($("#chuso1").prop('checked')) {
        chuso1 = 1;
    }

    if ($("#chuso2").prop('checked')) {
        chuso2 = 1;
    }

    if ($("#chuso3").prop('checked')) {
        chuso3 = 1;
    }

    if ($("#chuso4").prop('checked')) {
        chuso4 = 1;
    }

    if ($("#chuso5").prop('checked')) {
        chuso5 = 1;
    }

    if (isnEmpty(idcorreo, "id-correo") && isEmail(correo, "correo-uso") && isnEmpty(pass, "pass") && isnEmpty(remitente, "remitente") && isnEmpty(host, "host-correo") && isnEmpty(puerto, "puerto-acceso") && isnEmpty(seguridad, "seguridad")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "actualizarcorreo", idcorreo: idcorreo, correo: correo, pass: pass, remitente: remitente, mailremitente: mailremitente, host: host, puerto: puerto, seguridad: seguridad, chuso1: chuso1, chuso2: chuso2, chuso3: chuso3, chuso4: chuso4, chuso5: chuso5},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Datos guardados');
                    loadViewConfig('correo');
                }
                cargandoHide();
            }
        });
    }
}

function actualizarRemitente() {
    var correoUso = $("#correo-uso").val();
    $("#correo-remitente").val(correoUso);
}

$("#correo-uso").on("keyup", function () {
    actualizarRemitente();
});

function cargarLogoMail() {
    var formData = new FormData();
    var imgInput = $("#imagen")[0].files[0]; 
    var rutaLogoMail = "temporal/tmp/";

    if (imgInput) {
        formData.append("imagen", imgInput);
        formData.append("ruta_personalizada", rutaLogoMail);
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
    } else {
        alertify.error("Por favor selecciona una imagen.");
    }
}

function getMailBody() {
    var body = $("#id-body").val();
    if (isnEmpty(body, "id-body")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "editarbody", idbody:  $("#id-body").val()},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    cargandoHide();
                } else {
                    setValoresEditarBody(datos);
                    cargandoHide();

                }
            }
        });
    }
}

function setValoresEditarBody(datos) {
    var array = datos.split("</tr>");
    var mensaje = array[3];
    var filenm = array[4];
    var txt = mensaje.replace(new RegExp("<corte>", 'g'), '\n');

    $("#asunto").val(array[1]);
    $("#saludo").val(array[2]);
    $("#texto-correo").val(txt);
    $("#muestraimagen").html(array[5]);
    $("#filename").val(filenm);
    $("#imgactualizar").val(filenm);

    typeText();
}

function actualizarBody() {
    var idbody = $("#id-body").val();
    var asunto = $("#asunto").val();
    var saludo = $("#saludo").val();
    var mensaje = $("#texto-correo").val();
    var filenm = $("#filename").val();
    var imgactualizar = $("#imgactualizar").val();
    var chlogo = $("#chlogo").prop('checked') ? 1 : 0;
    var txtbd = mensaje.replace(/\n/g, '<corte>');

    if (isnEmpty(idbody, "id-body") && isnEmpty(asunto, "asunto") && isnEmpty(mensaje, "mensaje")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {
                transaccion: "actualizarbody",
                idbody: idbody,
                asunto: asunto,
                saludo: saludo,
                txtbd: txtbd,
                filenm: filenm,
                imgactualizar: imgactualizar,
                chlogo: chlogo
            },
            success: function(datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                cargandoHide();
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success("Datos actualizados");
                    loadViewConfig('correo');
                }
            }
        });
    }
}

function typeText() {
    var asunto = $("#asunto").val();
    var saludo = $("#saludo").val();
    var nombre = "(Razon Social del cliente)";
    var mensaje = $("#texto-correo").val();
    var txtformat = mensaje.replace(new RegExp("\n", 'g'), "</p> <p style='font-size:18px; text-align: justify;'>");

    $("#asunto-lab").html(asunto);
    $("#saludo-lab").html(saludo + "" + nombre);
    $("#txt-lab").html(txtformat);
}

function opcionesCorreo() {
    $.ajax({
        url: 'com.sine.enlace/enlaceconfig.php',
        type: 'POST',
        data: {transaccion: 'opcionescorreo'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                $(".contenedor-correos").html(datos);
            }
        }
    });
}

function testCorreo() {
    var correo = $("#correo-uso").val();
    var pass = $("#pass").val();
    var remitente = $("#remitente").val();
    var mailremitente = $("#correo-remitente").val();
    var host = $("#host-correo").val();
    var puerto = $("#puerto-acceso").val();
    var seguridad = $("#seguridad").val();

    if (isEmail(correo, "correo-uso") && isnEmpty(pass, "pass") && isnEmpty(remitente, "remitente") && isnEmpty(host, "host-correo") && isnEmpty(puerto, "puerto-acceso") && isnEmpty(seguridad, "seguridad")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "testcorreo", correo: correo, pass: pass, remitente: remitente, mailremitente: mailremitente, host: host, puerto: puerto, seguridad: seguridad},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(res);
                }
                cargandoHide();
            }
        });
    }
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
        url: 'com.sine.enlace/cargaroffice.php',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            var array = datos.split("<corte>");
            $("#filename").val(array[0]);
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
            data: { transaccion: "loadexcel", fnm: fnm, tabla: tabla },
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

//--------------------------------ENCABEZADOS
function loadEncabezado() {
    var encabezado = $("#id-encabezado").val();
    if (isnEmpty(encabezado, "id-encabezado")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "editarencabezado", encabezado: encabezado},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    cargandoHide();
                } else {
                    hideCommon();
                    setValoresEncabezado(datos);
                }
            }
        });
    }
}

function setValoresEncabezado(datos) {
    var idencabezado = $("#id-encabezado").val();
    var array = datos.split("</tr>");
    var tituloenc = array[0];
    var titulocarta = array[1];
    var colortitulo = array[2];
    var colorceltitulo = array[3];
    var colorcuadro = array[4];
    var colorsub = array[5];
    var colorfdatos = array[6];
    var colorbold = array[7];
    var colortexto = array[8];
    var colorhtabla = array[9];
    var colortittabla = array[10];
    var pagina = array[11];
    var correo = array[12];
    var tel1 = array[13];
    var tel2 = array[14];
    var numpag = array[15];
    var colorpie = array[16];
    var imglogo = array[17];
    var imgarr = imglogo.split("/");
    var observaciones = array[18];

    $("#titulo").val(tituloenc);
    $("#color-titulo").val(colortitulo);
    $("#color-celda").val(colorceltitulo);
    $("#color-cuadro").val(colorcuadro);
    $("#color-subtitulo").val(colorsub);
    $("#fondo-datos").val(colorfdatos);
    $("#texto-bold").val(colorbold);
    $("#color-texto").val(colortexto);
    $("#color-tabla").val(colorhtabla);
    $("#titulos-tabla").val(colortittabla);
    $("#pagina").val(pagina);
    $("#correo").val(correo);
    $("#telefono1").val(tel1);
    $("#telefono2").val(tel2);
    $("#color-pie").val(colorpie);
    $("#filename").val(imgarr[0]);
    $("#imgactualizar").val(imglogo);

    if( idencabezado == '12' ){
        $('#width-ticket').val(numpag);
        var array_datos = titulocarta.split('</>');

        if( array_datos[0] == 1){
            $('#chk-data').prop('checked', true);
            $('#datos-empresa').show('slow');
            $('#header-logo').show('slow');

        } else if( array_datos[0] == 2){
            $('#chk-logo').prop('checked', true);
            $('#data-empresa').hide('slow');
            $('#header-logo').show('slow');

        }else if( array_datos[0] == 3){
            $('#chk-only-data').prop('checked', true);
            $('#datos-empresa').show('slow');
            $('#header-logo').hide('slow');
        }
        
        $('#nombre-empresa').val(array_datos[1]);
        $('#razon-social').val(array_datos[2]);
        $('#direccion').val(array_datos[3]);
        $('#rfc-empresa').val(array_datos[4]);
    }

    if (numpag == '1') {
        $("#chnum").prop('checked', true);
    } else {
        $("#chnum").removeAttr('checked');
    }

    if (imglogo !== '') {
        $("#imagenencabezado").show('slow');
        $("#muestraimagenencabezado").html("<img src='img/logo/" + imglogo + "'>");
    }

    if (idencabezado == '3' || idencabezado == '12') {
        var txtbd = observaciones.replace(new RegExp("<ent>", 'g'), '\n');
        $("#add-observaciones").removeAttr('hidden');
        $("#observaciones-cot").val(txtbd);
    } else {
        $("#add-observaciones").attr('hidden', true);
        $("#observaciones-cot").val('');
    }
    
    if (idencabezado == '11') {
        $("#carta-titulo").removeAttr('hidden');
        $("#titulo2").val(titulocarta);
    } else {
        $("#carta-titulo").attr('hidden', true);
        $("#titulo2").val('');
    }
    $("#eliminarimagen").attr("onclick", "eliminarImagen('actualizar')");
    cargandoHide();
    visualizarPDF();
}

function cargarLogo() {
    var formData = new FormData();
    var imgInput = $("#imagen")[0].files[0]; 
    var rutaLogos = "temporal/tmp/";
    var idencabezado = $("#id-encabezado").val();
    var img = $("#imagen").val();
    
    formData.append("imagen", imgInput);
    formData.append("ruta_personalizada", rutaLogos);
    if (isnEmpty(idencabezado, "id-encabezado") && isnEmpty(img, "imagen")) {
        $.ajax({
            url: 'com.sine.enlace/cargarimg.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var array = datos.split("<corte>");
                if(array[0] != ""){
                    $("#imagenencabezado").show('slow');
                    $("#muestraimagenencabezado").html(array[0]);
                    $("#filename").val(array[1]);
                }
                visualizarPDF();
            }
        });
    }
}

function visualizarPDF() {
    var widthticket = $('#width-ticket').val();
    if (widthticket < 58) {
        widthticket = 58;
        $('#width-ticket').val(58); 
    }
    var idencabezado = $("#id-encabezado").val();
    var titulo = $("#titulo").val();
    var titulo2 = $("#titulo2").val();
    var clrtitulo = $("#color-titulo").val();
    var colorcelda = $("#color-celda").val();
    var clrcuadro = $("#color-cuadro").val();
    var clrsub = $("#color-subtitulo").val();
    var clrfdatos = $("#fondo-datos").val();
    var txtbold = $("#texto-bold").val();
    var clrtxt = $("#color-texto").val();
    var colorhtabla = $("#color-tabla").val();
    var tittabla = $("#titulos-tabla").val();
    var pagina = $("#pagina").val();
    var correo = $("#correo").val();
    var tel1 = $("#telefono1").val();
    var tel2 = $("#telefono2").val();
    var clrpie = $("#color-pie").val();
    var imagen = $('#filename').val();
    var widthticket = $('#width-ticket').val();
    var rfcempresa = $('#rfc-empresa').val();
    var direccion = $('#direccion').val();
    var razonsocial = $('#razon-social').val();
    var nombreempresa = $('#nombre-empresa').val(); 

    var observaciones = "";
    if (idencabezado == '3' || idencabezado == '12') {
        observaciones = $("#observaciones-cot").val();
    }

    var chnum = 0;
    if ($("#chnum").prop('checked')) {
        chnum = 1;
    }
    var chlogo = 0;
    if ($("#chlogo").prop('checked')) {
        chlogo = 1;
    }

    var chkdata = 0;
    if( $('#chk-data').is(':checked') ){//datos y logo
        chkdata = 1;
    } else if( $('#chk-logo').is(':checked') ){//solo logo
        chkdata = 2;
    }else if( $('#chk-only-data').is(':checked') ){//solo datos
        chkdata = 3;
    }
    if (isnEmpty(idencabezado, "id-encabezado")) {
        $.ajax({
            url: "com.sine.imprimir/configpdf.php",
            type: "POST",
            data: {transaccion: "pdf", idencabezado: idencabezado, titulo: titulo, clrtitulo: clrtitulo, colorcelda: colorcelda, clrcuadro: clrcuadro, clrsub: clrsub, clrfdatos: clrfdatos, txtbold: txtbold, clrtxt: clrtxt, colorhtabla: colorhtabla, tittabla: tittabla, pagina: pagina, correo: correo, tel1: tel1, tel2: tel2, clrpie: clrpie, imagen: imagen, chnum: chnum, chlogo: chlogo, observaciones: observaciones, widthticket: widthticket, titulo2: titulo2, rfcempresa: rfcempresa, direccion: direccion, razonsocial: razonsocial, nombreempresa: nombreempresa, chkdata: chkdata},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    
                    document.getElementById('myIframe').contentDocument.location.reload()
                }
                cargandoHide();
            }
        });
    }
}

function actualizarEncabezado() {
    var idencabezado = $("#id-encabezado").val();
    var titulo = $("#titulo").val();
    var titulocarta = $("#titulo2").val();
    var clrtitulo = $("#color-titulo").val();
    var colorcelda = $("#color-celda").val();
    var clrcuadro = $("#color-cuadro").val();
    var clrsub = $("#color-subtitulo").val();
    var clrfdatos = $("#fondo-datos").val();
    var txtbold = $("#texto-bold").val();
    var clrtxt = $("#color-texto").val();
    var colorhtabla = $("#color-tabla").val();
    var tittabla = $("#titulos-tabla").val();
    var pagina = $("#pagina").val();
    var correo = $("#correo").val();
    var tel1 = $("#telefono1").val();
    var tel2 = $("#telefono2").val();
    var clrpie = $("#color-pie").val();
    var imagen = $('#filename').val();
    var imgactualizar = $("#imgactualizar").val();
    var txtbd = "";
    if (idencabezado == '3' || idencabezado == '12') {
        var observaciones = $("#observaciones-cot").val();
        txtbd = observaciones.replace(new RegExp("\n", 'g'), '<ent>');
    }

    var chnum = 0;
    if ($("#chnum").prop('checked')) {
        chnum = 1;
    }

    if( idencabezado == '12' ){
        var chkdata = 0;
        var rfcempresa = $('#rfc-empresa').val();
        var direccion = $('#direccion').val();
        var razonsocial = $('#razon-social').val();
        var nombreempresa = $('#nombre-empresa').val();

        chnum = $('#width-ticket').val();

        if( $('#chk-data').is(':checked') ){//datos y logo
        chkdata = 1;
        } else if( $('#chk-logo').is(':checked') ){//solo logo
            chkdata = 2;
        }else if( $('#chk-only-data').is(':checked') ){//solo datos
            chkdata = 3;
        }
        titulocarta =  chkdata+'</>'+nombreempresa+'</>'+razonsocial+'</>'+direccion+'</>'+rfcempresa;
    }

    $("#eliminarimagen").attr("onclick", "eliminarImagen('actualizar')");
    var chlogo = 0;
    if ($("#chlogo").prop('checked')) {
        chlogo = 1;
    }

    if (isnEmpty(idencabezado, "id-encabezado") && isnEmpty(titulo, "titulo") && isnEmpty(clrtitulo, "color-titulo") && isnEmpty(tel1, "telefono1")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceconfig.php",
            type: "POST",
            data: {transaccion: "actualizarencabezado", idencabezado: idencabezado, titulo: titulo, titulocarta:titulocarta, clrtitulo: clrtitulo, colorcelda: colorcelda, clrcuadro: clrcuadro, clrsub: clrsub, clrfdatos: clrfdatos, txtbold: txtbold, clrtxt: clrtxt, colorhtabla: colorhtabla, tittabla: tittabla, pagina: pagina, correo: correo, tel1: tel1, tel2: tel2, clrpie: clrpie, imagen: imagen, chnum: chnum, imgactualizar: imgactualizar, chlogo: chlogo, observaciones: txtbd},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    cargandoHide();
                    alertify.error(res);
                } else {
                    cargandoHide();
                    
                    alertify.success('Datos guardados');
                    loadViewConfig('encabezado');
                }
            }
        });
    }
}

function hideCommon(){
    if( $('#id-encabezado').val() == 12 ){
        $('#common-ticket').show();
        $('#datos-empresa').show();
        $('#common-cuerpo').hide();
        $('#common-pie').hide();
        $('#common-subtitulos').hide();
        $('#common-table').hide();
        $('#config-gral').hide();
        $('#lbl-despedida').html('Mensaje Pie Ticket');
        $('#lbl-pagina').html('Inferior Izquierda');
        $('#lbl-correo').html('Inferior Derecha');
    }
    else {
        $('#common-ticket').hide();
        $('#datos-empresa').hide();
        $('#common-cuerpo').show();
        $('#common-pie').show();
        $('#common-subtitulos').show();
        $('#common-table').show();
        $('#config-gral').show();
        $('#lbl-despedida').html('Mensaje de Observaciones');
        $('#lbl-pagina').html('Pagina');
        $('#lbl-correo').html('Correo');
    }
}

function habilitarDatos() {
    if( $('#chk-data').is(':checked') ){//datos y logo
        $('#data-empressa').show('slow');
        $('#header-logo').show('slow');

    } else if( $('#chk-logo').is(':checked') ){//solo logo
         $('#data-empressa').hide('slow');
        $('#header-logo').show('slow');
        
    }else if ($('#chk-only-data').is(':checked')) {//solo datos
        $('#data-empressa').show('slow');
        $('#header-logo').toggle('slow');
    }
    visualizarPDF();
}

function verificaAnchoticket() {
    var width = $('#width-ticket').val();
    if (width >= 58) {
        visualizarPDF();
    } else {
        alertify.error("El tamaño mínimo del ancho del ticket es de 58mm.");
        $('#width-ticket').val(58);
        visualizarPDF(); 
    }
}

function eliminarImgTpm() {
    var imgtmp = $("#filename").val();
    if (imgtmp != '') {
        $.ajax({
            data: { transaccion: "eliminarimgtmp", imgtmp: imgtmp},
            url: 'com.sine.enlace/enlaceproducto.php',
            type: 'POST',
            dataType: 'JSON',
            success: function (datos) {
                cargandoHide();
                console.log(datos);
            }
        });
    }
}

function eliminarImagen(tipoOperacion, idproducto) {
    var confirmMessage = "";

    if (tipoOperacion === 'nuevo') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen?";
    } else if (tipoOperacion === 'actualizar') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen en relación al producto? Una vez borrada no se podrá incorporar nuevamente.";
    } else if (tipoOperacion === 'copia') {
        confirmMessage = "¿Estás seguro que deseas quitar la imagen actual?";
    }

    alertify.confirm(confirmMessage, function (e) {
        if (e) {
            if (tipoOperacion === 'nuevo') {
                eliminarImgTpm(); 
                $("#imagenencabezado").hide('slow');
            } else if (tipoOperacion === 'actualizar') {
                $("#imagenencabezado").hide('slow');
                $("#muestraimagenencabezado").html('');
                $("#filename").val('');
                $("#imgactualizar").val('');
            }
        }
    }).set({ title: "Q-ik" });
}
