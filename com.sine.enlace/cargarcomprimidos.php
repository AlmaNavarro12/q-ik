<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorImgs.php'; 

Session::start(); 
date_default_timezone_set("America/Mexico_City"); 
$ci = new ControladorImgs(); 
$carpeta = "../temporal/tmp/"; 

// Verifica si se ha recibido algún archivo comprimido
if (!empty($_FILES)) {
    $sessionid = session_id(); 
    $idusuario = $_SESSION[sha1("idusuario")];
    
    foreach ($_FILES as $file) { 
        $nombre = $file["name"];
        $extension = pathinfo($nombre, PATHINFO_EXTENSION); 

        if (in_array($extension, ['zip', 'rar'])) {
            // Obtiener los primeros bytes del archivo
            $bytes = file_get_contents($file["tmp_name"], FALSE, NULL, 0, 7);
            
            // Verificar la firma del archivo comprimido
            if (($extension == 'zip' && substr($bytes, 0, 2) == 'PK') ||
                ($extension == 'rar' && in_array(bin2hex($bytes), ['526172211a0701', '526172211a0700']))) {
                $fecha = date('YmdHis'); 
                $ranstr = substr(str_shuffle('0123456789011121314151617181920'), 0, 5); 
                $tmpnombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension; 
                //$insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid); 
                move_uploaded_file($file["tmp_name"], "../temporal/tmp/" . $tmpnombre); 
                echo $nombre; 
            } else {
                echo 'Archivo comprimido no válido<br>'; 
            }
        } else {
            echo 'Error: El archivo no es un archivo comprimido<br>'; 
        }
    }
} else if (empty($_FILES)) {
    echo "Error: No se recibieron archivos<br>"; 
} else {
    echo "Error: No se recibió ningún archivo comprimido<br>"; 
}