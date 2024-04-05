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

//--------------------------------------------------PRODUCTOS EN CARTA PORTE
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

function editarConcepto(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "editarconcepto", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                setValoresEditarConcepto(datos);
            }
            cargandoHide();
        }
    });
}

function setValoresEditarConcepto(datos) {
    var array = datos.split("</tr>");
    var idtmp = array[0];
    var nombre = array[1];
    var cantidad = array[2];
    var precio = array[3];
    var totunitario = array[4];
    var descuento = array[5];
    var impdescuento = array[6];
    var total = array[7];
    var observaciones = array[8];
    var clvfiscal = array[9];
    var clvunidad = array[10]
    var traslados = array[11];
    var retencion = array[12];

    $("#editar-idtmp").val(idtmp);
    $("#editar-descripcion").val(nombre);
    $("#editar-cfiscal").val(clvfiscal);
    $("#editar-cunidad").val(clvunidad);
    $("#editar-cantidad").val(cantidad);
    $("#editar-precio").val(precio);
    $("#editar-totuni").val(totunitario);
    $("#editar-descuento").val(descuento);
    $("#editar-impdesc").val(impdescuento);
    $("#editar-traslados").html(traslados);
    $("#editar-retencion").html(retencion);
    $("#editar-total").val(Math.floor(total * 100) / 100);
    $("#editar-observaciones").val(observaciones);
}

function actualizarConceptoFactura() {
    var idtmp = $("#editar-idtmp").val();
    var descripcion = $("#editar-descripcion").val();
    var clvfiscal = $("#editar-cfiscal").val();
    var clvunidad = $("#editar-cunidad").val();
    var cantidad = $("#editar-cantidad").val();
    var precio = $("#editar-precio").val();
    var totalunitario = $("#editar-totuni").val();
    var descuento = $("#editar-descuento").val();
    var impdescuento = $("#editar-impdesc").val();
    var total = $("#editar-total").val();
    var observaciones = $("#editar-observaciones").val();

    var traslados = [];
    $.each($("input[name='chtrasedit']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });

    var retenciones = [];
    $.each($("input[name='chretedit']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");
    var idretencion = retenciones.join("<impuesto>");
    if (isnEmpty(idtmp, "editar-idtmp") && isnEmpty(descripcion, "editar-descripcion") && isnEmpty(clvfiscal, "editar-cfiscal") && isnEmpty(clvunidad, "editar-cunidad") && isPositive(cantidad, "editar-cantidad") && isPositive(precio, "editar-precio") && isPositive(descuento, "editar-descuento")) {
        $.ajax({
            url: "com.sine.enlace/enlacefactura.php",
            type: "POST",
            data: {transaccion: "actualizarconcepto", idtmp: idtmp, descripcion: descripcion, clvfiscal: clvfiscal, clvunidad: clvunidad, cantidad: cantidad, precio: precio, totalunitario: totalunitario, descuento: descuento, impdescuento: impdescuento, total: total, observaciones: observaciones, idtraslados: idtraslados, idretencion: idretencion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    tablaProductos();
                    $("#editar-producto").modal('hide');
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
    var array = datos.split("</tr>");
    var idproducto = array[0];
    var codigo = array[1];
    var nombre = array[2];
    var clvunidad = array[3];
    var descripcion_unidad = array[4];
    var unidad = clvunidad + "-" + descripcion_unidad;
    var descripcion_producto = array[5];
    var pcompra = array[6];
    var porcentaje = array[7];
    var ganancia = array[8];
    var pventa = array[9];
    var tipo = array[10];
    var clvfiscal = array[11];
    var descripcionfiscal = array[12];
    var clavefiscal = clvfiscal + "-" + descripcionfiscal;
    var idproveedor = array[13];
    var empresa = array[14];
    var imagen = array[15];
    var chinventario = array[16];
    var cantidad = array[17];
    var img = array[18];

    if (tipo == "1") {
        $("#inventario").removeAttr('hidden');
    } else if (tipo == "2") {
        $("#inventario").attr('hidden', true);
    }

    if (chinventario == '1') {
        $("#chinventario").prop('checked', true);
        $("#cantidad").removeAttr('disabled');
    }

    $("#codigo-producto").val(codigo);
    $("#producto").val(nombre);
    $("#tipo").val(tipo);
    $("#cantidad").val(cantidad);
    $("#clave-unidad").val(unidad);
    $("#descripcion").val(descripcion_producto);
    $("#pcompra").val(pcompra);
    $("#porganancia").val(porcentaje)
    $("#ganancia").val(ganancia);
    $("#pventa").val(pventa);
    $("#clave-fiscal").val(clavefiscal);
    $("#id-proveedor").val(idproveedor);
    $("#filename").val(imagen);
    $("#imgactualizar").val(imagen);

    if (imagen !== '') {
        $("#muestraimagen").html(img);
    }

    $("#btn-form-producto-factura").attr("onclick", "actualizarProductoFactura(" + idproducto + "," + idtmp + ");");
}

function actualizarProductoFactura(idproducto, idtmp) {
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

    if (isnEmpty(codproducto, "codigo-producto") && isnEmpty(producto, "producto") && isList(clavefiscal, "clave-fiscal") && isnEmpty(tipo, "tipo") && isListUnit(unidad, "clave-unidad") && isPositive(porcentaje, "porganancia") && isPositive(ganancia, "ganancia") && isPositive(pventa, "pventa")) {
        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
            type: "POST",
            data: {transaccion: "actualizarproducto", idproducto: idproducto, idtmp: idtmp, codproducto: codproducto, producto: producto, tipo: tipo, unidad: unidad, descripcion: descripcion, pcompra: pcompra, porcentaje: porcentaje, ganancia: ganancia, pventa: pventa, clavefiscal: clavefiscal, idproveedor: idproveedor, imagen: imagen, imgactualizar: imgactualizar, chinventario: chinventario, cantidad: cantidad, insert: 'f'},
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

function incrementarCantidad(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "incrementar", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos();
                cargandoHide();
            }
        }
    });
}

function setCantidad(idtmp, cant) {
    $("#idcant").val(idtmp);
    $("#cantidad-producto").val(cant);
}

function reducirCantidad(idtmp) {
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "reducir", idtmp: idtmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos();
                cargandoHide();
            }
        }
    });
}

function modificarCantidad() {
    var idtmp = $("#idcant").val();
    var cant = $("#cantidad-producto").val();
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "modificartmp", idtmp: idtmp, cant: cant},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos();
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
        tipoImp = parseFloat(div[1]);

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

function checkIVA(idtmp) {
    var traslados = [];
    $.each($("input[name='chtrastabla" + idtmp + "']:checked"), function () {
        traslados.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idtraslados = traslados.join("<impuesto>");

    var retenciones = [];
    $.each($("input[name='chrettabla" + idtmp + "']:checked"), function () {
        retenciones.push(0 + "-" + $(this).val() + "-" + $(this).attr("data-impuesto"));
    });
    var idretenciones = retenciones.join("<impuesto>");
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "chivatmp", idtmp: idtmp, traslados: idtraslados, retenciones: idretenciones},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos();
                cargandoHide();
            }
        }
    });
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
                $("#modal-observaciones").modal('hide');
                tablaProductos(uuid);
            }
            cargandoHide();
        }
    });
}

function calcularImporteEditar() {
    var cantidad = $("#editar-cantidad").val() || '0';
    var precio = $("#editar-precio").val() || '0';

    var importe = parseFloat(cantidad) * parseFloat(precio);
    $("#editar-totuni").val(Math.floor(importe * 100) / 100);
    calcularDescuentoEditar();
}

function calcularDescuentoEditar() {
    var pordesc = $("#editar-descuento").val() || '0';
    var importe = $("#editar-totuni").val() || '0';

    var descuento = parseFloat(importe) * (parseFloat(pordesc) / 100);
    var subtotal = (parseFloat(importe) - parseFloat(descuento));
    var traslados = 0;
    var retencion = 0;

    $.each($("input[name='chtrasedit']:checked"), function () {
        var tasa = $(this).val();
        traslados += parseFloat(subtotal) * parseFloat(tasa);
    });

    $.each($("input[name='chretedit']:checked"), function () {
        var tasa = $(this).val();
        retencion += parseFloat(subtotal) * parseFloat(tasa);
    });
    var total = (parseFloat(subtotal) + parseFloat(traslados)) - parseFloat(retencion);
    $("#editar-impdesc").val(Math.floor(descuento * 100) / 100);
    $("#editar-total").val(Math.floor(total * 100) / 100);
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
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "tablaimgs", d: d},
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

function displayIMG(id) {
    $.ajax({
        url: "com.sine.imprimir/img.php",
        type: "POST",
        data: { img: id },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                var array = datos.split("<type>");
                console.log(array);
                var t = array[0];
                var data = array[1];
                if (t == 'd') {
                    var newTab = window.open('com.sine.imprimir/img.php?doc=' + id, '_blank');
                    newTab.document.body.innerHTML = data;
                } else {
                    $("#archivos").modal('show');
                    $('#fotito').html(data);
                }
            }
        }
    });
}

function eliminarIMG(idtmp) {
    alertify.confirm("¿Estás seguro que deseas eliminar este archivo?", function () {
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "eliminarimg", idtmp: idtmp},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                }
                tablaEvidencias('1');
            }
        });
    }).set({title: "Q-ik"});
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
            loadOpcionesEstado('contenedor-estado-op', 'estado-operador', idestado);
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
                    $("#peso-mercancia").val('0');
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
    var cp = $("#codigo_postal").val();
    var distancia = $("#distancia-ubicacion").val();
    var fecha = $("#fecha-llegada").val();
    var hora = $("#hora-llegada").val();

    var transaccion = (tid != null) ? "actualizarubicacion" : "agregarubicacion";

    if (isnEmpty(rfc, "rfc-ubicacion") && isnEmpty(tipo, "tipo-ubicacion") && isnEmpty(idestado, "id-estado") && isnEmpty(cp, "codigo_postal") && isPositive(distancia, "distancia-ubicacion") && isnEmpty(fecha, "fecha-llegada") && isnEmpty(hora, "hora-llegada")) {
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
                    $("#distancia-ubicacion").val('0');
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
    $("#codigo_postal").val(array[6]);
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

function insertarFacturaCarta(tag = null) {
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

    var nombre_comprobante = $("#tipo-comprobante option:selected").text();
    var nombre_metodo = $("#id-metodo-pago option:selected").text();
    var nombre_forma = $("#id-forma-pago option:selected").text();
    var nombre_moneda = $("#id-moneda option:selected").text();
    var nombre_cdfi = $("#id-uso option:selected").text();

    var periodicidad = $("#periodicidad-factura").val();
    var mesperiodo = $("#mes-periodo").val();
    var anhoperiodo = $("#anho-periodo").val();

    var chfirma = $("#chfirma").prop('checked') ? 1 : 0;
    var cfdis = $("#cfdirel").hasClass('in') ? 1 : 0;

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

    var uuid = $("#uuidfactura").val();

    var p_mercancia = $('#total-peso-mercancias').val();
    var p_vehiculo = $('#peso-vehiculo').val();
    var p_bruto = $('#peso-bruto').val();

    if ($('#peso-vehiculo').val() == "") {
        alertify.error('Ingresa el peso vehicular');
    } else if (isnEmpty(folio, "folio") && isnEmpty(iddatosF, "datos-facturacion") && isnEmpty(rfccliente, "rfc-cliente") && isnEmpty(razoncliente, "razon-cliente") && isnEmpty(regfiscal, "regfiscal-cliente") && isnEmpty(codpostal, "cp-cliente") && isnEmpty(tipoComprobante, "tipo-comprobante") && isnEmpty(idformapago, "id-forma-pago") && isnEmpty(idmetodopago, "id-metodo-pago") && isnEmpty(idmoneda, "id-moneda") && isnEmpty(tcambio, "tipo-cambio") && isnEmpty(iduso, "id-uso") && isnEmpty(tipomov, "tipo-movimiento") && isnEmpty(numpermiso, "num-permiso") && isnEmpty(tipopermiso, "tipo-permiso") && isnEmpty(tipotransporte, "conf-transporte") && isnEmpty(modelo, "anho-modelo") && isnEmpty(placavehiculo, "placa-vehiculo") && isnEmpty(segurorespcivil, "seguro-respcivil") && isnEmpty(polizarespcivil, "poliza-respcivil") && isnEmpty(p_vehiculo, 'peso-vehiculo') && isnEmpty(p_bruto, 'peso-bruto')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {
                transaccion: (tag != null) ? "actualizarcarta" : "insertarcarta",
                tag: tag,
                folio: folio,
                iddatosF: iddatosF,
                idcliente: idcliente,
                cliente: cliente,
                rfccliente: rfccliente,
                razoncliente: razoncliente,
                regfiscal: regfiscal,
                codpostal: codpostal,
                idformapago: idformapago,
                idmetodopago: idmetodopago,
                idmoneda: idmoneda,
                tcambio: tcambio,
                iduso: iduso,
                tipocomprobante: tipoComprobante,
                periodicidad: periodicidad,
                mesperiodo: mesperiodo,
                anhoperiodo: anhoperiodo,
                chfirma: chfirma,
                tipomov: tipomov,
                idvehiculo: idvehiculo,
                nombrevehiculo: nombrevehiculo,
                numpermiso: numpermiso,
                tipopermiso: tipopermiso,
                tipotransporte: tipotransporte,
                modelo: modelo,
                placavehiculo: placavehiculo,
                segurorespcivil: segurorespcivil,
                polizarespcivil: polizarespcivil,
                idremolque1: idremolque1,
                nombreremolque1: nombreremolque1,
                tiporemolque1: tiporemolque1,
                placaremolque1: placaremolque1,
                idremolque2: idremolque2,
                nombreremolque2: nombreremolque2,
                tiporemolque2: tiporemolque2,
                placaremolque2: placaremolque2,
                idremolque3: idremolque3,
                nombreremolque3: nombreremolque3,
                tiporemolque3: tiporemolque3,
                placaremolque3: placaremolque3,
                seguroambiente: seguroambiente,
                polizaambiente: polizaambiente,
                dircliente: dircliente,
                observaciones: txtbd,
                cfdis: cfdis,
                p_vehiculo: p_vehiculo,
                p_bruto: p_bruto,
                p_mercancia: p_mercancia,
                nombre_comprobante: nombre_comprobante,
                nombre_metodo: nombre_metodo,
                nombre_forma: nombre_forma,
                nombre_moneda: nombre_moneda,
                nombre_cdfi: nombre_cdfi,
                uuid: uuid
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success((tag !== null) ? 'Datos de carta actualizados' : 'Carta guardada correctamente');
                    var array = datos.split("<tag>");
                    var tagins = array[1];
                    loadView('listacarta');
                    if(tag == null){
                        if (idvehiculo == "0" || idremolque1 == '0' || idremolque2 == '0' || idremolque3 == '0' || flagoperador == '0') {
                            checkNuevoRegistro(tagins);
                        }
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
                    console.log(datos);
                    var array = datos.split("</tr>");
                    if (array[0] === "1") {
                        alertify.success('Datos de vehículo guardados correctamente.');
                    } else if (array[0] !== "0") {
                        alertify.error(array[0]);
                    }

                    if (array[1] === "1") {
                        alertify.success('Datos de remolque 1 guardados correctamente.');
                    } else if (array[1] !== "0") {
                        alertify.error(array[1]);
                    }

                    if (array[2] === "1") {
                        alertify.success('Datos de remolque 2 guardados correctamente.');
                    } else if (array[2] !== "0") {
                        alertify.error(array[2]);
                    }

                    if (array[3] === "1") {
                        alertify.success('Datos de remolque 3 guardados correctamente.');
                    } else if (array[3] !== "0") {
                        alertify.error(array[3]);
                    }

                    if (array[4] === "1") {
                        alertify.success('Datos de operador guardados correctamente.');
                    } else if (array[4] !== "0") {
                        alertify.error(array[4]);
                    }
                    filtrarCarta();
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}

function editarCarta(cid) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "editarcarta", cid: cid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('carta');
                window.setTimeout("setValoresEditarCarta('" + datos + "')", 900);
            }
        }
    });
}

function setValoresEditarCarta(datos) {
    changeText("#contenedor-titulo-form-carta", "Editar carta");
    changeText("#btn-form-carta", "Guardar cambios <span class='fas fa-save'></span>");

    var array = datos.split("</tr>");
    var idfactura = array[0];
    var fechacreacion = array[1];
    var rfcemisor = array[2];
    var rzsocial = array[3];
    var clvreg = array[4];
    var regimen = array[5];
    var codpostal = array[6];
    var serie = array[7];
    var letra = array[8];
    var folio = array[9];
    var idcliente = array[10];
    var cliente = array[11];
    var rfccliente = array[12];
    var rzcliente = array[13];
    var cpreceptor = array[14];
    var regfiscalreceptor = array[15];
    var chfirmar = array[16];
    var idforma_pago = array[17];
    var idmetodo_pago = array[18];
    var idmoneda = array[19];
    var tcambio = array[20];
    var iduso_cfdi = array[21];
    var idtipo_comprobante = array[22];
    var uuid = array[23];
    var iddatos = array[24];
    var iddatoscarta = array[25];
    var tipomovimiento = array[26];
    var idvehiculo = array[27];
    var nombrevehiculo = array[28];
    var numpermiso = array[29];
    var tipopermiso = array[30];
    var tipotransporte = array[31];
    var anhomod = array[32];
    var placa = array[33];
    var segurocivil = array[34];
    var polizaseguro = array[35];
    var idremolque1 = array[36];
    var nmremolque1 = array[37];
    var tiporemolque1 = array[38];
    var placaremolque1 = array[39];
    var idremolque2 = array[40];
    var nmremolque2 = array[41];
    var tiporemolque2 = array[42];
    var placaremolque2 = array[43];
    var idremolque3 = array[44];
    var nmremolque3 = array[45];
    var tiporemolque3 = array[46];
    var placaremolque3 = array[47];
    var tag = array[48];
    var seguroambiente = array[49];
    var polizaambiente = array[50];
    var periodoglobal = array[51];
    var mesperiodo = array[52];
    var anhoperiodo = array[53];
    var dircliente = array[54];
    var observaciones = array[55];
	var cfdisrel = array[56];
	var pesovehicular = array[57];
	var pesobruto = array[58];
    
    var txt = observaciones.replace(new RegExp("<ent>", 'g'), "\n");

    var divf = fechacreacion.split("-");
    fechacreacion = divf[2] + "/" + divf[1] + "/" + divf[0];

    if (uuid != "") {
        $(".not-timbre").html("<div class='alert alert-danger ps-4'><label class='label-required text-danger fw-bold'>* Esta factura ya ha sido timbrada, por lo que solo puedes editar la dirección del cliente, las observaciones de productos y modificar la firma del contribuyente.</label></div>");
        $("#btn-agregar-cfdi").attr("disabled", true);
        $("#folio").attr("disabled", true);
        $("#datos-facturacion").attr("disabled", true);
        $("#nombre-cliente").attr("disabled", true);
        $("#direccion-cliente").attr("disabled", true);
        $("#rfc-cliente").attr("disabled", true);
        $("#razon-cliente").attr("disabled", true);
        $("#regfiscal-cliente").attr("disabled", true);
        $("#cp-cliente").attr("disabled", true);
        $("#tipo-comprobante").attr("disabled", true);
        $("#id-forma-pago").attr("disabled", true);
        $("#id-metodo-pago").attr("disabled", true);
        $("#id-moneda").attr("disabled", true);
        $("#id-uso").attr("disabled", true);
        $("#periodicidad-factura").attr("disabled", true);
        $("#mes-periodo").attr("disabled", true);
        $("#anho-periodo").attr("disabled", true);
        $("#btn-nuevo-producto").attr("disabled", true);
        $("#btn-agregar-productos").attr("disabled", true);

        $("#tipo-movimiento").attr("disabled", true);
        $("#btn-agregar-mercancia").attr("disabled", true);
        $("#nombre-vehiculo").attr("disabled", true);
        $("#num-permiso").attr("disabled", true);
        $("#tipo-permiso").attr("disabled", true);
        $("#conf-transporte").attr("disabled", true);
        $("#anho-modelo").attr("disabled", true);
        $("#placa-vehiculo").attr("disabled", true);
        $("#seguro-respcivil").attr("disabled", true);
        $("#poliza-respcivil").attr("disabled", true);
        $("#seguro-medambiente").attr("disabled", true);
        $("#poliza-medambiente").attr("disabled", true);

        $("#nombre-remolque1").attr("disabled", true);
        $("#tipo-remolque1").attr("disabled", true);
        $("#placa-remolque1").attr("disabled", true);
        $("#nombre-remolque2").attr("disabled", true);
        $("#tipo-remolque2").attr("disabled", true);
        $("#placa-remolque2").attr("disabled", true);
        $("#nombre-remolque3").attr("disabled", true);
        $("#tipo-remolque3").attr("disabled", true);
        $("#placa-remolque3").attr("disabled", true);

        $("#btn-agregar-ubicacion").attr("disabled", true);
        $("#btn-agregar-operador").attr("disabled", true);
        $('#peso-vehiculo').attr("disabled", true);
        $('#chfirma').attr("disabled", true);
    
    	$("#rfc-emisor").val(rfcemisor);
        $("#razon-emisor").val(rzsocial);
        $("#regimen-emisor").val(clvreg + "-" + regimen);
        $("#cp-emisor").val(codpostal);
    } else {
    	loadFolioCarta(iddatos);
        if (idmoneda != "1") {
            $("#tipo-cambio").removeAttr('disabled');
        } else {
            $("#tipo-cambio").attr('disabled', true);
        }
    }
	
	if (cfdisrel == 1) {
        $.ajax({
            url: "com.sine.enlace/enlacefactura.php",
            type: "POST",
            data: {transaccion: "cfdisrelacionados", tag: tag, uuid: uuid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var array = datos.split("<corte>");
                    var p1 = array[0];
                    var p2 = array[1];
                    $("#body-lista-cfdi").html(p2);
                    $("#cfdirel").addClass('in');
                }
            }
        });
    }

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "prodfactura", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos(uuid);
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "mercanciacarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaMercancia(uuid);
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "ubicacioncarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaUbicacion(uuid);
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "operadorcarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaOperador(uuid);
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "doccarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            tablaEvidencias('1');
        }
    });

    loadOpcionesFolios('0', serie, letra+folio);
	$("#rfc-emisor").val(rfcemisor);
    $("#razon-emisor").val(rzsocial);
    $("#regimen-emisor").val(clvreg + "-" + regimen);
    $("#cp-emisor").val(codpostal);
    $("#fecha-creacion").val(fechacreacion);
    loadOpcionesFacturacion(iddatos);
    $("#id-cliente").val(idcliente);
    $("#nombre-cliente").val(cliente);
    $("#rfc-cliente").val(rfccliente);
    $("#razon-cliente").val(rzcliente);
    $("#regfiscal-cliente").val(regfiscalreceptor);
    $("#direccion-cliente").val(dircliente);
    $("#cp-cliente").val(cpreceptor);
    loadOpcionesComprobante('tipo-comprobante', idtipo_comprobante);
    loadOpcionesMetodoPago('id-metodo-pago', idmetodo_pago);
    loadOpcionesFormaPago2(idforma_pago);
    loadOpcionesMoneda('id-moneda', idmoneda);
    $("#tipo-cambio").val(tcambio);
    loadOpcionesUsoCFDI('id-uso', iduso_cfdi);
    opcionesPeriodoGlobal('periodicidad-factura', periodoglobal);
    opcionesMeses('mes-periodo', mesperiodo);
    if (anhoperiodo != "") {
        $("#option-default-anho-periodo").val(anhoperiodo);
        $("#option-default-anho-periodo").text(anhoperiodo);
    }

    if (chfirmar == '1') {
        $("#chfirma").attr('checked', true);
    }

    $("#tipo-movimiento").val(tipomovimiento);
    $("#id-vehiculo").val(idvehiculo);
    $("#nombre-vehiculo").val(nombrevehiculo);
    $("#num-permiso").val(numpermiso);
    $("#tipo-permiso").val(tipopermiso);
    $("#conf-transporte").val(tipotransporte);
    $("#anho-modelo").val(anhomod);
    $("#placa-vehiculo").val(placa);
    $("#seguro-respcivil").val(segurocivil);
    $("#poliza-respcivil").val(polizaseguro);
    $("#seguro-medambiente").val(seguroambiente);
    $("#poliza-medambiente").val(polizaambiente);
    $("#id-remolque1").val(idremolque1);
    $("#nombre-remolque1").val(nmremolque1);
    $("#tipo-remolque1").val(tiporemolque1);
    $("#placa-remolque1").val(placaremolque1);
    $("#id-remolque2").val(idremolque2);
    $("#nombre-remolque2").val(nmremolque2);
    $("#tipo-remolque2").val(tiporemolque2);
    $("#placa-remolque2").val(placaremolque2);
    $("#id-remolque3").val(idremolque3);
    $("#nombre-remolque3").val(nmremolque3);
    $("#tipo-remolque3").val(tiporemolque3);
    $("#placa-remolque3").val(placaremolque3);
    $("#observaciones-carta").val(txt);

    $('#peso-vehiculo').val(pesovehicular);
    $('#peso-bruto').val(pesobruto);

    $("#form-carta").append("<input type='hidden' id='uuidfactura' name='uuidfactura' value='" + uuid + "'/>");
    $("#btn-form-carta").attr("onclick", "insertarFacturaCarta('"+tag+"');");
    cargandoHide();

}

function eliminarCarta(cid) {
    alertify.confirm("¿Estás seguro que deseas eliminar esta factura con complemento carta porte?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "eliminarcarta", cid: cid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Se elimino correctamente la factura con complemento carta porte.')
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
                    var forma = comps[3];
                    var moneda = comps[4];

                    $(".sub-div").hide();
                    $(".tab-pago").removeClass("sub-tab-active");

                    var first = $("#tabs").find('.tab-pago:first').attr("data-tab");
                    if (first) {
                        $("#complemento-" + first).show();
                        $("#tab-" + first).addClass("sub-tab-active");
                    }
                    tablaRowCFDI(tag1, "", moneda);
                    loadFormaPago(tag1, forma);
                    loadMonedaComplemento(tag1, moneda);
                    disableCuenta(tag1, forma);
                    loadFormaPago(tag1, forma);
                    loadMonedaComplemento(tag1, moneda);
                    loadFactura(idfactura, 'c', tag1);
                }
            }
        }
    });
    loadFolioPago();
    cargandoHide();
}

function getClientebyRFC() {
    var rfc = $("#rfc-cliente").val();
    if (rfc != "") {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "getcliente", rfc: rfc},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    var array = res.split("</tr>");
                    $("#id-cliente").val(array[0]);
                    $("#razon-cliente").val(array[1]);
                    $("#regfiscal-cliente").val(array[2]);
                    $("#cp-cliente").val(array[3]);
                    $("#nombre-cliente").val(array[4]);
                    $("#direccion-cliente").val(array[5]);
                }
                cargandoHide();
            }
        });
    }
}

//--------------------------------------------------IMPRIMIT
function imprimirCarta(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimircarta.php?carta=' + id, 'Carta', '', '1024', '768', 'true');
    cargandoHide();
}

function copiarCarta(cid) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "editarcarta", cid: cid},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('carta');
                window.setTimeout("setValoresCopiarCarta('" + datos + "')", 600);
            }
        }
    });
}

function setValoresCopiarCarta(datos) {
    var array = datos.split("</tr>");
    var fechacreacion = array[1];
    var rfcemisor = array[2];
    var rzsocial = array[3];
    var clvreg = array[4];
    var regimen = array[5];
    var codpostal = array[6];
    var idcliente = array[10];
    var cliente = array[11];
    var rfccliente = array[12];
    var rzcliente = array[13];
    var cpreceptor = array[14];
    var regfiscalreceptor = array[15];
    var chfirmar = array[16];
    var idforma_pago = array[17];
    var idmetodo_pago = array[18];
    var idmoneda = array[19];
    var tcambio = array[20];
    var iduso_cfdi = array[21];
    var idtipo_comprobante = array[22];
    var iddatos = array[24];
    var tipomovimiento = array[26];
    var idvehiculo = array[27];
    var nombrevehiculo = array[28];
    var numpermiso = array[29];
    var tipopermiso = array[30];
    var tipotransporte = array[31];
    var anhomod = array[32];
    var placa = array[33];
    var segurocivil = array[34];
    var polizaseguro = array[35];
    var idremolque1 = array[36];
    var nmremolque1 = array[37];
    var tiporemolque1 = array[38];
    var placaremolque1 = array[39];
    var idremolque2 = array[40];
    var nmremolque2 = array[41];
    var tiporemolque2 = array[42];
    var placaremolque2 = array[43];
    var idremolque3 = array[44];
    var nmremolque3 = array[45];
    var tiporemolque3 = array[46];
    var placaremolque3 = array[47];
    var tag = array[48];
    var seguroambiente = array[49];
    var polizaambiente = array[50];
    var periodoglobal = array[51];
    var mesperiodo = array[52];
    var anhoperiodo = array[53];
    var dircliente = array[54];
    var observaciones = array[55];
    var pesovehicular = array[57];
	var pesobruto = array[58];
    var txt = observaciones.replace(new RegExp("<ent>", 'g'), "\n");

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "prodfactura", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaProductos();
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "mercanciacarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaMercancia();
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "ubicacioncarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaUbicacion();
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "operadorcarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                tablaOperador();
            }
        }
    });

    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "doccarta", tag: tag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            tablaEvidencias('1');
        }
    });

	$("#rfc-emisor").val(rfcemisor);
    $("#razon-emisor").val(rzsocial);
    $("#regimen-emisor").val(clvreg + "-" + regimen);
    $("#cp-emisor").val(codpostal);
    $("#fecha-creacion").val(fechacreacion);
    loadOpcionesFacturacion(iddatos);
    $("#id-cliente").val(idcliente);
    $("#nombre-cliente").val(cliente);
    $("#rfc-cliente").val(rfccliente);
    $("#razon-cliente").val(rzcliente);
    $("#regfiscal-cliente").val(regfiscalreceptor);
    $("#direccion-cliente").val(dircliente);
    $("#cp-cliente").val(cpreceptor);
    loadOpcionesComprobante('tipo-comprobante', idtipo_comprobante);
    loadOpcionesMetodoPago('id-metodo-pago', idmetodo_pago);
    loadOpcionesFormaPago2(idforma_pago);
    loadOpcionesMoneda('id-moneda', idmoneda);
    $("#tipo-cambio").val(tcambio);
    loadOpcionesUsoCFDI('id-uso', iduso_cfdi);
    opcionesPeriodoGlobal('periodicidad-factura', periodoglobal);
    opcionesMeses(mesperiodo);
    if (anhoperiodo != "") {
        $("#option-default-anho-periodo").val(anhoperiodo);
        $("#option-default-anho-periodo").text(anhoperiodo);
    }

    if (chfirmar == '1') {
        $("#chfirma").attr('checked', true);
    }

    $("#tipo-movimiento").val(tipomovimiento);
    $("#id-vehiculo").val(idvehiculo);
    $("#nombre-vehiculo").val(nombrevehiculo);
    $("#num-permiso").val(numpermiso);
    $("#tipo-permiso").val(tipopermiso);
    $("#conf-transporte").val(tipotransporte);
    $("#anho-modelo").val(anhomod);
    $("#placa-vehiculo").val(placa);
    $("#seguro-respcivil").val(segurocivil);
    $("#poliza-respcivil").val(polizaseguro);
    $("#seguro-medambiente").val(seguroambiente);
    $("#poliza-medambiente").val(polizaambiente);
    $("#id-remolque1").val(idremolque1);
    $("#nombre-remolque1").val(nmremolque1);
    $("#tipo-remolque1").val(tiporemolque1);
    $("#placa-remolque1").val(placaremolque1);
    $("#id-remolque2").val(idremolque2);
    $("#nombre-remolque2").val(nmremolque2);
    $("#tipo-remolque2").val(tiporemolque2);
    $("#placa-remolque2").val(placaremolque2);
    $("#id-remolque3").val(idremolque3);
    $("#nombre-remolque3").val(nmremolque3);
    $("#tipo-remolque3").val(tiporemolque3);
    $("#placa-remolque3").val(placaremolque3);
    $("#observaciones-carta").val(txt);

    $('#peso-vehiculo').val(pesovehicular);
    $('#peso-bruto').val(pesobruto);

    loadFolioCarta(iddatos);
    cargandoHide();
}

function verEvidencias(id) {
    $("#modal-evidencia").modal('show');
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "tablaimg", id: id},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                if(datos == '<tbody>'){
                    $("#tabla-evidencias").html("<div class='col-12 text-center py-3'>Este comunicado no contiene archivos.</div>");
                } else {
                    $("#tabla-evidencias").html(datos);
                }
            }
            cargandoHide();
        }
    });
}

function visutab(archivo, ext) {
    var ruta = "./cartaporte/" + archivo;
    if (ext == "jpg" || ext == "png" || ext == "jpeg" || ext == "gif") {
        $("#fotito").html('<img src="' + ruta + '"/>')
    } else {
        $('#fotito').html('<embed type="application/pdf" src="' + ruta + '"  width="100%" style="height: 31rem"/>');
    }
}

function cerrarModal(){
    $('#archivo').modal('hide');
    $('#fotito').html("");
}

function showCorreosCarta(idfactura) {
    cargandoHide();
    cargandoShow();
    $("#btn-enviar-carta").attr("onclick", "enviarCarta(" + idfactura + ")");
    $("#correo1").val('');
    $("#correo2").val('');
    $("#correo3").val('');
    $("#correo4").val('');
    $("#correo5").val('');
    $("#correo6").val('');
    getCorreos(idfactura);
    $("#chcorreo1").prop('checked', false);
    $("#chcorreo2").prop('checked', true);
    $("#chcorreo3").prop('checked', false);
    $("#chcorreo4").prop('checked', false);
    $("#chcorreo5").prop('checked', false);
    $("#chcorreo6").prop('checked', false);
    cargandoHide();
}

function getCorreos(idfactura) {
    $.ajax({
        url: "com.sine.enlace/enlacecarta.php",
        type: "POST",
        data: {transaccion: "getcorreos", idfactura: idfactura},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                $("#correo1").val(array[0]);
                $("#correo2").val(array[1]);
                $("#correo3").val(array[2]);
                $("#correo4").val(array[3]);
                $("#correo5").val(array[4]);
                $("#correo6").val(array[5]);
            }
        }
    });
}

function enviarCarta(id) {
    var mailalt1 = "ejemplo@ejemplo.com";
    var mailalt2 = "ejemplo@ejemplo.com";
    var mailalt3 = "ejemplo@ejemplo.com";
    var mailalt4 = "ejemplo@ejemplo.com";
    var mailalt5 = "ejemplo@ejemplo.com";
    var mailalt6 = "ejemplo@ejemplo.com";
    var chcorreo1 = 0;
    var chcorreo2 = 0;
    var chcorreo3 = 0;
    var chcorreo4 = 0;
    var chcorreo5 = 0;
    var chcorreo6 = 0;

    if ($("#chcorreo1").prop('checked')) {
        chcorreo1 = 1;
        mailalt1 = $("#correo1").val();
    }
    if ($("#chcorreo2").prop('checked')) {
        chcorreo2 = 1;
        mailalt2 = $("#correo2").val();
    }
    if ($("#chcorreo3").prop('checked')) {
        chcorreo3 = 1;
        mailalt3 = $("#correo3").val();
    }
    if ($("#chcorreo4").prop('checked')) {
        chcorreo4 = 1;
        mailalt4 = $("#correo4").val();
    }
    if ($("#chcorreo5").prop('checked')) {
        chcorreo5 = 1;
        mailalt5 = $("#correo5").val();
    }
    if ($("#chcorreo6").prop('checked')) {
        chcorreo6 = 1;
        mailalt6 = $("#correo6").val();
    }

    if (isEmailtoSend(mailalt1, "correo1") && isEmailtoSend(mailalt2, "correo2") && isEmailtoSend(mailalt3, "correo3") && isEmailtoSend(mailalt4, "correo4") && isEmailtoSend(mailalt5, "correo5") && isEmailtoSend(mailalt6, "correo6") && isCheckedMailSend(chcorreo1, chcorreo2, chcorreo3, chcorreo4, chcorreo5, chcorreo6)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.imprimir/imprimircarta.php",
            type: "POST",
            data: {transaccion: "pdf", id: id, ch1: chcorreo1, ch2: chcorreo2, ch3: chcorreo3, ch4: chcorreo4, ch5: chcorreo5, ch6: chcorreo6, mailalt1: mailalt1, mailalt2: mailalt2, mailalt3: mailalt3, mailalt4: mailalt4, mailalt5: mailalt5, mailalt6: mailalt6},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#enviarmail").modal('hide');
                    alertify.success(res);
                }
                cargandoHide();
            }
        });
    }
}

//-------------------------------------TIMBRAR
function timbrarCarta(id) {
    alertify.confirm("¿Estás seguro que deseas timbrar esta carta?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacecarta.php",
            type: "POST",
            data: {transaccion: "xml", id: id},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success("Carta timbrada correctamente");
                    loadView('listacarta');
                }
                cargandoHide();
            }
        });
    }).set({title: "Q-ik"});
}