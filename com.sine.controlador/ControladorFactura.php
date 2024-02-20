<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Factura.php';

date_default_timezone_set("America/Mexico_City");

class ControladorFactura
{

    function __construct(){
    }

    public function getFecha()
    {
        $datos = "";
        $fecha = getdate();
        $d = $fecha['mday'];
        $m = $fecha['mon'];
        $y = $fecha['year'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        $datos = "$d/$m/$y";
        return $datos;
    }

    
}
