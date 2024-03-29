$(function () {
    $(".button-tab").click(function () {
        $('.button-tab').removeClass("tab-active");
        $('.div-form').hide();
        var tab = $(this).attr("data-tab");
        $("#" + tab).show();
        $(this).addClass("tab-active");
    });
});

$(function () {
    $(".sub-button-tab").click(function () {
        $('.sub-button-tab').removeClass("sub-tab-active");
        $('.sub-div').hide();
        var tab = $(this).attr("data-tab");
        $("#sub-" + tab).show();
        $(this).addClass("sub-tab-active");
        setNavigation(tab);
    });
});

$(function () {
    $(".next-prev").click(function () {
        var tab = $(this).attr("data-tab");
        var nav = $(this).attr("data-nav");

        if (nav == 'top') {
            $('.div-form').hide();
            $('.button-tab').removeClass("tab-active");

            $("#div-" + tab).show();
            $("#tab-" + tab).addClass("tab-active");
        } else if (nav == 'sub') {
            $('.sub-div').hide();
            $('.sub-button-tab').removeClass("sub-tab-active");
            setNavigation(tab);
            $("#sub-" + tab).show();
            $("#tab-" + tab).addClass("sub-tab-active");
        }
        $("html,body").scrollTop(0);
    });
}); 

function filtrarCarta(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "filtrarfolio", pag: pag, REF: $("#buscar-carta").val(), numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-carta").html(datos);
                cargandoHide();
            }
        }
    });
}

function filtrarProducto(pag = "") {
    cargandoHide();
    cargandoShow();
    var NOM = $("#buscar-producto").val();
    var numreg = $("#num-reg").val();
    $("#body-lista-productos-factura").append('');
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: NOM, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                var array = datos.split("<pag>");
                var table = array[0];
                var pag = array[1];
                $("#body-lista-productos-factura").append(table);
                $("#pagination").append(pag);
                cargandoHide();
            }
        }
    });
}

function buscarProducto(pag = "") {
    var NOM = $("#buscar-producto").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: NOM, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<pag>");
                var table = array[0];
                var pag = array[1];
                $("#body-lista-productos-factura").html(table);
                $("#pagination").html(pag);
            }
        }
    });
}

function setCamposProducto() {
    $("#codigo-producto").val('');
    $("#producto").val('');
    $("#tipo").val('');
    $("#inventario").attr('hidden', true);
    $("#clave-unidad").val('');
    $("#descripcion").val('');
    $("#pcompra").val(0);
    $("#porganancia").val(0);
    $("#ganancia").val(0);
    $("#pventa").val(0);
    $("#clave-fiscal").val('');
    $('#id-proveedor').val('');
    $("#imagen").val('');
    $('#muestraimagen').html("");
    $("#btn-form-producto-factura").attr("onclick", "insertarProductoCarta();");
}

function addinventario() {
    var tipo = $("#tipo").val();
    if (tipo == '1') {
        $("#inventarios").show('slow');
        if ($("#chinventario").prop('checked')) {
            $("#cantidad").removeAttr('disabled');
            changeText("#labelinventario", "¿Desactivar inventario?");
        } else {
            $("#cantidad").attr('disabled', true);
            $("#cantidad").val('0');
            changeText("#labelinventario", "¿Activar inventario?");
        }
    } else {
        $("#chinventario").removeAttr('checked');
        $("#inventarios").hide('slow');
        $("#cantidad").attr('disabled', true);
        $("#cantidad").val('0');
        $("#clave-unidad").val('E48-Unidad de servicio');
    }
}

function cancelarCarta() {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "cancelar"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                loadView('listacarta');
            }
        }
    });
}

function calcularGanancia() {
    var preciocompra = $("#pcompra").val() || '0';
    var porcentaje = $("#porganancia").val() || '0';
    var importeganancia = (parseFloat(preciocompra) * parseFloat(porcentaje)) / 100;
    $("#ganancia").val(myRound(importeganancia, 2));
    var precioventa = parseFloat(preciocompra) + parseFloat(importeganancia);
    var total = myRound(precioventa, 2)
    $("#pventa").val(total);
    calcularImpuestosTotal();
}


function eliminarImagen(tipoOperacion) {
    var confirmMessage = "";

    if (tipoOperacion === 'nuevo') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen?";
    } else if (tipoOperacion === 'actualizar') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen en relación al producto? Una vez borrada no se podrá incorporar nuevamente.";
    } 

    alertify.confirm(confirmMessage, function (e) {
        if (e) {
            if (tipoOperacion === 'nuevo') {
                eliminarImgTpm(); 
                $("#imagenproducto").hide('slow');
            } else if (tipoOperacion === 'copia' || tipoOperacion === 'actualizar') {
                $("#imagenproducto").hide('slow');
                $("#muestraimagenproducto").html('');
                $("#filename").val('');
                $("#nameimg").val('');
                $("#imgactualizar").val('');
            }
        }
    }).set({ title: "Q-ik" });
}

function setNavigation(tab) {
    switch (tab) {
        case 'mercancia':
            $("#btn-form-prev").attr("data-tab", 'factura');
            $("#btn-form-prev").attr("data-nav", 'top');

            $("#btn-form-next").attr("data-tab", 'transporte');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'transporte':
            $("#btn-form-prev").attr("data-tab", 'mercancia');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("data-tab", 'ubicacion');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'ubicacion':
            $("#btn-form-prev").attr("data-tab", 'transporte');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("data-tab", 'operador');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'operador':
            $("#btn-form-prev").attr("data-tab", 'ubicacion');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("data-tab", 'evidencia');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'evidencia':
            $("#btn-form-prev").attr("data-tab", 'operador');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("hidden", 'true');
            break;
        default:
            $("#btn-form-prev").attr("data-tab", 'factura');
            $("#btn-form-prev").attr("data-nav", 'top');

            $("#btn-form-next").attr("data-tab", 'transporte');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
    }
}

function loadFolioCarta(iddatos = "") {
    cargandoShow();
    if(iddatos == ""){
        iddatos = $("#datos-facturacion").val();
    }
    $.ajax({
        url: 'com.sine.enlace/enlacecarta.php',
        type: 'POST',
        data: {transaccion: 'emisor', iddatos: iddatos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                var rfc = array[0];
                var razon = array[1];
                var clvreg = array[2];
                var regimen = array[3];
                var codpos = array[4];
                $("#rfc-emisor").val(rfc);
                $("#razon-emisor").val(razon);
                $("#regimen-emisor").val(clvreg + "-" + regimen);
                $("#cp-emisor").val(codpos);
            }
            cargandoHide();
        }
    });
}

//---------------------------------------------AUTOACOMPLETADO
function autocompletarMercancia() {
    var chbus = $("input[name=busqueda]:checked").val();
    $('#clv-producto').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=mercancia&&b=" + chbus,
        select: function (event, ui) {
            var a = ui.item.value;
            var nombre = ui.item.nombre;
            var peligro = ui.item.peligro;

            $("#descripcion-mercancia").val(nombre);
            $("#peligro-mercancia").val(peligro);
            if (peligro === '0-1' || peligro === '1') {
                if (peligro === '1') {
                    $("#material-peligroso").val(peligro);
                } else {
                    $("#material-peligroso").val('');
                }
                $("#material-peligroso").removeAttr('disabled');
                $("#clv-peligro").removeAttr('disabled');
                $("#clv-embalaje").removeAttr('disabled');
            } else if (peligro == '0') {
                $("#material-peligroso").attr('disabled', true);
                $("#clv-peligro").attr('disabled', true);
                $("#clv-embalaje").attr('disabled', true);
            }
        }
    });
}

function autocompletarCliente() {
    if ($("#nombre-cliente").val() == '') {
        $("#id-cliente").val('0');
    }
    $('#nombre-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var rfc = ui.item.rfc;
            var razon = ui.item.razon;
            var regfiscal = ui.item.regfiscal;
            var codpostal = ui.item.codpostal;
            var direccion = ui.item.direccion;

            $("#id-cliente").val(id);
            $("#rfc-cliente").val(rfc);
            $("#razon-cliente").val(razon);
            $("#regfiscal-cliente").val(regfiscal);
            $("#cp-cliente").val(codpostal);
            $("#direccion-cliente").val(direccion);
        }
    });
}