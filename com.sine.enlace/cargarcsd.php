<?php
if (isset($_FILES["certificado-csd"])) {
    $rfc = $_POST['rfc-empresa'];
    $tempfile = ($_FILES['certificado-csd']['tmp_name']);
    $file = $_FILES["certificado-csd"];
    $nombre = $file["name"];
    $tipo = $file["type"];
    $ruta_provisional = $file["tmp_name"];
    $size = $file["size"];
    $filename = pathinfo($_FILES['certificado-csd']['name'], PATHINFO_FILENAME);
    $ext = pathinfo($_FILES['certificado-csd']['name'], PATHINFO_EXTENSION);
    if ($ext == "cer") {
        $carpeta = "../temporal/$rfc";
        if (!is_dir($carpeta)) {
            mkdir($carpeta);
        }
        $src = $carpeta . '/csd.' . $ext;
        move_uploaded_file($ruta_provisional, $src);
        $shellserial = "openssl x509 -inform der -in $src -noout -serial > $carpeta/Serial.txt 2>&1";

        $exc = shell_exec($shellserial);
        echo '1'.$exc;
    } else {
        echo '0El tipo de archivo no es valido';
    }
}