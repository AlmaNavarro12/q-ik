<?php

require_once '../com.sine.dao/Consultas.php';

class ControladorOpcion {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function getCorreoListAux() {
        $consultado = false;
        $consulta = "select * from correoenvio order by correo;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesCorreoList() {
        $correos = $this->getCorreoListAux();
        $r = "";
        foreach ($correos as $actual) {
            $r = $r . "<option id='correo" . $actual['idcorreoenvio'] . "' value='" . $actual['idcorreoenvio'] . "'>" . $actual['correo'] . "</option>";
        }
        return $r;
    }

    private function getUsuariosAux() {
        $consultado = false;
        $consulta = "select * from usuario order by nombre;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesUsuario() {
        $get = $this->getUsuariosAux();
        $op = "";
        foreach ($get as $actual) {
            $idusuario = $actual['idusuario'];
            $op .= "<option id='usuario" . $idusuario . "' value='" . $idusuario . "'>" . $actual['nombre'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'] . "</option>";
        }
        return $op;
    }

    public function opcionesAno() {
        $anio_de_inicio = 2020;
        $fecha = getdate();
        $y = $fecha['year'];
        $r = "";
        foreach (range($anio_de_inicio, $y) as $x) {
            $r = $r . "<option id='ano" . $x . "' value='" . $x . "'>" . $x . "  " . "</option>";
        }
        return $r;
    }
}