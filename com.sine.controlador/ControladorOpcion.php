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
            $bancoCuentaPairs = [];
            for ($i = 0; $i < 4; $i++) {
                $idbanco = $clienteactual["idbanco" . ($i == 0 ? '' : $i)];
                $cuenta = $clienteactual["cuenta" . ($i == 0 ? '' : $i)];
    
                if ($idbanco != '0') {
                    $banco = $this->getNomBanco($idbanco);
                    $bancoCuentaPairs[] = "$banco - Cuenta: $cuenta";
                }
            }
            $r .= "<option value='" . ($i + 1) . "'>" . implode("</option><option value='" . ($i + 1) . "'>", $bancoCuentaPairs) . "</option>";
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
            $bancoCuentaPairs = [];
            for ($i = 0; $i < 4; $i++) {
                $idbanco = $actual["idbanco" . ($i == 0 ? '' : $i)];
                $cuenta = $actual["cuenta" . ($i == 0 ? '' : $i)];
                
                if ($idbanco != '0') {
                    $banco = $this->getNomBanco($idbanco);
                    $bancoCuentaPairs[] = "$banco - Cuenta: $cuenta";
                }
            }
            $r .= "<option value='" . ($i + 1) . "'>" . implode("</option><option value='" . ($i + 1) . "'>", $bancoCuentaPairs) . "</option>";
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

    private function getMotivosAux() {
        $consultado = false;
        $consulta = "SELECT * FROM catalogo_motivo order by clvmotivo;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesMotivo() {
        $get = $this->getMotivosAux();
        $op = "";
        foreach ($get as $actual) {
            $clv = $actual['clvmotivo'];
            $descripcion = $actual['descripcionmotivo'];
            $op .= "<option id='motivo" . $clv . "' value='" . $clv . "'>" . $clv . " " . $descripcion . "</option>";
        }
        return $op;
    }
}