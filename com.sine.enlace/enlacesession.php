<?php
require_once '../com.sine.controlador/ControladorSession.php';
require_once '../com.sine.modelo/Usuario.php';

if (!isset($_POST['transaccion'])) {
    header("Location: ../");
    exit();
}

//Enlace a controladorsession
$transaccion = $_POST['transaccion'];
$cs = new ControladorSession();

switch ($transaccion) {
    case 'login':
    case 'loginget':
        $usuario = new Usuario();
        $usuario->setUsuario($_POST['usuario']);
        $usuario->setContrasena(sha1($_POST['contrasena']));
        $existe = $cs->login($usuario);
        echo $existe ? sha1("holamundo") : "0Usuario o contraseña inválidos";
        break;
    case 'logout':
        echo $cs->logout('ab125?=o9_.2') ? "salir" : "0Ha ocurrido un error";
        break;
    case 'validarsession':
        echo $cs->sessionIsActive() ? '1' : '0';
        break;
}
