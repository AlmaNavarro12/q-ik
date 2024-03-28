<?php

class Reportes {

    private $fechainicio;
    private $fechafin;
    private $idcliente;
    private $estado;
    private $datos;
    private $tipo;
    private $usuario;
    private $moneda;
    //nuevo
    private $metodopago;
    private $formapago;

    function __construct() {
        
    }

    function getFechainicio() {
        return $this->fechainicio;
    }

    function getFechafin() {
        return $this->fechafin;
    }

    function getIdcliente() {
        return $this->idcliente;
    }

    function getEstado() {
        return $this->estado;
    }

    function setFechainicio($fechainicio) {
        $this->fechainicio = $fechainicio;
    }

    function setFechafin($fechafin) {
        $this->fechafin = $fechafin;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function getDatos() {
        return $this->datos;
    }

    function setDatos($datos) {
        $this->datos = $datos;
    }

    function getTipo() {
        return $this->tipo;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function getUsuario() {
        return $this->usuario;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }
    
    function getMoneda() {
        return $this->moneda;
    }

    function setMoneda($moneda) {
        $this->moneda = $moneda;
    }

    function setMetodopago($metodopago) {
        $this->metodopago = $metodopago;
    }

    function getMetodopago(){
        return $this->metodopago;
    }
    
    function setFormapago($formapago) {
       return $this->formapago = $formapago;
    }

    function getFormapago() {
        return $this->formapago;
    }
        
}