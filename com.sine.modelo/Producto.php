<?php

class Producto {

    private $idProducto;
    private $codproducto;
    private $producto;
    private $tipo;
    private $clvunidad;
    private $descripcionunidad;
    private $descripcion;
    private $precio_venta;
    private $porcentaje;
    private $ganancia;
    private $precio_compra;
    private $clavefiscal;
    private $descripcionfiscal;
    private $idproveedor;
    private $idinventario;
    private $idproser;
    private $chinventario;
    private $cantidad;
    private $imagen;
    private $imgactualizar;
    private $insert;
    private $idtmp;
    private $taxes;
    private $nameimg;


    function __construct() {
        
    }
    
    function getIdProducto() {
        return $this->idProducto;
    }

    function getCodproducto() {
        return $this->codproducto;
    }

    function getProducto() {
        return $this->producto;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getClvunidad() {
        return $this->clvunidad;
    }

    function getDescripcionunidad() {
        return $this->descripcionunidad;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getPrecio_venta() {
        return $this->precio_venta;
    }

    function getPorcentaje() {
        return $this->porcentaje;
    }

    function getGanancia() {
        return $this->ganancia;
    }

    function getNameImg() {
        return $this->nameimg;
    }

    function getPrecio_compra() {
        return $this->precio_compra;
    }

    function getClavefiscal() {
        return $this->clavefiscal;
    }

    function getDescripcionfiscal() {
        return $this->descripcionfiscal;
    }

    function getIdproveedor() {
        return $this->idproveedor;
    }

    function getIdinventario() {
        return $this->idinventario;
    }

    function getIdproser() {
        return $this->idproser;
    }

    function getChinventario() {
        return $this->chinventario;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getImagen() {
        return $this->imagen;
    }

    function getImgactualizar() {
        return $this->imgactualizar;
    }

    function getInsert() {
        return $this->insert;
    }

    function getTaxes() {
        return $this->taxes;
    }

    function setIdProducto($idProducto) {
        $this->idProducto = $idProducto;
    }

    function setCodproducto($codproducto) {
        $this->codproducto = $codproducto;
    }

    function setProducto($producto) {
        $this->producto = $producto;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setClvunidad($clvunidad) {
        $this->clvunidad = $clvunidad;
    }

    function setDescripcionunidad($descripcionunidad) {
        $this->descripcionunidad = $descripcionunidad;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setPrecio_venta($precio_venta) {
        $this->precio_venta = $precio_venta;
    }

    function setPorcentaje($porcentaje) {
        $this->porcentaje = $porcentaje;
    }

    function setGanancia($ganancia) {
        $this->ganancia = $ganancia;
    }

    function setPrecio_compra($precio_compra) {
        $this->precio_compra = $precio_compra;
    }

    function setClavefiscal($clavefiscal) {
        $this->clavefiscal = $clavefiscal;
    }

    function setDescripcionfiscal($descripcionfiscal) {
        $this->descripcionfiscal = $descripcionfiscal;
    }

    function setIdproveedor($idproveedor) {
        $this->idproveedor = $idproveedor;
    }

    function setIdinventario($idinventario) {
        $this->idinventario = $idinventario;
    }

    function setIdproser($idproser) {
        $this->idproser = $idproser;
    }

    function setChinventario($chinventario) {
        $this->chinventario = $chinventario;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setImagen($imagen) {
        $this->imagen = $imagen;
    }

    function setNameImg($nameimg) {
        $this->nameimg = $nameimg;
    }

    function setImgactualizar($imgactualizar) {
        $this->imgactualizar = $imgactualizar;
    }

    function setInsert($insert) {
        $this->insert = $insert;
    }
    
    function getIdtmp() {
        return $this->idtmp;
    }

    function setIdtmp($idtmp) {
        $this->idtmp = $idtmp;
    }

    function setTaxes($taxes) {
        $this->taxes = $taxes;
    }


}
