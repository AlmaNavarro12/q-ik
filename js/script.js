//Mostrar el spinner loading...
function cargandoShow() {
    $("body").append("<div id='contenedor-loader'></div>");
}

function cargandoHide() {
    $("#contenedor-loader").remove();
}

function changeText(elemento, texto) {
    $(elemento).html(texto);
}

function validarNum(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}

function validarLet(input) {
    input.value = input.value.replace(/[^A-Za-z. ]/g, '');
}

//Validacion de campos vacios
function isnEmpty(val, id) {
    if (val == "") {
        $("#" + id).css("border-color", "red");
        $("#" + id + "-errors").text("Este campo no puede estar vacío.");
        $("#" + id + "-errors").css("color", "red");
        $("#" + id).focus();
        return false;
    } else {
        $("#" + id + "-errors").text("");
        $("#" + id).css("border-color", "green");
        return true;
    }
}

function isPhoneNumber(val, id) {
    if (!isNaN(val)) {
        var n = val.toString();
        if (n.length > 6 && n.length < 11) {
            $("#" + id + "-errors").text("");
            $("#" + id).css("border-color", "green");
            return true;
        } else {
            $("#" + id).css("border-color", "red");
            $("#" + id + "-errors").text("Los números de teléfono deben tener entre 7 y 10 dígitos.");
            $("#" + id + "-errors").css("color", "red");
            $("#" + id).focus();
            return false;
        }
    }
}

function isEmail(val, id) {
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!expr.test(val)) {
        $("#" + id).css("border-color", "red");
        $("#" + id + "-errors").text("La dirección de correo (email) no es válida.");
        $("#" + id + "-errors").css("color", "red");
        $("#" + id).focus();
        return false;
    } else {
        $("#" + id + "-errors").text("");
        $("#" + id).css("border-color", "green");
        return true;
    }
}

function isNumber(val, id) {
    if (val != "") {
        if (!isNaN(val)) {
            if (val > 0) {
                $("#" + id + "-errors").text("");
                $("#" + id).css("border-color", "green");
                return true;
            } else {
                $("#" + id).css("border-color", "red");
                $("#" + id + "-errors").text("El número debe ser mayor a 0.");
                $("#" + id + "-errors").css("color", "red");
                $("#" + id).focus();
                return false;
            }
        }
    } else {
        $("#" + id).css("border-color", "red");
        $("#" + id + "-errors").text("Este campo no puede estar vacío.");
        $("#" + id + "-errors").css("color", "red");
        $("#" + id).focus();
        return false;
    }
}

//Función para ocultar el menu responsivo
function resetMenu() {
    if (window.innerWidth >= 700) {
        $('#main-menu').removeClass('content-menu2');
        $('.elipse').removeClass('elipse2');
        document.getElementById('menu-icon').style.display = 'none';
        document.getElementById('user-name').style.display = '';
        $('.user-info').removeClass('user-info2');
        $('#contenedor-vista-right').addClass('left-pad');
    } else if (window.innerWidth < 700) {
        $('#main-menu').addClass('content-menu2');
        $('.elipse').addClass('elipse2');
        document.getElementById('menu-icon').style.display = 'block';
        document.getElementById('user-name').style.display = 'none';
        $('.user-info').addClass('user-info2');
        $('#contenedor-vista-right').removeClass('left-pad');
    }
}

//JQuery Uso de menu y vistas en el contenedor right
$(function () {
    $("body").click(function (e) {
        if (window.innerWidth < 700) {
            if (e.target.id == "contenedor-vista-right" || $(e.target).parents("#contenedor-vista-right").length) {
                $('#main-menu').addClass('content-menu2');
                $('.elipse').addClass('elipse2');
            }
        }

    });
});

$(function () {
    $(".list-conf").click(function () {
        var submenu = $(this).attr("data-submenu");
        $('.list-element').removeClass("menu-active");
        $('.marker').removeClass("marker-active");
        $('.panel-collapse').removeClass("show");
        $('.lista-submenu-elemento').removeClass("sub-active");
        if (submenu != "") {
            loadView(submenu);
        }
    });
});

$(function () {
    $(".list-menu").click(function () {
        var submenu = $(this).attr("data-submenu");
        if (submenu != "") {
            loadView(submenu);
        }
    });
});

$(function () {
    $(".show-menu").click(function () {
        if ($('#main-menu').hasClass('content-menu2')) {
            $('#main-menu').removeClass('content-menu2');
            $('.elipse').removeClass('elipse2');
        } else {
            $('#main-menu').addClass('content-menu2');
            $('.elipse').addClass('elipse2');
        }
    });
});

$(function () {
    $(".list-element").click(function () {
        $('.list-element').removeClass("menu-active");
        $('.marker').removeClass("marker-active");
        if ($(this).hasClass("list-menu")) {
            $('.panel-collapse').removeClass("show");
            $('.lista-submenu-elemento').removeClass("sub-active");
        }
        $(this).addClass("menu-active");
        $(this).children('div.marker').addClass("marker-active");
    });
});

$(function () {
    $(".lista-submenu-elemento").click(function () {
        $('.lista-submenu-elemento').removeClass("sub-active");
        $(this).addClass("sub-active");
    });
});

//Cargar la vista en el lado derecho
function getView(view) {
    $.ajax({
        url: 'com.sine.enlace/enlaceenrutador.php',
        type: 'POST',
        data: {transaccion: "cargarvista", view: view},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var resto = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(resto);
            } else {
                $("#contenedor-vista-right").html('');
                $("#contenedor-vista-right").html(datos);
            }
        }
    });
}

//Funcion para cargar vista
function loadView(vista) {
    getView(vista);

    const actions = { //Mapa de las vistas, las claves son el nombre de la vista y cada clave almacena un array
        'paginicio': ["getUserFirstSession()", 300, "getSaldo()", 350, "datosGrafica()", 400, "loadopcionesAno()", 450],
        'notificacion': ["filtrarNotificaciones()", 350],
        'comprar': [],
        'nuevousuario': ["checkUsuario()", 350, /** "truncateTmp()", 400, "truncateTmpCot()", 400, "loadOpcionesEstado()", 450 */],
        'listasuarioaltas': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('usuario')", 370, "filtrarUsuario()", 400],
        'asignarpermisos': ["truncateTmp()", 300, "truncateTmpCot()", 350],
        'categoria': [],
        'listacategoria': ["loadBtnCrear('categoria')", 360, "loadListaCategorias()", 500],
        'nuevoproducto': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadOpcionesProveedor()", 350],
        'listaproductoaltas': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('producto')", 370, "loadListaProductosaltas()", 400],
        'valrfc': [],
        'nuevocliente': ["truncateTmpCot()", 350, "truncateTmp()", 400, "loadOpcionesEstado()", 420, "loadOpcionesBanco()", 450],
        'listaclientealtas': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('cliente')", 370, "loadListaClientesAltas()", 400],
        'comunicado': ["truncateTmpIMG()", 300, "loadFecha()", 350, "loadOpcionesFacturacion()", 400, "loadContactos()", 420],
        'listacomunicado': ["truncateTmpIMG()", 300, "loadBtnCrear('comunicado')", 350, "listaComunicados()", 400],
        'insertar': ["truncateTmp()", 350, "truncateTmpCot()", 400],
        'cfdi': ["truncateTmp()", 400],
        'impuesto': [],
        'listaimpuesto': ["loadBtnCrear('impuesto')", 350, "loadListaImpuesto()", 400],
        'datosempresa': ["firmaCanvas()", 400, "loadOpcionesBanco()", 400, "loadOpcionesEstado()", 500],
        'nuevocontrato': ["truncateTmpCot()", 300, "loadOpcionesFolios()", 320, "filtrarProductos()", 350, "loadFecha()", 370, "loadOpcionesFormaPago()", 400, "loadOpcionesMetodoPago()", 420, "loadOpcionesMoneda()", 450, "loadOpcionesUsoCFDI()", 470, "loadOpcionesFacturacion()", 500, "loadOpcionesProveedor()", 520],
        'precio': ["truncateTmp()", 400, "truncateTmpCot()", 450],
        'pago': ["loadFecha()", 300, "cancelarPago2()", 320, "loadOpcionesFolios('3')", 350, "loadOpcionesMoneda()", 400, "loadOpcionesFormaPago2()", 420, "loadOpcionesFacturacion()", 500],
        'listapago': ["loadBtnCrear('pago')", 350, "opcionesMotivoCancelar()", 380, "loadListaPago()", 400],
        'factura': ["truncateTmp()", 300, "loadOpcionesFacturacion()", 320, "loadFecha()", 350, "loadOpcionesFolios('1')", 370, "filtrarProducto()", 400, "loadOpcionesFormaPago()", 420, "loadOpcionesMetodoPago()", 450, "loadOpcionesMoneda()", 470, "loadOpcionesUsoCFDI()", 500, "loadOpcionesComprobante()", 520, "loadOpcionesProveedor()", 550, "loadOpcionesTipoRelacion()", 570, "opcionesPeriodoGlobal()", 600, "opcionesMeses()", 620, "opcionesAnoGlobal()", 650],
        'listafactura': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('factura')", 400, "opcionesMotivoCancelar()", 420, "filtrarFolio()", 450],
        'cotizacion': ["truncateTmpCot()", 300, "loadOpcionesImpuestos('1')", 320, "loadOpcionesImpuestos('2')", 340, "loadOpcionesFolios('5')", 350, "loadFecha()", 370, "loadOpcionesFacturacion()", 400, "loadOpcionesComprobante()", 420, "loadOpcionesFormaPago()", 450, "loadOpcionesMetodoPago()", 470, "loadOpcionesMoneda()", 500, "loadOpcionesUsoCFDI()", 520, "filtrarProducto() ", 550, "loadOpcionesProveedor()", 600],
        'listacotizacion': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('cotizacion')", 360, "filtrarCotizacion()", 400],
        'instalacion': ["truncateTmp()", 350, "truncateTmpCot()", 400, "loadFolio()", 430, "loadDocumento()", 450, "loadFecha()", 500],
        'listainstalacion': ["truncateTmp()", 350, "truncateTmpCot()", 400, "filtrarInstalacion() ", 500],
        'listacontratos': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('contrato')", 370, "filtrarContratos()", 400],
        'listaempresa': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('datos')", 370, "loadListaEmpresa()", 400],
        'listacfdi': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadListaCFDI()", 400],
        'nuevoproveedor': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadOpcionesBanco()", 400],
        'listaproveedor': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadBtnCrear('proveedor')", 370, "loadListaProveedor()", 400],
        'forminventario': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadOpcionesProducto()", 400],
        'listainventario': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadListaInventario()", 400],
        'reportefactura': ["truncateTmp()", 300, "truncateTmpCot()", 350, "loadOpcionesCliente()", 400, "loadOpcionesFacturacion()", 450, "loadOpcionesMoneda()", 470],
        'reportepago': ["loadOpcionesCliente()", 400, "loadOpcionesFacturacion()", 450, "loadOpcionesMoneda()", 470],
        'reportegrafica': ["loadOpcionesFacturacion()", 350, "loadopcionesAno()", 400, "reporteGraficaActual()", 450, "reporteGraficaAnterior()", 500],
        'reportesat': ["loadopcionesAno()", 400, "loadOpcionesFacturacion()", 450, "reporteBimestralActual()", 500, "reporteBimestralAnterior()", 550, "reporteBimestralAnterior2()", 600],
        'datosiva': ["loadopcionesAno()", 350, "loadlistaIVA()", 400, "loadOpcionesFacturacion()", 450],
        'reporteventas': ["truncateTmp()", 400, "truncateTmpCot()", 450, "loadOpcionesCliente()", 500, "loadOpcionesFacturacion()", 500, "loadOpcionesVendedor()", 500],
        'config': ["loadBtnConfig('config')", 350],
        'encabezado': [],
        'correo': ["opcionesCorreo()", 300],
        'folio': [],
        'listafolio': ["loadListaFolio()", 400],
        'comision': ["loadOpcionesUsuario()", 400],
        'listafiel': ["loadListaFiel()", 300],
        'nuevafiel': [],
        'listadescsolicitud': ["loadListaSolicitud()", 400],
        'descsolicitud': [],
        'empleado': ["loadOpcionesRegimen()", 300, "loadOpcionesPeriodicidad()", 310, "loadOpcionesJornada()", 320, "loadOpcionesContrato()", 330, "loadOpcionesEstado()", 330, "loadOpcionesBanco()", 340, "loadOpcionesRiesgo()", 350],
        'listaempleado': ["loadBtnCrear('empleado')", 300, "loadListaEmpleado()", 310],
        'nomina': ["loadFecha()", 300, "loadOpcionesFacturacion()", 310, "loadOpcionesRegimen()", 320, "listaPercepciones()", 330, "listaDeducciones()", 340, "listaOtrosPagos()", 350, "optionListPercepciones()", 360, "optionListDeducciones()", 370, "optionListOtrosPagos()", 380],
        'listanomina': ["loadBtnCrear('nomina')", 300, "filtrarFolio()", 320],
        'direccion': ["loadOpcionesEstado()", 320],
        'listadireccion': ["loadBtnCrear('destino')", 300, "filtrarUbicacion()", 320],
        'transporte': [],
        'listatransporte': ["loadBtnCrear('transporte')", 300, "filtrarTransporte()", 320],
        'remolque': [],
        'listaremolque': ["loadBtnCrear('remolque')", 300, "filtrarRemolque()", 320],
        'operador': ["loadOpcionesEstado()", 320],
        'listaoperador': ["loadBtnCrear('operador')", 300, "filtrarOperador()", 320],
        'carta': ["truncateTmpCarta()", 300, "truncateTmpIMG()", 320, "loadOpcionesFolios('4')", 350, "loadFecha()", 370, "loadOpcionesEstado()", 400, "filtrarProducto()", 420, "loadOpcionesFormaPago()", 450, "loadOpcionesMetodoPago()", 470, "loadOpcionesMoneda()", 500, "loadOpcionesUsoCFDI()", 520, "loadOpcionesComprobante()", 550, "loadOpcionesFacturacion()", 570, "loadOpcionesProveedor()", 600, "opcionesPeriodoGlobal()", 620, "opcionesMeses()", 650, "opcionesAnoGlobal()", 670],
        'listacarta': ["truncateTmpCarta()", 300, "truncateTmpIMG()", 300, "loadBtnCrear('carta')", 300, "filtrarCarta()", 320, "opcionesMotivoCancelar()", 350],
        'puntosdeventa': [],
    };

    if (actions[vista]) {
        const timeouts = actions[vista]; 
        for (let i = 0; i < timeouts.length; i += 2) { 
            window.setTimeout(timeouts[i], timeouts[i + 1]);
        }
    }
}

function loadBtnCrear(view) {
    $.ajax({
        url: "com.sine.enlace/enlacepermiso.php",
        type: "POST",
        data: {transaccion: "loadbtn", view: view},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            $("#btn-crear").html(datos);
            cargandoHide();
        }
    });
}

function logout() {
    cargandoShow();
    $.ajax({
        url: 'com.sine.enlace/enlacesession.php',
        type: 'POST',
        data: {transaccion: 'logout'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                if (datos == 'salir') {
                    location.href = 'index.php';
                } else {
                    alertify.error(res);
                }
            }
            cargandoHide();
        }
    });
}

$(function () {
    $(document).on('click', '.dropdown-menu li', function (event) {
        var $checkbox = $(this).find('.checkbox');
        var id = $(this).attr("data-id");
        var location = $(this).attr("data-location");

        if ($checkbox.length) {
            var $input = $checkbox.find('input');
            var $icon = $checkbox.find('span.far');
            
            $input.prop('checked', !$input.is(':checked'));
            $icon.toggleClass('far fa-square far fa-check-square');

            switch (location) {
                case 'lista':
                    calcularDescuento(id);
                    break;
                case 'tabla':
                    checkIVA(id);
                    break;
                case 'edit':
                    calcularDescuentoEditar();
                    break;
                case 'percepcion':
                    selectedPercepciones();
                    break;
                case 'deduccion':
                    selectedDeduccion();
                    break;
                case 'otrospagos':
                    selectedOtrosPagos();
                    break;
                case 'form':
                    calcularDescuentoObra();
                    break;
            }
        }
        return false;
    });
});

function loadImgPerfil(id) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "editarusuario", idusuario: id},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                setImgUsuario(datos);
            }
        }
    });
}

function setImgUsuario(datos) {
    var array = datos.split("</tr>");
    var idusuario = array[0];
    var idlogin = array[11];
    var tipologin = array[12];
    var imgnm = array[13];
    var img = array[14];

    if (imgnm !== '') {
        $("#profimg").html(img);
        $("#fileuser").val(imgnm);
    }

    $("#form-profile").append("<input type='hidden' id='profactualizar' name='profactualizar' value='" + imgnm + "'/>")
    $("#btn-edit-profile").attr("onclick", "editarPerfil(" + idusuario + ");");
    $("#btn-form-profile").attr("onclick", "actualizarImgPerfil(" + idusuario + ");");
    cargandoHide();
}

function actualizarImgPerfil(idusuario) {
    var img = $("#fileuser").val();
    var imgactualizar = $("#profactualizar").val();
    $.ajax({
        url: "com.sine.enlace/enlaceusuario.php",
        type: "POST",
        data: {transaccion: "actualizarimg", idusuario: idusuario, img: img, imgactualizar: imgactualizar},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                alertify.success('Se guardaron los datos correctamente ');
                location.href = 'home.php';
            }
        }
    });
}

function cargarImgPerfil() {
    var formData = new FormData(document.getElementById("form-profile"));
    var img = $("#imgprof").val();
    if (isnEmpty(img, 'imgprof')) {
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
                $("#profimg").html(view);
                $("#fileuser").val(fn);
                $("#imgprof").val('');
            }
        });
    }
}

function editarPerfil(idusuario) {
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
                $("#modal-profile-img").modal('hide');
                window.setTimeout("setValoresEditarUsuario('" + datos + "')", 400);
            }
        }
    });
}

function getNombreUsuario() {
    $("#nombre-soporte").val('');
    $("#telefono-soporte").val('');
    $("#check-soporte").removeAttr('checked');
    $("#correo-soporte").val('');
    $("#mensaje-soporte").val('');

    $.ajax({
        url: 'com.sine.enlace/enlaceinicio.php',
        type: 'POST',
        data: {transaccion: 'getnombre'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                var nombre = array[0];
                var telefono = array[1];
                var mail = array[2];

                $("#nombre-soporte").val(nombre);
                $("#telefono-soporte").val(telefono);
                $("#correo-soporte").val(mail);
            }
        }
    })
}

function enviarSoporte() {
    var nombre = $("#nombre-soporte").val();
    var telefono = $("#telefono-soporte").val();
    var correo = $("#correo-soporte").val();
    var msg = $("#mensaje-soporte").val();
    var txtbd = msg.replace(new RegExp("\n", 'g'), '<ntr>');
    var chwhats = 0;
    if ($("#check-soporte").prop('checked')) {
        chwhats = 1;
    }
    if (isnEmpty(nombre, "nombre-soporte") && isnEmpty(telefono, "telefono-soporte") && isnEmpty(correo, "correo-soporte") && isnEmpty(msg, "mensaje-soporte")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/enlaceinicio.php',
            type: 'POST',
            data: {transaccion: 'sendsoporte', nombre: nombre, telefono: telefono, chwhats: chwhats, correo: correo, msg: txtbd},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(res);
                }
                cargandoHide();
            }
        })
    }
}