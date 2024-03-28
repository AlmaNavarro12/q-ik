<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Reportes.php';
require_once '../com.sine.modelo/TMP.php';

class ControladorReportes {

    function __construct() {
        
    }

    public function translateMonth($m) {
        switch ($m) {
            case '01':
                $mes = 'Ene';
                break;
            case '02':
                $mes = 'Feb';
                break;
            case '03':
                $mes = "Mar";
                break;
            case '04':
                $mes = 'Abr';
                break;
            case '05':
                $mes = 'May';
                break;
            case '06':
                $mes = 'Jun';
                break;
            case '07':
                $mes = 'Jul';
                break;
            case '08':
                $mes = 'Ago';
                break;
            case '09':
                $mes = 'Sep';
                break;
            case '10':
                $mes = 'Oct';
                break;
            case '11':
                $mes = 'Nov';
                break;
            case '12':
                $mes = 'Dic';
                break;
            default :
                $mes = "";
                break;
        }
        return $mes;
    }

    private function getPagosAux($fid, $type) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT d.*, p.pago_idmoneda, p.pago_tcambio, m.c_moneda FROM detallepago d inner join pagos p on (d.foliopago=p.idpago) inner join catalogo_moneda m on (p.pago_idmoneda=m.idcatalogo_moneda) where d.pago_idfactura=:fid and type=:t and p.cancelado='1'";
        $val = array("fid" => $fid,
            "t" => $type);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function restanteParcial($fid, $factotal, $type) {
        $monto = 0;
        $datos = $this->getPagosAux($fid, $type);
        foreach ($datos as $actual) {
            $monto += $actual['monto'];
        }
        $total = $factotal - $monto;
        return $total;
    }

    public function getReporteCarta($f) {
        $fechainicio = $f->getFechainicio();
        $fechafin = $f->getFechafin();
        $iddatos = $f->getDatos();
        $idcliente = $f->getIdcliente();
        $estado = $f->getEstado();
        $tipo = $f->getTipo();
        //$idformapago =$f->getFormapago();

        $datos = "";
        $cliente = "";
        $status = "";
        $tipofactura = "";

        if ($iddatos != "") {
            $datos = " AND iddatosfacturacion = :iddatos";
        }

        if ($idcliente != "") {
            $cliente = "AND (idcliente = :idcliente)";
        }

        if ($estado != "") {
            $parcial = ")";
            if ($estado == '2') {
                $parcial = " OR status_pago = '4')";
            }
            $status = "AND (status_pago = :status$parcial";
        }
        //nuevo
       /*
        if ($idformapago != "") {
            $formapago = "AND id_forma_pago = :formapago";
        } else {
            $formapago = "";
        }
        */
        $consultado = false;
        //$consulta = "SELECT dat.*, m.c_moneda FROM factura_carta dat INNER JOIN catalogo_moneda m ON (dat.id_moneda=m.idcatalogo_moneda) WHERE (fecha_creacion BETWEEN :finicio and :ffin) $datos $cliente $status $tipofactura ORDER BY idfactura_carta DESC";
        //nueva
        $consulta = "SELECT dat.* FROM factura_carta dat WHERE (fecha_creacion BETWEEN :finicio AND :ffin) $datos $cliente $status $tipofactura ORDER BY idfactura_carta DESC";
        /*$consulta ="SELECT 
            p.*,
            dp.nombremoneda,
            cp.nombre_moneda AS nombremoneda_complemento,
            cp.nombre_forma_pago
        FROM 
            factura_carta p
        INNER JOIN 
            detallepago dp ON p.idfactura_carta = dp.pago_idfactura
        INNER JOIN 
            complemento_pago cp ON dp.tagpago = cp.tagcomplemento
        WHERE 
            (p.fecha_creacion BETWEEN :finicio AND :ffin) $datos $cliente $status $tipofactura $formapago
        ORDER BY 
            p.idfactura_carta DESC;
        ";
        */


        $val = array("finicio" => $fechainicio,
            "ffin" => $fechafin,
            "iddatos" => $iddatos,
            "idcliente" => $idcliente,
            "status" => $estado,
            "tipo" => $tipo);
            //"formapago" => $idformapago );
            
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getReporteFactura($f) {
        $fechainicio = $f->getFechainicio();
        $fechafin = $f->getFechafin();
        $iddatos = $f->getDatos();
        $idcliente = $f->getIdcliente();
        $estado = $f->getEstado();
        $tipo = $f->getTipo();
        $idmetodopago =$f->getMetodopago();
        $idformapago =$f->getFormapago();
        $datos = "";
        $cliente = "";
        $status = "";
        $tipofactura = "";
        $formapago="";
        
        

        if ($iddatos != "") {
            $datos = " and iddatosfacturacion = :iddatos";
        }

        if ($idcliente != "") {
            $cliente = "and (idcliente = :idcliente)";
        }

        if ($estado != "") {
            $parcial = ")";
            if ($estado == '2') {
                $parcial = " or status_pago = '4')";
            }
            $status = "and (status_pago = :status$parcial";
        }
        //nuevo
        if ($idmetodopago != "") {
            $metodopago = "AND id_metodo_pago = :metodopago";
        } else {
            $metodopago = "";
        }
    
        if ($idformapago != "") {
            $formapago = "AND id_forma_pago = :formapago";
        } else {
            $formapago = "";
        }

        $consultado = false;
        //vieja
        //$consulta = "SELECT dat.*, m.c_moneda FROM datos_factura dat INNER JOIN catalogo_moneda m ON (dat.id_moneda=m.idcatalogo_moneda) WHERE (fecha_creacion BETWEEN :finicio AND :ffin) $datos $cliente $status $tipofactura ORDER BY iddatos_factura DESC";
        //seminueva.
        //$consulta = "SELECT dat.* FROM datos_factura dat WHERE (fecha_creacion BETWEEN :finicio AND :ffin) $datos $cliente $status $tipofactura ORDER BY iddatos_factura DESC";
        //$consulta = "SELECT dat.* FROM datos_factura dat WHERE (fecha_creacion BETWEEN :finicio AND :ffin) $datos $cliente $status $tipofactura $metodopago $formapago ORDER BY iddatos_factura DESC";
          $consulta = "SELECT dat.* FROM datos_factura dat WHERE (fecha_creacion BETWEEN :finicio AND :ffin) $datos $cliente $status $tipofactura $metodopago $formapago ORDER BY iddatos_factura DESC";


        $val = array("finicio" => $fechainicio,
            "ffin" => $fechafin,
            "iddatos" => $iddatos,
            "idcliente" => $idcliente,
            "status" => $estado,
            "tipo" => $tipo,
            "metodopago" => $idmetodopago,
            "formapago" => $idformapago );
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function buscarFactura($f) {
        $datos = "<thead>
            <tr class='align-middle'>
                <th class='text-center col-md-2'>No. Folio </th>
                <th class='text-center col-md-3'>Folio Fiscal </th>
                <th class='text-center col-md-2'>Fecha </th>
                <th class='text-center col-md-3'>Emisor</th>
                <th class='text-center col-md-3'>Cliente</th>
                <th class='text-center col-md-3'>Estado </th>
                <th class='text-center col-md-3'>Método de pago</th>
                <th class='text-center col-md-3'>Forma de pago</th>
                <th class='text-center col-md-3'>Total ($)</th>

            </tr>
        </thead>
        <tbody>";
        $idmoneda = 1;
        $cmoneda = 1;
        if ($f->getMoneda() != "") {
            $idmoneda = $f->getMoneda();
        }
        
        $subtotalperiodo = 0;
        $ivaperiodo = 0;
        $retperiodo = 0;
        $descperiodo = 0;
        $totalperiodo = 0;
        if ($f->getTipo() == "" || $f->getTipo() == '1' || $f->getTipo() == '3') {
            $resultados = $this->getReporteFactura($f);
            foreach ($resultados as $reporteactual) {
                $idfactura = $reporteactual['iddatos_factura'];
                $folio = $reporteactual['letra'] . $reporteactual['folio_interno_fac'];
                $fecha = $reporteactual['fecha_creacion'];
                $emisor = $reporteactual['factura_rzsocial'];
                $nombre_cliente = $reporteactual['rzreceptor'];
                $estado = $reporteactual['status_pago'];
                $subtotal = $reporteactual['subtotal'];
                $iva = $reporteactual['subtotaliva'];
                $ret = $reporteactual['subtotalret'];
                $totaldescuentos = $reporteactual['totaldescuentos'];
                $total = $reporteactual['totalfactura'];
                $tcambio = $reporteactual['tcambio'];
                $monedaF = $reporteactual['id_moneda'];
                $divm = explode('-', $reporteactual['moneda']);
                $cmoneda = $divm[0];
                $uuid = $reporteactual['uuid'];
                $metodopago = substr($reporteactual['metodo_pago'], 4);
                $formapago = substr($reporteactual['forma_pago'], 3);

                $estadoF = "";
                $color = "";
                $divideF = explode("-", $fecha);

                $mes = $this->translateMonth($divideF[1]);
                $fecha = $divideF[2] . ' / ' . $mes;

                if ($estado == "1") {
                    $estadoF = "Pagada";
                    $color = "#34A853";
                    $title = "Factura pagada";
                } else if ($estado == "2") {
                    $estadoF = "Pendiente";
                    $color = "#ED495C";
                    $title = "Pago de factura aún pendiente";
                } else if ($estado == "3") {
                    $estadoF = "Cancelada";
                    $color = "#FBBC05";
                    $title = "Factura cancelada";
                } else if ($estado == "4") {
                    $estadoF = "Pago parcial (Restante)";
                    $color = "#02E7EF";
                    $title = "Factura pagada parcialmente";
                    $total = $this->restanteParcial($idfactura, $total, 'f');
                }

                if($metodopago =="1"){
                    $estadoF = "Pago en una sola exhibición";
                    $title = "PUE-Pago en una sola exhibición";

                }else if( $metodopago == "2"){
                    $estadoF = "Pago en parcialidades o diferido";
                    $title = "PPD-Pago en parcialidades o diferido";
                
                }

                $diviva = explode("<impuesto>", $iva);
                foreach ($diviva as $ivan) {
                    $traslados = $ivan;
                    $divt = explode("-", $traslados);
                    $ivaperiodo += $this->totalDivisa($divt[0], $tcambio, $idmoneda, $monedaF);
                }

                $divret = explode("<impuesto>", $ret);
                foreach ($divret as $retn) {
                    $retenciones = $retn;
                    $divr = explode("-", $retenciones);
                    $retperiodo += $this->totalDivisa($divr[0], $tcambio, $idmoneda, $monedaF);
                }

                $descperiodo += $this->totalDivisa($totaldescuentos, $tcambio, $idmoneda, $monedaF);
                $subtotalperiodo += $this->totalDivisa($subtotal, $tcambio, $idmoneda, $monedaF);
                $totalperiodo += $this->totalDivisa($total, $tcambio, $idmoneda, $monedaF);

                $datos .= "
                    <tr>
                        <td class='text-center'><a href='#' title='Ver factura' class='text-decoration-none' style='cursor:pointer;' onclick=\"imprimirFactura($idfactura); return false;\">$folio <span class='fas fa-file'></span></a></td>
                        <td class='text-center'>$uuid</td>
                        <td class='text-center'>$fecha</td>
                        <td class='text-center'>$emisor</td>
                        <td class='text-center'>$nombre_cliente</td>
                        
                        <td class='text-center fw-semibold'><a class='state-link' style='color: $color;' title='$title'><span>$estadoF</span></a></td>
                        <td class='text-center'>$metodopago</td>
                        <td class='text-center'>$formapago</td>

                        <td class='text-center'> $". number_format($total, 2, '.', ',') . "$cmoneda </td>
                    </tr>
                     ";
               
            }
        }

        if ($f->getTipo() == '2' || $f->getTipo() == '3') {
            $resultados = $this->getReporteCarta($f);
            foreach ($resultados as $reporteactual) {
                $idfactura = $reporteactual['idfactura_carta'];
                $folio = $reporteactual['letra'] . $reporteactual['foliocarta'];
                $fecha = $reporteactual['fecha_creacion'];
                $emisor = $reporteactual['factura_rzsocial'];
                $nombre_cliente = $reporteactual['rzreceptor'];
                $estado = $reporteactual['status_pago'];
                $subtotal = $reporteactual['subtotal'];
                $iva = $reporteactual['subtotaliva'];
                $ret = $reporteactual['subtotalret'];
                $totaldescuentos = $reporteactual['totaldescuentos'];
                $total = $reporteactual['totalfactura'];
                $tcambio = $reporteactual['tcambio'];
                $monedaF = $reporteactual['id_moneda'];
                $divm = explode('-', $reporteactual['nombre_moneda']);
                $cmoneda = $divm[0];
                $uuid = $reporteactual['uuid'];
                //n
                $formapago = $reporteactual['nombre_forma_pago'];


                $estadoF = "";
                $color = "";
                $divideF = explode("-", $fecha);

                $mes = $this->translateMonth($divideF[1]);
                $fecha = $divideF[2] . ' - ' . $mes;

                if ($estado == "1") {
                    $estadoF = "Pagada";
                    $color = "#34A853";
                    $title = "Factura Pagada";
                } else if ($estado == "2") {
                    $estadoF = "Pendiente";
                    $color = "#ED495C";
                    $title = "Pago de factura aun pendiente";
                } else if ($estado == "3") {
                    $estadoF = "Cancelada";
                    $color = "#FBBC05";
                    $title = "Factura Cancelada";
                } else if ($estado == "4") {
                    $estadoF = "Pago Parcial (Restante)";
                    $color = "#02E7EF";
                    $title = "Factura pagada parcialmente";
                    $total = $this->restanteParcial($idfactura, $total, 'c');
                }

                $diviva = explode("<impuesto>", $iva);
                foreach ($diviva as $ivan) {
                    $traslados = $ivan;
                    $divt = explode("-", $traslados);
                    $ivaperiodo += $this->totalDivisa($divt[0], $tcambio, $idmoneda, $monedaF);
                }

                $divret = explode("<impuesto>", $ret);
                foreach ($divret as $retn) {
                    $retenciones = $retn;
                    $divr = explode("-", $retenciones);
                    $retperiodo += $this->totalDivisa($divr[0], $tcambio, $idmoneda, $monedaF);
                }

                $descperiodo += $this->totalDivisa($totaldescuentos, $tcambio, $idmoneda, $monedaF);
                $subtotalperiodo += $this->totalDivisa($subtotal, $tcambio, $idmoneda, $monedaF);
                $totalperiodo += $this->totalDivisa($total, $tcambio, $idmoneda, $monedaF);

                $datos .= "
                    <tr class='align-middle'>
                        <td class='text-center'><a href='#' title='Ver factura' onclick=\"imprimirCarta($idfactura); return false;\">$folio <span class='glyphicon glyphicon-file'></span></a></td>
                        <td class='text-center'>$uuid</td>
                        <td class='text-center'>$fecha</td>
                        <td class='text-center'>$emisor</td>
                        <td class='text-center'>$nombre_cliente</td>
                        <td class='text-center'><a class='state-link' style='color: $color;' title='$title'><span>$estadoF</span></a></td>
                        <td class='text-center'>$ " . number_format($total, 2, '.', ',') . " $cmoneda</td>
                    </tr>
                     ";
            }
        }

        $datos .= "</tbody><tfoot>"
                . "<tr><th colspan='7'></th><th class='text-end'><b>Subtotal</b></td><th><b class='text-end'>$ " . number_format($subtotalperiodo, 2, '.', ',') . "</b></th></tr>";

        if ($ivaperiodo > 0) {
            $datos .= "<tr><th colspan='7'></th><th class='text-end'><b>Traslados</b></td><th><b class='text-end'>$ " . number_format($ivaperiodo, 2, '.', ',') . "</b></th></tr>";
        }

        if ($retperiodo > 0) {
            $datos .= "<tr><th colspan='7'></th><th class='text-end'><b>Retenciones</b></td><th><b class='text-end'>$ " . number_format($retperiodo, 2, '.', ',') . "</b></th></tr>";
        }

        if ($descperiodo > 0) {
            $datos .= "<tr><th colspan='7'></th><th class='text-end'><b>Descuentos</b></td><th><b class='text-end'>$ " . number_format($descperiodo, 2, '.', ',') . "</b></th></tr>";
        }

        $datos .= "<tr><th colspan='7'></th><th class='text-end' ><b>Total </b></th><th><b class='text-end'>$ " . number_format($totalperiodo, 2, '.', ',') . "</b></th></tr></tfoot>";
        return $datos;
    }

    public function getReporteVentas($f) {
        $datos = "";
        $cliente = "";
        $estado = "";
        $usuario = "";

        if ($f->getDatos() != "") {
            $datos = " AND (c.iddatosfacturacion = :iddatos)";
        }

        if ($f->getIdcliente() != "") {
            $cliente = "AND (f.idcliente = :idcliente)";
        }

        if ($f->getEstado() != "") {
            $estado = "AND status_pago = :status";
        }

        if ($f->getUsuario() != "") {
            if ($f->getUsuario() == '0') {
                $usuario = "AND u.tipo = '2'";
            } else {
                $usuario = "AND c.iddocumento=:iddoc";
            }
        }

        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT c.*, concat(f.letra,folio_interno_fac) foliofactura, f.totalfactura, cl.razon_social FROM datos_cotizacion c INNER JOIN datos_factura f ON (c.expfactura=f.iddatos_factura) INNER JOIN cliente cl ON (cl.id_cliente=f.idcliente) INNER JOIN usuario u ON (u.idusuario=c.iddocumento) WHERE (c.fecha_creacion BETWEEN :dinicio AND :dfin) $datos $cliente $usuario $estado";
        $val = array("dinicio" => $f->getFechainicio(),
            "dfin" => $f->getFechafin(),
            "iddatos" => $f->getDatos(),
            "idcliente" => $f->getIdcliente(),
            "iddoc" => $f->getUsuario(),
            "status" => $f->getEstado());
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function buscarVentas($f) {
        $datos = "<thead>
                    <tr>
                        <th>Folio Cotizacion </th>
                        <th>Fecha de Creacion </th>
                        <th>Realizo </th>
                        <th>Total Cotizacion </th>
                        <th>Folio Factura </th>
                        <th>Cliente </th>
                        <th>Total Factura </th>
                    </tr>
                  </thead>
                  <tbody>";

        $resultados = $this->getReporteVentas($f);
        foreach ($resultados as $reporteactual) {
            $idcotizacion = $reporteactual['iddatos_cotizacion'];
            $folio = $reporteactual['letra'] . $reporteactual['foliocotizacion'];
            $fecha = $reporteactual['fecha_creacion'];
            $realizo = $reporteactual['documento'];
            $totalcot = $reporteactual['totalcotizacion'];
            $idfactura = $reporteactual['expfactura'];
            $foliofactura = $reporteactual['foliofactura'];
            $cliente = $reporteactual['razon_social'];
            $totalfactura = $reporteactual['totalfactura'];
            $divideF = explode("-", $fecha);
            $fecha = $divideF[2] . '/' . $divideF[1] . '/' . $divideF[0];

            $datos .= "
                    <tr>
                        <td><a href='#' title='Ver cotizacion' onclick=\"imprimirCotizacion($idcotizacion); return false;\">$folio <span class='glyphicon glyphicon-file'></span></a></td>
                        <td>$fecha</td>
                        <td>$realizo</td>
                        <td>$ " . number_format($totalcot, 2, '.', ',') . "</td>
                        <td><a href='#' title='Ver factura' onclick=\"imprimirFactura($idfactura,'$foliofactura'); return false;\">$foliofactura <span class='glyphicon glyphicon-file'></span></a></td>
                        <td>$cliente</td>
                        <td>$ " . number_format($totalfactura, 2, '.', ',') . "</td>
                    </tr>";
        }

        $datos .= "</tbody><tfoot><tr><th colspan='7' align='center'><b>Ventas Individuales</b></th></tr>";
        $datos .= $this->ventasIndividuales($f);
        return $datos;
    }

    public function getUsuariosVentas($f) {
        $usuario = "";
        if ($f->getUsuario() != "") {
            if ($f->getUsuario() == '0') {
                $usuario = "and u.tipo = '2'";
            } else {
                $usuario = "and u.idusuario=:iddoc";
            }
        }
        $consultado = false;
        $consulta = "SELECT * FROM usuario u inner join comisionusuario c on (u.idusuario=c.comision_idusuario) $usuario order by nombre;";
        $val = array("iddoc" => $f->getUsuario());
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function ventasIndividuales($f) {
        $datos = "";
        $usuarios = $this->getUsuariosVentas($f);
        foreach ($usuarios as $actual) {
            $idusuario = $actual['idusuario'];
            $nombre = $actual['nombre'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'];
            $comision = $actual['comisionporcentaje'];
            if ($actual['calculo'] == '1') {
                $calculo = "antes de impuestos";
            } else if ($actual['calculo'] == '2') {
                $calculo = "despues de impuestos";
            }
            $total = $this->getTotalVentas($f, $idusuario, $actual['calculo']);
            $totalcom = $total * ($comision / 100);
            if ($totalcom > 0) {
                $datos .= "<tr><th colspan='4' align='right' ><b>$nombre</b></th><th>Comision: $comision% $calculo</th><th>Total Ventas: $ " . number_format($total, 2, '.', ',') . "</th><th><b>Comision: $ " . number_format($totalcom, 2, '.', ',') . "</b></th></tr></tbody>";
            }
        }
        return $datos;
    }

    public function getTotalVentas($f, $idusuario, $calculo) {
        $total = "";
        $get = $this->getTotalVentasAux($f, $idusuario, $calculo);
        foreach ($get as $actual) {
            $total = $actual['total'];
        }
        return $total;
    }

    private function getTotalVentasAux($f, $idusuario, $calculo) {
        $datos = "";
        $cliente = "";
        $estado = "";
        $total = "";

        if ($f->getDatos() != "") {
            $datos = " and (c.iddatosfacturacion = :iddatos)";
        }

        if ($f->getIdcliente() != "") {
            $cliente = "and (f.idcliente = :idcliente)";
        }
        if ($f->getEstado() != "") {
            $estado = "and status_pago = :status";
        }
        if ($calculo == '1') {
            $total = "c.subtot";
        } else if ($calculo == '2') {
            $total = "c.totalcotizacion";
        }
        $consultado = false;
        $consulta = "SELECT sum($total) total FROM datos_cotizacion c INNER JOIN datos_factura f ON (c.expfactura=f.iddatos_factura) INNER JOIN cliente cl ON (cl.id_cliente=f.idcliente) INNER JOIN usuario u ON (u.idusuario=c.iddocumento) WHERE (c.fecha_creacion BETWEEN :dinicio and :dfin) AND c.iddocumento=:iddoc $datos $cliente $estado";
        $val = array("dinicio" => $f->getFechainicio(),
            "dfin" => $f->getFechafin(),
            "iddatos" => $f->getDatos(),
            "idcliente" => $f->getIdcliente(),
            "iddoc" => $idusuario,
            "status" => $f->getEstado());
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getCfdisAux($num) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT p.pago_idfactura, p.type, p.foliodoc, pa.letra, pa.foliopago
        FROM detallepago p
        INNER JOIN pagos pa ON p.detalle_tagencabezado = pa.tagpago
        WHERE pa.idpago = :folio";
        $val = array("folio" => $num);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getFolioFacturaAux($id, $type) {
        $consultado = false;
        $con = new Consultas();
        if ($type == 'f') {
            $consulta = "SELECT iddatos_factura fid, concat(letra, folio_interno_fac) folio FROM datos_factura WHERE iddatos_factura=:id;";
        } else if ($type == 'c') {
            $consulta = "SELECT idfactura_carta fid, concat(letra, foliocarta) folio FROM factura_carta WHERE idfactura_carta=:id;";
        }
        $val = array("id" => $id);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    private function getFolioFactura($id, $type) {
        $datos = "";
        $facturas = $this->getFolioFacturaAux($id, $type);
        foreach ($facturas as $actual) {
            $fid = $actual['fid'];
            $folio = $actual['folio'];
            $datos = "$fid</tr>$folio";
        }
        return $datos;
    }

    public function getCfdis($num) {
        $contador = 0;
        $consulta = "";
        $datos = $this->getCfdisAux($num);
        foreach ($datos as $actual) {
            $idfactura = $actual['pago_idfactura'];
            $type = $actual['type'];
            $foliofactura = $actual['foliodoc'];

            if ($type == "f") {
                $function = "class='text-decoration-none' style='cursor: pointer;' onclick=\"imprimirFactura($idfactura); return false;\"";
            } else if ($type == 'c') {
                $function = "class='text-decoration-none' style='cursor: pointer;' onclick=\"imprimirCarta($idfactura); return false;\"";
            }

            if ($contador >= 1) {
                $consulta .= " - <a href='#' class='text-decoration-none' style='cursor: pointer;' title='Ver factura' $function>$foliofactura <span class='fas fa-file'></span></a>";
            } else {
                $consulta .= "<a href='#' class='text-decoration-none' style='cursor: pointer;' title='Ver factura' $function>$foliofactura <span class='fas fa-file'></span></a>";
            }
            $contador++;
        }
        return $consulta;
    }

    public function getCfdisPDF($num) {
        $contador = 0;
        $consulta = "";
        $datos = $this->getCfdisAux($num);
        foreach ($datos as $actual) {
            $foliofactura = $actual['letra'] . $actual['foliopago'];
            if ($contador >= 1) {
                $consulta .= " - $foliofactura";
            } else {
                $consulta .= "$foliofactura";
            }
            $contador++;
        }
        return $consulta;
    }

    public function getReportePagos($f) {
        $datos = "";
        $cliente = "";
        $formapago = "";
        $moneda = "";
        if ($f->getDatos() != "") {
            $datos = " AND pago_idfiscales = :iddatos";
        }
        if ($f->getIdcliente() != "") {
            $cliente = "AND (pago_idcliente = :idcliente)";
        }
        if ($f->getMoneda() != "") {
            $moneda = "AND idmonedadoc = :idmoneda";
        }
        if ($f->getFormapago() != "") {
            $formapago = "AND complemento_idformapago = :formapago";
        }
        $consultado = false;
        $consulta="SELECT 
        p.*,
        c.razon_social AS cliente,
        c.rfc,
        df.razon_social AS emisor,
        dp.tcambiodoc AS tcambiodoc,
        dp.idmonedadoc AS idmonedadoc,
        dp.nombre_moneda AS nombre_moneda,
        cmp.nombre_forma_pago AS nombre_forma_pago
        FROM 
            pagos p
        INNER JOIN 
            cliente c ON p.pago_idcliente = c.id_cliente
        INNER JOIN 
            datos_facturacion df ON p.pago_idfiscales = df.id_datos
        INNER JOIN 
            detallepago dp ON p.idpago = dp.pago_idfactura
        INNER JOIN 
            complemento_pago cmp ON p.tagpago = cmp.tagpago
        WHERE 
            (p.fechacreacion BETWEEN :dinicio AND :dfin) $cliente $datos $moneda $formapago
        ORDER BY 
            p.idpago DESC;
        ";


        $val = array("dinicio" => $f->getFechainicio(),
            "dfin" => $f->getFechafin(),
            "iddatos" => $f->getDatos(),
            "idcliente" => $f->getIdcliente(),
            "idmoneda" => $f->getMoneda(),
            "formapago" => $f->getFormapago());
        
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }
    
    public function buscarPagos($f) {
        $datos = "<thead>
            <tr class='align-middle'>
                <th class='text-center col-md-2'>No. Folio</th>
                <th class='text-center col-md-2'>Folio Fiscal</th>
                <th class='col-md-2 text-center'>Facturas Pagadas</th>
                <th class='text-center col-md-2'>Emisor</th>
                <th class='text-center col-md-2'>Cliente</th>
                <th class='text-center col-md-2'>Forma de pago</th>
                <th class='text-center col-md-2'>Fecha de pago</th>
                <th class='text-center col-md-2'>Total Pagado</th>
            </tr>
        </thead>
        <tbody>";

        $totalpagado = 0;
        $foot = "";
        $idmoneda = 1;
        if ($f->getMoneda() != "") {
            $idmoneda = $f->getMoneda();
        }
        $resultados = $this->getReportePagos($f);
        if (empty($resultados)) {
            $datos .= "<tr><td colspan='8'>No se encontraron registros.</td></tr>";
        } else {
        foreach ($resultados as $reporteactual) {
            $idpago = $reporteactual['idpago'];
            $folio = $reporteactual['letra'] . $reporteactual['foliopago'];
            $fechapago = $reporteactual['fechacreacion'];
            $horapago = $reporteactual['hora_creacion'];
            $horapago = date('g:i a', strtotime($horapago));
            $emisor = $reporteactual['emisor'];
            $nombre_cliente = $reporteactual['cliente'];
            $total = $reporteactual['totalpagado'];
            $tcambio = $reporteactual['tcambiodoc'];
            $monedaF = $reporteactual['idmonedadoc'];
            $divm = explode('-', $reporteactual['nombre_moneda']);
            $cmoneda = $divm[0];
            $uuid = $reporteactual['uuidpago'];
            //nuevo
            $formapago = $reporteactual['nombre_forma_pago'];


            $divFP = explode("-", $fechapago);
            $mes = $this->translateMonth($divFP[1]);
            $fechapago = $divFP[2] . '-' . $mes;
            $totalpagado += $this->totalDivisa($total, $tcambio, $idmoneda, $monedaF);
            $cfdis = $this->getCfdis($idpago);
            $datos .= "
                    <tr class='align-middle'> 
                        <td class='text-center'><a href='#' title='Ver pago' class='text-decoration-none' style='cursor: pointer;' onclick=\"imprimirPago($idpago); return false;\">$folio <span class='fas fa-file'></span></a></td>
                        <td class='text-center'>$uuid</td>
                        <td class='text-center'>$cfdis</td>
                        <td class='text-center'>$emisor</td>
                        <td class='text-center'>$nombre_cliente</td>
                        <td class='text-center'>$formapago</td>
                        <td class='text-center text-wrap'>$fechapago / ".date('h:i A', strtotime($horapago))."</td>
                        <td class='text-center'>$" . number_format($total, 2, '.', ',') . "$cmoneda</td>
                    </tr>
                     ";
        }
        $foot = "</tbody><tfoot><tr><th colspan='7' class='text-end'><b>Total en $cmoneda </b></th><th colspan='2' class='text-start'><b>$" . number_format($totalpagado, 2, '.', ',') . "</b></th></tr></tfoot>";
    }
        return $datos . $foot;
    }

    public function getDatos($iddatos, $y, $m) {
        $datosPagadas = $this->getDatosAux($iddatos, $y, $m, '1');
        $pagadas = "";
        foreach ($datosPagadas as $can) {
            $pagadas = $can['facturas'];
        }

        $datosPendientes = $this->getDatosAux($iddatos, $y, $m, '2');
        $pendientes = "";
        foreach ($datosPendientes as $can) {
            $pendientes = $can['facturas'];
        }

        $datosCancelados = $this->getDatosAux($iddatos, $y, $m, '3');
        $canceladas = "";
        foreach ($datosCancelados as $can) {
            $canceladas = $can['facturas'];
        }
        $datos = "$pagadas</tr>$pendientes</tr>$canceladas";
        return $datos;
    }

    private function getDatosAux($iddatos, $y, $m, $estado) {
        $datos = "";
        if ($iddatos != '' && $iddatos != '0') {
            $datos = "and iddatosfacturacion='$iddatos'";
        }
        $consultado = false;
        $consulta = "SELECT count(*) facturas FROM datos_factura WHERE fecha_creacion LIKE '$y-$m%' $datos and status_pago='$estado';";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getDatosActualEstado($iddatos, $y, $m, $status) {
        $datos = "";
        $clientes = "";
        $totales = "";
        $count = 0;
        $datosPagadas = $this->getClienteActualEstadoAux($iddatos, $y, $m, $status);
        foreach ($datosPagadas as $can) {
            $idcliente = $can['idcliente'];
            $cliente = $can['nombre_empresa'];

            $datos = $this->getDatosActualEstadoAux($iddatos, $y, $m, $status, $idcliente);

            $total = 0;
            foreach ($datos as $actual) {
                $monto = $actual['total'];
                $tcambio = $actual['tcambio'];
                $idmoneda = $actual['id_moneda'];
                $total += $this->totalDivisa($monto, $tcambio, 1, $idmoneda);
            }

            if ($count >= 1) {
                $clientes .= ", '$cliente'";
                $totales .= ", " . bcdiv($total, '1', 2);
            } else {
                $clientes .= "'$cliente'";
                $totales .= bcdiv($total, '1', 2);
            }
            $count++;
        }
        $datos = "$clientes</tr>$totales";
        return $datos;
    }

    private function getClienteActualEstadoAux($iddatos, $y, $m, $estado) {
        $datos = "";
        if ($iddatos != '' && $iddatos != '0') {
            $datos = "and iddatosfacturacion='$iddatos'";
        }
        $consultado = false;
        $consulta = "select idcliente, c.nombre_empresa from datos_factura d inner join cliente c on (d.idcliente=c.id_cliente) where status_pago='$estado' and fecha_creacion like '$y-$m%' $datos group by idcliente order by c.nombre_empresa;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getDatosActualEstadoAux($iddatos, $y, $m, $estado, $idcliente) {
        $datos = "";
        if ($iddatos != '' && $iddatos != '0') {
            $datos = "and iddatosfacturacion='$iddatos'";
        }
        $consultado = false;
        $consulta = "select totalfactura total, d.tcambio, d.id_moneda from datos_factura d inner join cliente c on (d.idcliente=c.id_cliente) where status_pago='$estado' and fecha_creacion like '$y-$m%' and idcliente='$idcliente' $datos;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function insertarDatos($datos) {
        $contador = 0;
        $div = explode("\n", $datos);
        foreach ($div as $d) {
            $contador++;
            if ($contador > 1) {
                $exp = explode("~", $d);
                if ($exp[0] != "" && $exp[1] != 'IMS421231I45' && $exp[1] != 'INF7205011ZA') {
                    $divf1 = explode(' ', $exp[6]);
                    $divf2 = explode(' ', $exp[7]);
                    $consulta2 = "REPLACE INTO `datossat` VALUES (:uuid, :rfcemisor, :nombreemisor, :rfcreceptor, :nombrereceptor, :rfcpac, :fechaemision, :horaemision, :fechacertsat, :horacertsat, :monto, :efectocomprobante, :status, :fechacancelacion);";
                    $valores2 = array("uuid" => $exp[0],
                        "rfcemisor" => $exp[1],
                        "nombreemisor" => $exp[2],
                        "rfcreceptor" => $exp[3],
                        "nombrereceptor" => $exp[4],
                        "rfcpac" => $exp[5],
                        "fechaemision" => $divf1[0],
                        "horaemision" => $divf1[1],
                        "fechacertsat" => $divf2[0],
                        "horacertsat" => $divf2[1],
                        "monto" => $exp[8],
                        "efectocomprobante" => $exp[9],
                        "status" => $exp[10],
                        "fechacancelacion" => $exp[11]);
                    $con = new Consultas();
                    $insertado = $con->execute($consulta2, $valores2);
                }
            }
        }
        return $insertado;
    }

    public function getDatosBimestral($y, $bim, $fiscales) {
        if ($bim == '') {
            $fecha = getdate();
            $m = $fecha['mon'];
            switch ($m) {
                case '1':
                    $bim = '1';
                    break;
                case '2':
                    $bim = '1';
                    break;
                case '3':
                    $bim = '2';
                    break;
                case '4':
                    $bim = '2';
                    break;
                case '5':
                    $bim = '3';
                    break;
                case '6':
                    $bim = '3';
                    break;
                case '7':
                    $bim = '4';
                    break;
                case '8':
                    $bim = '4';
                    break;
                case '9':
                    $bim = '5';
                    break;
                case '10':
                    $bim = '5';
                    break;
                case '11':
                    $bim = '6';
                    break;
                case '12':
                    $bim = '6';
                    break;
            }
        }
        switch ($bim) {
            case '1':
                $m1 = "01";
                $m2 = "02";
                break;
            case '2':
                $m1 = "03";
                $m2 = "04";
                break;
            case '3':
                $m1 = "05";
                $m2 = "06";
                break;
            case '4':
                $m1 = "07";
                $m2 = "08";
                break;
            case '5':
                $m1 = "09";
                $m2 = "10";
                break;
            case '6':
                $m1 = "11";
                $m2 = "12";
                break;
        }

        $labels = $this->getlabelsImpuesto();
        $ganancias = $this->getGanancias($y, $m1, $m2, $fiscales);

        $datos = "$labels<dataset>$ganancias";
        return $datos;
    }

    private function getlabelsImpuesto() {
        $datos = "";
        $contador = 0;
        $impuestos = $this->getlabelsImpuestoAux();
        foreach ($impuestos as $actual) {
            $nombre = $actual['nombre'];
            $porcentaje = $actual['porcentaje'];
            if ($contador >= 1) {
                $datos .= "</tr>$nombre $porcentaje";
            } else {
                $datos .= "$nombre $porcentaje";
            }
            $contador++;
        }
        return $datos . "</tr>Iva Recargo";
    }

    private function getlabelsImpuestoAux() {
        $datos = "";
        $consultado = false;
        $consulta = "select * from impuesto;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getGanancias($y, $m1, $m2, $fiscales) {
        $ganancias = $this->getGananciasAux($y, $m1, $m2, $fiscales);
        $imptraslados = $this->getlabelsImpuestoAux();
        $row = array();
        foreach ($imptraslados as $tactual) {
            $idimpuesto = $tactual['tipoimpuesto'];
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $Timp = 0;

            foreach ($ganancias as $productoactual) {
                $idmoneda = $productoactual['id_moneda'];
                $tcambio = $productoactual['tcambio'];

                if ($idimpuesto == '1') {
                    $traslados = $productoactual['subtotaliva'];
                } else if ($idimpuesto == '2') {
                    $traslados = $productoactual['subtotalret'];
                }
                if ($traslados != "") {
                    $div = explode("<impuesto>", $traslados);
                    foreach ($div as $d) {
                        $div2 = explode("-", $d);
                        if ($porcentaje == $div2[1] && $impuesto == $div2[2]) {
                            $Timp += $this->totalDivisa($div2[0], $tcambio, 1, $idmoneda);
                        }
                    }
                }
            }

            $row[] = bcdiv($Timp, '1', 2);
        }
        $trasarray = implode("</tr>", $row);
        $gastos = $this->getGastos($y, $m1, $m2, $fiscales);
        return $trasarray . "</tr>" . $gastos;
    }

    private function getGananciasAux($y, $m1, $m2, $idfiscales) {
        $fiscales = "";
        if ($idfiscales != "") {
            $fiscales = " and iddatosfacturacion='$idfiscales'";
        }
        $datos = "";
        $consultado = false;
        $consulta = "SELECT subtotaliva,subtotalret, id_moneda, tcambio FROM datos_factura WHERE (fecha_creacion between '$y-$m1-01' AND '$y-$m2-31') AND status_pago != '3' AND uuid != ''$fiscales;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getGastos($y, $m1, $m2, $fiscales) {
        $total = 0;
        $ganancias = $this->getGastosAux($y, $m1, $m2, $fiscales);
        foreach ($ganancias as $g) {
            $totalfactura = $g['monto'];
            $subtotal = ($totalfactura / 1.16);
            $iva = $subtotal * 0.16;
            $total += bcdiv($iva, '1', 2);
        }
        return $total;
    }

    private function getGastosAux($y, $m1, $m2, $idfiscales) {
        $fiscales = "";
        if ($idfiscales != "") {
            $rfc = $this->getRfc($idfiscales);
            $fiscales = " and rfcreceptor='$rfc'";
        }
        $datos = "";
        $consultado = false;
        $consulta = "SELECT monto from datossat WHERE (fechaemision BETWEEN '$y-$m1-01' AND '$y-$m2-31') AND fechacancelacion != ''$fiscales;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getIVAReporte($emisor, $receptor, $ano, $mes) {
        if ($emisor == "" && $receptor == "" && $ano == "" && $mes == "") {
            $fecha = getdate();
            $y = $fecha['year'];
            $m = $fecha['mon'];
            if ($m < 10) {
                $m = "0$m";
            }
            $condicionfolio = " where fechaemision like'$y-$m%' order by fechaemision desc, horaemision desc";
        } else {
            $rfc = "";
            if ($receptor != "") {
                $rfcreceptor = $this->getRfc($receptor);
                $rfc = "and rfcreceptor = '$rfcreceptor'";
            }
            $m = "";
            if ($ano != '' || $mes != '') {
                $m = "$ano-$mes";
            }
            $condicionfolio = "where rfcemisor LIKE '%$emisor%' $rfc and fechaemision like'%$m%' order by fechaemision desc, horaemision desc";
        }
        $consultado = false;
        $consulta = "SELECT * FROM datossat $condicionfolio;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getRfc($idfiscales) {
        $rfc = "";
        $datos = $this->getRfcAux($idfiscales);
        foreach ($datos as $d) {
            $rfc = $d['rfc'];
        }
        return $rfc;
    }

    private function getRfcAux($idfiscales) {
        $datos = "";
        $consultado = false;
        $consulta = "SELECT rfc FROM datos_facturacion where id_datos='$idfiscales';";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getIVAHistorial($condicion) {
        $consultado = false;
        $consulta = "SELECT * FROM datossat $condicion;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function listaIVAHistorial($emisor, $receptor, $ano, $mes) {
        $datos = "<thead class='sin-paddding'>
        <tr>
            <th class='col-md-2'>UUID </th>
            <th>RFC Emisor </th>
            <th class='col-md-3'>Nombre Emisor </th>
            <th>RFC Receptor </th>
            <th>Fecha Emision </th>
            <th>Monto </th>
            <th><span class='glyphicon glyphicon-remove'></span></th>
        </tr>
        </thead>
        <tbody>";
        $condicionfolio = "";
        if ($emisor == "" && $receptor == "" && $ano == "" && $mes == "") {
            $fecha = getdate();
            $y = $fecha['year'];
            $m = $fecha['mon'];
            if ($m < 10) {
                $m = "0$m";
            }
            $condicionfolio = " where fechaemision like'$y-$m%' order by fechaemision desc, horaemision desc";
        } else {
            $rfc = "";
            if ($receptor != "") {
                $rfc = $this->getRfc($receptor);
            }
            $m = "";
            if ($ano != '' || $mes != '') {
                if ($mes != "") {
                    $mes = "$mes-";
                }

                $m = "$ano-$mes";
            }
            $condicionfolio = "where rfcemisor LIKE '%$emisor%' and rfcreceptor like '%$rfc%' and fechaemision like'%$m%' order by fechaemision desc, horaemision desc";
        }
        $condicion = "$condicionfolio";
        //$condicion="";
        $iva = $this->getIVAHistorial($condicion);
        $contador = 0;
        foreach ($iva as $actual) {
            $uuid = $actual['uuid'];
            $rfcemisor = $actual['rfcemisor'];
            $nombreemisor = $actual['nombreemisor'];
            $rfcreceptor = $actual['rfcreceptor'];
            $fechaemision = $actual['fechaemision'];
            $horaemision = $actual['horaemision'];
            $monto = $actual['monto'];

            $datos .= "
                    <tr>
                        <td>$uuid</td>
                        <td>$rfcemisor</td>
                        <td>$nombreemisor</td>
                        <td>$rfcreceptor</td>
                        <td>$fechaemision $horaemision</td>
                        <td>$ " . bcdiv($monto, 1, 2) . "</td>
                        <td><button class='btn btn-danger btn-xs' onclick=\"eliminarDatos('$uuid')\"><span class='glyphicon glyphicon-remove'></span></button></td>
                    </tr>
                     ";
            $contador++;
        }
        if ($contador == 0) {
            $datos .= "<tr><td class='text-center' colspan='11'>No se encontraron registros</td></tr></tbody>";
        }
        return $datos;
    }

    public function eliminarRegistro($uuid) {
        $eliminado = false;
        $consulta = "DELETE FROM `datossat` WHERE uuid=:uuid;";
        $valores = array("uuid" => $uuid);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        return $eliminado;
    }

    public function saveIMG($dataactual, $datapasado, $dataantep) {
        $img = str_replace('data:image/png;base64,', '', $dataactual);
        $img = str_replace(' ', '+', $img);
        $fileData = base64_decode($img);
        $fileName = '../temporal/photo.png';
        file_put_contents($fileName, $fileData);

        // Definimos el nombre de archivo a descargar.
        $filename = "photo.png";
        // Ahora guardamos otra variable con la ruta del archivo
        $file = "../temporal/" . $filename;
        // Aquí, establecemos la cabecera del documento
        header("Content-Description: Descargar imagen");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/force-download");
        header("Content-Length: " . filesize($file));
        header("Content-Transfer-Encoding: binary");
        readfile($file);
    }
/*
    public function getFacturas($idfactura) {
        $consultado = false;
        $consulta = "select * from datos_factura as dat 
        inner join catalogo_metodo_pago as cmp on (dat.id_metodo_pago= cmp.idmetodo_pago) 
        inner join catalogo_pago as cp on (dat.id_forma_pago = cp.idcatalogo_pago) 
        inner join catalogo_moneda as cm on (cm.idcatalogo_moneda = dat.id_moneda)
        inner join catalogo_uso_cfdi as cuc on (dat.id_uso_cfdi= cuc.iduso_cfdi) 
        inner join catalogo_comprobante as cc on (dat.id_tipo_comprobante=cc.idcatalogo_comprobante) where dat.iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }
*/
    public function getFacturas($idfactura) {
        $consulta = "SELECT * FROM datos_factura WHERE iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }
    public function getDetalle($tag) {
        $consultado = false;
        $consulta = "SELECT det.* FROM detalle_factura det WHERE tagdetallef=:id";
        $consultas = new Consultas();
        $val = array("id" => $tag);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getCartas($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM factura_carta dat 
        INNER JOIN catalogo_metodo_pago cmp ON (dat.id_metodo_pago= cmp.idmetodo_pago) 
        INNER JOIN catalogo_pago cp ON (dat.id_forma_pago = cp.idcatalogo_pago) 
        INNER JOIN catalogo_moneda cm ON (cm.idcatalogo_moneda = dat.id_moneda)
        INNER JOIN catalogo_uso_cfdi cuc ON (dat.id_uso_cfdi= cuc.iduso_cfdi) 
        INNER JOIN catalogo_comprobante cc ON (dat.id_tipo_comprobante=cc.idcatalogo_comprobante) WHERE dat.idfactura_carta=:id;";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDetalleCarta($tag) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM detallefcarta det WHERE tagdetfactura=:tag";
        $val = array("tag" => $tag);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getMercancias($tag) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM detallemercancia WHERE tagmercancia=:tag";
        $val = array("tag" => $tag);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getUbicaciones($tag, $tipo) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM detalleubicacion u INNER JOIN estado e on (u.ubicacion_idestado=e.id_estado) WHERE tagubicacion=:tag and ubicacion_tipo=:tipo";
        $val = array("tag" => $tag,
            "tipo" => $tipo);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getOperadores($tag) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM detalleoperador u INNER JOIN estado e on (u.operador_idestado=e.id_estado) WHERE tagoperador=:tag;";
        $val = array("tag" => $tag);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosCarta($tag) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM datos_carta WHERE tagcarta=:tag;";
        $val = array("tag" => $tag);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getPrimerConceptoCarta($tag) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT det.*, ps.clave_fiscal, ps.clv_unidad, ps.desc_unidad FROM detallefcarta det INNER JOIN productos_servicios ps ON (det.id_producto_servicio=ps.idproser) WHERE tagdetfactura=:tag ORDER BY iddetallefcarta LIMIT 1";
        $val = array("tag" => $tag);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getPrimerMercancia($tag) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM detallemercancia WHERE tagmercancia=:tag ORDER BY iddetallemercancia LIMIT 1;";
        $val = array("tag" => $tag);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    private function getDistanciaTotalAux($tag) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT sum(ubicacion_distancia) distancia FROM detalleubicacion u WHERE tagubicacion=:tag";
        $val = array("tag" => $tag);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getDistanciaTotal($tag) {
        $distancia = 0;
        $datos = $this->getDistanciaTotalAux($tag);
        foreach ($datos as $actual) {
            $distancia = $actual['distancia'];
        }
        return $distancia;
    }

    public function getPrimerUbicacion($tag, $tipo) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM detalleubicacion u INNER JOIN estado e on (u.ubicacion_idestado=e.id_estado) WHERE tagubicacion=:tag and ubicacion_tipo=:tipo ORDER BY iddetalleubicacion LIMIT 1;";
        $val = array("tag" => $tag,
            "tipo" => $tipo);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getPrimerOperador($tag) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM detalleoperador u INNER JOIN estado e on (u.operador_idestado=e.id_estado) WHERE tagoperador=:tag ORDER BY iddetalleoperador LIMIT 1;";
        $val = array("tag" => $tag);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    private function getOperadorbyRFC($rfc) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM operador WHERE rfcoperador=:rfc;";
        $val = array("rfc" => $rfc);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    public function getNombreOperador($rfc) {
        $nombre = "";
        $operador = $this->getOperadorbyRFC($rfc);
        foreach ($operador as $actual) {
            $nombre = $actual['nombreoperador'] . ' ' . $actual['apaternooperador'] . ' ' . $actual['amaternooperador'];
        }
        return $nombre;
    }

    public function getDireccionUbicacion($idubicacion, $codp, $estado) {
        $direccion = "CP. $codp, $estado";
        $datos = $this->getUbicacionbyID($idubicacion);
        foreach ($datos as $actual) {
            $codpostal = $actual['codpostal'];
            $estado = $actual['estado'];
            $direccion = "CP. $codpostal, $estado";
            $calle = $actual['calle'];
            $numext = $actual['numext'];
            $numint = $actual['numint'];
            $localidad = $actual['localidad'];
            $idmunicipio = $actual['ubicacion_idmunicipio'];
            $colonia = $actual['colonia'];

            if ($numext != "") {
                $numext = " #$numext";
            }

            if ($numint != "") {
                $numint = ", Interior $numint";
            }

            if ($colonia != "") {
                $colonia = ", Colonia: $colonia";
            }

            if ($codpostal != "" && $codpostal != "0") {
                $codpostal = " CP. $codpostal";
            }

            $municipio = "";
            if ($idmunicipio != "0") {
                $muni = $this->getMunicipioAux($idmunicipio);
                $municipio = ", $muni";
            }

            $direccion = $calle . $numext . $numint . $colonia . $codpostal . $municipio . " $estado";
        }
        return $direccion;
    }

    public function getUbicacionbyID($uid) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM ubicacion u INNER JOIN estado e ON (u.ubicacion_idestado=e.id_estado) WHERE idubicacion=:uid;";
        $val = array("uid" => $uid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDireccionOperador($idubicacion, $calle, $codp, $estado) {
        $direccion = "CP. $codp, $estado";
        $datos = $this->getOperadorbyID($idubicacion);
        foreach ($datos as $actual) {
            $codpostal = $actual['cpoperador'];
            $estado = $actual['estado'];
            $calle = $actual['calle'];
            $idmunicipio = $actual['operador_idmunicipio'];

            if ($codpostal != "" && $codpostal != "0") {
                $codpostal = " CP. $codpostal";
            }

            $municipio = "";
            if ($idmunicipio != "0") {
                $muni = $this->getMunicipioAux($idmunicipio);
                $municipio = ", $muni";
            }

            $direccion = $calle . $codpostal . $municipio . " $estado";
        }
        return $direccion;
    }

    public function getOperadorbyID($uid) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM operador o INNER JOIN estado e ON (o.operador_idestado=e.id_estado) WHERE idoperador=:uid;";
        $val = array("uid" => $uid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosFacturacionbyId($iddatos) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion where id_datos=:id;";
        $val = array("id" => $iddatos);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosCliente($idcliente) {
        $consultado = false;
        $consulta = "SELECT * FROM cliente as c where id_cliente=:id;";
        $val = array("id" => $idcliente);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getEstadoAux($idestado) {
        $estado = "";
        $est = $this->getEstadoById($idestado);
        foreach ($est as $actual) {
            $estado = $actual['estado'];
        }
        return $estado;
    }

    private function getEstadoById($idestado) {
        $consultado = false;
        $consulta = "select * from datos_facturacion WHERE estado=:id;";
        $valores = array("id" => $idestado);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getMunicipioAux($idmun) {
        $municipio = "";
        $mun = $this->getMunicipioById($idmun);
        foreach ($mun as $actual) {
            $municipio = $actual['municipio'];
        }
        return $municipio;
    }

    private function getMunicipioById($idmun) {
        $consultado = false;
        $consulta = "select * from datos_facturacion WHERE municipio=:id;";
        $valores = array("id" => $idmun);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getPrimerConcepto($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT det.*, ps.clave_fiscal, ps.clv_unidad, ps.desc_unidad FROM detalle_factura det INNER JOIN productos_servicios ps ON (det.id_producto_servicio=ps.idproser) WHERE tagdetallef=:id ORDER BY iddetalle_factura LIMIT 1";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getPagos($idfactura) {
        $consultado = false;
        $consulta = "SELECT 
        p.*, 
        df.razon_social AS rzemisor, 
        df.rfc AS rfcemisor, 
        df.c_regimenfiscal, 
        df.regimen_fiscal, 
        df.calle AS calleemi, 
        df.numero_exterior AS numemisor, 
        df.colonia, 
        df.idmunicipio AS idmunemisor, 
        df.idestado AS idestemisor, 
        df.firma, 
        dp.*, 
        c.*, 
        comp.nombre_forma_pago
    FROM 
        pagos p 
    INNER JOIN 
        datos_facturacion df ON df.id_datos = p.pago_idfiscales 
    INNER JOIN 
        cliente c ON p.pago_idcliente = c.id_cliente 
    LEFT JOIN 
        detallepago dp ON dp.detalle_tagencabezado = p.tagpago
    LEFT JOIN 
        complemento_pago comp ON comp.tagpago = p.tagpago WHERE 
    p.idpago = :id";
        $consultas = new Consultas();
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDetallePago($idpago) {
        $consultado = false;
        $consulta = "SELECT p.*, 
        f.serie, 
        f.letra, 
        f.folio_interno_fac, 
        f.uuid, 
        f.status_pago, 
        f.tcambio
 FROM detallepago p
 INNER JOIN datos_factura f ON f.iddatos_factura = p.pago_idfactura
 INNER JOIN pagos pa ON (p.detalle_tagencabezado = pa.tagpago)
 WHERE pa.idpago =:folio";
        $val = array("folio" => $idpago);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }
/*
    public function totalDivisa($total, $tcambio, $monedaP, $monedaF) {
        if ($monedaP == $monedaF) {
            $OP = bcdiv($total, '1', 2);
        } else {
            $tcambio = 1 / $tcambio;
            if ($monedaP == '1') {
                $OP = bcdiv($total, '1', 2) / bcdiv($tcambio, '1', 6);
            } else if ($monedaP == '2') {
                if ($monedaF == '4') {
                    $tcambio = $this->getTipoCambio($monedaF, $monedaP);
                } else if ($monedaF = '1') {
                    $tcambio = $this->getTipoCambio($monedaP);
                    $tcambio = 1 / $tcambio;
                }
                $OP = bcdiv($total, '1', 2) * bcdiv($tcambio, '1', 6);
            } else if ($monedaP == '4') {
                if ($monedaF == '2') {
                    $tcambio = $this->getTipoCambio($monedaF, $monedaP);
                } else if ($monedaF = '1') {
                    $tcambio = $this->getTipoCambio($monedaP);
                    $tcambio = 1 / $tcambio;
                }
                $OP = bcdiv($total, '1', 2) * bcdiv($tcambio, '1', 6);
            }
        }
        return $OP;
    }
*/
    public function totalDivisa($total, $tcambio, $monedaP, $monedaF) {
        if ($monedaP == $monedaF) {
            $OP = bcdiv($total, '1', 2);
        } else {
            $tcambio = (float) $tcambio;
            $tcambio = 1 / $tcambio;

            if ($monedaP == '1') {
                $OP = bcdiv($total, '1', 2) / bcmul($tcambio, '1', 6);
            } else if ($monedaP == '2') {
                if ($monedaF == '4') {
                    $tcambio = $this->getTipoCambio($monedaF, $monedaP);
                } else if ($monedaF == '1') {
                    $tcambio = $this->getTipoCambio($monedaP);
                }
                $tcambio = bcmul($tcambio, '1', 6);
                $OP = bcdiv($total, '1', 2) * $tcambio;
            } else if ($monedaP == '4') {
                if ($monedaF == '2') {
                    $tcambio = $this->getTipoCambio($monedaF, $monedaP);
                } else if ($monedaF == '1') {
                    $tcambio = $this->getTipoCambio($monedaP);
                }
                $tcambio = bcmul($tcambio, '1', 6);
                $OP = bcdiv($total, '1', 2) * $tcambio;
            }
        }
        return $OP;
    }

    private function getTipoCambio($idmoneda, $idmonedaF = '0') {
        $moneda = $this->getTipoCambioAux($idmoneda);
        $tcambio = "";
        if ($idmonedaF == '0') {
            foreach ($moneda as $actual) {
                $tcambio = $actual['tipo_cambio'];
            }
        } else {
            if ($idmoneda == '2' && $idmonedaF == '4') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['cambioEuro'];
                }
            } else if ($idmoneda == '4' && $idmonedaF == '2') {
                foreach ($moneda as $actual) {
                    $tcambio = $actual['cambioDolar'];
                }
            }
        }
        return $tcambio;
    }

    private function getTipoCambioAux($idmoneda) {
        $consultado = false;
        $consulta = "select * from datos_factura where moneda=:id;";
        $val = array("id" => $idmoneda);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDireccionCliente($idcliente, $fid) {
        $direccion = "";
        $datos = $this->getDatosClienteByFactura($idcliente, $fid);
        foreach ($datos as $actual) {
            $codpostal = $actual['codigo_postal'];
            $cpreceptor = $actual['cpreceptor'];
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
                    $muni = $this->getMunicipioAux($idmunicipio);
                    $municipio = ", $muni";
                }

                $estadodir = "";
                if ($idestadodir != "0") {
                    $est = $this->getEstadoAux($idestadodir);
                    $estadodir = ", $est";
                }

                $direccion = $calle . $next . $col . $cp . $municipio . $estadodir;
            }
        }
        return $direccion;
    }

    private function getDatosClienteByFactura($idcliente, $fid) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT c.*, d.cpreceptor FROM cliente c INNER JOIN datos_factura d ON (c.id_cliente=d.idcliente) WHERE id_cliente=:cid AND iddatos_factura=:fid;";
        $val = array("cid" => $idcliente,
            "fid" => $fid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDireccionClienteCarta($idcliente, $fid) {
        $direccion = "";
        $datos = $this->getDatosClienteByCarta($idcliente, $fid);
        foreach ($datos as $actual) {
            $codpostal = $actual['codigo_postal'];
            $cpreceptor = $actual['cpreceptor'];
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
                    $muni = $this->getMunicipioAux($idmunicipio);
                    $municipio = ", $muni";
                }

                $estadodir = "";
                if ($idestadodir != "0") {
                    $est = $this->getEstadoAux($idestadodir);
                    $estadodir = ", $est";
                }

                $direccion = $calle . $next . $col . $cp . $municipio . $estadodir;
            }
        }
        return $direccion;
    }

    private function getDatosClienteByCarta($idcliente, $fid) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT c.*, d.cpreceptor FROM cliente c INNER JOIN factura_carta d ON (c.id_cliente=d.idcliente) WHERE idcliente=:cid AND idfactura_carta=:fid;";
        $val = array("cid" => $idcliente,
            "fid" => $fid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getcfdisRelacionados($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM cfdirelacionado WHERE desctiporel=:id ORDER BY tiporel;";
        $val = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

}
