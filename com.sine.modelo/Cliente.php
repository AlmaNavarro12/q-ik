<?php

class Cliente {
    
    private $idCliente;
    private $iddatos_fiscales;
    private $idcategoria;
    private $nombre;
    private $apellidoPaterno;
    private $apellidoMaterno;
    private $nombre_empresa;
    private $correoinfo;
    private $correo_fact;
    private $correo_gerencia;
    private $telefono;
    private $idbanco;
    private $cuenta;
    private $clabe;
    private $idbanco1;
    private $cuenta1;
    private $clabe1;
    private $idbanco2;
    private $cuenta2;
    private $clabe2;
    private $idbanco3;
    private $cuenta3;
    private $clabe3;
    private $rfc;
    private $razon;
    private $regimen;
    private $calle;
    private $num_interior;
    private $num_exterior;
    private $localidad;
    private $municipio;
    private $estado;
    private $pais;
    private $codigo_postal;
    private $nombre_estado;
    private $nombre_municipio;
    private $correoalt1;
    private $correoalt2;
    private $correoalt3;
    private $nombrebanco1;
    private $nombrebanco2;
    private $nombrebanco3;
    private $nombrebanco4;

    
    
    function __construct() {
    }
    
    function getIdCliente() {
        return $this->idCliente;
    }

    function getIddatos_fiscales() {
        return $this->iddatos_fiscales;
    }

    function getIdcategoria() {
        return $this->idcategoria;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getApellidoPaterno() {
        return $this->apellidoPaterno;
    }

    function getApellidoMaterno() {
        return $this->apellidoMaterno;
    }

    function getNombre_empresa() {
        return $this->nombre_empresa;
    }

    function getCorreoinfo() {
        return $this->correoinfo;
    }

    function getCorreo_fact() {
        return $this->correo_fact;
    }

    function getCorreo_gerencia() {
        return $this->correo_gerencia;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getIdbanco() {
        return $this->idbanco;
    }

    function getCuenta() {
        return $this->cuenta;
    }

    function getClabe() {
        return $this->clabe;
    }

    function getIdbanco1() {
        return $this->idbanco1;
    }

    function getCuenta1() {
        return $this->cuenta1;
    }

    function getClabe1() {
        return $this->clabe1;
    }

    function getNombreEstado() {
        return $this->nombre_estado;
    }

    function getNombreMunicipio() {
        return $this->nombre_municipio;
    }

    function getIdbanco2() {
        return $this->idbanco2;
    }

    function getCuenta2() {
        return $this->cuenta2;
    }

    function getClabe2() {
        return $this->clabe2;
    }

    function getNombreBanco1() {
        return $this->nombrebanco1;
    }

    function getNombreBanco2() {
        return $this->nombrebanco2;
    }

    function getNombreBanco3() {
        return $this->nombrebanco3;
    }

    function getNombreBanco4() {
        return $this->nombrebanco4;
    }
    
    function getIdbanco3() {
        return $this->idbanco3;
    }

    function getCuenta3() {
        return $this->cuenta3;
    }

    function getClabe3() {
        return $this->clabe3;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getRazon() {
        return $this->razon;
    }

    function getRegimen() {
        return $this->regimen;
    }

    function getCalle() {
        return $this->calle;
    }

    function getNum_interior() {
        return $this->num_interior;
    }

    function getNum_exterior() {
        return $this->num_exterior;
    }

    function getLocalidad() {
        return $this->localidad;
    }

    function getMunicipio() {
        return $this->municipio;
    }

    function getEstado() {
        return $this->estado;
    }

    function getPais() {
        return $this->pais;
    }

    function getCodigo_postal() {
        return $this->codigo_postal;
    }

    function getCorreoalt1() {
        return $this->correoalt1;
    }

    function getCorreoalt2() {
        return $this->correoalt2;
    }

    function getCorreoalt3() {
        return $this->correoalt3;
    }

    function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
    }

    function setIddatos_fiscales($iddatos_fiscales) {
        $this->iddatos_fiscales = $iddatos_fiscales;
    }

    function setIdcategoria($idcategoria) {
        $this->idcategoria = $idcategoria;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setApellidoPaterno($apellidoPaterno) {
        $this->apellidoPaterno = $apellidoPaterno;
    }

    function setApellidoMaterno($apellidoMaterno) {
        $this->apellidoMaterno = $apellidoMaterno;
    }

    function setNombre_empresa($nombre_empresa) {
        $this->nombre_empresa = $nombre_empresa;
    }

    function setCorreoinfo($correoinfo) {
        $this->correoinfo = $correoinfo;
    }

    function setCorreo_fact($correo_fact) {
        $this->correo_fact = $correo_fact;
    }

    function setCorreo_gerencia($correo_gerencia) {
        $this->correo_gerencia = $correo_gerencia;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setIdbanco($idbanco) {
        $this->idbanco = $idbanco;
    }

    function setNombreEstado($nombre_estado) {
        $this->nombre_estado = $nombre_estado;
    }

    function setNombreMunicipio($nombre_municipio) {
        $this->nombre_municipio = $nombre_municipio;
    }


    function setCuenta($cuenta) {
        $this->cuenta = $cuenta;
    }

    function setClabe($clabe) {
        $this->clabe = $clabe;
    }

    function setIdbanco1($idbanco1) {
        $this->idbanco1 = $idbanco1;
    }

    function setCuenta1($cuenta1) {
        $this->cuenta1 = $cuenta1;
    }

    function setClabe1($clabe1) {
        $this->clabe1 = $clabe1;
    }

    function setIdbanco2($idbanco2) {
        $this->idbanco2 = $idbanco2;
    }

    function setCuenta2($cuenta2) {
        $this->cuenta2 = $cuenta2;
    }

    function setClabe2($clabe2) {
        $this->clabe2 = $clabe2;
    }

    function setIdbanco3($idbanco3) {
        $this->idbanco3 = $idbanco3;
    }

    function setCuenta3($cuenta3) {
        $this->cuenta3 = $cuenta3;
    }

    function setClabe3($clabe3) {
        $this->clabe3 = $clabe3;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setRazon($razon) {
        $this->razon = $razon;
    }

    function setRegimen($regimen) {
        $this->regimen = $regimen;
    }

    function setCalle($calle) {
        $this->calle = $calle;
    }

    function setNum_interior($num_interior) {
        $this->num_interior = $num_interior;
    }

    function setNum_exterior($num_exterior) {
        $this->num_exterior = $num_exterior;
    }

    function setLocalidad($localidad) {
        $this->localidad = $localidad;
    }

    function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setPais($pais) {
        $this->pais = $pais;
    }

    function setCodigo_postal($codigo_postal) {
        $this->codigo_postal = $codigo_postal;
    }

    function setCorreoalt1($correoalt1) {
        $this->correoalt1 = $correoalt1;
    }

    function setCorreoalt2($correoalt2) {
        $this->correoalt2 = $correoalt2;
    }

    function setCorreoalt3($correoalt3) {
        $this->correoalt3 = $correoalt3;
    }
    
    function setNombreBanco1($nombrebanco1) {
        $this->nombrebanco1 = $nombrebanco1;
    }

    function setNombreBanco2($nombrebanco2) {
        $this->nombrebanco2 = $nombrebanco2;
    }

    function setNombreBanco3($nombrebanco3) {
        $this->nombrebanco3 = $nombrebanco3;
    }

    function setNombreBanco4($nombrebanco4) {
        $this->nombrebanco4 = $nombrebanco4;
    }

}
