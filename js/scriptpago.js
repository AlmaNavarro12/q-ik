function buscarPago(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "listapagoaltas", REF:  $("#buscar-pago").val(), pag: pag, numreg: $("#num-reg").val()},
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
        data: {transaccion: "listapagoaltas", REF: $("#buscar-pago").val(), pag: pag, numreg: $("#num-reg").val()},
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
        data: {transaccion: 'emisor', iddatos: iddatos},
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
            console.log(ui.item); 
            $("#id-cliente").val(ui.item.id);
            $("#rfc-cliente").val(ui.item.rfc);
            $("#razon-cliente").val(ui.item.razon);
            $("#regfiscal-cliente").val(ui.item.regfiscal);
            $("#cp-cliente").val(ui.item.codpostal);
        }
    });
}