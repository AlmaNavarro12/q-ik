<?php

require_once './com.sine.dao/Consultas.php';

class ControladorPermiso {

    function __construct() {
    }

    public function getAcceso($aid) {
        $modulos = "1-2-5-6-7-8-9-10-11-12";
        $servidor = "localhost";
        $basedatos = "sineacceso";
        $puerto = "3306";
        $mysql_user = "root";
        $mysql_password = "";
        try {
            $db = new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos", $mysql_user, $mysql_password);
            $stmttable = $db->prepare("SELECT * FROM paquete WHERE idpaquete='$aid'");

            if ($stmttable->execute()) {
                $resultado = $stmttable->fetchAll(PDO::FETCH_ASSOC);
                foreach ($resultado as $actual) {
                    $modulos = $actual["modulo"];
                }
                return "$modulos";
            } else {
                return "0";
            }
        } catch (PDOException $ex) {
            echo '<e>No se puede conectar a la bd ' . $ex->getMessage();
        }
    }

    private function getPermisoById() {
        $idusuario = $_SESSION[sha1("idusuario")];
        $consultado = false;
        $consulta = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.imgperfil, u.acceso, u.fecharegistro, u.paquete FROM usuariopermiso p INNER JOIN usuario u ON (p.permiso_idusuario=u.idusuario) WHERE permiso_idusuario=:idusuario;";
        $c = new Consultas();
        $valores = array("idusuario" => $idusuario);
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    public function getPermisos() {
        $datos = "";
        $permisos = $this->getPermisoById();
        foreach ($permisos as $usuarioactual) {
            $uid = $usuarioactual['permiso_idusuario'];
            $nombreusuario = $usuarioactual['nombre'] . ' ' . $usuarioactual['apellido_paterno'];
            $facturas = $usuarioactual['facturas'];
            $pago = $usuarioactual['pago'];
            $nomina = $usuarioactual['nomina'];
            $listaempleado = $usuarioactual['listaempleado'];
            $listanomina = $usuarioactual['listanomina'];
            $cartaporte = $usuarioactual['cartaporte'];
            $listaubicacion = $usuarioactual['listaubicacion'];
            $listatransporte = $usuarioactual['listatransporte'];
            $listaremolque = $usuarioactual['listaremolque'];
            $listaoperador = $usuarioactual['listaoperador'];
            $listacarta = $usuarioactual['listacarta'];
            $cotizacion = $usuarioactual['cotizacion'];
            $cliente = $usuarioactual['cliente'];
            $listacliente = $usuarioactual['listacliente'];
            $comunicado = $usuarioactual['comunicado'];
            $producto = $usuarioactual['producto'];
            $proveedor = $usuarioactual['proveedor'];
            $impuesto = $usuarioactual['impuesto'];
            $datosfacturacion = $usuarioactual['datosfacturacion'];
            $contrato = $usuarioactual['contrato'];
            $listausuario = $usuarioactual['listausuario'];
            $reporte = $usuarioactual['reporte'];
            $reportefactura = $usuarioactual['reportefactura'];
            $reportepago = $usuarioactual['reportepago'];
            $reportegrafica = $usuarioactual['reportegrafica'];
            $reporteiva = $usuarioactual['reporteiva'];
            $datosiva = $usuarioactual['datosiva'];
            $reporteventa = $usuarioactual['reporteventa'];
            $configuracion = $usuarioactual['configuracion'];
            $ventas = $usuarioactual['ventas'];
            $acceso = $usuarioactual['acceso'];
            $paquete = $usuarioactual['paquete'];
            $imgperfil = $usuarioactual['imgperfil'];
            
            $modulos = $this->getAcceso($paquete);
            
            $search = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
            $replace = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&ntilde;', '&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;');
            $nombreusuario = str_replace($search, $replace, $nombreusuario);

            $datos .= "$uid</tr>$nombreusuario</tr>$facturas</tr>$pago</tr>$nomina</tr>$listaempleado</tr>$listanomina</tr>$cartaporte</tr>$listaubicacion</tr>$listatransporte</tr>$listaremolque</tr>$listaoperador</tr>$listacarta</tr>$cotizacion</tr>$cliente</tr>$listacliente</tr>$comunicado</tr>$producto</tr>$proveedor</tr>$impuesto</tr>$datosfacturacion</tr>$contrato</tr>$listausuario</tr>$reporte</tr>$reportefactura</tr>$reportepago</tr>$reportegrafica</tr>$reporteiva</tr>$datosiva</tr>$reporteventa</tr>$configuracion</tr>$ventas</tr>$acceso</tr>$imgperfil</tr>$modulos";
        }
        return $datos;
    }

    private function countNotificacionAux() {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion where readed=:readed;";
        $c = new Consultas();
        $val = array("readed" => '0');
        $consultado = $c->getResults($consulta, $val);
        return $consultado;
    }

    private function countNotificacion() {
        $count = 0;
        $notification = $this->countNotificacionAux();
        foreach ($notification as $actual) {
            $count++;
        }
        return $count;
    }

    private function getNotificacionAux() {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion order by idnotificacion desc limit 5;";
        $c = new Consultas();
        $consultado = $c->getResults($consulta, null);
        return $consultado;
    }

    public function getNotificacion() {
        $count = 0;
        $num = $this->countNotificacion();
        $datos = "";
        $notificacion = $this->getNotificacionAux();
        foreach ($notificacion as $actual) {
            $id = $actual['idnotificacion'];
            $fecha = $actual['fechanot'];
            $hora = $actual['horanot'];
            $notificacion = $actual['notificacion'];
            $read = $actual['readed'];
            $unread = "";
            $marker = "";
            if ($read == '0') {
                $unread = "not-unread";
                $marker = "class='alert-marker-active'";
            }
            $div = explode("-", $fecha);
            $mes = $this->translateMonth($div[1]);
            $date = $div[2] . "/" . $mes . "/" . $div[0];
            $notificacion = substr($notificacion, 0, 40);
            $msg = "$date $hora<br/> $notificacion...";

            $datos .= "<li><a data-bs-toggle='modal' data-bs-target='#modal-notification' onclick='getNotification($id)' class='notification-link $unread'><div $marker></div> $msg </a></li>";
            $count++;
        }
        if ($count == 0) {
            $datos .= "<li><a class='notification-link '>No hay notificaciones </a></li>";
        }
        $datos .= "<corte>$num";
        return $datos;
    }

    public function translateMonth($m) {
        switch ($m) {
            case '01':
                $mes = 'Ene';
                break;
            case '02':
                $mes = 'Feb';
                break;
            case '03':
                $mes = "Mar";
                break;
            case '04':
                $mes = 'Abr';
                break;
            case '05':
                $mes = 'May';
                break;
            case '06':
                $mes = 'Jun';
                break;
            case '07':
                $mes = 'Jul';
                break;
            case '08':
                $mes = 'Ago';
                break;
            case '09':
                $mes = 'Sep';
                break;
            case '10':
                $mes = 'Oct';
                break;
            case '11':
                $mes = 'Nov';
                break;
            case '12':
                $mes = 'Dic';
                break;
            default :
                $mes = "";
                break;
        }
        return $mes;
    }

}
