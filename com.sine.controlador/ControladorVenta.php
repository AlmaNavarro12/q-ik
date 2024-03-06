<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/SendMail.php';

date_default_timezone_set("America/Mexico_City");
session_start();

class ControladorVenta
{

    private $consultas;

    function __construct()
    {
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

    public function loadNewTicket($ticket)
    {
        $tag = $this->genTag();
        $tab = "<button id='tab-$tag' class='sm-tab sub-tab-active' data-tab='$tag' name='tab' >Ticket $ticket &nbsp; <span  class='close-button' data-tab='$tag' type='button' aria-label='Close'><span aria-hidden='true'>&times;</span></span></button>
                <cut>
                    <div id='ticket-$tag' class='sub-div'>
                        <table id='prod-$tag' class='table tab-hover table-condensed table-responsive table-row table-venta'>
                            <thead class='sin-paddding'>
                                <tr>
                                    <th class='text-center'>COD.BARRAS</th>
                                    <th class='text-center'>CLV.FISCAL</th>
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

    private function newbuildArray($tipo, $precio, $taxes)
    {
        $row = array();
        $consulta = "SELECT * FROM impuesto WHERE tipoimpuesto = :tipo AND porcentaje IN ($taxes)";
        $val = array("tipo" => $tipo);
        $imptraslados = $this->consultas->getResults($consulta, $val);
        foreach ($imptraslados as $tactual) {
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $imp = $precio * $porcentaje;
            $row[] = bcdiv($imp, '1', 2) . '-' . $porcentaje . '-' . $impuesto;
        }

        $trasarray = implode("<impuesto>", $row);
        return $trasarray;
    }

    private function getProductobyCodAux($cod)
    {
        $datos = false;
        $consulta = "SELECT * FROM productos_servicios WHERE codproducto=:cod";
        $val = array("cod" => $cod);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getProductobyCod($cod)
    {
        $datos = false;
        $prod = $this->getProductobyCodAux($cod);
        foreach ($prod as $actual) {
            $idprod = $actual['idproser'];
            $clvfiscal = $actual['clave_fiscal'] . '-' . $actual['desc_fiscal'];
            $clvunidad = $actual['clv_unidad'] . '-' . $actual['desc_unidad'];
            $nombre = $actual['nombre_producto'];
            $pventa = $actual['precio_venta'];
            $taxes = $actual['impuestos_aplicables'];
            $inv = $actual['cantinv'];
            $datos = "$idprod</tr>$clvfiscal</tr>$clvunidad</tr>$nombre</tr>$pventa</tr>$taxes</tr>$inv";
        }
        return $datos;
    }

    public function agregarProducto($cod, $tab, $sid, $cantidad = 1)
    {
        $taxes_traslados = "''";
        $taxes_retencion = "''";

        $div = explode("-", $cod);
        $cod = $div[0];
        $prod = $this->getProductobyCod($cod);

        if ($prod) {
            $div = explode("</tr>", $prod);
            $id_prod = $div[0];
            $cfiscal = $div[1];
            $cunidad = $div[2];
            $product = $div[3];
            $precio = $div[4];
            $importe = bcdiv(($precio * $cantidad), '1', 2);

            $taxes = $div[5];
            $array_taxes = explode("<tr>", $taxes);

            for ($i = 0; $i < sizeof($array_taxes); $i++) {
                $div_tipo = explode("-", $array_taxes[$i]);
                if ($div_tipo[0] != "") {
                    $percen_tax = $div_tipo[0];
                    $tipo = $div_tipo[1];

                    if ($tipo == 1) {
                        $taxes_traslados .= ",'" . $percen_tax . "'";
                    } else {
                        $taxes_retencion .= ",'" . $percen_tax . "'";
                    }
                }
            }

            $traslados = $this->newbuildArray('1', $importe, $taxes_traslados);
            $retenciones = $this->newbuildArray('2', $importe, $taxes_retencion);

            $insertar = false;
            $consulta = "INSERT INTO `tmpticket` VALUES (:id, :idprod, :cod, :cfiscal, :cunidad, :prod, :precio, :cant, :descuento, :impdescuento, :importe, :totaldescuento, :traslados, :retenciones, :tab, :sid);";
            $val = array(
                "id" => null,
                "idprod" => $id_prod,
                "cod" => $cod,
                "cfiscal" => $cfiscal,
                "cunidad" => $cunidad,
                "prod" => $product,
                "precio" => $precio,
                "cant" => $cantidad,
                "descuento" => '0',
                "impdescuento" => '0',
                "importe" => $importe,
                "totaldescuento" => '0',
                "traslados" => $traslados,
                "retenciones" => $retenciones,
                "tab" => $tab,
                "sid" => $sid
            );
            $insertar = $this->consultas->execute($consulta, $val);
            $restante = $div[6] - $cantidad;
            $this->restaurarInvCant($div[0], $restante);
        } else {
            $insertar = "0No se encontró el producto";
        }

        return $insertar;
    }


    private function getTmpTicket($tab, $sid)
    {
        $consultado = false;
        $consulta = "SELECT * FROM tmpticket WHERE tagtab=:tab AND sid=:sid ORDER BY idtmpticket DESC;";
        $val = array(
            "tab" => $tab,
            "sid" => $sid
        );
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    //-------------------------------CAJA

    private function getDineroCajaAux()
    {
        $hoy = date("Y-m-d");
        $uid = $_SESSION[sha1("idusuario")];
        $datos = false;
        $consulta = "SELECT * FROM fondocaja WHERE fechaingreso=:fecha AND uidfondo=:uid";
        $val = array(
            "fecha" => $hoy,
            "uid" => $uid
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function checkDineroCaja()
    {
        $datos = $this->getDineroCajaAux();
        return empty($datos);
    }

    public function insertarMontoInicial($monto)
    {
        $hoy = date("Y-m-d");
        $hora = date("H:i");
        $insertado = false;
        $consulta = "INSERT INTO fondocaja VALUES (:id, :fecha, :hora, :fondo, :uid);";
        $val = array(
            "id" => null,
            "fecha" => $hoy,
            "hora" => $hora,
            "fondo" => $monto,
            "uid" => $_SESSION[sha1("idusuario")]
        );
        $insertado = $this->consultas->execute($consulta, $val);
        return $insertado;
    }

    public function insertarmovEfectivo($v)
    {
        $insertado = false;
        $hoy = date("Y-m-d");
        $hora = date("H:i");
        $uid = $_SESSION[sha1("idusuario")];
        $consulta = "INSERT INTO movefectivo VALUES (:id, :tipo, :fecha, :hora, :monto, :concepto, :uid);";
        $val = array(
            "id" => null,
            "tipo" => $v->getTipomov(),
            "fecha" => $hoy,
            "hora" => $hora,
            "monto" => $v->getMontomov(),
            "concepto" => $v->getConceptomov(),
            "uid" => $uid
        );
        $insertado = $this->consultas->execute($consulta, $val);
        return $insertado;
    }

    //-------------------------------IMPUESTOS
    private function getImpuestos($tipo)
    {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto WHERE tipoimpuesto=:tipo";
        $valores = array("tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function tablaTicket($tab, $sid)
    {
        $datos = "<thead class='sin-paddding'>
                <tr class='align-middle'>
                    <th class='text-center'>COD. BARRAS</th>
                    <th class='text-center'>CLV.FISCAL</th>
                    <th class='text-center col-md-3'>DESCRIPCIÓN</th>
                    <th class='text-center col-md-1'>PRECIO</th>
                    <th class='text-center col-md-1'>CANT.</th>
                    <th class='text-center col-sm-2'>IMPORTE</th>
                    <th class='text-center col-md-1'>DESCUENTOS</th>
                    <th class='text-center'>TRASLADOS</th>
                    <th class='text-center'>RETENCIONES</th>
                    <th class='text-center'><span class='fas fa-times'></span></th>
                </tr>
            </thead>
            <tbody style='max-width:90%; max-height: 280px; overflow-y: auto;'>";

        $subticket = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $totalticket = 0;
        $sumador_descuentos = 0;
        $productos = $this->getTmpTicket($tab, $sid);

        foreach ($productos as $productoactual) {
            $tid = $productoactual['idtmpticket'];
            $cod = $productoactual['tmpcod'];
            $cfiscal = $productoactual['tmpclvfiscal'];
            $nmprod = $productoactual['tmpprod'];
            $precio = $productoactual['tmpprecio'];
            $cant = $productoactual['tmpcant'];
            $importe = $productoactual['tmpimporte'];
            $traslados = $productoactual['tmptraslados'];
            $retencion = $productoactual['tmpretenciones'];
            $descuentos = $productoactual['descuento'];
            $impdescuentos = $productoactual['impdescuento'];

            if ($descuentos != 0) {
                $impdescuentos = bcdiv((($descuentos * $importe) / 100), '1', 2);
            }

            $disabledminus = ($cant == '1') ? "disabled" : "";

            $imp = 0;
            $checktraslado = "";
            if ($traslados != "") {
                $divtras = explode("<impuesto>", $traslados);
                foreach ($divtras as $tras) {
                    $impuestos = $tras;
                    $div = explode("-", $impuestos);
                    $checktraslado .= $div[1] . "-" . $div[2] . "<imp>";
                    $imp += bcdiv($div[0], '1', 2);
                }
            }

            $ret = 0;
            $checkretencion = "";
            if ($retencion != "") {
                $divret = explode("<impuesto>", $retencion);
                foreach ($divret as $retn) {
                    $retenciones = $retn;
                    $divr = explode("-", $retenciones);
                    $checkretencion .= $divr[1] . "-" . $divr[2] . "<imp>";
                    $ret += bcdiv($divr[0], '1', 2);
                }
            }

            $subticket += $importe;
            $sumador_iva += bcdiv($imp, '1', 2);
            $sumador_ret += bcdiv($ret, '1', 2);
            $sumador_descuentos += bcdiv($impdescuentos, '1', 2);


            $optraslados = "";
            $opretencion = "";
            $imptraslados = $this->getImpuestos('1');
            $impretenciones = $this->getImpuestos('2');

            if ($imptraslados != "") {
                $optraslados = $this->generarOpcionesImpuestos($imptraslados, $checktraslado, $tid, 'tras');
            } else {
                $optraslados = "No hay impuestos de traslado.";
            }

            if ($impretenciones != "") {
                $opretencion = $this->generarOpcionesImpuestos($impretenciones, $checkretencion, $tid, 'ret');
            } else {
                $opretencion = "No hay impuestos de retención.";
            }

            $datos .= "
            <tr>
                <td class='text-center'>$cod</td>
                <td class='text-center'>$cfiscal</td>
                <td class='text-center'>$nmprod</td>
                <td>$ " . bcdiv($precio, '1', 2) . "</td>
                <td>
                    <div class='btn-group btn-group-sm'>
                            <button type='button' class='btn btn-outline-secondary' $disabledminus data-type='minus' data-field='quant[1]' onclick='reducirCantidad($tid);'>
                                <span class='fas fa-minus small'></span>
                            </button>
                        <button class='badge btn btn-info' data-bs-toggle='modal' data-bs-target='#modal-cantidad' onclick='setCantidadVenta($tid,$cant, $precio)'>
                            <div class='badge fw-bold small' id='badcant$tid'> $cant</div>
                        </button>
                            <button type='button' class='btn btn-outline-secondary' data-type='plus' onclick='incrementarCantidad($tid);'>
                                <span class='fas fa-plus small'></span>
                            </button>
                    </div>
                </td>
                <td class='text-center'>$ " . bcdiv($importe, '1', 2) . "</td>
                <td class='text-center text-break'> " . bcdiv($impdescuentos, '1', 2) . " % <br>$ " . bcdiv($descuentos, '1', 2)  . "</td>
                <td>
                    <div class='input-group'>
                        <div class='dropdown'>
                            <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='true' data-bs-auto-close='outside'>Traslados</button>
                            <ul class='dropdown-menu'>
                                $optraslados
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class='input-group'>
                        <div class='dropdown'>
                            <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown'>Retenciones</button>
                            <ul class='dropdown-menu ps-3 lh-sm'>
                                $opretencion
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <span class='fas fa-times list-remove-icon pt-1 ps-2' onclick='eliminarProdTmp($tid);'></span>
                </td>
            </tr></tbody>";
        }

        $totalticket = ((($subticket + $sumador_iva) - $sumador_ret) - $sumador_descuentos);

        if ($totalticket != 0) {
            $datos .= "
        <tfoot class=''>
        <tr>
        <th colspan='6'><ul class='list-group mb-3 mt-3 pe-0'>
        <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-bold'>SUBTOTAL:</h6>
            </div>
            <span class='titulo-lista fw-bold fs-5'>$" . number_format(bcdiv($subticket, '1', 2), 2, '.', ',') . " </span>
        </li>";

            if ($sumador_descuentos > 0) {
                $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-bold'>DESCUENTO:</h6>
            </div>
            <span class='titulo-lista fw-bold fs-5'>$" . number_format(bcdiv($sumador_descuentos, '1', 2), 2, '.', ',') . " </span>
            </li>
            <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-bold'>SUBTOTAL - DESCUENTO:</h6>
            </div>
            <span class='titulo-lista fw-bold fs-5'>$" . number_format(bcdiv(($subticket - $sumador_descuentos), '1', 2), 2, '.', ',') . " </span>
            </li>";
            }

            if ($sumador_iva > 0) {
                $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-bold'>TRASLADOS:</h6>
            </div>
            <span class='titulo-lista fw-bold fs-5'>$" . number_format(bcdiv($sumador_iva, '1', 2), 2, '.', ',') . " </span>
            </li>";
            }

            if ($sumador_ret > 0) {
                $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-bold'>RETENCIONES:</h6>
            </div>
            <span class='titulo-lista fw-bold fs-5'>$" . number_format(bcdiv($sumador_ret, '1', 2), 2, '.', ',') . " </span>
            </li>";
            }

            $datos .= "<li class='list-group-item d-flex justify-content-between'>
            <span class='titulo-lista fs-6 fw-bold'>GRAN TOTAL (MXN)</span>
            <strong class='titulo-lista fw-bold fs-5'>$" . number_format(bcdiv($totalticket, '1', 2), 2, '.', ',') . "</strong>
            </li>
            </ul></th>
            </tr>";
        }
        return $datos;
    }

    private function generarOpcionesImpuestos($impuestos, $check, $tid, $tipo)
    {
        $opciones = "";
        foreach ($impuestos as $actual) {
            $icon = "far fa-square";
            $checked = "";

            $divcheck = explode("<imp>", $check);
            foreach ($divcheck as $chk) {
                if ($chk == $actual['porcentaje'] . "-" . $actual['impuesto']) {
                    $icon = "far fa-check-square";
                    $checked = "checked";
                    break;
                }
            }
            $nombre = $actual['nombre'];
            $porcentaje = $actual['porcentaje'];
            $impuesto = $actual['impuesto'];

            $opciones .= "<li data-location='tabla' data-id='$tid'>
            <label class='dropdown-menu-item checkbox ps-3'>
                <input type='checkbox' $checked value='$porcentaje' name='ch{$tipo}tabla$tid' data-impuesto='$impuesto' data-tipo='$tipo' />
                <span class='$icon me-2' id='chuso1span'></span>$nombre ($porcentaje%)
            </label>
        </li>";
        }

        return $opciones;
    }
    private function getTotalTicketAux($tag, $sid)
    {
        $datos = false;
        $consulta = "SELECT tmpimporte, tmptraslados, tmpretenciones, impdescuento FROM tmpticket WHERE tagtab=:tag AND sid=:sid;";
        $val = array(
            "tag" => $tag,
            "sid" => $sid
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    //-------------------------------TICKET FINAL
    private function getTotalArticulos($tag, $sid)
    {
        $datos = false;
        $consulta = "SELECT count(tmpcod) articulos FROM tmpticket WHERE tagtab=:tag AND sid=:sid;";
        $val = array(
            "tag" => $tag,
            "sid" => $sid
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getTotalTicket($tag, $sid)
    {
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

    private function sumarImpuestos($impuestos)
    {
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

    private function checarTicketTmpById($idtmp)
    {
        $consultado = false;
        $consulta = "SELECT * FROM tmpticket WHERE idtmpticket=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function reBuildArray2($importe, $array)
    {
        $div = explode("<impuesto>", $array);
        $row = array();
        $Timp = 0;
        foreach ($div as $d) {
            $div2 = explode("-", $d);
            $imp = ($importe * $div2[1]);
            $Timp += $imp;
            if ($Timp > 0) {
                $row[] = bcdiv($imp, '1', 2) . '-' . $div2[1] . '-' . $div2[2];
            }
        }
        $rearray = implode("<impuesto>", $row);
        return $rearray;
    }

    public function modificarChIva($idtmp, $chiva, $chret)
    {
        $check = $this->checarTicketTmpById($idtmp);
        foreach ($check as $actual) {
            $canttmp = $actual['tmpcant'];
            $precio_tmp = $actual['tmpprecio'];
        }
        $importe = $canttmp * $precio_tmp;
        $rebuildT = $this->reBuildArray2($importe, $chiva);
        $rebuildR = $this->reBuildArray2($importe, $chret);

        $consulta = "UPDATE `tmpticket` SET tmptraslados=:chiva, tmpretenciones=:chret, tmpimporte=:totun WHERE idtmpticket=:idtmp;";
        $val = array(
            "chiva" => $rebuildT,
            "chret" => $rebuildR,
            "totun" => $importe,
            "idtmp" => $idtmp
        );
        $datos = $this->consultas->execute($consulta, $val);
        return $datos;
    }

    //---------------------------------------------INVENTARIO
    private function restaurarInvCant($idproducto, $cantidad)
    {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=:cantidad where idproser=:idproducto;";
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function restaurarInventario($idproducto, $cantidad)
    {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv+:cantidad where idproser=:idproducto;";
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function checkProductoAux($idtmp)
    {
        $consultado = false;
        $consulta = "SELECT cantinv, chinventario FROM productos_servicios WHERE idproser=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function modificarCantidad($idtmp, $cant, $precio)
    {
        $check = $this->checarTicketTmpById($idtmp);
        $canttmp = $check[0]['tmpcant'];
        $precio_tmp = $check[0]['tmpprecio'];
        $trasladotmp = $check[0]['tmptraslados'];
        $rettmp = $check[0]['tmpretenciones'];
        $idproducto = $check[0]['tmpidprod'];

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
        }

        $importe = $cant * $precio_tmp;
        $traslado = $trasladotmp != "" ? $this->reBuildArray2($importe, $trasladotmp) : "";
        $retencion = $rettmp != "" ? $this->reBuildArray2($importe, $rettmp) : "";

        $restante = ($cantidad + $canttmp) - $cant;

        if ($chinv == '1' && $restante < 0) {
            $datos = "0El inventario no es suficiente para agregar más producto. Hay " . $cantidad + $canttmp . " productos en existencia.";
        } else {
            $consulta = "UPDATE `tmpticket` SET tmpcant=:cant, tmpimporte=:totuni, tmptraslados=:traslados, tmpretenciones=:retenciones  WHERE idtmpticket=:id;";
            $valores = array(
                "id" => $idtmp,
                "cant" => $cant,
                "totuni" => bcdiv($importe, '1', 2),
                "traslados" => $traslado,
                "retenciones" => $retencion
            );
            $datos = $this->consultas->execute($consulta, $valores);
            if ($chinv == '1') {
                $this->restaurarInvCant($idproducto, $restante);
            }
        }
        return $datos;
    }

    public function incrementarProducto($idtmp)
    {
        $check = $this->checarTicketTmpById($idtmp);
        $codprod = '';
        $precio_tmp = 0;
        $canttmp = 0;
        $trasladotmp = '';
        $rettmp = '';
        $descuento = 0;

        foreach ($check as $actual) {
            $codprod = $actual['tmpcod'];
            $precio_tmp = $actual['tmpprecio'];
            $canttmp = $actual['tmpcant'];
            $trasladotmp = $actual['tmptraslados'];
            $rettmp = $actual['tmpretenciones'];
            $descuento = $actual['descuento'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->getProductobyCodAux($codprod);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
            $idproducto = $pactual['idproser'];
        }
        $cant = $canttmp + 1;
        $importe = $cant * $precio_tmp;
        $descuento = ($descuento * $importe) / 100;
        $importe_descuento = $importe - $descuento;
        $restante = $cantidad - 1;

        $traslado = '';
        if ($trasladotmp != "") {
            $traslado = $this->reBuildArray2($importe_descuento, $trasladotmp);
        }

        $retencion = '';
        if ($rettmp != "") {
            $retencion = $this->reBuildArray2($importe_descuento, $rettmp);
        }

        $consulta = "UPDATE `tmpticket` SET tmpcant=:cant, tmpimporte=:importe, tmptraslados=:tras, tmpretenciones=:ret WHERE idtmpticket=:id;";
        $valores = array(
            "id" => $idtmp,
            "cant" => $cant,
            "importe" => bcdiv($importe, '1', 2),
            "tras" => $traslado,
            "ret" => $retencion
        );

        if ($chinv == '1' && $cantidad <= 0) {
            return "0El inventario no es suficiente para agregar más producto. Hay " . $canttmp . " productos en existencia.";
        }
        if ($chinv == '1') {
            $inv = $this->restaurarInvCant($idproducto, $restante);
        }
        $datos = $this->consultas->execute($consulta, $valores);
        return $datos;
    }

    public function reducirProducto($idtmp)
    {
        $check = $this->checarTicketTmpById($idtmp);
        foreach ($check as $actual) {
            $codprod = $actual['tmpcod'];
            $precio_tmp = $actual['tmpprecio'];
            $canttmp = $actual['tmpcant'];
            $trasladotmp = $actual['tmptraslados'];
            $rettmp = $actual['tmpretenciones'];
            $descuento = $actual['descuento'];
        }
        $chinv = 0;
        $prod = $this->getProductobyCodAux($codprod);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $idproducto = $pactual['idproser'];
        }

        $cant = $canttmp - 1;
        $importe = $cant * $precio_tmp;
        $descuento = ($descuento * $importe) / 100;
        $importe_descuento = ($importe - $descuento);
        $traslado = $this->reBuildArray2($importe_descuento, $trasladotmp);
        $retencion = $this->reBuildArray2($importe_descuento, $rettmp);

        $consulta = "UPDATE `tmpticket` SET tmpcant=:cant, tmpimporte=:importe, tmptraslados=:tras, tmpretenciones=:ret WHERE idtmpticket=:id;";
        $valores = array(
            "id" => $idtmp,
            "cant" => $cant,
            "importe" => bcdiv($importe, '1', 2),
            "tras" => $traslado,
            "ret" => $retencion
        );
        $datos = $this->consultas->execute($consulta, $valores);
        if ($chinv == '1') {
            $inv = $this->restaurarInventario($idproducto, '1');
        }
        return $datos;
    }


    private function obtenerProductosByTag($tag, $sid)
    {
        $consulta = "SELECT tmpidprod, tmpcant FROM tmpticket WHERE tagtab= :tag AND sid = :sid";
        $valores = array(
            "tag" => $tag,
            "sid" => $sid
        );
        return $this->consultas->getResults($consulta, $valores);
    }

    public function eliminarProducto($tid)
    {
        $eliminado = false;
        $resultados = $this->checarTicketTmpById($tid);
        if ($resultados && count($resultados) > 0) {
            foreach ($resultados as $resultado) {
                $idproducto = $resultado['tmpidprod'];
                $cantidad = $resultado['tmpcant'];
                $this->restaurarInventario($idproducto, $cantidad);
            }
        }
        $consultaEliminar = "DELETE FROM tmpticket WHERE idtmpticket=:tid";
        $valoresEliminar = array("tid" => $tid);
        $eliminado = $this->consultas->execute($consultaEliminar, $valoresEliminar);
        return $eliminado;
    }

    public function validaProductos($tag)
    {
        $consulta = "SELECT COUNT(*) AS maximo FROM tmpticket WHERE tagtab = :tag";
        $val = array("tag" => $tag);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach ($stmt as $row) {
            $maximo = $row['maximo'];
        }
        $json = array();
        $json['maximo'] = $maximo;
        return $json;
    }

    //---------------------------------GUARDAR TICKET
    private function getFoliobyID()
    {
        $consultado = false;
        $consulta = "SELECT * FROM folio WHERE usofolio LIKE '%7%';";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function updateFolioConsecutivo($id)
    {
        $consultado = false;
        $consulta = "UPDATE folio SET consecutivo=(consecutivo+1) WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getFolio()
    {
        $datos = "";
        $folios = $this->getFoliobyID();
        foreach ($folios as $actual) {
            $id = $actual['idfolio'];
            $serie = $actual['serie'];
            $letra = $actual['letra'];
            $consecutivo = $actual['consecutivo'];
            if ($consecutivo < 10) {
                $consecutivo = "000$consecutivo";
            } else if ($consecutivo >= 10 && $consecutivo < 100) {
                $consecutivo = "00$consecutivo";
            } else if ($consecutivo >= 100 && $consecutivo < 1000) {
                $consecutivo = "0$consecutivo";
            }
            $datos = "$serie</tr>$letra</tr>$consecutivo";
            $this->updateFolioConsecutivo($id);
        }
        return $datos;
    }

    private function detalleVenta($tag, $sid)
    {
        $insertado = false;
        $detalle = $this->getTmpTicket($tag, $sid);
        foreach ($detalle as $actual) {
            $idprod = $actual['tmpidprod'];
            $cod = $actual['tmpcod'];
            $cfiscal = $actual['tmpclvfiscal'];
            $cunidad = $actual['tmpclvunidad'];
            $nmprod = $actual['tmpprod'];
            $precio = $actual['tmpprecio'];
            $cant = $actual['tmpcant'];
            $importe = $actual['tmpimporte'];
            $traslados = $actual['tmptraslados'];
            $retenciones = $actual['tmpretenciones'];
            $tag = $actual['tagtab'];

            $descuento = $actual['descuento'];
            $impdescuento = $actual['impdescuento'];
            $totaldescuento = $actual['totaldescuento'];

            $consulta = "INSERT INTO detalle_venta VALUES (:id, :tag, :idprod, :cod, :clv, :cunidad, :prod, :precio, :cant, :descuento, :impdescuento, :totaldescuento, :importe, :tras, :ret);";
            $val = array(
                "id" => null,
                "tag" => $tag,
                "idprod" => $idprod,
                "cod" => $cod,
                "clv" => $cfiscal,
                "cunidad" => $cunidad,
                "prod" => $nmprod,
                "precio" => $precio,
                "cant" => $cant,
                "descuento" => $descuento,
                "impdescuento" => $impdescuento,
                "totaldescuento" => $totaldescuento,
                "importe" => $importe,
                "tras" => $traslados,
                "ret" => $retenciones
            );
            $insertado = $this->consultas->execute($consulta, $val);
        }
        $this->deleteTicket($tag, $sid);
        return $insertado;
    }

    public function insertarTicket($v)
    {
        $insertar = false;
        $hoy = date("Y-m-d");
        $hora = date("H:i");
        $folios = $this->getFolio();
        $Fdiv = explode("</tr>", $folios);
        $serie = $Fdiv[0];
        $letra = $Fdiv[1];
        $nfolio = $Fdiv[2];
        if ($v->getFormapago() == 'cash') {
            $pagado = $v->getMontopagado();
            $referencia = '';
            $cambio = ($v->getMontopagado() - $v->getTotalventa());
        } else if ($v->getFormapago() == 'card' || $v->getFormapago() == 'val') {
            $pagado = '0';
            $referencia = $v->getReferencia();
            $cambio = '0';
        }

        $consulta = "INSERT INTO datos_venta VALUES (:id, :serie, :letra, :folio, :tag, :fecha, :hora, :percentDescuento, :descuento, :total, :fmpago, :pago, :cambio, :refventa, :uid, :status);";
        $val = array(
            "id" => null,
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "tag" => $v->getTagventa(),
            "fecha" => $hoy,
            "hora" => $hora,
            "percentDescuento" => $v->getPercentDescuento(),
            "descuento" => $v->getDescuento(),
            "total" => $v->getTotalventa(),
            "fmpago" => $v->getFormapago(),
            "pago" => $pagado,
            "cambio" => $cambio,
            "refventa" => $referencia,
            "uid" => $_SESSION[sha1("idusuario")],
            "status" => '1'
        );
        $insertar = $this->consultas->execute($consulta, $val);
        $this->detalleVenta($v->getTagventa(), $v->getSid());
        return $insertar;
    }

    private function deleteTicket($tag, $sid)
    {
        $borrar = false;
        $consulta = "DELETE FROM tmpticket WHERE tagtab=:tag AND sid=:sid;";
        $val = array(
            "tag" => $tag,
            "sid" => $sid
        );
        $borrar = $this->consultas->execute($consulta, $val);
        return $borrar;
    }

    public function cerrarTicket($tag, $sid)
    {
        $actualizado = false;
        $productos = $this->obtenerProductosByTag($tag, $sid);
        foreach ($productos as $producto) {
            $idproducto = $producto['tmpidprod'];
            $cantidad = $producto['tmpcant'];
            $this->restaurarInventario($idproducto, $cantidad);
        }
        $actualizado = $this->deleteTicket($tag, $sid);
        return $actualizado;
    }

    private function getTmpTicketBySID($sid)
    {
        $consultado = false;
        $consulta = "SELECT * FROM tmpticket WHERE sid=:sid ORDER by idtmpticket";
        $val = array("sid" => $sid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function cancelar($sessionid)
    {
        $tmp = $this->getTmpTicketBySID($sessionid);
        foreach ($tmp as $actual) {
            $idprod = $actual['tmpidprod'];
            $cantidad = $actual['tmpcant'];
            $this->restaurarInventario($idprod, $cantidad);
        }
        $eliminado = false;
        $consulta = "DELETE FROM `tmpticket` WHERE sid=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    //-------------------------------------TICKETS ANTIGUOS
    private function getNumrowsAux($condicion)
    {
        $consultado = false;
        $consulta = "SELECT count(iddatos_venta) numrows FROM datos_venta $condicion;";
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

    private function getPermisoById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT p.crearventa, p.listaventa, p.cancelarventa, p.exportarventa FROM usuariopermiso p WHERE permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario)
    {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listaventa'];
            $cancelar = $actual['cancelarventa'];
            $exportar = $actual['exportarventa'];
            $datos .= "$lista</tr>$cancelar</tr>$exportar";
        }
        return $datos;
    }

    private function getSevicios($condicion)
    {
        $consultado = false;
        $consulta = "SELECT * FROM datos_venta $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getNameUser($id)
    {
        $nombre = "";
        $consulta = "SELECT CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre FROM usuario WHERE idusuario = :id";
        $val = array("id" => $id);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach ($stmt as $row) {
            $nombre = $row['nombre'];
        }
        return $nombre;
    }

    public function translateMonth($m)
    {
        return [
            '01' => 'Ene',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dic'
        ][$m] ?? '';
    }

    public function listaServiciosHistorial($pag, $REF, $numreg, $user)
    {
        require_once '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];

        $datos = "<thead class='sin-paddding'>
                    <tr>
                        <th class='text-center col-md-2'>Id. Venta </th>
                        <th class='text-center col-md-3'>Persona Realizó </th>
                        <th class='text-center'>Fecha de venta </th>
                        <th class='text-center'>Forma de Pago </th>
                        <th class='text-center'>Estado </th>
                        <th class='text-center'>Total </th>
                        <th class='text-center'>Opción </th>
                    </tr>
                </thead>
                <tbody>";

        $condicion = "";
        if ($REF == "") {
            if ($user == 0) {
                $condicion = "ORDER BY iddatos_venta DESC";
            } else {
                $condicion = "WHERE uid_venta = $user ORDER BY iddatos_venta DESC";
            }
        } else {
            if ($user == 0) {
                $condicion = "WHERE (concat(letra,folio) LIKE '%$REF%')";
            } else {
                $condicion = "WHERE uid_venta = $user AND (concat(letra,folio) LIKE '%$REF%')";
            }
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
            $finales = 0;
            $lista = $this->getSevicios($con);
            foreach ($lista as $actual) {

                $idventa = $actual['iddatos_venta'];
                $folio = $actual['letra'] . $actual['folio'];
                $tagventa = $actual['tagventa'];
                $fecha = $actual['fecha_venta'];
                $hora = $actual['hora_venta'];
                $formapago = $actual['formapago'];
                $formapago = ($formapago == "cash") ? "Efectivo" : $formapago;
                $totalventa = $actual['totalventa'];
                $status = $actual['status_venta'];
                $cve_usu = $actual['uid_venta'];
                $nombre_user = $this->getNameUser($cve_usu);
                $color = "#1E7457";
                $estado = "Entregado";
                $cancelar = "";
                $exportar = "";
                if ($div[1] == '1') {
                    $cancelar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='cancelarTicket($idventa);'>Cancelar ticket <i class='text-muted fas fa-times'></i></a></li>";
                }

                if ($div[2] == '1') {
                    $exportar = "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='exportarTicket($idventa);'>Exportar a factura <span class='fas fa-edit text-muted small'></span></a></li>";
                }

                $sello = "";

                if ($status == 0) {
                    $color = "#910024";
                    $estado = "Cancelado";
                    $cancelar = "";
                    $exportar = "";
                    $sello = "../img/TicketCancelado.png";
                }

                $divF = explode("-", $fecha);
                $mes = $this->translateMonth($divF[1]);

                $fecha = $divF[2] . ' - ' . $mes;

                $datos .= "<tr class='table-row'>
                           <td class='fw-semibold text-center'>$folio</td>
                           <td class='fw-semibold text-center'>$nombre_user</td>
                           <td class='fw-semibold text-center'>$fecha / $hora</td>
                           <td class='fw-semibold text-center'>$formapago</td>
                           <td class='fw-semibold text-center'><font style='color: $color'><b>$estado</b></font></td>
                           <td class='fw-semibold text-center'>$ " . number_format($totalventa, 2, '.', ',') . "</td>
                           <td class='text-center'>
                              <div class='dropdown'>
                                <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                                <span class='caret'></span></button>
                                <ul class='dropdown-menu dropdown-menu-right'>
                                $exportar
                                <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"imprimirTicket('$tagventa', '$sello');\">Imprimir ticket <span class='text-muted small fas fa-file'></span></a></li>
                                $cancelar
                                </ul>
                              </div>
                           </td>
                       </tr>";
                $finales++;
            }

            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarVentas";

            if ($finales == 0) {
                $datos .= "<tr><td colspan='11'>No se encontraron registros</td></tr>";
            }

            $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='4'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        }
        return $datos;
    }

    function checkPrecio($producto)
    {
        $id_prod = "";
        $cod_prod = "";
        $nom_prod = "";
        $taxes = "";
        $prec_prod = 0;
        $impuesto = 0;

        $div = explode('-', $producto);
        $producto = $div[0];
        $consulta = "SELECT idproser, codproducto, nombre_producto, precio_venta, impuestos_aplicables FROM productos_servicios WHERE codproducto = :prod";
        $val = array("prod" => $producto);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach ($stmt as $row) {
            $id_prod = $row['idproser'];
            $cod_prod = $row['codproducto'];
            $nom_prod = $row['nombre_producto'];
            $prec_prod = $row['precio_venta'];
            $taxes = $row['impuestos_aplicables'];
        }

        $html = '<div class="col-12 col-md">
                     <font style="color: #17177C; font-weight: bold;">PRECIO:</font>
                     <h4 class="text-primary-emphasis" style="margin: 0;">$<span id="SpnPrec">' . $prec_prod . '</span></h4>
                 </div>';
        $total = bcdiv($prec_prod, '1', 2);
        $div_taxes = explode('<tr>', $taxes);
        for ($i = 0; $i < sizeof($div_taxes); $i++) {
            $div = explode("-", $div_taxes[$i]);
            if ($div[0] != "") {
                $percent = $div[0];
                $tipo = $div[1];
                $nom_tax = $this->getNameTax($tipo, $percent);
                $impuesto = bcdiv(($prec_prod * $percent), '1', 2);
                if ($tipo == 1) {
                    $total += bcdiv($impuesto, '1', 2);
                } else if ($tipo == 2) {
                    $total -= bcdiv($impuesto, '1', 2);
                }
                $html .= '<div class="col-12 col-md text-center">
                              <font style="color: #17177C; font-weight: bold;">' . $nom_tax . ':</font>
                              <h4 class="text-primary-emphasis" style="margin: 0;">$<span id="SpnIva">' . $impuesto . '</span></h4>
                          </div>';
            }
        }

        $total .= '<div class="col-md-4">
                     <font style="color: #17177C; font-weight: bold;">TOTAL:</font>
                     <h2 class="text-primary-emphasis" style="margin: 0;">$<span id="SpnTotal">' . $total . '</span></h2>
                 </div>';

        $json = array();
        $json['id_prod']   = $id_prod;
        $json['cod_prod']  = $cod_prod;
        $json['nom_prod']  = $nom_prod;
        $json['html']      = $html;
        $json['total']     = $total;
        return $json;
    }

    public function getNameTax($tipo, $percent)
    {
        $nombre = "";
        $consultas = "SELECT CASE 
                        WHEN impuesto = 1 THEN 'ISR'
                        WHEN impuesto = 2 THEN 'IVA'
                        WHEN impuesto = 3 THEN 'IEPS'
                    END AS impuesto
                    FROM impuesto 
                    WHERE tipoimpuesto = $tipo AND porcentaje = '$percent'";
        $result = $this->consultas->getResults($consultas, null);
        foreach ($result as $rs) {
            $nombre = $rs['impuesto'];
        }
        return $nombre;
    }

    //--------------------------------IMPRIMIR TICKET
    public function getDatosTicket($tag)
    {
        $datos = false;
        $consulta = "SELECT * FROM datos_venta WHERE tagventa=:tag;";
        $val = array("tag" => $tag);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function obtenerLargoTicket($tag)
    {
        $altofila = 0;
        $consulta = "SELECT LENGTH(CONCAT(venta_codprod,' ',venta_producto)) AS strlen FROM detalle_venta WHERE tagdetallev = :tag";
        $val = array("tag" => $tag);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach ($stmt as $row) {
            $n = $row['strlen'];

            if ($n <= 19) {
                $altofila += 8;
            } else if ($n <= 34) {
                $altofila += 13;
            } else {
                $altofila += 18;
            }
        }
        return $altofila;
    }

    public function getDetalleTicket($tag)
    {
        $datos = false;
        $consulta = "SELECT * FROM detalle_venta WHERE tagdetallev=:tag;";
        $val = array("tag" => $tag);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function cancelarTicked($id)
    {
        $cancelado = false;
        $consulta = "UPDATE datos_venta SET status_venta = '0' WHERE (iddatos_venta = :id)";
        $val = array("id" => $id);
        $cancelado = $this->consultas->execute($consulta, $val);
        $this->retornarInventario($id);
        return $cancelado;
    }

    public function retornarInventario($id)
    {
        $tag = "";
        $id_prod = 0;
        $cantidad = 0;
        $bandera_inv = 0;
        $cantidad_inv = 0;
        $consulta = "SELECT tagventa FROM datos_venta WHERE iddatos_venta = :id";
        $val = array("id" => $id);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach ($stmt as $row) {
            $tag = $row['tagventa'];
        }

        $consulta = "SELECT venta_idprod, venta_cant, chinventario, cantinv 
                    FROM detalle_venta AS dv
                    INNER JOIN productos_servicios AS ps ON ps.idproser = dv.venta_idprod
                    WHERE dv.tagdetallev = :tag";

        $val = array("tag" => $tag);
        $stmt = $this->consultas->getResults($consulta, $val);

        foreach ($stmt as $row) {
            $id_prod = $row['venta_idprod'];
            $cantidad = $row['venta_cant'];
            $bandera_inv = $row['chinventario'];
            $cantidad_inv = $row['cantinv'];

            if ($bandera_inv == 1) {

                $cantidad_inv = $cantidad_inv + $cantidad;
                $consulta = "UPDATE productos_servicios SET cantinv = :cantidad_inv WHERE idproser = :id_prod";
                $val = array(
                    "cantidad_inv" => $cantidad_inv,
                    "id_prod" => $id_prod
                );
                $this->consultas->execute($consulta, $val);
            }
        }
    }

    //-------------------------------------CORTE DE CAJA
    public function printCorteCaja($user, $fecha)
    {
        $totventas = 0;
        $totganancia = 0;

        $ventas = $this->getTotalVentas($fecha, $user);
        foreach ($ventas as $actual) {
            $totventas += $actual['totalventa'];
        }

        $ganancias = $this->getGanancias($fecha, $user);
        foreach ($ganancias as $actual) {
            $pcompra = $actual['precio_compra'];
            $cant = $actual['venta_cant'];
            $importe = $actual['venta_importe'];
            $impcompra = floatval($cant) * floatval($pcompra);
            $totganancia += $importe - $impcompra;
        }
        $datos = "$totventas<cut>$totganancia";
        return $datos;
    }

    public function getUserbyID($uid)
    {
        $nombre = "";
        $usuarios = $this->getUserbyIDAux($uid);
        foreach ($usuarios as $actual) {
            $nombre = $actual['nombre'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'];
        }
        return $nombre;
    }

    private function getUserbyIDAux($uid)
    {
        $datos = false;
        $consulta = "SELECT * FROM usuario WHERE idusuario=:uid;";
        $val = array("uid" => $uid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getTotalVentas($fecha, $user = "")
    {
        $datos = false;
        $condicion = "";
        if ($user != '0') {
            $condicion = " AND (uid_venta=:uid)";
        }
        $consulta = "SELECT * FROM datos_venta WHERE (fecha_venta=:fecha)$condicion;";
        $val = array(
            "fecha" => $fecha,
            "uid" => $user
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getGanancias($fecha, $user)
    {
        $datos = false;
        $condicion = "";
        if ($user != '0') {
            $condicion = " AND (uid_venta=:uid)";
        }
        $consulta = "SELECT d.venta_importe, d.venta_cant, p.precio_compra FROM detalle_venta d INNER JOIN productos_servicios p ON (d.venta_idprod=p.idproser) INNER JOIN datos_venta v ON (d.tagdetallev=v.tagventa) WHERE (fecha_venta=:fecha)$condicion;";
        $val = array(
            "fecha" => $fecha,
            "uid" => $user
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getFondoCaja($uid, $fecha)
    {
        $user = "";
        if ($uid != '0') {
            $user = " AND uidfondo=:uid";
        }
        $datos = false;
        $consulta = "SELECT * FROM fondocaja WHERE fechaingreso=:fecha$user;";
        $val = array(
            "fecha" => $fecha,
            "uid" => $uid
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getMovEfectivo($t, $fecha, $uid)
    {
        $user = "";
        $user_tkt = "";
        if ($uid != '0') {
            $user = ' AND uid=:uid';
            $user_tkt = ' AND uid_venta = :uid';
        }
        $datos = false;
        if ($t == 2) {
            $consulta = "SELECT conceptomov, montomov FROM movefectivo WHERE tipomov=:tipo AND fechamov=:fecha$user
                        UNION ALL
                        SELECT CONCAT('Cancelacion ',letra,folio) AS conceptomov, totalventa AS montomov
                        FROM datos_venta
                        WHERE fecha_venta = :fecha AND status_venta = 0$user_tkt";
        } else {
            $consulta = "SELECT conceptomov, montomov FROM movefectivo WHERE tipomov=:tipo AND fechamov=:fecha$user";
        }

        $val = array(
            "tipo" => $t,
            "fecha" => $fecha,
            "uid" => $uid
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getEntradasEfectivo($uid, $fecha)
    {
        $fondo = 0;
        $total = 0;
        $datf = $this->getFondoCaja($uid, $fecha);
        foreach ($datf as $actual) {
            $fondo += $actual['fondo'];
            $total += $actual['fondo'];
        }

        $datos = "<ul class='list-group mb-3'>
                        <li class='list-group-item d-flex justify-content-between lh-sm'>
                            <div>
                            <h6 class='my-0 text-success'><i class='fas fa-arrow-up text-success me-1 small'></i>Dinero en caja</h6>
                            </div>
                            <input type='hidden' name='fondo_inicio' id='fondo_inicio' value=" . number_format($fondo, 2, '.', ',') . ">
                            <span class='text-secondary fw-semibold'>$ " . number_format($fondo, 2, '.', ',') . "</span>
                        </li>";
        $entradas = $this->getMovEfectivo('1', $fecha, $uid);
        foreach ($entradas as $actual) {
            $concepto = iconv("utf-8", "windows-1252", $actual['conceptomov']);
            $monto = number_format($actual['montomov'], 2, '.', ',');
            $total += $actual['montomov'];
            $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
                        <div class='col-8 px-0 text-start'>
                        <h6 class='my-0 text-success'><i class='fas fa-arrow-up text-success me-1 small'></i>
                         $concepto</h6>
                        </div>
                        <span class='text-success fw-semibold'>$ $monto</span>
                    </li>";
        }
        $datos .= "<li class='list-group-item d-flex justify-content-between'>
        <span class='fw-bold text-muted'>Total (MXN)</span>
        <input type='hidden' name='total_entradas' id='total_entradas' value=" . number_format($total, 2, '.', ',') . ">
        <strong>$ " . number_format($total, 2, '.', ',') . "</strong>
      </li></ul>";
        return $datos;
    }

    private function getSalidaEfectivo($uid, $fecha)
    {
        $total = 0;
        $datos = "<ul class='list-group mb-3'>";
        $entradas = $this->getMovEfectivo('2', $fecha, $uid);
        foreach ($entradas as $actual) {
            $concepto = iconv("utf-8", "windows-1252", $actual['conceptomov']);
            $monto = number_format($actual['montomov'], 2, '.', ',');
            $total += $actual['montomov'];
            $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
            <div class='col-8 px-0 text-start'>
            <h6 class='my-0 text-danger'><i class='fas fa-arrow-down text-danger me-1 small'></i> $concepto</h6>
            </div>
            <span class='text-danger fw-semibold'>$ $monto</span>
        </li>";
        }
        $datos .= "<li class='list-group-item d-flex justify-content-between'>
        <span class='fw-bold text-muted'>Total (MXN)</span>
        <input type='hidden' name='total_salidas' id='total_salidas' value=" . number_format($total, 2, '.', ',') . ">
        <strong>$ " . number_format($total, 2, '.', ',') . "</strong>
      </li></ul>";
        return $datos;
    }

    public function getVentasByTipo($fecha, $forma, $uid)
    {
        $user = "";
        if ($uid != '') {
            $user = " AND (uid_venta=:uid)";
        }
        $datos = false;
        $consulta = "SELECT * FROM datos_venta WHERE (fecha_venta=:fecha) AND (formapago=:fp)$user;";
        $val = array(
            "fecha" => $fecha,
            "fp" => $forma,
            "uid" => $uid
        );
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getDineroCaja($uid, $fecha)
    {
        $efectivo = 0;
        $tarjeta = 0;
        $vales = 0;
        $entradas = 0;
        $salidas = 0;
        $total = 0;

        $datf = $this->getFondoCaja($uid, $fecha);
        foreach ($datf as $actual) {
            $total += $actual['fondo'];
            $entradas += $actual['fondo'];
        }

        $datf = $this->getVentasByTipo($fecha, 'cash', $uid);
        foreach ($datf as $actual) {
            $total += $actual['totalventa'];
            $efectivo += $actual['totalventa'];
        }

        $datcd = $this->getVentasByTipo($fecha, 'card', $uid);
        foreach ($datcd as $actual) {
            $total += $actual['totalventa'];
            $tarjeta += $actual['totalventa'];
        }

        $datvl = $this->getVentasByTipo($fecha, 'val', $uid);
        foreach ($datvl as $actual) {
            $total += $actual['totalventa'];
            $vales += $actual['totalventa'];
        }

        $ent = $this->getMovEfectivo('1', $fecha, $uid);
        foreach ($ent as $actual) {
            $entradas += $actual['montomov'];
            $total += $actual['montomov'];
        }

        $out = $this->getMovEfectivo('2', $fecha, $uid);
        foreach ($out as $actual) {
            $salidas += $actual['montomov'];
            $total -= $actual['montomov'];
        }

        $datos = "<ul class='list-group mb-3 h-100'>
        <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div class='col-8 px-0 text-start'>
            <h6 class='my-0 text-success'><i class='fas fa-arrow-up text-success me-1 small'></i>Ventas en efectivo </h6>
            </div>
            <span class='text-success fw-semibold'>$ " . number_format($efectivo, 2, '.', ',') . "</span>
        </li>";

        if ($tarjeta > 0) {
            $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
            <div class='col-8 px-0 text-start'>
            <h6 class='my-0 text-success'><i class='fas fa-arrow-up text-success me-1 small'></i>Ventas en tarjeta </h6>
            </div>
            <span class='text-success fw-semibold'>$ " . number_format($tarjeta, 2, '.', ',') . "</span>
        </li>";
        }

        if ($vales > 0) {
            $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
            <div class='col-8 px-0 text-start'>
            <h6 class='my-0 text-success'><i class='fas fa-arrow-up text-success me-1 small'></i>Ventas en vales </h6>
            </div>
            <span class='text-success fw-semibold'>$ " . number_format($vales, 2, '.', ',') . "</span>
        </li>";
        }

        $datos .= "
            <li class='list-group-item d-flex justify-content-between lh-sm'>
                <div class='col-8 px-0 text-start'>
                <h6 class='my-0 text-success'><i class='fas fa-arrow-up text-success me-1 small'></i>Entradas </h6>
                </div>
                <span class='text-success fw-semibold'>$ " . number_format($entradas, 2, '.', ',') . "</span>
            </li>
            <li class='list-group-item d-flex justify-content-between lh-sm'>
                <div class='col-8 px-0 text-start'>
                <h6 class='my-0 text-danger'><i class='fas fa-arrow-down text-danger me-1 small'></i>Salidas </h6>
                </div>
                <span class='text-danger fw-semibold'>$ " . number_format($salidas, 2, '.', ',') . "</span>
            </li>
            <li class='list-group-item d-flex justify-content-between'>
            <span class='fw-bold text-muted'>Total (MXN)</span>
            <strong>$ " . number_format($total, 2, '.', ',') . "</strong>
            </li></ul>";
        return $datos;
    }

    public function getCorteCaja($user)
    {
        $totventas = 0;
        $totganancia = 0;
        $fecha = date('Y-m-d');

        $ventas = $this->getTotalVentas($fecha, $user);
        foreach ($ventas as $actual) {
            $totventas += $actual['totalventa'];
        }

        $ganancias = $this->getGanancias($fecha, $user);
        foreach ($ganancias as $actual) {
            $pcompra = $actual['precio_compra'];
            $cant = $actual['venta_cant'];
            $importe = $actual['venta_importe'];
            $impcompra = floatval($cant) * floatval($pcompra);
            $totganancia += $importe - $impcompra;
        }
        $entefec = $this->getEntradasEfectivo($user, $fecha);
        $dinerocaja = $this->getDineroCaja($user, $fecha);
        $salidaefectivo = $this->getSalidaEfectivo($user, $fecha);
        $datos = "$totventas<cut>$totganancia<cut>$entefec<cut>$dinerocaja<cut>$salidaefectivo";
        return $datos;
    }

    public function verificarF5()
    {
        $bandera = "";
        $uid = $_SESSION[sha1("idusuario")];
        $consulta = "SELECT crearproducto FROM usuariopermiso WHERE permiso_idusuario = :id_usuario";
        $val = array("id_usuario" => $uid);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach ($stmt as $row) {
            $bandera = $row['crearproducto'];
        }
        return $bandera;
    }

    public function validarSupervisor($usuario, $contrasena)
    {
        $bandera = "";
        $contrasenaencriptada = sha1($contrasena);
        $consulta = "SELECT u.*, p.cortedecaja 
                 FROM usuario u 
                 INNER JOIN usuariopermiso p ON u.idusuario = p.permiso_idusuario 
                 WHERE u.usuario = :usuario AND u.password = :contrasena 
                 LIMIT 1";

        $valores = array(
            "usuario" => $usuario,
            "contrasena" => $contrasenaencriptada
        );

        $resultados = $this->consultas->getResults($consulta, $valores);

        if (empty($resultados)) {
            $bandera = "0Credenciales incorrectas";
        } else {
            foreach ($resultados as $resultado) {
                if ($resultado['cortedecaja'] == 1) {
                    $bandera .= $resultado['cortedecaja'] . "<tr>" . $resultado['idusuario'];
                } else {
                    $bandera = "0No tiene permiso para registrar un corte de caja.";
                }
            }
        }

        return $bandera;
    }

    public function insertarCorte($c)
    {
        $insertado = false;
        $insertado = $this->gestionarCorte($c);
        return $insertado;
    }

    private function gestionarCorte($c)
    {
        $insertado = false;
        $hoy = date('Y-m-d');
        $hora = date('H:i');
        $tag =  $this->genTag();
        $consulta = "INSERT INTO `cortecaja` VALUES(NULL, :fecha_corte, :hora_corte, :total_ventas, :total_entradas, :total_salidas, :fondo_inicio, :usuario_cargo, :comentarios, :total_ganancias, :total_faltantes, :total_sobrantes, :id_supervisor, :tagcorte);";
        $valores = array(
            "fecha_corte" => $hoy,
            "hora_corte" => $hora,
            "total_ventas" => $c->getTotalVentas(),
            "total_entradas" => $c->getTotalentradas(),
            "total_salidas" => $c->getTotalsalidas(),
            "fondo_inicio" => $c->getFondoinicio(),
            "usuario_cargo" => $c->getUsuario(),
            "comentarios" => $c->getComentarios(),
            "total_ganancias" => $c->getTotalganancias(),
            "total_faltantes" => $c->getFaltantes(),
            "total_sobrantes" => $c->getSobrantes(),
            "id_supervisor" => $c->getIdSupervisor(),
            "tagcorte" => $tag,
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        if ($insertado) {
            $this->registrarDetallesCorte($hoy, $hora, $tag);
        }
        return $insertado;
    }

    private function obtenerIdDatosVenta($fecha_corte, $hora_corte)
    {
        $consultado = false;
        $consulta = "SELECT iddatos_venta FROM datos_venta WHERE fecha_venta = :fecha_venta AND TIME(hora_venta) < :hora_corte";
        $valores = array(
            "fecha_venta" => $fecha_corte,
            "hora_corte" => $hora_corte
        );
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function obtenerIdFondoInicio($fecha_corte, $hora_corte)
    {
        $consultado = false;
        $consulta = "SELECT idfondo FROM fondocaja WHERE fechaingreso = :fecha_corte AND TIME(horaingreso) < :hora_corte";
        $valores = array(
            "fecha_corte" => $fecha_corte,
            "hora_corte" => $hora_corte
        );
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function obtenerIdMovEfectivo($fecha_corte, $hora_corte)
    {
        $consultado = false;
        $consulta = "SELECT idmovefectivo FROM movefectivo WHERE fechamov = :fecha_corte AND TIME(horamov) < :hora_corte";
        $valores = array(
            "fecha_corte" => $fecha_corte,
            "hora_corte" => $hora_corte
        );
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function registrarDetallesCorte($fecha_corte, $hora_corte, $tag)
    {
        $idsDatosVenta = $this->obtenerIdDatosVenta($fecha_corte, $hora_corte);
        $idsFondo = $this->obtenerIdFondoInicio($fecha_corte, $hora_corte);
        $idsMovefectivo = $this->obtenerIdMovEfectivo($fecha_corte, $hora_corte);

        $consultaInsert = "INSERT INTO detalle_corte (id_datosventa, idfondo, idmovefectivo, tagcorte)
                       VALUES (:id_datosventa, :idfondo, :idmovefectivo, :tagcorte)";

        foreach ($idsDatosVenta as $idDatosVenta) {
            $valoresInsert = array(
                "id_datosventa" => $idDatosVenta["iddatos_venta"],
                "idfondo" => null,
                "idmovefectivo" => null,
                "tagcorte" => $tag,
            );

            $this->consultas->execute($consultaInsert, $valoresInsert);
        }

        foreach ($idsFondo as $idfondo) {
            $valoresInsert = array(
                "id_datosventa" => null,
                "idfondo" => $idfondo["idfondo"],
                "idmovefectivo" => null,
                "tagcorte" => $tag,
            );

            $this->consultas->execute($consultaInsert, $valoresInsert);
        }

        foreach ($idsMovefectivo as $idmovefectivo) {
            $valoresInsert = array(
                "id_datosventa" => null,
                "idfondo" => null,
                "idmovefectivo" => $idmovefectivo["idmovefectivo"],
                "tagcorte" => $tag,
            );

            $this->consultas->execute($consultaInsert, $valoresInsert);
        }
    }


    private function getCortes($condicion)
    {
        $consultado = false;
        $consulta = "SELECT * FROM cortecaja $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrowsCortesAux($condicion)
    {
        $consultado = false;
        $consulta = "SELECT count(idcorte_caja) numrows FROM cortecaja $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumCortesrows($condicion)
    {
        $numrows = 0;
        $rows = $this->getNumrowsCortesAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    public function listaCortesHistorial($US, $pag, $numreg)
    {
        require_once '../com.sine.common/pagination.php';

        $datos = "<thead class='p-0'>
        <tr class='align-middle'>
        <th class='text-center'>Fecha / Hora </th>
        <th class='text-center'>Usuario </th>
        <th class='text-center'>Supervisor </th>
        <th class='text-center'>Fondo inicio </th>
        <th class='text-center'>Entradas </th>
        <th class='text-center'>Salidas </th>
        <th class='text-center'>Ganancias</th>
        <th class='text-center'>Opción</th>
        </tr>
    </thead>
    <tbody>";

        $condicion = empty($US) ? " ORDER BY idcorte_caja" : "WHERE (fecha_corte LIKE '%$US%') OR (hora_corte LIKE '%$US%') ORDER BY idcorte_caja";

        $numrows = $this->getNumCortesrows($condicion);
        $page = isset($pag) && !empty($pag) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $cortes = $this->getCortes($con);

        $inicios = $offset + 1;
        $finales = $inicios + count($cortes) - 1;
        if (empty($cortes)) {
            $datos .= "<tr><td colspan='8'>No se encontraron registros.</td></tr>";
        } else {
            foreach ($cortes as $corte) {
                $fecha = $corte['fecha_corte'];
                $hora = $corte['hora_corte'];
                $usuario = $corte['usuario_cargo'];
                $supervisor = $corte['id_supervisor'];
                $fondoinicio = $corte['fondo_inicio'];
                $entradas = $corte['total_entradas'];
                $salidas = $corte['total_salidas'];
                $ganancias = $corte['total_ganancias'];
                $tag = $corte['tag_corte'];
                $nombreUsuario = $this->getUserbyID($usuario);
                $nombreSupervisor = $this->getUserbyID($supervisor);
                $horaFormateada = date('H:i', strtotime($hora));
                $datos .= "<tr>
                <td class='text-center'> " . date('d/m/Y', strtotime($fecha)) . " a " . $horaFormateada . " hrs</td>
                <td class='text-center'>$nombreUsuario</td>
                <td class='text-center'>$nombreSupervisor</td>
                <td class='text-center'>$fondoinicio</td>
                <td class='text-center text-secondary fw-semibold'><i class='fas fa-arrow-up text-success me-1 small'></i> $entradas</td>
                <td class='text-center text-danger fw-semibold'> <i class='fas fa-arrow-down text-danger me-1 small'></i> $salidas</td>
                <td class='text-center'>$ganancias</td>
                <td class='text-center'>
                    <div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                            <span class='fas fa-ellipsis-v text-muted'></span>
                            <span class='caret'></span>
                        </button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                            <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='imprimirCorteCaja(\"$usuario\", \"$fecha\", \"$horaFormateada\", \"$tag\")'>Imprimir corte <span class='text-muted fas fa-edit small'></span></a></li>";
                $datos .= "</ul>
                    </div>
                </td> 
            </tr>";
            }
        }

        $function = "buscarCorte";
        $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='5'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

        return $datos;
    }

    private function obtenerIdVentaByTag($tag)
    {
        $consultado = false;
        $consulta = "SELECT id_datosventa FROM detalle_corte WHERE tagcorte = :tag AND id_datosventa IS NOT NULL;";
        $valores = array(
            "tag" => $tag
        );
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function obtenerTagsVentas($tag)
    {
        $idsDatosVenta = $this->obtenerIdVentaByTag($tag);
        $tagsVenta = array();
        $consulta = "SELECT tagventa FROM datos_venta WHERE iddatos_venta = :iddatos_venta";
        foreach ($idsDatosVenta as $idactual) {
            $valores = array(
                "iddatos_venta" => $idactual["id_datosventa"]
            );
            $resultado = $this->consultas->getResults($consulta, $valores);

            foreach ($resultado as $resultado) {
                $tagsVenta[] = $resultado['tagventa'];
            }
        }
        return $tagsVenta;
    }

    public function obtenerDetallesProductosVendidos($tag)
    {
        $tagsVenta = $this->obtenerTagsVentas($tag);
        $detallesProductos = array();

        $consulta = "SELECT venta_codprod, venta_producto, venta_cant, venta_precio FROM detalle_venta WHERE tagdetallev= :tagventa";

        foreach ($tagsVenta as $tagVenta) {
            $valores = array(
                "tagventa" => $tagVenta
            );
            $resultadosDetalles = $this->consultas->getResults($consulta, $valores);

            foreach ($resultadosDetalles as $resultado) {
                $codproducto = $resultado['venta_codprod'];
                $nombreProducto = $resultado['venta_producto'];
                $cantidad = $resultado['venta_cant'];
                $precioUnitario = $resultado['venta_precio'];
                $detallesProductos[] = array($codproducto, $nombreProducto, $cantidad, $precioUnitario);
            }
        }

        return $detallesProductos;
    }

    private function obtenerTagsVentasCancelados($tag)
    {
        $idsDatosVenta = $this->obtenerIdVentaByTag($tag);
        $tagsVenta = array();
        $consulta = "SELECT tagventa FROM datos_venta WHERE iddatos_venta = :iddatos_venta AND status_venta = 0;";
        foreach ($idsDatosVenta as $idactual) {
            $valores = array(
                "iddatos_venta" => $idactual["id_datosventa"]
            );
            $resultado = $this->consultas->getResults($consulta, $valores);

            foreach ($resultado as $resultado) {
                $tagsVenta[] = $resultado['tagventa'];
            }
        }
        return $tagsVenta;
    }

    public function obtenerDetallesProductosCancelados($tag)
    {
        $tagsVenta = $this->obtenerTagsVentasCancelados($tag);
        $detallesProductos = array();

        $consulta = "SELECT venta_codprod, venta_producto, venta_cant, venta_precio FROM detalle_venta WHERE tagdetallev= :tagventa";

        foreach ($tagsVenta as $tagVenta) {
            $valores = array(
                "tagventa" => $tagVenta
            );
            $resultadosDetalles = $this->consultas->getResults($consulta, $valores);

            foreach ($resultadosDetalles as $resultado) {
                $codproducto = $resultado['venta_codprod'];
                $nombreProducto = $resultado['venta_producto'];
                $cantidad = $resultado['venta_cant'];
                $precioUnitario = $resultado['venta_precio'];
                $detallesProductos[] = array($codproducto, $nombreProducto, $cantidad, $precioUnitario);
            }
        }

        return $detallesProductos;
    }
}
