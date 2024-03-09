<?php
if (isset($_FILES["archivo-key"])) {
    $rfc = $_POST['rfc-empresa'];
    $pass = $_POST['password-key'];
    $tempfile = ($_FILES['archivo-key']['tmp_name']);
    $file = $_FILES["archivo-key"];
    $nombre = $file["name"];
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];
    $filename = pathinfo($_FILES['archivo-key']['name'], PATHINFO_FILENAME);
    $ext = pathinfo($_FILES['archivo-key']['name'], PATHINFO_EXTENSION);
    if ($ext == 'key') {
        $carpeta = "../temporal/$rfc";
        if (!is_dir($carpeta)) {
            mkdir($carpeta);
        }
        $src = $carpeta . '/key.' . $ext;
        move_uploaded_file($ruta_provisional, $src);
        echo '1' . $tipo;
    } else {
        echo '0El tipo de archivo no es válido.';
    }
}