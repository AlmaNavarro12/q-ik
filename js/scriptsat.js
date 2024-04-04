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
function loadOpcionesMoneda(id= "", selected = "") {
    $.ajax({
        data: { transaccion: 'getOptions'},
        url: rutaPrincipal + 'com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('#moneda-pago').html(res.datos);
                $('.contenedor-moneda').html(res.datos);
                if(selected != ""){
                    $('#'+id).val(selected);
                }
            }
        }
    });
}

function loadMonedaCFDI(tag = "", idmoneda = "") {
    $.ajax({
        url: rutaPrincipal + 'com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        dataType: 'html',
        data: { transaccion: 'opcionesmoneda', idmoneda: idmoneda },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                $(".contenedor-moneda-" + tag).html(datos);
            }
        }
    });
}

function loadMonedaComplemento(tag = "", select = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $(".contmoneda-" + tag).html(res.datos);
                if(select != ""){
                    $("#moneda-" + tag).val(select);
                }
            }
        }
    });
}

function getTipoCambio() {
    var tag = $("#tabs").find('.sub-tab-active').attr("data-tab");
    cargandoHide();
    cargandoShow();
    var idmoneda = $("#moneda-" + tag).val();
    $.ajax({
        url: rutaPrincipal+ 'com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        data: { transaccion: 'gettipocambio', idmoneda: idmoneda },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                if (idmoneda != "1") {
                    $("#cambio-" + tag).removeAttr('disabled');
                } else {
                    $("#cambio-" + tag).attr('disabled', true);
                }
                $("#cambio-" + tag).val(datos);
            }
            cargandoHide();
        }
    });
}

function getTipoCambioSinTag() {
    cargandoHide();
    cargandoShow();
    var idmoneda = $("#id-moneda").val();
    $.ajax({
        url: rutaPrincipal+ 'com.sine.enlace/enlaceMonedas.php',
        type: 'POST',
        data: { transaccion: 'gettipocambio', idmoneda: idmoneda },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                if (idmoneda != "1") {
                    $("#tipo-cambio").removeAttr('disabled');
                } else {
                    $("#tipo-cambio").attr('disabled', true);
                }
                $("#tipo-cambio").val(datos);
            }
            cargandoHide();
        }
    });
}
//-------------------------------------FORMA DE PAGO
function loadFormaPago(tag = "", select="") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceFormaPago.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $(".cont-fpago-" + tag).html(res.datos);
                if(select != ""){
                    $("#forma-" + tag).val(select);
                }
            }
        }
    });
}

function loadOpcionesFormaPago2(selected = "" ) {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceFormaPago.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('#forma-pago').html(res.datos);
                if(selected) {
                    $("#id-forma-pago").val(selected);
                }
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

//Por cruce de de funciones se hace una para operador
function getEstadoMunicipioByCodPOperador() {
    var cp = $("#cp-operador").val();
    if (cp !== "") {
        if (isNumber(cp, "cp-operador")) {
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
                        $(".contenedor-estado-op").html(estados);
                        $(".contenedor-municipio-op").html(municipios);
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


//Por cruce de funciones hay que hacer una para operador
function loadOpcionesMunicipioOperador(idmun = "", idestado = "") {
    cargandoHide();
    cargandoShow();
    if(idestado == ''){
        idestado = $("#estado-operador").val();
    }
    $.ajax({
        url: rutaPrincipal + "com.sine.enlace/enlaceCodigopostal.php",
        type: 'POST',
        dataType: 'JSON',
        data: {transaccion: 'opcionesmunicipio', idestado: idestado, idmunicipio:idmun},
        success: function (datos) {
            $(".contenedor-municipio-op").html(datos.datos);
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

//----------------------------------METODO DE PAGO
function loadOpcionesMetodoPago(id="", selected = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceMetodosPago.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-metodo-pago').html(res.datos);
                if(selected != ""){
                    $('#'+id).val(selected);
                }
            }
        }
    });
}

//---------------------------------TIPO COMPROBANTE
function loadOpcionesComprobante(id="", selected = "") {
    $.ajax({
        data: { transaccion: 'getOptions' },
        url: rutaPrincipal + 'com.sine.enlace/enlaceComprobante.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-tipo-comprobante').html(res.datos);
                if(selected != ""){
                    $('#'+id).val(selected);
                }
            }
        }
    });
}

//----------------------------------USO CFDI
function loadOpcionesUsoCFDI(id="", selected = "") {
    $.ajax({
        data: { transaccion: 'getOptions'},
        url: rutaPrincipal + 'com.sine.enlace/enlaceUsoCFDI.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-uso').html(res.datos);
                if(selected != ""){
                    $('#'+id).val(selected);
                }
            }
        }
    });
}

//-----------------------------------TIPO DE RELACIOMN
function loadOpcionesTipoRelacion() {
    $.ajax({
        data: { transaccion: 'getOptions'},
        url: rutaPrincipal + 'com.sine.enlace/enlaceRelacion.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-relacion').html(res.datos);
            }
        }
    });
}

//---------------------------------PERIODICIDAD
function opcionesPeriodoGlobal(id = "", selected = "") {
    $.ajax({
        data: { transaccion: 'getOptions'},
        url: rutaPrincipal + 'com.sine.enlace/enlacePeriodicidad.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-pglobal').html(res.datos);
                if(selected != ""){
                    $('#'+id).val(selected);
                }
            }
        }
    });
}

//--------------------------------MESES
function opcionesMeses(id = "", selected = "") {
    $.ajax({
        data: { transaccion: 'getOptions'},
        url: rutaPrincipal + 'com.sine.enlace/enlaceMeses.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-mes').html(res.datos);
                if(selected != ""){
                    $('#'+id).val(selected);
                }
            }
        }
    });
}

//----------------------------------TIPO PERMISO
function aucompletarPermiso(){
    $('#tipo-permiso').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlacePermiso.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

//----------------------------------TIPO AUTOTRANSPORTE
function aucompletarConfigTransporte() {
    $('#conf-transporte').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceAutotransporte.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

//---------------------------------TIPO REMOLQUE
function aucompletarTipoRemolque(number = "") {
    $('#tipo-remolque' + number).autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceTipoRemolque.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

//-----------------------------------MOTIVO DE CANCELACION
function opcionesMotivoCancelar() {
    $.ajax({
        data: { transaccion: 'getOptions'},
        url: rutaPrincipal + 'com.sine.enlace/enlaceMotivo.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (res) {
            if (res.status > 0) {
                $('.contenedor-motivos').html(res.datos);
            }
        }
    });
}

//----------------------------------- MATERIAL PELIGROSO
function autocompletarMaterialPeligroso() {
    $('#clv-peligro').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceMaterialPeligroso.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;

        }
    });
}

//-------------------------------------UNIDAD CARTA PORTE
function aucompletarUnitMercancia() {
    $('#unidad-mercancia').autocomplete({
        source: rutaPrincipal + "com.sine.enlace/enlaceUnidadCarta.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

//--------------------------------------EMBALAJE
function autocompletarEmbalaje() {
    $('#clv-embalaje').autocomplete({
        source: rutaPrincipal +  "com.sine.enlace/enlaceEmbalaje.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
        }
    });
}
