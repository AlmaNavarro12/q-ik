<?php

class CartaPorte{

    private $folio;
    private $tag;
    private $idfacturaCP;
    private $idcliente;
	private $cliente;
    private $rfccliente;
    private $rzcliente;
    private $regfiscalcliente;
    private $dircliente;
    private $codpostal;
    private $idformapago;
    private $idmetodopago;
    private $idmoneda;
    private $tcambio;
    private $idusocfdi;
    private $sessionid;
    private $tipocomprobante;
    private $iddatosfacturacion;
    private $periodicidad;
    private $mesperiodo;
    private $anoperiodo;
    private $chfirmar;
    private $tipomovimiento;
    private $idvehiculo;
    private $nombrevehiculo;
    private $numpermiso;
    private $tipopermiso;
    private $tipotransporte;
    private $modelo; 
    private $placavehiculo;
    private $segurorespcivil;
    private $polizarespcivil;
    private $seguroambiente;
    private $polizaambiente;
    private $idremolque1;
    private $nombreremolque1;
    private $tiporemolque1;
    private $placaremolque1;
    private $idremolque2;
    private $nombreremolque2;
    private $tiporemolque2;
    private $placaremolque2;
    private $idremolque3;
    private $nombreremolque3;
    private $tiporemolque3;
    private $placaremolque3;
    private $actualizarfolio;
    private $actualizarfiscales;
    private $uuid;
    private $observaciones;
	private $cfdisrel;
    private $p_mercancia;
    private $p_bruto;
    private $p_vehiculo;

    function __construct() {
        
    }
    
    function getTag() {
        return $this->tag;
    }

    function getIdfacturaCP() {
        return $this->idfacturaCP;
    }

    function getIdcliente() {
        return $this->idcliente;
    }

	function getCliente() {
        return $this->cliente;
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

    function getIdformapago() {
        return $this->idformapago;
    }

    function getIdmetodopago() {
        return $this->idmetodopago;
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

    function getTipomovimiento() {
        return $this->tipomovimiento;
    }

    function getIdvehiculo() {
        return $this->idvehiculo;
    }

    function getNombrevehiculo() {
        return $this->nombrevehiculo;
    }

    function getNumpermiso() {
        return $this->numpermiso;
    }

    function getTipopermiso() {
        return $this->tipopermiso;
    }

    function getTipotransporte() {
        return $this->tipotransporte;
    }

    function getModelo() {
        return $this->modelo;
    }

    function getPlacavehiculo() {
        return $this->placavehiculo;
    }

    function getSegurorespcivil() {
        return $this->segurorespcivil;
    }

    function getPolizarespcivil() {
        return $this->polizarespcivil;
    }

    function getIdremolque1() {
        return $this->idremolque1;
    }

    function getNombreremolque1() {
        return $this->nombreremolque1;
    }

    function getTiporemolque1() {
        return $this->tiporemolque1;
    }

    function getPlacaremolque1() {
        return $this->placaremolque1;
    }

    function getIdremolque2() {
        return $this->idremolque2;
    }

    function getNombreremolque2() {
        return $this->nombreremolque2;
    }

    function getTiporemolque2() {
        return $this->tiporemolque2;
    }

    function getPlacaremolque2() {
        return $this->placaremolque2;
    }

    function getIdremolque3() {
        return $this->idremolque3;
    }

    function getNombreremolque3() {
        return $this->nombreremolque3;
    }

    function getTiporemolque3() {
        return $this->tiporemolque3;
    }

    function getPlacaremolque3() {
        return $this->placaremolque3;
    }

    function getActualizarfolio() {
        return $this->actualizarfolio;
    }

    function getActualizarfiscales() {
        return $this->actualizarfiscales;
    }

    function getUuid() {
        return $this->uuid;
    }
	
	function getCFDISrel() {
        return $this->cfdisrel;
    }

    function setTag($tag) {
        $this->tag = $tag;
    }

    function setIdfacturaCP($idfacturaCP) {
        $this->idfacturaCP = $idfacturaCP;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }

	function setCliente($cliente) {
        $this->cliente = $cliente;
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

    function setIdformapago($idformapago) {
        $this->idformapago = $idformapago;
    }

    function setIdmetodopago($idmetodopago) {
        $this->idmetodopago = $idmetodopago;
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

    function setTipomovimiento($tipomovimiento) {
        $this->tipomovimiento = $tipomovimiento;
    }

    function setIdvehiculo($idvehiculo) {
        $this->idvehiculo = $idvehiculo;
    }

    function setNombrevehiculo($nombrevehiculo) {
        $this->nombrevehiculo = $nombrevehiculo;
    }

    function setNumpermiso($numpermiso) {
        $this->numpermiso = $numpermiso;
    }

    function setTipopermiso($tipopermiso) {
        $this->tipopermiso = $tipopermiso;
    }

    function setTipotransporte($tipotransporte) {
        $this->tipotransporte = $tipotransporte;
    }

    function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    function setPlacavehiculo($placavehiculo) {
        $this->placavehiculo = $placavehiculo;
    }

    function setSegurorespcivil($segurorespcivil) {
        $this->segurorespcivil = $segurorespcivil;
    }

    function setPolizarespcivil($polizarespcivil) {
        $this->polizarespcivil = $polizarespcivil;
    }

    function setIdremolque1($idremolque1) {
        $this->idremolque1 = $idremolque1;
    }

    function setNombreremolque1($nombreremolque1) {
        $this->nombreremolque1 = $nombreremolque1;
    }

    function setTiporemolque1($tiporemolque1) {
        $this->tiporemolque1 = $tiporemolque1;
    }

    function setPlacaremolque1($placaremolque1) {
        $this->placaremolque1 = $placaremolque1;
    }

    function setIdremolque2($idremolque2) {
        $this->idremolque2 = $idremolque2;
    }

    function setNombreremolque2($nombreremolque2) {
        $this->nombreremolque2 = $nombreremolque2;
    }

    function setTiporemolque2($tiporemolque2) {
        $this->tiporemolque2 = $tiporemolque2;
    }

    function setPlacaremolque2($placaremolque2) {
        $this->placaremolque2 = $placaremolque2;
    }

    function setIdremolque3($idremolque3) {
        $this->idremolque3 = $idremolque3;
    }

    function setNombreremolque3($nombreremolque3) {
        $this->nombreremolque3 = $nombreremolque3;
    }

    function setTiporemolque3($tiporemolque3) {
        $this->tiporemolque3 = $tiporemolque3;
    }

    function setPlacaremolque3($placaremolque3) {
        $this->placaremolque3 = $placaremolque3;
    }

    function setActualizarfolio($actualizarfolio) {
        $this->actualizarfolio = $actualizarfolio;
    }

    function setActualizarfiscales($actualizarfiscales) {
        $this->actualizarfiscales = $actualizarfiscales;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }
    
    function getSeguroambiente() {
        return $this->seguroambiente;
    }

    function getPolizaambiente() {
        return $this->polizaambiente;
    }

    function setSeguroambiente($seguroambiente) {
        $this->seguroambiente = $seguroambiente;
    }

    function setPolizaambiente($polizaambiente) {
        $this->polizaambiente = $polizaambiente;
    }
    
    function getFolio() {
        return $this->folio;
    }

    function setFolio($folio) {
        $this->folio = $folio;
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
    
    function getDircliente() {
        return $this->dircliente;
    }

    function setDircliente($dircliente) {
        $this->dircliente = $dircliente;
    }
    
    function getObservaciones() {
        return $this->observaciones;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }
	
	function setCFDISrel($cfdi) {
        return $this->cfdisrel = $cfdi;
    }

    function setPesoMercancia($val){
        $this->p_mercancia = $val;
    }
    
    function getPesoMercancia(){
        return $this->p_mercancia;
    }
    
    function setPesoVehicular($val){
        $this->p_vehiculo = $val;
    }
    
    function getPesoVehicular(){
        return $this->p_vehiculo;
    }
    
    function setPesoBruto($val){
        $this->p_bruto = $val;
    }
    
    function getPesoBruto(){
        return $this->p_bruto;
    }

}
