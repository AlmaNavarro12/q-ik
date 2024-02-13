<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';
require_once '../vendor/autoload.php';

date_default_timezone_set("America/Mexico_City");

use PHPMailer\PHPMailer\PHPMailer;
use Twilio\Rest\Client;

class ControladorInicio {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
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

    private function getSaldoAux() {
        $consultado = false;
        $consulta = "SELECT * FROM contador_timbres WHERE idtimbres=:id;";
        $valores = array("id" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getSaldo() {
        $datos = "";
        $saldo = $this->getSaldoAux();
        foreach ($saldo as $actual) {
            Session::start();
            $acceso = $this->getNombrePaquete($_SESSION[sha1("paquete")]);
            $datos = "$acceso</tr>{$actual['timbresComprados']}</tr>{$actual['timbresUtilizados']}</tr>{$actual['timbresRestantes']}";
        }
        return $datos;
    }
    
    private function  getNombrePaquete($aid) {
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
        $y = date('Y');
        $options = "";
    
        foreach (range($anio_de_inicio, $y) as $x) {
            $options .= "<option id='ano{$x}' value='{$x}'>{$x} </option>";
        }
        return $options;
    }
    
    public function getDatos($y) {
        $facturas = $this->getDatosFacturas($y);
        $cartas = $this->getDatosCartas($y);
        return "$facturas<datacfdi>$cartas";
    }

    private function getDatosCartas($y) {
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
            $con = "AND status_pago!='3' AND uuid!=''";
            $datosemitidos = $this->getDatosCartaAux($y, $m, $con);
            $dato = "";
            foreach ($datosemitidos as $emi) {
                $dato = $emi['emitidas'];
            }
            if ($contador >= 1) {
                $datos .= "</tr>$dato";
            } else {
                $datos .= "$dato";
            }

            $total = $this->getTotalesCarta($y, $m);
            if ($contador >= 1) {
                $totales .= "</tr>" . bcdiv($total, '1', 2);
            } else {
                $totales .= bcdiv($total, '1', 2);
            }

            $con2 = "AND status_pago = '3'";
            $datosCancelados = $this->getDatosCartaAux($y, $m, $con2);
            $cancelado = "";
            foreach ($datosCancelados as $can) {
                $cancelado = $can['emitidas'];
            }
            if ($contador >= 1) {
                $cancelados .= "</tr>$cancelado";
            } else {
                $cancelados .= "$cancelado";
            }

            $con3 = "AND uuid IS NULL";
            $datosNtimbre = $this->getDatosCartaAux($y, $m, $con3);
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

    private function getDatosCartaAux($y, $m, $con) {
        $consultado = false;
        $consulta = "SELECT count(*) emitidas FROM factura_carta WHERE fecha_creacion LIKE '$y-$m%' $con;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }
    
    private function getDatosFacturas($y) {
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
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getTotales($y, $m) {
        $total = 0;
        $gettotales = $this->getTotalesAux($y, $m);
        foreach ($gettotales as $actual) {
            //$total += $this->totalDivisa($actual['total'], $actual['tcambio'], 1, $actual['id_moneda']);
        }
        return $total;
    }

    private function getTotalesAux($y, $m) {
        $consultado = false;
        $consulta = "select totalfactura total, tcambio, id_moneda from datos_factura where fecha_creacion like '$y-$m%';";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getTotalesCarta($y, $m) {
        $total = 0;
        $gettotales = $this->getTotalesCartaAux($y, $m);
        foreach ($gettotales as $actual) {
            $total += $this->totalDivisa($actual['total'], $actual['tcambio'], 1, $actual['id_moneda']);
        }
        return $total;
    }

    private function getTotalesCartaAux($y, $m) {
        $consultado = false;
        $consulta = "SELECT totalfactura total, tcambio, id_moneda FROM factura_carta WHERE fecha_creacion LIKE '$y-$m%';";
        $consultado = $this->consultas->getResults($consulta, null);
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
                    //$tcambio = $this->getTipoCambio($monedaF, $monedaP);
                }
                $OP = bcdiv($total, '1', 2) * bcdiv($tcambio, '1', 6);
            } else if ($monedaP == '4') {
                if ($monedaF == '2') {
                    //$tcambio = $this->getTipoCambio($monedaF, $monedaP);
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
        $consultado = $this->consultas->getResults($consulta, $val);
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
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
    
    private function getNotificacionbyID($id) {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion where idnotificacion=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }
    
    public function getNotification($id){
        $datos = "";
        $not = $this->getNotificacionbyID($id);
    
        foreach($not as $actual){
            $datos .= implode('</tr>', $actual) . '</tr>';
        }
        return $datos;
    }
    
    private function updateNotification($id){
        $consulta = "UPDATE `notificacion` SET readed=:readed where idnotificacion=:id;";
        $valores = array("id" => $id,
            "readed" => '1');
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
    
    public function listNotificacion($id){
        $update = $this->updateNotification($id);
        $list = $this->getListNotificacion();
        return $list;
    }
    
    private function getNotificacionAux($con="") {
        $consultado = false;
        $consulta = "SELECT * FROM notificacion order by idnotificacion desc $con;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }
    
    private function getListNotificacion(){
        session_start();
        $idusuario = $_SESSION[sha1("idusuario")];
        $datos = "<corte><li><a class='notification-link' onclick='loadImgPerfil($idusuario)' data-toggle='modal' data-target='#modal-profile-img' title='Cambiar imagen de perfil'><span class='glyphicon glyphicon-user'></span> Cambiar imagen de perfil </a></li>";
    
        $notificaciones = $this->getNotificacionAux("limit 5");
        $count = count($notificaciones);
        
        foreach ($notificaciones as $actual){
            $id = $actual['idnotificacion'];
            $fecha = $this->formatFecha($actual['fechanot']);
            $hora = $actual['horanot'];
            $notificacion = substr($actual['notificacion'], 0, 40);
    
            $unread = ($actual['readed'] == '0') ? "not-unread" : "";
            $marker = ($unread) ? "class='alert-marker-active'" : "";
    
            $msg = "<span class='mt-0 mx-0 px-0'>$fecha $hora <br> $notificacion... </span>";
            $datos .= "<li class='p-2 py-2 $unread'><a data-bs-toggle='modal' data-bs-target='#modal-notification' onclick='getNotification($id)' class='notification-link px-0'> <div $marker></div> $msg </a></li>";
        }
    
        $datos .= ($count == 0) ? "<li><a class='notification-link'>No hay notificaciones </a></li>" : "";
        $datos .= "<li><a class='notification-link'>Ver todas las notificaciones </a></li><corte>$count";
        
        return $datos;
    }
    
    private function formatFecha($fecha) {
        $div = explode("-", $fecha);
        $mes = $this->translateMonth($div[1]);
        return $div[2] . "/" . $mes . "/" . $div[0];
    }

    private function translateMonth($m)
    {
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $m = intval($m);
        return (array_key_exists($m - 1, $months)) ? $months[$m - 1] : "";
    }

    private function getNumrowsAux() {
        $consultado = false;
        $consulta = "select count(idnotificacion) numrows FROM notificacion order by idnotificacion desc;";
        $consultado = $this->consultas->getResults($consulta, null);
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
                <th class='ps-4'>FECHA</th>
                <th>HORA</th>
                <th>NOTIFICACIÓN</th>
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
            $div = explode("-", $fecha);
            $mes = $this->translateMonth($div[1]);
            $date = $div[2]."/".$mes."/".$div[0];
            
            $datos .= "
                    <tr class='table-row'>
                        <td class='ps-4'>$date</td>
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
        $consulta = 'SELECT * FROM usuario WHERE idusuario=:uid;';
        $val = array("uid" => $_SESSION[sha1('idusuario')]);
        $consultado = $this->consultas->getResults($consulta, $val);
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

            $mail->Subject = iconv('UTF-8', 'windows-1252', 'Soporte Técnico Q-ik'); 
            $mail->isHTML(true);
            $mail->Body = $this->bodyMail($nombre, $telefono, $chwhats, $correo, $msg);
            $mail->addAddress('dsedge23@gmail.com');

            if (!$mail->send()) {
                echo '0No se envio el mensaje.';
                echo '0Mailer Error: ' . $mail->ErrorInfo;
            } else {
                return '1Se ha enviado la factura.';
            }
        } else {
            return "0No se ha configurado un correo de envío para esta área.";
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
                                            <h1>Solicitud de soporte técnico</h1>
                                            <hr/>
                                            <p style='font-size:15px; text-align: justify;'><b>RFC registrado:</b> $rfcuser</p>
                                            <p style='font-size:15px; text-align: justify;'><b>Nombre del solicitante:</b> " . iconv('UTF-8', 'windows-1252', $nombre) . "</p>
                                            <p style='font-size:15px; text-align: justify;'><b>Correo de contacto:</b> " . iconv('UTF-8', 'windows-1252', $correo) . "</p>
                                            <p style='font-size:15px; text-align: justify;'><b>Teléfono de contacto:</b> $telefono $whats</p>
                                            <p style='font-size:15px; text-align: justify;'><b>Solicitud:</b> </p>
                                            <p style='font-size:15px; text-align: justify;'>
                                                " .  iconv('UTF-8', 'windows-1252', $msg)  . "
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
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE idusuario=:uid;";
        $val = array("uid" => $uid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }
    
    private function updateUserFtsession($uid){
        $actualizado = false;
        $consulta = "UPDATE `usuario` SET firstsession=:ft WHERE idusuario=:uid;";
        $val = array("uid" => $uid,
            "ft" => "1");
        $actualizado = $this->consultas->execute($consulta, $val);
        return $actualizado;
    }
}