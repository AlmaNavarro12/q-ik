<?php 
require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.controlador/ControladorSat.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/CartaPorte.php';
require_once '../com.sine.modelo/SendMail.php';
require_once '../com.sine.modelo/TMP.php';

use SWServices\Toolkit\SignService as Sellar;
use SWServices\Stamp\StampService as StampService;
use SWServices\Cancelation\CancelationService as CancelationService;
use SWServices\SatQuery\SatQueryService as consultaCfdiSAT;
use PHPMailer\PHPMailer\PHPMailer;
use chillerlan\QRCode\QRCode;

date_default_timezone_set("America/Mexico_City");

class ControladorCarta {

    private $consultas;
    private $controladorSat;

    function __construct() {
        $this->consultas = new Consultas();
        $this->controladorSat = new ControladorSat();
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

    public function checkStatusRemolque($placa) {
        $check = '0';
        $datos = $this->checkRemolqueAux($placa);
        foreach ($datos as $actual) {
            $check = $actual['status'];
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

    public function checkStatusVehiculo($placa) {
        $status = '0';
        $datos = $this->checkVehiculoAux($placa);
        foreach ($datos as $actual) {
            $status = $actual['status'];
        }
        return $status;
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

    public function checkStatusOperador($rfc) {
        $check = '0';
        $datos = $this->checkOperadorAux($rfc);
        foreach ($datos as $actual) {
            $check = $actual['opstatus'];
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
                        <td class='text-center'>$rfc</td>
                        <td class='text-center'>$lic</td>
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
        $consulta = "UPDATE tmpoperador SET tmpidoperador=:oid, tmpnombre=:nombre, tmprfc=:rfc, tmplicencia=:lic, tmp_idestado=:estado, tmpnombre_estado=:nombre_estado, tmp_calle=:calle, tmp_cp=:cp, tmpidmunicipio=:idmunicipio, tmpnombre_municipio=:nombre_municipio WHERE idtmpoperador=:id;";
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
    
    private function getIdccpQR($tag){
        $idccp = "";
        $qridccp = "";
        $query = "SELECT idccp, qridccp FROM factura_carta where tagfactura = :tag";
        $val = array("tag" => $tag);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $idccp = $rs['idccp'];
            $qridccp = $rs['qridccp'];
        }

        if($idccp == "" && $qridccp == ""){
            $idccp = $this->generateIdCCP();
            $qridccp = $this->generateQRIdCCP($idccp);
        }

        return "$idccp</tr>$qridccp";

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

        $consulta = "INSERT INTO `factura_carta` VALUES (:id, :fecha, :rfc, :rzsocial, :clvreg, :regimen, :cpemisor, :serie, :letra, :folio, :idcliente, :rfcreceptor, :rzreceptor, :dircliente, :cpreceptor, :regfiscalreceptor, :chfirmar, :cadena, :certSAT, :certCFDI, :uuid, :selloSAT, :sellocfdi, :fechatimbrado, :qrcode, :cfdistring, :cfdicancel, :status, :idmetodopago, :nombre_metodo, :idformapago, :nombre_forma, :idmoneda, :nombre_moneda, :tcambio, :iduso, :nombre_cfdi, :tipocomprobante, :nombre_comprobante, :periodo, :mes, :anho, :iddatosfacturacion, :cfdisrel, :subtotal, :subiva, :subret, :totdescuentos, :total, :tag, :pesomercancia, :pesovehicular, :pesobruto, :idccp, :qridccp);";
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
            "nombre_metodo" => $f->getNombreMetodo(),
            "idformapago" => $f->getIdformapago(), //30
            "nombre_forma" => $f->getNombreForma(),
            "idmoneda" => $f->getIdmoneda(),
            "nombre_moneda" => $f->getNombreMoneda(),
            "tcambio" => $f->getTcambio(),
            "iduso" => $f->getIdusocfdi(),
            "nombre_cfdi" => $f->getNombreCfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "nombre_comprobante" => $f->getNombreComprobante(),
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
        $detfactura = $this->detalleFactura($f->getSessionid(), $tag);

		if ($f->getCFDISrel() == '1') {
            $this->detalleCFDIS($f->getSessionid(), $tag);
        }
        $detcarta = $this->detalleCarta($f->getSessionid(), $tag);
        return $insertado . "<tag>$tag<tag>";
    }

    public function modificarCarta($f) {
        $insertado = false;
        $validar = $this->validarFacturaCarta($f->getSessionid());
        if (!$validar) {
            $insertado = $this->actualizarCarta($f);
        }
        return $insertado;
    }

    private function actualizarCarta($f) {
        $actualizado = false;
        $updfolio = "";
        $serie = "";
        $letra = "";
        $nfolio = "";
        if ($f->getFolio() != '0') {
            $updfolio = "serie=:serie, letra=:letra, foliocarta=:folio,";
            $folios = $this->getFolio($f->getFolio());
            $Fdiv = explode("</tr>", $folios);
            $serie = $Fdiv[0];
            $letra = $Fdiv[1];
            $nfolio = $Fdiv[2];
        }

        $Fdividccp = $this->getIdccpQR($f->getTag());
        $dividccp = explode('</tr>', $Fdividccp);
        $idccp = $dividccp[0];
        $qridccp = $dividccp[1];

        $consulta = "UPDATE factura_carta SET $updfolio idcliente=:idcliente, rfcreceptor=:rfcreceptor, rzreceptor=:rzreceptor, dircliente=:dircliente, cpreceptor=:cpreceptor, regfiscalreceptor=:regfiscalreceptor, chfirmar=:chfirmar, id_metodo_pago=:idmetodopago, nombre_metodo_pago=:nombre_metodo, id_forma_pago=:idformapago, nombre_forma_pago =:nombre_forma, id_moneda=:idmoneda, nombre_moneda=:nombre_moneda, tcambio=:tcambio, id_uso_cfdi=:iduso, nombre_uso_cfdi=:nombre_cfdi, id_tipo_comprobante=:tipocomprobante, nombre_comprobante=:nombre_comprobante, periodoglobal=:periodoglobal, mesperiodo=:mesperiodo, anhoperiodo=:anhoperiodo, iddatosfacturacion=:iddatos, cfdisrel=:cfdisrel, pesomercancia = :peso_mercancia, pesovehicular = :peso_vehicular, pesobruto = :peso_bruto, idccp = :idccp, qridccp = :qridccp WHERE tagfactura=:tag;";
        $valores = array("tag" => $f->getTag(),
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
            "idmetodopago" => $f->getIdmetodopago(),
            "nombre_metodo" => $f->getNombreMetodo(),
            "idformapago" => $f->getIdformapago(),
            "nombre_forma" => $f->getNombreForma(),
            "idmoneda" => $f->getIdmoneda(),
            "nombre_moneda" => $f->getNombreMoneda(),
            "tcambio" => $f->getTcambio(),
            "iduso" => $f->getIdusocfdi(),
            "nombre_cfdi" => $f->getNombreCfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "nombre_comprobante" => $f->getNombreComprobante(),
            "periodoglobal" => $f->getPeriodicidad(),
            "mesperiodo" => $f->getMesperiodo(),
            "anhoperiodo" => $f->getAnoperiodo(),
            "iddatos" => $f->getIddatosfacturacion(),
			"cfdisrel" => $f->getCFDISrel(),
            "peso_mercancia" => $f->getPesoMercancia(),
            "peso_vehicular" => $f->getPesoVehicular(),
            "peso_bruto" => $f->getPesoBruto(),            
            "idccp" => $idccp,
            "qridccp" => $qridccp
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        
        $updfolio2 = "";
        if ($f->getFolio() != '0') {
            $updfolio2 = "carta_serie=:serie, carta_letra=:letra, cartafolio=:folio,";
        }

        if ($f->getUuid() == "") {
            $actualizar2 = false;
            $consulta2 = "UPDATE `datos_carta` SET $updfolio2 tipomovimiento=:tipomov, carta_idvehiculo=:idvehiculo, nombrevehiculo=:vehiculo, carta_numpermiso=:numpermiso, carta_tipopermiso=:tipopermiso, carta_conftransporte=:conftransporte, carta_anhomod=:modelo, carta_placa=:placa, carta_segurocivil=:segurocivil, carta_polizaseguro=:polizaseguro, carta_idremolque1=:idremolque1, carta_nmremolque1=:nmremolque1, carta_tiporemolque1=:tiporemolque1, carta_placaremolque1=:placaremolque1, carta_idremolque2=:idremolque2, carta_nmremolque2=:nmremolque2, carta_tiporemolque2=:tiporemolque2, carta_placaremolque2=:placaremolque2, carta_idremolque3=:idremolque3, carta_nmremolque3=:nmremolque3, carta_tiporemolque3=:tiporemolque3, carta_placaremolque3=:placaremolque3, carta_seguroambiente=:seguroambiente, carta_polizaambiente=:polizaambiente, carta_observaciones=:observaciones WHERE tagcarta=:tag;";
            $valores2 = array("tag" => $f->getTag(),
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
                "idremolque1" => $f->getIdremolque1(),
                "nmremolque1" => $f->getNombreremolque1(),
                "tiporemolque1" => $f->getTiporemolque1(),
                "placaremolque1" => $f->getPlacaremolque1(),
                "idremolque2" => $f->getIdremolque2(),
                "nmremolque2" => $f->getNombreremolque2(),
                "tiporemolque2" => $f->getTiporemolque2(),
                "placaremolque2" => $f->getPlacaremolque2(),
                "idremolque3" => $f->getIdremolque3(),
                "nmremolque3" => $f->getNombreremolque3(),
                "tiporemolque3" => $f->getTiporemolque3(),
                "placaremolque3" => $f->getPlacaremolque3(),
                "seguroambiente" => $f->getSeguroambiente(),
                "polizaambiente" => $f->getPolizaambiente(),
                "observaciones" => $f->getObservaciones());
            $actualizar2 = $this->consultas->execute($consulta2, $valores2);
            $this->actualizardetalleCarta($f->getSessionid(), $f->getTag());
        }
    	$this->actualizarDetalle($f->getSessionid(), $f->getTag());
		$this->actualizarCFDIS($f->getSessionid(), $f->getTag());
        return $actualizado;
    }

    public function actualizarDetalle($idsession, $tag) {
        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $this->eliminarFacturaAux($tag);
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

    private function actualizarCFDIS($idsession, $tag) {
        $insertado = false;
        $this->eliminarCFDIAux($tag);

        $cfdi = $this->getTMPCFDIS($idsession);
        foreach ($cfdi as $actual) {
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

    private function eliminarCFDIAux($tag) {
        $eliminado = false;
        $borrar = "DELETE FROM `cfdirelacionado` WHERE cfditag=:tag;";
        $borrarvalores = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarvalores);
        return $eliminado;
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

        $trasarray = join("<impuesto>", $row);
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

    private function insertarDetalleMercancia($sid, $tag) {
        $insertado = false;
        $mercancia = $this->getTMPMercancia($sid);
        
        foreach ($mercancia as $actual) {
            $consulta = "INSERT INTO `detallemercancia` VALUES (:id, :clv, :descripcion, :cant, :unidad, :peso, :tag, :condicion, :peligro, :clvmaterial, :embalaje);";
            $val = array(
                "id" => null,
                "clv" => $actual['tmpclave'],
                "descripcion" => $actual['tmpdescripcion'],
                "cant" => $actual['tmpcant'],
                "unidad" => $actual['tmpunidad'],
                "peso" => $actual['tmppeso'],
                "tag" => $tag,
                "condicion" => $actual['tmpcondpeligro'],
                "peligro" => $actual['tmppeligro'],
                "clvmaterial" => $actual['tmpclvmaterial'],
                "embalaje" => $actual['tmpembalaje']
            );
            $insertado = $this->consultas->execute($consulta, $val);
        }
        return $insertado;
    }
    
    private function insertarDetalleUbicacion($sid, $tag) {
        $insertado = false;
        $ubicacion = $this->getTMPUbicacion($sid);
        
        foreach ($ubicacion as $actual) {
            $consulta = "INSERT INTO `detalleubicacion` VALUES (:id, :idub, :nombre, :rfc, :tipo, :idestado, :nombre_estado, :codp, :distancia, :fecha, :hora, :tag, :direccion, :idmunicipio, :nombre_municipio);";
            $val = array(
                "id" => null,
                "idub" => $actual['tmpidubicacion'],
                "nombre" => $actual['tmpnombre'],
                "rfc" => $actual['tmprfc'],
                "tipo" => $actual['tmpidtipo'],
                "idestado" => $actual['tmpidestado'],
                "nombre_estado" => $actual['tmpnombre_estado'],
                "codp" => $actual['tmpcodpostal'],
                "distancia" => $actual['tmpdistancia'],
                "fecha" => $actual['tmpfecha'],
                "hora" => $actual['tmphora'],
                "tag" => $tag,
                "direccion" => $actual['tmpdireccion'],
                "idmunicipio" => $actual['tmpidmunicipio'],
                "nombre_municipio" => $actual['tmpnombre_municipio'],
            );
            $insertado = $this->consultas->execute($consulta, $val);
        }
        
        return $insertado;
    }

    private function insertarDetalleOperador($sid, $tag) {
        $insertado = false;
        $operador = $this->getTMPOperador($sid);
        
        foreach ($operador as $actual) {
            $consulta = "INSERT INTO `detalleoperador` VALUES (:id, :oid, :nombre, :rfc, :lic, :estado, :nombre_estado, :calle, :cp, :tag, :idmunicipio, :nombre_municipio);";
            $val = array(
                "id" => null,
                "oid" => $actual['tmpidoperador'],
                "nombre" => $actual['tmpnombre'],
                "rfc" => $actual['tmprfc'],
                "lic" => $actual['tmplicencia'],
                "estado" => $actual['tmp_idestado'],
                "nombre_estado" => $actual['tmpnombre_estado'],
                "calle" => $actual['tmp_calle'],
                "cp" => $actual['tmp_cp'],
                "tag" => $tag,
                "idmunicipio" => $actual['tmpidmunicipio'],
                "nombre_municipio" => $actual['tmpnombre_municipio'],
            );
            $insertado = $this->consultas->execute($consulta, $val);
        }
        
        return $insertado;
    }
    
    private function insertarDetalleEvidencias($sid, $tag) {
        $insertado = false;
        $evidencias = $this->getTMPEvidencias($sid);
        
        foreach ($evidencias as $evactual) {
            $consulta = "INSERT INTO `detalledoccarta` VALUES (:id, :orignm, :imgnm, :ext, :descripcion, :tag);";
            $val = array(
                "id" => null,
                "orignm" => $evactual['tmpname'],
                "imgnm" => $evactual['imgtmp'],
                "ext" => $evactual['ext'],
                "descripcion" => $evactual['tmpdescripcion'],
                "tag" => $tag
            );
            $insertado .= $this->consultas->execute($consulta, $val);
            rename('../temporal/tmp/' . $evactual['imgtmp'], '../img/cartaporte/' . $evactual['imgtmp']);
        }
        
        return $insertado;
    }
    
    private function detalleCarta($sid, $tag) {
        $insertado = false;
        $insertado .= $this->insertarDetalleMercancia($sid, $tag);
        $insertado .= $this->insertarDetalleUbicacion($sid, $tag);
        $insertado .= $this->insertarDetalleOperador($sid, $tag);
        $insertado .= $this->insertarDetalleEvidencias($sid, $tag);
        return $insertado;
    }

    private function actualizardetalleCarta($sid, $tag) {
        $insertado = false;

        $borrar = "DELETE FROM `detallemercancia` WHERE tagmercancia=:tag;";
        $borrarvalores = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarvalores);

        $insertado .= $this->insertarDetalleMercancia($sid, $tag);

        $borrar = "DELETE FROM `detalleubicacion` WHERE tagubicacion=:tag;";
        $borrarval = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarval);

        $insertado .= $this->insertarDetalleUbicacion($sid, $tag);

        $borrar = "DELETE FROM `detalleoperador` WHERE tagoperador=:tag;";
        $borrarval = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarval);

        $insertado .= $this->insertarDetalleOperador($sid, $tag);
        

        $borrar = "DELETE FROM `detalledoccarta` WHERE tagimg=:tag;";
        $borrarval = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarval);

        $insertado .= $this->insertarDetalleEvidencias($sid, $tag);
        return $insertado;
    }
    
    private function checkCartaAux($tag) {
        $existe = false;
        $detalle = $this->getDetalle($tag);
        foreach ($detalle as $detactual) {
            $existe = true;
            break;
        }
        return $existe;
    }

    public function checkCarta($tag) {
        $check = $this->checkCartaAux($tag);
        if ($check) {
            $datos = $this->nuevosRegistros($tag);
        }
        return $datos;
    }

    private function nuevosRegistros($tag){
        $vehiculo = false;
        $vehiculo = $this->nuevosRegistrosTransporte($tag);

        $remolque1 = false;
        $remolque1 = $this->nuevosRegistrosRemolque($tag, '1');

        $remolque2 = false;
        $remolque2 = $this->nuevosRegistrosRemolque($tag, '2');

        $remolque3 = false;
        $remolque3 = $this->nuevosRegistrosRemolque($tag, '3');

        $operador = false;
        $operador = $this->nuevosRegistrosOperador($tag);
        return "$vehiculo</tr>$remolque1</tr>$remolque2</tr>$remolque3</tr>$operador";
    }

    private function nuevosRegistrosTransporte($tag){
        $vehiculo = "";
        $datoscarta = $this->getDatosCarta($tag);
        foreach ($datoscarta as $datactual) {
            $idvehiculo = $datactual['carta_idvehiculo'];
            $placa = $datactual['carta_placa'];
            $nombre = $datactual['nombrevehiculo'];
            $numpermiso = $datactual['carta_numpermiso'];
            $tipopermiso = $datactual['carta_tipopermiso'];
            $tipotransporte = $datactual['carta_conftransporte'];
            $anhomod = $datactual['carta_anhomod'];
            $segurocivil = $datactual['carta_segurocivil'];
            $polizaseguro = $datactual['carta_polizaseguro'];
            $nombre = ($nombre == '') ? $placa : $nombre;
    
            $checkv = $this->checkVehiculo($placa); //9 
            $checks = $this->checkStatusVehiculo($placa); //1
    
            if ($idvehiculo == '0') {
                if ($checkv == '0') {
                    $consulta = "INSERT INTO `transporte` VALUES (:id, :nombre, :numpermiso, :tipopermiso, :conftransporte, :anho, :placa, :segurorc, :polizarc, :seguroma, :polizama, :segurocg, :polizacg, :st);";
                    $val = array(
                        "id" => null,
                        "nombre" => $nombre,
                        "numpermiso" => $numpermiso,
                        "tipopermiso" => $tipopermiso,
                        "conftransporte" => $tipotransporte,
                        "anho" => $anhomod,
                        "placa" => $placa,
                        "segurorc" => $segurocivil,
                        "polizarc" => $polizaseguro,
                        "seguroma" => '',
                        "polizama" => '',
                        "segurocg" => '',
                        "polizacg" => '',
                        "st" => '1'
                    );
                    $vehiculo = $this->consultas->execute($consulta, $val);
                } else if($checkv != "0" && $checks == "1") {
                    return $vehiculo = "Ya existe un registro de vehículo con el número de placas $placa.";
                } else if($checkv != "0" && $checks == "0"){
                    $consulta = "UPDATE `transporte` SET nombrevehiculo=:nombre, numeropermiso=:numpermiso, tipopermiso=:tipopermiso, conftransporte=:conftransporte, anhomodelo=:anho, placavehiculo=:placa, seguroCivil=:segurorc, polizaCivil=:polizarc, seguroAmbiente=:seguroma, polizaAmbiente=:polizama, seguroCarga=:segurocg, polizaCarga=:polizacg, status='1' WHERE placavehiculo=:placa;";
                    $val = array(
                        "nombre" => $nombre,
                        "numpermiso" => $numpermiso,
                        "tipopermiso" => $tipopermiso,
                        "conftransporte" => $tipotransporte,
                        "anho" => $anhomod,
                        "placa" => $placa,
                        "segurorc" => $segurocivil,
                        "polizarc" => $polizaseguro,
                        "seguroma" => '',
                        "polizama" => '',
                        "segurocg" => '',
                        "polizacg" => ''
                    );
                    $vehiculo = $this->consultas->execute($consulta, $val);
                }
            } else {
                if($checkv != "0" && $checks == "1"){
                    return $vehiculo = "Ya existe un registro de vehículo con el número de placas $placa.";
                } else if($checkv != "0" && $checks == "0"){
                    $consulta = "UPDATE `transporte` SET nombrevehiculo=:nombre, numeropermiso=:numpermiso, tipopermiso=:tipopermiso, conftransporte=:conftransporte, anhomodelo=:anho, placavehiculo=:placa, seguroCivil=:segurorc, polizaCivil=:polizarc, seguroAmbiente=:seguroma, polizaAmbiente=:polizama, seguroCarga=:segurocg, polizaCarga=:polizacg, status='1' WHERE placavehiculo=:placa;";
                    $val = array(
                        "nombre" => $nombre,
                        "numpermiso" => $numpermiso,
                        "tipopermiso" => $tipopermiso,
                        "conftransporte" => $tipotransporte,
                        "anho" => $anhomod,
                        "placa" => $placa,
                        "segurorc" => $segurocivil,
                        "polizarc" => $polizaseguro,
                        "seguroma" => '',
                        "polizama" => '',
                        "segurocg" => '',
                        "polizacg" => ''
                    );
                    $vehiculo = $this->consultas->execute($consulta, $val);
                }
            }
        }
        return $vehiculo;
    }
    
    private function nuevosRegistrosOperador($tag){
        $operador = false;

        $operadores = $this->getOperadores($tag);
        foreach ($operadores as $actual) {
            $idop = $actual['operador_id'];
            $rfc = $actual["operador_rfc"];
            $nombreop = $actual['operador_nombre'];
            $lic = $actual['operador_numlic'];
            $idestado = $actual["operador_idestado"];
            $nombre_estado = $actual["nombre_estado"];
            $calle = $actual["operador_calle"];
            $cp = $actual['operador_cp'];
            
            $checkop = $this->checkOperador($rfc);
            $checkops = $this->checkStatusOperador($rfc);

            if ($nombreop == '') {
                $nmop = $rfc;
                $apaternoop = '';
                $amaternoop = "";
            } else {
                $divnm = explode(" ", $nombreop);
                $nmop = $divnm[0];
                if (isset($divnm[3])) {
                    $apaternoop = $divnm[2];
                    $amaternoop = $divnm[3];
                } else {
                    $apaternoop = $divnm[1] ?? '';
                    $amaternoop = $divnm[2] ?? '';
                }
            }

            if ($idop == '0') {
                if ($checkop == '0') {
                    $consulta = "INSERT INTO `operador` VALUES (:id, :nombre, :apaterno, :amaterno, :licencia, :rfc, :empresa, :idestado, :nombre_estado, :idmunicipio, :nombre_municipio, :calle, :cp, :st);";
                    $valores = array(
                        "id" => null,
                        "nombre" => $nmop,
                        "apaterno" => $apaternoop,
                        "amaterno" => $amaternoop,
                        "licencia" => $lic,
                        "rfc" => $rfc,
                        "empresa" => '',
                        "idestado" => $idestado,
                        "nombre_estado" => $nombre_estado,
                        "idmunicipio" => '0',
                        "nombre_municipio" => '',
                        "calle" => $calle,
                        "cp" => $cp,
                        "st" => '1'
                    );
                    $operador = $this->consultas->execute($consulta, $valores);
                } else if($checkop != "0" && $checkops == "1"){
                    $operador = "Ya existe un registro de operador con el RFC $rfc.";
                } else if($checkop != "0" && $checkops == "0"){
                    $consulta = "UPDATE `operador` SET nombreoperador=:nombre, apaternooperador=:apaterno, amaternooperador=:amaterno, numlicencia=:licencia, rfcoperador=:rfc, empresa=:empresa, operador_idestado=:idestado, nombre_estado=:nombre_estado, operador_idmunicipio=:idmunicipio, nombre_municipio=:nombre_municipio, calle=:calle, cpoperador=:cp, opstatus=:st WHERE rfcoperador=:rfc;";
                    $valores = array(
                        "nombre" => $nmop,
                        "apaterno" => $apaternoop,
                        "amaterno" => $amaternoop,
                        "licencia" => $lic,
                        "rfc" => $rfc, 
                        "empresa" => '',
                        "idestado" => $idestado,
                        "nombre_estado" => $nombre_estado,
                        "idmunicipio" => '0',
                        "nombre_municipio" => '',
                        "calle" => $calle,
                        "cp" => $cp,
                        "st" => '1'
                    );
                    $operador = $this->consultas->execute($consulta, $valores);
                }
            } else {
                if($checkop != "0" && $checkops ="1"){
                    $operador = "Ya existe un registro de operador con el RFC $rfc.";
                } else if($checkop != "0" && $checkops == "0"){
                    $consulta = "UPDATE `operador` SET nombreoperador=:nombre, apaternooperador=:apaterno, amaternooperador=:amaterno, numlicencia=:licencia, rfcoperador=:rfc, empresa=:empresa, operador_idestado=:idestado, nombre_estado=:nombre_estado, operador_idmunicipio=:idmunicipio, nombre_municipio=:nombre_municipio, calle=:calle, cpoperador=:cp, opstatus=:st WHERE rfcoperador=:rfc;";
                    $valores = array(
                        "nombre" => $nmop,
                        "apaterno" => $apaternoop,
                        "amaterno" => $amaternoop,
                        "licencia" => $lic,
                        "rfc" => $rfc, 
                        "empresa" => '',
                        "idestado" => $idestado,
                        "nombre_estado" => $nombre_estado,
                        "idmunicipio" => '0',
                        "nombre_municipio" => '',
                        "calle" => $calle,
                        "cp" => $cp,
                        "st" => '1'
                    );
                    $operador = $this->consultas->execute($consulta, $valores);
                }
            }
        }
        return $operador;
    }

    private function nuevosRegistrosRemolque($tag, $num){
        $datoscarta = $this->getDatosCarta($tag);
        foreach ($datoscarta as $datactual) {
            $idremolque = $datactual["carta_idremolque$num"];
            $placaremolque = $datactual["carta_placaremolque$num"];
            $nmremolque = $datactual["carta_nmremolque$num"];
            $tiporemolque = $datactual["carta_tiporemolque$num"];

            $remolque = $this->insertarNuevoRemolque($idremolque, $placaremolque, $nmremolque, $tiporemolque, $num);
        }
        return $remolque;
    }

    private function insertarNuevoRemolque($idremolque, $placaremolque, $nmremolque, $tiporemolque, $numero) {
        $remolque = "";
        $checkrem = $this->checkRemolque($placaremolque);
        $checkrems = $this->checkStatusRemolque($placaremolque);

        if ($nmremolque == '') {
            $nmremolque = $placaremolque;
        }
    
        if ($idremolque == '0') {
            if ($checkrem == '0' && $placaremolque != "") {
                $consulta = "INSERT INTO `remolque` VALUES (:id, :nombre, :tipo, :placa, :st);";
                $valores = array(
                    "id" => null,
                    "nombre" => $nmremolque,
                    "tipo" => $tiporemolque,
                    "placa" => $placaremolque,
                    "st" => '1'
                );
                $remolque = $this->consultas->execute($consulta, $valores);
            } else if ($checkrem != '0' && $placaremolque != "") {
                $remolque = "Ya existe un registro de remolque (Remolque $numero) con el número de placas $placaremolque.";
            } else if(($checkrem != '0' && $placaremolque != "") && $checkrems == "0"){
                $consulta = "UPDATE `remolque` SET nombreremolque=:nombre, tiporemolque=:tipo, placaremolque=:placa, status=:st WHERE placaremolque=:placa;";
                $valores = array(
                    "nombre" => $nmremolque,
                    "tipo" => $tiporemolque,
                    "placa" => $placaremolque,
                    "st" => '1'
                );
                $remolque = $this->consultas->execute($consulta, $valores);
            }
        } else if ($idremolque != "0" && $placaremolque != "") {
            if ($checkrem != "0" && $checkrems == "1") {
                $remolque = "Ya existe un registro de remolque (Remolque $numero) con el número de placas $placaremolque.";
            } else if($checkrem != "0" && $checkrems == "1"){
                $consulta = "UPDATE `remolque` SET nombreremolque=:nombre, tiporemolque=:tipo, placaremolque=:placa, status=:st WHERE placaremolque=:placa;";
                $valores = array(
                    "nombre" => $nmremolque,
                    "tipo" => $tiporemolque,
                    "placa" => $placaremolque,
                    "st" => '1'
                );
                $remolque = $this->consultas->execute($consulta, $valores);
            }
        }
        return $remolque;
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
        $consulta = "SELECT p.crearcarta, p.editarcarta, p.eliminarcarta, p.timbrarcarta FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
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
            $timbrar = $actual['timbrarcarta'];
            $datos .= "$editar</tr>$eliminar</tr>$crear</tr>$timbrar";
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
                <th class='text-center col-md-2'>Emisor</th>
                <th class='text-center col-md-2'>Cliente</th>
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
                $tittimbre = "Cancelar carta";
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
                    $modal = "data-bs-toggle='modal' data-bs-target='#pagosfactura'";
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
                    $color = "#128B8F";
                    $title = "Factura pagada <br> parcialmente";
                    $function = "onclick='tablaPagos($idfactura,$estado)'";
                    $modal = "data-bs-toggle='modal' data-bs-target='#pagosfactura'";
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
                        <td class='lh-1'>$emisor</td>
                        <td class='lh-1'>$nombre_cliente</td>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <a class='state-link fw-semibold' style='color: $color;' $modal $function><span>$estadoF</span></a>
                                <span class='tiptext' style='color: $color;'>$title</span>
                            </div>
                        </td>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <span style='color: $colorB;' class='fas fa-bell'></span>
                                <span class='tiptext fw-semibold' style='color: $colorB;'>$titbell</span>
                            </div>
                        </td>
                        <td class='text-center'>$" . number_format($total, 2, '.', ',') . "</td>
                        <td class='text-center'>$cmoneda</td>
                        <td align='center'><div class='dropdown dropend'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right z-1'>";

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

            if($uuid != "" && $uuid != ""){
                $datos .= "<div class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' $xml>Ver XML <span class='text-muted fas fa-download'></span></a></div>";
            }

            if($div[3] == '1'){
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' $timbre>$tittimbre <span class='text-muted fas fa-bell'></span></a></li>";
            }

            $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"verEvidencias($idfactura);\">Ver evidencias <span class='text-muted fas fa-save'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#enviarmail' onclick='showCorreosCarta($idfactura);'>Enviar <span class='text-muted fas fa-envelope'></span></a></li>";

            if ($uuid != "") {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis lh-base' data-bs-toggle='modal' data-bs-target='#modal-stcfdi' onclick='statusCancelacionCarta($idfactura);'>Comprobar estado de <br> la factura <span class='fas fa-check-circle text-muted'></span></a></li>";
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
            $datos .= "<tr><td colspan='11'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='11'>Mostrando $inicios al $finales de $numrows registros " . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    private function getTagCartaAux($cid) {
        $consultado = false;
        $consulta = "SELECT * FROM factura_carta WHERE idfactura_carta=:id";
        $val = array("id" => $cid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getTagCartabyID($cid) {
        $tag = "";
        $datos = $this->getTagCartaAux($cid);
        foreach ($datos as $actual) {
            $tag = $actual['tagfactura'];
        }
        return $tag;
    }

    public function eliminarFactura($cid) {
        $tag = $this->getTagCartabyID($cid);
        $eliminado = false;
        $consulta = "DELETE FROM `factura_carta` WHERE idfactura_carta=:id;";
        $valores = array("id" => $cid);
        $eliminado = $this->consultas->execute($consulta, $valores);

        $del = false;
        $consulta = "DELETE FROM `datos_carta` WHERE tagcarta=:tag;";
        $val = array("tag" => $tag);
        $del = $this->consultas->execute($consulta, $val);

        $eliminado2 = $this->eliminarFacturaAux($tag);
        $carta = $this->eliminarDetalleCarta($tag);
        return $eliminado;
    }

    private function eliminarFacturaAux($tag) {
        $eliminado = false;
        $consulta = "DELETE FROM `detallefcarta` WHERE tagdetfactura=:tag;";
        $valores = array("tag" => $tag);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function eliminarDetalleCarta($tag) {
        $borrar = "DELETE FROM `detallemercancia` WHERE tagmercancia=:tag;";
        $borrarvalores = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarvalores);

        $borrar = "DELETE FROM `detalleubicacion` WHERE tagubicacion=:tag;";
        $borrarval = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarval);

        $borrar = "DELETE FROM `detalleoperador` WHERE tagoperador=:tag;";
        $borrarval = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $borrarval);
        return $eliminado;
    }
    //------------------------------------------------- PAGOS
    private function getFacturaPagoById($idfactura) {
        $consultado = false;
        $consulta = "SELECT f.*, df.nombre_contribuyente FROM factura_carta f INNER JOIN datos_facturacion df ON (f.iddatosfacturacion=df.id_datos) WHERE f.idfactura_carta=:id;";
        $val = array("id" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
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
            $idmoneda = $facturaactual['id_moneda'];
            $tcambio = $facturaactual['tcambio'];

            $datos = "$idfactura</tr>$folio</tr>$idcliente</tr>$nombrecliente</tr>$rfcreceptor</tr>$rzreceptor</tr>$cpreceptor</tr>$regfiscalreceptor</tr>$iddatosfacturacion</tr>$nombrecontribuyente</tr>$idformapago</tr>$idmoneda</tr>$tcambio";
            break;
        }
        return $datos;
    }

    private function getCartaEditar($idfactura) {
        $consultado = false;
        $consulta = "SELECT dat.*, dc.* FROM factura_carta dat INNER JOIN datos_carta dc ON (dat.tagfactura=dc.tagcarta) WHERE dat.idfactura_carta=:cid;";
        $val = array("cid" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getEditarCarta($cid) {
        $factura = $this->getCartaEditar($cid);
        $datos = "";
        foreach ($factura as $facturaactual) {
            $idfactura = $facturaactual['idfactura_carta'];
            $fecha_creacion = $facturaactual['fecha_creacion'];
            $rfcemisor = $facturaactual['factura_rfcemisor'];
            $rzsocial = $facturaactual['factura_rzsocial'];
            $clvreg = $facturaactual['factura_clvregimen'];
            $regimen = $facturaactual['factura_regimen'];
            $codpostal = $facturaactual['factura_cpemisor'];
            $serie = $facturaactual['serie'];
            $letra = $facturaactual['letra'];
            $folio = $facturaactual['foliocarta'];
            $idcliente = $facturaactual['idcliente'];
            $cliente = $this->getNombreCliente($idcliente);
            $rfccliente = $facturaactual['rfcreceptor'];
            $rzreceptor = $facturaactual['rzreceptor'];
            $dircliente = $facturaactual['dircliente'];
            $cpreceptor = $facturaactual['cpreceptor'];
            $regfiscalrec = $facturaactual['regfiscalreceptor'];
            $chfirmar = $facturaactual['chfirmar'];
            $idforma_pago = $facturaactual['id_forma_pago'];
            $idmetodo_pago = $facturaactual['id_metodo_pago'];
            $idmoneda = $facturaactual['id_moneda'];
            $tcambio = $facturaactual['tcambio'];
            $iduso_cfdi = $facturaactual['id_uso_cfdi'];
            $idtipo_comprobante = $facturaactual['id_tipo_comprobante'];
            $uuid = $facturaactual['uuid'];
            $iddatos = $facturaactual['iddatosfacturacion'];
            $iddatos_carta = $facturaactual['iddatos_carta'];
            $tipomovimiento = $facturaactual['tipomovimiento'];
            $idvehiculo = $facturaactual['carta_idvehiculo'];
            $nombrevehiculo = $facturaactual['nombrevehiculo'];
            $numpermiso = $facturaactual['carta_numpermiso'];
            $tipopermiso = $facturaactual['carta_tipopermiso'];
            $tipotrans = $facturaactual['carta_conftransporte'];
            $anhomod = $facturaactual['carta_anhomod'];
            $placa = $facturaactual['carta_placa'];
            $segurocivil = $facturaactual['carta_segurocivil'];
            $polizaseguro = $facturaactual['carta_polizaseguro'];
            $idremolque1 = $facturaactual['carta_idremolque1'];
            $nmremolque1 = $facturaactual['carta_nmremolque1'];
            $tiporemolque1 = $facturaactual['carta_tiporemolque1'];
            $placaremolque1 = $facturaactual['carta_placaremolque1'];
            $idremolque2 = $facturaactual['carta_idremolque2'];
            $nmremolque2 = $facturaactual['carta_nmremolque2'];
            $tiporemolque2 = $facturaactual['carta_tiporemolque2'];
            $placaremolque2 = $facturaactual['carta_placaremolque2'];
            $idremolque3 = $facturaactual['carta_idremolque3'];
            $nmremolque3 = $facturaactual['carta_nmremolque3'];
            $tiporemolque3 = $facturaactual['carta_tiporemolque3'];
            $placaremolque3 = $facturaactual['carta_placaremolque3'];
            $tag = $facturaactual['tagfactura'];
            $seguroambiente = $facturaactual['carta_seguroambiente'];
            $polizaambiente = $facturaactual['carta_polizaambiente'];
            $periodoglobal = $facturaactual['periodoglobal'];
            $mesperiodo = $facturaactual['mesperiodo'];
            $anhoperiodo = $facturaactual['anhoperiodo'];
            $observaciones = addslashes($facturaactual['carta_observaciones']);
			$cfdisrel = $facturaactual['cfdisrel'];
            $peso_vehicular = $facturaactual['pesovehicular'];
			$peso_bruto = $facturaactual['pesobruto'];

            $datos = "$idfactura</tr>$fecha_creacion</tr>$rfcemisor</tr>$rzsocial</tr>$clvreg</tr>$regimen</tr>$codpostal</tr>$serie</tr>$letra</tr>$folio</tr>$idcliente</tr>$cliente</tr>$rfccliente</tr>$rzreceptor</tr>$cpreceptor</tr>$regfiscalrec</tr>$chfirmar</tr>$idforma_pago</tr>$idmetodo_pago</tr>$idmoneda</tr>$tcambio</tr>$iduso_cfdi</tr>$idtipo_comprobante</tr>$uuid</tr>$iddatos</tr>$iddatos_carta</tr>$tipomovimiento</tr>$idvehiculo</tr>$nombrevehiculo</tr>$numpermiso</tr>$tipopermiso</tr>$tipotrans</tr>$anhomod</tr>$placa</tr>$segurocivil</tr>$polizaseguro</tr>$idremolque1</tr>$nmremolque1</tr>$tiporemolque1</tr>$placaremolque1</tr>$idremolque2</tr>$nmremolque2</tr>$tiporemolque2</tr>$placaremolque2</tr>$idremolque3</tr>$nmremolque3</tr>$tiporemolque3</tr>$placaremolque3</tr>$tag</tr>$seguroambiente</tr>$polizaambiente</tr>$periodoglobal</tr>$mesperiodo</tr>$anhoperiodo</tr>$dircliente</tr>$observaciones</tr>$cfdisrel</tr>$peso_vehicular</tr>$peso_bruto";
            break;
        }
        return $datos;
    }

    public function getMercancias($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detallemercancia WHERE tagmercancia=:tag";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function mercanciaCarta($tag, $sid) {
        $insertado = false;
        $productos = $this->getMercancias($tag);
        foreach ($productos as $productoactual) {
            $clv = $productoactual["clave_mercanca"];
            $descripcion = $productoactual["descripcion_mercancia"];
            $cant = $productoactual["cant_mercancia"];
            $unidad = $productoactual['unidad_mercancia'];
            $peso = $productoactual['peso_mercancia'];
            $condicional = $productoactual['condicion'];
            $peligro = $productoactual['peligro'];
            $clvmaterial = $productoactual['clvmaterial'];
            $embalaje = $productoactual['embalaje'];

            $consulta = "INSERT INTO `tmpmercancia` VALUES (:id, :clv, :descripcion, :cant, :unidad, :peso, :sid, :condpeligro, :peligro, :clvmaterial, :tmpembalaje);";
            $valores = array("id" => null,
                "clv" => $clv,
                "descripcion" => $descripcion,
                "cant" => $cant,
                "unidad" => $unidad,
                "peso" => $peso,
                "sid" => $sid,
                "condpeligro" => $condicional,
                "peligro" => $peligro,
                "clvmaterial" => $clvmaterial,
                "tmpembalaje" => $embalaje);

            $insertado = $this->consultas->execute($consulta, $valores);
        }
        return $insertado;
    }

    public function getUbicacionesEditar($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detalleubicacion WHERE tagubicacion=:tag;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function ubicacionCarta($tag, $sid) {
        $insertado = false;
        $ubicaciones = $this->getUbicacionesEditar($tag);
        foreach ($ubicaciones as $actual) {
            $idub = $actual['ubicacion_id'];
            $nombre = $actual["ubicacion_nombre"];
            $rfc = $actual['ubicacion_rfc'];
            $tipo = $actual["ubicacion_tipo"];
            $estado = $actual["ubicacion_idestado"];
            $nombreestado = $actual["nombre_estado"];
            $cp = $actual['ubicacion_codpostal'];
            $distancia = $actual['ubicacion_distancia'];
            $fechallegada = $actual['fechallegada'];
            $hora = $actual['horallegada'];
            $direccion = $actual['direccion'];
            $idmunicipio = $actual['idmunicipio'];
            $nombremunicipio = $actual["nombre_municipio"];

            $consulta = "INSERT INTO `tmpubicacion` VALUES (:id, :idub, :nombre, :rfc, :idtipo, :idestado, :nombreestado, :codp, :distancia, :fecha, :hora, :sid, :direccion, :idmunicipio,:nombre_municipio);";
            $valores = array("id" => null,
                "idub" => $idub,
                "nombre" => $nombre,
                "rfc" => $rfc,
                "idtipo" => $tipo,
                "idestado" => $estado,
                "nombre_estado" => $nombreestado,
                "codp" => $cp,
                "distancia" => $distancia,
                "fecha" => $fechallegada,
                "hora" => $hora,
                "sid" => $sid,
                "direccion" => $direccion,
                "idmunicipio" => $idmunicipio,
                "nombre_municipio" => $nombremunicipio
            );
            $insertado = $this->consultas->execute($consulta, $valores);
        }
        return $insertado;
    }

    public function getOperadores($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detalleoperador u WHERE tagoperador=:tag;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function operadorCarta($tag, $sid) {
        $insertado = false;
        $ubicaciones = $this->getOperadores($tag);
        foreach ($ubicaciones as $actual) {
            $idop = $actual['operador_id'];
            $nombreop = $actual['operador_nombre'];
            $rfc = $actual["operador_rfc"];
            $lic = $actual['operador_numlic'];
            $idestado = $actual["operador_idestado"];
            $nombre_estado = $actual["nombre_estado"];
            $calle = $actual["operador_calle"];
            $cp = $actual['operador_cp'];
            $idmunicipio = $actual['operador_idmunicipio'];
            $nombre_municipio = $actual["nombre_municipio"];

            $consulta = "INSERT INTO `tmpoperador` VALUES (:id, :idop, :nombre, :rfc, :lic, :idestado, :nombre_estado, :calle, :cp, :sid, :idmunicipio, :nombre_municipio);";
            $valores = array("id" => null,
                "idop" => $idop,
                "nombre" => $nombreop,
                "rfc" => $rfc,
                "lic" => $lic,
                "idestado" => $idestado,
                "nombre_estado" => $nombre_estado,
                "calle" => $calle,
                "cp" => $cp,
                "sid" => $sid,
                "idmunicipio" => $idmunicipio,
                "nombre_municipio" => $nombre_municipio,
            );
            $insertado = $this->consultas->execute($consulta, $valores);
        }
        return $insertado;
    }

    
    //----------------------------------RELACION CON OTROS MODULOS
    public function getProductosFactura($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detallefcarta WHERE tagdetfactura=:tag";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function productosFactura($id, $sid) {
        $insertado = false;
        $productos = $this->getProductosFactura($id);
        foreach ($productos as $productoactual) {
            $cantidad = $productoactual["cantidad"];
            $precio = $productoactual["precio"];
            $totunitario = $productoactual["totalunitario"];
            $descuento = $productoactual['descuento'];
            $impdescuento = $productoactual['impdescuento'];
            $totdescuento = $productoactual['totaldescuento'];
            $traslados = $productoactual['traslados'];
            $retenciones = $productoactual['retenciones'];
            $observaciones = $productoactual['observacionesproducto'];
            $idproducto = $productoactual["id_producto_servicio"];
            $nombre = $productoactual['carta_producto'];
            $chinventario = $productoactual['chinv'];
            $clvfiscal = $productoactual['clvfiscal'];
            $clvunidad = $productoactual['clvunidad'];

            $consulta = "INSERT INTO `tmp` VALUES (:id, :idproducto, :nombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :tras, :ret, :observaciones, :chinv, :cfiscal, :cunidad, :session);";
            $valores = array("id" => null,
                "idproducto" => $idproducto,
                "nombre" => $nombre,
                "cantidad" => $cantidad,
                "precio" => $precio,
                "importe" => $totunitario,
                "descuento" => $descuento,
                "impdescuento" => $impdescuento,
                "imptotal" => $totdescuento,
                "tras" => $traslados,
                "ret" => $retenciones,
                "observaciones" => $observaciones,
                "chinv" => $chinventario,
                "cfiscal" => $clvfiscal,
                "cunidad" => $clvunidad,
                "session" => $sid);
            $insertado = $this->consultas->execute($consulta, $valores);
        }
        return $insertado;
    }
    //----------------------------------CLIENTES
    public function checkCliente($rfc) {
        $cliente = '';
        $datos = $this->getClientebyRFC($rfc);
        foreach ($datos as $actual) {
            $estado =  $actual["nombre_estado"];
            $municipio =  $actual["nombre_municipio"];
            $int = "";
            if($actual['numero_interior'] != ""){
                $int = " Int. ".$actual['numero_interior'];
            }

            $idcliente = $actual['id_cliente'];
            $razon = $actual['razon_social'];
            $regimen = $actual['regimen_cliente'];
            $cp = $actual['codigo_postal'];
            $nombre = $actual['nombre'] . " " . $actual['apaterno'] . " - " . $actual['nombre_empresa'];
            $direccion = $actual['calle']." ".$actual['numero_exterior'].$int." ".$actual['localidad']." ".$municipio." ".$estado;
            $cliente .= "$idcliente</tr>$razon</tr>$regimen</tr>$cp</tr>$nombre</tr>$direccion";
        }
        return 'x' . $cliente;
    }

    private function getClientebyRFC($rfc) {
        $consultado = false;
        $consulta = "SELECT * FROM cliente WHERE rfc=:rfc;";
        $val = array("rfc" => $rfc);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getClientebyID($idcliente) {
        $consultado = false;
        $consulta = "SELECT * FROM cliente WHERE id_cliente=:cid;";
        $val = array("cid" => $idcliente);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getNombreCliente($idcliente) {
        $nombre = "";
        $datos = $this->getClientebyID($idcliente);
        foreach ($datos as $actual) {
            $nombre = $actual['nombre'] . " " . $actual['apaterno'] . "-" . $actual['nombre_empresa'];
        }
        return $nombre;
    }

    //----------------------------------------IMPRIMIR
    public function getFacturas($idfactura) {
        $consultado = false;
        $consulta = "SELECT * FROM factura_carta dat WHERE dat.idfactura_carta=:id";
        $val = array("id" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDetalle($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detallefcarta det WHERE tagdetfactura=:tag";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosCarta($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_carta WHERE tagcarta=:tag;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getDistanciaTotalAux($tag) {
        $consultado = false;
        $consulta = "SELECT sum(ubicacion_distancia) distancia FROM detalleubicacion u WHERE tagubicacion=:tag AND ubicacion_tipo=2;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDistanciaTotal($tag) {
        $distancia = 0;
        $datos = $this->getDistanciaTotalAux($tag);
        foreach ($datos as $actual) {
            $distancia = $actual['distancia'];
        }
        return $distancia;
    }

    public function getUbicaciones($tag, $tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM detalleubicacion u WHERE tagubicacion=:tag and ubicacion_tipo=:tipo";
        $val = array("tag" => $tag,
            "tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getOperadorbyRFC($rfc) {
        $consultado = false;
        $consulta = "SELECT * FROM operador WHERE rfcoperador=:rfc;";
        $val = array("rfc" => $rfc);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getNombreOperador($rfc) {
        $nombre = "";
        $operador = $this->getOperadorbyRFC($rfc);
        foreach ($operador as $actual) {
            $nombre = $actual['nombreoperador'] . ' ' . $actual['apaternooperador'] . ' ' . $actual['amaternooperador'];
        }
        return $nombre;
    }

    private function getConfigMailAux() {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE chuso1=:id;";
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

    public function mail($sm) {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $body = $cc->getMailBody('4');
        $divM = explode("</tr>", $body);
        $asunto = $divM[1];
        $saludo = $divM[2];
        $msg = $divM[3];
        $logo = $divM[4];

        $config = $this->getConfigMail();
        if ($config != "") {
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
            $mail->Username = $correoenvio;
            $mail->Password = $pass;
            $mail->From = $correoremitente;
            $mail->FromName = $remitente;

            $mail->Subject = iconv("utf-8", "windows-1252",$asunto);
            $mail->isHTML(true);
            $mail->Body = $this->bodyMail($asunto, $saludo, $sm->getRazonsocial(), $msg, $logo);

            if ($sm->getChmail1() == '1') {
                $mail->addAddress($sm->getMailalt1());
            }
            if ($sm->getChmail2() == '1') {
                $mail->addAddress($sm->getMailalt2());
            }
            if ($sm->getChmail3() == '1') {
                $mail->addAddress($sm->getMailalt3());
            }
            if ($sm->getChmail4() == '1') {
                $mail->addAddress($sm->getMailalt4());
            }
            if ($sm->getChmail5() == '1') {
                $mail->addAddress($sm->getMailalt5());
            }
            if ($sm->getChmail6() == '1') {
                $mail->addAddress($sm->getMailalt6());
            }

            $mail->addStringAttachment($sm->getPdfstring(), $sm->getFolio() . "_" . $sm->getRfcemisor() . "_" . $sm->getUuid() . ".pdf");
            if ($sm->getCfdistring() != "") {
                $mail->addStringAttachment($sm->getCfdistring(), $sm->getFolio() . "_" . $sm->getRfcemisor() . "_" . $sm->getUuid() . ".xml");
            }

            if (!$mail->send()) {
                echo '0No se envio el mensaje, ' . $mail->ErrorInfo;
            } else {
                return 'Se ha enviado la factura con carta porte.';
            }
        } else {
            return "0No se ha configurado un correo de envío para esta área de carta porte.";
        }
    }

    private function bodyMail($asunto, $saludo, $nombre, $msg, $logo) {
        $archivo = $_SESSION[sha1("database")].".ini";
        $ajustes = parse_ini_file($archivo, true);
        if (!$ajustes) {
            throw new Exception("No se puede abrir el archivo " . $archivo);
        }
        $rfcfolder = $ajustes['cron']['rfcfolder'];

        $txt = str_replace("<corte>", "</p><p style='font-size:18px; text-align: justify;'>", $msg);
        $message = "<html>
                        <body>
                            <table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0' style='border-radius: 25px;'>
                                <tr><td>
                                        <table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; border-radius: 20px; background-color:#fff; font-family:sans-serif;'>
                                            <thead>
                                                <tr height='80'>
                                                    <th align='left' colspan='4' style='padding: 6px; background-color:#f5f5f5; border-radius: 20px; border-bottom:solid 1px #bdbdbd;' ><img src='https://localhost/$rfcfolder/img/$logo' height='100px'/></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr align='center' height='10' style='font-family:sans-serif; '>
                                                    <td style='background-color:#09096B; text-align:center; border-radius: 5px;'></td>
                                                </tr>
                                                <tr>
                                                    <td colspan='4' style='padding:15px;'>
                                                        <h1>$asunto</h1>
                                                        <p style='font-size:20px;'>$saludo $nombre</p>
                                                        <hr/>
                                                        <p style='font-size:18px; text-align: justify;'>$txt</p>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </td></tr>
                            </table>
                        </body>
                    </html>";
        return $message;
    }

    //---------------------------------------EVIDENCIAS
    public function documentoCarta($tag, $sid) {
        $insertado = false;
        $ubicaciones = $this->getDocumentosCartaAux($tag);
        foreach ($ubicaciones as $actual) {
            $iddoc = $actual['iddetalledoccarta'];
            $orignm = $actual['orignm'];
            $imgnm = $actual["imgnm"];
            $ext = $actual['ext'];
            $descripcion = $actual["descripcion"];

            $consulta = "INSERT INTO `tmpimg` VALUES (:id, :tmpname, :imgtmp, :ext, :tmpdescripcion, :sid);";
            $valores = array("id" => null,
                "tmpname" => $orignm,
                "imgtmp" => $imgnm,
                "ext" => $ext,
                "tmpdescripcion" => $descripcion,
                "sid" => $sid);

            $insertado = $this->consultas->execute($consulta, $valores);
            copy("../img/cartaporte/$imgnm", "../temporal/tmp/$imgnm");
        }
        return $insertado;
    }

    private function getDocumentosCartaAux($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detalledoccarta WHERE tagimg=:tag";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaEvidencias($fid){
        $tag = $this->getTagCartabyID($fid);
        $datos = "<tbody>";

        $productos = $this->getDocumentosCartaAux($tag);
        foreach ($productos as $actual) {
            $orignm = $actual['orignm'];
            $nombredoc = $actual['imgnm'];
            $ext = $actual['ext'];

            $datos .= "<tr>
                        <td onclick='visutab(\"$nombredoc\",\"$ext\")'>$orignm</td>
                      </tr>";
        }
        return $datos;
    }

    private function getTmpImg($sid)
    {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimg where sessionid=:sid;";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaIMG($idtmp, $d = '')
    {
        $datos = "<corte>";
        $img = $this->getTmpImg($idtmp);
        foreach ($img as $actual) {
            $idtmp = $actual['idtmpimg'];
            $name = $actual['tmpname'];
            $coldesc = "";
            if ($d == "1") {
                $descripcion = $actual["tmpdescripcion"];
                $coldesc = "<td style='word-break: break-all;'><input class='form-control text-center input-sm' id='descripcion$idtmp' type='text' value='$descripcion' onblur=\"addDescripcion('$idtmp')\" placeholder='Añade un nombre o descripcion al archivo (opcional)' title='Añade un nombre o descripcion al archivo (opcional)'/> </td>";
            }
            $datos .= "
                <tr>
                    <td class='click-row' style='word-break: break-all;' onclick=\"displayIMG('$idtmp') \">$name </td>
                    $coldesc
                    <td><button class='btn btn-xs btn-danger' onclick='eliminarIMG($idtmp)' title='Eliminar imagen'><span class=' fas fa-times fa-sm'></span></button></td>
                </tr>";
        }
        return $datos;
    }

    public function eliminarIMG($idtmp)
    {
        $img = $this->getNameImg($idtmp);
        $consultado = false;
        $consulta = "DELETE FROM tmpimg where idtmpimg=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->execute($consulta, $val);
        unlink("../temporal/tmp/$img");
        return $consultado;
    }

    private function getNameImg($idsession)
    {
        $img = "";
        $imgs = $this->getIMGDataAux($idsession);
        foreach ($imgs as $actual) {
            $img = $actual['imgtmp'];
        }
        return $img;
    }

    private function getIMGDataAux($idsession)
    {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimg WHERE idtmpimg=:id;";
        $valores = array("id" => $idsession);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getCorreosAux($idfactura) {
        $consultado = false;
        $consulta = "SELECT c.email_informacion,c.email_facturacion,c.email_gerencia, c.correoalt1, c.correoalt2, c.correoalt3, c.telefono FROM factura_carta dat INNER JOIN cliente c on (dat.idcliente=c.id_cliente) WHERE dat.idfactura_carta=:id;";
        $val = array("id" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getCorreo($idfactura) {
        $datos = "";
        $correos = $this->getCorreosAux($idfactura);
        foreach ($correos as $actual) {
            $correo1 = $actual['email_informacion'];
            $correo2 = $actual['email_facturacion'];
            $correo3 = $actual['email_gerencia'];
            $correo4 = $actual['correoalt1'];
            $correo5 = $actual['correoalt2'];
            $correo6 = $actual['correoalt3'];
            $datos .= "$correo1<corte>$correo2<corte>$correo3<corte>$correo4<corte>$correo5<corte>$correo6";
        }
        return $datos;
    }

    //----------------------------------TIMBRADO
    private function getSaldoAux() {
        $consultado = false;
        $consulta = "SELECT * FROM contador_timbres WHERE idtimbres=:id;";
        $valores = array("id" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function checkSaldoAux() {
        $restantes = "0";
        $saldo = $this->getSaldoAux();
        foreach ($saldo as $actual) {
            $restantes = $actual['timbresRestantes'];
        }
        return $restantes;
    }

    public function checkSaldo($idfactura) {
        $timbrar = "";
        $saldo = $this->checkSaldoAux();
        if ($saldo > 0) {
            $timbrar = $this->guardarXML($idfactura);
        } else {
            $timbrar = "0Su saldo de timbres se ha agotado.";
        }
        return $timbrar;
    }

    private function getDistinctCfdisRelacionados($id) {
        $consultado = false;
        $consulta = "SELECT DISTINCT tiporel FROM cfdirelacionado WHERE cfditag=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getcfdisRelacionadosByTipo($id, $tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM cfdirelacionado WHERE cfditag=:id AND tiporel=:tipo;";
        $val = array("id" => $id,
            "tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function guardarXML($idfactura){
        $facturas = $this->getFacturas($idfactura);
        foreach ($facturas as $facturaactual) {
            $idcliente = $facturaactual['idcliente'];
            $razonSocial = $facturaactual['rzreceptor'];
            $rfcCliente = $facturaactual['rfcreceptor'];
            $cpreceptor = $facturaactual['cpreceptor'];
            $regfiscalreceptor = $facturaactual['regfiscalreceptor'];
            $iddatos = $facturaactual['iddatosfacturacion'];

            $cfdi = $facturaactual['nombre_uso_cfdi'];
            list($cuso, $descripcionuso) = explode('-', $cfdi, 2);

            $serie = $facturaactual['serie'];
            $letra = $facturaactual['letra'];
            $folio = $facturaactual['foliocarta'];
            $subtotal = bcdiv($facturaactual['subtotal'], '1', 2);
            $subiva = $facturaactual['subtotaliva'];
            $subret = $facturaactual['subtotalret'];
            $totdescuentos = $facturaactual['totaldescuentos'];
            $total = bcdiv($facturaactual['totalfactura'], '1', 2);
            $moneda = $facturaactual['nombre_moneda'];
            list($c_moneda, $des_moneda) = explode('-', $moneda, 2);
            $tcambio = $facturaactual['tcambio'];
            $metodo = $facturaactual['nombre_metodo_pago'];
            list($c_metodopago, $des_metodo) = explode('-', $metodo, 2);
            $forma = $facturaactual['nombre_forma_pago'];
            list($c_formapago, $des_pago) = explode('-', $forma, 2);
            $comprobante = $facturaactual['nombre_comprobante'];
            list($c_tipoComprobante, $tipocomprobante) = explode('-', $comprobante, 2);
            $tag = $facturaactual['tagfactura'];
            $p_bruto_vehicular = $facturaactual['pesobruto'];
            $idccp = $facturaactual['idccp'];
        }

        if ($c_tipoComprobante == 'T') {
            $subtotal = '0';
            $total = '0';
            $c_moneda = 'XXX';
        }

        $sine = $this->getDatosFacturacionbyId($iddatos);
        foreach ($sine as $sineactual) {
        	$rfcemisor = $sineactual['rfc'];
            $rzsocial = $sineactual['razon_social'];
            $clvregimen = $sineactual['c_regimenfiscal'];
            $regimenemisor = $sineactual['regimen_fiscal'];
            $cpemisor = $sineactual['codigo_postal'];
            $nocertificado = $sineactual['numcsd'];
            $csd = $sineactual['csd'];
            $difverano = $sineactual['difhorarioverano'];
            $difinvierno = $sineactual['difhorarioinvierno'];
        }

        $fecha = date('Y-m-d\TH:i:s', strtotime('-1 hour'));
        $xml = new DomDocument('1.0', 'UTF-8');
        $raiz = $xml->createElementNS('http://www.sat.gob.mx/cfd/4', 'cfdi:Comprobante');
        $raiz = $xml->appendChild($raiz);
        $raiz->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cartaporte30', 'http://www.sat.gob.mx/CartaPorte30');
        $raiz->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $raiz->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/CartaPorte30 http://www.sat.gob.mx/sitio_internet/cfd/CartaPorte/CartaPorte30.xsd');
        $raiz->setAttribute('Version', '4.0');
        $raiz->setAttribute('Serie', $serie);
        $raiz->setAttribute('Folio', $letra . $folio);
        $raiz->setAttribute('Fecha', $fecha);
        $raiz->setAttribute('SubTotal', $subtotal);
        $raiz->setAttribute('Moneda', $c_moneda);
        ($c_tipoComprobante == 'I')? $raiz->setAttribute('FormaPago', $c_formapago):'';
        ($c_tipoComprobante == 'I')? $raiz->setAttribute('MetodoPago', $c_metodopago):'';
        ($c_tipoComprobante == 'I')? $raiz->setAttribute('TipoCambio', $tcambio):'';
        ($totdescuentos > 0)?$raiz->setAttribute('Descuento', bcdiv($totdescuentos, '1', 2)):'';
        $raiz->setAttribute('Total', $total);
        $raiz->setAttribute('TipoDeComprobante', $c_tipoComprobante);
        $raiz->setAttribute('Exportacion', '01');
        $raiz->setAttribute('LugarExpedicion', iconv('UTF-8', 'windows-1252',$cpemisor));
        $raiz->setAttribute('NoCertificado', $nocertificado);
        //Convertir certificado a B64 con openssl: enc -in "CSD/00001000000407565367.cer" -a -A -out "cerB64.txt" 
        $raiz->setAttribute('Certificado', $csd);

        $cfdis = $this->getDistinctCfdisRelacionados($tag);
        foreach ($cfdis as $relactual) {
            $tiporel = $relactual['tiporel'];

            $cfdisrel = $xml->createElement('cfdi:CfdiRelacionados');
            $cfdisrel = $raiz->appendChild($cfdisrel);
            $cfdisrel->setAttribute('TipoRelacion', $tiporel);

            $cfdis2 = $this->getcfdisRelacionadosByTipo($tag, $tiporel);
            foreach ($cfdis2 as $relactual2) {
                $uuid = $relactual2['uuid'];
                $cfdirel = $xml->createElement('cfdi:CfdiRelacionado');
                $cfdirel = $cfdisrel->appendChild($cfdirel);
                $cfdirel->setAttribute('UUID', $uuid);
            }
        }

        $emisor = $xml->createElement('cfdi:Emisor');
        $emisor = $raiz->appendChild($emisor);
        $emisor->setAttribute('Rfc', $rfcemisor);
        $emisor->setAttribute('Nombre', strtoupper($rzsocial));
        $emisor->setAttribute('RegimenFiscal', $clvregimen);

        $receptor = $xml->createElement('cfdi:Receptor');
        $receptor = $raiz->appendChild($receptor);
        $receptor->setAttribute('Rfc', $rfcCliente);
        $receptor->setAttribute('Nombre', strtoupper($razonSocial));
        $receptor->setAttribute('DomicilioFiscalReceptor', $cpreceptor);
        $divreg = explode("-", $regfiscalreceptor);
        $receptor->setAttribute('RegimenFiscalReceptor', $divreg[0]);
        $receptor->setAttribute('UsoCFDI', $cuso);

        $baseT = 0;

        $conceptos = $xml->createElement('cfdi:Conceptos');
        $conceptos = $raiz->appendChild($conceptos);
        $detallefactura = $this->getDetalle($tag);
        foreach ($detallefactura as $detalleactual) {
            $claveFiscal = $detalleactual['clvfiscal'];
            $precioV = $detalleactual['precio'];
            $cantidad = $detalleactual['cantidad'];
            $unidad = $detalleactual['clvunidad'];
            $descripcion = $detalleactual['carta_producto'];
            $totalu = $detalleactual['totalunitario'];
            $impdescuento = $detalleactual['impdescuento'];
            $traslados = $detalleactual['traslados'];
            $retenciones = $detalleactual['retenciones'];
            $objimp = "01";
            $importe = bcdiv($totalu, '1', 2) - bcdiv($impdescuento, '1', 2);
            $divclv = explode("-", $claveFiscal);
            $claveFiscal = $divclv[0];

            $divunit = explode("-", $unidad);
            $cunidad = $divunit[0];
            $dunidad = $divunit[1];

            $concepto = $xml->createElement('cfdi:Concepto');
            $concepto = $conceptos->appendChild($concepto);
            $concepto->setAttribute('ClaveProdServ', $claveFiscal);
            $concepto->setAttribute('Cantidad', $cantidad);
            $concepto->setAttribute('ClaveUnidad', $cunidad);
            $concepto->setAttribute('Unidad', $dunidad);
            $concepto->setAttribute('Descripcion', $descripcion);
            $concepto->setAttribute('ValorUnitario', bcdiv($precioV, '1', 2));
            $concepto->setAttribute('Importe', bcdiv($totalu, '1', 2));
            if ($traslados != "" || $retenciones != "") {
                $objimp = "02";
            }
            if ($c_tipoComprobante == 'T') {
                $objimp = "01";
            }
            $concepto->setAttribute('ObjetoImp', $objimp);
            if ($impdescuento > 0) {
                $concepto->setAttribute('Descuento', bcdiv($impdescuento, '1', 2));
            }

            if ($c_tipoComprobante == 'I') {
                if ($traslados != "" || $retenciones != "") {
                    $impuestos = $xml->createElement('cfdi:Impuestos');
                    $impuestos = $concepto->appendChild($impuestos);
                    $baseT += bcdiv($importe, '1', 2);
                }
                if ($traslados != "") {
                    $nodetraslados = $xml->createElement('cfdi:Traslados');
                    $nodetraslados = $impuestos->appendChild($nodetraslados);

                    $divt = explode("<impuesto>", $traslados);
                    foreach ($divt as $tras) {
                        $divt = explode("-", $tras);
                        $imp = "00$divt[2]";
                        $traslado = $xml->createElement('cfdi:Traslado');
                        $traslado = $nodetraslados->appendChild($traslado);
                        $traslado->setAttribute('Base', bcdiv($importe, '1', 2));
                        $traslado->setAttribute('Impuesto', $imp);
                        $traslado->setAttribute('TipoFactor', 'Tasa');
                        $traslado->setAttribute('TasaOCuota', bcdiv($divt[1], '1', 6));
                        $traslado->setAttribute('Importe', bcdiv($divt[0], '1', 2));
                    }
                }

                if ($retenciones != "") {
                    $noderet = $xml->createElement('cfdi:Retenciones');
                    $noderet = $impuestos->appendChild($noderet);

                    $divr = explode("<impuesto>", $retenciones);
                    foreach ($divr as $ret) {
                        $divr = explode("-", $ret);
                        $imp = "00$divr[2]";
                        $retencion = $xml->createElement('cfdi:Retencion');
                        $retencion = $noderet->appendChild($retencion);
                        $retencion->setAttribute('Base', bcdiv($importe, '1', 2));
                        $retencion->setAttribute('Impuesto', $imp);
                        $retencion->setAttribute('TipoFactor', 'Tasa');
                        $retencion->setAttribute('TasaOCuota', bcdiv($divr[1], '1', 6));
                        $retencion->setAttribute('Importe', bcdiv($divr[0], '1', 2));
                    }
                }
            }
        }

        if ($c_tipoComprobante == 'I') {
            if ($subiva != "" || $subret != "") {
                $impuestosT = $xml->createElement('cfdi:Impuestos');
                $impuestosT = $raiz->appendChild($impuestosT);
            }
            $totalR = 0;
            if ($subret != "") {
                $noderet = $xml->createElement('cfdi:Retenciones');
                $noderet = $impuestosT->appendChild($noderet);
                $div2 = explode("<impuesto>", $subret);
                foreach ($div2 as $ret1) {
                    $divr = explode("-", $ret1);
                    $impr = "00$divr[2]";
                    $retencion = $xml->createElement('cfdi:Retencion');
                    $retencion = $noderet->appendChild($retencion);
                    $retencion->setAttribute('Impuesto', $impr);
                    $retencion->setAttribute('Importe', bcdiv($divr[0], '1', 2));
                    $totalR += bcdiv($divr[0], '1', 2);
                }
                $impuestosT->setAttribute('TotalImpuestosRetenidos', bcdiv($totalR, '1', 2));
            }

            $totalT = 0;
            if ($subiva != "") {
                $nodetraslados = $xml->createElement('cfdi:Traslados');
                $nodetraslados = $impuestosT->appendChild($nodetraslados);
                $div1 = explode("<impuesto>", $subiva);
                foreach ($div1 as $tras1) {
                    $divt = explode("-", $tras1);
                    $imp = "00$divt[2]";
                    $traslado = $xml->createElement('cfdi:Traslado');
                    $traslado = $nodetraslados->appendChild($traslado);
                    $traslado->setAttribute('Base', bcdiv($baseT, '1', 2));
                    $traslado->setAttribute('Impuesto', $imp);
                    $traslado->setAttribute('TipoFactor', 'Tasa');
                    $traslado->setAttribute('TasaOCuota', bcdiv($divt[1], '1', 6));
                    $traslado->setAttribute('Importe', bcdiv($divt[0], '1', 2));
                    $totalT += bcdiv($divt[0], '1', 2);
                }
                $impuestosT->setAttribute('TotalImpuestosTrasladados', bcdiv($totalT, '1', 2));
            }
        }
        $totaldistancia = $this->getDistanciaTotal($tag);
        
        $complemento = $xml->createElement('cfdi:Complemento');
        $complemento = $raiz->appendChild($complemento);
        $nodecp = $xml->createElement('cartaporte30:CartaPorte');
        $nodecp = $complemento->appendChild($nodecp);
        $nodecp->setAttribute('Version', '3.0');
        $nodecp->setAttribute('TranspInternac', 'No');

        $nodecp->setAttribute('IdCCP', $idccp);
        $nodecp->setAttribute('TotalDistRec', $totaldistancia);
        

        $ubicaciones = $xml->createElement('cartaporte30:Ubicaciones');
        $ubicaciones = $nodecp->appendChild($ubicaciones);
        $getorigenes = $this->getUbicaciones($tag, '1');
        foreach ($getorigenes as $oractual) {
            $rfcubicacion = $oractual['ubicacion_rfc'];
            $idestadoub = $oractual["ubicacion_idestado"];
            $codpostal = $oractual['ubicacion_codpostal'];
            $fechallegada = $oractual['fechallegada'];
            $hora = $oractual['horallegada'];
            $estadoub = $this->controladorSat->getClvEstado($idestadoub);

            $nodeubicacion = $xml->createElement('cartaporte30:Ubicacion');
            $nodeubicacion = $ubicaciones->appendChild($nodeubicacion);
            $nodeubicacion->setAttribute('TipoUbicacion', 'Origen');
            $nodeubicacion->setAttribute('RFCRemitenteDestinatario', $rfcubicacion);
            $nodeubicacion->setAttribute('FechaHoraSalidaLlegada', $fechallegada . 'T' . $hora . ":00");
            
            $nodedomicilio = $xml->createElement('cartaporte30:Domicilio');
            $nodedomicilio = $nodeubicacion->appendChild($nodedomicilio);
            $nodedomicilio->setAttribute('Estado', $estadoub);
            $nodedomicilio->setAttribute('Pais', 'MEX');
            $nodedomicilio->setAttribute('CodigoPostal', $codpostal);
        }

        $getdestinos = $this->getUbicaciones($tag, '2');
        foreach ($getdestinos as $desactual) {
            $rfcubicacion = $desactual['ubicacion_rfc'];
            $idestadoub = $desactual["ubicacion_idestado"];
            $codpostal = $desactual['ubicacion_codpostal'];
            $distancia = $desactual['ubicacion_distancia'];
            $fechallegada = $desactual['fechallegada'];
            $hora = $desactual['horallegada'];
            $estadoub = $this->controladorSat->getClvEstado($idestadoub);

            $nodeubicacion = $xml->createElement('cartaporte30:Ubicacion');
            $nodeubicacion = $ubicaciones->appendChild($nodeubicacion);
            $nodeubicacion->setAttribute('TipoUbicacion', 'Destino');
            $nodeubicacion->setAttribute('RFCRemitenteDestinatario', $rfcubicacion);
            $nodeubicacion->setAttribute('FechaHoraSalidaLlegada', $fechallegada . 'T' . $hora . ":00");
            $nodeubicacion->setAttribute('DistanciaRecorrida', $distancia);
            
            $nodedomicilio = $xml->createElement('cartaporte30:Domicilio');
            $nodedomicilio = $nodeubicacion->appendChild($nodedomicilio);
            $nodedomicilio->setAttribute('Estado', $estadoub);
            $nodedomicilio->setAttribute('Pais', 'MEX');
            $nodedomicilio->setAttribute('CodigoPostal', $codpostal);
        }

        $pesototal = 0;
        $nummercancias = 0;

        $mercancias = $xml->createElement('cartaporte30:Mercancias');
        $mercancias = $nodecp->appendChild($mercancias);

        $countpeligro = 0;
        $getmercancias = $this->getMercancias($tag);
        foreach ($getmercancias as $meractual) {
            $clv = $meractual['clave_mercanca'];
            $descripcion = $meractual['descripcion_mercancia'];
            $cantmercancia = $meractual['cant_mercancia'];
            $unidadmerc = $meractual['unidad_mercancia'];
            $peso = $meractual['peso_mercancia'];
            $condicion = $meractual['condicion'];
            $cpeligro = $meractual['peligro'];
            $clvmaterial = $meractual['clvmaterial'];
            $embalaje = $meractual['embalaje'];

            $divclv = explode("-", $clv);
            $divun = explode("-", $unidadmerc);

            $nodemercancia = $xml->createElement('cartaporte30:Mercancia');
            $nodemercancia = $mercancias->appendChild($nodemercancia);
            $nodemercancia->setAttribute('BienesTransp', $divclv[0]);
            $nodemercancia->setAttribute('Descripcion', $descripcion);
            $nodemercancia->setAttribute('Cantidad', $cantmercancia);
            $nodemercancia->setAttribute('ClaveUnidad', $divun[0]);
            $nodemercancia->setAttribute('PesoEnKg', bcdiv($peso, '1', 2));

            if ($condicion == '0-1' || $condicion == '1') {
                if ($cpeligro == '0') {
                    $peligro = 'No';
                } else if ($cpeligro == '1') {
                    $peligro = 'Si';
                    $countpeligro++;
                } else {
                    $peligro = 'No';
                }
                $divp = explode("-", $clvmaterial);
                $dive = explode("-", $embalaje);
                $nodemercancia->setAttribute('MaterialPeligroso', $peligro);
                if ($cpeligro == '1') {
                    $nodemercancia->setAttribute('CveMaterialPeligroso', $divp[0]);
                    $nodemercancia->setAttribute('Embalaje', $dive[0]);
                }
            }
            $nummercancias++;
            $pesototal += $peso;
        }

        $mercancias->setAttribute('PesoBrutoTotal', bcdiv($pesototal, '1', 2));
        $mercancias->setAttribute('UnidadPeso', 'KGM');
        $mercancias->setAttribute('NumTotalMercancias', $nummercancias);

        $datoscarta = $this->getDatosCarta($tag);
        foreach ($datoscarta as $datactual) {
            $numpermiso = $datactual['carta_numpermiso'];
            $tipopermiso = $datactual['carta_tipopermiso'];
            $tipotransporte = $datactual['carta_conftransporte'];
            $anhomod = $datactual['carta_anhomod'];
            $placa = $datactual['carta_placa'];
            $segurocivil = $datactual['carta_segurocivil'];
            $polizaseguro = $datactual['carta_polizaseguro'];
            $tiporemolque1 = $datactual['carta_tiporemolque1'];
            $placaremolque1 = $datactual['carta_placaremolque1'];
            $tiporemolque2 = $datactual['carta_tiporemolque2'];
            $placaremolque2 = $datactual['carta_placaremolque2'];
            $tiporemolque3 = $datactual['carta_tiporemolque3'];
            $placaremolque3 = $datactual['carta_placaremolque3'];
            $seguroambiente = $datactual['carta_seguroambiente'];
            $polizaambiente = $datactual['carta_polizaambiente'];

            $divper = explode("-", $tipopermiso);
            $divveh = explode("-", $tipotransporte);
        }
        $nodetransporte = $xml->createElement('cartaporte30:Autotransporte');
        $nodetransporte = $mercancias->appendChild($nodetransporte);
        $nodetransporte->setAttribute('PermSCT', $divper[0]);
        $nodetransporte->setAttribute('NumPermisoSCT', $numpermiso);

        $nodeidentvehicular = $xml->createElement('cartaporte30:IdentificacionVehicular');
        $nodeidentvehicular = $nodetransporte->appendChild($nodeidentvehicular);
        $nodeidentvehicular->setAttribute('ConfigVehicular', $divveh[0]);
        $nodeidentvehicular->setAttribute('PesoBrutoVehicular', $p_bruto_vehicular);
        $nodeidentvehicular->setAttribute('AnioModeloVM', $anhomod);
        $nodeidentvehicular->setAttribute('PlacaVM', $placa);
        
        $nodeseguros = $xml->createElement('cartaporte30:Seguros');
        $nodeseguros = $nodetransporte->appendChild($nodeseguros);
        $nodeseguros->setAttribute('AseguraRespCivil', $segurocivil);
        $nodeseguros->setAttribute('PolizaRespCivil', $polizaseguro);
        
        if ($countpeligro > 0) {
            $nodeseguros->setAttribute('AseguraMedAmbiente', $seguroambiente);
            $nodeseguros->setAttribute('PolizaMedAmbiente', $polizaambiente);
        }

        if ($tiporemolque1 != "" || $tiporemolque2 != "" || $tiporemolque3 != "") {
            $remolques = $xml->createElement('cartaporte30:Remolques');
            $remolques = $nodetransporte->appendChild($remolques);

            if ($tiporemolque1 != "") {
                $divrem1 = explode("-", $tiporemolque1);
                $noderemolque = $xml->createElement('cartaporte30:Remolque');
                $noderemolque = $remolques->appendChild($noderemolque);
                $noderemolque->setAttribute('SubTipoRem', $divrem1[0]);
                $noderemolque->setAttribute('Placa', $placaremolque1);
            }

            if ($tiporemolque2 != "") {
                $divrem2 = explode("-", $tiporemolque2);
                $noderemolque = $xml->createElement('cartaporte30:Remolque');
                $noderemolque = $remolques->appendChild($noderemolque);
                $noderemolque->setAttribute('SubTipoRem', $divrem2[0]);
                $noderemolque->setAttribute('Placa', $placaremolque2);
            }

            if ($tiporemolque3 != "") {
                $divrem3 = explode("-", $tiporemolque3);
                $noderemolque = $xml->createElement('cartaporte30:Remolque');
                $noderemolque = $remolques->appendChild($noderemolque);
                $noderemolque->setAttribute('SubTipoRem', $divrem3[0]);
                $noderemolque->setAttribute('Placa', $placaremolque3);
            }
        }

        $figuratransporte = $xml->createElement('cartaporte30:FiguraTransporte');
        $figuratransporte = $nodecp->appendChild($figuratransporte);

        $operadores = $this->getOperadores($tag);
        foreach ($operadores as $opactual) {
            $numlicencia = $opactual['operador_numlic'];
            $rfcoperador = $opactual['operador_rfc'];
            $operador_nombre = $opactual['operador_nombre'];

            $nodefigura = $xml->createElement('cartaporte30:TiposFigura');
            $nodefigura = $figuratransporte->appendChild($nodefigura);
            $nodefigura->setAttribute('TipoFigura', '01');
            $nodefigura->setAttribute('NombreFigura', $operador_nombre);
            $nodefigura->setAttribute('RFCFigura', $rfcoperador);
            $nodefigura->setAttribute('NumLicencia', $numlicencia);            
        }


        $sello = $this->SelloXML($xml->saveXML(), $rfcemisor);
        $obj = json_decode($sello);
        $xml2 = new DOMDocument("1.0", "UTF-8");
        $xml2->loadXML($xml->saveXML());
        $c = $xml2->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/4', 'Comprobante')->item(0);
        $c->setAttribute('Sello', $obj->sello);
        $doc = "../XML/XML2.xml";
        $xml2->save($doc);
        $timbre = $this->timbrado($xml2->saveXML(), $idfactura, $rfcemisor, $rzsocial, $clvregimen, $regimenemisor, $cpemisor);
        return $timbre;
    }

    function SelloXML($doc, $rfc) {
        $xmlFile = $doc;
        $carpeta = '../temporal/' . $rfc . '/';
        $xslFile = "../vendor/recursos/cadenaoriginal_4_0.xslt";
        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->loadXML($xmlFile);
        $xsl = new DOMDocument();
        $xsl->load($xslFile);
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        $cadenaOriginal = $proc->transformToXML($xml);
        $fichero = "../vendor/recursos/cadenaOriginal.txt";
        file_put_contents($fichero, $cadenaOriginal, LOCK_EX);
        $params = array(
            "cadenaOriginal" => "../vendor/recursos/cadenaOriginal.txt",
            //Archivo key pem: pkcs8 -inform DET -in CSD/cer.key -passin pass:12345678a -out llaveprivada.pem
            "archivoKeyPem" => $carpeta . 'keyPEM.pem',
            //archivo cer pem: x509 -inform der -in CSD/cer.cer -out certificado.pem
            "archivoCerPem" => $carpeta . 'csdPEM.pem'
        );
        try {
            $result = Sellar::ObtenerSello($params);
            return $result;
        } catch (Exception $e) {
            echo '0Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function getCartaById($idfactura) {
        $consultado = false;
        $consulta = "SELECT * FROM factura_carta dat 
            INNER JOIN datos_carta dc ON (dat.tagfactura=dc.tagcarta)
            INNER JOIN datos_facturacion df ON (df.id_datos=dat.iddatosfacturacion) WHERE dat.idfactura_carta=:cid;";
        $val = array("cid" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function checkStatusCFDI($idfactura) {
        $datos = false;
        $factura = $this->getCartaById($idfactura);
        foreach ($factura as $actual) {
            $emisor = $actual['factura_rfcemisor'];
            $receptor = $actual['rfcreceptor'];
            $total = $actual['totalfactura'];
            $uuid = $actual['uuid'];
            $cfdistring = $actual['cfdistring'];
            $tcomprobante = $actual['id_tipo_comprobante'];
        }
        if ($tcomprobante == '5') {
            $total = 0;
        }

        $xml = simplexml_load_string($cfdistring);
        $comprobante = $xml->xpath('/cfdi:Comprobante');
        $attr = $comprobante[0]->attributes();
        $sello = $attr['Sello'];
        $subsello = substr($sello, -8);
        $soapUrl = "https://pruebacfdiconsultaqr.cloudapp.net/ConsultaCFDIService.svc"; 
        //"https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc";//productivo
        //$soapUrl = "https://pruebacfdiconsultaqr.cloudapp.net/ConsultaCFDIService.svc";//pruebas
        $consultaCfdi = consultaCfdiSAT::ServicioConsultaSAT($soapUrl, $emisor, $receptor, $total, $uuid, $subsello);
        $codstatus = $consultaCfdi->CodigoEstatus;
        $estado = $consultaCfdi->Estado;
        $cancelable = $consultaCfdi->EsCancelable;
        $statusCancelacion = $consultaCfdi->EstatusCancelacion;
        $status = $consultaCfdi->Status;

        if (is_array($consultaCfdi->EsCancelable)) {
            $cancelable = "";
        }

        if (is_array($consultaCfdi->EstatusCancelacion)) {
            $statusCancelacion = "";
        }
        $reset = "";
        if ($statusCancelacion === "Solicitud rechazada") {
            $reset = "<button class='button-modal' onclick='resetCfdiCarta($idfactura)' id='btn-reset-cfdi'>Restaurar factura <span class='fas fa-redo'></span></button>";
        }
        $datos = "$codstatus</tr>$estado</tr>$cancelable</tr>$statusCancelacion</tr>$reset</tr>$status";
        return $datos;
    }

    private function getSWAccessAux() {
        $consultado = false;
        $consulta = "SELECT * FROM swaccess WHERE idswaccess=:id;";
        $valores = array("id" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getSWAccess() {
        $datos = "";
        $get = $this->getSWAccessAux();
        foreach ($get as $actual) {
            $token = $actual['accesstoken'];
            $url = $actual['sw_url'];
        }
        $datos = "$url</tr>$token";
        return $datos;
    }

    function timbrado($doc, $idfactura, $rfcemisor, $rzsocial, $clvregimen, $regimenemisor, $cpemisor) {
        $swaccess = $this->getSWAccess();
        $div = explode("</tr>", $swaccess);
        $url = $div[0];
        $token = $div[1];
        $params = array(
            "url" => $url,
            "token" => $token
        );

        try {
            header("Content-type: text/plain");
            $stamp = StampService::Set($params);
            $result = $stamp::StampV4($doc);
            if ($result->status == "error") {
                return '0' . $result->message . " " . $result->messageDetail;
            } else if ($result->status == "success") {
                $guardar = $this->guardarTimbre($result, $idfactura, $rfcemisor, $rzsocial, $clvregimen, $regimenemisor, $cpemisor);
                var_dump($result);
                return $guardar;
            }
        } catch (Exception $e) {
            header("Content-type: text/plain");
            echo "0" . $e->getMessage();
        }
    }

    private function guardarTimbre($result, $idfactura, $rfcemisor, $rzsocial, $clvregimen, $regimenemisor, $cpemisor) {
        $actualizado = false;
        $consulta = "UPDATE `factura_carta` SET  factura_rfcemisor=:rfc, factura_rzsocial=:razon, factura_clvregimen=:clvreg, factura_regimen=:regimen, factura_cpemisor=:cpemisor, cadenaoriginal=:cadena, nocertificadosat=:certSAT, nocertificadocfdi=:certCFDI, uuid=:uuid, sellosat=:selloSAT, sellocfdi=:selloCFDI, fechatimbrado=:fechatimbrado, qrcode=:qrcode, cfdistring=:cfdi WHERE idfactura_carta=:id;";
        $valores = array("rfc" => $rfcemisor, 
            "razon" => $rzsocial,
            "clvreg" => $clvregimen,
            "regimen" => $regimenemisor,
            "cpemisor" => $cpemisor,
            "cadena" => $result->data->cadenaOriginalSAT,
            "certSAT" => $result->data->noCertificadoSAT,
            "certCFDI" => $result->data->noCertificadoCFDI,
            "uuid" => $result->data->uuid,
            "selloSAT" => $result->data->selloSAT,
            "selloCFDI" => $result->data->selloCFDI,
            "fechatimbrado" => $result->data->fechaTimbrado,
            "qrcode" => $result->data->qrCode,
            "cfdi" => $result->data->cfdi,
            "id" => $idfactura);
        $actualizado = $this->consultas->execute($consulta, $valores);
        $timbres = $this->updateTimbres();
        return '+Timbre Guardado';
    }

    private function updateTimbres() {
        $actualizado = false;
        $consulta = "UPDATE `contador_timbres` SET  timbresUtilizados=timbresUtilizados+1, timbresRestantes=timbresRestantes-1 WHERE idtimbres=:idtimbres;";
        $valores = array("idtimbres" => '1');
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function getPagosReg($folio) {
        $consultado = false;
        $consulta = "SELECT * FROM pagos p INNER JOIN complemento_pago cp ON cp.tagpago = p.tagpago WHERE idpago = :id";
        $val = array("id" => $folio);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getPagosDetalle($id) {
        $consultado = false;
        $consulta = "SELECT idpago FROM detallepago dp INNER JOIN pagos p ON p.tagpago = dp.detalle_tagencabezado WHERE pago_idfactura=:id AND type=:type ORDER BY foliopago DESC;";
        $val = array("id" => $id,
            "type" => 'c');
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaPagosReg($idfactura, $status) {
        $datos = "<corte><thead class='sin-paddding'>
            <tr>
                <th class='text-center'>FOLIO DE PAGO</th>
                <th class='text-center'>FECHA DE PAGO</th>
                <th class='text-center'>FORMA DE PAGO</th>
                <th class='text-center'>TOTAL PAGADO</th>
                <th class='text-center'>ESTADO</th>
                <th class='text-center'>RECIBO</th>
                </thead><tbody>";
        $productos = $this->getPagosDetalle($idfactura);
        foreach ($productos as $productoactual) {
            $folio = $productoactual['idpago'];
            $pagos = $this->getPagosReg($folio);
            foreach ($pagos as $pagoactual) {
                $idpago = $pagoactual['idpago'];
                $foliopago = $pagoactual['letra'] . $pagoactual['foliopago'];
                $fechapago = $pagoactual['complemento_fechapago'];
                $div = explode("-", $fechapago);
                $mes = $this->translateMonth($div[1]);

                $fechapago = $div[2] . ' / ' . $mes;
                $horapago = $pagoactual['complemento_horapago'];
                $horapago = date('g:i A', strtotime($horapago));
                $totalpagado = $pagoactual['totalpagado'];
                $c_pago = $pagoactual['c_forma_pago'];
                $formapago = $pagoactual['nombre_forma_pago'];

                if ($pagoactual['cancelado'] == '0') {
                    $estado = "Activo";
                    if ($pagoactual['uuidpago'] != '') {
                        $estado = "Timbrado";
                    }
                } else if ($pagoactual['cancelado'] == '1') {
                    $estado = "Cancelado";
                }

                $datos .= "
                    <tr>
                        <td class='text-center'>$foliopago</td>
                        <td class='text-center'>$fechapago a $horapago</td>
                        <td class='text-center'>$c_pago $formapago</td>
                        <td class='text-center'>$ " . bcdiv($totalpagado, '1', 2) . "</td>
                        <td class='text-center'>$estado</td>
                        <td class='text-center'><a class='btn button-list' title='Descagar PDF' onclick=\"imprimirpago($idpago);\"><span class='fas fa-list-alt'></span></a></td>
                    </tr>
                     ";
            }
        }

        if ($status == '4') {
            $datos .= "<tr><td colspan='5'></td><td class='text-center'><a class='btn button-list' title='Agregar nuevo pago' data-bs-dismiss='modal' onclick=\"registrarPago($idfactura);\"><span class='fas fa-file'></span></a></td></tr>";
        }

        $datos .= "</tbody>";
        return $datos;
    }

    public function getUUID($idfactura) {
        $datos = "";
        $uuid = $this->getUUIDAux($idfactura);
        foreach ($uuid as $u) {
            $uuid = $u['uuid'];
            $folio = $u['letra'] . $u['foliocarta'];
            $rfc = $u['rfc'];
            $pass = $u['passcsd'];
            $csd = $u['csd'];
            $key = $u['keyb64'];
            $datos = "$uuid</tr>$folio</tr>$rfc</tr>$pass</tr>$csd</tr>$key";
        }
        return $datos;
    }

    public function getUUIDAux($idfactura) {
        $consultado = false;
        $consulta = "SELECT f.uuid, f.letra, f.foliocarta, d.rfc, d.passcsd, d.keyb64, d.csd FROM factura_carta f INNER JOIN datos_facturacion d ON (f.iddatosfacturacion=d.id_datos) WHERE f.idfactura_carta=:id;";
        $val = array("id" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function cancelarTimbre($idfactura, $motivo, $reemplazo) {
        $swaccess = $this->getSWAccess();
        $div = explode("</tr>", $swaccess);
        $url = $div[0];
        $token = $div[1];

        $get = $this->getUUID($idfactura);
        $divideU = explode("</tr>", $get);
        $uuid = $divideU[0];
        $rfc = $divideU[2];
        $pass = $divideU[3];
        $csd = $divideU[4];
        $key = $divideU[5];

        if ($motivo == '01') {
            $params = array(
                "url" => $url,
                "token" => $token,
                "uuid" => $uuid,
                "password" => $pass,
                "rfc" => $rfc,
                "motivo" => $motivo,
                "foliosustitucion" => $reemplazo,
                "cerB64" => $csd,
                "keyB64" => $key
            );
        } else {
            $params = array(
                "url" => $url,
                "token" => $token,
                "uuid" => $uuid,
                "password" => $pass,
                "rfc" => $rfc,
                "motivo" => $motivo,
                "cerB64" => $csd,
                "keyB64" => $key
            );
        }

        try {
            header('Content-type: text/plain');
            $cancelationService = CancelationService::Set($params);
            $result = $cancelationService::CancelationByCSD();
            if ($result->status == "error") {
                return '0' . $result->message . " " . $result->messageDetail;
            } else if ($result->status == "success") {
                $guardar = $this->cancelarFactura($idfactura, $result->data->acuse);
                return $guardar;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    private function cancelarFactura($idfactura, $cfdi) {
        $ruta = "../XML/XML_CANCEL.xml";
        $archivo = fopen($ruta,"w");
        fwrite($archivo, $cfdi);
        fclose($archivo);


        $xml = simplexml_load_file($ruta);
        foreach ($xml->Folios as $folio) {
            $uuid_cancel = $folio->UUID;
            $status_uuid = $folio->EstatusUUID;
        }

        switch($status_uuid){
            case 201:
                $mensaje_cancel = "Solicitud de cancelación exitosa.";
                break;
            case 202:
                $mensaje_cancel = "Folio fiscal previamente cancelado.";
                break;
            case 203:
                $mensaje_cancel = "Folio fiscal no correspondiente al emisor.";
                break;
            case 204:
                $mensaje_cancel = "Folio fiscal no aplicable a cancelación.";
                break;
            case 205:
                $mensaje_cancel = "Folio fiscal no existente.";
                break;
            case 206:
                $mensaje_cancel = "UUID no corresponde a un CFDI del sector primario.";
                break;
            case 207:
                $mensaje_cancel = "No se especificó el motivo de cancelación o el motivo no es válido.";
                break;
            case 208:
                $mensaje_cancel = "Folio sustitución inválido.";
                break;
            case 209:
                $mensaje_cancel = "Folio sustitución no requerido.";
                break;
            case 210:
                $mensaje_cancel = "La fecha de solicitud de cancelación es mayor a la fecha de declaración.";
                break;
            case 211:
                $mensaje_cancel = "La fecha de solicitud de cancelación límite para factura global.";
                break;
            case 212:
                $mensaje_cancel = "Relación no valida o inexistente.";
                break;
            case 300:
                $mensaje_cancel = "Usuario no válido.";
                break;
            case 301:
                $mensaje_cancel = "XML mal formado.";
                break;
            case 302:
                $mensaje_cancel = "Sello mal formado.";
                break;
            case 304:
                $mensaje_cancel = "Certificado revocado o caduco.";
                break;
            case 305:
                $mensaje_cancel = "Certificado inválido.";
                break;
            case 309:
                $mensaje_cancel = "Certificado inválido.";
                break;
            case 310:
                $mensaje_cancel = "CSD inválido.";
                break;
            case 311:
                $mensaje_cancel = "Motivo inválido.";
                break;
            case 312:
                $mensaje_cancel = "UUID no relacionado.";
                break;
        }

        $actualizado = "";
        if($status_uuid == 201 || $status_uuid == 202){
            $consulta = "UPDATE `factura_carta` SET status_pago=:estado, cfdicancel=:cfdi WHERE idfactura_carta=:id;);";
            $valores = array("id" => $idfactura,
                "estado" => '3',
                "cfdi" => $cfdi);
            $actualizado = $this->consultas->execute($consulta, $valores);
            $actualizado = "1$status_uuid - $mensaje_cancel";
        } else {
            $actualizado = "0$status_uuid - $mensaje_cancel";
        }
        return $actualizado;
    }

    public function getXMLImprimir($idfactura) {
        $consultado = false;
        $consulta = "SELECT dat.letra,dat.foliocarta, dat.uuid,dat.cfdistring,dat.cfdicancel,df.rfc FROM factura_carta dat INNER JOIN datos_facturacion df ON (dat.iddatosfacturacion=df.id_datos) WHERE dat.idfactura_carta=:id;";
        $val = array("id" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }
}