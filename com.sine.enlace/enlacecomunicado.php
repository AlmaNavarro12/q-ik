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
            echo $insertado ? $insertado : "0Error: no insertó el registro.";
            break;
        case 'listacomunicado':
            $datos = $cc->listaComunicado($_POST['REF'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'editarcomunicado':
            $datos = $cc->getDatosComunicado($_POST['idcomunicado']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'actualizarcomunicado':
            $c = obtenerDatosComunicado();
            $c->setIdcomunicado($_POST['idcomunicado']);
            $c->setTag($_POST['tag']);
            $insertado = $cc->actualizarComunicado($c);
            echo $insertado ? $insertado : "0Error: no actualizó el registro.";
            break;
        case 'eliminarcomunicado':
            $eliminado = $cc->eliminarComunicado($_POST['idcomunicado']);
            echo $eliminado ? $eliminado : "0Error: no eliminó el registro.";
            break;
        case 'fecha':
            $fecha = $cc->getFecha();
            echo $fecha != "" ? $fecha : "0No se han encontrado datos.";
            break;
        case 'opcionescategoria':
            $datos = $cc->opcionesCategoria();
            echo $datos != "" ? $datos : "0No hay categorias registradas.";
            break;
        case 'loadcategoria':
            $datos = $cc->getCategoriaById($_POST['idcategoria']);
            echo $datos != "" ? $datos : "0No hay categorias registradas.";
            break;
        case 'loadcontactos':
            $datos = $cc->getContactoByCat();
            echo $datos != "" ? $datos : "0No hay categorias registradas.";
            break;
        case 'imgscom':
            $datos = $cc->getImgsComunicado(session_id(), $_POST['tag']);
            echo $datos != "" ? $datos : "0No hay imágenes o archivos registrados en este comunicado.";
            break;
        case 'tablaimg':
            $datos = $cc->tablaIMG(session_id());
            echo  $datos;
            break;
        case 'eliminarimg':
            if (isset($_POST['idtmp'])) {
                $idtmp = $_POST['idtmp'];
                $resultado = $cc->eliminarIMG($idtmp);
                echo 'Archivo eliminado.';
            }
            break;
        case 'cancelar':
            $idtmp = session_id();
            $eliminado = $cc->deleteImgTmp($idtmp);
            if ($eliminado) {
                echo "1Cancelado";
            }
            break;
        case 'modaltabla':
            $datos = "";
            $datos = $cc->getIMGList($_POST['tag']);
            echo $datos;
            break;
    }
}

function obtenerDatosComunicado()
{
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