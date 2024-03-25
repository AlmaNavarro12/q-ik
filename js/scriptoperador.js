function autocompletarEmpleado() {
    $('#nombre-operador').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=empleado",
        select: function (event, ui) {
            cargandoHide();
            cargandoShow();
            var a = ui.item.value;
            var nombre = ui.item.nombre;
            var rfc = ui.item.rfc;
            window.setTimeout("setvaloresOperador('" + nombre + "', '" + rfc + "')", 100);
        }
    });
}

function setvaloresOperador(nombre, rfc) {
    var array = nombre.split(" ");
    var nmoperador = array[0];
    var apaterno = array[1];
    var amaterno = array[2];
    if (array[3]) {
        nmoperador = array[0] + " " + array[1];
        apaterno = array[2];
        amaterno = array[3];
    }
    $("#nombre-operador").val(nmoperador);
    $("#apaterno-operador").val(apaterno);
    $("#amaterno-operador").val(amaterno);
    $("#rfc-operador").val(rfc);
    cargandoHide();
}

function insertarOperador(oid = null) {
    var nombre = $("#nombre-operador").val();
    var apaterno = $("#apaterno-operador").val();
    var amaterno = $("#amaterno-operador").val();
    var numlicencia = $("#num-licencia").val();
    var rfc = $("#rfc-operador").val();
    var empresa = $("#empresa-operador").val();
    var idestado = $("#id-estado").val() || '0';
    var nombreestado = $("#id-estado option:selected").text().substring(6);
    var idmunicipio = $("#id-municipio").val() || '0';
    var nombremunicipio = $("#id-municipio option:selected").text();
    var calle = $("#calle-operador").val();
    var cp = $("#codigo_postal").val();

    if (isnEmpty(nombre, "nombre") && isnEmpty(apaterno, "apaterno-operador") && isnEmpty(amaterno, "amaterno-operador") && isnEmpty(numlicencia, "num-licencia") && isnEmpty(rfc, "rfc-operador") && isnEmpty(idestado, "id-estado") && isnEmpty(cp, "codigo_postal")) {
        cargandoHide();
        cargandoShow();
        var url = "com.sine.enlace/enlaceoperador.php";
        var transaccion = (oid !== null) ? "actualizaroperador" : "insertaroperador";

        var data = {
            transaccion: transaccion,
            oid: oid,
            nombre: nombre,
            apaterno: apaterno,
            amaterno: amaterno,
            numlicencia: numlicencia,
            rfc: rfc,
            empresa: empresa,
            idestado: idestado,
            nombreestado: nombreestado,
            idmunicipio: idmunicipio,
            nombremunicipio: nombremunicipio,
            calle: calle,
            cp: cp
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
                    var mensaje = oid ? "Datos Actualizados" : "Datos registrados";
                    alertify.success(mensaje);
                    loadView("listaoperador");
                }
                cargandoHide();
            }
        });
    }
}

function filtrarOperador(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlaceoperador.php",
        type: "POST",
        data: {transaccion: "filtraroperador", REF: $("#buscar-operador").val(), numreg: $("#num-reg").val(), pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-operadores").html(datos);
                cargandoHide();
            }
        }
    });
}

function editarOperador(id) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceoperador.php",
        type: "POST",
        data: {transaccion: "editaroperador", id: id},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('operador');
                window.setTimeout("setValoresEditarOperador('" + datos + "')", 400);
            }
        }
    });
}

function setValoresEditarOperador(datos) {
    changeText("#contenedor-titulo-form-operador", "Editar operador");
    changeText("#btn-form-operador", "Guardar cambios <span class='fas fa-save'></span></a>");
    var array = datos.split("</tr>");
    $("#nombre-operador").val(array[1]);
    $("#apaterno-operador").val(array[2]);
    $("#amaterno-operador").val(array[3]);
    $("#num-licencia").val(array[4]);
    $("#rfc-operador").val(array[5]);
    $("#empresa-operador").val(array[6]);
    loadOpcionesEstado('contenedor-estado', 'id-estado', array[7]);
    loadOpcionesMunicipio(array[8], array[7]);
    $("#calle-operador").val(array[9]);
    $("#codigo_postal").val(array[10]);
    $("#btn-form-operador").attr("onclick", "insertarOperador(" +  array[0] + ");");
    cargandoHide();
}

function eliminarOperador(idoperador) {
    alertify.confirm("¿Estás seguro que deseas eliminar este operador?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceoperador.php",
            type: "POST",
            data: {transaccion: "eliminaroperador", idoperador: idoperador},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    cargandoHide();
                    loadView('listaoperador');
                }
            }
        });
    }).set({title: "Q-ik"});
}