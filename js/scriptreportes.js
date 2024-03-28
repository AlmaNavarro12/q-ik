function buscarReporte() {
    var fechainicio = $('#date-inicio').val();
    var fechafin = $('#date-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var tipo = $("#tipo-factura").val();
    var moneda = $("#moneda-calculo").val();
    //nuevo
    var metodopago = $("#metodo-factura").val();
    var formapago = $("#id-forma-pago").val();

    if (dateEmpty(fechainicio, "date-inicio") && dateEmpty(fechafin, "date-fin")) {
        $.ajax({
            url: "com.sine.enlace/enlacereporte.php",
            type: "POST",
            data: {transaccion: "buscarFactura", fechainicio: fechainicio,
             fechafin: fechafin,
              idcliente: idcliente,
               estado: estado,
                datos: datos,
                 tipo: tipo,
                  moneda: moneda,
                  metodopago: metodopago,
                  formapago: formapago
                },
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    $("#body-lista-reporte-factura").html(res);
                } else {
                    $("#body-lista-reporte-factura").html(datos);
                }
            }
        });
    }
}

function setValue() {
    $(".list-option").click(function () {
        var v = $(this).attr("data-value");
        var d = $(this).attr("data");
        var t = $(this).text();
        $("#" + d).val(v);
        changeText("#" + d + "-button", t + "<span class='lnr lnr-chevron-down pull-right'></span>")
    });
}

function buscarReportePago() {
    var fechainicio = $('#inicio-pago').val();
    var fechafin = $('#fin-pago').val();
    var idcliente = $('#id-cliente').val();
    var datos = $("#datos-facturacion").val();
    var moneda = $("#moneda-calculo").val();
    var forma = $("#id-forma-pago").val();

    if (dateEmpty(fechainicio, "inicio-pago") && dateEmpty(fechafin, "fin-pago")) {
        $.ajax({
            url: "com.sine.enlace/enlacereporte.php",
            type: "POST",
            data: {transaccion: "buscarpagos", fechainicio: fechainicio, fechafin: fechafin, idcliente: idcliente, datos: datos, moneda: moneda, forma: forma},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    $("#body-lista-reporte-pago").html(res);
                } else {
                    $("#body-lista-reporte-pago").html(datos);
                }
            }
        });
    }
}

function buscarReporteVenta() {
    var fechainicio = $('#fecha-inicio').val();
    var fechafin = $('#fecha-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var usuario = $("#usuario-vendedor").val();
    if (dateEmpty(fechainicio, "fecha-inicio") && dateEmpty(fechafin, "fecha-fin")) {
        $.ajax({
            url: "com.sine.enlace/enlacereporte.php",
            type: "POST",
            data: {transaccion: "buscarventas", fechainicio: fechainicio, fechafin: fechafin, idcliente: idcliente, estado: estado, datos: datos, usuario: usuario},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    $("#body-lista-reporte-ventas").html(res);
                } else {
                    $("#body-lista-reporte-ventas").html(datos);
                }
            }
        });
    }
}

function cambiarEstado(idfactura, estado) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlacefactura.php",
        type: "POST",
        data: {transaccion: "editarestado", idfactura: idfactura, estado: estado},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                cargandoHide();
                loadView('reportefactura');
            }
        }
    });
}

function imprimir_reporte() {
    var fechainicio = $('#date-inicio').val();
    var fechafin = $('#date-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var tipo = $("#tipo-factura").val();
    var moneda = $("#moneda-calculo").val();


    if (dateEmpty(fechainicio, "date-inicio") && dateEmpty(fechafin, "date-fin")) {
        VentanaCentrada('./com.sine.imprimir/reporte_factura.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& cliente=' + idcliente + '&& estado=' + estado + '&& datos=' + datos + '&& tipo=' + tipo + '&& moneda=' + moneda, 'Factura', '', '1024', '768', 'true');
    }
}

function imprimirFacturaReporte() {
    var fechainicio = $('#date-inicio').val();
    var fechafin = $('#date-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var tipo = $("#tipo-factura").val();
    var moneda = $("#moneda-calculo").val();

    if (dateEmpty(fechainicio, "date-inicio") && dateEmpty(fechafin, "date-fin")) {
        VentanaCentrada('./com.sine.imprimir/reporteFacturasPDF.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& cliente=' + idcliente + '&& estado=' + estado + '&& datos=' + datos + '&& tipo=' + tipo + '&& moneda=' + moneda, 'Factura', '', '1024', '768', 'true');
    }
}

function imprimirRepExcl() {
    var fechainicio = $('#date-inicio').val();
    var fechafin = $('#date-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var tipo = $("#tipo-factura").val();
    var moneda = $("#moneda-calculo").val();

    if (dateEmpty(fechainicio, "date-inicio") && dateEmpty(fechafin, "date-fin")) {
        cargandoHide();
        cargandoShow();
        window.open('./com.sine.imprimir/imprimirreporteexcel.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& idcliente=' + idcliente + '&& estado=' + estado + '&& datos=' + datos + '&& tipo=' + tipo + '&& moneda=' + moneda, 'Factura', '', '1024', '768', 'true');
        cargandoHide();

        /*$.ajax({
         url: "com.sine.imprimir/imprimirreporteexcel.php",
         type: "POST",
         data: {transaccion: "imprimirpdf", fechainicio: fechainicio, fechafin: fechafin, idcliente: idcliente, estado: estado, datos: datos, tipo: tipo, moneda: moneda},
         success: function (datos) {
         var texto = datos.toString();
         var bandera = texto.substring(0, 1);
         var res = texto.substring(1, 1000);
         if (bandera == '0') {
         alertify.error(res);
         cargandoHide();
         } else {
         //alert(datos);
         window.open('reporteExcel/reporte_' + fechainicio + '_' + fechafin + '.xlsx', '_blank');
         cargandoHide();
         }
         }
         });*/
    }
}

function imprimir_reportePago() {
    var fechainicio = $('#inicio-pago').val();
    var fechafin = $('#fin-pago').val();
    var idcliente = $('#id-cliente').val();
    var datos = $("#datos-facturacion").val();
    var moneda = $("#moneda-calculo").val();
    var forma = $("#id-forma-pago").val();


    if (dateEmpty(fechainicio, "inicio-pago") && isnEmpty(fechafin, "fin-pago")) {
        VentanaCentrada('./com.sine.imprimir/reporte_pago.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& cliente=' + idcliente + '&& datos=' + datos + '&& moneda=' + moneda + '&& forma=' + forma, 'Pago', '', '1024', '768', 'true');
    }
}

function imprimirPagosReporte() {
    var fechainicio = $('#inicio-pago').val();
    var fechafin = $('#fin-pago').val();
    var idcliente = $('#id-cliente').val();
    var datos = $("#datos-facturacion").val();
    if (dateEmpty(fechainicio, "inicio-pago") && isnEmpty(fechafin, "fin-pago")) {
        VentanaCentrada('./com.sine.imprimir/reportePagosPDF.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& cliente=' + idcliente + '&& datos=' + datos, 'Pago', '', '1024', '768', 'true');
    }
}

function imprimirExclPago() {
    var fechainicio = $('#inicio-pago').val();
    var fechafin = $('#fin-pago').val();
    var idcliente = $('#id-cliente').val();
    var datos = $("#datos-facturacion").val();
    var moneda = $("#moneda-calculo").val();

    if (dateEmpty(fechainicio, "inicio-pago") && dateEmpty(fechafin, "fin-pago")) {
        cargandoHide();
        cargandoShow();
        window.open('./com.sine.imprimir/reporteexcelpagos.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& idcliente=' + idcliente + '&& datos=' + datos + '&& moneda=' + moneda, 'Factura', '', '1024', '768', 'true');
        cargandoHide();
    }
}

function reporteGraficaActual() {
    var iddatos = $("#datos-facturacion").val();
    var today = new Date();
    var y = today.getFullYear();
    var m = today.getMonth() + 1;

    $("#chart-actual").remove();
    $("#div-actual").append("<canvas id='chart-actual' ></canvas>");
    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'datosgrafica', iddatos: iddatos, y: y, m: m},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaActual(datos);
            //cargandoHide();
        }
    });
}

function graficaActual(datos) {
    var array = datos.split("</tr>");
    var pagadas = array[0];
    var pendientes = array[1];
    var canceladas = array[2];

    var popCanvas = document.getElementById("chart-actual");
    var barChart = new Chart(popCanvas, {
        type: 'doughnut',
        data: {
            labels: ["Pagadas", "Pendientes", "Canceladas"],
            datasets: [{
                    label: 'Facturas Emitidas',
                    data: [pagadas, pendientes, canceladas],
                    backgroundColor: [
                        'rgba(9, 9, 96, 1)',
                        'rgba(237, 73, 92, 1)',
                        'rgba(246, 176, 69, 1)'
                    ],
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: [
                        'rgba(9, 9, 96, 1)',
                        'rgba(237, 73, 92, 1)',
                        'rgba(246, 176, 69, 1)'
                    ],
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            }
        }
    });

    popCanvas.onclick = function (evt) {
        var activePoints = barChart.getElementsAtEvent(evt);
        if (activePoints[0]) {
            var chartData = activePoints[0]['_chart'].config.data;
            var idx = activePoints[0]['_index'];

            var label = chartData.labels[idx];
            $("#chart-prueba").remove();
            $("#div-prueba").append("<canvas id='chart-prueba' ></canvas>");
            $("#grafica-estado").modal('show');
            getActualEstado(label);
        }
    };
}

function getActualEstado(estado) {
    var iddatos = $("#datos-facturacion").val();
    $("#estado-facturas").html(estado);
    var today = new Date();
    var y = today.getFullYear();
    var m = today.getMonth() + 1;
    var status = '0';
    switch (estado) {
        case 'Pagadas':
            status = '1';
            break;
        case 'Pendientes':
            status = '2';
            break;
        case 'Canceladas':
            status = '3';
            break;
        default:
            status = '0';
            break;
    }

    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'getactualestado', iddatos: iddatos, y: y, m: m, status: status},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaEstado(datos);
            //cargandoHide();
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

function graficaEstado(datos) {
    var array = datos.split("</tr>");
    var clientes = array[0];
    var arrayclientes = clientes.split(",");
    var datos = array[1];
    var arraydatos = datos.split(",");
    var colors = poolColors(arraydatos.length);

    var popCanvas = document.getElementById("chart-prueba");
    var barChart = new Chart(popCanvas, {
        type: 'bar',
        data: {
            labels: arrayclientes,
            datasets: [{
                    label: 'Monto total $ MXN',
                    data: arraydatos,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 2
                }]
        },
        options: {
            plugins: {
                labels: {
                    render: 'value'
                }
            },
            responsive: true,
            maintainAspectRatio: true,
            animation: {
                duration: 500,
                easing: 'linear'
            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            }
        }
    });
}

function reporteGraficaAnterior(iddatos = "") {
    if (iddatos == '') {
        iddatos = $("#datos-facturacion").val();
    }
    var today = new Date();
    var y = today.getFullYear();
    var m = today.getMonth();
    if (m == 0) {
        m = 12;
    }

    $("#chart-anterior").remove();
    $("#div-anterior").append("<canvas id='chart-anterior' ></canvas>");
    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'datosgrafica', iddatos: iddatos, y: y, m: m},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaAnterior(datos);
            //cargandoHide();
        }
    });
}


function graficaAnterior(datos) {
    var array = datos.split("</tr>");
    var pagadas = array[0];
    var pendientes = array[1];
    var canceladas = array[2];

    var popCanvas = document.getElementById("chart-anterior");
    var barChart = new Chart(popCanvas, {
        type: 'doughnut',
        data: {
            labels: ["Pagadas", "Pendientes", "Canceladas"],
            datasets: [{
                    label: 'Facturas Emitidas',
                    data: [pagadas, pendientes, canceladas],
                    backgroundColor: [
                        'rgba(9, 9, 96, 1)',
                        'rgba(237, 73, 92, 1)',
                        'rgba(246, 176, 69, 1)'
                    ],
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: [
                        'rgba(9, 9, 96, 1)',
                        'rgba(237, 73, 92, 1)',
                        'rgba(246, 176, 69, 1)'
                    ],
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            }
        }
    });

    popCanvas.onclick = function (evt) {
        var activePoints = barChart.getElementsAtEvent(evt);
        if (activePoints[0]) {
            var chartData = activePoints[0]['_chart'].config.data;
            var idx = activePoints[0]['_index'];

            var label = chartData.labels[idx];
            $("#chart-prueba").remove();
            $("#div-prueba").append("<canvas id='chart-prueba' ></canvas>");
            $("#grafica-estado").modal('show');
            getAnteriorEstado(label);
        }
    };
}

function getAnteriorEstado(estado) {
    var iddatos = $("#datos-facturacion").val();
    $("#estado-facturas").html(estado);
    var today = new Date();
    var y = today.getFullYear();
    var m = today.getMonth();
    var status = '0';
    switch (estado) {
        case 'Pagadas':
            status = '1';
            break;
        case 'Pendientes':
            status = '2';
            break;
        case 'Canceladas':
            status = '3';
            break;
        default:
            status = '0';
            break;
    }
    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'getactualestado', iddatos: iddatos, y: y, m: m, status: status},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaEstado(datos);
            //cargandoHide();
        }
    });
}

function reporteGraficaBuscar() {
    var iddatos = $("#datos-facturacion").val();
    var today = new Date();
    var y = $("#opciones-ano").val();
    var m = $("#opciones-mes").val();

    if (y == "") {
        y = today.getFullYear();
    }

    $("#chart-buscar").remove();
    $("#div-buscar").append("<canvas id='chart-buscar' ></canvas>");

    if (isnEmpty(m, "opciones-mes")) {
        $.ajax({
            url: 'com.sine.enlace/enlacereporte.php',
            type: 'POST',
            data: {transaccion: 'datosgrafica', iddatos: iddatos, y: y, m: m},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                //alert(datos);
                graficaBuscar(datos);
                //cargandoHide();
            }
        });
    }
}

function graficaBuscar(datos) {
    var array = datos.split("</tr>");
    var pagadas = array[0];
    var pendientes = array[1];
    var canceladas = array[2];

    var popCanvas = document.getElementById("chart-buscar");
    var barChart = new Chart(popCanvas, {
        type: 'doughnut',
        data: {
            labels: ["Pagadas", "Pendientes", "Canceladas"],
            datasets: [{
                    label: 'Facturas Emitidas',
                    data: [pagadas, pendientes, canceladas],
                    backgroundColor: [
                        'rgba(9, 9, 96, 1)',
                        'rgba(237, 73, 92, 1)',
                        'rgba(246, 176, 69, 1)'
                    ],
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: [
                        'rgba(9, 9, 96, 1)',
                        'rgba(237, 73, 92, 1)',
                        'rgba(246, 176, 69, 1)'
                    ],
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            }
        }
    });

    popCanvas.onclick = function (evt) {
        var activePoints = barChart.getElementsAtEvent(evt);
        if (activePoints[0]) {
            var chartData = activePoints[0]['_chart'].config.data;
            var idx = activePoints[0]['_index'];

            var label = chartData.labels[idx];
            $("#chart-prueba").remove();
            $("#div-prueba").append("<canvas id='chart-prueba' ></canvas>");
            $("#grafica-estado").modal('show');
            getBuscarEstado(label);
        }
    };
}

function getBuscarEstado(estado) {
    var iddatos = $("#datos-facturacion").val();
    $("#estado-facturas").html(estado);
    var y = $("#opciones-ano").val();
    var m = $("#opciones-mes").val();

    if (y == "") {
        var today = new Date();
        y = today.getFullYear();
    }
    var status = '0';
    switch (estado) {
        case 'Pagadas':
            status = '1';
            break;
        case 'Pendientes':
            status = '2';
            break;
        case 'Canceladas':
            status = '3';
            break;
        default:
            status = '0';
            break;
    }
    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'getactualestado', iddatos: iddatos, y: y, m: m, status: status},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaEstado(datos);
            //cargandoHide();
        }
    });
}

function insertarArchivo() {
    var formData = new FormData(document.getElementById("form-archivo"));
//    var formData = new FormData($("#form-img")[0]);
    if (isnEmpty(formData, 'archivo-sat')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargartxtcont.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 5000);
                if (bandera == '0') {
                    cargandoHide();
                    alertify.error(res);
                    $("#archivo-sat").val('');
                } else {
                    cargandoHide();
                    alertify.success('Datos guardados correctamente');
                    //alert(datos);
                    $("#archivo-sat").val('');
                }

            }
        });
    }
}

function reporteBimestralActual() {
    var today = new Date();
    var y = today.getFullYear();
    var bim = "";
    var txt = "";
    var datos = $("#datos-facturacion").val();

    var m = today.getMonth();

    switch (m) {
        case 0:
            bim = '1';
            txt = "Bimestre Enero-Febrero";
            break;
        case 1:
            bim = '1';
            txt = "Bimestre Enero-Febrero";
            break;
        case 2:
            bim = '2';
            txt = "Bimestre Marzo-Abril";
            break;
        case 3:
            bim = '2';
            txt = "Bimestre Marzo-Abril";
            break;
        case 4:
            bim = '3';
            txt = "Bimestre Mayo-Junio";
            break;
        case 5:
            bim = '3';
            txt = "Bimestre Mayo-Junio";
            break;
        case 6:
            bim = '4';
            txt = "Bimestre Julio-Agosto";
            break;
        case 7:
            bim = '4';
            txt = "Bimestre Julio-Agosto";
            break;
        case 8:
            bim = '5';
            txt = "Bimestre Septiembre-Octubre";
            break;
        case 9:
            bim = '5';
            txt = "Bimestre Septiembre-Octubre";
            break;
        case 10:
            bim = '6';
            txt = "Bimestre Noviembre-Diciembre";
            break;
        case 11:
            bim = '6';
            txt = "Bimestre Noviembre-Diciembre";
            break;
    }

    $("#chart-actual").remove();
    $("#div-actual").append("<canvas id='chart-actual'></canvas>");

    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'datosbimestre', y: y, bim: bim, datos: datos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaBimestralActual(datos, txt);
            //cargandoHide();
        }
    });
}

function graficaBimestralActual(datos, txt) {
    var array = datos.split("<dataset>");
    var labels = array[0].split("</tr>");
    var montos = array[1].split("</tr>");
    var colors = poolColors(montos.length);

    var popCanvas = document.getElementById("chart-actual");
    var barChart = new Chart(popCanvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Monto total $',
                    data: montos,
                    backgroundColor: colors,
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: colors,
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            }
        }
    });

}

function reporteBimestralAnterior(datos = "") {
    var today = new Date();
    var y = today.getFullYear();
    var bim = "";
    var txt = "";
    if (datos == "") {
        datos = $("#datos-facturacion").val();
    }
    var m = today.getMonth();

    switch (m) {
        case 0:
            bim = '6';
            y = y - 1;
            txt = "Bimestre Noviembre-Diciembre";
            break;
        case 1:
            bim = '6';
            y = y - 1;
            txt = "Bimestre Noviembre-Diciembre";
            break;
        case 2:
            bim = '1';
            txt = "Bimestre Enero-Febrero";
            break;
        case 3:
            bim = '1';
            txt = "Bimestre Enero-Febrero";
            break;
        case 4:
            bim = '2';
            txt = "Bimestre Marzo-Abril";
            break;
        case 5:
            bim = '2';
            txt = "Bimestre Marzo-Abril";
            break;
        case 6:
            bim = '3';
            txt = "Bimestre Mayo-Junio";
            break;
        case 7:
            bim = '3';
            txt = "Bimestre Mayo-Junio";
            break;
        case 8:
            bim = '4';
            txt = "Bimestre Julio-Agosto";
            break;
        case 9:
            bim = '4';
            txt = "Bimestre Julio-Agosto";
            break;
        case 10:
            bim = '5';
            txt = "Bimestre Septiembre-Octubre";
            break;
        case 11:
            bim = '5';
            txt = "Bimestre Septiembre-Octubre";
            break;
    }

    $("#chart-anterior").remove();
    $("#div-anterior").append("<canvas id='chart-anterior'></canvas>");

    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'datosbimestre', y: y, bim: bim, datos: datos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaBimestralAnterior(datos, txt);
            //cargandoHide();
        }
    });
}

function graficaBimestralAnterior(datos, txt) {
    var array = datos.split("<dataset>");
    var labels = array[0].split("</tr>");
    var montos = array[1].split("</tr>");
    var colors = poolColors(montos.length);

    var popCanvas = document.getElementById("chart-anterior");

    var barChart = new Chart(popCanvas, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Monto total $',
                    data: montos,
                    backgroundColor: colors,
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: colors,
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            }
        }
    });

}

function reporteBimestralAnterior2(datos = "") {
    var today = new Date();
    var y = today.getFullYear();
    var bim = "";
    var txt = "";
    if (datos == "") {
        datos = $("#datos-facturacion").val();
    }
    var m = today.getMonth();

    switch (m) {
        case 0:
            bim = '5';
            y = y - 1;
            txt = "Bimestre Septiembre-Octubre";
            break;
        case 1:
            bim = '5';
            y = y - 1;
            txt = "Bimestre Septiembre-Octubre";
            break;
        case 2:
            bim = '6';
            y = y - 1;
            txt = "Bimestre Noviembre-Diciembre";
            break;
        case 3:
            bim = '6';
            y = y - 1;
            txt = "Bimestre Noviembre-Diciembre";
            break;
        case 4:
            bim = '1';
            txt = "Bimestre Enero-Febrero";
            break;
        case 5:
            bim = '1';
            txt = "Bimestre Enero-Febrero";
            break;
        case 6:
            bim = '2';
            txt = "Bimestre Marzo-Abril";
            break;
        case 7:
            bim = '2';
            txt = "Bimestre Marzo-Abril";
            break;
        case 8:
            bim = '3';
            txt = "Bimestre Mayo-Junio";
            break;
        case 9:
            bim = '3';
            txt = "Bimestre Mayo-Junio";
            break;
        case 10:
            bim = '4';
            txt = "Bimestre Julio-Agosto";
            break;
        case 11:
            bim = '4';
            txt = "Bimestre Julio-Agosto";
            break;
    }

    $("#chart-anterior2").remove();
    $("#div-anterior2").append("<canvas id='chart-anterior2' ></canvas>");

    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'datosbimestre', y: y, bim: bim, datos: datos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaBimestralAnterior2(datos, txt);
            //cargandoHide();
        }
    });
}

function graficaBimestralAnterior2(datos, txt) {
    var array = datos.split("<dataset>");
    var labels = array[0].split("</tr>");
    var montos = array[1].split("</tr>");
    var colors = poolColors(montos.length);

    var popCanvas = document.getElementById("chart-anterior2");
    var barChart = new Chart(popCanvas, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Monto total $',
                    data: montos,
                    backgroundColor: colors,
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: colors,
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            }
        }
    });
}

function reporteGraficaBimestral() {
    var y = $("#opciones-ano").val();
    var bim = $("#opciones-bimestre").val();
    var datos = $("#opciones-datos").val();
    //alert(datos);

    if (y == "") {
        var today = new Date();
        y = today.getFullYear();
    }

    var txt = "";

    if (bim == "") {
        var today = new Date();
        var m = today.getMonth();

        switch (m) {
            case 0:
                txt = "Bimestre Enero-Febrero " + y;
                break;
            case 1:
                txt = "Bimestre Enero-Febrero " + y;
                break;
            case 2:
                txt = "Bimestre Marzo-Abril " + y;
                break;
            case 3:
                txt = "Bimestre Marzo-Abril " + y;
                break;
            case 4:
                txt = "Bimestre Mayo-Junio " + y;
                break;
            case 5:
                txt = "Bimestre Mayo-Junio " + y;
                break;
            case 6:
                txt = "Bimestre Julio-Agosto " + y;
                break;
            case 7:
                txt = "Bimestre Julio-Agosto " + y;
                break;
            case 8:
                txt = "Bimestre Septiembre-Octubre " + y;
                break;
            case 9:
                txt = "Bimestre Septiembre-Octubre " + y;
                break;
            case 10:
                txt = "Bimestre Noviembre-Diciembre " + y;
                break;
            case 11:
                txt = "Bimestre Noviembre-Diciembre " + y;
                break;
        }
    } else {
        switch (bim) {
            case '1':
                txt = "Bimestre Enero-Febrero " + y;
                break;
            case '2':
                txt = "Bimestre Marzo-Abril " + y;
                break;
            case '3':
                txt = "Bimestre Mayo-Junio " + y;
                break;
            case '4':
                txt = "Bimestre Julio-Agosto " + y;
                break;
            case '5':
                txt = "Bimestre Septiembre-Octubre " + y;
                break;
            case '6':
                txt = "Bimestre Noviembre-Diciembre " + y;
                break;
        }
    }

    $("#chart-bimestre").remove();
    $("#div-bimestre").append("<canvas id='chart-bimestre' ></canvas>");

    $.ajax({
        url: 'com.sine.enlace/enlacereporte.php',
        type: 'POST',
        data: {transaccion: 'datosbimestre', y: y, bim: bim, datos: datos},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            //alert(datos);
            graficaBimestral(datos, txt);
            //cargandoHide();
        }
    });
}

function graficaBimestral(datos, txt) {
    var array = datos.split("<dataset>");
    var labels = array[0].split("</tr>");
    var montos = array[1].split("</tr>");
    var colors = poolColors(montos.length);

    var popCanvas = document.getElementById("chart-bimestre");
    var barChart = new Chart(popCanvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Monto total $',
                    data: montos,
                    backgroundColor: colors,
                    borderWidth: 2
                }]
        },
        plugins: [ChartDataLabels],
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
                    anchor: 'end',
                    align: 'end',
                    backgroundColor: colors,
                    borderRadius: 20,
                    color: 'white',
                    padding: 5
                }
            },
            title: {
                display: true,
                text: txt,
                fontSize: 20,
                fontColor: '#31708f',
                fontFamily: '"sans-serif", Monaco, monospace',
            },
            responsive: true,
            maintainAspectRatio: true,
            cutoutPercentage: 35,
            tooltips: {
                enabled: false
            },
            animation: {
                duration: 500,
                easing: 'linear'
            }
        }
    });
}

function loadlistaIVA() {
    var emisor = $("#emisor-historial").val();
    var receptor = $("#datos-facturacion").val();
    var ano = $("#anho-historial").val();
    var mes = $("#mes-historial").val();

    $.ajax({
        url: "com.sine.enlace/enlacereporte.php",
        type: "POST",
        data: {transaccion: "filtrariva", emisor: emisor, receptor: receptor, ano: ano, mes: mes},
        success: function (datos) {
            //alert(datos);
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-datos").html(datos);
            }
        }
    });
}

function eliminarDatos(uuid) {
    alertify.confirm("Esta seguro que desea eliminar este registro?", function () {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlacereporte.php",
            type: "POST",
            data: {transaccion: "eliminarregistro", uuid: uuid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                    cargandoHide();
                } else {
                    cargandoHide();
                    alertify.success('Se elimino correctamente el registro')
                    loadView('datosiva');
                }
            }
        });
    }).set({title: "Q-ik"});
}

function imprimirPDF() {
    var datos = $("#datos-facturacion").val();
    var actual = document.getElementById('chart-actual');
    var pasado = document.getElementById('chart-anterior');
    var antep = document.getElementById('chart-anterior2');
    var dataactual = actual.toDataURL();
    var datapasado = pasado.toDataURL();
    var dataantep = antep.toDataURL();
    $.ajax({
        url: "com.sine.imprimir/reporte_sat.php",
        type: "POST",
        data: {transaccion: "imprimirpdf", type: '1', datos: datos, dataactual: dataactual, datapasado: datapasado, dataantep: dataantep},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                cargandoHide();
                //alert(datos);
                VentanaCentrada('./ReportePDF/reporteImpuestos.pdf', 'Reporte', '', '1024', '768', 'true');
            }
        }
    });
}

function imprimirEXCL() {
    var datos = $("#datos-facturacion").val();
    var actual = document.getElementById('chart-actual');
    var pasado = document.getElementById('chart-anterior');
    var antep = document.getElementById('chart-anterior2');
    var dataactual = actual.toDataURL();
    var datapasado = pasado.toDataURL();
    var dataantep = antep.toDataURL();
    $.ajax({
        url: "com.sine.imprimir/reporteexcelivagrafica.php",
        type: "POST",
        data: {transaccion: "imprimirpdf", type: '1', datos: datos, dataactual: dataactual, datapasado: datapasado, dataantep: dataantep},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                //alert(datos);
                window.open('reporteExcel/reporteBimestral.xlsx', '_blank');
                cargandoHide();
            }
        }
    });
}

function imprimirPDFBusqueda() {
    var actual = document.getElementById('chart-bimestre');
    var dataactual = actual.toDataURL();
    $.ajax({
        url: "com.sine.imprimir/reporte_sat.php",
        type: "POST",
        data: {transaccion: "imprimirpdf", type: '2', dataactual: dataactual},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                //alert(datos);
                VentanaCentrada('./ReportePDF/reporteImpuestos.pdf', 'Reporte', '', '1024', '768', 'true');
                cargandoHide();
            }
        }
    });
}

function imprimirEXCLBusqueda() {
    var actual = document.getElementById('chart-bimestre');
    var dataactual = actual.toDataURL();
    $.ajax({
        url: "com.sine.imprimir/reporteexcelivagrafica.php",
        type: "POST",
        data: {transaccion: "imprimirpdf", type: '2', dataactual: dataactual},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                //alert(datos);
                window.open('reporteExcel/reporteBimestral.xlsx', '_blank');
                cargandoHide();
            }
        }
    });
}

function imprimirIMG() {
    var actual = document.getElementById('chart-actual');
    var pasado = document.getElementById('chart-anterior');
    var antep = document.getElementById('chart-anterior2');
    image = actual.toDataURL("image/png", 1.0).replace("image/png", "image/octet-stream");
    var link = document.createElement('a');
    link.download = "actual.png";
    link.href = image;
    link.click();

    image = pasado.toDataURL("image/png", 1.0).replace("image/png", "image/octet-stream");
    var link = document.createElement('a');
    link.download = "pasado.png";
    link.href = image;
    link.click();

    image = antep.toDataURL("image/png", 1.0).replace("image/png", "image/octet-stream");
    var link = document.createElement('a');
    link.download = "antep.png";
    link.href = image;
    link.click();
}

function guardarGraficaIMG() {
    var bimestre = document.getElementById('chart-bimestre');
    image = bimestre.toDataURL("image/png", 1.0).replace("image/png", "image/octet-stream");
    var link = document.createElement('a');
    link.download = "graficaBusqueda.png";
    link.href = image;
    link.click();
}

function reporteIva() {
    var emisor = $("#emisor-historial").val();
    var receptor = $("#datos-facturacion").val();
    var ano = $("#anho-historial").val();
    var mes = $("#mes-historial").val();
    VentanaCentrada('./com.sine.imprimir/reporteiva.php?emisor=' + emisor + '&& receptor=' + receptor + '&& anho=' + ano + '&& mes=' + mes, 'Pago', '', '1024', '768', 'true');
}

function imprimirEXCLIVA() {
    var emisor = $("#emisor-historial").val();
    var receptor = $("#datos-facturacion").val();
    var ano = $("#anho-historial").val();
    var mes = $("#mes-historial").val();

    window.open('./com.sine.imprimir/reporteexceliva.php?emisor=' + emisor + '&& receptor=' + receptor + '&& anho=' + ano + '&& mes=' + mes, '_blank');
}

function imprimirCotizacion(id_cotizacion) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimircotizacion.php?cot=' + id_cotizacion, 'Cotizacion', '', '1024', '768', 'true');
    cargandoHide();
}

function imprimirPago(idpago) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimirpago.php?pago=' + idpago, 'Pago', '', '1024', '768', 'true');
    cargandoHide();
}

function imprimirFactura(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimirfactura.php?factura=' + id, 'Factura', '', '1024', '768', 'true');
    cargandoHide();
}

function imprimirCarta(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/imprimircarta.php?carta=' + id, 'Carta', '', '1024', '768', 'true');
    cargandoHide();
}

function imprimirreporteVentas() {
    var fechainicio = $('#fecha-inicio').val();
    var fechafin = $('#fecha-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var usuario = $("#usuario-vendedor").val();

    if (dateEmpty(fechainicio, "fecha-inicio") && dateEmpty(fechafin, "fecha-fin")) {
        VentanaCentrada('./com.sine.imprimir/reporte_ventas.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& cliente=' + idcliente + '&& estado=' + estado + '&& datos=' + datos + '&& usuario=' + usuario, 'Factura', '', '1024', '768', 'true');
    }
}

function imprimirVentasExcl() {
    var fechainicio = $('#fecha-inicio').val();
    var fechafin = $('#fecha-fin').val();
    var idcliente = $('#id-cliente').val();
    var estado = $('#estado-historial').val();
    var datos = $("#datos-facturacion").val();
    var usuario = $("#usuario-vendedor").val();

    if (isnEmpty(fechainicio, "fecha-inicio") && isnEmpty(fechafin, "fecha-fin")) {
        window.open('./com.sine.imprimir/imprimirreporteventas.php?fechainicio=' + fechainicio + '&& fechafin=' + fechafin + '&& idcliente=' + idcliente + '&& estado=' + estado + '&& datos=' + datos + '&& usuario=' + usuario, '_blank');
    }
}