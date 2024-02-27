<?php
require_once '../com.sine.controlador/ControladorVenta.php';
require_once '../com.sine.modelo/Session.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cv = new ControladorVenta();
    switch ($transaccion) {
        case 'newventa':
            $ticket = $cv->loadNewTicket($_POST['ticket']);
            echo $ticket ? $ticket : "0No se han encontrado datos.";
            break;
    }
}
