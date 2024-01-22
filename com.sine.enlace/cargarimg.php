<?php
if (isset($_FILES["imagen"])) {
    date_default_timezone_set("America/Mexico_City");
    require_once '../com.sine.modelo/Session.php';
    Session::start();

    $maxsz = 200;
    if (isset($_POST['imgsz'])) {
        $maxsz = $_POST['imgsz'];
    }

    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];
    $fecha = date("mdYHis");
    
    $ranstr = "";
    $chars = "0123456789011121314151617181920";
    $charsize = strlen($chars);
    
    for ($i = 0; $i < 5; $i++) {
        $ranstr .= $chars[rand(0, $charsize - 1)];
    }

    $tempfile = ($_FILES['imagen']['tmp_name']);
    $prevfile = $_POST['filename'];
    $file = $_FILES["imagen"];
    $nombre = $file["name"];
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];
    $carpeta = "../temporal/tmp/";

    //Imágenes (jpg, jpeg, png o gif).
    if ($tipo == 'image/jpg' || $tipo == 'image/jpeg' || $tipo == 'image/png' || $tipo == 'image/gif') {
        $dimensiones = getimagesize($ruta_provisional); //Tomar sus dimensiones
        $width = $dimensiones[0];
        $height = $dimensiones[1];
    }

    if ($prevfile != "") {
        if (file_exists($carpeta . $prevfile)) {
            unlink($carpeta . $prevfile); //Elimina el archivo anterior
        }
    }

    $extension = pathinfo($nombre, PATHINFO_EXTENSION); //Obtener la extension del archivo
	$extension = ($extension == 'jfif') ? 'jpg' : $extension;

    $nombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension;

    if ($tipo != 'image/jpg' && $tipo != 'image/jpeg' && $tipo != 'image/png' && $tipo != 'image/gif' && $tipo != 'application/pdf' && $tipo != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $tipo != 'application/vnd.ms-excel' && $tipo != 'application/x-zip-compressed' && $tipo != 'application/octet-stream' && $tipo != 'application/zip' && $tipo != 'application/x-rar-compressed' && $tipo != 'multipart/x-zip') {
        echo "Error, tipo de archivo no permitido<corte>";
    } else if ($tipo == 'application/pdf' || $tipo == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $tipo == 'application/vnd.ms-excel' || $tipo == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $tipo == 'application/msword') {
        $src = $carpeta . $nombre; //Se le da el nombre al archivo
        move_uploaded_file($ruta_provisional, $src);
        echo "<a href='#' onclick='displayDocAnticipo();' class='btn btn-sm button-modal' title='Ver archivo' ><span class='glyphicon glyphicon-file'></span></a><corte>$nombre";
    } 
}

