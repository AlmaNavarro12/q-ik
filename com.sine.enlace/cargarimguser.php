<?php

if (isset($_FILES["imgprof"]) || isset($_FILES["imagen"])) {
    $imgtmp = isset($_FILES["imgprof"]) ? $_FILES["imgprof"] : $_FILES["imagen"];
    $tempfile = $imgtmp['tmp_name'];
    $file = $imgtmp;
    $tipo = $file["type"];

    if (!in_array($tipo, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
        echo "Error, el archivo no es una imagen<corte>";
    } else {
        require_once '../com.sine.modelo/Session.php';
        Session::start();
        date_default_timezone_set("America/Mexico_City");

        $sessionid = session_id();
        $idusuario = $_SESSION[sha1("idusuario")];

        $fecha = date('YmdHis');
        $ranstr = substr(str_shuffle("0123456789011121314151617181920"), 0, 5);

        $prevfile = isset($_POST['fileuser']) ? $_POST['fileuser'] : (isset($_POST['filename']) ? $_POST['filename'] : '');

        $ruta_provisional = $file["tmp_name"];
        $nombre = $file["name"];
        $size = $file["size"];

        $carpeta = "../temporal/tmp/";

        if ($prevfile && file_exists($carpeta . $prevfile)) {
            unlink($carpeta . $prevfile);
        }

        $dimensiones = getimagesize($ruta_provisional);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $extension = pathinfo($nombre, PATHINFO_EXTENSION);
        $nombre = $fecha . $ranstr . '_' . $idusuario . $sessionid . '.' . $extension;

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

        $image($dst_img, $carpeta . $nombre, $quality);

        if ($dst_img) imagedestroy($dst_img);
        if ($src_img) imagedestroy($src_img);

        $type = pathinfo($carpeta . $nombre, PATHINFO_EXTENSION);
        $data = file_get_contents($carpeta . $nombre);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        echo "<img src='$base64' width='200px' id='img'><corte>$nombre";
    }
}