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
        data: { transaccion: 'nuevocomplemento', comp: comp },
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

function loadFormaPago(tag = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: '../../CATSAT/CATSAT/com.sine.enlace/enlaceFormaPago.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $(".cont-fpago-" + tag).html(res.datos);
            }
        }
    });
}

function loadMonedaComplemento(tag = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: '../../CATSAT/CATSAT/com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
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
            data: { transaccion: 'borrarcomplemento', tab: tab },
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
    }).set({ title: "Q-ik" });
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
        data: { transaccion: 'opcionesbeneficiario', iddatos: iddatos },
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
        data: { transaccion: "loadfactura", idfactura: idfactura, type: type, idpago: idpago },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                alert(type);
                $("#type-" + tag).val(type);
                setvaloresFactura(datos, tag);
            }
            cargandoHide();
        }
    });
}

function setvaloresFactura(datos, tag) {
    var [iddatosfactura, uuid, tcambio, idmoneda, idmetodo, totalfactura, noparcialidad_tmp, montoant_tmp] = datos.split("</tr>").slice(0, 8);
    alert(iddatosfactura);
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
        dataType: 'html',
        data: { transaccion: 'opcionesmoneda', idmoneda: idmoneda },
        success: function (datos) {
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
            data: { transaccion: "agregarcfdi", tag: tag, idmoneda: idmoneda, tcambio: tcambio, type: type, idfactura: idfactura, folio: folio, uuid: uuid, tcamcfdi: tcamcfdi, idmonedacdfi: idmonedacdfi, cmetodo: cmetodo, factura: factura, parcialidad: parcialidad, totalfactura: totalfactura, montoanterior: montoanterior, montopago: montopago, montorestante: montorestante },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    loadTablaCFDI(uuid);
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
                }
                cargandoHide();
            }
        });
    }
}

function eliminarcfdi(idtemp) {
    alertify.confirm("¿Estás seguro que deseas eliminar este CFDI?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "eliminar", idtemp: idtemp},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    loadTablaCFDI();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

function insertarPago() {
    var folio = $("#folio-pago").val();
    var datosfiscales = $("#datos-facturacion").val();
    var idcliente = $("#id-cliente").val() || '0';
    var cliente = $("#nombre-cliente").val();
    var rfccliente = $("#rfc-cliente").val();
    var razoncliente = $("#razon-cliente").val();
    var regfiscalcliente = $("#regfiscal-cliente").val();
    var codpostal = $("#cp-cliente").val();
    var objimpuesto = $('#obj-impuesto').val();
    var chfirma = 0;
    if ($("#chfirma").prop('checked')) {
        chfirma = 1;
    }
    if (isnEmpty(folio, "folio-pago") && isnEmpty(datosfiscales, "datos-facturacion") && isnEmpty(rfccliente, "rfc-cliente") && isnEmpty(razoncliente, "razon-cliente") && isnEmpty(regfiscalcliente, "regfiscal-cliente") && isnEmpty(codpostal, "cp-cliente") && isnEmpty(objimpuesto, "obj-impuesto")) {
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "insertarpago", folio: folio, idcliente: idcliente, cliente: cliente, rfccliente: rfccliente, razoncliente: razoncliente, regfiscalcliente: regfiscalcliente, codpostal: codpostal, datosfiscales: datosfiscales, chfirma: chfirma, objimpuesto: objimpuesto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var array = datos.split("<cut>");
                    var tag = array[1];
                    insertarComplemento(tag);
                    loadView('listapago');
                }
            }
        });
    }
}

function insertarComplemento(tag) {
    var input = document.getElementsByName('tab-complemento');
    for (var i = 0; i < input.length; i++) {
        var a = input[i];
        var tagcomp = $(a).attr('data-tab');
        var orden = $(a).attr('data-ord');
        var idformapago = $("#forma-" + tagcomp).val();
        var moneda = $("#moneda-" + tagcomp).val();
        var tcambio = $("#cambio-" + tagcomp).val();
        var fechapago = $("#fecha-" + tagcomp).val();
        var horapago = $("#hora-" + tagcomp).val();
        var cuenta = $("#cuenta-" + tagcomp).val() || '0';
        var beneficiario = $("#benef-" + tagcomp).val() || '0';
        var numtransaccion = $("#transaccion-" + tagcomp).val();

        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "insertarcomplementos", tag: tag, tagcomp: tagcomp, orden:orden, idformapago: idformapago, moneda: moneda, tcambio: tcambio, fechapago: fechapago, horapago: horapago, cuenta: cuenta, beneficiario: beneficiario, numtransaccion: numtransaccion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(datos);
                }
            }
        });
    }
}

function xml(idpago) {
    alertify.confirm("¿Estás seguro que deseas timbrar este pago?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "xml", idpago: idpago},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(res);
                    loadView('listapago');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

function editarPago(idpago) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "editarpago", idpago: idpago},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                cargandoHide();
                alertify.error(res);
            } else {
                loadView('pago');
                window.setTimeout("setValoresEditarPago('" + datos + "')", 700);
            }
        }
    });
}

function setValoresEditarPago(datos) {
    changeText("#contenedor-titulo-form-pago", "Editar Pago");
    changeText("#btn-form-pago", "Guardar cambios <span class='fas fa-save'></span>");

    var array = datos.split("</tr>");
    var [idpago, serie, letra, folio, fechacreacion, idemisor, nombreemisor, rfcemisor, razonemisor, clvregemisor, regemisor, codpemisor, idcliente, nmcliente, rfcreceptor, rzreceptor, rfiscalreceptor, codpreceptor, totalpagado, chfirmar, uuid, tag, objimpuesto] = array;

    if (uuid != "") {
        $("#rfc-emisor").val(rfcemisor);
        $("#razon-emisor").val(razonemisor);
        $("#regimen-emisor").val(clvregemisor + "-" + regemisor);
        $("#cp-emisor").val(codpemisor);
        $("#not-timbre").html("<label class='mark-required text-right'>*</label> <label class='label-required text-right'> Esta pago ya ha sido timbrado, por lo que solo puedes modificar la firma del contribuyente.</label>");
        $("#folio-pago, #nombre-cliente, #rfc-cliente, #razon-cliente, #regfiscal-cliente, #cp-cliente, #datos-facturacion").prop("disabled", true);
    } else {
        loadFolioPago(idemisor);
        $("#folio-factura").removeAttr('disabled');
    }

    var d = fechacreacion.split("-");

    loadOpcionesFolios('0', serie, letra + folio);
    $("#fecha-creacion").val(d[2] + "/" + d[1] + "/" + d[0]);
    $("#option-default-datos").val(idemisor).text(nombreemisor);
    $("#id-cliente, #nombre-cliente, #rfc-cliente, #razon-cliente, #regfiscal-cliente, #cp-cliente").val(function(index) {
        return [idcliente, nmcliente, rfcreceptor, rzreceptor, rfiscalreceptor, codpreceptor][index];
    });
    $('#obj-impuesto').val(objimpuesto);
    if (chfirmar == '1') {
        $("#chfirma").prop('checked', true);
    }

    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "complementos", tag: tag, idemisor: idemisor, uuid:uuid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<comp>");
                for (var i = 0; i < array.length; i++) {
                    var comps = array[i].split("<cut>");
                    $("#tabs").append(comps[0]);
                    $("#complementos").append(comps[1]);
                    var tag1 = comps[2];
                    var orden = comps[3];
                    if (orden) {
                        comp = (parseFloat(orden) + 1);
                    }

                    $(".sub-div").hide();
                    $(".tab-pago").removeClass("sub-tab-active");

                    var first = $("#tabs").find('.tab-pago:first').attr("data-tab");
                    if (first) {
                        $("#complemento-" + first).show();
                        $("#tab-" + first).addClass("sub-tab-active");
                    }
                    tablaRowCFDI(tag1, uuid);
                }
            }
        }
    });

    $("#form-pago").append("<input id='idpagoactualizar' name='idpagoactualizar' type='hidden' value='" + idpago + "'/>");
    $("#form-pago").append("<input id='tagpago' name='tagpago' type='hidden' value='" + tag + "'/>");
    $("#btn-form-pago").attr("onclick", "actualizarPago(" + idpago + ");");
    cargandoHide();
}

function eliminarPago(idpago) {
    alertify.confirm("¿Estás seguro que deseas eliminar este pago?", function () {
        cargandoHide();
        cargandoShow();
        var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "eliminarpago", idpago: idpago},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(datos);
                    loadView('listapago');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

