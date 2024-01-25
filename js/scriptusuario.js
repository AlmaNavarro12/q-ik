function loadListaUsuariosaltas() {
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "listausuariosaltas"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-usuarios-altas").html(datos);
            }
            cargandoHide();
        }
    });
}

function filtrarUsuario(pag = "") {
    var US = $("#buscar-usuario").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "filtrarusuario", US: US, numreg: numreg, pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-usuarios-altas").html(datos);
                cargandoHide();
            }
        }
    });
}

function insertarUsuario() {
    var img = $("#filename").val();
    var nombre = $("#nombre").val();
    var apellidopaterno = $("#apellido-paterno").val();
    var apellidomaterno = $("#apellido-materno").val();
    var telefono = $("#telefono").val();
    var celular = $("#celular").val();
    var correo = $("#correo").val();
    var usuario = $("#usuario").val();
    var contrasena = $("#contrasena").val();
    var estatus = $("#estatus").val();
    var tipo = $("#tipo-usuario").val();

    if (isnEmpty(nombre, "nombre") && isnEmpty(apellidopaterno, "apellido-paterno") && isnEmpty(apellidomaterno, "apellido-materno") && isnEmpty(usuario, "usuario") && isnEmpty(contrasena, "contrasena") && isEmail(correo, "correo") && isPhoneNumber(telefono, "telefono") && isnEmpty(tipo, "tipo-usuario")) {
        $.ajax({
            url: "com.sine.enlace/enlaceusuario.php",
            type: "POST",
            data: {transaccion: "insertrausuario", nombre: nombre, apellidopaterno: apellidopaterno, apellidomaterno: apellidomaterno, telefono: telefono, celular: celular, usuario: usuario, password: contrasena, correo: correo, estatus: estatus, tipo: tipo, img: img},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Datos de usuario registrado');
                    loadView('listasuarioaltas');
                }
            }
        });
    }
}

function editarUsuario(idusuario) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "editarusuario", idusuario: idusuario},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                cargandoHide();
                loadView('nuevousuario');
                window.setTimeout("setValoresEditarUsuario('" + datos + "')", 400);
            }
        }
    });
}

function setValoresEditarUsuario(datos) {
    $("#usuario").attr('disabled', true);
    $("#contrasena").attr('disabled', true);
    changeText("#contenedor-titulo-form-usuario", "Editar usuario");
    changeText("#btn-form-usuario", "Guardar cambios <span class='fas fa-save'></span></a>");
    var array = datos.split("</tr>");
    var idusuario = array[0];
    var nombre = array[1];
    var apellidopaterno = array[2];
    var apellidomaterno = array[3];
    var telefono = array[7];
    var celular = array[6];
    var correo = array[5];
    var usuario = array[4];
    var estatus = array[8];
    var contraseña = array[9];
    var tipo = array[10];
    var idlogin = array[11];
    var tipologin = array[12];
    var imgnm = array[13];
    var img = array[14];

    $("#nombre").val(nombre);
    $("#apellido-paterno").val(apellidopaterno);
    $("#apellido-materno").val(apellidomaterno);
    $("#celular").val(celular);
    $("#telefono").val(telefono);
    $("#correo").val(correo);
    $("#usuario").val(usuario);
    $("#tipo-usuario").val(tipo);
    
    if(imgnm != ''){
        $("#muestraimagen").html(img);
        $("#filename").val(imgnm);
    }

    if (tipologin == '2') {
        $("#tipo-usuario").attr('disabled', true);
    }
    if (idusuario == idlogin || tipologin == '1') {
        $("#div-user").addClass('col-md-11');
        $("#span-user").addClass('col-md-1 ps-0');
        $("#span-user").append("<input class='input-check' type='checkbox' id='chuser' onclick='checkUser()' title='Cambiar nombre de usuario'/>");
        
        $("#div-pass").addClass('col-md-11');
        $("#span-pass").addClass('col-md-1 ps-0');
        $("#span-pass").append("<input class='input-check' type='checkbox' id='chpass' onclick='checkContrasena()' title='Cambiar contraseña'/>");
    }
    $("#contrasena").val("");

    $("#form-usuario").append("<input type='hidden' id='id-usuario' name='id-usuario' value='" + idusuario + "'/><input type='hidden' id='imgactualizar' name='imgactualizar' value='" + img + "'/>")
    $("#btn-form-usuario").attr("onclick", "actualizarUsuario();");
}

function checkUser() {
    var chuser = 0;
    if ($("#chuser").prop('checked')) {
        chuser = 1;
    }
    if (chuser == 0) {
        $("#usuario").prop('disabled', true);
    } else {
        alertify.confirm("¿Estás seguro que deseas cambiar el nombre de usuario?", function () {
            $("#usuario").removeAttr('disabled');
        }, function () {
            $("#chuser").removeAttr('checked');
        }).set({title: "Q-ik"});
    }
}

function checkContrasena() {
    var chpass = 0;
    if ($("#chpass").prop('checked')) {
        chpass = 1;
    }
    if (chpass == 0) {
        $("#contrasena").prop('disabled', true);
        $("#contrasena").val('');
    } else {
        alertify.confirm("¿Estás seguro que deseas cambiar la contraseña de este usuario?", function () {
            $("#contrasena").removeAttr('disabled');
        }, function () {
            $("#chpass").removeAttr('checked');
        }).set({title: "Q-ik"});
    }
}

function cargarImgUsuario() {
    var formData = new FormData(document.getElementById("form-usuario"));
    var img = $("#imagen").val();
    if (isnEmpty(img, 'imagen')) {
        $.ajax({
            url: 'com.sine.enlace/cargarimguser.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var array = datos.split("<corte>");
                var view = array[0];
                var fn = array[1];
                $("#muestraimagen").html(view);
                $("#filename").val(fn);
                $("#imagen").val('');
            }
        });
    }
}

function actualizarUsuario() {
    var idusuario = $("#id-usuario").val();
    var nombre = $("#nombre").val();
    var apellidopaterno = $("#apellido-paterno").val();
    var apellidomaterno = $("#apellido-materno").val();
    var telefono = $("#telefono").val();
    var celular = $("#celular").val();
    var correo = $("#correo").val();
    var usuario = $("#usuario").val();
    var contrasena = $("#contrasena").val();
    var tipo = $("#tipo-usuario").val();
    var img = $("#filename").val();
    var imgactualizar = $("#imgactualizar").val();
    var chpass = 0;
    if ($("#chpass").prop('checked')) {
        chpass = 1;
    }
    if (isnEmpty(nombre, "nombre") && isnEmpty(apellidopaterno, "apellido-paterno") && isnEmpty(apellidomaterno, "apellido-materno") && isPhoneNumber(telefono, "telefono") && isPhoneNumber(celular, "celular") && isnEmpty(usuario, "usuario") && isEmail(correo, "correo")) {
        $.ajax({
            url: "com.sine.enlace/enlaceusuario.php",
            type: "POST",
            data: {transaccion: "actualizarusuario", idusuario: idusuario, nombre: nombre, apellidopaterno: apellidopaterno, apellidomaterno: apellidomaterno, telefono: telefono, usuario: usuario, contrasena: contrasena, celular: celular, correo: correo, tipo: tipo, chpass: chpass, img: img, imgactualizar:imgactualizar},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se guardaron los datos correctamente ');
                    if (img != "") {
                        location.href = 'home.php';
                    } else {
                        loadView('listasuarioaltas');
                    }
                }
            }
        });
    }
}

function eliminarUsuario(idusuario) {
    alertify.confirm("Estas seguro que quieres eliminar este usuario?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceusuario.php",
            type: "POST",
            data: {transaccion: "eliminarusuario", idusuario: idusuario},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    cargandoHide();
                    loadView('listasuarioaltas');
                }
            }
        });
    }).set({title: "Q-ik"});
}

function crearIMG() {
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "crearimg"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                alert(datos);
            }
        }
    });
}