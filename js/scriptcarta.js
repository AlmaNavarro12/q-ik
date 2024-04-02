$(function () {
    $(".button-tab").click(function () {
        $('.button-tab').removeClass("tab-active");
        $('.div-form').hide();
        var tab = $(this).attr("data-tab");
        $("#" + tab).show();
        $(this).addClass("tab-active");
    });
});

$(function () {
    $(".sub-button-tab").click(function () {
        $('.sub-button-tab').removeClass("sub-tab-active");
        $('.sub-div').hide();
        var tab = $(this).attr("data-tab");
        $("#sub-" + tab).show();
        $(this).addClass("sub-tab-active");
        setNavigation(tab);
    });
});

$(function () {
    $(".next-prev").click(function () {
        var tab = $(this).attr("data-tab");
        var nav = $(this).attr("data-nav");

        if (nav == 'top') {
            $('.div-form').hide();
            $('.button-tab').removeClass("tab-active");

            $("#div-" + tab).show();
            $("#tab-" + tab).addClass("tab-active");
        } else if (nav == 'sub') {
            $('.sub-div').hide();
            $('.sub-button-tab').removeClass("sub-tab-active");
            setNavigation(tab);
            $("#sub-" + tab).show();
            $("#tab-" + tab).addClass("sub-tab-active");
        }
        document.getElementById("div-space").scrollIntoView();
    });
}); 

function filtrarCarta(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "filtrarfolio", pag: pag, REF: $("#buscar-carta").val(), numreg: $("#num-reg").val()},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-carta").html(datos);
                cargandoHide();
            }
        }
    });
}

function filtrarProducto(pag = "") {
    cargandoHide();
    cargandoShow();
    var NOM = $("#buscar-producto").val();
    var numreg = $("#num-reg").val();
    $("#body-lista-productos-factura").append('');
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: NOM, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                var array = datos.split("<pag>");
                var table = array[0];
                var pag = array[1];
                $("#body-lista-productos-factura").append(table);
                $("#pagination").append(pag);
                cargandoHide();
            }
        }
    });
}

function buscarProducto(pag = "") {
    var NOM = $("#buscar-producto").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "filtrarproducto", NOM: NOM, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<pag>");
                var table = array[0];
                var pag = array[1];
                $("#body-lista-productos-factura").html(table);
                $("#pagination").html(pag);
            }
        }
    });
}

function setCamposProducto() {
    $("#codigo-producto").val('');
    $("#producto").val('');
    $("#tipo").val('');
    $("#inventario").attr('hidden', true);
    $("#clave-unidad").val('');
    $("#descripcion").val('');
    $("#pcompra").val(0);
    $("#porganancia").val(0);
    $("#ganancia").val(0);
    $("#pventa").val(0);
    $("#clave-fiscal").val('');
    $('#id-proveedor').val('');
    $("#imagen").val('');
    $('#muestraimagen').html("");
    $("#btn-form-producto-factura").attr("onclick", "insertarProductoCarta();");
}

function addinventario() {
    var tipo = $("#tipo").val();
    if (tipo == '1') {
        $("#inventarios").show('slow');
        if ($("#chinventario").prop('checked')) {
            $("#cantidad").removeAttr('disabled');
            changeText("#labelinventario", "¿Desactivar inventario?");
        } else {
            $("#cantidad").attr('disabled', true);
            $("#cantidad").val('0');
            changeText("#labelinventario", "¿Activar inventario?");
        }
    } else {
        $("#chinventario").removeAttr('checked');
        $("#inventarios").hide('slow');
        $("#cantidad").attr('disabled', true);
        $("#cantidad").val('0');
        $("#clave-unidad").val('E48-Unidad de servicio');
    }
}

function myRound(num, dec) {
    var exp = Math.pow(10, dec || 2); 
    return parseInt(num * exp, 10) / exp;
}

function calcularImpuestosTotal() {
    var id = "";
    var div = [];
    var porcentaje = 0.0;
    var tipoImp = 0;
    var costo = $("#pventa").val();
    var total = $("#pventa").val();
    var impuesto = 0;

    $("input[name=taxes]:checked").each(function () {
        id = $(this).attr("id");
        div = $(this).val().split("-");
        porcentaje = parseFloat(div[0]);
        tipoImp = parseFloat(div[1]); //1 traslado //2retencion

        impuesto = costo * porcentaje;
        impuesto = impuesto.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];

        if (tipoImp == 1) {
            total = parseFloat(total) + parseFloat(impuesto);
        }
        else if (tipoImp == 2) {
            total = parseFloat(total) - parseFloat(impuesto);
        }
        $('#p' + id).val(impuesto);
    });
    var preciopub = myRound(total, 2);
    $("#ptotiva").val(preciopub);
}

function calcularImpuestosTotalReverse() {
    var id = "";
    var div = [];
    var porcentaje = 0.0;
    var tipoImp = 0;
    var costo = $("#ptotiva").val();
    var total = $("#ptotiva").val();
    var impuesto = 0;

    $("input[name=taxes]:checked").each(function () {
        id = $(this).attr("id");
        div = $(this).val().split("-");
        porcentaje = parseFloat(div[0]);
        tipoImp = parseFloat(div[1]); //1 traslado //2retencion

        if (tipoImp == 1) {
            costo = Math.round((total / (porcentaje + 1)) * 100) / 100;
            impuesto = costo * porcentaje;
            impuesto = impuesto.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
            total = parseFloat(total) - parseFloat(impuesto);
        }
        else if (tipoImp == 2) {
            porcentaje = porcentaje * 100;
            var restante = 100 - porcentaje;
            impuesto = Math.round(((costo * porcentaje) / restante) * 100) / 100;
            total = Math.round((parseFloat(total) + parseFloat(impuesto)) * 100) / 100;
        }
        $('#p' + id).val(impuesto);
    });
    $("#pventa").val(myRound(total, 2));
}

function insertarProductoCarta(idproducto, idtmp) {
    var codproducto = $("#codigo-producto").val();
    var producto = $("#producto").val();
    var descripcion = $("#descripcion").val();
    var clavefiscal = $("#clave-fiscal").val();
    var tipo = $("#tipo").val();
    var unidad = $("#clave-unidad").val();
    var pcompra = $("#pcompra").val();
    var porcentaje = $("#porganancia").val();
    var ganancia = $("#ganancia").val();
    var pventa = $("#pventa").val();
    var idproveedor = $("#id-proveedor").val() || '0';
    var imagen = $('#filename').val();
    var imgactualizar = $("#imgactualizar").val();
    var chinventario = 0;
    var cantidad = $("#cantidad").val();
    
    if ($("#chinventario").prop('checked')) {
        chinventario = 1;
    }

    var imp_apl = "";
    $("input[name=taxes]:checked").each(function () {
        imp_apl += $(this).val() + "<tr>";
    });

    if (
        isnEmpty(codproducto, "codigo-producto") &&
        isnEmpty(producto, "producto") &&
        isList(clavefiscal, "clave-fiscal") &&
        isnEmpty(tipo, "tipo") &&
        isList(unidad, "clave-unidad") &&
        isPositive(porcentaje, "porganancia") &&
        isPositive(ganancia, "ganancia") &&
        isPositive(pventa, "pventa")
    ) {
        var transaccion = (idproducto && idtmp) ? "actualizarproducto" : "insertarproducto";
        var data = {
            transaccion: transaccion,
            idproducto: idproducto || '',
            idtmp: idtmp || '',
            codproducto: codproducto,
            producto: producto,
            tipo: tipo,
            unidad: unidad,
            descripcion: descripcion,
            pcompra: pcompra,
            porcentaje: porcentaje,
            ganancia: ganancia,
            pventa: pventa,
            clavefiscal: clavefiscal,
            idproveedor: idproveedor,
            imagen: imagen,
            imgactualizar: imgactualizar,
            chinventario: chinventario,
            cantidad: cantidad,
            imp_apl: imp_apl,
            insert: 'f'
        };

        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
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
                    tablaProductos();
                    $("#nuevo-producto").modal('hide');
                }
            }
        });
    }
}

function editarProductoFactura(idprod, idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: {transaccion: "editarproducto", idproducto: idprod},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                setValoresEditarProducto(datos, idtmp);
            }
            cargandoHide();
        }
    });
}

function setValoresEditarProducto(datos, idtmp) {
    $("#muestraimagen").html('');
    changeText("#titulo-alerta-editar-producto", "Editar producto en factura");
    var array = datos.split("</tr>");
    console.log(array);
    var tipo = array[10];
    var imagen = array[14];
    var chinventario = array[15];
    var img = array[17];

    if (tipo == "1") {
        $("#inventarios").show('slow');
        changeText("#labelinventario", "¿Desactivar inventario?")
    } else if (tipo == "2") {
        $("#inventarios").hide('slow');
    }

    if (chinventario == '1') {
        $("#chinventario").prop('checked', true);
        $("#cantidad").removeAttr('disabled');
    }

    $("#codigo-producto").val(array[1]);
    $("#producto").val(array[2]);
    $("#tipo").val(tipo);
    $("#cantidad").val(array[16]);
    $("#clave-unidad").val(array[3] + "-" + array[4]);
    $("#descripcion").val(array[5]);
    $("#pcompra").val(array[6]);
    $("#porganancia").val(array[7])
    $("#ganancia").val(array[8]);
    $("#pventa").val(array[9]);
    $("#clave-fiscal").val(array[11] + "-" + array[12]);
    if (array[13] != '0') {
        loadOpcionesProveedor(array[13]);
    }
    getOptionsTaxes(array[18]);
    setTimeout(() => {
        calcularImpuestosTotal();
    }, 500);

    $("#filename").val(imagen);
    $("#imgactualizar").val(imagen);

    if (imagen !== '') {
        $("#imagenproducto").show('slow');
        $("#muestraimagenproducto").html(img);
        $("#filename").val(imagen);
    }
    $("#nameimg").val(imagen);
    $("#imgactualizar").val(img);

    $("#btn-form-producto-factura").attr("onclick", "insertarProductoCarta(" + array[0] + "," + idtmp + ");");
}

function getOptionsTaxes(taxes = "") {
    cargandoShow();
    $.ajax({
        data: { transaccion: "taxesproductos", taxes: taxes },
        url: 'com.sine.enlace/enlaceproducto.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.bandera > 0) {
                $('#imp-apli').html(response.impuestos);
                $('#input-imp-apli').html(response.inputs);
            } else {
                alertify.error("No hay impuestos registrados.");
            }
        }
    });
    cargandoHide();
}



function eliminar(idtemp, cantidad, idproducto) {
    alertify.confirm("Esta seguro que desea eliminar este producto?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacefactura.php",
            type: "POST",
            data: {transaccion: "eliminar", idtemp: idtemp, cantidad: cantidad, idproducto: idproducto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductos();

                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

function cancelarCarta() {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "cancelar"},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                loadView('listacarta');
            }
        }
    });
}

function calcularGanancia() {
    var preciocompra = $("#pcompra").val() || '0';
    var porcentaje = $("#porganancia").val() || '0';
    var importeganancia = (parseFloat(preciocompra) * parseFloat(porcentaje)) / 100;
    $("#ganancia").val(myRound(importeganancia, 2));
    var precioventa = parseFloat(preciocompra) + parseFloat(importeganancia);
    var total = myRound(precioventa, 2)
    $("#pventa").val(total);
    calcularImpuestosTotal();
}


function eliminarImagen(tipoOperacion) {
    var confirmMessage = "";

    if (tipoOperacion === 'nuevo') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen?";
    } else if (tipoOperacion === 'actualizar') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen en relación al producto? Una vez borrada no se podrá incorporar nuevamente.";
    } 

    alertify.confirm(confirmMessage, function (e) {
        if (e) {
            if (tipoOperacion === 'nuevo') {
                eliminarImgTpm(); 
                $("#imagenproducto").hide('slow');
            } else if (tipoOperacion === 'copia' || tipoOperacion === 'actualizar') {
                $("#imagenproducto").hide('slow');
                $("#muestraimagenproducto").html('');
                $("#filename").val('');
                $("#nameimg").val('');
                $("#imgactualizar").val('');
            }
        }
    }).set({ title: "Q-ik" });
}

function setNavigation(tab) {
    switch (tab) {
        case 'mercancia':
            $("#btn-form-prev").attr("data-tab", 'factura');
            $("#btn-form-prev").attr("data-nav", 'top');

            $("#btn-form-next").attr("data-tab", 'transporte');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'transporte':
            $("#btn-form-prev").attr("data-tab", 'mercancia');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("data-tab", 'ubicacion');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'ubicacion':
            $("#btn-form-prev").attr("data-tab", 'transporte');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("data-tab", 'operador');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'operador':
            $("#btn-form-prev").attr("data-tab", 'ubicacion');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("data-tab", 'evidencia');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
        case 'evidencia':
            $("#btn-form-prev").attr("data-tab", 'operador');
            $("#btn-form-prev").attr("data-nav", 'sub');

            $("#btn-form-next").attr("hidden", 'true');
            break;
        default:
            $("#btn-form-prev").attr("data-tab", 'factura');
            $("#btn-form-prev").attr("data-nav", 'top');

            $("#btn-form-next").attr("data-tab", 'transporte');
            $("#btn-form-next").attr("data-nav", 'sub');
            $("#btn-form-next").removeAttr("hidden");
            break;
    }
}


function setIDTMP(id, observaciones) {
    var txtbd = observaciones.replace(new RegExp("<ent>", 'g'), '\n');
    $("#idtmp").val(id);
    $("#observaciones-producto").val(txtbd);
}

function agregarObservaciones() {
    var idtmp = $("#idtmp").val();
    var observaciones = $("#observaciones-producto").val();
    var txtbd = observaciones.replace(new RegExp("\n", 'g'), '<ent>');
    var uuid = $("#uuidfactura").val() || "";
    
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "agregarobservaciones", idtmp: idtmp, observaciones: txtbd, uuid: uuid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos(uuid);
            }
            cargandoHide();
        }
    });
}

function loadFolioCarta(iddatos = "") {
    cargandoShow();
    if(iddatos == ""){
        iddatos = $("#datos-facturacion").val();
    }
    $.ajax({
        url: 'com.sine.enlace/enlacecarta.php',
        type: 'POST',
        data: {transaccion: 'emisor', iddatos: iddatos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                var rfc = array[0];
                var razon = array[1];
                var clvreg = array[2];
                var regimen = array[3];
                var codpos = array[4];
                $("#rfc-emisor").val(rfc);
                $("#razon-emisor").val(razon);
                $("#regimen-emisor").val(clvreg + "-" + regimen);
                $("#cp-emisor").val(codpos);
            }
            cargandoHide();
        }
    });
}

function checkVehiculo() {
    var placa = $("#placa-vehiculo").val();
    if (placa != '') {
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "valvehiculo", placa: placa},
            success: function (datos) {
                if (datos >= '0') {
                    $("#id-vehiculo").val(datos);
                }
                cargandoHide();
            }
        });
    } else {
        $("#id-vehiculo").val('');
    }
}

function checkRemolque(number) {
    var placa = $("#placa-remolque"+number).val();
    if (placa != '') {
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "valremolque", placa: placa},
            success: function (datos) {
                if (datos >= '0') {
                    $("#id-remolque"+number).val(datos);
                }
                cargandoHide();
            }
        });
    } else {
        $("#id-remolque"+number).val('');
    }
}

function checkOperador() {
    var rfc = $("#rfc-operador").val();
    if (rfc != '') {
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "valoperador", rfc: rfc},
            success: function (datos) {
                if (datos == '0') {
                    $("#flag-operador").val(datos);
                }
                $("#id-operador").val(datos);
                cargandoHide();
            }
        });
    }
}

function limpiarCampos(){
    $("#imagenproducto").hide();
    $("#muestraimagenproducto").val("");
    $("#filename").val("");
    $("#imgactualizar").val("");
}

function cargarImgEvidencia() {
    var formData = new FormData();
    var imgInputs = $("#img-evidencia")[0].files;

    if (imgInputs.length > 0) {
        for (var i = 0; i < imgInputs.length; i++) {
            var imgInput = imgInputs[i];
            if (imgInput && isnEmpty(imgInput.name, 'img-evidencia')) {
                formData.append("img-evidencia[]", imgInput);
            }
        }

        if (formData.has("img-evidencia[]")) {
            cargandoHide();
            cargandoShow();

            $.ajax({
                url: 'com.sine.enlace/cargarimgs.php',
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (datos) {
                    tablaEvidencias('1');
                }
            });
        }
    }
}

function tablaEvidencias(d = "") {
    $.ajax({
        url: "com.sine.enlace/enlaceimgs.php",
        type: "POST",
        data: {transaccion: "tablaimg", d: d},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var tab = array[1];
                $("#img-table").html(tab);
            }
            cargandoHide();
        }
    });
}
//---------------------------------------------AUTOACOMPLETADO
function autocompletarMercancia() {
    var chbus = $("input[name=busqueda]:checked").val();
    $('#clv-producto').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=mercancia&&b=" + chbus,
        select: function (event, ui) {
            var a = ui.item.value;
            var nombre = ui.item.nombre;
            var peligro = ui.item.peligro;

            $("#descripcion-mercancia").val(nombre);
            $("#peligro-mercancia").val(peligro);
            if (peligro === '0-1' || peligro === '1') {
                if (peligro === '1') {
                    $("#material-peligroso").val(peligro);
                } else {
                    $("#material-peligroso").val('');
                }
                $("#material-peligroso").removeAttr('disabled');
                $("#clv-peligro").removeAttr('disabled');
                $("#clv-embalaje").removeAttr('disabled');
            } else if (peligro == '0') {
                $("#material-peligroso").attr('disabled', true);
                $("#clv-peligro").attr('disabled', true);
                $("#clv-embalaje").attr('disabled', true);
            }
        }
    });
}

function autocompletarCliente() {
    if ($("#nombre-cliente").val() == '') {
        $("#id-cliente").val('0');
    }
    $('#nombre-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var rfc = ui.item.rfc;
            var razon = ui.item.razon;
            var regfiscal = ui.item.regfiscal;
            var codpostal = ui.item.codpostal;
            var direccion = ui.item.direccion;

            $("#id-cliente").val(id);
            $("#rfc-cliente").val(rfc);
            $("#razon-cliente").val(razon);
            $("#regfiscal-cliente").val(regfiscal);
            $("#cp-cliente").val(codpostal);
            $("#direccion-cliente").val(direccion);
        }
    });
}

function autocompletarVehiculo() {
    if ($("#nombre-vehiculo").val() == "") {
        $("#id-vehiculo").val('');
    }
    $('#nombre-vehiculo').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=vehiculo",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var numpermiso = ui.item.numpermiso;
            var tipopermiso = ui.item.tipopermiso;
            var tipoautot = ui.item.conftransporte;
            var anho = ui.item.anhomodelo;
            var placa = ui.item.placavehiculo;
            var seguro = ui.item.segurocivil;
            var poliza = ui.item.polizaCivil;
            var seguroambiente = ui.item.seguroambiente;
            var polizaambiente = ui.item.polizaambiente;

            $("#id-vehiculo").val(id);
            $("#num-permiso").val(numpermiso);
            $("#tipo-permiso").val(tipopermiso);
            $("#conf-transporte").val(tipoautot);
            $("#anho-modelo").val(anho);
            $("#placa-vehiculo").val(placa);
            $("#seguro-respcivil").val(seguro);
            $("#poliza-respcivil").val(poliza);
            $("#seguro-medambiente").val(seguroambiente);
            $("#poliza-medambiente").val(polizaambiente);
        }
    });
}

function autocompletarRemolque(number) {
    if ($("#nombre-remolque" + number).val() == "") {
        $("#id-remolque" + number).val('0');
    }
    $('#nombre-remolque' + number).autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=remolque",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var tiporemol = ui.item.tiporemolque;
            var placa = ui.item.placaremolque;

            $("#id-remolque" + number).val(id);
            $("#tipo-remolque" + number).val(tiporemol);
            $("#placa-remolque" + number).val(placa);
        }
    });
}

function autocompletarUbicacion() {
    var chbus = $("input[name=findubicacion]:checked").val();
    if (!$("#nombre-ubicacion").val()) {
        $("#id-ubicacion").val('0');
    }
    $('#nombre-ubicacion').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=ubicacion&&b=" + chbus,
        select: function (event, ui) {
            var { value: a, id, rfc, tipo, calle, numext, numint, colonia, idestado, idmunicipio, cp } = ui.item;

            var next = numext ? " #" + numext : "";
            var nint = numint ? ", Int " + numint : "";
            var col = colonia ? ", Colonia " + colonia : "";
            var dir = calle + next + nint + col;

            $("#id-ubicacion").val(id);
            $("#rfc-ubicacion").val(rfc);
            $("#tipo-ubicacion").val(tipo);
            $("#direccion-ubicacion").val(dir);
            $("#codigo_postal").val(cp);
            $("#id-estado").val(idestado);
            loadOpcionesMunicipio(idmunicipio, idestado);
            labelUbicacion();
        }
    });
}

function autocompletarOperador() {
    if ($("#nombre-operador").val() == "") {
        $("#id-operador").val('0');
    }
    $('#nombre-operador').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=operador",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var rfc = ui.item.rfc;
            var licencia = ui.item.licencia;
            var idestado = ui.item.idestado;
            var idmunicipio = ui.item.idmunicipio;
            var calle = ui.item.calle;
            var codpostal = ui.item.codpostal;

            $("#id-operador").val(id);
            $("#rfc-operador").val(rfc);
            $("#num-licencia").val(licencia);
            $("#estado-operador").val(idestado);
            $("#direccion-operador").val(calle);
            $("#cp-operador").val(codpostal);
            loadOpcionesMunicipioOperador(idmunicipio, idestado);
        }
    });
}

//-----------------------------AGREGAR CFDI
function addCFDI() {
    var rel = $("#tipo-relacion").val();
    var cfdi = $("#cfdi-rel").val();
    var descripcion = $('#tipo-relacion option:selected').text();
    if (isnEmpty(rel, "tipo-relacion") && isnEmpty(cfdi, "cfdi-rel")) {
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "addcfdi", rel: rel, cfdi: cfdi, descripcion:descripcion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    cargandoHide();
					alertify.success("CFDI relacionado correctamente");
                    var array = datos.split("<corte>");
                    var p2 = array[1];
                    $("#body-lista-cfdi").html(p2);
					$("#tipo-relacion").val("");
    				$("#cfdi-rel").val("");
                }
            }
        });
    }
}

function eliminarCFDI(idtmp) {
    alertify.confirm("¿Estás seguro que deseas eliminar este CFDI?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "eliminarcfdi", idtmp: idtmp},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    cargandoHide();
                } else {
                    var array = datos.split("<corte>");
                    var p1 = array[0];
                    var p2 = array[1];
                    $("#body-lista-cfdi").html(p2);
                    cargandoHide();
                }
            }
        });
    }).set({title: "Q-ik"});
}

//-----------------------------AGREGAR CONCEPTOS
function agregarProducto(idproducto) {
    var descripcion = $("#prodserv" + idproducto).val();
    var cantidad = $("#cantidad_" + idproducto).val();
    var pventa = $("#pventa_" + idproducto).val();
    var importe = $("#importe_" + idproducto).val();
    var descuento = $("#pordescuento_" + idproducto).val();
    var impdescuento = $("#descuento_" + idproducto).val();
    var total = $("#total_" + idproducto).val();

    var traslados = [];
    $.each($("input[name='chtraslado" + idproducto + "']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });

    var retenciones = [];
    $.each($("input[name='chretencion" + idproducto + "']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");
    var idretencion = retenciones.join("<impuesto>");

    if (isNumber(cantidad, "cantidad_" + idproducto) && isNumber(pventa, "pventa_" + idproducto)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacefactura.php",
            type: "POST",
            data: {transaccion: "agregarProducto", idproducto: idproducto, descripcion: descripcion, cantidad: cantidad, pventa: pventa, importe: importe, descuento: descuento, impdescuento: impdescuento, total: total, idtraslados: idtraslados, idretencion: idretencion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductos();
                }
                cargandoHide();
            }
        });
    }
}

function tablaProductos(uuid = "") {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "tablatmp", uuid: uuid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#resultados").html(datos);
            }
        }
    });
}

//---------------------------------------AGREGAR MERCANCIA
function agregarMercancia(tid = null) {
    var clvprod = $("#clv-producto").val();
    var descripcion = $("#descripcion-mercancia").val();
    var cantidad = $("#cantidad-mercancia").val();
    var unidad = $("#unidad-mercancia").val();
    var peso = $("#peso-mercancia").val();
    var condicional = $("#peligro-mercancia").val();
    var peligro = $("#material-peligroso").val();
    var clvmaterial = $("#clv-peligro").val();
    var embalaje = $("#clv-embalaje").val();

    var transaccion = (tid != null) ? "actualizarmercancia" : "agregarmercancia";

    if (isnEmpty(clvprod, "clv-producto") && isnEmpty(descripcion, "descripcion-mercancia") && isPositive(cantidad, "cantidad-mercancia") && isnEmpty(unidad, "unidad-mercancia") && isnEmpty(peso, "peso-mercancia")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {
                transaccion: transaccion,
                tid: tid,
                condicional: condicional,
                clvprod: clvprod,
                descripcion: descripcion,
                cantidad: cantidad,
                unidad: unidad,
                peso: peso,
                peligro: peligro,
                clvmaterial: clvmaterial,
                embalaje: embalaje
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#clv-producto").val('');
                    $("#descripcion-mercancia").val('');
                    $("#cantidad-mercancia").val('1');
                    $("#unidad-mercancia").val('');
                    $("#peso-mercancia").val('');
                    $("#material-peligroso").val('');
                    $("#clv-peligro").val('');
                    $("#clv-embalaje").val('');
                    if (tid) {
                        $("#btn-agregar-mercancia").css("background", "none");
                        $("#btn-agregar-mercancia").css("color", "#17177C");
                        changeText("#label-mercancia", "Agregar");
                        $("#btn-agregar-mercancia").html("<span class='fas fa-save'></span>");
                        $("#btn-agregar-mercancia").attr("onclick", "agregarMercancia()");
                    }
                    tablaMercancia();
                }
                cargandoHide();
            }
        });
    }
}


function tablaMercancia(uuid = "") {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "tablamercancia", uuid: uuid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#resultmercancia").html(datos);
            }
            obtenerPesoBrutoVehicular();
        }
    });
}

function incrementarMercancia(idtmp, flag) {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "incredmercancia", idtmp: idtmp, flag: flag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaMercancia();
                cargandoHide();
            }
        }
    });
}

function setCantMercancia(idtmp) {
    changeText("#contenedor-cant-title", "Editar cantidad");
    $("#idcant").val(idtmp);
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "getcantmercancia", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#cantidad-producto").val(datos);
                $("#btn-modificar-cant").attr("onclick", "modificarCantMercancia(" + idtmp + ")");
            }
        }
    });
}

function modificarCantMercancia(idtmp) {
    var cant = $("#cantidad-producto").val();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "modcantmercancia", idtmp: idtmp, cant: cant},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaMercancia();
                $("#modal-cantidad").modal('hide');
                cargandoHide();
            }
        }
    });
}

function editarMercancia(idtmp) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "editarmercancia", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                setValoresEditarMercancia(datos);
            }
        }
    });
}

function setValoresEditarMercancia(datos) {
    var array = datos.split("</tr>");
    var condicion = array[6];
    var tmppeligro = array[7];
    if(tmppeligro == 2 || tmppeligro == 0){
        tmppeligro = 0;
    }

    $("#peligro-mercancia").val(condicion);
    $("#clv-producto").val(array[1]);
    $("#descripcion-mercancia").val(array[2]);
    $("#cantidad-mercancia").val(array[3]);
    $("#unidad-mercancia").val(array[4]);
    $("#peso-mercancia").val(array[5]);
    $("#material-peligroso").val(array[7]);
    $("#clv-peligro").val(array[8]);
    $("#clv-embalaje").val(array[9]);

    if (condicion == '0-1' || condicion == '1') {
        $("#material-peligroso").removeAttr('disabled');
        $("#clv-peligro").removeAttr('disabled');
        $("#clv-embalaje").removeAttr('disabled');
    }

    $("#btn-agregar-mercancia").attr("onclick", "agregarMercancia(" + array[0] + ")");
    changeText("#label-mercancia", "Guardar modificación");
    $("#btn-agregar-mercancia").css("background", "#5cb85c");
    $("#btn-agregar-mercancia").css("color", "white");
    $("#btn-agregar-mercancia").html("<span class='fas fa-save'></span>");
    $("#clv-producto").focus();
    cargandoHide();
}

function eliminarMercancia(tid) {
    alertify.confirm("¿Estás seguro que deseas eliminar este registro?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "eliminarmercancia", tid: tid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaMercancia();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

//----------------------------------------------VEHICULOS

function obtenerPesoBrutoVehicular(){
    var p_mercancia = ($('#total-peso-mercancias').val() / 1000);
    var p_vehiculo = $('#peso-vehiculo').val() || '0';
    var p_bruto = Number.parseFloat(p_mercancia) + Number.parseFloat(p_vehiculo);
    p_bruto = Math.floor(p_bruto * 100)/100;
    
    $('#peso-bruto').val(p_bruto);
}

function filterFloat(evt,input){
    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        if(filter(tempValue) === false){
            return false;
        }else{       
            return true;
        }
    }else{
          if(key == 0) {     
              return true;              
          }else if(key == 46){
                if(filter(tempValue)=== false){
                    return false;
                }else{       
                    return true;
                }
          }else{
              return false;
          }
    }
}

function filter(__val__){
    var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
    if(preg.test(__val__) === true){
        return true;
    }else{
       return false;
    }   
}

//-----------------------------------------------UBICACION
function labelUbicacion() {
    var tipo = $("#tipo-ubicacion").val();
    if (tipo == '1') {
        $("#fecha-label").html("Fecha de salida");
        $("#hora-label").html("Hora de salida");
    } else if (tipo == '2') {
        $("#fecha-label").html("Fecha de llegada");
        $("#hora-label").html("Hora de llegada");
    }
}

function agregarUbicacion(tid = null) {
    var idubicacion = $("#id-ubicacion").val() || '0';
    var nombre = $("#nombre-ubicacion").val();
    var rfc = $("#rfc-ubicacion").val();
    var tipo = $("#tipo-ubicacion").val();
    var direccion = $("#direccion-ubicacion").val();
    var idestado = $("#id-estado").val();
    var nombreestado = $("#id-estado option:selected").text().substring(3);
    var idmunicipio = $("#id-municipio").val() || '0';
    var nombremunicipio = $("#id-municipio option:selected").text();
    var cp = $("#cp-ubicacion").val();
    var distancia = $("#distancia-ubicacion").val();
    var fecha = $("#fecha-llegada").val();
    var hora = $("#hora-llegada").val();

    var transaccion = (tid != null) ? "actualizarubicacion" : "agregarubicacion";

    if (isnEmpty(rfc, "rfc-ubicacion") && isnEmpty(tipo, "tipo-ubicacion") && isnEmpty(idestado, "id-estado") && isnEmpty(cp, "cp-ubicacion") && isPositive(distancia, "distancia-ubicacion") && isnEmpty(fecha, "fecha-llegada") && isnEmpty(hora, "hora-llegada")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {
                transaccion: transaccion,
                tid: tid,
                uid: idubicacion,
                nombre: nombre,
                rfc: rfc,
                tipo: tipo,
                idestado: idestado,
                nombreestado: nombreestado,
                cp: cp,
                distancia: distancia,
                fecha: fecha,
                hora: hora,
                direccion: direccion,
                idmunicipio: idmunicipio,
                nombremunicipio: nombremunicipio,
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#id-ubicacion").val('');
                    $("#nombre-ubicacion").val('');
                    $("#rfc-ubicacion").val('');
                    $("#tipo-ubicacion").val('');
                    $("#direccion-ubicacion").val('');
                    $("#id-estado").val('');
                    $("#id-municipio").val('');
                    $("#codigo_postal").val('');
                    $("#distancia-ubicacion").val('');
                    $("#fecha-llegada").val('');
                    $("#hora-llegada").val('');
                    if (tid) {
                        $("#btn-agregar-ubicacion").css("background", "none");
                        $("#btn-agregar-ubicacion").css("color", "#17177C");
                        changeText("#label-distancia", 'Agregar');
                        $("#btn-agregar-ubicacion").html("<span class='fas fa-plus'></span>");
                        $("#btn-agregar-ubicacion").attr("onclick", "agregarUbicacion()");
                    }
                    tablaUbicacion();
                }
                cargandoHide();
            }
        });
    }
}

function tablaUbicacion(uuid = "") {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "tablaubicacion", uuid: uuid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#resultubicacion").html(datos);
            }
        }
    });
}

function setDistancia(idtmp) {
    changeText("#contenedor-cant-title", "Editar Distancia");
    $("#idcant").val(idtmp);
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "getdistanciatmp", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == 'X') {
                alertify.error(res);
            } else {
                $("#cantidad-producto").val(datos);
                $("#btn-modificar-cant").attr("onclick", "modificarDistancia(" + idtmp + ")");
            }
        }
    });
}

function modificarDistancia(idtmp) {
    var cant = $("#cantidad-producto").val();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "moddistancia", idtmp: idtmp, cant: cant},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaUbicacion();
                $("#modal-cantidad").modal('hide');
                cargandoHide();
            }
        }
    });
}

function editarUbicacion(idtmp) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "editarubicacion", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                setValoresEditarUbicacion(datos);
            }

        }
    });
}

function setValoresEditarUbicacion(datos) {
    var array = datos.split("</tr>");
    var tipo = array[4];
    $("input[name='findubicacion']").prop('checked', false);
    $("input[id='findubicacion" + tipo + "']").prop("checked", true);
    $("#id-ubicacion").val(array[1]);
    $("#nombre-ubicacion").val(array[2]);
    $("#rfc-ubicacion").val(array[3]);
    $("#tipo-ubicacion").val(array[4]);
    $("#direccion-ubicacion").val(array[10]);
    $("#id-estado").val(array[5]);
    loadOpcionesMunicipio(array[11], array[5]);
    $("#cp-ubicacion").val(array[6]);
    $("#distancia-ubicacion").val(array[7]);
    $("#fecha-llegada").val(array[8]);
    $("#hora-llegada").val(array[9]);
    $("#btn-agregar-ubicacion").attr("onclick", "agregarUbicacion(" + array[0] + ")");
    $("#btn-agregar-ubicacion").css("background", "#5cb85c");
    $("#btn-agregar-ubicacion").css("color", "white");
    changeText("#label-distancia", 'Guardar cambios');
    $("#btn-agregar-ubicacion").html("<span class='fas fa-save'></span>");
    $("#nombre-ubicacion").focus();
    cargandoHide();
}

function eliminarUbicacion(tid) {
    alertify.confirm("¿Estás seguro que deseas eliminar esta ubicación?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "eliminarubicacion", tid: tid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaUbicacion();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

//-----------------------------------OPERADOR
function agregarOperador(tid = null) {
    var id = $("#id-operador").val() || '0';
    var nombre = $("#nombre-operador").val();
    var rfc = $("#rfc-operador").val();
    var licencia = $("#num-licencia").val();
    var estado = $("#estado-operador").val() || 0;
    var nombreestado = $("#estado-operador option:selected").text().substring(3);
    var direccion = $("#direccion-operador").val();
    var codpostal = $("#cp-operador").val();
    var idmunicipio = $("#municipio-operador").val() || '0';
    var nombremunicipio = $("#municipio-operador option:selected").text();

    var transaccion = (tid != null) ? "actualizaroperador" : "agregaroperador";

    if (isnEmpty(rfc, "rfc-operador") && isnEmpty(estado, "estado-operador") && isnEmpty(codpostal, "cp-operador")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {
                transaccion: transaccion,
                tid: tid,
                id: id,
                nombre: nombre,
                rfc: rfc,
                licencia: licencia,
                estado: estado,
                nombreestado : nombreestado,
                direccion: direccion,
                codpostal: codpostal,
                idmunicipio: idmunicipio,
                nombremunicipio : nombremunicipio,
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#id-operador").val('');
                    $("#nombre-operador").val('');
                    $("#rfc-operador").val('');
                    $("#num-licencia").val('');
                    $("#estado-operador").val('');
                    $("#direccion-operador").val('');
                    $("#cp-operador").val('');
                    $("#municipio-operador").val('');
                    if (tid) {
                        $("#btn-agregar-operador").css("background", "none");
                        $("#btn-agregar-operador").css("color", "#17177C");
                        $("#btn-agregar-operador").html("<span class='fas fa-plus'></span>");
                        $("#btn-agregar-operador").attr("onclick", "agregarOperador()");
                    }
                    tablaOperador();
                }
                cargandoHide();
            }
        });
    }
}

function tablaOperador(uuid = "") {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "tablaoperador", uuid: uuid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#result-operador").html(datos);
            }
        }
    });
}

function editarOperador(idtmp) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "editaroperador", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                setValoresEditarOperador(datos);
            }
        }
    });
}

function setValoresEditarOperador(datos) {
    var array = datos.split("</tr>");
    $("#id-operador").val(array[1]);
    $("#nombre-operador").val(array[2]);
    $("#rfc-operador").val(array[3]);
    $("#num-licencia").val(array[4]);
    $("#direccion-operador").val(array[6]);
    $("#estado-operador").val(array[5]);
    loadOpcionesMunicipioOperador(array[8], array[5]);
    $("#cp-operador").val(array[7]);
    $("#btn-agregar-operador").attr("onclick", "agregarOperador(" + array[0] + ")");
    $("#btn-agregar-operador").css("background", "#5cb85c");
    $("#btn-agregar-operador").css("color", "white");
    $("#btn-agregar-operador").html("<span class='fas fa-save'></span>");
    $("#nombre-operador").focus();
    cargandoHide();
}


function eliminarOperador(tid) {
    alertify.confirm("¿Estás seguro que deseas eliminar este operador?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "eliminaroperador", tid: tid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaOperador();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

//----------------------------------------INSERTAR CARTA
function checkMetodopago() {
    var idmetodopago = $("#id-metodo-pago").val();
    if (idmetodopago == '2') {
        $('#formapago6').prop('selected', true);
        $("#id-forma-pago").prop('disabled', true);
    } else {
        $('#formapago6').removeAttr('selected');
        $("#id-forma-pago").removeAttr('disabled');
    }
}

function insertarFacturaCarta() {
    var folio = $("#folio").val();
    var iddatosF = $("#datos-facturacion").val();
    var idcliente = $("#id-cliente").val() || '0';
    var cliente = $("#nombre-cliente").val();
    var rfccliente = $("#rfc-cliente").val();
    var razoncliente = $("#razon-cliente").val();
    var regfiscal = $("#regfiscal-cliente").val();
    var dircliente = $("#direccion-cliente").val();
    var codpostal = $("#cp-cliente").val();
    var tipoComprobante = $("#tipo-comprobante").val();
    var idformapago = $("#id-forma-pago").val();
    var idmetodopago = $("#id-metodo-pago").val();
    var idmoneda = $("#id-moneda").val();
    var tcambio = $("#tipo-cambio").val();
    var iduso = $("#id-uso").val();
    var periodicidad = $("#periodicidad-factura").val();
    var mesperiodo = $("#mes-periodo").val();
    var anhoperiodo = $("#anho-periodo").val();
    
	var chfirma = 0;
    var cfdis = 0;
	
	if ($("#chfirma").prop('checked')) {
        chfirma = 1;
    }
    if ($("#cfdirel").hasClass('in')) {
        cfdis = 1;
    }

    var tipomov = $("#tipo-movimiento").val();
    var idvehiculo = $("#id-vehiculo").val() || '0';
    var nombrevehiculo = $("#nombre-vehiculo").val();
    var numpermiso = $("#num-permiso").val();
    var tipopermiso = $("#tipo-permiso").val();
    var tipotransporte = $("#conf-transporte").val();
    var modelo = $("#anho-modelo").val();
    var placavehiculo = $("#placa-vehiculo").val();
    var segurorespcivil = $("#seguro-respcivil").val();
    var polizarespcivil = $("#poliza-respcivil").val();
    var seguroambiente = $("#seguro-medambiente").val();
    var polizaambiente = $("#poliza-medambiente").val();

    var idremolque1 = $("#id-remolque1").val() || '0';
    var nombreremolque1 = $("#nombre-remolque1").val();
    var tiporemolque1 = $("#tipo-remolque1").val();
    var placaremolque1 = $("#placa-remolque1").val();

    var idremolque2 = $("#id-remolque2").val() || '0';
    var nombreremolque2 = $("#nombre-remolque2").val();
    var tiporemolque2 = $("#tipo-remolque2").val();
    var placaremolque2 = $("#placa-remolque2").val();

    var idremolque3 = $("#id-remolque3").val() || '0';
    var nombreremolque3 = $("#nombre-remolque3").val();
    var tiporemolque3 = $("#tipo-remolque3").val();
    var placaremolque3 = $("#placa-remolque3").val();
    var flagoperador = $("#flag-operador").val();

    var observaciones = $("#observaciones-carta").val();
    var txtbd = observaciones.replace(new RegExp("\n", 'g'), '<ent>');

    var p_mercancia = $('#total-peso-mercancias').val();
    var p_vehiculo = $('#peso-vehiculo').val();
    var p_bruto = $('#peso-bruto').val();

    
    if($('#peso-vehiculo').val() == ""){
        alertify.error('Ingresa el peso vehicular');
    } 
    else if (isnEmpty(folio, "folio") && isnEmpty(iddatosF, "datos-facturacion") && isnEmpty(rfccliente, "rfc-cliente") && isnEmpty(razoncliente, "razon-cliente") && isnEmpty(regfiscal, "regfiscal-cliente") && isnEmpty(codpostal, "cp-cliente") && isnEmpty(tipoComprobante, "tipo-comprobante") && isnEmpty(idformapago, "id-forma-pago") && isnEmpty(idmetodopago, "id-metodo-pago") && isnEmpty(idmoneda, "id-moneda") && isnEmpty(tcambio, "tipo-cambio") && isnEmpty(iduso, "id-uso") && isnEmpty(tipomov, "tipo-movimiento") && isnEmpty(numpermiso, "num-permiso") && isnEmpty(tipopermiso, "tipo-permiso") && isnEmpty(tipotransporte, "conf-transporte") && isnEmpty(modelo, "anho-modelo") && isnEmpty(placavehiculo, "placa-vehiculo") && isnEmpty(segurorespcivil, "seguro-respcivil") && isnEmpty(polizarespcivil, "poliza-respcivil") && isnEmpty(p_vehiculo, 'peso-vehiculo') && isnEmpty(p_bruto, 'peso-bruto')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "insertarcarta", folio: folio, iddatosF: iddatosF, idcliente: idcliente, cliente:cliente, rfccliente: rfccliente, razoncliente: razoncliente, regfiscal: regfiscal, codpostal: codpostal, idformapago: idformapago, idmetodopago: idmetodopago, idmoneda: idmoneda, tcambio: tcambio, iduso: iduso, periodicidad: periodicidad, mesperiodo: mesperiodo, anhoperiodo: anhoperiodo, tipocomprobante: tipoComprobante, chfirma: chfirma, tipomov: tipomov, idvehiculo: idvehiculo, nombrevehiculo: nombrevehiculo, numpermiso: numpermiso, tipopermiso: tipopermiso, tipotransporte: tipotransporte, modelo: modelo, placavehiculo: placavehiculo, segurorespcivil: segurorespcivil, polizarespcivil: polizarespcivil, idremolque1: idremolque1, nombreremolque1: nombreremolque1, tiporemolque1: tiporemolque1, placaremolque1: placaremolque1, idremolque2: idremolque2, nombreremolque2: nombreremolque2, tiporemolque2: tiporemolque2, placaremolque2: placaremolque2, idremolque3: idremolque3, nombreremolque3: nombreremolque3, tiporemolque3: tiporemolque3, placaremolque3: placaremolque3, seguroambiente: seguroambiente, polizaambiente: polizaambiente, dircliente: dircliente, observaciones: txtbd, cfdis: cfdis, p_vehiculo: p_vehiculo, p_bruto:p_bruto, p_mercancia: p_mercancia},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Carta guardada correctamente');
                    var array = datos.split("<tag>");
                    var tag = array[1];
                    loadView('listacarta');
                    if (idvehiculo == "0" || idremolque1 == '0' || idremolque2 == '0' || idremolque3 == '0' || flagoperador == '0') {
                        console.log(idvehiculo);
                        console.log(idremolque1);
                        console.log(idremolque2);
                        console.log(idremolque3);
                        console.log(flagoperador);
                        checkNuevoRegistro(tag);
                    }
                }
                cargandoHide();
            }
        });
    }
}

function checkNuevoRegistro(tag) {
    alertify.confirm("¿Deseas guardar los nuevos datos registrados?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "nuevosdatos", tag: tag},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Datos guardados correctamente');
                    filtrarCarta();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

//-------------------------------------------------PAGOS
function registrarPago(idcarta) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "getdatospago", idcarta: idcarta},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $('.list-element').removeClass("menu-active");
                $('.marker').removeClass("marker-active");
                $('#pago-menu').addClass("menu-active");
                $('#pago-menu').children('div.marker').addClass("marker-active");
                window.setTimeout("loadView('pago')", 300);
                window.setTimeout("setvaloresRegistrarPago('" + datos + "')", 800);
            }
        }
    });
}

function setvaloresRegistrarPago(datos) {
    var array = datos.split("</tr>");
    var idfactura = array[0];
    var foliofactura = array[1];
    var idcliente = array[2];
    var nombrecliente = array[3];
    var rfccliente = array[4];
    var rzcliente = array[5];
    var cpreceptor = array[6];
    var regfiscal = array[7];
    var iddatosfacturacion = array[8];
    var nombrecontribuyente = array[9];
    var idformapago = array[10];
    var idmoneda = array[11];
    var tcambio = array[12];

    $("#id-cliente").val(idcliente);
    $("#nombre-cliente").val(nombrecliente);
    $("#rfc-cliente").val(rfccliente);
    $("#razon-cliente").val(rzcliente);
    $("#regfiscal-cliente").val(regfiscal);
    $("#cp-cliente").val(cpreceptor);
    $("#option-default-datos").val(iddatosfacturacion);
    $("#option-default-datos").text(nombrecontribuyente);
    
    $.ajax({
        url: "com.sine.enlace/enlacepago.php",
        type: "POST",
        data: {transaccion: "expcomplementos", idformapago:idformapago, idmoneda:idmoneda, tcambio:tcambio, idfactura:idfactura, foliofactura:foliofactura},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<comp>");
                comp = array.length;
                for (i = 0; i < array.length; i++) {
                    var comps = array[i].split("<cut>");
                    $("#tabs").append(comps[0]);
                    $("#complementos").append(comps[1]);
                    var tag1 = comps[2];

                    $(".sub-div").hide();
                    $(".tab-pago").removeClass("sub-tab-active");

                    var first = $("#tabs").find('.tab-pago:first').attr("data-tab");
                    if (first) {
                        $("#complemento-" + first).show();
                        $("#tab-" + first).addClass("sub-tab-active");
                    }
                    tablaRowCFDI(tag1);
                    loadFactura(idfactura, 'c', tag1);
                }
            }
        }
    });
    loadFolioPago();
    cargandoHide();
}