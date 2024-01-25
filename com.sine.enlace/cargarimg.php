<?php

require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorImgs.php';

$ci = new ControladorImgs();
Session::start();
date_default_timezone_set("America/Mexico_City");

$maxsz = isset($_POST['imgsz']) ? $_POST['imgsz'] : 200;
$sessionid = session_id();
$idusuario = $_SESSION[sha1("idusuario")];

$carpeta = "../temporal/tmp/";

function generateRandomString($length = 5) {
    $characters = "0123456789011121314151617181920";
    $charsize = strlen($characters);
    $ranstr = "";
    for ($i = 0; $i < $length; $i++) {
        $ranstr .= $characters[rand(0, $charsize - 1)];
    }
    return $ranstr;
}

function resizeImage($tempfile, $newwidth, $newheight) {
    list($width, $height) = getimagesize($tempfile);
    $image_create_func = 'imagecreatefromjpeg'; // default
    $image_save_func = 'imagejpeg'; // default

    if ($width < $height) {
        $image_create_func = 'imagecreatefrompng';
        $image_save_func = 'imagepng';
    } elseif ($width === $height) {
        $image_create_func = 'imagecreatefromgif';
        $image_save_func = 'imagegif';
    }

    $imagen = $image_create_func($tempfile);
    $imagen_p = imagecreatetruecolor($newwidth, $newheight);
    $white = imagecolorallocate($imagen_p, 255, 255, 255);
    imagefilledrectangle($imagen_p, 0, 0, $newwidth, $newheight, $white);
    imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    $img = $image_save_func($imagen_p, $tempfile);
    imagedestroy($imagen_p);
}

if (isset($_FILES["imagen"])) {
    $fileimg = $_FILES["imagen"];
    $total = count($fileimg['name']);

    for ($i = 0; $i < $total; $i++) {
        $f = getdate();
        $fecha = $f['year'] . sprintf("%02d%02d%02d%02d%02d%02d", $f['mon'], $f['mday'], $f['hours'], $f['minutes'], $f['seconds']);

        $tempfile = $fileimg['tmp_name'][$i];
        $prevfile = $_POST['filename'];
        $nombre = $fileimg["name"][$i];
        $tipo = $fileimg["type"][$i];
        $ruta_provisional = $fileimg["tmp_name"][$i];
        $size = $fileimg["size"][$i];

        $extension = pathinfo($nombre, PATHINFO_EXTENSION);
        if ($extension == 'jfif') {
            $extension = 'jpg';
        }

        $ranstr = generateRandomString();
        $tmpnombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension;

        if (!in_array($tipo, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/x-zip-compressed', 'application/octet-stream', 'application/zip', 'application/x-rar-compressed', 'multipart/x-zip'])) {
            echo "Error, tipo de archivo no permitido<corte>";
        } elseif (in_array($tipo, ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'])) {
            $src = $carpeta . $tmpnombre;
            move_uploaded_file($ruta_provisional, $src);
            echo "<a href='#' onclick='displayDocAnticipo();' class='btn btn-sm button-modal' title='Ver archivo'><span class='glyphicon glyphicon-file'></span></a><corte>$nombre";
        } elseif (in_array($tipo, ['application/x-zip-compressed', 'application/octet-stream', 'application/zip', 'application/x-rar-compressed', 'multipart/x-zip'])) {
            $src = $carpeta . $tmpnombre;
            $bytes = file_get_contents($ruta_provisional, FALSE, NULL, 0, 7);
            $sub = substr($nombre, -4);

            if ($sub == '.zip' && substr($bytes, 0, 2) == 'PK') {
                $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
                move_uploaded_file($ruta_provisional, $src);
                echo "$nombre";
            } elseif ($sub == '.rar' && (bin2hex($bytes) == '526172211a0701' || bin2hex($bytes) == '526172211a0700')) {
                $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
                move_uploaded_file($ruta_provisional, $src);
                echo "$nombre";
            } else {
                echo 'Archivo no permitido<corte>';
            }
        } else {
            $dimensiones = getimagesize($ruta_provisional);
            $width = $dimensiones[0];
            $height = $dimensiones[1];

            if ($size >= 900 * 900 || ($width >= 1800 || $height >= 1800)) {
                $newwidth = $width * ($size >= 900 * 900 ? 0.3 : 0.4);
                $newheight = $height * ($size >= 900 * 900 ? 0.3 : 0.4);
                resizeImage($tempfile, $newwidth, $newheight);
            }

            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);

            $type = pathinfo("../temporal/tmp/" . $tmpnombre, PATHINFO_EXTENSION);
            $data = file_get_contents("../temporal/tmp/" . $tmpnombre);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            if ($tipo != 'application/pdf') {
                $maxSize = $size >= 900 * 900 ? 200 : $maxsz;

                if ($width >= $height) {
                    $height = ($height * $maxSize) / $width;
                    $padding = $maxSize - $height;
                    echo "<img style='margin-top:$padding.;' src='$base64' width='" . $maxSize . "px' height='$height" . "px' id='img'><corte>$tmpnombre";
                } else {
                    $width = ($width * $maxSize) / $height;
                    echo "<img src='$base64' width='$width.px' height='" . $maxSize . "px' id='img'><corte>$tmpnombre";
                }
            }
        }
    }
} else {
    echo "Error, no se recibió ningún archivo<corte>";
}