<?php

class Cotizacion {

    private $iddatos_cotizacion;
    private $fecha_creacion;
    private $folio;
    private $idcliente;
    private $nombrecliente;
    private $emailcliente;
    private $emailcliente2;
    private $emailcliente3;
    private $idmetodopago;
    private $idformapago;
    private $idmoneda;
    private $idusocfdi;
    private $sessionid;
    private $tipocomprobante;
    private $iddatosfacturacion;
    private $observaciones;
    private $chfirmar;
    private $actualizarfolio;
    private $actualizarfiscales;
    private $tag;

    function __construct() {
        
    }

    function getIddatos_cotizacion() {
        return $this->iddatos_cotizacion;
    }

    function getFecha_creacion() {
        return $this->fecha_creacion;
    }

    function getFolio() {
        return $this->folio;
    }

    function getNombrecliente() {
        return $this->nombrecliente;
    }

    function getEmailcliente() {
        return $this->emailcliente;
    }

    function getIdmetodopago() {
        return $this->idmetodopago;
    }

    function getIdformapago() {
        return $this->idformapago;
    }

    function getIdmoneda() {
        return $this->idmoneda;
    }

    function getIdusocfdi() {
        return $this->idusocfdi;
    }

    function getSessionid() {
        return $this->sessionid;
    }

    function getTipocomprobante() {
        return $this->tipocomprobante;
    }

    function getIddatosfacturacion() {
        return $this->iddatosfacturacion;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function setIddatos_cotizacion($iddatos_cotizacion) {
        $this->iddatos_cotizacion = $iddatos_cotizacion;
    }

    function setFecha_creacion($fecha_creacion) {
        $this->fecha_creacion = $fecha_creacion;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }

    function setNombrecliente($nombrecliente) {
        $this->nombrecliente = $nombrecliente;
    }

    function setEmailcliente($emailcliente) {
        $this->emailcliente = $emailcliente;
    }

    function setIdmetodopago($idmetodopago) {
        $this->idmetodopago = $idmetodopago;
    }

    function setIdformapago($idformapago) {
        $this->idformapago = $idformapago;
    }

    function setIdmoneda($idmoneda) {
        $this->idmoneda = $idmoneda;
    }

    function setIdusocfdi($idusocfdi) {
        $this->idusocfdi = $idusocfdi;
    }

    function setSessionid($sessionid) {
        $this->sessionid = $sessionid;
    }

    function setTipocomprobante($tipocomprobante) {
        $this->tipocomprobante = $tipocomprobante;
    }

    function setIddatosfacturacion($iddatosfacturacion) {
        $this->iddatosfacturacion = $iddatosfacturacion;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function getChfirmar() {
        return $this->chfirmar;
    }

    function setChfirmar($chfirmar) {
        $this->chfirmar = $chfirmar;
    }
    function getActualizarfolio() {
        return $this->actualizarfolio;
    }

    function getActualizarfiscales() {
        return $this->actualizarfiscales;
    }

    function setActualizarfolio($actualizarfolio) {
        $this->actualizarfolio = $actualizarfolio;
    }

    function setActualizarfiscales($actualizarfiscales) {
        $this->actualizarfiscales = $actualizarfiscales;
    }
    
    function getIdcliente() {
        return $this->idcliente;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }
    
    function getEmailcliente2() {
        return $this->emailcliente2;
    }

    function getEmailcliente3() {
        return $this->emailcliente3;
    }

    function setEmailcliente2($emailcliente2) {
        $this->emailcliente2 = $emailcliente2;
    }

    function setEmailcliente3($emailcliente3) {
        $this->emailcliente3 = $emailcliente3;
    }
    
    function getTag() {
        return $this->tag;
    }

    function setTag($tag) {
        $this->tag = $tag;
    }

}
