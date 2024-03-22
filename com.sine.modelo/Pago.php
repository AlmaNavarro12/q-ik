<?php

class Pago {

    private $idpago;
    private $foliopago;
    private $fechacreacion;
    private $idcliente;
    private $nombrecliente;
    private $rfccliente;
    private $razoncliente;
    private $regfiscalcliente;
    private $codpostal;
    private $pago_idfiscales;
    private $pagoidformapago;
    private $pagoidmoneda;
    private $tipocambio;
    private $fechapago;
    private $horapago;
    private $pago_idbanco;
    private $pago_cuenta;
    private $nooperacion;
    private $totalpagado;
    private $sessionid;
    private $chfirmar;
    private $actualizarfiscales;
    private $idbancoB;
    private $cuentaB;
    private $tagPago;
    private $tagcomp;
    private $orden;
    private $nombremoneda;
    private $nombreforma;


            
    function __construct() {
        
    }
    
    function getIdpago() {
        return $this->idpago;
    }

    function getFoliopago() {
        return $this->foliopago;
    }

    function getFechacreacion() {
        return $this->fechacreacion;
    }

    function gettagcomp() {
        return $this->tagcomp;
    }

    function getorden() {
        return $this->orden;
    }

    function getIdcliente() {
        return $this->idcliente;
    }

    function getRfccliente() {
        return $this->rfccliente;
    }

    function getRazoncliente() {
        return $this->razoncliente;
    }

    function gettagPago() {
        return $this->tagPago;
    }

    function getRegfiscalcliente() {
        return $this->regfiscalcliente;
    }

    function getCodpostal() {
        return $this->codpostal;
    }

    function getPago_idfiscales() {
        return $this->pago_idfiscales;
    }

    function getPagoidformapago() {
        return $this->pagoidformapago;
    }

    function getPagoidmoneda() {
        return $this->pagoidmoneda;
    }

    function getTipocambio() {
        return $this->tipocambio;
    }

    function getFechapago() {
        return $this->fechapago;
    }

    function getHorapago() {
        return $this->horapago;
    }

    function getPago_idbanco() {
        return $this->pago_idbanco;
    }

    function getPago_cuenta() {
        return $this->pago_cuenta;
    }

    function getNooperacion() {
        return $this->nooperacion;
    }

    function getTotalpagado() {
        return $this->totalpagado;
    }

    function getSessionid() {
        return $this->sessionid;
    }

    function getChfirmar() {
        return $this->chfirmar;
    }

    function getNombreMoneda() {
        return $this->nombremoneda;
    }

    function getNombreForma() {
        return $this->nombreforma;
    }

    function getActualizarfiscales() {
        return $this->actualizarfiscales;
    }

    function getIdbancoB() {
        return $this->idbancoB;
    }

    function getCuentaB() {
        return $this->cuentaB;
    }

    function setIdpago($idpago) {
        $this->idpago = $idpago;
    }

    function setFoliopago($foliopago) {
        $this->foliopago = $foliopago;
    }

    function setFechacreacion($fechacreacion) {
        $this->fechacreacion = $fechacreacion;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }

    function setRfccliente($rfccliente) {
        $this->rfccliente = $rfccliente;
    }

    function setRazoncliente($razoncliente) {
        $this->razoncliente = $razoncliente;
    }

    function settagPago($tagPago) {
        $this->tagPago = $tagPago;
    }

    function setRegfiscalcliente($regfiscalcliente) {
        $this->regfiscalcliente = $regfiscalcliente;
    }

    function setCodpostal($codpostal) {
        $this->codpostal = $codpostal;
    }

    function setPago_idfiscales($pago_idfiscales) {
        $this->pago_idfiscales = $pago_idfiscales;
    }

    function setPagoidformapago($pagoidformapago) {
        $this->pagoidformapago = $pagoidformapago;
    }

    function setPagoidmoneda($pagoidmoneda) {
        $this->pagoidmoneda = $pagoidmoneda;
    }

    function setTipocambio($tipocambio) {
        $this->tipocambio = $tipocambio;
    }

    function setFechapago($fechapago) {
        $this->fechapago = $fechapago;
    }

    function setHorapago($horapago) {
        $this->horapago = $horapago;
    }

    function setPago_idbanco($pago_idbanco) {
        $this->pago_idbanco = $pago_idbanco;
    }

    function setPago_cuenta($pago_cuenta) {
        $this->pago_cuenta = $pago_cuenta;
    }

    function setNombreMoneda($nombremoneda) {
        $this->nombremoneda = $nombremoneda;
    }

    function setNombreForma($nombreforma) {
        $this->nombreforma = $nombreforma;
    }

    function setNooperacion($nooperacion) {
        $this->nooperacion = $nooperacion;
    }

    function setTotalpagado($totalpagado) {
        $this->totalpagado = $totalpagado;
    }

    function setSessionid($sessionid) {
        $this->sessionid = $sessionid;
    }

    function setChfirmar($chfirmar) {
        $this->chfirmar = $chfirmar;
    }

    function setActualizarfiscales($actualizarfiscales) {
        $this->actualizarfiscales = $actualizarfiscales;
    }

    function settagcomp($tagcomp) {
        $this->tagcomp = $tagcomp;
    }

    function setorden($orden) {
        $this->orden = $orden;
    }

    function setIdbancoB($idbancoB) {
        $this->idbancoB = $idbancoB;
    }

    function setCuentaB($cuentaB) {
        $this->cuentaB = $cuentaB;
    }
    
    function getNombrecliente() {
        return $this->nombrecliente;
    }

    function setNombrecliente($nombrecliente) {
        $this->nombrecliente = $nombrecliente;
    }

}
