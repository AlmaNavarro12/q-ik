<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/SendMail.php';

date_default_timezone_set("America/Mexico_City");
session_start();

class ControladorVenta {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
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

    public function loadNewTicket($ticket) {
        $tag = $this->genTag();
        $tab = "<button id='tab-$tag' class='sm-tab sub-tab-active' data-tab='$tag' name='tab' >Ticket $ticket &nbsp; <span  class='close-button' data-tab='$tag' type='button' aria-label='Close'><span aria-hidden='true'>&times;</span></span></button>
                <cut>
                    <div id='ticket-$tag' class='sub-div'>
                        <table id='prod-$tag' class='table tab-hover table-condensed table-responsive table-row table-venta'>
                            <thead class='sin-paddding'>
                                <tr>
                                    <th class='text-center'>COD BARRAS</th>
                                    <th class='text-center'>CLV FISCAL</th>
                                    <th class='text-center'>DESCRIPCIÓN</th>
                                    <th class='text-center'>PRECIO</th>
                                    <th class='text-center'>CANT.</th>
                                    <th class='text-center'>TRASLADOS</th>
                                    <th class='text-center'>RETENCIONES</th>
                                    <th class='text-center'>IMPORTE</th>
                                    <th class='text-center'>ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                   <th colspan='3'></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div><cut>$tag";
        return $tab;
    }

    private function newbuildArray($tipo, $precio, $taxes) {
        $row = [];
        $consulta = "SELECT * FROM impuesto WHERE tipoimpuesto = :tipo AND porcentaje IN ($taxes)";
        $val = ["tipo" => $tipo];
        $imptraslados = $this->consultas->getResults($consulta, $val);
        
        foreach ($imptraslados as $tactual) {
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $imp = $precio * $porcentaje;
            $row[] = bcdiv($imp, '1', 2) . '-' . $porcentaje . '-' . $impuesto;
        }
    
        return implode("<impuesto>", $row);
    }

    private function getProductobyCodAux($cod) {
        $datos = false;
        $consulta = "SELECT * FROM productos_servicios WHERE codproducto=:cod";
        $val = array("cod" => $cod);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getProductobyCod($cod) {
        $datos = false;
        $prod = $this->getProductobyCodAux($cod);
        foreach ($prod as $actual) {
            $idprod = $actual['idproser'];
            $clvfiscal = $actual['clave_fiscal'] . '-' . $actual['desc_fiscal'];
            $clvunidad = $actual['clv_unidad'] . '-' . $actual['desc_unidad'];
            $nombre = $actual['nombre_producto'];
            $pventa = $actual['precio_venta'];
            $taxes = $actual['impuestos_aplicables'];
            $datos = "$idprod</tr>$clvfiscal</tr>$clvunidad</tr>$nombre</tr>$pventa</tr>$taxes";
        }
        return $datos;
    }

    public function agregarProducto($cod, $tab, $sid) {
        $taxes_traslados = [];
        $taxes_retencion = [];
    
        $div = explode("-", $cod);
        $cod = $div[0];
        $prod = $this->getProductobyCod($cod);
    
        if ($prod) {
            $div = explode("</tr>", $prod);
            $taxes = $div[5];
            $array_taxes = explode("<tr>", $taxes);
    
            foreach ($array_taxes as $tax) {
                $div_tipo = explode("-", $tax);
                if ($div_tipo[0] !== "") {
                    $percen_tax = $div_tipo[0];
                    $tipo = $div_tipo[1];
                    
                    if ($tipo == 1) {
                        $taxes_traslados[] = $percen_tax;
                    } else {
                        $taxes_retencion[] = $percen_tax;
                    }
                }
            }
    
            $traslados = $this->newbuildArray('1', $div[4], implode(",", $taxes_traslados));
            $retenciones = $this->newbuildArray('2', $div[4], implode(",", $taxes_retencion));
    
            $insertar = false;
            
            $consulta = "INSERT INTO `tmpticket` VALUES (:id, :idprod, :cod, :cfiscal, :cunidad, :prod, :precio, :cant, :descuento, :impdescuento, :importe, :totaldescuento, :traslados, :retenciones, :tab, :sid);";
            $val = [
                "id" => null,
                "idprod" => $div[0],
                "cod" => $cod,
                "cfiscal" => $div[1],
                "cunidad" => $div[2],
                "prod" => $div[3],
                "precio" => $div[4],
                "cant" => '1',
                "descuento" => '0',
                "impdescuento" => '0',
                "importe" => $div[4],
                "totaldescuento" => '0',
                "traslados" => $traslados,
                "retenciones" => $retenciones,
                "tab" => $tab,
                "sid" => $sid
            ];
    
            $insertar = $this->consultas->execute($consulta, $val);
        } else {
            $insertar = "0No se encontró el producto";
        }
    
        return $insertar;
    }

    private function getTmpTicket($tab, $sid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpticket WHERE tagtab=:tab AND sid=:sid ORDER BY idtmpticket DESC;";
        $val = array("tab" => $tab,
            "sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getImpuestos($tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto WHERE tipoimpuesto=:tipo";
        $valores = array("tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function tablaTicket($tab, $sid) {
        $datos = "<thead class='sin-paddding'>
                    <tr>
                      <th class='text-center'>COD. BARRAS</th>
                      <th class='text-center'>CLV FISCAL</th>
                      <th class='text-center col-md-3'>DESCRIPCIÓN</th>
                      <th class='text-center col-md-1'>PRECIO</th>
                      <th class='text-center col-md-1'>CANT.</th>
                      <th class='text-center col-sm-2'>IMPORTE</th>
                      <th class='text-center col-md-1'>DESCUENTOS</th>
                      <th class='text-center'>TRASLADOS</th>
                      <th class='text-center'>RETENCIONES</th>
                      <th class='text-center'>ELIMINAR</th>
                    </tr>
                  </thead>
                  <tbody style='max-height: 280px; overflow-y: auto;'>";
    
        $subticket = $sumador_iva = $sumador_ret = $totalticket = $sumador_descuentos = 0;
        $productos = $this->getTmpTicket($tab, $sid);
    
        foreach ($productos as $productoactual) {
            extract($productoactual);
            if($descuento != 0){
                $impdescuentos = bcdiv((($descuento * $tmpimporte) / 100), '1', 2);
            }
    
            $disabledminus = ($tmpcant == '1') ? "disabled" : "";
    
            $imp = $ret = 0;
            $checktraslado = $checkretencion = "";
    
            $impuestos = explode("<impuesto>", $tmptraslados);
            foreach ($impuestos as $tras) {
                [$precio_imp, $porcentaje, $impuesto] = explode("-", $tras);
                $checktraslado .= "$porcentaje-$impuesto<imp>";
                $imp += bcdiv($precio_imp, '1', 2);
            }
    
            $retenciones = explode("<impuesto>", $tmpretenciones);
            foreach ($retenciones as $retn) {
                [$precio_ret, $porcentaje, $impuesto] = explode("-", $retn);
                $checkretencion .= "$porcentaje-$impuesto<imp>";
                $ret += bcdiv($precio_ret, '1', 2);
            }
    
            $subticket += $tmpimporte;
            $sumador_iva += bcdiv($imp, '1', 2);
            $sumador_ret += bcdiv($ret, '1', 2);
            $sumador_descuentos += bcdiv($impdescuentos, '1', 2);
    
            $imptraslados = $this->getImpuestos('1');
            $impretenciones = $this->getImpuestos('2');
    
            $optraslados = $opretencion = "";
    
            foreach ($imptraslados as $tactual) {
                $checkedT = (strpos($checktraslado, "{$tactual['porcentaje']}-{$tactual['impuesto']}") !== false) ? "checked" : "";
                $iconT = ($checkedT === "checked") ? "glyphicon-check" : "glyphicon-unchecked";
                $optraslados .= "<li data-location='tabla' data-id='$idtmpticket'>
                                    <label class='dropdown-menu-item checkbox'>
                                        <input type='checkbox' $checkedT value='{$tactual['porcentaje']}' name='chtrastabla$idtmpticket' data-impuesto='{$tactual['impuesto']}' data-tipo='{$tactual['tipoimpuesto']}'/>
                                        <span class='glyphicon $iconT'></span>{$tactual['nombre']} ({$tactual['porcentaje']}%)</label>
                                </li>";
            }
    
            foreach ($impretenciones as $ractual) {
                $checkedR = (strpos($checkretencion, "{$ractual['porcentaje']}-{$ractual['impuesto']}") !== false) ? "checked" : "";
                $iconR = ($checkedR === "checked") ? "glyphicon-check" : "glyphicon-unchecked";
                $opretencion .= "<li data-location='tabla' data-id='$idtmpticket'>
                                    <label class='dropdown-menu-item checkbox'>
                                        <input type='checkbox' $checkedR value='{$ractual['porcentaje']}' name='chrettabla$idtmpticket' data-impuesto='{$ractual['impuesto']}' data-tipo='{$ractual['tipoimpuesto']}'/>
                                        <span class='glyphicon $iconR'></span>{$ractual['nombre']} ({$ractual['porcentaje']}%)</label>
                                </li>";
            }
    
            $datos .= "
                    <tr>
                        <td>$tmpcod</td>
                        <td>$tmpclvfiscal</td>
                        <td>$tmpprod</td>
                        <td>$ " . bcdiv($tmpprecio, '1', 2) . "</td>
                        <td>
                            <div class='input-group'>
                                <span class='input-group-btn'>
                                    <button type='button' class='btn btn-xs btn-default btn-number' $disabledminus data-type='minus' data-field='quant[1]' onclick='reducirCantidad($idtmpticket);'>
                                        <span class='glyphicon glyphicon-minus'></span>
                                    </button>
                                </span>
                                <button class='badge btn btn-info btn-xs center-block' data-toggle='modal' data-target='#modal-cantidad' onclick='setCantidadVenta($idtmpticket,$tmpcant, $tmpprecio)'>
                                    <div class='badge' id='badcant$idtmpticket'> $tmpcant</div>
                                </button>
                                <span class='input-group-btn'>
                                    <button type='button' class='btn btn-xs btn-default btn-number' data-type='plus' onclick='incrementarCantidad($idtmpticket);'>
                                        <span class='glyphicon glyphicon-plus'></span>
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>$ " . bcdiv($tmpimporte, '1', 2) . "</td>
                        <td class='text-center'> $descuento% <br> $ " . bcdiv($impdescuentos, '1', 2) . "</td>
                        <td>
                            <div class='input-group'>
                                <div class='dropdown'>
                                    <button type='button' class='button-impuesto dropdown-toggle' data-toggle='dropdown'>Traslados <span class='caret'></span></button>
                                    <ul class='dropdown-menu'>
                                        $optraslados
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class='input-group'>
                                <div class='dropdown'>
                                    <button type='button' class='button-impuesto dropdown-toggle' data-toggle='dropdown'>Retenciones <span class='caret'></span></button>
                                    <ul class='dropdown-menu'>
                                        $opretencion
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td>
                         <span class='glyphicon glyphicon-remove list-remove-icon' onclick='eliminarProdTmp($idtmpticket);'></span>
                        </td>
                    </tr>";
        }
    
        $totalticket = ((($subticket + $sumador_iva) - $sumador_ret) - $sumador_descuentos);
    
        $datos .= "</tbody><tfoot><tr>
            <th colspan='3'></th>
            <th class='text-right' colspan='2'>SUBTOTAL:</th>
            <th>$ " . number_format(bcdiv($subticket, '1', 2), 2, '.', ',') . "</th></tr>";
    
        if($sumador_descuentos > 0){
            $datos .= "<tr>
                <th colspan='3'></th>
                <th class='text-right' colspan='2'>DESCUENTO:</th>
                <th>$ " . number_format(bcdiv($sumador_descuentos, '1', 2), 2, '.', ',') . "</th></tr>
                <tr>
                <th colspan='3'></th>
                <th class='text-right' colspan='2'>SUBTOTAL - DESCUENTO:</th>
                <th>$ " . number_format(bcdiv(($subticket-$sumador_descuentos), '1', 2), 2, '.', ',') . "</th></tr>";
        }
    
        if ($sumador_iva > 0) {
            $datos .= "<tr>
                <th colspan='3'></th>
                <th class='text-right' colspan='2'>TRASLADOS:</th>
                <th>$ " . number_format(bcdiv($sumador_iva, '1', 2), 2, '.', ',') . "</th></tr>";
        }
    
        if ($sumador_ret > 0) {
            $datos .= "<tr>
                <th colspan='3'></th>
                <th class='text-right' colspan='2'>RETENCIONES:</th>
                <th>$ " . number_format(bcdiv($sumador_ret, '1', 2), 2, '.', ',') . "</th></tr>";
        }
    
        $datos .= "<tr>
        <th colspan='3'></th>
        <th class='text-right' colspan='2'>GRAN TOTAL:</th>
        <th>$ " . number_format(bcdiv($totalticket, '1', 2), 2, '.', ',') . "</th></tr></tfoot>";
        return $datos;
    }
    
    public function insertarmovEfectivo($v) {
        $insertado = false;
        $hoy = date("Y-m-d");
        $hora = date("H:i");
        $uid = $_SESSION[sha1("idusuario")];
        $consulta = "INSERT INTO movefectivo VALUES (:id, :tipo, :fecha, :hora, :monto, :concepto, :uid);";
        $val = array("id" => null,
            "tipo" => $v->getTipomov(),
            "fecha" => $hoy,
            "hora" => $hora,
            "monto" => $v->getMontomov(),
            "concepto" => $v->getConceptomov(),
            "uid" => $uid);
        $insertado = $this->consultas->execute($consulta, $val);
        return $insertado;
    }

    private function getTotalTicketAux($tag, $sid) {
        $datos = false;
        $consulta = "SELECT tmpimporte, tmptraslados, tmpretenciones, impdescuento FROM tmpticket WHERE tagtab=:tag AND sid=:sid;";
        $val = array("tag" => $tag,
            "sid" => $sid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getTotalArticulos($tag, $sid) {
        $datos = false;
        $consulta = "SELECT count(tmpcod) articulos FROM tmpticket WHERE tagtab=:tag AND sid=:sid;";
        $val = array("tag" => $tag,
            "sid" => $sid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }
    
    public function getTotalTicket($tag, $sid) {
        $subticket = 0;
        $imp = 0;
        $ret = 0;
        $descuento = 0;
        $total = 0;
        $articulos = 0;
    
        $datos = $this->getTotalTicketAux($tag, $sid);
        
        foreach ($datos as $actual) {
            $subticket += $actual['tmpimporte'];
            $descuento += $actual['impdescuento'];
            
            $imp += $this->sumarImpuestos($actual['tmptraslados']);
            $ret += $this->sumarImpuestos($actual['tmpretenciones']);
        }
    
        $total = (($subticket + $imp) - $ret) - $descuento;
    
        $datos2 = $this->getTotalArticulos($tag, $sid);
        $articulos = $datos2[0]['articulos'] ?? 0;
    
        return $total . "</tr>" . $articulos . "</tr>" . $descuento;
    }
    
    private function sumarImpuestos($impuestos) {
        $suma = 0;
        if (!empty($impuestos)) {
            $divImpuestos = explode("<impuesto>", $impuestos);
            foreach ($divImpuestos as $impuesto) {
                [$monto] = explode("-", $impuesto, 2);
                $suma += bcdiv($monto, '1', 2);
            }
        }
        return $suma;
    }
    
}
