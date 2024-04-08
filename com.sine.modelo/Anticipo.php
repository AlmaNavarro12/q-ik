<?php

class Anticipo {

    private $idanticipo;
    private $idcotizacion;
    private $monto;
    private $restante;
    private $autorizacion;
    private $fecha;
    private $img;
    private $mensaje;
    private $actualizarimg;
    private $emision;

    function __construct() {
        
    }

    function getIdanticipo() {
        return $this->idanticipo;
    }

    function getIdcotizacion() {
        return $this->idcotizacion;
    }

    function getMonto() {
        return $this->monto;
    }

    function getRestante() {
        return $this->restante;
    }

    function getAutorizacion() {
        return $this->autorizacion;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getImg() {
        return $this->img;
    }

    function getMensaje() {
        return $this->mensaje;
    }

    function getActualizarimg() {
        return $this->actualizarimg;
    }

    function getEmision() {
        return $this->emision;
    }

    function setIdanticipo($idanticipo) {
        $this->idanticipo = $idanticipo;
    }

    function setIdcotizacion($idcotizacion) {
        $this->idcotizacion = $idcotizacion;
    }

    function setMonto($monto) {
        $this->monto = $monto;
    }

    function setRestante($restante) {
        $this->restante = $restante;
    }

    function setAutorizacion($autorizacion) {
        $this->autorizacion = $autorizacion;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setImg($img) {
        $this->img = $img;
    }

    function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    function setActualizarimg($actualizarimg) {
        $this->actualizarimg = $actualizarimg;
    }

    function setEmision($emision) {
        $this->emision = $emision;
    }

}
