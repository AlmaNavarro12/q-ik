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

    public function opcionesFolios($id, $serie, $folio) {
        $datos = $this->getFoliosAux();
        $op = "";
        $check = false;
    
        foreach ($datos as $actual) {
            $consecutivo = str_pad($actual['consecutivo'], 4, '0', STR_PAD_LEFT);
    
            $selected = ($id == $actual['usofolio'] || in_array($id, explode("-", $actual['usofolio']))) ? "selected" : "";
    
            $op .= "<option class='option-folio text-start ps-5' id='folio{$actual['idfolio']}' value='{$actual['idfolio']}' $selected> Serie {$actual['serie']}-{$actual['letra']}$consecutivo</option>";
    
            if ($selected) {
                $check = true;
            }
        }
    
        if ($id == "0" && !$check) {
            $op .= "<option selected id='folio{$id}' value='{$id}'> Serie {$serie}-{$folio}</option>";
        }
    
        return $op;
    }

    private function getFoliosAux() {
        $consultado = false;
        $consulta = "SELECT * FROM folio ORDER BY serie;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }
}