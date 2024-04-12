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

    $(document).keyup(function (event) {
        if (event.keyCode == 46) { 
            cerrarTicket();
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
            $("#cambio-descuento").show();
            $("#referencia-pago").val("");
        } else if (tab == 'card') {
            tab = 'ref';
            $("#cambio-descuento").show();
            $("#cambio-label").hide();
            $("#referencia-pago").val("");
            $("#tipo-tarjeta").show();
        } else if(tab == 'val'){
            tab = 'ref';
            $("#cambio-descuento").show();
            $("#cambio-label").hide();
            $("#referencia-pago").val("");
            $("#tipo-tarjeta").hide();
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

function aucompletarBuscarProducto() {
    $('#buscar-producto-precio').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=producto",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function buscarPrecioProducto(){
    var producto = $("#buscar-producto-precio").val();
    if(isnEmpty(producto, "buscar-producto-precio")) {
        $.ajax({
            url: "com.sine.enlace/enlaceventa.php",
            type: "POST",
            data: {transaccion: "checkPrecio", producto: producto},
            dataType: "JSON",
            success: function (datos) {
                $("#buscar-producto-precio").val("");
                $('#CollapsePrecio').collapse('show');
                $('#SpnCodigo').html(datos.cod_prod);
                $('#SpnProd').html(datos.nom_prod);
                $('#impuestos_modal').html(datos.html);
                $('#impuestos_modal').html(total.html);
                window.setTimeout(()=>{ aucompletarBuscarProducto(); }, 500);
                $('#cantidad-producto-precio').select();
            }
        });
    }

}

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

function agregarProductoTicket(){ 
    cargandoShow();
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var producto = $("#SpnCodigo").text()+'-'+$('#SpnProd').text();
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "agregarproductobusqueda", producto: producto, tab: tab, cantidad: $('#cantidad-producto-precio').val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProducto();
                $('#modal-consulta-precios').modal('hide');
            }
        }
    });
    cargandoHide();
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
            if (bandera != '0') {
                $("#modal-dincaja").modal('show');
            }
            cargandoHide();
        }
    });
}

function actualizarCantidad() {
    var idtmp = $("#idcant").val();
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

function cerrarValores(){
    $("#label-cambio").html("$ 0.00");
    $('#ChkDescuento').prop('checked', false);
    $('#Spndescuento').removeClass('far fa-check-square');
    $('#Spndescuento').addClass('far fa-square');
    $('#groupDesc').hide('slow');
    $('#label-descuento').val("$0.00");
    $('#PercentDescuento').val(5);
    $("#referencia-pago").val("");
}

function setValoresCobrar() {
    $("#modal-cobrar").modal('show');
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    $("#label-cambio").val("$0.00");
    cerrarValores();
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'totalticket', tab: tab},
        success: function (datos) {
            var array = datos.split("</tr>");
            var total = array[0] || "0.00";
            var articulos = array[1] || "0";
            var descuento = array[2] || "0.00";

            $("#label-total").html("$" + total);
            $("#total-cobrar").val(total);
            $("#total-original").val(total);
            $("#monto-pagado").val(total);
            $("#label-art").html(articulos);
            $('#label-descuento').html("$ "+descuento);
            $('#input-descuento').val(descuento);
            $('#input-descuento-original').val(descuento);
            window.setTimeout(() => {
                $("#monto-pagado").select();
            }, 500);
        }
    });
}

function imprimirTicket(tab = "", cancelado = "") {
    cargandoHide();
    cargandoShow();
    var myLeft = (screen.width - 400) / 2;
    var myTop = (screen.height - 768) / 2;
    var features = 'left=' + myLeft + ',top=' + myTop;
    var mywindow = window.open('./com.sine.imprimir/imprimirticket.php?imagen='+cancelado+'&t=' + tab, 'Ticket', features+',width=400,height=768');
    window.setTimeout(()=>{ mywindow.print(); },900);
    cargandoHide();
}

function cancelarTicket(id) {
    alertify.confirm("¿Estás seguro que deseas cancelar este ticket?", function () {
        $.ajax({
            url: 'com.sine.enlace/enlaceventa.php',
            type: 'POST',
            data: {transaccion: 'cancelarTicked', id: id},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success("Ticket cancelado. Inventario restaurado.");
                    filtrarVentas();
                }
            }
        });
    }).set({title: "Q-ik"});
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
    $("#cambio-descuento").show();
    var total = $('#total-original').val();
    var descuento = $('#input-descuento-original').val();
    $('#label-total').html("$" + total);
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
    if(cambio > 0){
        $("#label-cambio").html("$" + cambio.toFixed(2));
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
    $('#label-total').html(descuento == 0 ? "$" + total : "$" +totalDescuento);
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
    $('#btn-print').prop('disabled', true);
    $('#btn-form-reg').prop('disabled', true);

    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    var total = $("#total-cobrar").val();
    var descuento = $('#input-descuento').val();
    var percent_descuento = 0;
    var fmpago = $("#btn-fmpago").find('.button-venta-active').attr("pago-tab");
    var referencia = "null";
    var validado = 0;
    var pagado = $("#monto-pagado").val() || 0.0;
    var tarjeta = "null";

    if (fmpago == 'cash') {
        pagado = parseFloat(pagado);
        total = parseFloat(total);
        if (pagado >= total){
            validado = 1;
        }
    } else if(fmpago == 'card') {
        var pagado = "1.0";
        validado = 1;
        referencia = $("#referencia-pago").val();
        tarjeta = $("input[name=tarjeta]:checked").val();
    } else {
        var pagado = "1.0";
        validado = 1;
        referencia = $("#referencia-pago").val();
    }

    if($('#ChkDescuento').is(':checked') ){
        percent_descuento = $('#PercentDescuento').val();
        if (isnZero(pagado, "monto-pagado") && isnEmpty(referencia, "referencia-pago") && isNumberPositive(percent_descuento, "PercentDescuento")) {
            if( nprod > 0 ){
                if( validado == 1){    
                    $.ajax({
                        url: 'com.sine.enlace/enlaceventa.php',
                        type: 'POST',
                        data: {transaccion: 'insertarticket', tab: tab, total: total, pagado: pagado, referencia: referencia, fmpago: fmpago, tarjeta:tarjeta, descuento: descuento, percent_descuento:percent_descuento},
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
                                $("#modal-cobrar").modal('hide');
                                if (p == '1') {
                                    imprimirTicket(tab);
                                }
                                alertify.success("Venta registrada correctamente.");
                                ResetDescuento();
                            }
                        }
                    });
                } else {
                    alertify.error("El monto pagado es menor que el total de la venta.");
                }
            } else {
                alertify.error("El ticket seleccionado no contiene productos.");
            }
        }
    } else {
        if (isnZero(pagado, "monto-pagado") && isnEmpty(referencia, "referencia-pago")) {
            if( nprod > 0 ){
                if( validado == 1){    
                    $.ajax({
                        url: 'com.sine.enlace/enlaceventa.php',
                        type: 'POST',
                        data: {transaccion: 'insertarticket', tab: tab, total: total, pagado: pagado, referencia: referencia, tarjeta:tarjeta, fmpago: fmpago, descuento: descuento, percent_descuento:percent_descuento},
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
                                $("#modal-cobrar").modal('hide');
                                if (p == '1') {
                                    imprimirTicket(tab);
                                }
                                alertify.success("Venta registrada correctamente.");
                                ResetDescuento();
                            }
                        }
                    });
                } else {
                    alertify.error("El monto pagado es menor que el total de la venta.");
                }
            } else {
                alertify.error("El ticket seleccionado no contiene productos.");
            }
        }
    }
    $('#btn-print').prop('disabled', false);
    $('#btn-form-reg').prop('disabled', false);
}

function ResetDescuento(){

    var total = $('#total-original').val();
    var descuento = $('#input-descuento-original').val();

    $('#label-total').html(total);
    $('#total-cobrar').val(total);
    $('#label-descuento').html("$ "+descuento);
    $('#input-descuento').val(descuento);
    $('#PercentDescuento').val(5);

    $('#Spndescuento').removeClass('far fa-square');
    $('#Spndescuento').addClass('far fa-check-square ');
    $('#groupDesc').hide('slow');

    calcularCambio();
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

function corteCaja(user = "") {
    cargandoHide();
    cargandoShow();
    var user = $("#usuario-corte").val() || '0';
    (user == "0") ? $("#pago_factura").hide('slow') : $("#pago_factura").show('slow');
    var pago = $("#pago").is(":checked") ? 1 : 0;
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'cortecaja', user: user, pago:pago},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '') {
                alertify.error(res);
            } else {
                var array = datos.split("<cut>");
                var ventas = array[0];
                var ganancias = array[1];
                var entradas = array[2];
                var caja = array[3];
                var salidas = array[4];

                $("#lbl-ventas").text("$" + ventas);
                $("#ventas_totales").val(ventas);
                $("#lbl-ganancia").text("$" + ganancias);
                $("#ganancias_totales").val(ganancias);
                $("#tab-entradas").html(entradas);
                $("#tab-caja").html(caja);
                $("#tab-salidas").html(salidas);
                cargandoHide();
            }
        }
    });
}

function getPermisoNewProducto() {
    $bandera = false;
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: { transaccion: 'checkPersisionNewProduct'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            if (bandera == 1) {
                $('.marker').removeClass("marker-active");
                $('.list-element').removeClass("menu-active");
                $('#alta-productos').addClass("menu-active");
                $('#alta-productos').children('div.marker').addClass("marker-active");
                loadView('nuevoproducto');
                window.setTimeout(function () {
                    $('#codigo-producto').focus();
                }, 900);
            } else {
                alertify.error("No tienes los permisos para crear nuevos productos");
            }
        }
    });
}

function imprimirCorteCaja(user, fecha, hora, tag, id, supervisor) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimircortecaja.php?u=' + user + '&&f=' + fecha + '&&h=' + hora +'&&t=' + tag +'&&i=' + id +'&&s=' + supervisor, 'Corte Caja', '', '1024', '768', 'true');
    cargandoHide();
}

function modalSupervisor(){
    var usuario = $("#usuario-corte").val();
    if(isnEmpty(usuario, "usuario-corte")){
        $("#modal-supervisor").modal('show');
    }
}

function validarSupervisor() {
    var usuario = $("#supervisor").val();
    var contrasena = $("#contrasena").val();
    if (isnEmpty(usuario, "supervisor") && isnEmpty(contrasena, "contrasena")) {
        $.ajax({
            url: "com.sine.enlace/enlaceventa.php",
            type: "POST",
            data: {transaccion: "validarsupervisor", usuario: usuario, contrasena: contrasena},
            success: function (datos) {
                var array = datos.split("<tr>");
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    insertarCorte(array[1]);
                }
            }
        });
    }
}

$('#comentarios-extras').on('click', function () { 
    if ($('#comentarios-extras').prop('checked')) {
        $("#complemento-corte").show('slow');
    } else {
        $("#comentarios").val("");
        $("#total_sobrantes").val("");
        $("#total_faltantes").val("");
        $("#complemento-corte").hide('slow');
    }
});

function insertarCorte(idsupervisor = ""){
    var totalventas = $("#ventas_totales").val();
    var totalentradas = $("#total_entradas").val();
    var totalsalidas = $("#total_salidas").val();
    var fondoinicio = $("#fondo_inicio").val();
    var usuario = $("#usuario-corte").val();
    var fechaventa = $("#fecha-corte").val();
    var totalganancias = $("#ganancias_totales").val();
    var comentarios = $("#comentarios").val();
    var totalfaltantes = $("#total_faltantes").val();
    var totalsobrantes = $("#total_sobrantes").val();
    var pago = $("#pago").is(":checked") ? 1 : 0;

    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "insertarcorte", totalventas: totalventas, totalentradas: totalentradas, totalsalidas: totalsalidas, fondoinicio: fondoinicio, usuario: usuario, fechaventa: fechaventa, totalganancias: totalganancias, idsupervisor: idsupervisor, comentarios: comentarios, totalsobrantes: totalsobrantes, totalfaltantes: totalfaltantes, pago:pago},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<tr>");
                $("#modal-supervisor").modal('hide');
                loadView('listacortes');
                alertify.success('Corte de caja registrado, espere un momento para visualizar cambios.');
                cargandoHide();
                setTimeout(function() {
                    var nuevaVentana = imprimirCorteCaja(array[0], array[1], array[2], array[3], array[4], array[5]);
                    nuevaVentana.focus();
                }, 2000);
            }
        }
    });
}

function buscarCorte(pag = ""){
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "filtrarcorte", REF: $("#buscar-corte").val(), numreg: $("#num-reg").val(), pag:pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-cortes").html(datos);
            }
        }
    });
}

function loadListaCorte(pag = ""){
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceventa.php",
        type: "POST",
        data: {transaccion: "filtrarcorte", REF: $("#buscar-corte").val(), numreg: $("#num-reg").val(), pag:pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-cortes").html(datos);
                cargandoHide();
            }
        }
    });
}

function exportarTicket(id) {
    $('.list-element').removeClass("menu-active");
    $('.marker').removeClass("marker-active");
    $('#factura-menu').addClass("menu-active");
    $('#factura-menu').children('div.marker').addClass("marker-active");
    cargandoHide();
    cargandoShow();
    window.setTimeout("prodTickets('" + id + "')", 100);
}

function prodTickets(id) {
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'exportarticket', id: id},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                loadView('factura');
                window.setTimeout("mostrarDatosVenta('" + datos + "')", 800);
                window.setTimeout("exportarProductos('" + id + "')", 800);
            }
            cargandoHide();
        }
    });
}

function exportarProductos(id){
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'exportarproductos', id: id},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos();
            }
            cargandoHide();
        }
    });
}

function mostrarDatosVenta(datos) {
    var idformapago = 0;
    var array = datos.split("</tr>");
    idventa = array[0];
    forma = array[1];
    tarjeta = array[2];
    if (forma == 'card') {
        if (tarjeta == 'credito') {
            idformapago = 4; // Crédito
        } else if (tarjeta == 'debito') {
            idformapago = 18; // Débito
        }
    } else {
        idformapago = (forma == 'cash') ? 1 : ((forma == 'val') ? 7 : 0);
    }

    $("#id-metodo-pago").attr('disabled', true);
    loadOpcionesMetodoPago('id-metodo-pago', 1);
    loadOpcionesFormaPago2(idformapago);
    $("#id-forma-pago").attr('disabled', true);
    $("#idticket").val(idventa);
    $("#btn-nuevo-producto").attr('disabled', true);
    $("#btn-agregar-productos").attr('disabled', true);
}

function cobrarCotizacion(idcotizacion){
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecotizacion.php",
        type: "POST",
        data: {transaccion: "cobrar", idcotizacion: idcotizacion},
        success: function (datos) {
            if(datos != null){
                window.setTimeout("asignarTag('"+datos+"')", 500);
            }
        }
    });
    cargandoHide();
}

function asignarTag(sid){
    cargandoHide();
    cargandoShow();
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");

    $.ajax({
        url:  "com.sine.enlace/enlaceventa.php",
        type:"POST",
        data: {'transaccion' : "asignatagcotizacion", 'tab' : tab, 'sid' : sid },
        success: function (datos) {
            tablaProducto();
            setValoresCobrar();
            $('#modal-cobrar').modal('show');
            window.setTimeout(() => {
                $("#monto-pagado").select();
            }, 500);
        }
    });
    cargandoHide();
}