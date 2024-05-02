<?php
class Usuario {
    //Todos los astributos de la tabla Usuario (Métodos Getter y Setter)
    
    private $idUsuario;
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
    private $chpass;
    private $img;
    private $imgactualizar;
    private $nameimg;

    function __construct() {
    }
    
    function getIdUsuario() {
        return $this->idUsuario;
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


    function getTelefono() {
        return $this->telefono;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getContrasena() {
        return $this->contrasena;
    }

    function getEstatus() {
        return $this->estatus;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
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

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setContrasena($contrasena) {
        $this->contrasena = $contrasena;
    }
    
    function getCelular() {
        return $this->celular;
    }

    function setCelular($celular) {
        $this->celular = $celular;
    }

    
    function setEstatus($estatus) {
        $this->estatus = $estatus;
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
    
    function getTipo() {
        return $this->tipo;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function getChpass() {
        return $this->chpass;
    }

    function setChpass($chpass) {
        $this->chpass = $chpass;
    }
    
    function getImg() {
        return $this->img;
    }

    function getImgactualizar() {
        return $this->imgactualizar;
    }

    function setImg($img) {
        $this->img = $img;
    }

    function setImgactualizar($imgactualizar) {
        $this->imgactualizar = $imgactualizar;
    }

    function getNameImg() {
        return $this->nameimg;
    }
    
    function setNameImg($nameimg) {
        $this->nameimg = $nameimg;
    }
}
