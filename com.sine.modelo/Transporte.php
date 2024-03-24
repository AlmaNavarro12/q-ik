<?php

class Transporte {

    private $idtransporte;
    private $nombre;
    private $numpermiso;
    private $tipopermiso;
    private $conftransporte;
    private $anhomodelo;
    private $placavehiculo;
    private $segurorc;
    private $polizarc;
    private $seguroma;
    private $polizama;
    private $segurocg;
    private $polizacg;

    function __construct() {
        
    }
    
    function getIdtransporte() {
        return $this->idtransporte;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getNumpermiso() {
        return $this->numpermiso;
    }

    function getTipopermiso() {
        return $this->tipopermiso;
    }

    function getConftransporte() {
        return $this->conftransporte;
    }

    function getAnhomodelo() {
        return $this->anhomodelo;
    }

    function getPlacavehiculo() {
        return $this->placavehiculo;
    }

    function getSegurorc() {
        return $this->segurorc;
    }

    function getPolizarc() {
        return $this->polizarc;
    }

    function getSeguroma() {
        return $this->seguroma;
    }

    function getPolizama() {
        return $this->polizama;
    }

    function getSegurocg() {
        return $this->segurocg;
    }

    function getPolizacg() {
        return $this->polizacg;
    }

    function setIdtransporte($idtransporte) {
        $this->idtransporte = $idtransporte;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setNumpermiso($numpermiso) {
        $this->numpermiso = $numpermiso;
    }

    function setTipopermiso($tipopermiso) {
        $this->tipopermiso = $tipopermiso;
    }

    function setConftransporte($conftransporte) {
        $this->conftransporte = $conftransporte;
    }

    function setAnhomodelo($anhomodelo) {
        $this->anhomodelo = $anhomodelo;
    }

    function setPlacavehiculo($placavehiculo) {
        $this->placavehiculo = $placavehiculo;
    }

    function setSegurorc($segurorc) {
        $this->segurorc = $segurorc;
    }

    function setPolizarc($polizarc) {
        $this->polizarc = $polizarc;
    }

    function setSeguroma($seguroma) {
        $this->seguroma = $seguroma;
    }

    function setPolizama($polizama) {
        $this->polizama = $polizama;
    }

    function setSegurocg($segurocg) {
        $this->segurocg = $segurocg;
    }

    function setPolizacg($polizacg) {
        $this->polizacg = $polizacg;
    }

}
