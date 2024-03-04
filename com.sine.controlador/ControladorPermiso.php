<?php

require_once './com.sine.dao/Consultas.php';
require_once './com.sine.dao/ConsultasSine.php';

class ControladorPermiso {

    private $consultas;
    private $consultasSine;

    function __construct() {
        $this->consultas = new Consultas();
        $this->consultasSine = new ConsultasSine();
    }

    public function getAcceso($aid) {
        $consulta = "SELECT modulo FROM paquete WHERE idpaquete = :aid;";
        $valores = array("aid" => $aid);
        $resultados = $this->consultasSine->getResults($consulta, $valores);
        $modulos = "1-2-3-4-5-6-7-8-9-10-11-12";
        if ($resultados) {
            foreach ($resultados as $actual) {
                $modulos = $actual["modulo"];
            }
            return "$modulos";
        } else {
            $modulos = "0"; 
        }
        return $modulos;
    }
    
    
    private function getPermisoById() {
        $idusuario = $_SESSION[sha1("idusuario")];
        $consultado = false;
        $consulta = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.imgperfil, u.acceso, u.fecharegistro, u.paquete FROM usuariopermiso p INNER JOIN usuario u ON (p.permiso_idusuario=u.idusuario) WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getPermisos() {
        $datos = "";
        $search = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
        $replace = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&ntilde;', '&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;');
        
        foreach ($this->getPermisoById() as $usuarioactual) {
            $uid = $usuarioactual['permiso_idusuario'];
            $nombreusuario = str_replace($search, $replace, $usuarioactual['nombre'] . ' ' . $usuarioactual['apellido_paterno']);
            $modulos = $this->getAcceso($usuarioactual['paquete']);
    
            $fields = ['facturas', 'pago', 'nomina', 'listaempleado', 'listanomina', 'cartaporte', 'listaubicacion', 'listatransporte', 'listaremolque', 'listaoperador', 'listacarta', 'cotizacion', 'cliente', 'listacliente', 'comunicado', 'producto', 'proveedor', 'impuesto', 'datosfacturacion', 'contrato', 'listausuario', 'reporte', 'reportefactura', 'reportepago', 'reportegrafica', 'reporteiva', 'datosiva', 'reporteventa', 'configuracion', 'ventas', 'crearventa', 'listaventa', 'registrarentrada', 'registrarsalida', 'acceso', 'imgperfil'];
            $datos .= "$uid</tr>$nombreusuario</tr>";
    
            foreach ($fields as $field) {
                $datos .= "{$usuarioactual[$field]}</tr>";
            }
            $datos .= "$modulos</tr>";
        }
        return $datos;
    }
    
    private function countNotificacionAux() {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion where readed=:readed;";
        $val = array("readed" => '0');
        $consultado =  $this->consultas->getResults($consulta, $val);
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
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getNotificacion() {
        $count = 0;
        $datos = "";
        $notificaciones = $this->getNotificacionAux();
        
        foreach ($notificaciones as $actual) {
            $id = $actual['idnotificacion'];
            $fecha = $this->formatFecha($actual['fechanot']);
            $hora = $actual['horanot'];
            $notificacion = substr($actual['notificacion'], 0, 40);
            $read = $actual['readed'];
            $unread = ($read == '0') ? "not-unread" : "";
            $marker = $unread ? "class='alert-marker-active'" : "";
            
            $msg = "<span class='mt-0 mx-0 px-0'>$fecha $hora <br> $notificacion... </span>";
            $datos .= "<li class='px-2 py-2 $unread'><a data-bs-toggle='modal' data-bs-target='#modal-notification' onclick='getNotification($id)' class='notification-link px-0'> <div $marker></div> $msg </a></li>";
            $count++;
        }
    
        $datos .= ($count == 0) ? "<li><a class='notification-link'>No hay notificaciones</a></li>" : "";
        $datos .= "<corte>" . $this->countNotificacion();
    
        return $datos;
    }    
    
    private function formatFecha($fecha) {
        $div = explode("-", $fecha);
        $mes = $this->translateMonth($div[1]);
        return $div[2] . "/" . $mes . "/" . $div[0];
    }

    private function translateMonth($m){
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $m = intval($m);
        return (array_key_exists($m - 1, $months)) ? $months[$m - 1] : "";
    }
}