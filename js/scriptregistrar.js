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