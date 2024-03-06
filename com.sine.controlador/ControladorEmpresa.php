<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.dao/ConsultasSine.php';

date_default_timezone_set("America/Mexico_City");

class ControladorEmpresa {
    private $consultas;
    private $consultasSine;

    function __construct() {
        
        $this->consultasSine = new ConsultasSine();
        $this->consultas = new Consultas();
        
    }

    private function getFirmaEmpresaB64($folder, $fn) {
        $src = '../temporal/' . $folder . '/' . $fn;
        $type = pathinfo($src, PATHINFO_EXTENSION);
        $data = file_get_contents($src);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        //unlink($src);
        return $base64;
    }

    public function getFirmaEmpresa() {
        $datos = $this->getFirmas();
        foreach ($datos as $actual) {
            $id = $actual['id_datos'];
            $firma = $actual['firma'];
            $rfc = $actual['rfc'];
            $img = $this->getFirmaEmpresaB64($rfc, $firma);

            $insertado = false;
            $consulta = "UPDATE `datos_facturacion` set firma=:firma WHERE id_datos=:id;);";
            $valores = array("firma" => $img,
                "id" => $id);
            $insertado = $this->consultas->execute($consulta, $valores);
        }
        return $insertado;
    }

    private function getFirmas() {
        $consultado = false;
        $consulta = "SELECT * from datos_facturacion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "select count(*) numrows from datos_facturacion as s inner join municipio as m on (s.idmunicipio=m.id_municipio) inner join estado as e on (s.idestado=e.id_estado) $condicion;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
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

    public function getEmpresa($condicion) {
        $consultado = false;
        $c = new Consultas();
        $consulta = "SELECT * FROM datos_facturacion s  $condicion;";
        $consultado = $c->getResults($consulta, null);
        return $consultado;
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso p where permiso_idusuario=:idusuario;";
        $c = new Consultas();
        $valores = array("idusuario" => $idusuario);
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listadatos'];
            $editar = $actual['editardatos'];
            $eliminar = $actual['eliminardatos'];
            $descargar = $actual['descargardatos'];

            $datos .= "$lista</tr>$editar</tr>$eliminar</tr>$descargar";
        }
        return $datos;
    }

    public function listasEmpresa($nom, $numreg, $pag) {
        include '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
            <tr >
                <th></th>
                <th class='col-md-2 text-start'>Contribuyente</th>
                <th>RFC </th>
                <th class='col-md-2'>Razón social</th>
                <th>Dirección</th>
                <th>Régimen fiscal</th>
                <th>Opción</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($nom == "") {
            $condicion = "ORDER BY s.nombre_contribuyente";
        } else {
            $condicion = "WHERE (s.nombre_contribuyente LIKE '%$nom%') OR (s.rfc LIKE '%$nom%') ORDER BY s.nombre_contribuyente";
        }

        $permisos = $this->getPermisos($idlogin);
        $divp = explode("</tr>", $permisos);

        if ($divp[0] == '1') {
            $numrows = $this->getNumrows($condicion);
            $page = (isset($pag) && !empty($pag)) ? $pag : 1;
            $per_page = $numreg;
            $adjacents = 4;
            $offset = ($page - 1) * $per_page;
            $total_pages = ceil($numrows / $per_page);
            $con = $condicion . " LIMIT $offset,$per_page ";
            $empresa = $this->getEmpresa($con);
            $finales = 0;
            foreach ($empresa as $datossine) {
                $id = $datossine['id_datos'];
                $nombre = $datossine['nombre_contribuyente'];
                $rfc = $datossine['rfc'];
                $razonsocial = $datossine['razon_social'];
                $calle = $datossine['calle'];
                $numero_interior = $datossine['numero_interior'];
                $numero_exterior = $datossine['numero_exterior'];
                $colonia = $datossine['colonia'];
                $municipio = $datossine['municipio'];
                $estado = $datossine['estado'];
                $pais = $datossine['pais'];
                $codigo_postal = $datossine['codigo_postal'];
                $regimenfiscal = $datossine['regimen_fiscal'];
                $color = $datossine['color'];
                $direccion = "$calle #$numero_exterior, Col. $colonia,";

                $datos .= "<tr>
                         <td style='background-color: $color;'></td>
                         <td>$nombre</td>
                         <td>$rfc</td>
                         <td>$razonsocial</td>
                         <td>$direccion $codigo_postal, $municipio, $estado, $pais</td>
                         <td>$regimenfiscal</td>
                         <td align='center'>
                         <div class='dropdown'>
                            <button class='button-list dropdown-bs-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu dropdown-menu-right'>";
                if ($divp[3] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"descargarArchivos($id);\">Desc. Archivos <span class='text-muted small fas fa-download'></span></a></li>";
                }
                if ($divp[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarEmpresa($id)'>Editar Datos <span class='text-muted small far fa-edit'></span></a></li>";
                }
                if ($divp[2] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarEmpresa($id)'>Eliminar Datos <span class='text-muted small fas fa-trash-alt'></span></a></li>";
                }
                $datos .= "</ul>
                            </div>
                         </td>
                    </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarEmpresa";
            if ($finales == 0) {
                $datos .= "<tr><td colspan='11'>No se encontraron registros</td></tr>";
            }
            $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='4'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        }
        return $datos;
    }

    public function getEmpresaById($idempresa) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:idempresa;";
        $c = new Consultas();
        $valores = array("idempresa" => $idempresa);
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosEmpresa($idempresa) {
        $sine = $this->getEmpresaById($idempresa);
        $datos = "";
        foreach ($sine as $datossine) {
            $id = $datossine['id_datos'];
            $nombreempresa = $datossine['nombre_contribuyente'];
            $rfc = $datossine['rfc'];
            $razon = $datossine['razon_social'];
            $color = $datossine['color'];
            $calle = $datossine['calle'];
            $num_interior = $datossine['numero_interior'];
            $num_exterior = $datossine['numero_exterior'];
            $colonia = $datossine['colonia'];
            $idmunicipio = $datossine['idmunicipio'];
            $idestado = $datossine['idestado'];
            $pais = $datossine['pais'];
            $cp = $datossine['codigo_postal'];
            $folio_fiscal = $datossine['c_regimenfiscal'];
            $regimen = $datossine['regimen_fiscal'];
            $correo = $datossine['correodatos'];
            $telefono = $datossine['telefono'];
            $idbanco = $datossine['idbanco'];
            $sucursal = $datossine['sucursal'];
            $cuenta = $datossine['cuenta'];
            $clabe = $datossine['clabe'];
            $oxxo = $datossine['tarjetaoxxo'];
            $idbanco1 = $datossine['idbanco1'];
            $sucursal1 = $datossine['sucursal1'];
            $cuenta1 = $datossine['cuenta1'];
            $clabe1 = $datossine['clabe1'];
            $oxxo1 = $datossine['tarjetaoxxo1'];
            $idbanco2 = $datossine['idbanco2'];
            $sucursal2 = $datossine['sucursal2'];
            $cuenta2 = $datossine['cuenta2'];
            $clabe2 = $datossine['clabe2'];
            $oxxo2 = $datossine['tarjetaoxxo2'];
            $idbanco3 = $datossine['idbanco3'];
            $sucursal3 = $datossine['sucursal3'];
            $cuenta3 = $datossine['cuenta3'];
            $clabe3 = $datossine['clabe3'];
            $oxxo3 = $datossine['tarjetaoxxo3'];
            $firma = $datossine['firma'];
            $datos = "$id</tr>$nombreempresa</tr>$rfc</tr>$razon</tr>$calle</tr>$num_interior</tr>$num_exterior</tr>$colonia</tr>$pais</tr>$cp</tr>$folio_fiscal</tr>$regimen</tr>$idmunicipio</tr>$idestado</tr>$idbanco</tr>$sucursal</tr>$cuenta</tr>$clabe</tr>$oxxo</tr>$idbanco1</tr>$sucursal1</tr>$cuenta1</tr>$clabe1</tr>$oxxo1</tr>$idbanco2</tr>$sucursal2</tr>$cuenta2</tr>$clabe2</tr>$oxxo2</tr>$idbanco3</tr>$sucursal3</tr>$cuenta3</tr>$clabe3</tr>$oxxo3</tr>$firma</tr>$color</tr>$correo</tr>$telefono";
            break;
        }
        return $datos;
    }

    public function getNombreBanco($idbanco) {
        $banco = $this->getNombreBancoAux($idbanco);
        $nombre = "";
        foreach ($banco as $actual) {
            $nombre = $actual['nombre_banco'];
        }
        return $nombre;
    }

    public function getNombreBancoAux($idbanco) {
        $consultado = false;
        $consulta = "select nombre_banco from catalogo_banco where idcatalogo_banco='$idbanco';";
        $c = new Consultas();
        $consultado = $c->getResults($consulta, null);
        return $consultado;
    }

    private function crearFirma($firma, $rfc) {
        $f = getdate();
        $d = $f['mday'];
        $m = $f['mon'];
        $y = $f['year'];
        $h = $f['hours'];
        $mi = $f['minutes'];
        $s = $f['seconds'];
        $hoy = $y . '.' . $m . '.' . $d . '.' . $h . '.' . $mi . '.' . $s;

        $firma = str_replace('data:image/png;base64,', '', $firma);
        $firma = str_replace(' ', '+', $firma);
        $fileData = base64_decode($firma);
        $fileName = 'firma' . $hoy . '.png';
        file_put_contents("../temporal/" . $rfc . "/" . $fileName, $fileData);
        return $fileName;
    }

    public function getError($c) {
        $carpeta = '../temporal/' . $c->getRfc() . "/";
        $key = $carpeta . 'key.key';
        $keypem = $carpeta . 'keyPEM.pem';
        $shellConvertKey = "openssl pkcs8 -inform DER -in $key -out $keypem -passin pass:" . $c->getPasscsd() . " 2>&1";
        $exec = shell_exec($shellConvertKey);
        $exec = substr($exec, 0, 40);
        return $exec;
    }

    public function saveDatos($c) {
        $datos = "";
        $check = $this->getError($c);
        if ($check != "") {
            $datos = "0Error la contraseñaaaa es incorrecta " . $check;
        } else {
            $datos = $this->insertarDatos($c);
        }
        return $datos;
    }

    private function insertarDatos($c) {
        $diferencias = $this->getZonaHoraria($c->getCp());
        $div = explode("</tr>", $diferencias);
        $insertado = false;
        $consulta = "INSERT INTO `datos_facturacion` VALUES (:id, :nombre, :rfc, :razon, :color, :calle, :numint, :numext, :colonia, :idmunicipio, :idestado, :estado, :municipio, :pais, :cp, :clave, :regimen, :correo, :telefono, :keyb64, :csd, :numcsd, :passcsd, :idbanco, :sucursal, :cuenta, :clabe, :oxxo, :idbanco1, :sucursal1, :cuenta1, :clabe1, :oxxo1, :idbanco2, :sucursal2, :cuenta2, :clabe2, :oxxo2, :idbanco3, :sucursal3, :cuenta3, :clabe3, :oxxo3, :firma, :difhorarioverano, :difhorarioinvierno) ;";
        $valores = array("id" => null,
            "nombre" => $c->getNombreEmpresa(),
            "rfc" => $c->getRfc(),
            "razon" => $c->getRazonSocial(),
            "color" => $c->getColor(),
            "calle" => $c->getCalle(),
            "numint" => $c->getNumint(),
            "numext" => $c->getNumext(),
            "colonia" => $c->getColonia(),
            "idmunicipio" => $c->getMunicipio(),
            "idestado" => $c->getEstado(),
            "estado"=> $c->getNombreEstado(),
            "municipio"=> $c->getNombreMunicipio(),
            "pais" => $c->getPais(),
            "cp" => $c->getCp(),
            "clave" => $c->getFolioFiscal(),
            "regimen" => $c->getRegimenFiscal(),
            "correo" => $c->getCorreo(),
            "telefono" => $c->getTelefono(),
            "keyb64" => $c->getKeyB64(),
            "csd" => $c->getCsd(),
            "numcsd" => $c->getNumcert(),
            "passcsd" => $c->getPasscsd(),
            "idbanco" => $c->getIdbanco(),
            "sucursal" => $c->getSucursal(),
            "cuenta" => $c->getCuenta(),
            "clabe" => $c->getClabe(),
            "oxxo" => $c->getOxxo(),
            "idbanco1" => $c->getIdbanco1(),
            "sucursal1" => $c->getSucursal1(),
            "cuenta1" => $c->getCuenta1(),
            "clabe1" => $c->getClabe1(),
            "oxxo1" => $c->getOxxo1(),
            "idbanco2" => $c->getIdbanco2(),
            "sucursal2" => $c->getSucursal2(),
            "cuenta2" => $c->getCuenta2(),
            "clabe2" => $c->getClabe2(),
            "oxxo2" => $c->getOxxo2(),
            "idbanco3" => $c->getIdbanco3(),
            "sucursal3" => $c->getSucursal3(),
            "cuenta3" => $c->getCuenta3(),
            "clabe3" => $c->getClabe3(),
            "oxxo3" => $c->getOxxo3(),
            "firma" => $c->getFirma(),
            "difhorarioverano" => $div[0],
            "difhorarioinvierno" => $div[1]);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
   
    public function getZonaHoraria($codpostal) {
        $dif = "0No";
        $servidor = "localhost";
        $basedatos = "sineacceso";
        $puerto = "3306";
        $mysql_user = "root";
        $mysql_password = "";//S1ne15QvikXJWc

        try {
            $db = new PDO("mysql:host=$servidor;port=$puerto;dbname=$basedatos", $mysql_user, $mysql_password);
            $stmttable = $db->prepare("SELECT * FROM catalogo_codpostal WHERE c_CodigoPostal='$codpostal'");
            $hoy = date('Y-m-d');
            $div = explode("-", $hoy);

            if ($stmttable->execute()) {
                $resultado = $stmttable->fetchAll(PDO::FETCH_ASSOC);
                foreach ($resultado as $actual) {
                    $dif = $actual["Diferencia_Horaria_Verano"] . "</tr>" . $actual["Diferencia_Horaria_Invierno"];
                }
                return "$dif";
            } else {
                return "0";
            }
        } catch (PDOException $ex) {
            echo '<e>No se puede conectar a la bd ' . $ex->getMessage();
        }
    }

    public function actualizarDatos($c) {
        $datos = "";
        if ($c->getKeyB64() != "") {
            $check = $this->getError($c);
            if ($check != "") {
                $datos = "0Error la contraseña es incorrecta " . $check;
            } else {
                $datos = $this->modificarEmpresa($c);
            }
        } else {
            $datos = $this->modificarEmpresa($c);
        }

        return $datos;
    }

    public function modificarEmpresa($c) {
        $diferencias = $this->getZonaHoraria($c->getCp());
        $div = explode("</tr>", $diferencias);

        $firma = $c->getFirma();
        if ($firma == "empty") {
            $firma = $c->getFirmaanterior();
        }

        $key = $c->getKeyB64();
        if ($key == "") {
            $key = $this->getKeyPrevio($c->getIdempresa());
        }

        $csd = $c->getCsd();
        if ($csd == "") {
            $csd = $this->getCSDPrevio($c->getIdempresa());
        }

        $numcsd = $c->getNumcert();
        if ($numcsd == "") {
            $numcsd = $this->getNumCSDPrevio($c->getIdempresa());
        }

        $passcsd = "";
        if ($c->getPasscsd() != "") {
            $passcsd = " passcsd=:passcsd,";
        }

        $insertado = false;
        $consulta = "UPDATE `datos_facturacion` SET nombre_contribuyente=:nombre, rfc=:rfc, razon_social=:razon, color=:color, calle=:calle, numero_interior=:numint, numero_exterior=:numext, colonia=:colonia, idmunicipio=:idmunicipio, idestado=:idestado, estado=:estado, municipio=:municipio, pais=:pais, codigo_postal=:cp, c_regimenfiscal=:folio, regimen_fiscal=:regimen, correodatos=:correo, telefono=:telefono, keyb64=:keyb64, csd=:csd, numcsd=:numcsd,$passcsd idbanco=:idbanco, sucursal=:sucursal, cuenta=:cuenta, clabe=:clabe, tarjetaoxxo=:oxxo, idbanco1=:idbanco1, sucursal1=:sucursal1, cuenta1=:cuenta1, clabe1=:clabe1, tarjetaoxxo1=:oxxo1, idbanco2=:idbanco2, sucursal2=:sucursal2, cuenta2=:cuenta2, clabe2=:clabe2, tarjetaoxxo2=:oxxo2, idbanco3=:idbanco3, sucursal3=:sucursal3, cuenta3=:cuenta3, clabe3=:clabe3, tarjetaoxxo3=:oxxo3, firma=:firma, difhorarioverano=:difverano, difhorarioinvierno=:difinvierno WHERE id_datos=:id;);";
        $valores = array("nombre" => $c->getNombreEmpresa(),
            "rfc" => $c->getRfc(),
            "razon" => $c->getRazonSocial(),
            "color" => $c->getColor(),
            "calle" => $c->getCalle(),
            "numint" => $c->getNumint(),
            "numext" => $c->getNumext(),
            "colonia" => $c->getColonia(),
            "idmunicipio" => $c->getMunicipio(),
            "idestado" => $c->getEstado(),
            "estado"=> $c->getNombreEstado(),
            "municipio"=> $c->getNombreMunicipio(),
            "pais" => $c->getPais(),
            "cp" => $c->getCp(),
            "folio" => $c->getFolioFiscal(),
            "regimen" => $c->getRegimenFiscal(),
            "correo" => $c->getCorreo(),
            "telefono" => $c->getTelefono(),
            "keyb64" => $key,
            "csd" => $csd,
            "numcsd" => $numcsd,
            "passcsd" => $c->getPasscsd(),
            "idbanco" => $c->getIdbanco(),
            "sucursal" => $c->getSucursal(),
            "cuenta" => $c->getCuenta(),
            "clabe" => $c->getClabe(),
            "oxxo" => $c->getOxxo(),
            "idbanco1" => $c->getIdbanco1(),
            "sucursal1" => $c->getSucursal1(),
            "cuenta1" => $c->getCuenta1(),
            "clabe1" => $c->getClabe1(),
            "oxxo1" => $c->getOxxo1(),
            "idbanco2" => $c->getIdbanco2(),
            "sucursal2" => $c->getSucursal2(),
            "cuenta2" => $c->getCuenta2(),
            "clabe2" => $c->getClabe2(),
            "oxxo2" => $c->getOxxo2(),
            "idbanco3" => $c->getIdbanco3(),
            "sucursal3" => $c->getSucursal3(),
            "cuenta3" => $c->getCuenta3(),
            "clabe3" => $c->getClabe3(),
            "oxxo3" => $c->getOxxo3(),
            "firma" => $firma,
            "difverano" => $div[0],
            "difinvierno" => $div[1],
            "id" => $c->getIdempresa());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function moverArchivos($vista1, $vista2) {
        $carpeta1 = "../temporal/$vista1/";
        $carpeta2 = "../temporal/$vista2/";
        $files = scandir($carpeta1);
        foreach ($files as $fname) {
            if ($fname != '.' && $fname != '..') {
                rename($carpeta1 . $fname, $carpeta2 . $fname);
            }
        }
    }

    private function getCSDPrevio($id) {
        $csd = "";
        $getcsd = $this->getCSDPrevioAux($id);
        foreach ($getcsd as $actual) {
            $csd = $actual['csd'];
        }
        return $csd;
    }

    private function getNumCSDPrevio($id) {
        $csd = "";
        $getcsd = $this->getCSDPrevioAux($id);
        foreach ($getcsd as $actual) {
            $csd = $actual['numcsd'];
        }
        return $csd;
    }

    private function getKeyPrevio($id) {
        $csd = "";
        $getcsd = $this->getCSDPrevioAux($id);
        foreach ($getcsd as $actual) {
            $csd = $actual['keyb64'];
        }
        return $csd;
    }

    private function getRFCPrevio($id) {
        $csd = "";
        $getcsd = $this->getCSDPrevioAux($id);
        foreach ($getcsd as $actual) {
            $csd = $actual['rfc'];
        }
        return $csd;
    }

    private function getCSDPrevioAux($id) {
        $consultado = false;
        $consulta = "SELECT csd,numcsd,keyb64,rfc FROM datos_facturacion where id_datos=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function generarCSDPEM($rfc) {
        $carpeta = '../temporal/' . $rfc . '/';
        $cerpem = $carpeta . $rfc . 'csd.pem';
        $csd = $carpeta . $rfc . '.cer';
        $certificateCAcerContent = file_get_contents($csd);
        /* Convert .cer to .pem, cURL uses .pem */
        $certificateCApemContent = '-----BEGIN CERTIFICATE-----' . PHP_EOL
                . chunk_split(base64_encode($certificateCAcerContent), 64, PHP_EOL)
                . '-----END CERTIFICATE-----' . PHP_EOL;
        $certificateCApem = $cerpem;
        file_put_contents($certificateCApem, $certificateCApemContent);
    }

    public function generarKEYPEM($rfc) {
        $carpeta = '../temporal/' . $rfc . '/';
        $key = $carpeta . $rfc . '.key';
        $keypem = $carpeta . $rfc . 'key.pem';
        $private_key = file_get_contents($key);
        file_put_contents($keypem, $this->DerToPem($private_key, 'PRIVATE KEY'));
    }

    function DerToPem($Der, $Private = false) {
        //Encode:
        $Der = base64_encode($Der);
        //Split lines:
        $lines = str_split($Der, 65);
        $body = implode("\n", $lines);
        //Get title:
        $title = $Private ? 'PRIVATE KEY' : 'PUBLIC KEY';
        //Add wrapping:
        $result = "-----BEGIN {$title}-----\n";
        $result .= $body . "\n";
        $result .= "-----END {$title}-----\n";

        return $result;
    }

    public function getSevicios($condicion) {
        $consultado = false;
        $consulta = "SELECT s.ID,s.NoTelefono,s.PaginaWeb,v.RazonSocial,v.Rfc,di.Calle,di.Numero,di.Colonia,di.Localidad,di.Cp,di.Pais,m.municipio,e.estado 
            FROM datosdelaempresa AS s INNER JOIN datosfiscales AS v  INNER JOIN direcciones as di INNER JOIN municipio as m INNER JOIN estado as e on(v.ClaveUnicaDF=s.DatosFiscales_ClaveUnicaDF) and ( v.Direcciones_IDdir=di.IDdir ) and ( m.id_municipio=di.Municipio_id_municipio) 
            and ( e.id_estado=di.Estado_id_estado)$condicion;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function listaServiciosHistorial($RAZ, $RF) {
        $datos = "";
        $condicionfolio = "";
        if ($RAZ == "" && $RF == "") {
            $condicionfolio = " ORDER BY v.RazonSocial DESC LIMIT 150";
        } else {
            //$condicionfolio="AND  nombre LIKE '%$Nombr%' AND (d.RazonSocial LIKE '%$Razon%' OR d.Rfc LIKE '%$Razon%')  ORDER BY nombre DESC";
            $condicionfolio = "AND  v.RazonSocial LIKE '%$RAZ%' AND (v.Rfc LIKE '%$RF%')  ORDER BY v.RazonSocial DESC";
        }
        $condicion = "WHERE  v.RazonSocial!='2-Esperando Servicio' AND v.Rfc!='2-Esperando Servicio' $condicionfolio";
        //$condicion="";
        $destinatarios = $this->getSevicios($condicion);
        $contador = 0;
        foreach ($destinatarios as $destinatarioactual) {
            // $id_servicio=$servicioactual['id_servicio'];
            $ID = $destinatarioactual['ID'];
            $NoTelefono = $destinatarioactual['NoTelefono'];
            $PaginaWeb = $destinatarioactual['PaginaWeb'];
            $Rfc = $destinatarioactual['Rfc'];
            $RazonSocial = $destinatarioactual['RazonSocial'];
            $Calle = $destinatarioactual['Calle'];
            $Numero = $destinatarioactual['Numero'];
            $Colonia = $destinatarioactual['Colonia'];
            $Localidad = $destinatarioactual['Localidad'];
            $estado = $destinatarioactual['estado'];
            $municipio = $destinatarioactual['municipio'];
            $Direccion = "$Calle #$Numero, $Colonia, $Localidad, $municipio, $estado";

            //$Contratistas="$Nombre,$ApellidoPaterno,$ApellidoMaterno";
            //   $id_valoracion=$servicioactual['id_valoracion'];
            // $folio=$servicioactual['folio'];
            $datos .= "
                    <tr>
                        <td id='a$ID-b0'>$NoTelefono</td>
                        <td id='a$ID-b1' class='cel-content-input' ondblclick=\"editarCampo(1,'textarea',$ID, '$PaginaWeb');\" >$PaginaWeb</td>
                        <td id='a$ID-b2' class='cel-content-input' ondblclick=\"editarCampo(2,'textarea',$ID, '$RazonSocial');\" >$RazonSocial</td>
                        <td id='a$ID-b3' class='cel-content-input' ondblclick=\"editarCampo(3,'textarea',$ID, '$Rfc');\" >$Rfc </td>
                        <td id='a$ID-b4' class='cel-content-input' ondblclick=\"editarCampo(4,'textarea',$ID, '$Direccion');\" >$Direccion</td>
                      
                       <td><button class='btn btn-xs btn-primary' onclick='editarEmpresa($ID)' title='Editar este Camion'><span class='glyphicon glyphicon-edit'></span></button></td>
                       <td><button class='btn btn-xs btn-danger' onclick='eliminarEmpresa($ID)' title='Eliminar este Camion'><span class='glyphicon glyphicon-remove'></span></button></td>
                    </tr>
                     ";
            $contador++;
        }
        if ($contador == 0) {
            $datos = "<tr><td class='text-center' colspan='11'>No se encontraron registros</td></tr>";
        }
        return $datos;
    }

    private function getRFCEmpresa($did) {
        $rfc = "";
        $datos = $this->getEmpresaById($did);
        foreach ($datos as $actual) {
            $rfc = $actual['rfc'];
        }
        return $rfc;
    }

    public function quitarEmpresa($did) {
        $rfc = $this->getRFCEmpresa($did);
        $eliminado = $this->eliminarEmpresa($did, $rfc);
        return $eliminado;
    }

    private function eliminarEmpresa($did, $rfc) {
        $eliminado = false;
        $consultas = new Consultas();
        $consulta = "DELETE FROM `datos_facturacion` WHERE id_datos=:id;";
        $valores = array("id" => $did);
        $eliminado = $consultas->execute($consulta, $valores);
        $this->eliminarArchivos($rfc);
        return $eliminado;
    }

    private function eliminarArchivos($rfc) {
        $carpeta1 = "../temporal/$rfc/";
        $files = scandir($carpeta1);
        foreach ($files as $fname) {
            if ($fname != '.' && $fname != '..') {
                unlink($carpeta1 . $fname);
            }
        }
    }

  
    public function validaPaquete() {
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $paquete = 0;
        $nombrePaquete= '';
        $nRazones = 0;
        $paqueteFinal = "Premium";
        $consulta = "SELECT paquete FROM usuario WHERE idusuario =:id";
        $valores = array("id"=>$idlogin);
        $stmt = $this->consultas->getResults($consulta, $valores);
        foreach($stmt as $rs){
            $paquete = $rs['paquete'];
        }

       
    $this->consultas= new Consultas;
        $consultaD = "SELECT COUNT(*) ndatos FROM datos_facturacion";
        $stmtD = $this->consultas->getResults($consultaD, NULL);
        foreach($stmtD as $rsD){
            $nRazones = $rsD['ndatos'];
        }


        $consultaP = "SELECT nombre FROM paquete WHERE idpaquete =:id;";
        $valoresP = array("id"=>$paquete);
        $stmtP = $this->consultasSine->getResults($consultaP, $valoresP);
        foreach($stmtP as $rsP){
            $nombrePaquete = $rsP['nombre'];
        }

        if( str_contains($nombrePaquete, 'Basico' )){
            $paqueteFinal = "Basico";
        }

    return "$paqueteFinal</tr>$nRazones";
    }
}