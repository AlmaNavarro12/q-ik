<?php

class TMPOperador {

    private $tmpid;
    private $tmpidoperador;
    private $tmpnombre;
    private $tmprfc;
    private $tmplic;
    private $estado;
    private $calle;
    private $codpostal;
    private $sid;
    private $idmunicipio;
    private $nombreestado;
    private $nombremunicipio;
    
    function __construct() {
        
    }
    
    function getTmpid() {
        return $this->tmpid;
    }

    function getTmpidoperador() {
        return $this->tmpidoperador;
    }

    function getTmpnombre() {
        return $this->tmpnombre;
    }

    function getTmprfc() {
        return $this->tmprfc;
    }

    function getTmplic() {
        return $this->tmplic;
    }

    function getEstado() {
        return $this->estado;
    }

    function getCalle() {
        return $this->calle;
    }

    function getCodpostal() {
        return $this->codpostal;
    }

    function getNombreEstado() {
        return $this->nombreestado;
    }

    function getNombreMunicipio() {
        return $this->nombremunicipio;
    }

    function getSid() {
        return $this->sid;
    }

    function setTmpid($tmpid) {
        $this->tmpid = $tmpid;
    }

    function setTmpidoperador($tmpidoperador) {
        $this->tmpidoperador = $tmpidoperador;
    }

    function setTmpnombre($tmpnombre) {
        $this->tmpnombre = $tmpnombre;
    }

    function setTmprfc($tmprfc) {
        $this->tmprfc = $tmprfc;
    }

    function setTmplic($tmplic) {
        $this->tmplic = $tmplic;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setNombreEstado($nombreestado) {
        $this->nombreestado = $nombreestado;
    }

    function setNombreMunicipio($nombremunicipio) {
        $this->nombremunicipio = $nombremunicipio;
    }


    function setCalle($calle) {
        $this->calle = $calle;
    }

    function setCodpostal($codpostal) {
        $this->codpostal = $codpostal;
    }

    function setSid($sid) {
        $this->sid = $sid;
    }
    
    function getIdmunicipio() {
        return $this->idmunicipio;
    }

    function setIdmunicipio($idmunicipio) {
        $this->idmunicipio = $idmunicipio;
    }

}
