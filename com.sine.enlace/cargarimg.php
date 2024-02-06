<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorImgs.php';

Session::start();
date_default_timezone_set("America/Mexico_City");
$ci = new ControladorImgs();
$carpeta = "../temporal/tmp/";

// Si se ha enviado un archivo de imagen
if (isset($_FILES["imgprof"]) || isset($_FILES["imagen"]) || isset($_FILES["img-evidencia"])) {
    $imgtmp = isset($_FILES["imgprof"]) ? $_FILES["imgprof"] : (isset($_FILES["imagen"]) ? $_FILES["imagen"] : $_FILES["img-evidencia"]);
    $file = $imgtmp;
    $tipo = $file["type"];

    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];

    // Ruta provisional y nombre del archivo
    $ruta_provisional = $file["tmp_name"];
    $nombre = $file["name"];
    $size = $file["size"];
    $fecha = date('YmdHis');
    $ranstr = substr(str_shuffle("0123456789011121314151617181920"), 0, 5);
    $prevfile = isset($_POST['fileuser']) ? $_POST['fileuser'] : (isset($_POST['filename']) ? $_POST['filename'] : '');

    // Obtener dimensiones y tipo de la imagen
    $dimensiones = getimagesize($ruta_provisional);
    $width = $dimensiones[0];
    $height = $dimensiones[1];
    $extension = pathinfo($nombre, PATHINFO_EXTENSION);
    $nombre = $fecha . $ranstr . '_' . $idusuario . $sessionid . '.' . $extension;

    // Si el archivo es una imagen válida (jpg, jpeg, png, gif)
    if (in_array($tipo, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
        // Eliminar el archivo anterior si existe
        if ($prevfile && file_exists($carpeta . $prevfile)) {
            unlink($carpeta . $prevfile);
        }

        // Variables para redimensionar la imagen
        $max_width = 500;
        $max_height = 500;
        $mime = $dimensiones['mime'];

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                $quality = 80;
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 8;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;

            default:
                echo "Error, formato de imagen no soportado<corte>";
                return;
        }

        // Crear las img
        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($ruta_provisional);

        // Nuevas dimensiones manteniendo la proporción
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;

        // Redimensiona la imagen y guardar en tmp
        if ($width_new > $width) {
            $h_point = (($height - $height_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        // Guardar la imagen redimensionada
        $image($dst_img, $carpeta . $nombre);

        if ($dst_img) imagedestroy($dst_img);
        if ($src_img) imagedestroy($src_img);

        // Convierte la imagen a base64 para mostrarla
        $type = pathinfo($carpeta . $nombre, PATHINFO_EXTENSION);
        $data = file_get_contents($carpeta . $nombre);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        echo "<img src='$base64' width='200' id='img'><corte>$nombre";
    } 
    else if (in_array($tipo, ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'])) {
        $extension = pathinfo($nombre, PATHINFO_EXTENSION);
        $nombre = $fecha . $ranstr . '_' . $idusuario . $sessionid . '.' . $extension;
        $src = $carpeta . $nombre;

        move_uploaded_file($ruta_provisional, $src);
        $insertar = $ci->insertarImg($nombre, $nombre, $extension, $sessionid);
        //echo "<a href='#' onclick='displayDocAnticipo();' class='btn btn-sm button-modal' title='Ver archivo' ><span class='glyphicon glyphicon-file'></span></a><corte>$nombre";
    } 
    else if (in_array($tipo, ['application/x-zip-compressed', 'application/octet-stream', 'application/zip', 'application/x-rar-compressed', 'multipart/x-zip'])) {
        // Obtiener los primeros bytes del archivo
        $bytes = file_get_contents($ruta_provisional, FALSE, NULL, 0, 7);
        // Verificar la extensión del archivo
        $sub = substr($nombre, -4);
        if ($sub == '.zip') {
            $sign = substr($bytes, 0, 2);
            if ($sign == 'PK') {
                $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
                move_uploaded_file($ruta_provisional, $src);
                echo "$nombre";
            } else {
                echo 'Archivo no permitido<corte>';
            }
        } else if ($sub == '.rar') {
            $sign = bin2hex($bytes);
            if ($sign == '526172211a0701' || $sign == '526172211a0700') {
                $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
                move_uploaded_file($ruta_provisional, $src);
                echo "$nombre";
            } else {
                echo 'Archivo no permitido<corte>';
            }
        } else {
            echo 'Archivo no permitido<corte>';
        }
    }
} else {
    echo "Error, no se recibió ningún archivo<corte>";
}
