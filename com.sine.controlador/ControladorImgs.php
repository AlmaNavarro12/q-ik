<?php

require_once '../com.sine.dao/Consultas.php';

class ControladorImgs {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    public function insertarImg($nombre, $img, $ext, $sid) {
        $insertado = false;
        $consulta = "INSERT INTO `tmpimg` VALUES (:id, :tmpname, :imgtmp, :ext, :descripcion, :sid);";
        $valores = array("id" => null,
            "tmpname" => $nombre,
            "imgtmp" => $img,
            "ext" => $ext,
            "descripcion" => '',
            "sid" => $sid);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getTmpImg($sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimg where sessionid=:sid;";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaIMG($idsession, $d ='') {
        $datos = "<corte>";
        $img = $this->getTmpImg($idsession);
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
                    <td class='click-row' style='word-break: break-all;' onclick=\"displayIMG('$idtmp')\">$name </td>
                    $coldesc
                    <td><button class='btn btn-xs btn-danger' onclick='eliminarIMG($idtmp)' title='Eliminar imagen'><span class='glyphicon glyphicon-remove'></span></button></td>
                </tr>";
        }

        return $datos;
    }
    
    private function getIMGDataAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimg WHERE idtmpimg=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getIMGData($id) {
        $datos = "";
        $imgs = $this->getIMGDataAux($id);
        foreach ($imgs as $actual) {
            $img = $actual['imgtmp'];
            $ext = $actual['ext'];
            $ornm = $actual['tmpname'];
            $datos .= "$img<corte>$ext<corte>$ornm";
        }
        return $datos;
    }

    private function getNameImg($id) {
        $img = "";
        $imgs = $this->getIMGDataAux($id);
        foreach ($imgs as $actual) {
            $img = $actual['imgtmp'];
        }
        return $img;
    }

    public function eliminarImgTmp($id) {
        $img = $this->getNameImg($id);
        $consultado = false;
        $consulta = "DELETE FROM tmpimg where idtmpimg=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        unlink("../temporal/tmp/$img");
        return $consultado;
    }
    
    private function getIMGComunicadoAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM doccomunicado WHERE iddoccomunicado=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getIMGComunicado($id) {
        $datos = "";
        $imgs = $this->getIMGComunicadoAux($id);
        foreach ($imgs as $actual) {
            $img = $actual['docfile'];
            $ext = $actual['ext'];
            $ornm = $actual['docname'];
            $datos .= "$img<corte>$ext<corte>$ornm";
        }
        return $datos;
    }
    
    public function deleteImgTmp($sid) {
        $getimg = $this->getTmpImg($sid);
        foreach ($getimg as $actual){
            $fn = $actual['imgtmp'];
            unlink("../temporal/tmp/$fn");
        }
        $consultado = false;
        $consulta = "DELETE FROM tmpimg where sessionid=:sid;";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }
    
    private function getIMGAnticipoAux($id) {
        $consultado = false;
        $consulta = "SELECT imganticipo FROM anticipo WHERE idanticipo=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getIMGAnticipo($id) {
        $datos = "";
        $imgs = $this->getIMGAnticipoAux($id);
        foreach ($imgs as $actual) {
            $img = $actual['imganticipo'];
            $datos .= "$img";
        }
        return $datos;
    }
    
    public function updateDescripcion($id, $descripcion) {
        $insertado = false;
        $consulta = "UPDATE `tmpimg` SET tmpdescripcion=:descripcion WHERE idtmpimg=:id;";
        $valores = array("id" => null,
            "descripcion" => $descripcion,
            "id" => $id);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
    
    private function getIMGCartaPorteAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM detalledoccarta WHERE iddetalledoccarta=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getIMGCartaPorte($id) {
        $datos = "";
        $imgs = $this->getIMGCartaPorteAux($id);
        foreach ($imgs as $actual) {
            $img = $actual['imgnm'];
            $ext = $actual['ext'];
            $ornm = $actual['orignm'];
            $datos .= "$img<corte>$ext<corte>$ornm";
        }
        return $datos;
    }
    private function getDatosFacturacionAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosFacturacion($id) {
        $rfc = "";
        $imgs = $this->getDatosFacturacionAux($id);
        foreach ($imgs as $actual) {
            $rfc = $actual['rfc'];
        }
        return $rfc;
    }
}