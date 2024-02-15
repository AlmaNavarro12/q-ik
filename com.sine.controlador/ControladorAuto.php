<?php

require_once '../com.sine.dao/Consultas.php';

class ControladorAuto {

    private $consultas;

    function __construct() {
        $this->consultas = new consultas();
    }

    public function getCoincidenciasBusquedaCliente($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM cliente WHERE (nombre_empresa LIKE '%$referencia%') OR (concat(nombre,' ',apaterno,' ',amaterno) LIKE '%$referencia%') OR (rfc LIKE '%$referencia%') OR (razon_social LIKE '%$referencia%') LIMIT 0,5;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $estado = $this->getEstadoAux($resultado['idestado']);
            $municipio = $this->getMunicipioAux($resultado['idmunicipio']);
            $int = "";
            if($resultado['numero_interior'] != ""){
                $int = " Int. ".$resultado['numero_interior'];
            }
            $datos[] = array("value" => $resultado['nombre'] . " " . $resultado['apaterno'] . " - " . $resultado['nombre_empresa'],
                "id" => $resultado["id_cliente"],
                "rfc" => $resultado['rfc'],
                "razon" => $resultado['razon_social'],
                "regfiscal" => $resultado['regimen_cliente'],
                "codpostal" => $resultado['codigo_postal'],
                "direccion" => $resultado['calle']." ".$resultado['numero_exterior'].$int." ".$resultado['localidad']." ".$municipio." ".$estado,
                "mailinfo" => $resultado['email_informacion'],
                "mailfacturas" => $resultado['email_facturacion'],
                "mailgerencia" => $resultado['email_gerencia']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    private function getEstadoAux($idestado) {
        $estado = "";
        $est = $this->getEstadoById($idestado);
        foreach ($est as $actual) {
            $estado = $actual['estado'];
        }
        return $estado;
    }
    
    private function getEstadoById($idestado) {
        $consultado = false;
        $consulta = "SELECT * FROM estado WHERE id_estado=:id;";
        $valores = array("id" => $idestado);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getMunicipioAux($idmun) {
        $municipio = "";
        $mun = $this->getMunicipioById($idmun);
        foreach ($mun as $actual) {
            $municipio = $actual['municipio'];
        }
        return $municipio;
    }

    private function getMunicipioById($idmun) {
        $consultado = false;
        $consulta = "SELECT * FROM municipio WHERE id_municipio=:id;";
        $valores = array("id" => $idmun);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }
}