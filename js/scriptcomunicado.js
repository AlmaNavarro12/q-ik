
$("[name=typecom]").click(function () {
    if ($(this).val() == '1') {
        $('#contactos-div').hide('slow');
    } else if ($(this).val() == '2') {
        $('#contactos-div').show('slow');
    }
});

function displayFileCom(id) {
    $.ajax({
        url: "com.sine.imprimir/img.php",
        type: "POST",
        data: {pic: id},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                var array = datos.split("<type>");
                var t = array[0];
                var data = array[1];
                if(t == 'd'){
                    $('#archivo').modal('show');
                    $('#foto').html(data);
                    //var newTab = window.open('com.sine.imprimir/img.php?filecom='+id);
                    //newTab.document.body.innerHTML = data;
                }else{
                    //var newTab = window.open();
                    //newTab.document.body.innerHTML = data;
                    $('#archivo').modal('show');
                    $('#foto').html(data);
                }
            }
        }
    });
}

function disableDatos() {
    if ($("#chfirmar").prop('checked')) {
        $("#datos-facturacion").removeAttr('disabled');
    } else {
        $("#datos-facturacion").attr('disabled', true);
    }
}

function loadContactos() {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php",
        type: "POST",
        data: {transaccion: "loadcontactos"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#contactos-div").html(datos);
            }
            cargandoHide();
        }
    });
}

function loadCategoria() {
    var idcategoria = $("#id-categoria").val();
    $.ajax({
        url: 'com.sine.enlace/enlacecomunicado.php',
        type: 'POST',
        data: {transaccion: 'loadcategoria', idcategoria: idcategoria},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);

            } else {
                $("#categoria").val(datos);
            }
            //cargandoHide();
        }
    });
}

function cargarImgCom() {
    var img = $("#imagen").val();
    var formData = new FormData(document.getElementById("form-comunicado"));
    if (isnEmpty(img, 'imagen')) {
        cargandoHide(); 
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarimgs.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                tablaIMG();
            }
        });
    }
}

function tablaIMG(d="") {
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php",
        type: "POST",
        data: {transaccion: "tablaimg", d:d},
        success: function (datos) {
            console.log(datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var tab = array[1];
                $("#img-table").html(tab);
            }
            cargandoHide();
        }
    });
}

function eliminarIMG(idtmp) {
    alertify.confirm("Esta seguro que desea eliminar este archivo?", function () {
        //cargandoHide();
        //cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecomunicado.php",
            type: "POST",
            data: {transaccion: "eliminarimg", idtmp: idtmp},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    //cargandoHide();

                }
                tablaIMG();
            }
        });
    }).set({title: "Q-ik"});
}

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

function aucompletarCliente() {
    $('#nombre-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {

            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function aucompletarCorreo() {
    $('#email-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=emailcliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}


function gestionarComunicado(idcomunicado = null) {
    var tag = $("#tag").val();
    var chcom = $("input[name=typecom]:checked").val();
    var asunto = $("#asunto").val();
    var emision = $("#emision").val();
    var color = $("#color-txt").val();
    var size = $("#size-txt").val();
    var texto = $("#texto-comunicado").val();
    var sellar = $("#chsello").prop('checked') ? 1 : 0;
    var firma = $("#chfirmar").prop('checked') ? 1 : 0;
    var iddatos = $("#datos-facturacion").val() || '0';
    var txtbd = texto.replace(new RegExp("\n", 'g'), '<corte>');
    var idcontactos = "0";

    if (chcom == '2') {
        var contacto = [];
        $.each($("input[name='contacto']:checked"), function () {
            contacto.push($(this).val());
        });
        idcontactos = contacto.join("-");
    }

    if (isnEmpty(asunto, "asunto") &&
        isnEmpty(emision, "emision") &&
        isnEmpty(texto, "texto-comunicado")) {

        cargandoHide();
        cargandoShow();

        var url = "com.sine.enlace/enlacecomunicado.php";
        var transaccion = idcomunicado ? "actualizarcomunicado" : "insertarcomunicado";
        var data = {
            transaccion: transaccion,
            idcomunicado: idcomunicado,
            tag: tag,
            chcom: chcom,
            asunto: asunto,
            emision: emision,
            color: color,
            size: size,
            txtbd: txtbd,
            idcontactos: idcontactos,
            sellar: sellar,
            firma: firma,
            iddatos: iddatos
        };

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success("Comunicado " + (transaccion === 'insertarcomunicado' ? 'guardado' : 'actualizado') + " correctamente");
                    loadView('listacomunicado');
                    tablaIMG();
                }
                cargandoHide();
            }
        });
    }
}

function buscarComunicados(pag = "") {
    var REF = $("#buscar-comunicado").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php",
        type: "POST",
        data: {transaccion: "listacomunicado", REF: REF, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-comunicado").html(datos);
            }
        }
    });
}

function listaComunicados(pag = "") {
    cargandoHide();
    cargandoShow();
    var REF = "";
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php",
        type: "POST",
        data: {transaccion: "listacomunicado", REF: REF, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-comunicado").html(datos);
            }
            cargandoHide();
        }
    });
}

function editarComunicado(idcomunicado) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php",
        type: "POST",
        data: {transaccion: "editarcomunicado", idcomunicado: idcomunicado},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('comunicado');
                window.setTimeout("setValoresEditarComunicado('" + datos + "')", 500);
            }
        }
    });
}

function setValoresContactos(datos) {
    $.each($("input[name='contacto']:checked"), function () {
        $("input[name='contacto']").removeAttr('checked');
    });
    var array = datos.split("-");
    for (var i = 0, max = array.length; i < max; i++) {
        $("#ch" + array[i]).prop('checked', true);
    }
    cargandoHide();
}

function setValoresEditarComunicado(datos) {
    changeText("#contenedor-titulo-form-comunicado", "Editar Comunicado");
    changeText("#btn-form-comunicado", "Guardar cambios <span class='fas fa-save'></span>");

    var array = datos.split("</tr>");
    var idcomunicado = array[0];
    var fechacom = array[1];
    var horacom = array[2];
    var chcom = array[3];
    var asunto = array[4];
    var mensaje = array[5];
    var tag = array[6];
    var color = array[7];
    var size = array[8];
    var contactos = array[9];
    var chsellar = array[10];
    var chfirmar = array[11];
    var iddatos = array[12];
    var nombre = array[13];
    var emision = array[14];
    var txt = mensaje.replace(new RegExp("<corte>", 'g'), '\n');

    $("#tag").val(tag);
    $("#fecha-creacion").val(fechacom + " " + horacom);
    $("#typecom" + chcom).prop('checked', true);
    $("#asunto").val(asunto);
    $("#emision").val(emision);
    $("#color-txt").val(color);
    $("#size-txt").val(size);
    $("#texto-comunicado").val(txt);
    
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php",
        type: "POST",
        data: {transaccion: "imgscom", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                tablaIMG();
            }
        }
    });

    if (chsellar == '1') {
        $("#chsello").attr('checked', true);
    }

    if (chfirmar == '1') {
        $("#chfirmar").attr('checked', true);
        $("#datos-facturacion").removeAttr('disabled');
        $("#option-default-datos").val(iddatos);
        $("#option-default-datos").text(nombre);
    }

    $("#btn-form-comunicado").attr("onclick", "gestionarComunicado(" + idcomunicado + ");");
    if(chcom == '2'){
        $('#contactos-div').show('slow');
        window.setTimeout("setValoresContactos('" + contactos + "')", 300);
    }
}


function eliminarComunicado(idcomunicado) {
    alertify.confirm("Esta seguro que desea eliminar este comunicado?", function () {
        //cargandoHide();
        //cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecomunicado.php",
            type: "POST",
            data: {transaccion: "eliminarcomunicado", idcomunicado: idcomunicado},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    //cargandoHide();
                    alertify.success('Se elimino correctamente el comunicado')
                    loadView('listacomunicado');
                }
            }
        });
    }).set({title: "Q-ik"});
}

function loadFecha() {
    ////cargandoShow();
    $.ajax({
        url: 'com.sine.enlace/enlacecomunicado.php',
        type: 'POST',
        data: {transaccion: 'fecha'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var fecha = array[0];
                var nombre = array[1];
                $("#fecha-creacion").val(fecha);
                $("#fecha-nombre").val(nombre);
            }
            ////cargandoHide();
        }
    });
}

function imprimirComunicado(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimircomunicado.php?com=' + id, 'Comunicado', '', '1024', '768', 'true');
    cargandoHide();
}

function crearComunicado(idcomunicado) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.imprimir/imprimircomunicado.php",
        type: "POST",
        data: {transaccion: "pdf", idcomunicado: idcomunicado},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(0, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                alertify.success(res);
                cargandoHide();
            }
        }
    });
}

function canelarComunicado() {
    $.ajax({
        url: "com.sine.enlace/comunicado.php",
        type: "POST",
        data: {transaccion: "cancelar"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                loadView('listacomunicado');
                
            }
        }
    });
    
}

//funcio
function tablamodal(tag) {
    console.log(tag);
    $('#archivo').modal('show');
    $.ajax({
        url: "com.sine.enlace/enlacecomunicado.php", 
        type: "POST",
        data: { transaccion: "modaltabla", tag:tag }, 
        success: function (datos) {
            console.log(datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#listaarchivo").html(datos);
            }
        }
       
    
        
    });
}

function visutab(archivo) {
    var ruta = "./comunicado/" + archivo;
    $('#foto embed').attr('src', ruta);
}