<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Configuracion.php';
require_once '../vendor/autoload.php'; //Carga automatica

use PhpOffice\PhpSpreadsheet\Reader\Xlsx; //Biblioteca Xlsx 
date_default_timezone_set("America/Mexico_City");
use PHPMailer\PHPMailer\PHPMailer;

class ControladorConfiguracion
{

    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

    //---------------------------------------------------GENERAL
    private function getBancoAux($banco){
        $consulta = "SELECT idcatalogo_banco FROM catalogo_banco WHERE nombre_banco LIKE '%$banco%' OR descripcion_banco LIKE '%$banco%';";
        return $this->consultas->getResults($consulta, null);
    }

    private function getBanco($banco)
    {
        $datos = $this->getBancoAux($banco);
        return $datos ? $datos[0]['idcatalogo_banco'] : "0";
    }

    private function getEstadoAux($estado)
    {
        $consulta = "SELECT id_estado FROM estado WHERE estado LIKE '%$estado%';";
        return $this->consultas->getResults($consulta, null);
    }

    private function getEstado($estado)
    {
        $datos = $this->getEstadoAux($estado);
        return $datos ? $datos[0]['id_estado'] : "0";
    }

    private function getMunicipioAux($municipio, $idestado)
    {
        $consulta = "SELECT id_municipio FROM municipio WHERE municipio LIKE '%$municipio%' AND id_estado=:idestado;";
        $val = array("municipio" => $municipio, "idestado" => $idestado);
        return $this->consultas->getResults($consulta, $val);
    }

    private function getMunicipio($municipio, $idestado)
    {
        $datos = $this->getMunicipioAux($municipio, $idestado);
        return $datos ? $datos[0]['id_municipio'] : "0";
    }

    //----------------------------------------------------FOLIO
    public function valFolio($f)
    {
        $insertar = "";
        $insertar = $this->insertarFolio($f);
        return $insertar;
    }

    private function insertarFolio($f)
    {
        $insertado = false;
        $consulta = "INSERT INTO `folio` VALUES (:id, :serie, :letra, :numinicio, :consecutivo, :uso);";
        $valores = array(
            "id" => null,
            "serie" => $f->getSerie(),
            "letra" => $f->getLetra(),
            "numinicio" => $f->getNuminicio(),
            "consecutivo" => $f->getNuminicio(),
            "uso" => $f->getUsofolio()
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getNumrowsAux($con)
    {
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM folio $con;";
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

    private function getFolios($con = "")
    {
        $consultado = false;
        $consulta = "SELECT * FROM folio $con;";
        $consultado = $this->consultas->getResults($consulta, NULL);
        return $consultado;
    }

    private function getPermisoById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT p.editarfolio, p.eliminarfolio FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario)
    {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $editar = $actual['editarfolio'];
            $eliminar = $actual['eliminarfolio'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    private function getFoliosById($id)
    {
        $consultado = false;
        $consulta = "SELECT * FROM folio WHERE idfolio=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosFolio($id)
    {
        $datos = "";
        $folios = $this->getFoliosById($id);
        foreach ($folios as $actual) {
            $idfolio = $actual['idfolio'];
            $serie = $actual['serie'];
            $letra = $actual['letra'];
            $numinicio = $actual['numinicio'];
            $uso = $actual['usofolio'];
            $datos .= "$idfolio</tr>$serie</tr>$letra</tr>$numinicio</tr>$uso";
        }
        return $datos;
    }

    public function valFolioActualizar($f)
    {
        $insertar = "";
        $insertar = $this->actualizarFolio($f);
        return $insertar;
    }

    private function actualizarFolio($f)
    {
        $actualizado = false;
        $inicio = $f->getActualizarinicio();
        if ($f->getNuminicio() != $f->getActualizarinicio()) {
            $inicio = $f->getNuminicio();
        }
        $consulta = "UPDATE `folio` SET serie=:serie, letra=:letra, numinicio=:numinicio, consecutivo=:consecutivo, usofolio=:uso WHERE idfolio=:id;";
        $valores = array(
            "id" => $f->getIdfolio(),
            "serie" => $f->getSerie(),
            "letra" => $f->getLetra(),
            "numinicio" => $f->getNuminicio(),
            "consecutivo" => $inicio,
            "uso" => $f->getUsofolio()
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function eliminarFolio($id)
    {
        $insertado = false;
        $consulta = "DELETE FROM `folio` where idfolio=:id;";
        $valores = array("id" => $id);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function listaFolios($pag, $REF, $numreg)
    {
        require_once '../com.sine.common/pagination.php';
        $datos = "<thead class='sin-paddding'>
        <tr>
        <th class='col-2'>Serie</th>
        <th class='col-2'>Letra</th>
        <th class='col-2'>N° Inicio</th>
        <th class='col-2'>Folio Actual</th>
        <th class='col-2'>Uso del folio</th>
        <th class='col-2 text-center'>Opción <span class='fas fa-ellipsis-v text-muted'></span></th>
        </tr>
    </thead>
    <tbody>";

        $condicion = ($REF == "") ? "ORDER BY serie" : "WHERE (serie LIKE '%$REF%') OR (letra LIKE '%$REF%') ORDER BY serie";

        Session::start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $permisos = explode("</tr>", $this->getPermisos($idlogin));
        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = "$condicion LIMIT $offset,$per_page ";

        $folios = $this->getFolios($con);

        $inicios = $offset + 1;
        $finales = $inicios + count($folios) - 1;

        foreach ($folios as $actual) {
            $idfolio = $actual['idfolio'];

            $datos .= "<tr>
        <td>{$actual['serie']}</td>
        <td>{$actual['letra']}</td>
        <td>{$actual['numinicio']}</td>
        <td>{$actual['consecutivo']}</td>
        <td>{$this->getUsoFolio($actual['usofolio'])}</td>
                     <td>
                        <div class='dropdown d-flex justify-content-center'>
                           <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                           <span class='caret'></span></button>
                           <ul class='dropdown-menu dropdown-menu-right'>";
            if ($permisos[0] == '1') {
                $datos .= "<li class='notification-link py-1'><a class='text-decoration-none text-secondary-emphasis' title='Editar folio' onclick='editarFolio($idfolio)'>Editar folio <span class='text-muted fas fa-edit small'></span></a></li>";
            }

            if ($permisos[1] == '1') {
                $datos .= "<li class='notification-link py-1'><a class='text-decoration-none text-secondary-emphasis' title='Eliminar folio' onclick='eliminarFolio($idfolio)'>Eliminar folio <span class='text-muted fas fa-times'></span></a></li>";
            }
            $datos .= "        </ul>
                        </div>
                     </td>
                   </tr>";
        }

        $function = "buscarFolio";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='7'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='7'>Mostrando $inicios al $finales de $numrows registros " . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    private function getUsoFolio($iduso)
    {
        $usos = ["Facturas", "Notas de crédito", "Pagos", "Cartas porte", "Cotizaciones"];
        $divuso = explode("-", $iduso);
        $usofolio = implode(", ", array_map(function ($uso) use ($usos) {
            return isset($usos[$uso - 1]) ? $usos[$uso - 1] : "No disponible";
        }, $divuso));

        return $usofolio;
    }

    //------------------------------------------IMPORTAR TABLAS
    public function importTable($fn, $tabla)
    {
        $targetPath = '../temporal/tmp/' . $fn;
        $ext = pathinfo($targetPath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return "0Error: Tipo de archivo no válido, solo archivos excel (.xls, .xlsx).";
        }
        return $tabla == '1' ? $this->tablaClientes($fn) : ($tabla == '2' ? $this->tablaProductos($fn) : null);
    }

    private function tablaClientes($fn)
    {
        $targetPath = '../temporal/tmp/' . $fn;
        $Reader = new Xlsx();
        $hoja = $Reader->load($targetPath);
        $fileexcel = $hoja->getActiveSheet()->toArray();
        $insert = false;

        foreach ($fileexcel as $row) {
            if (count($row) == 21) {
                // Validar RFC una vez hecho controlador cliente
                $consulta = "INSERT INTO cliente VALUES (:id, :nombre, :apaterno, :amaterno, :empresa, :correoinfo, :correofact, :correogerencia, :telefono, :idbanco, :cuenta, :clabe, :idbanco1, :cuenta1, :clabe1, :idbanco2, :cuenta2, :clabe2, :idbanco3, :cuenta3, :clabe3, :rfc, :razon, :regimen, :calle, :interior, :exterior, :localidad, :municipio, :estado, :pais, :codpostal, :correoalt1, :correoalt2, :correoalt3);";
                $valores = array(
                    "id" => null,
                    "nombre" => $row[0],
                    "apaterno" => $row[1],
                    "amaterno" => $row[2],
                    "empresa" => $row[3],
                    "correoinfo" => $row[4],
                    "correofact" => $row[5],
                    "correogerencia" => $row[6],
                    "telefono" => $row[7],
                    "idbanco" => $this->getBanco($row[8]),
                    "cuenta" => $row[9],
                    "clabe" => $row[10],
                    "idbanco1" => '0',
                    "cuenta1" => '',
                    "clabe1" => '',
                    "idbanco2" => '0',
                    "cuenta2" => '',
                    "clabe2" => '',
                    "idbanco3" => '0',
                    "cuenta3" => '',
                    "clabe3" => '',
                    "rfc" => $row[11],
                    "razon" => $row[12],
                    "regimen" => $row[13],
                    "calle" => $row[14],
                    "interior" => $row[15],
                    "exterior" => $row[16],
                    "localidad" => $row[17],
                    "municipio" => $this->getMunicipio($row[19], $this->getEstado($row[18])),
                    "estado" => $this->getEstado($row[18]),
                    "pais" => 'México',
                    "codpostal" => $row[20],
                    "correoalt1" => '',
                    "correoalt2" => '',
                    "correoalt3" => ''
                );
                $insert = $this->consultas->execute($consulta, $valores);
            }
        }
        if ($insert) {
            unlink($targetPath);
        }
        return $insert;
    }

    private function tablaProductos($fn){
        $targetPath = '../temporal/tmp/' . $fn;
        $Reader = new Xlsx();
        $hoja = $Reader->load($targetPath);
        $fileexcel = $hoja->getActiveSheet()->toArray();
        $insert = false;

        foreach ($fileexcel as $row) {
            if (count($row) == 14) {
                $consulta = "INSERT INTO `productos_servicios` VALUES (:id, :codproducto, :producto, :clvunidad, :unidad, :descripcion, :pcompra, :porcentaje, :ganancia, :pventa, :tipo, :clvfiscal, :descfiscal, :idproveedor, :imagen, :chinventario, :cantidad);";
                $valores = array(
                    "id" => null,
                    "codproducto" => $row[0],
                    "producto" => $row[1],
                    "clvunidad" => $row[2],
                    "unidad" => $row[3],
                    "descripcion" => $row[4],
                    "pcompra" => $row[5],
                    "porcentaje" => $row[6],
                    "ganancia" => $row[7],
                    "pventa" => $row[8],
                    "tipo" => $row[9],
                    "clvfiscal" => $row[10],
                    "descfiscal" => $row[11],
                    "idproveedor" => '0',
                    "imagen" => '',
                    "chinventario" => $row[12],
                    "cantidad" => $row[13]
                );
                $insert = $this->consultas->execute($consulta, $valores);
            }
        }
        if ($insert) {
            unlink($targetPath);
        }
        return $insert;
    }

    //-----------------------------------------COMISIONES
    private function getUsuariosAux($con = "") {
        $consultado = false;
        $consulta = "select * from usuario $con order by nombre;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function checkComisionUsuarioAux($idusuario) {
        $consultado = false;
        $consulta = "select * from comisionusuario where comision_idusuario=:id;";
        $val = array("id" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkComisionUsuario($idusuario) {
        $datos = "";
        $get = $this->checkComisionUsuarioAux($idusuario);
        foreach ($get as $actual) {
            $idcomisionusuario = $actual['idcomisionusuario'];
            $idusu = $actual['comision_idusuario'];
            $porcentaje = $actual['comisionporcentaje'];
            $calculo = $actual['calculo'];
            $datos = "$idcomisionusuario</tr>$idusu</tr>$porcentaje</tr>$calculo";
        }
        return $datos;
    }

    public function datosUsuario($idusuario) {
        $get = $this->getUsuariosAux("where idusuario='$idusuario'");
        $datos = "";
        $check = '0';
        foreach ($get as $actual) {
            $tipo = $actual['tipo'];
            $datcom = $this->checkComisionUsuario($idusuario);
            if ($datcom != "") {
                $check = '1';
            }
            $datos .= "$tipo</tr>$check</tr>$datcom";
        }
        return $datos;
    }

    public function insertarComision($f) {
        $insertado = false;
        $consulta = "INSERT INTO `comisionusuario` VALUES (:id, :idusuario, :porcentaje, :calculo);";
        $valores = array("id" => null,
            "idusuario" => $f->getIdusuario(),
            "porcentaje" => $f->getPorcentaje(),
            "calculo" => $f->getChcalculo());
        $insertado = $this->consultas->execute($consulta, $valores);
        $insertado = true;

        if ($f->getChcom() == '1') {
            $auto = $this->checkComision($f);
        }
        return $insertado;
    }

    public function actualizarComision($f) {
        $insertado = false;
        $consulta = "UPDATE `comisionusuario` SET comisionporcentaje=:porcentaje, calculo=:calculo where idcomisionusuario=:id;";
        $valores = array("id" => $f->getIdcomision(),
            "porcentaje" => $f->getPorcentaje(),
            "calculo" => $f->getChcalculo());
        $insertado = $this->consultas->execute($consulta, $valores);
        if ($f->getChcom() == '1') {
            $auto = $this->checkComision($f);
        }
        return $insertado;
    }
    
    public function quitarComision($idcomision) {
        $eliminado = false;
        $consulta = "DELETE FROM `comisionusuario` WHERE idcomisionusuario=:id;";
        $valores = array("id" => $idcomision);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function checkComision($f) {
        $comision = "";
        $getusu = $this->getUsuariosAux();
        foreach ($getusu as $actual) {
            $check = false;
            $idusuario = $actual['idusuario'];
            $com = $this->checkComisionUsuarioAux($idusuario);
            foreach ($com as $comactual) {
                $idcom = $comactual['idcomisionusuario'];
                $check = true;
            }
            if ($check) {
                $comision = $this->actualizarComision2($f, $idcom);
            } else {
                $comision = $this->insertarComision2($f, $idusuario);
            }
        }
        return $comision;
    }

    private function insertarComision2($f, $idusuario) {
        $insertado = false;
        $consulta = "INSERT INTO `comisionusuario` VALUES (:id, :idusuario, :porcentaje, :calculo);";
        $valores = array("id" => null,
            "idusuario" => $idusuario,
            "porcentaje" => $f->getPorcentaje(),
            "calculo" => $f->getChcalculo());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function actualizarComision2($f, $idcom) {
        $insertado = false;
        $consulta = "UPDATE `comisionusuario` SET comisionporcentaje=:porcentaje, calculo=:calculo where idcomisionusuario=:id;";
        $valores = array("id" => $idcom,
            "porcentaje" => $f->getPorcentaje(),
            "calculo" => $f->getChcalculo());
        $insertado=$this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    //---------------------------------CORREO
    public function getMail($idcorreo) {
        $datos = "";
        $get = $this->getMailAux($idcorreo);
        foreach ($get as $actual) {
            $correo = $actual['correo'];
            $pass = $actual['password'];
            $remitente = $actual['remitente'];
            $correoremitente = $actual['correoremitente'];
            $host = $actual['host'];
            $puerto = $actual['puerto'];
            $seguridad = $actual['seguridad'];
            $chuso1 = $actual['chuso1'];
            $chuso2 = $actual['chuso2'];
            $chuso3 = $actual['chuso3'];
            $chuso4 = $actual['chuso4'];
            $chuso5 = $actual['chuso5'];
            $datos = "$correo</tr>$pass</tr>$remitente</tr>$correoremitente</tr>$host</tr>$puerto</tr>$seguridad</tr>$chuso1</tr>$chuso2</tr>$chuso3</tr>$chuso4</tr>$chuso5";
        }
        return $datos;
    }

    private function getMailAux($idcorreo) {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE idcorreoenvio=:id;";
        $valores = array("id" => $idcorreo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function nuevoCorreo($c) {
        $insertar = "";
        $check = $this-> verificarCorreo($c);
       
        if (!$check) {
            $insertar = $this->insertarCorreoEnvio($c);
        }
        return $insertar;
    }
    
    private function insertarCorreoEnvio($c) {
        $actualizado = false;
        $consulta = "INSERT INTO `correoenvio` VALUES (:id, :correo, :password, :remitente, :correoremitente, :host, :puerto, :seguridad, :chuso1, :chuso2, :chuso3, :chuso4, :chuso5);";
        $valores = array("id" => null,
            "correo" => $c->getCorreoenvio(),
            "password" => $c->getPasscorreo(),
            "remitente" => $c->getRemitente(),
            "correoremitente" => $c->getMailremitente(),
            "host" => $c->getHostcorreo(),
            "puerto" => $c->getPuertocorreo(),
            "seguridad" => $c->getSeguridadcorreo(),
            "chuso1" => $c->getChusocorreo1(),
            "chuso2" => $c->getChusocorreo2(),
            "chuso3" => $c->getChusocorreo3(),
            "chuso4" => $c->getChusocorreo4(),
            "chuso5" => $c->getChusocorreo5());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    private function getMailval($correo) {
        $consulta = "SELECT COUNT(*) AS total FROM correoenvio WHERE correo = :correo OR correoremitente = :correo";
        $valores = array("correo" => $correo);
        $resultado = $this->consultas->getResults($consulta, $valores);
        if (!empty($resultado)) {
            $totalCorreos = $resultado[0]['total'];
            return $totalCorreos;
        } else {
            return 0;
        }
    }
    
    private function getMailbyUsoAux($uso) {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE chuso$uso=:chuso;";
        $valores = array("chuso" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function verificarCorreo($c) {
        $existe = false;

        if($this->getMailval($c->getCorreoenvio()) > 0){
            echo "0Ya existe un correo registrado.";
            $existe = true;
        } else {
            if ($c->getChusocorreo1() == '1') {
                $correos = $this->getMailbyUsoAux('1');
                foreach ($correos as $actual) {
                    echo "0Ya existe un correo asignado a Facturas.";
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                if ($c->getChusocorreo2() == '1') {
                    $correos = $this->getMailbyUsoAux('2');
                    foreach ($correos as $actual) {
                        echo "0Ya existe un correo asignado a Pagos.";
                        $existe = true;
                        break;
                    }
                }
            }
            if (!$existe) {
                if ($c->getChusocorreo3() == '1') {
                    $correos = $this->getMailbyUsoAux('3');
                    foreach ($correos as $actual) {
                        echo "0Ya existe un correo asignado a Cotizaciones.";
                        $existe = true;
                        break;
                    }
                }
            }
            if (!$existe) {
                if ($c->getChusocorreo4() == '1') {
                    $correos = $this->getMailbyUsoAux('4');
                    foreach ($correos as $actual) {
                        echo "0Ya existe un correo asignado a Comunicados.";
                        $existe = true;
                        break;
                    }
                }
            }
            if (!$existe) {
                if ($c->getChusocorreo5() == '1') {
                    $correos = $this->getMailbyUsoAux('5');
                    foreach ($correos as $actual) {
                        echo "0Ya existe un correo asignado a Contratos.";
                        $existe = true;
                        break;
                    }
                }
            }
        }
        return $existe;
    }

    public function modificarCorreo($c) {
        $insertar = "";
        $check = $this->verificarActualizarCorreo($c);
        if (!$check) {
            $insertar = $this->actualizarCorreoEnvio($c);
        }
        return $insertar;
    }

    private function getMailbyUsoAux2($uso, $id) {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE chuso$uso=:chuso and idcorreoenvio!=:id;";
        $valores = array("chuso" => '1',
            "id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function verificarActualizarCorreo($c) {
        $existe = false;
        if ($c->getChusocorreo1() == '1') {
            $correos = $this->getMailbyUsoAux2('1', $c->getIdcorreo());
            foreach ($correos as $actual) {
                echo "0Ya existe un correo asignado a Facturas.";
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            if ($c->getChusocorreo2() == '1') {
                $correos = $this->getMailbyUsoAux2('2', $c->getIdcorreo());
                foreach ($correos as $actual) {
                    echo "0Ya existe un correo asignado a Pagos.";
                    $existe = true;
                    break;
                }
            }
        }
        if (!$existe) {
            if ($c->getChusocorreo3() == '1') {
                $correos = $this->getMailbyUsoAux2('3', $c->getIdcorreo());
                foreach ($correos as $actual) {
                    echo "0Ya existe un correo asignado a Cotizaciones.";
                    $existe = true;
                    break;
                }
            }
        }
        if (!$existe) {
            if ($c->getChusocorreo4() == '1') {
                $correos = $this->getMailbyUsoAux2('4', $c->getIdcorreo());
                foreach ($correos as $actual) {
                    echo "0Ya existe un correo asignado a Comunicados.";
                    $existe = true;
                    break;
                }
            }
        }
        if (!$existe) {
            if ($c->getChusocorreo5() == '1') {
                $correos = $this->getMailbyUsoAux2('5', $c->getIdcorreo());
                foreach ($correos as $actual) {
                    echo "0Ya existe un correo asignado a Contratos.";
                    $existe = true;
                    break;
                }
            }
        }
        return $existe;
    }

    private function actualizarCorreoEnvio($c) {  
        $actualizado = false;
        $consulta = "UPDATE `correoenvio` set correo=:correo, password=:password, remitente=:remitente, correoremitente=:correoremitente, host=:host, puerto=:puerto, seguridad=:seguridad, chuso1=:chuso1, chuso2=:chuso2, chuso3=:chuso3, chuso4=:chuso4, chuso5=:chuso5 WHERE idcorreoenvio=:id;";
        $valores = array("id" => $c->getIdcorreo(),
            "correo" => $c->getCorreoenvio(),
            "password" => $c->getPasscorreo(),
            "remitente" => $c->getRemitente(),
            "correoremitente" => $c->getMailremitente(),
            "host" => $c->getHostcorreo(),
            "puerto" => $c->getPuertocorreo(),
            "seguridad" => $c->getSeguridadcorreo(),
            "chuso1" => $c->getChusocorreo1(),
            "chuso2" => $c->getChusocorreo2(),
            "chuso3" => $c->getChusocorreo3(),
            "chuso4" => $c->getChusocorreo4(),
            "chuso5" => $c->getChusocorreo5());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function actualizarBodyMail($c) {
        $actualizado = false;
        $img = $c->getImglogo();
        if ($img == '') {
            $img = $c->getImgactualizar();
        }
        $consulta = "UPDATE `mailbody` SET asunto=:asunto, saludo=:saludo, mensaje=:mensaje, logomsg=:img WHERE idmailbody=:id;";
        $valores = array("id" => $c->getIdbodymail(),
            "asunto" => $c->getAsuntobody(),
            "saludo" => $c->getSaludobody(),
            "mensaje" => $c->getTxtbody(),
            "img" => $img);
        rename("../temporal/tmp/" . $img, "../img/" . $img);
        $actualizado = $this->consultas->execute($consulta, $valores);
        if ($c->getChlogo()) {
            $this->updateLogoBody($c->getIdbodymail(), $img);
        }
        return $actualizado;
    }

    private function updateLogoBody($id, $img) {
        $actualizado = FALSE;
        $consulta = $consulta = "UPDATE `mailbody` SET logomsg=:img WHERE idmailbody!=:id;";
        $val = array("img" => $img,
            "id" => $id);
        $actualizado = $this->consultas->execute($consulta, $val);
        return $actualizado;
    }

    public function getMailBody($id) {
        $datos = "";
        $get = $this->getMailBodyAux($id);
        foreach ($get as $actual) {
            $idmailbody = $actual['idmailbody'];
            $asunto = $actual['asunto'];
            $saludo = $actual['saludo'];
            $mensaje = $actual['mensaje'];
            $imagen = $actual['logomsg'];
            $img = "";
            if ($imagen != "") {
                $imgfile = "../img/" . $imagen;
                $type = pathinfo($imgfile, PATHINFO_EXTENSION);
                $data = file_get_contents($imgfile);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $img = "<div class=\"col-4\"><img src=\"$base64\" style=\"max-width:100%; height:auto;\" class=\"img-fluid\"></div>";
            }
            $datos = "$idmailbody</tr>$asunto</tr>$saludo</tr>$mensaje</tr>$imagen</tr>$img";
        }
        return $datos;
    }

    public function mailPrueba($sm) {
        $mail = new PHPMailer;
        $mail->Mailer = 'smtp';
        $mail->SMTPAuth = true;
        $mail->Host = $sm->getHostcorreo();
        $mail->Port = $sm->getPuertocorreo();
        $mail->SMTPSecure = $sm->getSeguridadcorreo();
        $mail->Username = $sm->getCorreoenvio();
        $mail->Password = $sm->getPasscorreo();
        $mail->From = $sm->getMailremitente();
        $mail->FromName = $sm->getRemitente();
        $mail->Subject = iconv("utf-8","windows-1252","Prueba de conexión de correo");

        $mail->isHTML(true);
        $mail->Body = "<html>
        <body>
            <table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0' style='border-radius: 25px;'>
                <tr><td>
                        <table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; border-radius: 20px; background-color:#fff; font-family:sans-serif;'>
                            <thead>
                                <tr height='80'>
                                    <th align='left' colspan='4' style='padding: 6px; background-color:#f5f5f5; border-radius: 20px; border-bottom:solid 1px #bdbdbd;' ><img src='https://q-ik.mx/Demo/img/LogoQik.png' height='100px'/></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr align='center' height='10' style='font-family:sans-serif; '>
                                    <td style='background-color:#09096B; text-align:center; border-radius: 5px;'></td>
                                </tr>
                                <tr>
                                    <td colspan='4' style='padding:15px;'>
                                        <h1>Prueba de conexion</h1>
                                        <p style='font-size:20px;'>Hola soy un correo de prueba</p>
                                        <hr/>
                                        <p style='font-size:18px; text-align: justify;'>Si recibiste este mensaje, significa que la configuracion del correo que ingresaste para el envio de tus facturas se hizo correctamente.</p>
                                        <p style='font-size:18px; text-align: justify;'>Tu sistema ahora esta listo para enviar correos, recuerda que los correos se envian desde esta cuenta por lo que las posibles respuestas de tus clientes o avisos de errores de envio por direcciones incorrectas o inexistentes llegaran aqui.</p>
                                        <p style='font-size:18px; text-align: justify;'>Gracias y un saludo por parte del equipo de <span style='font-family:sans-serif; color: #17177c; font-weight: bolder;'>Q-ik</span>.</p>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </td></tr>
            </table>
        </body>
    </html>";
        $mail->addAddress($sm->getCorreoenvio());
        if (!$mail->send()) {
            echo '0No se envió el mensaje' . $mail->ErrorInfo;
        } else {
            return '1Se ha enviado el correo.';
        }
    }
    private function getMailBodyAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM mailbody WHERE idmailbody=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }
}
