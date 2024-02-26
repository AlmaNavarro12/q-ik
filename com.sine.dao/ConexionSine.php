<?php

abstract class ConexionSine {

    protected $conexion_bd;
    protected $datahost;
    
    function __construct() {
    }

    protected function conectar($archivo='sineacceso.ini'){
        $ajustes=  parse_ini_file($archivo,true);
        if(!$ajustes){
            throw new Exception("No se puede abrir el archivo ".$archivo);
        }
        $controlador=$ajustes['database']['driver'];
        $servidor=$ajustes['database']['host'];
        $puerto=$ajustes['database']['port'];
        $basedatos=$ajustes['database']['schema'];
        $password=$ajustes['database']['password'];
        $usuario=$ajustes['database']['username'];
        try{
            return $this->datahost=new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos",$usuario,$password,array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
        } catch (PDOException $ex) {
            echo 'No se puede conectar a la bd '.$ex->getMessage();
        }
    }
}
