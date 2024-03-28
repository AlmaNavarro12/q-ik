<?php

class Factura {

    private $iddatos_factura;
    private $fecha_creacion;
    private $folio;
    private $idcliente;
    private $cliente;
    private $rfccliente;
    private $rzcliente;
    private $regfiscalcliente;
    private $dircliente;
    private $codpostal;
    private $cadena;
    private $num_certificadoSAT;
    private $numcertificadoCFDI;
    private $uuid;
    private $sellosat;
    private $sellocfdi;
    private $fechatimbrado;
    private $qrcode;
    private $status_pago;
    private $idmetodopago;
    private $idformapago;
    private $idmoneda;
    private $tcambio;
    private $idusocfdi;
    private $sessionid;
    private $tipocomprobante;
    private $iddatosfacturacion;
    private $chfirmar;
    private $cfdisrel;
    private $idcotizacion;
    private $actualizarfolio;
    private $actualizarfiscales;
    private $periodicidad;
    private $mesperiodo;
    private $anoperiodo;
    private $tag;
    //NUEVO
    private $nombremoneda;
    private $nombremetodo;
    private $nombrecomprobante;
    private $nombrepago;
    private $nombrecfdi;


    

    function __construct() {
        
    }
    
    function getIddatos_factura() {
        return $this->iddatos_factura;
    }

    function getFecha_creacion() {
        return $this->fecha_creacion;
    }

    function getFolio() {
        return $this->folio;
    }

    function getIdcliente() {
        return $this->idcliente;
    }

    function getRfccliente() {
        return $this->rfccliente;
    }

    function getRzcliente() {
        return $this->rzcliente;
    }

    function getRegfiscalcliente() {
        return $this->regfiscalcliente;
    }

    function getCodpostal() {
        return $this->codpostal;
    }

    function getCadena() {
        return $this->cadena;
    }

    function getNum_certificadoSAT() {
        return $this->num_certificadoSAT;
    }

    function getNumcertificadoCFDI() {
        return $this->numcertificadoCFDI;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getSellosat() {
        return $this->sellosat;
    }

    function getSellocfdi() {
        return $this->sellocfdi;
    }

    function getFechatimbrado() {
        return $this->fechatimbrado;
    }

    function getQrcode() {
        return $this->qrcode;
    }

    function getStatus_pago() {
        return $this->status_pago;
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

    function getTcambio() {
        return $this->tcambio;
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

    function getChfirmar() {
        return $this->chfirmar;
    }

    function getCfdisrel() {
        return $this->cfdisrel;
    }

    function getIdcotizacion() {
        return $this->idcotizacion;
    }

    function getActualizarfolio() {
        return $this->actualizarfolio;
    }

    function getActualizarfiscales() {
        return $this->actualizarfiscales;
    }
    
    function setIddatos_factura($iddatos_factura) {
        $this->iddatos_factura = $iddatos_factura;
    }

    function setFecha_creacion($fecha_creacion) {
        $this->fecha_creacion = $fecha_creacion;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }

    function setRfccliente($rfccliente) {
        $this->rfccliente = $rfccliente;
    }

    function setRzcliente($rzcliente) {
        $this->rzcliente = $rzcliente;
    }

    function setRegfiscalcliente($regfiscalcliente) {
        $this->regfiscalcliente = $regfiscalcliente;
    }

    function setCodpostal($codpostal) {
        $this->codpostal = $codpostal;
    }

    function setCadena($cadena) {
        $this->cadena = $cadena;
    }

    function setNum_certificadoSAT($num_certificadoSAT) {
        $this->num_certificadoSAT = $num_certificadoSAT;
    }

    function setNumcertificadoCFDI($numcertificadoCFDI) {
        $this->numcertificadoCFDI = $numcertificadoCFDI;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setSellosat($sellosat) {
        $this->sellosat = $sellosat;
    }

    function setSellocfdi($sellocfdi) {
        $this->sellocfdi = $sellocfdi;
    }

    function setFechatimbrado($fechatimbrado) {
        $this->fechatimbrado = $fechatimbrado;
    }

    function setQrcode($qrcode) {
        $this->qrcode = $qrcode;
    }

    function setStatus_pago($status_pago) {
        $this->status_pago = $status_pago;
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

    function setTcambio($tcambio) {
        $this->tcambio = $tcambio;
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

    function setChfirmar($chfirmar) {
        $this->chfirmar = $chfirmar;
    }

    function setCfdisrel($cfdisrel) {
        $this->cfdisrel = $cfdisrel;
    }

    function setIdcotizacion($idcotizacion) {
        $this->idcotizacion = $idcotizacion;
    }

    function setActualizarfolio($actualizarfolio) {
        $this->actualizarfolio = $actualizarfolio;
    }

    function setActualizarfiscales($actualizarfiscales) {
        $this->actualizarfiscales = $actualizarfiscales;
    }

    function getTag() {
        return $this->tag;
    }

    function setTag($tag) {
        $this->tag = $tag;
    }
    
    function getDircliente() {
        return $this->dircliente;
    }

    function setDircliente($dircliente) {
        $this->dircliente = $dircliente;
    }
    
    function getPeriodicidad() {
        return $this->periodicidad;
    }

    function getMesperiodo() {
        return $this->mesperiodo;
    }

    function getAnoperiodo() {
        return $this->anoperiodo;
    }

    function setPeriodicidad($periodicidad) {
        $this->periodicidad = $periodicidad;
    }

    function setMesperiodo($mesperiodo) {
        $this->mesperiodo = $mesperiodo;
    }

    function setAnoperiodo($anoperiodo) {
        $this->anoperiodo = $anoperiodo;
    }
    
    function getCliente() {
        return $this->cliente;
    }

    function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    //nuevoSET++++++++++++++++
    function setNombremoneda($idmoneda) {
        $this->nombremoneda = $idmoneda;
    }

    function setNombremetodo($idmetodopago) {
        $this->nombremetodo = $idmetodopago;    
    }
    
    function setNombrecomprobante($tipocomprobante) {
        $this->nombrecomprobante = $tipocomprobante;  
    }
    
    function setNombrepago($idformapago) { 
        $this->nombrepago = $idformapago;
    }
    
    function setNombrecfdi($idusocfdi) {   
         $this->nombrecfdi = $idusocfdi;    
    }

    function getNombremoneda() {
        return $this->nombremoneda;
    }

    function getNombremetodo() {
        return $this->nombremetodo;     
    }

    function getNombrecomprobante() {
        return $this->nombrecomprobante;    
    }

    function getNombrepago() {  
        return $this->nombrepago;
    }

    function getNombrecfdi(){
        return $this->nombrecfdi;
    }

}