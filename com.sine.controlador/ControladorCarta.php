<?php 
require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/CartaPorte.php';
require_once '../com.sine.modelo/SendMail.php';
require_once '../com.sine.modelo/TMP.php';

use SWServices\Toolkit\SignService as Sellar;
use SWServices\Stamp\StampService as StampService;
use SWServices\Cancelation\CancelationService as CancelationService;
use SWServices\SatQuery\SatQueryService as consultaCfdiSAT;


date_default_timezone_set("America/Mexico_City");

class ControladorCarta {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    public function getDatosFacturacionbyId($iddatos) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:id;";
        $valores = array("id" => $iddatos);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosEmisor($fid) {
        $datos = "";
        $sine = $this->getDatosFacturacionbyId($fid);
        foreach ($sine as $dactual) {
            $rfc = $dactual['rfc'];
            $razonsocial = $dactual['razon_social'];
            $clvreg = $dactual['c_regimenfiscal'];
            $regimen = $dactual['regimen_fiscal'];
            $codpos = $dactual['codigo_postal'];
            $datos .= "$rfc</tr>$razonsocial</tr>$clvreg</tr>$regimen</tr>$codpos";
        }
        return $datos;
    }

    private function checkRemolqueAux($placa) {
        $consultado = false;
        $consulta = "SELECT * FROM remolque WHERE placaremolque=:placa;";
        $val = array("placa" => $placa);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function checkRemolque($placa) {
        $check = '0';
        $datos = $this->checkRemolqueAux($placa);
        foreach ($datos as $actual) {
            $check = $actual['idremolque'];
        }
        return $check;
    }

    private function checkVehiculoAux($placa) {
        $consultado = false;
        $consulta = "SELECT * FROM transporte WHERE placavehiculo=:placa;";
        $val = array("placa" => $placa);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function checkVehiculo($placa) {
        $check = '0';
        $datos = $this->checkVehiculoAux($placa);
        foreach ($datos as $actual) {
            $check = $actual['idtransporte'];
        }
        return $check;
    }

    private function checkOperadorAux($rfc) {
        $consultado = false;
        $consulta = "SELECT * FROM operador WHERE rfcoperador=:rfc;";
        $val = array("rfc" => $rfc);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function checkOperador($rfc) {
        $check = '0';
        $datos = $this->checkOperadorAux($rfc);
        foreach ($datos as $actual) {
            $check = $actual['idoperador'];
        }
        return $check;
    }

    public function agregarCFDI($t) {
        $insertado = false;
        $consulta = "INSERT INTO `tmpcfdi` VALUES (:id, :tiporel, :desc_tiporel, :uuid, :session);";
        $valores = array("id" => null,
            "tiporel" => $t->getTiporel(),
            "desc_tiporel" =>  $t->getDescripcion(),
            "uuid" => $t->getUuid(),
            "session" => $t->getSessionid());
        $this->consultas->execute($consulta, $valores);
        $datos = $this->tablaCFDI($t->getSessionid());
        return $datos;
    }

    private function tablaCFDI($idsession, $uuid = "") {
        $datos = "<corte><thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>RELACIÓN</th>
                <th class='text-center'>CFDI</th>
                <th class='text-center'>ELIMINAR</th></tr>
                </thead><tbody>";
        $disuuid = "";
        if ($uuid != "") {
            $disuuid = "disabled";
        }
        $cfdi = $this->getTMPCFDIS($idsession);
        foreach ($cfdi as $actual) {
            $idtmp = $actual['idtmpcfdi'];
            $tiporel = $actual['desc_tiporel'];
            $uuid = $actual['uuid'];
            $datos .= "
                    <tr class='align-middle'>
                        <td class='text-center'>$tiporel</td>
                        <td class='text-center'>$uuid</td>
                        <td class='text-center'><button $disuuid class='button-list' onclick='eliminarCFDI($idtmp)' title='Eliminar CFDI'><span class='fas fa-times'></span></button></td>
                    </tr>
                     ";
        }
        return $datos;
    }

    private function getTMPCFDIS($sessionId) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpcfdi WHERE sessionid=:sessionid ORDER BY idtmpcfdi";
        $valores = array("sessionid" => $sessionId);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    //----------------------------------------------CARTA PORTE
    //----------------------------------------------MERCANCIA
    public function agregarMercancia($t) {
        $insertado = false;
        $consulta = "INSERT INTO `tmpmercancia` VALUES (:id, :clave, :descripcion, :cant, :unidad, :peso, :sid, :condicional, :peligro, :clvmaterial, :embalaje);";
        $valores = array("id" => null,
            "clave" => $t->getClvprod(),
            "descripcion" => $t->getDescripcion(),
            "cant" => $t->getCantidad(),
            "unidad" => $t->getUnidad(),
            "peso" => $t->getPeso(),
            "condicional" => $t->getCondicional(),
            "peligro" => $t->getPeligro(),
            "clvmaterial" => $t->getClvmaterial(),
            "embalaje" => $t->getEmbalaje(),
            "sid" => $t->getSid());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
    
    private function getTMPMercancia($sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpmercancia WHERE sid=:sid ORDER BY idtmpmercancia";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaMercancia($sessionid, $uuid = "") {
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>MERCANCÍA</th>
                <th class='text-center'>DESCRIPCIÓN</th>
                <th class='text-center col-md-1'>CANTIDAD</th>
                <th class='text-center'>CLAVE UNIDAD</th>
                <th class='text-center'>PESO</th>
                <th class='text-center'>MATERIAL PELIGROSO</th>
                <th class='text-center'>EMBALAJE</th>
                <th class='text-center'>OPCIÓN</th></tr>
                </thead><tbody>";

        $disuuid = "";
        $peso_mercancias = 0;
        if ($uuid != "") {
            $disuuid = "disabled";
        }

        $productos = $this->getTMPMercancia($sessionid);
        foreach ($productos as $actual) {
            $idtmp = $actual['idtmpmercancia'];
            $tmpclave = $actual['tmpclave'];
            $descripcion = $actual['tmpdescripcion'];
            $cant = $actual['tmpcant'];
            $unidad = $actual['tmpunidad'];
            $peso = $actual['tmppeso'];
            $peligro = $actual['tmppeligro'];
            $clvmaterial = $actual['tmpclvmaterial'];
            $embalaje = $actual['tmpembalaje'];

            $peligro = ($peligro == '0' || $peligro == '2') ? 'No' : ($peligro == '1' ? 'Si' : '');
            $materialpeligro = ($clvmaterial !== '') ? $peligro . " - " . $clvmaterial : $peligro;
            $peso_mercancias += bcdiv($peso, '1', 2);
            $disabledminus = ($cant == '1') ? "disabled" : "";
            $datos .= "
                    <tr>
                        <td>$tmpclave</td>
                        <td class='text-center'>$descripcion</td>
                        <td>
                        <div class='btn-group btn-group-sm'>
                            <button type='button' class='btn btn-outline-secondary' $disabledminus $disuuid data-type='minus' data-field='quant[1]' onclick='incrementarMercancia($idtmp, 2);'>
                                <span class='fas fa-minus small'></span>
                            </button>
                            <button class='badge btn btn-info' data-bs-toggle='modal' data-bs-target='#modal-cantidad' onclick='setCantMercancia($idtmp)'>
                                <div class='badge' id='badcant$idtmp'> $cant</div>
                            </button>   
                            <button type='button' class='btn btn-outline-secondary' $disuuid data-type='plus' onclick='incrementarMercancia($idtmp, 1);'>
                                <span class='fas fa-plus small'></span>
                            </button> </div></td>
                        <td>$unidad</td>
                        <td class='text-center'>$peso&nbsp;KG</td>
                        <td class='text-center'>$materialpeligro</td>
                        <td class='text-center'>$embalaje</td>
                        <td class='text-center'><div class='dropdown dropend'>
                        <button class='button-list dropdown-toggle' $disuuid title='Editar' type='button' data-bs-toggle='dropdown'><span class='fas fa-edit'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarMercancia($idtmp);'>Editar mercancía <span class='fas fa-edit small text-muted'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarMercancia($idtmp); return false;'>Eliminar mercancía<span class='fas fa-times text-muted'></span></a></li>
                        </ul>
                        </div></td>
                    </tr>
                     ";
        }
        return $datos."<input type='hidden' id='total-peso-mercancias' value='$peso_mercancias'/>";
    }

    private function getTMPMercanciaById($tid) {
        $consulta = "SELECT * FROM tmpmercancia WHERE idtmpmercancia=:tid";
        $val = array("tid" => $tid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function modificarCantMercancia($idtmp, $cant) {
        $datos = false;
        $consulta = "UPDATE `tmpmercancia` SET tmpcant=:cant WHERE idtmpmercancia=:id;";
        $valores = array("id" => $idtmp,
            "cant" => $cant);
        $datos = $this->consultas->execute($consulta, $valores);
        return $datos;
    }

    public function getCantTMPMercancia($tid) {
        $cant = false;
        $datos = $this->getTMPMercanciaById($tid);
        foreach ($datos as $actual) {
            $cant = $actual['tmpcant'];
        }
        return $cant;
    }

    public function incrementarMercancia($idtmp, $flag) {
        $cant = "+1";
        if ($flag == '2') {
            $cant = "-1";
        }
        $consulta = "UPDATE `tmpmercancia` SET tmpcant=(tmpcant$cant) WHERE idtmpmercancia=:id;";
        $valores = array("id" => $idtmp);
        $datos = $this->consultas->execute($consulta, $valores);
        return $datos;
    }

    public function getDatosMercancia($idtmp) {
        $tmp = $this->getTMPMercanciaById($idtmp);
        $datos = "";
        foreach ($tmp as $actual) {
            $idtmp = $actual['idtmpmercancia'];
            $clave = $actual['tmpclave'];
            $descripcion = $actual['tmpdescripcion'];
            $tmpcant = $actual['tmpcant'];
            $unidad = $actual['tmpunidad'];
            $peso = $actual['tmppeso'];
            $condicion = $actual['tmpcondpeligro'];
            $tmppeligro = $actual['tmppeligro'];
            $tmpclvmaterial = $actual['tmpclvmaterial'];
            $tmpembalaje = $actual['tmpembalaje'];
            $datos = "$idtmp</tr>$clave</tr>$descripcion</tr>$tmpcant</tr>$unidad</tr>$peso</tr>$condicion</tr>$tmppeligro</tr>$tmpclvmaterial</tr>$tmpembalaje";
            break;
        }
        return $datos;
    }

    public function actualizarMercancia($t) {
        $actualizado = false;
        $consulta = "UPDATE tmpmercancia SET tmpclave=:clave, tmpdescripcion=:descripcion, tmpcant=:cant, tmpunidad=:unidad, tmppeso=:peso, tmpcondpeligro=:tmpcondpeligro, tmppeligro=:tmppeligro, tmpclvmaterial=:tmpclvmaterial, tmpembalaje=:tmpembalaje WHERE idtmpmercancia=:id;";
        $valores = array("id" => $t->getTmpid(),
            "clave" => $t->getClvprod(),
            "descripcion" => $t->getDescripcion(),
            "cant" => $t->getCantidad(),
            "unidad" => $t->getUnidad(),
            "peso" => $t->getPeso(),
            "tmpcondpeligro" => $t->getCondicional(),
            "tmppeligro" => $t->getPeligro(),
            "tmpclvmaterial" => $t->getClvmaterial(),
            "tmpembalaje" => $t->getEmbalaje());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function eliminarMercancia($idtmp) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpmercancia` WHERE idtmpmercancia=:id;";
        $valores = array("id" => $idtmp);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    //-------------------------------------------UBICACION
    public function agregarUbicacion($t) {
        $insertado = false;
        $consulta = "INSERT INTO `tmpubicacion` VALUES (:id, :ubid, :nombre, :rfc, :idtipo, :idestado, :tmpnombre_estado, :cp, :distancia, :fecha, :hora, :sid, :direccion, :idmunicipio, :tmpnombre_municipio);";
        $valores = array("id" => null,
            "ubid" => $t->getTmpidubicacion(),
            "nombre" => $t->getNombre(),
            "rfc" => $t->getRfc(),
            "idtipo" => $t->getTipo(),
            "idestado" => $t->getEstado(),
            "tmpnombre_estado" => $t->getNombreEstado(),
            "cp" => $t->getCodpos(),
            "distancia" => $t->getDistancia(),
            "fecha" => $t->getFecha(),
            "hora" => $t->getHora(),
            "sid" => $t->getSid(),
            "direccion" => $t->getDireccion(),
            "idmunicipio" => $t->getIdmunicipio(),
            "tmpnombre_municipio" => $t->getNombreMunicipio(),
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }


    private function getTMPUbicacion($sid) {
        $consultado = false;
        $consulta = "SELECT t.* FROM tmpubicacion t WHERE sid=:sid ORDER BY tmpidtipo asc, tmpfecha asc, tmphora asc";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaUbicacion($sessionid, $uuid = "") {
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>RFC</th>
                <th class='text-center'>TIPO</th>
                <th class='text-center'>DIRECCIÓN</th>
                <th class='text-center col-md-1'>DISTANCIA</th>
                <th>FECHA y HORA DE SALIDA/LLEGADA</th>
                <th class='text-center col-md-1'>OPCIÓN</th></tr>
                </thead><tbody>";

        $disuuid = "";
        if ($uuid != "") {
            $disuuid = "disabled";
        }

        $productos = $this->getTMPUbicacion($sessionid);
        foreach ($productos as $actual) {
            $idtmp = $actual['idtmpubicacion'];
            $tmprfc = $actual['tmprfc'];
            $tmptipo = $actual['tmpidtipo'];
            $cp = $actual['tmpcodpostal'];
            $distancia = $actual['tmpdistancia'];
            $fecha = $actual['tmpfecha'];
            $hora = $actual['tmphora'];
            $estado = $actual['tmpnombre_estado'];
            $direccion = $actual['tmpdireccion'];
            $municipio = $actual['tmpnombre_municipio'];

            $div = explode("-", $fecha);
            $fecha = $div[2] . "/" . $div[1] . "/" . $div[0];

            if ($tmptipo == '1') {
                $tipo = "Origen";
            } else if ($tmptipo == '2') {
                $tipo = "Destino";
            }

            $datos .= "
                    <tr>
                        <td>$tmprfc</td>
                        <td>$tipo</td>
                        <td>$direccion $cp $municipio $estado</td>
                        <td class='text-center'>
                        <div class='input-group d-flex justify-content-center'>
                        <button $disuuid class='badge btn btn-info btn-xs center-block' data-bs-toggle='modal' data-bs-target='#modal-cantidad' onclick='setDistancia($idtmp)'><div class='badge' id='baddis$idtmp'> $distancia</div></button>
                        </div></td>
                        <td>$fecha $hora</td>
                        <td class='text-center'><div class='dropdown'>
                        <button $disuuid class='button-list dropdown-toggle' title='Editar'  type='button' data-bs-toggle='dropdown'><span class='fas fa-edit'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarUbicacion($idtmp);'>Editar ubicación <span class='fas fa-edit text-muted small'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarUbicacion($idtmp); return false;'>Eliminar ubicación<span class='fas fa-times text-muted'></span></a></li>
                        </ul>
                        </div></td>
                    </tr>
                     ";
        }
        return $datos;
    }

    private function getTMPUbicacionById($tid) {
        $consulta = "SELECT * FROM tmpubicacion WHERE idtmpubicacion=:tid";
        $val = array("tid" => $tid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getDistanciaTMP($tid) {
        $cant = "";
        $datos = $this->getTMPUbicacionById($tid);
        foreach ($datos as $actual) {
            $cant = $actual['tmpdistancia'];
        }
        return $cant;
    }

    public function modificarDistancia($idtmp, $cant) {
        $datos = false;
        $consulta = "UPDATE `tmpubicacion` SET tmpdistancia=:cant WHERE idtmpubicacion=:id;";
        $valores = array("id" => $idtmp,
            "cant" => $cant);
        $datos = $this->consultas->execute($consulta, $valores);
        return $datos;
    }

    public function getDatosUbicacion($idtmp) {
        $tmp = $this->getTMPUbicacionById($idtmp);
        $datos = "";
        foreach ($tmp as $actual) {
            $idtmp = $actual['idtmpubicacion'];
            $idubicacion = $actual['tmpidubicacion'];
            $nombre = $actual['tmpnombre'];
            $rfc = $actual['tmprfc'];
            $tipo = $actual['tmpidtipo'];
            $idestado = $actual['tmpidestado'];
            $codpostal = $actual['tmpcodpostal'];
            $distancia = $actual['tmpdistancia'];
            $fecha = $actual['tmpfecha'];
            $hora = $actual['tmphora'];
            $direccion = $actual['tmpdireccion'];
            $idmunicipio = $actual['tmpidmunicipio'];
            $datos = "$idtmp</tr>$idubicacion</tr>$nombre</tr>$rfc</tr>$tipo</tr>$idestado</tr>$codpostal</tr>$distancia</tr>$fecha</tr>$hora</tr>$direccion</tr>$idmunicipio";
            break;
        }
        return $datos;
    }

    public function actualizarUbicacion($t) {
        $actualizado = false;
        $consulta = "UPDATE tmpubicacion SET tmpidubicacion=:uid, tmpnombre=:nombre, tmprfc=:rfc, tmpidtipo=:idtipo, tmpidestado=:idestado, tmpnombre_estado=:nombre_estado, tmpcodpostal=:cp, tmpdistancia=:distancia, tmpfecha=:fecha, tmphora=:hora, tmpdireccion=:direccion, tmpidmunicipio=:idmunicipio, tmpnombre_municipio=:nombre_municipio WHERE idtmpubicacion=:id;";
        $valores = array("id" => $t->getTmpid(),
            "uid" => $t->getTmpidubicacion(),
            "nombre" => $t->getNombre(),
            "rfc" => $t->getRfc(),
            "idtipo" => $t->getTipo(),
            "idestado" => $t->getEstado(),
            "nombre_estado" => $t->getNombreEstado(),
            "cp" => $t->getCodpos(),
            "distancia" => $t->getDistancia(),
            "fecha" => $t->getFecha(),
            "hora" => $t->getHora(),
            "direccion" => $t->getDireccion(),
            "idmunicipio" => $t->getIdmunicipio(),
            "nombre_municipio" => $t->getNombreMunicipio(),
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function eliminarUbicacion($idtmp) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpubicacion` WHERE idtmpubicacion=:id;";
        $valores = array("id" => $idtmp);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    //-------------------------------------------OPERADOR
    public function agregarOperador($t) {
        $insertado = false;
        $consulta = "INSERT INTO `tmpoperador` VALUES (:id, :idop, :nombre, :rfc, :lic, :idestado, :nombreestado, :calle, :cp, :sid, :idmunicipio, :nombremunicipio);";
        $valores = array("id" => null,
            "rfc" => $t->getTmprfc(),
            "idop" => $t->getTmpidoperador(),
            "nombre" => $t->getTmpnombre(),
            "lic" => $t->getTmplic(),
            "idestado" => $t->getEstado(),
            "nombreestado" => $t->getNombreEstado(),
            "calle" => $t->getCalle(),
            "cp" => $t->getCodpostal(),
            "sid" => $t->getSid(),
            "idmunicipio" => $t->getIdmunicipio(),
            "nombremunicipio" => $t->getNombreMunicipio(),
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    
    private function getTMPOperador($sid) {
        $consultado = false;
        $consulta = "SELECT t.* FROM tmpoperador t WHERE sid=:sid ORDER BY tmprfc;";
        $consultas = new Consultas();
        $val = array("sid" => $sid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaOperador($sid, $uuid = "") {
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>NOMBRE</th>
                <th class='text-center'>RFC</th>
                <th class='text-center'>No. LICENCIA</th>
                <th class='text-center'>DIRECCIÓN</th>
                <th class='text-center col-md-1'>OPCIÓN</th></tr>
                </thead><tbody>";

        $disuuid = "";
        if ($uuid != "") {
            $disuuid = "disabled";
        }

        $productos = $this->getTMPOperador($sid);
        foreach ($productos as $actual) {
            $idtmp = $actual['idtmpoperador'];
            $nombre = $actual['tmpnombre'];
            $rfc = $actual['tmprfc'];
            $lic = $actual['tmplicencia'];
            $estado = $actual['tmpnombre_estado'];
            $calle = $actual['tmp_calle'];
            $cp = $actual['tmp_cp'];
            $municipio = $actual['tmpnombre_municipio'];

            $datos .= "
                    <tr>
                        <td>$nombre</td>
                        <td>$rfc</td>
                        <td>$lic</td>
                        <td>$calle $cp $municipio $estado</td>
                        <td class='text-center'><div class='dropdown'>
                        <button $disuuid class='button-list dropdown-toggle' title='Editar'  type='button' data-bs-toggle='dropdown'><span class='fas fa-edit'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarOperador($idtmp);'>Editar operador <span class='text-muted fas fa-edit small'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarOperador($idtmp); return false;'>Eliminar operador<span class='text-muted fas fa-times'></span></a></li>
                        </ul>
                        </div></td>
                    </tr>
                     ";
        }
        return $datos;
    }

    private function getTMPOperadorByID($idtmp) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpoperador WHERE idtmpoperador=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosOperador($idtmp) {
        $tmp = $this->getTMPOperadorByID($idtmp);
        $datos = "";
        foreach ($tmp as $actual) {
            $idtmp = $actual['idtmpoperador'];
            $idoperador = $actual['tmpidoperador'];
            $nombre = $actual['tmpnombre'];
            $rfc = $actual['tmprfc'];
            $lic = $actual['tmplicencia'];
            $idestado = $actual['tmp_idestado'];
            $calle = $actual['tmp_calle'];
            $cp = $actual['tmp_cp'];
            $idmunicipio = $actual['tmpidmunicipio'];

            $datos = "$idtmp</tr>$idoperador</tr>$nombre</tr>$rfc</tr>$lic</tr>$idestado</tr>$calle</tr>$cp</tr>$idmunicipio";
            break;
        }
        return $datos;
    }

    public function actualizarOperador($t) {
        $actualizado = false;
        $consulta = "UPDATE tmpoperador SET tmpidoperador=:oid, tmpnombre=:nombre, tmprfc=:rfc, tmplicencia=:lic, tmp_idestado=:estado, tmpnombre_estado=:nombre_estado, tmp_calle=:calle, tmp_cp=:cp, tmpidmunicipio=:idmunicipio, tmpnombre_municipio=:nombre_municipio, WHERE idtmpoperador=:id;";
        $valores = array("id" => $t->getTmpid(),
            "oid" => $t->getTmpidoperador(),
            "nombre" => $t->getTmpnombre(),
            "rfc" => $t->getTmprfc(),
            "lic" => $t->getTmplic(),
            "estado" => $t->getEstado(),
            "nombre_estado" => $t->getNombreEstado(),
            "calle" => $t->getCalle(),
            "cp" => $t->getCodpostal(),
            "idmunicipio" => $t->getIdmunicipio(),
            "nombre_municipio" => $t->getNombreMunicipio(),
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function eliminarOperador($idtmp) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpoperador` WHERE idtmpoperador=:id;";
        $valores = array("id" => $idtmp);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    //-------------------------------------------ELIMINAR TEMPORAL

    private function getTMP($sid) {
        $consultado = false;
        $consulta = "SELECT tmp.* FROM tmp WHERE tmp.session_id=:sid ORDER BY idtmp";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function restaurarInventario($idproducto, $cantidad) {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv+:cantidad where idproser=:idproducto;";
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function cancelar($sessionid) {
        $tmp = $this->getTMP($sessionid);
        foreach ($tmp as $actual) {
            $chinv = $actual['chinventariotmp'];
            if ($chinv == '1') {
                $idprod = $actual['id_productotmp'];
                $cantidad = $actual['cantidad_tmp'];
                $inv = $this->restaurarInventario($idprod, $cantidad);
            }
        }
        $eliminado = false;
        $consulta = "DELETE FROM `tmp` WHERE session_id=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        $this->deleteTMPCarta($sessionid);
        $this->deleteTMPUbicacion($sessionid);
        $this->deleteTMPOperador($sessionid);
        return $eliminado;
    }

    private function deleteTMPCarta($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpmercancia` WHERE sid=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function deleteTMPUbicacion($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpubicacion` WHERE sid=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function deleteTMPOperador($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpoperador` WHERE sid=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    //------------------------------------CARTA
    public function nuevaFacturaCarta($f) {
        $insertado = false;
        $validar = $this->validarFacturaCarta($f->getSessionid());
        if (!$validar) {
            $insertado = $this->insertarFacturaCarta($f);
        }
        return $insertado;
    }

    private function verificarProductos($sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmp WHERE session_id=:idsession;";
        $valores = array("idsession" => $sid);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function verificarMercancia($sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpmercancia WHERE sid=:sid;";
        $valores = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function verificarUbicaciones($sid, $tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpubicacion WHERE sid=:sid and tmpidtipo=:tipo;";
        $valores = array("sid" => $sid,
            "tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function verificarOperadores($sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpoperador WHERE sid=:sid;";
        $valores = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function validarFacturaCarta($sid) {
        $validar = false;
        $prod = 0;
        $productos = $this->verificarProductos($sid);
        foreach ($productos as $actual) {
            $prod ++;
        }
        if ($prod == 0) {
            $validar = true;
            echo "0No se han agregado productos a la factura.";
        }

        if (!$validar) {
            $prod = 0;
            $productos = $this->verificarMercancia($sid);
            foreach ($productos as $actual) {
                $prod ++;
            }
            if ($prod == 0) {
                $validar = true;
                echo "0No se ha agregado la mercancía a transportar.";
            }
        }

        if (!$validar) {
            $locat = 0;
            $locations = $this->verificarUbicaciones($sid, '1');
            foreach ($locations as $actual) {
                $locat ++;
            }
            if ($locat == 0) {
                $validar = true;
                echo "0Debe agregar por lo menos una ubicación de origen.";
            }
        }

        if (!$validar) {
            $locat = 0;
            $locations = $this->verificarUbicaciones($sid, '2');
            foreach ($locations as $actual) {
                $locat ++;
            }
            if ($locat == 0) {
                $validar = true;
                echo "0Debe agregar por lo menos una ubicación de destino.";
            }
        }

        if (!$validar) {
            $op = 0;
            $operadores = $this->verificarOperadores($sid);
            foreach ($operadores as $actual) {
                $op ++;
            }
            if ($op == 0) {
                $validar = true;
                echo "0Debe agregar por lo menos un operador.";
            }
        }

        return $validar;
    }

    private function genTag($sid) {
        $tag = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 8);
        $ranstr = date("ymdHis") . $_SESSION[sha1("idusuario")];
        $tag .= $ranstr . $sid;
        return $tag;
    }

    private function updateFolioConsecutivo($id) {
        $consultado = false;
        $consulta = "UPDATE folio SET consecutivo=(consecutivo+1) WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getFoliobyID($id) {
        $consultado = false;
        $consulta = "SELECT * FROM folio WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getFolio($id) {
        $datos = "";
        $folios = $this->getFoliobyID($id);
        foreach ($folios as $actual) {
            $serie = $actual['serie'];
            $letra = $actual['letra'];
            $consecutivo = $actual['consecutivo'];
            if ($consecutivo < 10) {
                $consecutivo = "000$consecutivo";
            } else if ($consecutivo < 100 && $consecutivo >= 10) {
                $consecutivo = "00$consecutivo";
            } else if ($consecutivo < 1000 && $consecutivo >= 100) {
                $consecutivo = "0$consecutivo";
            }
            $datos = "$serie</tr>$letra</tr>$consecutivo";
            $update = $this->updateFolioConsecutivo($id);
        }
        return $datos;
    }

    private function generateIdCCP() { //CAMBIOS A CARTA PORTE 3.0
        return sprintf('CCC%05x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), 
        mt_rand(0, 0xffff),
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
        // 48 bits for "node"
        mt_rand(0, 0xffff), 
        mt_rand(0, 0xffff), 
        mt_rand(0, 0xffff)
        );
    }

    private function generateQRIdCCP($textqr){
        $tempDir = '../temporal/tmp/';
        $codeContents = $textqr;
        $fileName = '005_file_'.md5($codeContents).'.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        
        $qrCode = new chillerlan\QRCode\QRCode;
        $qrCode->render($codeContents, $pngAbsoluteFilePath);
        
        $data = file_get_contents($pngAbsoluteFilePath);
        $base64 = base64_encode($data);
        unlink($pngAbsoluteFilePath); 
        return $base64;
    }
    
    private function insertarFacturaCarta($f) {
        $insertado = false;
        $hoy = date("Y-m-d");
        $tag = $this->genTag($f->getSessionid());

        $folios = $this->getFolio($f->getFolio());
        $Fdiv = explode("</tr>", $folios);
        $serie = $Fdiv[0];
        $letra = $Fdiv[1];
        $nfolio = $Fdiv[2];
        $idCCP = $this->generateIdCCP();
        $QrIdCCP = $this->generateQRIdCCP($idCCP);

        $consulta = "INSERT INTO `factura_carta` VALUES (:id, :fecha, :rfc, :rzsocial, :clvreg, :regimen, :cpemisor, :serie, :letra, :folio, :idcliente, :rfcreceptor, :rzreceptor, :dircliente, :cpreceptor, :regfiscalreceptor, :chfirmar, :cadena, :certSAT, :certCFDI, :uuid, :selloSAT, :sellocfdi, :fechatimbrado, :qrcode, :cfdistring, :cfdicancel, :status, :idmetodopago, :idformapago, :idmoneda, :tcambio, :iduso, :tipocomprobante, :periodo, :mes, :anho, :iddatosfacturacion, :cfdisrel, :subtotal, :subiva, :subret, :totdescuentos, :total, :tag, :pesomercancia, :pesovehicular, :pesobruto, :idccp, :qridccp);";
        $valores = array("id" => null,
            "fecha" => $hoy,
            "rfc" => '',
            "rzsocial" => '',
            "clvreg" => '',
            "regimen" => '',
            "cpemisor" => '',
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "idcliente" => $f->getIdcliente(),
            "rfcreceptor" => $f->getRfccliente(),
            "rzreceptor" => $f->getRzcliente(),
            "dircliente" => $f->getDircliente(),
            "cpreceptor" => $f->getCodpostal(),
            "regfiscalreceptor" => $f->getRegfiscalcliente(),
            "chfirmar" => $f->getChfirmar(),
            "cadena" => null,
            "certSAT" => null, //19
            "certCFDI" => null,
            "uuid" => null,
            "selloSAT" => null,
            "sellocfdi" => null,
            "fechatimbrado" => null,
            "qrcode" => null,
            "cfdistring" => null,
            "cfdicancel" => null,
            "status" => '2',
            "idmetodopago" => $f->getIdmetodopago(),
            "idformapago" => $f->getIdformapago(), //30
            "idmoneda" => $f->getIdmoneda(),
            "tcambio" => $f->getTcambio(),
            "iduso" => $f->getIdusocfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "periodo" => $f->getPeriodicidad(),
            "mes" => $f->getMesperiodo(),
            "anho" => $f->getAnoperiodo(),
            "iddatosfacturacion" => $f->getIddatosfacturacion(),
			"cfdisrel" => $f->getCFDISrel(), //falta
            "subtotal" => null,
            "subiva" => null,
            "subret" => null,
            "totdescuentos" => null,
            "total" => null,
            "tag" => $tag, //Faltan de aqui en adelante
            "pesomercancia" => $f->getPesoMercancia(),
            "pesovehicular" => $f->getPesoVehicular(),
            "pesobruto" => $f->getPesoBruto(),
            "idccp" => $idCCP,
            "qridccp" => $QrIdCCP
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        echo "Primera insercion " . $insertado;

        $idremolque1 = $f->getIdremolque1() ?: '0';
        $idremolque2 = $f->getIdremolque2() ?: '0';
        $idremolque3 = $f->getIdremolque3() ?: '0';

        $insertado2 = false;
        $consulta2 = "INSERT INTO `datos_carta` VALUES (:id, :fecha, :serie, :letra, :folio, :tipomov, :idvehiculo, :vehiculo, :numpermiso, :tipopermiso, :conftransporte, :modelo, :placa, :segurocivil, :polizaseguro, :seguroambiente, :polizaambiente, :idremolque1, :nmremolque1, :tiporemolque1, :placaremolque1, :idremolque2, :nmremolque2, :tiporemolque2, :placaremolque2, :idremolque3, :nmremolque3, :tiporemolque3, :placaremolque3, :observaciones, :tag);";
        $valores2 = array("id" => null,
            "fecha" => $hoy,
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "tipomov" => $f->getTipomovimiento(),
            "idvehiculo" => $f->getIdvehiculo(),
            "vehiculo" => $f->getNombrevehiculo(),
            "numpermiso" => $f->getNumpermiso(),
            "tipopermiso" => $f->getTipopermiso(),
            "conftransporte" => $f->getTipotransporte(),
            "modelo" => $f->getModelo(),
            "placa" => $f->getPlacavehiculo(),
            "segurocivil" => $f->getSegurorespcivil(),
            "polizaseguro" => $f->getPolizarespcivil(),
            "seguroambiente" => $f->getSeguroambiente(),
            "polizaambiente" => $f->getPolizaambiente(),
            "idremolque1" => $idremolque1,
            "nmremolque1" => $f->getNombreremolque1(),
            "tiporemolque1" => $f->getTiporemolque1(),
            "placaremolque1" => $f->getPlacaremolque1(),
            "idremolque2" => $idremolque2,
            "nmremolque2" => $f->getNombreremolque2(),
            "tiporemolque2" => $f->getTiporemolque2(),
            "placaremolque2" => $f->getPlacaremolque2(),
            "idremolque3" => $idremolque3,
            "nmremolque3" => $f->getNombreremolque3(),
            "tiporemolque3" => $f->getTiporemolque3(),
            "placaremolque3" => $f->getPlacaremolque3(),
            "observaciones" => $f->getObservaciones(),
            "tag" => $tag);
        $insertado2 = $this->consultas->execute($consulta2, $valores2);
        echo "Segunda insercion " . $insertado2;

        $detfactura = $this->detalleFactura($f->getSessionid(), $tag);
        echo "Tercera insercion " . $detfactura;

		if ($f->getCFDISrel() == '1') {
            $this->detalleCFDIS($f->getSessionid(), $tag);
        }
        $detcarta = $this->detalleCarta($f->getSessionid(), $tag);
        echo "Cuarta insercion " . $detcarta;

        return $insertado . "<tag>$tag<tag>";
    }

    private function detalleCFDIS($idsession, $tag) {
        $cfdi = $this->getTMPCFDIS($idsession);
        foreach ($cfdi as $actual) {
            $idtmpcfdi = $actual['idtmpcfdi'];
            $tiporel = $actual['tiporel'];
            $uuid = $actual['uuid'];

            $consulta2 = "INSERT INTO `cfdirelacionado` VALUES (:id, :tiporel, :uuid, :tag);";
            $valores2 = array("id" => null,
                "tiporel" => $tiporel,
                "uuid" => $uuid,
                "tag" => $tag);
            $insertado = $this->consultas->execute($consulta2, $valores2);
        }
        $cfdi = $this->deleteTMPCFDI($idsession);
        return $insertado;
    }

    private function deleteTMPCFDI($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpcfdi` WHERE sessionid=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function getImpuestos($tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto WHERE tipoimpuesto=:tipo";
        $valores = array("tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function checkArray($idsession, $idimpuesto) {
        $productos = $this->getTMP($idsession);
        $imptraslados = $this->getImpuestos($idimpuesto);
        $row = array();
        foreach ($imptraslados as $tactual) {
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $Timp = 0;

            foreach ($productos as $productoactual) {
                if ($idimpuesto == '1') {
                    $traslados = $productoactual['trasladotmp'];
                } else if ($idimpuesto == '2') {
                    $traslados = $productoactual['retenciontmp'];
                }
                if ($traslados != "") {
                    $div = explode("<impuesto>", $traslados);
                    foreach ($div as $d) {
                        $div2 = explode("-", $d);
                        if ($porcentaje == $div2[1] && $impuesto == $div2[2]) {
                            $Timp += $div2[0];
                        }
                    }
                }
            }
            if ($Timp > 0) {
                $row[] = bcdiv($Timp, '1', 2) . '-' . $porcentaje . '-' . $impuesto;
            }
        }

        $trasarray = implode("<impuesto>", $row);
        return $trasarray;
    }

    private function detalleFactura($idsession, $tag) {
        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $productos = $this->getTMP($idsession);
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmp'];
            $idproducto = $productoactual['id_productotmp'];
            $cantidad = $productoactual['cantidad_tmp'];
            $pventa = $productoactual['precio_tmp'];
            $nombre = $productoactual['tmpnombre'];
            $ptotal = $productoactual['totunitario_tmp'];
            $descuento = $productoactual['descuento_tmp'];
            $impdescuento = $productoactual['impdescuento_tmp'];
            $imptotal = $productoactual['imptotal_tmp'];
            $observaciones = $productoactual['observaciones_tmp'];
            $traslados = $productoactual['trasladotmp'];
            $retencion = $productoactual['retenciontmp'];
            $chinv = $productoactual['chinventariotmp'];
            $clvfiscal = $productoactual['clvfiscaltmp'];
            $clvunidad = $productoactual['clvunidadtmp'];

            $tras = 0;
            $divT = explode("<impuesto>", $traslados);
            foreach ($divT as $tactual) {
                $impuestos = $tactual;
                $div = explode("-", $impuestos);
                $tras += (bcdiv($div[0], '1', 2));
            }

            $ret = 0;
            $divR = explode("<impuesto>", $retencion);
            foreach ($divR as $ractual) {
                $impuestos = $ractual;
                $div = explode("-", $impuestos);
                $ret += (bcdiv($div[0], '1', 2));
            }

            $sumador_iva += bcdiv($tras, '1', 2);
            $sumador_ret += bcdiv($ret, '1', 2);
            $sumador_total += bcdiv($ptotal, '1', 2);
            $sumador_descuento += bcdiv($impdescuento, '1', 2);
            $consulta2 = "INSERT INTO `detallefcarta` VALUES (:id, :cantidad, :precio, :subtotal, :descuento, :impdescuento, :totdescuento, :traslados, :retenciones, :observaciones, :idproducto, :nombreprod, :chinv, :cfiscal, :cunidad, :tag);";
            $valores2 = array("id" => null,
                "cantidad" => $cantidad,
                "precio" => bcdiv($pventa, '1', 2),
                "subtotal" => bcdiv($ptotal, '1', 2),
                "descuento" => bcdiv($descuento, '1', 2),
                "impdescuento" => bcdiv($impdescuento, '1', 2),
                "totdescuento" => bcdiv($imptotal, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retencion,
                "observaciones" => $observaciones,
                "idproducto" => $idproducto,
                "nombreprod" => $nombre,
                "chinv" => $chinv,
                "cfiscal" => $clvfiscal,
                "cunidad" => $clvunidad,
                "tag" => $tag);

            $insertado = $this->consultas->execute($consulta2, $valores2);
        }
        $totaltraslados = $this->checkArray($idsession, '1');
        $totalretencion = $this->checkArray($idsession, '2');
        $borrar = "DELETE FROM `tmp` WHERE session_id=:id;";
        $valores3 = array("id" => $idsession);
        $eliminado = $this->consultas->execute($borrar, $valores3);

        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);
        $update = "UPDATE `factura_carta` SET subtotal=:subtotal, subtotaliva=:iva, subtotalret=:ret, totaldescuentos=:totdesc, totalfactura=:total WHERE tagfactura=:tag;";
        $valores4 = array("tag" => $tag,
            "subtotal" => bcdiv($sumador_total, '1', 2),
            "iva" => $totaltraslados,
            "ret" => $totalretencion,
            "totdesc" => bcdiv($sumador_descuento, '1', 2),
            "total" => bcdiv($total_factura, '1', 2));
        $insertado = $this->consultas->execute($update, $valores4);
        return $insertado;
    }

    private function detalleCarta($sid, $tag) {
        $insertado = false;
        $mercancia = $this->getTMPMercancia($sid);
        foreach ($mercancia as $actual) {
            $consulta = "INSERT INTO `detallemercancia` VALUES (:id, :clv, :descripcion, :cant, :unidad, :peso, :tag, :condicion, :peligro, :clvmaterial, :embalaje);";
            $val = array("id" => null,
                "clv" => $actual['tmpclave'],
                "descripcion" => $actual['tmpdescripcion'],
                "cant" => $actual['tmpcant'],
                "unidad" => $actual['tmpunidad'],
                "peso" => $actual['tmppeso'],
                "tag" => $tag,
                "condicion" => $actual['tmpcondpeligro'],
                "peligro" => $actual['tmppeligro'],
                "clvmaterial" => $actual['tmpclvmaterial'],
                "embalaje" => $actual['tmpembalaje']);
            $insertado .= $this->consultas->execute($consulta, $val);
        }

        $ubicacion = $this->getTMPUbicacion($sid);
        foreach ($ubicacion as $actual) {
            $consulta = "INSERT INTO `detalleubicacion` VALUES (:id, :ubid, :nombre, :rfc, :tipo, :idestado, :codp, :distancia, :fecha, :hora, :tag, :direccion, :idmunicipio);";
            $val = array("id" => null,
                "ubid" => $actual['tmpidubicacion'],
                "nombre" => $actual['tmpnombre'],
                "rfc" => $actual['tmprfc'],
                "tipo" => $actual['tmpidtipo'],
                "idestado" => $actual['tmpidestado'],
                "codp" => $actual['tmpcodpostal'],
                "distancia" => $actual['tmpdistancia'],
                "fecha" => $actual['tmpfecha'],
                "hora" => $actual['tmphora'],
                "tag" => $tag,
                "direccion" => $actual['tmpdireccion'],
                "idmunicipio" => $actual['tmpidmunicipio']);
            $insertado .= $this->consultas->execute($consulta, $val);
        }

        $operador = $this->getTMPOperador($sid);
        foreach ($operador as $actual) {
            $consulta = "INSERT INTO `detalleoperador` VALUES (:id, :oid, :nombre, :rfc, :lic, :estado, :calle, :cp, :tag, :idmunicipio);";
            $val = array("id" => null,
                "oid" => $actual['tmpidoperador'],
                "nombre" => $actual['tmpnombre'],
                "rfc" => $actual['tmprfc'],
                "lic" => $actual['tmplicencia'],
                "estado" => $actual['tmp_idestado'],
                "calle" => $actual['tmp_calle'],
                "cp" => $actual['tmp_cp'],
                "tag" => $tag,
                "idmunicipio" => $actual['tmpidmunicipio']);
            $insertado .= $this->consultas->execute($consulta, $val);
        }

        $evidencias = $this->getTMPEvidencias($sid);
        foreach ($evidencias as $evactual) {
            $consulta = "INSERT INTO `detalledoccarta` VALUES (:id, :orignm, :imgnm, :ext, :descripcion, :tag);";
            $val = array("id" => null,
                "orignm" => $evactual['tmpname'],
                "imgnm" => $evactual['imgtmp'],
                "ext" => $evactual['ext'],
                "descripcion" => $evactual['tmpdescripcion'],
                "tag" => $tag);
            $insertado .= $this->consultas->execute($consulta, $val);
            rename('../temporal/tmp/' . $evactual['imgtmp'], '../cartaporte/' . $evactual['imgtmp']);
        }

        return $insertado;
    }
    private function getTMPEvidencias($sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimg WHERE sessionid=:sid ORDER BY idtmpimg;";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT p.crearcarta, p.editarcarta, p.eliminarcarta FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $crear = $actual['crearcarta'];
            $editar = $actual['editarcarta'];
            $eliminar = $actual['eliminarcarta'];
            $datos .= "$editar</tr>$eliminar</tr>$crear";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(idfactura_carta) numrows FROM factura_carta dat INNER JOIN datos_facturacion d on (d.id_datos=dat.iddatosfacturacion) $condicion;";
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

    private function getSevicios($condicion) {
        $consultado = false;
        $consulta = "SELECT dat.idfactura_carta, dat.letra, dat.foliocarta, dat.fecha_creacion, dat.rzreceptor cliente, dat.status_pago, dat.uuid, dat.idcliente, dat.totalfactura, dat.factura_rzsocial emisor, dat.iddatosfacturacion, d.color, dat.id_moneda FROM factura_carta dat INNER JOIN datos_facturacion d on (d.id_datos=dat.iddatosfacturacion)  $condicion ;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNombreEmisor($fid) {
        $razonsocial = "";
        $sine = $this->getDatosFacturacionbyId($fid);
        foreach ($sine as $dactual) {
            $razonsocial = $dactual['razon_social'];
        }
        return $razonsocial;
    }

    public function translateMonth($m) {
        $meses = [
            '01' => 'Ene',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dic'
        ];
    
        return $meses[$m] ?? '';
    }    

    public function listaServiciosHistorial($pag, $REF, $numreg) {
        require_once '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th></th>
                <th class='text-center'>No.Folio </th>
                <th class='text-center'>Fecha de Creación </th>
                <th class='text-start'>Emisor</th>
                <th class='text-start'>Cliente</th>
                <th class='text-center'>Estado </th>
                <th class='text-center'>Timbre </th>
                <th class='text-center'>Total </th>
                <th class='text-center'>Moneda </th>
                <th class='text-center'>Opción</th>
            </tr>
        </thead>
        <tbody>";

        $condicion = "";
        if ($REF == "") {
            $condicion = "ORDER BY idfactura_carta DESC";
        } else {
            $condicion = "WHERE (concat(letra,foliocarta) LIKE '%$REF%') or (dat.rzreceptor LIKE '%$REF%') OR (dat.factura_rzsocial LIKE '%$REF%') ORDER BY idfactura_carta DESC";
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
        $listafactura = $this->getSevicios($con);
        $finales = 0;
        foreach ($listafactura as $listafacturaActual) {
            $idfactura = $listafacturaActual['idfactura_carta'];
            $folio = $listafacturaActual['letra'] . $listafacturaActual['foliocarta'];
            $fecha = $listafacturaActual['fecha_creacion'];
            $nombre_cliente = $listafacturaActual['cliente'];
            $estado = $listafacturaActual['status_pago'];
            $uuid = $listafacturaActual['uuid'];
            $total = $listafacturaActual['totalfactura'];
            $colorrow = $listafacturaActual['color'];
            $cmoneda = ($listafacturaActual['id_moneda'] == 1) ? 'MXN' : (($listafacturaActual['id_moneda'] == 2) ? 'USD' : (($listafacturaActual['id_moneda'] == 3) ? 'EUR' : ''));

            $timbre = "";
            $xml = "";

            if ($uuid != "") {
            	$emisor = $listafacturaActual['emisor'];
                $colorB = "#2AA010";
                $titbell = "Factura timbrada";
                $xml = "href='./com.sine.imprimir/imprimirxml.php?c=$idfactura&t=a' target='_blank'";
                $timbre = "data-bs-toggle='modal' data-bs-target='#modalcancelar' onclick='setCancelacion($idfactura);'";
                $tittimbre = "Cancelar Carta";
            } else {
            	$emisor = $this->getNombreEmisor($listafacturaActual['iddatosfacturacion']);
                $timbre = "onclick='timbrarCarta($idfactura);'";
                $xml = "href='./com.sine.imprimir/imprimirxml.php?c=$idfactura&t=a' target='_blank'";
                $tittimbre = "Timbrar carta";
                $colorB = "#ED495C";
                $titbell = "Factura sin timbrar";
            }

            switch ($estado) {
                case '1':
                    $estadoF = "Pagada";
                    $color = "#34A853";
                    $title = "Factura pagada";
                    $function = "onclick='tablaPagos($idfactura,$estado)'";
                    $modal = "data-toggle='modal' data-target='#pagosfactura'";
                    break;
                case '2':
                    $estadoF = "Pendiente";
                    $color = "#ED495C";
                    $title = "Pago pendiente";
                    $function = "onclick='registrarPago($idfactura)'";
                    $modal = "";
                    break;
                case '3':
                    $estadoF = "Cancelada";
                    $color = "#FBBC05";
                    $title = "Factura cancelada";
                    $function = "";
                    $modal = "";
                    $tittimbre = "Carta cancelada";
                    $timbre = "href='./com.sine.imprimir/imprimirxml.php?c=$idfactura&t=c' target='_blank'";
                    $xml = "href='./com.sine.imprimir/imprimirxml.php?c=$idfactura&t=a' target='_blank'";
                    $colorB = "#f0ad4e";
                    $titbell = "Factura cancelada";
                    break;
                case '4':
                    $estadoF = "Pago parcial";
                    $color = "#02E7EF";
                    $title = "Factura pagada parcialmente";
                    $function = "onclick='tablaPagos($idfactura,$estado)'";
                    $modal = "data-toggle='modal' data-target='#pagosfactura'";
                    break;
                default:
                    $estadoF = "Pendiente";
                    $color = "#ED495C";
                    $title = "Pago pendiente";
                    $function = "onclick='registrarPago($idfactura)'";
                    $modal = "";
                    break;
            }
            
            $divideF = explode("-", $fecha);
            $mes = $this->translateMonth($divideF[1]);
            $fecha = $divideF[2] . ' / ' . $mes;

            $datos .= "
                    <tr class='table-row'>
                        <td style='background-color: $colorrow;'></td>
                        <td class='text-center'>$folio</td>
                        <td class='text-center'>$fecha</td>
                        <td>$emisor</td>
                        <td>$nombre_cliente</td>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <a class='state-link fw-semibold' style='color: $color;' $modal $function><span>$estadoF</span></a>
                                <span class='tiptext' style='color: $color;'>$title</span>
                            </div>
                        </td>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <span style='color: $colorB;' class='fas fa-bell'></span>
                                <span class='tiptext fw-semibold' style='color: $color;'>$titbell</span>
                            </div>
                        </td>
                        <td>$" . number_format($total, 2, '.', ',') . "</td>
                        <td class='text-center'>$cmoneda</td>
                        <td align='center'><div class='dropdown dropend'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";

            if ($div[0] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarCarta($idfactura);'>Editar carta <span class='text-muted fas fa-edit small'></span></a></li>";
            }

            if ($div[1] == '1' && $uuid == "") {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarCarta($idfactura);'>Eliminar carta <span class='text-muted fas fa-times'></span></a></li>";
            }

            if ($div[2] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='copiarCarta($idfactura);'>Copiar carta <span class='text-muted fas fa-copy'></span></a></li>";
            }

            $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"imprimirCarta($idfactura);\">Ver carta porte <span class='text-muted fas fa-eye'></span></a></li>";

            if($uuid != ""){
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' $xml>Ver XML <span class='text-muted fas fa-download'></span></a></li>";
            }

            $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' $timbre>$tittimbre <span class='text-muted fas fa-bell'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-toggle='modal' data-target='#modal-evidencia' onclick=\"verEvidencias($idfactura);\">Ver evidencias <span class='text-muted fas fa-save'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-toggle='modal' data-target='#enviarmail' onclick='showCorreosCarta($idfactura);'>Enviar <span class='text-muted fas fa-envelope'></span></a></li>";

            if ($uuid != "") {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-toggle='modal' data-target='#modal-stcfdi' onclick='statusCancelacionCarta($idfactura);'>Comprobar estado de la factura <span class='glyphicon glyphicon-ok-sign'></span></a></li>";
            }
            $datos .= "</ul>
                        </div></td>
                    </tr>
                     ";
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarCarta";
        if ($finales == 0) {
            $datos .= "<tr><td class='text-center' colspan='11'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='11'>Mostrando $inicios al $finales de $numrows registros " . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    //------------------------------------------------- PAGOS
    private function getFacturaPagoById($idfactura) {
        $consultado = false;
        $consulta = "SELECT f.*, df.nombre_contribuyente, p.*, m.c_moneda, m.descripcion_moneda FROM factura_carta f INNER JOIN datos_facturacion df ON (f.iddatosfacturacion=df.id_datos) INNER JOIN catalogo_pago p ON (f.id_forma_pago=p.idcatalogo_pago) INNER JOIN catalogo_moneda m ON (m.idcatalogo_moneda=f.id_moneda) WHERE f.idfactura_carta=:id;";
        $val = array("id" => $idfactura);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosFacPago($idfactura) {
        $factura = $this->getFacturaPagoById($idfactura);
        $datos = "";
        foreach ($factura as $facturaactual) {
            $idfactura = $facturaactual['idfactura_carta'];
            $folio = $facturaactual['letra'] . $facturaactual['foliocarta'];
            $idcliente = $facturaactual['idcliente'];
            $nombrecliente = $this->getNombreCliente($idcliente);
            $rfcreceptor = $facturaactual['rfcreceptor'];
            $rzreceptor = $facturaactual['rzreceptor'];
            $cpreceptor = $facturaactual['cpreceptor'];
            $regfiscalreceptor = $facturaactual['regfiscalreceptor'];
            $iddatosfacturacion = $facturaactual['iddatosfacturacion'];
            $nombrecontribuyente = $facturaactual['nombre_contribuyente'];
            $idformapago = $facturaactual['id_forma_pago'];
            $c_pago = $facturaactual['c_pago'];
            $forma_pago = $facturaactual['descripcion_pago'];
            $idmoneda = $facturaactual['id_moneda'];
            $tcambio = $facturaactual['tcambio'];
            $c_moneda = $facturaactual['c_moneda'];
            $dmoneda = $facturaactual['descripcion_moneda'];

            $datos = "$idfactura</tr>$folio</tr>$idcliente</tr>$nombrecliente</tr>$rfcreceptor</tr>$rzreceptor</tr>$cpreceptor</tr>$regfiscalreceptor</tr>$iddatosfacturacion</tr>$nombrecontribuyente</tr>$idformapago</tr>$c_pago</tr>$forma_pago</tr>$idmoneda</tr>$tcambio</tr>$c_moneda</tr>$dmoneda";
            break;
        }
        return $datos;
    }

}