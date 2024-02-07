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
    $extension = pathinfo($nombre, PATHINFO_EXTENSION);
    $fecha = date('YmdHis');
    $ranstr = substr(str_shuffle('0123456789011121314151617181920'), 0, 5);
    $tmpnombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension;
    $src = $carpeta . $tmpnombre;

    // Verificar si el archivo es un archivo comprimido
    if (in_array($extension, ['zip', 'rar'])) {
        // Obtiener los primeros bytes del archivo
        $bytes = file_get_contents($fileimg["tmp_name"], FALSE, NULL, 0, 7);
        
        // Verificar la firma del archivo comprimido
        if (($extension == 'zip' && substr($bytes, 0, 2) == 'PK') ||
            ($extension == 'rar' && in_array(bin2hex($bytes), ['526172211a0701', '526172211a0700']))) {
            // Insertar en la base de datos y mover el archivo al directorio temporal
            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
            move_uploaded_file($fileimg["tmp_name"], "../temporal/tmp/" . $tmpnombre);
            echo "$nombre";
        } else {
            echo 'Archivo comprimido no válido<corte>';
        }
    } else {
        echo 'Error: El archivo no es un archivo comprimido<corte>';
    }
} else {
    echo "Error: No se recibió ningún archivo o no es un archivo comprimido.<corte>";
}
?>
