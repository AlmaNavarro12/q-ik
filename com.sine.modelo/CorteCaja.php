<?php

class CorteCaja {

    private $totalventas;
    private $totalentradas;
    private $totalsalidas; 
    private $fondoinicio; 
    private $usuario; 
    private $fechaventa; 
    private $totalganancias; 
    private $idsupervisor;  
    private $comentarios;  
    private $sobrantes;  
    private $faltantes;  
    private $fechacorte;  
    private $horacorte;  
    private $tag;  


    public function getTotalventas() {
        return $this->totalventas;
    }

    public function setTotalventas($totalventas) {
        $this->totalventas = $totalventas;
    }

    public function getTotalentradas() {
        return $this->totalentradas;
    }

    public function setTotalentradas($totalentradas) {
        $this->totalentradas = $totalentradas;
    }

  
    public function getTotalsalidas() {
        return $this->totalsalidas;
    }

    public function setTotalsalidas($totalsalidas) {
        $this->totalsalidas = $totalsalidas;
    }
   
    public function getFondoinicio() {
        return $this->fondoinicio;
    }

    public function setFondoinicio($fondoinicio) {
        $this->fondoinicio = $fondoinicio;
    }

   
    public function getUsuario() {
        return $this->usuario;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function getFechaventa() {
        return $this->fechaventa;
    }

    public function setFechaventa($fechaventa) {
        $this->fechaventa = $fechaventa;
    }

    public function getTotalganancias() {
        return $this->totalganancias;
    }

    public function setTotalganancias($totalganancias) {
        $this->totalganancias = $totalganancias;
    }

    public function getIdsupervisor() {
        return $this->idsupervisor;
    }

    public function setIdsupervisor($idsupervisor) {
        $this->idsupervisor = $idsupervisor;
    }

    public function getComentarios() {
        return $this->comentarios;
    }

    public function setComentarios($comentarios) {
        $this->comentarios = $comentarios;
    }

    public function getSobrantes() {
        return $this->sobrantes;
    }

    public function setSobrantes($sobrantes) {
        $this->sobrantes = $sobrantes;
    }

    public function getFaltantes() {
        return $this->faltantes;
    }

    public function setFaltantes($faltantes) {
        $this->faltantes = $faltantes;
    }

    public function getFechacorte() {
        return $this->fechacorte;
    }

    public function setFechacorte($fechacorte) {
        $this->fechacorte = $fechacorte;
    }

    public function getHoracorte() {
        return $this->horacorte;
    }

    public function setHoracorte($horacorte) {
        $this->horacorte = $horacorte;
    }

    public function getTag() {
        return $this->tag;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }
}