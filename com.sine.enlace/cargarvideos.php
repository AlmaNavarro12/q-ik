<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorInstalacion.php';

if (isset($_FILES["video"])) {
    $ci = new ControladorInstalacion();
    Session::start();
    date_default_timezone_set("America/Mexico_City");

    $idusuario = $_SESSION[sha1("idusuario")];
    $idorden = $_POST['idorden'];
    $paso = $_POST['paso'];
    $check = $_POST['check'];

    $carpeta = "../temporal/tmpvideo/";

    $tipo_permitido = ['video/mp4', 'video/webm', 'video/x-matroska', 'video/x-msvideo', 'video/mpeg', 'video/3gpp', 'video/x-ms-wmv'];
    $extension_permitida = ['mp4', 'webm', 'mkv', 'avi', 'mpeg', '3gp', 'wmv'];

    $file = $_FILES["video"];
    $name = $file["name"];
    $tipo = $file["type"];

    if (!in_array($tipo, $tipo_permitido)) {
        echo "XError, el archivo no es un formato de video válido.<corte>";
    } else {
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if (!in_array($extension, $extension_permitida)) {
            echo "XError, la extensión del archivo no es permitida.<corte>";
        } else {
            $tempfile = $file['tmp_name'];
            $nombre = uniqid() . '_' . $idusuario . '_' . session_id() . '.' . $extension;
            $src = $carpeta . $nombre;
            if (move_uploaded_file($tempfile, $src)) {
                $insertar = $ci->insertarVid($name, $nombre, session_id(), $idorden, $paso, $check);
                echo $nombre;
            } else {
                echo "XError al mover el archivo.<corte>";
            }
        }
    }
}