<?php
require_once '../com.sine.controlador/ControladorInicio.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
        case 'getsaldo':
            $ci = new ControladorInicio();
            $insertado = $ci->getSaldo();
            if ($insertado != "") {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'copyfolder':
            $ci = new ControladorInicio();
            $src = "../../SineFacturacion";
            $dst = "../../Copia";
            $insertado = $ci->copyFolder($src, $dst);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'ini':
            $ci = new ControladorInicio();
            $crear = $ci->iniFile();
            echo $crear;
            break;
        case 'datosgrafica':
            $ci = new ControladorInicio();
            $fecha = getdate();
            $y = $fecha['year'];
            $insertado = $ci->getDatos($y);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'opcionesano':
            $cf = new ControladorInicio();
            $datos = $cf->opcionesAno();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay cartas porte asignadas a este permisionario";
            }
            break;
        case 'buscargrafica':
            $cf = new ControladorInicio();
            $y = $_POST['ano'];
            $datos = $cf->getDatos($y);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay cartas porte asignadas a este permisionario";
            }
            break;
        case 'valperiodo':
            $cf = new ControladorInicio();
            $datos = $cf->checkAcceso();
            if (!$datos) {
                echo "1Si";
            } else {
                echo "0Su periodo de prueba de 15 dias ha concluido, si deseas seguir usando Q-ik te invitamos a adquirir el paquete de timbres que mas se ajuste a tus necesidades para continuar con el servicio";
            }
            break;
        case 'sendmsg':
            $ci = new ControladorInicio();
            $insertado = $ci->sendMSG();
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'getnotification':
            $ci = new ControladorInicio();
            $id = $_POST['id'];
            $insertado = $ci->getNotification($id);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'updatenotification':
            $ci = new ControladorInicio();
            $id = $_POST['id'];
            $insertado = $ci->listNotificacion($id);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'filtrarnotificaciones':
            $pag = $_POST['pag'];
            $cs = new ControladorInicio();
            $datos = $cs->listaServiciosHistorial($pag);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
    	case 'getnombre':
            $cs = new ControladorInicio();
            $datos = $cs->getUsuarioLogin();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'sendsoporte':
            $cs = new ControladorInicio();
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $chwhats = $_POST['chwhats'];
            $correo = $_POST['correo'];
            $msg = $_POST['msg'];
            
            $send = $cs->sendMailSoporte($nombre, $telefono, $chwhats, $correo, $msg);
            echo $send;
            break;
        case 'firstsession':
            $cs = new ControladorInicio();
            
            $ft = $cs->firstSession();
            echo $ft;
            break;
        default:
            break;
    }
}