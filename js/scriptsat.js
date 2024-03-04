var rutaPrincipal = '../../CATSAT/CATSAT/';

//---------------------------------------PROD_SERV
function aucompletarCatalogo() {
    $('#clave-fiscal').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceProdServ.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
        }
    });
}

//--------------------------------------UNIDAD DE MEDIDAS
function aucompletarUnidad() {
    $('#clave-unidad').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceClaveUnidad.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

//-------------------------------------MONEDAS
function loadOpcionesMoneda() {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('#moneda-pago').html(res.datos);
            }
        }
    });
}

//-------------------------------------FORMA DE PAGO
function loadOpcionesFormaPago2() {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceFormaPago.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('#forma-pago').html(res.datos);
            }
        }
    });
}

//------------------------------------BANCO
//Parametros: Dato a seleccionar, el id del select
function loadOpcionesBanco(id, selectValue = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceBanco.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.' + id).html(res.datos);
                $('#' + id).val(selectValue);
            }
        }
    });
}

//---------------------------------------CODIGO POSTAL
function getEstadoMunicipioByCodP() {
    var cp = $("#codigo_postal").val();
    if (cp !== "") {
        if (isNumber(cp, "codigo_postal")) {
            cargandoHide();
            cargandoShow();
            $.ajax({
                url: rutaPrincipal + "com.sine.enlace/enlaceCodigopostal.php",
                type: 'POST',
                dataType: 'html',
                data: {transaccion: 'buscarcp', cp: cp},
                success: function (datos) {
                    var array = datos.split("<tr>");
                    var estados = array[0];
                    var municipios = array[1];
                    console.log(datos);
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 5000);
                    if (bandera == 0) {
                        alertify.error(res);
                    } else {
                        cargandoHide();
                        $(".contenedor-estado").html(estados);
                        $(".contenedor-municipio").html(municipios);
                    }
                    cargandoHide();
                }
            });
        }
    }
}

//------------------------------------ESTADO
function loadOpcionesEstado(id, selectValue = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceBanco.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.' + id).html(res.datos);
                $('#' + id).val(selectValue);
            }
        }
    });
}

//------------------------------------MUNICIPIO
function loadOpcionesMunicipio(id, selectValue = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceBanco.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.' + id).html(res.datos);
                $('#' + id).val(selectValue);
            }
        }
    });
}