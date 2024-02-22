<?php
class Proveedor{
    private $id_proveedor;
    private $empresa;
    private $representante;
    private $telefono;
    private $email;
    private $num_cuenta;
    private $clave_interbancaria;
    private $id_banco;
    private $nombre_banco;
    
    private $sucursal;
    private $rfc;
    private $razon;
    
    function __construct() {
    }
    
    function getId_proveedor() {
        return $this->id_proveedor;
    }

    function getEmpresa() {
        return $this->empresa;
    }

    function getRepresentante() {
        return $this->representante;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getEmail() {
        return $this->email;
    }

    function getNum_cuenta() {
        return $this->num_cuenta;
    }

    function getClave_interbancaria() {
        return $this->clave_interbancaria;
    }

    function getId_banco() {
        return $this->id_banco;
    }

    function getSucursal() {
        return $this->sucursal;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getRazon() {
        return $this->razon;
    }

    function setId_proveedor($id_proveedor) {
        $this->id_proveedor = $id_proveedor;
    }

    function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }

    function setRepresentante($representante) {
        $this->representante = $representante;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setNum_cuenta($num_cuenta) {
        $this->num_cuenta = $num_cuenta;
    }

    function setClave_interbancaria($clave_interbancaria) {
        $this->clave_interbancaria = $clave_interbancaria;
    }

    function setId_banco($id_banco) {
        $this->id_banco = $id_banco;
    }

    function getNombre_banco() {
        return $this->nombre_banco;
    }

    function setNombre_banco($nombre_banco) {
        $this->nombre_banco = $nombre_banco;
    }

    function setSucursal($sucursal) {
        $this->sucursal = $sucursal;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setRazon($razon) {
        $this->razon = $razon;
    }

}