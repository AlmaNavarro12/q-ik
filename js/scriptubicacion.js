function aucompletarLocalidad() {
    $('#clv-localidad').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=localidad",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function insertarUbicacion(uid = null) {
    var tipo = $("#tipo-ubicacion").val();
    var nombre = $("#nombre-destino").val();
    var rfc = $("#rfc-destino").val();
    var calle = $("#calle-destino").val();
    var numext = $("#num-exterior").val();
    var numint = $("#num-interior").val();
    var cp = $("#codigo_postal").val();
    var referencia = $("#referencia").val();
    var idestado = $("#id-estado").val() || '0';
    var nombreestado = $("#id-estado option:selected").text().substring(4);
    var idmunicipio = $("#id-municipio").val() || '0';
    var nombremunicipio = $("#id-municipio option:selected").text();
    var localidad = $("#clv-localidad").val();
    var colonia = $("#clv-colonia").val();

    if (isnEmpty(tipo, "tipo-ubicacion") && isnEmpty(nombre, "nombre-destino") && isnEmpty(rfc, "rfc-destino") && isnEmpty(cp, "cod-postal") && isnEmpty(idestado, "id-estado")) {
        cargandoHide();
        cargandoShow();
        var url = "com.sine.enlace/enlaceubicacion.php";
        var transaccion = (uid != null) ? "actualizarubicacion" : "insertarubicacion";
        
        var data = {
            transaccion: transaccion,
            uid: uid,
            tipo: tipo,
            nombre: nombre,
            rfc: rfc,
            calle: calle,
            numext: numext,
            numint: numint,
            cp: cp,
            referencia: referencia,
            idestado: idestado,
            nombreestado: nombreestado,
            idmunicipio: idmunicipio,
            nombremunicipio: nombremunicipio,
            localidad: localidad,
            colonia: colonia
        };
        
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var mensaje = uid ? 'Ubicación actualizada.' : 'Ubicación registrada.';
                    alertify.success(mensaje);
                    loadView('listadireccion');
                }
                cargandoHide();
            }
        });
    }
}

function buscarUbicacion(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlaceubicacion.php",
        type: "POST",
        data: {transaccion: "filtrarubicacion", REF: $("#buscar-destino").val(), tipo:  $("#tipo-reg").val(), pag: pag, numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-ubicacion").html(datos);
            }
        }
    });
}

function editarUbicacion(uid) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceubicacion.php",
        type: "POST",
        data: {transaccion: "editarubicacion", uid: uid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('direccion');
                window.setTimeout("setValoresEditarUbicacion('" + datos + "')", 600);
            }
        }
    });
}

function setValoresEditarUbicacion(datos) {
    changeText("#contenedor-titulo-form-ubicacion", "Editar ubicación");
    changeText("#btn-form-ubicacion", "Guardar cambios <span class='fas fa-save'></span>");
    var array = datos.split("</tr>");
    $("#tipo-ubicacion").val(array[1]);
    $("#nombre-destino").val(array[2]);
    $("#rfc-destino").val(array[3]);
    $("#calle-destino").val(array[4]);
    $("#num-exterior").val(array[5]);
    $("#num-interior").val(array[6]);
    $("#codigo_postal").val(array[7]);
    $("#referencia").val(array[8]);
    loadOpcionesEstado('contenedor-estado', 'id-estado', array[9]);
    loadOpcionesMunicipio(array[10], array[9]);

    $("#clv-localidad").val(array[11]);
    $("#clv-colonia").val(array[12]);
    $("#btn-form-ubicacion").attr("onclick", "insertarUbicacion(" +  array[0] + ");");
    cargandoHide();
}

function eliminarUbicacion(uid) {
    alertify.confirm("¿Estás seguro que deseas eliminar esta ubicación?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceubicacion.php",
            type: "POST",
            data: {transaccion: "eliminarubicacion", uid: uid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se eliminó correctamente una ubicación.');
                    buscarUbicacion();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}