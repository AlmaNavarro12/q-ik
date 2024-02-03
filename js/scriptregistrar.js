function buscarCuenta() {
    var rfc = $("#rfc-usuario").val();
    if (isnEmpty(rfc, "rfc-usuario")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceregistro.php",
            type: 'POST',
            data: {transaccion: 'buscarcuenta', rfc: rfc},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#modal-recover").modal('show');
                    $("#usuarios-registrados").html(datos);
                }
                cargandoHide();
            }
        })
    }
}

function recuperarCuenta() {
    var rfc = $("#rfc-usuario").val();
    var iduser = $("input[name=chuser]:checked").val();
    if (isnEmpty(rfc, "rfc-usuario") && isnChecked(iduser, "chuser")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceregistro.php",
            type: 'POST',
            data: {transaccion: 'sendrecover', rfc: rfc, iduser: iduser},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#modal-recover").modal('hide');
                    setvaloresEnvio(rfc, iduser);
                }
                cargandoHide();
            }
        })
    }
}