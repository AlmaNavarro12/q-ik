<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorImgs.php';

Session::start();
date_default_timezone_set("America/Mexico_City");
$ci = new ControladorImgs();
$carpeta = "../temporal/tmp/";

// Verificar si se han enviado archivos y si al menos uno de ellos es del tipo permitido
if (!empty($_FILES)) {
    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];

    foreach ($_FILES as $fileimg) {
        $nombre = $fileimg["name"];
        $tipo = $fileimg["type"];

        if ($tipo == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || 
            $tipo == 'application/vnd.ms-excel' || 
            $tipo == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
            $tipo == 'application/msword') {
            
            $extension = pathinfo($nombre, PATHINFO_EXTENSION);
            $fecha = date('YmdHis');
            $ranstr = substr(str_shuffle('0123456789011121314151617181920'), 0, 5);
            $tmpnombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension;
            //$insertar = $ci->insertarImg($nombre, $tmpnombre, $extension , $sessionid);
            move_uploaded_file($fileimg["tmp_name"], "../temporal/tmp/" . $tmpnombre);
            $vista = "temporal/tmp/" . $tmpnombre;
            echo $nombre . '<corte>' .  $vista;
            
        } else {
            echo "Error: El archivo $nombre debe ser un archivo de Excel, Word u otro documento compatible.";
        }
    }
} elseif (empty($_FILES)) {
    echo "Error: No se recibió ningún archivo.";
} else {
    echo "Error: Ninguno de los archivos subidos es del tipo permitido.";
}
