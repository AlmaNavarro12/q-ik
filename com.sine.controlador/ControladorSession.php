<?php
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.dao/Consultas.php';

//Relacion a enlacesession.php
class ControladorSession {

    function __construct() {}

    private function autenticarUsuario($u) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE usuario=:usuario and password=:contrasena LIMIT 1;";
        $valores = array("usuario" => $u->getUsuario(), "contrasena" => $u->getContrasena());
        $c = new Consultas();
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    public function login($u) {
        $existe = false;
        $resultados = $this->autenticarUsuario($u);
        foreach ($resultados as $resultado) {
            Session::start();
            $existe = true;
            $_SESSION[sha1("usuario")] = $resultado['usuario'];
            $_SESSION[sha1("idusuario")] = $resultado['idusuario'];
            $_SESSION[sha1("tipousuario")] = $resultado['tipo'];
            $_SESSION[sha1("acceso")] = $resultado['acceso'];
            $_SESSION[sha1("paquete")] = $resultado['paquete'];
        }
        return $existe;
    }

    public function sessionIsActive() {
        $activa = false;
        Session::start();
        $existe = true;
        if (isset($_SESSION[sha1("usuario")])) {
            $activa = true;
        }
        return $activa;
    }

    public function logout($b) {
        $destruido = false;
        if ($b == 'ab125?=o9_.2') {
            Session::start();
            Session::destroy();
            $destruido = true;
        }
        return $destruido;
    }
}
