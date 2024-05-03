<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Instalacion.php';

date_default_timezone_set("America/Mexico_City");
use PHPMailer\PHPMailer\PHPMailer;

class ControladorInstalacion {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    public function getFecha(){
        $datos = "";
        $hoy = date("Y-m-d");
        $hora = date("H:i");
        $datos = $hoy;
        return $datos."</tr>".$hora;
    }

    //-----------------------------------------------EQUIPO GPS
    public function validarExisteGps($gps){
        $insertado = false;
        $consulta = "SELECT COUNT(*) AS total FROM equipogps WHERE nombreequipo = :gps";
        $resultado = $this->consultas->getResults($consulta, array("gps" => $gps));
        return $resultado[0]['total'] > 0;
    }

    public function insertarGPS($gps, $idGPS) {
        $existe = $this->validarExisteGPS($gps);
        if($existe){
            return "0Ya existe un modelo GPS con este nombre ".$gps.".";
        }
    
        if($idGPS == 0){
            $consulta = "INSERT INTO equipogps (nombreequipo) VALUES (:gps)";
            $valores = array("gps" => $gps);
        } else {
            $consulta = "UPDATE equipogps SET nombreequipo = :gps WHERE idequipogps = :id";
            $valores = array("id" => $idGPS, "gps" => $gps);
        }
        
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminaGPS($id){ 
        $insertado = false;
        $consulta = "DELETE FROM equipogps WHERE (idequipogps = :id)";
        $valores = array("id" => $id);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getNumrowsAuxGPS($condicion) {
        $consultado = false;
        $consulta = "SELECT COUNT(*) numrows FROM equipogps $condicion";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrowsGPS($condicion) {
        $numrows = 0;
        $rows = $this->getNumrowsAuxGPS($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    public function getGPS($condicion) {
        $consultado = false;
        $consulta = "SELECT * FROM equipogps $condicion ";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getPermisoById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso p where permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario)
    {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listagps'];
            $editar = $actual['editargps'];
            $eliminar = $actual['eliminargps'];

            $datos .= "$lista</tr>$editar</tr>$eliminar";
        }
        return $datos;
    }

    public function listaGPS($REF, $numreg, $pag)
    {
        $idlogin = $_SESSION[sha1("idusuario")];
        include '../com.sine.common/pagination.php';
        $datos = "<thead>
            <tr class='align-middle'>
            <th class='col-4 text-center'>Folio </th>
            <th class='col-4 text-center'>Nombre del equipo </th>
            <th class='col-4 text-center'>Opción</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($REF != "") {
            $condicion = "WHERE (nombreequipo LIKE '%$REF%') ";
        }

        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        if ($div[0] == '1') {
            $numrows = $this->getNumrowsGPS($condicion);
            $page = (isset($pag) && !empty($pag)) ? $pag : 1;
            $per_page = $numreg;
            $adjacents = 4;
            $offset = ($page - 1) * $per_page;
            $total_pages = ceil($numrows / $per_page);
            $con = $condicion . "ORDER BY idequipogps LIMIT $offset,$per_page ";
            $lista = $this->getGPS($con);
            $finales = 0;
            foreach ($lista as $listaActual) {

                $idequipogps = $listaActual['idequipogps'];
                $nombreequipo = $listaActual['nombreequipo'];

                $datos .= "
                    <tr>
                        <td class='text-center'>$idequipogps</td>
                        <td class='text-center'>$nombreequipo</td>
                        <td class='text-center'>
                            <div class='dropdown'>
                                <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'>
                                    <span class='fas fa-ellipsis-v text-muted'></span>
                                </button>
                                <ul class='dropdown-menu dropdown-menu-right z-1'>";
                if ($div[1] == '1') {
                    $datos .= " <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarGPS($idequipogps, \"$nombreequipo\");'>Editar equipo <span class='fas fa-edit small text-muted'></span></a></li>";
                }
                if ($div[2] == '1') {
                    $datos .= " <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarGPS($idequipogps);'>Eliminar equipo <span class='fas fa-times text-muted'></span></a></li>";
                }
                $datos .= "</ul>
                            </div>
                        </td>
                    </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "loadListaGPS";
            $datos .= "</tbody><tfoot><tr><th colspan='1' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='2'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

            if ($finales == 0) {
                $datos .= "<tr><td colspan='12'>No se encontraron registros</td></tr>";
            }
        }
        return $datos;
    }

    private function getInstalador() {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE tipo = 4 ORDER BY nombre";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesInstalador() {
        $opciones = $this->getInstalador();
        $r = "";
        foreach ($opciones as $actual) {
            $r = $r . "<option value='" . $actual['idusuario'] . "' id='instalador" . $actual['idusuario'] . "'>" . $actual['nombre'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'] . "</option>";
        }
        return $r;
    }

    public function getInstaladoresCH(){
        $n = 0;
        $datos = "";
        $query = "SELECT CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) nombre, idusuario FROM usuario WHERE tipo = 4 ORDER BY nombre";
        $stmt = $this->consultas->getResults($query, null);
        foreach($stmt as $rs){
            $n++;
            $datos .= '<div class="label-radio d-flex align-items-center col-md-4"><input class="input-check-sm me-2" type="checkbox" id="chInstalador'.$rs['idusuario'].'" value="'.$rs['idusuario'].'" name="asigna-instalador"> '.$rs['nombre'].'</div>';
        }

        return $datos;
    }

    //-----------------------------------------------INSTALACION
    private function getFoliobyID($id)
    {
        $consultado = false;

        $consulta = "SELECT * FROM folio WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function updateFolioConsecutivo($id)
    {
        $consultado = false;

        $consulta = "UPDATE folio SET consecutivo=(consecutivo+1) WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getFolio($id)
    {
        $datos = "";
        $folios = $this->getFoliobyID($id);
        foreach ($folios as $actual) {
            $serie = $actual['serie'];
            $letra = $actual['letra'];
            $consecutivo = $actual['consecutivo'];
            if ($consecutivo < 10) {
                $consecutivo = "000$consecutivo";
            } else if ($consecutivo >= 10 && $consecutivo < 100) {
                $consecutivo = "00$consecutivo";
            } else if ($consecutivo >= 100 && $consecutivo < 1000) {
                $consecutivo = "0$consecutivo";
            }
            $datos = "$serie</tr>$letra</tr>$consecutivo";
            $update = $this->updateFolioConsecutivo($id);
        }
        return $datos;
    }

    public function insertarInstalacion($i) {
        $fecha = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $idlogin = $_SESSION[sha1("idusuario")];

        $folio = $this->getFolio($i->getFolio());
        $arrayFolio = explode("</tr>", $folio);
        $serie = $arrayFolio[0];
        $letra = $arrayFolio[1];
        $nfolio = $arrayFolio[2];

        $imgfrente = $i->getImgfrente();
        $imgvin = $i->getImgnserie();
        $imgtabinicial = $i->getImgtabinicial();

        $imgfrente = ($imgfrente == "noimage") ? "" : $imgfrente;
        $imgvin = ($imgvin == "noimage") ? "" : $imgvin;
        $imgtabinicial = ($imgtabinicial == "noimage") ? "" : $imgtabinicial;
        
        $insertado = false;
        $consulta = "INSERT INTO ordeninstalacion (idorden, serie_folio, letra_folio, folioinstalacion, fechaservicio, horaservicio, idcliente, cliente, plataforma, tipounidad, marca, modelo, anho, color, serie, numeconomico, km, placas, fecha_creacion_hoja, id_persona_creo, estado_orden, idtservicio, otrostservicio, modeloanterior, imeianterior, simanterior, gpsvehiculo, imei, numtelefono, idinstalador, idaccesorio)
                    VALUES (:id, :serie_folio, :letra, :folio, :fechaservicio,:horaservicio, :idcliente, :cliente,:plataforma, :tipounidad, :marca, :modelo, :anho, :color, :serie, :numeconomico, :km, :placas, :fecha_creacion_hoja, :id_persona_creo, :estado_orden, :idtservicio, :otrostservicio, :modeloanterior, :imeianterior, :simanterior, :gpsvehiculo, :imei, :numtelefono, :idinstalador, :idaccesorio)";
        $valores = array("id" => null,
            "serie_folio"=> $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "fechaservicio" => $i->getFechaservicio(),
            "horaservicio" => $i->getHoraservicio(),
            "idcliente" => $i->getIdcliente(),
            "cliente" => $i->getNombrecliente(),
            "plataforma" => $i->getPlataforma(),
            "tipounidad" => $i->getTipounidad(),
            "marca" => $i->getMarca(),
            "modelo" => $i->getModelo(),
            "anho" => $i->getAnho(),
            "color" => $i->getColor(),
            "serie" => $i->getSerie(),
            "numeconomico" => $i->getNumeconomico(),
            "km" => $i->getKm(),
            "placas" => $i->getPlacas(),
            "fecha_creacion_hoja" => $fecha,
            "id_persona_creo" => $idlogin, 
            "estado_orden" => '1',
            "idtservicio" => $i->getIdtservicio(),
            "otrostservicio" => $i->getOtrostservicio(),
            "modeloanterior" => $i->getModeloanterior(),
            "imeianterior" => $i->getImeianterior(),
            "simanterior" => $i->getSimanterior(),
            "gpsvehiculo" => $i->getIdgpsvehiculo(),
            "imei" => $i->getImei(),
            "numtelefono" => $i->getNumtelefono(),
            "idinstalador" => $i->getIdinstalador(),
            "idaccesorio" => $i->getIdaccesorio()
        );
        
        $insertado = $this->consultas->execute($consulta, $valores);
        $asignacion = $this->asignarInstalacion($serie, $letra, $nfolio, $i->getIdAsignacion());
        echo "asignacion " . $asignacion;
        return $insertado;
    }

    public function asignarInstalacion($serie, $letra, $folio, $valores){
        echo "serie " . $serie;
        echo "letra " . $letra;
        echo "folio" . $folio;
        $query = "SELECT idorden FROM ordeninstalacion WHERE serie_folio = :serie AND letra_folio = :letra AND folioinstalacion = :folioinstalacion";
        $val = array("serie" => $serie, "letra" => $letra, "folioinstalacion" => $folio);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $idorden = $rs['idorden'];
        }
        $divAsigna = explode("-", $valores);
        foreach($divAsigna as $idinstalador){
            $query = "INSERT INTO ordenasignacion VALUES (:idasignacion, :idorden, :idinstalador)";
            $val = array("idasignacion" => null, "idorden" => $idorden, "idinstalador" => $idinstalador);
            $insertado = $this->consultas->execute($query, $val);
        }
        return $insertado;
    }

    private function filtroBusqueda($filtro) {
        $campos = [
            '1' => 'idorden',
            '2' => 'cliente',
            '3' => 'plataforma',
            '4' => 'gpsvehiculo',
            '5' => 'imei',
            '6' => 'numtelefono',
            '7' => 'modeloanterior',
            '8' => 'imeianterior',
            '9' => 'simanterior',
            '10' => 'numeconomico',
            '11' => 'serie',
        ];
        return $campos[$filtro] ?? 'idorden';
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "select count(*) numrows from ordeninstalacion as o $condicion;";
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
    
    public function getSevicios($condicion) {
        $consultado = false;
        $consulta = "select * from ordeninstalacion as o $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getNombreUsuario($id){
        $nombre = "";
        $consulta = "SELECT CONCAT(nombre,\" \",apellido_paterno,\" \",apellido_materno) nombre FROM usuario WHERE idusuario = :id";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        foreach($consultado as $rs){
            $nombre = $rs['nombre'];
        }
        return $nombre;
    }

    public function listServicios($idtservicio, $otros) {
        $servicios = "";
        $count = 0;
        $div = explode("-", $idtservicio);
        $otros = ($otros == "") ? "Otros" : $otros;
        $nombresServicios = [
            '1' => 'Instalación',
            '2' => 'Reubicación',
            '3' => 'Reposición',
            '4' => 'Retiro',
            '5' => 'Revisión',
            '6' => 'Cambio de unidad',
            '7' => $otros,
            '8' => 'Cambio de equipo',
            '9' => 'Cambio de SIM',
        ];
    
        foreach ($div as $actual) {
            $servicio = $nombresServicios[$actual] ?? "";
            $servicios .= ($count > 0) ? ", " . $servicio : $servicio;
            $count++;
        }
    
        return $servicios;
    }

    public function listAccesorios($idaccesorio) {
        $accesorios = "";
        $count = 0;
        $div = explode("-", $idaccesorio);
    
        $nombresAccesorios = [
            '1' => 'Botón de Pánico',
            '2' => 'Bocina',
            '3' => 'Micrófono',
            '4' => 'Corte de Corriente/Combustible',
            '5' => 'Sensor de Gasolina',
            '6' => 'Sensores de Puertas',
            '7' => 'Sensor de Impacto',
            '8' => 'Cámara',
            '9' => 'Chapa Magnética',
            '10' => 'Solo GPS',
            '11' => 'Solo Revisión',
            '12' => 'Ninguno',
            '13' => 'Claxon',
        ];
    
        foreach ($div as $actual) {
            $acc = $nombresAccesorios[$actual] ?? "";
    
            $accesorios .= ($count > 0) ? ", " . $acc : $acc;
            $count++;
        }
    
        return $accesorios;
    }
    
    private function getNombreInstalador($idinstalador) {
        $instalador = "";
        $opciones = $this->getInstaladorbyId($idinstalador);
        foreach ($opciones as $actual) {
            $nombre = $actual['nombre'];
            $apaterno = $actual['apellido_paterno'];
            $amaterno = $actual['apellido_materno'];
            $instalador .= "$nombre $apaterno $amaterno";
        }
        return $instalador;
    }

    private function getInstaladorbyId($id) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE idusuario=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisosInstalacion($idusuario)
    {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listainstalacion'];
            $inicio = $actual['editarinicio'];
            $pasos = $actual['editarpasos'];
            $cancelar = $actual['cancelarinstalacion'];
            $eliminar = $actual['eliminarinstalacion'];
            $datos .= "$lista</tr>$inicio</tr>$pasos</tr>$cancelar</tr>$eliminar";
        }
        return $datos;
    }
    
    public function listaServiciosHistorial($REF, $servicio, $filtro, $pag, $numreg) {
        include '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
        <tr class='info align-middle'>
            <th class='text-center'>Folio</th>
            <th class='text-center'>Fecha Servicio </th>
            <th class='text-center'>Cliente</th>
            <th class='text-center'>Servicio </th>
            <th class='text-center'>Equipo </th>
            <th class='text-center'>Plataforma</th>
            <th class='text-center'>IMEI </th>
            <th class='text-center'>No. Económico </th>
            <th class='text-center'>Instalador </th>
            <th class='text-center'>Encargado </th>
            <th class='text-center'>Creó </th>
            <th class='text-center'>Editó </th>
            <th class='text-center'>Finalizó </th>
            <th class='text-center'>Estado </th>
            <th class='text-center'>Opción</th>
        </tr>
    </thead>
    <tbody>";
        $tserv = "";
        $campo = $this->filtroBusqueda($filtro);
        $condicion = "";
        if ($REF == "") {
            if ($servicio != "") {
                $tserv = "WHERE (idtservicio = '$servicio')";
            }
            $condicion = "$tserv ORDER BY o.idorden desc";
        } else {
            if ($servicio != "") {
                $tserv = " AND (idtservicio = '$servicio')";
            }
            $condicion = "WHERE ($campo LIKE '%$REF%')$tserv ORDER BY o.idorden DESC";
        }

        $permisos = $this->getPermisosInstalacion($idlogin);
        $div = explode("</tr>", $permisos);

        if($div[0] == '1'){
        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $lista = $this->getSevicios($con);
        
        $finales = 0;
        foreach ($lista as $listaActual) {
            $idorden = $listaActual['idorden'];
            $letra = $listaActual['letra_folio'];
            $folio = $listaActual['folioinstalacion'];
            $cliente = $listaActual['cliente'];
            $plataforma = $listaActual['plataforma'];
            $fechaservicio = $listaActual['fechaservicio'];
            $horaservicio = $listaActual['horaservicio'];
            $idtservicio = $listaActual['idtservicio'];
            $otrosservicios = $listaActual['otrostservicio'];
            $encargado = $listaActual['encargado'];
            $imei = $listaActual['imei'];
            $numeconomico = $listaActual['numeconomico'];
            $idaccesorio = $listaActual['idaccesorio'];
            if($idtservicio != ""){
                $servicios = $this->listServicios($idtservicio, $otrosservicios);
            } else {
                $servicios = "";
            }
            
            $equipo = $listaActual['gpsvehiculo'];
            $tipounidad = $listaActual['tipounidad'];
            
            $idinstalador = $listaActual['idinstalador'];
            $id_persona_creo = $listaActual['id_persona_creo'];
            $id_persona_edita = $listaActual['id_persona_edita'];
            $idfinaliza = $listaActual['idfinaliza'];

            $idestado_orden = $listaActual['estado_orden'];

            $creo = $this->getNombreUsuario($id_persona_creo);
            $edito = $this->getNombreUsuario($id_persona_edita);
            $finalizo = $this->getNombreUsuario($idfinaliza);
            $listaccesorios = $this->listAccesorios($idaccesorio);
            $estado = "";
            $txt_estado = "";
            $bg_estado = "";

            switch($idestado_orden){
                case '1':
                    $estado = "Generada";
                    $txt_estado = "#9C5700";
                    $bg_estado = "#FFEB9C";
                    break;
                case '2': 
                    $estado = "Trabajando";
                    $txt_estado = "#3F3F76";
                    $bg_estado = "#BDD7EF";
                    break;
                case '3':
                    $estado = "Firmada";
                    $txt_estado = "#006100";
                    $bg_estado = "#C6EFCE";
                    break;
                case '4': 
                    $estado = "Cancelada";
                    $txt_estado = "#9C0006";
                    $bg_estado = "#FFC7CE";
                    break;
            }

            $instalador = $this->getNombreInstalador($idinstalador);
            $divF = explode("-", $fechaservicio);
            $fechaservicio = $divF[2]."/".$divF[1]."/".$divF[0]; 

            $noorden = $idorden;
            if ($noorden < 10) {
                $noorden = "00$idorden";
            } else if ($noorden >= 10 && $noorden < 100) {
                $noorden = "0$idorden";
            }
            $editar = "";
            $pasos = "";
            $copiar = "";
            $cancelar = "";
            $eliminar = "";
            $hoja = "";
            $firmar = "";
            $videos = "";
            $enviar = "";
            
            if($div[1] == '1' && $idestado_orden != '3' && $idestado_orden != '4'){
                $editar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarInstalacion($idorden, 1);'>Editar inicio instalacion <span class='fas fa-edit text-muted small'></span></a></li>";
            }

            if($div[2] == '1' && $idestado_orden != '3' && $idestado_orden != '4'){
                $pasos = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarInstalacion($idorden, 2);'>Editar pasos instalación <span class='fas fa-edit text-muted small'></span></a></li>";
            }

            if($div[3] == '1'){
                if($idestado_orden == 4){
                    $cancelar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='verCancelacion($idorden);'>Ver cancelación <span class='fas fa-eye text-muted'></span></a></li>";
                } else if($idestado_orden == 3){
                    $cancelar = "";
                } else{
                    $cancelar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='cancelarInstalacion($idorden);'>Cancelar instalación <span class='fas fa-ban'></span></a></li>";
                }
            }

            if($div[4] == '1' && $idestado_orden != '3' && $idestado_orden != '4'){
                $eliminar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarInstalacion($idorden);'>Eliminar instalación <span class='fas fa-times text-muted'></span></a></li>";
            }

            $copiar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='copiarInstalacion($idorden);'>Copiar instalación <span class='text-muted fas fa-copy'></span></a></li>";
            
            if($idestado_orden != 1 && $idestado_orden != 4){
                $firmar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#sign-modal' onclick=\"setIDInstalacion($idorden, '$encargado');\">Firmar instalación <span class='text-muted fas fa-edit'></span></a></li>";
            }

            if($idestado_orden != 1){
              $videos = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#videos-instalacion' onclick=\"videosServicio('$letra$folio')\";'>Videos de servicio <span class='text-muted fas fa-play'></span></a></li>";
            }

            if($idestado_orden == 3){
                $hoja = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"hojaServicio($idorden)\";'>Hoja de servicio <span class='text-muted fas fa-file'></span></a></li>";
                $enviar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#enviar-mail' onclick='showCorreosIns($idorden);'>Enviar instalación <span class='text-muted fas fa-envelope'></span></a></li>";
            }

            $datos .= "
                    <tr>
                        <td class='lh-sm'>$letra$folio</td>
                        <td class='lh-sm text-center'>$fechaservicio ".date('h:i A', strtotime($horaservicio))."</td>
                        <td class='text-center'>$cliente</td>
                        <td><ul class='text-start'>
                        <li class='mb-2 lh-1'><span class='fw-semibold text-muted small'>Tipo unidad: </span>".(($tipounidad==1)?'Vehiculo':'Caja')."</li>
                        <li class='mb-2 lh-1'><span class='fw-semibold text-muted small'>Servicios: </span> <br> $servicios</li>
                        <li class='mb-2 lh-1'><span class='fw-semibold text-muted small'>Accesorios: </span> <br> $listaccesorios</li>
                        </ul></td>
                        <td class='lh-sm text-center'>$equipo</td>
                        <td class='lh-sm text-center'>$plataforma</td>
                        <td class='lh-sm text-center'>$imei</td>
                        <td class='lh-sm text-center'>$numeconomico</td>
                        <td class='lh-sm text-center'>$instalador</td>
                        <td class='lh-sm text-center'>$encargado</td>
                        <td class='lh-sm text-center'>$creo</td>
                        <td class='lh-sm text-center'>$edito</td>
                        <td class='lh-sm text-center'>$finalizo</td>
                        <td class='fw-bold text-center' style='background:$bg_estado; color: $txt_estado'>$estado</td>
                        <td align='center'><div class='dropdown dropend z-1'>
                        <button class='button-list dropdown-toggle' title='Opciones' type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                        </button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                        $editar
                        $pasos
                        $cancelar
                        $eliminar
                        $hoja
                        $videos
                        $copiar
                        $firmar
                        $enviar
                        </ul>
                        </div></td>
                    </tr>";
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarInstalacion";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='15'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='6' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='9'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
    }
        return $datos;
    }

    public function getOrdenbyId($id) {
        $consultado = false;
        $consulta = "SELECT o.*, i.nombre nombre_inst, i.apellido_paterno, i.apellido_materno FROM ordeninstalacion o LEFT JOIN usuario i ON (o.idinstalador = i.idusuario) WHERE idorden = :id";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }
    
    public function getIdAsignacion($idorden){
        $datos = "";
        $query = "SELECT GROUP_CONCAT(idinstalador SEPARATOR '-') idasignacion FROM ordenasignacion WHERE idorden = :id";
        $val = array("id" => $idorden);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $datos = $rs['idasignacion'];
        }
        return $datos;
    }

    public function getDatosInstalacion($idinstalacion, $genTmp) {
        $instalacion = $this->getOrdenbyId($idinstalacion);
        $datos = "";
        foreach ($instalacion as $actual) {
            $idorden = $actual['idorden'];
            $fechaservicio = $actual['fechaservicio'];
            $horaservicio = $actual['horaservicio'];
            $idcliente = $actual['idcliente'];
            $cliente = $actual['cliente'];
            $marca = $actual['marca'];
            $modelo = $actual['modelo'];
            $anho = $actual['anho'];
            $color = $actual['color'];
            $serie = $actual['serie'];
            $numeconomico = $actual['numeconomico'];
            $km = $actual['km'];
            $placas = $actual['placas'];
            $gpsvehiculo = $actual['gpsvehiculo'];
            $imei = $actual['imei'];
            $numtelefono = $actual['numtelefono'];
            $idinstalador = $actual['idinstalador'];
            $idaccesorio = $actual['idaccesorio'];
            $instalador = $actual['nombre_inst'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'];
            $serie_folio = $actual['serie_folio'];
            $letra = $actual['letra_folio'];
            $folio = $actual['folioinstalacion'];
            $modeloanterior = $actual['modeloanterior'];
            $imeianterior = $actual['imeianterior'];
            $simanterior = $actual['simanterior'];
            $plataforma = $actual['plataforma'];
            $tipounidad = $actual["tipounidad"];
            $idtservicio = $actual['idtservicio'];
            $otrostservicio = $actual['otrostservicio'];
            $idasignacion = $this->getIdAsignacion($idorden);

            $datos = "$idorden</tr>$fechaservicio</tr>$horaservicio</tr>$cliente</tr>$marca</tr>$modelo</tr>$anho</tr>$color</tr>$serie</tr>$numeconomico</tr>$km</tr>$placas</tr>$gpsvehiculo</tr>$imei</tr>$numtelefono</tr>$idinstalador</tr>$idaccesorio</tr>$idinstalacion</tr>$instalador</tr>$serie_folio</tr>$letra</tr>$folio</tr>$idcliente</tr>$modeloanterior</tr>$imeianterior</tr>$simanterior</tr>$plataforma</tr>$tipounidad</tr>$idasignacion</tr>$idtservicio</tr>$otrostservicio";
        
            $datos_2 = "$idorden</tr>$serie_folio</tr>$letra</tr>$folio</tr>$fechaservicio</tr>$horaservicio</tr>$idcliente</tr>$cliente</tr>$plataforma</tr>$tipounidad</tr>$marca</tr>$modelo</tr>$anho</tr>$color</tr>$serie</tr>$numeconomico</tr>$km</tr>$placas</tr>$idtservicio</tr>$modeloanterior</tr>$imeianterior</tr>$simanterior</tr>$otrostservicio</tr>$gpsvehiculo</tr>$imei</tr>$numtelefono</tr>$idinstalador</tr>$idaccesorio";
            break;
        }

        if($genTmp == 1){
            $this->genTemp($datos_2);
        }
        return $datos;
    }

    private function existeTempInstalacion($id){
        $consultas = "SELECT count(*) maximo FROM tmpinstalacion WHERE idorden = :idorden";
        $val = array("idorden" => $id);
        $consultado = $this->consultas->getResults($consultas, $val);
        foreach($consultado as $rs){
            $existe = $rs['maximo'];
        }
        return $existe;
    }

    private function genTemp($datos){
        $insertado = 0;
        $fecha = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $idlogin = $_SESSION[sha1("idusuario")];
        $div = explode("</tr>", $datos);
        $existe = $this->existeTempInstalacion($div[0]);
        if($existe == 0){
            $consultas = "INSERT INTO tmpinstalacion (idtemp, idorden, serie_folio, letra_folio, folioinstalacion, fechaservicio, horaservicio, idcliente, cliente, plataforma, tipounidad, marca, modelo, anho, color, serie, numeconomico, km, placas, idtservicio, modeloanterior, imeianterior, simanterior, otrostservicio, gpsvehiculo, imei, numtelefono, idinstalador, idaccesorio, sid)
                          VALUES (:idtemp, :idorden, :serie_folio, :letra, :folioinstalacion, :fechaservicio, :horaservicio, :idcliente, :cliente, :plataforma, :tipounidad, :marca, :modelo, :anho, :color, :serie, :numeconomico, :km, :placas, :idtservicio, :modeloanterior, :imeianterior, :simanterior, :otrostservicio, :gpsvehiculo, :imei, :numtelefono, :idinstalador, :idaccesorio, :sid)";

            $val = array(
                "idtemp" => null,
                "idorden" => $div[0],
                "serie_folio" => $div[1],
                "letra" => $div[2],
                "folioinstalacion" => $div[3],
                "fechaservicio" => $div[4],
                "horaservicio" => $div[5],
                "idcliente" => $div[6],
                "cliente" => $div[7],
                "plataforma" => $div[8],
                "tipounidad" => $div[9],
                "marca" => $div[10],
                "modelo" => $div[11],
                "anho" => $div[12],
                "color" => $div[13],
                "serie" => $div[14],
                "numeconomico" => $div[15],
                "km" => $div[16],
                "placas" => $div[17],
                "idtservicio" => $div[18],
                "modeloanterior" => $div[19],
                "imeianterior" => $div[20],
                "simanterior" => $div[21],
                "otrostservicio" => $div[22],
                "gpsvehiculo" => $div[23],
                "imei" => $div[24],
                "numtelefono" => $div[25],
                "idinstalador" => $div[26],
                "idaccesorio" => $div[27],
                "sid" => $idlogin
            );
            $insertado = $this->consultas->execute($consultas, $val);

            $consultas = "UPDATE ordeninstalacion SET estado_orden = :estado, id_persona_edita = :id_usu, fecha_edicion_hoja = :fecha WHERE idorden = :id_orden";
            $val = array("estado" => 2, "id_usu" => $idlogin, "fecha" => $fecha, "id_orden" => $div[0]);
            $insertado = $this->consultas->execute($consultas, $val);

            $consultas = "INSERT INTO ordencontrolpasos (idpasos, idorden, paso1) VALUES (:idpasos, :idorden, :paso1)";
            $val = array("idpasos" => null, "idorden" => $div[0], "paso1" => $fecha);
            $insertado = $this->consultas->execute($consultas, $val);
        }
        return $insertado;
    }

    private function getFolioInstalacion($id) {
        $folio = "";
        $letra = "";
        $datos = $this->getOrdenbyId($id);
        foreach ($datos as $actual) {
            $letra = $actual['letra'];
            $folio = $actual['folioinstalacion'];
        }
        return "$folio</tr>$letra";
    }

    public function eliminarInstalacion($id) {
        $folio = $this->getFolioInstalacion($id);
        $val = explode("</tr>", $folio);
        $consultado = false;
        $consulta = "DELETE FROM ordeninstalacion WHERE idorden=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        $delimgs = $this->eliminarImgsInstalacion($val[0], $val[1], $val[2]);
        return $consultado;
    }

    private function eliminarImgsInstalacion($folio, $serie, $letra) {
        $consultado = false;
        $folioins = $letra.$folio;
        $consulta = "DELETE FROM imginstalacion where img_folioins=:folio;";
        $val = array("folio" => $folioins);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function actualizarInstalacion($i) {        
        $actualizado = false;        
        $consulta = "UPDATE ordeninstalacion SET fechaservicio = :fechaservicio, horaservicio = :horaservicio, idcliente = :idcliente, cliente = :cliente, plataforma = :plataforma, tipounidad = :tipounidad, marca = :marca, modelo = :modelo, anho = :anho, color = :color, serie = :serie, numeconomico = :numeconomico, km = :km, placas = :placas, idtservicio = :idtservicio, modeloanterior = :modeloanterior, imeianterior = :imeianterior, simanterior = :simanterior, otrostservicio = :otrostservicio, gpsvehiculo = :gpsvehiculo, imei = :imei, numtelefono = :numtelefono, idinstalador = :idinstalador, idaccesorio = :idaccesorio WHERE (idorden = :idorden)";
        $valores = array("idorden" => $i->getIdhojaservicio(),
            "fechaservicio" => $i->getFechaservicio(),
            "horaservicio" => $i->getHoraservicio(),
            "idcliente" => $i->getIdcliente(),
            "cliente" => $i->getNombrecliente(),
            "plataforma" => $i->getPlataforma(),
            "tipounidad" => $i->getTipounidad(),
            "marca" => $i->getMarca(),
            "modelo" => $i->getModelo(),
            "anho" => $i->getAnho(),
            "color" => $i->getColor(),
            "serie" => $i->getSerie(),
            "numeconomico" => $i->getNumeconomico(),
            "km" => $i->getKm(),
            "placas" => $i->getPlacas(),
            "idtservicio" => $i->getIdtservicio(),
            "modeloanterior" => $i->getModeloanterior(),
            "imeianterior" => $i->getImeianterior(),
            "simanterior" => $i->getSimanterior(),
            "otrostservicio" => $i->getOtrostservicio(),
            "gpsvehiculo" => $i->getIdgpsvehiculo(),
            "imei" => $i->getImei(),
            "numtelefono" => $i->getNumtelefono(),
            "idinstalador" => $i->getIdinstalador(),
            "idaccesorio" => $i->getIdaccesorio()
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        $actualiza_asignacion = $this->updateIdAsignacion($i->getIdhojaservicio(), $i->getIdAsignacion());
        if($this->existeTempInstalacion($i->getIdhojaservicio()) > 0){
            $actualizatmp = $this->updateTmpInst($i);
        }
        return $actualizado;
    }

    public function updateIdAsignacion($idorden, $idasignacion){
        $query = "DELETE FROM ordenasignacion WHERE idorden = :id";
        $val = array("id" => $idorden);
        if($this->consultas->execute($query, $val) > 0){
            $divAsigna = explode("-", $idasignacion);
            foreach($divAsigna as $idinstalador){
                $query = "INSERT INTO ordenasignacion VALUES (:idasignacion, :idorden, :idinstalador)";
                $val = array("idasignacion" => null, "idorden" => $idorden, "idinstalador" => $idinstalador);
                $insertado = $this->consultas->execute($query, $val);
            }
        }
        return $insertado;
    }

    private function updateTmpInst($i){
        $actualizado = false;
        $consulta = "UPDATE tmpinstalacion SET fechaservicio = :fechaservicio, horaservicio = :horaservicio, idcliente = :idcliente, cliente = :cliente, plataforma = :plataforma, tipounidad = :tipounidad, marca = :marca, modelo = :modelo, anho = :anho, color = :color, serie = :serie, numeconomico = :numeconomico, km = :km, placas = :placas, idtservicio = :idtservicio, modeloanterior = :modeloanterior, imeianterior = :imeianterior, simanterior = :simanterior, otrostservicio = :otrostservicio, gpsvehiculo = :gpsvehiculo, imei = :imei, numtelefono = :numtelefono, idinstalador = :idinstalador WHERE (idorden = :idorden)";
        $valores = array("idorden" => $i->getIdhojaservicio(),
            "fechaservicio" => $i->getFechaservicio(),
            "horaservicio" => $i->getHoraservicio(),
            "idcliente" => $i->getIdcliente(),
            "cliente" => $i->getNombrecliente(),
            "plataforma" => $i->getPlataforma(),
            "tipounidad" => $i->getTipounidad(),
            "marca" => $i->getMarca(),
            "modelo" => $i->getModelo(),
            "anho" => $i->getAnho(),
            "color" => $i->getColor(),
            "serie" => $i->getSerie(),
            "numeconomico" => $i->getNumeconomico(),
            "km" => $i->getKm(),
            "placas" => $i->getPlacas(),
            "idtservicio" => $i->getIdtservicio(),
            "modeloanterior" => $i->getModeloanterior(),
            "imeianterior" => $i->getImeianterior(),
            "simanterior" => $i->getSimanterior(),
            "otrostservicio" => $i->getOtrostservicio(),
            "gpsvehiculo" => $i->getIdgpsvehiculo(),
            "imei" => $i->getImei(),
            "numtelefono" => $i->getNumtelefono(),
            "idinstalador" => $i->getIdinstalador()
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function getTMP($idorden){
        $consultas = "SELECT iddanhos, tipounidad, letra_folio, folioinstalacion, idmolduras, otrosmolduras, idtablero, otrostablero, idcableado, otroscableado, idccorriente, otroscorriente, idtservicio, modeloanterior, imeianterior, simanterior, otrostservicio, gpsvehiculo, imei, numtelefono, idinstalador, idaccesorio, observaciones, idinstalacion, encargado, firma, imgfrentevehiculo, imgfrentebase, imgnserie, imgseriebase, imgtabinicial, imgtabinibase, imgtabfinal, imgtabfinbase, imgantesinst, imgantesbase, imgdespuesinst, imgdespuesbase, observacion_general, ubicacion_boton_panico, tipo_corte FROM tmpinstalacion WHERE idorden = :idorden";
        $val = array("idorden" => $idorden);
        $consultado = $this->consultas->getResults($consultas, $val);
        foreach($consultado as $rs){
            $iddanhos = $rs["iddanhos"];
            $idmolduras = $rs["idmolduras"];
            $otrosmolduras = $rs["otrosmolduras"];
            $idtablero = $rs["idtablero"];
            $otrostablero = $rs["otrostablero"];
            $idcableado = $rs["idcableado"];
            $otroscableado = $rs["otroscableado"];
            $idccorriente = $rs["idccorriente"];
            $otroscorriente = $rs["otroscorriente"];
            $idtservicio = $rs["idtservicio"];
            $modeloanterior = $rs["modeloanterior"];
            $imeianterior = $rs["imeianterior"];
            $simanterior = $rs["simanterior"];
            $otrostservicio = $rs["otrostservicio"];
            $gpsvehiculo = $rs["gpsvehiculo"];
            $imei = $rs["imei"];
            $numtelefono = $rs["numtelefono"];
            $idinstalador = $rs["idinstalador"];
            $idaccesorio = $rs["idaccesorio"];
            $observaciones = $rs["observaciones"];
            $idinstalacion = $rs["idinstalacion"];
            $encargado = $rs["encargado"];
            $firma = $rs["firma"];
            $imgfrentevehiculo = $rs["imgfrentevehiculo"];
            $imgnserie = $rs["imgnserie"];
            $imgtabinicial = $rs["imgtabinicial"];
            $imgtabfinal = $rs["imgtabfinal"];
            $imgantesinst = $rs["imgantesinst"];
            $imgdespuesinst = $rs["imgdespuesinst"];
            $observacion_general = $rs["observacion_general"];
            $ubicacion_boton_panico = $rs["ubicacion_boton_panico"];
            $tipo_corte = $rs["tipo_corte"];
            $foliofinal = $rs["letra_folio"].$rs["folioinstalacion"];
            $imgfrentebase = $rs["imgfrentebase"];
            $imgseriebase = $rs["imgseriebase"];
            $imgtabinibase = $rs['imgtabinibase'];
            $imgantesinsbase = $rs['imgantesbase'];
            $imffininsbase = $rs['imgdespuesbase'];
            $imgtabfinbase = $rs['imgtabfinbase'];
            $tipounidad = $rs['tipounidad'];
            $instalador = $this->getNombreInstalador($idinstalador);
        }
        $datos = "$iddanhos</tr>$idmolduras</tr>$otrosmolduras</tr>$idtablero</tr>$otrostablero</tr>$idcableado</tr>$otroscableado</tr>$idccorriente</tr>$otroscorriente</tr>$idtservicio</tr>$modeloanterior</tr>$imeianterior</tr>$simanterior</tr>$otrostservicio</tr>$gpsvehiculo</tr>$imei</tr>$numtelefono</tr>$instalador</tr>$idaccesorio</tr>$observaciones</tr>$idinstalacion</tr>$encargado</tr>$firma</tr>$imgfrentevehiculo</tr>$imgnserie</tr>$imgtabinicial</tr>$imgtabfinal</tr>$imgantesinst</tr>$imgdespuesinst</tr>$observacion_general</tr>$ubicacion_boton_panico</tr>$tipo_corte</tr>$foliofinal</tr>$imgfrentebase</tr>$imgseriebase</tr>$imgtabinibase</tr>$imgantesinsbase</tr>$imffininsbase</tr>$imgtabfinbase</tr>$tipounidad";
        return $datos;
    }

    public function verifyImgTMP($paso, $check, $idorden){
        $consultas = "SELECT COUNT(*) maximo FROM tmpimginstotras WHERE paso = :paso AND ch = :ch AND idorden = :idorden";
        $val = array("paso" => $paso, "ch" => $check,  "idorden" => $idorden);
        $stmt = $this->consultas->getResults($consultas, $val);
        foreach($stmt as $rs){
            $n = $rs['maximo'];
        }
        return $n;
    }

    public function getCorreo($idfactura) {
        $correos = $this->getCorreosAux($idfactura);
        foreach ($correos as $actual) {
            $correo1 = $actual['email_informacion'];
            $correo2 = $actual['email_facturacion'];
            $correo3 = $actual['email_gerencia'];
        }
        return "$correo1<corte>$correo2<corte>$correo3";
    }

    private function getCorreosAux($idorden) {
        $consultado = false;
        $consulta = "select o.idorden, o.letra_folio, o.folioinstalacion, o.cliente, c.email_informacion,c.email_facturacion,c.email_gerencia from ordeninstalacion o inner join cliente c on o.idcliente=c.id_cliente where o.idorden=:id;";
        $val = array("id" => $idorden);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function registraImgTMPOtras($img, $base64, $orden, $paso, $ch, $titulo){
        $sid = $_SESSION[sha1("idusuario")];
        if($this->verifyImgTMP($paso, $ch, $orden) == 0 || $paso == 0){
            $consultas = "INSERT INTO tmpimginstotras VALUES (:id, :img, :imagenbase, :idorden, :paso, :ch, :sid, :titulo)";
        } else {
            $consultas = "UPDATE tmpimginstotras SET imgname = :img, imagenbase= :imagenbase WHERE paso = :paso AND ch = :ch AND idorden = :idorden";
        }
        $val = array("id" => null, "img" => $img, "imagenbase"=>$base64, "idorden" => $orden, "paso" => $paso, "ch" => $ch, "sid" => $sid, "titulo" => $titulo);
        $insertado = $this->consultas->execute($consultas, $val);
        return $insertado;
    }

    public function saveStep($step, $cve_orden){
        $fecha = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $query = "UPDATE ordencontrolpasos SET paso$step = :fecha WHERE idorden = :idorden";
        $val = array("fecha" => $fecha, "idorden" => $cve_orden);
        $update = $this->consultas->execute($query, $val);
        return $update;
    }

    public function updateImgEvidencias($base, $idorden, $columna){
        $consultado = false;
        $consulta = "UPDATE tmpinstalacion SET $columna = :base where idorden = :idorden";
        $val = array("base" => $base,"idorden" => $idorden);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    public function eliminarEvidencias($idorden, $name, $base){
        $consultado = false;
        $consulta = "UPDATE tmpinstalacion SET $name = :name, $base =:base where idorden = :idorden";
        $val = array("name" => null,"base"=>null,"idorden" => $idorden);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getTmpImg($idorden) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimginstotras where idorden = :idorden AND paso = 0";
        $val = array("idorden" => $idorden);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaIMG($idorden) {
        $datos = "<corte>";
        $img = $this->getTmpImg($idorden);
        foreach ($img as $actual) {
            $idtmp = $actual['idimg'];
            $name = $actual['imgname'];
            $base = $actual['imagenbase'];
            $datos .= "
                <tr class='align-middle'>
                    <td class='click-row text-muted' style='word-break: break-all;' onclick=\"showImgOtras('$name', '$base')\">$name </td>
                    <td><button class='btn btn-xs btn-danger' onclick='eliminarIMG($idtmp)' title='Eliminar imagen'><span class='fas fa-times'></span></button></td>
                </tr>";
        }
        return $datos;
    }

    private function getTmpVid($idorden) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpvid where idorden=:idorden and paso = 0";
        $val = array("idorden" => $idorden);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaVid($idorden) {
        $datos = "<corte>";
        $img = $this->getTmpVid($idorden);
        foreach ($img as $actual) {
            $idtmp = $actual['idtmpvid'];
            $vidtmp = $actual['vidtmp'];
            $name = $actual['namevid'];
            $datos .= "
                <tr class='align-middle'>
                    <td class='click-row text-muted' style='word-break: break-all;'>$name </td>
                    <td><button class='btn btn-primary btn-xs' onclick='showVid(\"$vidtmp\", \"$name\")'><span class='fas fa-video'></span></button></td>
                    <td><button class='btn btn-xs btn-danger' onclick='eliminarVid($idtmp)' title='Eliminar video'><span class='fas fa-times'></span></button></td>
                </tr>";
        }

        return $datos;
    }

    public function deleteTmpByName($nombre){
        $id = 0;
        $query = "SELECT idimg FROM tmpimginstotras WHERE imgname = :imgname";
        $val = array("imgname" => $nombre);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $id = $rs['idimg'];
        }
        return $this->eliminarImgTmp($id);
    }

    public function eliminarImgTmp($id) {
        $consultado = false;
        $consulta = "DELETE FROM tmpimginstotras where idimg=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    public function saveTMP($i){
        $consulta = "UPDATE tmpinstalacion SET iddanhos = :iddanhos, idmolduras = :idmolduras, otrosmolduras = :otrosmolduras, idtablero = :idtablero, otrostablero = :otrostablero, idcableado = :idcableado, otroscableado = :otroscableado, idccorriente = :idccorriente, otroscorriente = :otroscorriente, idaccesorio = :idaccesorio, observaciones = :observaciones, idinstalacion = :idinstalacion, encargado = :encargado, firma = :firma, imgfrentevehiculo = :imgfrentevehiculo, imgnserie = :imgnserie, imgtabinicial = :imgtabinicial, imgtabfinal = :imgtabfinal, imgantesinst = :imgantesinst, imgdespuesinst = :imgdespuesinst, observacion_general = :observacion_general, ubicacion_boton_panico = :ubicacion_boton_panico, tipo_corte = :tipo_corte WHERE idorden = :id";
        $val = array(
            "iddanhos" => $i->getIddanhos(),
            "idmolduras" => $i->getIdmolduras(),
            "otrosmolduras" => $i->getOtrosmolduras(),
            "idtablero" => $i->getIdtablero(),
            "otrostablero" => $i->getOtrostablero(),
            "idcableado" => $i->getIdcableado(),
            "otroscableado" => $i->getOtroscableado(),
            "idccorriente" => $i->getIdccorriente(),
            "otroscorriente" => $i->getOtrosccorriente(),
            "idaccesorio" => $i->getIdaccesorio(),
            "observaciones" => $i->getObservaciones(),
            "idinstalacion" => $i->getIdinstalacion(),
            "encargado" => $i->getEncargado(),
            "firma" => $i->getFirma(),
            "imgfrentevehiculo" => $i->getImgfrente(),
            "imgnserie" => $i->getImgnserie(),
            "imgtabinicial" => $i->getImgtabinicial(),
            "imgtabfinal" => $i->getImgtabfinal(),
            "imgantesinst" => $i->getImgAntesInstalacion(),
            "imgdespuesinst" => $i->getImgDespuesInstalacion(),
            "id" => $i->getIdhojaservicio(),
            "observacion_general" => $i->getObservacionGral(),
            "ubicacion_boton_panico" => $i->getUbicacionPanico(),
            "tipo_corte" => $i->getTipoCorte()
        );

        if($this->consultas->execute($consulta, $val)){
            $message = "Temporales guardadas correctamente.";
        } else {
            $message = "Error al guardar temporales.";
        }
        if($i->getFirma() != ""){
            $query = "UPDATE ordeninstalacion SET firma = :firma WHERE idorden = :id";
            $val = array("firma" => $i->getFirma(), "id" => $i->getIdhojaservicio());
            $this->consultas->execute($query, $val);
        }
        return $message;
    }

    public function getFilesTMPOtras($idorden){
        $datos = "";
        $query = "SELECT CONCAT(\"filech\",ch,\"-\",paso) filech, CONCAT(\"img-evidencia\",ch,\"-\",paso) evidencia, imagenbase, imgname img FROM tmpimginstotras WHERE idorden = :idorden";
        $val = array("idorden" => $idorden);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $datos .= $rs['filech']."</b>".$rs['evidencia']."</b>".$rs['img']."</b>".$rs['imagenbase']."</tr>";
        }
        return $datos;
    }
    
    public function getFilesTMPVid($idorden){
        $datos = "";
        $query = "SELECT CONCAT(\"filech\",ch,\"-\",paso) filech, CONCAT(\"img-evidencia\",ch,\"-\",paso) evidencia, vidtmp img FROM tmpvid where  idorden = :idorden";
        $val = array("idorden" => $idorden);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $datos .= $rs['filech']."</b>".$rs['evidencia']."</b>".$rs['img']."</tr>";
        }
        return $datos;
    }

    public function getStep($cve_orden){
        $paso = 0;
        $query = "SELECT * FROM ordencontrolpasos WHERE idorden = :idorden";
        $val = array("idorden" => $cve_orden);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            if($rs['paso11'] != ""){ $paso = 11; } else
            if($rs['paso10'] != ""){ $paso = 10; } else
            if($rs['paso9']  != ""){ $paso = 9;  } else
            if($rs['paso8']  != ""){ $paso = 8;  } else
            if($rs['paso7']  != ""){ $paso = 7;  } else
            if($rs['paso6']  != ""){ $paso = 6;  } else
            if($rs['paso5']  != ""){ $paso = 5;  } else
            if($rs['paso4']  != ""){ $paso = 4;  } else
            if($rs['paso3']  != ""){ $paso = 3;  } else
            if($rs['paso2']  != ""){ $paso = 2;  } else
            if($rs['paso1']  != ""){ $paso = 1;  } 
        }
        return $paso;
    }   

    public function getVistaPrevia($idorden, $tipo_unidad){
        $datos = "";
        $consultas = "SELECT DATE_FORMAT(fechaservicio, '%d/%m/%Y') fechaservicio, TIME_FORMAT(horaservicio,'%r') horaservicio, cliente, plataforma, CASE WHEN tipounidad = 1 THEN 'Vehiculo' ELSE 'Caja' END tipo, marca, modelo, anho, color, serie, numeconomico, km, placas, iddanhos, idmolduras, otrosmolduras, idtablero, otrostablero, idcableado, otroscableado, idccorriente, otroscorriente, idtservicio, modeloanterior, imeianterior, simanterior, otrostservicio, gpsvehiculo, imei, numtelefono, tmp.idinstalador, idaccesorio, observaciones, idinstalacion, encargado, firma, imgfrentevehiculo, imgnserie, imgtabinicial, imgtabfinal, imgantesinst, imgdespuesinst, CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) nombreinstalador, ubicacion_boton_panico, observacion_general, tipo_corte FROM tmpinstalacion tmp INNER JOIN usuario ins ON ins.idusuario = tmp.idinstalador 
        WHERE idorden = :id";
        $val = array("id" => $idorden);
        $consultado = $this->consultas->getResults($consultas, $val);
        foreach($consultado as $rs){
            $fechaservicio = $rs['fechaservicio'];
            $horaservicio = $rs['horaservicio'];
            $cliente = $rs['cliente'];
            $plataforma = $rs['plataforma'];
            $tipo = $rs['tipo'];
            $marca = $rs['marca'];
            $modelo = $rs['modelo'];
            $anho = $rs['anho'];
            $color = $rs['color'];
            $serie = $rs['serie'];
            $numeconomico = $rs['numeconomico'];
            $km = $rs['km'];
            $placas = $rs['placas'];
            $iddanhos = $rs['iddanhos'];
            $idmolduras = $rs['idmolduras'];
            $otrosmolduras = $rs['otrosmolduras'];
            $idtablero = $rs['idtablero'];
            $otrostablero = $rs['otrostablero'];
            $idcableado = $rs['idcableado'];
            $otroscableado = $rs['otroscableado'];
            $idccorriente = $rs['idccorriente'];
            $otroscorriente = $rs['otroscorriente'];
            $idtservicio = $rs['idtservicio'];
            $modeloanterior = $rs['modeloanterior'];
            $imeianterior = $rs['imeianterior'];
            $simanterior = $rs['simanterior'];
            $otrostservicio = $rs['otrostservicio'];
            $gpsvehiculo = $rs['gpsvehiculo'];
            $imei = $rs['imei'];
            $numtelefono = $rs['numtelefono'];
            $idaccesorio = $rs['idaccesorio'];
            $observaciones = $rs['observaciones'];
            $idinstalacion = $rs['idinstalacion'];
            $encargado = $rs['encargado'];
            $nombreinstalador = $rs['nombreinstalador'];
            $ubicacion_boton_panico = $rs['ubicacion_boton_panico'];
            $observacion_general = $rs['observacion_general'];



            if($tipo_unidad == 1){
                $datos .= "<div class='row'>
                                <div class='col-md-12 mb-2'><b><h4 class='titulo-lista fs-6 fw-semibold text-uppercase fw-bold'>Datos Servicio</h4></b></div>
                                <div class='col-md-3'><b class='text-muted fw-semibold'>Fecha del servicio:</b><br> $fechaservicio</div>
                                <div class='col-md-3'><b class='text-muted fw-semibold'>Hora del servicio:</b> <br>$horaservicio</div>
                                <div class='col-md-3'><b class='text-muted fw-semibold'>Cliente:</b> <br>$cliente</div>
                                <div class='col-md-3'><b class='text-muted fw-semibold'>Plataforma:</b> <br>$plataforma</div>
                            </div>
                            <div class='row mt-5'>
                            <div class='col-md-12'><b><h4 class='titulo-lista fs-6 fw-semibold text-uppercase fw-bold'>Datos del vehículo</h4></b></div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Tipo:</b> $tipo</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Marca:</b> $marca</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Modelo:</b> $modelo</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Año:</b> $anho</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Color:</b> $color</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Serie:</b> $serie</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>No. Económico:</b> $numeconomico</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Kilometraje:</b> $km</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Placas:</b> $placas</div>
                            <div class='col-md-4 mt-5'>
                                <b class='text-muted fw-semibold'>Daños del vehiculo</b>
                                <ul>";
                                $divdanhos = explode("-",$iddanhos);
                                for($i = 0; $i < sizeof($divdanhos); $i++){
                                    $datos .= ($divdanhos[$i]==1)?"<li class='text-primary fw-semibold'>Parachoques delantero.</li>":"";
                                    $datos .= ($divdanhos[$i]==2)?"<li class='text-primary fw-semibold'>Parachoques trasero.</li>":"";
                                    $datos .= ($divdanhos[$i]==3)?"<li class='text-primary fw-semibold'>Lateral izquierdo.</li>":"";
                                    $datos .= ($divdanhos[$i]==4)?"<li class='text-primary fw-semibold'>Lateral derecho.</li>":"";
                                    $datos .= ($divdanhos[$i]==5)?"<li class='text-primary fw-semibold'>Parabrisas.</li>":"";
                                    $datos .= ($divdanhos[$i]==6)?"<li class='text-primary fw-semibold'>Cajuela.</li>":"";
                                    $datos .= ($divdanhos[$i]==7)?"<li class='text-primary fw-semibold'>Cofre.</li>":"";
                                    $datos .= ($divdanhos[$i]==8)?"<li class='text-primary fw-semibold'>Techo.</li>":"";
                                    $datos .= ($divdanhos[$i]==9)?"<li class='text-primary fw-semibold'>Sin daños.</li>":"";
                                }
                    $datos .="  </ul>
                            </div>
                            <div class='col-md-4 mt-5'>
                                <b class='text-muted fw-semibold'>Condiciones de molduras del vehículo</b>
                                <ul>";
                                $divmolduras = explode("-",$idmolduras);
                                for($i = 0; $i < sizeof($divmolduras); $i++){
                                    $datos .= ($divmolduras[$i]==1)?"<li class='text-primary fw-semibold'>Molduras dañadas (Rotas, maltratadas, marcadas, ralladas, etc).</li>":"";
                                    $datos .= ($divmolduras[$i]==2)?"<li class='text-primary fw-semibold'>Tornillos, grapas o pijas faltantes.</li>":"";
                                    $datos .= ($divmolduras[$i]==3)?"<li class='text-primary fw-semibold'>Sin observación.</li>":"";
                                    $datos .= ($divmolduras[$i]==4)?"<li class='text-primary fw-semibold'>Otros: $otrosmolduras</li>":"";
                                }
                    $datos .= " </ul>
                            </div>
                            <div class='col-md-4 mt-5'>
                                <b class='text-muted fw-semibold'>Condiciones tablero de vehículo</b>
                                <ul>";
                                $divtablero = explode("-",$idtablero);
                                for($i = 0; $i < sizeof($divtablero); $i++){
                                    $datos .= ($divtablero[$i]==1)?"<li class='text-primary fw-semibold'>Testigos encendidos (Motor, servicio, aceite, Temperatura, etc).</li>":"";
                                    $datos .= ($divtablero[$i]==2)?"<li class='text-primary fw-semibold'>No enciende.</li>":"";
                                    $datos .= ($divtablero[$i]==3)?"<li class='text-primary fw-semibold'>No marca gasolina, RPM, KM, temperatura, etc.</li>":"";
                                    $datos .= ($divtablero[$i]==4)?"<li class='text-primary fw-semibold'>Arnés o contra arnés dañado.</li>":"";
                                    $datos .= ($divtablero[$i]==5)?"<li class='text-primary fw-semibold'>Sin observación.</li>":"";
                                    $datos .= ($divtablero[$i]==6)?"<li class='text-primary fw-semibold'>Otros: $otrostablero</li>":"";
                                }
                    $datos.= "  </ul>
                            </div>
                            <div class='col-md-4 mt-5'>
                                <b class='text-muted fw-semibold'>Condiciones cableado interno del tablero</b>
                                <ul>";
                                $divcableado = explode("-",$idcableado);
                                for($i = 0; $i < sizeof($divcableado); $i++){
                                    $datos .= ($divcableado[$i]==1)?"<li class='text-primary fw-semibold'>Cables sueltos.</li>":"";
                                    $datos .= ($divcableado[$i]==2)?"<li class='text-primary fw-semibold'>Cables sin aislamiento.</li>":"";
                                    $datos .= ($divcableado[$i]==3)?"<li class='text-primary fw-semibold'>Empalme de cables excesivo.</li>":"";
                                    $datos .= ($divcableado[$i]==4)?"<li class='text-primary fw-semibold'>Sin observación.</li>":"";
                                    $datos .= ($divcableado[$i]==5)?"<li class='text-primary fw-semibold'>Otros: $otroscableado</li>":"";
                                }
                    $datos .="  </ul>
                            </div>
                            <div class='col-md-4 mt-5'>
                                <b class='text-muted fw-semibold'>El vehículo cuenta 'Ya' con sistema de corta corriente</b>
                                <ul>";
                                $divcorriente = explode("-",$idccorriente);
                                for($i = 0; $i < sizeof($divcorriente); $i++){
                                    $datos .= ($divcorriente[$i]==1)?"<li>Alarma con corta corriente activo.</li>":"";
                                    $datos .= ($divcorriente[$i]==2)?"<li>GPS con corta corriente activo.</li>":"";
                                    $datos .= ($divcorriente[$i]==3)?"<li>Switch corta corriente.</li>":"";
                                    $datos .= ($divcorriente[$i]==4)?"<li>NO CUENTA.</li>":"";
                                    $datos .= ($divcorriente[$i]==5)?"<li>Otros: $otroscorriente</li>":"";
                                }
                    $datos .="  </ul>
                            </div>
                            <div class='col-md-4 mt-5'>
                                <b class='text-muted fw-semibold'>Tipo de servicio</b>
                                <ul>";
                                $divservicio = explode("-",$idtservicio);
                                for($i = 0; $i < sizeof($divservicio); $i++){
                                    $datos .= ($divservicio[$i]==1)?"<li>Instalación.</li>":"";
                                    $datos .= ($divservicio[$i]==2)?"<li>Reubicación.</li>":"";
                                    $datos .= ($divservicio[$i]==3)?"<li>Reposición.</li>":"";
                                    $datos .= ($divservicio[$i]==4)?"<li>Retiro.</li>":"";
                                    $datos .= ($divservicio[$i]==5)?"<li>Revisión.</li>":"";
                                    $datos .= ($divservicio[$i]==6)?"<li>Cambio de unidad.</li>":"";
                                    $datos .= ($divservicio[$i]==8)?"<li>Cambio de unidad, Modelo anterior: $modeloanterior, IMEI anterior: $imeianterior</li>":"";
                                    $datos .= ($divservicio[$i]==9)?"<li>Cambio de SIM, Número anterior: $simanterior</li>":"";
                                    $datos .= ($divservicio[$i]==7)?"<li>Otros: $otrostservicio</li>":"";
                                }
                    $datos .= " </ul>
                            </div>
                        </div>
                        <div class='row mt-5'>
                            <div class='col-md-12'><b><h4 class='titulo-lista fs-6 fw-semibold text-uppercase fw-bold'>Datos del equipo GPS</h4></b></div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Modelo de GPS:</b> <br>$gpsvehiculo</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>IMEI:</b> <br>$imei</div>
                            <div class='col-md-4'><b class='text-muted fw-semibold'>Número teléfonico:</b> <br>$numtelefono</div>
                            <div class='col-md-12'>&nbsp;</div>
                            <div class='col-md-4 mt-3'>
                                <b class='text-muted fw-semibold'>Accesorios a instalar</b>
                                <ul>";
                                $divaccesorio = explode("-",$idaccesorio);
                                for($i = 0; $i < sizeof($divaccesorio); $i++){
                                    $datos .= ($divaccesorio[$i]==1)?"<li>Botón de pánico.</li>":"";
                                    $datos .= ($divaccesorio[$i]==2)?"<li>Bocina.</li>":"";
                                    $datos .= ($divaccesorio[$i]==3)?"<li>Micrófono.</li>":"";
                                    $datos .= ($divaccesorio[$i]==4)?"<li>Corte de corriente/ combustible.</li>":"";
                                    $datos .= ($divaccesorio[$i]==5)?"<li>Sensor de gasolina.</li>":"";
                                    $datos .= ($divaccesorio[$i]==6)?"<li>Sensores de puertas.</li>":"";
                                    $datos .= ($divaccesorio[$i]==7)?"<li>Sensor de impacto.</li>":"";
                                    $datos .= ($divaccesorio[$i]==8)?"<li>Cámara.</li>":"";
                                    $datos .= ($divaccesorio[$i]==9)?"<li>Chapa magnética.</li>":"";
                                    $datos .= ($divaccesorio[$i]==10)?"<li>Solo GPS.</li>":"";
                                    $datos .= ($divaccesorio[$i]==11)?"<li>Solo revisión.</li>":"";
                                    $datos .= ($divaccesorio[$i]==12)?"<li>Ninguno.</li>":"";                                
                                }
                    $datos .= " </ul>
                            </div>
                            <div class='col-md-4 mt-3'>
                                <b class='text-muted fw-semibold'>Observaciones</b>
                                <ul>";
                                $datos .= ($observaciones != "")?"<li>$observaciones</li>":"<li>Ninguna</li>";
                    $datos.="   </ul>
                            </div>
                            <div class='col-md-4 mt-3'>
                                <b class='text-muted fw-semibold'>Lista de instalación.</b>
                                <ul>";
                                $divinst = explode("-",$idinstalacion);
                                for($i = 0; $i < sizeof($divinst); $i++){
                                    $datos .= ($divinst[$i]==1)?"<li>GPS fijo o móvil.</li>":"";
                                    $datos .= ($divinst[$i]==2)?"<li>Arnés protegido.</li>":"";
                                    $datos .= ($divinst[$i]==3)?"<li>Corte de corriente/ combustible protegido.</li>":"";
                                    $datos .= ($divinst[$i]==4)?"<li>Conexiones del arnés al GPS conectadas y protegidas.</li>":"";
                                    $datos .= ($divinst[$i]==5)?"<li>Accesorios bien sujetados.</li>":"";
                                    $datos .= ($divinst[$i]==6)?"<li>Apagado exitoso.</li>":"";
                                    $datos .= ($divinst[$i]==7)?"<li>Desbloqueo exitoso.</li>":"";
                                    $datos .= ($divinst[$i]==8)?"<li>El equipo envía datos GPS con éxito.</li>":"";                              
                                }                            
                    $datos .="  </ul>
                            </div>
                            <div class='col-md-6 mt-2 mb-5'><b class='text-muted fw-semibold'>Nombre del instalador:</b> <br>$nombreinstalador</div>
                            <div class='col-md-6 mb-4'>
                                <div class='col-12'>
                                    <label class='control-label col-md-2 text-right text-muted fw-semibold' for='encargado-cliente'>Encargado</label>
                                    <br>
                                    <div class='col-md-12 form-group'>
                                        <div class='input-group'>
                                            <input class='form-control text-center input-form col-12' id='encargado-cliente' name='encargado-cliente' type='text' value='$encargado' onchange='saveTMP();'/>
                                        </div>
                                        <div id='encargado-cliente-errors'></div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                } 
                else if($tipo_unidad == 2){
                    $datos .= " <div class='row'>
                                    <div class='col-md-12 mb-2'><b><h4 class='titulo-lista fs-6 fw-semibold text-uppercase fw-bold'>Datos Servicio</h4></b></div>
                                    <div class='col-md-3'><b class='text-muted fw-semibold'>Fecha del servicio:</b><br> $fechaservicio</div>
                                    <div class='col-md-3'><b class='text-muted fw-semibold'>Hora del servicio:</b> <br>$horaservicio</div>
                                    <div class='col-md-3'><b class='text-muted fw-semibold'>Cliente:</b> <br>$cliente</div>
                                    <div class='col-md-3'><b class='text-muted fw-semibold'>Plataforma:</b> <br>$plataforma</div>
                                </div>
                                <div class='row mt-5'>
                                    <div class='col-md-12'><b><h4 class='titulo-lista fs-6 fw-semibold text-uppercase fw-bold'>Datos del vehiculo</h4></b></div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Tipo:</b> $tipo</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Marca:</b> ".(($marca=="")?"---":$marca)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Modelo:</b> ".(($modelo=="")?"---":$modelo)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Año:</b> ".(($anho=="")?"---":$anho)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Color:</b> ".(($color=="")?"---":$color)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Serie:</b> ".(($serie=="")?"---":$serie)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>N° Economico:</b> ".(($numeconomico=="")?"---":$numeconomico)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Kilometraje:</b> ".(($km=="")?"---":$km)."</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Placas:</b> ".(($placas=="")?"---":$placas)."</div>
                                    
                                    <div class='col-md-12 mt-5'><b class='text-muted fw-semibold'><h4 class='titulo-lista fs-6 fw-semibold text-uppercase fw-bold'>Datos del equipo GPS</h4></b></div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Modelo de GPS:</b> $gpsvehiculo</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>IMEI:</b> $imei</div>
                                    <div class='col-md-4'><b class='text-muted fw-semibold'>Num Telefonico:</b> $numtelefono</div>
                                    <div class='col-12'>&nbsp;</div>
                                    <div class='col-md-4 mt-3'>
                                        <b class='text-muted fw-semibold'>Condiciones del cableado interno del tablero.</b>
                                        <ul>";
                                        $divcableado = explode("-",$idcableado);
                                        for($i = 0; $i < sizeof($divcableado); $i++){
                                            $datos .= ($divcableado[$i]==1)?"<li class='text-primary fw-semibold'>Cables sueltos.</li>":"";
                                            $datos .= ($divcableado[$i]==2)?"<li class='text-primary fw-semibold'>Cables sin aislamiento.</li>":"";
                                            $datos .= ($divcableado[$i]==3)?"<li class='text-primary fw-semibold'>Empalme de cables excesivo.</li>":"";
                                            $datos .= ($divcableado[$i]==4)?"<li class='text-primary fw-semibold'>Sin observación.</li>":"";
                                            $datos .= ($divcableado[$i]==5)?"<li class='text-primary fw-semibold'>Otros: $otroscableado</li>":"";
                                        }

                            $datos .= " </ul>
                                    </div>
                                    <div class='col-md-4 mt-3'>
                                        <b class='text-muted fw-semibold'>Tipo de servicio.</b>
                                        <ul>";
                                        $divservicio = explode("-",$idtservicio);
                                        for($i = 0; $i < sizeof($divservicio); $i++){
                                            $datos .= ($divservicio[$i]==1)?"<li>Instalación.</li>":"";
                                            $datos .= ($divservicio[$i]==2)?"<li>Reubicación.</li>":"";
                                            $datos .= ($divservicio[$i]==3)?"<li>Reposición.</li>":"";
                                            $datos .= ($divservicio[$i]==4)?"<li>Retiro.</li>":"";
                                            $datos .= ($divservicio[$i]==5)?"<li>Revisión.</li>":"";
                                            $datos .= ($divservicio[$i]==6)?"<li>Cambio de unidad.</li>":"";
                                            $datos .= ($divservicio[$i]==8)?"<li>Cambio de unidad, Modelo anterior: $modeloanterior, IMEI anterior: $imeianterior.</li>":"";
                                            $datos .= ($divservicio[$i]==9)?"<li>Cambio de SIM, Número anterior: $simanterior.</li>":"";
                                            $datos .= ($divservicio[$i]==7)?"<li>Otros: $otrostservicio</li>":"";
                                        }
                            $datos .= " </ul>
                                    </div>
                                    <div class='col-md-4 mt-3'>
                                        <b class='text-muted fw-semibold'>Accesorios a instalar</b>
                                        <ul>";
                                        $divaccesorio = explode("-",$idaccesorio);
                                        for($i = 0; $i < sizeof($divaccesorio); $i++){
                                            $datos .= ($divaccesorio[$i]==1)?"<li>Botón de pánico.</li>":"";
                                            $datos .= ($divaccesorio[$i]==2)?"<li>Bocina.</li>":"";
                                            $datos .= ($divaccesorio[$i]==3)?"<li>Micrófono.</li>":"";
                                            $datos .= ($divaccesorio[$i]==4)?"<li>Corte de corriente/ combustible.</li>":"";
                                            $datos .= ($divaccesorio[$i]==5)?"<li>Sensor de gasolina.</li>":"";
                                            $datos .= ($divaccesorio[$i]==6)?"<li>Sensores de puertas.</li>":"";
                                            $datos .= ($divaccesorio[$i]==7)?"<li>Sensor de impacto.</li>":"";
                                            $datos .= ($divaccesorio[$i]==8)?"<li>Cámara.</li>":"";
                                            $datos .= ($divaccesorio[$i]==9)?"<li>Chapa magnética.</li>":"";
                                            $datos .= ($divaccesorio[$i]==10)?"<li>Solo GPS.</li>":"";
                                            $datos .= ($divaccesorio[$i]==11)?"<li>Solo revisión.</li>":"";
                                            $datos .= ($divaccesorio[$i]==12)?"<li>Ninguno.</li>":"";                                
                                            $datos .= ($divaccesorio[$i]==13)?"<li>Claxón.</li>":"";                                
                                        }
                            $datos .= " </ul>
                                        <b>Observaciones Accesorios: ".(($observaciones != "")?"$observaciones":"---")."</b>
                                    </div>
                                    <div class='col-md-12 mt-2'>
                                        <b class='text-muted fw-semibold'>Lista de instalación.</b>
                                        <ul>";
                                        $divinst = explode("-",$idinstalacion);
                                        for($i = 0; $i < sizeof($divinst); $i++){
                                            $datos .= ($divinst[$i]==1)?"<li>GPS fijo o móvil.</li>":"";
                                            $datos .= ($divinst[$i]==2)?"<li>Arnés protegido.</li>":"";
                                            $datos .= ($divinst[$i]==3)?"<li>Corte de corriente/ combustible protegido.</li>":"";
                                            $datos .= ($divinst[$i]==4)?"<li>Conexiones Del Arnés al GPS conectadas y protegidas.</li>":"";
                                            $datos .= ($divinst[$i]==5)?"<li>Accesorios bien sujetados.</li>":"";
                                            $datos .= ($divinst[$i]==6)?"<li>Apagado exitoso.</li>":"";
                                            $datos .= ($divinst[$i]==7)?"<li>Desbloqueo exitoso.</li>":"";
                                            $datos .= ($divinst[$i]==8)?"<li>El equipo envía datos GPS con éxito.</li>":"";                              
                                            $datos .= ($divinst[$i]==9)?"<li>Claxón exitoso.</li>":"";                              
                                            $datos .= ($divinst[$i]==10)?"<li>Botón de pánico (Ubicación: $ubicacion_boton_panico).</li>":"";                         
                                            $datos .= ($divinst[$i]==13)?"<li>Cámaras.</li>":"";                              
                                        }
                            $datos .="  </ul>
                                    </div>
                                    <div class='col-md-12'>
                                        <b class='text-muted fw-semibold'>Observaciones en general:</b> <br> ".(($observacion_general != "")?"$observacion_general":"---")."
                                    </div>
                                    <div class='col-md-6 mt-4'><b class='text-muted fw-semibold'>Nombre del instalador:</b> $nombreinstalador</div>
                                    <div class='col-md-6 mb-4'>
                                        <div class='row'>
                                            <label class='control-label col-md-2 text-right class='text-muted fw-semibold'' for='encargado-cliente'>Encargado</label>
                                            <div class='col-md-10 form-group'>
                                                <div class='input-group'>
                                                    <input class='form-control text-center input-form' id='encargado-cliente' name='encargado-cliente' type='text' value='$encargado' onchange='saveTMP();'/>
                                                </div>
                                                <div id='encargado-cliente-errors'></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                }

        }
        return $datos;
    }

    public function existeTMPvideo($paso, $ch, $idorden){
        $consulta = "SELECT COUNT(*) maximo FROM tmpvid WHERE paso = :paso AND ch = :ch AND idorden = :idorden";
        $val = array("paso" => $paso, "ch" => $ch, "idorden" => $idorden);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach($stmt as $rs){
            $n = $rs['maximo'];
        }

        return $n;
    }

    public function insertarVid($name, $vid, $sessionid, $idorden, $paso, $check) {
        $existe = $this->existeTMPvideo($paso, $check, $idorden);
        $insertado = false;
        if($existe == 0 || $paso == 0){
            $consulta = "INSERT INTO `tmpvid` VALUES (:id, :name, :vidtmp, :sessionid, :idorden, :ch, :paso);";
        }else{
            $consulta = "UPDATE `tmpvid` SET namevid=:name, vidtmp = :vidtmp WHERE idorden = :idorden AND ch = :ch AND paso = :paso);";
        }
        
        $valores = array("id" => null,
            "vidtmp" => $vid,
            "name" => $name,
            "sessionid" => $sessionid,
            "idorden" => $idorden,
            "ch" => $check,
            "paso" => $paso
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function prevFileVidAux($idtmp) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpvid  WHERE idtmpvid = :id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function prevFileVid($idtmp) {
        $previmg = "";
        $img = $this->prevFileVidAux($idtmp);
        foreach ($img as $actual) {
            $previmg = $actual['vidtmp'];
        }
        return $previmg;
    }

    public function eliminarVidTmp($id) {
        $vid = $this->prevFileVid($id);
        if(file_exists("../temporal/tmpvideo/$vid")){
            unlink("../temporal/tmpvideo/$vid");
        }
        $consultado = false;
        $consulta = "DELETE FROM tmpvid where idtmpvid=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getFirma($id){
        $firma = "";
        $query = "SELECT firma FROM ordeninstalacion WHERE idorden = :id";
        $val = array("id" => $id);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $firma = $rs['firma'];
        }
        return $firma;
    }

    public function finalizarOrden($idorden){
        $firma_final = "";
        $firma_hoja = $this->getFirma($idorden);
        $fecha = date("Y-m-d H:i:s", strtotime("-1 hour"));
        $idlogin = $_SESSION[sha1("idusuario")];
        $insertado = 0;
        
        $consultas = "SELECT * FROM tmpinstalacion WHERE idorden = :idorden";
        $val = array("idorden" => $idorden);
        $consultado = $this->consultas->getResults($consultas, $val);
        foreach($consultado as $rs){
            $iddanhos = $rs["iddanhos"];
            $idmolduras = $rs["idmolduras"];
            $otrosmolduras = $rs["otrosmolduras"];
            $idtablero = $rs["idtablero"];
            $otrostablero = $rs["otrostablero"];
            $idcableado = $rs["idcableado"];
            $otroscableado = $rs["otroscableado"];
            $idccorriente = $rs["idccorriente"];
            $otroscorriente = $rs["otroscorriente"];
            $idtservicio = $rs["idtservicio"];
            $modeloanterior = $rs["modeloanterior"];
            $imeianterior = $rs["imeianterior"];
            $simanterior = $rs["simanterior"];
            $otrostservicio = $rs["otrostservicio"];
            $gpsvehiculo = $rs["gpsvehiculo"];
            $imei = $rs["imei"];
            $numtelefono = $rs["numtelefono"];
            $idinstalador = $rs["idinstalador"];
            $idaccesorio = $rs["idaccesorio"];
            $observaciones = $rs["observaciones"];
            $idinstalacion = $rs["idinstalacion"];
            $encargado = $rs["encargado"];
            $firma_tmp = $rs["firma"];
            $imgfrentevehiculo = $rs["imgfrentevehiculo"];
            $frentebase = $rs['imgfrentebase'];
            $imgnserie = $rs["imgnserie"];
            $seriebase = $rs['imgseriebase'];
            $imgtabinicial = $rs["imgtabinicial"];
            $tableroinibase = $rs['imgtabinibase'];
            $imgtabfinal = $rs["imgtabfinal"];
            $tablerofinbase = $rs['imgtabfinbase'];
            $imgantesinst = $rs["imgantesinst"];
            $antesbase = $rs['imgantesbase'];
            $imgdespuesinst = $rs["imgdespuesinst"];
            $despuesbase = $rs['imgdespuesbase'];
            $letrafolio = $rs["letra_folio"];
            $folioinstalacion = $rs["folioinstalacion"];
            $observacion_general = $rs["observacion_general"];
            $ubicacion_boton_panico = $rs["ubicacion_boton_panico"];
            $tipo_corte = $rs["tipo_corte"];
        }

        $folio = $letrafolio . $folioinstalacion;

        if($firma_tmp == ""){
            $firma_final = $firma_hoja;
        } else if($firma_tmp == $firma_hoja){
            $firma_final = $firma_hoja;
        } else if($firma_tmp != $firma_hoja){
            $firma_final = $firma_tmp;
        }

        $consulta = "UPDATE ordeninstalacion SET iddanhos = :iddanhos, idmolduras = :idmolduras, otrosmolduras = :otrosmolduras, idtablero = :idtablero, otrostablero = :otrostablero, idcableado = :idcableado, otroscableado = :otroscableado, idccorriente = :idccorriente, otroscorriente = :otroscorriente, idtservicio = :idtservicio, modeloanterior = :modeloanterior, imeianterior = :imeianterior, simanterior = :simanterior, otrostservicio = :otrostservicio, gpsvehiculo = :gpsvehiculo, imei = :imei, numtelefono = :numtelefono, idinstalador = :idinstalador, idaccesorio = :idaccesorio, observaciones = :observaciones, idinstalacion = :idinstalacion, encargado = :encargado, firma = :firma, imgfrentevehiculo = :imgfrentevehiculo, imgfrentebase=:frentebase, imgnserie = :imgnserie, imgseriebase=:seriebase, imgtabinicial = :imgtabinicial, imgtabinibase=:tableroinibase, imgtabfinal = :imgtabfinal, imgtabfinbase=:tablerofinbase, imgantesinst = :imgantesinst, imgantesbase=:antesbase, imgdespuesinst = :imgdespuesinst, imgdespuesbase=:despuesbase, fecha_edicion_hoja = :fechaedicion, fecha_firma_cliente = :fechfirma, estado_orden=:estado, observacion_general = :observacion_general, ubicacion_boton_panico = :ubicacion_boton_panico, tipo_corte = :tipo_corte, idfinaliza = :idfinaliza WHERE idorden = :id";
        $val = array(
            "iddanhos" => $iddanhos,
            "idmolduras" => $idmolduras,
            "otrosmolduras" => $otrosmolduras,
            "idtablero" => $idtablero,
            "otrostablero" => $otrostablero,
            "idcableado" => $idcableado,
            "otroscableado" => $otroscableado,
            "idccorriente" => $idccorriente,
            "otroscorriente" => $otroscorriente,
            "idtservicio" => $idtservicio,
            "modeloanterior" => $modeloanterior,
            "imeianterior" => $imeianterior,
            "simanterior" => $simanterior,
            "otrostservicio" => $otrostservicio,
            "gpsvehiculo" => $gpsvehiculo,
            "imei" => $imei,
            "numtelefono" => $numtelefono,
            "idinstalador" => $idinstalador,
            "idaccesorio" => $idaccesorio,
            "observaciones" => $observaciones,
            "idinstalacion" => $idinstalacion,
            "encargado" => $encargado,
            "firma" => $firma_final,
            "imgfrentevehiculo" => $imgfrentevehiculo,
            "frentebase" => $frentebase,
            "imgnserie" => $imgnserie,
            "seriebase" => $seriebase,
            "imgtabinicial" => $imgtabinicial,
            "tableroinibase" => $tableroinibase,
            "imgtabfinal" => $imgtabfinal,
            "tablerofinbase" => $tablerofinbase,
            "imgantesinst" => $imgantesinst,
            "antesbase" => $antesbase,
            "imgdespuesinst" => $imgdespuesinst,
            "despuesbase" => $despuesbase,
            "fechaedicion" => $fecha,
            "fechfirma" => $fecha,
            "estado" => 3,
            "id" => $idorden,
            "observacion_general" => $observacion_general,
            "ubicacion_boton_panico" => $ubicacion_boton_panico,
            "tipo_corte" => $tipo_corte,
            "idfinaliza" => $idlogin
        );

        $insertado = $this->consultas->execute($consulta, $val);
        $foto = $this->actualizarDetalleNew($idorden, $folio);
        return $insertado;
    }

    public function actualizarDetalleNew($idorden, $folio) {
        $query = "DELETE FROM tmpinstalacion WHERE idorden = :id";
        $val = array("id" => $idorden);
        $eliminado = $this->consultas->execute($query, $val);

        $query = "SELECT * FROM tmpimginstotras WHERE idorden = :id";
        $val = array("id" => $idorden);
        $consultado = $this->consultas->getResults($query, $val);
        foreach($consultado as $rs){
            $nameimg = $rs['imgname'];
            $base = $rs['imagenbase'];
            $paso = $rs['paso'];
            $ch = $rs['ch'];
            $titulo = $rs['titulo'];
            $consulta = "INSERT INTO imginstalacion VALUES (:id, :imginstalacion, :imgb64, :folio, :paso, :ch, :titulo);";
            $valores = array("id" => null,
                "imginstalacion" => $base,
                "imgb64" => $nameimg,
                "folio" => $folio,
                "paso" => $paso,
                "ch" => $ch,
                "titulo" => $titulo);
            $insertado = $this->consultas->execute($consulta, $valores);
        }
        $query = "DELETE FROM tmpimginstotras WHERE idorden = :id";
        $val = array("id" => $idorden);
        $eliminado = $this->consultas->execute($query, $val);

        $query = "SELECT * FROM tmpvid WHERE idorden = :id";
        $val = array("id" => $idorden);
        $consultadotmp = $this->consultas->getResults($query, $val);
        
        foreach ($consultadotmp as $vactual) {
            $vidtmp = $vactual['vidtmp'];
            $nametmp = $vactual['namevid'];

            $consultav = "INSERT INTO vidinstalacion VALUES (:id, :namevid, :vidinstalacion, :folio)";
            $val = array("id" => null, "namevid"=>$nametmp, "vidinstalacion" => $vidtmp, "folio" => $folio);
            $insertadov = $this->consultas->execute($consultav, $val);
            $ruta = "../temporal/tmpvideo/$vidtmp";
            $contenido = @file_get_contents($ruta); 
            if ($contenido !== false) {
                rename("../temporal/tmpvideo/$vidtmp", "../img/instalacion/$vidtmp");
            }
        }
        
        $querytmp = "DELETE FROM tmpvid WHERE idorden = :id";
        $val = array("id" => $idorden);
        $eliminado = $this->consultas->execute($querytmp, $val);
        return $eliminado;
    }

    public function deleteImgsPanic($id){
        $deleted = 0;
        $query = "SELECT idimg FROM tmpimginstotras WHERE idorden = :id AND paso = 10 AND (ch = 11 OR ch = 12)";
        $val = array("id" => $id);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $idtmp = $rs['idimg'];
            $consulta = "DELETE FROM tmpimginstotras where idimg=:id;";
            $val = array("id" => $idtmp);
            $deleted = $this->consultas->execute($consulta, $val);
        }
        return $deleted;
    }

    public function deleteTmpVidByName($nombre){
        $id = 0;
        $query = "SELECT idtmpvid FROM tmpvid WHERE vidtmp = :vidtmp";
        $val = array("vidtmp" => $nombre);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $id = $rs['idtmpvid'];
        }

        return $this->eliminarVidTmp($id);
    } 

    public function verifyVideoTMP($paso, $check, $idorden){
        $consultas = "SELECT COUNT(*) maximo FROM tmpvid WHERE paso = :paso AND ch = :ch AND idorden = :idorden";
        $val = array("paso" => $paso, "ch" => $check,  "idorden" => $idorden);
        $stmt = $this->consultas->getResults($consultas, $val);
        foreach($stmt as $rs){
            $n = $rs['maximo'];
        }
        return $n;
    }

    private function getVidInsAux($folio) {
        $consultado = false;
        $consulta = "SELECT * FROM vidinstalacion where vid_folioins=:folio";
        $val = array("folio" => $folio);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }


    public function getVidRegistrados($folio) {
        $datos = "";
        $vids = $this->getVidInsAux($folio);
        foreach ($vids as $actual) {
            $vid = $actual['idvidinstalacion'];
            $vidins = $actual['vidinstalacion'];
            $namevid = $actual['namevid'];

            $datos .= "
                <tr>
                    <td title='Ver video' style='word-break: break-all;' onclick=\"displayVid('$vidins')\">$namevid <span class='fas fa-video text-body-tertiary small'></span></td>
                </tr>";
        }
        if($datos == ""){
            $datos = "<tr>
                    <td style='word-break: break-all;'>No hay videos disponibles</td>
                </tr>";
        }
        return $datos;
    }

    public function existVideo($video){
        $ruta = "../img/instalacion/$video";
        $contenido = @file_get_contents($ruta); 
        if ($contenido !== false) {
            $message = "1"; 
        } else {
            $message = "0"; 
        }
        return $message;
    }
    

    public function getDatosClientebyID($id) {
        $consultado = false;
        $consulta = "SELECT * FROM cliente c WHERE id_cliente=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getNameImgOtras($paso, $check, $folio){
        $datos = "";
        $query = "SELECT CONCAT(imginstalacion,'</tr>',titulo) datos FROM imginstalacion WHERE paso = :paso and ch = :ch and img_folioins = :folio";
        $val = array("paso" => $paso, "ch" => $check, "folio" => $folio);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $datos = $rs['datos'];
        }
        return $datos;
    }

    public function getImgInsAux($folio) {
        $consultado = false;
        $consulta = "SELECT * FROM imginstalacion where img_folioins=:folio AND paso = :paso AND ch = :ch";
        $val = array("folio" => $folio, "paso" => 0, "ch" => 0);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getConfigMailAux() {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE idcorreoenvio=:id;";
        $valores = array("id" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getConfigMail() {
        $datos = "";
        $get = $this->getConfigMailAux();
        foreach ($get as $actual) {
            $correo = $actual['correo'];
            $pass = $actual['password'];
            $remitente = $actual['remitente'];
            $correoremitente = $actual['correoremitente'];
            $host = $actual['host'];
            $puerto = $actual['puerto'];
            $seguridad = $actual['seguridad'];
            $datos = "$correo</tr>$pass</tr>$remitente</tr>$correoremitente</tr>$host</tr>$puerto</tr>$seguridad";
        }
        return $datos;
    }

    public function mail($m) {
        $config = $this->getConfigMail();
        $div = explode("</tr>", $config);
        $correoenvio = $div[0];
        $pass = $div[1];
        $remitente = $div[2];
        $correoremitente = $div[3];
        $host = $div[4];
        $puerto = $div[5];
        $seguridad = $div[6];
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Mailer = 'smtp';
        $mail->SMTPAuth = true;
        $mail->Host = $host;
        $mail->Port = $puerto;
        $mail->SMTPSecure = $seguridad;
        $correo = $this->getCorreosAux($m->getIdfactura());
        foreach ($correo as $correoactual) {
            $idorden = $correoactual['idorden'];
            $letrafolio = $correoactual['letra_folio'];
            $folio = $correoactual['folioinstalacion'];
            $cliente = $correoactual['cliente'];
            $correo1 = $correoactual['email_informacion'];
            $correo2 = $correoactual['email_facturacion'];
            $correo3 = $correoactual['email_gerencia'];
        }
        
        $noorden = $idorden;
        if ($noorden < 10) {
            $noorden = "00$idorden";
        } else if ($noorden >= 10 && $noorden < 100) {
            $noorden = "0$idorden";
        }
        $mail->Username = $correoenvio;
        $mail->Password = $pass;
        $mail->From = $correoremitente;
        $mail->FromName = $remitente;
        $mail->Subject = 'Hoja de servicio';
        $mail->Body = 'Buenos dias '. iconv('UTF-8', 'windows-1252',$cliente).' le enviamos el reporte del servicio realizado. Un gusto saludarle.';
        if($m->getChmail1() == '1'){
            $mail->addAddress($correo1);
        }
        if($m->getChmail2() == '1'){
            $mail->addAddress($correo2);
        }
        if($m->getChmail3() == '1'){
            $mail->addAddress($correo3);
        }
        if($m->getChmail4() == '1'){
            $mail->addAddress($m->getMailalt1());
        }
        
        $mail->isHTML(true);
        $mail->addStringAttachment($m->getPdfstring(), "Hoja_Servicio_$noorden.pdf");
        $vid = $this->getVidInsAux($letrafolio.$folio);
        foreach ($vid as $vactual){
            $vidinstalacion = $vactual['vidinstalacion'];
            $mail->addAttachment("../img/instalacion/$vidinstalacion");
        }
        if (!$mail->send()) {
            echo '0No se ha podido mandar el mensaje '. $mail->ErrorInfo;
        } else {
            return 'Se envió el correo correctamente.';
        }
    }

    public function actualizarFirma($i) {
        $actualizado = false;
        $consulta = "UPDATE `ordeninstalacion` SET encargado=:encargado, firma=:firma where idorden=:id;";
        $valores = array("id" => $i->getIdhojaservicio(),
            "encargado" => $i->getEncargado(),
            "firma" => $i->getFirma());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function cancelarInstalacion($id, $motivo){
        $fecha = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $idlogin = $_SESSION[sha1("idusuario")];
        $query = "UPDATE ordeninstalacion SET estado_orden = :estado, fecha_cancelacion = :fecha, motivo_cancelacion = :motivo, id_persona_cancela = :idusu WHERE idorden = :id;";
        $val = array(
            "estado" => 4,
            "fecha" => $fecha,
            "motivo" => $motivo,
            "idusu" => $idlogin,
            "id" => $id);
        $cancelado = $this->consultas->execute($query, $val);
        return $cancelado;
    }

    public function showCancel($id){
        $json  = array();
        $query = "SELECT DATE_FORMAT(fecha_cancelacion, '%d/%m/%Y') fecha,
        TIME_FORMAT(fecha_cancelacion, '%r') hora,
        motivo_cancelacion,
        CONCAT(u.nombre,' ',apellido_paterno,' ',apellido_materno) nombre,
        folioinstalacion, letra_folio, idorden
        FROM ordeninstalacion o
        INNER JOIN usuario u ON u.idusuario = o.id_persona_cancela
        WHERE idorden = :id";
        $val = array("id" => $id);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $json['fecha'] = $rs['fecha'];
            $json['hora'] = $rs['hora'];
            $json['motivo'] = $rs['motivo_cancelacion'];
            $json['nombre'] = $rs['nombre'];
            $json['letra_folio'] = $rs['letra_folio'];
            $json['idorden'] = $rs['idorden'];
            $json['folio'] = $rs['folioinstalacion'];
        }
        return $json;
    }

    public function getOrdenAux($dinicio, $dfin, $nomcliente, $idservicio) {
        $cliente = "";
        if($nomcliente != ""){
            $cliente = " and cliente like '%$nomcliente%'";
        }
        $servicio = "";
        if($idservicio != ""){
            $servicio = " and idtservicio like '%$idservicio%'";
        }
        $consultado = false;
        $consulta = "SELECT * FROM ordeninstalacion where (fechaservicio between :dinicio and :dfin)$servicio $cliente;";
        $val = array("dinicio" => $dinicio,
            "dfin" => $dfin);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }
}
