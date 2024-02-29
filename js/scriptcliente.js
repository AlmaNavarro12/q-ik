$('#datosficales').on('click', function () { 
    if ($('#datosficales').prop('checked')) {
        $("#fiscales").show(400);
    } else {
        $('.hideable').hide(400);
    }
});

function gestionarCliente(idcliente = null) {
    var nombre = $("#nombre").val();
    var apellidopaterno = $("#apellido-paterno").val();
    var apellidomaterno = $("#apellido-materno").val();
    var nombre_empresa = $("#nombre_empresa").val();
    var correoinfo = $("#correo_info").val();
    var correo_fact = $("#correo_fact").val();
    var correo_gerencia = $("#correo_gerencia").val();
    var telefono = $("#telefono").val();
    var correoalt1 = $("#correo_alt1").val();
    var correoalt2 = $("#correo_alt2").val();
    var correoalt3 = $("#correo_alt3").val();
    var rfc = 'XAXX010101000';
    var razon = nombre + ' ' + apellidopaterno + ' ' + apellidomaterno;
    var regimenfiscal = "616 - Sin obligaciones fiscales";
    var calle = null;
    var interior = null;
    var exterior = null;
    var estado = null;
    var municipio = null;
    var localidad = null;
    var postal = null;
    var idbanco = $("#id-banco").val();
    var cuenta = $("#cuenta").val();
    var clabe = $("#clabe").val();
    var idbanco1 = $("#id-banco1").val();
    var cuenta1 = $("#cuenta1").val();
    var clabe1 = $("#clabe1").val();
    var idbanco2 = $("#id-banco2").val();
    var cuenta2 = $("#cuenta2").val();
    var clabe2 = $("#clabe2").val();
    var idbanco3 = $("#id-banco3").val();
    var cuenta3 = $("#cuenta3").val();
    var clabe3 = $("#clabe3").val();

    if ($("#datosficales").prop('checked')) {
        rfc = $("#rfc").val();
        razon = $("#razon_social").val();
        regimenfiscal = $("#regimen-fiscal").val();
        calle = $("#calle").val();
        interior = $("#num_interior").val();
        exterior = $("#num_exterior").val();
        estado = $("#id-estado").val();
        municipio = $("#id-municipio").val();
        localidad = $("#localidad").val();
        postal = $("#codigo_postal").val();
    }

    if (isnEmpty(nombre, "nombre") &&
        isnEmpty(apellidopaterno, "apellido-paterno") &&
        isnEmpty(apellidomaterno, "apellido-materno") &&
        isnEmpty(nombre_empresa, "nombre_empresa") &&
        isEmail(correo_fact, "correo_fact") &&
        isPhoneNumber(telefono, "telefono") &&
        isnEmpty(rfc, "rfc") &&
        isnEmpty(razon, "razon_social") &&
        isnEmpty(regimenfiscal, "regimen-fiscal") &&
        isnEmpty(calle, "calle") &&
        isnEmpty(exterior, "num_exterior") &&
        isnEmpty(estado, "id-estado") &&
        isnEmpty(municipio, "id-municipio") &&
        isnEmpty(localidad, "localidad") &&
        isnEmpty(postal, "codigo_postal")) {

        cargandoHide();
        cargandoShow();

        var url = "com.sine.enlace/enlacecliente.php";
        var transaccion = idcliente ? "actualizarcliente" : "insertarcliente";
        var data = {
            transaccion: transaccion,
            idcliente: idcliente,
            nombre: nombre,
            apellidopaterno: apellidopaterno,
            apellidomaterno: apellidomaterno,
            nombre_empresa: nombre_empresa,
            correoinfo: correoinfo,
            correo_fact: correo_fact,
            correo_gerencia: correo_gerencia,
            telefono: telefono,
            rfc: rfc,
            razon: razon,
            regimenfiscal: regimenfiscal,
            calle: calle,
            interior: interior,
            exterior: exterior,
            estado: estado,
            municipio: municipio,
            localidad: localidad,
            postal: postal,
            idbanco: idbanco,
            cuenta: cuenta,
            clabe: clabe,
            idbanco1: idbanco1,
            cuenta1: cuenta1,
            clabe1: clabe1,
            idbanco2: idbanco2,
            cuenta2: cuenta2,
            clabe2: clabe2,
            idbanco3: idbanco3,
            cuenta3: cuenta3,
            clabe3: clabe3,
            correoalt1: correoalt1, 
            correoalt2: correoalt2, 
            correoalt3: correoalt3
        };

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function(datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success("Cliente " + (transaccion === 'insertarcliente' ? 'registrado' : 'actualizado') + " correctamente");
                    loadView('listaclientealtas');
                }
                cargandoHide();
            }
        });
    }
}

function buscarCliente(pag = "") {
    var REF = $("#buscar-cliente").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacecliente.php",
        type: "POST",
        data: { transaccion: "filtrarcliente", REF: REF, pag: pag, numreg: numreg },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-clientes").html(datos);
            }
        }
    });
}

function loadListaClientesAltas(pag = "") {
    cargandoHide();
    cargandoShow();
    var REF = $("#buscar-cliente").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacecliente.php",
        type: "POST",
        data: { transaccion: "filtrarcliente", REF: REF, pag: pag, numreg: numreg },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-clientes").html(datos);
                cargandoHide();
            }
        }
    });
}

function editarCliente(idcliente) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecliente.php",
        type: "POST",
        data: { transaccion: "editarcliente", idcliente: idcliente },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('nuevocliente');
                window.setTimeout("setValoresEditarCliente('" + datos + "')", 500);
            }
        }
    });
}

function setValoresEditarCliente(datos) {
    changeText("#contenedor-titulo-form-cliente", "Editar cliente");
    var array = datos.split("</tr>");
    $("#id-cliente").val(array[0]);
    $("#nombre").val(array[1]);
    $("#apellido-paterno").val(array[2]);
    $("#apellido-materno").val(array[3]);
    $("#nombre_empresa").val(array[4]);
    $("#correo_info").val(array[5]);
    $("#correo_fact").val(array[6]);
    $("#correo_gerencia").val(array[7]);
    $("#telefono").val(array[8]);
    $("#rfc").val(array[9]);
    $("#razon_social").val(array[10]);
    $("#regimen-fiscal").val(array[11]);
    $("#calle").val(array[12]);
    $("#num_interior").val(array[13]);
    $("#num_exterior").val(array[14]);

    if (array[15] != '0') {
        loadOpcionesEstado(array[15]);
    }

    if (array[16] != '0') {
        loadOpcionesMunicipio(array[16], array[15]);
    }

    if (array[19] !== '0') {
        loadOpcionesBanco("id-banco", array[19]);
        $("#cuenta").val(array[20]);
        $("#clabe").val(array[21]);
    }

    $("#localidad").val(array[17]);
    $("#codigo_postal").val(array[18]);
    $("#correo_alt1").val(array[31]);
    $("#correo_alt2").val(array[32]);
    $("#correo_alt3").val(array[33]);

    inicializarCampos(array[22], array[23], array[24], 1);
    inicializarCampos(array[25], array[26], array[27], 2);
    inicializarCampos(array[28], array[29], array[30], 3);

    changeText("#btn-form-cliente-guardar", "Guardar cambios <span class='fas fa-save'></span></a>");
    $("#btn-form-cliente-guardar").attr("onclick", "gestionarCliente("+array[0]+");");
    cargandoHide();
}

function eliminarCliente(idcliente) {
    alertify.confirm("¿Estás seguro que quieres eliminar este cliente?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecliente.php",
            type: "POST",
            data: { transaccion: "eliminarcliente", idcliente: idcliente },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    cargandoHide();
                    loadView('listaclientealtas');
                    alertify.success('Cliente elmininado.')
                }
            }
        });
    }).set({ title: "Q-ik" });
}

var renglon = 0;
function confirmarEliminacion(id) {
    alertify.confirm("¿Estás seguro que quieres eliminar los campos de esta cuenta?", function (e) {
        if (e) {
            quitarCampo(id);
        }
    }).set({ title: "Q-ik" });
}

function quitarCampo(id) {
        $("#id-banco" + id).val("");
        $("#cuenta" + id).val("");
        $("#clabe" + id).val("");

        //Se esconde el addcuentas segun el id
        $("#addcuentas"+ id).attr("hidden", true);
        renglon--;
}

function nuevoCampo() {
    renglon++;
    if (renglon > 3) {
        renglon = 1; //Si pasa 3, hay que iniciar de nuevo
    }

    var campoActual = "#addcuentas" + renglon;

    // Si el campo está oculto, mostrarlo
    if ($(campoActual).is(":hidden")) {
        $(campoActual).attr("hidden", false);
        console.log(renglon);
        loadOpcionesBanco("contenedor-banco"+renglon, "");
    }
}

function inicializarCampos(idbanco, cuenta, clabe, i) {
    if (idbanco !== '0') {
        $("#addcuentas" + i).attr('hidden', false);
        loadOpcionesBanco('id-banco'+i, idbanco);
        $("#cuenta" + i).val(cuenta);
        $("#clabe" + i).val(clabe);
        renglon = i;
        $("#rmvcuentas").removeAttr('disabled');
    }
}
