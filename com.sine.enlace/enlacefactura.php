<?php

require_once '../com.sine.modelo/Factura.php';
require_once '../com.sine.modelo/Pago.php';
require_once '../com.sine.controlador/ControladorFactura.php';
require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Session.php';
Session::start();
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    
    $ca = new ControladorFactura();

    switch ($transaccion) {
        case 'fecha':
            $fecha = $ca->getFecha();
            if ($fecha) {
                echo $fecha;
            } else {
                echo "0No se han encontrado datos";
            }
            break;
    }
}
