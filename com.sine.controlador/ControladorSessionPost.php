<?php
require_once 'com.sine.modelo/Session.php';
require_once 'com.sine.dao/Consultas.php';

#Relación a enlacesession.php
class ControladorSessionPost {

    public function __construct() {}

    private function autenticarUsuarioPost($usuario, $contrasena) {
        $consulta = "SELECT * FROM usuario WHERE usuario = :usuario AND password = :contrasena LIMIT 1;";
        $valores = array("usuario" => $usuario, "contrasena" => $contrasena);
        $consultas = new Consultas();
        return $consultas->getResults($consulta, $valores);
    }

    public function loginPost($usuario, $contrasena) {
        $resultados = $this->autenticarUsuarioPost($usuario, $contrasena);
        foreach ($resultados as $resultado) {
            Session::start();
            $_SESSION[sha1("usuario")] = $resultado['usuario'];
            $_SESSION[sha1("idusuario")] = $resultado['idusuario'];
            $_SESSION[sha1("tipousuario")] = $resultado['tipo'];
            return true;
        }
        return false;
    }

    public function sessionIsActive() {
        Session::start();
        return isset($_SESSION[sha1("usuario")]);
    }

    public function logout($clave) {
        if ($clave == 'ab125?=o9_.2') {
            Session::start();
            Session::destroy();
            return true;
        }
        return false;
    }
}