<?php

require_once '../com.sine.controlador/ControladorRegistro.php';
require_once '../com.sine.modelo/Registro.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    
    $r = new Registro();
    $cr = new ControladorRegistro();

    switch ($transaccion) {
        case 'buscarcuenta':
            $rfc = strtoupper(trim($_POST['rfc']));
            echo ($datos = $cr->getDatosCuenta($rfc)) ? $datos : "0No se han encontrado datos de este RFC en el sistema.";
            break;
        
    }
}