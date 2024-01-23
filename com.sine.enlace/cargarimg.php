<?php
if (isset($_FILES["imagen"])) {
    date_default_timezone_set("America/Mexico_City");
    require_once '../com.sine.modelo/Session.php';
    Session::start();

    $maxsz = isset($_POST['imgsz']) ? $_POST['imgsz'] : 200;

    $sessionid = session_id();
    $idusuario = $_SESSION[sha1("idusuario")];
    $fecha = date("mdYHis");

    //str_shuffle — Reordena aleatoriamente una cadena
    $ranstr = substr(str_shuffle("0123456789011121314151617181920"), 0, 5);

    $file = $_FILES["imagen"];
    $tempfile = ($_FILES['imagen']['tmp_name']);
    $nombre = $file["name"];
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];
    $prevfile = $_POST['filename'];
    $carpeta = "../temporal/tmp/";

    if ($prevfile !== "" && file_exists($carpeta . $prevfile)) {
        unlink($carpeta . $prevfile);
    }

    $extension = pathinfo($nombre, PATHINFO_EXTENSION); //Obtener la extension del archivo
	$extension = ($extension == 'jfif') ? 'jpg' : $extension;
    $nombre = $ranstr . $fecha . '_' . $idusuario . $sessionid . '.' . $extension;

    if (!in_array($tipo, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/x-zip-compressed', 'application/octet-stream', 'application/zip', 'application/x-rar-compressed', 'multipart/x-zip'])) {
        echo "Error, tipo de archivo no permitido<corte>";
    } else if (in_array($tipo, ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'])) {
        move_uploaded_file($ruta_provisional, $carpeta . $nombre);
        echo "<a href='#' onclick='displayDocAnticipo();' class='btn btn-sm button-modal' title='Ver archivo'><span class='fas fa-file'></span></a><corte>$nombre";
    } else if ($size > 900 * 900) {
        $newwidth = $width * 0.5;
        $newheight = $height * 0.5;
    
        $image_create_func = 'imagecreatefrom' . substr($tipo, 6);
        $image_save_func = 'image' . substr($tipo, 6);
    
        //function_exists — Devuelve true si la función dada ha sido definida
        if (!function_exists($image_create_func) || !function_exists($image_save_func)) {
            throw new Exception('Unknown image type.');
        }
    
        $imagen = $image_create_func($tempfile);
        $imagen_p = imagecreatetruecolor($newwidth, $newheight);
        imagefilledrectangle($imagen_p, 0, 0, $width, $height, imagecolorallocate($imagen_p, 255, 255, 255));
        imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $img = $image_save_func($imagen_p, $carpeta . $nombre);
        imagedestroy($imagen_p);
    
        $vista = "temporal/tmp/" . $nombre;
        $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
        $data = file_get_contents("../" . $vista);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    
        if ($tipo != 'application/pdf') {
            $escalaWidth = $width >= $height ? $maxsz : ($width * $maxsz) / $height;
            $escalaHeight = $width >= $height ? ($height * $maxsz) / $width : $maxsz;
            $padding = $maxsz - $escalaHeight;
            echo "<img style='margin-top:$padding.;' src='$base64' width='$escalaWidth.px' height='$escalaHeight.px' id='img'><corte>$nombre";
        }
    } else if ($width < 60 || $height < 60) {
        echo "Error, la anchura y la altura mínima permitida es 60px<corte>";
    } else if ($tipo == 'image/png') {
        $newwidth = $width * 1;
        $newheight = $height * 1;
    
        $imagen = imagecreatefrompng($tempfile);
        $imagen_p = imagecreatetruecolor($newwidth, $newheight);
        imagefilledrectangle($imagen_p, 0, 0, $width, $height, imagecolorallocate($imagen_p, 255, 255, 255));
        imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
        $img = imagepng($imagen_p, $carpeta . $nombre);
        imagedestroy($imagen_p);
    
        $vista = "temporal/tmp/" . $nombre;
        $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
        $data = file_get_contents("../" . $vista);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    
        if ($tipo != 'application/pdf') {
            $escalaWidth = $width >= $height ? $maxsz : ($width * $maxsz) / $height;
            $escalaHeight = $width >= $height ? ($height * $maxsz) / $width : $maxsz;
            $padding = $maxsz - $escalaHeight;
            echo "<img style='margin-top:$padding.;' src='$base64' width='$escalaWidth.px' height='$escalaHeight.px' id='img'><corte>$nombre";
        }
    } else {
        $rawBaseName = pathinfo($nombre, PATHINFO_FILENAME);
        $extension = pathinfo($nombre, PATHINFO_EXTENSION);
    
        move_uploaded_file($ruta_provisional, $carpeta . $nombre);
        $vista = "temporal/tmp/" . $nombre;
        $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
        $data = file_get_contents("../" . $vista);
    
        if ($tipo != 'application/pdf') {
            $escalaWidth = $width >= $height ? $maxsz : ($width * $maxsz) / $height;
            $escalaHeight = $width >= $height ? ($height * $maxsz) / $width : $maxsz;
            $padding = $maxsz - $escalaHeight;
            echo "<img style='margin-top:$padding;' src='data:image/$type;base64," . base64_encode($data) . "' width='$escalaWidth.px' height='$escalaHeight.px' id='img'><corte>$nombre";
        } else {
            echo "Vista previa del PDF no disponible";
        }
    }    
}

/*
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
    $charsize = strlen($chars); //Longitud de chars
    
    for ($i = 0; $i < 5; $i++) { 
        $ranstr .= $chars[rand(0, $charsize - 1)]; //Se agrega un caracter al azar de chars
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
    } 
    //Si si es compatible con los formatos
    else if ($tipo == 'application/pdf' || $tipo == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $tipo == 'application/vnd.ms-excel' || $tipo == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $tipo == 'application/msword') {
        $src = $carpeta . $nombre; //Se le da el nombre al archivo
        move_uploaded_file($ruta_provisional, $src); //Mueve el archivo a src
        echo "<a href='#' onclick='displayDocAnticipo();' class='btn btn-sm button-modal' title='Ver archivo' ><span class='fas fa-file'></span></a><corte>$nombre";
    } 
    //Si si es compatible con los formatos
    else if ($size > 900 * 900) {
        $newwidth = $width * 0.5;
        $newheight = $height * 0.5;
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
                throw new Exception('Unknown image type.');
        }
        $imagen = $image_create_func($tempfile); //Crear una imagen en base a un archivo temporal
        $imagen_p = imagecreatetruecolor($newwidth, $newheight); // tamaño específico
        $white = imagecolorallocate($imagen_p, 255, 255, 255); //rellena la imagen con un fondo blanco
        imagefilledrectangle($imagen_p, 0, 0, $width, $height, $white); //dibujar un rectángulo con relleno
        imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height); //Copia y redimensiona la imagen original a las nuevas medidas
        $img = $image_save_func($imagen_p, $carpeta . $nombre); // Guarda la imagen nueva en la carpeta
        imageDestroy($imagen_p); //Destruye la imagen temporal

        $vista = "temporal/tmp/" . $nombre; 
        $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
        $data = file_get_contents("../" . $vista); // contenido del archivo como una cadena
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); // codifica a base64

        if ($tipo != 'application/pdf'){
            if ($width >= $height) { 
                $height = ($height * $maxsz) / $width; // Calcula la nueva altura proporcional al tamaño máximo
                $padding = $maxsz - $height; 
                echo "<img style='margin-top:$padding.;' src='$base64' width='" . $maxsz . "px' height='$height" . "px' id='img'><corte>$nombre";  // Muestra la imagen con el nuevo tamaño y margen superior
            } else {
                $width = ($width * $maxsz) / $height;
                echo "<img src='$base64' width='$width.px' height='" . $maxsz . "px' id='img'><corte>$nombre";
            }
        }
    } else if ($width < 60 || $height < 60) {
        echo "Error la anchura y la altura mínima permitida es 60px<corte>";
    } else if ($tipo == 'image/png') {
        $newwidth = $width * 1;
        $newheight = $height * 1;
        $imagen = imagecreatefrompng($tempfile);
        $imagen_p = imagecreatetruecolor($newwidth, $newheight);
        $white = imagecolorallocate($imagen_p, 255, 255, 255);
        imagefilledrectangle($imagen_p, 0, 0, $width, $height, $white);
        imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        $img = imagepng($imagen_p, $carpeta . $nombre);
        imageDestroy($imagen_p);
        $vista = "temporal/tmp/" . $nombre;
        $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
        $data = file_get_contents("../" . $vista);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        
        if ($tipo != 'application/pdf') {
            if ($width >= $height) {
                $height = ($height * $maxsz) / $width;
                $padding = $maxsz - $height;
                echo "<img style='margin-top:$padding.;' src='$base64' width='" . $maxsz . "px' height='$height" . "px' id='img'><corte>$nombre";
            } else {
                $width = ($width * $maxsz) / $height;
                echo "<img src='$base64' width='$width.px' height='" . $maxsz . "px' id='img'><corte>$nombre";
            }
        }
    } else {
        $rawBaseName = pathinfo($nombre, PATHINFO_FILENAME);
        $extension = pathinfo($nombre, PATHINFO_EXTENSION);

        $src = $carpeta . $nombre;
        move_uploaded_file($ruta_provisional, $src);
        $vista = "temporal/tmp/" . $nombre;
        $type = pathinfo("../" . $vista, PATHINFO_EXTENSION);
        $data = file_get_contents("../" . $vista);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        if ($tipo != 'application/pdf') {
            if ($width >= $height) {
                $height = ($height * $maxsz) / $width;
                $padding = $maxsz - $height;
                echo "<img style='margin-top:$padding.;' src='$base64' width='" . $maxsz . "px' height='$height" . "px' id='img'><corte>$nombre";
            } else {
                $width = ($width * $maxsz) / $height;
                echo "<img src='$base64' width='$width.px' height='" . $maxsz . "px' id='img'><corte>$nombre";
            }
        } else {
            echo "Vista previa del PDF no disponible";
        }
    }
} */
