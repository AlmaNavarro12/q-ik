<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../../CATSAT/CATSAT/com.sine.controlador/controladorMonedas.php';

date_default_timezone_set("America/Mexico_City");
session_start();

class ControladorPago {

    private $consultas;
    private $controladorMoneda;

    function __construct() {
        $this->consultas = new consultas();
        $this->controladorMoneda = new ControladorMonedas();
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

    public function getTabla($sessionid, $tag, $idmoneda, $tcambio, $uuid) {
        $tabla = $this->tablaPago($sessionid, $tag, $tcambio, $idmoneda, $uuid);
        return $tabla;
    }

    private function tablaPago($sessionid, $tag, $tcambio = 1, $idmoneda = "1", $uuid = "") {
        $table = "<corte><thead class='sin-paddding'>
            <tr>
                <th class='text-center'>FACTURA</th>
                <th class='text-center'>PARCIALIDAD</th>
                <th class='text-center'>TOTAL FACTURA</th>
                <th class='text-center'>MONEDA</th>
                <th class='text-center'>MONTO ANT.</th>
                <th class='text-center'>MONTO PAGADO</th>
                <th class='text-center'>RESTANTE</th>
                <th class='text-center'>OPCIONES</th>
                </thead><tbody>";
    
        $totalpagados = 0;
        $disuuid = ($uuid != "") ? "disabled" : "";
    
        $productos = $this->getPagosTMP($sessionid, $tag);
    
        foreach ($productos as $pagoactual) {
            $folio = $pagoactual['foliofacturatmp'];
            $noparcialidad = $pagoactual['noparcialidadtmp'];
            $monto = $pagoactual['montotmp'];
            $montoanterior = $pagoactual['montoanteriortmp'];
            $montoinsoluto = $pagoactual['montoinsolutotmp'];
            $totalfactura = $pagoactual['totalfacturatmp'];
            $idtmp = $pagoactual['idtmppago'];
    
            $totalpagados += $this->totalDivisa($monto, $idmoneda, $pagoactual['idmonedatmp'], $pagoactual['tcambiotmp'], $tcambio);
    
            $table .= "
                <tr>
                    <td>$folio</td>
                    <td>$noparcialidad</td>
                    <td>$ " . number_format($totalfactura, 2, '.', ',') . "</td>
                    <td>$ " . number_format($montoanterior, 2, '.', ',') . "</td>
                    <td>$ " . number_format($monto, 2, '.', ',') . "</td>
                    <td>$ " . number_format($montoinsoluto, 2, '.', ',') . "</td>
                    <td><a $disuuid class='btn button-list' title='Eliminar' onclick='eliminarcfdi($idtmp);'><span class='glyphicon glyphicon-remove'></span></a></td>
                </tr>";
        }
    
        $monedapago = ($idmoneda == 1) ? "MXN" : (($idmoneda == 2) ? "USD" : (($idmoneda == 3) ? "EUR" : "Desconocida"));
    
        $table .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='3' align='right'></th>
                    <th align='right'><b>TOTAL PAGADO:</b></th>
                    <th>$ " . number_format($totalpagados, 2, '.', ',') . " $monedapago</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>";
    
        return $table;
    }
    

    private function totalDivisa($total, $monedaP, $monedaF, $tcambioF = '0', $tcambioP = '0') {
        if ($monedaP == $monedaF) {
            $OP = bcdiv($total, '1', 2);
        } else {
            $tcambio = $this->controladorMoneda->getTipoCambio($monedaP, $monedaF, $tcambioF, $tcambioP);
            $OP = bcdiv($total, '1', 2) / bcdiv($tcambio, '1', 6);
        }
        return $OP;
    }

    public function getPagosTMP($sid, $tag) {
        $consultado = false;
        $consulta = "SELECT t.* FROM tmppago t WHERE sessionid=:sid AND tmptagcomp=:tag ORDER BY idtmppago ASC;";
        $val = array("sid" => $sid, "tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function genTag() {
        $fecha = date('YmdHis');
        $idusu = $_SESSION[sha1("idusuario")];
        $sid = session_id();
        $ranstr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
        $tag = $ranstr . $fecha . $idusu . $sid;
        return $tag;
    }
    
    public function nuevoComplemento($comp) {
        $tag = $this->genTag();
        $datos = "<button id='tab-$tag' class='tab-pago sub-tab-active' data-tab='$tag' data-ord='$comp' name='tab-complemento'>Complemento $comp &nbsp; <span data-tab='$tag' type='button' class='close-button' aria-label='Close'><span aria-hidden='true'>&times;</span></span></button>
                <cut>
                <div id='complemento-$tag' class='sub-div'>
                <div class='row'>
                <div class='col-md-4 py-2'>
                    <label class='label-form text-right' for='forma-$tag'>Forma de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                    <div class='form-group'>
                            <select class='form-select text-center input-form' id='forma-$tag' name='forma-$tag' onchange='disableCuenta();'>
                                <option value='' id='default-fpago-$tag'>- - - -</option>
                                <optgroup id='forma-pago-$tag' class='cont-fpago-$tag text-left'> </optgroup>
                            </select>
                        <div id='forma-$tag-errors'></div>
                    </div>
                </div>

                <div class='col-md-2 py-2'>
                    <label class='label-form text-right' for='moneda-$tag'>Moneda de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                    <div class='form-group'>
                        <select class='form-select text-center input-form' id='moneda-$tag' name='moneda-$tag' onchange='getTipoCambio(); loadTablaCFDI();'>
                            <option value='' id='default-moneda-$tag'>- - - -</option>
                            <optgroup id='mpago-$tag' class='contmoneda-$tag text-left'> </optgroup>
                        </select>
                        <div id='moneda-$tag-errors'></div>
                    </div>
                </div>

                <div class='col-md-2 py-2'>
                    <label class='label-form text-right' for='cambio-$tag'>Tipo de cambio</label>
                    <label class='mark-required text-danger fw-bold'></label>
                    <div class='form-group'>
                        <input type='text' class='form-control input-form' id='cambio-$tag' placeholder='Tipo de cambio de moneda' disabled=''>
                        <div id='cambio-$tag-errors'></div>
                    </div>
                </div>

                <div class='col-md-4 py-2'>
                    <label class='label-form text-right' for='fecha-$tag'>Fecha de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                    <div class='form-group'>
                        <input class='form-control text-center input-form' id='fecha-$tag' name='fecha-$tag' type='date'/>
                        <div id='fecha-$tag-errors'></div>
                    </div>
                </div>
            </div>

            <div class='row'>
                <div class='col-md-4 py-2'>
                    <label class='label-form text-right' for='hora-$tag'>Hora de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                    <div class='form-group'>
                        <input class='form-control text-center input-form' id='hora-$tag' name='hora-$tag' type='time' />
                        <div id='hora-$tag-errors'></div>
                    </div>
                </div>

                <div class='col-md-4 py-2'>
                    <label class='label-form text-right' for='uenta-$tag'>Cuenta ordenante (Cliente)</label>
                    <label class='mark-required text-danger fw-bold'></label>
                    <div class='form-group'>
                        <select class='form-select text-center input-form' id='cuenta-$tag' name='cuenta-$tag' disabled>
                            <option value='' id='default-cuenta-$tag'>- - - -</option>
                            <optgroup id='ordenante-$tag' class='contenedor-cuenta-$tag text-start ps-2'> </optgroup>
                        </select>
                        <div id='cuenta-$tag-errors'></div>
                    </div>
                </div>

                <div class='col-md-4 py-2'>
                    <label class='label-form text-right' for='benef-$tag'>Cuenta beneficiario (Mis cuentas)</label>
                    <label class='mark-required text-danger fw-bold'></label>
                    <div class='form-group'>
                        <select class='form-select text-center input-form' id='benef-$tag' name='benef-$tag' disabled>
                            <option value='' id='default-benef-$tag'>- - - -</option>
                            <optgroup id='beneficiario-$tag' class='contenedor-beneficiario-$tag text-start ps-2'> </optgroup>
                        </select>
                        <div id='benef-$tag-errors'></div>
                    </div>
                </div>
            </div>

            <div class='row'>
                <div class='col-md-4 py-2'>
                    <label class='label-form text-right' for='transaccion-$tag'>No. de transaccion</label>
                    <label class='mark-required text-danger fw-bold'></label>
                    <div class='form-group'>
                        <input class='form-control text-center input-form' id='transaccion-$tag' name='transaccion-$tag' placeholder='N° de Transaccion' type='number' disabled/>
                        <div id='transaccion-$tag-errors'>
                        </div>
                    </div>
                </div>
            </div>

            <div class='row mt-3'>
                <div class='col-md-12'>
                    <div class='new-tooltip icon tip'> 
                        <label class='label-sub' for='fecha-creacion'>CFDIS RELACIONADOS </label> <span
                        class='fas fa-question-circle small text-primary-emphasis'></span>
                        <span class='tiptext'>Para agregar una factura realice la b&uacute;squeda por Folio de la factura y se cargaran los datos, la b&uacute;squeda se limita a las facturas asignadas al cliente seleccionado en el campo Cliente.</span>
                    </div>
                </div>
            </div>

            <div class='row scrollX'>
                <div class='col-md-12'>
                    <table class='table tab-hover table-condensed table-responsive table-row thead-form'>
                        <tbody >
                            <tr>
                                <td colspan='2'>
                                    <label class='label-form mb-1' for='factura-$tag'>Folio factura</label>
                                    <input id='id-factura-$tag' type='hidden' /><input class='form-control text-center input-form' id='factura-$tag' name='factura-$tag' placeholder='Factura' type='text' oninput='aucompletarFactura();'/>
                                </td>
                                <td colspan='2'>
                                    <label class='label-form mb-1' for='uuid-$tag'>UUID Factura</label>
                                    <input class='form-control cfdi text-center input-form' id='uuid-$tag' name='uuid-$tag' placeholder='UUID del cfdi' type='text'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='type-$tag'>Tipo factura</label>
                                    <select class='form-select text-center input-form' id='type-$tag' name='type-$tag'>
                                        <option value='' id='default-tipo-$tag'>- - - -</option>
                                        <option value='f' id='tipo-f-$tag'>Factura</option>
                                        <option value='c' id='tipo-c-$tag'>Carta Porte</option>
                                    </select>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='monedarel-$tag'>Moneda factura</label>
                                    <input id='cambiocfdi-$tag' type='hidden' />
                                    <input id='metcfdi-$tag' type='hidden' />
                                    <select class='form-select text-center input-form' id='monedarel-$tag' name='monedarel-$tag'>
                                        <option value='' id='default-moneda-$tag'>- - - -</option>
                                        <optgroup id='moncfdi-$tag' class='contenedor-moneda-$tag'> </optgroup>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class='label-form mb-1' for='parcialidad-$tag'>No. Parcialidad</label>
                                    <input class='form-control text-center input-form' id='parcialidad-$tag' disabled name='parcialidad-$tag' placeholder='No Parcialidad' type='text'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='total-$tag'>Total factura</label>
                                    <input class='form-control text-center input-form' id='total-$tag' disabled name='total-$tag' placeholder='Total de Factura' type='number' step='any'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='anterior-$tag'>Monto anterior</label>
                                    <input class='form-control text-center input-form' id='anterior-$tag' disabled name='anterior-$tag' placeholder='Monto Anterior' type='number' step='any'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='monto-$tag'>Monto a pagar</label>
                                    <input class='form-control text-center input-form' id='monto-$tag' name='monto-$tag' placeholder='Monto Pagado' type='number' step='any' oninput='calcularRestante()'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='restante-$tag'>Monto restante</label>
                                    <input class='form-control text-center input-form' id='restante-$tag' disabled name='cantidad' placeholder='Monto Restante' type='number' step='any'/>
                                </td>
                                <td class='text-center'>
                                    <label class='label-space text-light' for='btn-agregar-cfdi'>Algo</label>
                                    <button id='btn-agregar-cfdi' class='button-modal' onclick='agregarCFDI();'><span class='fas fa-plus'></span> Agregar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class='table tab-hover table-condensed table-responsive table-row table-head' id='lista-cfdi-$tag'>

                    </table>
                </div>
            </div>
        </div><cut>$tag";
        return $datos;
    }

    public function loadFactura($id, $idpago, $type) {
        return ($type == 'f') ? $this->dataFactura($id, $idpago) : (($type == 'c') ? $this->dataFacturaCarta($id, $idpago) : false);
    }

    private function dataFactura($id, $idpago) {
        $datos = "";
        $getfactura = $this->getFactura($id);
        $actual = reset($getfactura); 
    
        if ($actual !== false) { 
            $iddatosfactura = $actual['iddatos_factura'];
            $tcambio = $actual['tcambio'];
            $idmoneda = $actual['id_moneda'];
            $uuid = $actual['uuid'];
            $cmetodo = $actual['id_metodo_pago'];
            $totalfactura = $actual['totalfactura'];
    
            $parcialidad = $this->getParcialidad($iddatosfactura, $idpago);
            $noparc = $parcialidad - 1;
            $montoanterior = ($noparc == '0') ? $actual['totalfactura'] : $this->getMontoAnterior($noparc, $iddatosfactura);
            $datos = "$iddatosfactura</tr>$uuid</tr>$tcambio</tr>$idmoneda</tr>$cmetodo</tr>$totalfactura</tr>$parcialidad</tr>$montoanterior</tr>$noparc";
        }
        return $datos;
    }
    
    private function getFactura($id) {
        $consultado = false;
        $consulta = "SELECT f.iddatos_factura, f.letra, f.folio_interno_fac, f.tcambio, f.uuid, f.totalfactura,f.status_pago, f.id_moneda, f.id_metodo_pago FROM datos_factura f WHERE f.iddatos_factura=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getParcialidad($idfactura, $idpago) {
        $datos = "1";
        $parcialidad = $this->getParcialidadAux($idfactura, $idpago);
        foreach ($parcialidad as $p) {
            $datos = $p['par'];
        }
        $datos2 = "";
        $parcialidad2 = $this->getParcialidadAux2($idfactura);
        foreach ($parcialidad2 as $p) {
            $datos2 = $p['par'];
        }
        if ($datos2 > $datos) {
            $datos = $datos2;
        }
        return $datos;
    }
    
    private function getParcialidadAux($idfactura, $idpago) {
        $consultado = false;
        $consulta = "SELECT (noparcialidad)+1 par FROM detallepago dt INNER JOIN pagos p ON (p.tagpago=dt.detalle_tagencabezado) WHERE pago_idfactura=:id AND cancelado != '1' AND p.tagpago != :idpago AND type=:tipo;";
        $valores = array("id" => $idfactura,
            "idpago" => $idpago,
            "tipo" => 'f');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getMontoAnteriorAux($noparcialidad, $idfactura_tmp) {
        $consultado = false;
        $consulta = "SELECT montoinsoluto FROM detallepago WHERE noparcialidad=:noparcialidad AND pago_idfactura=:idfactura AND type=:tipo;";
        $val = array("noparcialidad" => $noparcialidad,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'f');
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getMontoAnterior($noparcialidad_tmp, $idfactura_tmp) {
        $totalfactura = $this->getMontoAnteriorAux($noparcialidad_tmp, $idfactura_tmp);
        if (empty($totalfactura)) {
            $totalfactura = $this->getMontoAnteriorAux2($noparcialidad_tmp, $idfactura_tmp);
        }
        return $totalfactura ?? "";
    }
    
    private function getParcialidadAux2($idfactura) {
        $consulta = "SELECT (noparcialidadtmp)+1 as par FROM tmppago WHERE idfacturatmp=:id and sessionid=:sid AND type=:tipo;";
        $valores = array(
            "id" => $idfactura,
            "sid" => session_id(),
            "tipo" => 'f'
        );
        return $this->consultas->getResults($consulta, $valores) ?: false;
    }

    private function getMontoAnteriorAux2($noparcialidad_tmp, $idfactura_tmp) {
        $consulta = "SELECT montoinsolutotmp FROM tmppago WHERE noparcialidadtmp=:noparcialidad AND idfacturatmp=:idfactura AND type=:tipo;";
        $val = array(
            "noparcialidad" => $noparcialidad_tmp,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'f'
        );
        return $this->consultas->getResults($consulta, $val) ?: false;
    }

    private function getParcialidadCarta($idfactura, $idpago) {
        $parcialidad = $this->getParcialidadCartaAux($idfactura, $idpago);
        foreach ($parcialidad as $p) {
            return $p['par'];
        }
        
        $parcialidad2 = $this->getParcialidadCartaAux2($idfactura);
        foreach ($parcialidad2 as $p2) {
            return $p2['par'];
        }
        return "1";
    }

    private function getParcialidadCartaAux($idfactura, $idpago) {
        $consulta = "SELECT (noparcialidad)+1 par FROM detallepago dt INNER JOIN pagos p ON (p.tagpago=dt.detalle_tagencabezado) WHERE pago_idfactura=:id AND cancelado != '1' AND p.tagpago != :idpago AND type=:tipo;";
        $valores = array("id" => $idfactura,
            "idpago" => $idpago,
            "tipo" => 'c');
        return $this->consultas->getResults($consulta, $valores) ?: false;
    }

    private function getParcialidadCartaAux2($idfactura) {
        $sessionid = session_id();
        $consulta = "SELECT (noparcialidadtmp)+1 as par FROM tmppago WHERE idfacturatmp=:id and sessionid=:sid AND type=:tipo";
        $valores = array(
            "id" => $idfactura,
            "sid" => $sessionid,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $valores) ?: false;
    }
    
    private function getMontoAnteriorCartaAux2($noparcialidad_tmp, $idfactura_tmp) {
        $consulta = "SELECT montoinsolutotmp FROM tmppago WHERE noparcialidadtmp=:noparcialidad AND idfacturatmp=:idfactura AND type=:tipo;";
        $val = array(
            "noparcialidad" => $noparcialidad_tmp,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $val) ?: false;
    }
    
    private function getMontoAnteriorCartaAux($noparcialidad, $idfactura_tmp) {
        $consulta = "SELECT montoinsoluto FROM detallepago WHERE noparcialidad=:noparcialidad AND pago_idfactura=:idfactura AND type=:tipo;";
        $val = array(
            "noparcialidad" => $noparcialidad,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $val) ?: false;
    }
    
    private function getMontoAnteriorCarta($noparcialidad_tmp, $idfactura_tmp) {
        $total = $this->getMontoAnteriorCartaAux($noparcialidad_tmp, $idfactura_tmp);
        
        foreach ($total as $actual) {
            return $actual['montoinsoluto'];
        }
        
        $total2 = $this->getMontoAnteriorCartaAux2($noparcialidad_tmp, $idfactura_tmp);
        foreach ($total2 as $actual2) {
            return $actual2['montoinsolutotmp'];
        }
        return "";
    }
    
    private function getFacturaCarta($id) {
        $consultado = false;
        $consulta = "SELECT f.idfactura_carta, f.letra, f.foliocarta, f.tcambio, f.uuid, f.totalfactura, f.status_pago, f.id_moneda, f.id_metodo_pago FROM factura_carta f WHERE f.idfactura_carta=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function dataFacturaCarta($id, $idpago) {
        $datos = "";
        $getfactura = $this->getFacturaCarta($id);
        foreach ($getfactura as $actual) {
            $iddatosfactura = $actual['idfactura_carta'];
            $tcambio = $actual['tcambio'];
            $idmoneda = $actual['id_moneda'];
            $uuid = $actual['uuid'];
            $idmetodo = $actual['id_metodo_pago'];
            $totalfactura = $actual['totalfactura'];

            $parcialidad = $this->getParcialidadCarta($iddatosfactura, $idpago);
            $noparc = $parcialidad - 1;
            if ($noparc == '0') {
                $montoanterior = $actual['totalfactura'];
            } else {
                $montoanterior = $this->getMontoAnteriorCarta($noparc, $iddatosfactura);
            }

            $datos = "$iddatosfactura</tr>$uuid</tr>$tcambio</tr>$idmoneda</tr>$idmetodo</tr>$totalfactura</tr>$parcialidad</tr>$montoanterior</tr>$noparc";
        }
        return $datos;
    }

    private function checkPago($t) {
        $montoins = $t->getMontoinsolutotmp();
        if (bccomp($montoins, '0', 2) < 0) {
            echo "0El monto ingresado supera al total de la factura";
            return true;
        }
    
        $parcialidad = $t->getParcialidadtmp();
        $idfactura = $t->getIdfacturatmp();
        $type = $t->getType();
    
        if ($this->checkParcialidadAux($parcialidad, $idfactura, $type) || $this->checkParcialidadTmp($parcialidad, $idfactura, $type)) {
            echo "0Ya esta registrado este numero de parcialidad";
            return true;
        }
    
        return false;
    }

    private function checkParcialidadAux($p, $id, $type) {
        $datos = false;
        $consulta = "SELECT * 
                    FROM detallepago dp INNER JOIN pagos p ON p.tagpago = dp.detalle_tagencabezado WHERE dp.noparcialidad=:p AND dp.pago_idfactura=:id AND dp.type=:type AND p.cancelado = 0";
        $val = array("p" => $p,
            "id" => $id,
            "type" => $type);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function checkParcialidadTmp($p, $id, $type) {
        $datos = false;
        $consulta = "SELECT * FROM tmppago WHERE noparcialidadtmp=:p AND idfacturatmp=:id AND type=:type";
        $val = array("p" => $p,
            "id" => $id,
            "type" => $type);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }
    
    public function agregarPago($t) {
        if ($this->checkPago($t)) {
            return false;
        }
    
        $consulta = "INSERT INTO `tmppago` VALUES (:id, :parcialidad, :idfactura, :folio, :uuid, :tcambio, :idmoneda, :cmetodo, :monto, :montoant, :montoins, :total, :type, :tag, :session);";
        $valores = array(
            "id" => null,
            "parcialidad" => $t->getParcialidadtmp(),
            "idfactura" => $t->getIdfacturatmp(),
            "folio" => $t->getFoliotmp(),
            "uuid" => $t->getUuid(),
            "tcambio" => $t->getTcamcfdi(),
            "idmoneda" => $t->getIdmonedacdfi(),
            "cmetodo" => $t->getCmetodo(),
            "monto" => bcdiv($t->getMontopagotmp(), '1', 2),
            "montoant" => bcdiv($t->getMontoanteriortmp(), '1', 2),
            "montoins" => bcdiv($t->getMontoinsolutotmp(), '1', 2),
            "total" => $t->getTotalfacturatmp(),
            "type" => $t->getType(),
            "session" => $t->getSessionid(),
            "tag" => $t->getTag()
        );
    
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
}