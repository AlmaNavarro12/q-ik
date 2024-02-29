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