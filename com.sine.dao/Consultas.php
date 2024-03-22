<?php
require 'Conexion.php';
class Consultas extends Conexion {

    private $conexion;
    
    function __construct() {
        $this->conexion = parent::conectar();
        return $this->conexion;
    }

    public function execute($consulta, $valores) {
        $resultado = false;
        $statement = $this->conexion->prepare($consulta);
        if ($statement) {
            if (preg_match_all("/(:\w+)/", $consulta, $campo, PREG_PATTERN_ORDER)) {
                $campo = array_pop($campo);
                foreach ($campo as $parametro) {
                    $statement->bindValue($parametro, $valores[substr($parametro, 1)]);
                }
            }
            try {

                if (!$statement->execute()) {
                    echo '0' . var_dump($statement->errorInfo());
                } else {
                    $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);
                    $statement->closeCursor();
                    $resultado = true;
                }
            } catch (PDOException $evento) {
                echo "0No puede ejecutar la consulta.";
                echo $evento->getMessage();
            }
        }
        return $resultado;
        $this->conexion = null;
    }

    public function getResults($consulta, $valores) {
        $resultado = "0";
        $statement = $this->conexion->prepare($consulta);
        if ($statement) {
            if (preg_match_all("/(:\w+)/", $consulta, $campo, PREG_PATTERN_ORDER)) {
                $campo = array_pop($campo);
                foreach ($campo as $parametro) {
                    $statement->bindValue($parametro, $valores[substr($parametro, 1)]);
                }
            }
            try {
                if (!$statement->execute()) {
                    $resultado = "0" . $statement->errorInfo();
                } else {
                    $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);
                    $statement->closeCursor();
                }
            } catch (PDOException $evento) {
                $resultado .= "0No puede ejecutar la consulta:" . $evento->getMessage();
            }
        }
        return $resultado;
        $this->conexion = null;
    }
}