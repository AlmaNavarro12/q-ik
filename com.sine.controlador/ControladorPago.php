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

    
    private function getTipoCambioAux($idmoneda) {
        $consultado = false;
        $consulta = "SELECT * FROM catalogo_moneda WHERE idcatalogo_moneda=:id;";
        $val = array("id" => $idmoneda);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getTipoCambio($idmoneda, $idmonedaF = '0', $tcambioF = '0', $tcambioP = '0') {
        $moneda = $this->getTipoCambioAux($idmoneda);
        foreach ($moneda as $actual) {
            $tcambio = $actual['tipo_cambio'];
        }
        if ($idmoneda == '1') {
            if ($idmonedaF == '1') {
                if ($tcambioF != "0") {
                    $tcambio = $tcambioF;
                } else {
                    foreach ($moneda as $actual) {
                        $tcambio = $actual['tipo_cambio'];
                    }
                }
            } else if ($idmonedaF == '2') {
                if ($tcambioF != "0") {
                    $tcambio = bcdiv(1, $tcambioF, 6);
                } else {
                    foreach ($moneda as $actual) {
                        $tcambio = $actual['cambioDolar'];
                    }
                }
            } else if ($idmonedaF == '4') {
                if ($tcambioF != "0") {
                    $tcambio = bcdiv(1, $tcambioF, 6);
                } else {
                    foreach ($moneda as $actual) {
                        $tcambio = $actual['cambioEuro'];
                    }
                }
            }
        } else if ($idmoneda == '2') {
            if ($idmonedaF == '1') {
                if ($tcambioP != '0') {
                    $tcambio = $tcambioP;
                } else {
                    foreach ($moneda as $actual) {
                        $tcambio = $actual['tipo_cambio'];
                    }
                }
            } else if ($idmonedaF == '2') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['cambioDolar'];
                }
            } else if ($idmonedaF == '4') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['cambioEuro'];
                }
            }
        } else if ($idmoneda == '4') {
            if ($idmonedaF == '1') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['tipo_cambio'];
                }
            } else if ($idmonedaF == '2') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['cambioDolar'];
                }
            } else if ($idmonedaF == '4') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['cambioEuro'];
                }
            }
        }
        return $tcambio;
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
        $disuuid = "";
        if ($uuid != "") {
            $disuuid = "disabled";
        }
        $productos = $this->getPagosTMP($sessionid, $tag);
        foreach ($productos as $pagoactual) {
            $idtmp = $pagoactual['idtmppago'];
            $idfactura = $pagoactual['idfacturatmp'];
            $folio = $pagoactual['foliofacturatmp'];
            $idmonedaF = $pagoactual['idmonedatmp'];
            $tcambioF = $pagoactual['tcambiotmp'];
            $noparcialidad = $pagoactual['noparcialidadtmp'];
            $monto = $pagoactual['montotmp'];
            $montoanterior = $pagoactual['montoanteriortmp'];
            $montoinsoluto = $pagoactual['montoinsolutotmp'];
            $totalfactura = $pagoactual['totalfacturatmp'];
            $type = $pagoactual['type'];

            $totalpagados += $this->totalDivisa($monto, $idmoneda, $idmonedaF, $tcambioF, $tcambio);
            $table .= "
                     <tr>
                        <td>$folio</td>
                        <td>$noparcialidad</td>
                        <td>$ " . number_format(bcdiv($totalfactura, '1', 2), 2, '.', ',') . "</td>
                        <td>$ " . number_format(bcdiv($montoanterior, '1', 2), 2, '.', ',') . "</td>
                        <td>$ " . number_format(bcdiv($monto, '1', 2), 2, '.', ',') . "</td>
                        <td>$ " . number_format(bcdiv($montoinsoluto, '1', 2), 2, '.', ',') . "</td>
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
            <th>$ " . number_format(bcdiv($totalpagados, '1', 2), 2, '.', ',') . " $monedapago</th>
            <th></th>
            </tr>
        </tfoot>";
        return $table;
    }

    private function totalDivisa($total, $monedaP, $monedaF, $tcambioF = '0', $tcambioP = '0') {
        if ($monedaP == $monedaF) {
            $OP = bcdiv($total, '1', 2);
        } else {
            $tcambio = $this->getTipoCambio($monedaP, $monedaF, $tcambioF, $tcambioP);
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
        $datos = "<button id='tab-$tag' class='tab-pago sub-tab-active' data-tab='$tag' data-ord='$comp' name='tab-complemento' >Complemento $comp &nbsp; <span data-tab='$tag' type='button' class='close-button' aria-label='Close'><span aria-hidden='true'>&times;</span></span></button>
                <cut>
                <div id='complemento-$tag' class='sub-div'>
                <div class='row'>
            <div class='col-md-4'>
                <label class='label-form text-right' for='forma-$tag'>Forma de Pago</label> <label class='mark-required text-right'>*</label>
                <div class='form-group'>
                        <select class='form-control text-center input-form' id='forma-$tag' name='forma-$tag' onchange='disableCuenta();'>
                            <option value='' id='default-fpago-$tag'>- - - -</option>
                            <optgroup id='forma-pago-$tag' class='cont-fpago-$tag text-left'> </optgroup>
                        </select>
                    <div id='forma-$tag-errors'></div>
                </div>
            </div>

            <div class='col-md-2'>
                <label class='label-form text-right' for='moneda-$tag'>Moneda de Pago</label> <label class='mark-required text-right'>*</label>
                <div class='form-group'>
                    <select class='form-control text-center input-form' id='moneda-$tag' name='moneda-$tag' onchange='getTipoCambio(); loadTablaCFDI();'>
                        <option value='' id='default-moneda-$tag'>- - - -</option>
                        <optgroup id='mpago-$tag' class='contmoneda-$tag text-left'> </optgroup>
                    </select>
                    <div id='moneda-$tag-errors'></div>
                </div>
            </div>

            <div class='col-md-2'>
                <label class='label-form text-right' for='cambio-$tag'>Tipo de Cambio</label>
                <div class='form-group'>
                    <input type='text' class='form-control input-form' id='cambio-$tag' placeholder='Tipo de cambio de Moneda' disabled=''>
                    <div id='cambio-$tag-errors'></div>
                </div>
            </div>

            <div class='col-md-4'>
                <label class='label-form text-right' for='fecha-$tag'>Fecha de Pago</label> <label class='mark-required text-right'>*</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='fecha-$tag' name='fecha-$tag' type='date'/>
                    <div id='fecha-$tag-errors'></div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-4'>
                <label class='label-form text-right' for='hora-$tag'>Hora de Pago</label> <label class='mark-required text-right'>*</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='hora-$tag' name='hora-$tag' type='time' />
                    <div id='hora-$tag-errors'></div>
                </div>
            </div>

            <div class='col-md-4'>
                <label class='label-form text-right' for='uenta-$tag'>Cuenta Ordenante (Cliente)</label>
                <div class='form-group'>
                    <select class='form-control text-center input-form' id='cuenta-$tag' name='cuenta-$tag' disabled>
                        <option value='' id='default-cuenta-$tag'>- - - -</option>
                        <optgroup id='ordenante-$tag' class='contenedor-cuenta-$tag text-left'> </optgroup>
                    </select>
                    <div id='cuenta-$tag-errors'></div>
                </div>
            </div>

            <div class='col-md-4'>
                <label class='label-form text-right' for='benef-$tag'>Cuenta Beneficiario (Mis Cuentas)</label>
                <div class='form-group'>
                    <select class='form-control text-center input-form' id='benef-$tag' name='benef-$tag' disabled>
                        <option value='' id='default-benef-$tag'>- - - -</option>
                        <optgroup id='beneficiario-$tag' class='contenedor-beneficiario-$tag text-left'> </optgroup>
                    </select>
                    <div id='benef-$tag-errors'></div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-4'>
                <label class='label-form text-right' for='transaccion-$tag'>N° de Transaccion</label>
                <div class='form-group'>
                    <input class='form-control text-center input-form' id='transaccion-$tag' name='transaccion-$tag' placeholder='N° de Transaccion' type='number' disabled/>
                    <div id='transaccion-$tag-errors'>
                    </div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-12'>
                <div class='new-tooltip icon tip'> 
                    <label class='label-sub' for='fecha-creacion'>CFDIS RELACIONADOS </label> <span class='glyphicon glyphicon-question-sign'></span>
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
                                <label class='label-form text-right' for='factura-$tag'>Folio Factura</label>
                                <input id='id-factura-$tag' type='hidden' /><input class='form-control text-center input-form' id='factura-$tag' name='factura-$tag' placeholder='Factura' type='text' oninput='aucompletarFactura();'/>
                            </td>
                            <td colspan='2'>
                                <label class='label-form text-right' for='uuid-$tag'>UUID Factura</label>
                                <input class='form-control cfdi text-center input-form' id='uuid-$tag' name='uuid-$tag' placeholder='UUID del cfdi' type='text'/>
                            </td>
                            <td>
                                <label class='label-form text-right' for='type-$tag'>Tipo Factura</label>
                                <select class='form-control text-center input-form' id='type-$tag' name='type-$tag'>
                                    <option value='' id='default-tipo-$tag'>- - - -</option>
                                    <option value='f' id='tipo-f-$tag'>Factura</option>
                                    <option value='c' id='tipo-c-$tag'>Carta Porte</option>
                                </select>
                            </td>
                            <td>
                                <label class='label-form text-right' for='monedarel-$tag'>Moneda Factura</label>
                                <input id='cambiocfdi-$tag' type='hidden' />
                                <input id='metcfdi-$tag' type='hidden' />
                                <select class='form-control text-center input-form' id='monedarel-$tag' name='monedarel-$tag'>
                                    <option value='' id='default-moneda-$tag'>- - - -</option>
                                    <optgroup id='moncfdi-$tag' class='contenedor-moneda-$tag text-left'> </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class='label-form text-right' for='parcialidad-$tag'>N° Parcialidad</label>
                                <input class='form-control text-center input-form' id='parcialidad-$tag' name='parcialidad-$tag' placeholder='No Parcialidad' type='text'/>
                            </td>
                            <td>
                                <label class='label-form text-right' for='total-$tag'>Total Factura</label>
                                <input class='form-control text-center input-form' id='total-$tag' name='total-$tag' placeholder='Total de Factura' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-form text-right' for='anterior-$tag'>Monto Anterior</label>
                                <input class='form-control text-center input-form' id='anterior-$tag' name='anterior-$tag' placeholder='Monto Anterior' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-form text-right' for='monto-$tag'>Monto a Pagar</label>
                                <input class='form-control text-center input-form' id='monto-$tag' name='monto-$tag' placeholder='Monto Pagado' type='number' step='any' oninput='calcularRestante()'/>
                            </td>
                            <td>
                                <label class='label-form text-right' for='restante-$tag'>Monto Restante</label>
                                <input class='form-control text-center input-form' id='restante-$tag' name='cantidad' placeholder='Monto Restante' type='number' step='any'/>
                            </td>
                            <td>
                                <label class='label-space' for='btn-agregar-cfdi'></label>
                                <button id='btn-agregar-cfdi' class='button-modal' onclick='agregarCFDI();'><span class='glyphicon glyphicon-plus'></span> Agregar</button>
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
}