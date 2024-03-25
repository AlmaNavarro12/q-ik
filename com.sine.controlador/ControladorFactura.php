<?php
require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Factura.php';
require_once '../com.sine.modelo/SendMail.php';
require_once '../com.sine.modelo/TMP.php';

use SWServices\Toolkit\SignService as Sellar;
use SWServices\Stamp\StampService as StampService;
use SWServices\Cancelation\CancelationService as CancelationService;
use SWServices\SatQuery\SatQueryService as consultaCfdiSAT;

date_default_timezone_set("America/Mexico_City");

class ControladorFactura {
    private  $consultas;
    
    function __construct() {
        $this->consultas = new Consultas();
    }

    private function getSWAccessAux() {
        $consultado = false;
        $consulta = "SELECT * FROM swaccess WHERE idswaccess=:id;";
        $consultas = new Consultas();
        $valores = array("id" => '1');
        $consultado = $consultas->getResults($consulta, $valores);
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

    private function getTMPCFDIS($sessionId) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM tmpcfdi WHERE sessionid=:sessionid ORDER BY idtmpcfdi";
        $valores = array("sessionid" => $sessionId);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function agregarCFDI($t) {
        $insertado = false;
        $consulta = "INSERT INTO `tmpcfdi` VALUES (:id, :tiporel, :uuid, :session);";
        $valores = array("id" => null,
            "tiporel" => $t->getTiporel(),
            "uuid" => $t->getUuid(),
            "session" => $t->getSessionid());
        $con = new Consultas();
        $con->execute($consulta, $valores);
        $datos = $this->tablaCFDI($t->getSessionid());
        return $datos;
    }

    private function tablaCFDI($idsession, $uuid = "") {
        $datos = "<corte><thead class='sin-paddding'>
            <tr>
                <th class='text-center'>RELACION</th>
                <th class='text-center'>CFDI</th>
                <th class='text-center'>ELIMINAR</th></tr>
                </thead><tbody>";
        $disuuid = "";
        if ($uuid != "") {
            $disuuid = "disabled";
        }
        $cfdi = $this->getTMPCFDIS($idsession);
        foreach ($cfdi as $actual) {
            $idtmp = $actual['idtmpcfdi'];
            $tiporel = $actual['tiporel'];
            $uuid = $actual['uuid'];
            $datos .= "
                    <tr>
                        <td>$tiporel</td>
                        <td>$uuid</td>
                        <td><button $disuuid class='button-list' onclick='eliminarCFDI($idtmp)' title='Eliminar CFDI'><span class='fas fa-times'></span></button></td>
                    </tr>
                     ";
        }
        return $datos;
    }

    private function getDistinctCfdisRelacionados($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT DISTINCT tiporel FROM cfdirelacionado WHERE cfditag=:id;";
        $val = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getcfdisRelacionadosByTipo($id, $tipo) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM cfdirelacionado WHERE cfditag=:id AND tiporel=:tipo;";
        $val = array("id" => $id,
            "tipo" => $tipo);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getcfdisRelacionados($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM cfdirelacionado c INNER JOIN catalogo_relacion r ON (r.c_tiporelacion=c.tiporel) WHERE c.cfditag=:id ORDER BY tiporel;";
        $val = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }
   
    
  
    

    public function cfdisRelacionados($tag, $sessionid, $uuidTim) {
        $productos = $this->getcfdisRelacionados($tag);
        foreach ($productos as $productoactual) {
            $tiporel = $productoactual["tiporel"];
            $uuid = $productoactual['uuid'];

            $consulta = "INSERT INTO `tmpcfdi` VALUES (:id, :tiporel, :uuid, :session);";
            $valores = array("id" => null,
                "tiporel" => $tiporel,
                "uuid" => $uuid,
                "session" => $sessionid);
            $con = new Consultas();
            $con->execute($consulta, $valores);
        }
        $tabla = $this->tablaCFDI($sessionid, $uuidTim);
        return $tabla;
    }

    public function eliminarCFDI($idtmp, $sessionid) {
        $insertado = false;
        $con = new Consultas();
        $consulta = "DELETE FROM `tmpcfdi` WHERE idtmpcfdi=:id;";
        $valores = array("id" => $idtmp);
        $con->execute($consulta, $valores);
        $datos = $this->tablaCFDI($sessionid);
        return $datos;
    }

    public function modificarChIva($idtmp, $chiva, $chret) {
        $check = $this->checkProductoTMPAux($idtmp);
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $precio_tmp = $actual['precio_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
            $trasladostmp = $actual['trasladotmp'];
            $retenciontmp = $actual['retenciontmp'];
            $idproducto = $actual['id_productotmp'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
        }

        $totalun = $canttmp * $precio_tmp;
        $impdesc = $totalun * ($descuento_tmp / 100);
        $importe = $totalun - $impdesc;

        $rebuildT = $this->reBuildArray($importe, $chiva);
        $divT = explode("<cut>", $rebuildT);
        $traslados = $divT[0];
        $Timp = $divT[1];

        $rebuildR = $this->reBuildArray($importe, $chret);
        $divR = explode("<cut>", $rebuildR);
        $retenciones = $divR[0];
        $Tret = $divR[1];

        $total = (( bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));
        $consulta = "UPDATE `tmp` set trasladotmp=:chiva, retenciontmp=:chret, totunitario_tmp=:totun, impdescuento_tmp=:impdesc, imptotal_tmp=:total where idtmp = :idtmp;";
        $con = new Consultas();
        $val = array("chiva" => $traslados,
            "chret" => $retenciones,
            "totun" => $totalun,
            "impdesc" => $impdesc,
            "total" => $total,
            "idtmp" => $idtmp);
        $datos = $con->execute($consulta, $val);
        return $datos;
    }

    public function getDatosEncabezado() {
        $consultado = false;
        $consulta = "SELECT * FROM encabezados WHERE idencabezado=:id;";
        $consultas = new Consultas();
        $valores = array("id" => '1');
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getTipoCambio() {
        $data = json_decode(file_get_contents('http://www.banxico.org.mx/pubDGOBC_Sisfix-war/Sisfix_JSON'), true);
        $cambio = $data['tcfix'];
        $actualizar = $this->saveTipoCambio($cambio);
        return $cambio;
    }

    public function saveTipoCambio($cambio) {
        $consulta = "UPDATE `catalogo_moneda` SET tipo_cambio=:tcambio where idcatalogo_moneda=:id;";
        $valores = array("id" => '2',
            "tcambio" => $cambio);
        $con = new Consultas();
        $insertado = $con->execute($consulta, $valores);
    }

    public function genString($length = 6) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function getTelefono($idfactura) {
        $correos = $this->getCorreosAux($idfactura);
        foreach ($correos as $actual) {
            $telefono = $actual['telefono'];
        }
        return "$telefono";
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
        $consultas = new Consultas();
        $consulta = "SELECT c.email_informacion,c.email_facturacion,c.email_gerencia, c.correoalt1, c.correoalt2, c.correoalt3, c.telefono FROM datos_factura dat INNER JOIN cliente c ON (dat.idcliente=c.id_cliente) WHERE dat.iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkProductoTMPAux($idtmp) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM tmp WHERE idtmp=:id;";
        $val = array("id" => $idtmp);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkProductoAux($idtmp) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT cantinv, chinventario FROM productos_servicios WHERE idproser=:id;";
        $val = array("id" => $idtmp);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function reBuildArray2($importe, $array) {
        $div = explode("<impuesto>", $array);
        $row = array();
        $Timp = 0;
        foreach ($div as $d) {
            $div2 = explode("-", $d);
            $imp = $importe * $div2[1];
            $Timp += $imp;
            if ($Timp > 0) {
                $row[] = bcdiv($imp, '1', 2) . '-' . $div2[1] . '-' . $div2[2];
            }
          
        }
        $rearray = implode("<impuesto>", $row);
        return $rearray;
    }

    private function reBuildArray($importe, $array) {
        $div = explode("<impuesto>", $array);
        $row = array();
        $Timp = 0;
        foreach ($div as $d) {
            $div2 = explode("-", $d);
            $imp = $importe * $div2[1];
            $Timp += $imp;
            if ($Timp > 0) {
                $row[] = bcdiv($imp, '1', 2) . '-' . $div2[1] . '-' . $div2[2];
            }
        }
        $rearray = implode("<impuesto>", $row);
        return $rearray . "<cut>" . $Timp;
    }

   

    public function incrementarProducto($idtmp) {
        $check = $this->checkProductoTMPAux($idtmp);
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $precio_tmp = $actual['precio_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
            $trasladostmp = $actual['trasladotmp'];
            $retenciontmp = $actual['retenciontmp'];
            $idproducto = $actual['id_productotmp'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) { 
            $chinv = $pactual['chinventario']; //328
            $cantidad = $pactual['cantinv'];
        }

        $cant = $canttmp + 1;
        $totalun = $cant * $precio_tmp;
        $impdesc = $totalun * ($descuento_tmp / 100);
        $importe = $totalun - $impdesc;

        $rebuildT = $this->reBuildArray($importe, $trasladostmp);
        $divT = explode("<cut>", $rebuildT);
        $traslados = $divT[0];
        $Timp = $divT[1];

        $rebuildR = $this->reBuildArray($importe, $retenciontmp);
        $divR = explode("<cut>", $rebuildR);
        $retenciones = $divR[0];
        $Tret = $divR[1];

        $total = ((bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        if ($chinv == '1') {
            if ($cantidad <= 0) {
                $datos = "0El inventario no es suficiente para agregar mas producto";
            } else {
                $consulta = "UPDATE `tmp` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmp = :id;";
                $valores = array("id" => $idtmp,
                    "cant" => $cant,
                    "totuni" => bcdiv($totalun, '1', 2),
                    "impdesc" => bcdiv($impdesc, '1', 2),
                    "imptot" => bcdiv($total, '1', 2),
                    "traslados" => $traslados,
                    "retenciones" => $retenciones);
                $con = new Consultas();
                $datos = $con->execute($consulta, $valores);
                $inv = $this->removerInventario($idproducto, '1');
            }
        } else if ($chinv == '0') {
            $consulta = "UPDATE `tmp` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmp = :id;";
            $valores = array("id" => $idtmp,
                "cant" => $cant,
                "totuni" => bcdiv($totalun, '1', 2),
                "impdesc" => bcdiv($impdesc, '1', 2),
                "imptot" => bcdiv($total, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retenciones);
            $con = new Consultas();
            $datos = $con->execute($consulta, $valores);
        }
        return $datos;
    }

    public function reducirProducto($idtmp) {
        $check = $this->checkProductoTMPAux($idtmp);
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $precio_tmp = $actual['precio_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
            $trasladostmp = $actual['trasladotmp'];
            $retenciontmp = $actual['retenciontmp'];
            $idproducto = $actual['id_productotmp'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
        }

        $cant = $canttmp - 1;
        $totalun = $cant * $precio_tmp;
        $impdesc = $totalun * ($descuento_tmp / 100);
        $importe = $totalun - $impdesc;

        $rebuildT = $this->reBuildArray($importe, $trasladostmp);
        $divT = explode("<cut>", $rebuildT);
        $traslados = $divT[0];
        $Timp = $divT[1];

        $rebuildR = $this->reBuildArray($importe, $retenciontmp);
        $divR = explode("<cut>", $rebuildR);
        $retenciones = $divR[0];
        $Tret = $divR[1];

        $total = ((bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        $consulta = "UPDATE `tmp` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmp = :id;";
        $valores = array("id" => $idtmp,
            "cant" => $cant,
            "totuni" => bcdiv($totalun, '1', 2),
            "impdesc" => bcdiv($impdesc, '1', 2),
            "imptot" => bcdiv($total, '1', 2),
            "traslados" => $traslados,
            "retenciones" => $retenciones);
        $con = new Consultas();
        $datos = $con->execute($consulta, $valores);
        if ($chinv == '1') {
            $inv = $this->restaurarInventario($idproducto, '1');
        }
        return $datos;
    }

    public function modificarCantidad($idtmp, $cant) {
        $check = $this->checkProductoTMPAux($idtmp);
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $precio_tmp = $actual['precio_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
            $traslados = $actual['trasladotmp'];
            $retenciones = $actual['retenciontmp'];
            $idproducto = $actual['id_productotmp'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
        }

        $totalun = $cant * $precio_tmp;
        $impdesc = $totalun * ($descuento_tmp / 100);
        $importe = $totalun - $impdesc;

        $rebuildT = $this->reBuildArray($importe, $traslados);
        $divT = explode("<cut>", $rebuildT);
        $traslados = $divT[0];
        $Timp = $divT[1];

        $rebuildR = $this->reBuildArray($importe, $retenciones);
        $divR = explode("<cut>", $rebuildR);
        $retenciones = $divR[0];
        $Tret = $divR[1];

        $restante = ($cantidad + $canttmp) - $cant;
        $total = (( bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        if ($chinv == '1') {
            if ($restante < 0) {
                $datos = "0El inventario no es suficiente para agregar mas producto";
            } else {
                $consulta = "UPDATE `tmp` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmp = :id;";
                $valores = array("id" => $idtmp,
                    "cant" => $cant,
                    "totuni" => bcdiv($totalun, '1', 2),
                    "impdesc" => bcdiv($impdesc, '1', 2),
                    "imptot" => bcdiv($total, '1', 2),
                    "traslados" => $traslados,
                    "retenciones" => $retenciones);
                $con = new Consultas();
                $datos = $con->execute($consulta, $valores);
                $inv = $this->restaurarInvCant($idproducto, $restante);
            }
        } else if ($chinv == '0') {
            $consulta = "UPDATE `tmp` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmp = :id;";
            $valores = array("id" => $idtmp,
                "cant" => $cant,
                "totuni" => bcdiv($totalun, '1', 2),
                "impdesc" => bcdiv($impdesc, '1', 2),
                "imptot" => bcdiv($total, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retenciones);
            $con = new Consultas();
            $datos = $con->execute($consulta, $valores);
        }
        return $datos;
    }

    public function getCodNomProducto($id) {
        $consultado = false;
        $consulta = "SELECT codproducto,nombre_producto FROM productos_servicios WHERE idproser=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function checkInventarioAux($idprod) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT chinventario,cantinv FROM productos_servicios where idproser=:pid;";
        $val = array("pid" => $idprod);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function checkInventario($t) {
        $idproducto = $t->getIdproductotmp();
        $cantidad = $t->getCantidadtmp();
        $inventario = $this->checkInventarioAux($idproducto);
        foreach ($inventario as $invactual) {
            $chinv = $invactual['chinventario'];
            $cantidadinv = $invactual['cantinv'];
        }
        $restante = $cantidadinv - $cantidad;
        if ($chinv == '1') {
            if ($restante < 0) {
                $datos = "0El inventario no es suficiente para agregar este producto";
            } else {
                echo print_r($t);
                $datos = $this->agregar($t, $chinv);
                $inventario = $this->removerInventario($idproducto, $cantidad);
            }
        } else if ($chinv == '0') {
            $datos = $this->agregar($t, $chinv);
        }

        return $datos;
    }

    public function agregar($t, $chinv) {
        $insertado = true; 
        $con = new Consultas();
        $datos = "";
        $clvfiscal = "";
        $clvunidad = "";
        $productos = $this->getProductoById($t->getIdproductotmp());
        foreach ($productos as $prod) {
            $clvfiscal = $prod['clave_fiscal'] . '-' . $prod['desc_fiscal'];
            $clvunidad = $prod['clv_unidad'] . '-' . $prod['desc_unidad'];
        }
        $importe = $t->getImportetmp() - $t->getImpdescuento();
        $traslados = $this->reBuildArray2($importe, $t->getIdtraslados());
        $retenciones = $this->reBuildArray2($importe, $t->getIdretencion());

        $consulta = "INSERT INTO `tmp` VALUES (:id, :idproducto, :nombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :idtraslado, :idretencion, :observaciones, :chinventario, :clvfiscal, :clvunidad, :session);";
        $valores = array("id" => null,
            "idproducto" => $t->getIdproductotmp(),
            "nombre" => $t->getDescripciontmp(),
            "cantidad" => $t->getCantidadtmp(),
            "precio" => $t->getPreciotmp(),
            "importe" => $t->getImportetmp(),
            "descuento" => $t->getDescuento(),
            "impdescuento" => $t->getImpdescuento(),
            "imptotal" => $t->getImptotal(),
            "idtraslado" => $traslados,
            "idretencion" => $retenciones,
            "observaciones" => '',
            "chinventario" => $chinv,
            "clvfiscal" => $clvfiscal,
            "clvunidad" => $clvunidad,
            "session" => $t->getSessionid());
        $datos = $con->execute($consulta, $valores);
        return $datos;
    }
   

    public function tablaProd($sessionid, $uuid = "") {
        $datos = "<thead class='sin-paddding'>
            <tr >
                <th class='text-center'>CLV FISCAL</th>
                <th class='text-center'>CANT.</th>
                <th class='text-center'>DESCRIPCION</th>
                <th class='text-center'>PRECIO</th>
                <th class='text-center'>IMPORTE</th>
                <th class='text-center'>DESCUENTO</th>
                <th class='text-center'>TRASLADOS</th>
                <th class='text-center'>RETENCIONES</th>
                <th class='text-center col-md-2'>OBSERVACIONES</th>
                <th class='text-center'>OPCIONES</th></tr>
                </thead><tbody>";
        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $disuuid = "";
        if ($uuid != "") {
            $disuuid = "disabled";
        }
        $productos = $this->getTMP($sessionid);
        $imptraslados = $this->getImpuestos('1');
        $impretenciones = $this->getImpuestos('2');
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmp'];
            $idproducto = $productoactual['id_productotmp'];
            $nombre = $productoactual['tmpnombre'];
            $cantidad = $productoactual['cantidad_tmp'];
            $clavefiscal = $productoactual['clvfiscaltmp'];
            $pventa = $productoactual['precio_tmp'];
            $ptotal = $productoactual['totunitario_tmp'];
            $descuento = $productoactual['impdescuento_tmp'];
            $traslados = $productoactual['trasladotmp'];
            $retencion = $productoactual['retenciontmp'];
            $observaciones = $productoactual['observaciones_tmp'];
            $divclv = explode("-", $clavefiscal);
            $clvfiscal = $divclv[0];
            $obser = str_replace("<ent>", "\n", $observaciones);

            if ($obser == "") {
                $obser = "<span class='fas fa-pencil-alt'></span>";
              
            }

            $importe = bcdiv($ptotal, '1', 2) - bcdiv($descuento, '1', 2);
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


            $checkedT = "";
            $iconT = "";
            $optraslados = "";
            foreach ($imptraslados as $tactual) {
                $divcheck = explode("<imp>", $checktraslado);
                foreach ($divcheck as $t) {
                    if ($t == $tactual['porcentaje'] . "-" . $tactual['impuesto']) {
                        $iconT = "";
                        $checkedT = "checked";
                        break;
                    } else {
                        $iconT = "";
                        $checkedT = "";
                    }
                }
                $optraslados = $optraslados . "<li data-location='tabla' data-id='$id_tmp'><label class='dropdown-item checkbox'>
                <input type='checkbox' $checkedT value='" . $tactual['porcentaje'] . "' name='chtrastabla$id_tmp' data-impuesto='" . $tactual['impuesto'] . "' data-tipo='" . $tactual['tipoimpuesto'] . "' />
                <span class='glyphicon $iconT' id='chuso1span'></span>
                " . $tactual['nombre'] . " (" . $tactual['porcentaje'] . "%)
            </label></li>";
                }

            $checkedR = "";
            $iconR = "";
            $opretencion = "";
            foreach ($impretenciones as $ractual) {
                $divcheckR = explode("<imp>", $checkretencion);
                foreach ($divcheckR as $r) {
                    if ($r == $ractual['porcentaje'] . "-" . $ractual['impuesto']) {
                        $iconR = "";
                        $checkedR = "checked";
                        break;
                    } else {
                        $iconR = "";
                        $checkedR = "";
                    }
                }
                 $opretencion = $opretencion . "<li data-location='tabla' data-id='$id_tmp'><label class='dropdown-item checkbox'>
                 <input type='checkbox' $checkedR value='" . $ractual['porcentaje'] . "' name='chrettabla$id_tmp' data-impuesto='" . $ractual['impuesto'] . "' data-tipo='" . $ractual['tipoimpuesto'] . "' />
                     <span class='glyphicon $iconR' id='chuso1span'></span>
                     " . $ractual['nombre'] . " (" . $ractual['porcentaje'] . "%)
                 </label></li>";
               }

            $sumador_iva += bcdiv($imp, '1', 2);
            $sumador_ret += bcdiv($ret, '1', 2);
            $sumador_total += bcdiv($ptotal, '1', 2);
            $sumador_descuento += bcdiv($descuento, '1', 2);
            $disabledminus = "";
            if ($cantidad == '1') {
                $disabledminus = "disabled";
            }
            $datos .= " 
            <tr>
                <td>$clvfiscal</td>
                <td>
                    <div class='btn-group'>
                        <button type='button' class='btn btn-outline-secondary btn-sm ' $disabledminus $disuuid data-type='minus' data-field='quant[1]' onclick='reducirCantidad($id_tmp);'>
                            <i class='fas fa-minus'></i>
                        </button>
                        <button $disuuid class='badge rounded-pill text-bg-info' data-bs-toggle='modal' data-bs-target='#modal-cantidad' onclick='setCantidad($id_tmp,$cantidad)'>
                            <div class='badge' id='badcant$id_tmp'>$cantidad</div>
                        </button>
                        <button type='button' class='btn btn-outline-secondary btn-sm ' data-type='plus' onclick='incrementarCantidad($id_tmp);' $disuuid>
                            <i class='fas fa-plus'></i>
                        </button>
                    </div>
                </td>
                <td>$nombre</td>
                <td>$ " . number_format(bcdiv($pventa, '1', 2), 2, '.', ',') . "</td>
                <td>$ " . number_format(bcdiv($ptotal, '1', 2), 2, '.', ',') . "</td>
                <td>$ " . number_format(bcdiv($descuento, '1', 2), 2, '.', ',') . "</td>
                <td>
                    <div class='input-group'>
                        <div class='dropdown'>
                            <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown' $disuuid>Traslados <span class='caret'></span></button>
                            <ul class='dropdown-menu'>
                                $optraslados
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class='input-group'>
                        <div class='dropdown'>
                            <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown' $disuuid>Retenciones <span class='caret'></span></button>
                            <ul class='dropdown-menu'>
                                $opretencion
                            </ul>
                        </div>
                    </div>
                </td>
                <td title='Da click para agregar Observaciones' data-bs-toggle='modal' data-bs-target='#modal-observaciones' onclick=\"setIDTMP($id_tmp,'$observaciones');\" style='vertical-align:middle; cursor: pointer; color: #17177C; text-align:center;'>$obser</td>
                <td>
                    <div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Editar'  type='button' data-bs-toggle='dropdown' $disuuid><i class='fas fa-edit'></i> <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-end'>
                            <li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis'  data-bs-toggle='modal' data-bs-target='#editar-producto' onclick='editarConcepto($id_tmp);'>Editar Factura <i class='text-muted small fas fa-edit'></i></a></li>
                            <li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis'  data-bs-toggle='modal' data-bs-target='#nuevo-producto' onclick='editarProductoFactura($idproducto,$id_tmp);'>Editar Productos <i class='text-muted small fas fa-edit'></i></a></li>
                            <li class='notification-link py-1 ps-3' ><a  class='text-decoration-none text-secondary-emphasis' onclick='eliminar($id_tmp,$cantidad,$idproducto); return false;'>Eliminar <i class='text-muted small fas fa-trash'></i></a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            ";
        }

        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);

        $datos .= "</tbody><tfoot><tr>
        <th colspan='3'></th>
        <th>SUBTOTAL</th>
        <th colspan='2'>$ " . number_format(bcdiv($sumador_total, '1', 2), 2, '.', ',') . "</th>
        <th></th></tr>";

        if ($sumador_iva > 0) {
            $datos .= "<tr>
                <th colspan='3'></th>
                <th>TRASLADOS</th>
                <th colspan='2'>$ " . number_format(bcdiv($sumador_iva, '1', 2), 2, '.', ',') . "</th>
                <th></th></tr>";
        }
        if ($sumador_ret > 0) {
            $datos .= "<tr>
                <th colspan='3'></th>
                <th>RETENCIONES</th>
                <th colspan='2'>$ " . number_format(bcdiv($sumador_ret, '1', 2), 2, '.', ',') . "</th>
                <th></th></tr>";
        }
        if ($sumador_descuento > 0) {
            $datos .= "<tr>
                <th colspan='3'></th>
                <th>DESCUENTOS</th>
                <th colspan='2'>$ " . number_format(bcdiv($sumador_descuento, '1', 2), 2, '.', ',') . "</th>
                <th></th></tr>";
        }
        $datos .= "<tr>
        <th colspan='3'></th>
        <th>TOTAL</th>
        <th colspan='2'>$ " . number_format(bcdiv($total_factura, '1', 2), 2, '.', ',') . "</th>
        <th></th></tr></tfoot>";
            return $datos;
        }

    public function gettmppagoAux($idfactura) {
        $consultado = false;
        $consulta = "select min(montoinsoluto_tmp) montoinsoluto from tmppago where idfactura_tmp =$idfactura;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function checktmppago($idfactura) {
        $tmp = $this->gettmppagoAux($idfactura);
        $montoant = "";
        foreach ($tmp as $tmpactual) {
            $montoant = $tmpactual['montoinsoluto'];
        }
        if ($montoant == "") {
            $datos = "";
        } else {
            $datos = "$montoant";
        }
        return $montoant;
    }

    public function agregarObservaciones($t) {
        $consulta = "UPDATE `tmp` set observaciones_tmp=:observaciones where idtmp = :id;";
        $valores = array("id" => $t->getIdtmp(),
            "observaciones" => $t->getObservacionestmp());
        $con = new Consultas();
        $datos = $con->execute($consulta, $valores);
        return $datos;
    }

    public function getDetalle($idfactura) {
        $consultado = false;
        $consulta = "SELECT det.* FROM detalle_factura det WHERE tagdetallef=:id ORDER BY iddetalle_factura ASC";
        $consultas = new Consultas();
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getProductosFactura($id) {
        $consultas = new Consultas();
        $consultado = false;
        $consulta = "SELECT * FROM detalle_factura WHERE tagdetallef=:id ORDER BY iddetalle_factura";
        $val = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function productosFactura($tag, $sessionid) {
        $insertado = false;
        $con = new Consultas();
        $productos = $this->getProductosFactura($tag);
        foreach ($productos as $productoactual) {
            $idproducto = $productoactual["id_producto_servicio"];
            $nombre = $productoactual['factura_producto'];
            $cantidad = $productoactual["cantidad"];
            $precio = $productoactual["precio"];
            $totunitario = $productoactual["totalunitario"];
            $descuento = $productoactual['descuento'];
            $impdescuento = $productoactual['impdescuento'];
            $totdescuento = $productoactual['totaldescuento'];
            $traslados = $productoactual['traslados'];
            $retenciones = $productoactual['retenciones'];
            $observaciones = $productoactual['observacionesproducto'];
            $chinventario = $productoactual['chinv'];
            $clvfiscal = $productoactual['clvfiscal'];
            $clvunidad = $productoactual['clvunidad'];

            $consulta = "INSERT INTO `tmp` VALUES (:id, :idproducto, :nombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :tras, :ret, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
            $valores = array("id" => null,
                "idproducto" => $idproducto,
                "nombre" => $nombre,
                "cantidad" => $cantidad,
                "precio" => $precio,
                "importe" => $totunitario,
                "descuento" => $descuento,
                "impdescuento" => $impdescuento,
                "imptotal" => $totdescuento,
                "tras" => $traslados,
                "ret" => $retenciones,
                "observaciones" => $observaciones,
                "chinv" => $chinventario,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "session" => $sessionid);
            $insertado = $con->execute($consulta, $valores);
        }
        return $insertado;
    }

    private function getTMP($sid) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT tmp.* FROM tmp WHERE tmp.session_id=:sid ORDER by idtmp";
        $val = array("sid" => $sid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getTMPPago($sessionId) {
        $consultado = false;
        $consulta = "SELECT tmp.*, cb.nombre_banco, dat.uuid FROM tmppago tmp inner join catalogo_banco cb on (cb.idcatalogo_banco=tmp.idbanco_tmp) inner join datos_factura dat on (dat.iddatos_factura=tmp.idfactura_tmp) where tmp.sessionid='$sessionId' order by idtmppago";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function verificarProductos($sessionId) {
        $consultado = false;
        $consulta = "SELECT * FROM tmp WHERE session_id=:idsession;";
        $consultas = new Consultas();
        $valores = array("idsession" => $sessionId);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function verificarPagos($sessionid) {
        $consultado = false;
        $consulta = "SELECT * FROM tmppago WHERE sessionid=:idsession;";
        $consultas = new Consultas();
        $valores = array("idsession" => $sessionid);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function checkPagosaux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM pagos WHERE pago_idfactura=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function checkPagos($id) {
        $pagos = $this->checkPagosaux($id);
        $idpago = "";
        foreach ($pagos as $actual) {
            $idpago = $actual['idpago'];
        }
        if ($idpago == "") {
            $datos = "";
        } else {
            $datos = $idpago;
        }
        return $datos;
    }

    public function checkDetallePagosaux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM detalle_pago WHERE idfactura=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function checkDetallePagos($id) {
        $pagos = $this->checkDetallePagosaux($id);
        $idpago = "";
        foreach ($pagos as $actual) {
            $idpago = $actual['iddetalle_pago'];
        }
        if ($idpago == "") {
            $datos = "";
        } else {
            $datos = $idpago;
        }
        return $datos;
    }

    private function genTag($sid) {
        $fecha = getdate();
        $d = $fecha['mday'];
        $m = $fecha['mon'];
        $y = $fecha['year'];
        $h = $fecha['hours'];
        $mi = $fecha['minutes'];
        $sec = $fecha['seconds'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        if ($h < 10) {
            $h = "0$h";
        }
        if ($mi < 10) {
            $mi = "0$mi";
        }
        if ($sec < 10) {
            $sec = "0$sec";
        }

        $idusuario = $_SESSION[sha1("idusuario")];

        $dtag = $m . $y . $d . $h . $mi . $sec;
        $ranstr = "";
        $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
        $charsize = strlen($chars);
        for ($r = 0; $r < 8; $r++) {
            $ranstr .= $chars[rand(0, $charsize - 1)];
        }

        $tag = $ranstr . $dtag . $idusuario . $sid;
        return $tag;
    }

    private function validarFactura($sid) {
        $validar = false;
        $prod = 0;
        $productos = $this->verificarProductos($sid);
        foreach ($productos as $actual) {
            $prod ++;
        }
        if ($prod == 0) {
            $validar = true;
            echo "0debes agregar un producto o selecionar un concepto.";
        }
        return $validar;
    }

    public function nuevoFactura($c) {
        $insertado = false;
        $validar = $this->validarFactura($c->getSessionid());
        if (!$validar) {
            $insertado = $this->insertarFactura($c);
        }
        return $insertado;
    }

    private function updateFolioConsecutivo($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "UPDATE folio SET consecutivo=(consecutivo+1) WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getFoliobyID($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM folio WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getFolio($id) {
        $datos = "";
        $folios = $this->getFoliobyID($id);
        foreach ($folios as $actual) {
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
            $update = $this->updateFolioConsecutivo($id);
        }
        return $datos;
    }

    private function insertarFactura($f) {
        $insertado = false;
        $hoy = date("Y-m-d");
        $tag = $this->genTag($f->getSessionid());

        $folios = $this->getFolio($f->getFolio());
        $Fdiv = explode("</tr>", $folios);
        $serie = $Fdiv[0];
        $letra = $Fdiv[1];
        $nfolio = $Fdiv[2];

        $consulta = "INSERT INTO `datos_factura` VALUES (:id, :fecha, :rfcemisor, :rzsocial, :clvregimen, :regimen, :codp, :serie, :letra, :folio, :idcliente, :cliente, :rfcreceptor, :rzreceptor, :dirreceptor, :cpreceptor, :regfiscalreceptor, :chfirmar, :cadena, :certSAT, :certCFDI, :uuid, :selloSAT, :sellocfdi, :fechatimbrado, :qrcode, :cfdistring, :cfdicancel, :status, :idmetodopago, :idformapago, :idmoneda, :tcambio, :iduso, :tipocomprobante, :tipofactura, :iddatosfacturacion, :cfdis, :subtotal, :subiva, :subret, :totdescuentos, :total, :chperiodo, :periodocobro, :tag, :periodo, :mes, :anho);";
        $valores = array("id" => null,
            "fecha" => $hoy,
            "rfcemisor" => '',
            "rzsocial" => '',
            "clvregimen" => '',
            "regimen" => '',
            "codp" => '',
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "idcliente" => $f->getIdcliente(),
            "cliente" => $f->getCliente(),
            "rfcreceptor" => $f->getRfccliente(),
            "rzreceptor" => $f->getRzcliente(),
            "dirreceptor" => $f->getDircliente(),
            "cpreceptor" => $f->getCodpostal(),
            "regfiscalreceptor" => $f->getRegfiscalcliente(),
            "chfirmar" => $f->getChfirmar(),
            "cadena" => null,
            "certSAT" => null,
            "certCFDI" => null,
            "uuid" => null,
            "selloSAT" => null,
            "sellocfdi" => null,
            "fechatimbrado" => null,
            "qrcode" => null,
            "cfdistring" => null,
            "cfdicancel" => null,
            "status" => '2',
            "idmetodopago" => $f->getIdmetodopago(),
            "idformapago" => $f->getIdformapago(),
            "idmoneda" => $f->getIdmoneda(),
            "tcambio" => $f->getTcambio(),
            "iduso" => $f->getIdusocfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "tipofactura" => '2',
            "iddatosfacturacion" => $f->getIddatosfacturacion(),
            "cfdis" => $f->getCfdisrel(),
            "subtotal" => null,
            "subiva" => null,
            "subret" => null,
            "totdescuentos" => null,
            "total" => null,
            "chperiodo" => '0',
            "periodocobro" => '0',
            "tag" => $tag,
            "periodo" => $f->getPeriodicidad(),
            "mes" => $f->getMesperiodo(),
            "anho" => $f->getAnoperiodo());
        $con = new Consultas();
        $insertado = $con->execute($consulta, $valores);

        $this->detalleFactura($f->getSessionid(), $tag);
        if ($f->getCfdisrel() == '1') {
            $this->detalleCFDIS($f->getSessionid(), $tag);
        }
        if ($f->getIdcotizacion() != "") {
            $this->actualizarCotizacion($f->getIdcotizacion(), $tag);
        }
        $datos = '<corte>' . $tag . '<corte>';
        return $datos;
    }

    private function detalleFactura($idsession, $tag) {
        $sumador_total = 0;
        $sumador_iva = 0;   
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $con = new Consultas();
        $productos = $this->getTMP($idsession);
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmp'];
            $idproducto = $productoactual['id_productotmp'];
            $cantidad = $productoactual['cantidad_tmp'];
            $pventa = $productoactual['precio_tmp'];
            $nombre = $productoactual['tmpnombre'];
            $ptotal = $productoactual['totunitario_tmp'];
            $descuento = $productoactual['descuento_tmp'];
            $impdescuento = $productoactual['impdescuento_tmp'];
            $imptotal = $productoactual['imptotal_tmp'];
            $observaciones = $productoactual['observaciones_tmp'];
            $traslados = $productoactual['trasladotmp'];
            $retencion = $productoactual['retenciontmp'];
            $chinv = $productoactual['chinventariotmp'];
            $clvfiscal = $productoactual['clvfiscaltmp'];
            $clvunidad = $productoactual['clvunidadtmp'];

            $tras = 0;
            $divT = explode("<impuesto>", $traslados);
            foreach ($divT as $tactual) {
                $impuestos = $tactual;
                $div = explode("-", $impuestos);
                $tras += (bcdiv($div[0], '1', 2));
            }

            $ret = 0;
            $divR = explode("<impuesto>", $retencion);
            foreach ($divR as $ractual) {
                $impuestos = $ractual;
                $div = explode("-", $impuestos);
                $ret += (bcdiv($div[0], '1', 2));
            }

            $sumador_iva += bcdiv($tras, '1', 2);
            $sumador_ret += bcdiv($ret, '1', 2);
            $sumador_total += bcdiv($ptotal, '1', 2);
            $sumador_descuento += bcdiv($impdescuento, '1', 2);
            $consulta2 = "INSERT INTO `detalle_factura` VALUES (:id,:cantidad,:precio, :subtotal, :descuento, :impdescuento, :totdescuento, :traslados, :retenciones, :observaciones, :idproducto, :nombreprod, :chinv, :clvfiscal, :clvunidad, :iddatosfactura);";
            $valores2 = array("id" => null,
                "cantidad" => $cantidad,
                "precio" => bcdiv($pventa, '1', 2),
                "subtotal" => bcdiv($ptotal, '1', 2),
                "descuento" => bcdiv($descuento, '1', 2),
                "impdescuento" => bcdiv($impdescuento, '1', 2),
                "totdescuento" => bcdiv($imptotal, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retencion,
                "observaciones" => $observaciones,
                "idproducto" => $idproducto,
                "nombreprod" => $nombre,
                "chinv" => $chinv,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "iddatosfactura" => $tag);

            $insertado = $con->execute($consulta2, $valores2);
        }
        $totaltraslados = $this->checkArray($idsession, '1');
        $totalretencion = $this->checkArray($idsession, '2');
        $borrar = "DELETE FROM `tmp` WHERE session_id=:id;";
        $valores3 = array("id" => $idsession);
        $eliminado = $con->execute($borrar, $valores3);

        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);
        $update = "UPDATE `datos_factura` set subtotal=:subtotal, subtotaliva=:iva, subtotalret=:ret, totaldescuentos=:totdesc, totalfactura=:total WHERE tagfactura=:tag;";
        $valores4 = array("tag" => $tag,
            "subtotal" => bcdiv($sumador_total, '1', 2),
            "iva" => $totaltraslados,
            "ret" => $totalretencion,
            "totdesc" => bcdiv($sumador_descuento, '1', 2),
            "total" => bcdiv($total_factura, '1', 2));
        $insertado = $con->execute($update, $valores4);
        return $insertado;
    }

    private function detalleCFDIS($idsession, $tag) {
        $cfdi = $this->getTMPCFDIS($idsession);
        foreach ($cfdi as $actual) {
            $idtmpcfdi = $actual['idtmpcfdi'];
            $tiporel = $actual['tiporel'];
            $uuid = $actual['uuid'];

            $con = new Consultas();
            $consulta2 = "INSERT INTO `cfdirelacionado` VALUES (:id, :tiporel, :uuid, :tag);";
            $valores2 = array("id" => null,
                "tiporel" => $tiporel,
                "uuid" => $uuid,
                "tag" => $tag);
            $insertado = $con->execute($consulta2, $valores2);
        }
        $cfdi = $this->deleteTMPCFDI($idsession);
        return $insertado;
    }

    private function actualizarCotizacion($idcot, $idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "UPDATE `datos_cotizacion` SET expfactura=:exp WHERE iddatos_cotizacion=:id;";
        $valores = array("id" => $idcot, 
            "exp" => $idfactura);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getIDFacturaAux($folio, $iddatos) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT iddatos_factura FROM datos_factura WHERE folio_interno_fac=:folio AND iddatosfacturacion=:iddatos;";
        $valores = array("folio" => $folio,
            "iddatos" => $iddatos);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getIDFactura($folio, $iddatos) {
        $idfactura = "";
        $facturas = $this->getIDFacturaAux($folio, $iddatos);
        foreach ($facturas as $facturaactual) {
            $idfactura = $facturaactual['iddatos_factura'];
        }
        return $idfactura;
    }

    public function removerInventario($idproducto, $cantidad) {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv-:cantidad where idproser=:idproducto;";
        $consultas = new Consultas();
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function restaurarInventario($idproducto, $cantidad) {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv+:cantidad where idproser=:idproducto;";
        $consultas = new Consultas();
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function restaurarInvCant($idproducto, $cantidad) {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=:cantidad where idproser=:idproducto;";
        $consultas = new Consultas();
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function estadoFactura($idfactura) {
        $actualizado = false;
        $consulta = "UPDATE `datos_factura` SET status_pago=:estado, cfdicancel=:cfdi WHERE iddatos_factura=:id;);";
        $valores = array("id" => $idfactura,
            "estado" => '2',
            "cfdi" => '');
        $con = new Consultas();
        $actualizado = $con->execute($consulta, $valores);
        return $actualizado;
    }

    public function getFecha() {
        $datos = "";
        $fecha = getdate();
        $d = $fecha['mday'];
        $m = $fecha['mon'];
        $y = $fecha['year'];
        if ($d < 10) {
            $d = "0$d";
        }
        if ($m < 10) {
            $m = "0$m";
        }
        $datos = "$d/$m/$y";
        return $datos;
    }

    public function getParcialidad($idfactura) {
        $datos = "";
        $parcialidad = $this->getParcialidadAux($idfactura);
        foreach ($parcialidad as $p) {
            $datos = $p['par'];
        }
        if ($datos == "") {
            $datos = "1";
        }
        return $datos;
    }

    public function getParcialidadAux($idfactura) {
        $consultado = false;
        $consulta = "SELECT (noparcialidad)+1 as par FROM pagos where pago_idfactura=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosEmisor($fid) {
        $datos = "";
        $sine = $this->getDatosFacturacionbyId($fid);
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

    public function getFacturaById($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM datos_factura dat INNER JOIN catalogo_metodo_pago cmp ON (dat.id_metodo_pago= cmp.idmetodo_pago) 
                INNER JOIN catalogo_pago cp ON (dat.id_forma_pago = cp.idcatalogo_pago) 
                INNER JOIN catalogo_moneda cm ON (cm.idcatalogo_moneda = dat.id_moneda)
                INNER JOIN catalogo_uso_cfdi cuc ON (dat.id_uso_cfdi= cuc.iduso_cfdi) 
                INNER JOIN catalogo_comprobante cc ON (dat.id_tipo_comprobante=cc.idcatalogo_comprobante) 
                INNER JOIN datos_facturacion df ON (df.id_datos=dat.iddatosfacturacion) WHERE dat.iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getFacturaPagoById2($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "select p.*, dat.iddatos_factura, dat.folio_interno_fac,dat.uuid, dat.id_metodo_pago,dat.id_forma_pago from pagos p inner join datos_factura dat on (p.pago_idfactura=dat.iddatos_factura) where p.pago_idfactura = '$idfactura' order by idpago desc limit 1;";
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getPagoById($idpago) {
        $consultado = false;
        $consulta = "SELECT p.*, dat.folio_interno_fac, dat.id_metodo_pago, cp.descripcion_pago FROM pagos p inner join datos_factura dat on (p.pago_idfactura=dat.iddatos_factura) inner join catalogo_pago cp on (p.id_formapago=cp.idcatalogo_pago) where idpago='$idpago';";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getMontoAnteriorAux($noparc, $idfactura) {
        $consultado = false;
        $consulta = "select montoinsoluto from pagos where noparcialidad=$noparc and pago_idfactura=$idfactura;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getMontoAnterior($noparc, $idfactura) {
        $total = $this->getMontoAnteriorAux($noparc, $idfactura);
        $totalfactura = "";
        foreach ($total as $actual) {
            $totalfactura = $actual['montoinsoluto'];
        }
        return $totalfactura;
    }

    private function getNombancoaux($idbanco) {
        $consultado = false;
        $consulta = "select nombre_banco from catalogo_banco where idcatalogo_banco=:bid;";
        $consultas = new Consultas();
        $val = array("bid" => $idbanco);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getNomBanco($idbanco) {
        $banco = $this->getNombancoaux($idbanco);
        $nombre = "";
        foreach ($banco as $bactual) {
            $nombre = $bactual['nombre_banco'];
        }
        return $nombre;
    }

    public function getDatosPago($idpago) {
        $pago = $this->getPagoById($idpago);
        $datos = "";
        foreach ($pago as $pagoactual) {
            $idpago = $pagoactual['idpago'];
            $foliopago = $pagoactual['foliopago'];
            $noparcialidad = $pagoactual['noparcialidad'];
            $idfactura = $pagoactual['pago_idfactura'];
            $idcliente = $pagoactual['pago_idcliente'];
            $fechapago = $pagoactual['fechapago'];
            $monto = $pagoactual['monto'];
            $idformapago = $pagoactual['id_formapago'];
            $formapago = $pagoactual['descripcion_pago'];
            $parcialidadanterior = $noparcialidad - 1;
            if ($parcialidadanterior == '0') {
                $montoanterior = $pagoactual['montoanterior'];
            } else {
                $montoanterior = $this->getMontoAnterior($parcialidadanterior, $idfactura);
            }

            $montoinsoluto = $pagoactual['montoinsoluto'];
            $total = $pagoactual['total'];
            $idbanco = $pagoactual['idbanco'];
            $cuenta = $pagoactual['cuenta'];
            $nombrebanco = "";
            if ($idbanco != '0') {
                $nombrebanco = $this->getNomBanco($idbanco);
            }
            $notransaccion = $pagoactual['notransaccion'];
            $foliofactura = $pagoactual['folio_interno_fac'];
            $idmetodo = $pagoactual['id_metodo_pago'];
            $datos = "$idpago</tr>$idfactura</tr>$idcliente</tr>$foliopago</tr>$foliofactura</tr>$noparcialidad</tr>$fechapago</tr>$monto</tr>$montoanterior</tr>$montoinsoluto</tr>$total</tr>$idbanco</tr>$cuenta</tr>$nombrebanco</tr>$notransaccion</tr>$idmetodo</tr>$idformapago</tr>$formapago";
            break;
        }
        return $datos;
    }

    private function getFacturaPagoById($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT f.*, df.nombre_contribuyente FROM datos_factura f INNER JOIN datos_facturacion df ON (f.iddatosfacturacion=df.id_datos) WHERE iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosFacPago($idfactura) {
        $factura = $this->getFacturaPagoById($idfactura);
        $datos = "";
        foreach ($factura as $facturaactual) {
            $idfactura = $facturaactual['iddatos_factura'];
            $folio = $facturaactual['letra'] . $facturaactual['folio_interno_fac'];
            $idcliente = $facturaactual['idcliente'];
            $nombrecliente = $this->getNombreCliente($idcliente);
            $rfcreceptor = $facturaactual['rfcreceptor'];
            $rzreceptor = $facturaactual['rzreceptor'];
            $cpreceptor = $facturaactual['cpreceptor'];
            $regfiscalreceptor = $facturaactual['regfiscalreceptor'];
            $iddatosfacturacion = $facturaactual['iddatosfacturacion'];
            $nombrecontribuyente = $facturaactual['nombre_contribuyente'];
            $idformapago = $facturaactual['id_forma_pago'];
            $idmoneda = $facturaactual['id_moneda'];
            $tcambio = $facturaactual['tcambio'];

            $datos = "$idfactura</tr>$folio</tr>$idcliente</tr>$nombrecliente</tr>$rfcreceptor</tr>$rzreceptor</tr>$cpreceptor</tr>$regfiscalreceptor</tr>$iddatosfacturacion</tr>$nombrecontribuyente</tr>$idformapago</tr>$idmoneda</tr>$tcambio";
            break;
        }
        return $datos;
    }

    public function getDatosFacPago2($idfactura) {
        $factura = $this->getFacturaPagoById2($idfactura);
        $datos = "";
        foreach ($factura as $facturaactual) {
            $idfactura = $facturaactual['iddatos_factura'];
            $folio = $facturaactual['folio_interno_fac'];
            $idcliente = $facturaactual['pago_idcliente'];
            $uuid = $facturaactual['uuid'];
            $totalfactura = $facturaactual['montoinsoluto'];
            $idmetodopago = $facturaactual['id_metodo_pago'];
            $idformapago = $facturaactual['id_forma_pago'];
            $datos = "$idfactura</tr>$folio</tr>$idcliente</tr>$uuid</tr>$totalfactura</tr>$idmetodopago</tr>$idformapago";
            break;
        }
        return $datos;
    }

    public function getClienteExportar($idcliente) {
        $datos = "";
        $clientes = $this->getClientebyID($idcliente);
        foreach ($clientes as $actual) {
            $nombre = $actual['nombre'] . " " . $actual['apaterno'] . "-" . $actual['nombre_empresa'];
            $rfc = $actual['rfc'];
            $razonsocial = $actual['razon_social'];
            $regimen = $actual['regimen_cliente'];
            $codpostal = $actual['codigo_postal'];

            $datos = "$nombre</tr>$rfc</tr>$razonsocial</tr>$regimen</tr>$codpostal";
        }
        return $datos;
    }

    private function getClientebyID($idcliente) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM cliente WHERE id_cliente=:cid;";
        $val = array("cid" => $idcliente);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    private function getNombreCliente($idcliente) {
        $nombre = "";
        $datos = $this->getClientebyID($idcliente);
        foreach ($datos as $actual) {
            $nombre = $actual['nombre'] . " " . $actual['apaterno'] . "-" . $actual['nombre_empresa'];
        }
        return $nombre;
    }

    private function getFacturaEditar($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM datos_factura dat WHERE dat.iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosFactura($idFactura) {
        $factura = $this->getFacturaEditar($idFactura);
        $datos = "";
        foreach ($factura as $facturaactual) {
            $idfactura = $facturaactual['iddatos_factura'];
            $serie = $facturaactual['serie'];
            $letra = $facturaactual['letra'];
            $folio = $facturaactual['folio_interno_fac'];
            $fecha_creacion = $facturaactual['fecha_creacion'];
            $idcliente = $facturaactual['idcliente'];
            $cliente = $facturaactual['nombrecliente'];
            $rfccliente = $facturaactual['rfcreceptor'];
            $rzreceptor = $facturaactual['rzreceptor'];
            $cpreceptor = $facturaactual['cpreceptor'];
            $regfiscalrec = $facturaactual['regfiscalreceptor'];
            $idforma_pago = $facturaactual['id_forma_pago'];
            $idmetodo_pago = $facturaactual['id_metodo_pago'];
            $idmoneda = $facturaactual['id_moneda'];
            $iduso_cfdi = $facturaactual['id_uso_cfdi'];
            $idtipo_comprobante = $facturaactual['id_tipo_comprobante'];
            $status = $facturaactual['status_pago'];
            $uuid = $facturaactual['uuid'];
            $iddatos = $facturaactual['iddatosfacturacion'];
            $chfirmar = $facturaactual['chfirmar'];
            $cfdisrel = $facturaactual['cfdisrel'];
            $tcambio = $facturaactual['tcambio'];
            $rfc = $facturaactual['factura_rfcemisor'];
            $rzsocial = $facturaactual['factura_rzsocial'];
            $clvreg = $facturaactual['factura_clvregimen'];
            $regimen = $facturaactual['factura_regimen'];
            $tag = $facturaactual['tagfactura'];
            $dirreceptor = $facturaactual['dircliente'];
            $periodoglobal = $facturaactual['periodoglobal'];
            $mesperiodo = $facturaactual['mesperiodo'];
            $anhoperiodo = $facturaactual['anhoperiodo'];
        	$cpemisor = $facturaactual['factura_cpemisor'];

            $datos = "$idfactura</tr>$serie</tr>$letra</tr>$folio</tr>$fecha_creacion</tr>$idcliente</tr>$cliente</tr>$rfccliente</tr>$rzreceptor</tr>$cpreceptor</tr>$regfiscalrec</tr>$idmetodo_pago</tr>$idmoneda</tr>$iduso_cfdi</tr>$idforma_pago</tr>$idtipo_comprobante</tr>$status</tr>$uuid</tr>$iddatos</tr>$chfirmar</tr>$cfdisrel</tr>$tcambio</tr>$rfc</tr>$rzsocial</tr>$clvreg</tr>$regimen</tr>$tag</tr>$dirreceptor</tr>$periodoglobal</tr>$mesperiodo</tr>$anhoperiodo</tr>$cpemisor";
            break;
        }
        return $datos;
    }

    public function checkArray($idsession, $idimpuesto) {
        $productos = $this->getTMP($idsession);
        $imptraslados = $this->getImpuestos($idimpuesto);
        $row = array();
        foreach ($imptraslados as $tactual) {
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $Timp = 0;

            foreach ($productos as $productoactual) {
                if ($idimpuesto == '1') {
                    $traslados = $productoactual['trasladotmp'];
                } else if ($idimpuesto == '2') {
                    $traslados = $productoactual['retenciontmp'];
                }
                if ($traslados != "") {
                    $div = explode("<impuesto>", $traslados);
                    foreach ($div as $d) {
                        $div2 = explode("-", $d);
                        if ($porcentaje == $div2[1] && $impuesto == $div2[2]) {
                            $Timp += $div2[0];
                        }
                    }
                }
            }
            if ($Timp > 0) {
                $row[] = bcdiv($Timp, '1', 2) . '-' . $porcentaje . '-' . $impuesto;
            }
        }

        $trasarray = implode("<impuesto>", $row);
        return $trasarray;
    }

    public function modificarFactura($c) {
        $insertado = false;
        $validar = $this->validarFactura($c->getSessionid());
        if (!$validar) {
            $insertado = $this->actualizarFactura($c);
        }
        return $insertado;
    }

    public function actualizarFactura($f) {
        $actualizado = false;
        $con = new Consultas();

        $updfolio = "";
        $serie = "";
        $letra = "";
        $nfolio = "";
        if ($f->getFolio() != '0') {
            $updfolio = "serie=:serie, letra=:letra, folio_interno_fac=:folio,";
            $folios = $this->getFolio($f->getFolio());
            $Fdiv = explode("</tr>", $folios);
            $serie = $Fdiv[0];
            $letra = $Fdiv[1];
            $nfolio = $Fdiv[2];
        }

        $consulta = "UPDATE `datos_factura` SET $updfolio idcliente=:idcliente, nombrecliente=:cliente, rfcreceptor=:rfcreceptor, rzreceptor=:rzreceptor, dircliente=:dircliente, cpreceptor=:cpreceptor, regfiscalreceptor=:regfiscalreceptor, chfirmar=:chfirmar, id_metodo_pago=:idmetodopago,id_forma_pago=:idformapago,id_moneda=:idmoneda, tcambio=:tcambio, id_uso_cfdi=:iduso,id_tipo_comprobante=:tipocomprobante, iddatosfacturacion=:iddatos, cfdisrel=:cfdisrel, periodoglobal=:periodo, mesperiodo=:mesp, anhoperiodo=:anhop WHERE iddatos_factura=:idfactura;";
        $valores = array("idfactura" => $f->getIddatos_factura(),
            /*"rfc" => $rfc,
            "rzsocial" => $rzsocial,
            "clvreg" => $clvreg,
            "regimen" => $regimen,
            "codp" => $codpos,*/
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "idcliente" => $f->getIdcliente(),
            "cliente" => $f->getCliente(),
            "rfcreceptor" => $f->getRfccliente(),
            "rzreceptor" => $f->getRzcliente(),
            "dircliente" => $f->getDircliente(),
            "cpreceptor" => $f->getCodpostal(),
            "regfiscalreceptor" => $f->getRegfiscalcliente(),
            "chfirmar" => $f->getChfirmar(),
            "idmetodopago" => $f->getIdmetodopago(),
            "idformapago" => $f->getIdformapago(),
            "idmoneda" => $f->getIdmoneda(),
            "tcambio" => $f->getTcambio(),
            "iduso" => $f->getIdusocfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "iddatos" => $f->getIddatosfacturacion(),
            "cfdisrel" => $f->getCfdisrel(),
            "periodo" => $f->getPeriodicidad(),
            "mesp" => $f->getMesperiodo(),
            "anhop" => $f->getAnoperiodo());

        $actualizado = $con->execute($consulta, $valores);
        $datos = $this->actualizarDetalle($f->getSessionid(), $f->getTag());
        $cfdi = $this->actualizarCFDIS($f->getSessionid(), $f->getTag());
        return $actualizado;
    }

    public function actualizarDetalle($idsession, $tag) {
        $insertado = false;
        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
    	
        $borrar = $this->eliminarFacturaAux($tag);
        $productos = $this->getTMP($idsession);
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmp'];
            $idproducto = $productoactual['id_productotmp'];
            $cantidad = $productoactual['cantidad_tmp'];
            $pventa = $productoactual['precio_tmp'];
            $nombre = $productoactual['tmpnombre'];
            $ptotal = $productoactual['totunitario_tmp'];
            $descuento = $productoactual['descuento_tmp'];
            $impdescuento = $productoactual['impdescuento_tmp'];
            $imptotal = $productoactual['imptotal_tmp'];
            $observaciones = $productoactual['observaciones_tmp'];
            $traslados = $productoactual['trasladotmp'];
            $retencion = $productoactual['retenciontmp'];
            $chinv = $productoactual['chinventariotmp'];
            $clvfiscal = $productoactual['clvfiscaltmp'];
            $clvunidad = $productoactual['clvunidadtmp'];

            $tras = 0;
            $divT = explode("<impuesto>", $traslados);
            foreach ($divT as $tactual) {
                $impuestos = $tactual;
                $div = explode("-", $impuestos);
                $tras += (bcdiv($div[0], '1', 2));
            }

            $ret = 0;
            $divR = explode("<impuesto>", $retencion);
            foreach ($divR as $ractual) {
                $impuestos = $ractual;
                $div = explode("-", $impuestos);
                $ret += (bcdiv($div[0], '1', 2));
            }

            $sumador_iva += bcdiv($tras, '1', 2);
            $sumador_ret += bcdiv($ret, '1', 2);
            $sumador_total += bcdiv($ptotal, '1', 2);
            $sumador_descuento += bcdiv($impdescuento, '1', 2);
            $consulta2 = "INSERT INTO `detalle_factura` VALUES (:id,:cantidad,:precio, :subtotal, :descuento, :impdescuento, :totdescuento, :traslados, :retenciones, :observaciones, :idproducto, :nombreprod, :chinv, :clvfiscal, :clvunidad, :iddatosfactura);";
            $valores2 = array("id" => null,
                "cantidad" => $cantidad,
                "precio" => bcdiv($pventa, '1', 2),
                "subtotal" => bcdiv($ptotal, '1', 2),
                "descuento" => bcdiv($descuento, '1', 2),
                "impdescuento" => bcdiv($impdescuento, '1', 2),
                "totdescuento" => bcdiv($imptotal, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retencion,
                "observaciones" => $observaciones,
                "idproducto" => $idproducto,
                "nombreprod" => $nombre,
                "chinv" => $chinv,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "iddatosfactura" => $tag);
                $con = new Consultas();
            $insertado = $con->execute($consulta2, $valores2);
        }

        $con = new Consultas();
        $totaltraslados = $this->checkArray($idsession, '1');
        $totalretencion = $this->checkArray($idsession, '2');
        $borrar = "DELETE FROM `tmp` WHERE session_id=:id;";
        $valores3 = array("id" => $idsession);
        $eliminado = $con->execute($borrar, $valores3);

        $con = new Consultas();
        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);
        $update = "UPDATE `datos_factura` SET subtotal=:subtotal, subtotaliva=:iva, subtotalret=:ret, totaldescuentos=:totdesc, totalfactura=:total WHERE tagfactura=:tag;";
        $valores4 = array("tag" => $tag,
            "subtotal" => bcdiv($sumador_total, '1', 2),
            "iva" => $totaltraslados,
            "ret" => $totalretencion,
            "totdesc" => bcdiv($sumador_descuento, '1', 2),
            "total" => bcdiv($total_factura, '1', 2));
        $insertado = $con->execute($update, $valores4);
        return $insertado;
    }

    private function actualizarCFDIS($idsession, $tag) {
        $insertado = false;
        
        $this->eliminarCFDIAux($tag);

        $cfdi = $this->getTMPCFDIS($idsession);
        foreach ($cfdi as $actual) {
            $idtmpcfdi = $actual['idtmpcfdi'];
            $tiporel = $actual['tiporel'];
            $uuid = $actual['uuid'];

            $consulta2 = "INSERT INTO `cfdirelacionado` VALUES (:id, :tiporel, :uuid, :tag);";
            $valores2 = array("id" => null,
                "tiporel" => $tiporel,
                "uuid" => $uuid,
                "tag" => $tag);
                $con = new Consultas();
            $insertado = $con->execute($consulta2, $valores2);
        }
        $cfdi = $this->deleteTMPCFDI($idsession);
        return $insertado;
    }

    private function getTagbyID($id) {
        $consultado = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM datos_factura WHERE iddatos_factura=:id";
        $val = array("id" => $id);
        $consultado = $con->getResults($consulta, $val);
        return $consultado;
    }

    private function getTagAux($id) {
        $tag = "";
        $datos = $this->getTagbyID($id);
        foreach ($datos as $actual) {
            $tag = $actual['tagfactura'];
        }
        return $tag;
    }

    public function eliminarFactura($idfactura) {
        $eliminado = false;
        $consultas = new Consultas();
        $tag = $this->getTagAux($idfactura);
        $consulta = "DELETE FROM `datos_factura` WHERE tagfactura=:tag;";
        $valores = array("tag" => $tag);
        $eliminado = $consultas->execute($consulta, $valores);
        $this->eliminarFacturaAux($tag);
        $this->eliminarCFDIAux($tag);
        return $eliminado;
    }

    private function eliminarFacturaAux($idFactura) {
        $eliminado = false;
        $consultas = new Consultas();
        $consulta = "DELETE FROM `detalle_factura` WHERE tagdetallef=:id;";
        $valores = array("id" => $idFactura);
        $eliminado = $consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function eliminarCFDIAux($tag) {
        $con = new Consultas();
        $eliminado = false;
        $borrar = "DELETE FROM `cfdirelacionado` WHERE cfditag=:tag;";
        $borrarvalores = array("tag" => $tag);
        $eliminado = $con->execute($borrar, $borrarvalores);
        return $eliminado;
    }

    private function getTMPById($idtmp) {
        $consultado = false;
        $consulta = "SELECT * FROM tmp WHERE idtmp=:id";
        $val = array("id" => $idtmp);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosTMP($idtmp) {
        $tmp = $this->getTMPById($idtmp);
        $datos = "";
        foreach ($tmp as $actual) {
            $idtmp = $actual['idtmp'];
            $nombre = $actual['tmpnombre'];
            $cantidad = $actual['cantidad_tmp'];
            $precio = $actual['precio_tmp'];
            $totunitario = $actual['totunitario_tmp'];
            $descuento = $actual['descuento_tmp'];
            $impdescuento = $actual['impdescuento_tmp'];
            $total = $actual['imptotal_tmp'];
            $observaciones = $actual['observaciones_tmp'];
            $traslados = $actual['trasladotmp'];
            $retencion = $actual['retenciontmp'];
            $clvfiscal = $actual['clvfiscaltmp'];
            $clvunidad = $actual['clvunidadtmp'];

            $imptraslados = $this->getImpuestos('1');
            $checktraslado = "";
            if ($traslados != "") {
                $divtras = explode("<impuesto>", $traslados);
                foreach ($divtras as $tras) {
                    $impuestos = $tras;
                    $div = explode("-", $impuestos);
                    $checktraslado .= $div[1] . "-" . $div[2] . "<imp>";
                }
            }

            $checkedT = "";
            $iconT = "";
            $optraslados = "";
            foreach ($imptraslados as $tactual) {
                $divcheck = explode("<imp>", $checktraslado);
                foreach ($divcheck as $t) {
                    if ($t == $tactual['porcentaje'] . "-" . $tactual['impuesto']) {
                        $iconT = "fa fa-check";
                        $checkedT = "checked";
                        break;
                    } else {
                        $iconT = "glyphicon-unchecked";
                        $checkedT = "";
                    }
                }
                $optraslados .= "<li data-location='edit' data-id='$idtmp'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedT value='" . $tactual['porcentaje'] . "' name='chtrasedit' data-impuesto='" . $tactual['impuesto'] . "' data-tipo='" . $tactual['tipoimpuesto'] . "'/><span class='glyphicon $iconT' id='chuso1span'></span>" . $tactual['nombre'] . " (" . $tactual['porcentaje'] . "%)" . "</label></li>";
            }

            $checkretencion = "";
            $impretenciones = $this->getImpuestos('2');
            if ($retencion != "") {
                $divret = explode("<impuesto>", $retencion);
                foreach ($divret as $retn) {
                    $retenciones = $retn;
                    $divr = explode("-", $retenciones);
                    $checkretencion .= $divr[1] . "-" . $divr[2] . "<imp>";
                }
            }

            $checkedR = "";
            $iconR = "";
            $opretencion = "";
            foreach ($impretenciones as $ractual) {
                $divcheckR = explode("<imp>", $checkretencion);
                foreach ($divcheckR as $r) {
                    if ($r == $ractual['porcentaje'] . "-" . $ractual['impuesto']) {
                        $iconR = "fa fa-check";
                        $checkedR = "checked";
                        break;
                    } else {
                        $iconR = "glyphicon-unchecked";
                        $checkedR = "";
                    }
                }
                $opretencion .= "<li data-location='edit' data-id='$idtmp'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedR value='" . $ractual['porcentaje'] . "' name='chretedit' data-impuesto='" . $ractual['impuesto'] . "' data-tipo='" . $ractual['tipoimpuesto'] . "'/><span class='glyphicon $iconR' id='chuso1span'></span>" . $ractual['nombre'] . " (" . $ractual['porcentaje'] . "%)" . "</label></li>";
            }

            $datos = "$idtmp</tr>$nombre</tr>$cantidad</tr>$precio</tr>$totunitario</tr>$descuento</tr>$impdescuento</tr>$total</tr>$observaciones</tr>$clvfiscal</tr>$clvunidad</tr>$optraslados</tr>$opretencion";
            break;
        }
        return $datos;
    }

    public function getDatosProducto($id) {
        $tmp = $this->getProductoById($id);
        $datos = "";
        foreach ($tmp as $productoactual) {
            $id_producto = $productoactual['idproser'];
            $codigo = $productoactual['codproducto'];
            $nombre = addslashes($productoactual['nombre_producto']);
            $clvunidad = $productoactual['clv_unidad'];
            $descripcion_unidad = $productoactual['desc_unidad'];
            $descripcion_producto = addslashes($productoactual['descripcion_producto']);
            $pcompra = $productoactual['precio_compra'];
            $porcentaje = $productoactual['porcentaje'];
            $ganancia = $productoactual['ganancia'];
            $pventa = $productoactual['precio_venta'];
            $tipo = $productoactual['tipo'];
            $clavefiscal = $productoactual['clave_fiscal'];
            $descripcion = $productoactual['desc_fiscal'];
            $idproveedor = $productoactual['idproveedor'];
            $empresa = $this->getProveedor($idproveedor);
            $imagen = $productoactual['imagenprod'];
            $chinventario = $productoactual['chinventario'];
            $cantidad = $productoactual['cantinv'];
            $datos .= "$id_producto</tr>$codigo</tr>$nombre</tr>$clvunidad</tr>$descripcion_unidad</tr>$descripcion_producto</tr>$pcompra</tr>$porcentaje</tr>$ganancia</tr>$pventa</tr>$tipo</tr>$clavefiscal</tr>$descripcion</tr>$idproveedor</tr>$empresa</tr>$imagen</tr>$chinventario</tr>$cantidad";
            break;
        }
        return $datos;
    }

    private function getProveedorAux($pid) {
        $datos = false;
        $con = new Consultas();
        $consulta = "SELECT * FROM proveedor WHERE idproveedor=:pid";
        $val = array("pid" => $pid);
        $datos = $con->getResults($consulta, $val);
        return $datos;
    }

    private function getProveedor($pid) {
        $nombre = "";
        $datos = $this->getProveedorAux($pid);
        foreach ($datos as $actual) {
            $nombre = $actual['empresa'];
        }
        return $nombre;
    }

    public function checkConcepto($t) {
        $cant = $t->getCantidadtmp();
        $check = $this->checkProductoTMPAux($t->getIdtmp());
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $idproducto = $actual['id_productotmp'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
        }
        $restante = ($cantidad + $canttmp) - $cant;
        if ($chinv == '1') {
            if ($restante < 0) {
                $datos = "0El inventario no es suficiente para agregar mas producto";
            } else {
                $datos = $this->actualizarConcepto($t);
                $inv = $this->restaurarInvCant($idproducto, $restante);
            }
        } else if ($chinv == '0') {
            $datos = $this->actualizarConcepto($t);
        }
        return $datos;
    }

    private function actualizarConcepto($t) {
        $actualizado = false;
        $con = new Consultas();
        $importe = $t->getImportetmp() - $t->getImpdescuento();
        $traslados = $this->reBuildArray2($importe, $t->getIdtraslados());
        $retenciones = $this->reBuildArray2($importe, $t->getIdretencion());

        $consulta = "UPDATE `tmp` SET tmpnombre=:nombre, cantidad_tmp=:cantidad, precio_tmp=:precio, totunitario_tmp=:totunitario, descuento_tmp=:descuento, impdescuento_tmp=:impdescuento, imptotal_tmp=:imptotal, observaciones_tmp=:observaciones, trasladotmp=:tras, retenciontmp=:ret, clvfiscaltmp=:cfiscal, clvunidadtmp=:cunidad WHERE idtmp=:id;";
        $valores = array("id" => $t->getIdtmp(),
            "nombre" => $t->getDescripciontmp(),
            "cantidad" => $t->getCantidadtmp(),
            "precio" => $t->getPreciotmp(),
            "totunitario" => $t->getImportetmp(),
            "descuento" => $t->getDescuento(),
            "impdescuento" => $t->getImpdescuento(),
            "imptotal" => $t->getImptotal(),
            "observaciones" => $t->getObservacionestmp(),
            "tras" => $traslados,
            "ret" => $retenciones,
            "cfiscal" => $t->getCfiscaltmp(),
            "cunidad" => $t->getCunidadtmp());
        $insertado = $con->execute($consulta, $valores);
        return $insertado;
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT p.crearfactura,p.editarfactura, p.eliminarfactura FROM usuariopermiso p where permiso_idusuario=:idusuario;";
        $c = new Consultas();
        $valores = array("idusuario" => $idusuario);
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(iddatos_factura) numrows FROM datos_factura dat INNER JOIN datos_facturacion d ON (d.id_datos=dat.iddatosfacturacion) $condicion;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
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

    private function getSevicios($condicion) {
        $consultado = false;
        $consulta = "SELECT dat.iddatos_factura, dat.iddatosfacturacion, dat.letra, dat.folio_interno_fac, dat.fecha_creacion, dat.rzreceptor cliente, dat.status_pago, dat.uuid, dat.idcliente, dat.tipofactura, dat.subtotal,dat.subtotaliva, dat.subtotalret, dat.totalfactura, dat.factura_rzsocial emisor, dat.id_moneda, d.color FROM datos_factura dat INNER JOIN datos_facturacion d ON (d.id_datos=dat.iddatosfacturacion) $condicion;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $crearfactura = $actual['crearfactura'];
            $editar = $actual['editarfactura'];
            $eliminar = $actual['eliminarfactura'];
            $datos .= "$editar</tr>$eliminar</tr>$crearfactura";
        }
        return $datos;
    }

	private function getNombreEmisor($fid) {
        $razonsocial = "";
        $sine = $this->getDatosFacturacionbyId($fid);
        foreach ($sine as $dactual) {
            $razonsocial = $dactual['razon_social'];
        }
        return $razonsocial;
    }

    public function listaServiciosHistorial($pag, $REF, $numreg) {
        require_once '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead class='sin-padding'>
            <tr class='align-middle'>
                <th></th>
                <th>No.Folio </th>
                <th class='text-center'>Fecha de creación </th>
                <th class='col-md-3'>Emisor</th>
                <th>Cliente</th>
                <th>Estado </th>
                <th>Timbre </th>
                <th>Subtotal </th>
                <th>Traslados </th>
                <th>Retenciones </th>
                <th>Total </th>
                <th>Moneda </th>
                <th>Opción</th>
                
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($REF == "") {
            $condicion = "ORDER BY iddatos_factura desc";
        } else {
            $condicion = "WHERE (concat(letra,folio_interno_fac) LIKE '%$REF%') OR (dat.rzreceptor LIKE '%$REF%') OR (dat.factura_rzsocial LIKE '%$REF%') ORDER BY iddatos_factura DESC";
        }
        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $listafactura = $this->getSevicios($con);
        $finales = 0;
        foreach ($listafactura as $listafacturaActual) {
            $idfactura = $listafacturaActual['iddatos_factura'];
            $folio = $listafacturaActual['letra'] . $listafacturaActual['folio_interno_fac'];
            $fecha = $listafacturaActual['fecha_creacion'];
            $nombre_cliente = $listafacturaActual['cliente'];
            $estado = $listafacturaActual['status_pago'];
            $uuid = $listafacturaActual['uuid'];
            $idcliente = $listafacturaActual['idcliente'];
            $tipofactura = $listafacturaActual['tipofactura'];
            $subtotal = $listafacturaActual['subtotal'];
            $subiva = $listafacturaActual['subtotaliva'];
            $subret = $listafacturaActual['subtotalret'];
            $total = $listafacturaActual['totalfactura'];
            $idmoneda = $listafacturaActual['id_moneda'];
            $emisor = $listafacturaActual['emisor'];
            $colorrow = $listafacturaActual['color'];
        	if ($emisor == "") {
                $emisor = $this->getNombreEmisor($listafacturaActual['iddatosfacturacion']);
            }

            $iva = 0;
            if ($subiva != "") {
                $diviva = explode("<impuesto>", $subiva);
                foreach ($diviva as $ivan) {
                    $traslados = $ivan;
                    $divt = explode("-", $traslados);
                    $iva += $divt[0];
                }
            }

            $ret = 0;
            if ($subret != "") {
                $divret = explode("<impuesto>", $subret);
                foreach ($divret as $retn) {
                    $retenciones = $retn;
                    $divr = explode("-", $retenciones);
                    $ret += $divr[0];
                }
            }

            $timbre = "";

            switch ($estado) {
                case '1':
                    $estadoF = "Pagada";
                    $color = "text-success";
                    $title = "Factura pagada";
                    $function = "onclick='tablaPagos($idfactura,$estado)'";
                    $modal = "data-toggle='modal' data-target='#pagosfactura'";
                    break;
                case '2':
                    $estadoF = "Pendiente";
                    $color = "text-danger";
                    $title = "Pago pendiente";
                    $function = "onclick='registrarPago($idfactura)'";
                    $modal = "";
                    break;
                case '3':
                    $estadoF = "Cancelada";
                    $color = "text-muted";
                    $title = "Factura cancelada";
                    $function = "";
                    $modal = "";
                    break;
                case '4':
                    $estadoF = "Pago Parcial";
                    $color = "text-warning";
                    $title = "Factura pagada parcialmente";
                    $function = "onclick='tablaPagos($idfactura,$estado)'";
                    $modal = "data-toggle='modal' data-target='#pagosfactura'";
                    break;
                default:
                    $estadoF = "Pendiente";
                    $color = "text-danger";
                    $title = "Pago pendiente";
                    $function = "onclick='registrarPago($idfactura)'";
                    $modal = "";
                    break;
            }

            if ($uuid != "") {
                $colorB = "text-success";
                $titbell = "Factura timbrada";
                if ($estado == "3") {
                    $tittimbre = "Factura Cancelada";
                    $timbre = "href='./com.sine.imprimir/imprimirxml.php?f=$idfactura&t=c' target='_blank'";
                    $colorB = "text-muted";
                    $titbell = "Factura cancelada";
                } else {
                    $timbre = "data-toggle='modal' data-target='#modalcancelar' onclick='setCancelacion($idfactura)'";
                    $tittimbre = "Cancelar factura";
                }
            } else {
                $timbre = "onclick='timbrarFactura($idfactura);'";
                $tittimbre = "Timbrar Factura";
                $colorB = "text-danger";
                $titbell = "Factura sin timbrar";
            }

            $divideF = explode("-", $fecha);
            $mes = $this->translateMonth($divideF[1]);

            $fecha = $divideF[2] . ' / ' . $mes;
        
            $datos .= "
                    <tr class='table-row'>
                        <td style='background-color: $colorrow;'></td>
                        <td class='text-center'>$folio</td>
                        <td class='text-center'>$fecha</td>
                        <td>$emisor</td>
                        <td>$nombre_cliente</td>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <a class='state-link $color' $modal $function ><span>$estadoF</span></a>
                                <span class='tiptext $color'>$title</span>
                            </div>
                        <td class='text-center'>
                            <div class='small-tooltip icon tip'>
                                <span class='fas fa-bell $colorB'></span>
                                <span class='tiptext $colorB'>$titbell</span>
                            </div>
                        </td>
                        <td class='text-center'>$ " . number_format($subtotal, 2, '.', ',') . "</td>
                        <td class='text-center'>$ " . number_format($iva, 2, '.', ',') . "</td>
                        <td class='text-center'>$ " . number_format($ret, 2, '.', ',') . "</td>
                        <td class='text-center'>$" . number_format($total, 2, '.', ',') . "</td>
                        <td class='text-center'>$idmoneda</td>
                        <td align='center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-end'>";

            if ($div[0] == '1') {
                //$datos .= "<li class='px-2'><a onclick='editarFactura($idfactura);'>Editar Factura <span class='glyphicon fas fa-edit'></span></a></li>";
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarFactura($idfactura);'>Editar Factura <span class='text-muted small fas fa-edit'></span></a></li>";
            }
            if ($div[1] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"eliminarFactura('$idfactura');\">Eliminar Factura <span class=' text-muted small fas fa-times'></span></a></li>";
            }

            $datos .= " <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"imprimir_factura($idfactura);\">Ver Factura <span class=' text-muted small fas fa-eye'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' href='./com.sine.imprimir/imprimirxml.php?f=$idfactura&t=a' target='_blank'>Ver XML <span class=' text-muted small fas fa-download'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' $timbre>$tittimbre <span class='text-muted small fas fa-bell'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#enviarmail' onclick='showCorreos($idfactura);'>Enviar <span class=' text-muted small fas fa-envelope'></span></a></li>";

            if ($div[2] == '1') {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='copiarFactura($idfactura);'>Copiar Factura <span class='text-muted small fas fa-clipboard'></span></a></li>";
            }

            if ($uuid != "") {
                $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#modal-stcfdi' onclick='checkStatusCancelacion(\"".$idfactura."\");'>Comprobar estado del CFDI <i class='text-muted small fas fa-check-circle'></i></a></li>";
            }
            $datos .= "</ul>
                        </div></td>
                    </tr>";
            $finales++;
        }
        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarFactura";
        if ($finales == 0) {
            $datos .= "<tr><td colspan='15'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='7' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='6'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        return $datos;
    }

    private function getProductoById($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM productos_servicios p WHERE p.idproser=:pid;";
        $val = array("pid" => $id);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function eliminar($idtemp, $sessionid, $cantidad, $idproducto) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmp` WHERE idtmp=:id;";
        $valores = array("id" => $idtemp);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        $restaurar = $this->restaurarInventario($idproducto, $cantidad);
        return $eliminado;
    }

    public function getPagosReg($folio, $idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * 
					FROM pagos p 
					INNER JOIN complemento_pago cp ON (cp.tagpago=p.tagpago) 
					INNER JOIN catalogo_pago c ON (c.idcatalogo_pago=cp.complemento_idformapago)
					INNER JOIN detallepago dp ON (dp.detalle_tagencabezado=cp.tagpago) 
					AND (dp.detalle_tagcomplemento=cp.tagcomplemento) 
					WHERE p.tagpago = :pid 
					AND dp.pago_idfactura = :fid";
        $val = array("pid" => $folio, "fid" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getPagosDetalle($id) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT pagos.foliopago, detallepago.detalle_tagencabezado 
					FROM detallepago 
					INNER JOIN pagos ON pagos.tagpago = detallepago.detalle_tagencabezado 
					WHERE pago_idfactura=:id AND type=:type 
					ORDER BY pagos.foliopago DESC";
        $val = array("id" => $id,
            "type" => 'f');
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaPagosReg($idfactura, $status) {
        $datos = "<corte><thead class='sin-paddding'>
            <tr>
                <th class='text-center'>FOLIO DE PAGO</th>
                <th class='text-center'>FECHA DE PAGO</th>
                <th class='text-center'>FORMA DE PAGO</th>
                <th class='text-center'>TOTAL PAGADO</th>
                <th class='text-center'>ESTADO</th>
                <th class='text-center'>RECIBO</th>
                </thead><tbody>";
        $productos = $this->getPagosDetalle($idfactura);
        foreach ($productos as $productoactual) {
            $folio = $productoactual['detalle_tagencabezado'];
            $pagos = $this->getPagosReg($folio, $idfactura);
            foreach ($pagos as $pagoactual) {
                $idpago = $pagoactual['idpago'];
                $foliopago = $pagoactual['letra'] . $pagoactual['foliopago'];
                $fechapago = $pagoactual['complemento_fechapago'];
                $div = explode("-", $fechapago);
                $mes = $this->translateMonth($div[1]);

                $fechapago = $div[2] . ' - ' . $mes;
                $horapago = $pagoactual['complemento_horapago'];
                $horapago = date('g:i a', strtotime($horapago));
                $totalpagado = $pagoactual['monto'];
                $c_pago = $pagoactual['c_pago'];
                $formapago = $pagoactual['descripcion_pago'];

                if ($pagoactual['cancelado'] == '0') {
                    $estado = "Activo";
                    if ($pagoactual['uuidpago'] != '') {
                        $estado = "Timbrado";
                    }
                } else if ($pagoactual['cancelado'] == '1') {
                    $estado = "Cancelado";
                }

                $datos .= "
                    <tr>
                        <td>$foliopago</td>
                        <td>$fechapago - $horapago</td>
                        <td>$c_pago $formapago</td>
                        <td>$ " . number_format(bcdiv($totalpagado, '1', 2),2) . "</td>
                        <td>$estado</td>
                        <td align='center'><a class='btn button-list' title='Descagar PDF' onclick=\"imprimirpago($idpago);\"><span class='glyphicon glyphicon-list-alt'></span></a></td>
                    </tr>
                     ";
            }
        }

        if ($status == '4') {
            $datos .= "<tr><td colspan='5'></td><td><a class='btn button-list' title='Agregar nuevo pago' data-dismiss='modal' onclick=\"registrarPago($idfactura);\"><span class='glyphicon glyphicon-file'></span></a></td></tr>";
        }

        $datos .= "</tbody>";
        return $datos;
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

    public function resetPagos($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmppago` WHERE sessionid=:id;";
        $valores = array("id" => $sessionid);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        return $eliminado;
    }

    public function cancelar($sessionid) {
        $tmp = $this->getTMP($sessionid);
        foreach ($tmp as $actual) {
            $chinv = $actual['chinventariotmp'];
            if ($chinv == '1') {
                $idprod = $actual['id_productotmp'];
                $cantidad = $actual['cantidad_tmp'];
                $inv = $this->restaurarInventario($idprod, $cantidad);
            }
        }
        $eliminado = false;
        $consulta = "DELETE FROM `tmp` WHERE session_id=:id;";
        $valores = array("id" => $sessionid);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        $cfdi = $this->deleteTMPCFDI($sessionid);
        return $eliminado;
    }

    private function deleteTMPCFDI($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpcfdi` WHERE sessionid=:id;";
        $valores = array("id" => $sessionid);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        return $eliminado;
    }

    public function getPagos($idfactura) {
        $consultado = false;
        $consulta = "select * from pagos p inner join catalogo_pago cp on (p.id_formapago=cp.idcatalogo_pago) where idpago=:idpago;";
        $consultas = new Consultas();
        $val = array("idpago" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getXMLImprimir($idfactura) {
        $consultado = false;
        $consulta = "SELECT dat.letra,dat.folio_interno_fac, dat.uuid,dat.cfdistring,dat.cfdicancel,df.rfc FROM datos_factura as dat inner join datos_facturacion as df on (dat.iddatosfacturacion=df.id_datos) where dat.iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getFacturas($idfactura) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT * FROM datos_factura dat 
        INNER JOIN catalogo_metodo_pago cmp ON (dat.id_metodo_pago= cmp.idmetodo_pago) 
        INNER JOIN catalogo_pago cp ON (dat.id_forma_pago = cp.idcatalogo_pago) 
        INNER JOIN catalogo_moneda cm ON (cm.idcatalogo_moneda = dat.id_moneda)
        INNER JOIN catalogo_uso_cfdi cuc ON (dat.id_uso_cfdi= cuc.iduso_cfdi) 
        INNER JOIN catalogo_comprobante cc ON (dat.id_tipo_comprobante=cc.idcatalogo_comprobante) WHERE dat.iddatos_factura=:id;";
        $val = array("id" => $idfactura);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getFacturasAux($id) {
        $consultado = false;
        $consulta = "SELECT tipo FROM productos_servicios WHERE idproser=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $id);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosFacturacionbyId($iddatos) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:id;";
        $consultas = new Consultas();
        $valores = array("id" => $iddatos);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDireccionCliente($idcliente, $fid) {
        $direccion = "";
        $datos = $this->getDatosCliente($idcliente, $fid);
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

    private function getDatosCliente($idcliente, $fid) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT c.*, d.cpreceptor FROM cliente c INNER JOIN datos_factura d ON (c.id_cliente=d.idcliente) WHERE id_cliente=:cid AND iddatos_factura=:fid;";
        $val = array("cid" => $idcliente,
            "fid" => $fid);
        $consultado = $consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getEstadoAux($idestado) {
        $estado = "";
        $est = $this->getEstadoById($idestado);
        foreach ($est as $actual) {
            $estado = $actual['estado'];
        }
        return $estado;
    }

    private function getEstadoById($idestado) {
        $consultado = false;
        $consulta = "select * from estado WHERE id_estado=:id;";
        $valores = array("id" => $idestado);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getMunicipioAux($idmun) {
        $municipio = "";
        $mun = $this->getMunicipioById($idmun);
        foreach ($mun as $actual) {
            $municipio = $actual['municipio'];
        }
        return $municipio;
    }

    private function getMunicipioById($idmun) {
        $consultado = false;
        $consulta = "select * from municipio WHERE id_municipio=:id;";
        $valores = array("id" => $idmun);
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getImpuestos($tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto where tipoimpuesto=:tipo";
        $consultas = new Consultas();
        $valores = array("tipo" => $tipo);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getRowsProdAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(idproser) numrows FROM productos_servicios p $condicion ;";
        $consultas = new Consultas();
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getRowsProd($condicion) {
        $numrows = 0;
        $rows = $this->getRowsProdAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    private function getProdHistorial($condicion) {
        $consultado = false;
        $consultas = new Consultas();
        $consulta = "SELECT p.codproducto, p.idproser, p.nombre_producto, p.descripcion_producto, p.precio_venta, p.tipo, p.clave_fiscal FROM productos_servicios p $condicion;";
        $consultado = $consultas->getResults($consulta, null);
        return $consultado;
    }

    public function listaProductosHistorial($NOM, $pag, $numreg) {
        require_once '../com.sine.common/pagination.php';
        $datos = "<thead>
                    <tr>
                        <th class='col-md-1'>Código </th>
                        <th class='col-md-6'>Producto/Servicio   </th>
                        <th class='col-md-1'>Cantidad </th>
                        <th class='col-md-1'>P.Venta </th>
                        <th class='col-md-1'>Importe </th>
                        <th class='col-md-1'>Desc %</th>
                        <th class='col-md-1'>Traslados</th>
                        <th class='col-md-1'>Retenciones</th>
                        <th class='col-md-1'>Total</th>
                        <th class='text-center'><span class='fas fa-plus'></span> </th>
                    </tr>
                  </thead>
                  <tbody>";
        $condicion = "";
        if ($NOM == "") {
            $condicion = "ORDER BY p.nombre_producto";
        } else {
            $condicion = "WHERE (p.nombre_producto LIKE '%$NOM%') OR (codproducto LIKE '%$NOM%') OR (clave_fiscal LIKE '%$NOM%') OR (desc_fiscal LIKE '%$NOM%') ORDER BY p.nombre_producto";
        }

        $numrows = $this->getRowsProd($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg; 
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $productos = $this->getProdHistorial($con);
        $finales = 0;
        $traslados = $this->getImpuestos('1');
        $retenciones = $this->getImpuestos('2');
        foreach ($productos as $productoactual) {
            $idprod = $productoactual['idproser'];
            $nombre = $productoactual['nombre_producto'];
            $pventa = $productoactual['precio_venta'];
            $tipo = $productoactual['tipo'];
            $codigo = $productoactual['codproducto'];

            if ($tipo == "1") {
                $tipoP = "Producto";
            } else if ($tipo == "2") {
                $tipoP = "Servicio";
            }
            $checkedT = "";
            $iconT = "";
            $optraslados = "";
            $impT = 0;
            foreach ($traslados as $tactual) {
                if ($tactual['chuso'] == '1') {
                    $iconT = "far fa-check-square mx-2";
                    $checkedT = "checked";
                    $impT += $pventa * $tactual['porcentaje'];
                } else {
                    $iconT = "far fa-square mx-2";
                    $checkedT = "";
                }
                $optraslados .= "<li data-location='lista' data-id='$idprod'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedT value='" . $tactual['porcentaje'] . "' name='chtraslado$idprod' data-impuesto='" . $tactual['impuesto'] . "' data-tipo='" . $tactual['tipoimpuesto'] . "'/><span class='glyphicon $iconT' id='chuso1span'></span>" . $tactual['nombre'] . " (" . $tactual['porcentaje'] . "%)" . "</label></li>";

            }

            $checkedR = "";
            $iconR = "";
            $opretencion = "";
            $impR = 0;
            foreach ($retenciones as $ractual) {
                if ($ractual['chuso'] == '1') {
                    $iconR = "far fa-check-square mx-2";
                    $checkedR = "checked";
                    $impR = $pventa * $ractual['porcentaje'];
                } else {
                    $iconR = "far fa-square mx-2";
                    $checkedR = "";
                }
                $opretencion .= "<li data-location='lista' data-id='$idprod'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedR value='" . $ractual['porcentaje'] . "' name='chretencion$idprod' data-impuesto='" . $ractual['impuesto'] . "' data-tipo='" . $ractual['tipoimpuesto'] . "'/><span class='glyphicon $iconR' id='chuso1span'></span>" . $ractual['nombre'] . " (" . $ractual['porcentaje'] . "%)" . "</label></li>";
            }

            $total = (bcdiv($pventa, '1', 2) + bcdiv($impT, '1', 2)) - bcdiv($impR, '1', 2);
            $datos .= "
                    <tr>
                        <td>$codigo</td>
                        <td><textarea rows='2' id='prodserv$idprod' class='form-control input-form' placeholder='Descripcion del producto' >$nombre</textarea></td>
                        <td><input class='form-control input-modal text-center input-sm' value='1' id='cantidad_$idprod' name='cantidad_$idprod' placeholder='Cantidad' type='number' oninput='calcularImporte($idprod)'/></td>
                        <td><input class='form-control input-modal text-center input-sm' id='pventa_$idprod' name='pventa_$idprod' value='$pventa' type='text' oninput='calcularImporte($idprod)'/></td>
                        <td><input class='form-control input-modal text-center input-sm' disabled id='importe_$idprod' name='importe_$idprod' value='$pventa' type='text'/></td>
                        <td><input class='form-control input-modal text-center input-sm' id='pordescuento_$idprod' name='pordescuento_$idprod' value='0' type='number' oninput='calcularDescuento($idprod)'/> <input class='form-control input-modal text-center input-sm' id='descuento_$idprod' name='descuento_$idprod' value='0' type='hidden'/></td>
                        <td><div class='input-group'>
                        <div class='dropdown' >
                        <button type='button' class='btn btn-sm btn-default dropdown-bs-toggle' data-bs-toggle='dropdown' data-bs-auto-close='outside' aria-expanded='false'>Traslados <span class='caret'></span></button>
                        <ul class='dropdown-menu'>
                            $optraslados
                        </ul>
                        </div>
                        </div></td>
                        <td><div class='input-group'>
                        <div class='dropdown'>
                        <button type='button' class='btn btn-sm btn-default dropdown-bs-toggle' data-bs-toggle='dropdown' data-bs-auto-close='outside' aria-expanded='false'>Retenciones <span class='caret'></span></button>
                        <ul class='dropdown-menu'>
                            $opretencion
                        </ul>
                        </div>
                        </div></td>
                        <td><input class='form-control input-modal text-center input-sm' disabled id='total_$idprod' name='pventa' value='$total' type='text'/></td>
                        <td><button title='Agregar Producto' class='button-add-prod' onclick='agregarProducto($idprod);'><span class='fas fa-plus'></span></button></td>
                    </tr>
                     ";
            $finales++;
        }

        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarProducto";

        if ($finales == 0) {
            $datos .= "<tr><td class='text-center' colspan='10'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><pag><div class='div-pag'>Mostrando $inicios al $finales de $numrows registros " . paginate($page, $total_pages, $adjacents, $function) . "</div>";
        return $datos;
    }

    private function getSaldoAux() {
        $consultado = false;
        $consulta = "SELECT * FROM contador_timbres WHERE idtimbres=:id;";
        $consultas = new Consultas();
        $valores = array("id" => '1');
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function checkSaldoAux() {
        $restantes = "0";
        $saldo = $this->getSaldoAux();
        foreach ($saldo as $actual) {
            $restantes = $actual['timbresRestantes'];
        }
        return $restantes;
    }

    public function checkSaldo($idfactura) {
        $timbrar = "";
        $saldo = $this->checkSaldoAux();
        if ($saldo > 0) {
            $timbrar = $this->guardarXML($idfactura);
        } else {
            $timbrar = "0Su saldo de timbres se ha agotado";
        }
        return $timbrar;
    }

    private function getFechaFactura($difverano, $difinvierno) {
        $hoy = date('Y-m-d');
        $hoy = date('Y-m-d', strtotime($hoy));
        $primer = date('Y-m-d', strtotime($_SESSION[sha1("primerdomingo")]));
        $ultimo = date('Y-m-d', strtotime($_SESSION[sha1("ultimodomingo")]));
        if (($hoy >= $primer) && ($hoy <= $ultimo)) {
            $tz = $difverano;
        } else {
            $tz = $difinvierno;
        }
        date_default_timezone_set("UTC");
        $utc = date('Y-m-d H:i:s');
        $gmt = strtotime($tz . ' hour', strtotime($utc));
        $gmt = date('Y-m-d H:i:s', $gmt);
        $fecha = str_replace(" ", "T", $gmt);

        return $fecha;
    }

    private function guardarXML($idfactura) {
        $timbre = false;
        $facturas = $this->getFacturas($idfactura);
        foreach ($facturas as $facturaactual) {
            $idcliente = $facturaactual['idcliente'];
            $rfcCliente = $facturaactual['rfcreceptor'];
            $razonSocial = $facturaactual['rzreceptor'];
            $cpreceptor = $facturaactual['cpreceptor'];
            $regfiscalreceptor = $facturaactual['regfiscalreceptor'];
            $iddatos = $facturaactual['iddatosfacturacion'];
            $cuso = $facturaactual['c_usocfdi'];
            $serie = $facturaactual['serie'];
            $letra = $facturaactual['letra'];
            $folio = $facturaactual['folio_interno_fac'];
            $subtotal = $facturaactual['subtotal'];
            $subiva = $facturaactual['subtotaliva'];
            $subret = $facturaactual['subtotalret'];
            $totdescuentos = $facturaactual['totaldescuentos'];
            $total = $facturaactual['totalfactura'];
            $c_moneda = $facturaactual['c_moneda'];
            $tcambio = $facturaactual['tipo_cambio'];
            $c_metodopago = $facturaactual['c_metodopago'];
            $c_formapago = $facturaactual['c_pago'];
            $c_tipoComprobante = $facturaactual['c_tipocomprobante'];
            $periodoglob = $facturaactual['periodoglobal'];
            $mesperiodo = $facturaactual['mesperiodo'];
            $anhoperiodo = $facturaactual['anhoperiodo'];
            $tag = $facturaactual['tagfactura'];
        }

        $sine = $this->getDatosFacturacionbyId($iddatos);
        foreach ($sine as $sineactual) {
        	$rfcemisor = $sineactual['rfc'];
            $rzemisor = $sineactual['razon_social'];
            $clvregemisor = $sineactual['c_regimenfiscal'];
            $regemisor = $sineactual['regimen_fiscal'];
            $cp = $sineactual['codigo_postal'];
            $nocertificado = $sineactual['numcsd'];
            $csd = $sineactual['csd'];
            $difverano = $sineactual['difhorarioverano'];
            $difinvierno = $sineactual['difhorarioinvierno'];
        }

        $xml = new DomDocument('1.0', 'UTF-8');
        $raiz = $xml->createElementNS('http://www.sat.gob.mx/cfd/4', 'cfdi:Comprobante');
        $raiz = $xml->appendChild($raiz);
    	
        ////$fecha = $this->getFechaFactura($difverano, $difinvierno); Esta es la funcion que tenia David, la sustitui para poder timbrar
   
        ///// Se usa para obtener la fecha 
            $f = getdate();

                $d = $f['mday'];
                $m = $f['mon'];
                $y = $f['year'];
                $h = $f['hours']-1;
                $mi = $f['minutes'];
                $s = $f['seconds'];
                if ($d < 10) {
                    $d = "0$d";
                }
                if ($m < 10) {
                    $m = "0$m";
                }
                if ($h < 10) {
                    $h = "0$h";
                }
                if ($mi < 10) {
                    $mi = "0$mi";
                }
                if ($s < 10) {
                    $s = "0$s";
                }
                $fecha = $y . '-' . $m . '-' . $d . 'T' . $h . ':' . $mi . ':' . $s;
            
            
             ///final de obtener la fecha
            
                
                $raiz->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $raiz->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd');
                $raiz->setAttribute('Version', '4.0');
                $raiz->setAttribute('Serie', $serie);
                $raiz->setAttribute('Folio', $letra . $folio);
                $raiz->setAttribute('Fecha', $fecha);
                $raiz->setAttribute('FormaPago', $c_formapago);
                $raiz->setAttribute('SubTotal', bcdiv($subtotal, '1', 2));
                if ($totdescuentos > 0) {
                    $raiz->setAttribute('Descuento', bcdiv($totdescuentos, '1', 2));
                }
                $raiz->setAttribute('TipoCambio', $tcambio);
                $raiz->setAttribute('Moneda', $c_moneda);
                $raiz->setAttribute('Total', bcdiv($total, '1', 2));
                $raiz->setAttribute('TipoDeComprobante', $c_tipoComprobante);
                $raiz->setAttribute('Exportacion', '01');
                $raiz->setAttribute('MetodoPago', $c_metodopago);
                $raiz->setAttribute('LugarExpedicion', utf8_decode($cp));
                $raiz->setAttribute('NoCertificado', $nocertificado);
                //Convertir certificado a B64 con openssl: enc -in "CSD/00001000000407565367.cer" -a -A -out "cerB64.txt" 
                $raiz->setAttribute('Certificado', $csd);

                if ($rfcCliente == "XAXX010101000") {
                    $global = $xml->createElement('cfdi:InformacionGlobal');
                    $global = $raiz->appendChild($global);
                    $global->setAttribute('Periodicidad', $periodoglob);
                    $global->setAttribute('Meses', $mesperiodo);
                    $global->setAttribute('Año', $anhoperiodo);
                }

                $cfdis = $this->getDistinctCfdisRelacionados($tag);
                foreach ($cfdis as $relactual) {
                    $tiporel = $relactual['tiporel'];

                    $cfdisrel = $xml->createElement('cfdi:CfdiRelacionados');
                    $cfdisrel = $raiz->appendChild($cfdisrel);
                    $cfdisrel->setAttribute('TipoRelacion', $tiporel);

                    $cfdis2 = $this->getcfdisRelacionadosByTipo($tag, $tiporel);
                    foreach ($cfdis2 as $relactual2) {
                        $uuid = $relactual2['uuid'];
                        $cfdirel = $xml->createElement('cfdi:CfdiRelacionado');
                        $cfdirel = $cfdisrel->appendChild($cfdirel);
                        $cfdirel->setAttribute('UUID', $uuid);
                    }
                }

                $emisor = $xml->createElement('cfdi:Emisor');
                $emisor = $raiz->appendChild($emisor);
                $emisor->setAttribute('Rfc', $rfcemisor);
                $emisor->setAttribute('Nombre', strtoupper($rzemisor));
                $emisor->setAttribute('RegimenFiscal', $clvregemisor);

                $receptor = $xml->createElement('cfdi:Receptor');
                $receptor = $raiz->appendChild($receptor);
                $receptor->setAttribute('Rfc', $rfcCliente);
                $receptor->setAttribute('Nombre', strtoupper($razonSocial));
                $receptor->setAttribute('DomicilioFiscalReceptor', $cpreceptor);
                $divreg = explode("-", $regfiscalreceptor);
                $receptor->setAttribute('RegimenFiscalReceptor', $divreg[0]);
                $receptor->setAttribute('UsoCFDI', $cuso);
                $baseT = 0;
                $baseR = 0;

                $conceptos = $xml->createElement('cfdi:Conceptos');
                $conceptos = $raiz->appendChild($conceptos);
                $detallefactura = $this->getDetalle($tag);
                foreach ($detallefactura as $detalleactual) {
                    $claveFiscal = $detalleactual['clvfiscal'];
                    $precioV = $detalleactual['precio'];
                    $cantidad = $detalleactual['cantidad'];
                    $unidad = $detalleactual['clvunidad'];
                    $descripcion = $detalleactual['factura_producto'];
                    $totalu = $detalleactual['totalunitario'];
                    $impdescuento = $detalleactual['impdescuento'];
                    $traslados = $detalleactual['traslados'];
                    $retenciones = $detalleactual['retenciones'];
                    $objimp = "01";
                    $importe = bcdiv($totalu, '1', 2) - bcdiv($impdescuento, '1', 2);

                    $divfiscal = explode("-", $claveFiscal);
                    $cfiscal = $divfiscal[0];

                    $divunit = explode("-", $unidad);
                    $cunidad = $divunit[0];
                    $descunidad = $divunit[1];

                    $concepto = $xml->createElement('cfdi:Concepto');
                    $concepto = $conceptos->appendChild($concepto);
                    $concepto->setAttribute('ClaveProdServ', $cfiscal);
                    $concepto->setAttribute('Cantidad', $cantidad);
                    $concepto->setAttribute('ClaveUnidad', $cunidad);
                    $concepto->setAttribute('Unidad', $descunidad);
                    $concepto->setAttribute('Descripcion', utf8_decode($descripcion));
                    $concepto->setAttribute('ValorUnitario', bcdiv($precioV, '1', 2));
                    $concepto->setAttribute('Importe', bcdiv($totalu, '1', 2));
                    if ($traslados != "" || $retenciones != "") {
                        $objimp = "02";
                    }
                    $concepto->setAttribute('ObjetoImp', $objimp);

                    if ($impdescuento > 0) {
                        $concepto->setAttribute('Descuento', bcdiv($impdescuento, '1', 2));
                    }

                    if ($traslados != "" || $retenciones != "") {
                        $impuestos = $xml->createElement('cfdi:Impuestos');
                        $impuestos = $concepto->appendChild($impuestos);
                        $baseT += bcdiv($importe, '1', 2);
                    }

                    if ($traslados != "") {
                        $nodetraslados = $xml->createElement('cfdi:Traslados');
                        $nodetraslados = $impuestos->appendChild($nodetraslados);

                        $divt = explode("<impuesto>", $traslados);
                        foreach ($divt as $tras) {
                            $divt = explode("-", $tras);
                            $imp = "00$divt[2]";
                            $traslado = $xml->createElement('cfdi:Traslado');
                            $traslado = $nodetraslados->appendChild($traslado);
                            $traslado->setAttribute('Base', bcdiv($importe, '1', 2));
                            $traslado->setAttribute('Impuesto', $imp);
                            $traslado->setAttribute('TipoFactor', 'Tasa');
                            $traslado->setAttribute('TasaOCuota', bcdiv($divt[1], '1', 6));
                            $traslado->setAttribute('Importe', bcdiv($divt[0], '1', 2));
                        }
                    }

                    if ($retenciones != "") {
                        $noderet = $xml->createElement('cfdi:Retenciones');
                        $noderet = $impuestos->appendChild($noderet);

                        $divr = explode("<impuesto>", $retenciones);
                        foreach ($divr as $ret) {
                            $divr = explode("-", $ret);
                            $imp = "00$divr[2]";
                            $retencion = $xml->createElement('cfdi:Retencion');
                            $retencion = $noderet->appendChild($retencion);
                            $retencion->setAttribute('Base', bcdiv($importe, '1', 2));
                            $retencion->setAttribute('Impuesto', $imp);
                            $retencion->setAttribute('TipoFactor', 'Tasa');
                            $retencion->setAttribute('TasaOCuota', bcdiv($divr[1], '1', 6));
                            $retencion->setAttribute('Importe', bcdiv($divr[0], '1', 2));
                        }
                    }
                }

                if ($subiva != "" || $subret != "") {
                    $impuestosT = $xml->createElement('cfdi:Impuestos');
                    $impuestosT = $raiz->appendChild($impuestosT);
                }

                $totalR = 0;
                if ($subret != "" && $subret != "0.00--") {
                    $noderet = $xml->createElement('cfdi:Retenciones');
                    $noderet = $impuestosT->appendChild($noderet);
                    $div2 = explode("<impuesto>", $subret);
                    foreach ($div2 as $ret1) {
                        $divr = explode("-", $ret1);
                        $impr = "00$divr[2]";
                        $retencion = $xml->createElement('cfdi:Retencion');
                        $retencion = $noderet->appendChild($retencion);
                        $retencion->setAttribute('Impuesto', $impr);
                        $retencion->setAttribute('Importe', bcdiv($divr[0], '1', 2));
                        $totalR += bcdiv($divr[0], '1', 2);
                    }
                    $impuestosT->setAttribute('TotalImpuestosRetenidos', bcdiv($totalR, '1', 2));
                }

                $totalT = 0;
                if ($subiva != "" && $subiva != "0.00--") {
                    $nodetraslados = $xml->createElement('cfdi:Traslados');
                    $nodetraslados = $impuestosT->appendChild($nodetraslados);
                    $div1 = explode("<impuesto>", $subiva);
                    foreach ($div1 as $tras1) {
                        $divt = explode("-", $tras1);
                        $imp = "00$divt[2]";
                        $traslado = $xml->createElement('cfdi:Traslado');
                        $traslado = $nodetraslados->appendChild($traslado);
                        $traslado->setAttribute('Base', bcdiv($baseT, '1', 2));
                        $traslado->setAttribute('Impuesto', $imp);
                        $traslado->setAttribute('TipoFactor', 'Tasa');
                        $traslado->setAttribute('TasaOCuota', bcdiv($divt[1], '1', 6));
                        $traslado->setAttribute('Importe', bcdiv($divt[0], '1', 2));
                        $totalT += bcdiv($divt[0], '1', 2);
                    }
                    $impuestosT->setAttribute('TotalImpuestosTrasladados', bcdiv($totalT, '1', 2));
                }

                $sello = $this->SelloXML($xml->saveXML(), $rfcemisor);
                $obj = json_decode($sello);
                $xml2 = new DOMDocument("1.0", "UTF-8");
                $xml2->loadXML($xml->saveXML());
                $c = $xml2->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/4', 'Comprobante')->item(0);
                $c->setAttribute('Sello', $obj->sello); 
                $doc = "../XML/XML2.xml";
                $xml2->save($doc);
                $timbre = $this->timbrado($xml2->saveXML(), $idfactura, $rfcemisor, $rzemisor, $clvregemisor, $regemisor, $cp);
                return $timbre;
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
                echo '0Caught exception: ', $e->getMessage(), "\n";
            }
        }
      
 
            function generarXML($filename, $content) {
                $f = fopen($filename, "w");
                fwrite($f, pack("CCC", 0xef, 0xbb, 0xbf));
                fwrite($f, $content);
                fclose($f);
            }

            function timbrado($doc, $idfactura, $rfcemisor, $rzemisor, $clvregemisor, $regemisor, $cp) {
                $swaccess = $this->getSWAccess();
                $div = explode("</tr>", $swaccess);
                $url = $div[0];
                $token = $div[1];
                $params = array(
                    "url" => $url,
                    "token" => $token
                );

                try {
                    //header("Content-type: text/plain");
                    $stamp = StampService::Set($params);
                    $result = $stamp::StampV4($doc);
                    if ($result->status == "error") {
                        return '0' . $result->message . " " . $result->messageDetail;
                    } else if ($result->status == "success") {
                        $guardar = $this->guardarTimbre($result, $idfactura, $rfcemisor, $rzemisor, $clvregemisor, $regemisor, $cp);
                        var_dump($result);
                        return $guardar;
                    }
                } catch (Exception $e) {
                    //header("Content-type: text/plain");
                    echo "0" . $e->getMessage();
                }
            }

            private function guardarTimbre($result, $idfactura, $rfcemisor, $rzemisor, $clvregemisor, $regemisor, $cp) {
                $actualizado = false;
                $consulta = "UPDATE `datos_factura` SET factura_rfcemisor=:rfc, factura_rzsocial=:rzsocial, factura_clvregimen=:clvreg, factura_regimen=:regimen, factura_cpemisor=:cpemisor, cadenaoriginal=:cadena, nocertificadosat=:certSAT, nocertificadocfdi=:certCFDI, uuid=:uuid, sellosat=:selloSAT, sellocfdi=:selloCFDI, fechatimbrado=:fechatimbrado, qrcode=:qrcode, cfdistring=:cfdi, tipofactura=:tipo WHERE iddatos_factura=:id;";
                $valores = array("rfc" => $rfcemisor,
                    "rzsocial" => $rzemisor,
                    "clvreg" => $clvregemisor,
                    "regimen" => $regemisor,
                    "cpemisor" => $cp,
                    "cadena" => $result->data->cadenaOriginalSAT,
                    "certSAT" => $result->data->noCertificadoSAT,
                    "certCFDI" => $result->data->noCertificadoCFDI,
                    "uuid" => $result->data->uuid,
                    "selloSAT" => $result->data->selloSAT,
                    "selloCFDI" => $result->data->selloCFDI,
                    "fechatimbrado" => $result->data->fechaTimbrado,
                    "qrcode" => $result->data->qrCode,
                    "cfdi" => $result->data->cfdi,
                    "tipo" => '1',
                    "id" => $idfactura);
                $consultas = new Consultas();
                $actualizado = $consultas->execute($consulta, $valores);
                $timbres = $this->updateTimbres();
                return '+Timbre Guardado';
            }

            private function updateTimbres() {
                $actualizado = false;
                $consulta = "UPDATE `contador_timbres` SET  timbresUtilizados=timbresUtilizados+1, timbresRestantes=timbresRestantes-1 WHERE idtimbres=:idtimbres;";
                $valores = array("idtimbres" => '1');
                $consultas = new Consultas();
                $actualizado = $consultas->execute($consulta, $valores);
                return $actualizado;
            }

            public function getUUID($idfactura) {
                $datos = "";
                $uuid = $this->getUUIDAux($idfactura);
                foreach ($uuid as $u) {
                    $uuid = $u['uuid'];
                    $folio = $u['letra'] . $u['folio_interno_fac'];
                    $rfc = $u['rfc'];
                    $pass = $u['passcsd'];
                    $csd = $u['csd'];
                    $key = $u['keyb64'];
                    $datos = "$uuid</tr>$folio</tr>$rfc</tr>$pass</tr>$csd</tr>$key";
                }
                return $datos;
            }

            public function getUUIDAux($idfactura) {
                $consultado = false;
                $consulta = "SELECT f.uuid,f.letra,f.folio_interno_fac,d.rfc,d.passcsd, d.keyb64, d.csd FROM datos_factura f inner join datos_facturacion d on (f.iddatosfacturacion=d.id_datos) where iddatos_factura=:id;";
                $val = array("id" => $idfactura);
                $consultas = new Consultas();
                $consultado = $consultas->getResults($consulta, $val);
                return $consultado;
            }

            function cancelarTimbre($idfactura, $motivo, $reemplazo) {
                $swaccess = $this->getSWAccess();
                $div = explode("</tr>", $swaccess);
                $url = $div[0];
                $token = $div[1];

                $get = $this->getUUID($idfactura);
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
                        return '0' . $result->message . " " . $result->messageDetail;
                    } else if ($result->status == "success") {
                        $guardar = $this->cancelarFactura($idfactura, $result->data->acuse);
                        var_dump($result);
                        return $guardar;
                    }
                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
            }

            public function cancelarFactura($idfactura, $cfdi) {
                $actualizado = false;
                $consulta = "UPDATE `datos_factura` set status_pago=:estado, cfdicancel=:cfdi WHERE iddatos_factura=:id;);";
                $valores = array("id" => $idfactura,
                    "estado" => '3',
                    "cfdi" => $cfdi);
                $con = new Consultas();
                $actualizado = $con->execute($consulta, $valores);
                return $actualizado;
            }

            private function getConfigMailAux() {
                $consultado = false;
                $consulta = "SELECT * FROM correoenvio WHERE chuso1=:id;";
                $consultas = new Consultas();
                $valores = array("id" => '1');
                $consultado = $consultas->getResults($consulta, $valores);
                return $consultado;
            }

            private function getConfigMail() {
                $datos = "";
                $get = $this->getConfigMailAux();
                foreach ($get as $actual) {
                    $correo = $actual['correo'];
                    $pass = $actual['password'];
                    $remitente = $actual['remitente'];
                    $correoremitente = $actual['correoremitente'];
                    $host = $actual['host'];
                    $puerto = $actual['puerto'];
                    $seguridad = $actual['seguridad'];
                    $datos = "$correo</tr>$pass</tr>$remitente</tr>$correoremitente</tr>$host</tr>$puerto</tr>$seguridad";
                }
                return $datos;
            }

            public function mail($sm) {
                require_once '../com.sine.controlador/ControladorConfiguracion.php';
                $cc = new ControladorConfiguracion();
                $body = $cc->getMailBody('1');
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
                    $correoremitente = $div[3];
                    $host = $div[4];
                    $puerto = $div[5];
                    $seguridad = $div[6];

                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->Mailer = 'smtp';
                    $mail->SMTPAuth = true;
                    $mail->Host = $host;
                    $mail->Port = $puerto;
                    $mail->SMTPSecure = $seguridad;

                    $mail->Username = $correoenvio;
                    $mail->Password = $pass;
                    $mail->From = $correoremitente;
                    $mail->FromName = $remitente;

                    $mail->Subject = utf8_decode($asunto);
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
                        echo '0No se envio el mensaje';
                        echo '0Mailer Error: ' . $mail->ErrorInfo;
                    } else {
                        return 'Se ha enviado la factura';
                    }
                } else {
                    return "No se configuro correo de envio";
                }
            }

            private function bodyMail($asunto, $saludo, $nombre, $msg, $logo) {
                $archivo = "../com.sine.dao/configuracion.ini";
                $ajustes = parse_ini_file($archivo, true);
                if (!$ajustes) {
                    throw new Exception("No se puede abrir el archivo " . $archivo);
                }
                $rfcfolder = $ajustes['cron']['rfcfolder'];

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

            /* public function sendMSG($file, $cod, $number) {
            $fullnumb = $cod . $number;
            $sid = "AC6256c8483286f5e0fd804de145ba42bf";
            $token = "aabd7bf61f0efdf18965ffbef4f261bd";
            $twilio = new Client($sid, $token);
            try {
            $message = $twilio->messages->create("whatsapp:+$fullnumb", // to
            ["from" => "whatsapp:+14155238886",
            "body" => "Hola BB",
            "mediaUrl" => ["https://q-ik.mx/SineFacturacion/pdf/facturaFAC20200025.pdf"]]);
            return $message->sid;
            } catch (Exception $e) {
            header("Content-type: text/plain");
            echo "0" . $e->getMessage();
            }
            } */

            public function actualizarDiasHorario($primer, $ultimo) {
                Session::start();
                $_SESSION[sha1("primerdomingo")] = $primer;
                $_SESSION[sha1("ultimodomingo")] = $ultimo;

                $actualizado = false;
                $consultas = new Consultas();
                $consulta = "UPDATE `horario_verano` SET primerdomingoabril=:primer, ultimodomingooctubre=:ultimo WHERE idhorario_verano=:id;";
                $valores = array("id" => '1',
                    "primer" => $primer,
                    "ultimo" => $ultimo);
                $actualizado = $consultas->execute($consulta, $valores);
                return $actualizado;
            }

            public function getInfoGlobal($clvp, $clvm) {
                $periodo = "";
                $datosP = $this->getPeriodicidadbyClvAux($clvp);
                foreach ($datosP as $actualP) {
                    $periodo = $actualP['descripcion_periodoglobal'];
                }
                
                $mes = "";
                $datosM = $this->getMesbyClvAux($clvm);
                foreach ($datosM as $actualM) {
                    $mes = $actualM['descripcion_meses'];
                }

                $datos = "$periodo</tr>$mes";
                return $datos;
            }

            private function getPeriodicidadbyClvAux($clv) {
                $consultado = false;
                $con = new Consultas();
                $consulta = "SELECT * FROM catalogo_periodoglobal WHERE c_periodoglobal=:clv;";
                $val = array("clv" => $clv);
                $consultado = $con->getResults($consulta, $val);
                return $consultado;
            }

            private function getMesbyClvAux($clv) {
                $consultado = false;
                $con = new Consultas();
                $consulta = "SELECT * FROM catalogo_meses WHERE c_meses=:clv;";
                $val = array("clv" => $clv);
                $consultado = $con->getResults($consulta, $val);
                return $consultado;
            }

            public function checkStatusCFDI($idfactura) {
                $datos = false;
                $factura = $this->getFacturaById($idfactura);
                foreach ($factura as $actual) {
                    $emisor = $actual['factura_rfcemisor'];
                    $receptor = $actual['rfcreceptor'];
                    $total = $actual['totalfactura'];
                    $uuid = $actual['uuid'];
                    $cfdistring = $actual['cfdistring'];
                }

                $xml = simplexml_load_string($cfdistring);
                $comprobante = $xml->xpath('/cfdi:Comprobante');
                $attr = $comprobante[0]->attributes();
                $sello = $attr['Sello'];
                $subsello = substr($sello, -8);
                //$soapUrl = "https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc";
                $soapUrl = "https://pruebacfdiconsultaqr.cloudapp.net/ConsultaCFDIService.svc";
                $consultaCfdi = consultaCfdiSAT::ServicioConsultaSAT($soapUrl, $emisor, $receptor, $total, $uuid, $subsello);
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
                    $reset = "<button class='button-modal' onclick='resetCfdi($idfactura)' id='btn-reset-cfdi'>Restaurar Factura <span class='glyphicon glyphicon-repeat'></span></button>";
                }
                $datos = "$codstatus</tr>$estado</tr>$cancelable</tr>$statusCancelacion</tr>$reset";
                return $datos;
            }

        }