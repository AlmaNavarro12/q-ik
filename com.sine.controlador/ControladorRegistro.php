<?php

require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';



class ControladorRegistro {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }


    public function getDatosCuenta($val) {
        $datos = false;
        $rfc = "";
        $users = $this->getRFCUserAux($val);
        foreach ($users as $actual) {
            $rfc = $actual['rfc'];
        }
        $datos = $this->buscarCuentaAux($rfc);
        return $datos;
    }

    private function getRFCUserAux($uid) {
        $datos = false;
        $consulta = "SELECT * FROM usuarios WHERE (rfc LIKE '%$uid%') OR (mailcontacto LIKE '%$uid%');";
        $datos = $this->consultas->getResults($consulta, null);
        return $datos;
    }

    private function buscarCuentaAux($rfc) {
        $opt = "";
        $servidor = "localhost";
        $basedatos = "naga021226fj0";
        $puerto = "3306";
        $mysql_user = "root" ;
        $mysql_password = "";

        try {
            $db = new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos", $mysql_user, $mysql_password);
            $stmttable = $db->prepare("SELECT * FROM usuario ORDER BY idusuario;");

            if ($stmttable->execute()) {
                $resultado = $stmttable->fetchAll(PDO::FETCH_ASSOC);
                foreach ($resultado as $actual) {
                    $opt .= "<tr>
                                <td class='col-md-1'>
                                    <input type='radio' value='" . $actual['idusuario'] . "' name='chuser' class='input-radio' id='chuser" . $actual['idusuario'] . "'>
                                </td>
                                <td class='col-md-1'>
                                    <div class='img-user-recover'>
                                        <img src='../$rfc/img/usuarios/" . $actual['imgperfil'] . "'/>
                                    </div>
                                </td>
                                <td>
                                    " . $actual['nombre'] . ' ' . $actual['apellido_paterno'] . ' ' . $actual['apellido_materno'] . "
                                </td>
                            </tr>";
                }
                return $opt;
            } else {
                return "0";
            }
        } catch (PDOException $ex) {
            echo '0No se puede conectar a la bd ' . $ex->getMessage();
        }
    }
}