function filtrarCotizacion(pag = "") {
    cargandoHide();
    cargandoShow();
    var REF = $("#buscar-cotizacion").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "filtrarcotizacion", REF: REF, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-cotizacion").html(datos);
                cargandoHide();
            }
        }
    });
}

function filtrarProducto(pag = "") {
    cargandoHide();
    cargandoShow(); 
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: $("#buscar-producto").val(), pag: pag, numreg: $("#num-reg").val()},
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
                $("#body-lista-productos-cotizacion").html(table);
                $("#pagination").html(pag);
            }
            cargandoHide();
        }
    });
}

function autocompletarEmisor(iddatos = "") {
    cargandoHide();
    cargandoShow();
    if (iddatos == "") {
        iddatos = $("#datos-facturacion").val();
    }
    $.ajax({
        url: 'com.sine.enlace/enlacecotizacion.php',
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

function aucompletarCliente() {
    if ($("#nombre-cliente").val() == "") {
        $("#id-cliente").val('0');
    }
    $('#nombre-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var mailinfo = ui.item.mailinfo;
            var mailfacturas = ui.item.mailfacturas;
            var mailgerencia = ui.item.mailgerencia;

            $("#id-cliente").val(id);
            $("#email-cliente1").val(mailfacturas);
            $("#email-cliente2").val(mailinfo);
            $("#email-cliente3").val(mailgerencia);
        }
    });
}

function aucompletarCorreo() {
    $('.correo-cotizacion').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=emailcliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function calcularImporteObra() {
    var cantidad = $("#cant-obra").val() || '0';
    var precio = $("#precio-venta").val() || '0';

    var importe = parseFloat(cantidad) * parseFloat(precio);
    $("#importe-obra").val(Math.floor(importe * 100) / 100);
    calcularDescuentoObra();
}

function calcularDescuentoObra() {
    var pordesc = $("#por-descuento").val() || '0';
    var importe = $("#importe-obra").val() || '0';

    var descuento = parseFloat(importe) * (parseFloat(pordesc) / 100);
    var subtotal = (parseFloat(importe) - parseFloat(descuento));
    var traslados = 0;
    var retencion = 0;

    $.each($("input[name='chtrasladoobra']:checked"), function () {
        var tasa = $(this).val();
        traslados += parseFloat(subtotal) * parseFloat(tasa);
    });

    $.each($("input[name='chretencionobra']:checked"), function () {
        var tasa = $(this).val();
        retencion += parseFloat(subtotal) * parseFloat(tasa);
    });

    var total = (parseFloat(subtotal) + parseFloat(traslados)) - parseFloat(retencion);
    $("#importe-descuento").val(Math.floor(descuento * 100) / 100);
    $("#total-obra").val(Math.floor(total * 100) / 100);
}

function loadDocumento() {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "documento"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#realizo").val(datos);
            }
        }
    });
}

function limpiarCampos(){
    $("#imagenproducto").hide();
    $("#muestraimagenproducto").val("");
    $("#filename").val("");
    $("#imgactualizar").val("");
}

function calcularImpuestosTotal() {
    var id = "";
    var div = [];
    var porcentaje = 0.0;
    var tipoImp = 0;
    var costo = $("#pventa").val();
    var total = $("#pventa").val();
    var impuesto = 0;

    $("input[name=taxes]:checked").each(function () {
        id = $(this).attr("id");
        div = $(this).val().split("-");
        porcentaje = parseFloat(div[0]);
        tipoImp = parseFloat(div[1]); //1 traslado //2retencion

        impuesto = costo * porcentaje;
        impuesto = impuesto.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];

        if (tipoImp == 1) {
            total = parseFloat(total) + parseFloat(impuesto);
        }
        else if (tipoImp == 2) {
            total = parseFloat(total) - parseFloat(impuesto);
        }
        $('#p' + id).val(impuesto);
    });
    var preciopub = myRound(total, 2);
    $("#ptotiva").val(preciopub);
}

function calcularImpuestosTotalReverse() {
    var id = "";
    var div = [];
    var porcentaje = 0.0;
    var tipoImp = 0;
    var costo = $("#ptotiva").val();
    var total = $("#ptotiva").val();
    var impuesto = 0;

    $("input[name=taxes]:checked").each(function () {
        id = $(this).attr("id");
        div = $(this).val().split("-");
        porcentaje = parseFloat(div[0]);
        tipoImp = parseFloat(div[1]); //1 traslado //2retencion

        if (tipoImp == 1) {
            costo = Math.round((total / (porcentaje + 1)) * 100) / 100;
            impuesto = costo * porcentaje;
            impuesto = impuesto.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
            total = parseFloat(total) - parseFloat(impuesto);
        }
        else if (tipoImp == 2) {
            porcentaje = porcentaje * 100;
            var restante = 100 - porcentaje;
            impuesto = Math.round(((costo * porcentaje) / restante) * 100) / 100;
            total = Math.round((parseFloat(total) + parseFloat(impuesto)) * 100) / 100;
        }
        $('#p' + id).val(impuesto);
    });
    $("#pventa").val(myRound(total, 2));
}

function myRound(num, dec) {
    var exp = Math.pow(10, dec || 2); 
    return parseInt(num * exp, 10) / exp;
}

function setCamposProducto() {
    $("#codigo-producto").val('');
    $("#producto").val('');
    $("#tipo").val('');
    $("#inventario").attr('hidden', true);
    $("#clave-unidad-prod").val('');
    $("#descripcion").val('');
    $("#pcompra").val(0);
    $("#porganancia").val(0);
    $("#ganancia").val(0);
    $("#pventa").val(0);
    $("#clave-fiscal-prod").val('');
    $('#id-proveedor').val('');
    $("#imagen").val('');
    $('#muestraimagen').html("");
    $("#btn-form-producto-factura").attr("onclick", "insertarProductoCot();");
}

function insertarProductoCot() {
    var codproducto = $("#codigo-producto").val();
    var producto = $("#producto").val();
    var descripcion = $("#descripcion").val();
    var clavefiscal = $("#clave-fiscal-prod").val();
    var tipo = $("#tipo").val();
    var unidad = $("#clave-unidad-prod").val();
    var pcompra = $("#pcompra").val();
    var porcentaje = $("#porganancia").val();
    var ganancia = $("#ganancia").val();
    var pventa = $("#pventa").val();
    var idproveedor = $("#id-proveedor").val() || '0';
    var imagen = $('#filename').val();
    var chinventario = 0;
    var cantidad = $("#cantidad").val();
    if ($("#chinventario").prop('checked')) {
        chinventario = 1;
    }

    var imp_apl = "";
    $("input[name=taxes]:checked").each(function () {
        imp_apl += $(this).val() + "<tr>";
    });

    if (isnEmpty(codproducto, "codigo-producto") && isnEmpty(producto, "producto") && isList(clavefiscal, "clave-fiscal") && isnEmpty(tipo, "tipo") && isList(unidad, "clave-unidad") && isPositive(porcentaje, "porganancia") && isPositive(ganancia, "ganancia") && isPositive(pventa, "pventa")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
            type: "POST",
            data: { transaccion: "insertarproducto", codproducto: codproducto, producto: producto, tipo: tipo, unidad: unidad, descripcion: descripcion, pcompra: pcompra, porcentaje: porcentaje, ganancia: ganancia, pventa: pventa, clavefiscal: clavefiscal, idproveedor: idproveedor, imagen: imagen, chinventario: chinventario, cantidad: cantidad, imp_apl: imp_apl, insert: 'c' },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductosCot();
                    $("#nuevo-producto").modal('hide');
                }
                cargandoHide();
            }
        });
    }
}

function agregarManoObra() {
    var idproducto = '0';
    var descripcion = $("#descripcion-mano").val();
    var clvfiscal = $("#clave-fiscal").val();
    var clvunidad = $("#clave-unidad").val();
    var cantidad = $("#cant-obra").val();
    var pventa = $("#precio-venta").val();
    var importe = $("#importe-obra").val();
    var descuento = $("#por-descuento").val();
    var impdescuento = $("#importe-descuento").val();
    var total = $("#total-obra").val();

    var traslados = [];
    $.each($("input[name='chtrasladoobra']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });

    var retenciones = [];
    $.each($("input[name='chretencionobra']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");
    var idretencion = retenciones.join("<impuesto>");

    if (isnEmpty(descripcion, "descripcion-mano") && isnEmpty(clvfiscal, "clave-fiscal") && isnEmpty(clvunidad, "clave-unidad") && isnZero(cantidad, "cant-obra") && isnZero(pventa, "precio-venta")) {
        $.ajax({
            url: "com.sine.enlace/enlacecotizacion.php",
            type: "POST",
            data: {transaccion: "agregarmanoobra", idproducto: idproducto, clvfiscal: clvfiscal, clvunidad: clvunidad, descripcion: descripcion, cantidad: cantidad, pventa: pventa, importe: importe, descuento: descuento, impdescuento: impdescuento, total: total, idtraslados: idtraslados, idretencion: idretencion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);

                } else {
                    tablaProductosCot();
                }
                cargandoHide();
            }
        });
    }
}

function tablaProductosCot() {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "tablatmp"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#resultados").html(datos);
            }
        }
    });
}

function setIDTMP(id, observaciones) {
    var txtbd = observaciones.replace(new RegExp("<ent>", 'g'), '\n');
    $("#idtmp").val(id);
    $("#observaciones-producto").val(txtbd);
}

function agregarObservaciones() {
    var idtmp = $("#idtmp").val();
    var observaciones = $("#observaciones-producto").val();
    var txtbd = observaciones.replace(new RegExp("\n", 'g'), '<ent>');
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "agregarobservaciones", idtmp: idtmp, observaciones: txtbd},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#modal-observaciones").modal('hide');
                tablaProductosCot();
            }
            cargandoHide();
        }
    });
}

function cancelarCotizacion() {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "cancelar"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                loadView('listacotizacion');
            }
        }
    });
}

function incrementarCantidad(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "incrementar", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductosCot();
                cargandoHide();
            }
        }
    });
}

function reducirCantidad(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "reducir", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductosCot();
                cargandoHide();
            }
        }
    });
}

function setCantidad(idtmp, cant) {
    $("#idcant").val(idtmp);
    $("#cantidad-producto").val(cant);
}

function modificarCantidad() {
    var idtmp = $("#idcant").val();
    var cant = $("#cantidad-producto").val();
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "modificartmp", idtmp: idtmp, cant: cant},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#modal-cantidad").modal('hide');
                tablaProductosCot();
                cargandoHide();
            }
        }
    });
}


function editarConcepto(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "editarconcepto", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                setValoresEditarConcepto(datos);
            }
            cargandoHide();
        }
    });
}

function setValoresEditarConcepto(datos) {
    var array = datos.split("</tr>");
    $("#editar-idtmp").val(array[0]);
    $("#editar-descripcion").val(array[1]);
    $("#editar-cfiscal").val(array[9]);
    $("#editar-cunidad").val(array[10]);
    $("#editar-cantidad").val(array[2]);
    $("#editar-precio").val(array[3]);
    $("#editar-totuni").val(array[4]);
    $("#editar-descuento").val(array[5]);
    $("#editar-impdesc").val(array[6]);
    $("#editar-traslados").html(array[11]);
    $("#editar-retencion").html(array[12]);
    $("#editar-total").val(Math.floor(array[7] * 100) / 100);
    $("#editar-observaciones").val(array[8]);
}

function actualizarConceptoCotizacion() {
    var idtmp = $("#editar-idtmp").val();
    var descripcion = $("#editar-descripcion").val();
    var clvfiscal = $("#editar-cfiscal").val();
    var clvunidad = $("#editar-cunidad").val();
    var cantidad = $("#editar-cantidad").val();
    var pventa = $("#editar-precio").val();
    var importe = $("#editar-totuni").val();
    var descuento = $("#editar-descuento").val();
    var impdescuento = $("#editar-impdesc").val();
    var total = $("#editar-total").val();
    var observaciones = $("#editar-observaciones").val();

    var traslados = [];
    $.each($("input[name='chtrasedit']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });

    var retenciones = [];
    $.each($("input[name='chretedit']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");
    var idretencion = retenciones.join("<impuesto>");

    if (isnEmpty(idtmp, "editar-idtmp") && isnEmpty(descripcion, "editar-descripcion") && isnEmpty(clvfiscal, "editar-cfiscal") && isnEmpty(clvunidad, "editar-cunidad") && isPositive(cantidad, "editar-cantidad") && isPositive(pventa, "editar-precio") && isPositive(descuento, "editar-descuento")) {
        $.ajax({
            url: "com.sine.enlace/enlacecotizacion.php",
            type: "POST",
            data: { transaccion: "actualizarconcepto", idtmp: idtmp, descripcion: descripcion, clvfiscal: clvfiscal, clvunidad: clvunidad, cantidad: cantidad, pventa: pventa, importe: importe, descuento: descuento, impdescuento: impdescuento, total: total, observaciones: observaciones, idtraslados: idtraslados, idretencion: idretencion },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductosCot();
                    $("#editar-producto").modal('hide');
                }
            }
        });

    }
}

function eliminar(idtemp, cantidad, idproducto) {
    alertify.confirm("¿Estás seguro que deseas eliminar este producto?", function () {
        $.ajax({
            url: "com.sine.enlace/enlacecotizacion.php",
            type: "POST",
            data: {transaccion: "eliminar", idtemp: idtemp, cantidad: cantidad, idproducto: idproducto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    cargandoHide();
                } else {
                    tablaProductosCot();
                    cargandoHide();
                }
            }
        });
    }).set({title: "Q-ik"});
}

function agregarProducto(idproducto) {
    var descripcion = $("#prodserv" + idproducto).val();
    var cantidad = $("#cantidad_" + idproducto).val();
    var pventa = $("#pventa_" + idproducto).val();
    var importe = $("#importe_" + idproducto).val();
    var descuento = $("#pordescuento_" + idproducto).val();
    var impdescuento = $("#descuento_" + idproducto).val();
    var total = $("#total_" + idproducto).val();

    var traslados = [];
    $.each($("input[name='chtraslado" + idproducto + "']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });

    var retenciones = [];
    $.each($("input[name='chretencion" + idproducto + "']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");
    var idretencion = retenciones.join("<impuesto>");

    if (isNumber(cantidad, "cantidad_" + idproducto) && isNumber(pventa, "pventa_" + idproducto)) {
        $.ajax({
            url: "com.sine.enlace/enlacecotizacion.php",
            type: "POST",
            data: {transaccion: "agregarProducto", idproducto: idproducto, descripcion: descripcion, cantidad: cantidad, pventa: pventa, importe: importe, descuento: descuento, impdescuento: impdescuento, total: total, idtraslados: idtraslados, idretencion: idretencion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductosCot();
                }
                cargandoHide();
            }
        });
    }
}

function editarProductoCot(idprod, idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: {transaccion: "editarproducto", idproducto: idprod},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                setValoresEditarProducto(datos, idtmp);
            }
            cargandoHide();
        }
    });
}

function checkIVA(idtmp) {
    var traslados = [];
    $.each($("input[name='chtrastabla" + idtmp + "']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");

    var retenciones = [];
    $.each($("input[name='chrettabla" + idtmp + "']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idretenciones = retenciones.join("<impuesto>");
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "chivatmp", idtmp: idtmp, traslados: idtraslados, retenciones: idretenciones},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductosCot();
                cargandoHide();
            }
        }
    });
}

function setValoresEditarProducto(datos, idtmp) {
    $("#muestraimagen").html('');
    changeText("#titulo-alerta-editar-producto", "Editar producto en cotización");
    var array = datos.split("</tr>");
    var tipo = array[10];
    var imagen = array[14];
    var chinventario = array[15];
    var img = array[17];

    if (tipo == "1") {
        $("#inventarios").show('slow');
        changeText("#labelinventario", "¿Desactivar inventario?")
    } else if (tipo == "2") {
        $("#inventarios").hide('slow');
    }

    if (chinventario == '1') {
        $("#chinventario").prop('checked', true);
        $("#cantidad").removeAttr('disabled');
    }

    $("#codigo-producto").val(array[1]);
    $("#producto").val(array[2]);
    $("#tipo").val(tipo);
    $("#cantidad").val(array[16]);
    $("#clave-unidad-prod").val(array[3] + "-" + array[4]);
    $("#descripcion").val(array[5]);
    $("#pcompra").val(array[6]);
    $("#porganancia").val(array[7])
    $("#ganancia").val(array[8]);
    $("#pventa").val(array[9]);
    $("#clave-fiscal-prod").val(array[11] + "-" + array[12]);
    if (array[13] != '0') {
        loadOpcionesProveedor(array[13]);
    }
    getOptionsTaxes(array[18]);
    setTimeout(() => {
        calcularImpuestosTotal();
    }, 500);

    $("#filename").val(imagen);
    $("#imgactualizar").val(imagen);

    if (imagen !== '') {
        $("#imagenproducto").show('slow');
        $("#muestraimagenproducto").html(img);
        $("#filename").val(imagen);
    }
    $("#nameimg").val(imagen);
    $("#imgactualizar").val(img);

    $("#btn-form-producto-factura").attr("onclick", "actualizarProductoFactura(" + array[0] + "," + idtmp + ");");
}


function getOptionsTaxes(taxes = "") {
    cargandoShow();
    $.ajax({
        data: { transaccion: "taxesproductos", taxes: taxes },
        url: 'com.sine.enlace/enlaceproducto.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.bandera > 0) {
                $('#imp-apli').html(response.impuestos);
                $('#input-imp-apli').html(response.inputs);
            } else {
                alertify.error("No hay impuestos registrados.");
            }
        }
    });
    cargandoHide();
}

function actualizarProductoFactura(idproducto, idtmp) {
    var codproducto = $("#codigo-producto").val();
    var producto = $("#producto").val();
    var descripcion = $("#descripcion").val();
    var clavefiscal = $("#clave-fiscal").val();
    var tipo = $("#tipo").val();
    var unidad = $("#clave-unidad").val();
    var pcompra = $("#pcompra").val();
    var porcentaje = $("#porganancia").val();
    var ganancia = $("#ganancia").val();
    var pventa = $("#pventa").val();
    var idproveedor = $("#id-proveedor").val();
    var imagen = $('#filename').val();
    var imgactualizar = $("#imgactualizar").val();
    var chinventario = 0;
    var cantidad = $("#cantidad").val();
    if ($("#chinventario").prop('checked')) {
        chinventario = 1;
    }

    var imp_apl = "";
    $("input[name=taxes]:checked").each(function () {
        imp_apl += $(this).val() + "<tr>";
    });

    if (isnEmpty(codproducto, "codigo-producto") && isnEmpty(producto, "producto") && isList(clavefiscal, "clave-fiscal") && isnEmpty(tipo, "tipo") && isList(unidad, "clave-unidad") && isPositive(porcentaje, "porganancia") && isPositive(ganancia, "ganancia") && isPositive(pventa, "pventa")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
            type: "POST",
            data: { transaccion: "actualizarproducto", idproducto: idproducto, idtmp: idtmp, codproducto: codproducto, producto: producto, tipo: tipo, unidad: unidad, descripcion: descripcion, pcompra: pcompra, porcentaje: porcentaje, ganancia: ganancia, pventa: pventa, clavefiscal: clavefiscal, idproveedor: idproveedor, imagen: imagen, imgactualizar: imgactualizar, chinventario: chinventario, cantidad: cantidad,  imp_apl: imp_apl, insert: 'f' },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductosCot();
                    $("#nuevo-producto").modal('hide');
                }
                cargandoHide();
            }
        });
    }
}

function calcularImporteEditar() {
    var cantidad = $("#editar-cantidad").val() || '0';
    var precio = $("#editar-precio").val() || '0';

    var importe = parseFloat(cantidad) * parseFloat(precio);
    $("#editar-totuni").val(Math.floor(importe * 100) / 100);
    calcularDescuentoEditar();
}

function calcularDescuentoEditar() {
    var pordesc = $("#editar-descuento").val() || '0';
    var importe = $("#editar-totuni").val() || '0';

    var descuento = parseFloat(importe) * (parseFloat(pordesc) / 100);
    var subtotal = (parseFloat(importe) - parseFloat(descuento));
    var traslados = 0;
    var retencion = 0;

    $.each($("input[name='chtrasedit']:checked"), function () {
        var tasa = $(this).val();
        traslados += parseFloat(subtotal) * parseFloat(tasa);
    });

    $.each($("input[name='chretedit']:checked"), function () {
        var tasa = $(this).val();
        retencion += parseFloat(subtotal) * parseFloat(tasa);
    });
    var total = (parseFloat(subtotal) + parseFloat(traslados)) - parseFloat(retencion);
    $("#editar-impdesc").val(Math.floor(descuento * 100) / 100);
    $("#editar-total").val(Math.floor(total * 100) / 100);
}

function insertarCotizacion(idcotizacion = null) {
    var folio = $("#folio-cotizacion").val();
    var fecha_creacion = $("#fecha-creacion").val();
    var idcliente = $("#id-cliente").val();
    var nombrecliente = $("#nombre-cliente").val();
    var correocliente = $("#email-cliente1").val();
    var correocliente2 = $("#email-cliente2").val();
    var correocliente3 = $("#email-cliente3").val();
    var tipoComprobante = $("#tipo-comprobante").val() || '0';
    var idformapago = $("#id-forma-pago").val() || '0';
    var idmetodopago = $("#id-metodo-pago").val() || '0';
    var idmoneda = $("#id-moneda").val() || '0';
    var iduso = $("#id-uso").val() || '0';
    var datosfac = $("#datos-facturacion").val();
    var observaciones = $("#observaciones").val();
    var tag = $("#tagcotizacion").val();
    var chfirmar = $("#chfirma").prop('checked') ? 1 : 0;

    var txtbd = observaciones.replace(new RegExp("\n", 'g'), '<ent>');

    if (isnEmpty(folio, "folio-cotizacion") && isnEmpty(datosfac, "datos-facturacion") && isnEmpty(nombrecliente, "nombre-cliente") && isEmail(correocliente, "email-cliente")) {
        var url = "com.sine.enlace/enlacecotizacion.php";
        var transaccion = (idcotizacion != null) ? "actualizarcotizacion" : "insertarcotizacion";

        var data = {
            transaccion: transaccion,
            idcotizacion: idcotizacion,
            folio: folio,
            fecha_creacion: fecha_creacion,
            idcliente: idcliente,
            nombrecliente: nombrecliente,
            correocliente: correocliente,
            correocliente2: correocliente2,
            correocliente3: correocliente3,
            tipocomprobante: tipoComprobante,
            idformapago: idformapago,
            idmetodopago: idmetodopago,
            idmoneda: idmoneda,
            iduso: iduso,
            datosfac: datosfac,
            observaciones: txtbd,
            chfirmar: chfirmar,
            tag: tag
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
                    cargandoHide();
                    alertify.error(res);
                } else {
                    cargandoHide();
                    var mensaje = (idcotizacion != null) ? 'Cotización actualizada.' : 'Cotización registrada.';
                    alertify.success(mensaje);
                    loadView('listacotizacion');
                }
            }
        });
    }
}


function editarCotizacion(idcotizacion) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "editarcotizacion", idcotizacion: idcotizacion},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('cotizacion', '1');
                window.setTimeout("setValoresEditarCotizacion('" + datos + "')", 1000);
            }
        }
    });
}

function setValoresEditarCotizacion(datos) {
    changeText("#contenedor-titulo-form-cotizacion", "Editar cotización");
    changeText("#btn-form-cotizacion", "Guardar cambios <span class='fas fa-save'></span>");

    var array = datos.split("</tr>");
    var serie = array[1];
    var letra = array[2];
    var folio = array[3];
    var fechacreacion = array[4];
    var idmetodo_pago = array[10];
    var idmoneda = array[11];
    var iduso_cfdi = array[12];
    var idforma_pago = array[13];
    var idtipo_comprobante = array[14];
    var observaciones = array[22];
    var chfirmar = array[23];
    var documento = array[24];
    var tag = array[25];
    var txtbd = observaciones.replace(new RegExp("<ent>", 'g'), '\n');

    var arrayF = fechacreacion.split("-");
    var fecha = arrayF[2] + "/" + arrayF[1] + "/" + arrayF[0];

    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "prodcotizacion", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductosCot();
            }
        }
    });

    $("#fecha-creacion").val(fecha);
    loadOpcionesFolios('0', serie, letra + folio);
    $("#option-default-datos").val(array[15]);
    $("#option-default-datos").text(array[16]);
    $("#rfc-emisor").val(array[17]);
    $("#cp-emisor").val(array[21]);
    $("#razon-emisor").val(array[18]);
    $("#regimen-emisor").val(array[19] + " " + array[20]);
    $("#id-cliente").val(array[5]);
    $("#nombre-cliente").val(array[6]);
    $("#email-cliente1").val(array[7]);
    $("#email-cliente2").val(array[8]);
    $("#email-cliente3").val(array[9]);

    if (idtipo_comprobante !== "0") loadOpcionesComprobante("tipo-comprobante", idtipo_comprobante);
    if (idforma_pago !== "0") loadOpcionesFormaPago2(idforma_pago);
    if (idmetodo_pago !== "0") loadOpcionesMetodoPago('id-metodo-pago', idmetodo_pago);
    if (idmoneda !== "0") loadOpcionesMoneda('id-moneda', idmoneda);
    if (iduso_cfdi !== "0") loadOpcionesUsoCFDI('id-uso', iduso_cfdi);
    $("#observaciones").val(txtbd);

    if (chfirmar == '1') {
        $("#chfirma").attr('checked', true);
    }
    $("#realizo").val(documento);
    $("#form-cotizacion").append("<input type='hidden' id='tagcotizacion' name='tagcotizacion' value='" + tag + "'/>");
    $("#btn-form-cotizacion").attr("onclick", "insertarCotizacion(" +  array[0] + ");");
    cargandoHide();
}

function imprimirCotizacion(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimircotizacion.php?cot=' + id, 'Cotizacion', '', '1024', '768', 'true');
    cargandoHide();
}

function copiarCotizacion(idcotizacion) {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "editarcotizacion", idcotizacion: idcotizacion},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                loadView('cotizacion');
                window.setTimeout("setValoresCopiarCotizacion('" + datos + "')", 700);
            }
        }
    });
}


function setValoresCopiarCotizacion(datos) {
    var array = datos.split("</tr>");
    var serie = array[1];
    var letra = array[2];
    var folio = array[3];
    var fechacreacion = array[4];
    var idmetodo_pago = array[10];
    var idmoneda = array[11];
    var iduso_cfdi = array[12];
    var idforma_pago = array[13];
    var idtipo_comprobante = array[14];
    var observaciones = array[22];
    var chfirmar = array[23];
    var documento = array[24];
    var tag = array[25];
    var txtbd = observaciones.replace(new RegExp("<ent>", 'g'), '\n');

    var arrayF = fechacreacion.split("-");
    var fecha = arrayF[2] + "/" + arrayF[1] + "/" + arrayF[0];

    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "prodcotizacion", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductosCot();
            }
        }
    });

    $("#fecha-creacion").val(fecha);
    $("#option-default-datos").val(array[15]);
    $("#option-default-datos").text(array[16]);
    $("#rfc-emisor").val(array[17]);
    $("#cp-emisor").val(array[21]);
    $("#razon-emisor").val(array[18]);
    $("#regimen-emisor").val(array[19] + " " + array[20]);
    $("#id-cliente").val(array[5]);
    $("#nombre-cliente").val(array[6]);
    $("#email-cliente1").val(array[7]);
    $("#email-cliente2").val(array[8]);
    $("#email-cliente3").val(array[9]);

    if (idtipo_comprobante !== "0") loadOpcionesComprobante("tipo-comprobante", idtipo_comprobante);
    if (idforma_pago !== "0") loadOpcionesFormaPago2(idforma_pago);
    if (idmetodo_pago !== "0") loadOpcionesMetodoPago('id-metodo-pago', idmetodo_pago);
    if (idmoneda !== "0") loadOpcionesMoneda('id-moneda', idmoneda);
    if (iduso_cfdi !== "0") loadOpcionesUsoCFDI('id-uso', iduso_cfdi);
    $("#observaciones").val(txtbd);

    if (chfirmar == '1') {
        $("#chfirma").attr('checked', true);
    }
}

function eliminarCotizacion(idcotizacion) {
    alertify.confirm("¿Estás seguro que deseas eliminar esta cotización?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecotizacion.php",
            type: "POST",
            data: {transaccion: "eliminarcotizacion", idcotizacion: idcotizacion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se eliminó correctamente la cotización.')
                    loadView('listacotizacion');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

function showCorreos(idcotizacion) {
    cargandoHide();
    cargandoShow();
    $("#idfacturaenvio").val(idcotizacion);
    $("#correo1").val('');
    $("#correo2").val('');
    $("#correo3").val('');
    $("#chcorreo1").prop('checked', true);
    $("#chcorreo2").prop('checked', false);
    $("#chcorreo3").prop('checked', false);

    $("#btn-send").attr("onclick", "enviarcotizacion(" + idcotizacion + ");");
    getCorreos(idcotizacion);
    cargandoHide();
}

function getCorreos(idcotizacion) {
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "getcorreos", idcotizacion: idcotizacion},
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

function enviarcotizacion(id_cotizacion) {
    var mailalt1 = "ejemplo@ejemplo.com";
    var mailalt2 = "ejemplo@ejemplo.com";
    var mailalt3 = "ejemplo@ejemplo.com";
    var chcorreo1 = 0;
    var chcorreo2 = 0;
    var chcorreo3 = 0;
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

    if (isEmailtoSend(mailalt1, "correo1") && isEmailtoSend(mailalt2, "correo2") && isEmailtoSend(mailalt3, "correo3") && isCheckedMailSend(chcorreo1, chcorreo2, chcorreo3, '0', '0', '0')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.imprimir/imprimircotizacion.php",
            type: "POST",
            data: {transaccion: "pdf", idcotizacion: id_cotizacion, correo1: mailalt1, correo2: mailalt2, correo3: mailalt3, ch1: chcorreo1, ch2: chcorreo2, ch3: chcorreo3},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#enviarmail").modal('hide');
                    alertify.success(res);
                }
                cargandoHide();
            }
        });
    }


}