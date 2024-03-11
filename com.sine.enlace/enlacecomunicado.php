<?php

require_once '../com.sine.modelo/Comunicado.php';
require_once '../com.sine.controlador/ControladorComunicado.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';
Session::start();
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cc = new ControladorComunicado();
    
    switch ($transaccion) {
        case 'insertarcomunicado':
            $insertado = $cc->insertarComunicado(obtenerDatosComunicado());
            echo $insertado ? "Registro insertado" : "Error: no insertó el registro";
            break;
        case 'listacomunicado':
            $REF = $_POST['REF'];
            $pag = $_POST['pag'];
            $numreg = $_POST['numreg'];
            $datos = $cc->listaComunicado($REF, $pag, $numreg);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'editarcomunicado':
            $idcomunicado = $_POST['idcomunicado'];
            $datos = $cc->getDatosComunicado($idcomunicado);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'actualizarcomunicado':
            $c = new Comunicado();
            $idcomunicado = $_POST['idcomunicado'];
            $tag = $_POST['tag'];
            $chcom =$_POST['chcom'];
            $idcontactos = $_POST['idcontactos'];
            $asunto = $_POST['asunto'];
            $emision = $_POST['emision'];
            $color = $_POST['color'];
            $size = $_POST['size'];
            $texto = $_POST['txtbd'];
            $sellar = $_POST['sellar'];
            $firma = $_POST['firma'];
            $iddatos = $_POST['iddatos'];
            $sessionid = session_id();
            
            $c->setIdcomunicado($idcomunicado);
            $c->setTag($tag);
            $c->setChcom($chcom);
            $c->setIdcontactos($idcontactos);
            $c->setAsunto($asunto);
            $c->setEmision($emision);
            $c->setColor($color);
            $c->setSize($size);
            $c->setMensaje($texto);
            $c->setSellar($sellar);
            $c->setFirma($firma);
            $c->setIddatos($iddatos);
            $c->setSid($sessionid);
            $insertado = $cc->actualizarComunicado($c);
            if ($insertado) {
                echo $insertado;
            }
            break;
        case 'eliminarcomunicado':
            //$ca = new ControladorComunicado();
            $idcomunicado = $_POST['idcomunicado'];
            $eliminado = $cc->eliminarComunicado($idcomunicado);
            if ($eliminado) {
                echo "1Registro eliminado";
            }
            break;
        case 'fecha':
            //$ca = new ControladorComunicado();
            $fecha = $cc->getFecha();
            if ($fecha) {
                echo $fecha;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
        case 'opcionescategoria':
            //$cf = new ControladorComunicado();
            $datos = $cc->opcionesCategoria();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay categorias registradas";
            }
            break;
        case 'loadcategoria':
            $idcategoria = $_POST['idcategoria'];
            //$cf = new ControladorComunicado();
            $datos = $cc->getCategoriaById($idcategoria);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay categorias registradas";
            }
            break;
        case 'loadcontactos':
            //$cc = new ControladorComunicado();
            $datos = $cc->getContactoByCat();
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay categorias registradas";
            }
            break;
        case 'imgscom':
            $datos = "";
            //$cf = new ControladorComunicado();
            $sid = session_id();
            $tag = $_POST['tag'];
            
            $datos = $cc->getImgsComunicado($sid, $tag);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'tablaimg':
                $datos = "";
                $idtmp = session_id();
                $datos = $cc->tablaIMG($idtmp);
                echo  $datos;
            break;
        case 'eliminarimg':
            if (isset($_POST['idtmp'])) {
                $idtmp = $_POST['idtmp'];
                //$cf = new ControladorComunicado();
                $resultado = $cc->eliminarIMG($idtmp);
                echo 'archivo eliminada'; 
            } 
            break;
        case 'cancelar':
                //$cf = new ControladorComunicado();
                $idtmp = session_id();
                $eliminado = $cc->deleteImgTmp($idtmp);
                if ($eliminado) {
                    echo "1canelado";
                } 
                break;
        case 'modaltabla':
                    $datos = "";
                    $tag = $_POST['tag'];
                    $datos =$cc->getIMGList($tag); 
                    echo $datos;
                    break;
                
        
        default:
            break;
    }
}

function obtenerDatosComunicado() {
    $c = new Comunicado();
    $c->setChcom($_POST['chcom']);
    $c->setIdcontactos($_POST['idcontactos']);
    $c->setAsunto($_POST['asunto']);
    $c->setEmision($_POST['emision']);
    $c->setColor($_POST['color']);
    $c->setSize($_POST['size']);
    $c->setMensaje($_POST['txtbd']);
    $c->setSellar($_POST['sellar']);
    $c->setFirma($_POST['firma']);
    $c->setIddatos($_POST['iddatos']);
    $c->setSid(session_id());
    return $c;
}