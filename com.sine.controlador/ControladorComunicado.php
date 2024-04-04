<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';

use PHPMailer\PHPMailer\PHPMailer;

date_default_timezone_set("America/Mexico_City");
Session::start();
class ControladorComunicado
{
    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

    private function getClientesbyCategoria()
    {
        $consultado = false;
        $consulta = "SELECT * FROM cliente;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getContactoByCat()
    {
        $datos = "";
        $categoria = $this->getClientesbyCategoria();
        foreach ($categoria as $actual) {
            $idcliente = $actual['id_cliente'];
            $nombre = $actual['nombre_empresa'];
            $datos .= "<label class='me-3'><input class='input-check-sm' id='ch$idcliente' type='checkbox' checked value='$idcliente' name='contacto'> $nombre</label> ";
        }
        return $datos;
    }

    private function getCategoriaAux($idcategoria)
    {
        $consultado = false;
        $consulta = "select * from categoria where idcategoria='$idcategoria'";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getCategoriaById($idcategoria)
    {
        $nombre = "";
        $categoria = $this->getCategoriaAux($idcategoria);
        foreach ($categoria as $actual) {
            $nombre = $actual['nombrecategoria'];
        }
        return $nombre;
    }

    private function getConfigMailAux()
    {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE chuso4=:id;";
        $valores = array("id" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getConfigMail()
    {
        $datos = "";
        $get = $this->getConfigMailAux();
        foreach ($get as $actual) {
            $correo = $actual['correo'];
            $pass = $actual['password'];
            $remitente = $actual['remitente'];
            $host = $actual['host'];
            $puerto = $actual['puerto'];
            $seguridad = $actual['seguridad'];
            $datos = "$correo</tr>$pass</tr>$remitente</tr>$host</tr>$puerto</tr>$seguridad";
        }
        return $datos;
    }

    private function getClientesbyId($idcliente)
    {
        $consultado = false;
        $consulta = "SELECT * FROM cliente WHERE id_cliente=:id";
        $val = array("id" => $idcliente);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function enviarComunicado($idcomunicado, $pdf)
    {
        $com = $this->getComunicadoById($idcomunicado);
        foreach ($com as $comactual) {
            $chcom = $comactual['chcom'];
            $idcontactos = $comactual['idcontactos'];
            $fechacom = $comactual['fechacom'];
            $asunto = $comactual['asunto'];
            $tag = $comactual['comtag'];
        }

        $config = $this->getConfigMail();
        if ($config != "") {
            $div = explode("</tr>", $config);
            $correoenvio = $div[0];
            $pass = $div[1];
            $remitente = $div[2];
            $host = $div[3];
            $puerto = $div[4];
            $seguridad = $div[5];

            $mail = new PHPMailer;
            $mail->Mailer = 'smtp';
            $mail->SMTPAuth = true;
            $mail->Host = $host;
            $mail->Port = $puerto;
            $mail->SMTPSecure = $seguridad;
            $mail->Username = $correoenvio;
            $mail->Password = $pass;
            $mail->From = $correoenvio;
            $mail->FromName = $remitente;
            $mail->Subject = iconv("utf-8", "windows-1252", $asunto);
            $mail->Body = "Por este medio le hacemos llegar el siguiente comunicado informativo.";

            if ($chcom == '1') {
                $contactos = $this->getClientesbyCategoria();
            } else if ($chcom == '2') {
                $contactos = explode("-", $idcontactos);
            }

            foreach ($contactos as $conactual) {
                if ($chcom == '1') {
                    $correo1 = $conactual['email_informacion'];
                    $correo2 = $conactual['email_facturacion'];
                    $correo3 = $conactual['email_gerencia'];
                } else if ($chcom == '2') {
                    $correos = $this->getClientesbyId($conactual);
                    foreach ($correos as $actual) {
                        $correo1 = $actual['email_informacion'];
                        $correo2 = $actual['email_facturacion'];
                        $correo3 = $actual['email_gerencia'];
                    }
                }

                $mail->addAddress($correo1);
                $mail->addAddress($correo2);
                $mail->addAddress($correo3);

                $mail->isHTML(true);
                $mail->addStringAttachment($pdf, 'comunicado-' . $fechacom . '.pdf');

                $docs = $this->getImgComAux($tag);
                foreach ($docs as $dactual) {
                    $orignm = $dactual['docname'];
                    $fn = $dactual['docfile'];
                    $ext = $dactual['ext'];
                    if ($ext == "pdf" || $ext == "xlsx" || $ext == "xls" || $ext == "doc" || $ext == "docx" || $ext == "pptx" || $ext == "ppt") {
                        $mail->addAttachment('../comunicado/' . $fn, $orignm);
                    }
                }

                if (!$mail->send()) {
                    echo '0No se ha podido mandar el mensaje';
                    echo '0Mailer Error: ' . $mail->ErrorInfo;
                } else {
                    echo 'Se envió el comunicado al correo correspondiente.';
                }

                $mail->clearAddresses();
                $mail->clearAttachments();
            }
        } else {
            return "0No se ha configurado un correo de envio para esta área";
        }
    }

    private function genTag($sid)
    {
        $insertado = false;
        $fecha = getdate();
        $d = $fecha['mday'];
        $m = $fecha['mon'];
        $y = $fecha['year'];
        $h = $fecha['hours'];
        $mi = $fecha['minutes'];
        $sec = $fecha['seconds'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        if ($h < 10) {
            $h = "0$h";
        }
        if ($mi < 10) {
            $mi = "0$mi";
        }
        if ($sec < 10) {
            $sec = "0$sec";
        }

        $hoy = "$y-$m-$d";
        $hora = "$h:$mi";
        $idusu = $_SESSION[sha1("idusuario")];

        $dtag = $m . $y . $d . $h . $mi . $sec;
        $ranstr = "";
        $chars = "0123456789011121314151617181920";
        $charsize = strlen($chars);
        for ($r = 0; $r < 5; $r++) {
            $ranstr .= $chars[rand(0, $charsize - 1)];
        }

        $tag = $ranstr . $dtag . $idusu . $sid;
        return $tag;
    }

    public function insertarComunicado($c)
    {
        $tag = $this->genTag($c->getSid());
        $insertado = false;
        $f = getdate();
        $d = $f['mday'];
        $m = $f['mon'];
        $y = $f['year'];
        $h = $f['hours'];
        $mi = $f['minutes'];
        $s = $f['seconds'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        if ($h < 10) {
            $h = "0$h";
        }
        if ($mi < 10) {
            $mi = "0$mi";
        }
        if ($s < 10) {
            $s = "0$s";
        }
        $hoy = "$y-$m-$d";
        $hora = "$h:$mi:$s";

        $consulta = "INSERT INTO `comunicado` VALUES (:id, :fecha, :hora, :chcom, :idcontactos, :asunto, :emision, :color, :size, :mensaje, :sello, :firma, :iddatos, :tag);";
        $valores = array(
            "id" => null,
            "fecha" => $hoy,
            "hora" => $hora,
            "chcom" => $c->getChcom(),
            "idcontactos" => $c->getIdcontactos(),
            "asunto" => $c->getAsunto(),
            "emision" => $c->getEmision(),
            "color" => $c->getColor(),
            "size" => $c->getSize(),
            "mensaje" => $c->getMensaje(),
            "sello" => $c->getSellar(),
            "firma" => $c->getFirma(),
            "iddatos" => $c->getIddatos(),
            "tag" => $tag
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        $imgs = $this->detalleFotos($c->getSid(), $tag);
        return $insertado;
    }

    private function detalleFotos($sid, $tag)
    {
        $insertado = false;
        $img = $this->getTmpImg($sid);
        foreach ($img as $actual) {
            $imgname = $actual['tmpname'];
            $imgtmp = $actual['imgtmp'];
            $ext = $actual['ext'];
            $consulta = "INSERT INTO `doccomunicado` VALUES (:id, :docnm, :doc, :ext, :tag);";
            $valores = array(
                "id" => null,
                "docnm" => $imgname,
                "doc" => $imgtmp,
                "ext" => $ext,
                "tag" => $tag
            );
            $insertado = $this->consultas->execute($consulta, $valores);
            rename('../temporal/tmp/' . $imgtmp, '../comunicado/' . $imgtmp);
        }

        $borrar = "DELETE FROM `tmpimg` WHERE sessionid=:id;";
        $valores3 = array("id" => $sid);
        $eliminado = $this->consultas->execute($borrar, $valores3);
        return $insertado;
    }

    private function getTmpImg($sid)
    {
        $consultado = false;
        $consulta = "SELECT * FROM tmpimg where sessionid=:sid;";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function deleteImgTmp($sid)
    {
        $getimg = $this->getTmpImg($sid);
        foreach ($getimg as $actual) {
            $fn = $actual['imgtmp'];
            unlink("../temporal/tmp/$fn");
        }
        $consultado = false;
        $consulta = "DELETE FROM tmpimg where sessionid=:sid;";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->execute($consulta, $val);
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

    public function getFecha()
    {
        $datos = "";
        $f = getdate();
        $d = $f['mday'];
        $m = $f['mon'];
        $y = $f['year'];
        $h = $f['hours'];
        $mi = $f['minutes'];
        $s = $f['seconds'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        if ($h < 10) {
            $h = "0$h";
        }
        if ($mi < 10) {
            $mi = "0$mi";
        }
        if ($s < 10) {
            $s = "0$s";
        }
        $datos = "$d/$m/$y $h:$mi:$s<corte>$y-$m-$d";
        return $datos;
    }

    private function getDatosFacturacionAux($iddatos)
    {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion where id_datos=$iddatos;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getDatosFacturacionbyId($iddatos)
    {
        $nombre = "";
        $datos = $this->getDatosFacturacionAux($iddatos);
        foreach ($datos as $actual) {
            $nombre = $actual['nombre_contribuyente'];
        }
        return $nombre;
    }

    public function getComunicadoById($idcomunicado)
    {
        $consultado = false;
        $consulta = "select * from comunicado c where idcomunicado=:id;";
        $val = array("id" => $idcomunicado);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosComunicado($idcomunicado)
    {
        $comunicado = $this->getComunicadoById($idcomunicado);
        $datos = "";
        $sid = session_id();
        foreach ($comunicado as $comactual) {
            $idcomunicado = $comactual['idcomunicado'];
            $fechacom = $comactual['fechacom'];
            $horacom = $comactual['horacom'];
            $chcom = $comactual['chcom'];
            $idcontactos = $comactual['idcontactos'];
            $asunto = addslashes($comactual['asunto']);
            $emision = addslashes($comactual['lugaremision']);
            $color = $comactual['color'];
            $size = $comactual['size'];
            $mensaje = addslashes($comactual['mensaje']);
            $tag = $comactual['comtag'];
            $chsellar = $comactual['chsellar'];
            $chfirmar = $comactual['chfirmar'];
            $iddatos = $comactual['iddatos'];
            $nombre = "";
            if ($chfirmar == '1') {
                $nombre = $this->getDatosFacturacionbyId($iddatos);
            }

            $datos = "$idcomunicado</tr>$fechacom</tr>$horacom</tr>$chcom</tr>$asunto</tr>$mensaje</tr>$tag</tr>$color</tr>$size</tr>$idcontactos</tr>$chsellar</tr>$chfirmar</tr>$iddatos</tr>$nombre</tr>$emision";
        }
        $this->getImgsComunicado($sid, $tag);
        return $datos;
    }

    public function getImgsComunicado($sid, $tag)
    {
        $insertado = false;
        $imgs = $this->getImgComAux($tag);
        foreach ($imgs as $actual) {
            $docnm = $actual['docname'];
            $file = $actual['docfile'];
            $ext = $actual['ext'];
            $consulta = "INSERT INTO `tmpimg` VALUES (:id, :tmpname, :imgtmp, :ext, :tmpdescripcion, :sid);";
            $valores = array(
                "id" => null,
                "tmpname" => $docnm,
                "imgtmp" => $file,
                "ext" => $ext,
                "tmpdescripcion" => null,
                "sid" => $sid
            );
            $insertado = $this->consultas->execute($consulta, $valores);
            copy("../comunicado/$file", "../temporal/tmp/$file");
        }
        return $insertado;
    }

    public function actualizarComunicado($c)
    {
        $actualizado = false;
        $consulta = "UPDATE `comunicado` SET chcom=:chcom, idcontactos=:idcontactos, asunto=:asunto, lugaremision=:emision, color=:color, size=:size, mensaje=:mensaje, chsellar=:chsellar, chfirmar=:chfirmar, iddatos=:iddatos where idcomunicado=:id;";
        $valores = array(
            "id" => $c->getIdcomunicado(),
            "chcom" => $c->getChcom(),
            "idcontactos" => $c->getIdcontactos(),
            "asunto" => $c->getAsunto(),
            "emision" => $c->getEmision(),
            "color" => $c->getColor(),
            "size" => $c->getSize(),
            "mensaje" => $c->getMensaje(),
            "chsellar" => $c->getSellar(),
            "chfirmar" => $c->getFirma(),
            "iddatos" => $c->getIddatos()
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        $actualizarfotos = $this->actualizarFotos($c->getSid(), $c->getTag());
        return $actualizado;
    }

    private function actualizarFotos($sid, $tag)
    {
        $delete = $this->deleteIMGS($tag);
        $img = $this->getTmpImg($sid);
        foreach ($img as $actual) {
            $imgname = $actual['tmpname'];
            $imgtmp = $actual['imgtmp'];
            $ext = $actual['ext'];
            $consulta = "INSERT INTO `doccomunicado` VALUES (:id, :docnm, :doc, :ext, :tag);";
            $valores = array(
                "id" => null,
                "docnm" => $imgname,
                "doc" => $imgtmp,
                "ext" => $ext,
                "tag" => $tag
            );
            $insertado = $this->consultas->execute($consulta, $valores);
            rename('../temporal/tmp/' . $imgtmp, '../comunicado/' . $imgtmp);
        }
        $borrar = "DELETE FROM `tmpimg` WHERE sessionid=:id;";
        $valores3 = array("id" => $sid);
        $eliminado = $this->consultas->execute($borrar, $valores3);
        return $eliminado;
    }

    private function deleteIMGS($tag)
    {
        $datos = "";
        $imgs = $this->getImgComAux($tag);
        foreach ($imgs as $actual) {
            $doc = $actual['docfile'];
            unlink('../comunicado/' . $doc);
        }
        $eliminado = false;
        $borrar = "DELETE FROM `doccomunicado` WHERE doc_tag=:tag;";
        $valores3 = array("tag" => $tag);
        $eliminado = $this->consultas->execute($borrar, $valores3);
        return $eliminado;
    }

    private function getTagCom($cid)
    {
        $tag = "";
        $datos = $this->getComunicadoById($cid);
        foreach ($datos as $actual) {
            $tag = $actual['comtag'];
        }
        return $tag;
    }

    public function eliminarComunicado($id)
    {
        $tag = $this->getTagCom($id);
        $eliminado = false;
        $consulta = "DELETE FROM `comunicado` WHERE idcomunicado=:id;";
        $valores = array("id" => $id);
        $eliminado = $this->consultas->execute($consulta, $valores);
        $deldocs = $this->deleteIMGS($tag);
        return $eliminado;
    }

    private function getNumrowsAux($condicion)
    {
        $consultado = false;
        $consulta = "select count(*) numrows from comunicado c $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrows($condicion)
    {
        $numrows = 0;
        $rows = $this->getNumrowsAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    public function getListaComunicado($condicion)
    {
        $consultado = false;
        $consulta = "select * from comunicado c $condicion;";
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
            $lista = $actual['listacomunicado'];
            $editar = $actual['editarcomunicado'];
            $eliminar = $actual['eliminarcomunicado'];
            $datos .= "$lista</tr>$editar</tr>$eliminar";
        }
        return $datos;
    }

    public function listaComunicado($REF, $pag, $numreg)
    {
        include '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead>
                    <tr>
                        <th>Fecha / Hora de Creación</th>
                        <th class='col-md-4'>Asunto </th>
                        <th class='col-md-2 text-center'>Archivos adjuntos </th>
                        <th class='text-center'>Opción</th>
                    </tr>
                  </thead>
                <tbody>";
        $condicion = "";
        if ($REF == "") {
            $condicion = " ORDER BY c.fechacom DESC";
        } else {
            $condicion = "WHERE (c.asunto LIKE '%$REF%') ORDER BY c.fechacom DESC";
        }

        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        if ($div[0] == '1') {
            $numrows = $this->getNumrows($condicion);
            $page = (isset($pag) && !empty($pag)) ? $pag : 1;
            $per_page = $numreg;
            $adjacents = 4;
            $offset = ($page - 1) * $per_page;
            $total_pages = ceil($numrows / $per_page);
            $con = $condicion . " LIMIT $offset,$per_page ";
            $comunicado = $this->getListaComunicado($con);
            $finales = 0;
            foreach ($comunicado as $comactual) {
                $idcom = $comactual['idcomunicado'];
                $fechacom = $comactual['fechacom'];
                $horacom = $comactual['horacom'];
                $asunto = $comactual['asunto'];
                $tag = $comactual['comtag'];
                $divideF = explode("-", $fechacom);
                $mes = $this->translateMonth($divideF[1]);
                $fecha = $divideF[2] . ' - ' . $mes;

                $datos .= "<tr>
                <td>$fecha a ".date('h:i A', strtotime($horacom))."</td>
                <td>$asunto</td>
                <td class='text-center'>
                    <div>
                     <button type='button' class='btn btn-outline-secondary btn-sm' onclick='tablamodal(\"" . $tag . "\")'>
                    <span class='fas fa-eye' ></span>
                        </button>
                    </div>
                </td>
                        <td class='text-center'><div class='dropdown dropend'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right z-1'>";
                if ($div[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a  class='text-decoration-none text-secondary-emphasis' onclick='editarComunicado($idcom);'>Editar comunicado <span class=' text-muted small fas fa-edit'></span></a></li>";
                }

                if ($div[2] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a  class='text-decoration-none text-secondary-emphasis' onclick='eliminarComunicado($idcom);'>Eliminar comunicado <span class=' text-muted small fas fa-times'></span></a></li>";
                }

                $datos .= "<li class='notification-link py-1 ps-3'><a  class='text-decoration-none text-secondary-emphasis' onclick=\"imprimirComunicado($idcom);\">Imprimir comunicado <span class=' text-muted small fas fa-file'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a  class='text-decoration-none text-secondary-emphasis' onclick='crearComunicado($idcom);'>Enviar comunicado <span class=' text-muted small fas fa-envelope'></span></a></li>
                        </ul>
                        </div></td>
                    </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarComunicados";
            if ($finales == 0) {
                $datos .= "<tr><td colspan='11'>No se encontraron registros</td></tr>";
            }
            $datos .= "</tbody><tfoot><tr><th colspan='1' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='3'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        }

        return $datos;
    }

    public function getImgComAux($tag)
    {
        $consultado = false;
        $consulta = "SELECT * FROM doccomunicado where doc_tag=:tag;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getIMGList($tag)
    {
        $datos = "<tbody>";

        $archivos = "";
        $imgs = $this->getImgComAux($tag);
        foreach ($imgs as $actual) {
            $nombrefoto = $actual['docname'];
            $nombredoc = $actual['docfile'];
            $ext = $actual['ext'];

            $datos .= "<tr>
                        <td onclick='visutab(\"$nombredoc\",\"$ext\")'>$nombrefoto</td>
                      </tr>";
        }
        $datos .= "</tbody>";

        return $datos;
    }



    public function getCategoria()
    {
        $consultado = false;
        $consulta = "SELECT * FROM categoria ORDER BY nombrecategoria;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function opcionesCategoria()
    {
        $cat = $this->getCategoria();
        $r = "";
        foreach ($cat as $catactual) {
            $r = $r . "<option value='" . $catactual['idcategoria'] . "'>" . $catactual['nombrecategoria'] . "</option>";
        }
        return $r;
    }

    public function translateMonth($m)
    {
        switch ($m) {
            case '01':
                $mes = 'Ene';
                break;
            case '02':
                $mes = 'Feb';
                break;
            case '03':
                $mes = "Mar";
                break;
            case '04':
                $mes = 'Abr';
                break;
            case '05':
                $mes = 'May';
                break;
            case '06':
                $mes = 'Jun';
                break;
            case '07':
                $mes = 'Jul';
                break;
            case '08':
                $mes = 'Ago';
                break;
            case '09':
                $mes = 'Sep';
                break;
            case '10':
                $mes = 'Oct';
                break;
            case '11':
                $mes = 'Nov';
                break;
            case '12':
                $mes = 'Dic';
                break;
            default:
                $mes = "";
                break;
        }
        return $mes;
    }
}
