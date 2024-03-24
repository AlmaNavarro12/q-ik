<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';

class ControladorOperador {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function validarExistenciaOperador($rfc, $numlicencia, $idusuario) {
        $existe = false;
        $operadores = $this->getOperadorByRFC($rfc);
        foreach ($operadores as $actual) {
            $idusuarioactual = $actual['idoperador'];
            if ($idusuarioactual != $idusuario) {
                echo "0Ya está registrado un operador con este RFC.";
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            if ($numlicencia != "") {
                $usuarios = $this->getOperadorByLicencia($numlicencia);
                foreach ($usuarios as $usuarioactual) {
                    $idusuarioactual = $usuarioactual['idoperador'];
                    if ($idusuarioactual != $idusuario) {
                        echo "0Este número de licencia ya está registrado para otro operador.";
                        $existe = true;
                        break;
                    }
                }
            }
        }
        return $existe;
    }

    private function getOperadorByRFC($rfc) {
        $consultado = false;
        $consulta = "SELECT * FROM operador WHERE rfcoperador=:rfc and opstatus=:st;";
        $valores = array("rfc" => $rfc,
            "st" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getOperadorByLicencia($numlic) {
        $consultado = false;
        $consulta = "SELECT * FROM operador WHERE numlicencia=:lic and opstatus=:st;";
        $valores = array("lic" => $numlic,
            "st" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function nuevoOperador($o) {
        $existe = $this->validarExistenciaOperador($o->getRfc(), $o->getNumlicencia(), 0);
        $insertado = false;
        if (!$existe) {
            $insertado = $this->insertarOperador($o);
        }
        return $existe;
    }
    
    private function insertarOperador($o) {
        $registrado = false;
        $consulta = "INSERT INTO `operador` VALUES (:id, :nombre, :apaterno, :amaterno, :licencia, :rfc, :empresa, :idestado, :nombre_estado, :idmunicipio, :nombre_municipio, :calle, :cp, :st);";
        $valores = array("id" => null,
            "nombre" => $o->getNombre(), 
            "apaterno" => $o->getApaterno(),
            "amaterno" => $o->getAmaterno(),
            "licencia" => $o->getNumlicencia(),
            "rfc" => $o->getRfc(),
            "empresa" => $o->getEmpresa(),
            "idestado" => $o->getIdestado(),
            "nombre_estado" => $o->getNombreEstado(),
            "idmunicipio" => $o->getIdmunicipio(),
            "nombre_municipio" => $o->getNombreMunicipio(),
            "calle" => $o->getCalle(),
            "cp" => $o->getCodpostal(),
            "st" => '1');
        $registrado = $this->consultas->execute($consulta, $valores);
        return $registrado;
    }
    
    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM operador o $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrows($condicion) {
        $numrows = 0;
        $rows = $this->getNumrowsAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }
    
    private function getOperadores($condicion) {
        $consultado = false;
        $consulta = "SELECT o.*  FROM operador o $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }
    
    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT p.editaroperador, p.eliminaroperador FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $editar = $actual['editaroperador'];
            $eliminar = $actual['eliminaroperador'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    public function listaOperadoresHistorial($REF, $numreg, $pag) {
        include '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>Nombre </th>
                <th class='text-center'>No. Licencia </th>
                <th class='text-center'>RFC </th>
                <th class='text-center'>Empresa </th>
                <th class='text-center'>Dirección </th>
                <th class='text-center'>Opción</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($REF == "") {
            $condicion = " WHERE opstatus='1' ORDER BY o.nombreoperador";
        } else {
            $condicion = "WHERE opstatus='1' AND ((concat(nombreoperador,' ',apaternooperador,' ',amaternooperador) LIKE '%$REF%') OR (numlicencia LIKE '%$REF%') OR (rfcoperador LIKE '%$REF%') OR (empresa LIKE '%$REF%')) ORDER BY o.nombreoperador;";
        }
        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $operador = $this->getOperadores($con);
        $finales = 0;
        foreach ($operador as $actual) {
            $idoperador = $actual['idoperador'];
            $nombre = $actual['nombreoperador'];
            $apaterno = $actual['apaternooperador'];
            $amaterno = $actual['amaternooperador'];
            $numlicencia = $actual['numlicencia'];
            $rfcoperador = $actual['rfcoperador'];
            $empresa = $actual['empresa'];
            $nombre_estado = $actual['nombre_estado'];
            $idmunicipio = $actual['operador_idmunicipio'];
            $nombre_municipio = $actual['nombre_municipio'];
            $calle = $actual['calle'];
            $cpoperador= $actual['cpoperador'];
            $municipio = "";
            
            if ($calle != ""){
                $calle = "$calle, ";
            }
            
            if($idmunicipio != '0'){
                $municipio = $nombre_municipio.", ";
            }
            $direccion = "$calle $cpoperador, $municipio $nombre_estado";

            $datos .= "<tr>
                         <td class='col-md-2'>$nombre $apaterno $amaterno</td>
                         <td class='text-center col-md-2'>$numlicencia</td>
                         <td class='text-center col-md-2'>$rfcoperador</td>
                         <td class='text-center col-md-2'>$empresa</td>
                         <td class='col-md-3'>$direccion</td>
                         <td class='text-center col-md-1'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";
            
            if($div[0] == '1'){
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarOperador($idoperador)'>Editar operador <span class='text-muted fas fa-edit small'></span></a></li>";
            }
            
            if($div[1] == '1'){
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarOperador($idoperador)'>Eliminar operador <span class='text-muted fas fa-times'></span></a></li>";
            }
                        
            $datos .= "</ul>
                        </div></td> 
                   </tr>";
            
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarOperador";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='11'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='4' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='7'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }
    
    private function getOperadorById($oid) {
        $consultado = false;
        $consulta = "SELECT * FROM operador o WHERE o.idoperador=:oid;";
        $valores = array("oid" => $oid);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosOperador($oid) {
        $operador = $this->getOperadorById($oid);
        $datos = "";
        foreach ($operador as $actual) {
            $idoperador = $actual['idoperador'];
            $nombre = $actual['nombreoperador'];
            $apaterno = $actual['apaternooperador'];
            $amaterno = $actual['amaternooperador'];
            $numlicencia = $actual['numlicencia'];
            $rfc = $actual['rfcoperador'];
            $empresa = $actual['empresa'];
            $idestado = $actual['operador_idestado'];
            $idmunicipio = $actual['operador_idmunicipio'];
            $calle = $actual['calle'];
            $cpoperador = $actual['cpoperador'];

            $datos = "$idoperador</tr>$nombre</tr>$apaterno</tr>$amaterno</tr>$numlicencia</tr>$rfc</tr>$empresa</tr>$idestado</tr>$idmunicipio</tr>$calle</tr>$cpoperador";
            break;
        }
        return $datos;
    }
    
    public function modificarUsuario($o) {
        $existe = $this->validarExistenciaOperador($o->getRfc(), $o->getNumlicencia(), $o->getIdoperador());
        $actualizado = false;
        if (!$existe) {
            $actualizado = $this->actualizarOperador($o);
        }
        return $actualizado;
    }

    private function actualizarOperador($o) {
        $actualizado = false;
        $consulta = "UPDATE `operador` SET  nombreoperador=:nombre, apaternooperador=:apaterno, amaternooperador=:amaterno, numlicencia=:licencia, rfcoperador=:rfc, empresa=:empresa, operador_idestado=:idestado, nombre_estado=:nombre_estado, operador_idmunicipio=:idmunicipio, nombre_municipio=:nombre_municipio, calle=:calle, cpoperador=:cp WHERE idoperador=:id;";
        $valores = array("id" => $o->getIdoperador(),
            "nombre" => $o->getNombre(),
            "apaterno" => $o->getApaterno(),
            "amaterno" => $o->getAmaterno(),
            "licencia" => $o->getNumlicencia(),
            "rfc" => $o->getRfc(),
            "empresa" => $o->getEmpresa(),
            "idestado" => $o->getIdestado(),
            "nombre_estado" => $o->getNombreEstado(),
            "idmunicipio" => $o->getIdmunicipio(),
            "nombre_municipio" => $o->getNombreMunicipio(),
            "calle" => $o->getCalle(),
            "cp" => $o->getCodpostal());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }
    
    public function quitarOperador($idoperador) {
        $eliminado = $this->eliminarOperador($idoperador);
        return $eliminado;
    }

    private function eliminarOperador($idoperador) {
        $eliminado = false;
        $consulta = "UPDATE `operador` SET  opstatus=:st WHERE idoperador=:id;";
        $valores = array("id" => $idoperador,
            "st" => '0');
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }
}