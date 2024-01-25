<?php

require_once '../com.sine.modelo/Usuario.php';
require_once '../com.sine.modelo/Permiso.php';
require_once '../com.sine.controlador/ControladorUsuario.php';
if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cs = new ControladorUsuario();
    switch ($transaccion) {
        case 'filtrarusuario':
            $US = $_POST['US'];
            $numreg = $_POST['numreg'];
            $pag = $_POST['pag'];
            $datos = $cs->listaServiciosHistorial($US, $numreg, $pag);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0Ah ocurrido un error";
            }
            break;
        case 'editarusuario':
            $cu = new ControladorUsuario();
            $idusuario = $_POST['idusuario'];
            $datos = $cu->getDatosUsuario($idusuario);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'insertrausuario':
            $u = new Usuario();
            $cu = new ControladorUsuario();
            $nombre = $_POST['nombre'];
            $apellidopaterno = $_POST['apellidopaterno'];
            $apellidomaterno = $_POST['apellidomaterno'];
            $usuario = $_POST['usuario'];
            $contrasena = sha1($_POST['password']);
            $correo = $_POST['correo'];
            $celular = $_POST['celular'];
            $telefono = $_POST['telefono'];
            $tipou = $_POST['tipo'];
            $img = $_POST["img"];

            $u->setNombre($nombre);
            $u->setApellidoPaterno($apellidopaterno);
            $u->setApellidoMaterno($apellidomaterno);
            $u->setUsuario($usuario);
            $u->setContrasena($contrasena);
            $u->setCorreo($correo);
            $u->setCelular($celular);
            $u->setTelefono($telefono);
            $u->setEstatus("activo");
            $u->setTipo($tipou);
            $u->setImg($img);

            $insertado = $cu->nuevoUsuario($u);
            if (!$insertado) {
                echo "Registro Insertado";
            } else {
                echo "0Error: no inserto el registro ";
            }
            break;
        case 'eliminarusuario':
                $cu = new ControladorUsuario();
                $idusuario = $_POST['idusuario'];
                $eliminado = $cu->quitarUsuario($idusuario);
                if ($eliminado) {
                    echo "Registro eliminado";
                } else {
                    echo "0No se han econtrado datos";
                }
                break;
    }
}
