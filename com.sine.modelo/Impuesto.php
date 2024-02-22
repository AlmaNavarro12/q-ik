<?php

class Impuesto {

    private $idimpuesto;
    private $nombre;
    private $tipo;
    private $impuesto;
    private $factor;
    private $tipotasa;
    private $tasa;
    private $chuso;

    function __construct() {
        
    }
    
    function getIdimpuesto() {
        return $this->idimpuesto;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getImpuesto() {
        return $this->impuesto;
    }

    function getFactor() {
        return $this->factor;
    }

    function getTipotasa() {
        return $this->tipotasa;
    }

    function getTasa() {
        return $this->tasa;
    }

    function getChuso() {
        return $this->chuso;
    }

    function setIdimpuesto($idimpuesto) {
        $this->idimpuesto = $idimpuesto;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setImpuesto($impuesto) {
        $this->impuesto = $impuesto;
    }

    function setFactor($factor) {
        $this->factor = $factor;
    }

    function setTipotasa($tipotasa) {
        $this->tipotasa = $tipotasa;
    }

    function setTasa($tasa) {
        $this->tasa = $tasa;
    }

    function setChuso($chuso) {
        $this->chuso = $chuso;
    }
    
}
