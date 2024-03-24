<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Ubicacion.php';

date_default_timezone_set("America/Mexico_City");

class ControladorUbicacion {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function checkUbicacionAux($nombre, $tipo, $uid = "") {
        $consultado = false;
        $mod = "";
        if ($uid != "") {
            $mod = " and idubicacion != :uid";
        }
        $consulta = "SELECT * FROM ubicacion where nombre=:nombre and tipoubicacion=:tipo and status=:st$mod;";
        $val = array("nombre" => $nombre,
            "tipo" => $tipo,
            "st" => '1',
            "uid" => $uid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkUbicacion($nombre, $tipo) {
        $check = false;
        $datos = $this->checkUbicacionAux($nombre, $tipo);
        foreach ($datos as $actual) {
            $check = true;
        }
        return $check;
    }

    public function nuevaUbicacion($u) {
        $datos = "";
        $check = $this->checkUbicacion($u->getNombre(), $u->getTipoubicacion());
        if ($check) {
            $datos = "0Ya existe una ubicación con este nombre";
        } else {
            $datos = $this->insertarUbicacion($u);
        }
        return $datos;
    }

    private function insertarUbicacion($u) {
        $insertado = false;
        $consulta = "INSERT INTO `ubicacion` VALUES (:id, :tipo, :nombre, :rfc, :calle, :numext, :numint, :codpostal, :referencia, :idestado, :nombre_estado, :idmun, :nombre_municipio, :localidad, :colonia, :estado);";
        $valores = array("id" => null,
            "tipo" => $u->getTipoubicacion(),
            "nombre" => $u->getNombre(),
            "rfc" => $u->getRfc(),
            "calle" => $u->getCalle(),
            "numext" => $u->getNumext(),
            "numint" => $u->getNumint(),
            "codpostal" => $u->getCodigopostal(),
            "referencia" => $u->getReferencia(),
            "idestado" => $u->getEstado(),
            "nombre_estado" => $u->getNombreEstado(),
            "idmun" => $u->getMunicipio(),
            "nombre_municipio" => $u->getNombreMunicipio(),
            "localidad" => $u->getLocalidad(),
            "colonia" => $u->getColonia(),
            "estado" => '1');
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(idubicacion) numrows FROM ubicacion u $condicion;";
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

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT p.editarubicacion,p.eliminarubicacion FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }
    
    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $editar = $actual['editarubicacion'];
            $eliminar = $actual['eliminarubicacion'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    private function getSevicios($condicion) {
        $consultado = false;
        $consulta = "SELECT u.* FROM ubicacion u $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function listaServiciosHistorial($pag, $REF, $tipo, $numreg) {
        require_once '../com.sine.common/pagination.php';
        Session::start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>Tipo</th>
                <th class='text-center'>Nombre</th>
                <th class='text-center'>RFC</th>
                <th class='col-md-5'>Dirección</th>
                <th class='text-center'>Opcion</th>
            </tr>
        </thead>
        <tbody>";

        $condicion = "";

        if ($REF == "" && $tipo == "") {
            $condicion = " WHERE status='1' ORDER BY tipoubicacion";
        } else {
            $tub = "";
            if ($tipo != "") {
                $tub = " AND tipoubicacion='$tipo'";
            }
            $condicion = "WHERE status='1' AND ((nombre LIKE '%$REF%') or (nombre_estado LIKE '%$REF%') or (rfcubicacion LIKE '%$REF%'))$tub ORDER BY tipoubicacion";
        }

        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $listas = $this->getSevicios($con);
        $finales = 0;
        foreach ($listas as $listaactual) {
            $idubicacion = $listaactual['idubicacion'];
            $tipoubicacion = $listaactual['tipoubicacion'];
            $nombre = $listaactual['nombre'];
            $rfc = $listaactual['rfcubicacion'];
            $calle = $listaactual['calle'];
            $numext = $listaactual['numext'];
            $numint = $listaactual['numint'];
            $codpostal = $listaactual['codpostal'];
            $estado = $listaactual['nombre_estado'];
            $idmunicipio = $listaactual['ubicacion_idmunicipio'];
            $nombre_municipio = $listaactual['nombre_municipio'];
            $colonia = $listaactual['colonia'];
            switch ($tipoubicacion) {
                case '1':
                    $tipo = "Origen";
                    break;
                case '2':
                    $tipo = "Destino";
                    break;
                default:
                    $tipo = "Origen";
                    break;
            }

            $numext = ($numext != "") ? "#" . $numext : "";
            $numint = ($numint != "") ? "Int. " . $numint : "";
            $col = ($colonia != "") ? "Col. " . $colonia . "," : "";
            $cp = ($codpostal != "") ? "CP " . $codpostal : "";
            $municipio = ($idmunicipio != '0') ? $nombre_municipio . "," : "";

            $datos .= "
                    <tr class='table-row'>
                        <td class='text-center'>$tipo</td>
                        <td class='text-center'>$nombre</td>
                        <td class='text-center'>$rfc</td>
                        <td>$calle $numext $numint $col $municipio $estado, $cp</td>
                        <td class='text-center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones' type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";
            if ($div[0] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarUbicacion($idubicacion);'>Editar ubicación <span class='text-muted fas fa-edit small'></span></a></li>";
            }
            if ($div[1] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarUbicacion($idubicacion);'>Eliminar ubicación <span class='text-muted fas fa-times'></span></a></li>";
            }
            $datos .= "</ul>
                        </div></td>
                    </tr>
                     ";
            $finales++;
        }
        
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarUbicacion";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='5'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='2' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='3'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    private function getUbicacionById($uid) {
        $consultado = false;
        $consulta = "SELECT * FROM ubicacion WHERE idubicacion=:uid;";
        $val = array("uid" => $uid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosUbicacion($uid) {
        $ubicacion = $this->getUbicacionById($uid);
        $datos = "";
        foreach ($ubicacion as $actual) {
            $idubicacion = $actual['idubicacion'];
            $tipoubicacion = $actual['tipoubicacion'];
            $nombre = $actual['nombre'];
            $rfc = $actual['rfcubicacion'];
            $calle = $actual['calle'];
            $numext = $actual['numext'];
            $numint = $actual['numint'];
            $codpostal = $actual['codpostal'];
            $referencia = $actual['referencia'];
            $idestado = $actual['ubicacion_idestado'];
            $idmunicipio = $actual['ubicacion_idmunicipio'];
            $localidad = $actual['localidad'];
            $colonia = $actual['colonia'];
            $datos .= "$idubicacion</tr>$tipoubicacion</tr>$nombre</tr>$rfc</tr>$calle</tr>$numext</tr>$numint</tr>$codpostal</tr>$referencia</tr>$idestado</tr>$idmunicipio</tr>$localidad</tr>$colonia";
            break;
        }
        return $datos;
    }

    public function modificarUbicacion($u) {
        $check = $this->checkUbicacionAux($u->getNombre(), $u->getTipoubicacion(), $u->getIdubicacion());
        if ($check) {
            $datos = "0Ya existe una ubicación con este nombre.";
        } else {
            $datos = $this->actualizarUbicacion($u);
        }
        return $datos;
    }

    private function actualizarUbicacion($u) {
        $actualizado = false;
        $consulta = "UPDATE `ubicacion` SET tipoubicacion=:tipo, nombre=:nombre, rfcubicacion=:rfc, calle=:calle, numext=:numext, numint=:numint, codpostal=:codpostal, referencia=:referencia, ubicacion_idestado=:idestado, nombre_estado=:nombre_estado, ubicacion_idmunicipio=:idmun, nombre_municipio=:nombre_municipio, localidad=:localidad, colonia=:colonia where idubicacion=:id;";
        $valores = array("id" => $u->getIdubicacion(),
            "tipo" => $u->getTipoubicacion(),
            "nombre" => $u->getNombre(),
            "rfc" => $u->getRfc(),
            "calle" => $u->getCalle(),
            "numext" => $u->getNumext(),
            "numint" => $u->getNumint(),
            "codpostal" => $u->getCodigopostal(),
            "referencia" => $u->getReferencia(),
            "idestado" => $u->getEstado(),
            "nombre_estado" => $u->getNombreEstado(),
            "idmun" => $u->getMunicipio(),
            "nombre_municipio" => $u->getNombreMunicipio(),
            "localidad" => $u->getLocalidad(),
            "colonia" => $u->getColonia());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminarUbicacion($uid) {
        $eliminado = false;
        $consulta = "UPDATE `ubicacion` SET status=:st WHERE idubicacion=:uid;";
        $valores = array("st" => '0',
            "uid" => $uid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }
}
