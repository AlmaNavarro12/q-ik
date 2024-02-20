//------------------LISTA DE PAGOS
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

//------------------FORMULARIO
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

//------------------COMPLEMENTOS
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

function cerrarComplemento(tab = "") {
    alertify.confirm("¿Estás seguro que deseas eliminar este complemento? (Toda la información ingresada se perderá)", function () {
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
                    alertify.success('Pago registrado.');
                }
            }
        });
    }
}
//-------------------TMPPAGO Y PAGO
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
    changeText("#btn-form-pago", "Guardar cambios <span class='glyphicon glyphicon-floppy-disk'></span>");

    var array = datos.split("</tr>");
    var idpago = array[0];
    var serie = array[1];
    var letra = array[2];
    var folio = array[3];
    var fechacreacion = array[4];
    var idemisor = array[5];
    var nombreemisor = array[6];
    var rfcemisor = array[7];
    var razonemisor = array[8];
    var clvregemisor = array[9];
    var regemisor = array[10];
    var codpemisor = array[11];
    var idcliente = array[12];
    var nmcliente = array[13];
    var rfcreceptor = array[14];
    var rzreceptor = array[15];
    var rfiscalreceptor = array[16];
    var codpreceptor = array[17];
    var totalpagado = array[18];
    var chfirmar = array[19];
    var uuid = array[20];
    var tag = array[21];
    var objimpuesto = array[22];

    if (uuid != "") {
        $("#rfc-emisor").val(rfcemisor);
        $("#razon-emisor").val(razonemisor);
        $("#regimen-emisor").val(clvregemisor + "-" + regemisor);
        $("#cp-emisor").val(codpemisor);
        $("#not-timbre").html("<label class='mark-required text-right'>*</label> <label class='label-required text-right'> Esta pago ya ha sido timbrado, por lo que solo puedes modificar la firma del contribuyente.</label>");
        $("#folio-pago").attr("disabled", true);
        $("#nombre-cliente").attr("disabled", true);
        $("#rfc-cliente").attr("disabled", true);
        $("#razon-cliente").attr("disabled", true);
        $("#regfiscal-cliente").attr("disabled", true);
        $("#cp-cliente").attr("disabled", true);
        $("#datos-facturacion").attr("disabled", true);
    } else {
        loadFolioPago(idemisor);
        $("#folio-factura").removeAttr('disabled');
    }

    var d = fechacreacion.split("-");

    loadOpcionesFolios('0', serie, letra + folio);
    $("#fecha-creacion").val(d[2] + "/" + d[1] + "/" + d[0]);
    $("#option-default-datos").val(idemisor);
    $("#option-default-datos").text(nombreemisor);
    $("#id-cliente").val(idcliente);
    $("#nombre-cliente").val(nmcliente);
    $("#rfc-cliente").val(rfcreceptor);
    $("#razon-cliente").val(rzreceptor);
    $("#regfiscal-cliente").val(rfiscalreceptor);
    $("#cp-cliente").val(codpreceptor);
    $('#obj-impuesto').val(objimpuesto);
    if (chfirmar == '1') {
        $("#chfirma").attr('checked', true);
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
                for (i = 0; i < array.length; i++) {
                    var comps = array[i].split("<cut>");
                    $("#tabs").append(comps[0]);
                    $("#complementos").append(comps[1]);
                    var tag1 = comps[2];
                    var orden = comps[3];
                    if(orden){
                        comp = (parseFloat(orden)+1);
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

//-------------------FECHA
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
//-------------------CATALOGOS SAT
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
                $(".contenedor-moneda-" + tag).html(datos);
            }
        }
    });
}

//--------------------------------DATOS SELECT O AUNTOCOMPLETADS
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

//-----------------------------------------CANCELAR
function cancelarPago2() {
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "cancelar"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);

        }
    });
}

function cancelarPago() {
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "cancelar"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            loadView('listapago');
        }
    });
}

//-----------------------IMPRIMIR
function imprimirReporteFactura() {
    var fechainicio = $("#fecha-inicio").val();
    var fechafin = $("#fecha-fin").val();
    var idpermisionario = $("#id-permisionario").val();
    var estadofactura = $("#estado-factura").val();
    var orden = $("#orden-reporte").val();
    if (isnEmpty(fechainicio, "fecha-inicio") && isnEmpty(fechafin, "fecha-fin") && isInicioFecha(fechainicio, fechafin, "fecha-inicio")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.imprimir/imprimirreportefactura.php",
            type: "POST",
            data: {transaccion: "generarreporte", fechainicio: fechainicio, fechafin: fechafin, idpermisionario: idpermisionario, estadofactura: estadofactura, orden: orden},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                cargandoHide();
                window.open('reportefactura/reporte_' + fechainicio + '_' + fechafin + '.xlsx', '_blank');

            }
        });
    }
}

//----------------------------------------FACTURAS
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

function calcularRestante(tag = "") {
    if (tag == "") {
        tag = $("#tabs").find('.sub-tab-active').attr('data-tab');
    }
    var monto = $("#monto-" + tag).val();
    var total = $("#anterior-" + tag).val();
    var restante = parseFloat(total) - parseFloat(monto);
    $("#restante-" + tag).val(restante);
}

//----------------------------------------TIMBRES
function getTimbres() {
    $.ajax({
        url: 'com.sine.enlace/enlacefactura.php',
        type: 'POST',
        data: {transaccion: 'gettimbres'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            var text = "text-info";
            if (datos <= '10') {
                text = "text-danger";
            }
            $("#titulo-timbres").addClass(text);
            $("#cant-timbres").addClass(text);
            $("#cant-timbres").html(datos);
        }
    });
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


//-----------------------------CFDIS
function loadTablaCFDI(uuid = "") {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var idmoneda = $("#moneda-" + tag).val();
    var tcambio = $("#cambio-" + tag).val();
    if (isnEmpty(idmoneda, "id-moneda-pago")) {
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "loadtabla", tag: tag, idmoneda: idmoneda, tcambio: tcambio, uuid: uuid},
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

function resetPagos(idfactura, idcliente, monto) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "resetpagos"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                cambiarEstado(idfactura, idcliente, monto);
            }
        }
    });
}

function loadPagos(idfactura) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "loadpagos", idfactura: idfactura},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var p2 = array[1];
                var p3 = array[2];
                if (!p3) {
                    parcialidad = 0;
                } else {
                    parcialidad = p3;
                }
                $("#resultadospagos").html(p2);
            }
        }
    });
}

function cambiarEstado(idfactura, idcliente, monto) {
    cargandoHide();
    cargandoShow();
    loadOpcionesBancoCliente(idcliente);
    $("#resultadospagos").html('');
    loadPagos(idfactura);
    $("#regpago").modal('show');
    $("#monto-pago").val(monto);
    $("#idfactura").val(idfactura);
    $("#idcliente").val(idcliente);
    $("#totalfactura").val(monto);
    parcialidad = 0;
    cargandoHide();
}

function tablaMoneda() {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var idmoneda = $("#moneda-" + tag).val();
    var tcambio = $("#cambio-" + tag).val();
    var uuid = "";
    if (isnEmpty(idmoneda, "moneda-" + tag)) {
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "loadtabla", idmoneda: idmoneda, tcambio: tcambio, uuid: uuid},
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

function tablaPagos(idfactura) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "pagosfactura", idfactura: idfactura},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var p2 = array[1];
                var p3 = array[2];
                $("#pagostabla").html(p2);
            }
        }
    });
}

function imprimirRecibo(idpago) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.imprimir/imprimirrecibo.php",
        type: "POST",
        data: {transaccion: "pdf", idpago: idpago},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(0, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#pagosfactura").modal('hide');
                enviarRecibo(idpago);
            }
            cargandoHide();
        }
    });
}

function agregarEntrega() {
    var codprod = $("#codigo-prod").val();
    var claveprod = $("#clave-prod").val();
    var cantidad = $("#cantidad-prod").val();
    var unidad = $("#unidad").val();
    var claveuni = $("#clave-unidad").val();
    var descripcion = $("#descripcion-prod").val();
    var preciounitario = $("#precio-unitario").val();
    var totalunitario = $("#total-unitario").val();
    var descuento = $("#descuento-unitario").val();
    var importe = $("#importe-concepto").val();
    var chiva = 0;
    var chret = 0;
    if ($("#chiva").prop('checked')) {
        chiva = 1;
    }

    if ($("#chret").prop('checked')) {
        chret = 1;
    }
    if (isnEmpty(codprod, "codigo-prod") && isnEmpty(claveprod, "clave-prod") && isPositive(cantidad, "cantidad-prod") && isnEmpty(unidad, "unidad") && isnEmpty(claveuni, "clave-unidad") && isnEmpty(descripcion, "descripcion-prod") && isPositive(preciounitario, "precio-unitario") && isnEmpty(descuento, "descuento-unitario") && isPositive(importe, "importe-concepto")) {
        $.ajax({
            url: "com.sine.enlace/enlacefactura.php",
            type: "POST",
            data: {transaccion: "agregarProducto", codprod: codprod, claveprod: claveprod, cantidad: cantidad, unidad: unidad, claveuni: claveuni, descripcion: descripcion, preciounitario: preciounitario, totalunitario: totalunitario, descuento: descuento, importe: importe, chiva: chiva, chret: chret},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var array = datos.split("<corte>");
                    var p1 = array[0];
                    var p2 = array[1];

                    $("#body-lista-conceptos").html(p2);
                }
                cargandoHide();
            }
        });
    }
}

function loadDestino() {
    var idcarta = $("#id-carta-porte").val();
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "loaddestino", idcarta: idcarta},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                var nombre = array[0];
                var estado = array[2];
                var municipio = array[1];
                var calle = array[3];
                var numero = array[4];
                var colonia = array[5];
                var direccion = calle + ' #' + numero + ', col. ' + colonia + ', ' + municipio + ', ' + estado;
                $("#descripcion-prod").val("Servicio de transporte a " + nombre + " " + direccion);
            }
        }
    });
}

function loadDocumento() {
    $.ajax({
        url: 'com.sine.enlace/enlacepagopermi.php',
        type: 'POST',
        data: {transaccion: 'documento'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                $("#documento").val(datos);
            }
        }
    });
}

function setidFacturaCancelar(idfactura) {
    $("#idfacturacancelar").val(idfactura);
    $("#passcsd").val('');
}

function setCancelarPago(fid) {
    $("#motivo-cancelacion").val();
    $("#uuid-reemplazo").val();
    $("#div-reemplazo").hide('slow');
    $("#btn-cancelar").attr('onclick', 'cancelarTimbrePago(' + fid + ')')
}

function checkCancelarPago() {
    var motivo = $("#motivo-cancelacion").val();
    if (motivo === '01') {
        $("#div-reemplazo").show('slow');
    } else {
        $("#div-reemplazo").hide('slow');
    }
}

function cancelarTimbrePago(idpago) {
    var motivo = $("#motivo-cancelacion").val();
    var reemplazo = "0";
    if (motivo === '01') {
        reemplazo = $("#uuid-reemplazo").val();
    }
    if (isnEmpty(motivo, "motivo-cancelacion") && isnEmpty(reemplazo, "uuid-reemplazo")) {
        alertify.confirm("Esta seguro que desea cancelar este pago?", function () {
            cargandoHide();
            cargandoShow();
            $.ajax({
                url: "com.sine.enlace/enlacepago.php",
                type: "POST",
                data: {transaccion: "cancelartimbre", idpago: idpago, motivo: motivo, reemplazo: reemplazo},
                success: function (datos) {
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 1000);
                    if (bandera == '0') {
                        alertify.error(res);
                        cargandoHide();
                    } else {
                        cargandoHide();
                        $("#modalcancelar").modal('hide');
                        alertify.success(datos);
                        buscarPago();
                    }
                }
            });
        }).set({title: "Q-ik"});
    }
}

function facturaRastreo(idcarta, folio) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "facturarastreo", idcarta: idcarta, folio: folio},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {

            }
        }
    });
}

function tablaRowCFDI(tag, uuid = "") {
    var idmoneda = $("#moneda-" + tag).val();
    var tcambio = $("#cambio-" + tag).val();
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "loadtabla", tag: tag, idmoneda: idmoneda, tcambio: tcambio, uuid: uuid},
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

function setvaloresCuentas(idordenante, idbeneficiario) {
    if (idordenante != '0') {
        $("#id-bancocuenta").val(idordenante);
    }
    if (idbeneficiario != '0') {
        $("#id-bancobeneficiario").val(idbeneficiario);
    }
}

function actualizarPago(idpago) {
    var tag = $("#tagpago").val();
    var foliopago = $("#folio-pago").val();
    var pago_idfiscales = $("#datos-facturacion").val();
    var idcliente = $("#id-cliente").val() || '0';
    var cliente = $("#nombre-cliente").val();
    var rfccliente = $("#rfc-cliente").val();
    var razoncliente = $("#razon-cliente").val();
    var regfiscalcliente = $("#regfiscal-cliente").val();
    var codpostal = $("#cp-cliente").val();
    var objimpuesto = $('#obj-impuesto').val()
    var chfirmar = 0;
    if ($("#chfirma").prop('checked')) {
        chfirmar = 1;
    }

    if (isnEmpty(foliopago, "folio-pago") && isnEmpty(pago_idfiscales, "datos-facturacion") && isnEmpty(rfccliente, "rfc-cliente") && isnEmpty(razoncliente, "razon-cliente") && isnEmpty(regfiscalcliente, "regfiscal-cliente") && isnEmpty(codpostal, "cp-cliente") && isnEmpty(objimpuesto, "obj-impuesto")) {
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "actualizarpago", tag: tag, idpago: idpago, foliopago: foliopago, idcliente: idcliente, cliente: cliente, rfccliente: rfccliente, razoncliente: razoncliente, regfiscalcliente: regfiscalcliente, codpostal: codpostal, pago_idfiscales: pago_idfiscales, chfirmar: chfirmar, objimpuesto: objimpuesto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {

                    alertify.error(res);
                } else {
                    actualizarComplementos(tag);
                    loadView('listapago');
                    alertify.success(datos);
                }
            }
        });

    }
}

function actualizarComplementos(tag) {
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
            data: {transaccion: "actualizarcomplementos", tag: tag, tagcomp: tagcomp, orden:orden, idformapago: idformapago, moneda: moneda, tcambio: tcambio, fechapago: fechapago, horapago: horapago, cuenta: cuenta, beneficiario: beneficiario, numtransaccion: numtransaccion},
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

function imprimirFactura(idfactura) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "getfacturapdf", idfactura: idfactura},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                sendPDFDatos(datos);
            }

        }
    });
}

function getCorreos(idfactura) {
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "getcorreos", idfactura: idfactura},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var correo1 = array[0];
                var correo2 = array[1];
                var correo3 = array[2];
                var correo4 = array[3];
                var correo5 = array[4];
                var correo6 = array[5];
                $("#correo1").val(correo1);
                $("#correo2").val(correo2);
                $("#correo3").val(correo3);
                $("#correo4").val(correo4);
                $("#correo5").val(correo5);
                $("#correo6").val(correo6);
            }
        }
    });
}

function showCorreos(idfactura) {
    cargandoHide();
    cargandoShow();
    $("#idreciboenvio").val(idfactura);
    $("#correo1").val('');
    $("#correo2").val('');
    $("#correo3").val('');
    $("#correo4").val('');
    $("#correo5").val('');
    $("#correo6").val('');
    getCorreos(idfactura);
    $("#chcorreo1").prop('checked', false);
    $("#chcorreo2").prop('checked', true);
    $("#chcorreo3").prop('checked', false);
    $("#chcorreo4").prop('checked', false);
    $("#chcorreo5").prop('checked', false);
    $("#chcorreo6").prop('checked', false);
    cargandoHide();
}

function imprimirpago(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimirpago.php?pago=' + id, 'Pago', '', '1024', '768', 'true');
    cargandoHide();
}

function xmlPago(idpago) {
    alertify.confirm("Esta seguro que desea timbrar los pagos de esta factura?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "xmlpago", idpago: idpago},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#pagosfactura").modal('hide');
                    window.setTimeout("loadView('listafactura');", 300);

                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

function enviarPago() {
    var idpago = $("#idreciboenvio").val();
    var mailalt1 = "ejemplo@ejemplo.com";
    var mailalt2 = "ejemplo@ejemplo.com";
    var mailalt3 = "ejemplo@ejemplo.com";
    var mailalt4 = "ejemplo@ejemplo.com";
    var mailalt5 = "ejemplo@ejemplo.com";
    var mailalt6 = "ejemplo@ejemplo.com";
    var chcorreo1 = 0;
    var chcorreo2 = 0;
    var chcorreo3 = 0;
    var chcorreo4 = 0;
    var chcorreo5 = 0;
    var chcorreo6 = 0;

    if ($("#chcorreo1").prop('checked')) {
        chcorreo1 = 1;
        mailalt1 = $("#correo1").val();
    }
    if ($("#chcorreo2").prop('checked')) {
        chcorreo2 = 1;
        mailalt2 = $("#correo2").val();
    }
    if ($("#chcorreo3").prop('checked')) {
        chcorreo3 = 1;
        mailalt3 = $("#correo3").val();
    }
    if ($("#chcorreo4").prop('checked')) {
        chcorreo4 = 1;
        mailalt4 = $("#correo4").val();
    }
    if ($("#chcorreo5").prop('checked')) {
        chcorreo5 = 1;
        mailalt5 = $("#correo5").val();
    }
    if ($("#chcorreo6").prop('checked')) {
        chcorreo6 = 1;
        mailalt6 = $("#correo6").val();
    }

    if (isEmailtoSend(mailalt1, "correo1") && isEmailtoSend(mailalt2, "correo2") && isEmailtoSend(mailalt3, "correo3") && isEmailtoSend(mailalt4, "correo4") && isEmailtoSend(mailalt5, "correo5") && isEmailtoSend(mailalt6, "correo6") && isCheckedMailSend(chcorreo1, chcorreo2, chcorreo3, chcorreo4, chcorreo5, chcorreo6)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.imprimir/imprimirpago.php",
            type: "POST",
            data: {transaccion: "pdf", idpago: idpago, ch1: chcorreo1, ch2: chcorreo2, ch3: chcorreo3, ch4: chcorreo4, ch5: chcorreo5, ch6: chcorreo6, mailalt1: mailalt1, mailalt2: mailalt2, mailalt3: mailalt3, mailalt4: mailalt4, mailalt5: mailalt5, mailalt6: mailalt6},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(res);
                    $("#enviarrecibo").modal('hide');
                }
                cargandoHide();
            }
        });
    }
}

function checkStatusCancelacion(fid) {
    $("#cod-status").html('');
    $("#estado-cfdi").html('');
    $("#cfdi-cancelable").html('');
    $("#estado-cancelacion").html('');
    $("#div-reset").html('');
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "statuscfdi", fid: fid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                var codstatus = array[0];
                var estado = array[1];
                var cancelable = array[2];
                var stcancelacion = array[3];
                var reset = array[4];

                $("#cod-status").html(codstatus);
                $("#estado-cfdi").html(estado);
                $("#cfdi-cancelable").html(cancelable);
                $("#estado-cancelacion").html(stcancelacion);
                $("#div-reset").html(reset);
            }
            cargandoHide();
        }
    });
}

function resetCfdiPago(idpago) {
    alertify.confirm("Este proceso devolvera borrara el acuse de cancelacion generado, ¿Desea continuar?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacepago.php",
            type: "POST",
            data: {transaccion: "editarestado", idpago: idpago},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Pago Restaurado');
                    $("#modal-stcfdi").modal('hide');
                    loadListaPago();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}