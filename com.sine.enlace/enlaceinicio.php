<?php
require_once '../com.sine.controlador/ControladorInicio.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $ci = new ControladorInicio();

    switch ($transaccion) {
        case 'getsaldo':
            echo ($insertado = $ci->getSaldo()) ? $insertado : "0Error: no inserto el registro.";
            break;
        case 'copyfolder':
            $src = "../../SineFacturacion";
            $dst = "../../Copia";
            echo $ci->copyFolder($src, $dst) ? $insertado : "0Error: no inserto el registro.";
            break;
        case 'ini':
            echo $ci->iniFile();
            break;
        case 'datosgrafica':
            $y = getdate()['year'];
            echo ($insertado = $ci->getDatos($y)) ? $insertado : "0Error: no inserto el registro.";
            break;
        case 'opcionesano':
            echo ($datos = $ci->opcionesAno()) ? $datos : "0No hay cartas porte asignadas a este permisionario.";
            break;
        case 'buscargrafica':
            echo ($datos = $ci->getDatos($_POST['ano'])) ? $datos : "0No hay cartas porte asignadas a este permisionario.";
            break;
        case 'valperiodo':
            echo (!$ci->checkAcceso()) ? "1Si" : "0Su periodo de prueba de 15 días ha concluido. Si deseas seguir usando Q-ik, te invitamos a adquirir el paquete de timbres que más se ajuste a tus necesidades para continuar con el servicio";
            break;
        case 'sendmsg':
            echo ($insertado = $ci->sendMSG()) ? $insertado : "0Error: no inserto el registro.";
            break;
        case 'getnotification':
            echo ($insertado = $ci->getNotification($_POST['id'])) ? $insertado : "0Error: no inserto el registro.";
            break;
        case 'updatenotification':
            echo ($insertado = $ci->listNotificacion($_POST['id'])) ? $insertado : "0Error: no inserto el registro.";
            break;
        case 'filtrarnotificaciones':
            echo ($datos = $ci->listaServiciosHistorial($_POST['pag'])) ? $datos : "0Ha ocurrido un error.";
            break;
        case 'getnombre':
            echo ($datos = $ci->getUsuarioLogin()) ? $datos : "0Ha ocurrido un error.";
            break;
        case 'sendsoporte':
            echo $ci->sendMailSoporte(
                $_POST['nombre'],
                $_POST['telefono'],
                $_POST['chwhats'],
                $_POST['correo'],
                $_POST['msg']
            );
            break;
        case 'firstsession':
            echo $ci->firstSession();
            break;
        default:
            break;
    }
}