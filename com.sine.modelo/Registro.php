<?php

class Registro {

    private $idUsuario;
    private $rfc;
    private $razonsocial;
    private $nombre;
    private $apellidoPaterno;
    private $apellidoMaterno;
    private $usuario;
    private $contrasena;
    private $correo;
    private $celular;
    private $telefono;
    private $estatus;
    private $calle;
    private $numero;
    private $colonia;
    private $idestado;
    private $idmunicipio;
    private $tipo;
    private $numint;
    private $codp;
	private $paquete;

    function __construct() {
        
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getRazonsocial() {
        return $this->razonsocial;
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

    function getUsuario() {
        return $this->usuario;
    }

    function getContrasena() {
        return $this->contrasena;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getCelular() {
        return $this->celular;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getEstatus() {
        return $this->estatus;
    }

    function getCalle() {
        return $this->calle;
    }

    function getNumero() {
        return $this->numero;
    }

    function getColonia() {
        return $this->colonia;
    }

    function getIdestado() {
        return $this->idestado;
    }

    function getIdmunicipio() {
        return $this->idmunicipio;
    }

    function getTipo() {
        return $this->tipo;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setRazonsocial($razonsocial) {
        $this->razonsocial = $razonsocial;
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

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setContrasena($contrasena) {
        $this->contrasena = $contrasena;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setCelular($celular) {
        $this->celular = $celular;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setEstatus($estatus) {
        $this->estatus = $estatus;
    }

    function setCalle($calle) {
        $this->calle = $calle;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setColonia($colonia) {
        $this->colonia = $colonia;
    }

    function setIdestado($idestado) {
        $this->idestado = $idestado;
    }

    function setIdmunicipio($idmunicipio) {
        $this->idmunicipio = $idmunicipio;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function getNumint() {
        return $this->numint;
    }

    function getCodp() {
        return $this->codp;
    }

    function setNumint($numint) {
        $this->numint = $numint;
    }

    function setCodp($codp) {
        $this->codp = $codp;
    }

	function getPaquete() {
        return $this->paquete;
    }

    function setPaquete($paquete) {
        $this->paquete = $paquete;
    }

}
