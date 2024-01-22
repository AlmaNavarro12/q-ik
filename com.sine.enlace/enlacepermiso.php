<?php

require_once '../com.sine.controlador/ControladorButton.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    switch ($transaccion) {
        case 'loadbtn':
            $cc = new ControladorButton();
            $view = $_POST['view'];
            $insertado = $cc->loadButton($view);
            echo $insertado;
            break;
        default:
            break;
    }
}
