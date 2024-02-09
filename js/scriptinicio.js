//--------------------------------------NOTIFICACIONES
function filtrarNotificaciones(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceinicio.php",
        type: "POST",
        data: {transaccion: "filtrarnotificaciones", pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-notificacion").html(datos);
                cargandoHide();
            }
        }
    });
}

function buscarNotificaciones(pag = "") {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceinicio.php",
        type: "POST",
        data: {transaccion: "filtrarnotificaciones", pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-notificacion").html(datos);
                cargandoHide();
            }
        }
    });
}

//--------------------------------------GRAFICA
function datosGrafica() {
    cargandoShow();
    cargandoHide();
    $.ajax({
        url: 'com.sine.enlace/enlaceinicio.php',
        type: 'POST',
        data: {transaccion: 'datosgrafica'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            var today = new Date();
            changeText("#contenedor-titulo-facturas-emitidas", "Facturas emitidas en " + today.getFullYear());
            grafica(datos);
        }
    });
}

function getSaldo() {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: 'com.sine.enlace/enlaceinicio.php',
        type: 'POST',
        data: {transaccion: 'getsaldo'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                changeText("#contenedor-timbres", array[3]);
                changeText("#contenedor-usados", array[2]);
                changeText("#contenedor-plan", array[0]);
            }
            cargandoHide();
        }
    });
}

function dynamicColors() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    return "rgba(" + r + "," + g + "," + b + ", 1)";
}

function poolColors(a) {
    var pool = [];
    for (i = 0; i < a; i++) {
        pool.push(dynamicColors());
    }
    return pool;
}

function grafica(datos) {
    var arrcfdi = datos.split("<datacfdi>");
    var array = arrcfdi[0].split("<dataset>");
    var f1 = array[0].split("</tr>");
    var f2 = array[1].split("</tr>");
    var f3 = array[2].split("</tr>");
    var f4 = array[3].split("</tr>");
    var colorF = poolColors(1).toString();

    var array2 = arrcfdi[1].split("<dataset>");
    var c1 = array2[0].split("</tr>");
    var c2 = array2[1].split("</tr>");
    var c3 = array2[2].split("</tr>");
    var c4 = array2[3].split("</tr>");
    var colorC = poolColors(1).toString();

    var popCanvas = $("#chart1");
    var barChart = new Chart(popCanvas, {
        type: 'bar',
        data: {
            labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            datasets: [{
                label: 'Totales Facturas',
                data: f2,
                backgroundColor: colorF,
                borderColor: colorF,
                borderWidth: 2
            }, {
                label: 'Totales Cartas',
                data: c2,
                backgroundColor: colorC,
                borderColor: colorC,
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 16
                        }
                    }
                },
                datalabels: {
                    align: 'top',
                    backgroundColor: 'rgba(0, 0, 0, 0.4)',
                    borderRadius: 20,
                    color: 'white',
                    rotation: 90,
                    padding: 5,
                    formatter: function (value, context) {
                        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                }
            }
        }
    });
    cargandoHide();
}


function buscarGrafica() {
    var d = new Date();
    var y = d.getFullYear();
    var ano = $("#opciones-ano").val() || y;
    cargandoShow();
    $.ajax({
        url: 'com.sine.enlace/enlaceinicio.php',
        type: 'POST',
        data: {transaccion: 'buscargrafica', ano: ano},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            changeText("#contenedor-titulo-facturas-emitidas", "Facturas emitidas en " + ano);
            $("#chart1").remove();
            $("#chart-div").append("<canvas id='chart1' style='height:100px;width: 300px;'></canvas>");
            grafica(datos);
        }
    });
}