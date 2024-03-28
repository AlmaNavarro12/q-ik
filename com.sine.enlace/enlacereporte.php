<?php

require_once '../com.sine.modelo/Reportes.php';
require_once '../com.sine.controlador/ControladorReportes.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';
Session::start();
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cf = new ControladorReportes();
    switch ($transaccion) {
        case 'buscarFactura':
            $f = new Reportes();
            $f->setFechainicio($_POST['fechainicio']);
            $f->setFechafin($_POST['fechafin']);
            $f->setIdcliente($_POST['idcliente']);
            $f->setEstado($_POST['estado']);
            $f->setDatos($_POST['datos']);
            $f->setTipo($_POST['tipo']);
            $f->setMoneda($_POST['moneda']);
            $f->setMetodopago($_POST['metodopago']);
            $f->setFormapago($_POST['formapago']);
            
            $insertado = $cf->buscarFactura($f);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0<tr><td colspan='6' class='text-center'>No hay registros entre estas fechas</td></tr>";
            }
            break;
        case 'buscarpagos':
            $f = new Reportes();
            $cf = new ControladorReportes();
            $fechainicio = $_POST['fechainicio'];
            $fechafin = $_POST['fechafin'];
            $idcliente = $_POST['idcliente'];
            $datos = $_POST['datos'];
            $moneda = $_POST['moneda'];
            $forma = $_POST['forma'];
            $f->setFechainicio($fechainicio);
            $f->setFechafin($fechafin);
            $f->setIdcliente($idcliente);
            $f->setDatos($datos);
            $f->setMoneda($moneda);
            $f->setFormapago($forma);

            $insertado = $cf->buscarPagos($f);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0<tr><td colspan='6' class='text-center'>No hay registros entre estas fechas</td></tr>";
            }
            break;
        case 'buscarventas':
            $f = new Reportes();
            $cf = new ControladorReportes();
            $fechainicio = $_POST['fechainicio'];
            $fechafin = $_POST['fechafin'];
            $idcliente = $_POST['idcliente'];
            $estado = $_POST['estado'];
            $datos = $_POST['datos'];
            $usuario = $_POST['usuario'];

            $f->setFechainicio($fechainicio);
            $f->setFechafin($fechafin);
            $f->setIdcliente($idcliente);
            $f->setEstado($estado);
            $f->setDatos($datos);
            $f->setUsuario($usuario);
            
            $insertado = $cf->buscarVentas($f);
            if ($insertado) {
                echo $insertado;
            } else {
                echo "0<tr><td colspan='6' class='text-center'>No hay registros entre estas fechas</td></tr>";
            }
            break;
        case 'datosgrafica':
            $cf = new ControladorReportes();

            $iddatos = $_POST['iddatos'];
            $y = $_POST['y'];
            $m = $_POST['m'];

            if ($m < 10) {
                $m = "0$m";
            }

            $datos = $cf->getDatos($iddatos, $y, $m);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay clientes registrados";
            }
            break;
        case 'getactualestado':
            $cf = new ControladorReportes();

            $iddatos = $_POST['iddatos'];
            $y = $_POST['y'];
            $m = $_POST['m'];
            $status = $_POST['status'];
            if ($m < 10) {
                $m = "0$m";
            }

            $datos = $cf->getDatosActualEstado($iddatos, $y, $m, $status);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay clientes registrados";
            }
            break;
        case 'datosbimestre':
            $cf = new ControladorReportes();

            $y = $_POST['y'];
            $bim = $_POST['bim'];
            $fiscales = $_POST['datos'];
            
            $datos = $cf->getDatosBimestral($y, $bim, $fiscales);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay clientes registrados";
            }
            break;
        case 'filtrariva':
            $cf = new ControladorReportes();

            $emisor = $_POST['emisor'];
            $receptor = $_POST['receptor'];
            $ano = $_POST['ano'];
            $mes = $_POST['mes'];

            $datos = $cf->listaIVAHistorial($emisor, $receptor, $ano, $mes);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay clientes registrados";
            }
            break;
        case 'eliminarregistro':
            $cf = new ControladorReportes();

            $uuid = $_POST['uuid'];

            $eliminar = $cf->eliminarRegistro($uuid);
            if ($eliminar != "") {
                echo $eliminar;
            } else {
                echo "0No hay clientes registrados";
            }
            break;
        case 'imprimirimg':
            $cf = new ControladorReportes();
            $dataactual = $_POST['dataactual'];
            $datapasado = $_POST['datapasado'];
            $dataantep = $_POST['dataantep'];

            $datos = $cf->saveIMG($dataactual, $datapasado, $dataantep);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No hay clientes registrados";
            }
            break;
        default:
            break;
    }
}
