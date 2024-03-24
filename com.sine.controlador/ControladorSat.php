<?php
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorBanco.php';
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorMonedas.php';

class ControladorSat{
    
    private $banco;
    private $monedas;


    function __construct(){
        $this->banco = new ControladorBanco();
        $this->monedas = new ControladorMonedas();
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

    public function totalDivisa($total, $monedaP, $monedaF, $tcambioF = '0', $tcambioP = '0')
    {
        if ($monedaP == $monedaF) {
            $OP = bcdiv($total, '1', 2);
        } else {
            $tcambio = $this->monedas->getTipoCambio($monedaP, $monedaF, $tcambioF, $tcambioP);
            echo "tcambio: " . $tcambio . "nana";
            $OP = bcdiv($total, '1', 2) / bcdiv($tcambio, '1', 6);
        }
        return $OP;
    }
}