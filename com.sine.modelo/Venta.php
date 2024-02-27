<?php
class Venta {

    private $idventa;
    private $tagventa;
    private $fechaventa;
    private $horaventa;
    private $totalventa;
    private $formapago;
    private $montopagado;
    private $cambio;
    private $referencia;
    private $tipomov;
    private $montomov;
    private $conceptomov;
    private $uid;
    private $fechacorte;
    private $sid;
    private $descuento;
    private $percent_descuento;

    function __construct() {
    }
    
    function getIdventa() {
        return $this->idventa;
    }

    function getTagventa() {
        return $this->tagventa;
    }

    function getFechaventa() {
        return $this->fechaventa;
    }

    function getHoraventa() {
        return $this->horaventa;
    }

    function getTotalventa() {
        return $this->totalventa;
    }

    function getFormapago() {
        return $this->formapago;
    }

    function getMontopagado() {
        return $this->montopagado;
    }

    function getCambio() {
        return $this->cambio;
    }

    function getReferencia() {
        return $this->referencia;
    }

    function getTipomov() {
        return $this->tipomov;
    }

    function getMontomov() {
        return $this->montomov;
    }

    function getConceptomov() {
        return $this->conceptomov;
    }

    function getUid() {
        return $this->uid;
    }

    function getFechacorte() {
        return $this->fechacorte;
    }

    function getSid() {
        return $this->sid;
    }

    function getDescuento(){
        return $this->descuento;
    }

    function getPercentDescuento(){
        return $this->percent_descuento;
    }

    function setIdventa($idventa) {
        $this->idventa = $idventa;
    }

    function setTagventa($tagventa) {
        $this->tagventa = $tagventa;
    }

    function setFechaventa($fechaventa) {
        $this->fechaventa = $fechaventa;
    }

    function setHoraventa($horaventa) {
        $this->horaventa = $horaventa;
    }

    function setTotalventa($totalventa) {
        $this->totalventa = $totalventa;
    }

    function setFormapago($formapago) {
        $this->formapago = $formapago;
    }

    function setMontopagado($montopagado) {
        $this->montopagado = $montopagado;
    }

    function setCambio($cambio) {
        $this->cambio = $cambio;
    }

    function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    function setTipomov($tipomov) {
        $this->tipomov = $tipomov;
    }

    function setMontomov($montomov) {
        $this->montomov = $montomov;
    }

    function setConceptomov($conceptomov) {
        $this->conceptomov = $conceptomov;
    }

    function setUid($uid) {
        $this->uid = $uid;
    }

    function setFechacorte($fechacorte) {
        $this->fechacorte = $fechacorte;
    }

    function setSid($sid) {
        $this->sid = $sid;
    }

    function setDescuento($descuento){
        $this->descuento = $descuento;
    }

    function setPercentDescuento($percent){
        $this->percent_descuento = $percent;
    }

}
