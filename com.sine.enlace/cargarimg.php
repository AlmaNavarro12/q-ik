<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorImgs.php';

Session::start();
date_default_timezone_set("America/Mexico_City");

$maxsz = isset($_POST['imgsz']) ? $_POST['imgsz'] : 200;
$carpeta = "../temporal/tmp/";

function crearNombre($extension)
{
    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];
    $fecha = date('YmdHis');
    $ranstr = substr(str_shuffle("0123456789011121314151617181920"), 0, 5);
    return $fecha . $ranstr . '_' . $idusuario . $sessionid . '.' . $extension;
}

if (isset($_FILES["imagenusuario"]) || isset($_FILES["imagenperfil"])) {
    $imgtmp = $_FILES["imagenusuario"] ?? $_FILES["imagenperfil"];
    $file = $imgtmp;
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];
    $prevfile = isset($_POST['fileuser']) ? $_POST['fileuser'] : (isset($_POST['filename']) ? $_POST['filename'] : '');

    if ($prevfile && file_exists($carpeta . $prevfile)) {
        unlink($carpeta . $prevfile);
    }

    $dimensiones = getimagesize($ruta_provisional);
    $width = $dimensiones[0];
    $height = $dimensiones[1];
    $nombre = crearNombre(pathinfo($file["name"], PATHINFO_EXTENSION));

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

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($ruta_provisional);

    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;

    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }

    $image($dst_img, $carpeta . $nombre);
    //$insertar = $ci->insertarImg($nombre, $tmpnombre, $extension , $sessionid); 
    if ($dst_img) imagedestroy($dst_img);
    if ($src_img) imagedestroy($src_img);

    $type = pathinfo($carpeta . $nombre, PATHINFO_EXTENSION);
    $data = file_get_contents($carpeta . $nombre);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    echo "<img src='$base64' width='200' class='rounded-circle border border-secondary shadow-sm' id='img'><corte>$nombre";
} 

else if (isset($_FILES["imagen"])) {
    $file = $_FILES["imagen"];
    $nombre = crearNombre(pathinfo($file["name"], PATHINFO_EXTENSION));
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];

    $dimensiones = getimagesize($ruta_provisional);
    $width = $dimensiones[0];
    $height = $dimensiones[1];

    if (($size > 900 * 900) || ($width > 2000 || $height > 2000)) {
        $newwidth = $width * 0.5;
        $newheight = $height * 0.5;
        procesarImagen($ruta_provisional, $carpeta, $nombre, $newheight, $newheight, 0);
    } else if ($width < 60 || $height < 60) {
        echo "Error: La anchura y la altura mínima permitida es 60px.<corte>";
    } else if ($tipo == 'image/png') {
        $newwidth = $width * 1;
        $newheight = $height * 1;
        $imagen = imagecreatefrompng($ruta_provisional);
        $imagen_p = imagecreatetruecolor($newwidth, $newheight);
        $white = imagecolorallocate($imagen_p, 255, 255, 255);
        imagefilledrectangle($imagen_p, 0, 0, $width, $height, $white);
        imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $img = imagepng($imagen_p, $carpeta . $nombre);
        imageDestroy($imagen_p);
    } else {
        $rawBaseName = pathinfo($nombre, PATHINFO_FILENAME);
        $extension = pathinfo($nombre, PATHINFO_EXTENSION);
        $src = $carpeta . $nombre;
        move_uploaded_file($ruta_provisional, $src);
    }

    $vista = "temporal/tmp/" . $nombre;
    $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
    $data = file_get_contents("../" . $vista);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    if ($tipo != 'application/pdf') {
        $maxsz = 200; 
        if ($width >= $height) {
            $height = ($height * $maxsz) / $width;
            $padding = $maxsz - $height;
            echo "<img style='margin-top:$padding.;' src='$base64' width='" . $maxsz . "px' height='$height" . "px' id='img'><corte>$nombre";
        } else {
            $width = ($width * $maxsz) / $height;
            echo "<img src='$base64' width='$width.px' height='" . $maxsz . "px' id='img'><corte>$nombre";
        }
    } else {
        echo "Vista previa no disponible";
    }
}

function procesarImagen($ruta_provisional, $carpeta, $nombre, $max_width, $max_height, $quality)
{
    $dimensiones = getimagesize($ruta_provisional);
    $width = $dimensiones[0];
    $height = $dimensiones[1];
    $tipo = $dimensiones['mime'];

    switch ($tipo) {
        case 'image/jpeg':
            $image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            break;
        case 'image/png':
            $image_create_func = 'imagecreatefrompng';
            $image_save_func = 'imagepng';
            break;
        case 'image/gif':
            $image_create_func = 'imagecreatefromgif';
            $image_save_func = 'imagegif';
            break;
        default:
            throw new Exception('Tipo de imagen desconocido.');
    }

    $imagen = $image_create_func($ruta_provisional);
    $imagen_p = imagecreatetruecolor($max_width, $max_height);
    $white = imagecolorallocate($imagen_p, 255, 255, 255);
    imagefilledrectangle($imagen_p, 0, 0, $max_width, $max_height, $white);
    imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $max_width, $max_height, $width, $height);

    switch ($tipo) {
        case 'image/jpeg':
            $image_save_func($imagen_p, $carpeta . $nombre, $quality);
            break;
        case 'image/png':
            $image_save_func($imagen_p, $carpeta . $nombre, $quality);
            break;
        case 'image/gif':
            $image_save_func($imagen_p, $carpeta . $nombre);
            break;
        default:
            throw new Exception('Tipo de imagen desconocido.');
    }

    imagedestroy($imagen_p);
    imagedestroy($imagen);
}
