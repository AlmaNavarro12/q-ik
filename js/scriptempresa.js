function firmaCanvas() {
    var canvas = document.getElementById('firma-canvas');
    var signaturePad = new SignaturePad(canvas);
    signaturePad.on();

    document.getElementById('clear').addEventListener('click', function () {
        signaturePad.clear();
    });

    document.getElementById('undo').addEventListener('click', function () {
        var data = signaturePad.toData();
        if (data) {
            data.pop();
            signaturePad.fromData(data);
        }
    });

    document.getElementById('save').addEventListener('click', function () {
        if (signaturePad.isEmpty()) {
            return alertify.error("Dibuje una firma válida.");
        }
        var firma = canvas.toDataURL();
        var firmaanterior = $("#firma-actual").val();
        alertify.confirm("Esta reemplazará la firma actual, ¿Desea continuar?", function () {
            cargandoHide();
            cargandoShow();
            $.ajax({
                url: "com.sine.enlace/enlaceconfig.php",
                type: "POST",
                data: { transaccion: "guardarfirma", firma: firma, firmaanterior: firmaanterior },
                success: function (datos) {
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 1000);
                    if (bandera == '0') {
                        alertify.error(res);
                        cargandoHide();
                    } else {
                        cargandoHide();
                        var arr = datos.split("<corte>");
                        var img = arr[1];
                        $("#div-firma").html("<img src='img/logo/" + img + "' width='200px' id='imgfirma'>");
                        $("#firma-actual").val(img);
                    }
                }
            });
        }).set({ title: "Q-ik" });
    });

}

function insertarDatos() {
    var nombre = $("#nombre-empresa").val();
    var rfc = $("#rfc-empresa").val();
    var razon = $("#razon-social").val();
    var color = $("#color-datos").val();
    var calle = $("#calle-empresa").val();
    var interior = $("#num-int-empresa").val();
    var exterior = $("#num-ext-empresa").val();
    var colonia = $("#colonia-empresa").val();
    var cp = $("#codigo_postal").val();
    var idestado = $("#id-estado").val();
    var idmunicipio = $("#id-municipio").val() || '0';
    var estado = $("#id-estado option:selected").text().substring(4);
    var municipio = $("#id-municipio option:selected").text();
    var pais = $("#pais-empresa").val();
    var regimen = $("#regimen-fiscal").val();
    var correo = $("#correo-electronico").val();
    var telefono = $("#telefono").val();
    var idbanco = $("#id-banco").val() || '0';
    var sucursal = $("#sucursal").val();
    var cuenta = $("#cuenta").val();
    var clabe = $("#clabe").val();
    var oxxo = $("#tarjeta-oxxo").val();
    var idbanco1 = $("#id-banco1").val() || '0';
    var sucursal1 = $("#sucursal1").val();
    var cuenta1 = $("#cuenta1").val();
    var clabe1 = $("#clabe1").val();
    var oxxo1 = $("#tarjeta-oxxo1").val();
    var idbanco2 = $("#id-banco2").val() || '0';
    var sucursal2 = $("#sucursal2").val();
    var cuenta2 = $("#cuenta2").val();
    var clabe2 = $("#clabe2").val();
    var oxxo2 = $("#tarjeta-oxxo2").val();
    var idbanco3 = $("#id-banco3").val() || '0';
    var sucursal3 = $("#sucursal3").val();
    var cuenta3 = $("#cuenta3").val();
    var clabe3 = $("#clabe3").val();
    var oxxo3 = $("#tarjeta-oxxo3").val();
    var csd = $("#certificado-csd").val();
    var key = $("#archivo-key").val();
    var passkey = $("#password-key").val();
    var nombre_banco1 = $("#id-banco option:selected").text().substring(4);
    var nombre_banco2 = $("#id-banco1 option:selected").text().substring(4);
    var nombre_banco3 = $("#id-banco2 option:selected").text().substring(4);
    var nombre_banco4 = $("#id-banco3 option:selected").text().substring(4);
    var canvas = document.getElementById('firma-canvas');
    var firma = canvas.toDataURL();
    var firmaanterior = $("#firma-actual").val();

    if (isnEmpty(nombre, "nombre-empresa") && isnEmpty(rfc, "rfc-empresa") && isnEmpty(razon, "razon-social") && isnEmpty(calle, "calle-empresa") && isnEmpty(exterior, "num-ext-empresa") && isnEmpty(colonia, "colonia-empresa") && isnEmpty(cp, "cp-postal") && isnEmpty(estado, "id-estado") && isnEmpty(municipio, "id-municipio") && isnEmpty(pais, "pais-empresa") && isList(regimen, "regimen-fiscal") && isEmail(correo, "correo-electronico") && isnEmpty(telefono, "telefono") && isnEmpty(csd, "certificado-csd") && isnEmpty(key, "archivo-key") && isnEmpty(passkey, "password-key")) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceempresa.php",
            type: "POST",
            data: {
                transaccion: "insertardatos", nombre: nombre, rfc: rfc, razon: razon, color: color, calle: calle, interior: interior, exterior: exterior, colonia: colonia, correo: correo, telefono: telefono, cp: cp, idestado: idestado, idmunicipio: idmunicipio, estado: estado, municipio: municipio, pais: pais, regimen: regimen, passkey: passkey, idbanco: idbanco, sucursal: sucursal, cuenta: cuenta, clabe: clabe, oxxo: oxxo, idbanco1: idbanco1, sucursal1: sucursal1, cuenta1: cuenta1, clabe1: clabe1, oxxo1: oxxo1, idbanco2: idbanco2, sucursal2: sucursal2, cuenta2: cuenta2, clabe2: clabe2, oxxo2: oxxo2, idbanco3: idbanco3, sucursal3: sucursal3, cuenta3: cuenta3, clabe3: clabe3, oxxo3: oxxo3, firma: firma, firmaanterior: firmaanterior,nombrebanco1: nombre_banco1, nombrebanco2: nombre_banco2, nombrebanco3: nombre_banco3, nombrebanco4: nombre_banco4
            },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Datos de facturación guardados correctamente.');
                    loadView('listaempresa');
                }
                cargandoHide();
            }
        });
    }
}

function loadListaEmpresa(pag = "") {
    cargandoHide();
    cargandoShow();
    var nom = $("#buscar-empresa").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlaceempresa.php",
        type: "POST",
        data: { transaccion: "listaempresa", nom: nom, numreg: numreg, pag: pag },
        success: function (datos) {
            //alert ("hola   "+datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-empresa").html(datos);
            }
            cargandoHide();
        }
    });
}

function buscarEmpresa(pag = "") {
    var nom = $("#buscar-empresa").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlaceempresa.php",
        type: "POST",
        data: { transaccion: "listaempresa", nom: nom, numreg: numreg, pag: pag },
        success: function (datos) {
            //alert ("hola   "+datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-empresa").html(datos);
            }
        }
    });
}

function editarEmpresa(idempresa) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceempresa.php",
        type: "POST",
        data: { transaccion: "editarempresa", idempresa: idempresa },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                cargandoHide();
                loadView('datosempresa');
                window.setTimeout("setValoresEditarEmpresa('" + datos + "')", 600);
            }
        }
    });
}

function setValoresEditarEmpresa(datos) {
    changeText("#contenedor-titulo-form-empresa", "Editar datos");
    changeText("#btn-form-empresa", "Guardar cambios <span class='fas fa-save'></span></a>");
    var array = datos.split("</tr>");
    var idEmpresa = array[0];
    var nombre = array[1];
    var rfc = array[2];
    var razon = array[3];
    var calle = array[4];
    var interior = array[5];
    var exterior = array[6];
    var colonia = array[7];
    var pais = array[8];
    var cp = array[9];
    var creg = array[10];
    var regimen = array[11];
    var idmunicipio = array[12];
    var idestado = array[13];
    var idbanco = array[14];
    var sucursal = array[15];
    var cuenta = array[16];
    var clabe = array[17];
    var oxxo = array[18];
    var idbanco1 = array[19];
    var sucursal1 = array[20];
    var cuenta1 = array[21];
    var clabe1 = array[22];
    var oxxo1 = array[23];
    var idbanco2 = array[24];
    var sucursal2 = array[25];
    var cuenta2 = array[26];
    var clabe2 = array[27];
    var oxxo2 = array[28];
    var idbanco3 = array[29];
    var sucursal3 = array[30];
    var cuenta3 = array[31];
    var clabe3 = array[32];
    var oxxo3 = array[33];
    var firma = array[34];
    var colordatos = array[35];
    var correo = array[36];
    var telefono = array[37];

    $("#nombre-empresa").val(nombre);
    $("#rfc-empresa").val(rfc);
    $("#razon-social").val(razon);
    $("#color-datos").val(colordatos);
    $("#calle-empresa").val(calle);
    $("#num-int-empresa").val(interior);
    $("#num-ext-empresa").val(exterior);
    $("#colonia-empresa").val(colonia);

    loadOpcionesEstado('contenedor-estado', 'id-estado', idestado);
    loadOpcionesMunicipio(idmunicipio, idestado);

    $("#pais-empresa").val(pais);
    $("#codigo_postal").val(cp);
    $("#regimen-fiscal").val(creg + "-" + regimen);
    $("#correo-electronico").val(correo);
    $("#telefono").val(telefono);

    if (idbanco != '0') {
        loadOpcionesBanco("id-banco", idbanco);
        $("#sucursal").val(sucursal);
        $("#cuenta").val(cuenta);
        $("#clabe").val(clabe);
        $("#tarjeta-oxxo").val(oxxo);
    }

    inicializarCampos(idbanco1, cuenta1, clabe1, sucursal1, oxxo1, 1);
    inicializarCampos(idbanco2, cuenta2, clabe2, sucursal2, oxxo2, 2);
    inicializarCampos(idbanco3, cuenta3, clabe3, sucursal3, oxxo3, 3);


    $("#firma-actual").val(firma);
    $("#div-firma").html("<label class='label-sub text-right mt-3'>Firma Actual</label><img src='" + firma + "' width='200px' id='imgfirma'>");
    $("#btn-form-empresa").attr("onclick", "actualizarEmpresa(" + idEmpresa + ");");
}

function errorKEY() {
    var passkey = $("#password-key").val();
    $.ajax({
        url: 'com.sine.enlace/enlaceempresa.php',
        type: "POST",
        data: { transaccion: "execkey", passkey: passkey },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                alert(datos);
            }
        }
    });
}

function cargarCSD() {
    var rfc = $("#rfc-empresa").val();
    var formData = new FormData();
    var certificado = $("#certificado-csd")[0].files[0];
    formData.append('certificado-csd', certificado);
    formData.append('rfc-empresa', rfc);
    if (isnEmpty(rfc, 'rfc-empresa') && isnEmpty(certificado, 'certificado-csd')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarcsd.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    $("#label-csd").css("border-color", "red");
                    $("#certificado-csd-errors").text("El tipo de archivo no es valido");
                    $("#certificado-csd-errors").css("color", "red");
                } else {
                    $("#label-csd").css("border-color", "green");
                    alertify.success("Archivo CSD subido satisfactoriamente.")
                }
                cargandoHide();
            }
        });
    }
}

function cargarKEY() {
    var rfc = $("#rfc-empresa").val();
    var formData = new FormData();
    var key = $("#archivo-key")[0].files[0];
    formData.append('archivo-key', key);
    formData.append('rfc-empresa', rfc);
    if (isnEmpty(rfc, 'rfc-empresa') && isnEmpty(key, 'archivo-key')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarkey.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    $("#label-key").css("border-color", "red");
                    $("#archivo-key-errors").text("El tipo de archivo no es válido");
                    $("#archivo-key-errors").css("color", "red");
                } else {
                    $("#label-key").css("border-color", "green");
                    alertify.success("Archivo KEY subido satisfactoriamente.")
                }
                cargandoHide();
            }
        });
    }
}

function isCanvasBlank(canvas) {
    const context = canvas.getContext('2d');
    const pixelBuffer = new Uint32Array(
        context.getImageData(0, 0, canvas.width, canvas.height).data.buffer
    );
    return !pixelBuffer.some(color => color !== 0);
}

function actualizarEmpresa(idempresa) {
    var nombre = $("#nombre-empresa").val();
    var rfc = $("#rfc-empresa").val();
    var razon = $("#razon-social").val();
    var color = $("#color-datos").val();
    var calle = $("#calle-empresa").val();
    var interior = $("#num-int-empresa").val();
    var exterior = $("#num-ext-empresa").val();
    var colonia = $("#colonia-empresa").val();
    var idestado = $("#id-estado").val();
    var idmunicipio = $("#id-municipio").val();
    var estado = $("#id-estado option:selected").text();
    var municipio = $("#id-municipio option:selected").text();
    var pais = $("#pais-empresa").val();
    var cp = $("#codigo_postal").val();
    var regimen = $("#regimen-fiscal").val();
    var correo = $("#correo-electronico").val();
    var telefono = $("#telefono").val();
    var idbanco = $("#id-banco").val();
    var sucursal = $("#sucursal").val();
    var cuenta = $("#cuenta").val();
    var clabe = $("#clabe").val();
    var oxxo = $("#tarjeta-oxxo").val();
    var idbanco1 = $("#id-banco1").val();
    var sucursal1 = $("#sucursal1").val();
    var cuenta1 = $("#cuenta1").val();
    var clabe1 = $("#clabe1").val();
    var oxxo1 = $("#tarjeta-oxxo1").val();
    var idbanco2 = $("#id-banco2").val();
    var sucursal2 = $("#sucursal2").val();
    var cuenta2 = $("#cuenta2").val();
    var clabe2 = $("#clabe2").val();
    var oxxo2 = $("#tarjeta-oxxo2").val();
    var idbanco3 = $("#id-banco3").val();
    var sucursal3 = $("#sucursal3").val();
    var cuenta3 = $("#cuenta3").val();
    var clabe3 = $("#clabe3").val();
    var oxxo3 = $("#tarjeta-oxxo3").val();
    var certificado = $("#certificado-csd").val();
    var key = $("#archivo-key").val();
    var passkey = $("#password-key").val();
    var nombre_banco1 = $("#id-banco option:selected").text().substring(4);
    var nombre_banco2 = $("#id-banco1 option:selected").text().substring(4);
    var nombre_banco3 = $("#id-banco2 option:selected").text().substring(4);
    var nombre_banco4 = $("#id-banco3 option:selected").text().substring(4);
    var firmaactual = $("#firma-actual").val();
    var canvas = document.getElementById('firma-canvas');
    const blank = isCanvasBlank(canvas);
    var firma = "";
    if (blank) {
        firma = "empty";
    } else {
        firma = canvas.toDataURL();
    }

    if (idbanco == "") {
        idbanco = '0';
    }

    if (idbanco1 == "") {
        idbanco1 = '0';
    }

    if (idbanco2 == "") {
        idbanco2 = '0';
    }

    if (idbanco3 == "") {
        idbanco3 = '0';
    }

    if (isnEmpty(nombre, "nombre-empresa") && isnEmpty(rfc, "rfc-empresa") && isnEmpty(razon, "razon-empresa") && isnEmpty(calle, "calle-empresa") && isnEmpty(exterior, "num-ext-empresa") && isnEmpty(colonia, "colonia-empresa") && isnEmpty(idestado, "id-estado") && isnEmpty(idmunicipio, "id-municipio") && isnEmpty(cp, "cp-postal") && isnEmpty(pais, "pais-empresa") && isList(regimen, "regimen-fiscal")) {
        $.ajax({
            url: "com.sine.enlace/enlaceempresa.php",
            type: "POST",
            data: { transaccion: "actualizarempresa", idempresa: idempresa, nombre: nombre, rfc: rfc, razon: razon, color: color, calle: calle, interior: interior, exterior: exterior, colonia: colonia, cp: cp, idestado: idestado, idmunicipio: idmunicipio, estado: estado, municipio: municipio, pais: pais, regimen: regimen, correo: correo, telefono: telefono, certificado: certificado, key: key, passkey: passkey, idbanco: idbanco, sucursal: sucursal, cuenta: cuenta, clabe: clabe, oxxo: oxxo, idbanco1: idbanco1, sucursal1: sucursal1, cuenta1: cuenta1, clabe1: clabe1, oxxo1: oxxo1, idbanco2: idbanco2, sucursal2: sucursal2, cuenta2: cuenta2, clabe2: clabe2, oxxo2: oxxo2, idbanco3: idbanco3, sucursal3: sucursal3, cuenta3: cuenta3, clabe3: clabe3, oxxo3: oxxo3, firma: firma, firmaactual: firmaactual, 
            nombrebanco1: nombre_banco1,
            nombrebanco2: nombre_banco2,
            nombrebanco3: nombre_banco3,
            nombrebanco4: nombre_banco4},
            success: function (datos) {
                //alert(datos);
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Datos actualizados');
                    loadView('listaempresa');
                }
            }
        });
    }
}

function filtrarEmpresa() {
    //alert();
    var RAZ = "" + $("#razon-historial").val();
    var RF = "" + $("#rfc-historial").val();
    //alert("este es cliente"+cliente);
    // alert("este es placa"+placa);
    $.ajax({
        url: "com.sine.enlace/enlaceempresa.php",
        type: "POST",
        data: { transaccion: "filtrarempresa", RAZ: RAZ, RF: RF },
        success: function (datos) {
            //alert(datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-empresa").html(datos);
            }
        }
    });
}

function descargarArchivos(id) {
    $.ajax({
        url: "com.sine.imprimir/download.php",
        type: "POST",
        data: { datosid: id },
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            var newTab = window.open('./temporal/csd.zip');
        }
    });
}


function eliminarEmpresa(did) {
    alertify.confirm("Al realizar esta acción se borrarán también los archivos CSD y KEY registrados, ¿estás seguro que deseas continuar?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceempresa.php",
            type: "POST",
            data: { transaccion: "eliminarempresa", did: did },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    loadView('listaempresa');
                    alertify.success('Se eliminaron correctamente los datos');
                }
                cargandoHide();
            }
        });
    }).set({ title: "Q-ik" });
}

function validaPaquete() {
    $.ajax({
        data: { transaccion: 'validaPaquete' },
        url: 'com.sine.enlace/enlaceempresa.php',
        type: 'POST',
        success: function (datos) {
            var div = datos.split('</tr>');
            var paquete = div[0];
            var nrazon = div[1];

            if ((paquete == 'Basico' && nrazon < 2) || paquete != 'Basico') {
                loadView('datosempresa');
            } else {
                alertify.error('el limite del paquete basico son 2 razones sociales')
            }
        }
    })

}


var renglon = 0;
function confirmarEliminacion(id) {
    alertify.confirm("¿Estás seguro que quieres eliminar los campos de esta cuenta?", function (e) {
        if (e) {
            quitarCampo(id);
        }
    }).set({ title: "Q-ik" });
}

function quitarCampo(id) {
    $("#id-banco" + id).val("");
    $("#cuenta" + id).val("");
    $("#clabe" + id).val("");

    //Se esconde el addcuentas segun el id
    $("#addcuentas" + id).attr("hidden", true);
    renglon--;
}

function nuevoCampo() {
    renglon++;
    if (renglon > 3) {
        renglon = 1; //Si pasa 3, hay que iniciar de nuevo
    }

    var campoActual = "#addcuentas" + renglon;

    // Si el campo está oculto, mostrarlo
    if ($(campoActual).is(":hidden")) {
        $(campoActual).attr("hidden", false);
        loadOpcionesBanco("contenedor-banco" + renglon, "");
    }
}

function inicializarCampos(idbanco, cuenta, clabe, sucursal, tarjeta, i) {
    if (idbanco !== '0') {
        $("#addcuentas" + i).attr('hidden', false);
        loadOpcionesBanco('id-banco' + i, idbanco);
        $("#cuenta" + i).val(cuenta);
        $("#sucursal" + i).val(sucursal);
        $("#tarjeta-oxxo" + i).val(tarjeta);
        $("#clabe" + i).val(clabe);
        renglon = i;
        $("#rmvcuentas").removeAttr('disabled');
    }
}