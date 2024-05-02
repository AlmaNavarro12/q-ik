//------------------------------------------------------NAVEGACION ENTRE FORMULARIO DE VEHICULO
var current = 1;
var current_step; 
var next_step;
var steps = 11;

$(document).ready(function(){
    setProgressBar(current);
});

function next(){
    if(validaStep(current)){
        
        $('#li-'+current).removeClass('active');
        current_step = $('#step-'+current);
        
        current++;
        saveStepControl(current);

        $('#li-'+current).addClass('active');
        $('#li-'+current).removeClass('disabled');
        $('#btn-'+current).attr('disabled', false);
        next_step = $('#step-'+current);

        next_step.show('fade', 500);
        current_step.hide();
        setProgressBar(current);
        
        if(current >= 3){
            $('#img-step').hide();
        }

        if(current >= 11){
            verificaVideoTMP(0, 0);
            getVistaPrevia();
        }

        if(current == 1){
            $('#li-prev').addClass('disabled');
            $('#btn-prev').attr('disabled', true);
        } else {
            $('#li-prev').removeClass('disabled');
            $('#btn-prev').attr('disabled', false);
        }

        if(current == 11){
            $('#li-next').addClass('disabled');
            $('#btn-next').attr('disabled', true);
        } else {
            $('#li-next').removeClass('disabled');
            $('#btn-next').attr('disabled', false);
        }        
        saveTMP();
    }
}

function prev(){
    $('#li-'+current).removeClass('active');
    current_step = $('#step-'+current);
    
    current--;
    
    $('#li-'+current).addClass('active');
    next_step = $('#step-'+current);
    
    next_step.show('fade', 500);
    current_step.hide();
    setProgressBar(current);
    
    if(current < 3){
        $('#img-step').show('fade', 900);
    }

    if(current == 1){
        $('#li-prev').addClass('disabled');
        $('#btn-prev').attr('disabled', true);
    } else {
        $('#li-prev').removeClass('disabled');
        $('#btn-prev').attr('disabled', false);
    }

    if(current == 11){
        $('#li-next').addClass('disabled');
        $('#btn-next').attr('disabled', true);
    } else {
        $('#li-next').removeClass('disabled');
        $('#btn-next').attr('disabled', false);
    }
    saveTMP();
}

function goStep(step){
    var localcurrent = current;

    if(step != current){
        if(validaStep(current) || current == 11){
            if(step == 1){
                $('#li-prev').addClass('disabled');
                $('#btn-prev').attr('disabled', true);
            } else {
                $('#li-prev').removeClass('disabled');
                $('#btn-prev').attr('disabled', false);
            }

            $('#li-'+current).removeClass('active');
            current_step = $('#step-'+current);
            
            current = step;
            
            $('#li-'+current).addClass('active');
            next_step = $('#step-'+current);

            if(current < 3){
                $('#img-step').show('fade', 500);
            } else {
                $('#img-step').hide();
            }
            
            if(localcurrent > step){
                next_step.show('fade', 500);
            }
            else {
                next_step.show('fade', 500);
            }
            
            current_step.hide();
            setProgressBar(current);
            if(step == 11){
                getVistaPrevia();
                $('#li-next').addClass('disabled');
                $('#btn-next').attr('disabled', true);
            } else {
                $('#li-next').removeClass('disabled');
                $('#btn-next').attr('disabled', false);
            }
        }
    }
}

function validaStep(step){
    var valida = false;
    var valida_step = 0;
    switch(step){
        case 1:
            valida = true;
            break;
        case 2:
            var danho = [];
            $.each($("input[name='danhos-vehiculo']:checked"), function () {
                danho.push($(this).val());
            });
            var iddanhos = danho.join("-");
            if(!isCheckedOption(iddanhos, "danhos-vehiculo")){
                valida_step++;
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 3:
            var moldura = [];
            $.each($("input[name='molduras-vehiculo']:checked"), function () {
                moldura.push($(this).val());
            });
            var idmolduras = moldura.join("-");

            if(!isCheckedOption(idmolduras, "molduras-vehiculo")){
                valida_step++;
            }else if($('#chmol4').is(':checked')){
                if(!isnEmpty($('#otros-molduras').val(),"otros-molduras")){
                    valida_step++;
                } 
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 4:
            var tablero = [];
            $.each($("input[name='tablero-vehiculo']:checked"), function () {
                tablero.push($(this).val());
            });
            var idtablero = tablero.join("-");
            if(!isCheckedOption(idtablero, "tablero-vehiculo")){
                valida_step++;
            }else if($('#chtab6').is(':checked')){
                if(!isnEmpty($('#otros-tablero').val(),"otros-tablero")){
                    valida_step++;
                } 
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 5:
            var cableado = [];
            $.each($("input[name='cableado-vehiculo']:checked"), function () {
                cableado.push($(this).val());
            });
            var idcableado = cableado.join("-");
            if(!isCheckedOption(idcableado, "cableado-vehiculo")){
                valida_step++;
            }else if($('#chcab5').is(':checked')){
                if(!isnEmpty($('#otros-cableado').val(), 'otros-cableado')){
                    valida_step++;
                }
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 6:
            var ccorriente = [];
            $.each($("input[name='tcorriente-vehiculo']:checked"), function () {
                ccorriente.push($(this).val());
            });
            var idccorriente = ccorriente.join("-");
            if(!isCheckedOption(idccorriente, "tcorriente-vehiculo")){
                valida_step++;
            }else if($('#chtcor5').is(':checked')){
                if(!isnEmpty($('#otros-ccorriente').val(), 'otros-ccorriente')){
                    valida_step++;
                }
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 7:
            valida = true;
            break;
        case 8:
            var accesorio = [];
            $.each($("input[name='accesorio-gps-step']:checked"), function () {
                accesorio.push($(this).val());
            });
            var idaccesorio = accesorio.join("-");
            if(!isCheckedOption(idaccesorio, "accesorio-gps-step")){
                valida_step++;
            }else if($('#chacc4-step').is(':checked')){ 
                if(!isnEmpty($("#tipo-corte").val(), 'tipo-corte')){
                    valida_step++;
                }
            }else if(!isnEmpty($("#observaciones-step").val(), 'observaciones-step')){
                valida_step++;
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 9:
            var instalacion = [];
            $.each($("input[name='instalacion-list']:checked"), function () {
                instalacion.push($(this).val());
            });

            var idinstalacion = instalacion.join("-");
            if(isCheckedOption(idinstalacion, "instalacion-list")){
                if(!$('#chins1').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar GPS fijo.");
                }
                else if(!$('#chins2').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar Arnés protegido.");
                }
                else if(!$('#chins3').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar Corte de corriente/combustible protegido.");
                }
                else if(!$('#chins4').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar Conexiones del arnés al GPS conectadas y protegidas.");
                }
                else if(!$('#chins5').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar accesorios bien sujetados.");
                }

                if(valida_step == 0){
                    valida = true;
                }
            }
            break;
        case 10:
            valida = true;
            break;
    }
    return valida;
}

function validaStepIMG(step){
    var valida = false;
    var valida_step = 0;
    switch(step){
        case 1:
            var frenteimg = $("#frenteimg").val();
            var vinimg = $("#vinimg").val();
            var frentepic = $("#frentepic").val();
            var vinpic = $("#vinpic").val();

            if(frentepic == ""){
                if(!isnEmptyImg(frenteimg, 'frenteimg', 'Frente del vehiculo')){
                    valida_step++;
                    alertify.error("En el paso 1 sube la imagen del frete del vehículo.");
                }
            }

            if(vinpic == ""){
                if(!isnEmptyImg(vinimg,'vinimg', 'No. de Serie o VIN')){
                    valida_step++;
                    alertify.error("En el paso 1 sube la imagen del No. de serie o VIN.");
                }
            }

            if (valida_step == 0) {
                valida = true;
            }
            break;
        case 2:
            var mensaje = "";
            for(var i = 1; i <= 8; i++){
                if(i == 1){ mensaje = "En el paso 2 sube la evidencia de Parachoques Delantero."; }
                else if(i == 2){ mensaje = "En el paso 2 sube la evidencia de Parachoques trasero."; }
                else if(i == 3){ mensaje = "En el paso 2 sube la evidencia de Lateral izquierdo."; }
                else if(i == 4){ mensaje = "En el paso 2 sube la evidencia de Lateral derecho."; }
                else if(i == 5){ mensaje = "En el paso 2 sube la evidencia de Parabrisas."; }
                else if(i == 6){ mensaje = "En el paso 2 sube la evidencia de Cajuela."; }
                else if(i == 7){ mensaje = "En el paso 2 sube la evidencia de Cofre."; }
                else if(i == 8){ mensaje = "En el paso 2 sube la evidencia de Techo."; }
                
                if($('#ch'+i).is(':checked')){
                    if($('#filech'+i+'-2').val() == 0){
                        alertify.error(mensaje);
                        valida_step++;
                        break;
                    }    
                }
            }
            
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 3:
            var mensaje = "";
            for(var i = 1; i <= 4; i++){
                if(i == 1){ mensaje = "En el paso 3 sube la evidencia de Molduras dañadas."; }
                if(i == 2){ mensaje = "En el paso 3 sube la evidencia de Tornillos, grapas o pijas faltantes."; }
                if(i == 4){ mensaje = "En el paso 3 sube la evidencia de Otros daños."; }

                if(i != 3){
                    if($('#chmol'+i).is(':checked')){
                        if($('#filech'+i+'-3').val() == 0){
                            alertify.error(mensaje);
                            //$('#evidencia'+i+'-3').click();
                            valida_step++;
                            break;
                        } else if(i == 4){
                            if(!isnEmpty($('#otros-molduras').val(),"otros-molduras")){
                                alertify.error("En el paso 3 específica cuales son los otros daños o faltantes.");
                                valida_step++;
                            } 
                        }
                    }
                }
            }                    
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 4:
            var tabiimg = $("#tabiimg").val();
            var tabipic = $("#tabipic").val();
            if(tabipic == ""){
                if(!isnEmptyImg(tabiimg, 'tabiimg', 'Tablero Inicial')){
                    valida_step++;
                    alertify.error("En el paso 4 sube la imagen del tablero inicial.");
                }
            }
            
            var mensaje = "";
            for(var i = 4; i <= 6; i++){
                if(i == 4){ mensaje = "En el paso 4 sube la evidencia de Arnés o contra arnés dañado del clúster de instrumentos."; }
                if(i == 6){ mensaje = "En el paso 4 sube la evidencia de cuáles otros daños o que otros detalles tiene el vehículo."; }

                if(i != 5){
                    if($('#chtab'+i).is(':checked')){
                        if($('#filech'+i+'-4').val() == 0){
                            alertify.error(mensaje);
                            //$('#evidencia'+i+'-4').click();
                            valida_step++;
                            break;
                        } else if(i == 6){
                            if(!isnEmpty($('#otros-tablero').val(),"otros-tablero")){
                                alertify.error("Específica cuáles son los otros daños o que otros detalles tiene el vehículo.");
                                valida_step++;
                            } 
                        }
                    }
                }
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 5:
            var beforeIns = $("#beforeIns").val();
            var tabbefore = $("#tabbefore").val();
            if(tabbefore == ""){
                if(!isnEmptyImg(beforeIns,'beforeIns','Cableado Antes de la Instalación / Revisión') ){
                    valida_step++;
                    alertify.error("En el paso 5 sube la imagen del cableado.");
                }
            }
            if(valida_step == 0){
                valida = true;
            }                    
            break;
        case 6:
            if($('#chtcor5').is(':checked')){
                if(!isnEmpty($('#otros-ccorriente').val(), 'otros-ccorriente')){
                    valida_step++;
                }
            }
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 7:            
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 8:
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 9:
            var tabafter = $("#tabafter").val();
            var afterIns = $("#afterIns").val();
            if(tabafter == ""){
                if(!isnEmptyImg(afterIns, 'afterIns', 'Cableado despúes de la instalación.')){
                    valida_step++;
                    alertify.error("En el paso 9 sube la imagen del cableado final.");
                }
            }
            
            var mensaje = "";
            for(var i = 6; i <= 9; i++){
                if(i == 6){ mensaje = "En el paso 9 sube la evidencia de Apagado exitoso."; }
                if(i == 7){ mensaje = "En el paso 9 sube la evidencia de Desbloqueo exitoso."; }
                if(i == 9){ mensaje = "En el paso 9 sube la evidencia de Claxón exitoso."; }

                if(i != 8){
                    if($('#chins'+i).is(':checked')){
                        if($('#filech'+i+'-10').val() == 0){
                            alertify.error(mensaje);
                            $('#evidencia'+i+'-10').click();
                            valida_step++;
                            break;
                        }
                    }
                }
            }

            if($('#chacc1-step').is(':checked')){
                if(!$('#chins10').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 selecciona el botón de pánico.");
                } else {
                    if($('#filech11-10').val() == 0){
                        alertify.error("En el paso 9 sube la foto de donde quedo el botón de pánico.");
                        $('#evidencia11-10').click();
                        valida_step++;
                    } else if($('#filech12-10').val() == 0){
                        alertify.error("En el paso 9 sube la foto de la notificación.");
                        $('#evidencia12-10').click();
                        valida_step++;
                    } else if($('#descUbicacion').val() == ""){
                        alertify.error('En el paso 9 ingresa la descripcion de donde quedó el botón de pánico.');
                        valida_step++;
                    }
                }
            } else {
                if($('#chins10').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 quita el botón de pánico.");
                }
            }

            
            if($('#chacc4-step').is(':checked')){
                if($('#chins6').is(':checked')){
                    if($('#filech6-10').val() == ""){
                        valida_step++;
                        alertify.error("En el paso 9 sube la evidencia del apagado exitoso.");
                    }
                } else {
                    valida_step++;
                    alertify.error("En el paso 9 selecciona el apagado exitoso.");
                }

                if($('#chins7').is(':checked')){
                    if($('#filech6-10').val() == ""){
                        valida_step++;
                        alertify.error("En el paso 9 quita el desbloqueo exitoso.");
                    }
                } else {
                    valida_step++;
                    alertify.error("En el paso 9 selecciona el desbloqueo exitoso.");
                }
            } else {
                if($('#chins6').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 quita el apagado exitoso.");
                }
                if($('#chins7').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 quita el desbloqueo exitoso.");
                }
            }

            if($('#chacc8-step').is(':checked')){
                if(!$('#chins13').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 selecciona las cámaras.");
                } else {
                    if($('#filech13-10').val() == 0){
                        alertify.error("En el paso 9 sube la foto de las cámaras.");
                        $('#evidencia13-10').click();
                        valida_step++;
                    }
                }
            } else {
                if($('#chins13').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 quita las cámaras.");
                }
            }

            if($('#chacc13-step').is(':checked')){
                if(!$('#chins9').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 selecciona claxón exitoso.");
                } else {
                    if($('#filech9-10').val() == 0){
                        alertify.error("En el paso 9 sube el video del claxón exitoso.");
                        $('#evidencia9-10').click();
                        valida_step++;
                    }
                }
            } else {
                if($('#chins9').is(':checked')){
                    valida_step++;
                    alertify.error("En el paso 9 quita claxón exitoso.");
                }
            }


            
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 10:
            var tabfimg = $("#tabfimg").val();
            var tabfpic = $("#tabfpic").val();

            if(tabfpic == ""){
                if(!isnEmptyImg(tabfimg, 'tabfimg', 'Tablero Final')){
                    valida_step++;
                    alertify.error("En el paso 10 sube la imagen del tablero final.");
                }
            }
            if(valida_step == 0){
                valida = true
            }
            break;
        case 11:
            if(!isnEmpty($('#encargado-cliente').val(), "encargado-cliente")){
                valida_step++;
            } else if( $("#firma-actual").val() == ""){
                valida_step++;
                alertify.error("Antes de cerrar guarda la firma de la hoja.");
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
    }
    return valida;
}

//------------------------------------------------------NAVEGACION ENTRE FORMULARIO DE CAJA
function next_caja(){
    if(validaStepCaja(current)){
        $('#li-'+current+'-caja').removeClass('active');
        current_step = $('#step-'+current+'-caja');
        
        current++;
        saveStepControl(current);

        $('#li-'+current+'-caja').addClass('active');
        $('#li-'+current+'-caja').removeClass('disabled');
        $('#btn-'+current+'-caja').attr('disabled', false);
        next_step = $('#step-'+current+'-caja');

        next_step.show('fade', 500);
        current_step.hide();
        setProgressBar(current);

        if(current >= 6){
            verificaVideoTMP(0, 0);
            getVistaPrevia();
        }

        if(current == 1){
            $('#li-prev-caja').addClass('disabled');
            $('#btn-prev-caja').attr('disabled', true);
        } else {
            $('#li-prev-caja').removeClass('disabled');
            $('#btn-prev-caja').attr('disabled', false);
        }

        if(current == 7){
            $('#li-next-caja').addClass('disabled');
            $('#btn-next-caja').attr('disabled', true);
        } else {
            $('#li-next-caja').removeClass('disabled');
            $('#btn-next-caja').attr('disabled', false);
        }        
        saveTMP();
    }
}

function prev_caja(){
    $('#li-'+current+'-caja').removeClass('active');
    current_step = $('#step-'+current+'-caja');
    
    current--;
    
    $('#li-'+current+'-caja').addClass('active');
    next_step = $('#step-'+current+'-caja');
    
    next_step.show('fade', 500);
    current_step.hide();
    setProgressBar(current);

    if(current == 1){
        $('#li-prev-caja').addClass('disabled');
        $('#btn-prev-caja').attr('disabled', true);
    } else {
        $('#li-prev-caja').removeClass('disabled');
        $('#btn-prev-caja').attr('disabled', false);
    }

    if(current == 7){
        $('#li-next-caja').addClass('disabled');
        $('#btn-next-caja').attr('disabled', true);
    } else {
        $('#li-next-caja').removeClass('disabled');
        $('#btn-next-caja').attr('disabled', false);
    }
    saveTMP();
}

function goStep_caja(step){
    var localcurrent = current;

    if(step != current){
        if(validaStepCaja(current) || current == 7){
            if(step == 1){
                $('#li-prev-caja').addClass('disabled');
                $('#btn-prev-caja').attr('disabled', true);
            } else {
                $('#li-prev-caja').removeClass('disabled');
                $('#btn-prev-caja').attr('disabled', false);
            }

            $('#li-'+current+'-caja').removeClass('active');
            current_step = $('#step-'+current+'-caja');
            
            current = step;
            
            $('#li-'+current+'-caja').addClass('active');
            next_step = $('#step-'+current+'-caja');
            
            if(localcurrent > step){
                next_step.show('fade', 500);
            }
            else {
                next_step.show('fade', 500);
            }
            
            current_step.hide();
            setProgressBar(current);
            if(step == 7){
                getVistaPrevia();
                $('#li-next-caja').addClass('disabled');
                $('#btn-next-caja').attr('disabled', true);
            } else {
                $('#li-next-caja').removeClass('disabled');
                $('#btn-next-caja').attr('disabled', false);
            }
        }
    }
}

function validaStepCaja(step){
    var valida = false;
    var valida_step = 0;
    switch(step){
        case 1:
            valida = true;
            break;
        case 2:
            var cableado = [];
            $.each($("input[name='cableado-vehiculo-caja']:checked"), function () {
                cableado.push($(this).val());
            });
            var idcableado = cableado.join("-");
            if(!isCheckedOption(idcableado, "cableado-vehiculo-caja")){
                valida_step++;
            }else if($('#chcab5-caja').is(':checked')){
                if(!isnEmpty($('#otros-cableado-caja').val(), 'otros-cableado-caja')){
                    valida_step++;
                }
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 3:
            valida = true;
            break;
        case 4:
            var accesorio = [];
            $.each($("input[name='accesorio-gps-caja']:checked"), function () {
                accesorio.push($(this).val());
            });
            var idaccesorio = accesorio.join("-");
            if(!isCheckedOption(idaccesorio, "accesorio-gps-caja")){
                valida_step++;
            }
            else if(!isnEmpty($("#observaciones-caja").val(), 'observaciones-caja')){
                valida_step++;
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 5:
            var instalacion = [];
            $.each($("input[name='instalacion-list-caja']:checked"), function () {
                instalacion.push($(this).val());
            });

            var idinstalacion = instalacion.join("-");
            if(isCheckedOption(idinstalacion, "instalacion-list-caja")){
                if(!$('#chins1-caja').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar GPS fijo.");
                }
                else if(!$('#chins2-caja').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar Arnés protegido.");
                }
                else if(!$('#chins4-caja').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar Conexiones del arnés al GPS conectadas y protegidas.");
                }
                else if(!$('#chins5-caja').is(':checked')){
                    valida_step++;
                    alertify.error("Falta seleccionar Accesorios bien sujetados.");
                } else if ($("#chacc8-caja").is(":checked") && !$("#chins13-caja").is(":checked") ){
                    valida_step++;
                    alertify.error("Falta seleccionar Cámaras.");
                }
                if(valida_step == 0){
                    valida = true;
                }
            }
            break;
        case 6:
            valida = true;
            break;
    }
    return valida;
}

function validaStepIMGCaja(step){
    var valida = false;
    var valida_step = 0;
    switch(step){
        case 1:
            var frenteimg = $("#frenteimg-caja").val();
            var frentepic = $("#frentepic-caja").val();
            
            if(frentepic == ""){
                if(!isnEmptyImg(frenteimg, 'frenteimg-caja', 'Placas del vehículo')){
                    valida_step++;
                    alertify.error("En el paso 1 sube la imagen de las placas del vehículo.");
                }
            }

            if (valida_step == 0) {
                valida = true;
            }
            break;
        case 2:
            var beforeIns = $("#beforeIns-caja").val();
            var tabbefore = $("#tabbefore-caja").val();
            if(tabbefore == ""){
                if(!isnEmptyImg(beforeIns,'beforeIns-caja','Cableado antes de la instalación / revisión.') ){
                    valida_step++;
                    alertify.error("En el paso 2 sube la imagen del cableado antes de la instalación.");
                }
            }
            if(valida_step == 0){
                valida = true;
            }                    
            break;
        case 3:
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 4:            
            if(valida_step == 0){
                valida = true;
            }
            break;
        case 5:
            var tabafter = $("#tabafter-caja").val();
            var afterIns = $("#afterIns-caja").val();
            if(tabafter == ""){
                if(!isnEmptyImg(afterIns, 'afterIns-caja', 'Cableado después de la instalación.')){
                    valida_step++;
                    alertify.error("En el paso 2 sube la imagen del cableado después de la instalación.");
                }
            }

            var mensaje = "";
            for(var i = 6; i <= 9; i++){
                if(i == 6){ mensaje = "En el paso 5 sube la evidencia de Apagado exitoso."; }
                if(i == 7){ mensaje = "En el paso 5 sube la evidencia de Desbloqueo exitoso."; }
                if(i == 9){ mensaje = "En el paso 5 sube la evidencia de Claxón exitoso."; }

                if(i != 8){
                    if($('#chins'+i+'-caja').is(':checked')){
                        if($('#filech'+i+'-10-caja').val() == 0){
                            alertify.error(mensaje);
                            valida_step++;
                            break;
                        }
                    }
                }
            }

            if($('#chacc1-caja').is(':checked')){
                if(!$('#chins10-caja').is(':checked')){
                    alertify.error("En el paso 5 selecciona el Botón de pánico.");
                } else {
                    if($('#filech11-10-caja').val() == 0){
                        alertify.error("En el paso 5 sube la foto de donde quedo el Botón de pánico.");
                        valida_step++;
                    } else if($('#filech12-10-caja').val() == 0){
                        alertify.error("En el paso 5 sube la foto de la notificación.");
                        valida_step++;
                    } else if($('#descUbicacion-caja').val() == ""){
                        alertify.error('En el paso 5 ingresa la descripción de dónde quedó el Botón de pánico.');
                        valida_step++;
                    }
                }
            } else {
                if($('#chins10-caja').is(':checked')){
                    alertify.error("En el paso 5 quita el Botón de pánico.");
                }
            }

            if($('#chacc8-caja').is(':checked')){
                if(!$('#chins13-caja').is(':checked')){
                    alertify.error("En el paso 5 selecciona las Cámaras.");
                } else {
                    if($('#filech13-10-caja').val() == 0){
                        alertify.error("En el paso 5 sube la foto de las Cámaras.");
                        valida_step++;
                    }
                }
            } else {
                if($('#chins13-caja').is(':checked')){
                    alertify.error("En el paso 5 quita las Cámaras.");
                }
            }

            if($('#chacc13-caja').is(':checked')){
                if(!$('#chins9-caja').is(':checked')){
                    alertify.error("En el paso 5 selecciona Claxón exitoso.");
                } else {
                    if($('#filech9-10-caja').val() == 0){
                        alertify.error("En el paso 5 sube el video del Claxón exitoso.");
                        valida_step++;
                    }
                }
            } else {
                if($('#chins9-caja').is(':checked')){
                    alertify.error("En el paso 5 quita Claxón exitoso.");
                }
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
        case 6:
            if(valida_step == 0){
                valida = true
            }
            break;
        case 7:
            if(!isnEmpty($('#encargado-cliente').val(), "encargado-cliente")){
                valida_step++;
            } else if( $("#firma-actual-caja").val() == ""){
                valida_step++;
                alertify.error("Antes de cerrar guarda la firma de la hoja.");
            }

            if(valida_step == 0){
                valida = true;
            }
            break;
    }
    return valida;
}

//------------------------------------------------------FIRMA
function firmaInstalacion(tipo) {
    var firma = (tipo==1) ? 'firma-canvas' : 'firma-canvas-caja';
    var clear = (tipo==1) ? 'clear' : 'clear-caja';
    var undo = (tipo==1) ? 'undo' : 'undo-caja';

    var canvas = document.getElementById(firma);
    var signaturePad = new SignaturePad(canvas);
    signaturePad.on();

    document.getElementById(clear).addEventListener('click', function () {
        signaturePad.clear();
    });

    document.getElementById(undo).addEventListener('click', function () {
        var data = signaturePad.toData();
        if (data) {
            data.pop();
            signaturePad.fromData(data);
        }
    });
}

function firmaModalIns() {
    var canvas = document.getElementById('firma-modal');
    var signaturePad = new SignaturePad(canvas);
    signaturePad.on();

    document.getElementById('clearmod').addEventListener('click', function () {
        signaturePad.clear();
    });

    document.getElementById('undomod').addEventListener('click', function () {
        var data = signaturePad.toData();
        if (data) {
            data.pop(); 
            signaturePad.fromData(data);
        }
    });
}
//------------------------------------------------------INICIO DE EQUIPO GPS
function nuevoGPS(){
    $('#titleGPS').html('Agregar nuevo modelo de GPS');
    $('#cve-gps').val(0);
    $("#nuevo-gps").val("");
    $('#modal-gps').modal('show');
}

function loadListaGPS(pag = "") {
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "loadListaGPS", REF: $("#buscar-equipo").val(), numreg: $("#num-reg").val(), pag: pag},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                $("#body-lista-gps").html(datos);
            }
        }
    });
}

function insertarModeloGPS() {
    var idGPS = $('#cve-gps').val();
    var nuevogps = $("#nuevo-gps").val();

    if (isnEmpty(nuevogps, "nuevo-gps") ) {
        $.ajax({
            url: "com.sine.enlace/enlaceinstalacion.php",
            type: "POST",
            data: {transaccion: "insertargps", nuevogps: nuevogps, idGPS: idGPS},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    cargandoHide();
                    alertify.error(res);
                } else {
                    cargandoHide();
                    if(idGPS == 0){
                        alertify.success('Equipo agregado correctamente.');
                    } else {
                        alertify.success('Equipo actualizado correctamente.');
                    }
                    $("#modal-gps").modal('hide');
                    $("#modelo-gps").val(nuevogps);
                    loadListaGPS();
                }
            }
        });
    }
}

function editarGPS(id, modelo){
    $('#titleGPS').html('Editar modelo de GPS');
    $('#cve-gps').val(id);
    $("#nuevo-gps").val(modelo);
    changeText("#btn-form-gps", "Guardar <span class='fas fa-save'></span>");
    $('#modal-gps').modal('show');
}

function eliminarGPS(id){
    alertify.confirm("¿Estás seguro que deseas eliminar este equipo GPS, una vez hecho esto no habrá forma de revertirse?", function () {
        cargandoShow();
        $.ajax({
            data : {transaccion: "eliminarGPS", id: id},
            url  : "com.sine.enlace/enlaceinstalacion.php",
            type : "POST",
            success : function(datos){
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(datos);
                }
                cargandoHide();
                loadListaGPS();
            }
        });
    }).set({title: "Q-ik"});
}

function aucompletarClienteIns() {
    $('#nombre-cliente').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            var nombre = ui.item.nombre;
            $("#id-cliente").val(id);
            $("#nombre-cliente").val(nombre);
        }
    });
}

function autocompletarCliente() {
    $('#cliente-search').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=nombrecliente",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function checkTipo(tipo = ""){
    var tipounidad = (tipo=="") ? $("input[name=tipo-unidad]:checked").val() : tipo;
    console.log(tipounidad);
    if(tipounidad == "2"){
        $("#km-unidad").val("0");
        $("#marca-unidad").val("");
        $("#modelo-unidad").val("");
        $("#anho-unidad").val("");
        $("#color-unidad").val("");
        $("#serie-unidad").val("");
        changeText("#label-marca", "");
        changeText("#label-modelo", "");
        changeText("#label-anho", "");
        changeText("#label-color", "");
        changeText("#label-serie", "");
        changeText("#label-kilometraje", "");
        changeText("#label-economico", "*");
        changeText("#label-placas", "*");
        $("#chacc1-div, #chacc2-div, #chacc3-div, #chacc4-div, #chacc5-div, #chacc11-div").removeClass('d-flex').hide();
    }else{
        changeText("#label-marca", "*");
        changeText("#label-modelo", "*");
        changeText("#label-anho", "*");
        changeText("#label-color", "*");
        changeText("#label-serie", "*");
        changeText("#label-kilometraje", "*");
        changeText("#label-economico", "*");
        changeText("#label-placas", "*");
        $("#chacc1-div, #chacc2-div, #chacc3-div, #chacc4-div, #chacc5-div, #chacc11-div").addClass('d-flex').show();
        $("#km-unidad").val("");
    }
}

function loadOpcionesInstalador() {
    $.ajax({
        url: 'com.sine.enlace/enlaceinstalacion.php',
        type: 'POST',
        data: {transaccion: 'opcionesinstalador'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == 0) {
                alertify.error(res);
            } else {
                $(".contenedor-instaladores").html(datos);
            }
        }
    });
}

function getInstaladoresCH(){
    $.ajax({
        data: {transaccion: "getInstaladoresCH"},
        url : "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        success: function(datos){
            $('#asignarInstalador').html(datos);
        }
    })
}

function hideInput() {
    if ($("#chserv8").prop('checked')) {
        $("#div-imei").show('slow');
    } else if (!$("#chserv8").prop('checked')) {
        $("#div-imei").hide('slow');
    }
    if ($("#chserv9").prop('checked')) {
        $("#div-sim").show('slow');
    } else if (!$("#chserv9").prop('checked')) {
        $("#div-sim").hide('slow');
    }
}

function aucompletarModeloGPS() {
    $('#modelo-gps').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=modelogps",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
            $('#existeModelo').val('1');
        }
    });
}

function aucompletarModeloGPS2() {
    $('#modelo-anterior').autocomplete({
        source: "com.sine.enlace/enlaceautocompletar.php?transaccion=modelogps",
        select: function (event, ui) {
            var a = ui.item.value;
            var id = ui.item.id;
        }
    });
}

function loadFechaIns() {
    $.ajax({
        url: 'com.sine.enlace/enlaceinstalacion.php',
        type: 'POST',
        data: {transaccion: 'fecha'},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 5000);
            if (bandera == '') {
                alertify.error(res);
            } else {
                var array = datos.split("</tr>");
                
                var fecha = array[0];
                var hora = array[1];
                $("#fecha-servicio").val(fecha);
                $("#hora-servicio").val(hora);
            }
        }
    });
}

function insertarInstalacion(idorden = null) {
    var folio = $("#folio-servicio").val();
    var fechaservicio = $("#fecha-servicio").val();
    var horaservicio = $("#hora-servicio").val();
    var idcliente = $("#id-cliente").val();
    var nombrecliente = $("#nombre-cliente").val();
    var plataforma = $("#plataforma").val();
    var tipounidad = $("input[name=tipo-unidad]:checked").val();
    var marca = $("#marca-unidad").val();
    var modelo = $("#modelo-unidad").val();
    var anho = $("#anho-unidad").val();
    var color = $("#color-unidad").val();
    var serie = $("#serie-unidad").val();
    var numeconomico = $("#num-economico").val();
    var km = $("#km-unidad").val();
    var placas = $("#placas-unidad").val();

    var tservicio = [];
    $.each($("input[name='tservicio-vehiculo']:checked"), function () {
        tservicio.push($(this).val());
    });
    var idtservicio = tservicio.join("-");

    var modeloanterior = "NA";
    var imeianterior = "NA";
    var simanterior = "0";
    if ($("#chserv8").prop('checked')) {
        modeloanterior = $("#modelo-anterior").val();
        imeianterior = $("#imei-anterior").val();
    }
    if ($("#chserv9").prop('checked')) {
        simanterior = $("#tel-anterior").val();
    }
    var otrostservicio = $("#otros-tservicio").val();

    var gpsvehiculo = $("#modelo-gps").val();
    var imei = $("#imei-gps").val();
    var numtelefono = $("#num-telefono").val();
    var idinstalador = $("#nombre-instalador").val();

    var accesorio = [];
    $.each($("input[name='accesorio-gps']:checked"), function () {
        accesorio.push($(this).val());
    });
    var idaccesorio = accesorio.join("-");

    var asingnacion = [];
    $.each($("input[name='asigna-instalador']:checked"), function () {
        asingnacion.push($(this).val());
    });
    var idasingnacion = asingnacion.join("-");

    
    if (idcliente == "") {
        idcliente = '0';
    }

    var valida = 0;
    
    if(tipounidad == 2){
        if (isnEmpty(fechaservicio, "fecha-servicio") && isnEmpty(horaservicio, "hora-servicio") && isnEmpty(nombrecliente, "nombre-cliente") && isnEmpty(plataforma,"plataforma") && isnEmpty(numeconomico, "num-economico") && isnEmpty(placas, "placas-unidad") && isCheckedOption(idtservicio, "tservicio-vehiculo") && isnEmpty(gpsvehiculo, 'modelo-gps') && isnEmpty(imei, 'imei-gps') && isPhoneNumber(numtelefono, 'num-telefono') && isnEmpty(idinstalador, 'nombre-instalador') && isCheckedOption(idaccesorio, "accesorio-gps") && isCheckedOption(idasingnacion, "asigna-instalador")) {
            if($('#chserv8').is(':checked')){
                if(!isnEmpty($('#modelo-anterior').val(),"modelo-anterior")){
                    valida++;
                }else if(!isnEmpty($('#imei-anterior').val(),"imei-anterior")){
                    valida++;
                }
            }                
            
            if($('#chserv9').is(':checked')){
                if(!isnEmpty($('#tel-anterior').val(),"tel-anterior")){
                    valida++;
                }
            }                
            
            if($('#chserv7').is(':checked')){
                if(!isnEmpty($('#otros-tservicio').val(),"otros-tservicio")){
                    valida++;
                }
            }
        } else {
            valida++;
        }
    } else {
        if (isnEmpty(fechaservicio, "fecha-servicio") && isnEmpty(horaservicio, "hora-servicio") && isnEmpty(nombrecliente, "nombre-cliente") && isnEmpty(plataforma,"plataforma") && isnEmpty(marca, "marca-unidad") && isnEmpty(modelo, "modelo-unidad") && isnEmpty(anho, "anho-unidad") && isnEmpty(color, "color-unidad") && isnEmpty(serie, "serie-unidad") && isnEmpty(numeconomico, "num-economico") && isnEmpty(km, "km-unidad") && isnEmpty(placas, "placas-unidad") && isCheckedOption(idtservicio, "tservicio-vehiculo") && isnEmpty(gpsvehiculo, 'modelo-gps') && isnEmpty(imei, 'imei-gps') && isPhoneNumber(numtelefono, 'num-telefono') && isnEmpty(idinstalador, 'nombre-instalador') && isCheckedOption(idaccesorio, "accesorio-gps") && isCheckedOption(idasingnacion, "asigna-instalador")) {
            if($('#chserv8').is(':checked')){
                if(!isnEmpty($('#modelo-anterior').val(),"modelo-anterior")){
                    valida++;
                }else if(!isnEmpty($('#imei-anterior').val(),"imei-anterior")){
                    valida++;
                }
            }                
            
            if($('#chserv9').is(':checked')){
                if(!isnEmpty($('#tel-anterior').val(),"tel-anterior")){
                    valida++;
                }
            }                
            
            if($('#chserv7').is(':checked')){
                if(!isnEmpty($('#otros-tservicio').val(),"otros-tservicio")){
                    valida++;
                }
            }
        } else {
            valida++;
        }
    }
    
    var transaccion = idorden ? "actualizarinstalacion" : "insertarinstalacion";
    if(valida == 0){
        cargandoShow();
        $.ajax({
            url: "com.sine.enlace/enlaceinstalacion.php",
            type: "POST",
            data: {transaccion: transaccion, folio:folio, idorden: idorden, fechaservicio: fechaservicio, horaservicio: horaservicio, idcliente: idcliente, nombrecliente: nombrecliente, plataforma: plataforma, marca: marca, modelo: modelo, anho: anho, color: color, serie: serie, numeconomico: numeconomico, km: km, placas: placas, tipounidad: tipounidad, idtservicio: idtservicio, otrostservicio: otrostservicio, modeloanterior: modeloanterior, imeianterior: imeianterior, simanterior: simanterior, gpsvehiculo: gpsvehiculo, imei: imei, numtelefono: numtelefono, idinstalador: idinstalador, idaccesorio: idaccesorio, idasingnacion: idasingnacion},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    cargandoHide();
                    alertify.error(res);
                } else {
                    var mensaje = idorden ? "Instalación actualizada correctamente. " : "Instalación registrada correctamente.";
                    alertify.success(mensaje);
                    loadView('listainstalacion');
                    cargandoHide();
                }
            }
        });
    }
}

function buscarInstalacion(pag = "") {
    var REF = $("#buscar-instalacion").val();
    var servicio = $("#tipo-servicio").val();
    var filtro = $("#filtro-busqueda").val();
    var numreg = $("#num-reg").val();
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "filtrarinstalacion", REF: REF, servicio:servicio, filtro:filtro, pag: pag, numreg: numreg},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#body-lista-instalacion").html(datos);

            }
        }
    });
}

function editarInstalacion(idinstalacion, tipo) {
    var gentmp = 0;
    (tipo == 2) ? gentmp = 1 : gentmp = 0;
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "editarinstalacion", idinstalacion: idinstalacion, gentmp: gentmp},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                var array = datos.split("</tr>");
                var tipounidad = array[27];
                if(tipo == 1){
                    loadView('instalacion');
                } else if(tipo == 2){
                    if(tipounidad == 1){
                        loadView('pasosvehiculo');
                    } else if(tipounidad == 2){
                        loadView('pasoscaja');
                    }
                }
                window.setTimeout("setValoresEditarInstalacion('" + datos + "'," + tipo + ")", 500);;
            }
        }
    });
}

function eliminarInstalacion(iid) {
    alertify.confirm("¿Estás seguro que deseas eliminar esta instalación?", function () {
        $.ajax({
            url: "com.sine.enlace/enlaceinstalacion.php",
            type: "POST",
            data: {transaccion: "eliminarinstalacion", iid: iid},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    loadView('listainstalacion');
                }
                
            }
        });
    }).set({title: "Sine Facturacion"});
}

function setValoresEditarInstalacion(datos, tipo_edicion) {
    changeText("#contenedor-titulo-form-instalacion", "Editar instalación");
    changeText("#btn-form-instalacion", "Guardar cambios <span class='fas fa-save'></span>");
    var array = datos.split("</tr>");
    var idorden = array[0];
    var fechaservicio = array[1];
    var horaservicio = array[2];
    var nombrecliente = array[3];
    var marca = array[4];
    var modelo = array[5];
    var anho = array[6];
    var color = array[7];
    var serie = array[8];
    var numeco = array[9];
    var km = array[10];
    var placas = array[11];
    var gpsvehiculo = array[12];
    var imei = array[13];
    var numtelefono = array[14];
    var idinstalador = array[15];
    var idaccesorio = array[16];
    var instalador = array[18];
    var serie_folio = array[19];
    var letra = array[20];
    var folio = array[21];
    var idcliente = array[22];
    var modeloanterior = array[23];
    var imeianterior = array[24];
    var simaanterior = array[25];
    var plataforma = array[26];
    var tipounidad = array[27];
    var idasignacion = array[28];
    var idtservicio = array [29];
    var otrosservicios = array[30];

    checkTipo(tipounidad);
    $('#cve_orden').val(idorden);
    loadOpcionesFolios(0, serie_folio, letra+folio);
    $("#fecha-servicio").val(fechaservicio);
    $("#hora-servicio").val(horaservicio);
    $("#id-cliente").val(idcliente);
    $("#nombre-cliente").val(nombrecliente);
    $("#plataforma").val(plataforma);
    $("#tipo" + tipounidad).prop('checked', true);
    $("#marca-unidad").val(marca);
    $("#modelo-unidad").val(modelo);
    $("#anho-unidad").val(anho);
    $("#color-unidad").val(color);
    $("#serie-unidad").val(serie);
    $("#num-economico").val(numeco);
    $("#km-unidad").val(km);
    $("#placas-unidad").val(placas);

    var servicio = idtservicio.split("-");
    for (var i = 0, max = servicio.length; i < max; i++) {
        $("#chserv" + servicio[i]).prop('checked', true);
        if (servicio[i] == 8) {
            $("#div-imei").show('slow');
        } else if (servicio[i] == 9) {
            $("#div-sim").show('slow');
        }
    }
    
    if (modeloanterior != "NA") {
        $("#modelo-anterior").val(modeloanterior);
    }
    if (imeianterior != "NA") {
        $("#imei-anterior").val(imeianterior);
    }

    if (simaanterior != '0') {
        $("#tel-anterior").val(simaanterior);
    }
    $("#otros-tservicio").val(otrosservicios);

    $("#modelo-gps").val(gpsvehiculo);
    $("#imei-gps").val(imei);
    $("#num-telefono").val(numtelefono);
    
    $("#option-default-nombre-instalador").val(idinstalador);
    $("#option-default-nombre-instalador").text(instalador);


    var accesorio = idaccesorio.split("-");
    for (var i = 0, max = accesorio.length; i < max; i++) {
        $("#chacc" + accesorio[i]).prop('checked', true);
    }

    window.setTimeout(function(){ 
        var asignacion = idasignacion.split("-");
        for (var i = 0, max = asignacion.length; i < max; i++) {
            $("#chInstalador" + asignacion[i]).prop('checked', true);
        }
    },500);

    $("#btn-form-instalacion").attr("onclick", "insertarInstalacion(" + idorden + ");");
    $("#fecha-servicio").attr("disabled", true);
    $("#hora-servicio").attr("disabled", true);
                    
    cargandoHide();

    if(tipo_edicion == 2){
        checkTMP(idorden); 
        if(tipounidad == 2){
            steps = 7;
        } else if(tipounidad == 1){
            steps = 11;
        }
    } 
}

function checkTMP(idorden){
    $.ajax({
        data : {transaccion: "checkTMP", idorden: idorden},
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "POST",
        success  : function(res){
            window.setTimeout(()=>{setValoresTMP(res, idorden);},900);
        }
    })
}

function setValoresTMP(datos, idorden){
    var div = datos.split("</tr>");
    var iddanhos = div[0];
    var idmolduras = div[1];
    var otrosmolduras = div[2];
    var idtablero = div[3];
    var otrostablero = div[4];
    var idcableado = div[5];
    var otroscableado = div[6];
    var idcorriente = div[7];
    var otroscorriente = div[8];
    var gpsvehiculo = div[14];
    var imei = div[15];
    var numtelefono = div[16];
    var idinstalador = div[17];
    var idaccesorio = div[18];
    var observaciones = div[19];
    var idinstalacion = div[20];
    var encargado = div[21];
    var firma = div[22];
    var imgfrente = div[23];
    var imgvin = div[24];
    var imgtabinicial = div[25];
    var imgtabfinal = div[26];
    var imgantesinst = div[27];
    var imgdespuesinst = div[28];
    var observacionesgral = div[29];
    var ubicacionbtnpanico = div[30];
    var tipo_corte = div[31];
    var folio = div[32];
    var imgfrentebase = div[33];
    var imgvinbase = div[34];
    var imgtabinibase = div[35];
    var imginibase = div[36];
    var imgfinbase = div[37];
    var imgtabfinbase = div[38];
    var tipounidad = div[39];
    
    $('#cve_orden').val(idorden);
    $('#tipo_unidad').val(tipounidad);
    $('#spn-cve-orden').html(folio);

    //----------------------------PASO 1
    if (imgfrente != "") {
        if($('#tipo_unidad').val() == '2'){
            $("#label-frenteimg-caja").css("border-color", "green");
            $("#frentepic-caja").val(imgfrente);
            $("#frenteactualizar-caja").val(imgfrente);
            $("#imgfrente-caja").html("<a class='btn btn-success btn-xs col-11 me-2' onclick=\"showimg('1','"+imgfrente+"', '"+imgfrentebase+"')\">"+imgfrente+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgfrentevehiculo', 'imgfrentebase', 'imgfrente-caja', 'label-frenteimg-caja', 'frentepic-caja', 'frenteactualizar-caja')\"><span class='fas fa-times'></span></a>");

        } else {
            $("#label-frenteimg").css("border-color", "green");
            $("#frentepic").val(imgfrente);
            $("#frenteactualizar").val(imgfrente);
            $("#imgfrente").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('1','"+imgfrente+"', '"+imgfrentebase+"')\">"+imgfrente+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgfrentevehiculo', 'imgfrentebase', 'imgfrente', 'label-frenteimg', 'frentepic', 'frenteactualizar')\"><span class='fas fa-times'></span></a>");

        }        
    }

    if (imgvin != "") {
        if($('#tipo_unidad').val() == '2'){ 
            $("#label-vinimg-caja").css("border-color", "green");
            $("#vinpic-caja").val(imgvin);
            $("#vinpicactualizar-caja").val(imgvin);
            $("#imgvin-caja").html("<a class='btn btn-success btn-xs col-11 me-2' onclick=\"showimg('2','"+imgvin+"', '"+imgvinbase+"')\">"+imgvin+" <span class='fas fa-image'></span></a>"
                                 + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgnserie', 'imgseriebase', 'imgvin-caja', 'label-vinimg-caja', 'vinpic-caja', 'vinpicactualizar-caja')\"><span class='fas fa-times'></span></a>");

        } else {
            $("#label-vinimg").css("border-color", "green");
            $("#vinpic").val(imgvin);
            $("#vinpicactualizar").val(imgvin);
            $("#imgvin").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('2','"+imgvin+"', '"+imgvinbase+"')\">"+imgvin+" <span class='fas fa-image'></span></a>" 
            + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgnserie', 'imgseriebase', 'imgvin', 'label-vinimg', 'vinpic', 'vinpicactualizar')\"><span class='fas fa-times'></span></a>");
        }
    }

    //----------------------------------PASO 2
    var danhos = iddanhos.split("-");
    for (var i = 0, max = danhos.length; i < max; i++) {
        $("#ch" + danhos[i]).prop('checked', true);
    }

    //---------------------------------PASO 3
    var molduras = idmolduras.split("-");
    for (var i = 0, max = molduras.length; i < max; i++) {
        $("#chmol" + molduras[i]).prop('checked', true);
    }
    $("#otros-molduras").val(otrosmolduras);

    //---------------------------------PASO 4
    if (imgtabinicial != "") {
        $("#label-tabiimg").css("border-color", "green");
        $("#tabipic").val(imgtabinicial);
        $("#tabipicactualizar").val(imgtabinicial);
        $("#imgtabi").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('3', '"+imgtabinicial+"', '"+imgtabinibase+"')\">"+imgtabinicial+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgtabinicial', 'imgtabinibase', 'imgtabi', 'label-tabiimg', 'tabipic', 'tabipicactualizar')\"><span class='fas fa-times'></span></a>");
    }

    var tablero = idtablero.split("-");
    for (var i = 0, max = tablero.length; i < max; i++) {
        $("#chtab" + tablero[i]).prop('checked', true);
    }
    $("#otros-tablero").val(otrostablero);

    //---------------------------------PASO 5
    if (imgantesinst != "") {
        if($('#tipo_unidad').val() == '2'){
            $("#label-beforeIns-caja").css("border-color", "green");
            $("#tabbefore-caja").val(imgantesinst);
            $("#tabbeforeactualizar-caja").val(imgantesinst);
            $("#imgbeforeIns-caja").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('5', '"+imgantesinst+"', '"+imginibase+"')\">"+imgantesinst+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgantesinst', 'imgantesbase', 'imgbeforeIns-caja', 'label-beforeIns-caja', 'tabbefore-caja', 'tabbeforeactualizar-caja')\"><span class='fas fa-times'></span></a>");

        } else {
            $("#label-beforeIns").css("border-color", "green");
            $("#tabbefore").val(imgantesinst);
            $("#tabbeforeactualizar").val(imgantesinst);
            $("#imgbeforeIns").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('5', '"+imgantesinst+"', '"+imginibase+"')\">"+imgantesinst+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgantesinst', 'imgantesbase', 'imgbeforeIns', 'label-beforeIns', 'tabbefore', 'tabbeforeactualizar')\"><span class='fas fa-times'></span></a>");
        }
    }

    var cableado = idcableado.split("-");
    if($('#tipo_unidad').val() == '2'){
        for (var i = 0, max = cableado.length; i < max; i++) {
            $("#chcab" + cableado[i] + "-caja").prop('checked', true);
        }
        $("#otros-cableado-caja").val(otroscableado);

    } else {
        for (var i = 0, max = cableado.length; i < max; i++) {
            $("#chcab" + cableado[i]).prop('checked', true);
        }
        $("#otros-cableado").val(otroscableado);
    }
    
    //----------------------------------------PASO 6
    var corriente = idcorriente.split("-");
    for (var i = 0, max = corriente.length; i < max; i++) {
        $("#chtcor" + corriente[i]).prop('checked', true);
    }
    $("#otros-ccorriente").val(otroscorriente);

    //---------------------------------------PASO 7
    if($('#tipo_unidad').val() == '2'){
        $("#modelo-gps-caja").val(gpsvehiculo);
        $("#imei-gps-caja").val(imei);
        $("#num-telefono-caja").val(numtelefono);
        $("#nombre-instalador-caja").val(idinstalador);
    } else {
        $("#modelo-gps-step").val(gpsvehiculo);
        $("#imei-gps-step").val(imei);
        $("#num-telefono-step").val(numtelefono);
        $("#nombre-instalador-step").val(idinstalador);
    }

    //-------------------------------PASO 8
    var txt = observaciones.replace(new RegExp("<corte>", 'g'), '\n');
    var accesorio = idaccesorio.split("-");
    if($('#tipo_unidad').val() == '2'){
        for (var i = 0, max = accesorio.length; i < max; i++) {
            $("#chacc" + accesorio[i] + '-caja').prop('checked', true);
        }
        $("#observaciones-caja").val(txt);
    } else {
        for (var i = 0, max = accesorio.length; i < max; i++) {
            $("#chacc" + accesorio[i] + '-step').prop('checked', true);
        }
        $("#observaciones-step").val(txt);
        if($('#chacc4-step').is(':checked')){
            $('#container-tipo-corte').collapse('show');
            $('#tipo-corte').val(tipo_corte);
        }
    }
    
    //----------------------------PASO 9
    if (imgdespuesinst != "") {
        if($('#tipo_unidad').val() == '2'){
            $("#label-afterIns-caja").css("border-color", "green");
            $("#tabafter-caja").val(imgdespuesinst);
            $("#tabafteractualizar-caja").val(imgdespuesinst);
            $("#imgafterIns-caja").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('6', '"+imgdespuesinst+"', '"+imgfinbase+"')\">"+imgdespuesinst+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgdespuesinst', 'imgdespuesbase', 'imgafterIns-caja', 'label-afterIns-caja', 'tabafter-caja', 'tabafteractualizar-caja')\"><span class='fas fa-times'></span></a>");

        } else {
            $("#label-afterIns").css("border-color", "green");
            $("#tabafter").val(imgdespuesinst);
            $("#tabafteractualizar").val(imgdespuesinst);
            $("#imgafterIns").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('6', '"+imgdespuesinst+"', '"+imgfinbase+"')\">"+imgdespuesinst+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgdespuesinst', 'imgdespuesbase', 'imgafterIns', 'label-afterIns', 'tabafter', 'tabafteractualizar')\"><span class='fas fa-times'></span></a>");
        }
    }

    var instalacion = idinstalacion.split("-");
    if($('#tipo_unidad').val() == '2'){
        for (var i = 0, max = instalacion.length; i < max; i++) {
            $("#chins" + instalacion[i] + "-caja").prop('checked', true);
        }

        if($('#chins10-caja').is(':checked')){
            $('#complementoBtnPanico-caja').collapse('show');
            $('#descUbicacion-caja').val(ubicacionbtnpanico);
        } 

    } else {
        for (var i = 0, max = instalacion.length; i < max; i++) {
            $("#chins" + instalacion[i]).prop('checked', true);
        }

        if($('#chins10').is(':checked')){
            $('#complementoBtnPanico').collapse('show');
            $('#descUbicacion').val(ubicacionbtnpanico);
        }        
    }
    
    //--------------------------------------PASO 10
    if (imgtabfinal != "") {
        $("#label-tabfimg").css("border-color", "green");
        $("#tabfpic").val(imgtabfinal);
        $("#tabfpicactualizar").val(imgtabfinal);
        $("#imgtabf").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('4', '"+imgtabfinal+"', '"+imgtabfinbase+"')\">"+imgtabfinal+" <span class='fas fa-image'></span></a>"
        + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgtabfinal', 'imgtabfinbase', 'imgtabf', 'label-tabfimg', 'tabfpic', 'tabfpicactualizar')\"><span class='fas fa-times'></span></a>");
    }

    if($('#tipo_unidad').val() == '2'){
        $('#observaciones-gral-caja').val(observacionesgral);
    } else {
        $('#observaciones-gral').val(observacionesgral);
    }
    
    tablaIMG();
    tablaVid();
    getFileTMPOtras();
    getFileTMPVid();

    //-------------------------------------PASO 11
    $("#encargado-cliente").val(encargado);
    if(firma != ""){
        if($('#tipo_unidad').val() == '2'){
            $("#firma-actual-caja").val(firma);
            $("#div-firma-caja").html("<label class='control-label text-right text-muted mb-1 fw-semibold'>Firma actual</label><img src='" + firma + "' width='100%' id='imgfirma'>");
        } else {
            $("#firma-actual").val(firma);
            $("#div-firma").html("<label class='control-label text-right text-muted mb-1 fw-semibold'>Firma actual</label><img src='" + firma + "' width='100%' id='imgfirma'>");
        }
    }    
    
    if(current >= 11){
        $('#hidpaso').val(0);
        $('#hidcheck').val(0);
    }
    getStepControl();
}

function getStepControl(){
    var cve_orden = $('#cve_orden').val();
    var tipo = $('#tipo_unidad').val();
    $.ajax({
        data: {transaccion: "getStep", cve_orden: cve_orden},
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "post",
        success: function(datos){
            for(var i = 1; i <= datos; i++){
                if(tipo == 1){ 
                    if(i > 1){
                        $('#li-prev').removeClass('disabled');
                        $('#btn-prev').attr('disabled', false);
                    }

                    if(i > 2){
                        $('#img-step').hide();
                    }

                    current = i;
                    $('#step-'+(i-1)).hide();
                    $('#step-'+i).show();
                    setProgressBar(current);

                    $('#li-'+(i-1)).removeClass('active');
                    $('#li-'+i).removeClass('disabled');
                    $('#li-'+i).addClass('active');
                    $('#btn-'+i).attr('disabled', false);

                    if(i == 11){
                        getVistaPrevia();
                        $('#li-next').addClass('disabled');
                        $('#btn-next').attr('disabled', true);
                    }

                } else {
                    if(i > 1){
                        $('#li-prev-caja').removeClass('disabled');
                        $('#btn-prev-caja').attr('disabled', false);
                    }

                    current = i;
                    $('#step-'+(i-1)+'-caja').hide();
                    $('#step-'+i+'-caja').show();
                    setProgressBar(current);

                    $('#li-'+(i-1)+'-caja').removeClass('active');
                    $('#li-'+i+'-caja').removeClass('disabled');
                    $('#li-'+i+'-caja').addClass('active');
                    $('#btn-'+i+'-caja').attr('disabled', false);

                    if(i == 7){
                        getVistaPrevia();
                        $('#li-next-caja').addClass('disabled');
                        $('#btn-next-caja').attr('disabled', true);
                    }
                }
            }
        }
    });
}

//---------------------------------------------------EVIDENCIAS
function tablaIMG() {
    var caja = "";
    if($('#tipo_unidad').val() == '2'){
        caja = "-caja";
    }
    var idorden = $('#cve_orden').val();
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "tablaimg", idorden:idorden},
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

                $("#img-table"+caja).html(p2);
            }
            cargandoHide();
        }
    });
}

function tablaVid() {
    var caja = "";
    if($('#tipo_unidad').val() == '2'){
        caja = "-caja";
    }
    var idorden = $('#cve_orden').val();
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "tablavid", idorden: idorden},
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
                $("#vid-table"+caja).html(p2);
            }
            cargandoHide();
        }
    });
}

function getFileTMPOtras(){
    cargandoShow();
    var idorden = $('#cve_orden').val();
    $.ajax({
        data : {transaccion: "getfilestmpotras", idorden: idorden},
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "POST",
        success : function(datos){
            if (datos != "") {
                var div_id = datos.split("</tr>");
                for (var i = 0; i < div_id.length; i++) {
                    if (div_id[div_id.length - 1] === "") {
                        div_id.pop();
                    }

                    var values = div_id[i].split("</b>");
                    if ($('#tipo_unidad').val() == '2') {
                        $('#' + values[0] + '-caja').val(values[2]);
                        $("#" + values[1] + '-caja').html("<a class='btn btn-success btn-xs' onclick=\"showImgOtras('" + values[2] + "','" + values[3] + "')\"><span class='fas fa-image'></span></a>");
                    } else {
                        $('#' + values[0]).val(values[2]);
                        $("#" + values[1]).html("<a class='btn btn-success btn-xs' onclick=\"showImgOtras('" + values[2] + "','" + values[3] + "')\"><span class='fas fa-image'></span></a>");
                    }
                }
            }
            cargandoHide();
        }
    })
}

function getFileTMPVid() {
    cargandoShow();
    var idorden = $('#cve_orden').val();
    $.ajax({
        data: { transaccion: "getfilestmpvid", idorden: idorden },
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        success: function (datos) {
            if (datos != "") {
                var div_id = datos.split("</tr>");
                for (var i = 0; i < div_id.length; i++) {
                    if (div_id[div_id.length - 1] === "") {
                        div_id.pop();
                    }
                    var values = div_id[i].split("</b>");
                    if ($('#tipo_unidad').val() == '2') {
                        $('#' + values[0] + '-caja').val(values[2]);
                        $("#" + values[1] + '-caja').html("<a class='btn btn-success btn-xs' onclick=\"showVid('" + values[2] + "')\"><span class='fas fa-video'></span></a>");
                    } else {
                        $('#' + values[0]).val(values[2]);
                        $("#" + values[1]).html("<a class='btn btn-success btn-xs' onclick=\"showVid('" + values[2] + "')\"><span class='fas fa-video'></span></a>");
                    }
                }
            }
            cargandoHide();
        }
    })
}

function saveStepControl(step){
    var cve_orden = $('#cve_orden').val();
    $.ajax({
        data: {transaccion: "saveStep", step: step, cve_orden: cve_orden},
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "post",
        success: function(datos){
        }
    });
}

function setProgressBar(curStep){
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    $(".progress-bar")
    .css("width",percent+"%")
    .html(percent+"%");   
}

function validaCheck(check, paso, id){
    if($('#'+id).is(':checked')){
        alertify.error("Sube la evidencia marcada.");
        $('#evidencia'+check+'-'+paso).click();
    } else {
        var nombre = $('#filech'+check+'-'+paso).val();
        $.ajax({
            data: {transaccion: "getDelTmpImg", nombre: nombre},
            url: 'com.sine.enlace/enlaceinstalacion.php',
            type: "POST",
            success: function(datos){
                $('#filech'+check+'-'+paso).val('0');
                $('#img-evidencia'+check+'-'+paso).html("");
            }
        })
    }
}

function quitacheck(id){
    if($('#'+id).is(':checked')){
        $('#'+id).prop('checked', false);
    }
}

function saveTMP(p = 0){
    var danho = [];
    $.each($("input[name='danhos-vehiculo']:checked"), function () {
        danho.push($(this).val());
    });
    var iddanhos = danho.join("-");

    var moldura = [];
    $.each($("input[name='molduras-vehiculo']:checked"), function () {
        moldura.push($(this).val());
    });
    var idmolduras = moldura.join("-");

    var tablero = [];
    $.each($("input[name='tablero-vehiculo']:checked"), function () {
        tablero.push($(this).val());
    });
    var idtablero = tablero.join("-");

    var cableado = [];
    $.each($("input[name='cableado-vehiculo']:checked"), function () {
        cableado.push($(this).val());
    });
    var idcableado = cableado.join("-");
    var otroscableado = $('#otros-cableado').val();

    var ccorriente = [];
    $.each($("input[name='tcorriente-vehiculo']:checked"), function () {
        ccorriente.push($(this).val());
    });
    var idccorriente = ccorriente.join("-");

    var accesorio = [];
    $.each($("input[name='accesorio-gps-step']:checked"), function () {
        accesorio.push($(this).val());
    });
    var idaccesorio = accesorio.join("-");

    var observaciones = $("#observaciones-step").val() || "";
    var txtbd = observaciones.replace(new RegExp("\n", 'g'), '<corte>');

    var instalacion = [];
    $.each($("input[name='instalacion-list']:checked"), function () {
        instalacion.push($(this).val());
    });
    var idinstalacion = instalacion.join("-");

    var firma;
    var canvas;
    if(p == 123){
        if($('#tipo_unidad').val() == '2'){
            canvas = document.getElementById('firma-canvas-caja');
            firma = canvas.toDataURL();
            $("#firma-actual-caja").val(firma);
            $("#div-firma-caja").html("<label class='control-label text-right'>Firma actual</label><img src='" + firma + "' width='200px' id='imgfirma'>");
        } else {
            canvas = document.getElementById('firma-canvas');
            firma = canvas.toDataURL();
            $("#firma-actual").val(firma);
            $("#div-firma").html("<label class='control-label text-right'>Firma actual</label><img src='" + firma + "' width='200px' id='imgfirma'>");
        }
    }else {
        if($('#tipo_unidad').val() == '2'){
            firma = $("#firma-actual-caja").val();
        } else {
            firma = $("#firma-actual").val();   
        }
    }

    var imgfrente = $("#frentepic").val();
    var imgvin = $("#vinpic").val();
    var imgtabinicial = $("#tabipic").val();
    var imgtabfinal = $("#tabfpic").val();
    var imgantesins = $('#tabbefore').val();
    var imgdespuesins = $('#tabafter').val();

    var tipounidad = $("#tipo_unidad").val();
    var observaciongral = $("#observaciones-gral").val();
    var descUbicacion = $("#descUbicacion").val();
    var tipocorte = $('#tipo-corte').val();

    if (tipounidad == 2) {
        imgtabinicial = "noimage";
        imgfrente = $("#frentepic-caja").val();
        imgvin = $("#vinpic-caja").val();
        imgantesins = $('#tabbefore-caja').val();

        var cableado_caja = [];
        $.each($("input[name='cableado-vehiculo-caja']:checked"), function () {
            cableado_caja.push($(this).val());
        });
        idcableado = cableado_caja.join("-");
        otroscableado = $('#otros-cableado-caja').val();

        gpsvehiculo = $("#modelo-gps-caja").val();
        imei = $("#imei-gps-caja").val();
        numtelefono = $("#num-telefono-caja").val();
        idinstalador = $("#nombre-instalador-caja").val();

        var accesorio_caja = [];
        $.each($("input[name='accesorio-gps-caja']:checked"), function () {
            accesorio_caja.push($(this).val());
        });
        idaccesorio = accesorio_caja.join("-");

        imgdespuesins = $('#tabafter-caja').val();

        var instalacion_caja = [];
        $.each($("input[name='instalacion-list-caja']:checked"), function () {
            instalacion_caja.push($(this).val());
        });
        idinstalacion = instalacion_caja.join("-");

        descUbicacion = "";

        observaciones = $("#observaciones-caja").val();
        txtbd = observaciones.replace(new RegExp("\n", 'g'), '<corte>');

        observaciongral = $("#observaciones-gral-caja").val();
    }
    

    var encargado = $("#encargado-cliente").val() || "";
    
    var par = {
        transaccion : "saveTMP", 
        iddanhos : iddanhos,
        idmolduras : idmolduras,
        otrosmolduras : $("#otros-molduras").val(),
        idtablero : idtablero,
        otrostablero : $("#otros-tablero").val(),
        idcableado : idcableado,
        otroscableado : otroscableado,
        idccorriente : idccorriente,
        otroscorriente : $("#otros-ccorriente").val(),
        idaccesorio : idaccesorio,
        observaciones : txtbd,
        idinstalacion : idinstalacion,
        encargado : encargado,
        firma : firma,
        imgfrentevehiculo : imgfrente,
        imgnserie : imgvin,
        imgtabinicial : imgtabinicial,
        imgtabfinal : imgtabfinal,
        imgantesinst : imgantesins,
        imgdespuesinst : imgdespuesins,
        idorden : $('#cve_orden').val(),
        observaciongral: observaciongral,
        descUbicacion: descUbicacion,
        tipocorte: tipocorte
    };

    $.ajax({
        data : par,
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "POST",
        dataType : "JSON",
        success  : function(res){
            if(p==123){
                alertify.success("Firma guardada correctamente.");
            }
        }
    })
}

function verifyTipoCorte(){
    if($('#chacc4-step').is(':checked')){
        $('#container-tipo-corte').collapse('show');
    } else {
        $('#container-tipo-corte').collapse('hide');
        $('#tipo-corte').val("");
    }
    saveTMP();
}

function verificaCheckBtnPanic(){
    var id = "chins10";
    if($('#tipo_unidad').val() == '2'){
        id = "chins10-caja";
    }    

    if($('#'+id).is(':checked')){
        if($('#tipo_unidad').val() == '2'){
            $('#complementoBtnPanico-caja').collapse('show');
        } else {
            $('#complementoBtnPanico').collapse('show');
        }
    } else {
        if($('#tipo_unidad').val() == '2'){
            $('#complementoBtnPanico-caja').collapse('hide');
            $('#descUbicacion-caja').val("");
            $('#img-evidencia11-10-caja').html("");
            $('#img-evidencia12-10-caja').html("");

        } else {
            $('#complementoBtnPanico').collapse('hide');
            $('#descUbicacion').val("");
            $('#img-evidencia11-10').html("");
            $('#img-evidencia12-10').html("");
        }
        
        eliminarImgsPanic();
    }
    window.setTimeout(function(){saveTMP();},500);
}

function eliminarImgsPanic(){
    var idorden = $('#cve_orden').val();
    $.ajax({
        data : {transaccion: "deleteImgsPanic", idorden: idorden},
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "post",
        success : function(datos){
        }
    });
}

function quitaseleccion(name, id){
    if($('#'+id).is(':checked')){
        $.each($("input[name='"+name+"']:checked"), function () {
            $(this).click();
        });
        $('#'+id).prop('checked', true);

        switch(id){
            case 'chmol3':
                $('#otros-molduras').val("");
                break;
            case 'chtab5':
                $('#otros-tablero').val("");
                break;
            case 'chcab4':
                $('#otros-cableado').val("");
                break;
            case 'chtcor4':
                $('#otros-ccorriente').val("");
                break;
        }
    }
    saveTMP();
}

function cargarImgInstalacion(check = 0, paso = 0, id = "imagen") {
    var caja = "";
    if($('#tipo_unidad').val() == '2'){
        caja = "-caja";
    }

    cargandoShow();
    var formData = new FormData();
    var imagen = $('#'+id+caja)[0].files[0];
    var idorden = $('#cve_orden').val();

    var titulo = "";
    if(paso == 0 && check == 0){
        titulo = $('#titulo-img').val();
    }

    formData.append("imagen", imagen);
    formData.append("paso", paso);
    formData.append("check", check);
    formData.append("idorden", idorden);
    formData.append("accion", "otras");
    formData.append("titulo", titulo);
    $.ajax({
        url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            if(paso == 0 && check == 0){
                $('#titulo-foto').modal('hide');
            }
            var array = datos.split("</tr>");

            tablaIMG();
            let el = $('#evidencia'+check+'-'+paso+caja);
            el.wrap('<form>').closest('form').get(0).reset();
            el.unwrap();

            //$('#filech'+check+'-'+paso+caja).val(array[0]);
            $("#img-"+id+caja).html("<a class='btn btn-success btn-xs' onclick=\"showImgOtras('"+array[0]+"','"+array[1]+"')\"><span class='fas fa-image'></span></a>");
            cargandoHide();
        }
    });
}

function cargarImgFrente() {
    var img = ""; 
    var id="";
    var formData = new FormData();
    if($('#tipo_unidad').val() == '2'){
        img = $("#frenteimg-caja").val();
        id = 'frenteimg-caja';
    } else {
        img = $("#frenteimg").val();
        id = 'frenteimg';
    }

    var idorden = $('#cve_orden').val();
    var imagen = $('#'+id)[0].files[0];
    formData.append("imagen", imagen);
    formData.append("idorden", idorden);
    formData.append("accion", "frente");
    if (isnEmpty(img, id)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var array = texto.split("</tr>");
                if($('#tipo_unidad').val() == '2'){
                    $("#frentepic-caja-errors").text("");
                    $("#label-frenteimg-caja").css("border-color", "green");
                    $("#frentepic-caja").val(array[0]);
                    $("#label-frenteimg-caja").css("border-color", "green");
                    $("#frenteactualizar-caja").val(array[0]);
                    $("#imgfrente-caja").html("<a class='btn btn-success btn-xs col-11 me-2' onclick=\"showimg('1', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgfrentevehiculo', 'imgfrentebase', 'imgfrente-caja', 'label-frenteimg-caja', 'frentepic-caja', 'frenteactualizar-caja')\"><span class='fas fa-times'></span></a>");
                } else {                  
                    $("#frentepic-errors").text("");  
                    $("#label-frenteimg").css("border-color", "green");
                    $("#frentepic").val(array[0]);
                    $("#label-frenteimg").css("border-color", "green");
                    $("#frenteactualizar").val(array[0]);
                    $("#imgfrente").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('1', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgfrentevehiculo', 'imgfrentebase', 'imgfrente', 'label-frenteimg', 'frentepic', 'frenteactualizar')\"><span class='fas fa-times'></span></a>");
                }
                saveTMP();
                cargandoHide();
            }
        });
    }
}

function cargarImgVin() {
    var img = ""; 
    var id="";
    var formData = new FormData();
    if($('#tipo_unidad').val() == '2'){
        img = $("#vinimg-caja").val();
        id = 'vinimg-caja';
    } else {
        img = $("#vinimg").val();
        id = 'vinimg';
    }

    var idorden = $('#cve_orden').val();
    var imagen = $('#'+id)[0].files[0];
    formData.append("imagen", imagen);
    formData.append("idorden", idorden);
    formData.append("accion", "serieovin");
    if (isnEmpty(img, id)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var array = texto.split("</tr>");
                if($('#tipo_unidad').val() == '2'){
                    $("#vinimg-caja-errors").text("");
                    $("#label-vinimg-caja").css("border-color", "green");
                    $("#vinpic-caja").val(array[0]);
                    $("#vinpicactualizar-caja").val(array[0]);
                    $("#imgvin-caja").html("<a class='btn btn-success btn-xs col-11 me-2' onclick=\"showimg('2', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgnserie', 'imgseriebase', 'imgvin-caja', 'label-vinimg-caja', 'vinpic-caja', 'vinpicactualizar-caja')\"><span class='fas fa-times'></span></a>");
                } else {
                    $("#vinimg-errors").text("");
                    $("#label-vinimg").css("border-color", "green");
                    $("#vinpic").val(array[0]);
                    $("#vinpicactualizar").val(array[0]);
                    $("#imgvin").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('2', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgnserie', 'imgseriebase', 'imgvin', 'label-vinimg', 'vinpic', 'vinpicactualizar')\"><span class='fas fa-times'></span></a>");
                }
                saveTMP();
                cargandoHide();
            }
        });
    }
}

function cargarImgTableroInicial() {
    var formData = new FormData();
    var imagen = $('#tabiimg')[0].files[0];
    var idorden = $('#cve_orden').val();

    formData.append("imagen", imagen);
    formData.append("idorden", idorden);
    formData.append("accion", "tableroini");
    var img = $("#tabiimg").val();
    if (isnEmpty(img, 'tabiimg')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var array = texto.split("</tr>");
                if (texto != '') {
                    $("#label-tabiimg").css("border-color", "green");
                    $("#tabipic").val(array[0]);
                    $("#tabipicactualizar").val(array[0]);
                    $("#imgtabi").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('3', '"+array[0]+"', '"+array[1]+"')\">" + array[0]+ " <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgtabinicial', 'imgtabinibase', 'imgtabi', 'label-tabiimg')\"><span class='fas fa-times'></span></a>");
                    saveTMP();
                    cargandoHide();
                }
            }
        });
    }
}

function cargarImgAntesInst() {
    var img = "";
    var id = "";

    if($('#tipo_unidad').val() == '2'){
        img = $("#beforeIns-caja").val();
        id = 'beforeIns-caja';
    } else {
        img = $("#beforeIns").val();
        id = 'beforeIns';
    }

    var formData = new FormData();
    var imagen = $('#'+id)[0].files[0];
    var idorden = $('#cve_orden').val();

    formData.append("imagen", imagen);
    formData.append("idorden", idorden);
    formData.append("accion", "cableadoini");
    if (isnEmpty(img, id)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var array = texto.split("</tr>");
                if ($('#tipo_unidad').val() == '2') {
                    $("#beforeIns-caja-errors").text("");
                    $("#label-beforeIns-caja").css("border-color", "green");
                    $("#tabbefore-caja").val(array[0]);
                    $("#tabbeforeactualizar-caja").val(array[0]);
                    $("#imgbeforeIns-caja").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('5', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgantesinst', 'imgantesbase', 'imgbeforeIns-caja', 'label-beforeIns-caja', 'tabbefore-caja', 'tabbeforeactualizar-caja')\"><span class='fas fa-times'></span></a>");
                } else {
                    $("#beforeIns-errors").text("");
                    $("#label-beforeIns").css("border-color", "green");
                    $("#tabbefore").val(array[0]);
                    $("#tabbeforeactualizar").val(array[0]);
                    $("#imgbeforeIns").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('5', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgantesinst', 'imgantesbase', 'imgbeforeIns', 'label-beforeIns', 'tabbefore', 'tabbeforeactualizar')\"><span class='fas fa-times'></span></a>");
                }
                saveTMP();
                cargandoHide();

            }
        });
    }
}

function cargarImgDespuesInst() {
    var img = "";
    var id = "";

    if($('#tipo_unidad').val() == '2'){
        img = $("#afterIns-caja").val();
        id = 'afterIns-caja';
    } else {
        img = $("#afterIns").val()
        id = 'afterIns';
    }

    var formData = new FormData();
    var imagen = $('#'+id)[0].files[0];
    var idorden = $('#cve_orden').val();
    formData.append("imagen", imagen);
    formData.append("idorden", idorden);
    formData.append("accion", "cableadofin");

    if (isnEmpty(img, id)) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var array = texto.split("</tr>");
                if ($('#tipo_unidad').val() == '2') {
                    $("#afterIns-caja-errors").text("");
                    $("#label-afterIns-caja").css("border-color", "green");
                    $("#tabafter-caja").val(array[0]);
                    $("#tabafteractualizar-caja").val(array[0]);
                    $("#imgafterIns-caja").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('6', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgdespuesinst', 'imgdespuesbase', 'imgafterIns-caja', 'label-afterIns-caja', 'tabafter-caja', 'tabafteractualizar-caja')\"><span class='fas fa-times'></span></a>");
                } else {
                    $("#afterIns-errors").text("");
                    $("#label-afterIns").css("border-color", "green");
                    $("#tabafter").val(array[0]);
                    $("#tabafteractualizar").val(array[0]);
                    $("#imgafterIns").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('6', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgdespuesinst', 'imgdespuesbase', 'imgafterIns', 'label-afterIns', 'tabafter', 'tabafteractualizar')\"><span class='fas fa-times'></span></a>");
                }
                saveTMP();
                cargandoHide();
            }
        });
    }
}

function cargarImgTableroFinal() {
    var formData = new FormData();
    var imagen = $('#tabfimg')[0].files[0];
    var idorden = $('#cve_orden').val();
    formData.append("imagen", imagen);
    formData.append("idorden", idorden);
    formData.append("accion", "tablerofin");

    var img = $("#tabfimg").val();
    if (isnEmpty(img, 'tabfimg')) {
        cargandoHide();
        cargandoShow();
        $.ajax({
            url: 'com.sine.enlace/cargarevidenciasinstalacion.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
                var texto = datos.toString();
                var array = texto.split("</tr>");
                $("#label-tabfimg").css("border-color", "green");
                $("#tabfpic").val(array[0]);
                $("#tabfpicactualizar").val(array[0]);
                $("#imgtabf").html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showimg('4', '"+array[0]+"', '"+array[1]+"')\">"+array[0]+" <span class='fas fa-image'></span></a>" + "<a class='btn btn-outline-danger btn-xs' onclick=\"eliminarEvidencia("+idorden+", 'imgtabfinal', 'imgtabfinbase', 'imgtabf', 'label-tabfimg', 'tabfpic', 'tabfpicactualizar')\"><span class='fas fa-times'></span></a>");
                saveTMP();
                cargandoHide();
                
            }
        });
    }
}

function showModalTitIMG(){
    $('#titulo-img').val("");
    $('#titulo-foto').modal('show');
}

function showImgOtras(nombre, img) {
    $('#verIMGbody').html("<img class='col-12' style='max-height: 500px;' src="+img+">");
    changeText("#titulo-imagen", nombre);
    $('#verIMG').modal('show');
}

function showimg(id="", nombre, imagen) {
    $('#verIMGbody').html("<img class='col-12' style='max-height: 500px;' src="+imagen+">");
    changeText("#titulo-imagen", nombre);
    $('#verIMG').modal('show');
}

function getVistaPrevia(){
    cargandoShow();
    var tipo_unidad = 1;
    var idorden = $('#cve_orden').val();
    if($('#tipo_unidad').val() == '2'){
        tipo_unidad = 2;
    }

    $.ajax({
        data: {transaccion: "vistaPrevia", idorden: idorden, tipo_unidad: tipo_unidad},
        url : "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        success: function(datos){
            if(tipo_unidad == 2){
                $('#resume-caja').html(datos);
            } else {
                $('#resume').html(datos);
            }
            cargandoHide();
        }
    })
}

function subirImgTitulo(){
    var caja = "";
    if($('#tipo_unidad').val() == '2'){
        caja = "-caja";
    }

    if(isnEmpty($('#titulo-img').val(), 'titulo-img')){    
        $('#imagen'+caja).click();
    }
}

function eliminarIMG(idtmp) {
    alertify.confirm("¿Estás seguro que deseas eliminar esta imagen?", function () {
        $.ajax({
            url: "com.sine.enlace/enlaceinstalacion.php",
            type: "POST",
            data: {transaccion: "eliminarimg", idtmp: idtmp},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Imagen eliminada correctamente.');
                }
                tablaIMG();
            }
        });
    }).set({title: "Q-ik"});
}

function validaCheckVideo(check, paso, id){
    var caja = "";
    if($('#tipo_unidad').val() == '2'){
        caja = "-caja";
    }

    if($('#'+id).is(':checked')){
        alertify.error("Sube la evidencia marcada");
        $('#evidencia'+check+'-'+paso+caja).click();
    } else {
        var nombre = $('#filech'+check+'-'+paso+caja).val();
        $.ajax({
            data: {transaccion: "getDelTmpVid", nombre: nombre},
            url: 'com.sine.enlace/enlaceinstalacion.php',
            type: "POST",
            success: function(datos){
                $('#filech'+check+'-'+paso+caja).val('0');
                $('#img-evidencia'+check+'-'+paso+caja).html("");
            }
        })
    }
}

function eliminarEvidencia(idorden, name, base, div, label, input, actualizar){
    alertify.confirm("¿Estás seguro que deseas eliminar esta imagen?", function () {
        $.ajax({
            url: "com.sine.enlace/enlaceinstalacion.php",
            type: "POST",
            data: {transaccion: "eliminarevidencia", idorden: idorden, name:name, base:base},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Imagen eliminada correctamente.');
                    $("#"+div).html("");
                    $("#"+input).val("");
                    $("#"+actualizar).val("");
                    $("#"+label).css("border-color", "");
                }
                tablaIMG();
            }
        });
    }).set({title: "Q-ik"});
}

function cargarVidInstalacion(check = 0, paso = 0, id = 'video') {
    var caja = "";
    if($('#tipo_unidad').val() == '2'){
        caja = "-caja";
    }

    cargandoShow();
    var formData = new FormData();
    var video = $('#'+id+caja)[0].files[0];
    var idorden = $('#cve_orden').val();
    var vidname = $('#vidname'+caja).val();
    var paso  = paso;
    var check = check;
    
    formData.append("video", video);
    formData.append("idorden", idorden);
    formData.append("vidname", vidname);
    formData.append("paso", paso);
    formData.append("check", check);
    
    $.ajax({
        url: 'com.sine.enlace/cargarvideos.php',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == 'X') {
                alertify.error(res);
                $("#vid-errors").css("color", "red");
                cargandoHide();
            } else {
                $("#vid-errors").html('');
                $("#filename"+caja).val(datos);

                $('#filech'+check+'-'+paso+caja).val(datos);
                $("#img-"+id).html("<a class='btn btn-success btn-xs col-9 me-2' onclick=\"showVid('"+datos+"')\"><span class='fas fa-video'></span></a>");
                tablaVid();
                cargandoHide();
            }

            let el = $('#filech'+check+'-'+paso+caja);
            el.wrap('<form>').closest('form').get(0).reset();
            el.unwrap();

        },
        error: function (jqXHR, textStatus, errorThrown) {

            if (jqXHR.status === 0) {
                alertify.error('No conectado: Verifica la red.');
                cargandoHide();
            } else if (jqXHR.status == 404) {
                alertify.error('Página solicitada no encontrada [404]');
                cargandoHide();
            } else if (jqXHR.status == 500) {
                alertify.error('Error interno del servidor [500].');
                cargandoHide();
            } else if (textStatus === 'parsererror') {
                alertify.error('Error al analizar el JSON solicitado.');
                cargandoHide();
            } else if (textStatus === 'timeout') {
                alertify.error('Error de tiempo de espera.');
                cargandoHide();
            } else if (textStatus === 'abort') {
                alertify.error('Solicitud AJAX abortada.');
                cargandoHide();
            } else {
                alertify.error('Error no capturado: ' + jqXHR.responseText);
                cargandoHide();
            }
        }
    });
}

function showVid(video, name){
    var html = '<video src="temporal/tmpvideo/'+video+'" autoplay="true" muted="true" loop="true" controls="false" width="100%" style="max-height: 500px"></video>';
    $('#verVIDbody').html(html);
    changeText("#titulo-alerta-video", name);
    $('#verVID').modal('show');
}

function eliminarVid(idtmp) {
    alertify.confirm("¿Estás seguro que deseas eliminar este video?", function () {
        $.ajax({
            url: "com.sine.enlace/enlaceinstalacion.php",
            type: "POST",
            data: {transaccion: "eliminarvid", idtmp: idtmp},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(1, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success('Video eliminado correctamente.')
                }
                tablaVid();
            }
        });
    }).set({title: "Q-ik"});
}

function finalizarOrden(){
    var valida = 0;
    var tipo_unidad = 1;
    if($('#tipo_unidad').val() == '2'){
        tipo_unidad = 2;
    }

    if(tipo_unidad == 1){
        if(!validaStepIMG(1)){ valida++;  } 
        else if(!validaStepIMG(2)){ valida++; }
        else if(!validaStepIMG(3)){ valida++; }
        else if(!validaStepIMG(4)){ valida++; }
        else if(!validaStepIMG(5)){ valida++;  }
        else if(!validaStepIMG(6)){ valida++;}
        else if(!validaStepIMG(7)){ valida++;  }
        else if(!validaStepIMG(8)){ valida++; }
        else if(!validaStepIMG(9)){ valida++;  }
        else if(!validaStepIMG(10)){ valida++;  }
        else if(!validaStepIMG(11)){ valida++;  }
    }
    else if (tipo_unidad == 2){
        if(!validaStepIMGCaja(1)){ valida++;  } 
        else if(!validaStepIMGCaja(2)){ valida++; }
        else if(!validaStepIMGCaja(3)){ valida++;  }
        else if(!validaStepIMGCaja(4)){ valida++; }
        else if(!validaStepIMGCaja(5)){ valida++; }
        else if(!validaStepIMGCaja(6)){ valida++; }
        else if(!validaStepIMGCaja(7)){ valida++; }
    }

    if(valida == 0) {
        alertify.confirm("¿Estás seguro de finalizar la orden? <br> Una vez hecho esto no la podrás editar nuevamente.", function () {
            cargandoShow();
            var idorden = $('#cve_orden').val();
            $.ajax({
                data: {transaccion: "finalizarOrden", idorden: idorden},
                url : "com.sine.enlace/enlaceinstalacion.php",
                type: "POST",
                success: function(datos){
                    cargandoHide();
                    if(datos == 1){
                        loadView('listainstalacion');
                        alertify.success('Instalación finalizada correctamente.')
                    }
                }
            });
        }).set({title: "Q-ik"});
    }    
}

function verificaVideoTMP(paso, check){
    $.ajax({
        data : {transaccion: "verifyVideoTMP", paso: paso, check: check, idorden: $('#cve_orden').val()},
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "POST",
        success : function(res){
            $('#filech'+check+'-'+paso).val(res);
            $('#hidpaso').val(paso);
            $('#hidcheck').val(check);
        }
    })
}

function videosServicio(folio){
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "getvidregistrados", folio: folio},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                $("#reg-vid").html(datos);
            }
        }
    });
}

function copiarInstalacion(idinstalacion) {
    cargandoHide();
    cargandoShow();
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "editarinstalacion", idinstalacion: idinstalacion, gentmp: 0},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
                cargandoHide();
            } else {
                loadView('instalacion');
                window.setTimeout("setValoresCopiarInstalacion('" + datos + "')", 700);
            }
        }
    });
}

function setValoresCopiarInstalacion(datos) {
    var array = datos.split("</tr>");
    var nombrecliente = array[3];
    var marca = array[4];
    var modelo = array[5];
    var anho = array[6];
    var color = array[7];
    var serie = array[8];
    var numeco = array[9];
    var km = array[10];
    var placas = array[11];
    var gpsvehiculo = array[12];
    var imei = array[13];
    var numtelefono = array[14];
    var idinstalador = array[15];
    var idaccesorio = array[16];
    var instalador = array[18];
    var idcliente = array[22];
    var modeloanterior = array[23];
    var imeianterior = array[24];
    var simaanterior = array[25];
    var plataforma = array[26];
    var tipounidad = array[27];
    var idasignacion = array[28];
    var idtservicio = array [29];
    var otrosservicios = array[30];

    $("#id-cliente").val(idcliente);
    $("#nombre-cliente").val(nombrecliente);
    $("#plataforma").val(plataforma);
    $("#tipo" + tipounidad).prop('checked', true);
    $("#marca-unidad").val(marca);
    $("#modelo-unidad").val(modelo);
    $("#anho-unidad").val(anho);
    $("#color-unidad").val(color);
    $("#serie-unidad").val(serie);
    $("#num-economico").val(numeco);
    $("#km-unidad").val(km);
    $("#placas-unidad").val(placas);

    var servicio = idtservicio.split("-");
    for (var i = 0, max = servicio.length; i < max; i++) {
        $("#chserv" + servicio[i]).prop('checked', true);
        if (servicio[i] == 8) {
            $("#div-imei").show('slow');
        } else if (servicio[i] == 9) {
            $("#div-sim").show('slow');
        }
    }
    if (modeloanterior != "NA") {
        $("#modelo-anterior").val(modeloanterior);
    }
    if (imeianterior != "NA") {
        $("#imei-anterior").val(imeianterior);
    }

    if (simaanterior != '0') {
        $("#tel-anterior").val(simaanterior);
    }
    $("#otros-tservicio").val(otrosservicios);

    $("#modelo-gps").val(gpsvehiculo);
    $("#imei-gps").val(imei);
    $("#num-telefono").val(numtelefono);
    $("#option-default-nombre-instalador").val(idinstalador);
    $("#option-default-nombre-instalador").text(instalador);

    var accesorio = idaccesorio.split("-");
    for (var i = 0, max = accesorio.length; i < max; i++) {
        $("#chacc" + accesorio[i]).prop('checked', true);
    }

    window.setTimeout(function(){ 
        var asignacion = idasignacion.split("-");
        for (var i = 0, max = asignacion.length; i < max; i++) {
            $("#chInstalador" + asignacion[i]).prop('checked', true);
        }
    },500);
    cargandoHide();
}

function showCorreosIns(idorden) {
    cargandoHide();
    cargandoShow();
    $("#idinstalacionenvio").val(idorden);
    getCorreos(idorden);
    $("#chcorreoinstalacion1").prop('checked', false);
    $("#chcorreoinstalacion2").prop('checked', true);
    $("#chcorreoinstalacion3").prop('checked', false);
    $("#chcorreoinstalacion4").prop('checked', false);
    cargandoHide();
}

function getCorreos(idorden) {
    $.ajax({
        url: "com.sine.enlace/enlaceinstalacion.php",
        type: "POST",
        data: {transaccion: "getcorreos", idorden: idorden},
        success: function (datos) {
            var texto = datos.toString();
            var bandera = texto.substring(0, 1);
            var res = texto.substring(1, 1000);
            if (bandera == '0') {
                alertify.error(res);
            } else {
                var array = datos.split("<corte>");
                var correo1 = array[0];
                var correo2 = array[1];
                var correo3 = array[2];
                $("#correoinstalacion1").val(correo1);
                $("#correoinstalacion2").val(correo2);
                $("#correoinstalacion3").val(correo3);

            }
        }
    });
}

function enviarInstalacion() {
    cargandoHide();
    cargandoShow();
    var idorden = $("#idinstalacionenvio").val();
    var chcorreo1 = 0;
    var chcorreo2 = 0;
    var chcorreo3 = 0;
    var chcorreo4 = 0;
    var correoalt = "ejemplo@ejemplo.com";
    if ($("#chcorreoinstalacion1").prop('checked')) {
        chcorreo1 = 1;
    }
    if ($("#chcorreoinstalacion2").prop('checked')) {
        chcorreo2 = 1;
    }
    if ($("#chcorreoinstalacion3").prop('checked')) {
        chcorreo3 = 1;
    }
    if ($("#chcorreoinstalacion4").prop('checked')) {
        chcorreo4 = 1;
    }
    if (chcorreo1 == 0 && chcorreo2 == 0 && chcorreo3 == 0 && chcorreo4 == 0) {
        alertify.error('Debes seleccionar por lo menos un correo eletrónico para el envio.');
        cargandoHide();
    } else {
        if(chcorreo4 == 1){
            correoalt = $("#correoinstalacion4").val();
        }
        $.ajax({
            url: "com.sine.imprimir/hojaservicio.php",
            type: "POST",
            data: {transaccion: "pdf", id: idorden, ch1:chcorreo1, ch2:chcorreo2, ch3:chcorreo3, ch4:chcorreo4, correoalt:correoalt},
            success: function (datos) {
                var texto = datos.toString();
                var bandera = texto.substring(0, 1);
                var res = texto.substring(0, 1000);
                if (bandera == '0') {
                    alertify.error(res);
                } else {
                    alertify.success(datos);
                }
                cargandoHide();
            }
        });
    }
}

function hojaServicio(id) {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/hojaservicio.php?s=' + id, 'Hoja de Servicio', '', '1024', '768', 'true');
    cargandoHide();
}

function displayVid(video) {
    $('#reproducir-video').html('');
    var rutaUrl = "http://localhost/NAGA021226FJ0/img/instalacion/"+video;
    var videoHTML = '<video src="'+rutaUrl+'" autoplay="true" muted="true" loop="true" controls="false" width="100%" style="max-height: 500px;"></video>';
    $.ajax({
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "POST",
        data : {transaccion: "existeVid", video: video},
        success: function(datos){
            if(datos == 1){
                $('#reproducir-video').html(videoHTML);
            } else {
                $('#reproducir-video').html('No existe el video: '+video+'');
            }   
        }
    });
}

function setIDInstalacion(id, encargado) {
    $("#encargado-firma").val(encargado);
    $("#idinstalacionfirma").val(id);
}

function actualizarFirma() {
    var encargado = $("#encargado-firma").val();
    var idorden = $("#idinstalacionfirma").val();
    var canvas = document.getElementById('firma-modal');
    if (isCanvasBlank(canvas, "firma-modal")) {
        alertify.confirm("Esta acción reemplazará la firma actual ¿Desea continuar?", function () {
            cargandoHide();
            cargandoShow();
            var firma = canvas.toDataURL();
            $.ajax({
                url: "com.sine.enlace/enlaceinstalacion.php",
                type: "POST",
                data: {transaccion: "guardarfirma", idorden:idorden, encargado:encargado, firma: firma},
                success: function (datos) {
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 1000);
                    if (bandera == '0') {
                        alertify.error(res);
                        cargandoHide();
                    } else {
                        $("#sign-modal").modal('hide');
                        alertify.success('Firma guardada correctamente.')
                        window.setTimeout("loadView('listainstalacion')",200);
                        cargandoHide();
                    }
                }
            });
        }).set({title: "Q-ik"});
    }
}

function generarBitacora() {
    var fechainicio = $("#date-inicio").val();
    var fechafin = $("#date-fin").val();
    var cliente = $("#cliente-search").val();
    var servicio = $("#search-servicio").val();
    if (isnEmpty(fechainicio, "date-inicio") && isnEmpty(fechafin, "date-fin")) {
        cargandoHide();
        cargandoShow();
        VentanaCentrada('./com.sine.imprimir/reportehojaservicio.php?i=' + fechainicio + '&&f=' + fechafin + '&&c=' + cliente + '&&s=' + servicio, 'Bitacora', '', '1024', '768', 'true');
    }
    cargandoHide();
}

function hojaenBlanco() {
    cargandoHide();
    cargandoShow();
    VentanaCentrada('./com.sine.imprimir/hojaservicio.php?b=1', 'Hoja de Servicio', '', '1024', '768', 'true');
    cargandoHide();
}

function cancelarInstalacion(id){
    $('#motivo-cancel').val("");
    var html = '<button type="button" class="btn button-file text-uppercase fw-semibold" onclick="confirmaCancel('+id+')">Aceptar <span class="fas fa-save"></span></button>';
    $('#cancelInst-footer').html(html);
    $('#cancelInst').modal('show');
}

function confirmaCancel(id){
    var motivo = $('#motivo-cancel').val();
    motivo = motivo.replaceAll('\n', '<ent>');
    if(isnEmpty(motivo, 'motivo-cancel')){
        alertify.confirm("¿Estás seguro que deseas cancelar esta instalación? <br>Una vez hecho esto no habrá forma de revertirse.", function () {
            cargandoShow();
            $.ajax({
                data : {transaccion: "cancelInst", id: id, motivo: motivo},
                url  : "com.sine.enlace/enlaceinstalacion.php",
                type : "post",
                success : function(datos){
                    var texto = datos.toString();
                    var bandera = texto.substring(0, 1);
                    var res = texto.substring(1, 1000);
                    if (bandera == '0') {
                        alertify.error(res);
                    } else {
                        alertify.success(res);
                    }
                    cargandoHide();
                    $('#cancelInst').modal('hide');
                    window.setTimeout(function(){ loadView('listainstalacion'); },500);
                }
            });
        }).set({title: "Q-ik"});
    }
}

function verCancelacion(id){
    cargandoShow();
    $.ajax({
        data : {transaccion: "showcancel", id: id},
        url  : "com.sine.enlace/enlaceinstalacion.php",
        type : "POST",
        dataType : "JSON",
        success  : function(datos){
            cargandoHide();
            var motivo = datos.motivo.replaceAll("<ent>", "<br>");

            var html = '<div class="row">'
                         + '<div class="col-12"><label><b class="text-primary-emphasis fw-semibold">Folio:</b></label> <span class="text-muted fw-semibold"> '+datos.letra_folio+datos.folio+'</span></div>'
                         + '<div class="col-12"><label><b class="text-primary-emphasis fw-semibold">No. Orden:</b></label> <span class="text-muted fw-semibold"> '+datos.idorden+'</span></div>'
                         + '<div class="col-12"><label><b class="text-primary-emphasis fw-semibold">Fecha y hora de cancelación:</b></label> <span class="text-muted fw-semibold"> '+datos.fecha+' a '+datos.hora+'</span></div>'
                         + '<div class="col-12"><label><b class="text-primary-emphasis fw-semibold">Persona canceló:</b></label> <span class="text-muted fw-semibold"> '+datos.nombre+'</span></div>'
                         + '<div class="col-12"><label><b class="text-primary-emphasis fw-semibold">Motivo:</b></label><br><span class="text-muted fw-semibold">'+motivo+'</span></div>'
                     + '</div>';
            $('#verCancelInst-body').html(html);
            $('#verCancelInst').modal('show');
        }
    });
}