<?php
require_once '../com.sine.modelo/Session.php'; 
require_once '../com.sine.controlador/ControladorImgs.php'; 

Session::start(); 
date_default_timezone_set("America/Mexico_City"); 
$ci = new ControladorImgs(); 

$rutaPersonalizada = $_POST["ruta_personalizada"];
$rutaFile = '../' . $rutaPersonalizada;

if (!empty($_FILES)) { 
    $sessionid = session_id(); 
    $idusuario = $_SESSION[sha1("idusuario")]; 
    
    foreach ($_FILES as $file) { 
        $nombre = $file["name"]; 
        $tipo = $file["type"]; 

        if ($tipo == 'application/pdf') { 
            $extension = pathinfo($nombre, PATHINFO_EXTENSION); 
            $fecha = date('YmdHis'); 
            $ranstr = substr(str_shuffle('0123456789011121314151617181920'), 0, 5); 
            $tmpnombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension; 

            //$insertar = $ci->insertarImg($nombre, $tmpnombre, $extension , $sessionid); 
            move_uploaded_file($file["tmp_name"], $rutaFile . $tmpnombre); 
            //echo $nombre; 
            $vista = $rutaPersonalizada . $tmpnombre; 
            echo "<a onclick=\"displayDocAnticipo('$tmpnombre')\"; class='btn btn-sm button-file col-12' title='Ver archivo'>Archivo <span class='fas fa-file'></span></a><corte>$tmpnombre";
        } else {
            echo "Error: El archivo $nombre debe ser un PDF<br>"; 
        }
    }
} else {
    echo "Error: No se recibieron archivos.";
}