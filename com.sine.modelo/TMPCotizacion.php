<?php

class TMPCotizacion {

    private $idtmp;
    private $idproductotmp;
    private $descripciontmp;
    private $cantidadtmp;
    private $preciotmp;
    private $importetmp;
    private $descuento;
    private $impdescuento;
    private $imptotal;
    private $sessionid;
    private $idfactura;
    private $observacionestmp;
    private $idtraslados;
    private $idretencion;
    private $clvfiscal;
    private $clvunidad;

    function __construct() {
        
    }
    
    function getIdtmp() {
        return $this->idtmp;
    }

    function getIdproductotmp() {
        return $this->idproductotmp;
    }

    function getCantidadtmp() {
        return $this->cantidadtmp;
    }

    function getPreciotmp() {
        return $this->preciotmp;
    }

    function getImportetmp() {
        return $this->importetmp;
    }

    function getDescuento() {
        return $this->descuento;
    }

    function getImpdescuento() {
        return $this->impdescuento;
    }

    function getImptotal() {
        return $this->imptotal;
    }

    function getSessionid() {
        return $this->sessionid;
    }

    function getIdfactura() {
        return $this->idfactura;
    }

    function setIdtmp($idtmp) {
        $this->idtmp = $idtmp;
    }

    function setIdproductotmp($idproductotmp) {
        $this->idproductotmp = $idproductotmp;
    }

    function setCantidadtmp($cantidadtmp) {
        $this->cantidadtmp = $cantidadtmp;
    }

    function setPreciotmp($preciotmp) {
        $this->preciotmp = $preciotmp;
    }

    function setImportetmp($importetmp) {
        $this->importetmp = $importetmp;
    }

    function setDescuento($descuento) {
        $this->descuento = $descuento;
    }

    function setImpdescuento($impdescuento) {
        $this->impdescuento = $impdescuento;
    }

    function setImptotal($imptotal) {
        $this->imptotal = $imptotal;
    }

    function setSessionid($sessionid) {
        $this->sessionid = $sessionid;
    }

    function setIdfactura($idfactura) {
        $this->idfactura = $idfactura;
    }
    
    function getDescripciontmp() {
        return $this->descripciontmp;
    }

    function getObservacionestmp() {
        return $this->observacionestmp;
    }

    function setDescripciontmp($descripciontmp) {
        $this->descripciontmp = $descripciontmp;
    }

    function setObservacionestmp($observacionestmp) {
        $this->observacionestmp = $observacionestmp;
    }
    
    function getIdtraslados() {
        return $this->idtraslados;
    }

    function getIdretencion() {
        return $this->idretencion;
    }

    function setIdtraslados($idtraslados) {
        $this->idtraslados = $idtraslados;
    }

    function setIdretencion($idretencion) {
        $this->idretencion = $idretencion;
    }
    
    function getClvfiscal() {
        return $this->clvfiscal;
    }

    function getClvunidad() {
        return $this->clvunidad;
    }

    function setClvfiscal($clvfiscal) {
        $this->clvfiscal = $clvfiscal;
    }

    function setClvunidad($clvunidad) {
        $this->clvunidad = $clvunidad;
    }

}