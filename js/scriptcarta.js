function filtrarCarta(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "filtrarfolio", pag: pag, REF: $("#buscar-carta").val(), numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-carta").html(datos);
                cargandoHide();
            }
        }
    });
}
