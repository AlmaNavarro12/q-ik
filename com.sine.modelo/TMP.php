<?php

class TMP {

    private $idtmp;
    private $idproductotmp;
    private $descripciontmp;
    private $cfiscaltmp;
    private $cunidadtmp;
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
    private $uuid;

    function __construct() {
        
    }
    function getIdtmp() {
        return $this->idtmp;
    }

    function getIdproductotmp() {
        return $this->idproductotmp;
    }

    function getDescripciontmp() {
        return $this->descripciontmp;
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

    function getObservacionestmp() {
        return $this->observacionestmp;
    }

    function getIdtraslados() {
        return $this->idtraslados;
    }

    function getIdretencion() {
        return $this->idretencion;
    }

    function getUuid() {
        return $this->uuid;
    }

    function setIdtmp($idtmp) {
        $this->idtmp = $idtmp;
    }

    function setIdproductotmp($idproductotmp) {
        $this->idproductotmp = $idproductotmp;
    }

    function setDescripciontmp($descripciontmp) {
        $this->descripciontmp = $descripciontmp;
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

    function setObservacionestmp($observacionestmp) {
        $this->observacionestmp = $observacionestmp;
    }

    function setIdtraslados($idtraslados) {
        $this->idtraslados = $idtraslados;
    }

    function setIdretencion($idretencion) {
        $this->idretencion = $idretencion;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }
    
    function getCfiscaltmp() {
        return $this->cfiscaltmp;
    }

    function getCunidadtmp() {
        return $this->cunidadtmp;
    }

    function setCfiscaltmp($cfiscaltmp) {
        $this->cfiscaltmp = $cfiscaltmp;
    }

    function setCunidadtmp($cunidadtmp) {
        $this->cunidadtmp = $cunidadtmp;
    }

}