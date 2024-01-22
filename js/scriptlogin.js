$(document).ready(function () {
    validarSession();
});

function loadFormLogin() {
    $("#body-index").load("com.sine.vista/login/formlogin.html");
}

function validarSession() {
    cargandoShow(); 
    $.ajax({
        url: 'com.sine.enlace/enlacesession.php',
        type: 'POST',
        data: {transaccion: 'validarsession'},
        success: function (datos) {
            if (datos.toString() == '0') {
                loadFormLogin();
            } else {
                location.href = 'home.php';
            }
            cargandoHide();
        }
    });
    cargandoHide(); 
}

function login() {
    var usuario = $("#usuario").val();
    var contrasena = $("#contrasena").val();
    if (isnEmpty(usuario, "usuario") && isnEmpty(contrasena, "contrasena")) {
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/enlacesession.php',
            type: 'POST',
            data: {transaccion: 'login', contrasena: contrasena, usuario: usuario},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == 0) {
                    alertify.error(res);
                } else {
                    if (datos == 'e516f979536994a14d9b0500bca3a1287b9ea9fe') {
                        location.href = 'home.php';
                    } else {
                        alertify.error(res);
                    }
                }
                cargandoHide();
            }
        });
        cargandoHide();
    }
}