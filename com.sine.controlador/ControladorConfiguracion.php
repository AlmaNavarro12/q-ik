<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Configuracion.php';
require_once '../vendor/autoload.php'; //Carga automatica

use PhpOffice\PhpSpreadsheet\Reader\Xlsx; //Biblioteca Xlsx 
date_default_timezone_set("America/Mexico_City");

class ControladorConfiguracion
{

    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

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
        $c = new Consultas();
        $consulta = "SELECT p.editarfolio, p.eliminarfolio FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $c->getResults($consulta, $valores);
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
        return $insert;
    }

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
}
