<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorImgs.php';

Session::start();
date_default_timezone_set("America/Mexico_City");
$ci = new ControladorImgs();
$carpeta = "../temporal/tmp/";

if (isset($_FILES["imagen"]) || isset($_FILES["img-evidencia"])) {
    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];
    
    $fileimg = isset($_FILES["imagen"]) ? $_FILES["imagen"] : $_FILES["img-evidencia"];
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
        $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension , $sessionid);
        move_uploaded_file($fileimg["tmp_name"], "../temporal/tmp/" . $tmpnombre);
        echo "$nombre";
        $vista = "temporal/tmp/" . $tmpnombre;
    } else {
        echo "Error: El archivo debe ser un archivo de Excel, Word u otro documento compatible.";
    }
} else {
    echo "Error: No se recibió ningún archivo.";
}
