<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';
require_once '../vendor/autoload.php';
date_default_timezone_set("America/Mexico_City");

use SWServices\AccountBalance\AccountBalanceService as AccountBalanceService;
use Twilio\Rest\Client;

class ControladorInicio {

    function __construct() {
    }
    
    public function sendMSG() {
        $sid = "AC6256c8483286f5e0fd804de145ba42bf";
        $token = "aabd7bf61f0efdf18965ffbef4f261bd";
        $twilio = new Client($sid, $token);
        try {
            $message = $twilio->messages->create("whatsapp:+5214271221859", // to
                    ["from" => "whatsapp:+14155238886",
                "body" => "Hola BB",
                "mediaUrl" => ["https://q-ik.mx/SineFacturacion/pdf/facturaFAC20200025.pdf"]]);
            return $message->sid;
        } catch (Exception $e) {
            header("Content-type: text/plain");
            echo "0" . $e->getMessage();
        }
    }

    function copyFolder($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyFolder($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function iniFile() {
        $sampleData = array(
            'database' => array(
                'driver' => 'PDO',
                'host' => 'localhost',
                'port' => '3306',
                'schema' => 'sistema_sine',
                'username' => 'root',
                'password' => ''
        ),'cron'=> array(
            'rfcfolder'=> 'SineFacturacion'
        ));
        
        $this->write_ini_file($sampleData, '../com.sine.dao/configuracion1.ini', true);
        $this->write_ini_file($sampleData, '../cron/configuracion.ini', true);
    }

    private function write_ini_file($assoc_arr, $path, $has_sections = FALSE) {
        $content = "";
        if ($has_sections) {
            foreach ($assoc_arr as $key => $elem) {
                $content .= "[" . $key . "]\n";
                foreach ($elem as $key2 => $elem2) {
                    if (is_array($elem2)) {
                        for ($i = 0; $i < count($elem2); $i++) {
                            $content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
                        }
                    } else if ($elem2 == "")
                        $content .= $key2 . " = \n";
                    else
                        $content .= $key2 . " = \"" . $elem2 . "\"\n";
                }
            }
        }
        else {
            foreach ($assoc_arr as $key => $elem) {
                if (is_array($elem)) {
                    for ($i = 0; $i < count($elem); $i++) {
                        $content .= $key . "[] = \"" . $elem[$i] . "\"\n";
                    }
                } else if ($elem == "")
                    $content .= $key . " = \n";
                else
                    $content .= $key . " = \"" . $elem . "\"\n";
            }
        }

        if (!$handle = fopen($path, 'w')) {
            return false;
        }

        $success = fwrite($handle, $content);
        fclose($handle);

        return $success;
    }

    public function loadSQL() {
        /* $consulta = "Create database qikbd;";
          $con = new Consultas();
          $insertado = $con->execute($consulta, null); */

        $servidor = "localhost";
        $basedatos = "qikbd";
        $puerto = "3306";
        $mysql_user = "root";
        $mysql_password = "";

        $db = new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos", $mysql_user, $mysql_password);

        /* $tablequery = file_get_contents("../../queries/qikTables.sql");

          $stmttable = $db->prepare($tablequery);

          if ($stmttable->execute()) {
          echo "Success";
          } else {
          echo "Fail";
          } */

        $pass = sha1('holamundo');

        $userquery = "INSERT INTO `usuario` VALUES (:id, :nombre, :apaterno, :amaterno, :user, :pass, :mail, :phone, :phone2, :status, :tipo, :accesso, :date, :img); INSERT INTO `usuariopermiso` VALUES (null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');";

        $stmttable = $db->prepare($userquery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        if ($stmttable->execute(array(':id' => null,
                    ':nombre' => "Si",
                    ':apaterno' => 'red',
                    ':amaterno' => 'blue',
                    ':user' => 'user',
                    ':pass' => $pass,
                    ':mail' => 'ejemplo@ejemplo.com',
                    ':phone' => '4271221859',
                    ':phone2' => '4271221859',
                    ':status' => 'activo',
                    ':tipo' => '1',
                    ':accesso' => '1',
                    ':date' => '2021-03-31',
                    ':img' => 'img.jpg'))) {
            echo "Success";
        } else {
            echo "Fail";
        }

        //$loadprod = $this->loadProdServicios($servidor, $puerto, $basedatos, $mysql_user, $mysql_password);
    }

    private function loadProdServicios($servidor, $puerto, $basedatos, $mysql_user, $mysql_password) {
        $db = new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos", $mysql_user, $mysql_password);

        $query1 = file_get_contents("../../queries/prodserv1.sql");
        $stmt = $db->prepare($query1);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Fail";
        }

        $query2 = file_get_contents("../../queries/prodserv2.sql");
        $stmt = $db->prepare($query2);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Fail";
        }

        $query3 = file_get_contents("../../queries/prodserv3.sql");

        $stmt = $db->prepare($query3);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Fail";
        }

        $query4 = file_get_contents("../../queries/prodserv4.sql");

        $stmt = $db->prepare($query4);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Fail";
        }

        $query5 = file_get_contents("../../queries/prodserv5.sql");

        $stmt = $db->prepare($query5);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Fail";
        }
    }

    private function getSaldoAux() {
        $consultado = false;
        $consulta = "SELECT * FROM contador_timbres WHERE idtimbres=:id;";
        $consultas = new Consultas();
        $valores = array("id" => '1');
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }
    
    private function getTAcceso($uid){
        $tacceso = "";
        $datos = $this->getTAccesoAux($uid);
        foreach ($datos as $actual){
            $tacceso = $actual['acceso'];
        }
        return $tacceso;
    }
    
    private function getTAccesoAux($uid){
        $consultado = false;
        $consulta = "SELECT acceso FROM usuario WHERE idusuario=:uid;";
        $consultas = new Consultas();
        $val = array("uid" => $uid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getSaldo() {
        $datos = "";
        $saldo = $this->getSaldoAux();
        foreach ($saldo as $actual) {
            Session::start();
            $acceso = $this->getNombrePaquete($_SESSION[sha1("paquete")]);
            $comprados = $actual['timbresComprados'];
            $usados = $actual['timbresUtilizados'];
            $restantes = $actual['timbresRestantes'];
            
            $datos = "$acceso</tr>$comprados</tr>$usados</tr>$restantes";
        }
        return $datos;
    }
    
    private function getNombrePaquete($aid) {
        $paquete = "Prueba";
        $servidor = "localhost";
        $basedatos = "sineacceso";
        $puerto = "3306";
        $mysql_user = "root";
        $mysql_password = "";
        try {
            $db = new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos", $mysql_user, $mysql_password);
            $stmttable = $db->prepare("SELECT * FROM paquete WHERE idpaquete='$aid'");

            if ($stmttable->execute()) {
                $resultado = $stmttable->fetchAll(PDO::FETCH_ASSOC);
                foreach ($resultado as $actual) {
                    $paquete = $actual["nombre"];
                }
                return "$paquete";
            } else {
                return "0Error";
            }
        } catch (PDOException $ex) {
            echo '<e>No se puede conectar a la bd ' . $ex->getMessage();
        }
    }

    public function opcionesAno() {
        $anio_de_inicio = 2018;
        $fecha = getdate();
        $y = $fecha['year'];
        $r = "";
        foreach (range($anio_de_inicio, $y) as $x) {
            $r = $r . "<option id='ano" . $x . "' value='" . $x . "'>" . $x . "  " . "</option>";
        }
        return $r;
    }

    public function getDatos($y) {
        $datos = "";
        $totales = "";
        $cancelados = "";
        $sintimbre = "";
        $contador = 0;
        for ($i = 1; $i <= 12; $i++) {
            $m = $i;
            if ($m < 10) {
                $m = "0$m";
            }
            $con = "and status_pago!='3' and uuid!=''";
            $datosemitidos = $this->getDatosAux($y, $m, $con);
            $dato = "";
            foreach ($datosemitidos as $emi) {
                $dato = $emi['emitidas'];
            }
            if ($contador >= 1) {
                $datos .= "</tr>$dato";
            } else {
                $datos .= "$dato";
            }

            $total = $this->getTotales($y, $m);
            if ($contador >= 1) {
                $totales .= "</tr>" . bcdiv($total, '1', 2);
            } else {
                $totales .= bcdiv($total, '1', 2);
            }

            $con2 = "and status_pago = '3'";
            $datosCancelados = $this->getDatosAux($y, $m, $con2);
            $cancelado = "";
            foreach ($datosCancelados as $can) {
                $cancelado = $can['emitidas'];
            }
            if ($contador >= 1) {
                $cancelados .= "</tr>$cancelado";
            } else {
                $cancelados .= "$cancelado";
            }

            $con3 = "and uuid is null";
            $datosNtimbre = $this->getDatosAux($y, $m, $con3);
            $notimbre = "";
            foreach ($datosNtimbre as $st) {
                $notimbre = $st['emitidas'];
            }
            if ($contador >= 1) {
                $sintimbre .= "</tr>$notimbre";
            } else {
                $sintimbre .= "$notimbre";
            }

            $contador++;
        }
        return $datos . "<dataset>" . $totales . "<dataset>" . $cancelados . "<dataset>" . $sintimbre;
    }

    private function getDatosAux($y, $m, $con) {
        $consultado = false;
        $consulta = "select count(*) emitidas from datos_factura where fecha_creacion like '$y-$m%' $con;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getTotales($y, $m) {
        $total = 0;
        $gettotales = $this->getTotalesAux($y, $m);
        foreach ($gettotales as $actual) {
            $total += $this->totalDivisa($actual['total'], $actual['tcambio'], 1, $actual['id_moneda']);
        }
        return $total;
    }

    private function getTotalesAux($y, $m) {
        $consultado = false;
        $consulta = "select totalfactura total, tcambio, id_moneda from datos_factura where fecha_creacion like '$y-$m%';";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function totalDivisa($total, $tcambio, $monedaP, $monedaF) {
        if ($monedaP == $monedaF) {
            $OP = bcdiv($total, '1', 2);
        } else {
            $tcambio = 1 / $tcambio;
            if ($monedaP == '1') {
                $OP = bcdiv($total, '1', 2) / bcdiv($tcambio, '1', 6);
            } else if ($monedaP == '2') {
                if ($monedaF == '4') {
                    $tcambio = $this->getTipoCambio($monedaF, $monedaP);
                }
                $OP = bcdiv($total, '1', 2) * bcdiv($tcambio, '1', 6);
            } else if ($monedaP == '4') {
                if ($monedaF == '2') {
                    $tcambio = $this->getTipoCambio($monedaF, $monedaP);
                }
                $OP = bcdiv($total, '1', 2) * bcdiv($tcambio, '1', 6);
            }
        }
        return $OP;
    }

    private function getUsuarioAux($idusuario) {
        $consultado = false;
        $consulta = "select * from usuario where idusuario=:id;";
        $val = array("id" => $idusuario);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getFechaRegistro($idusuario) {
        $datos = "";
        $usuario = $this->getUsuarioAux($idusuario);
        foreach ($usuario as $actual) {
            $acceso = $actual['acceso'];
            $fecha = $actual['fecharegistro'];
            $datos .= "$acceso</tr>$fecha";
        }
        return $datos;
    }

    public function checkAcceso() {
        Session::start();
        $inter = false;
        $idusuario = $_SESSION[sha1("idusuario")];
        $data = $this->getFechaRegistro($idusuario);
        $div = explode("</tr>", $data);
        $acceso = $div[0];
        $fecha = $div[1];
        if ($acceso == '0') {
            $d = new DateTime(date('Y-m-d H:i:s'));
            $d2 = new DateTime($fecha);
            $intervalo = $d->diff($d2);
            if ($intervalo->format('%a') >= '15') {
                $inter = true;
                $numtimbres = $this->updateNumTimbres();
            }
        }
        return $inter;
    }
    
    private function updateNumTimbres(){
        $consulta = "UPDATE `contador_timbres` SET timbresComprados=:comprados, timbresRestantes=:restantes where idtimbres=:id;";
        $valores = array("id" => '1',
            "comprados" => '0',
            "restantes" => '0');
        $con = new Consultas();
        $insertado = $con->execute($consulta, $valores);
        return $insertado;
    }
    
    private function getNotificacionbyID($id) {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion where idnotificacion=:id;";
        $val = array("id" => $id);
        $c = new Consultas();
        $consultado = $c->getResults($consulta, $val);
        return $consultado;
    }
    
    public function getNotification($id){
        $datos = "";
        $not = $this->getNotificacionbyID($id);
        foreach($not as $actual){
            $idnot = $actual['idnotificacion'];
            $fecha = $actual['fechanot'];
            $hora = $actual['horanot'];
            $notificacion = $actual['notificacion'];
            $readed = $actual['readed'];
            $datos .= "$idnot</tr>$fecha</tr>$hora</tr>$notificacion</tr>$readed";
        }
        return $datos;
    }
    
    private function updateNotification($id){
        $consulta = "UPDATE `notificacion` SET readed=:readed where idnotificacion=:id;";
        $valores = array("id" => $id,
            "readed" => '1');
        $con = new Consultas();
        $insertado = $con->execute($consulta, $valores);
        return $insertado;
    }
    
    public function listNotificacion($id){
        $update = $this->updateNotification($id);
        
        $list = $this->getListNotificacion();
        
        return $list;
    }
    
    private function countNotificacionAux() {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion where readed=:readed;";
        $c = new Consultas();
        $val = array("readed" => '0');
        $consultado = $c->getResults($consulta, $val);
        return $consultado;
    }
    
    private function countNotificacion() {
        $count = 0;
        $notification = $this->countNotificacionAux();
        foreach ($notification as $actual){
            $count++;
        }
        return $count;
    }
    
    private function getNotificacionAux($con="") {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion order by idnotificacion desc $con;";
        $c = new Consultas();
        $consultado = $c->getResults($consulta, null);
        return $consultado;
    }
    
    private function getListNotificacion(){
        session_start();
        $idusuario = $_SESSION[sha1("idusuario")];
        $datos = "<corte><li><a class='notification-link' onclick='loadImgPerfil($idusuario)' data-toggle='modal' data-target='#modal-profile-img' title='Cambiar imagen de perfil'><span class='glyphicon glyphicon-user'></span> Cambiar imagen de perfil </a></li>";
        $count = 0;
        $num = $this->countNotificacion();
        $notificacion = $this->getNotificacionAux("limit 5");
        foreach ($notificacion as $actual){
            $id = $actual['idnotificacion'];
            $fecha = $actual['fechanot'];
            $hora = $actual['horanot'];
            $notificacion = $actual['notificacion'];
            $read = $actual['readed'];
            $unread = "";
            $marker = "";
            if($read == '0'){
                $unread = "not-unread";
                $marker = "class='alert-marker-active'";
            }
            $div = explode("-", $fecha);
            $mes = $this->translateMonth($div[1]);
            $date = $div[2]."/".$mes."/".$div[0];
            $notificacion = substr($notificacion, 0, 40);
            $msg = "$date $hora<br/> $notificacion...";
            
            $datos .= "<li><a data-toggle='modal' data-target='#modal-notification' onclick='getNotification($id)' class='notification-link $unread'><div $marker></div> $msg </a></li>";
            $count++;
        }
        if($count == 0){
            $datos .= "<li><a class='notification-link'>No hay notificaciones </a></li>";
        }
        $datos .= "<li><a class='notification-link'>Ver todas las notificaciones </a></li><corte>$num";
        return $datos;
    }
    
    private function translateMonth($m) {
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
            default :
                $mes = "";
                break;
        }
        return $mes;
    }
    
    private function getNumrowsAux() {
        $consultado = false;
        $consulta = "select count(idnotificacion) numrows FROM notificacion order by idnotificacion desc;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrows() {
        $numrows = 0;
        $rows = $this->getNumrowsAux();
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }
    
    public function listaServiciosHistorial($pag) {
        require_once '../com.sine.common/pagination.php';
        $datos = "<thead class='sin-paddding'>
            <tr>
                <th colspan='3'></th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        $numrows = $this->getNumrows();
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = 20;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $listanot = $this->getNotificacionAux($con);
        $finales = 0;
        foreach ($listanot as $actual) {
            $id = $actual['idnotificacion'];
            $fecha = $actual['fechanot'];
            $hora = $actual['horanot'];
            $notificacion = $actual['notificacion'];
            $read = $actual['readed'];
            $unread = "";
            $marker = "";
            if($read == '0'){
                $unread = "not-unread";
                $marker = "class='alert-marker-active'";
            }
            $div = explode("-", $fecha);
            $mes = $this->translateMonth($div[1]);
            $date = $div[2]."/".$mes."/".$div[0];
            
            $datos .= "
                    <tr class='table-row'>
                        <td>$date</td>
                        <td>$hora</td>
                        <td>$notificacion</td>
                    </tr>
                     ";
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarNotificaciones";
        if ($finales == 0) {
            $datos .= "<tr><td class='text-center' colspan='4'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='4'>Mostrando $inicios al $finales de $numrows registros " . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

	private function getUsuarioLoginAux() {
        Session::start();
        $consultado = false;
        $con = new Consultas();
        $consulta = 'SELECT * FROM usuario WHERE idusuario=:uid;';
        $val = array("uid" => $_SESSION[sha1('idusuario')]);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getUsuarioLogin() {
        $datos = "";
        $datos = $this->getUsuarioLoginAux();
        foreach ($datos as $actual) {
            $nombre = $actual['nombre'] . ' ' . $actual['apellido_paterno'] . ' ' . $actual['apellido_materno'];
            $telefono = $actual['telefono_fijo'];
            $correo = $actual['email'];
            $datos = "$nombre</tr>$telefono</tr>$correo";
        }
        return $datos;
    }

    private function getConfigMailAux() {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM correoenvio WHERE chuso1=:id;";
        $valores = array("id" => '1');
        $consultado = $consultas->getResults($consulta, $valores);
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

    public function sendMailSoporte($nombre, $telefono, $chwhats, $correo, $msg) {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();

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

            $mail->Subject = utf8_decode('Soporte Tecnico Q-ik');
            $mail->isHTML(true);
            $mail->Body = $this->bodyMail($nombre, $telefono, $chwhats, $correo, $msg);
            $mail->addAddress('dsedge23@gmail.com');

            if (!$mail->send()) {
                echo '0No se envio el mensaje';
                echo '0Mailer Error: ' . $mail->ErrorInfo;
            } else {
                return '1Se ha enviado la factura';
            }
        } else {
            return "0No se ha configurado un correo de envio para esta area";
        }
    }

    private function bodyMail($nombre, $telefono, $chwhats, $correo, $msg) {
        $archivo = "../com.sine.dao/configuracion.ini";
        $ajustes = parse_ini_file($archivo, true);
        if (!$ajustes) {
            throw new Exception("No se puede abrir el archivo " . $archivo);
        }
        $rfcuser = $ajustes['cron']['rfcfolder'];

        $txt = str_replace("<corte>", "</p><p style='font-size:18px; text-align: justify;'>", $msg);
        $whats = "";
        if ($chwhats == '1') {
            $whats = "(cuenta con Whatsapp)";
        }
        $message = "<html>
    <body>
        <table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0' style='border-radius: 25px;'>
            <tr>
                <td>
                    <table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; border-radius: 20px; background-color:#fff; font-family:sans-serif;'>
                        <thead>
                            <tr height='80'>
                                <th align='left' colspan='4' style='padding: 6px; background-color:#f5f5f5; border-radius: 20px; border-bottom:solid 1px #bdbdbd;' ><img src='https://q-ik.mx/Registro/img/LogoQik.png' height='100px'/></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr align='center' height='10' style='font-family:sans-serif; '>
                                <td style='background-color:#09096B; text-align:center; border-radius: 5px;'></td>
                            </tr>
                            <tr>
                                <td colspan='4' style='padding:15px;'>
                                    <h1>Solicitud de soporte tecnico</h1>
                                    <hr/>
                                    <p style='font-size:15px; text-align: justify;'><b>RFC registrado:</b> $rfcuser</p>
                                    <p style='font-size:15px; text-align: justify;'><b>Nombre del solicitante:</b> " . utf8_decode($nombre) . "</p>
                                    <p style='font-size:15px; text-align: justify;'><b>Correo de contacto:</b> " . utf8_decode($correo) . "</p>
                                    <p style='font-size:15px; text-align: justify;'><b>Telefono de contacto:</b> $telefono $whats</p>
                                    <p style='font-size:15px; text-align: justify;'><b>Solicitud:</b> </p>
                                    <p style='font-size:15px; text-align: justify;'>
                                        " . utf8_decode($msg) . "
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>";
        return $message;
    }

	public function firstSession() {
        Session::start();
        $uid = $_SESSION[sha1("idusuario")];
        $ft = $this->getuserFT($uid);
        if ($ft == '0') {
            $this->updateUserFtsession($uid);
        }
        return $ft;
    }

    private function getuserFT($uid) {
        $ft = 0;
        $datos = $this->getuserFTAux($uid);
        foreach ($datos as $actual) {
            $ft = $actual['firstsession'];
        }
        return $ft;
    }

    private function getuserFTAux($uid) {
        $con = new Consultas();
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE idusuario=:uid;";
        $val = array("uid" => $uid);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }
    
    private function updateUserFtsession($uid){
        $actualizado = false;
        $con = new Consultas();
        $consulta = "UPDATE `usuario` SET firstsession=:ft WHERE idusuario=:uid;";
        $val = array("uid" => $uid,
            "ft" => "1");
        $actualizado = $con->execute($consulta, $val);
        return $actualizado;
    }

}
