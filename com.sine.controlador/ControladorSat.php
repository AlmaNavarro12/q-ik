<?php
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorBanco.php';

class ControladorSat{
    
    private $banco;

    function __construct(){
        $this->banco = new ControladorBanco();
    }

    public function getRFCBancoOrdenante($id) {
        $rfc = "";
        $datos = $this->banco->getRFCBancoOrdAux($id);
        foreach ($datos as $actual) {
            $rfc = $actual['rfcbanco'];
        }
        return $rfc;
    }

    public function getRFCBancoBeneficiario($id) {
        $rfc = "";
        $datos = $this->banco->getRFCBancoOrdAux($id);
        foreach ($datos as $actual) {
            $rfc = $actual['rfcbanco'];
        }
        return $rfc;
    }
}