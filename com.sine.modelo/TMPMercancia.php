<?php

class TMPMercancia {

    private $tmpid;
    private $condicional;
    private $clvprod;
    private $descripcion;
    private $cantidad;
    private $unidad;
    private $peso;
    private $peligro;
    private $clvmaterial;
    private $embalaje;
    private $sid;
    
    function __construct() {
        
    }
    
    function getTmpid() {
        return $this->tmpid;
    }

    function getClvprod() {
        return $this->clvprod;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getUnidad() {
        return $this->unidad;
    }

    function getPeso() {
        return $this->peso;
    }

    function getSid() {
        return $this->sid;
    }

    function setTmpid($tmpid) {
        $this->tmpid = $tmpid;
    }

    function setClvprod($clvprod) {
        $this->clvprod = $clvprod;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setUnidad($unidad) {
        $this->unidad = $unidad;
    }

    function setPeso($peso) {
        $this->peso = $peso;
    }

    function setSid($sid) {
        $this->sid = $sid;
    }
    
    function getCondicional() {
        return $this->condicional;
    }

    function setCondicional($condicional) {
        $this->condicional = $condicional;
    }
    
    function getPeligro() {
        return $this->peligro;
    }

    function getClvmaterial() {
        return $this->clvmaterial;
    }

    function getEmbalaje() {
        return $this->embalaje;
    }

    function setPeligro($peligro) {
        $this->peligro = $peligro;
    }

    function setClvmaterial($clvmaterial) {
        $this->clvmaterial = $clvmaterial;
    }

    function setEmbalaje($embalaje) {
        $this->embalaje = $embalaje;
    }

}
