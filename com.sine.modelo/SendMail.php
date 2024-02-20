<?php

class SendMail {

    private $idfactura;
    private $chmail1;
    private $chmail2;
    private $chmail3;
    private $chmail4;
    private $chmail5;
    private $chmail6;
    private $mailalt1;
    private $mailalt2;
    private $mailalt3;
    private $mailalt4;
    private $mailalt5;
    private $mailalt6;
    private $pdfstring;
    private $rfcemisor;
    private $razonsocial;
    private $idcliente;
    private $uuid;
    private $folio;
    private $cfdistring;

    function __construct() {
        
    }
    
    function getIdfactura() {
        return $this->idfactura;
    }

    function getChmail1() {
        return $this->chmail1;
    }

    function getChmail2() {
        return $this->chmail2;
    }

    function getChmail3() {
        return $this->chmail3;
    }

    function getChmail4() {
        return $this->chmail4;
    }

    function getChmail5() {
        return $this->chmail5;
    }

    function getChmail6() {
        return $this->chmail6;
    }

    function getMailalt1() {
        return $this->mailalt1;
    }

    function getMailalt2() {
        return $this->mailalt2;
    }

    function getMailalt3() {
        return $this->mailalt3;
    }

    function getMailalt4() {
        return $this->mailalt4;
    }

    function getMailalt5() {
        return $this->mailalt5;
    }

    function getMailalt6() {
        return $this->mailalt6;
    }

    function getPdfstring() {
        return $this->pdfstring;
    }

    function getRfcemisor() {
        return $this->rfcemisor;
    }

    function getRazonsocial() {
        return $this->razonsocial;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getFolio() {
        return $this->folio;
    }

    function setIdfactura($idfactura) {
        $this->idfactura = $idfactura;
    }

    function setChmail1($chmail1) {
        $this->chmail1 = $chmail1;
    }

    function setChmail2($chmail2) {
        $this->chmail2 = $chmail2;
    }

    function setChmail3($chmail3) {
        $this->chmail3 = $chmail3;
    }

    function setChmail4($chmail4) {
        $this->chmail4 = $chmail4;
    }

    function setChmail5($chmail5) {
        $this->chmail5 = $chmail5;
    }

    function setChmail6($chmail6) {
        $this->chmail6 = $chmail6;
    }

    function setMailalt1($mailalt1) {
        $this->mailalt1 = $mailalt1;
    }

    function setMailalt2($mailalt2) {
        $this->mailalt2 = $mailalt2;
    }

    function setMailalt3($mailalt3) {
        $this->mailalt3 = $mailalt3;
    }

    function setMailalt4($mailalt4) {
        $this->mailalt4 = $mailalt4;
    }

    function setMailalt5($mailalt5) {
        $this->mailalt5 = $mailalt5;
    }

    function setMailalt6($mailalt6) {
        $this->mailalt6 = $mailalt6;
    }

    function setPdfstring($pdfstring) {
        $this->pdfstring = $pdfstring;
    }

    function setRfcemisor($rfcemisor) {
        $this->rfcemisor = $rfcemisor;
    }

    function setRazonsocial($razonsocial) {
        $this->razonsocial = $razonsocial;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }
    
    function getIdcliente() {
        return $this->idcliente;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }
    
    function getCfdistring() {
        return $this->cfdistring;
    }

    function setCfdistring($cfdistring) {
        $this->cfdistring = $cfdistring;
    }

}
