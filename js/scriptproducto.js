function buscarProducto(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: $("#buscar-producto").val(), pag: pag, numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-productos-altas").html(datos);
            }
            cargandoHide();
        }
    });
}

function loadListaProductosaltas(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: $("#buscar-producto").val(), pag: pag, numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-productos-altas").html(datos);
            }
            cargandoHide();
        }
    });
}

function LlenaDescripcion() {
    var producto = $("#producto").val();
    $("#descripcion").val(producto);
}

function aucompletarCatalogo() {
    $('#clave-fiscal').autocomplete({
        source: "../../CATSAT/CATSAT/com.sine.enlace/enlaceProdServ.php?transaccion=autocompleta",
        select: function (event, ui) {
            var a = ui.item.value;
        }
    });
}

function addinventario() {
    var tipo = $("#tipo").val();
    if (tipo == '1') {
        $("#inventario").show('slow');
        if ($("#chinventario").prop('checked')) {
            $("#cantidad").removeAttr('disabled');
        } else {
            $("#cantidad").attr('disabled', true);
            $("#cantidad").val('0');
        }
    } else {
        $("#chinventario").removeAttr('checked');
        $("#inventario").hide('slow');
        $("#cantidad").attr('disabled', true);
        $("#cantidad").val('0');
        $("#clave-unidad").val('E48-Unidad de servicio');
    }
}

function aucompletarUnidad() {
    $('#clave-unidad').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=catunidad",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function calcularGanancia() {
    var preciocompra = $("#pcompra").val() || '0';
    var porcentaje = $("#porganancia").val() || '0';
    var importeganancia = (parseFloat(preciocompra) * parseFloat(porcentaje)) / 100;
    $("#ganancia").val(importeganancia);
    var precioventa = parseFloat(preciocompra) + parseFloat(importeganancia);
    $("#pventa").val(precioventa);
    calcularImpuestosTotal();
}

function calcularImpuestosTotal(){
    var id = "";
    var div = [];
    var porcentaje = 0.0;
    var tipoImp = 0;
    var costo = $("#pventa").val();
    var total = $("#pventa").val();
    var impuesto = 0;

    $("input[name=taxes]:checked").each(function(){
        id = $(this).attr("id");
        div = $(this).val().split("-");
        porcentaje = parseFloat(div[0]);
        tipoImp = parseFloat(div[1]); //1 traslado //2retencion

        impuesto = costo * porcentaje;
        impuesto = impuesto.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
        //impuesto = Math.round(impuesto * 100) / 100;

        if(tipoImp == 1){
            total = parseFloat(total) + parseFloat(impuesto);
        }
        else if(tipoImp == 2){
            total = parseFloat(total) - parseFloat(impuesto);
        }
        $('#p'+id).val(impuesto);
    });
    $("#ptotiva").val(total);
}