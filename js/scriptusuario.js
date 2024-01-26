function loadListaUsuariosaltas() {
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: { transaccion: "listausuariosaltas" },
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
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: { transaccion: "filtrarusuario", US: $("#buscar-usuario").val(), numreg: $("#num-reg").val(), pag: pag },
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

function obtenerDatosUsuario() {
    return {
        nombre: $("#nombre").val(),
        apellidopaterno: $("#apellido-paterno").val(),
        apellidomaterno: $("#apellido-materno").val(),
        telefono: $("#telefono").val(),
        celular: $("#celular").val(),
        usuario: $("#usuario").val(),
        password: $("#contrasena").val(),
        correo: $("#correo").val(),
        tipo: $("#tipo-usuario").val(),
        img: $("#filename").val(),
        chpass: ($("#chpass").prop('checked')) ? 1 : 0,
        imgactualizar: $("#imgactualizar").val()
    };
}

function insertarUsuario(idusuario = null) {
    var datosUsuario = obtenerDatosUsuario();
    if ((idusuario == null && isnEmpty(datosUsuario.nombre, "nombre") && isnEmpty(datosUsuario.apellidopaterno, "apellido-paterno") && isnEmpty(datosUsuario.apellidomaterno, "apellido-materno") && isnEmpty(datosUsuario.usuario, "usuario") && isnEmpty(datosUsuario.password, "contrasena") && isEmail(datosUsuario.correo, "correo") && isPhoneNumber(datosUsuario.telefono, "telefono") && isnEmpty(datosUsuario.tipo, "tipo-usuario")) 
    || (idusuario != null && isnEmpty(datosUsuario.nombre, "nombre") && isnEmpty(datosUsuario.apellidopaterno, "apellido-paterno") &&  isnEmpty(datosUsuario.usuario, "usuario") && isEmail(datosUsuario.correo, "correo") && isPhoneNumber(datosUsuario.telefono, "telefono") && isnEmpty(datosUsuario.tipo, "tipo-usuario"))){
        var transaccion = (idusuario == null) ? "insertarusuario" : "actualizarusuario";
        $.ajax({
            url: "com.sine.enlace/enlaceusuario.php",
            type: "POST",
            data: {
                transaccion: transaccion,
                idusuario: idusuario,
                transaccion: transaccion,
                idusuario: idusuario,
                nombre: datosUsuario.nombre,
                apellidopaterno: datosUsuario.apellidopaterno,
                apellidomaterno: datosUsuario.apellidomaterno,
                telefono: datosUsuario.telefono,
                celular: datosUsuario.celular,
                usuario: datosUsuario.usuario,
                password: datosUsuario.password,
                correo: datosUsuario.correo,
                tipo: datosUsuario.tipo,
                img: datosUsuario.img,
                chpass: datosUsuario.chpass,
                imgactualizar: datosUsuario.imgactualizar
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);

                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se guardaron los datos correctamente ');
                    if (datosUsuario.img != "" && idusuario != null) {
                        location.href = 'home.php';
                    } else {
                        loadView('listasuarioaltas');
                    }
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
        data: { transaccion: "editarusuario", idusuario: idusuario },
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

    if (imgnm != '') {
        $("#muestraimagen").html(img);
        $("#filename").val(imgnm);
    }

    if (tipologin == '2') {
        $("#tipo-usuario").attr('disabled', true);
    }
    if (idusuario == idlogin || tipologin == '1') {
        $("#div-user").addClass('col-md-11');
        $("#span-user").addClass('col-md-1 ps-0 py-2');
        $("#span-user").append("<input class='input-check' type='checkbox' id='chuser' onclick='checkUser()' title='Cambiar nombre de usuario'/>");

        $("#div-pass").addClass('col-md-11');
        $("#span-pass").addClass('col-md-1 ps-0 py-2');
        $("#span-pass").append("<input class='input-check' type='checkbox' id='chpass' onclick='checkContrasena()' title='Cambiar contraseña'/>");
    }
    $("#contrasena").val("");
    $("#form-usuario").append("<input type='hidden' id='id-usuario' name='id-usuario' value='" + idusuario + "'/><input type='hidden' id='imgactualizar' name='imgactualizar' value='" + img + "'/>")
    $("#btn-form-usuario").attr("onclick", "insertarUsuario(" + idusuario + ");");
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
        }).set({ title: "Q-ik" });
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
        }).set({ title: "Q-ik" });
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

function eliminarUsuario(idusuario) {
    alertify.confirm("¿Estás seguro que quieres eliminar este usuario?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceusuario.php",
            type: "POST",
            data: { transaccion: "eliminarusuario", idusuario: idusuario },
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
    }).set({ title: "Q-ik" });
}

function crearIMG() {
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: { transaccion: "crearimg" },
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

function checkUsuario() {
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "gettipousuario"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                if (datos == '2') {
                    $("#tipo-usuario").val('2');
                    $("#tipo-usuario").attr('disabled', true);
                }
            }
        }
    });
}

function asignarPermisos(idusuario) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "asignarpermiso", idusuario: idusuario},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                cargandoHide();
                alertify.error(res);
            } else {
                cargandoHide();
                loadView('asignarpermisos');
                window.setTimeout("setValoresAsignarPermisos('" + datos + "')", 400);
            }
        }
    });
}

function checkAll() {
    var checkAllCheckbox = $("#checkall");
    if (checkAllCheckbox.prop('checked')) {
        $(".collapse-permission").removeClass('show');
        $(".collapse-permission").addClass('hidden');
        $("input:checkbox").prop('checked', false);
    } else {
        $(".collapse-permission").removeClass('hidden');
        $(".collapse-permission").addClass('show');
        $("input:checkbox").prop('checked', true);
    }
}
