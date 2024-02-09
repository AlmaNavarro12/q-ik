<?php

require_once '../com.sine.modelo/Configuracion.php';
require_once '../com.sine.modelo/Folios.php';
require_once '../com.sine.controlador/ControladorConfiguracion.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cc = new ControladorConfiguracion();

    switch ($transaccion) {
            //------------------------------------ FOLIO
        case 'insertarfolio':
            $insertado = $cc->valFolio(obtenerDatosFolio());
            break;
        case 'listafolios':
            $insertado = $cc->listaFolios($_POST['pag'], $_POST['REF'], $_POST['numreg']);
            break;
        case 'editarfolio':
            $insertado = $cc->getDatosFolio($_POST['idfolio']);
            break;
        case 'actualizarfolio':
            $f = obtenerDatosFolio();
            $f->setIdfolio($_POST['idfolio']);
            $f->setActualizarinicio($_POST['inicio']);
            $insertado = $cc->valFolioActualizar($f);
            break;
        case 'eliminarfolio':
            $insertado = $cc->eliminarFolio($_POST['idfolio']);
            break;
            //------------------------------------COMISION
        case 'datosusuario':
            $datos = $cc->datosUsuario($_POST['idusuario']);
            echo $datos != "" ? $datos : "0No hay clientes registrados.";
            break;
        case 'insertarcomision':
            $insertado = $cc->insertarComision(obtenerDatosComision());
            break;
        case 'actualizarcomision':
            $c = obtenerDatosComision();
            $c->setIdcomision($_POST['idcomision']);
            $insertado = $cc->actualizarComision($c);
            break;
        case 'quitarcomision':
            $insertado = $cc->quitarComision($_POST['idcomision']);
            break;
            //------------------------------------CORREO
        case 'loadmail':
            $datos = $cc->getMail($_POST['idcorreo']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'insertarcorreo':
            $insertado = $cc->nuevoCorreo(obtenerDatosCorreo());
            break;
        case 'actualizarcorreo':
            $c = obtenerDatosCorreo();
            $c->setIdCorreo($_POST['idcorreo']);
            $insertado = $cc->modificarCorreo($c);
            break;
        case 'editarbody':
            $datos = $cc->getMailBody($_POST['idbody']);
            echo $datos != "" ? $datos : "0Botón activo.";
            break;
        case 'actualizarbody':
            $insertado = $cc->actualizarBodyMail(obtenerDatosBodyCorreo());
            break;
        case 'testcorreo':
            $datos = $cc->mailPrueba(obtenerDatosTesteo());
            echo $datos != "" ? $datos : "0No hay clientes registrados.";
            break;
            //------------------------------------TABLAS
        case 'loadexcel':
            $fnm = $_POST['fnm'];
            $tabla = $_POST['tabla'];
            echo ($datos = $cc->importTable($fnm, $tabla)) != "" ? $datos : "0Error: El archivo no contiene los campos esperados. O bien, no contiene datos.";
            break;
    }

    if (isset($insertado)) {
        echo $insertado ? $insertado : "0Error: No se pudo realizar la operación.";
    }
}

function obtenerDatosFolio()
{
    $f = new Folio();
    $f->setSerie($_POST['serie']);
    $f->setLetra($_POST['letra']);
    $f->setNuminicio($_POST['folio']);
    $f->setUsofolio($_POST['usofolio']);
    return $f;
}

function obtenerDatosComision()
{
    $c = new Configuracion();
    $c->setIdUsuario($_POST['idusuario']);
    $c->setPorcentaje($_POST['porcentaje']);
    $c->setChCalculo($_POST['chcalculo']);
    $c->setChCom($_POST['chcom']);
    return $c;
}

function obtenerDatosCorreo()
{
    $c = new Configuracion();
    $c->setCorreoEnvio($_POST['correo']);
    $c->setPassCorreo($_POST['pass']);
    $c->setRemitente($_POST['remitente']);
    $c->setMailRemitente($_POST['mailremitente']);
    $c->setHostCorreo($_POST['host']);
    $c->setPuertoCorreo($_POST['puerto']);
    $c->setSeguridadCorreo($_POST['seguridad']);
    $c->setChUsoCorreo1($_POST['chuso1']);
    $c->setChUsoCorreo2($_POST['chuso2']);
    $c->setChUsoCorreo3($_POST['chuso3']);
    $c->setChUsoCorreo4($_POST['chuso4']);
    $c->setChUsoCorreo5($_POST['chuso5']);
    return $c;
}

function obtenerDatosTesteo() {
    $c = new Configuracion();
    $c->setCorreoEnvio($_POST['correo']);
    $c->setPassCorreo($_POST['pass']);
    $c->setRemitente($_POST['remitente']);
    $c->setMailRemitente($_POST['mailremitente']);
    $c->setHostCorreo($_POST['host']);
    $c->setPuertoCorreo($_POST['puerto']);
    $c->setSeguridadCorreo($_POST['seguridad']);
    return $c;
}

function obtenerDatosBodyCorreo()
{
    $c = new Configuracion();
    $c->setIdBodyMail($_POST['idbody']);
    $c->setAsuntoBody($_POST['asunto']);
    $c->setSaludoBody($_POST['saludo']);
    $c->setTxtBody($_POST['txtbd']);
    $c->setImgLogo($_POST['filenm']);
    $c->setImgActualizar($_POST['imgactualizar']);
    $c->setChLogo($_POST['chlogo']);
    return $c;
}
