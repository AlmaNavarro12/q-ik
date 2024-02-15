<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
date_default_timezone_set("America/Mexico_City");
session_start();

class ControladorPago {

    private $consultas;

    function __construct() {
        $this->consultas = new consultas();
    }

    public function listaServiciosHistorial($REF, $pag, $numreg) {
        include '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='p-0'>
            <tr >
                <th class='col-auto'>No. Folio </th>
                <th class='col-auto'>Creaci&oacute;n </th>
                <th class='col-auto'>Emisor </th>
                <th class='col-auto'>Receptor </th>
                <th class='col-auto'>Fecha / Hora de pago </th>
                <th class='col-auto'>Total </th>
                <th class='col-auto'>Opci&oacute;n</th>
            </tr>
        </thead>
        <tbody>";

        $condicion = "";
        if ($REF == "") {
            $condicion = "ORDER BY idpago DESC";
        } else {
            $condicion = "WHERE (concat(p.letra,p.foliopago) LIKE '%$REF%') OR (p.razonreceptor LIKE '%$REF%') OR (p.razonemisor LIKE '%$REF%') ORDER BY idpago DESC";
        }

        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        if ($div[0] == "1") {
            $numrows = $this->getNumrows($condicion);
            $page = (isset($pag) && !empty($pag)) ? $pag : 1;
            $per_page = $numreg;
            $adjacents = 4;
            $offset = ($page - 1) * $per_page;
            $total_pages = ceil($numrows / $per_page);
            $con = $condicion . " LIMIT $offset,$per_page ";
            $listapago = $this->getServicios($con);
            $finales = 0;
            foreach ($listapago as $actual) {
                $idpago = $actual['idpago'];
                $folio = $actual['foliopago'];
                $foliopago = $actual['letra'] . $folio;
                $idfiscales = $actual['pago_idfiscales'];
                $fechaemision = $actual['fechacreacion'];
                $divideF = explode("-", $fechaemision);
                $m = $divideF[1];
                $mes = $this->translateMonth($m);
                $fechaemision = $divideF[2] . '/' . $mes . '/' . $divideF[0];
                $receptor = $actual['razonreceptor'];
                $emisor = $actual['razonemisor'];
                $totalpagado = $actual['totalpagado'];
                $uuidpago = $actual['uuidpago'];
                $cancelado = $actual['cancelado'];
                $colorrow = $actual['color'];

                if ($uuidpago != "") {
                    $iconbell = "glyphicon-bell";
                    $colorB = "#34A853";
                    $titbell = "Pago Timbrado";
                    $titletimbre = "Cancelar Pago";
                    $functiontimbre = "data-toggle='modal' data-target='#modalcancelar' onclick='setCancelarPago($idpago)'";
                    if ($cancelado == '1') {
                        $btn = "btn-warning";
                        $titletimbre = "Pago Cancelado";
                        $functiontimbre = "href='./com.sine.imprimir/imprimirxml.php?p=$idpago&t=c' target='_blank'";
                        $iconbell = "glyphicon-bell";
                        $colorB = "#f0ad4e";
                        $titbell = "Pago Cancelado";
                    }
                } else {
                    $titletimbre = "Timbrar Pago";
                    $functiontimbre = "onclick=\"xml($idpago);\"";
                    $iconbell = "glyphicon-bell";
                    $colorB = "#ED495C";
                    $titbell = "Pago sin Timbrar";
                    $emisor = $this->getNombreEmisor($idfiscales);
                }

                $datos .= "<tr>
                        <td style='background-color: $colorrow;'></td>
                        <td>$foliopago</td>
                        <td>$fechaemision</td>
                        <td>$emisor</td>
                        <td>$receptor</td>
                        <td>
                            <div class='small-tooltip icon tip'>
                                <span style='color: $colorB;' class='glyphicon $iconbell'></span>
                                <span class='tiptext'>$titbell</span>
                            </div>
                        </td>
                        <td>$ " . number_format($totalpagado, 2, '.', ',') . "</td>
                        <td align='center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-option-vertical'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";
                if ($div[1] == '1') {
                    $datos .= "<li><a onclick='editarPago($idpago);'>Editar Pago <span class='glyphicon glyphicon-edit'></span></a></li>";
                }
                if ($div[2] == '1') {
                    $datos .= "<li><a onclick=\"eliminarPago('$idpago');\">Eliminar Pago <span class='glyphicon glyphicon-remove'></span></a></li>";
                }
                $datos .= "<li><a onclick=\"imprimirpago($idpago);\">Ver Pago <span class='glyphicon glyphicon-file'></span></a></li>
                        <li><a href='./com.sine.imprimir/imprimirxml.php?p=$idpago&t=a' target='_blank'>Ver XML <span class='glyphicon glyphicon-download-alt'></span></a></li>";

                if ($div[3] == '1') {
                    $datos .= "<li><a $functiontimbre> $titletimbre <span class='glyphicon glyphicon-bell'></span></a></li>";
                }

                $datos .= "<li><a data-toggle='modal' data-target='#enviarrecibo' onclick='showCorreos($idpago);'>Enviar <span class='glyphicon glyphicon-envelope'></span></a></li>";

                if ($uuidpago != "") {
                    $datos .= "<li><a data-toggle='modal' data-target='#modal-stcfdi' onclick='checkStatusCancelacion($idpago);'>Comprobar estado del CFDI <span class='glyphicon glyphicon-ok-sign'></span></a></li>";
                }
                $datos .= "</ul>
                        </div></td>
                       </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarPago";

            $datos .= "</tbody><tfoot><tr><th colspan='5' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='3'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
    
            if ($finales == 0) {
                $datos .= "<tr><td colspan='10' class='fs-6'>No se encontraron registros.</td></tr>";
            }
        }
        return $datos;
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }
    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listapago'];
            $editar = $actual['editarpago'];
            $eliminar = $actual['eliminarpago'];
            $timbrar = $actual['pago'];
            $datos .= "$lista</tr>$editar</tr>$eliminar</tr>$timbrar";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM pagos p INNER JOIN datos_facturacion d ON (d.id_datos=p.pago_idfiscales) $condicion;";
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

    private function getServicios($condicion) {
        $consultado = false;
        $consulta = "SELECT p.*, d.color FROM pagos p  INNER JOIN datos_facturacion d ON (d.id_datos=p.pago_idfiscales) $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getNombreEmisor($fid) {
        $razonsocial = "";
        $sine = $this->getDatosFacturacion($fid);
        foreach ($sine as $dactual) {
            $razonsocial = $dactual['razon_social'];
        }
        return $razonsocial;
    }

    private function getDatosFacturacion($id) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:id";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function translateMonth($m)
    {
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $m = intval($m);
        return (array_key_exists($m - 1, $months)) ? $months[$m - 1] : "";
    }

    public function getDatosEmisor($fid) {
        $datos = "";
        $sine = $this->getDatosFacturacion($fid);
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
}