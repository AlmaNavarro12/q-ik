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
        imgactualizar: $("#imgactualizar").val(),
        nameimg: $("#nameimg").val()
    };
}

function insertarUsuario(idusuario = null) {
    var datosUsuario = obtenerDatosUsuario();
    if ((idusuario == null && isnEmpty(datosUsuario.nombre, "nombre") && isnEmpty(datosUsuario.apellidopaterno, "apellido-paterno") && isnEmpty(datosUsuario.apellidomaterno, "apellido-materno") && isnEmpty(datosUsuario.usuario, "usuario") && isnEmpty(datosUsuario.password, "contrasena") && isEmail(datosUsuario.correo, "correo") && isPhoneNumber(datosUsuario.telefono, "telefono") && isnEmpty(datosUsuario.tipo, "tipo-usuario"))
        || (idusuario != null && isnEmpty(datosUsuario.nombre, "nombre") && isnEmpty(datosUsuario.apellidopaterno, "apellido-paterno") && isnEmpty(datosUsuario.apellidomaterno, "apellido-materno") && isnEmpty(datosUsuario.usuario, "usuario") && isEmail(datosUsuario.correo, "correo") && isPhoneNumber(datosUsuario.telefono, "telefono") && isnEmpty(datosUsuario.tipo, "tipo-usuario"))) {
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
                imgactualizar: datosUsuario.imgactualizar,
                nameimg: datosUsuario.nameimg
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);

                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var nombre = datosUsuario.nombre + ' ' + datosUsuario.apellidopaterno;
                    if ((datosUsuario.img != imagenperfil || nombre != nombreusuario) && idusuario == uid) {
                        location.href='home.php';
                        alertify.success('Se guardaron los datos correctamente ');
                    } else {
                        loadView('listasuarioaltas');
                        alertify.success('Se guardaron los datos correctamente ');
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
    changeText("#btn-form-usuario", "Actualizar <span class='fas fa-save'></span></a>");
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
        $("#div-user").addClass('col-11');
        $("#span-user").addClass('col-1 ps-0 py-2');
        $("#span-user").append("<input class='input-check' type='checkbox' id='chuser' onclick='checkUser()' title='Cambiar nombre de usuario'/>");
        
        $("#div-pass").addClass('col-11');
        $("#span-pass").addClass('col-1 ps-0 py-2');
        $("#span-pass").append("<input class='input-check' type='checkbox' id='chpass' onclick='checkContrasena()' title='Cambiar contraseña'/>");
    }
    $("#contrasena").val("");
    $("#form-usuario").append("<input type='hidden' id='nameimg' name='nameimg' value='" + imgnm + "'/>")
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
            $("#chuser").prop('checked', false);
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
            $("#chpass").prop('checked', false);
        }).set({ title: "Q-ik" });
    }
}

function cargarImgUsuario() {
    var formData = new FormData();
    var imgInput = $("#imagenperfil")[0].files[0]; 
    var rutaUsuarios = "temporal/usuarios/";
    if (imgInput) {
        formData.append("imagenperfil", imgInput);
        formData.append("ruta_personalizada", rutaUsuarios);
        $.ajax({
            url: 'com.sine.enlace/cargarimg.php',
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
                $("#imagenperfil").val('');
            }
        });
    } else {
        alertify.error("Por favor selecciona una imagen.");
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

function checkUsuario() {
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: { transaccion: "gettipousuario" },
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
        data: { transaccion: "asignarpermiso", idusuario: idusuario },
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
    var btnAllPermisos = $("#btn-all-permisos");
    if (btnAllPermisos.text().includes("Asignar todos los permisos")) {
        $(".collapse-permission").removeClass('hidden').addClass('show');
        $("input:checkbox").prop('checked', true);
        changeText("#btn-all-permisos", "Quitar todos los permisos <span class='fas fa-times'></span></a>");
    } else if (btnAllPermisos.text().includes("Quitar todos los permisos")) {
        $(".collapse-permission").removeClass('show').addClass('hidden');
        $("input:checkbox").prop('checked', false);
        changeText("#btn-all-permisos", "Asignar todos los permisos <span class='fas fa-check'></span></a>");
    }
}

function setValoresAsignarPermisos(datos) {
    var array = datos.split("</tr>");
    var permisos = [
        "idusuario", "nombre",
        "facturas", "crearfactura", "editarfactura", "eliminarfactura", "listafactura", "timbrarfactura", //Timbrar
        "pago", "crearpago", "editarpago", "eliminarpago", "listapago", "timbrarpago", //Timbrar
        "nomina", "listaempleado", "crearempleado", "editarempleado", "eliminarempleado", "listanomina", "crearnomina", "editarnomina", "eliminarnomina", "timbrarnomina", //Timbrar
        "cartaporte", "listaubicacion", "crearubicacion", "editarubicacion", "eliminarubicacion", "listatransporte", "creartransporte", "editartransporte", "eliminartransporte", "listaremolque", "crearremolque", "editarremolque", "eliminarremolque", "listaoperador", "crearoperador", "editaroperador", "eliminaroperador", "listacarta", "crearcarta", "editarcarta", "eliminarcarta", "timbrarcarta", //Timbrar
        "cotizacion", "crearcotizacion", "editarcotizacion", "eliminarcotizacion", "listacotizacion","anticipo", "exportarfactura",//Exportar a factura 
        "cliente", "crearcliente", "editarcliente", "eliminarcliente", "listacliente",
        "comunicado", "crearcomunicado", "editarcomunicado", "eliminarcomunicado", "listacomunicado",
        "producto", "crearproducto", "editarproducto", "eliminarproducto", "listaproducto",
        "proveedor", "crearproveedor", "editarproveedor", "eliminarproveedor", "listaproveedor",
        "impuesto", "crearimpuesto", "editarimpuesto", "eliminarimpuesto", "listaimpuesto",
        "datosfacturacion", "creardatos", "editardatos", "listadatos", "eliminardatos", "descargardatos", //Eliminar, descargar archivos
        "contrato", "crearcontrato", "editarcontrato", "eliminarcontrato", "listacontrato",
        "usuarios", "crearusuario", "listausuario", "eliminarusuario", "asignarpermiso",
        "reporte", "reportefactura", "reportepago", "reportegrafica", "reporteiva", "datosiva", "reporteventas", "reporteinventario", "reportepuntoventa", "reportecorte",
        "configuracion", "addfolio", "listafolio", "editarfolio", "eliminarfolio", "addcomision", "encabezados", "confcorreo", "importar", 
        "ventas", "crearventa", "cancelarventa", "exportarventa", "listaventa", "registrarentrada", "registrarsalida", "cortedecaja",
        "instalaciongps", "creargps", "editargps", "eliminargps", "listagps", "crearinstalacion", "editarinicio", "editarpasos", "cancelarinstalacion", "eliminarinstalacion", "listainstalacion",
        "accion", "idlogin"
    ];

    var permisosMapa = {};

    for (var i = 0; i < permisos.length; i++) {
        permisosMapa[permisos[i]] = array[i];
    }
    changeText("#titulo-asignar", "Asignando permisos a: " + permisosMapa.nombre);

    var tienePermisos = false;
    for (var permiso in permisosMapa) {
        if (permisosMapa[permiso] == '1' && permiso !== 'idusuario' && permiso !== 'accion' && permiso !== 'idlogin') {
            $("#" + permiso).attr('checked', true);
            tienePermisos = true;
        }
    }

    if (tienePermisos) {
        $(".collapse-permission").removeClass('hidden').addClass('show');
        changeText("#btn-all-permisos", "Quitar todos los permisos <span class='fas fa-times'></span></a>");
    } else {
        $(".collapse-permission").removeClass('show').addClass('hidden');
        changeText("#btn-all-permisos", "Asignar todos los permisos <span class='fas fa-check'></span></a>");
    }


    $("#form-permisos").append("<input type='hidden' id='idlogin' value='" + permisosMapa.idlogin + "'/>");
    $("#form-permisos").append("<input type='hidden' id='accion' value='" + permisosMapa.accion + "'/>");
    $("#btn-guardar-permisos").attr("onclick", "actualizarPermisos(" + permisosMapa.idusuario + ");");
}

function actualizarPermisos(idusuario) {
    var idlogin = $("#idlogin").val();
    var accion = $("#accion").val();

    var categorias = {
        facturas: ["crearfactura", "editarfactura", "eliminarfactura", "listafactura", "timbrarfactura"],
        pago: ["crearpago", "editarpago", "eliminarpago", "listapago", "timbrarpago"],
        nomina: ["listaempleado", "crearempleado", "editarempleado", "eliminarempleado", "listanomina", "crearnomina", "editarnomina", "eliminarnomina", "timbrarnomina"],
        cartaporte: ["listaubicacion", "crearubicacion", "editarubicacion", "eliminarubicacion", "listatransporte", "creartransporte", "editartransporte", "eliminartransporte", "listaremolque", "crearremolque", "editarremolque", "eliminarremolque", "listaoperador", "crearoperador", "editaroperador", "eliminaroperador", "listacarta", "crearcarta", "editarcarta", "eliminarcarta", "timbrarcarta"],
        cotizacion: ["crearcotizacion", "editarcotizacion", "eliminarcotizacion", "listacotizacion", "anticipo", "exportarfactura"],
        cliente: ["crearcliente", "editarcliente", "eliminarcliente", "listacliente"],
        comunicado: ["crearcomunicado", "editarcomunicado", "eliminarcomunicado", "listacomunicado"],
        producto: ["crearproducto", "editarproducto", "eliminarproducto", "listaproducto"],
        proveedor: ["crearproveedor", "editarproveedor", "eliminarproveedor", "listaproveedor"],
        impuesto: ["crearimpuesto", "editarimpuesto", "eliminarimpuesto", "listaimpuesto"],
        datosfacturacion: ["creardatos", "editardatos", "listadatos", "eliminardatos", "descargardatos"],
        contrato: ["crearcontrato", "editarcontrato", "eliminarcontrato", "listacontrato"],
        usuarios: ["crearusuario", "listausuario", "eliminarusuario", "asignarpermiso"],
        reporte: ["reportefactura", "reportepago", "reportegrafica", "reporteiva", "datosiva", "reporteventas", "reporteinventario", "reportepuntoventa", "reportecorte"],
        configuracion: ["addfolio", "listafolio", "editarfolio", "eliminarfolio", "addcomision", "encabezados", "confcorreo", "importar"],
        ventas: ["crearventa", "cancelarventa", "exportarventa", "listaventa", "registrarentrada", "registrarsalida", "cortedecaja"],
        instalacion: ["creargps", "editargps", "eliminargps", "listagps", "crearinstalacion", "editarinicio", "editarpasos", "cancelarinstalacion", "eliminarinstalacion", "listainstalacion"]
    };

    var datos = {
        transaccion: "actualizarpermisos",
        idusuario: idusuario,
        accion: accion
    };

    for (var categoria in categorias) {
        if ($("#collapse-" + categoria).hasClass('show')) {
            var categoriaMarcada = false; 
            for (var i = 0; i < categorias[categoria].length; i++) {
                var permiso = categorias[categoria][i];
                var valorCheckbox = $("#" + permiso).prop('checked');
                datos[permiso] = valorCheckbox ? 1 : 0;
                if (valorCheckbox) {
                    categoriaMarcada = true;
                }
            }
            datos[categoria] = categoriaMarcada ? 1 : 0;
        } else {
            datos[categoria] = 0;
            for (var i = 0; i < categorias[categoria].length; i++) {
                var permiso = categorias[categoria][i];
                datos[permiso] = 0;
            }
        }
    }
    
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: datos,
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                if (idlogin != idusuario) {
                    loadView('listasuarioaltas');
                } else {
                    location.href = 'home.php';
                }
                alertify.success('Se guardaron los permisos correctamente');
            }
        }
    });
}

function resposiveButton() {
    if (window.innerWidth <= 1240) {
        $("#contenedor-button").removeClass('row').addClass('flex-column');
        $("#buno").removeClass('col-md-5').addClass('col-12 mb-3');
        $("#bdos").removeClass('col-md-6 row d-flex justify-content-end px-0 mx-0').addClass('col-12 mb-3');
        $("#bdos > div").removeClass('col-md-6 mb-3').addClass('col-12 mb-3');
    } else {
        $("#contenedor-button").removeClass('flex-column').addClass('row');
        $("#buno").removeClass('col-12 mb-3').addClass('col-md-5');
        $("#bdos").removeClass('col-12 mb-3').addClass('col-md-6 row d-flex justify-content-end px-0 mx-0');
        $("#bdos > div").removeClass('col-12 mb-3').addClass('col-md-6 mb-3');
    }
}

function scrollToTop(){
    document.getElementById("inicio-vista").scrollIntoView();
}