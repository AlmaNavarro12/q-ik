<?php

class TMPUbicacion {

    private $tmpid;
    private $tmpidubicacion;
    private $nombre;
    private $rfc;
    private $tipo;
    private $direccion;
    private $idmunicipio;
    private $estado;
    private $codpos;
    private $distancia;
    private $fecha;
    private $hora;
    private $sid;
    
    function __construct() {
        
    }
    
    function getTmpid() {
        return $this->tmpid;
    }

    function getTmpidubicacion() {
        return $this->tmpidubicacion;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getEstado() {
        return $this->estado;
    }

    function getCodpos() {
        return $this->codpos;
    }

    function getDistancia() {
        return $this->distancia;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getHora() {
        return $this->hora;
    }

    function getSid() {
        return $this->sid;
    }

    function setTmpid($tmpid) {
        $this->tmpid = $tmpid;
    }

    function setTmpidubicacion($tmpidubicacion) {
        $this->tmpidubicacion = $tmpidubicacion;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setCodpos($codpos) {
        $this->codpos = $codpos;
    }

    function setDistancia($distancia) {
        $this->distancia = $distancia;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setHora($hora) {
        $this->hora = $hora;
    }

    function setSid($sid) {
        $this->sid = $sid;
    }
    
    function getDireccion() {
        return $this->direccion;
    }

    function getIdmunicipio() {
        return $this->idmunicipio;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setIdmunicipio($idmunicipio) {
        $this->idmunicipio = $idmunicipio;
    }

}
