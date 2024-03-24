function insertarTransporte(tid = null) {
    var nombre = $("#nombre-unidad").val();
    var numpermiso = $("#num-permiso").val();
    var tipopermiso = $("#tipo-permiso").val();
    var conftransporte = $("#conf-transporte").val();
    var anho = $("#anho-modelo").val();
    var placa = $("#placa-vehiculo").val();
    var segurorc = $("#seguro-respcivil").val();
    var polizarc = $("#poliza-respcivil").val();
    var seguroma = $("#seguro-medioambiente").val();
    var polizama = $("#poliza-medioambiente").val();
    var segurocg = $("#seguro-carga").val();
    var polizacg = $("#poliza-carga").val();

    if (isnEmpty(nombre, "nombre-unidad") && isnEmpty(numpermiso, "num-permiso") && isnEmpty(tipopermiso, "tipo-permiso") && isnEmpty(conftransporte, "conf-transporte") && isPositive(anho, "anho-modelo") && isnEmpty(placa, "placa-vehiculo") && isnEmpty(segurorc, "seguro-respcivil") && isnEmpty(polizarc, "poliza-respcivil")) {
        cargandoHide();
        cargandoShow();
        var transaccion = (tid != null) ? "actualizartransporte" : "insertartransporte";
        var data = {
            transaccion: transaccion,
            tid: tid,
            nombre: nombre,
            numpermiso: numpermiso,
            tipopermiso: tipopermiso,
            conftransporte: conftransporte,
            anho: anho,
            placa: placa,
            segurorc: segurorc,
            polizarc: polizarc,
            seguroma: seguroma,
            polizama: polizama,
            segurocg: segurocg,
            polizacg: polizacg
        };

        $.ajax({
            url: "com.sine.enlace/enlacetransporte.php",
            type: "POST",
            data: data,
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var mensaje = tid ? 'Transporte actualizado.' : 'Transporte registrado.';
                    loadView('listatransporte');
                    alertify.success(mensaje);
                }
                cargandoHide();
            }
        });
    }
}


function buscarTransporte(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlacetransporte.php",
        type: "POST",
        data: {transaccion: "filtrartransporte", REF: $("#buscar-transporte").val(), pag: pag, numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-transporte").html(datos);
                cargandoHide();
            }
        }
    });
}

function editarTransporte(tid) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacetransporte.php",
        type: "POST",
        data: {transaccion: "editartransporte", tid: tid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('transporte');
                window.setTimeout("setValoresEditarTransporte('" + datos + "')", 600);
            }
        }
    });
}

function setValoresEditarTransporte(datos) {
    changeText("#contenedor-titulo-form-transporte", "Editar transporte");
    changeText("#btn-form-transporte", "Guardar cambios <span class='fas fa-save'></span>");
    var array = datos.split("</tr>");
    $("#nombre-unidad").val(array[1]);
    $("#num-permiso").val(array[2]);
    $("#tipo-permiso").val(array[3]);
    $("#conf-transporte").val(array[4]);
    $("#anho-modelo").val(array[5]);
    $("#placa-vehiculo").val(array[6]);
    $("#seguro-respcivil").val(array[7]);
    $("#poliza-respcivil").val(array[8]);
    $("#seguro-medioambiente").val(array[9]);
    $("#poliza-medioambiente").val(array[10]);
    $("#seguro-carga").val(array[11]);
    $("#poliza-carga").val(array[12]);
    $("#btn-form-transporte").attr("onclick", "insertarTransporte(" + array[0] + ");");
    cargandoHide();
}

function eliminarVehiculo(tid) {
    alertify.confirm("¿Estás seguro que deseas eliminar este transporte?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacetransporte.php",
            type: "POST",
            data: {transaccion: "eliminartransporte", tid: tid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se elimino correctamente los datos del transporte');
                    loadView('listatransporte');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

//------------------------------------------------------REMOLQUE
function insertarRemolque(rid = null) {
    var nombre = $("#nombre-remolque").val();
    var tiporemolque = $("#tipo-remolque").val();
    var placa = $("#placa-remolque").val();

    if (isnEmpty(nombre, "nombre-remolque") && isnEmpty(tiporemolque, "tipo-remolque") && isnEmpty(placa, "placa-remolque")) {
        cargandoHide();
        cargandoShow();
        var url = "com.sine.enlace/enlacetransporte.php";
        var transaccion = (rid != null) ? "actualizarremolque" : "insertarremolque";
        
        var data = {
            transaccion: transaccion,
            rid: rid,
            nombre: nombre,
            tiporemolque: tiporemolque,
            placa: placa
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
                    var mensaje = rid ? 'Datos de remolque actualizados' : 'Datos de remolque registrados';
                    alertify.success(mensaje);
                    loadView('listaremolque');
                }
                cargandoHide();
            }
        });
    }
}


function buscarRemolque(pag = "") {
    var REF = $("#buscar-placa").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacetransporte.php",
        type: "POST",
        data: {transaccion: "filtrarremolque", REF: REF, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-remolque").html(datos);
                cargandoHide();
            }
        }
    });
}

function editarRemolque(rid) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacetransporte.php",
        type: "POST",
        data: {transaccion: "editarremolque", rid: rid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('remolque');
                window.setTimeout("setValoresEditarRemolque('" + datos + "')", 600);
            }
        }
    });
}

function setValoresEditarRemolque(datos) {
    changeText("#contenedor-titulo-form-remolque", "Editar remolque");
    changeText("#btn-form-remolque", "Guardar cambios <span class='fas fa-save'></span>");
    var array = datos.split("</tr>");
    $("#nombre-remolque").val(array[1]);
    $("#tipo-remolque").val( array[2]);
    $("#placa-remolque").val(array[3]);
    $("#btn-form-remolque").attr("onclick", "insertarRemolque(" +  array[0] + ");");
    cargandoHide();

}

function eliminarRemolque(rid) {
    alertify.confirm("¿Estás seguro que deseas eliminar este remolque?", function () {
        $.ajax({
            url: "com.sine.enlace/enlacetransporte.php",
            type: "POST",
            data: {transaccion: "eliminarremolque", rid: rid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se eliminó correctamente los datos del remolque.');
                    loadView('listaremolque');
                }
            }
        });
    }).set({title: "Q-ik"});
}