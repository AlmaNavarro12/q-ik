<?php

if (isset($_FILES["imagen"]) || isset($_FILES["img-evidencia"])) {
    require_once '../com.sine.modelo/Session.php';
    require_once '../com.sine.controlador/ControladorImgs.php';
    $ci = new ControladorImgs();
    Session::start();
    date_default_timezone_set("America/Mexico_City");

    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];

    $tmpfile = "";
    
    if(isset($_FILES["imagen"])){
        $fileimg = $_FILES["imagen"];
    }else if (isset($_FILES["img-evidencia"])){
        $fileimg = $_FILES["img-evidencia"];
    }
    $total = count($fileimg['name']);
    for ($i = 0; $i < $total; $i++) {
        $f = getdate();
        $d = $f['mday'];
        $m = $f['mon'];
        $y = $f['year'];
        $h = $f['hours'];
        $mi = $f['minutes'];
        $s = $f['seconds'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        if ($h < 10) {
            $h = "0$h";
        }
        if ($mi < 10) {
            $mi = "0$mi";
        }
        if ($s < 10) {
            $s = "0$s";
        }
        $fecha = $m . $y . $d . $h . $mi . $s;

        $ranstr = "";
        $chars = "0123456789011121314151617181920";
        $charsize = strlen($chars);
        for ($r = 0; $r < 5; $r++) {
            $ranstr .= $chars[rand(0, $charsize - 1)];
        }

        $tempfile = $fileimg['tmp_name'][$i];
        $prevfile = $_POST['filename'];
        $nombre = $fileimg["name"][$i];
        $tipo = $fileimg["type"][$i];
        $ruta_provisional = $fileimg["tmp_name"][$i];
        $size = $fileimg["size"][$i];

        if ($tipo == 'image/jpg' || $tipo == 'image/jpeg' || $tipo == 'image/png' || $tipo == 'image/gif') {
            $dimensiones = getimagesize($ruta_provisional);
            $width = $dimensiones[0];
            $height = $dimensiones[1];
        }//Para permitir avance del sistema cuando es pdf
        
        $carpeta = "../temporal/tmp/";

        $extension = pathinfo($nombre, PATHINFO_EXTENSION);
        if ($extension == 'jfif') {
            $extension = 'jpg';
        }
        $tmpnombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension;


        if ($tipo == 'application/pdf' || $tipo == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $tipo == 'application/vnd.ms-excel' || $tipo == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $tipo == 'application/msword') {
            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
            $src = $carpeta . $tmpnombre;
            move_uploaded_file($ruta_provisional, $src);
            //El 
            $vista = "temporal/tmp/" . $tmpnombre;
            //Altura responsiva
            if ($tipo != 'application/pdf' && $tipo != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $tipo != 'application/vnd.ms-excel' && $tipo != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' && $tipo != 'application/msword') {

                if ($width >= $height) {
                    $height = ($height * 200) / $width;
                    $padding = 200 - $height;
                    echo "$nombre";
                } else {
                    $width = ($width * 200) / $height;
                    echo "$nombre";
                }
            } else {
                echo "$nombre";
            }
        } else if ($tipo == 'application/x-zip-compressed' || $tipo == 'application/octet-stream' || $tipo == 'application/zip' || $tipo == 'application/x-rar-compressed' || $tipo == 'multipart/x-zip') {
            $src = $carpeta . $tmpnombre;
            $bytes = file_get_contents($ruta_provisional, FALSE, NULL, 0, 7);
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
        } else if ($tipo != 'image/jpg' && $tipo != 'image/jpeg' && $tipo != 'image/png' && $tipo != 'image/gif' && $tipo != 'application/pdf' && $tipo != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $tipo != 'application/vnd.ms-excel' && $tipo != 'application/x-zip-compressed' && $tipo != 'application/octet-stream' && $tipo != 'application/zip' && $tipo != 'application/x-rar-compressed' && $tipo != 'multipart/x-zip') {
            echo "XError, el archivo no es una imagen<corte>";
        } else if ($size >= 900 * 900) {
            $newwidth = $width * 0.3;
            $newheight = $height * 0.3;
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
                    throw Exception('Unknown image type.');
            }
            $imagen = $image_create_func($tempfile);
            $imagen_p = imagecreatetruecolor($newwidth, $newheight);
            $white = imagecolorallocate($imagen_p, 255, 255, 255);
            imagefilledrectangle($imagen_p, 0, 0, $width, $height, $white);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            $img = $image_save_func($imagen_p, $carpeta . $tmpnombre);
            imageDestroy($imagen_p);

            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
            //El
            //Altura responsiva
            if ($tipo != 'application/pdf') {

                if ($width >= $height) {
                    $height = ($height * 200) / $width;
                    $padding = 200 - $height;
                    echo "$nombre";
                } else {
                    $width = ($width * 200) / $height;
                    echo "$nombre";
                }
            }
        } else if ($width >= 1800 || $height >= 1800) {
            $newwidth = $width * 0.4;
            $newheight = $height * 0.4;
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
                    throw Exception('Unknown image type.');
            }
            $imagen = $image_create_func($tempfile);
            $imagen_p = imagecreatetruecolor($newwidth, $newheight);
            $white = imagecolorallocate($imagen_p, 255, 255, 255);
            imagefilledrectangle($imagen_p, 0, 0, $width, $height, $white);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            $img = $image_save_func($imagen_p, $carpeta . $tmpnombre);
            imageDestroy($imagen_p);

            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
            //El
            $vista = "temporal/tmp/" . $nombre;
            //Altura responsiva
            if ($tipo != 'application/pdf') {

                if ($width >= $height) {
                    $height = ($height * 200) / $width;
                    $padding = 200 - $height;
                    echo "$nombre";
                } else {
                    $width = ($width * 200) / $height;
                    echo "$nombre";
                }
            }
        } else if ($width < 60 || $height < 60) {
            echo "XError la anchura y la altura mínima permitida es 60px<corte>";
        } else if ($tipo == 'image/png') {
            $newwidth = $width * 1;
            $newheight = $height * 1;
            $imagen = imagecreatefrompng($tempfile);
            $imagen_p = imagecreatetruecolor($newwidth, $newheight);
            $white = imagecolorallocate($imagen_p, 255, 255, 255);
            imagefilledrectangle($imagen_p, 0, 0, $width, $height, $white);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            $img = imagepng($imagen_p, $carpeta . $tmpnombre);
            imageDestroy($imagen_p);

            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
            //El
            $vista = "temporal/tmp/" . $nombre;
            //Altura responsiva
            if ($tipo != 'application/pdf') {

                if ($width >= $height) {
                    $height = ($height * 200) / $width;
                    $padding = 200 - $height;
                    echo "$nombre";
                } else {
                    $width = ($width * 200) / $height;
                    echo "$nombre";
                }
            }
        } else {
            $src = $carpeta . $tmpnombre;
            move_uploaded_file($ruta_provisional, $src);
            $insertar = $ci->insertarImg($nombre, $tmpnombre, $extension, $sessionid);
            //El
            $vista = "temporal/tmp/" . $nombre;
            //Altura responsiva
            if ($tipo != 'application/pdf') {

                if ($width >= $height) {
                    $height = ($height * 200) / $width;
                    $padding = 200 - $height;
                    echo "$nombre";
                } else {
                    $width = ($width * 200) / $height;
                    echo "$nombre";
                }
            } else {
                echo "Vista previa del PDF no disponible";
            }
        }
    }
}