<?php

require_once '../com.sine.modelo/Instalacion.php';
require_once '../com.sine.controlador/ControladorInstalacion.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';

$ci = new ControladorInstalacion();
Session::start();

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
        case 'fecha':
            echo $fecha = $ci->getFecha();
            break;
            //--------------------------------------EQUIPO GPS
        case 'insertargps':
            $insertado = $ci->insertarGPS($_POST['nuevogps'], $_POST['idGPS']);
            echo $insertado ? $insertado : "0Error: No se pudo realizar el registro.";
            break;
        case 'eliminarGPS':
            $insertado = $ci->eliminaGPS($_POST['id']);
            echo $insertado ? "Registro eliminado correctamente." : "0Error: No se pudo eliminar el registro.";
            break;
        case 'loadListaGPS':
            $datos = $ci->listaGPS($_POST['REF'], $_POST['numreg'], $_POST['pag']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
            //---------------------------------------INSTALADORES
        case 'opcionesinstalador':
            $datos = $ci->opcionesInstalador();
            echo $datos;
            break;
        case 'getInstaladoresCH':
            $instaladores = $ci->getInstaladoresCH();
            echo $instaladores;
            break;
            //---------------------------------------INSTALACION
        case 'insertarinstalacion':
            $insertado = $ci->insertarInstalacion(obtenerDatosInstalacion());
            echo $insertado ? $insertado : "0Error: No se pudo registrar el registro.";
            break;
        case 'filtrarinstalacion':
            $datos = $ci->listaServiciosHistorial($_POST['REF'], $_POST['servicio'], $_POST['filtro'], $_POST['pag'], $_POST['numreg']);
            echo $datos != "" ? $datos : "0Ha ocurrido un error.";
            break;
        case 'editarinstalacion':
            $datos = $ci->getDatosInstalacion($_POST['idinstalacion'], $_POST['gentmp']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
        case 'eliminarinstalacion':
            $eliminado = $ci->eliminarInstalacion($_POST['iid']);
            echo $eliminado != "" ? $eliminado : "0No se ha eliminadó el resgitro.";
            break;
        case 'actualizarinstalacion':
            $i = obtenerDatosInstalacion();
            $i->setIdhojaservicio(($_POST['idorden']));
            $insertado = $ci->actualizarInstalacion($i);
            echo $insertado ? $insertado : "0Error: No se pudo actualizar el registro.";
            break;
            //-------------------------------------------TEMPORAL
        case 'checkTMP':
            echo $consultado = $ci->getTMP($_POST['idorden']);
            break;
        case 'saveStep':
            echo $update = $ci->saveStep($_POST['step'], $_POST['cve_orden']);
            break;
        case 'saveTMP':
            $insertado = $ci->saveTMP(obtenerDatosInstalacionTmp());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($insertado);
            break;
            //------------------------------------------EVIDENCIAS
        case 'tablaimg':
            $tabla = $ci->tablaIMG($_POST['idorden']);
            echo $tabla != "" ? $tabla : "0No se han encontrado datos.";
            break;
        case 'tablavid':
            $tabla = $ci->tablaVid($_POST['idorden']);
            echo $tabla != "" ? $tabla : "0No se han encontrado datos.";
            break;
        case 'getDelTmpImg':
            $eliminado = $ci->deleteTmpByName($_POST['nombre']);
            echo $eliminado;
            break;
        case 'getDelTmpVid':
            echo $eliminado = $ci->deleteTmpVidByName($_POST['nombre']);
            break;
        case 'getfilestmpotras':
            $consultado = $ci->getFilesTMPOtras($_POST['idorden']);
            echo $consultado;
            break;
        case 'getfilestmpvid':
            $consultado = $ci->getFilesTMPVid($_POST['idorden']);
            echo $consultado;
            break;
        case 'eliminarimg':
            $tabla = $ci->eliminarImgTmp($_POST['idtmp'], );
            echo $tabla != "" ? $tabla : "0No se ha eliminado la imagen.";
            break;
        case 'eliminarevidencia':
            $tabla = $ci->eliminarEvidencias($_POST['idorden'], $_POST['name'], $_POST['base']);
            echo $tabla != "" ? $tabla : "0No se ha eliminado la imagen.";
            break;
        case 'eliminarvid':
            $tabla = $ci->eliminarVidTmp($_POST['idtmp']);
            echo $tabla != "" ? $tabla : "0No se ha eliminado el video.";
            break;
        case 'deleteImgsPanic':
            echo $deleted = $ci->deleteImgsPanic($_POST['idorden']);
            break;
        case 'verifyVideoTMP':
            echo $consultado = $ci->verifyVideoTMP($_POST['paso'], $_POST['check'], $_POST['idorden']);
            break;
        case 'getvidregistrados':
            $tabla = $ci->getVidRegistrados($_POST['folio']);
            echo $tabla != "" ? $tabla : "0No se han encontrado datos.";
            break;
        case 'existeVid':
            echo $video = $ci->existVideo($_POST['video']);
            break;
            //-------------------------------------------PASOS
        case 'getStep':
            echo $update = $ci->getStep($_POST['cve_orden']);
            break;
        case 'vistaPrevia':
            echo $consultado = $ci->getVistaPrevia($_POST['idorden'], $_POST['tipo_unidad']);
            break;
        case 'finalizarOrden':
            echo $consultado = $ci->finalizarOrden($_POST['idorden']);
            break;
        case 'guardarfirma':
            $i = new Instalacion();
            $i->setIdhojaservicio($_POST['idorden']);
            $i->setEncargado($_POST['encargado']);
            $i->setFirma($_POST['firma']);
            $insertado = $ci->actualizarFirma($i);
            echo $insertado ? $insertado : "0Error: No se pudo realizar el registro.";
            break;
            //-------------------------------------------CORREOS
        case 'getcorreos':
            $datos = $ci->getCorreo($_POST['idorden']);
            echo $datos != "" ? $datos : "0No se han encontrado datos.";
            break;
            //-------------------------------------------CANCELACION 
        case 'cancelInst':
            $cancelado = $ci->cancelarInstalacion($_POST['id'], $_POST['motivo']);
            echo $cancelado != "" ? "1Instalación cancelada." : "0Error: no se pudo cancelar la instalación.";
            break;
        case 'showcancel':
            $data = $ci->showCancel($_POST['id']);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
            break;
    }
}

function obtenerDatosInstalacion()
{
    $i = new Instalacion();
    $i->setFolio($_POST['folio']);
    $i->setFechaservicio($_POST['fechaservicio']);
    $i->setHoraservicio($_POST['horaservicio']);
    $i->setIdcliente($_POST['idcliente']);
    $i->setNombrecliente($_POST['nombrecliente']);
    $i->setPlataforma($_POST['plataforma']);
    $i->setMarca($_POST['marca']);
    $i->setModelo($_POST['modelo']);
    $i->setAnho($_POST['anho']);
    $i->setColor($_POST['color']);
    $i->setSerie($_POST['serie']);
    $i->setNumeconomico($_POST['numeconomico']);
    $i->setKm($_POST['km']);
    $i->setPlacas($_POST['placas']);
    $i->setTipounidad($_POST["tipounidad"]);
    $i->setIdtservicio($_POST['idtservicio']);
    $i->setOtrostservicio($_POST['otrostservicio']);
    $i->setModeloanterior($_POST['modeloanterior']);
    $i->setImeianterior($_POST['imeianterior']);
    $i->setSimanterior($_POST['simanterior']);
    $i->setIdgpsvehiculo($_POST['gpsvehiculo']);
    $i->setImei($_POST['imei']);
    $i->setNumtelefono($_POST['numtelefono']);
    $i->setIdinstalador($_POST['idinstalador']);
    $i->setIdaccesorio($_POST['idaccesorio']);
    $i->setIdAsignacion($_POST['idasingnacion']);
    return $i;
}

function obtenerDatosInstalacionTmp()
{
    $i = new Instalacion();
    $i->setIddanhos($_POST["iddanhos"]);
    $i->setIdmolduras($_POST["idmolduras"]);
    $i->setOtrosmolduras($_POST["otrosmolduras"] ?? "");
    $i->setIdtablero($_POST["idtablero"]);
    $i->setOtrostablero($_POST["otrostablero"] ?? "");
    $i->setIdcableado($_POST["idcableado"]);
    $i->setOtroscableado($_POST["otroscableado"] ?? "");
    $i->setIdccorriente($_POST["idccorriente"]);
    $i->setOtrosccorriente($_POST["otroscorriente"] ?? "");
    $i->setIdaccesorio($_POST["idaccesorio"]);
    $i->setObservaciones($_POST["observaciones"]);
    $i->setIdinstalacion($_POST["idinstalacion"]);
    $i->setEncargado($_POST["encargado"]);
    $i->setFirma($_POST["firma"]);
    $i->setImgfrente($_POST["imgfrentevehiculo"] ?? "");
    $i->setImgnserie($_POST["imgnserie"] ?? "");
    $i->setImgtabinicial($_POST["imgtabinicial"] ?? "");
    $i->setImgtabfinal($_POST["imgtabfinal"] ?? "");
    $i->setImgAntesInstalacion($_POST["imgantesinst"] ?? "");
    $i->setImgDespuesInstalacion($_POST["imgdespuesinst"] ?? "");
    $i->setIdhojaservicio($_POST["idorden"] ?? "");
    $i->setUbicacionPanico($_POST["descUbicacion"] ?? "");
    $i->setObservacionesGral($_POST["observaciongral"] ?? "");
    $i->setTipoCorte($_POST["tipocorte"] ?? "");
    return $i;
}
