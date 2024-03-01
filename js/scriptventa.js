$(function () {
    $("#tabs").on("click", "button.sm-tab", function () {
        $('.sm-tab').removeClass("sub-tab-active");
        $('.sub-div').hide();
        var tab = $(this).attr("data-tab");
        $("#ticket-" + tab).show();
        $(this).addClass("sub-tab-active");
    });

    $("#tabs").on("click", "span.close-button", function () {
        var tab = $(this).attr("data-tab");
        cerrarTicket(tab);
    });

    $("#buscar-producto").keyup(function (event) {
        if (event.keyCode == 13) {
            agregarProducto();
        }
    });
    
    $('#cantidad-producto-precio').keyup(function (event) {
        if (event.keyCode == 13) {
            agregarProductoTicket();
        }
    });

    $("#buscar-producto-precio").keyup(function (event) {
        if (event.keyCode == 13) {
            buscarPrecioProducto();
        }
    });

    $(".button-venta").click(function () {
        $('.button-venta').removeClass("button-venta-active");
        $(this).addClass("button-venta-active");
        var tab = $(this).attr("pago-tab");
        $(".div-forma").hide();
        if (tab == 'cash') {
            $("#cambio-label").show();
        } else if (tab == 'card' || tab == 'val') {
            tab = 'ref';
        }
        $("#" + tab + "-div").show('slow');
    });

    $('#cantidad-producto').keypress(function(event){
		if(event.which == 13) {
			$('#precio-prod').select();
		}
	});

    $('#precio-prod').keypress((e)=>{
        if(e.which == 13){
            actualizarCantidad();
            $('#modal-cantidad').modal('hide');
        }
    });

    $('#monto-entrada').keypress((e)=>{
        if(e.which == 13) {
            if( $('#monto-entrada').val() == "" ){
                $('#monto-entrada').select();
            }else{
                $('#concepto-entrada').select();
            }
        }
    });

    $('#PercentDescuento').keyup((e)=>{
        if(e.which == 13) {
			$('#monto-pagado').select();            
		}
        CalculaDescuentoTotal();
    });

    $('#monto-pagado').keypress((e)=>{
        if(e.which == 13) {
            validarProductosVenta(1);
		}
    });
});

function registrarDineroInicial() {
    var monto = $("#monto-inicial").val();
    if (isnZero(monto, "monto-inicial")) {
        $.ajax({
            url: "com.sine.enlace/enlaceventa.php",
            type: "POST",
            data: {transaccion: "fondoinicial", monto: monto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#modal-dincaja").modal('hide');
                }
            }
        });
    }
}

function setLabelIngreso(ele) {
    var type = $(ele).attr("id");
    $("#monto-entrada").val('');
    $("#concepto-entrada").val('');
    if (type == 'btn-entrada') {
        $('#label-ingresos').text('Registrar entrada de efectivo');
        $("#type-movimiento").val('1');
    } else if (type == 'btn-salida') {
        $('#label-ingresos').text('Registrar salida de efectivo');
        $("#type-movimiento").val('2');
    }
    window.setTimeout(()=>{
        $('#monto-entrada').select();
    },500);
}

var numticket = 1;
function newVenta() {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "newventa", ticket: numticket},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<cut>");
                $("#tabs").append(array[0]);
                $("#tickets").append(array[1]);
                var tag = array[2];

                $(".sub-div").hide();
                $(".sm-tab").removeClass("sub-tab-active");

                $("#tab-" + tag).addClass('sub-tab-active');
                $("#ticket-" + tag).show();
                numticket++;
            }
            cargandoHide();
            $('#buscar-producto').select();
        }
    });
}

function cerrarTicket(tab = "") {
    alertify.confirm("¿Estás seguro que deseas eliminar este Ticket? (Toda la información ingresada se perderá).", function () {
        if (tab == '') {
            tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
            $("#modal-cobrar").modal('hide');
        }
        $.ajax({
            url: 'com.sine.enlace/enlaceventa.php',
            type: 'POST',
            data: {transaccion: 'borrarticket', tab: tab},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#tab-" + tab).remove();
                    $("#ticket-" + tab).remove();
                    var first = $("#tabs").find('.sm-tab:first').attr("data-tab");
                    if (first) {
                        $("#ticket-" + first).show();
                        $("#tab-" + first).addClass("sub-tab-active");
                    } else {
                        newVenta();
                    }
                }
            }
        });
    }).set({title: "Q-ik"});
}

function aucompletarProducto() {
    $('#buscar-producto').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=producto",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function agregarProducto() {
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var producto = $("#buscar-producto").val();
    if (isnEmpty(producto, "buscar-producto")) {
        $.ajax({
            url: "com.sine.enlace/enlaceventa.php",
            type: "POST",
            data: {transaccion: "agregarproducto", producto: producto, tab: tab},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#buscar-producto").val('');
                    aucompletarProducto();
                    tablaProducto();
                }
            }
        });
    }
}

function tablaProducto() {
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "tablatmp", tab: tab},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#prod-" + tab).html(datos);
                cargandoHide();
            }
        }
    });
}

function registrarEntrada() {
    var cantidad = $("#monto-entrada").val();
    var concepto = $("#concepto-entrada").val();
    if (isNumber(cantidad, "monto-entrada") && isnEmpty(concepto, "concepto-entrada")) {
        $.ajax({
            url: 'com.sine.enlace/enlaceventa.php',
            type: 'POST',
            data: {transaccion: 'registrarmovimiento', tipo: $("#type-movimiento").val(), cantidad: cantidad, concepto: concepto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#modal-entradas").modal("hide");
                    alertify.success("Movimiento de efectivo registrado.");
                }
            }
        });
    }
}

function setCantidadVenta(idtmp, cant, precio) {
    $("#idcant").val(idtmp);
    $("#precio-orig").val(precio);
    $("#cantidad-producto").val(cant);
    $("#precio-prod").val(precio);
    window.setTimeout(()=>{
        $('#cantidad-producto').select();
    }, 500);
}

function incrementarCantidad(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "incrementar", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProducto();
            }
            cargandoHide();
        }
    });
}

function reducirCantidad(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "reducir", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProducto();
            }
            cargandoHide();
        }
    });
}

function calcularPrecio(){
    var precio = $("#precio-orig").val() || '0';
    var cant = $("#cantidad-producto").val() || '0';
    var importe = parseFloat(precio)*parseFloat(cant);
    $("#precio-prod").val(importe);
}

function checkFondo() {
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "checkfondo"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (datos == false) {
                $("#modal-dincaja").modal('show');
            }
            cargandoHide();
        }
    });
}

function actualizarCantidad() {
    var idtmp = $("#idcant").val();
    console.log(idtmp);
    var cant = $("#cantidad-producto").val();
    var precioprod = $("#precio-prod").val();
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "modificartmp", idtmp: idtmp, cant: cant, precio:precioprod},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $('#modal-cantidad').modal('hide');
                tablaProducto();
                cargandoHide();
            }
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
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "chivatmp", idtmp: idtmp, traslados: idtraslados, retenciones: idretenciones},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProducto();
            }
            cargandoHide();
        }
    });
}

function eliminarProdTmp(tid) {
    alertify.confirm("¿Estás seguro que deseas eliminar este producto?", function () {
        $.ajax({
            url: 'com.sine.enlace/enlaceventa.php',
            type: 'POST',
            data: {transaccion: 'eliminarprod', tid: tid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProducto();
                }
            }
        });
    }).set({title: "Q-ik"});
}

function registrarDineroInicial() {
    var monto = $("#monto-inicial").val();
    if (isNumber(monto, "monto-inicial")) {
        $.ajax({
            url: "com.sine.enlace/enlaceventa.php",
            type: "POST",
            data: {transaccion: "fondoinicial", monto: monto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#modal-dincaja").modal('hide');
                    alertify.success('Monto de $' + monto + ' registrado en caja.')
                }
            }
        });
    }
}

function setValoresCobrar() {
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    $("#label-cambio").val("$0.00");
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'totalticket', tab: tab},
        success: function (datos) {
            var array = datos.split("</tr>");
            var total = array[0] || "0.00";
            var art = array[1] || "0";
            var descuento = array[2] || "0.00";

            $("#label-total").html("$" + total);
            $("#total-cobrar").val(total);
            $("#total-original").val(total);
            $("#monto-pagado").val(total);
            $("#label-art").html(art);
            $('#label-descuento').html("$ " + descuento);
            $('#input-descuento').val(descuento);
            $('#input-descuento-original').val(descuento);

            window.setTimeout(() => {
                $("#monto-pagado").select();
            }, 500);
        }
    });
}


function habilitarDescuento(){
    if( $('#ChkDescuento').is(':checked') ){
        $('#Spndescuento').removeClass('far fa-square');
        $('#Spndescuento').addClass('far fa-check-square');
        $('#groupDesc').show('slow');
        window.setTimeout(()=>{ $('#PercentDescuento').select(); },500);
        CalculaDescuentoTotal();
    }else{
        resetDescuento();
    }
}

function resetDescuento(){
    var total = $('#total-original').val();
    var descuento = $('#input-descuento-original').val();
    $('#label-total').html(total);
    $('#total-cobrar').val(total);
    $('#label-descuento').html("$ "+descuento);
    $('#input-descuento').val(descuento);
    $('#PercentDescuento').val(5);
    $('#Spndescuento').removeClass('far fa-check-square');
    $('#Spndescuento').addClass('far fa-square');
    $('#groupDesc').hide('slow');
    calcularCambio();
}

function calcularCambio() {
    var total = $("#total-cobrar").val() || '0';
    var monto = $("#monto-pagado").val() || '0';
    var cambio = parseFloat(monto) - parseFloat(total);
    cambio = Math.floor(cambio * 100)/100;
    if(cambio > 0){
        $("#label-cambio").html("$" + cambio);
    }else{
        $("#label-cambio").html("$ 0.00");
    }
}

function CalculaDescuentoTotal(){
    var total = $('#total-original').val();
    var descuento_original = $('#input-descuento-original').val();
    var descuento = $('#PercentDescuento').val();
    var monto = (descuento * total) / 100;
    monto = Math.floor(monto * 100)/100;
    var suma_descuentos = parseFloat(descuento_original) + parseFloat(monto);
    suma_descuentos = Math.floor(suma_descuentos*100)/100;
    var totalDescuento = total - monto;
    totalDescuento = Math.floor(totalDescuento * 100)/100;
    $('#label-total').html(descuento == 0 ? total : totalDescuento);
    $('#total-cobrar').val(descuento == 0 ? total : totalDescuento);
    $('#label-descuento').html("$ " + (descuento == 0 ? descuento_original : suma_descuentos));
    $('#input-descuento').val(descuento == 0 ? descuento_original : suma_descuentos);
    calcularCambio();
}

function validarProductosVenta(p){
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'validaproductos', tab: tab},
        dataType: 'JSON',
        success: function (datos) {
            guardarVenta(p, datos.maximo);
        }
    });
}

function guardarVenta(p, nprod) {
    $('#btn-print, #btn-form-reg').prop('disabled', true);
    var tab = $("#tabs .sub-tab-active").attr("data-tab");
    var total = parseFloat($("#total-cobrar").val());
    var descuento = $('#input-descuento').val();
    var percent_descuento = $('#ChkDescuento').is(':checked') ? $('#PercentDescuento').val() : 0;
    var fmpago = $("#btn-fmpago .button-venta-active").attr("pago-tab");
    var referencia = (fmpago == 'cash') ? "null" : $("#referencia-pago").val();
    var validado = (fmpago == 'cash' && parseFloat($("#monto-pagado").val()) >= total) ? 1 : 0;
    var pagado = (fmpago == 'cash') ? parseFloat($("#monto-pagado").val()) : 1;

    if (isnZero(pagado, "monto-pagado") && isnEmpty(referencia, "referencia-pago")) {
        if (nprod > 0 && validado == 1) {
            $.ajax({
                url: 'com.sine.enlace/enlaceventa.php',
                type: 'POST',
                data: {
                    transaccion: 'insertarticket',
                    tab: tab,
                    total: total,
                    pagado: pagado,
                    referencia: referencia,
                    fmpago: fmpago,
                    descuento: descuento,
                    percent_descuento: percent_descuento
                },
                success: function (datos) {
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 5000);
                    if (bandera == '0') {
                        alertify.error(res);
                    } else {
                        $("#tab-" + tab + ", #ticket-" + tab).remove();
                        var first = $("#tabs .sm-tab:first").attr("data-tab");
                        if (first) {
                            $("#ticket-" + first).show();
                            $("#tab-" + first).addClass("sub-tab-active");
                        } else {
                            newVenta();
                        }
                        $("#modal-cobrar").modal('hide');
                        if (p == '1') {
                            imprimirTicket(tab);
                        }
                        alertify.success("Venta Registrada");
                        ResetDescuento();
                    }
                }
            });
        } else {
            alertify.error((nprod == 0) ? "El ticket seleccionado no contiene productos." : "El monto pagado es menor que el total de la venta.");
        }
    }
    $('#btn-print, #btn-form-reg').prop('disabled', false);
}

function filtrarVentas(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'listaventa', pag: pag, REF: $("#buscar-ticket").val(), numreg: $("#num-reg").val(), usuario: $("#usuarios").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-ticket").html(datos);
            }
            cargandoHide();
        }
    });
}

function buscarVentas(pag = "") {
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'listaventa', pag: pag, REF: $("#buscar-ticket").val(), numreg: $("#num-reg").val(), usuario: $('#usuarios').val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-ticket").html(datos);
            }
        }
    });
}

function selectedPercepciones() {
    var percepciones = [];
    $.each($("input[name='chpercepcion']:checked"), function () {
        percepciones.push($(this).attr('data-id') + "/" + $(this).attr('value'));
    });

    $.ajax({
        url: "com.sine.enlace/enlacenomina.php",
        type: "POST",
        data: {transaccion: "selectedpercepcion", percepcion: percepciones},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#selected-percepciones").html(datos);
            }
        }
    });
    calcularTotalPercepciones();
}