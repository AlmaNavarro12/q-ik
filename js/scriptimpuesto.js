function buscarImpuesto(pag = "") {
    var REF = $("#buscar-impuesto").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlaceimpuesto.php",
        type: "POST",
        data: {transaccion: "listaimpuesto", REF:REF, numreg:numreg, pag:pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-impuesto").html(datos);

            }
        }
    });
}

function loadListaImpuesto(pag = "") {
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceimpuesto.php",
        type: "POST",
        data: {transaccion: "listaimpuesto", REF:$("#buscar-impuesto").val(), numreg:$("#num-reg").val(), pag:pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-impuesto").html(datos);

            }
            cargandoHide();
        }
    });
}

function guardarImpuesto(idimpuesto = null) {
    var nombre = $("#descripcion-impuesto").val();
    var tipo = $("#tipo-impuesto").val();
    var impuesto = $("#impuesto-aplicado").val();
    var factor = $("#tipo-factor").val();
    var tipotasa = $("#tipo").val();
    var tasa = "";
    var id = "";
    if(tipotasa == 'fijo'){
        tasa = $("#tasa-opcion").val();
        id = "tasa-opcion";
    } else if(tipotasa == 'rango'){
        tasa = $("#tasa-impuesto").val();
        id = "tasa-impuesto";
    }
    var chuso = ($("#chuso").prop('checked')) ? "1" : "0";
    
    if (isnEmpty(nombre, "descripcion-impuesto") && isnEmpty(tipo, "tipo-impuesto") && isnEmpty(impuesto, "impuesto-aplicado") && isnEmpty(factor, "tipo-factor") && isnEmpty(tasa, id)) {
        var url = "com.sine.enlace/enlaceimpuesto.php";
        var transaccion = (idimpuesto != null) ? "actualizarimpuesto" : "insertarimpuesto";
        
        var data = {
            transaccion: transaccion,
            idimpuesto: idimpuesto,
            nombre: nombre,
            tipo: tipo,
            impuesto: impuesto,
            factor: factor,
            tipotasa: tipotasa,
            tasa: tasa,
            chuso: chuso
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
                    cargandoHide();
                    alertify.error(res);
                } else {
                    cargandoHide();
                    var mensaje = (idimpuesto != null) ? 'Impuesto actualizado.' : 'Impuesto guardado.';
                    alertify.success(mensaje);
                    loadView('listaimpuesto');
                }
            }
        });
    }
}

function loadOpcionesTasa() {
    $.ajax({
        url: "com.sine.enlace/enlaceimpuesto.php",
        type: "POST",
        data: {transaccion: "opcionestasa", tipo:$("#tipo-impuesto").val(), impuesto: $("#impuesto-aplicado").val(), factor:$("#tipo-factor").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                cargandoHide();
                alertify.error(res);
            } else {
                setPorcentajes(datos);
            }
        }
    });
}

function setPorcentajes(datos){
    var array = datos.split("</tr>");
    var tipo = array[0];
    if(tipo == 'fijo'){
        $("#tipo").val(tipo);
        $("#tasa-num").attr('hidden',true);
        $("#tasa-opt").attr('hidden',false);
        $(".contenedor-tasa").html(array[1]);
        $("#tasa-impuesto-rango").text('');
    }else if(tipo=='rango'){
        var min = array[1];
        var max = array[2];
        $("#tipo").val(tipo);
        $("#rango-min").val(min);
        $("#rango-max").val(max);
        $("#tasa-impuesto-rango").text("Valor mínimo "+min+" | Valor Maximo "+max);
        $("#tasa-num").attr('hidden',false);
        $("#tasa-opt").attr('hidden',true);
    }else{
        $(".contenedor-tasa").html('');
        $("#tasa-impuesto-rango").text('');
        $("#tipo").val('');
        $("#rango-min").val('');
        $("#rango-max").val('');
    }
}

function loadImpuesto() {
    var tipo = $("#tipo-impuesto").val();
    var datos = "";
    if (tipo == '1') {
        datos = "<option value='2'>IVA</option><option value='3'>IEPS</option>";
    } else {
        datos = "<option value='1'>ISR</option><option value='2'>IVA</option><option value='3'>IEPS</option>";
    }
    $(".contenedor-impuestos").html(datos);

}

function editarImpuesto(idimpuesto) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceimpuesto.php",
        type: "POST",
        data: {transaccion: "editarimpuesto", idimpuesto: idimpuesto},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                cargandoHide();
                loadView('impuesto');
                window.setTimeout("setValoresEditarImpuesto('" + datos + "')", 500);
            }
        }
    });
}

function setValoresEditarImpuesto(datos) {
    changeText("#contenedor-titulo-form-impuesto", "Editar impuesto");
    changeText("#btn-form-impuesto", "Guardar cambios <span class='fas fa-save'></span></a>");

    var array = datos.split("</tr>");
    var impuesto = {
        '1': 'ISR',
        '2': 'IVA',
        '3': 'IEPS'
    };

    var idimpuesto = array[0];
    var nombre = array[1];
    var tipoimpuesto = array[2];
    var tclave = impuesto[array[3]];
    var factor = array[4];
    var tipotasa = array[5];
    var porcentaje = array[6];

    $("#descripcion-impuesto").val(nombre);
    $('#tipo-impuesto').val(tipoimpuesto);
    $("#option-default-impuesto-aplicado").val(array[3]);
    $("#option-default-impuesto-aplicado").text(tclave);
    $('#tipo-factor').val(factor);
    $("#tipo").val(tipotasa);
    if (array[7] == '0') {
        $("#chuso").removeAttr('checked');
    }

    loadOpcionesTasa2(tipoimpuesto, array[3], factor);
    loadImpuesto();

    if (tipotasa == "fijo") {
        $("#option-default-tasa-opcion").val(porcentaje).text(porcentaje);
    } else if (tipotasa == 'rango') {
        $("#tasa-impuesto").val(porcentaje);
    }

    $("#btn-form-impuesto").attr("onclick", "guardarImpuesto(" + idimpuesto + ");");
}

function loadOpcionesTasa2(tipo, impuesto, factor) {
    
    $.ajax({
        url: "com.sine.enlace/enlaceimpuesto.php",
        type: "POST",
        data: {transaccion: "opcionestasa", tipo:tipo, impuesto: impuesto, factor:factor},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);

            if (bandera == '0') {
                cargandoHide();
                alertify.error(res);
            } else {
                setPorcentajes(datos);
            }
        }
    });
}

function eliminarImpuesto(id){
    alertify.confirm("¿Estás seguro que deseas eliminar este impuesto?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceimpuesto.php",
            type: "POST",
            data: {transaccion: "eliminarimpuesto", idimpuesto: id},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success("Registro eliminado.");
                    loadView('listaimpuesto');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}