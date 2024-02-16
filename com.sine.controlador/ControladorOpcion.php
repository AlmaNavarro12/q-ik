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

    public function opcionesBancobyCliente($idcliente) {
        $cliente = $this->getClienteID($idcliente);
        $r = "";
        foreach ($cliente as $clienteactual) {
            $idbanco = $clienteactual['idbanco'];
            $cuenta = $clienteactual['cuenta'];
            $idbanco1 = $clienteactual['idbanco1'];
            $cuenta1 = $clienteactual['cuenta1'];
            $idbanco2 = $clienteactual['idbanco2'];
            $cuenta2 = $clienteactual['cuenta2'];
            $idbanco3 = $clienteactual['idbanco3'];
            $cuenta3 = $clienteactual['cuenta3'];
        }
        $banco = $this->getNomBanco($idbanco);
        if ($idbanco != '0') {
            $banco = $this->getNomBanco($idbanco);
            $r .= "<option value='1'>" . $banco . " - Cuenta:" . $cuenta . "</option>";
        }

        if ($idbanco1 != '0') {
            $banco1 = $this->getNomBanco($idbanco1);
            $r .= "<option value='2'>" . $banco1 . " - Cuenta:" . $cuenta1 . "</option>";
        }

        if ($idbanco2 != '0') {
            $banco2 = $this->getNomBanco($idbanco2);
            $r .= "<option value='3'>" . $banco2 . " - Cuenta:" . $cuenta2 . "</option>";
        }

        if ($idbanco3 != '0') {
            $banco3 = $this->getNomBanco($idbanco3);
            $r .= "<option value='4'>" . $banco3 . " - Cuenta:" . $cuenta3 . "</option>";
        }

        return $r;
    }

    private function getClienteID($idcliente) {
        $consultado = false;
        $consulta = "select * from cliente where id_cliente=:cid;";
        $val = array("cid" => $idcliente);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getNombancoaux($idbanco) {
        $consultado = false;
        $consulta = "select nombre_banco from catalogo_banco where idcatalogo_banco=:bid;";
        $val = array("bid" => $idbanco);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getNomBanco($idbanco) {
        $banco = $this->getNombancoaux($idbanco);
        $nombre = "";
        foreach ($banco as $bactual) {
            $nombre = $bactual['nombre_banco'];
        }
        return $nombre;
    }

    public function opcionesBeneficiario($iddatos) {
        $datos = $this->getDatosFacturacionbyID($iddatos);
        $r = "";
        foreach ($datos as $actual) {
            $idbanco = $actual['idbanco'];
            $cuenta = $actual['cuenta'];
            $idbanco1 = $actual['idbanco1'];
            $cuenta1 = $actual['cuenta1'];
            $idbanco2 = $actual['idbanco2'];
            $cuenta2 = $actual['cuenta2'];
            $idbanco3 = $actual['idbanco3'];
            $cuenta3 = $actual['cuenta3'];
        }
        $banco = $this->getNomBanco($idbanco);
        if ($idbanco != '0') {
            $banco = $this->getNomBanco($idbanco);
            $r .= "<option value='1'>" . $banco . " - Cuenta:" . $cuenta . "</option>";
        }
        if ($idbanco1 != '0') {
            $banco1 = $this->getNomBanco($idbanco1);
            $r .= "<option value='2'>" . $banco1 . " - Cuenta:" . $cuenta1 . "</option>";
        }
        if ($idbanco2 != '0') {
            $banco2 = $this->getNomBanco($idbanco2);
            $r .= "<option value='3'>" . $banco2 . " - Cuenta:" . $cuenta2 . "</option>";
        }
        if ($idbanco3 != '0') {
            $banco3 = $this->getNomBanco($idbanco3);
            $r .= "<option value='4'>" . $banco3 . " - Cuenta:" . $cuenta3 . "</option>";
        }

        return $r;
    }

    private function getDatosFacturacionbyID($id) {
        $consultado = false;
        $consulta = "select * from datos_facturacion where id_datos=:id";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getDatosFacturacion() {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion order by nombre_contribuyente;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesDatFacturacion($id = "") {
        $cliente = $this->getDatosFacturacion();
        $r = "";
        foreach ($cliente as $clienteactual) {
            $selected = "";
            if($id == $clienteactual['id_datos']){
                $selected = "selected";
            }
            $r .= "<option $selected value='" . $clienteactual['id_datos'] . "'>" . $clienteactual['nombre_contribuyente'] . "</option>";
        }
        return $r;
    }
}