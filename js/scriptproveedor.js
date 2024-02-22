function buscarProveedor(pag = ""){
    $.ajax({
        url: "com.sine.enlace/enlaceproveedor.php",
        type: "POST",
        data: {transaccion: "filtrarproveedor", REF: $("#buscar-proveedor").val(), numreg: $("#num-reg").val(), pag:pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-proveedores").html(datos);
            }
        }
    });
}

function loadListaProveedor(pag = ""){
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproveedor.php",
        type: "POST",
        data: {transaccion: "filtrarproveedor", REF: $("#buscar-proveedor").val(), numreg: $("#num-reg").val(), pag:pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-proveedores").html(datos);
                cargandoHide();
            }
        }
    });
}

function checkfiscales(){
    if (!$("#datosficales").prop('checked')){
        $("#fiscales").hide(400);
        $("#rfc-proveedor").val('');
        $("#razon-proveedor").val('');
    } else if ($("#datosficales").prop('checked')) {
        $("#fiscales").show(400);
    }
}

$(document).ready(function() {
    $("#id-banco").change(function() {
        var nombreBanco = $(this).find("option:selected").text().trim().substring(5); 
        $("#nombre_banco").val(nombreBanco); 
    });
});

function guardarProveedor(idproveedor = null) {
    var empresa = $("#empresa").val();
    var representante = $("#representante").val();
    var telefono = $("#telefono").val();
    var correo = $("#correo").val();
    var cuenta = $("#cuenta").val();
    var clabe = $("#clabe").val();
    var banco = $("#id-banco").val() || '0';
    var nombre_banco = $("#nombre_banco").val();
    var sucursal = $("#sucursal").val();
    var rfc = $("#rfc-proveedor").val();
    var razon = $("#razon-proveedor").val();
    
    if (isnEmpty(empresa, "empresa") && isnEmpty(representante, "representante") && isPhoneNumber(telefono, "telefono") && isEmail(correo, "correo")) {
        var url = "com.sine.enlace/enlaceproveedor.php";
        var transaccion = (idproveedor != null) ? "actualizarproveedor" : "insertarproveedor";
        
        var data = {
            transaccion: transaccion,
            idproveedor: idproveedor,
            empresa: empresa,
            representante: representante,
            telefono: telefono,
            correo: correo,
            cuenta: cuenta,
            clabe: clabe,
            idbanco: banco,
            banco: nombre_banco,
            sucursal: sucursal,
            rfc: rfc,
            razon: razon
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
                    var mensaje = (idproveedor != null) ? 'Proveedor actualizado.' : 'Proveedor registrado.';
                    alertify.success(mensaje);
                    loadView('listaproveedor');
                }
            }
        });
    }
}

function editarProveedor(idproveedor) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceproveedor.php",
        type: "POST",
        data: {transaccion: "editarproveedor", idproveedor: idproveedor},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            }
            else {
                cargandoHide();
                loadView('nuevoproveedor');
                window.setTimeout("setValoresEditarProveedor('" + datos + "')", 600);
            }
        }
    });
}

function setValoresEditarProveedor(datos) {
    changeText("#contenedor-titulo-form-proveedor", "Editar Proveedor");
    changeText("#btn-form-proveedor", "Guardar cambios <span class='fas fa-save'></span></a>");

    var [idproveedor, empresa, representante, telefono, correo, cuenta, clabe, idbanco, sucursal, rfc, razon, banco] = datos.split("</tr>", 20);
    $("#empresa").val(empresa);
    $("#representante").val(representante);
    $("#telefono").val(telefono);
    $("#correo").val(correo);
    $("#cuenta").val(cuenta);
    $("#clabe").val(clabe);
    $("#id-banco").val(idbanco);
    $("#sucursal").val(sucursal);
    $("#rfc-proveedor").val(rfc);
    $("#razon-proveedor").val(razon);
    $("#nombre_banco").val(banco);
    $("#btn-form-proveedor").attr("onclick", "guardarProveedor("+idproveedor+");");
}

function eliminarProveedor(idproveedor) {
    alertify.confirm("¿Estás seguro que deseas eliminar este proveedor?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceproveedor.php",
            type: "POST",
            data: {transaccion: "eliminarproveedor", idproveedor: idproveedor},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                }
                else {
                    cargandoHide();
                    alertify.success('Proveedor eliminado.')
                    loadView('listaproveedor');
                }
            }
        });
    }).set({title: "Q-ik"});
}