<?php

class Comunicado {

    private $idcomunicado;
    private $tag;
    private $fecha;
    private $hora;
    private $idcategoria;
    private $idcontactos;
    private $color;
    private $size;
    private $asunto;
    private $emision;
    private $mensaje;
    private $sellar;
    private $firma;
    private $iddatos;
    private $chcom;
    private $sid;

    function __construct() {
        
    }
    
    function getIdcomunicado() {
        return $this->idcomunicado;
    }

    function getTag() {
        return $this->tag;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getHora() {
        return $this->hora;
    }

    function getIdcategoria() {
        return $this->idcategoria;
    }

    function getIdcontactos() {
        return $this->idcontactos;
    }

    function getColor() {
        return $this->color;
    }

    function getSize() {
        return $this->size;
    }

    function getAsunto() {
        return $this->asunto;
    }

    function getEmision() {
        return $this->emision;
    }

    function getMensaje() {
        return $this->mensaje;
    }

    function getSellar() {
        return $this->sellar;
    }

    function getFirma() {
        return $this->firma;
    }

    function getIddatos() {
        return $this->iddatos;
    }

    function getChcom() {
        return $this->chcom;
    }

    function getSid() {
        return $this->sid;
    }

    function setIdcomunicado($idcomunicado) {
        $this->idcomunicado = $idcomunicado;
    }

    function setTag($tag) {
        $this->tag = $tag;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setHora($hora) {
        $this->hora = $hora;
    }

    function setIdcategoria($idcategoria) {
        $this->idcategoria = $idcategoria;
    }

    function setIdcontactos($idcontactos) {
        $this->idcontactos = $idcontactos;
    }

    function setColor($color) {
        $this->color = $color;
    }

    function setSize($size) {
        $this->size = $size;
    }

    function setAsunto($asunto) {
        $this->asunto = $asunto;
    }

    function setEmision($emision) {
        $this->emision = $emision;
    }

    function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    function setSellar($sellar) {
        $this->sellar = $sellar;
    }

    function setFirma($firma) {
        $this->firma = $firma;
    }

    function setIddatos($iddatos) {
        $this->iddatos = $iddatos;
    }

    function setChcom($chcom) {
        $this->chcom = $chcom;
    }

    function setSid($sid) {
        $this->sid = $sid;
    }
    
}