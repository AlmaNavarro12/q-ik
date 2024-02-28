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
        $("#" + tab + "-div").show();
    });

    /*$(".btn-corte").click(function () {
        $('.btn-corte').removeClass("selected");
        $(this).addClass("selected");
        corteCaja();
    });*/

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

function setValoresCobrar() {
    var tab = $("#tabs").find('.sub-tab-active').attr("data-tab");
    $("#label-cambio").val("$0.00");
    $.ajax({
        url: 'com.sine.enlace/enlaceventa.php',
        type: 'POST',
        data: {transaccion: 'totalticket', tab: tab},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = res.split("</tr>");
                var total = array[0];
                var articulos = array[1];
                var descuento = array[2];
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
        }
    });
}