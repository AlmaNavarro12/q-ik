<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.controlador/ControladorSat.php';
date_default_timezone_set("America/Mexico_City");
session_start();

use SWServices\Toolkit\SignService as Sellar;
use SWServices\Stamp\StampService as StampService;
use SWServices\Cancelation\CancelationService as CancelationService;
use SWServices\SatQuery\SatQueryService as consultaCfdiSAT;
use PHPMailer\PHPMailer\PHPMailer;

class ControladorPago{

    private $consultas;
    private $controladorSAT;

    function __construct()
    {
        $this->consultas = new consultas();
        $this->controladorSAT = new ControladorSat();
    }

    public function listaServiciosHistorial($REF, $pag, $numreg)
    {
        include '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='p-0 mb-3'>
            <tr class='align-middle'>
                <th></th>
                <th class='col-auto text-center'>No. Folio </th>
                <th class='col-auto text-center'>Fecha de creaci&oacute;n </th>
                <th class='col-auto text-center'>Emisor </th>
                <th class='col-auto text-center'>Receptor </th>
                <th class='col-auto text-center'>Timbre </th>
                <th class='col-auto text-center'>Total </th>
                <th class='col-auto text-center'>Opci&oacute;n</th>
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
                    $iconbell = "fas fa-bell";
                    $colorB = "#34A853";
                    $titbell = "Pago timbrado";
                    $class = 'text-success fw-semibold';
                    $titletimbre = "Cancelar timbre";
                    $functiontimbre = "data-bs-toggle='modal' data-bs-target='#modalcancelar' onclick='setCancelarPago($idpago)'";
                    if ($cancelado == '1') {
                        $titletimbre = "Pago cancelado";
                        $functiontimbre = "href='./com.sine.imprimir/imprimirxml.php?p=$idpago&t=c' target='_blank'";
                        $iconbell = "fas fa-bell";
                        $colorB = "#FFB900";
                        $class = 'fw-semibold' ;
                        $titbell = "Pago cancelado";
                    }
                } else {
                    $titletimbre = "Timbrar pago";
                    $functiontimbre = "onclick=\"xml($idpago);\"";
                    $class = 'text-danger fw-semibold';
                    $iconbell = "fas fa-bell";
                    $colorB = "#ED495C";
                    $titbell = "Pago sin timbrar";
                }
                $emisor = $this->getNombreEmisor($idfiscales);
                $datos .= "<tr>
                        <td style='background-color: $colorrow;'></td>
                        <td class='text-center'>$foliopago</td>
                        <td class='text-center'>$fechaemision</td>
                        <td class='text-center'>$emisor</td>
                        <td class='text-center'>$receptor</td>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <span style='color: $colorB;' class='fas $iconbell'></span>
                                <span class='tiptext $class' style='color: $colorB;'>$titbell</span>
                            </div>
                        </td>
                        <td class='text-center'>$ " . number_format($totalpagado, 2, '.', ',') . "</td>
                        <td class='text-center'>
                        <div class='dropdown dropend'>
                        <button class='button-list dropdown-toggle' title='Opciones' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                        <span class='fas fa-ellipsis-v'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-start z-1'>";
                if ($div[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarPago($idpago);'>Editar pago <span class='text-muted small fas fa-edit'></span></a></li>";
                }
                if ($div[2] == '1' && $uuidpago == "") {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"eliminarPago('$idpago');\">Eliminar pago <span class='text-muted small fas fa-times'></span></a></li>";
                }
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"imprimirpago($idpago);\">Ver pago <span class='text-muted small fas fa-file'></span></a></li>";
                
                if($uuidpago != "" && $cancelado != 1){
                    $datos .= "<div class='notification-link py-2 ps-3'><a class='text-decoration-none text-secondary-emphasis' href='./com.sine.imprimir/imprimirxml.php?p=$idpago&t=a' target='_blank'>Ver XML <span class='text-muted small fas fa-download'></span></a></div>";
                }
                
                if ($div[3] == '1') {
                    $datos .= "<div class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' $functiontimbre> $titletimbre <span class='text-muted small fas fa-bell'></span></a></div>";
                }

                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#enviarrecibo' onclick='showCorreos($idpago);'>Enviar <span class='text-muted small fas fa-envelope'></span></a></li>";

                if ($uuidpago != "") {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis lh-base' data-bs-toggle='modal' data-bs-target='#modal-stcfdi' onclick='checkStatusCancelacion($idpago);'>Comprobar estado <br> del CFDI <span class='text-muted small fas fa-check-circle'></span></a></li>";
                }
                $datos .= "</ul>
                        </div></td>
                       </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarPago";

            $datos .= "</tbody><tfoot><tr><th colspan='4' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='4'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

            if ($finales == 0) {
                $datos .= "<tr><td colspan='10' class='fs-6'>No se encontraron registros.</td></tr>";
            }
        }
        return $datos;
    }

    private function getPermisoById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario)
    {
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

    private function getNumrowsAux($condicion)
    {
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM pagos p INNER JOIN datos_facturacion d ON (d.id_datos=p.pago_idfiscales) $condicion;";
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

    private function getServicios($condicion)
    {
        $consultado = false;
        $consulta = "SELECT p.*, d.color FROM pagos p  INNER JOIN datos_facturacion d ON (d.id_datos=p.pago_idfiscales) $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getNombreEmisor($fid)
    {
        $razonsocial = "";
        $sine = $this->getDatosFacturacion($fid);
        foreach ($sine as $dactual) {
            $razonsocial = $dactual['razon_social'];
        }
        return $razonsocial;
    }

    private function getDatosFacturacion($id)
    {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:id";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function translateMonth($m)
    {
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $m = intval($m);
        return (array_key_exists($m - 1, $months)) ? $months[$m - 1] : "";
    }

    public function getDatosEmisor($fid)
    {
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

    public function getTabla($sessionid, $tag, $idmoneda, $tcambio, $uuid)
    {
        $tabla = $this->tablaPago($sessionid, $tag, $tcambio, $idmoneda, $uuid);
        return $tabla;
    }

    private function tablaPago($sessionid, $tag, $tcambio, $idmoneda, $uuid = "")
    {
        $table = "<corte><thead class='sin-paddding'>
            <tr>
                <th class='text-center col-auto'>FACTURA</th>
                <th class='text-center col-auto'>PARCIALIDAD</th>
                <th class='text-center col-auto'>TOTAL FACTURA</th>
                <th class='text-center col-auto'>MONEDA</th>
                <th class='text-center col-auto'>MONTO ANT.</th>
                <th class='text-center col-auto'>MONTO PAGADO</th>
                <th class='text-center col-auto'>RESTANTE</th>
                <th class='text-center col-auto'>OPCIONES</th>
                </thead><tbody>";

        $totalpagados = 0;
        $disuuid = ($uuid != "") ? "disabled" : "";
        $productos = $this->getPagosTMP($sessionid, $tag);

        foreach ($productos as $pagoactual) {
            $folio = $pagoactual['foliofacturatmp'];
            $noparcialidad = $pagoactual['noparcialidadtmp'];
            $monto = $pagoactual['montotmp'];
            $cmoneda = $pagoactual['nombre_moneda'];
            $montoanterior = $pagoactual['montoanteriortmp'];
            $montoinsoluto = $pagoactual['montoinsolutotmp'];
            $totalfactura = $pagoactual['totalfacturatmp'];
            $idtmp = $pagoactual['idtmppago'];

            $totalpagados += $this->controladorSAT->totalDivisa($monto, $idmoneda, $pagoactual['idmonedatmp'], $pagoactual['tcambiotmp'], $tcambio);
            $table .= "
                <tr>
                    <td class='text-center'>$folio</td>
                    <td class='text-center'>$noparcialidad</td>
                    <td class='text-center'>$ " . number_format($totalfactura, 2, '.', ',') . "</td>
                    <td class='text-center'>$cmoneda</td>
                    <td class='text-center'>$ " . number_format($montoanterior, 2, '.', ',') . "</td>
                    <td class='text-center'>$ " . number_format($monto, 2, '.', ',') . "</td>
                    <td class='text-center'>$ " . number_format($montoinsoluto, 2, '.', ',') . "</td>
                    <td class='text-center d-flex justify-content-center'><a $disuuid class='btn button-list d-flex align-items-center justify-content-center' title='Eliminar' onclick='eliminarcfdi($idtmp);'><span class='fas fa-times'></span></a></td>
                </tr>";
        }

        $monedapago = ($idmoneda == 1) ? "MXN" : (($idmoneda == 2) ? "USD" : (($idmoneda == 3) ? "EUR" : ""));
        $table .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='3'></th>
                    <th><b>TOTAL PAGADO:</b></th>
                    <th>$ " . number_format($totalpagados, 2, '.', ',') . " $monedapago</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>";

        return $table;
    }

    
    
    public function getPagosTMP($sid, $tag)
    {
        $consultado = false;
        $consulta = "SELECT t.* FROM tmppago t WHERE sessionid=:sid AND tmptagcomp=:tag ORDER BY idtmppago ASC;";
        $val = array("sid" => $sid, "tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function genTag()
    {
        $fecha = date('YmdHis');
        $idusu = $_SESSION[sha1("idusuario")];
        $sid = session_id();
        $ranstr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
        $tag = $ranstr . $fecha . $idusu . $sid;
        return $tag;
    }

    public function nuevoComplemento($comp)
    {
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
                                <optgroup id='forma-pago-$tag' class='cont-fpago-$tag text-start'> </optgroup>
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
                            <optgroup id='mpago-$tag' class='contmoneda-$tag text-start'> </optgroup>
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
                            <optgroup id='ordenante-$tag' class='contenedor-cuenta-$tag text-start'> </optgroup>
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
                    <label class='label-form text-right' for='transaccion-$tag'>No. de transacci&oacute;n</label>
                    <label class='mark-required text-danger fw-bold'></label>
                    <div class='form-group'>
                        <input class='form-control text-center input-form' id='transaccion-$tag' name='transaccion-$tag' placeholder='No. de transacción' type='number' disabled/>
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
                                    <input id='id-factura-$tag' type='hidden'/><input class='form-control text-center input-form' id='factura-$tag' name='factura-$tag' placeholder='Factura' type='text' oninput='aucompletarFactura();'/>
                                </td>
                                <td colspan='2'>
                                    <label class='label-form mb-1' for='uuid-$tag'>UUID Factura</label>
                                    <input class='form-control cfdi text-center input-form' id='uuid-$tag' name='uuid-$tag' placeholder='UUID del cfdi' type='text'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='type-$tag'>Tipo factura</label>
                                    <select class='form-select text-center input-form' id='type-$tag' name='type-$tag'>
                                        <option value='' id='default-tipo-$tag'>- - - -</option>
                                        <option value='f' class='text-start' id='tipo-f-$tag'>Factura</option>
                                        <option value='c' class='text-start' id='tipo-c-$tag'>Carta Porte</option>
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
                                    <input class='form-control text-center input-form' id='parcialidad-$tag' name='parcialidad-$tag' placeholder='No Parcialidad' type='text'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='total-$tag'>Total factura</label>
                                    <input class='form-control text-center input-form' id='total-$tag' name='total-$tag' placeholder='Total de factura' type='number' step='any'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='anterior-$tag'>Monto anterior</label>
                                    <input class='form-control text-center input-form' id='anterior-$tag' name='anterior-$tag' placeholder='Monto anterior' type='number' step='any'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='monto-$tag'>Monto a pagar</label>
                                    <input class='form-control text-center input-form' id='monto-$tag' name='monto-$tag' placeholder='Monto pagado' type='number' step='any' oninput='calcularRestante()'/>
                                </td>
                                <td>
                                    <label class='label-form mb-1' for='restante-$tag'>Monto restante</label>
                                    <input class='form-control text-center input-form' id='restante-$tag' name='cantidad' placeholder='Monto restante' type='number' step='any'/>
                                </td>
                                <td class='text-center'>
                                    <label class='label-space text-light' for='btn-agregar-cfdi'>Algo</label>
                                    <button id='btn-agregar-cfdi' class='button-modal col-12' onclick='agregarCFDI();'><span class='fas fa-plus'></span> Agregar</button>
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

    public function loadFactura($id, $idpago, $type)
    {
        return ($type == 'f') ? $this->dataFactura($id, $idpago) : (($type == 'c') ? $this->dataFacturaCarta($id, $idpago) : false);
    }

    private function dataFactura($id, $idpago) {
        $datos = "";
        $getfactura = $this->getFactura($id);
        foreach ($getfactura as $actual) {
            $iddatosfactura = $actual['iddatos_factura'];
            $tcambio = $actual['tcambio'];
            $idmoneda = $actual['id_moneda'];
            $uuid = $actual['uuid'];
            $cmetodo = $actual['id_metodo_pago'];
            $totalfactura = $actual['totalfactura'];

            $parcialidad = $this->getParcialidad($iddatosfactura, $idpago);
            $noparc = $parcialidad - 1;
            if ($noparc == '0') {
                $montoanterior = $actual['totalfactura'];
            } else {
                $montoanterior = $this->getMontoAnterior($noparc, $iddatosfactura);
            }
            $egresos = "</tr>".$this->getTieneEgreso($uuid);
            $datos = "$iddatosfactura</tr>$uuid</tr>$tcambio</tr>$idmoneda</tr>$cmetodo</tr>$totalfactura</tr>$parcialidad</tr>$montoanterior</tr>$noparc".$egresos;
        }
        return $datos;
    }

    private function getTieneEgreso($uuid){
        $egreso = 0;
        $monto = 0;
        $query = "SELECT * FROM cfdiegreso WHERE uuid = :uuid AND aplicada = :aplicada";
        $val = array("uuid" => $uuid, "aplicada" => 0);
        $stmt = $this->consultas->getResults($query, $val);
        foreach($stmt as $rs){
            $egreso++;
            $monto = $rs['montoegreso'];
        }
        return "$egreso</tr>$monto";
    }

    private function getFactura($id)
    {
        $consultado = false;
        $consulta = "SELECT f.iddatos_factura, f.letra, f.folio_interno_fac, f.tcambio, f.uuid, f.totalfactura,f.status_pago, f.id_moneda, f.id_metodo_pago FROM datos_factura f WHERE f.iddatos_factura=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getParcialidad($idfactura, $idpago)
    {
        $datos = "1";
        $parcialidad = $this->getParcialidadAux($idfactura, $idpago);
        if (is_array($parcialidad)) {
            foreach ($parcialidad as $p) {
                $datos = $p['par'];
            }
        }
        $datos2 = "";
        $parcialidad2 = $this->getParcialidadAux2($idfactura);
        if (is_array($parcialidad2)) {
            foreach ($parcialidad2 as $p) {
                $datos2 = $p['par'];
            }
        }
        if ($datos2 > $datos) {
            $datos = $datos2;
        }
        return $datos;
    }

    private function getParcialidadAux($idfactura, $idpago)
    {
        $consultado = false;
        $consulta = "SELECT (noparcialidad)+1 par FROM detallepago dt INNER JOIN pagos p ON (p.tagpago=dt.detalle_tagencabezado) 
        WHERE pago_idfactura=:id AND cancelado != '1' AND p.tagpago != :idpago AND type=:tipo;";
        $valores = array(
            "id" => $idfactura,
            "idpago" => $idpago,
            "tipo" => 'f'
        );
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
        $totalfactura = "";
        $totalfactura2 = "";
        $total = $this->getMontoAnteriorAux($noparcialidad_tmp, $idfactura_tmp);
        foreach ($total as $actual) {
            $totalfactura = $actual['montoinsoluto'];
        }
        if ($totalfactura == "") {
            $total2 = $this->getMontoAnteriorAux2($noparcialidad_tmp, $idfactura_tmp);
            foreach ($total2 as $actual2) {
                $totalfactura2 = $actual2['montoinsolutotmp'];
            }
            $totalfactura = $totalfactura2;
        }
        return $totalfactura;
    }

    private function getMontoAnteriorAux2($noparcialidad_tmp, $idfactura_tmp)
    {
        $consultado = false;
        $consulta = "SELECT montoinsolutotmp FROM tmppago WHERE noparcialidadtmp=:noparcialidad AND idfacturatmp=:idfactura AND type=:tipo;";
        $val = array(
            "noparcialidad" => $noparcialidad_tmp,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'f'
        );
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getParcialidadAux2($idfactura)
    {
        $consultado = false;
        $consulta = "SELECT (noparcialidadtmp)+1 as par FROM tmppago WHERE idfacturatmp=:id and sessionid=:sid AND type=:tipo;";
        $valores = array(
            "id" => $idfactura,
            "sid" => session_id(),
            "tipo" => 'f'
        );
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getParcialidadCarta($idfactura, $idpago)
    {
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

    private function getParcialidadCartaAux($idfactura, $idpago)
    {
        $consulta = "SELECT (noparcialidad)+1 par FROM detallepago dt INNER JOIN pagos p ON (p.tagpago=dt.detalle_tagencabezado) WHERE pago_idfactura=:id AND cancelado != '1' AND p.tagpago != :idpago AND type=:tipo;";
        $valores = array(
            "id" => $idfactura,
            "idpago" => $idpago,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $valores) ?: false;
    }

    private function getParcialidadCartaAux2($idfactura)
    {
        $sessionid = session_id();
        $consulta = "SELECT (noparcialidadtmp)+1 as par FROM tmppago WHERE idfacturatmp=:id and sessionid=:sid AND type=:tipo";
        $valores = array(
            "id" => $idfactura,
            "sid" => $sessionid,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $valores) ?: false;
    }

    private function getMontoAnteriorCartaAux2($noparcialidad_tmp, $idfactura_tmp)
    {
        $consulta = "SELECT montoinsolutotmp FROM tmppago WHERE noparcialidadtmp=:noparcialidad AND idfacturatmp=:idfactura AND type=:tipo;";
        $val = array(
            "noparcialidad" => $noparcialidad_tmp,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $val) ?: false;
    }

    private function getMontoAnteriorCartaAux($noparcialidad, $idfactura_tmp)
    {
        $consulta = "SELECT montoinsoluto FROM detallepago WHERE noparcialidad=:noparcialidad AND pago_idfactura=:idfactura AND type=:tipo;";
        $val = array(
            "noparcialidad" => $noparcialidad,
            "idfactura" => $idfactura_tmp,
            "tipo" => 'c'
        );
        return $this->consultas->getResults($consulta, $val) ?: false;
    }

    private function getMontoAnteriorCarta($noparcialidad_tmp, $idfactura_tmp)
    {
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

    private function getFacturaCarta($id)
    {
        $consultado = false;
        $consulta = "SELECT f.idfactura_carta, f.letra, f.foliocarta, f.tcambio, f.uuid, f.totalfactura, f.status_pago, f.id_moneda, f.id_metodo_pago FROM factura_carta f WHERE f.idfactura_carta=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function dataFacturaCarta($id, $idpago)
    {
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
            $egresos = "</tr>".$this->getTieneEgreso($uuid);
            $datos = "$iddatosfactura</tr>$uuid</tr>$tcambio</tr>$idmoneda</tr>$idmetodo</tr>$totalfactura</tr>$parcialidad</tr>$montoanterior</tr>$noparc".$egresos;
        }
        return $datos;
    }

    private function checkPago($t)
    {
        $check = false;
        $montoins = $t->getMontoinsolutotmp();
        if (bcdiv($montoins, '1', 2) < 0) {
            echo "0El monto ingresado supera al total de la factura.";
            $check = true;
        }

        if (!$check) {
            $datos = $this->checkParcialidadAux($t->getParcialidadtmp(), $t->getIdfacturatmp(), $t->getType());
            foreach ($datos as $actual) {
                echo "00Ya está registrado este número de parcialidad.";
                $check = true;
                break;
            }
        }

        if (!$check) {
            $datos = $this->checkParcialidadTmp($t->getParcialidadtmp(), $t->getIdfacturatmp(), $t->getType());
            foreach ($datos as $actual) {
                echo "01Ya está registrado este número de parcialidad.";
                $check = true;
                break;
            }
        }
        return $check;
    }

    private function checkParcialidadAux($p, $id, $type)
    {
        $datos = false;
        $consulta = "SELECT * FROM detallepago dp INNER JOIN pagos p ON p.tagpago = dp.detalle_tagencabezado WHERE dp.noparcialidad=:p AND dp.pago_idfactura=:id AND dp.type=:type AND p.cancelado = 0";
        $val = array(
            "p" => $p,
            "id" => $id,
            "type" => $type
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return is_array($datos) ? $datos : array();
    }

    private function checkParcialidadTmp($p, $id, $type)
    {
        $datos = false;
        $consulta = "SELECT * FROM tmppago WHERE noparcialidadtmp=:p AND idfacturatmp=:id AND type=:type";
        $val = array(
            "p" => $p,
            "id" => $id,
            "type" => $type
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function agregarPago($t)
    {
        if ($this->checkPago($t)) {
            return false;
        }

        $consulta = "INSERT INTO `tmppago` VALUES (:id, :parcialidad, :idfactura, :folio, :uuid, :tcambio, :idmoneda, :nombre_moneda, :cmetodo, :monto, :montoant, :montoins, :total, :type, :tag, :session);";
        $valores = array(
            "id" => null,
            "parcialidad" => $t->getParcialidadtmp(),
            "idfactura" => $t->getIdfacturatmp(),
            "folio" => $t->getFoliotmp(),
            "uuid" => $t->getUuid(),
            "tcambio" => $t->getTcamcfdi(),
            "idmoneda" => $t->getIdmonedacdfi(),
            "nombre_moneda" => $t->getNombreMoneda(),
            "cmetodo" => $t->getCmetodo(),
            "monto" => bcdiv($t->getMontopagotmp(), '1', 2),
            "montoant" => bcdiv($t->getMontoanteriortmp(), '1', 2),
            "montoins" => bcdiv($t->getMontoinsolutotmp(), '1', 2),
            "total" => $t->getTotalfacturatmp(),
            "type" => $t->getType(),
            "tag" => $t->getTag(),
            "session" => $t->getSessionid()
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    /*public function eliminar($idtemp, $sessionid)
    {
        $eliminado = false;
        $consulta = "DELETE FROM `tmppago` WHERE idtmppago=:id;";
        $valores = array("id" => $idtemp);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }*/

    public function eliminar($idtemp)
    {
        $eliminado = false;

        $consulta_idfactura = "SELECT idfacturatmp FROM tmppago WHERE idtmppago = :id";
        $valores_idfactura = array("id" => $idtemp);
        $idfactura = $this->consultas->getResults($consulta_idfactura, $valores_idfactura);

        if ($idfactura) {
            $consulta_modificar = "UPDATE datos_factura SET status_pago = 2 WHERE iddatos_factura = :idfactura";
            $valores_modificar = array("idfactura" => $idfactura[0]['idfacturatmp']);
            $this->consultas->execute($consulta_modificar, $valores_modificar);

            $consulta_eliminar = "DELETE FROM tmppago WHERE idtmppago = :id";
            $valores_eliminar = array("id" => $idtemp);
            $eliminado = $this->consultas->execute($consulta_eliminar, $valores_eliminar);
        }
        return $eliminado;
    } 

    public function cancelar($sid, $tag = "")
    {
        $com = "";
        if ($tag != "") {
            $com = " AND tmptagcomp=:tag";
        }
        $eliminado = false;
        $consulta = "DELETE FROM `tmppago` WHERE sessionid=:id$com;";
        $valores = array(
            "id" => $sid,
            "tag" => $tag
        );
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    public function validarPago($p, $objimpuesto)
    {
        $datos = $this->insertarPago($p, $objimpuesto);
        return $datos;
    }

    private function insertarPago($p, $objimpuesto)
    {
        $insertado = false;
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $tag = $this->genTag();
        $folios = $this->getFolio($p->getFoliopago());
        list($serie, $letra, $nfolio) = explode("</tr>", $folios, 3);
        $consulta = "INSERT INTO `pagos` VALUES (:idpago, :rfcemisor, :razonemisor, :clvregemisor, :regfiscalemisor, :codpemisor, :serie, :letra, :foliopago, :fechacreacion, :hora_creacion, :pago_idcliente, :nombrecliente, :rfcreceptor, :razonreceptor, :regfiscalreceptor, :codpreceptor, :pago_idfiscales, :totalpagado, :chfirmar, :cadenaoriginalpago, :nocertsatpago, :nocertcfdipago, :uuidpago, :sellosatpago, :sellocfdipago, :fechatimbrado, :qrcode, :cfdipago, :cfdicancel, :cancelado, :tagpago, :objimpuesto, :sessionid)";
        $valores = array(
            "idpago" => null,
            "rfcemisor" => '',
            "razonemisor" => '',
            "clvregemisor" => '',
            "regfiscalemisor" => '',
            "codpemisor" => '',
            "serie" => $serie,
            "letra" => $letra,
            "foliopago" => $nfolio,
            "fechacreacion" => $fecha,
            "hora_creacion" => $hora,
            "pago_idcliente" => $p->getIdcliente(),
            "nombrecliente" => $p->getNombrecliente(),
            "rfcreceptor" => $p->getRfccliente(),
            "razonreceptor" => $p->getRazoncliente(),
            "regfiscalreceptor" => $p->getRegfiscalcliente(),
            "codpreceptor" => $p->getCodpostal(),
            "pago_idfiscales" => $p->getPago_idfiscales(),
            "totalpagado" => '0',
            "chfirmar" => $p->getChfirmar(),
            "cadenaoriginalpago" => null,
            "nocertsatpago" => null,
            "nocertcfdipago" => null,
            "uuidpago" => null,
            "sellosatpago" => null,
            "sellocfdipago" => null,
            "fechatimbrado" => null,
            "qrcode" => null,
            "cfdipago" => null,
            "cfdicancel" => null,
            "cancelado" => '0',
            "tagpago" => $tag,
            "objimpuesto" => $objimpuesto,
            "sessionid" => $_SESSION[sha1("idusuario")]
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return "<cut>$tag<cut>";
    }

    private function getFoliobyID($id)
    {
        $consultado = false;
        $consulta = "SELECT * FROM folio WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getFolio($id)
    {
        $datos = "";
        $folios = $this->getFoliobyID($id);
        foreach ($folios as $actual) {
            $serie = $actual['serie'];
            $letra = $actual['letra'];
            $consecutivo = $actual['consecutivo'];
            if ($consecutivo < 10) {
                $consecutivo = "000$consecutivo";
            } else if ($consecutivo < 100 && $consecutivo >= 10) {
                $consecutivo = "00$consecutivo";
            } else if ($consecutivo < 1000 && $consecutivo >= 100) {
                $consecutivo = "0$consecutivo";
            }
            $datos = "$serie</tr>$letra</tr>$consecutivo";
            $update = $this->updateFolioConsecutivo($id);
        }
        return $datos;
    }

    private function updateFolioConsecutivo($id)
    {
        $consultado = false;
        $consulta = "UPDATE folio SET consecutivo=(consecutivo+1) WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    public function insertarComplemento($p) {
        $insertar = false;
        $consulta = "INSERT INTO complemento_pago VALUES (:id, :orden, :idforma, :c_forma, :nombre_forma, :idmoneda, :nombremoneda, :tcambio, :fechapago, :horapago, :cuentaord, :cuentabnf, :notransaccion, :total, :tagcomp, :tagpago);";
        $val = array("id" => null,
            "orden" => $p->getOrden(),
            "idforma" => $p->getPagoidformapago(),
            "c_forma" => $p->getCForma(),
            "nombre_forma" => $p->getNombreForma(),
            "idmoneda" => $p->getPagoidmoneda(),
            "nombremoneda" => $p->getNombreMoneda(),
            "tcambio" => $p->getTipocambio(),
            "fechapago" => $p->getFechapago(),
            "horapago" => $p->getHorapago(),
            "cuentaord" => $p->getPago_idbanco(),
            "cuentabnf" => $p->getIdbancoB(),
            "notransaccion" => $p->getNooperacion(),
            "total" => '0',
            "tagcomp" => $p->getTagcomp(),
            "tagpago" => $p->getTagpago());
        $insertar = $this->consultas->execute($consulta, $val);
        $this->detallePago($p->getSessionid(), $p->getTagpago(), $p->getTagcomp(), $p->getTipocambio(), $p->getPagoidmoneda());
        return $insertar;
    }

    public function detallePago($idsession, $tagpago, $tagcomp, $tcambio, $monedapago)
    {
        $totalpagado = 0;
        $cfdi = $this->getPagosTMP($idsession, $tagcomp);
        foreach ($cfdi as $actual) {
            $totalpagado += $actual['montotmp'];
            $this->consultas->execute("INSERT INTO `detallepago` VALUES (:iddetallepago, :noparcialidad, :pagoidfactura, :folio, :uuid, :tcambio, :idmoneda, :nombremoneda, :cmetodo, :monto, :montoanterior, :montoinsoluto, :totalfactura, :type, :tagpago, :tagcomp);", [
                "iddetallepago" => null,
                "noparcialidad" => $actual['noparcialidadtmp'],
                "pagoidfactura" => $actual['idfacturatmp'],
                "folio" => $actual['foliofacturatmp'],
                "uuid" => $actual['uuidtmp'],
                "tcambio" => $actual['tcambiotmp'],
                "idmoneda" => $actual['idmonedatmp'],
                "nombremoneda" => $actual['nombre_moneda'],
                "cmetodo" => $actual['idmetodotmp'],
                "monto" => $actual['montotmp'],
                "montoanterior" => $actual['montoanteriortmp'],
                "montoinsoluto" => $actual['montoinsolutotmp'],
                "totalfactura" => $actual['totalfacturatmp'],
                "type" => $actual['type'],
                "tagpago" => $tagpago,
                "tagcomp" => $tagcomp
            ]);

            $estado = ($actual['montoinsolutotmp'] != '0') ? '4' : '1';
            if ($actual['idfacturatmp'] != '0') {
                $this->estadoFactura($actual['idfacturatmp'], $estado, $actual['type']);
            }
        }

        $this->cancelar($idsession, $tagcomp);
        $this->consultas->execute("UPDATE `complemento_pago` SET total_complemento=:total WHERE tagcomplemento=:tag;", [
            "tag" => $tagcomp,
            "total" => $totalpagado
        ]);

        $this->consultas->execute("UPDATE `pagos` SET totalpagado=(totalpagado+$totalpagado) WHERE tagpago=:tag;", [
            "tag" => $tagpago
        ]);
        return true;
    }

    public function estadoFactura($idfactura, $estado, $type)
    {
        $consulta = "";
        $actualizado = false;
        if ($type == 'f') {
            $consulta = "UPDATE `datos_factura` SET status_pago=:estado WHERE iddatos_factura=:id";
        } else if ($type == 'c') {
            $consulta = "UPDATE `factura_carta` SET status_pago=:estado WHERE idfactura_carta=:id";
        }
        $valores = array("id" => $idfactura, "estado" => $estado);
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function checkCancelados($idpago) {
        $datos = $this->guardarXML($idpago);
        return $datos;
    }

    /*private function getTotalesImpuestos($tag){
        $subtotal = 0;
        $traslado = 0;
        $retencionIVA = 0;
        $retencionISR = 0;
        $retencionIEPS = 0;
        $consulta = "SELECT dp.*, df.subtotal, df.subtotaliva, df.subtotalret FROM detallepago dp
                    INNER JOIN datos_factura df ON df.uuid = dp.uuiddoc WHERE dp.detalle_tagencabezado=:tag
                    UNION ALL SELECT dp.*, fc.subtotal, fc.subtotaliva, fc.subtotalret FROM detallepago dp INNER JOIN factura_carta fc ON fc.uuid = dp.uuiddoc
                    WHERE dp.detalle_tagencabezado=:tag ORDER BY iddetallepago";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        
        foreach ($consultado as $actual) {
            $subtotal += $actual['subtotal'];
            if ($actual['subtotaliva'] != "") {
                $imp_tras = explode("<impuesto>", $actual['subtotaliva']);
                foreach ($imp_tras as $im_tras) {
                    $divIVA = explode("-", $im_tras);
                    $traslado += $divIVA[0];
                }
            }

            if ($actual['subtotalret'] != "") {
                $imp_ret = explode("<impuesto>", $actual['subtotalret']);
                foreach ($imp_ret as $im_ret) {
                    $divRET = explode("-", $im_ret);
                    switch ($divRET[2]) {
                        case 1:
                            $retencionISR += $divRET[0];
                            break;
                        case 2:
                            $retencionIVA += $divRET[0];
                            break;
                        case 3:
                            $retencionIEPS += $divRET[0];
                            break;
                    }
                }
            }
        }
        return "$subtotal</tr>$traslado</tr>$retencionIVA</tr>$retencionISR</tr>$retencionIEPS";
    }*/

    public function getComplementoPago($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM complemento_pago WHERE tagpago=:tag ORDER BY ordcomplemento ASC;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDetallePago($tag, $comp) {
        $consultado = false;
        $consulta = "SELECT dp.*, df.subtotal, df.subtotaliva, df.subtotalret 
                    FROM detallepago dp
                    INNER JOIN datos_factura df ON df.uuid = dp.uuiddoc
                    WHERE dp.detalle_tagencabezado=:tag
                    AND dp.detalle_tagcomplemento=:comp
                    UNION ALL                    
                    SELECT dp.*, fc.subtotal, fc.subtotaliva, fc.subtotalret 
                    FROM detallepago dp
                    INNER JOIN factura_carta fc ON fc.uuid = dp.uuiddoc
                    WHERE dp.detalle_tagencabezado=:tag
                    AND dp.detalle_tagcomplemento=:comp
                    ORDER BY iddetallepago";
        $val = array("tag" => $tag,
            "comp" => $comp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getBaseRetenciones($folio, $option){
        $base = 0;
        $consulta = "";
        if($option == 1){
            $consulta = "select ifnull(sum(dc.totalunitario),0) AS base 
                        from datos_factura fc 
                        inner join detalle_factura dc on dc.tagdetallef = fc.tagfactura 
                        where concat(fc.letra,fc.folio_interno_fac) like '%$folio%' 
                        and dc.traslados != '' 
                        union all 
                        select ifnull(sum(dc.totalunitario),0) AS base 
                        from factura_carta fc 
                        inner join detallefcarta dc on dc.tagdetfactura = fc.tagfactura 
                        where concat(fc.letra,fc.foliocarta) like '%$folio%' 
                        and dc.traslados != ''";
        }else if($option == 2){
            $consulta = "select ifnull(sum(dc.totalunitario),0) AS base 
                        from datos_factura fc
                        inner join detalle_factura dc on dc.tagdetallef = fc.tagfactura
                        where concat(fc.letra,fc.folio_interno_fac) like '%$folio%' 
                        and dc.retenciones != ''
                        union all
                        select ifnull(sum(dc.totalunitario),0) AS base 
                        from factura_carta fc
                        inner join detallefcarta dc on dc.tagdetfactura = fc.tagfactura 
                        where concat(fc.letra,fc.foliocarta) like '%$folio%' 
                        and dc.retenciones != ''";
        }
        $stmt = $this->consultas->getResults($consulta, null);
        foreach($stmt as $rs){
            $base += $rs['base'];
        }
        return $base;
    }

    private function guardarXML($idpago) { 
        $timbre = false;
        $pagos = $this->getPagoById($idpago);
        foreach ($pagos as $pagoactual) {
            $idpago = $pagoactual['idpago'];
            $serie = $pagoactual['serie'];
            $letra = $pagoactual['letra'];
            $folio = $pagoactual['foliopago'];
            $foliopago = $letra . $folio;
            $rfcCliente = $pagoactual['rfcreceptor'];
            $razonCliente = $pagoactual['razonreceptor'];
            $regfcliente = $pagoactual['regfiscalreceptor'];
            $codpreceptor = $pagoactual['codpreceptor'];
            $idfiscales = $pagoactual['pago_idfiscales'];
            $totalpago = $pagoactual['totalpagado'];
            $tagpago = $pagoactual['tagpago'];
            $objimpuesto = $pagoactual['objimpuesto'];
        }

        $empresa = $this->getDatosFacturacion($idfiscales);
        foreach ($empresa as $eactual) {
            $rfcemisor = $eactual['rfc'];
            $razonemisor = $eactual['razon_social'];
            $clvreg = $eactual['c_regimenfiscal'];
            $regimen = $eactual['regimen_fiscal'];
            $codpemisor = $eactual['codigo_postal'];
            $csd = $eactual['csd'];
            $nocertificado = $eactual['numcsd'];
        }

        $xml = new DomDocument('1.0', 'UTF-8');
        $raiz = $xml->createElementNS('http://www.sat.gob.mx/cfd/4', 'cfdi:Comprobante');
        $raiz = $xml->appendChild($raiz);

        $fecha = date('Y-m-d\TH:i:s', strtotime('-1 hour'));
        $raiz->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $raiz->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/Pagos20 http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd');
        $raiz->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:pago20', 'http://www.sat.gob.mx/Pagos20');
        $raiz->setAttribute('Version', '4.0');
        $raiz->setAttribute('Serie', $serie);
        $raiz->setAttribute('Folio', $foliopago);
        $raiz->setAttribute('Fecha', $fecha);
        $raiz->setAttribute('SubTotal', '0');
        $raiz->setAttribute('Moneda', 'XXX');
        $raiz->setAttribute('Total', '0');
        $raiz->setAttribute('TipoDeComprobante', 'P');
        $raiz->setAttribute('Exportacion', '01');
        $raiz->setAttribute('LugarExpedicion', $codpemisor);
        $raiz->setAttribute('NoCertificado', $nocertificado);
        
        //Convertir certificado a B64 con openssl enc -in "CSD/00001000000407565367.cer" -a -A -out "cerB64.txt" 
        $raiz->setAttribute('Certificado', $csd);
        $emisor = $xml->createElement('cfdi:Emisor');
        $emisor = $raiz->appendChild($emisor);
        $emisor->setAttribute('Rfc', $rfcemisor);
        $emisor->setAttribute('Nombre', strtoupper($razonemisor));
        $emisor->setAttribute('RegimenFiscal', $clvreg);

        $receptor = $xml->createElement('cfdi:Receptor');
        $receptor = $raiz->appendChild($receptor);
        $receptor->setAttribute('Rfc', $rfcCliente);
        $receptor->setAttribute('Nombre', strtoupper($razonCliente));
        $receptor->setAttribute('DomicilioFiscalReceptor', $codpreceptor);
        $divreg = explode("-", $regfcliente);
        $receptor->setAttribute('RegimenFiscalReceptor', $divreg[0]);
        $receptor->setAttribute('UsoCFDI', 'CP01');

        $conceptos = $xml->createElement('cfdi:Conceptos');
        $conceptos = $raiz->appendChild($conceptos);
        $concepto = $xml->createElement('cfdi:Concepto');
        $concepto = $conceptos->appendChild($concepto);
        $concepto->setAttribute('ClaveProdServ', '84111506');
        $concepto->setAttribute('Cantidad', '1');
        $concepto->setAttribute('ClaveUnidad', 'ACT');
        $concepto->setAttribute('Descripcion', 'Pago');
        $concepto->setAttribute('ValorUnitario', '0');
        $concepto->setAttribute('Importe', '0');
        $concepto->setAttribute('ObjetoImp', '01');

        $complemento = $xml->createElement('cfdi:Complemento');
        $complemento = $raiz->appendChild($complemento);
        $pagos = $xml->createElement('pago20:Pagos');
        $pagos = $complemento->appendChild($pagos);
        $pagos->setAttribute('Version', '2.0');

        $totales = $xml->createElement('pago20:Totales');
        $totales = $pagos->appendChild($totales);
        $div_obj_imp = explode("-", $objimpuesto);

        //INICIO DE MODULOS DE IMPUESTOS
        if($div_obj_imp[0] == '01'){
            $totales->setAttribute('MontoTotalPagos', number_format($totalpago, 2, '.', ''));
        }
        else if($div_obj_imp[0] == '02'){
            /*echo $tagpago;
            $totalesCFDIS = $this->getTotalesImpuestos($tagpago);
            $divTotales = explode("</tr>",$totalesCFDIS);
            $subTotlGral = $divTotales[0];
            $subIvaGral = $divTotales[1];
            $retencionIVA = $divTotales[2];
            $retencionISR = $divTotales[3];
            $retencionIEPS = $divTotales[4];*/
            $totales->setAttribute('MontoTotalPagos', number_format($totalpago, 2, '.', ''));  
            $subTotlGral = 0;
            $subIvaGral = 0;
            $retencionIVA = 0;
            $retencionISR = 0;
            $retencionIEPS = 0;
        }

        $complementos = $this->getComplementoPago($tagpago);
        foreach ($complementos as $actualcfdi) {
            $cformapago = $actualcfdi['c_forma_pago'];
            $idmoneda = $actualcfdi['complemento_idmoneda'];
            $tcambio = $actualcfdi['complemento_tcambio'];
            $cmoneda = $actualcfdi['nombre_moneda'];
            $fechapago = $actualcfdi['complemento_fechapago'];
            $horapago = $actualcfdi['complemento_horapago'];
            $cuentaord = $actualcfdi['complemento_idcuentaOrd'];
            $cuentabnf = $actualcfdi['complemento_idcuentaBnf'];
            $totalcomp = $actualcfdi['total_complemento'];
            $tagcomplemento = $actualcfdi['tagcomplemento'];

            $pago = $xml->createElement('pago20:Pago');
            $pago = $pagos->appendChild($pago);
            $fechapago2 = $fechapago . 'T' . $horapago . ':00';
            $pago->setAttribute('FechaPago', $fechapago2);
            $pago->setAttribute('FormaDePagoP', $cformapago);
            $pago->setAttribute('MonedaP', $cmoneda);
            
            $pago->setAttribute('TipoCambioP', '1');
            $pago->setAttribute('Monto', number_format($totalcomp, 2, '.', ''));
            if ($cuentaord != '0') {
                $banco = $this->controladorSAT->getRFCBancoOrdenante($cuentaord);
                $pago->setAttribute('RfcEmisorCtaOrd', $banco);
            }

            if ($cuentabnf != '0') {
                $bancoB = $this->controladorSAT->getRFCBancoBeneficiario($cuentabnf);
                $pago->setAttribute('RfcEmisorCtaBen', $bancoB);
            }

            $bandera_tras = 0;
            $bandera_ret = 0;
            $cfdis = $this->getDetallePago($tagpago, $tagcomplemento);
            foreach ($cfdis as $cfdiactual) {
                $noparcialidad = $cfdiactual['noparcialidad'];
                $monto = $cfdiactual['monto'];
                $montoanterior = $cfdiactual['montoanterior'];
                $montoinsoluto = $cfdiactual['montoinsoluto'];
                $folioF = $cfdiactual['foliodoc'];
                $uuid = $cfdiactual['uuiddoc'];
                $idmonedadoc = $cfdiactual['idmonedadoc'];
                $monedaP = $cfdiactual['nombre_moneda'];
                $tipocambioF = $cfdiactual['tcambiodoc'];
                $traslado = $cfdiactual['subtotaliva'];
                $retencio = $cfdiactual['subtotalret'];
                
                if ($tcambio != $tipocambioF) {
                    $tipocambioF = bcdiv($tcambio, '1', 6);
                } else {
                    $tipocambioF = '1';
                }

                if ($idmoneda == $idmonedadoc) {
                    $tipocambioF = '1';
                }
                $doctorel = $xml->createElement('pago20:DoctoRelacionado');
                $doctorel = $pago->appendChild($doctorel);
                $doctorel->setAttribute('IdDocumento', $uuid);
                $doctorel->setAttribute('Folio', $folioF);
                $doctorel->setAttribute('MonedaDR', $monedaP);
                $doctorel->setAttribute('EquivalenciaDR', $tipocambioF);
                $doctorel->setAttribute('NumParcialidad', $noparcialidad);
                $doctorel->setAttribute('ImpSaldoAnt', $montoanterior);
                $doctorel->setAttribute('ImpPagado', $monto);
                $doctorel->setAttribute('ImpSaldoInsoluto', $montoinsoluto);
                $doctorel->setAttribute('ObjetoImpDR', $div_obj_imp[0]);

                if ($div_obj_imp[0] == '02') {
                    $nodoImpuestos = $xml->createElement('pago20:ImpuestosDR');
                    $nodoImpuestos = $doctorel->appendChild($nodoImpuestos);
                    if ($retencio != "") {
                        $impuestosCalculados = $this->generarImpuestosDr($monto, $retencio);
                        $bandera_ret = 1;
                        $subnodoRetencion = $xml->createElement('pago20:RetencionesDR');
                        $subnodoRetencion = $nodoImpuestos->appendChild($subnodoRetencion);
                        foreach ($impuestosCalculados as $impuesto) {
                            switch($impuesto['ImpuestoDR']){
                                case 001:
                                    $retencionISR += $this->truncarDosDecimales($impuesto['ImporteDR']);
                                    break;
                                case 002:
                                    $retencionIVA += $this->truncarDosDecimales($impuesto['ImporteDR']);
                                    break;
                                case 003:
                                    $retencionIEPS += $this->truncarDosDecimales($impuesto['ImporteDR']);
                                    break;
                            }
                            $hijoretencion = $xml->createElement('pago20:RetencionDR');
                            $hijoretencion = $subnodoRetencion->appendChild($hijoretencion);
                            $hijoretencion->setAttribute('BaseDR', $this->truncarDosDecimales($impuesto['BaseDR']));
                            $hijoretencion->setAttribute('ImpuestoDR', $impuesto['ImpuestoDR']);
                            $hijoretencion->setAttribute('TipoFactorDR', 'Tasa');
                            $hijoretencion->setAttribute('TasaOCuotaDR', bcdiv($impuesto['TasaOCuotaDR'], '1', 6));
                            $hijoretencion->setAttribute('ImporteDR', $this->truncarDosDecimales($impuesto['ImporteDR']));
                        }
                    }
                    if ($traslado != "") {
                        $impuestosCalculados = $this->generarImpuestosTrasladosDr($monto, $traslado);
                        $bandera_tras = 1;
                        $subnodoTraslado = $xml->createElement('pago20:TrasladosDR');
                        $subnodoTraslado = $nodoImpuestos->appendChild($subnodoTraslado);
                        foreach ($impuestosCalculados as $impuesto) {
                            $subTotlGral += $this->truncarDosDecimales($impuesto['BaseDR']);
                            $subIvaGral += $this->truncarDosDecimales($impuesto['ImporteDR']);
                            $hijotraslado = $xml->createElement('pago20:TrasladoDR');
                            $hijotraslado = $subnodoTraslado->appendChild($hijotraslado);
                            $hijotraslado->setAttribute('BaseDR', $this->truncarDosDecimales($impuesto['BaseDR']));
                            $hijotraslado->setAttribute('ImpuestoDR', $impuesto['ImpuestoDR']);
                            $hijotraslado->setAttribute('TipoFactorDR', 'Tasa');
                            $hijotraslado->setAttribute('TasaOCuotaDR', bcdiv($impuesto['TasaOCuotaDR'], '1', 6));
                            $hijotraslado->setAttribute('ImporteDR', $this->truncarDosDecimales($impuesto['ImporteDR']));
                        }
                    }
                }
            }

            if ($div_obj_imp[0] == '02') {
                $pagoImpuestos = $xml->createElement('pago20:ImpuestosP');
                $pagoImpuestos = $pago->appendChild($pagoImpuestos);
                if ($bandera_ret > 0) {
                    $impretenciones = $this->generarRetencionesP($monto, $retencio);
                    $pagoRetenciones = $xml->createElement('pago20:RetencionesP');
                    $pagoRetenciones = $pagoImpuestos->appendChild($pagoRetenciones);
                    foreach ($impretenciones as $retimp) {
                        $retencionesPago = $xml->createElement('pago20:RetencionP');
                        $pagoRetenciones->appendChild($retencionesPago);
                        $retencionesPago->setAttribute('ImpuestoP',  $retimp["ImpuestoP"]);
                        $retencionesPago->setAttribute('ImporteP', $this->truncarDosDecimales($retimp['ImporteP']));
                    }
                }

                if ($bandera_tras > 0) {
                    $impuestos = $this->generarTrasladoP($monto, $traslado);
                    $pagoTraslados = $xml->createElement('pago20:TrasladosP');
                    $pagoTraslados = $pagoImpuestos->appendChild($pagoTraslados);
                    $trasladosPago = $xml->createElement('pago20:TrasladoP');
                    $trasladosPago = $pagoTraslados->appendChild($trasladosPago);
                    foreach ($impuestos as $impuesto) {
                        $trasladosPago->setAttribute('BaseP', $this->truncarDosDecimales($impuesto['BaseP']));
                        $trasladosPago->setAttribute('ImpuestoP', $impuesto["ImpuestoP"]);
                        $trasladosPago->setAttribute('TipoFactorP', 'Tasa');
                        $trasladosPago->setAttribute('TasaOCuotaP', "0.160000");
                        $trasladosPago->setAttribute('ImporteP', $this->truncarDosDecimales($impuesto['ImporteP']));
                    }
                }

                if ($subTotlGral > 0) {
                    $totales->setAttribute('TotalTrasladosBaseIVA16', $this->truncarDosDecimales($subTotlGral));
                    $totales->setAttribute('TotalTrasladosImpuestoIVA16', $this->truncarDosDecimales($subIvaGral));
                }
                if ($retencionISR > 0) {
                    $totales->setAttribute('TotalRetencionesISR', $this->truncarDosDecimales($retencionISR));
                }
                if ($retencionIVA > 0) {
                    $totales->setAttribute('TotalRetencionesIVA', $this->truncarDosDecimales($retencionIVA));
                }
                if ($retencionIEPS > 0) {
                    $totales->setAttribute('TotalRetencionesIEPS', $this->truncarDosDecimales($retencionIEPS));
                }
            }
        }

        $sello = $this->SelloXML($xml->saveXML(), $rfcemisor);
        $obj = json_decode($sello);
        $xml2 = new DOMDocument("1.0", "UTF-8");
        $xml2->loadXML($xml->saveXML());
        $c = $xml2->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/4', 'Comprobante')->item(0);
        $c->setAttribute('Sello', $obj->sello);
        $doc = "../XML/XML2.xml";
        $xml2->save($doc);
        $timbre = $this->timbradoPago($xml2->saveXML(), $idpago, $rfcemisor, $razonemisor, $clvreg, $regimen, $codpemisor);
        return $timbre;
    }

    function truncarDosDecimales($numero) {
        $parteEntera = (int)$numero;
        $decimales = $numero - $parteEntera;
        $factor = 100; 
        $truncado = $decimales * $factor;
        $valor = $parteEntera + $truncado / $factor;
        return number_format($valor, 2, '.', '');
    }
    

    function generarImpuestosDr($monto_neto, $retenciones){
        $divRetencion = explode('<impuesto>', $retenciones);
        foreach ($divRetencion as $arrayretnecion) {
            $divret = explode('-', $arrayretnecion);
            $base_impuesto = $monto_neto / (1 - $divret[1]);
            $importe_retencion = $base_impuesto * $divret[1];

            $impuestos_calculados[] = array(
                'BaseDR' => $base_impuesto,
                'ImpuestoDR' => '00' . $divret[2], 
                'TasaOCuotaDR' => $divret[1],
                'ImporteDR' => $importe_retencion
            );
        }
        return $impuestos_calculados;
    }

    function generarImpuestosTrasladosDr($monto_neto, $traslados){
        $divTraslado = explode('<impuesto>', $traslados);
        foreach ($divTraslado as $arraytraslados) {
            $divtras = explode('-', $arraytraslados);
            $base_impuesto = $monto_neto / (1 + $divtras[1]);
            $importe_traslado = $base_impuesto * $divtras[1];

            $impuestos_calculados[] = array(
                'BaseDR' => $base_impuesto,
                'ImpuestoDR' => '00' . $divtras[2], 
                'TasaOCuotaDR' => $divtras[1],
                'ImporteDR' => $importe_traslado
            );
        }
        return $impuestos_calculados;
    }

    function generarTrasladoP($monto_total, $traslados){
        $divTraslado = explode('<impuesto>', $traslados);
        foreach ($divTraslado as $arraytraslados) {
            $divtras = explode('-', $arraytraslados);
            $base_impuesto = $monto_total / (1 + $divtras[1]);
            $importe_traslado = $base_impuesto * $divtras[1];

            $impuestos_calculados[] = array(
                'BaseP' => $base_impuesto,
                'ImpuestoP' => '00' . $divtras[2], 
                'TasaOCuotaP' => $divtras[1],
                'ImporteP' => $importe_traslado
            );
        }
        return $impuestos_calculados;
    }

    function generarRetencionesP($monto_total, $retenciones){
        $divRetencion = explode('<impuesto>', $retenciones);
        foreach ($divRetencion as $arrayretenciones) {
            $divret = explode('-', $arrayretenciones);
            $base_impuesto = $monto_total / (1 - $divret[1]);
            $importe_retencion = $base_impuesto * $divret[1];

            $impuestos_calculados[] = array(
                'ImpuestoP' => '00' . $divret[2], 
                'ImporteP' => $importe_retencion
            );
        }
        return $impuestos_calculados;
    }

    function SelloXML($doc, $rfc) {
        $xmlFile = $doc;
        $carpeta = '../temporal/' . $rfc . '/';
        $xslFile = "../vendor/recursos/cadenaoriginal_4_0.xslt";
        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->loadXML($xmlFile);
        $xsl = new DOMDocument();
        $xsl->load($xslFile);
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        $cadenaOriginal = $proc->transformToXML($xml);
        $fichero = "../vendor/recursos/cadenaOriginal.txt";
        file_put_contents($fichero, $cadenaOriginal, LOCK_EX);
        $params = array(
            "cadenaOriginal" => "../vendor/recursos/cadenaOriginal.txt",
            //Archivo key pem: pkcs8 -inform DET -in CSD/cer.key -passin pass:12345678a -out llaveprivada.pem
            "archivoKeyPem" => $carpeta . 'keyPEM.pem',
            //archivo cer pem: x509 -inform der -in CSD/cer.cer -out certificado.pem
            "archivoCerPem" => $carpeta . 'csdPEM.pem'
        );
        try {
            $result = Sellar::ObtenerSello($params);
            return $result;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    private function getSWAccessAux() {
        $consultado = false;
        $consulta = "SELECT * FROM swaccess WHERE idswaccess=:id;";
        $valores = array("id" => '1');
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getSWAccess() {
        $datos = "";
        $get = $this->getSWAccessAux();
        foreach ($get as $actual) {
            $token = $actual['accesstoken'];
            $url = $actual['sw_url'];
        }
        $datos = "$url</tr>$token";
        return $datos;
    }

    private function timbradoPago($doc, $idpago, $rfcemisor, $razonemisor, $clvreg, $regimen, $codpemisor) {
        $swaccess = $this->getSWAccess();
        $div = explode("</tr>", $swaccess);
        $url = $div[0];
        $token = $div[1];

        $params = array(
            "url" => $url,
            "token" => $token
        );

        try {
            header("Content-type: text/plain");
            $stamp = StampService::Set($params);
            $result = $stamp::StampV4($doc);
            if ($result->status == "error") {
                return '0' . $result->message . " " . $result->messageDetail;
            } else if ($result->status == "success") {
                $guardar = $this->guardarTimbrePago($result, $idpago, $rfcemisor, $razonemisor, $clvreg, $regimen, $codpemisor);
                return $guardar;
            }
        } catch (Exception $e) {
            header("Content-type: text/plain");
            echo $e->getMessage();
        }
    }

    private function guardarTimbrePago($result, $idpago, $rfcemisor, $razonemisor, $clvreg, $regimen, $codpemisor) {
        $actualizado = false;
        $consulta = "UPDATE `pagos` SET rfcemisor=:rfc, razonemisor=:rzemisor, clvregemisor=:creg, regfiscalemisor=:regimen, codpemisor=:cp, cadenaoriginalpago=:cadena, nocertsatpago=:certSAT, nocertcfdipago=:certCFDI, uuidpago=:uuid, sellosatpago=:selloSAT, sellocfdipago=:selloCFDI, fechatimbrado=:fechatimbrado, qrcode=:qrcode, cfdipago=:cfdipago WHERE idpago=:id;";
        $valores = array("rfc" => $rfcemisor,
            "rzemisor" => $razonemisor,
            "creg" => $clvreg,
            "regimen" => $regimen,
            "cp" => $codpemisor,
            "cadena" => $result->data->cadenaOriginalSAT,
            "certSAT" => $result->data->noCertificadoSAT,
            "certCFDI" => $result->data->noCertificadoCFDI,
            "uuid" => $result->data->uuid,
            "selloSAT" => $result->data->selloSAT,
            "selloCFDI" => $result->data->selloCFDI,
            "fechatimbrado" => $result->data->fechaTimbrado,
            "qrcode" => $result->data->qrCode,
            "cfdipago" => $result->data->cfdi,
            "id" => $idpago);
        $actualizado = $this->consultas->execute($consulta, $valores);
        $this->updateTimbres();
        return '+Timbre guardado correctamente.';
    }

    private function updateTimbres() {
        $actualizado = false;
        $consulta = "UPDATE `contador_timbres` SET  timbresUtilizados=timbresUtilizados+1, timbresRestantes=timbresRestantes-1 WHERE idtimbres=:idtimbres;";
        $valores = array("idtimbres" => '1');
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }
    
    public function getPagoById($idpago) {
        $consultado = false;
        $consulta = "SELECT p.*, df.nombre_contribuyente, df.firma, df.rfc, df.razon_social, df.codigo_postal, df.c_regimenfiscal, df.regimen_fiscal FROM pagos p INNER JOIN datos_facturacion df ON (df.id_datos=p.pago_idfiscales) WHERE idpago=:idpago;";
        $val = array("idpago" => $idpago);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getPagoByIdReportes($idpago) {
        $consultado = false;
        $consulta = "SELECT p.*, df.*, dp.*, cp.* FROM pagos p INNER JOIN datos_facturacion df ON df.id_datos = p.pago_idfiscales
        INNER JOIN detallepago dp ON dp.detalle_tagencabezado = p.tagpago
        INNER JOIN complemento_pago cp ON cp.tagpago = p.tagpago WHERE idpago=:idpago;";
        $val = array("idpago" => $idpago);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosPago($idpago) {
        $datos = "";
        $pago = $this->getPagoById($idpago);
        foreach ($pago as $pagoactual) {
            $idpago = $pagoactual['idpago'];
            $serie = $pagoactual['serie'];
            $letra = $pagoactual['letra'];
            $foliopago = $pagoactual['foliopago'];
            $fechacreacion = $pagoactual['fechacreacion'];
            $pago_idfiscales = $pagoactual['pago_idfiscales'];
            $nombrefiscales = $pagoactual['nombre_contribuyente'];
            $rfcemisor = $pagoactual['rfcemisor'];
            $razonemisor = $pagoactual['razonemisor'];
            $clvregemisor = $pagoactual['clvregemisor'];
            $regfiscalemisor = $pagoactual['regfiscalemisor'];
            $codpemisor = $pagoactual['codpemisor'];
            $pago_idcliente = $pagoactual['pago_idcliente'];
            $nombrecliente = $pagoactual['nombrecliente'];
            $rfcreceptor = $pagoactual['rfcreceptor'];
            $razonreceptor = $pagoactual['razonreceptor'];
            $regfiscalreceptor = $pagoactual['regfiscalreceptor'];
            $codpreceptor = $pagoactual['codpreceptor'];
            $totalpagado = $pagoactual['totalpagado'];
            $chfirmar = $pagoactual['chfirmar'];
            $uuidpago = $pagoactual['uuidpago'];
            $tag = $pagoactual['tagpago'];
            $objimpuesto = $pagoactual['objimpuesto'];

            $datos = "$idpago</tr>$serie</tr>$letra</tr>$foliopago</tr>$fechacreacion</tr>$pago_idfiscales</tr>$nombrefiscales</tr>$rfcemisor</tr>$razonemisor</tr>$clvregemisor</tr>$regfiscalemisor</tr>$codpemisor</tr>$pago_idcliente</tr>$nombrecliente</tr>$rfcreceptor</tr>$razonreceptor</tr>$regfiscalreceptor</tr>$codpreceptor</tr>$totalpagado</tr>$chfirmar</tr>$uuidpago</tr>$tag</tr>$objimpuesto";

            break;
        }
        return $datos;
    }

    private function getComplementosAux($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM complemento_pago WHERE tagpago=:tag ORDER BY ordcomplemento ASC;";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function cfdisPago($tagpago, $tagcomp, $sid) {
        $datos = "";
        $cfdi = $this->getDetallePago($tagpago, $tagcomp);
        foreach ($cfdi as $cfdiactual) {
            $parcialidad = $cfdiactual['noparcialidad'];
            $idfactura = $cfdiactual['pago_idfactura'];
            $foliodoc = $cfdiactual['foliodoc'];
            $uuiddoc = $cfdiactual['uuiddoc'];
            $tcambiodoc = $cfdiactual['tcambiodoc'];
            $idmonedadoc = $cfdiactual['idmonedadoc'];
            $nombremoneda = $cfdiactual['nombre_moneda'];
            $cmetododoc = $cfdiactual['cmetododoc'];
            $monto = $cfdiactual['monto'];
            $montoanterior = $cfdiactual['montoanterior'];
            $montoinsoluto = $cfdiactual['montoinsoluto'];
            $totalfactura = $cfdiactual['totalfactura'];
            $tagcmp = $cfdiactual['detalle_tagcomplemento'];
            $type = $cfdiactual['type'];

            $consulta = "INSERT INTO `tmppago` VALUES (:id, :parcialidad, :idfactura, :folio, :uuid, :tcambio, :idmoneda, :nombre_moneda, :cmetodo,  :monto, :montoanterior, :montoinsoluto, :totalfactura, :type, :tmptag, :sid);";
            $valores = array("id" => null,
                "parcialidad" => $parcialidad,
                "idfactura" => $idfactura,
                "folio" => $foliodoc,
                "uuid" => $uuiddoc,
                "tcambio" => $tcambiodoc,
                "idmoneda" => $idmonedadoc,
                "nombre_moneda" => $nombremoneda,
                "cmetodo" => $cmetododoc,
                "monto" => $monto,
                "montoanterior" => $montoanterior,
                "montoinsoluto" => $montoinsoluto,
                "totalfactura" => $totalfactura,
                "type" => $type,
                "tmptag" => $tagcmp,
                "sid" => $sid);
            $datos = $this->consultas->execute($consulta, $valores);
        }
        return $datos;
    }

    public function buildComplementos($tag, $sid, $uuid) {
        $datos = "";
        $complementos = $this->getComplementosAux($tag);
        foreach ($complementos as $actual) {
            $orden = $actual['ordcomplemento'];
            $tcambio = $actual['complemento_tcambio'];
            $fechapago = $actual['complemento_fechapago'];
            $horapago = $actual['complemento_horapago'];
            $numtransaccion = $actual['complemento_notransaccion'];
            $tagcomp = $actual['tagcomplemento'];
            $tagpago = $actual['tagpago'];
            $idcuentaord = $actual['complemento_idcuentaOrd'];
            $idcuentabnf = $actual['complemento_idcuentaBnf'];
            $fpid = $actual['complemento_idformapago'];
            $mid = $actual['complemento_idmoneda'];
            $total = $actual['total_complemento'];

            $disabled = "";
            $close = "<span data-tab='$tagcomp' type='button' class='close-button' aria-label='Close'><span aria-hidden='true'>&times;</span></span>";
            if ($uuid != "") {
                $disabled = "disabled";
                $close = "";
            }

            $this->cfdisPago($tagpago, $tagcomp, $sid);
            $datos .= "<button id='tab-$tagcomp' class='tab-pago sub-tab-active' data-tab='$tagcomp' data-ord='$orden' name='tab-complemento' >Complemento $orden &nbsp; $close</button>
                <cut>
                <div id='complemento-$tagcomp' class='sub-div'>
                <div class='row'>
            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='forma-$tagcomp'>Forma de pago </label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                        <select class='form-select text-center input-form' id='forma-$tagcomp' name='forma-$tagcomp' onchange='disableCuenta();' $disabled>
                            <option value='' id='default-fpago-$tagcomp'>- - - -</option>
                            <optgroup id='forma-pago-$tagcomp' class='cont-fpago-$tagcomp text-start'></optgroup>
                        </select>
                    <div id='forma-$tag-errors'></div>
                </div>
            </div>
            <div class='col-md-2 py-2'>
                <label class='label-form text-right' for='moneda-$tagcomp'>Moneda de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                    <select class='form-select text-center input-form' id='moneda-$tagcomp' name='moneda-$tagcomp' onchange='getTipoCambio(); loadTablaCFDI();' $disabled>
                        <option value='' id='default-moneda-$tagcomp'>- - - -</option>
                        <optgroup id='mpago-$tagcomp' class='contmoneda-$tagcomp text-start'></optgroup>
                    </select>
                    <div id='moneda-$tag-errors'></div>
                </div>
            </div>
            <div class='col-md-2 py-2'>
                <label class='label-form text-right' for='cambio-$tagcomp'>Tipo de cambio</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                    <input type='text' class='form-control input-form' id='cambio-$tagcomp' placeholder='Tipo de cambio de Moneda' disabled='' value='$tcambio' $disabled>
                    <div id='cambio-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='fecha-$tagcomp'>Fecha de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='fecha-$tagcomp' name='fecha-$tagcomp' type='date' value='$fechapago' $disabled/>
                    <div id='fecha-$tagcomp-errors'></div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='hora-$tagcomp'>Hora de pago</label> <label
                        class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='hora-$tagcomp' name='hora-$tagcomp' type='time' value='$horapago' $disabled/>
                    <div id='hora-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='cuenta-$tagcomp'>Cuenta ordenante (Cliente)</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                <select class='form-select text-center input-form' id='cuenta-$tagcomp' name='cuenta-$tagcomp' $disabled>
                        <option value='' id='default-cuenta-$tagcomp'>- - - -</option>
                        <optgroup id='ordenante-$tagcomp' class='contenedor-cuenta-$tagcomp text-start'></optgroup>
                    </select>
                    <div id='cuenta-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='benef-$tagcomp'>Cuenta beneficiario (Mis cuentas)</label>
                <label class='mark-required text-danger fw-bold'></label>
                <div class='form-group'>
                    <select class='form-select text-center input-form' id='benef-$tagcomp' name='benef-$tagcomp' disabled>
                        <option value='' id='default-benef-$tagcomp'>- - - -</option>
                        <optgroup id='beneficiario-$tagcomp' class='contenedor-beneficiario-$tagcomp text-start'></optgroup>
                    </select>
                    <div id='benef-$tagcomp-errors'></div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='transaccion-$tagcomp'>No. de transacción</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='transaccion-$tagcomp' name='transaccion-$tagcomp' placeholder='No. de Transacción' type='number' value='$numtransaccion' $disabled/>
                    <div id='transaccion-$tagcomp-errors'>
                    </div>
                </div>
            </div>
        </div>

        <div class='row mt-3'>
            <div class='col-md-12'>
                <div class='new-tooltip icon tip'> 
                    <label class='label-sub' for='fecha-creacion'>CFDIS RELACIONADOS </label>  <span
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
                                <label class='label-form mb-1' for='factura-$tagcomp'>Folio factura</label>
                                <input id='id-factura-$tagcomp' type='hidden' /><input class='form-control text-center input-form' $disabled id='factura-$tagcomp' name='factura-$tagcomp' placeholder='Factura' type='text' oninput='aucompletarFactura();'/>
                            </td>
                            <td colspan='2'>
                                <label class='label-form mb-1' for='uuid-$tagcomp'>UUID Factura</label>
                                <input class='form-control cfdi text-center input-form' id='uuid-$tagcomp' name='uuid-$tagcomp' $disabled placeholder='UUID del cfdi' type='text'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='type-$tagcomp'>Tipo factura</label>
                                <select class='form-select text-center input-form' id='type-$tagcomp' name='type-$tagcomp' $disabled>
                                    <option value='' id='default-tipo-$tagcomp'>- - - -</option>
                                    <option value='f' id='tipo-f-$tagcomp'>Factura</option>
                                    <option value='c' id='tipo-c-$tagcomp'>Carta Porte</option>
                                </select>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='monedarel-$tagcomp'>Moneda factura</label>
                                <input id='cambiocfdi-$tagcomp' type='hidden' />
                                <input id='metcfdi-$tagcomp' type='hidden' />
                                <select class='form-select text-center input-form' id='monedarel-$tagcomp' name='monedarel-$tagcomp' $disabled>
                                    <option value='' id='default-moneda-$tagcomp'>- - - -</option>
                                    <optgroup id='moncfdi-$tagcomp' class='contenedor-moneda-$tagcomp text-start'> </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class='label-form mb-1' for='parcialidad-$tagcomp'>No. Parcialidad</label>
                                <input class='form-control text-center input-form' id='parcialidad-$tagcomp' $disabled name='parcialidad-$tagcomp' placeholder='No Parcialidad' type='text'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='total-$tagcomp'>Total factura</label>
                                <input class='form-control text-center input-form' id='total-$tagcomp' $disabled name='total-$tagcomp' placeholder='Total de factura' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='anterior-$tagcomp'>Monto anterior</label>
                                <input class='form-control text-center input-form' id='anterior-$tagcomp' $disabled name='anterior-$tagcomp' placeholder='Monto anterior' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='monto-$tagcomp'>Monto a pagar</label>
                                <input class='form-control text-center input-form' id='monto-$tagcomp' $disabled name='monto-$tagcomp' placeholder='Monto Pagado' type='number' step='any' oninput='calcularRestante()'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='restante-$tagcomp'>Monto restante</label>
                                <input class='form-control text-center input-form' id='restante-$tagcomp' $disabled name='cantidad' placeholder='Monto restante' type='number' step='any'/>
                            </td>
                            <td class='text-center'>
                                    <label class='label-space text-light' for='btn-agregar-cfdi'>Algo</label>
                                    <button id='btn-agregar-cfdi' class='button-modal col-12' $disabled onclick='agregarCFDI();'><span class='fas fa-plus'></span> Agregar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class='table tab-hover table-condensed table-responsive table-row table-head' id='lista-cfdi-$tagcomp'>

                </table>
            </div>
        </div>
        </div><cut>$tagcomp<cut>$orden<cut>$fpid<cut>$mid<cut>$idcuentaord<cut>$idcuentabnf<cut>$tcambio<comp>";
        }
        return $datos; 
    }

    private function getTagPagoAux($idpago) {
        $resultados = "";
        $consulta = "SELECT * FROM pagos WHERE idpago=:id;";
        $val = array("id" => $idpago);
        $resultados = $this->consultas->getResults($consulta, $val);
        return $resultados;
    }

    private function getTagPago($idpago) {
        $tag = "";
        $datos = $this->getTagPagoAux($idpago);
        foreach ($datos as $actual) {
            $tag = $actual['tagpago'];
        }
        return $tag;
    }

    public function eliminarPago($idpago) {
        $tag = $this->getTagPago($idpago);
        $eliminado = false;
        $consulta = "DELETE FROM `pagos` WHERE idpago=:id;";
        $valores = array("id" => $idpago);
        $eliminado = $this->consultas->execute($consulta, $valores);
        $detalle = $this->eliminarDetallePago($tag);
        return $detalle;
    }

    private function getDetalleEliminar($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detallepago WHERE detalle_tagencabezado=:tag ORDER BY iddetallepago;";
        $val = array("tag" => $tag);    
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getestadoFacturaAux($id, $type) {
        $consultado = false;
        if ($type == 'f') {
            $consulta = "SELECT * FROM datos_factura WHERE iddatos_factura=:id;";
        } else if ($type == 'c') {
            $consulta = "SELECT * FROM factura_carta WHERE idfactura_carta=:id;";
        }
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getestadoFactura($id, $type) {
        $status = "";
        $datos = $this->getestadoFacturaAux($id, $type);
        foreach ($datos as $actual) {
            $status = $actual['status_pago'];
        }
        return $status;
    }

    private function eliminarDetallePago($tag) {
        $getpago = $this->getDetalleEliminar($tag);
        foreach ($getpago as $actual) {
            $idfactura = $actual['pago_idfactura'];
            $type = $actual['type'];
            $estado = $this->getestadoFactura($idfactura, $type);
            if ($estado != '3') {
                $actualizar = $this->estadoFactura($idfactura, '2', $type);
            }
        }

        $eliminado = false;
        $consulta = "DELETE FROM complemento_pago WHERE tagpago=:tag;";
        $val = array("tag" => $tag);
        $eliminado = $this->consultas->execute($consulta, $val);

        $consulta2 = "DELETE FROM detallepago WHERE detalle_tagencabezado=:tag;";
        $val2 = array("tag" => $tag);
        $eliminado2 = $this->consultas->execute($consulta2, $val2);
        return $eliminado;
    }

    private function getConfigMailAux() {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE chuso2=:id;";
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
            $host = $actual['host'];
            $puerto = $actual['puerto'];
            $seguridad = $actual['seguridad'];
            $datos = "$correo</tr>$pass</tr>$remitente</tr>$host</tr>$puerto</tr>$seguridad";
        }
        return $datos;
    }

    public function mail($sm) {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $body = $cc->getMailBody('2');
        $divM = explode("</tr>", $body);
        $asunto = $divM[1];
        $saludo = $divM[2];
        $msg = $divM[3];
        $logo = $divM[4];

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
            $mail->isSMTP();
            $mail->Mailer = 'smtp';
            $mail->SMTPAuth = true;
            $mail->Host = $host;
            $mail->Port = $puerto;
            $mail->SMTPSecure = $seguridad;
            $mail->Username = $correoenvio;
            $mail->Password = $pass;
            $mail->From = $correoenvio;
            $mail->FromName = $remitente;

            $mail->Subject =  iconv('UTF-8', 'windows-1252', $asunto);
            $mail->isHTML(true);
            $mail->Body = $this->bodyMail($asunto, $saludo, $sm->getRazonsocial(), $msg, $logo);

            if ($sm->getChmail1() == '1') {
                $mail->addAddress($sm->getMailalt1());
            }
            if ($sm->getChmail2() == '1') {
                $mail->addAddress($sm->getMailalt2());
            }
            if ($sm->getChmail3() == '1') {
                $mail->addAddress($sm->getMailalt3());
            }
            if ($sm->getChmail4() == '1') {
                $mail->addAddress($sm->getMailalt4());
            }
            if ($sm->getChmail5() == '1') {
                $mail->addAddress($sm->getMailalt5());
            }
            if ($sm->getChmail6() == '1') {
                $mail->addAddress($sm->getMailalt6());
            }

            $mail->addStringAttachment($sm->getPdfstring(), $sm->getFolio() . "_" . $sm->getRfcemisor() . "_" . $sm->getUuid() . ".pdf");
            if ($sm->getCfdistring() != "") {
                $mail->addStringAttachment($sm->getCfdistring(), $sm->getFolio() . "_" . $sm->getRfcemisor() . "_" . $sm->getUuid() . ".xml");
            }
            if (!$mail->send()) {
                echo '0No se envio el mensaje: ' . $mail->ErrorInfo;
            } else {
                return '1Se ha enviado el pago.';
            }
        } else {
            return "0No se ha configurado un correo de envio para esta área. Ingrese a Configuración -> Correo, e ingrese uno para Pagos.";
        }
    }

    private function bodyMail($asunto, $saludo, $nombre, $msg, $logo) {
        $archivo = "../com.sine.dao/configuracion.ini";
        $ajustes = parse_ini_file($archivo, true);
        if (!$ajustes) {
            throw new Exception("No se puede abrir el archivo " . $archivo);
        }
        $rfcfolder = isset($ajustes['cron']['rfcfolder']) ? $ajustes['cron']['rfcfolder'] : 'NAGA021226FJ0';

        $txt = str_replace("<corte>", "</p><p style='font-size:18px; text-align: justify;'>", $msg);
        $message = "<html>
                        <body>
                            <table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0' style='border-radius: 25px;'>
                                <tr>
                                    <td>
                                        <table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; border-radius: 20px; background-color:#fff; font-family:sans-serif;'>
                                            <thead>
                                                <tr height='80'>
                                                    <th align='left' colspan='4' style='padding: 6px; background-color:#f5f5f5; border-radius: 20px; border-bottom:solid 1px #bdbdbd;' ><img src='https://q-ik.mx/$rfcfolder/img/$logo' height='100px'/></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr align='center' height='10' style='font-family:sans-serif; '>
                                                    <td style='background-color:#09096B; text-align:center; border-radius: 5px;'></td>
                                                </tr>
                                                <tr>
                                                    <td colspan='4' style='padding:15px;'>
                                                        <h1>$asunto</h1>
                                                        <p style='font-size:20px;'>$saludo $nombre</p>
                                                        <hr/>
                                                        <p style='font-size:18px; text-align: justify;'>$txt</p>
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

    private function getUUID($idfactura) {
        $datos = "";
        $uuid = $this->getUUIDAux($idfactura);
        foreach ($uuid as $u) {
            $uuid = $u['uuidpago'];
            $folio = $u['letra'] . $u['foliopago'];
            $rfc = $u['rfc'];
            $pass = $u['passcsd'];
            $csd = $u['csd'];
            $key = $u['keyb64'];
            $datos = "$uuid</tr>$folio</tr>$rfc</tr>$pass</tr>$csd</tr>$key";
        }
        return $datos;
    }

    private function getUUIDAux($idpago) {
        $consultado = false;
        $consulta = "SELECT p.uuidpago, p.letra, p.foliopago, df.rfc, df.passcsd, df.keyb64,df.csd FROM pagos p INNER JOIN datos_facturacion df ON (p.pago_idfiscales=df.id_datos) WHERE p.idpago=:id;";
        $val = array("id" => $idpago);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    function cancelarTimbre($idpago, $motivo, $reemplazo) {
        $swaccess = $this->getSWAccess();
        $div = explode("</tr>", $swaccess);
        $url = $div[0];
        $token = $div[1];

        $get = $this->getUUID($idpago);
        $divideU = explode("</tr>", $get);
        $uuid = $divideU[0];
        $rfc = $divideU[2];
        $pass = $divideU[3];
        $csd = $divideU[4];
        $key = $divideU[5];

        if ($motivo == '01') {
            $params = array(
                "url" => $url,
                "token" => $token,
                "uuid" => $uuid,
                "password" => $pass,
                "rfc" => $rfc,
                "motivo" => $motivo,
                "foliosustitucion" => $reemplazo,
                "cerB64" => $csd,
                "keyB64" => $key
            );
        } else {
            $params = array(
                "url" => $url,
                "token" => $token,
                "uuid" => $uuid,
                "password" => $pass,
                "rfc" => $rfc,
                "motivo" => $motivo,
                "cerB64" => $csd,
                "keyB64" => $key
            );
        }

        try {
            header('Content-type: text/plain');
            $cancelationService = CancelationService::Set($params);
            $result = $cancelationService::CancelationByCSD();
            if ($result->status == "error") {
                echo '0';
                return var_dump($result);
            } else if ($result->status == "success") {
                $guardar = $this->cancelarPago($idpago, $result->data->acuse);
                return $guardar;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        return var_dump($result);
    }

    private function cancelarPago($idpago, $cfdi) {
        $ruta = "../XML/XML_CANCEL.xml";
        $archivo = fopen($ruta,"w");
        fwrite($archivo, $cfdi);
        fclose($archivo);


        $xml = simplexml_load_file($ruta);
        foreach ($xml->Folios as $folio) {
            $uuid_cancel = $folio->UUID;
            $status_uuid = $folio->EstatusUUID;
        }

        switch($status_uuid){
            case 201:
                $mensaje_cancel = "Solicitud de cancelación exitosa.";
                break;
            case 202:
                $mensaje_cancel = "Folio fiscal previamente cancelado.";
                break;
            case 203:
                $mensaje_cancel = "Folio fiscal no correspondiente al emisor.";
                break;
            case 204:
                $mensaje_cancel = "Folio fiscal no aplicable a cancelación.";
                break;
            case 205:
                $mensaje_cancel = "Folio fiscal no existente.";
                break;
            case 206:
                $mensaje_cancel = "UUID no corresponde a un CFDI del sector primario.";
                break;
            case 207:
                $mensaje_cancel = "No se especificó el motivo de cancelación o el motivo no es válido.";
                break;
            case 208:
                $mensaje_cancel = "Folio sustitución inválido.";
                break;
            case 209:
                $mensaje_cancel = "Folio sustitución no requerido.";
                break;
            case 210:
                $mensaje_cancel = "La fecha de solicitud de cancelación es mayor a la fecha de declaración.";
                break;
            case 211:
                $mensaje_cancel = "La fecha de solicitud de cancelación límite para factura global.";
                break;
            case 212:
                $mensaje_cancel = "Relación no valida o inexistente.";
                break;
            case 300:
                $mensaje_cancel = "Usuario no válido.";
                break;
            case 301:
                $mensaje_cancel = "XML mal formado.";
                break;
            case 302:
                $mensaje_cancel = "Sello mal formado.";
                break;
            case 304:
                $mensaje_cancel = "Certificado revocado o caduco.";
                break;
            case 305:
                $mensaje_cancel = "Certificado inválido.";
                break;
            case 309:
                $mensaje_cancel = "Certificado inválido.";
                break;
            case 310:
                $mensaje_cancel = "CSD inválido.";
                break;
            case 311:
                $mensaje_cancel = "Motivo inválido.";
                break;
            case 312:
                $mensaje_cancel = "UUID no relacionado.";
                break;
        }

        $actualizado = "";
        if($status_uuid == 201 || $status_uuid == 202){
            $consulta = "UPDATE `pagos` SET cancelado=:estado, cfdicancel=:cfdi WHERE idpago=:id;";
            $valores = array("id" => $idpago,
                "estado" => '1',
                "cfdi" => $cfdi);
            $actualizado = $this->consultas->execute($consulta, $valores);
            

            if($actualizado){
                $actualizado = "1$status_uuid - $mensaje_cancel";
                $this->actualizarEstadoFacturas($idpago);
            }
        } else {
            $actualizado = "0$status_uuid - $mensaje_cancel";
        }
        return $actualizado;
    }

    function actualizarEstadoFacturas($idpago) {
        $consulta_tag = "SELECT tagpago FROM `pagos` WHERE idpago=:id;";
        $valores_tag = array("id" => $idpago);
        $result_tag = $this->consultas->getResults($consulta_tag, $valores_tag);
    
        foreach ($result_tag as $tagfactura) {
            $tag = $tagfactura['tagpago'];
    
            $consulta_facturas = "SELECT pago_idfactura FROM `detallepago` WHERE detalle_tagencabezado=:tag;";
            $valores_facturas = array("tag" => $tag);
            $result_facturas = $this->consultas->getResults($consulta_facturas, $valores_facturas);
    
                foreach ($result_facturas as $factura) {
                    $idfactura = $factura['pago_idfactura'];
                    $type = 'f'; 
    
                    $estado = $this->getestadoFactura($idfactura, $type);
                    if ($estado != '3') {
                        $actualizar = $this->estadoFactura($idfactura, '2', $type);
                    }
                }
        }
    }
    

    public function validarActualizarPago($p, $objimpuesto) {
        $datos = $this->actualizarPago($p, $objimpuesto);
        return $datos;
    }

    private function actualizarPago($p, $objimpuesto) {
        $updfolio = "";
        $serie = "";
        $letra = "";
        $nfolio = "";

        if ($p->getFoliopago() != '0') {
            $updfolio = "serie=:serie, letra=:letra, foliopago=:folio,";
            $folios = $this->getFolio($p->getFoliopago());
            $Fdiv = explode("</tr>", $folios);
            $serie = $Fdiv[0];
            $letra = $Fdiv[1];
            $nfolio = $Fdiv[2];
        }

        $insertado = false;
        $consulta = "UPDATE `pagos` SET $updfolio pago_idcliente=:pago_idcliente, nombrecliente=:cliente, rfcreceptor=:rfcreceptor, razonreceptor=:razonreceptor, regfiscalreceptor=:regfiscalreceptor, codpreceptor=:codpreceptor, pago_idfiscales=:pago_idfiscales, chfirmar=:chfirmar, objimpuesto=:objimpuesto WHERE idpago=:id;";
        $valores = array("id" => $p->getIdpago(),
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "pago_idcliente" => $p->getIdcliente(),
            "cliente" => $p->getNombrecliente(),
            "rfcreceptor" => $p->getRfccliente(),
            "razonreceptor" => $p->getRazoncliente(),
            "regfiscalreceptor" => $p->getRegfiscalcliente(),
            "codpreceptor" => $p->getCodpostal(),
            "pago_idfiscales" => $p->getPago_idfiscales(),
            "chfirmar" => $p->getChfirmar(),
            "objimpuesto" => $objimpuesto,
        );
        $insertado = $this->consultas->execute($consulta, $valores);

        $this->eliminarComplementos($p->getTagpago());
        $update1 = "UPDATE `pagos` SET totalpagado='0' WHERE tagpago=:tag;";
        $valores5 = array("tag" => $p->getTagpago());
        $insertado = $this->consultas->execute($update1, $valores5);
        return $insertado;
    }

    private function eliminarComplementos($tag) {
        $eliminado = false;
        $consulta = "DELETE FROM complemento_pago WHERE tagpago=:tag;";
        $val = array("tag" => $tag);
        $eliminado = $this->consultas->execute($consulta, $val);

        $consulta2 = "DELETE FROM detallepago WHERE detalle_tagencabezado=:tag;";
        $val2 = array("tag" => $tag);
        $eliminado2 = $this->consultas->execute($consulta2, $val2);
        return $eliminado;
    }

    public function actualizarComplemento($p) {
        $insertar = false;
        $consulta = "INSERT INTO complemento_pago VALUES (:id, :orden, :idforma, :cforma, :formapago, :idmoneda, :nombremoneda, :tcambio, :fechapago, :horapago, :cuentaord, :cuentabnf, :notransaccion, :total, :tagcomp, :tagpago)";
        $val = array("id" => null,
            "orden" => $p->getOrden(),
            "idforma" => $p->getPagoidformapago(),
            "cforma" => $p->getCForma(),
            "formapago" => $p->getNombreForma(),
            "idmoneda" => $p->getPagoidmoneda(),
            "nombremoneda" => $p->getNombreMoneda(),
            "tcambio" => $p->getTipocambio(),
            "fechapago" => $p->getFechapago(),
            "horapago" => $p->getHorapago(),
            "cuentaord" => $p->getPago_idbanco(),
            "cuentabnf" => $p->getIdbancoB(),
            "notransaccion" => $p->getNooperacion(),
            "total" => '0',
            "tagcomp" => $p->getTagcomp(),
            "tagpago" => $p->getTagpago());
        $insertar = $this->consultas->execute($consulta, $val);
        $this->actualizarDetalle($p->getSessionid(), $p->getTagpago(), $p->getTagcomp(), $p->getTipocambio(), $p->getPagoidmoneda());
        return $insertar;
    }

    public function actualizarDetalle($idsession, $tagpago, $tagcomp, $tcambio, $monedapago) {
        $totalpagado = 0;
        $cfdi = $this->getPagosTMP($idsession, $tagcomp);
        foreach ($cfdi as $actual) {
            $parcialidad = $actual['noparcialidadtmp'];
            $idfactura = $actual['idfacturatmp'];
            $folio = $actual['foliofacturatmp'];
            $uuid = $actual['uuidtmp'];
            $tcambiodoc = $actual['tcambiotmp'];
            $idmonedaF = $actual['idmonedatmp'];
            $nombremoneda = $actual['nombre_moneda'];
            $cmetodotmp = $actual['idmetodotmp'];
            $montopagado = $actual['montotmp'];
            $montoanterior = $actual['montoanteriortmp'];
            $montoinsoluto = $actual['montoinsolutotmp'];
            $totalfactura = $actual['totalfacturatmp'];
            $type = $actual['type'];
            $tag = $actual['tmptagcomp'];

            $totalpagado += $montopagado;

            $consulta2 = "INSERT INTO `detallepago` VALUES (:iddetallepago, :noparcialidad, :pagoidfactura, :folio, :uuid, :tcambio, :idmoneda, :nombremoneda, :cmetodo, :monto, :montoanterior, :montoinsoluto, :totalfactura, :type, :tagpago, :tagcomp);";
            $valores2 = array("iddetallepago" => null,
                "noparcialidad" => $parcialidad,
                "pagoidfactura" => $idfactura,
                "folio" => $folio,
                "uuid" => $uuid,
                "tcambio" => $tcambiodoc,
                "idmoneda" => $idmonedaF,
                "nombremoneda" => $nombremoneda,
                "cmetodo" => $cmetodotmp,
                "monto" => $montopagado,
                "montoanterior" => $montoanterior,
                "montoinsoluto" => $montoinsoluto,
                "totalfactura" => $totalfactura,
                "type" => $type,
                "tagpago" => $tagpago,
                "tagcomp" => $tagcomp);
            $insertado = $this->consultas->execute($consulta2, $valores2);
            if ($montoinsoluto != '0') {
                $estado = '4';
            } else if ($montoinsoluto == '0') {
                $estado = '1';
            }
            if ($idfactura != '0') {
                $this->estadoFactura($idfactura, $estado, $type);
            }
        }

        $this->cancelar($idsession, $tagcomp);
        $update = "UPDATE `complemento_pago` SET total_complemento=:total WHERE tagcomplemento=:tag";
        $valores4 = array("tag" => $tagcomp,
            "total" => $totalpagado);
        $insertado = $this->consultas->execute($update, $valores4);

        $update1 = "UPDATE `pagos` SET totalpagado=(totalpagado+$totalpagado) WHERE tagpago=:tag";
        $valores5 = array("tag" => $tagpago);
        $insertado = $this->consultas->execute($update1, $valores5);
        return $insertado;
    }

    public function getCorreo($idfactura) {
        $datos = "";
        $correos = $this->getCorreosAux($idfactura);
        foreach ($correos as $actual) {
            $correo1 = $actual['email_informacion'];
            $correo2 = $actual['email_facturacion'];
            $correo3 = $actual['email_gerencia'];
            $correo4 = $actual['correoalt1'];
            $correo5 = $actual['correoalt2'];
            $correo6 = $actual['correoalt3'];
            $datos .= "$correo1<corte>$correo2<corte>$correo3<corte>$correo4<corte>$correo5<corte>$correo6";
        }
        return $datos;
    }

    public function getCorreosAux($idfactura) {
        $consultado = false;
        $consulta = "SELECT c.email_informacion, c.email_facturacion, c.email_gerencia, c.correoalt1, c.correoalt2, c.correoalt3 FROM pagos p INNER JOIN cliente c ON (p.pago_idcliente=c.id_cliente) WHERE p.idpago=:id;";
        $valores = array("id" => $idfactura);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getXMLInfo($id) {
        $consultado = false;
        $consulta = "SELECT * FROM pagos p WHERE p.idpago=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function exportarComplemento($idformapago, $idmoneda, $tcambio, $idfactura, $folio, $sid) {
        $datos = "";
        $tagcomp = $this->genTag();

        $datos .= "<button id='tab-$tagcomp' class='tab-pago sub-tab-active' data-tab='$tagcomp' data-ord='1' name='tab-complemento' >Complemento 1 &nbsp; <span data-tab='$tagcomp' type='button' class='close-button' aria-label='Close'><span aria-hidden='true'>&times;</span></span></button>
                <cut>
                <div id='complemento-$tagcomp' class='sub-div'>
                <div class='row'>
            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='forma-$tagcomp'>Forma de pago</label> <label
                    class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                        <select class='form-select text-center input-form' id='forma-$tagcomp' name='forma-$tagcomp' onchange='disableCuenta();'>
                            <option value='' id='default-fpago-$tagcomp'>- - - -</option>
                            <optgroup id='forma-pago-$tagcomp' class='cont-fpago-$tagcomp text-start'></optgroup>
                        </select>
                    <div id='forma-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-2 py-2'>
                <label class='label-form text-right' for='moneda-$tagcomp'>Moneda de pago</label> <label
                    class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                    <select class='form-select text-center input-form' id='moneda-$tagcomp' disabled name='moneda-$tagcomp' onchange='getTipoCambio(); loadTablaCFDI();'>
                        <option value='' id='default-moneda-$tagcomp'>- - - -</option>
                        <optgroup id='mpago-$tagcomp' class='contmoneda-$tagcomp text-start'></optgroup>
                    </select>
                    <div id='moneda-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-2 py-2'>
                <label class='label-form text-right' for='cambio-$tagcomp'>Tipo de cambio</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                    <input type='text' class='form-control input-form' id='cambio-$tagcomp' placeholder='Tipo de cambio de Moneda' disabled='' value='$tcambio'>
                    <div id='cambio-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='fecha-$tagcomp'>Fecha de pago</label> <label
                    class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='fecha-$tagcomp' name='fecha-$tagcomp' type='date' />
                    <div id='fecha-$tagcomp-errors'></div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='hora-$tagcomp'>Hora de pago</label> <label
                    class='mark-required text-danger fw-bold'>*</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='hora-$tagcomp' name='hora-$tagcomp' type='time'/>
                    <div id='hora-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='uenta-$tagcomp'>Cuenta ordenante (Cliente)</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                    <select class='form-select text-center input-form' id='cuenta-$tagcomp' name='cuenta-$tagcomp' disabled>
                        <option value='' id='default-cuenta-$tagcomp'>- - - -</option>
                        <optgroup id='ordenante-$tagcomp' class='contenedor-cuenta-$tagcomp text-start'></optgroup>
                    </select>
                    <div id='cuenta-$tagcomp-errors'></div>
                </div>
            </div>

            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='benef-$tagcomp'>Cuenta beneficiario (Mis cuentas)</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                    <select class='form-select text-center input-form' id='benef-$tagcomp' name='benef-$tagcomp' disabled>
                        <option value='' id='default-benef-$tagcomp'>- - - -</option>
                        <optgroup id='beneficiario-$tagcomp' class='contenedor-beneficiario-$tagcomp text-start'></optgroup>
                    </select>
                    <div id='benef-$tagcomp-errors'></div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-4 py-2'>
                <label class='label-form text-right' for='transaccion-$tagcomp'>No. de transacción</label><label
                class='mark-required text-danger fw-bold'>&nbsp;</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='transaccion-$tagcomp' name='transaccion-$tagcomp' placeholder='No. de transacción' type='number' disabled />
                    <div id='transaccion-$tagcomp-errors'>
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
                                <label class='label-form mb-1' for='factura-$tagcomp'>Folio factura </label>
                                <input id='id-factura-$tagcomp' type='hidden' value='$idfactura' /><input class='form-control text-center input-form' id='factura-$tagcomp' name='factura-$tagcomp' placeholder='Factura' type='text' oninput='aucompletarFactura();' value='Factura-$folio'/>
                            </td>
                            <td colspan='2'>
                                <label class='label-form mb-1' for='uuid-$tagcomp'>UUID factura</label>
                                <input class='form-control cfdi text-center input-form' id='uuid-$tagcomp' name='uuid-$tagcomp' placeholder='UUID del cfdi' type='text'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='type-$tagcomp'>Tipo factura</label>
                                <select class='form-select text-center input-form' id='type-$tagcomp' name='type-$tagcomp'>
                                    <option value='' id='default-tipo-$tagcomp'>- - - -</option>
                                    <option class='text-start' value='f' id='tipo-f-$tagcomp'>Factura</option>
                                    <option class='text-start' value='c' id='tipo-c-$tagcomp'>Carta Porte</option>
                                </select>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='monedarel-$tagcomp'>Moneda factura</label>
                                <input id='cambiocfdi-$tagcomp' type='hidden' />
                                <input id='metcfdi-$tagcomp' type='hidden' />
                                <select class='form-select text-center input-form' id='monedarel-$tagcomp' disabled name='monedarel-$tagcomp'>
                                    <option value='' id='default-moneda-$tagcomp'>- - - -</option>
                                    <optgroup id='moncfdi-$tagcomp' class='contenedor-moneda-$tagcomp text-start'> </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class='label-form mb-1' for='parcialidad-$tagcomp'>No. Parcialidad</label>
                                <input class='form-control text-center input-form' id='parcialidad-$tagcomp' name='parcialidad-$tagcomp' placeholder='No Parcialidad' type='text'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='total-$tagcomp'>Total factura</label>
                                <input class='form-control text-center input-form' id='total-$tagcomp' name='total-$tagcomp' placeholder='Total de factura' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='anterior-$tagcomp'>Monto anterior</label>
                                <input class='form-control text-center input-form' id='anterior-$tagcomp' name='anterior-$tagcomp' placeholder='Monto anterior' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='monto-$tagcomp'>Monto a pagar</label>
                                <input class='form-control text-center input-form' id='monto-$tagcomp' name='monto-$tagcomp' placeholder='Monto Pagado' type='number' step='any' oninput='calcularRestante()'/>
                            </td>
                            <td>
                                <label class='label-form mb-1' for='restante-$tagcomp'>Monto restante</label>
                                <input class='form-control text-center input-form' id='restante-$tagcomp' name='cantidad' placeholder='Monto restante' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-space' for='btn-agregar-cfdi'></label>
                                <button id='btn-agregar-cfdi' class='button-modal col-12' onclick='agregarCFDI();'><span class='fas fa-plus'></span> Agregar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class='table tab-hover table-condensed table-responsive table-row table-head' id='lista-cfdi-$tagcomp'>

                </table>
            </div>
        </div>
        </div><cut>$tagcomp<cut>$idformapago<cut>$idmoneda<comp>";
        return $datos;
    }

    public function checkStatusCFDI($idpago) {
        $datos = false;
        $factura = $this->getPagoById($idpago);
        foreach ($factura as $actual) {
            $emisor = $actual['rfcemisor'];
            $receptor = $actual['rfcreceptor'];
            $uuid = $actual['uuidpago'];
            $cfdistring = $actual['cfdipago'];
        }

        $xml = simplexml_load_string($cfdistring);
        $comprobante = $xml->xpath('/cfdi:Comprobante');
        $attr = $comprobante[0]->attributes();
        $sello = $attr['Sello'];
        $subsello = substr($sello, -8);
        //https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc
        //
        $soapUrl = "https://pruebacfdiconsultaqr.cloudapp.net/ConsultaCFDIService.svc";
        $consultaCfdi = consultaCfdiSAT::ServicioConsultaSAT($soapUrl, $emisor, $receptor, 0, $uuid, $subsello);
        $codstatus = $consultaCfdi->CodigoEstatus;
        $estado = $consultaCfdi->Estado;
        $cancelable = $consultaCfdi->EsCancelable;
        $statusCancelacion = $consultaCfdi->EstatusCancelacion;

        if (is_array($consultaCfdi->EsCancelable)) {
            $cancelable = "";
        }

        if (is_array($consultaCfdi->EstatusCancelacion)) {
            $statusCancelacion = "";
        }
        $reset = "";
        if ($statusCancelacion === "Solicitud rechazada") {
            $reset = "<button class='button-modal' onclick='resetCfdiPago($idpago)' id='btn-reset-cfdi'>Restaurar Pago <span class='glyphicon glyphicon-repeat'></span></button>";
        }
        $datos = "$codstatus</tr>$estado</tr>$cancelable</tr>$statusCancelacion</tr>$reset";
        return $datos;
    }

    public function estadoPago($idpago) {
        $actualizado = false;
        $consulta = "UPDATE `pagos` SET cfdicancel=:cfdi, cancelado=:estado WHERE idpago=:id;";
        $valores = array("id" => $idpago,
            "estado" => '0',
            "cfdi" => '');
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function aplicarEgreso($uuid, $monto, $monto_egreso){
        $aplicado = 0;
        $total = 0;
        $query = "UPDATE cfdiegreso SET aplicada = :aplicada WHERE uuid = :uuid";
        $val = array("aplicada" => 1, "uuid" => $uuid);
        $aplicado = $this->consultas->execute($query, $val);

        $total = bcdiv(($monto - $monto_egreso), '1', 2);

        return "$aplicado</tr>$total";
    }

    private function getDatosCliente($idcliente, $fid) {
        $consultado = false;
        $consulta = "SELECT c.*, p.codpreceptor FROM cliente c INNER JOIN pagos p ON (c.id_cliente=p.pago_idcliente) WHERE id_cliente=:cid AND p.idpago=:fid;";
        $val = array("cid" => $idcliente,
            "fid" => $fid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDireccionCliente($idcliente, $fid) {
        $direccion = "";
        $datos = $this->getDatosCliente($idcliente, $fid);
        foreach ($datos as $actual) {
            $codpostal = $actual['codigo_postal'];
            $cpreceptor = $actual['codpreceptor'];
            $direccion = "CP. $cpreceptor";
            if ($codpostal == $cpreceptor) {
                $calle = $actual['calle'];
                $numext = $actual['numero_exterior'];
                $localidad = $actual['localidad'];
                $idmunicipio = $actual['idmunicipio'];
                $idestadodir = $actual['idestado'];

                $next = "";
                if ($numext != "") {
                    $next = " #$numext";
                }

                $col = "";
                if ($localidad != "") {
                    $col = ", Colonia: $localidad";
                }

                $cp = "";
                if ($codpostal != "" && $codpostal != "0") {
                    $cp = " CP. $codpostal";
                }

                $municipio = "";
                if ($idmunicipio != "0") {
                    $muni = $actual['nombre_estado'];
                    $municipio = ", $muni";
                }

                $estadodir = "";
                if ($idestadodir != "0") {
                    $est = $actual['nombre_municipio'];
                    $estadodir = ", $est";
                }

                $direccion = $calle . $next . $col . $cp . $municipio . $estadodir;
            }
        }
        return $direccion;
    }
}