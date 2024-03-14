<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.modelo/Session.php';


class ControladorImpuesto {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    public function listaImpuesto($pag, $REF, $numreg) {
        include '../com.sine.common/pagination.php';
        Session::start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center col-2'>Nombre</th>
                <th class='text-center col-2'>Tipo</th>
                <th class='text-center col-2'>Impuesto</th>
                <th class='text-center col-2'>Factor</th>
                <th class='text-center col-2'>Porcentaje</th>
                <th class='text-center col-2'>Opci&oacute;n</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($REF == "") {
            $condicion = "ORDER BY nombre";
        } else {
            $condicion = "WHERE (nombre LIKE '%$REF%') OR (porcentaje LIKE '%$REF%') ORDER BY nombre";
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
            $catalogo = $this->getCatalogo($condicion);
            $finales = 0;
            foreach ($catalogo as $actual) {
                $idimpuesto = $actual['idimpuesto'];
                $nombre = $actual['nombre'];
                $tipoimpuesto = $actual['tipoimpuesto'];
                $impuesto = $actual['impuesto'];
                $factor = $actual['factor'];
                $porcentaje = $actual['porcentaje'];

                $tipoimpuesto_map = [
                    '1' => 'Traslado',
                    '2' => 'Retencion'
                ];

                $impuesto_map = [
                    '1' => 'ISR',
                    '2' => 'IVA',
                    '3' => 'IEPS'
                ];

                $factor_map = [
                    '1' => 'Tasa',
                    '2' => 'Cuota'
                ];

                $tipo = $tipoimpuesto_map[$tipoimpuesto] ?? 'Tipo no definido';
                $imp = $impuesto_map[$impuesto] ?? 'Impuesto no definido';
                $fac = $factor_map[$factor] ?? 'Factor no definido';

                $datos .= "<tr>
                                <td class='text-center'>$nombre</td>
                                <td class='text-center'>$tipo</td>
                                <td class='text-center'>$imp</td>
                                <td class='text-center'>$fac</td>
                                <td class='text-center'>".number_format($porcentaje, 2, '.', ',')."</td>
                        <td class='text-center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";
                
                if ($div[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarImpuesto($idimpuesto)'>Editar impuesto <span class='text-muted small fas fa-edit'></span></a></li>";
                }
                
                if ($div[2] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarImpuesto($idimpuesto)'>Eliminar impuesto <span class='fas fa-times'></span></a></li>";
                }

                $datos .= "</ul>
                        </div></td>
                           </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarImpuesto";
            if ($finales == 0) {
                $datos .= "<tr><td colspan='11'>No se encontraron registros</td></tr>";
            }
            $datos .= "</tbody><tfoot><tr><th colspan='2' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='4'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
            }
        return $datos;
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT p.listaimpuesto, p.editarimpuesto, p.eliminarimpuesto, p.crearimpuesto FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listaimpuesto'];
            $editar = $actual['editarimpuesto'];
            $eliminar = $actual['eliminarimpuesto'];
            $crear = $actual['crearimpuesto'];
            
            $datos .= "$lista</tr>$editar</tr>$eliminar</tr>$crear";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "select count(idimpuesto) numrows FROM impuesto $condicion;";
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

    private function getCatalogo($condicion) {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getPorcentajesAux($tipo, $impuesto, $factor) {
        switch ($tipo) {
            case '1':
                $traslado = "traslado";
                break;
            case '2':
                $traslado = "retencion";
                break;
            default:
                $traslado = "";
                break;
        }

        $consultado = false;
        $consulta = "SELECT * FROM catalogo_impuestos WHERE impuesto=:impuesto AND factor=:factor AND $traslado=:traslado ORDER BY maximo;";
        $valores = array("impuesto" => $impuesto,
            "factor" => $factor,
            "traslado" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function checkImpuestoAux($cf) {
        $valido = false;
        $impuestos = $this->getPorcentajesAux($cf->getTipo(), $cf->getImpuesto(), $cf->getFactor());
        foreach ($impuestos as $actual) {
            $tipo = $actual['tipo'];
            if ($tipo == 'rango') {
                $min = $actual['minimo'];
                $max = $actual['maximo'];
                if ($cf->getTasa() < $min || $cf->getTasa() > $max) {
                    $valido = TRUE;
                    echo "0El valor ingresado esta fuera de rango";
                }
            }
        }
        return $valido;
    }

    public function checkImpuesto($cf) {
        $datos = false;
        $check = $this->checkImpuestoAux($cf);
        if (!$check) {
            $datos = $this->guardarImpuesto($cf);
        }
        return $datos;
    }


    private function guardarImpuesto($cf) {
        $consulta = ($cf->getIdimpuesto() != null) ?
        "UPDATE `impuesto` SET nombre=:nombre, tipoimpuesto=:tipo, impuesto=:impuesto, factor=:factor, tipotasa=:tipotasa, porcentaje=:tasa, chuso=:chuso WHERE idimpuesto=:id;":
        "INSERT INTO `impuesto` VALUES (NULL, :nombre, :tipo, :impuesto, :factor, :tipotasa, :tasa, :chuso);";
        
        $valores = array(
            "id" => $cf->getIdimpuesto(),
            "nombre" => $cf->getNombre(),
            "tipo" => $cf->getTipo(),
            "impuesto" => $cf->getImpuesto(),
            "factor" => $cf->getFactor(),
            "tipotasa" => $cf->getTipotasa(),
            "tasa" => $cf->getTasa(),
            "chuso" => $cf->getChuso()
        );
    
        return $this->consultas->execute($consulta, $valores);
    }

    public function checkUpdImpuesto($cf) {
        $datos = false;
        $check = $this->checkImpuestoAux($cf);
        if (!$check) {
            $datos = $this->guardarImpuesto($cf);
        }
        return $datos;
    }

    public function getPorcentajes($tipo, $impuesto, $factor) {
        $datos = "";
        $tipoimp = "";
        $porcentajes = $this->getPorcentajesAux($tipo, $impuesto, $factor);
        foreach ($porcentajes as $actual) {
            $tipoimp = $actual['tipo'];
            $min = $actual['minimo'];
            $max = $actual['maximo'];
            if ($tipoimp == 'fijo') {
                $datos .= "<option value='" . $max . "'>" . $max . "</option>";
            } else if ($tipoimp == 'rango') {
                $datos .= "$min</tr>$max";
            }
        }
        return $tipoimp . "</tr>" . $datos;
    }
    
    public function getDatosImpuesto($id) {
        $datos = "";
        $impuestos = $this->getDatosImpuestobyID($id);
        foreach ($impuestos as $actual) {
            $idimpuesto = $actual['idimpuesto'];
            $nombre = $actual['nombre'];
            $tipoimpuesto = $actual['tipoimpuesto'];
            $impuesto = $actual['impuesto'];
            $factor = $actual['factor'];
            $tipotasa = $actual['tipotasa'];
            $porcentaje = $actual['porcentaje'];
            $chuso = $actual['chuso'];

            $datos .= "$idimpuesto</tr>$nombre</tr>$tipoimpuesto</tr>$impuesto</tr>$factor</tr>$tipotasa</tr>$porcentaje</tr>$chuso";
        }
        return $datos;
    }

    private function getDatosImpuestobyID($id) {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto WHERE idimpuesto=:id;";
        $valores = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function eliminarImpuesto($id) {
        $insertado = false;
        $consulta = "DELETE FROM `impuesto` WHERE idimpuesto=:id;";
        $valores = array("id" => $id);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
}