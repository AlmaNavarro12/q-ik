function buscarProducto(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: { transaccion: "filtrarproducto", NOM: $("#buscar-producto").val(), pag: pag, numreg: $("#num-reg").val() },
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
        data: { transaccion: "filtrarproducto", NOM: $("#buscar-producto").val(), pag: pag, numreg: $("#num-reg").val() },
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
            changeText("#labelinventario", "¿Desactivar inventario?")
        } else {
            $("#cantidad").attr('disabled', true);
            $("#cantidad").val('0');
            changeText("#labelinventario", "¿Activar inventario?")
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
        source: "../CATSAT/CATSAT/com.sine.enlace/enlaceClaveUnidad.php?transaccion=autocompleta",
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
        //impuesto = Math.round(impuesto * 100) / 100;

        if (tipoImp == 1) {
            total = parseFloat(total) + parseFloat(impuesto);
        }
        else if (tipoImp == 2) {
            total = parseFloat(total) - parseFloat(impuesto);
        }
        $('#p' + id).val(impuesto);
    });
    $("#ptotiva").val(total);
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
    $("#pventa").val(total);
}

function cargarImgProducto() {
    cargandoShow();
    cargandoHide();
    var formData = new FormData();
    var imgInput = $("#imagen")[0].files[0];
    var rutaProductos = "temporal/productos/";
    if (imgInput) {
        formData.append("imagen", imgInput);
        formData.append("ruta_personalizada", rutaProductos);
        $.ajax({
            url: 'com.sine.enlace/cargarimg.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var array = datos.split("<corte>");
                var view = array[0];
                var fn = array[1];
                if(view != ""){
                    $("#imagenproducto").show('slow');
                    $("#muestraimagenproducto").html(view);
                    $("#filename").val(fn);
                    $("#imagen").val('');
                }
                cargandoHide();
            }
        });
    } else {
        alert("Por favor selecciona una imagen.");
    }
}

function eliminarImgTpm() {
    var imgtmp = $("#filename").val();
    if (imgtmp != '') {
        $.ajax({
            data: { transaccion: "eliminarimgtmp", imgtmp: imgtmp},
            url: 'com.sine.enlace/enlaceproducto.php',
            type: 'POST',
            dataType: 'JSON',
            success: function (datos) {
                cargandoHide();
                console.log(datos);
            }
        });
    }
}

function gestionarProducto(idproducto = null) {
    var codproducto = $("#codigo-producto").val();
    var producto = $("#producto").val();
    var tipo = $("#tipo").val();
    var unidad = $("#clave-unidad").val();
    var descripcion = $("#descripcion").val();
    var pcompra = $("#pcompra").val() || '0';
    var porcentaje = $("#porganancia").val() || '0';
    var ganancia = $("#ganancia").val() || '0';
    var pventa = $("#pventa").val();
    var clavefiscal = $("#clave-fiscal").val();
    var idproveedor = $("#id-proveedor").val() || '0';
    var imagen = $('#filename').val();
    var chinventario = 0;
    var imgactualizar= $("#imgactualizar").val();
    var nameimg= $("#nameimg").val();
    var cantidad = $("#cantidad").val() || '0';
    if ($("#chinventario").prop('checked')) {
        chinventario = 1;
    }

    var imp_apl = "";
    $("input[name=taxes]:checked").each(function () {
        imp_apl += $(this).val() + "<tr>";
    });

    if (isnEmpty(codproducto, "codigo-producto") &&
        isnEmpty(producto, "producto") &&
        isList(clavefiscal, "clave-fiscal") &&
        isnEmpty(tipo, "tipo") &&
        isList(unidad, "clave-unidad") &&
        isPositive(pventa, "pventa")) {

        cargandoHide();
        cargandoShow();

        var url = "com.sine.enlace/enlaceproducto.php";
        var transaccion = idproducto ? "actualizarproducto" : "insertarproducto";
        var data = {
            transaccion: transaccion,
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
            chinventario: chinventario,
            cantidad: cantidad,
            imp_apl: imp_apl,
            idproducto: idproducto,
            insert: null,
            imgactualizar: imgactualizar,
            nameimg: nameimg
        };

        var mensaje = idproducto ? "Producto actualizado." : "Producto registrado.";
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
                    alertify.success(mensaje);
                    loadView('listaproductoaltas');
                }
                cargandoHide();
            }
        });
    }
}

function getOptionsTaxes(taxes = "") {
    cargandoShow();
    $.ajax({
        data: { transaccion: "taxesproductos", taxes: taxes },
        url: 'com.sine.enlace/enlaceproducto.php',
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
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

function habilitaImp(val) {
    var div = val.split('-');
    var porcentaje = div[0];
    var impuesto = div[1];
    var id = div[2];
    if ($('#imp' + impuesto).is(':checked')) {
        $('#CalcImp' + impuesto).show(1500, "easeOutQuint");
    } else {
        $('#CalcImp' + impuesto).hide(1500, "easeOutQuint");
    }
    if ($('#pventa').val() != "") {
        calcularImpuestosTotal();
    } else {
        calcularImpuestosTotalReverse();
    }
}

function desactivarInventario(idproducto) {
    alertify.confirm("¿Estás seguro que deseas desactivar el inventario de este producto? Si se desactiva la cantidad registrada se establecera en 0", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
            type: "POST",
            data: { transaccion: "desactivarinventario", idproducto: idproducto, cantidad: 0, estado: 0 },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    loadView('listaproductoaltas');
                }
                cargandoHide();
            }
        });
    }).set({ title: "Q-ik" });
}

function cambiarCantidad() {
    var cantidad = $("#cantidad-nueva").val();
    if (isnEmpty(cantidad, "cantidad-nueva")) {
        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
            type: "POST",
            data: { transaccion: "cambiarcantidad", idproducto: $("#idproducto-inventario").val(), cantidad: cantidad },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    $("#cambiarcantidad").modal('hide');
                    window.setTimeout("loadView('listaproductoaltas')", 200);
                }
            }
        });
    }
}

function setCant(idproducto) {
    $("#idproducto").val(idproducto);
    $("#cantidad-inventario").val('0');
}

function setCantInventario(idproducto, cantidad) {
    $("#idproducto-inventario").val(idproducto);
    $("#cantidad-nueva").val(cantidad);
}

function estadoInventario() {
    var cantidad = $("#cantidad-inventario").val();
    if (isnEmpty(cantidad, "cantidad-inventario")) {
        alertify.confirm("¿Estás seguro que deseas activar el inventario de este producto?", function () {
            $.ajax({
                url: "com.sine.enlace/enlaceproducto.php",
                type: "POST",
                data: { transaccion: "activarinventario", idproducto: $("#idproducto").val(), cantidad: cantidad, estado: 1 },
                success: function (datos) {
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 1000);
                    if (bandera == '0') {
                        alertify.error(res);
                    } else {
                        $("#cambiarestado").modal('hide');
                        window.setTimeout("loadView('listaproductoaltas')", 200);
                        alertify.success(datos);
                    }
                }
            });
        }).set({ title: "Q-ik" });
    }
}

function editarProducto(idproducto) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: {transaccion: "editarproducto", idproducto: idproducto},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('nuevoproducto', '1');
                window.setTimeout("setValoresEditarProducto('" + datos + "')", 600);
            }
        }
    });
}

function setValoresEditarProducto(datos) {
    changeText("#contenedor-titulo-form-producto", "Editar producto");
    changeText("#btn-form-producto-guardar", "Guardar cambios <span class='fas fa-save'></span></a>");

    var array = datos.split("</tr>");
    var tipo = array[10];
    var imagen = array[14];
    var chinventario = array[15];
    var img = array[17];
    console.log(imagen);

    if (tipo == "1") {
        $("#inventario").show('slow');
        changeText("#labelinventario", "¿Desactivar inventario?")
    } else if (tipo == "2") {
        $("#inventario").hide('slow');
    }

    if (chinventario == '1') {
        $("#chinventario").prop('checked', true);
        $("#cantidad").removeAttr('disabled');
    }

    $("#codigo-producto").val(array[1]);
    $("#producto").val(array[2]);
    $("#tipo").val(tipo);
    $("#cantidad").val(array[16]);
    $("#clave-unidad").val(array[11] + "-" + array[12]);
    $("#descripcion").val(array[5]);
    $("#pcompra").val(array[6]);
    $("#porganancia").val(array[7])
    $("#ganancia").val(array[8]);
    $("#pventa").val(array[9]);
    $("#clave-fiscal").val(array[11] + "-" + array[12]);
    loadOpcionesProveedor(array[13]);

    if (imagen !== '') {
        $("#imagenproducto").show('slow');
        $("#muestraimagenproducto").html(img);
        $("#filename").val(imagen);
    }
    $("#nameimg").val(imagen);
    $("#imgactualizar").val(img);
    getOptionsTaxes(array[18]);
    setTimeout(() => {
        calcularImpuestosTotal();
    }, 500);

    $("#eliminarimagen").attr("onclick", "eliminarImagen('actualizar')");
    $("#btn-form-producto-guardar").attr("onclick", "gestionarProducto(" + array[0] + ");");
    cargandoHide();
}

function eliminarProducto(idproducto) {
    alertify.confirm("¿Estás seguro que deseas eliminar este producto?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceproducto.php",
            type: "POST",
            data: {transaccion: "eliminarproducto", idproducto: idproducto},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    cargandoHide();
                    loadView('listaproductoaltas');
                }
            }
        });
    }).set('oncancel', function (closeEvent) {
        loadView('listaproductoaltas');
    }).set({title: "Q-ik"});
}

function copiarProducto(idproducto) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproducto.php",
        type: "POST",
        data: {transaccion: "editarproducto", idproducto: idproducto},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('nuevoproducto', '1');
                window.setTimeout("setValoresCopiarProducto('" + datos + "')", 600);
            }
        }
    });
}

function setValoresCopiarProducto(datos) {
    var array = datos.split("</tr>");
    var idproducto = array[0];
    var codigo = array[1];
    var nombre = array[2];
    var unidad = array[3] + "-" + array[4];
    var descripcion_producto = array[5];
    var pcompra = array[6];
    var porcentaje = array[7];
    var ganancia = array[8];
    var pventa = array[9];
    var tipo = array[10];
    var clavefiscal = array[11] + "-" + array[12];
    var idproveedor = array[13];
    var imagen = array[14];
    var chinventario = array[15];
    var cantidad = array[16];
    var img = array[17];

    if (tipo == "1") {
        $("#inventario").show('slow');
        changeText("#labelinventario", "¿Desactivar inventario?")
    } else if (tipo == "2") {
        $("#inventario").hide('slow');
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
    loadOpcionesProveedor(idproveedor);

    if (imagen !== '') {
        $("#imagenproducto").show('slow');
        $("#muestraimagenproducto").html(img);
        $("#filename").val(imagen);
    }
    cargandoHide();

    $("#eliminarimagen").attr("onclick", "eliminarImagen('copia')");
}

function eliminarImagen(tipoOperacion, idproducto) {
    var confirmMessage = "";

    if (tipoOperacion === 'nuevo') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen?";
    } else if (tipoOperacion === 'actualizar') {
        confirmMessage = "¿Estás seguro que deseas eliminar esta imagen en relación al producto? Una vez borrada no se podrá incorporar nuevamente.";
    } else if (tipoOperacion === 'copia') {
        confirmMessage = "¿Estás seguro que deseas quitar la imagen actual?";
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

