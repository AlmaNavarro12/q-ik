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
        $datos = $this->getFoliosAux($id);
        $op = "";
    	$check = false;
        foreach ($datos as $actual) {
            $idfolio = $actual['idfolio'];
            $consecutivo = $actual['consecutivo'];
            $iduso = $actual['usofolio'];

            if ($consecutivo < 10) {
                $consecutivo = "000$consecutivo";
            } else if ($consecutivo < 100 && $consecutivo >= 10) {
                $consecutivo = "00$consecutivo";
            } else if ($consecutivo < 1000 && $consecutivo >= 100) {
                $consecutivo = "0$consecutivo";
            }

            $divuso = explode("-", $iduso);
            $selected = "";
            foreach ($divuso as $uso) {
                if ($id == $uso) {
                    $selected = "selected";
                	$check = true;
                    break;
                }
            }

            $op .= "<option class='option-folio' id='folio" . $idfolio . "' value='" . $idfolio . "' $selected> Serie " . $actual['serie'] . "-" . $actual['letra'] . $consecutivo . "</option>";
        }
    	if ($id == "0" && !$check) {
            $op .= "<option selected id='folio" . $id . "' value='" . $id . "'> Serie " . $serie . "-" . $folio . "</option>";
        }
        return $op;
    }

    private function getFoliosAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM folio WHERE usofolio =:uso ORDER BY serie;";
        $valores = array("uso" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function opcionesBancobyCliente($idcliente) {
        $datos = $this->getClienteID($idcliente);
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
        
        if ($idbanco != '0') {
            $r .= "<option value='1'>" . $actual['nombre_banco']. ": " . $cuenta . "</option>";
        }
        if ($idbanco1 != '0') {
            $r .= "<option value='2'>" . $actual['nombre_banco1'] . ": " . $cuenta1 . "</option>";
        }
        if ($idbanco2 != '0') {
            $r .= "<option value='3'>" . $actual['nombre_banco2'] . ": " . $cuenta2 . "</option>";
        }
        if ($idbanco3 != '0') {
            $r .= "<option value='4'>" . $actual['nombre_banco3'] . ": " . $cuenta3 . "</option>";
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
        
        if ($idbanco != '0') {
            $r .= "<option value='1'>" . $actual['nombre_banco']. ": " . $cuenta . "</option>";
        }
        if ($idbanco1 != '0') {
            $r .= "<option value='2'>" . $actual['nombre_banco1'] . ": " . $cuenta1 . "</option>";
        }
        if ($idbanco2 != '0') {
            $r .= "<option value='3'>" . $actual['nombre_banco2'] . ": " . $cuenta2 . "</option>";
        }
        if ($idbanco3 != '0') {
            $r .= "<option value='4'>" . $actual['nombre_banco3'] . ": " . $cuenta3 . "</option>";
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

    private function getProveedor() {
        $consultado = false;
        $consulta = "select * from proveedor order by empresa";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesProveedor($idprov = "") {
        $proveedor = $this->getProveedor();
        $r = "";
        foreach ($proveedor as $proveedoractual) {
            $selected = "";
            if($idprov == $proveedoractual['idproveedor']){
                $selected = "selected";
            }
            $r = $r . "<option $selected value='" . $proveedoractual['idproveedor'] . "' id='proveedor" . $proveedoractual['idproveedor'] . "'>" . $proveedoractual['empresa'] . "</option>";
        }
        return $r;
    }

    public function opcionesAnoGlobal() {
        $fecha = getdate();
        $y = $fecha['year'];
        $anio_de_inicio = $y-1;
        $op = "";
        foreach (range($anio_de_inicio, $y) as $x) {
            $op .= "<option id='ano" . $x . "' value='" . $x . "'>" . $x . "  " . "</option>";
        }
        return $op;
    }
}