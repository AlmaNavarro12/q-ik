<?php
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorBanco.php';
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorMonedas.php';
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorImpuestos.php'; 

class ControladorSat{
    
    private $banco;
    private $catalogoimpuestos;


    function __construct(){
        $this->banco = new ControladorBanco();
        $this->monedas = new ControladorMonedas();
        $this->catalogoimpuestos = new ControladorImpuestos();
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
            $OP = bcdiv($total, '1', 2) / bcdiv($tcambio, '1', 6);
        }
        return $OP;
    }

    public function checkImpuestoAux($cf) {
        $valido = false;
        $impuestos = $this->catalogoimpuestos->getPorcentajesAux($cf->getTipo(), $cf->getImpuesto(), $cf->getFactor());
        foreach ($impuestos as $actual) {
            $tipo = $actual['tipo'];
            if ($tipo == 'rango') {
                $min = $actual['minimo'];
                $max = $actual['maximo'];
                if ($cf->getTasa() < $min || $cf->getTasa() > $max) {
                    $valido = TRUE;
                    echo "0El valor ingresado esta fuera de rango";
                }
            }
        }
        return $valido;
    }

    public function getPorcentajes($tipo, $impuesto, $factor) {
        $datos = "";
        $tipoimp = "";
        $porcentajes = $this->catalogoimpuestos->getPorcentajesAux($tipo, $impuesto, $factor);
        foreach ($porcentajes as $actual) {
            $tipoimp = $actual['tipo'];
            $min = $actual['minimo'];
            $max = $actual['maximo'];
            if ($tipoimp == 'fijo') {
                $datos .= "<option value='" . $max . "'>" . $max . "</option>";
            } else if ($tipoimp == 'rango') {
                $datos .= "$min</tr>$max";
            }
        }
        return $tipoimp . "</tr>" . $datos;
    }
}