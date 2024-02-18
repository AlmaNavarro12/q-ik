<?php

class TMPPago {

    private $idpagotmp;
    private $idmoneda;
    private $tcambio;
    private $idfacturatmp;
    private $foliotmp;
    private $uuid;
    private $tcamcfdi;
    private $idmonedacdfi;
    private $cmonedacfdi;
    private $cmetodo;
    private $parcialidadtmp;
    private $montopagotmp;
    private $montoanteriortmp;
    private $montoinsolutotmp;
    private $totalfacturatmp;
    private $sessionid;
    private $type;
    private $tag;

    
    function __construct() {
        
    }
    
    function getIdpagotmp() {
        return $this->idpagotmp;
    }

    function getIdmoneda() {
        return $this->idmoneda;
    }

    function getTcambio() {
        return $this->tcambio;
    }

    function getIdfacturatmp() {
        return $this->idfacturatmp;
    }

    function getFoliotmp() {
        return $this->foliotmp;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getTcamcfdi() {
        return $this->tcamcfdi;
    }

    function getTag() {
        return $this->tag;
    }

    function getIdmonedacdfi() {
        return $this->idmonedacdfi;
    }

    function getCmonedacfdi() {
        return $this->cmonedacfdi;
    }

    function getCmetodo() {
        return $this->cmetodo;
    }

    function getParcialidadtmp() {
        return $this->parcialidadtmp;
    }

    function getMontopagotmp() {
        return $this->montopagotmp;
    }

    function getMontoanteriortmp() {
        return $this->montoanteriortmp;
    }

    function getMontoinsolutotmp() {
        return $this->montoinsolutotmp;
    }

    function getTotalfacturatmp() {
        return $this->totalfacturatmp;
    }

    function getSessionid() {
        return $this->sessionid;
    }

    function getType() {
        return $this->type;
    }

    function setIdpagotmp($idpagotmp) {
        $this->idpagotmp = $idpagotmp;
    }

    function setIdmoneda($idmoneda) {
        $this->idmoneda = $idmoneda;
    }

    function setTcambio($tcambio) {
        $this->tcambio = $tcambio;
    }

    function setTag($tag) {
        $this->tag = $tag;
    }

    function setIdfacturatmp($idfacturatmp) {
        $this->idfacturatmp = $idfacturatmp;
    }

    function setFoliotmp($foliotmp) {
        $this->foliotmp = $foliotmp;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setTcamcfdi($tcamcfdi) {
        $this->tcamcfdi = $tcamcfdi;
    }

    function setIdmonedacdfi($idmonedacdfi) {
        $this->idmonedacdfi = $idmonedacdfi;
    }

    function setCmonedacfdi($cmonedacfdi) {
        $this->cmonedacfdi = $cmonedacfdi;
    }

    function setCmetodo($cmetodo) {
        $this->cmetodo = $cmetodo;
    }

    function setParcialidadtmp($parcialidadtmp) {
        $this->parcialidadtmp = $parcialidadtmp;
    }

    function setMontopagotmp($montopagotmp) {
        $this->montopagotmp = $montopagotmp;
    }

    function setMontoanteriortmp($montoanteriortmp) {
        $this->montoanteriortmp = $montoanteriortmp;
    }

    function setMontoinsolutotmp($montoinsolutotmp) {
        $this->montoinsolutotmp = $montoinsolutotmp;
    }

    function setTotalfacturatmp($totalfacturatmp) {
        $this->totalfacturatmp = $totalfacturatmp;
    }

    function setSessionid($sessionid) {
        $this->sessionid = $sessionid;
    }

    function setType($type) {
        $this->type = $type;
    }

}
