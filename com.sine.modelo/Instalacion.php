<?php

class Instalacion {

    private $idhojaservicio;
    private $fechaservicio;
    private $horaservicio;
    private $idcliente;
    private $nombrecliente;
    private $plataforma;
    private $marca;
    private $modelo;
    private $anho; 
    private $color;
    private $serie;
    private $numeconomico;
    private $km;
    private $placas;
    private $iddanhos; 
    private $idmolduras;
    private $otrosmolduras;
    private $idtablero;
    private $otrostablero;
    private $idcableado;
    private $otroscableado;
    private $idccorriente;
    private $otrosccorriente;
    private $idtservicio;
    private $otrostservicio;
    private $idgpsvehiculo;
    private $otrosgps;
    private $imei;
    private $numtelefono;
    private $idinstalador;
    private $idaccesorio;
    private $observaciones;
    private $img;
    private $imgactualizar;
    private $idinstalacion;
    private $encargado;
    private $firma;
    private $firmaactual;
    private $modeloanterior;
    private $imeianterior;
    private $simanterior;
    private $sesionid;
    private $folio;
    private $imgfrente;
    private $imgnserie;
    private $imgtabinicial;
    private $imgtabfinal;
    private $imgfrenteactualizar;
    private $imgnserieactualizar;
    private $imgtabactualizar;
    private $imgtabfactualizar;
    private $tipounidad;

    private $imgantesinst;
    private $imgdespuesinst;
    private $idasignacion;
    private $observaciones_gral;
    private $ubicacion_panico;
    private $tipo_corte;


    function __construct() {
        
    }
    
    function getIdhojaservicio() {
        return $this->idhojaservicio;
    }

    function getFechaservicio() {
        return $this->fechaservicio;
    }

    function getHoraservicio() {
        return $this->horaservicio;
    }

    function getIdcliente() {
        return $this->idcliente;
    }

    function getNombrecliente() {
        return $this->nombrecliente;
    }

    function getPlataforma() {
        return $this->plataforma;
    }

    function getMarca() {
        return $this->marca;
    }

    function getModelo() {
        return $this->modelo;
    }

    function getAnho() {
        return $this->anho;
    }

    function getColor() {
        return $this->color;
    }

    function getSerie() {
        return $this->serie;
    }

    function getNumeconomico() {
        return $this->numeconomico;
    }

    function getKm() {
        return $this->km;
    }

    function getPlacas() {
        return $this->placas;
    }

    function getIddanhos() {
        return $this->iddanhos;
    }

    function getIdmolduras() {
        return $this->idmolduras;
    }

    function getOtrosmolduras() {
        return $this->otrosmolduras;
    }

    function getIdtablero() {
        return $this->idtablero;
    }

    function getOtrostablero() {
        return $this->otrostablero;
    }

    function getIdcableado() {
        return $this->idcableado;
    }

    function getOtroscableado() {
        return $this->otroscableado;
    }

    function getIdccorriente() {
        return $this->idccorriente;
    }

    function getOtrosccorriente() {
        return $this->otrosccorriente;
    }

    function getIdtservicio() {
        return $this->idtservicio;
    }

    function getOtrostservicio() {
        return $this->otrostservicio;
    }

    function getIdgpsvehiculo() {
        return $this->idgpsvehiculo;
    }

    function getOtrosgps() {
        return $this->otrosgps;
    }

    function getImei() {
        return $this->imei;
    }

    function getNumtelefono() {
        return $this->numtelefono;
    }

    function getIdinstalador() {
        return $this->idinstalador;
    }

    function getIdaccesorio() {
        return $this->idaccesorio;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getImg() {
        return $this->img;
    }

    function getImgactualizar() {
        return $this->imgactualizar;
    }

    function getIdinstalacion() {
        return $this->idinstalacion;
    }

    function getEncargado() {
        return $this->encargado;
    }

    function getFirma() {
        return $this->firma;
    }

    function getFirmaactual() {
        return $this->firmaactual;
    }

    function getModeloanterior() {
        return $this->modeloanterior;
    }

    function getImeianterior() {
        return $this->imeianterior;
    }

    function getSimanterior() {
        return $this->simanterior;
    }

    function getSesionid() {
        return $this->sesionid;
    }

    function getFolio() {
        return $this->folio;
    }

    function getImgfrente() {
        return $this->imgfrente;
    }

    function getImgnserie() {
        return $this->imgnserie;
    }

    function getImgtabinicial() {
        return $this->imgtabinicial;
    }

    function getImgtabfinal() {
        return $this->imgtabfinal;
    }

    function getImgfrenteactualizar() {
        return $this->imgfrenteactualizar;
    }

    function getImgnserieactualizar() {
        return $this->imgnserieactualizar;
    }

    function getImgtabactualizar() {
        return $this->imgtabactualizar;
    }

    function getImgtabfactualizar() {
        return $this->imgtabfactualizar;
    }

    function setIdhojaservicio($idhojaservicio) {
        $this->idhojaservicio = $idhojaservicio;
    }

    function setFechaservicio($fechaservicio) {
        $this->fechaservicio = $fechaservicio;
    }

    function setHoraservicio($horaservicio) {
        $this->horaservicio = $horaservicio;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }

    function setNombrecliente($nombrecliente) {
        $this->nombrecliente = $nombrecliente;
    }

    function setPlataforma($plataforma) {
        $this->plataforma = $plataforma;
    }

    function setMarca($marca) {
        $this->marca = $marca;
    }

    function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    function setAnho($anho) {
        $this->anho = $anho;
    }

    function setColor($color) {
        $this->color = $color;
    }

    function setSerie($serie) {
        $this->serie = $serie;
    }

    function setNumeconomico($numeconomico) {
        $this->numeconomico = $numeconomico;
    }

    function setKm($km) {
        $this->km = $km;
    }

    function setPlacas($placas) {
        $this->placas = $placas;
    }

    function setIddanhos($iddanhos) {
        $this->iddanhos = $iddanhos;
    }

    function setIdmolduras($idmolduras) {
        $this->idmolduras = $idmolduras;
    }

    function setOtrosmolduras($otrosmolduras) {
        $this->otrosmolduras = $otrosmolduras;
    }

    function setIdtablero($idtablero) {
        $this->idtablero = $idtablero;
    }

    function setOtrostablero($otrostablero) {
        $this->otrostablero = $otrostablero;
    }

    function setIdcableado($idcableado) {
        $this->idcableado = $idcableado;
    }

    function setOtroscableado($otroscableado) {
        $this->otroscableado = $otroscableado;
    }

    function setIdccorriente($idccorriente) {
        $this->idccorriente = $idccorriente;
    }

    function setOtrosccorriente($otrosccorriente) {
        $this->otrosccorriente = $otrosccorriente;
    }

    function setIdtservicio($idtservicio) {
        $this->idtservicio = $idtservicio;
    }

    function setOtrostservicio($otrostservicio) {
        $this->otrostservicio = $otrostservicio;
    }

    function setIdgpsvehiculo($idgpsvehiculo) {
        $this->idgpsvehiculo = $idgpsvehiculo;
    }

    function setOtrosgps($otrosgps) {
        $this->otrosgps = $otrosgps;
    }

    function setImei($imei) {
        $this->imei = $imei;
    }

    function setNumtelefono($numtelefono) {
        $this->numtelefono = $numtelefono;
    }

    function setIdinstalador($idinstalador) {
        $this->idinstalador = $idinstalador;
    }

    function setIdaccesorio($idaccesorio) {
        $this->idaccesorio = $idaccesorio;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setImg($img) {
        $this->img = $img;
    }

    function setImgactualizar($imgactualizar) {
        $this->imgactualizar = $imgactualizar;
    }

    function setIdinstalacion($idinstalacion) {
        $this->idinstalacion = $idinstalacion;
    }

    function setEncargado($encargado) {
        $this->encargado = $encargado;
    }

    function setFirma($firma) {
        $this->firma = $firma;
    }

    function setFirmaactual($firmaactual) {
        $this->firmaactual = $firmaactual;
    }

    function setModeloanterior($modeloanterior) {
        $this->modeloanterior = $modeloanterior;
    }

    function setImeianterior($imeianterior) {
        $this->imeianterior = $imeianterior;
    }

    function setSimanterior($simanterior) {
        $this->simanterior = $simanterior;
    }

    function setSesionid($sesionid) {
        $this->sesionid = $sesionid;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }

    function setImgfrente($imgfrente) {
        $this->imgfrente = $imgfrente;
    }

    function setImgnserie($imgnserie) {
        $this->imgnserie = $imgnserie;
    }

    function setImgtabinicial($imgtabinicial) {
        $this->imgtabinicial = $imgtabinicial;
    }

    function setImgtabfinal($imgtabfinal) {
        $this->imgtabfinal = $imgtabfinal;
    }

    function setImgfrenteactualizar($imgfrenteactualizar) {
        $this->imgfrenteactualizar = $imgfrenteactualizar;
    }

    function setImgnserieactualizar($imgnserieactualizar) {
        $this->imgnserieactualizar = $imgnserieactualizar;
    }

    function setImgtabactualizar($imgtabactualizar) {
        $this->imgtabactualizar = $imgtabactualizar;
    }

    function setImgtabfactualizar($imgtabfactualizar) {
        $this->imgtabfactualizar = $imgtabfactualizar;
    }
    
    function getTipounidad() {
        return $this->tipounidad;
    }

    function setTipounidad($tipounidad) {
        $this->tipounidad = $tipounidad;
    }

    function getImgAntesInstalacion(){
        return $this->imgantesinst;
    }

    function setImgAntesInstalacion($img){
        $this->imgantesinst = $img;
    }
    
    function getImgDespuesInstalacion(){
        return $this->imgdespuesinst;
    }

    function setImgDespuesInstalacion($img){
        $this->imgdespuesinst = $img;
    }

    function getIdAsignacion(){
        return $this->idasignacion;
    }

    function setIdAsignacion($val){
        $this->idasignacion = $val;
    }

    function getObservacionGral(){
        return $this->observaciones_gral;
    }

    function setObservacionesGral($val){
        $this->observaciones_gral = $val;
    }

    function getUbicacionPanico(){
        return $this->ubicacion_panico;
    }

    function setUbicacionPanico($val){
        $this->ubicacion_panico = $val;
    }
    
    function getTipoCorte(){
        return $this->tipo_corte;
    }

    function setTipoCorte($val){
        $this->tipo_corte = $val;
    }

}
