<?php

class Remolque {

    private $idremolque;
    private $nombre;
    private $tiporemolque;
    private $placaremolque;

    function __construct() {
        
    }
    
    function getIdremolque() {
        return $this->idremolque;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getTiporemolque() {
        return $this->tiporemolque;
    }

    function getPlacaremolque() {
        return $this->placaremolque;
    }

    function setIdremolque($idremolque) {
        $this->idremolque = $idremolque;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setTiporemolque($tiporemolque) {
        $this->tiporemolque = $tiporemolque;
    }

    function setPlacaremolque($placaremolque) {
        $this->placaremolque = $placaremolque;
    }
    
}
