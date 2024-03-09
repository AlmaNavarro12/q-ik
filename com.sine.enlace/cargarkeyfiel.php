<?php
if (isset($_FILES["key-fiel"])) {
    $rfc = $_POST['rfc-fiel'];
    $tempfile = ($_FILES['key-fiel']['tmp_name']);
    $file = $_FILES["key-fiel"];
    $nombre = $file["name"];
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];
    $filename = pathinfo($_FILES['key-fiel']['name'], PATHINFO_FILENAME);
    $ext = pathinfo($_FILES['key-fiel']['name'], PATHINFO_EXTENSION);
    if ($ext == 'key') {
        $carpeta = "../temporal/Fiel";
        $src = $carpeta . '/' . $rfc . '.' . $ext;
        move_uploaded_file($ruta_provisional, $src);
        echo '1' . $tipo;
    } else {
        echo '0El tipo de archivo no es válido.';
    }
}