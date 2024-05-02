<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.controlador/ControladorInstalacion.php';

$ci = new ControladorInstalacion();
Session::start();
date_default_timezone_set("America/Mexico_City");

$sessionid = session_id();
$idusuario = $_SESSION[sha1("idusuario")];

$idorden = $_POST['idorden'];

function generarBase($tempfile, $name)
{
    $type = pathinfo($name, PATHINFO_EXTENSION);
    $file_content = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($tempfile));
    return $file_content;
}

if (isset($_FILES["imagen"])) {
    $tempfile = $_FILES['imagen']['tmp_name'];
    $name = $_FILES["imagen"]["name"];
    $file_content = generarBase($tempfile, $name);

    if ($_POST['accion'] == 'otras') {
        $paso = $_POST['paso'];
        $check = $_POST['check'];
        $titulo = $_POST['titulo'];

        if ($file_content) {
            $insertar = $ci->registraImgTMPOtras($name, $file_content, $idorden, $paso, $check, $titulo);
            if ($insertar) {
                echo "$name</tr>$file_content";
            }
        }
    } elseif (in_array($_POST['accion'], ['frente', 'serieovin', 'tableroini', 'cableadoini', 'cableadofin', 'tablerofin'])) {
        if ($file_content) {
            $campo = '';
            switch ($_POST['accion']) {
                case 'frente':
                    $campo = 'imgfrentebase';
                    break;
                case 'serieovin':
                    $campo = 'imgseriebase';
                    break;
                case 'tableroini':
                    $campo = 'imgtabinibase';
                    break;
                case 'cableadoini':
                    $campo = 'imgantesbase';
                    break;
                case 'cableadofin':
                    $campo = 'imgdespuesbase';
                    break;
                case 'tablerofin':
                    $campo = 'imgtabfinbase';
                    break;
            }
            $insertar = $ci->updateImgEvidencias($file_content, $idorden, $campo);
            if ($insertar) {
                echo "$name</tr>$file_content";
            }
        }
    }
}
