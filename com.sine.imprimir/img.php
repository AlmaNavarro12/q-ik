<?php

if (isset($_POST['img'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_POST['img']);

    $data = $con->getIMGData($id);
    $dt = explode("<corte>", $data);

    $fn = $dt[0];
    $ext = $dt[1];

    $src = "../temporal/tmp/$fn";

    if (file_exists($src)) {

        if ($ext == 'pdf' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'doc' || $ext == 'docx' || $ext == 'rar' || $ext == 'zip') {
            echo "d<type>);";
        } else {
            $type = pathinfo($src, PATHINFO_EXTENSION);
            $data = file_get_contents($src);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            echo 'i<type><img src="' . $base64 . '" style="max-width: 100%;"/>';
        }
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_POST['pic'])) {

    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_POST['pic']);

    $data = $con->getIMGComunicado($id);
    $dt = explode("<corte>", $data);

    $fn = $dt[0];
    $ext = $dt[1];

    $src = "../comunicado/" . $fn;

    if (file_exists($src)) {
        if ($ext == 'pdf' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'doc' || $ext == 'docx' || $ext == 'rar' || $ext == 'zip') {
            echo "d<type>);";
        } else {
            $type = pathinfo($src, PATHINFO_EXTENSION);
            $data = file_get_contents($src);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            echo 'i<type><img src="' . $base64 . '" style="max-width: 100%;"/>';
        }
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_GET['doc'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_GET['doc']);

    $data = $con->getIMGData($id);
    $dt = explode("<corte>", $data);

    $fn = $dt[0];
    $ext = $dt[1];
    $orignm = $dt[2];

    $src = "../temporal/tmp/$fn";

    if (file_exists($src)) {
        header("Content-Disposition: attachment; filename=$orignm");

        readfile($src);
        exit();
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_GET['filecom'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_GET['filecom']);

    $data = $con->getIMGComunicado($id);
    $dt = explode("<corte>", $data);

    $fn = $dt[0];
    $ext = $dt[1];
    $orignm = $dt[2];

    $src = "../comunicado/$fn";

    if (file_exists($src)) {
        header("Content-Disposition: attachment; filename=$orignm");

        readfile($src);
        exit();
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_POST['aid'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_POST['aid']);
    $fn = $con->getIMGAnticipo($id);
    $src = "../img/anticipos/$fn";

    if (file_exists($src)) {
        $type = pathinfo($src, PATHINFO_EXTENSION);
        if ($type == 'pdf') {
            echo "d<type>";
            header('Content-Disposition: attachment; filename='.$fn); readfile($src); exit();
        }else{
            $data = file_get_contents($src);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            echo 'i<type><img src="' . $base64 . '" style="max-width: 100%;"/>';
        }
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_GET['aid'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_GET['aid']);
    $fn = $con->getIMGAnticipo($id);
    $src = "../img/anticipos/$fn";
    
    $type = pathinfo($src, PATHINFO_EXTENSION);

    if (file_exists($src)) {
        header('Content-Disposition: attachment; filename='.sha1($fn).".$type"); 
        readfile($src);
        exit();
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_GET['afn'])) {
    
    $src = "../temporal/tmp/".$_GET['afn'];
    
    $type = pathinfo($src, PATHINFO_EXTENSION);

    if (file_exists($src)) {
        header('Content-Disposition: attachment; filename='.sha1($_GET['afn']).".$type"); 
        readfile($src);
        exit();
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_POST['imgcp'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_POST['imgcp']);

    $data = $con->getIMGCartaPorte($id);
    $dt = explode("<corte>", $data);

    $fn = $dt[0];
    $ext = $dt[1];
    $orignm = $dt[2];

    $src = "../cartaporte/$fn";

    if (file_exists($src)) {
        if ($ext == 'pdf' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'doc' || $ext == 'docx' || $ext == 'rar' || $ext == 'zip') {
            echo "d<type>);";
        } else {
            $type = pathinfo($src, PATHINFO_EXTENSION);
            $data = file_get_contents($src);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            echo 'i<type><img src="' . $base64 . '" style="max-width: 100%;"/>';
        }
    } else {
        echo "404 No se encontro el archivo";
    }
} else if (isset($_GET['doccp'])) {
    require_once '../com.sine.controlador/ControladorImgs.php';

    $con = new ControladorImgs();

    $id = intval($_GET['doccp']);

    $data = $con->getIMGCartaPorte($id);
    $dt = explode("<corte>", $data);

    $fn = $dt[0];
    $ext = $dt[1];
    $orignm = $dt[2];

    $src = "../cartaporte/$fn";

    if (file_exists($src)) {
        header("Content-Disposition: attachment; filename=$orignm");

        readfile($src);
        exit();
    } else {
        echo "404 No se encontro el archivo";
    }
} else {
    echo "Error no se encontro el archivo";
}