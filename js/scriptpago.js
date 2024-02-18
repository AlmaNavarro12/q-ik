$(function () {
    $("#tabs").on("click", "button.tab-pago", function () {
        $('.tab-pago').removeClass("sub-tab-active");
        $('.sub-div').hide();
        var tab = $(this).attr("data-tab");
        $("#complemento-" + tab).show();
        $(this).addClass("sub-tab-active");
    });

    $("#tabs").on("click", "span.close-button", function () {
        var tab = $(this).attr("data-tab");
        cerrarComplemento(tab);
    });
});

var comp = 1;
function agregarComplemento() {
    $.ajax({
        url: 'com.sine.enlace/enlacepago.php',
        type: 'POST',
        data: {transaccion: 'nuevocomplemento', comp: comp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                var array = datos.split("<cut>");
                $("#tabs").append(array[0]);
                $("#complementos").append(array[1]);
                var tag = array[2];
                $(".sub-div").hide();
                $(".tab-pago").removeClass("sub-tab-active");

                $("#tab-" + tag).addClass('sub-tab-active');
                $("#complemento-" + tag).show();
                loadFormaPago(tag);
                loadMonedaComplemento(tag);
                comp++;
            }
        }
    });
}

function loadFormaPago(tag="") {
    $.ajax({
        data : {transaccion: 'getOptions'},
        url  : '../../CATSAT/CATSAT/com.sine.enlace/enlaceFormaPago.php',
        type : 'POST',
        dataType : 'JSON',
        success  : function(res){
            if(res.status > 0){
                $(".cont-fpago-" + tag).html(res.datos);
            }
        }
    });
}

function loadMonedaComplemento(tag = "") {
    $.ajax({
        data : {transaccion: 'getOptions'},
        url  : '../../CATSAT/CATSAT/com.sine.enlace/enlaceMonedas.php',
        type : 'POST',
        dataType : 'JSON',
        success  : function(res){
            if(res.status > 0){
                $(".contmoneda-" + tag).html(res.datos);
            }
        }
    });
}

function cerrarComplemento(tab = "") {
    alertify.confirm("Esta seguro que desea eliminar este complemento? (Toda la informacion ingresada se perdera)", function () {
        if (tab == '') {
            tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
        }
        $.ajax({
            url: 'com.sine.enlace/enlacepago.php',
            type: 'POST',
            data: {transaccion: 'borrarcomplemento', tab: tab},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#tab-" + tab).remove();
                    $("#complemento-" + tab).remove();
                    var first = $("#tabs").find('.tab-pago:first').attr("data-tab");
                    if (first) {
                        $("#complemento-" + first).show();
                        $("#tab-" + first).addClass("sub-tab-active");
                    }
                }
            }
        });
    }).set({title: "Q-ik"});
}


function buscarPago(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: { transaccion: "listapagoaltas", REF: $("#buscar-pago").val(), pag: pag, numreg: $("#num-reg").val() },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-pagos").html(datos);
            }
        }
    });
}

function loadFecha() {
    $.ajax({
        url: 'com.sine.enlace/enlacefactura.php',
        type: 'POST',
        data: { transaccion: 'fecha' },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '') {
                alertify.error(res);
            } else {
                $("#fecha-creacion").val(datos);
            }
        }
    });
}

function loadListaPago(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: { transaccion: "listapagoaltas", REF: $("#buscar-pago").val(), pag: pag, numreg: $("#num-reg").val() },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-pagos").html(datos);
                cargandoHide();
            }
        }
    });
}

function loadFolioPago(iddatos = "") {
    cargandoHide();
    cargandoShow();
    iddatos = iddatos || $("#datos-facturacion").val();

    $.ajax({
        url: 'com.sine.enlace/enlacepago.php',
        type: 'POST',
        data: { transaccion: 'emisor', iddatos: iddatos },
        success: function (datos) {
            var array = datos.split("</tr>");
            var bandera = datos.charAt(0);
            var res = datos.substring(1, 5000);

            if (bandera == "") {
                alertify.error(res);
                cargandoHide();
            } else {
                var array = datos.split("</tr>");
                $("#rfc-emisor").val(array[0]);
                $("#razon-emisor").val(array[1]);
                $("#regimen-emisor").val(array[2] + "-" + array[3]);
                $("#cp-emisor").val(array[4]);
                cargandoHide();
            }
        }
    });
}

function autocompletarCliente() {
    $('#nombre-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {
            $("#id-cliente").val(ui.item.id);
            $("#rfc-cliente").val(ui.item.rfc);
            $("#razon-cliente").val(ui.item.razon);
            $("#regfiscal-cliente").val(ui.item.regfiscal);
            $("#cp-cliente").val(ui.item.codpostal);
        }
    });
}

function disableCuenta() {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var formapago = $("#forma-" + tag).val();
    if (formapago == '2' || formapago == '3' || formapago == '4' || formapago == '5' || formapago == '6' || formapago == '18' || formapago == '19') {
        $("#cuenta-" + tag).removeAttr('disabled');
        $("#benef-" + tag).removeAttr('disabled');
        $("#transaccion-" + tag).removeAttr('disabled');
        loadBancoCliente(tag);
        loadBancoBeneficiario(tag);
    } else {
        $("#cuenta-" + tag).attr('disabled', true);
        $("#cuenta-" + tag).val('');
        $("#benef-" + tag).attr('disabled', true);
        $("#benef-" + tag).val('');
        $("#transaccion-" + tag).attr('disabled', true);
    }
}

function loadBancoCliente(tag) {
    $.ajax({
        url: 'com.sine.enlace/enlaceopcion.php',
        type: 'POST',
        data: { transaccion: 'opcionesbancocliente', idcliente: $("#id-cliente").val() },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                //alertify.error('Debe llenar datos del receptor.');
            } else {
                $(".contenedor-cuenta-" + tag).html(datos);
            }
        }
    });
}

function loadBancoBeneficiario(tag) {
    var iddatos = $("#datos-facturacion").val();
    $.ajax({
        url: 'com.sine.enlace/enlaceopcion.php',
        type: 'POST',
        data: {transaccion: 'opcionesbeneficiario', iddatos: iddatos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                //alertify.error(res);
            } else {
                $(".contenedor-beneficiario-" + tag).html(datos);
            }
        }
    });
}

function getTipoCambio() {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    cargandoHide();
    cargandoShow();
    var idmoneda = $("#moneda-" + tag).val();
    $.ajax({
        url: '../../CATSAT/CATSAT/com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        data: { transaccion: 'gettipocambio', idmoneda: idmoneda },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                if (idmoneda != "1") {
                    $("#cambio-" + tag).removeAttr('disabled');
                } else {
                    $("#cambio-" + tag).attr('disabled', true);
                }
                $("#cambio-" + tag).val(datos);
            }
            cargandoHide();
        }
    });
}

function loadTablaCFDI(uuid = "") {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var idmoneda = $("#moneda-" + tag).val();
    var tcambio = $("#cambio-" + tag).val();
    if (isnEmpty(idmoneda, "id-moneda-pago")) {
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: { transaccion: "loadtabla", tag: tag, idmoneda: idmoneda, tcambio: tcambio, uuid: uuid },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var array = datos.split("<corte>");
                    var p2 = array[1];
                    $("#lista-cfdi-" + tag).html(p2);
                }
                cargandoHide();
            }
        });
    }

    function setvaloresFactura(datos, tag) {
        var array = datos.split("</tr>");
        var iddatosfactura = array[0];
        var uuid = array[1];
        var tcambio = array[2];
        var idmoneda = array[3];
        var idmetodo = array[4];
        var totalfactura = array[5];
        var noparcialidad_tmp = array[6];
        var montoant_tmp = array[7];

        $("#id-factura-" + tag).val(iddatosfactura);
        $("#uuid-" + tag).val(uuid);
        $("#cambiocfdi-" + tag).val(tcambio);
        loadMonedaCFDI(tag, idmoneda);
        $("#metcfdi-" + tag).val(idmetodo);
        $("#parcialidad-" + tag).val(noparcialidad_tmp);
        $("#total-" + tag).val(totalfactura);
        $("#anterior-" + tag).val(montoant_tmp);
        $("#monto-" + tag).val(montoant_tmp);
        calcularRestante(tag);
    }

    function calcularRestante(tag = "") {
        if (tag == "") {
            tag = $("#tabs").find('.sub-tab-active').attr('data-tab');
        }
        var monto = $("#monto-" + tag).val();
        var total = $("#anterior-" + tag).val();
        var restante = parseFloat(total) - parseFloat(monto);
        $("#restante-" + tag).val(restante);
    }

    function agregarCFDI() {
        var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
        var idmoneda = $("#moneda-" + tag).val();
        var tcambio = $("#cambio-" + tag).val();
        var idfactura = $("#id-factura-" + tag).val() || '0';
        var folio = $("#factura-" + tag).val();
        var uuid = $("#uuid-" + tag).val();
        var type = $("#type-" + tag).val();
        var tcamcfdi = $("#cambiocfdi-" + tag).val();
        var cmetodo = $("#metcfdi-" + tag).val();
        var idmonedacdfi = $("#monedarel-" + tag).val();
        var factura = $("#factura-" + tag).val();
        var parcialidad = $("#parcialidad-" + tag).val();
        var totalfactura = $("#total-" + tag).val();
        var montoanterior = $("#anterior-" + tag).val();
        var montopago = $("#monto-" + tag).val();
        var montorestante = $("#restante-" + tag).val();

        if (isnEmpty(idmoneda, "moneda-" + tag) && isnEmpty(factura, "factura-" + tag) && isnEmpty(type, "type-" + tag) && isnEmpty(idmonedacdfi, "monedarel-" + tag) && isnEmpty(parcialidad, "parcialidad-" + tag) && isnEmpty(totalfactura, "total-" + tag) && isnZero(montoanterior, "anterior-" + tag) && isPositive(montopago, "monto-" + tag) && isnEmpty(montorestante, "restante-" + tag)) {
            $.ajax({
                url: "com.sine.enlace/enlacepago.php",
                type: "POST",
                data: { transaccion: "agregarcfdi", tag: tag, idmoneda: idmoneda, tcambio: tcambio, type: type, idfactura: idfactura, folio: folio, uuid: uuid, tcamcfdi: tcamcfdi, idmonedacdfi: idmonedacdfi, cmetodo: cmetodo, factura: factura, parcialidad: parcialidad, totalfactura: totalfactura, montoanterior: montoanterior, montopago: montopago, montorestante: montorestante },
                success: function (datos) {
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 1000);
                    if (bandera == '0') {
                        alertify.error(res);
                    } else {
                        $("#id-factura-" + tag).val('');
                        $("#factura-" + tag).val('');
                        $("#uuid-" + tag).val('');
                        $("#type-" + tag).val('');
                        $("#cambiocfdi-" + tag).val('');
                        $("#metcfdi-" + tag).val('');
                        $("#monedarel-" + tag).val('');
                        $("#factura-" + tag).val('');
                        $("#parcialidad-" + tag).val('');
                        $("#total-" + tag).val('');
                        $("#anterior-" + tag).val('');
                        $("#monto-" + tag).val('');
                        $("#restante-" + tag).val('');
                        loadTablaCFDI();
                    }
                    cargandoHide();
                }
            });
        }
    }

}

function aucompletarFactura() {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var idcliente = $("#id-cliente").val() || '0';
    $('#factura-' + tag).autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=facturas&&iddatos=" + idcliente,
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var type = ui.item.type;
            loadFactura(id, type, tag);
        }
    });
}

function loadFactura(idfactura, type, tag) {
    var idpago = 0;
    if ($("#idpagoactualizar").val()) {
        idpago = $("#idpagoactualizar").val();
    }
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "loadfactura", idfactura: idfactura, type: type, idpago: idpago},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#type-" + tag).val(type);
                setvaloresFactura(datos, tag);
            }
            cargandoHide();
        }
    });
}

function setvaloresFactura(datos, tag) {
    var [iddatosfactura, uuid, tcambio, idmoneda, idmetodo, totalfactura, noparcialidad_tmp, montoant_tmp] = datos.split("</tr>").slice(0, 8);
    $("#id-factura-" + tag).val(iddatosfactura);
    $("#uuid-" + tag).val(uuid);
    $("#cambiocfdi-" + tag).val(tcambio);
    loadMonedaCFDI(tag, idmoneda);
    $("#metcfdi-" + tag).val(idmetodo);
    $("#parcialidad-" + tag).val(noparcialidad_tmp);
    $("#total-" + tag).val(totalfactura);
    $("#anterior-" + tag).val(montoant_tmp);
    $("#monto-" + tag).val(montoant_tmp);
    calcularRestante(tag);
}

function loadMonedaCFDI(tag = "", idmoneda = "") {
    $.ajax({
        url: '../../CATSAT/CATSAT/com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        data: {transaccion: 'opcionesmoneda', idmoneda: idmoneda},
        success: function (datos) {
            alert(datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                $("#monedarel-" + tag).html(datos);
            }
        }
    });
}

function calcularRestante(tag = "") {
    tag = tag || $("#tabs .sub-tab-active").attr('data-tab');
    var restante = parseFloat($("#anterior-" + tag).val()) - parseFloat($("#monto-" + tag).val());
    restante = restante.toFixed(2);
    $("#restante-" + tag).val(restante);
}

function agregarCFDI() {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var idmoneda = $("#moneda-" + tag).val();
    var tcambio = $("#cambio-" + tag).val();
    var idfactura = $("#id-factura-" + tag).val() || '0';
    var folio = $("#factura-" + tag).val();
    var uuid = $("#uuid-" + tag).val();
    var type = $("#type-" + tag).val();
    var tcamcfdi = $("#cambiocfdi-" + tag).val();
    var cmetodo = $("#metcfdi-" + tag).val();
    var idmonedacdfi = $("#monedarel-" + tag).val();
    var factura = $("#factura-" + tag).val();
    var parcialidad = $("#parcialidad-" + tag).val();
    var totalfactura = $("#total-" + tag).val();
    var montoanterior = $("#anterior-" + tag).val();
    var montopago = $("#monto-" + tag).val();
    var montorestante = $("#restante-" + tag).val();

    if (isnEmpty(idmoneda, "moneda-" + tag) && isnEmpty(factura, "factura-" + tag) && isnEmpty(type, "type-" + tag) && isnEmpty(idmonedacdfi, "monedarel-" + tag) && isnEmpty(parcialidad, "parcialidad-" + tag) && isnEmpty(totalfactura, "total-" + tag) && isnZero(montoanterior, "anterior-" + tag) && isPositive(montopago, "monto-" + tag) && isnEmpty(montorestante, "restante-" + tag)) {
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "agregarcfdi", tag: tag, idmoneda: idmoneda, tcambio: tcambio, type: type, idfactura: idfactura, folio: folio, uuid: uuid, tcamcfdi: tcamcfdi, idmonedacdfi: idmonedacdfi, cmetodo: cmetodo, factura: factura, parcialidad: parcialidad, totalfactura: totalfactura, montoanterior: montoanterior, montopago: montopago, montorestante: montorestante},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#id-factura-" + tag).val('');
                    $("#factura-" + tag).val('');
                    $("#uuid-" + tag).val('');
                    $("#type-" + tag).val('');
                    $("#cambiocfdi-" + tag).val('');
                    $("#metcfdi-" + tag).val('');
                    $("#monedarel-" + tag).val('');
                    $("#factura-" + tag).val('');
                    $("#parcialidad-" + tag).val('');
                    $("#total-" + tag).val('');
                    $("#anterior-" + tag).val('');
                    $("#monto-" + tag).val('');
                    $("#restante-" + tag).val('');
                    loadTablaCFDI();
                }
                cargandoHide();
            }
        });
    }
}

