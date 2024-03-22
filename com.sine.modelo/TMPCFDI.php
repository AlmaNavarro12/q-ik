<?php

class TMPCFDI {

    private $idtmpcfdi;
    private $tiporel;
    private $uuid;
    private $sessionid;

    private $id_doc;
    private $folio_doc;
    private $type;
    private $descripcion;
    private $tipo_comp;
    
    function __construct() {
        
    }
    
    function getIdtmpcfdi() {
        return $this->idtmpcfdi;
    }

    function getTiporel() {
        return $this->tiporel;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getSessionid() {
        return $this->sessionid;
    }

    function getIdDoc(){
        return $this->id_doc;
    }

    function getFolioDoc(){
        return $this->folio_doc;
    }

    function getType(){
        return $this->type;
    }

    function getDescripcion(){
        return $this->descripcion;
    }

    function getTipoComprobante(){
        return $this->tipo_comp;
    }

    function setIdtmpcfdi($idtmpcfdi) {
        $this->idtmpcfdi = $idtmpcfdi;
    }

    function setTiporel($tiporel) {
        $this->tiporel = $tiporel;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setSessionid($sessionid) {
        $this->sessionid = $sessionid;
    }

    function setIdDoc($val){
        $this->id_doc = $val;
    }

    function setFolioDoc($val){
        $this->folio_doc = $val;
    }

    function setType($val){
        $this->type = $val;
    }

    function setDescripcion($val){
        $this->descripcion = $val;
    }

    function setTipoComprobante($tipo_comprobante){
        $this->tipo_comp = $tipo_comprobante;
    }

}
