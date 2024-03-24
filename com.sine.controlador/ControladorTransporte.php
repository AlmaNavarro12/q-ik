<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Transporte.php';
require_once '../com.sine.modelo/Remolque.php';

date_default_timezone_set("America/Mexico_City");

class ControladorTransporte {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function checkUnidadAux($placa, $tid = "") {
        $consultado = false;
        $mod = "";
        if ($tid != "") {
            $mod = " and idtransporte!=:tid";
        }
        $consulta = "SELECT * FROM transporte where placavehiculo=:placa and status='1'$mod;";
        $val = array("placa" => $placa,
            "tid" => $tid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkUnidad($placa) {
        $check = false;
        $datos = $this->checkUnidadAux($placa);
        foreach ($datos as $actual) {
            $check = true;
        }
        return $check;
    }

    public function nuevoTransporte($t) {
        $datos = "";
        $check = $this->checkUnidad($t->getPlacavehiculo());
        if ($check) {
            $datos = "0Ya esta registrado un vehículo con este número de placa.";
        } else {
            $datos = $this->insertarTransporte($t);
        }
        return $datos;
    }

    private function insertarTransporte($t) {
        $insertado = false;
        $consulta = "INSERT INTO `transporte` VALUES (:id, :nombre, :numpermiso, :tipopermiso, :conftransporte, :anho, :placa, :segurorc, :polizarc, :seguroma, :polizama, :segurocg, :polizacg, :st);";
        $valores = array("id" => null,
            "nombre" => $t->getNombre(),
            "numpermiso" => $t->getNumpermiso(),
            "tipopermiso" => $t->getTipopermiso(),
            "conftransporte" => $t->getConftransporte(),
            "anho" => $t->getAnhomodelo(),
            "placa" => $t->getPlacavehiculo(),
            "segurorc" => $t->getSegurorc(),
            "polizarc" => $t->getPolizarc(),
            "seguroma" => $t->getSeguroma(),
            "polizama" => $t->getPolizama(),
            "segurocg" => $t->getSegurocg(),
            "polizacg" => $t->getPolizacg(),
            "st" => '1');
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT p.editartransporte, p.eliminartransporte, p.editarremolque, p.eliminarremolque FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisosTransporte($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $editar = $actual['editartransporte'];
            $eliminar = $actual['eliminartransporte'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(idtransporte) numrows FROM transporte $condicion;";
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

    private function getTransportes($condicion) {
        $consultado = false;
        $consulta = "SELECT * FROM transporte $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function listaTransportesHistorial($pag, $REF, $numreg) {
        require_once '../com.sine.common/pagination.php';
        Session::start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>Nombre</th>
                <th class='text-center'>A&ntilde;o</th>
                <th class='text-center'>Placa</th>
                <th class='text-center'>N&uacute;mero Permiso</th>
                <th class='text-center'>Tipo Permiso</th>
                <th class='text-center'>Opción</th>
            </tr>
        </thead>
        <tbody>";

        $condicion = "";

        if ($REF == "") {
            $condicion = " WHERE status='1' ORDER BY nombrevehiculo";
        } else {
            $condicion = "WHERE status='1' and (nombrevehiculo LIKE '%$REF%' or placavehiculo LIKE '%$REF%') ORDER BY nombrevehiculo";
        }

        $permisos = $this->getPermisosTransporte($idlogin);
        $div = explode("</tr>", $permisos);

        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $listas = $this->getTransportes($con);
        $finales = 0;
        foreach ($listas as $listaactual) {
            $tid = $listaactual['idtransporte'];
            $nombre = $listaactual['nombrevehiculo'];
            $numpermiso = $listaactual['numeropermiso'];
            $tipopermiso = $listaactual['tipopermiso'];
            $anho = $listaactual['anhomodelo'];
            $placa = $listaactual['placavehiculo'];

            $datos .= "
                    <tr class='table-row'>
                        <td class='text-center'>$nombre</td>
                        <td class='text-center'>$anho</td>
                        <td class='text-center'>$placa</td>
                        <td class='text-center'>$numpermiso</td>
                        <td class='text-center'>$tipopermiso</td>
                        <td class='text-center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";

            if ($div[0] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarTransporte($tid);'>Editar vehículo <span class='text-muted fas fa-edit small'></span></a></li>";
            }
            if ($div[1] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarVehiculo($tid);'>Eliminar vehículo <span class='text-muted fas fa-times'></span></a></li>";
            }

            $datos .= "</ul>
                        </div></td>
                    </tr>
                     ";
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarTransporte";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='6'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='4'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    private function getVehiculoById($vid) {
        $consultado = false;
        $consulta = "SELECT * FROM transporte WHERE idtransporte=:tid;";
        $val = array("tid" => $vid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosTransporte($vid) {
        $ubicacion = $this->getVehiculoById($vid);
        $datos = "";
        foreach ($ubicacion as $actual) {
            $idtransporte = $actual['idtransporte'];
            $nombre = $actual['nombrevehiculo'];
            $numpermiso = $actual['numeropermiso'];
            $tipopermiso = $actual['tipopermiso'];
            $conftransporte = $actual['conftransporte'];
            $anho = $actual['anhomodelo'];
            $placa = $actual['placavehiculo'];
            $segurocivil = $actual['seguroCivil'];
            $polizacivil = $actual['polizaCivil'];
            $seguroambiente = $actual['seguroAmbiente'];
            $polizaambiente = $actual['polizaambiente'];
            $segurocarga = $actual['seguroCarga'];
            $polizacarga = $actual['polizaCarga'];
            $datos .= "$idtransporte</tr>$nombre</tr>$numpermiso</tr>$tipopermiso</tr>$conftransporte</tr>$anho</tr>$placa</tr>$segurocivil</tr>$polizacivil</tr>$seguroambiente</tr>$polizaambiente</tr>$segurocarga</tr>$polizacarga";
            break;
        }
        return $datos;
    }

    public function modificarTransporte($t) {
        $datos = false;
        $check = $this->checkUnidadAux($t->getPlacavehiculo(), $t->getIdtransporte());
        if ($check) {
            $datos = "0Ya esta registrado un vehículo con este número de placa.";
        } else {
            $datos = $this->actualizarTransporte($t);
        }
        return $datos;
    }

    private function actualizarTransporte($t) {
        $actualizado = false;
        $consulta = "UPDATE `transporte` SET nombrevehiculo=:nombre, numeropermiso=:numpermiso, tipopermiso=:tipo, conftransporte=:conf, anhomodelo=:anho, placavehiculo=:placa, seguroCivil=:segurorc, polizaCivil=:polizarc, seguroAmbiente=:seguroma, polizaambiente=:polizama, seguroCarga=:segurocg, polizaCarga=:polizacg where idtransporte=:id;";
        $valores = array("id" => $t->getIdtransporte(),
            "nombre" => $t->getNombre(),
            "numpermiso" => $t->getNumpermiso(),
            "tipo" => $t->getTipopermiso(),
            "conf" => $t->getConftransporte(),
            "anho" => $t->getAnhomodelo(),
            "placa" => $t->getPlacavehiculo(),
            "segurorc" => $t->getSegurorc(),
            "polizarc" => $t->getPolizarc(),
            "seguroma" => $t->getSeguroma(),
            "polizama" => $t->getPolizama(),
            "segurocg" => $t->getSegurocg(),
            "polizacg" => $t->getPolizacg());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminarTransporte($tid) {
        $eliminado = false;
        $consulta = "UPDATE `transporte` SET status=:st WHERE idtransporte=:tid;";
        $valores = array("st" => '0',
            "tid" => $tid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function checkRemolqueAux($placa, $rid = "") {
        $consultado = false;
        $mod = "";
        if ($rid != "") {
            $mod = " and idremolque!=:tid";
        }
        $consulta = "SELECT * FROM remolque where placaremolque=:placa and status='1'$mod;";
        $val = array("placa" => $placa,
            "rid" => $rid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkRemolque($placa) {
        $check = false;
        $datos = $this->checkRemolqueAux($placa);
        foreach ($datos as $actual) {
            $check = true;
        }
        return $check;
    }

    public function nuevoRemolque($r) {
        $datos = "";
        $check = $this->checkRemolque($r->getPlacaremolque());
        if ($check) {
            $datos = "0Ya esta registrado un remolque con este número de placa";
        } else {
            $datos = $this->insertarRemolque($r);
        }
        return $datos;
    }

    private function insertarRemolque($r) {
        $insertado = false;
        $consulta = "INSERT INTO `remolque` VALUES (:id, :nombre, :tipo, :placa, :st);";
        $valores = array("id" => null,
            "nombre" => $r->getNombre(),
            "tipo" => $r->getTiporemolque(),
            "placa" => $r->getPlacaremolque(),
            "st" => '1');
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getNumrowsRemAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(idremolque) numrows FROM remolque $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrowsRem($condicion) {
        $numrows = 0;
        $rows = $this->getNumrowsRemAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    private function getRemolques($condicion) {
        $consultado = false;
        $consulta = "SELECT * FROM remolque $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getPermisosRem($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $editar = $actual['editarremolque'];
            $eliminar = $actual['eliminarremolque'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    public function listaRemolquesHistorial($pag, $REF, $numreg) {
        require_once '../com.sine.common/pagination.php';
        Session::start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>Nombre</th>
                <th class='text-center'>Placa</th>
                <th class='text-center'>Tipo Remolque</th>
                <th class='col-md-2 text-center'>Opción</th>
            </tr>
        </thead>
        <tbody>";

        $condicion = "";

        if ($REF == "") {
            $condicion = "WHERE status='1' ORDER BY nombreremolque";
        } else {
            $condicion = "WHERE status='1' and (nombreremolque LIKE '%$REF%' or placaremolque LIKE '%$REF%') ORDER BY nombreremolque";
        }

        $permisos = $this->getPermisosRem($idlogin);
        $div = explode("</tr>", $permisos);

        $numrows = $this->getNumrowsRem($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $listas = $this->getRemolques($con);
        $finales = 0;
        foreach ($listas as $listaactual) {
            $rid = $listaactual['idremolque'];
            $nombre = $listaactual['nombreremolque'];
            $tiporemolque = $listaactual['tiporemolque'];
            $placa = $listaactual['placaremolque'];

            $datos .= "
                    <tr class='table-row'>
                        <td class='text-center'>$nombre</td>
                        <td class='text-center'>$placa</td>
                        <td class='text-center'>$tiporemolque</td>
                        <td class='text-center'>
                        <div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones' type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";
            if ($div[0] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarRemolque($rid);'>Editar remolque <span class='text-muted fas fa-edit small'></span></a></li>";
            }
            if ($div[1] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarRemolque($rid);'>Eliminar remolque <span class='fas fa-times text-muted'></span></a></li>";
            }

            $datos .= "</ul>
                        </div></td>
                    </tr>
                     ";
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarRemolque";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='6'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='2' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='4'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    private function getRemolqueById($rid) {
        $consultado = false;
        $consulta = "SELECT * FROM remolque WHERE idremolque=:rid;";
        $val = array("rid" => $rid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosRemolque($rid) {
        $remolque = $this->getRemolqueById($rid);
        $datos = "";
        foreach ($remolque as $actual) {
            $idremolque = $actual['idremolque'];
            $nombre = $actual['nombreremolque'];
            $tipo = $actual['tiporemolque'];
            $placa = $actual['placaremolque'];

            $datos .= "$idremolque</tr>$nombre</tr>$tipo</tr>$placa";
            break;
        }
        return $datos;
    }

    public function modificarRemolque($r) {
        $datos = false;
        $check = $this->checkRemolqueAux($r->getPlacaremolque(), $r->getIdremolque());
        if ($check) {
            $datos = "0Ya esta registrado un remolque con este número de placa.";
        } else {
            $datos = $this->actualizarRemolque($r);
        }
        return $datos;
    }

    private function actualizarRemolque($r) {
        $actualizado = false;
        $consulta = "UPDATE `remolque` SET nombreremolque=:nombre, tiporemolque=:tipo, placaremolque=:placa where idremolque=:id;";
        $valores = array("id" => $r->getIdremolque(),
            "nombre" => $r->getNombre(),
            "tipo" => $r->getTiporemolque(),
            "placa" => $r->getPlacaremolque());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminarRemolque($rid) {
        $eliminado = false;
        $consulta = "UPDATE `remolque` SET status=:st WHERE idremolque=:rid;";
        $valores = array("st" => '0',
            "rid" => $rid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }
}