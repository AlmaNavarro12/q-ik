
function filtrarNotificaciones(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceinicio.php",
        type: "POST",
        data: {transaccion: "filtrarnotificaciones", pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-notificacion").html(datos);
                cargandoHide();
            }
        }
    });
}

function buscarNotificaciones(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceinicio.php",
        type: "POST",
        data: {transaccion: "filtrarnotificaciones", pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-notificacion").html(datos);
                cargandoHide();
            }
        }
    });
}