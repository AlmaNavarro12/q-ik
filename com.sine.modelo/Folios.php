<?php

class Folio {

    private $idfolio;
    private $serie;
    private $letra;
    private $numinicio;
    private $usofolio;
    private $iddatos;
    private $actualizarinicio;

    function __construct() {
        
    }

    function getIdfolio() {
        return $this->idfolio;
    }

    function getSerie() {
        return $this->serie;
    }

    function getLetra() {
        return $this->letra;
    }

    function getNuminicio() {
        return $this->numinicio;
    }

    function getUsofolio() {
        return $this->usofolio;
    }

    function setIdfolio($idfolio) {
        $this->idfolio = $idfolio;
    }

    function setSerie($serie) {
        $this->serie = $serie;
    }

    function setLetra($letra) {
        $this->letra = $letra;
    }

    function setNuminicio($numinicio) {
        $this->numinicio = $numinicio;
    }

    function setUsofolio($usofolio) {
        $this->usofolio = $usofolio;
    }
    
    function getIddatos() {
        return $this->iddatos;
    }

    function setIddatos($iddatos) {
        $this->iddatos = $iddatos;
    }
    
    function getActualizarinicio() {
        return $this->actualizarinicio;
    }

    function setActualizarinicio($actualizarinicio) {
        $this->actualizarinicio = $actualizarinicio;
    }

}
