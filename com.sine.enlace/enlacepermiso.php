<?php
require_once '../com.sine.controlador/ControladorButton.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cc = new ControladorButton();

    switch ($transaccion) {
        case 'loadbtn':
            $view = $_POST['view'];
            $insertado = $cc->loadButton($view);
            echo $insertado;
            break;
    }
}