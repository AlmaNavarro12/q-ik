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
function loadOpcionesBanco(id = "", selectValue = "") {
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
function loadOpcionesEstado(contenedor, id = "", selectValue = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceEstado.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                if (id != "" && selectValue != "") {
                    $('.' + contenedor).html(res.datos);
                    $('#' + id).val(selectValue);
                } else {
                    $('.contenedor-estado').html(res.datos);
                }
            }
        }
    });
}

//------------------------------------MUNICIPIO
function loadOpcionesMunicipio(idmun = "", idestado = "") {
    cargandoHide();
    cargandoShow();
    if(idestado == ''){
        idestado = $("#id-estado").val();
    }
    $.ajax({
        url: rutaPrincipal + "com.sine.enlace/enlaceCodigopostal.php",
        type: 'POST',
        dataType: 'JSON',
        data: {transaccion: 'opcionesmunicipio', idestado: idestado, idmunicipio:idmun},
        success: function (datos) {
            $(".contenedor-municipio").html(datos.datos);
            cargandoHide();
        }
       
    });
}

//----------------------------------REGIMEN FISCAL
function aucompletarRegimen(){
    $('#regimen-fiscal').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceRegimenFiscal.php?transaccion=autocompleta",
        select: function (event, ui) {
            var c_regimen = ui.item.c_regimenfiscal;
            var desc_regimen = ui.item.descripcion_regimen;
            var regimen = c_regimen + " - " + desc_regimen;
            $('#regimen-fiscal').val(regimen);
            $('#desc_regimenFiscal').val(desc_regimen);
        }
    });
}