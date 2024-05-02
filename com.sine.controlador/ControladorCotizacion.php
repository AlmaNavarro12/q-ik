<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/Cotizacion.php';
require_once '../com.sine.modelo/TMPCotizacion.php';
require_once '../com.sine.modelo/SendMail.php';
require_once '../vendor/numeroaletras/NumeroALetras.php';

date_default_timezone_set("America/Mexico_City");
use PHPMailer\PHPMailer\PHPMailer;


class ControladorCotizacion {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function getPermisoById($idusuario) {
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso p where permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario) {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listacotizacion'];
            $editar = $actual['editarcotizacion'];
            $eliminar = $actual['eliminarcotizacion'];
            $crear = $actual['crearcotizacion'];
            $crearfac = $actual['crearfactura'];
            $exportar = $actual['exportarfactura'];
            $anticipo = $actual['anticipo'];
            $datos .= "$lista</tr>$editar</tr>$eliminar</tr>$crear</tr>$crearfac</tr>$exportar</tr>$anticipo";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "select count(*) numrows from datos_cotizacion as dat $condicion;";
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

    public function getSevicios($condicion) {
        $consultado = false;
        $consulta = "select * from datos_cotizacion as dat $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function checkAnticiposAux($idcotizacion) {
        $consultado = false;
        $consulta = "select * from anticipo where anticipo_idcotizacion=:id;";
        $val = array("id" => $idcotizacion);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkAnticipos($idcotizacion) {
        $datos = 0;
        $check = $this->checkAnticiposAux($idcotizacion);
        foreach ($check as $actual) {
            $datos++;
        }
        return $datos;
    }

    public function translateMonth($m) {
        $meses = [
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
        ];
    
        return $meses[$m] ?? '';
    }

    private function getUsuariobyID($idusuario) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE idusuario=:cid;";
        $val = array("cid" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getNombreUsuarioById($idusuario) {
        $nombre = "";
        $datos = $this->getUsuariobyID($idusuario);
        foreach ($datos as $actual) {
            $nombre = $actual['nombre'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'];
        }
        return $nombre;
    }

    public function listaServiciosHistorial($REF, $pag, $numreg) {
        include '../com.sine.common/pagination.php';
        $idlogin = $_SESSION[sha1("idusuario")];

        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th>No.Folio </th>
                <th class='text-center'>Fecha de Creación </th>
                <th class='text-center'>Cliente</th>
                <th class='text-center'>Email</th>
                <th class='text-center'>Exportó </th>
                <th class='text-center'>Fecha de exportación </th>
                <th class='text-center'>Total </th>
                <th class='text-center'>Opción</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($REF == "") {
            $condicion = "ORDER BY iddatos_cotizacion DESC";
        } else {
            $condicion = "WHERE (concat(letra,foliocotizacion) LIKE '%$REF%') OR (nombrecliente LIKE '%$REF%') ORDER BY iddatos_cotizacion DESC";
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
            $listafactura = $this->getSevicios($con);
            $finales = 0;
            foreach ($listafactura as $listafacturaActual) {
                $idcotizacion = $listafacturaActual['iddatos_cotizacion'];
                $folio = $listafacturaActual['letra'] . $listafacturaActual['foliocotizacion'];
                $fecha = $listafacturaActual['fecha_creacion'];
                $cliente = $listafacturaActual['nombrecliente'];
                $correo = $listafacturaActual['emailcot'];
                $total = $listafacturaActual['totalcotizacion'];
                $tagfactura = $listafacturaActual['expfactura'];
                $idexporto = $listafacturaActual['sessionexporto'];
                $fechaexportacion = "";
                $nombreexporto = $this->getNombreUsuarioById($idexporto);

                $divideF = explode("-", $fecha);
                $mes = $this->translateMonth($divideF[1]);
                $fecha = $divideF[2] . ' / ' . $mes;
                
                $check = $this->checkAnticipos($idcotizacion);

                if(isset($listafacturaActual['fecha_exportar'])) {
                    $fechaexportacion = $listafacturaActual['fecha_exportar'];
                    list($año, $mes, $dia) = explode('-', $fechaexportacion);
                    $nombreMes = $this->translateMonth($mes);
                    $fechaexportacion = "$dia / $nombreMes";
                }

                if ($check == '0') {
                    $txtanticipo = "Agregar anticipo";
                    $datamodal = "data-bs-target='#anticipos' onclick='cargarDatosAnticipo($idcotizacion);'";
                } else {
                    $txtanticipo = "Ver anticipos";
                    $datamodal = "data-bs-target='#lista-anticipo' onclick='loadListaAnticipos($idcotizacion);'";
                }

                $datos .= "<tr>
                        <td>$folio</td>
                        <td class='text-center'>$fecha</td>
                        <td class='lh-sm text-center'>$cliente</td>
                        <td>$correo</td>
                        <td class='text-center text-uppercase'>$nombreexporto</td>
                        <td class='text-center'>$fechaexportacion</td>

                        <td class='text-center'>$" . number_format($total, 2, '.', ',') . "</td>
                        <td align='center'><div class='dropdown dropend'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right z-1'>";

                if ($div[1] == '1' && $tagfactura == "0") {
                    $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick='editarCotizacion($idcotizacion);'>Editar cotización <span class='fas fa-edit text-muted small'></span></a></li>";
                }

                if ($div[2] == '1' && $tagfactura == "0") {
                    $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarCotizacion($idcotizacion);'>Eliminar cotización <span class='fas fa-times text-muted'></span></a></li>";
                }

                $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick=\"imprimirCotizacion($idcotizacion)\";'>Ver cotización <span class='fas fa-eye text-muted'></span></a></li>
                        <li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#enviarmail' onclick='showCorreos($idcotizacion);'>Enviar al cliente <span class='fas fa-envelope text-muted small' ></span></a></li>";

                        //<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' onclick='cobrarCotizacion($idcotizacion);'>Cobrar cotización <span class='fas fa-dollar-sign text-muted small'></span></a></li>
                if ($div[5] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick='exportarCotizacion($idcotizacion);'>Exportar como factura <span class='text-muted small fas fa-external-link-alt'></span></a></li>";
                }

                if ($div[3] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick='copiarCotizacion($idcotizacion);'>Copiar cotización <span class='text-muted small fas fa-copy'></span></a></li>";
                }

                $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick='actualizarPrecios($idcotizacion);'>Actualizar precios <span class='text-muted small fas fa-sync-alt'></span></a></li>";

                if($div[6] == '1'){
                        $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' $datamodal>$txtanticipo <span class='text-muted small fas fa-copy'></span></a></li>";
                }
                $datos .= "</ul>
                        </div></td>
                    </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarCotizacion";
            if ($finales == 0) {
                $datos .= "<tr><td  colspan='11'>No se encontraron registros</td></tr>";
            }
            $datos .= "</tbody><tfoot><tr><th colspan='12'>Mostrando $inicios al $finales de $numrows registros " . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        }
        return $datos;
    }

    public function getDatosFacturacionbyId($iddatos) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion df WHERE df.id_datos=:id;";
        $val = array("id" => $iddatos);
        $consultado = $this->consultas->getResults($consulta, $val);
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

    public function getDocumento() {
        $idusuario = $_SESSION[sha1("idusuario")];
        $datos = "";
        $documento = $this->getDocumentoAux($idusuario);
        foreach ($documento as $d) {
            $do = $d['doc'];
            $datos = '' . $do;
        }
        return $datos;
    }

    public function getDocumentoAux($id) {
        $consultado = false;
        $consulta = "SELECT concat(nombre,' ',apellido_paterno) as doc FROM usuario WHERE idusuario=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getRowsProdAux($condicion)
    {
        $consultado = false;
        $consulta = "SELECT count(idproser) numrows FROM productos_servicios p $condicion ;";

        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getRowsProd($condicion)
    {
        $numrows = 0;
        $rows = $this->getRowsProdAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    private function getProdHistorial($condicion)
    {
        $consultado = false;
        $consulta = "SELECT p.codproducto, p.idproser, p.nombre_producto, p.descripcion_producto, p.precio_venta, p.tipo, p.clave_fiscal, p.impuestos_aplicables FROM productos_servicios p $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getImpuestos($tipo)
    {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto where tipoimpuesto=:tipo";
        $valores = array("tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function listaProductosHistorial($NOM, $pag, $numreg)
    {
        require_once '../com.sine.common/pagination.php';
        $datos = "<thead>
                    <tr>
                        <th class='col-md-1'>CÓdigo </th>
                        <th class='col-md-3'>Producto/Servicio   </th>
                        <th class='col-md-1'>Cantidad </th>
                        <th class='col-md-1'>P.Venta </th>
                        <th class='col-md-1'>Importe </th>
                        <th class='col-md-1'>Desc % </th>
                        <th class='col-md-1'>Traslados</th>
                        <th class='col-md-1'>Retenciones</th>
                        <th class='col-md-2'>Total</th>
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
                $optraslados .= "<li data-location='lista' data-id='$idprod'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedT value='" . $tactual['porcentaje'] . "' name='chtraslado$idprod' data-impuesto='" . $tactual['impuesto'] . "' data-tipo='" . $tactual['tipoimpuesto'] . "'/><span class='$iconT' id='chuso1span'></span>" . $tactual['nombre'] . " (" . $tactual['porcentaje'] . "%)" . "</label></li>";
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
                $opretencion .= "<li data-location='lista' data-id='$idprod'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedR value='" . $ractual['porcentaje'] . "' name='chretencion$idprod' data-impuesto='" . $ractual['impuesto'] . "' data-tipo='" . $ractual['tipoimpuesto'] . "'/><span class='$iconR' id='chuso1span'></span>" . $ractual['nombre'] . " (" . $ractual['porcentaje'] . "%)" . "</label></li>";
            }

            $total = (bcdiv($pventa, '1', 2) + bcdiv($impT, '1', 2)) - bcdiv($impR, '1', 2);
            $datos .= "
                    <tr>
                        <td>$codigo</td>
                        <td><textarea rows='2' id='prodserv$idprod' class='form-control input-form' placeholder='Descripción del producto' >$nombre</textarea></td>
                        <td><input class='form-control input-modal text-center input-sm' value='1' id='cantidad_$idprod' name='cantidad_$idprod' placeholder='Cantidad' type='number' oninput='calcularImporte($idprod)'/></td>
                        <td><input class='form-control input-modal text-center input-sm' id='pventa_$idprod' name='pventa_$idprod' value='$pventa' type='text' oninput='calcularImporte($idprod)'/></td>
                        <td><input class='form-control input-modal text-center input-sm' disabled id='importe_$idprod' name='importe_$idprod' value='$pventa' type='text'/></td>
                        <td><input class='form-control input-modal text-center input-sm' id='pordescuento_$idprod' name='pordescuento_$idprod' value='0' type='text' oninput='calcularDescuento($idprod); validarNum(this)'/> <input class='form-control input-modal text-center input-sm' id='descuento_$idprod' name='descuento_$idprod' value='0' type='hidden'/></td>
                        <td><div class='input-group'>
                        <div class='dropdown'>
                        <button type='button' class='button-impuesto dropdown-bs-toggle' data-bs-toggle='dropdown'>Traslados <span class='caret'></span></button>
                        <ul class='dropdown-menu'>
                            $optraslados
                        </ul>
                        </div>
                        </div></td>
                        <td><div class='input-group'>
                        <div class='dropdown'>
                        <button type='button' class='button-impuesto dropdown-bs-toggle' data-bs-toggle='dropdown'>Retenciones <span class='caret'></span></button>
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

    public function cancelar($sessionid) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpcotizacion` WHERE session_id=:id;";
        $valores = array("id" => $sessionid);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function getProductoById($id) {
        $consultado = false;
        $consulta = "SELECT * FROM productos_servicios p WHERE p.idproser=:pid;";
        $val = array("pid" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
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

    private function checkInventarioAux($idprod) {
        $consultado = false;
        $consulta = "SELECT chinventario,cantinv FROM productos_servicios where idproser='$idprod';";
        $consultado = $this->consultas->getResults($consulta, null);
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
                $datos = "0El inventario no es suficiente para agregar este producto.";
            } else {
                $datos = $this->agregar($t, $chinv);
            }
        } else if ($chinv == '0') {
            $datos = $this->agregar($t, $chinv);
        }

        return $datos;
    }

    public function agregar($t, $chinv) {
        $insertado = false;
        $clvfiscal = "";
        $clvunidad = "";

        if ($t->getIdproductotmp() == '0') {
            $clvfiscal = $t->getClvfiscal();
            $clvunidad = $t->getClvunidad();
        } else {
            $productos = $this->getProductoById($t->getIdproductotmp());
            foreach ($productos as $prod) {
                $clvfiscal = $prod['clave_fiscal'] . '-' . $prod['desc_fiscal'];
                $clvunidad = $prod['clv_unidad'] . '-' . $prod['desc_unidad'];
            }
        }

        $importe = $t->getImportetmp() - $t->getImpdescuento();
        $traslados = $this->reBuildArray2($importe, $t->getIdtraslados());
        $retenciones = $this->reBuildArray2($importe, $t->getIdretencion());

        $consulta = "INSERT INTO `tmpcotizacion` VALUES (:id, :idproducto, :nombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :idtraslado, :idretencion, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
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
            "chinv" => $chinv,
            "clvfiscal" => $clvfiscal,
            "clvunidad" => $clvunidad,
            "session" => $t->getSessionid());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getTMP($sessionId) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpcotizacion ps WHERE session_id=:sid ORDER BY idtmpcotizacion";
        $val = array("sid" => $sessionId);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function tablaProd($sessionid) {
        $datos = "<thead class='sin-paddding'>
            <tr class='align-middle'>
                <th class='text-center'>CLAVE FISCAL</th>
                <th class='text-center'>CANTIDAD</th>
                <th class='text-center'>DESCRIPCIÓN</th>
                <th class='text-center'>PRECIO UNITARIO</th>
                <th class='text-center'>IMPORTE</th>
                <th class='text-center'>DESC%</th>
                <th class='text-center'>TRASLADOS</th>
                <th class='text-center'>RETENCIONES</th>
                <th class='text-center col-md-2'>OBSERVACIÓN</th>
                <th class='text-center'><i class='fas fa-ellipsis-v'></i></th></tr>
                </thead><tbody>";

        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $productos = $this->getTMP($sessionid);
        $imptraslados = $this->getImpuestos('1');
        $impretenciones = $this->getImpuestos('2');
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmpcotizacion'];
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
            $obser = str_replace("<ent>", "\n", $observaciones);

            $divclv = explode("-", $clavefiscal);
            $clavefiscal = $divclv[0];

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
                        $iconT = "far fa-check-square mx-2";
                        $checkedT = "checked";
                        break;
                    } else {
                        $iconT = "far fa-square mx-2";
                        $checkedT = "";
                    }
                }
                $optraslados = $optraslados . "<li data-location='tabla' data-id='$id_tmp'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedT value='" . $tactual['porcentaje'] . "' name='chtrastabla$id_tmp' data-impuesto='" . $tactual['impuesto'] . "' data-tipo='" . $tactual['tipoimpuesto'] . "'/><span class='glyphicon $iconT' id='chuso1span'></span>" . $tactual['nombre'] . " (" . $tactual['porcentaje'] . "%)" . "</label></li>";
            }

            $checkedR = "";
            $iconR = "";
            $opretencion = "";
            foreach ($impretenciones as $ractual) {
                $divcheckR = explode("<imp>", $checkretencion);
                foreach ($divcheckR as $r) {
                    if ($r == $ractual['porcentaje'] . "-" . $ractual['impuesto']) {
                        $iconR = "far fa-check-square mx-2";
                        $checkedR = "checked";
                        break;
                    } else {
                        $iconR = "far fa-square mx-2";
                        $checkedR = "";
                    }
                }
                $opretencion = $opretencion . "<li data-location='tabla' data-id='$id_tmp'><label class='dropdown-menu-item checkbox'><input type='checkbox' $checkedR value='" . $ractual['porcentaje'] . "' name='chrettabla$id_tmp' data-impuesto='" . $ractual['impuesto'] . "' data-tipo='" . $ractual['tipoimpuesto'] . "'/><span class='glyphicon $iconR' id='chuso1span'></span>" . $ractual['nombre'] . " (" . $ractual['porcentaje'] . "%)" . "</label></li>";
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
                    <tr class='text-center'>
                        <td>$clavefiscal</td>
                        <td>
                        <div class='btn-group btn-group-sm'>
                        <button type='button' class='btn btn-outline-secondary btn-sm ' $disabledminus data-type='minus' data-field='quant[1]' onclick='reducirCantidad($id_tmp);'>
                            <i class='fas fa-minus'></i>
                        </button>
                        <button class='badge btn btn-info' data-bs-toggle='modal' data-bs-target='#modal-cantidad' onclick='setCantidad($id_tmp,$cantidad)'>
                            <div class='badge' id='badcant$id_tmp'>$cantidad</div>
                        </button>
                        <button type='button' class='btn btn-outline-secondary btn-sm ' data-type='plus' onclick='incrementarCantidad($id_tmp);'>
                            <i class='fas fa-plus'></i>
                        </button>
                        </div>
                            </td>
                        <td>$nombre</td>
                        <td class='text-center'>$" . number_format(bcdiv($pventa, '1', 2), 2, '.', ',') . "</td>
                        <td class='text-center'>$" . number_format(bcdiv($ptotal, '1', 2), 2, '.', ',') . "</td>
                        <td class='text-center'>$" . number_format(bcdiv($descuento, '1', 2), 2, '.', ',') . "</td>
                        <td class='text-center'>
                            <div class='input-group'>
                                <div class='dropdown'>
                                    <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown'>Traslados <span class='caret'></span></button>
                                    <ul class='dropdown-menu'>
                                        $optraslados
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class='input-group'>
                                <div class='dropdown'>
                                    <button type='button' class='button-impuesto dropdown-toggle' data-bs-toggle='dropdown'>Retenciones <span class='caret'></span></button>
                                    <ul class='dropdown-menu'>
                                        $opretencion
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td title='Da click para agregar Observaciones' data-bs-toggle='modal' data-bs-target='#modal-observaciones' onclick=\"setIDTMP($id_tmp,'$observaciones');\"  style='vertical-align:middle; cursor: pointer; color: #17177C; text-align:center; white-space: pre'>$obser </td>
                        <td><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Editar Producto'  type='button' data-bs-toggle='dropdown'><span class='fas fa-edit text-muted samll'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                        <li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#editar-producto' onclick='editarConcepto($id_tmp);'>Editar en cotización <span class='fas fa-edit text-muted small'></span></a></li>";

                        if($idproducto >0 ){
                            $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#nuevo-producto' onclick='editarProductoCot($idproducto,$id_tmp);'>Editar en productos <span class='fas fa-edit text-muted small'></span></a></li>";
                        }
                        
                        $datos .= "<li class='notification-link py-1 ps-3' ><a class='text-decoration-none text-secondary-emphasis' onclick='eliminar($id_tmp, $cantidad, $idproducto);'>Eliminar <span class='fas fa-times text-muted'></span></a></li>
                        </ul>
                        </div></td>
                    </tr>
                     ";
        }

        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);
        $subdescuento = $sumador_total - $sumador_descuento;

        $datos .= "</tbody><tfoot><tr>
        <th colspan='5'>
        <ul class='list-group mb-3 mt-3 pe-0'>
        <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-semibold'>SUBTOTAL:</h6>
            </div>
            <span class='titulo-lista fw-semibold fs-6 pe-0 me-0'>$" . number_format(bcdiv($sumador_total, '1', 2), 2, '.', ',') . " </span>
        </li>";

        if ($sumador_iva > 0) {
            $datos .= " <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-semibold'>TRASLADOS:</h6>
            </div>
            <span class='titulo-lista fw-semibold fs-6 pe-0 me-0'>$" . number_format(bcdiv($sumador_iva, '1', 2), 2, '.', ',') . " </span>
        </li>";
        }
        if ($sumador_ret > 0) {
            $datos .= " <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-semibold'>RETENCIONES:</h6>
            </div>
            <span class='titulo-lista fw-semibold fs-6 pe-0 me-0'>$" . number_format(bcdiv($sumador_ret, '1', 2), 2, '.', ',') . " </span>
        </li>";
        }
        if ($sumador_descuento > 0) {
            $datos .= " <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-semibold'>DESCUENTOS:</h6>
            </div>
            <span class='titulo-lista fw-semibold fs-6 pe-0 me-0'>$" . number_format(bcdiv($sumador_descuento, '1', 2), 2, '.', ',') . " </span>
        </li>";

            $datos .= " <li class='list-group-item d-flex justify-content-between lh-sm'>
            <div>
                <h6 class='my-0 titulo-lista fs-6 fw-semibold'>SUBTOTAL - DESCUENTO:</h6>
            </div>
            <span class='titulo-lista fw-semibold fs-6 pe-0 me-0'>$" . number_format(bcdiv($subdescuento, '1', 2), 2, '.', ',') . " </span>
        </li>";
        }
        $datos .= "<li class='list-group-item d-flex justify-content-between lh-sm'>
        <div>
            <h6 class='my-0 titulo-lista fs-6 fw-bold'>TOTAL:</h6>
        </div>
        <span class='titulo-lista fw-bold fs-6 pe-0 me-0'>$" . number_format(bcdiv($total_factura, '1', 2), 2, '.', ',') . " </span>
    </li></ul></th></tfoot>";
        return $datos;
    }

    public function agregarObservaciones($t) {
        $insertado = false;
        $consulta = "UPDATE `tmpcotizacion` set observaciones_tmp=:observaciones where idtmpcotizacion= :id;";
        $valores = array("id" => $t->getIdtmp(),
            "observaciones" => $t->getObservacionestmp());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminar($idtemp) {
        $eliminado = false;
        $consulta = "DELETE FROM `tmpcotizacion` WHERE idtmpcotizacion=:id;";
        $valores = array("id" => $idtemp);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function checkProductoTMPAux($idtmp) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpcotizacion WHERE idtmpcotizacion=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function checkProductoAux($idtmp) {
        $consultado = false;
        $consulta = "SELECT cantinv, chinventario FROM productos_servicios WHERE idproser=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
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

        $cant = $canttmp + 1;
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

        $total = (( bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        if ($chinv == '1') {
            if ($cantidad <= 0) {
                $datos = "0El inventario no es suficiente para agregar más producto.";
            } else {
                $consulta = "UPDATE `tmpcotizacion` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmpcotizacion = :id;";
                $valores = array("id" => $idtmp,
                    "cant" => $cant,
                    "totuni" => bcdiv($totalun, '1', 2),
                    "impdesc" => bcdiv($impdesc, '1', 2),
                    "imptot" => bcdiv($total, '1', 2),
                    "traslados" => $traslados,
                    "retenciones" => $retenciones);
                $datos = $this->consultas->execute($consulta, $valores);
            }
        } else if ($chinv == '0') {
            $consulta = "UPDATE `tmpcotizacion` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmpcotizacion = :id;";
            $valores = array("id" => $idtmp,
                "cant" => $cant,
                "totuni" => bcdiv($totalun, '1', 2),
                "impdesc" => bcdiv($impdesc, '1', 2),
                "imptot" => bcdiv($total, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retenciones);
            $datos = $this->consultas->execute($consulta, $valores);
        }
        return $datos;
    }

    public function reducirProducto($idtmp) {
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

        $cant = $canttmp - 1;
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

        $total = (( bcdiv($totalun, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        $consulta = "UPDATE `tmpcotizacion` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmpcotizacion = :id;";
        $valores = array("id" => $idtmp,
            "cant" => $cant,
            "totuni" => bcdiv($totalun, '1', 2),
            "impdesc" => bcdiv($impdesc, '1', 2),
            "imptot" => bcdiv($total, '1', 2),
            "traslados" => $traslados,
            "retenciones" => $retenciones);
        $datos = $this->consultas->execute($consulta, $valores);
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
        $total = ((bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        if ($chinv == '1') {
            if ($restante < 0) {
                $datos = "0El inventario no es suficiente para agregar más producto.";
            } else {
                $consulta = "UPDATE `tmpcotizacion` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmpcotizacion = :id;";
                $valores = array("id" => $idtmp,
                    "cant" => $cant,
                    "totuni" => bcdiv($totalun, '1', 2),
                    "impdesc" => bcdiv($impdesc, '1', 2),
                    "imptot" => bcdiv($total, '1', 2),
                    "traslados" => $traslados,
                    "retenciones" => $retenciones);
                $datos = $this->consultas->execute($consulta, $valores);
            }
        } else if ($chinv == '0') {
            $consulta = "UPDATE `tmpcotizacion` set cantidad_tmp=:cant, totunitario_tmp=:totuni, impdescuento_tmp=:impdesc, imptotal_tmp=:imptot, trasladotmp=:traslados, retenciontmp=:retenciones  where idtmpcotizacion = :id;";
            $valores = array("id" => $idtmp,
                "cant" => $cant,
                "totuni" => bcdiv($totalun, '1', 2),
                "impdesc" => bcdiv($impdesc, '1', 2),
                "imptot" => bcdiv($total, '1', 2),
                "traslados" => $traslados,
                "retenciones" => $retenciones);
            $datos = $this->consultas->execute($consulta, $valores);
        }
        return $datos;
    }

    private function getTMPById($idtmp) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpcotizacion WHERE idtmpcotizacion=:id";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getDatosTMP($idtmp) {
        $tmp = $this->getTMPById($idtmp);
        $datos = "";
        foreach ($tmp as $actual) {
            $idtmp = $actual['idtmpcotizacion'];
            $nombre = $actual['tmpnombre'];
            $cantidad = $actual['cantidad_tmp'];
            $precio = $actual['precio_tmp'];
            $totunitario = $actual['totunitario_tmp'];
            $descuento = $actual['descuento_tmp'];
            $impdescuento = $actual['impdescuento_tmp'];
            $total = $actual['imptotal_tmp'];
            $observaciones = $actual['observaciones_tmp'];
            $clvfiscal = $actual['clvfiscaltmp'];
            $clvunidad = $actual['clvunidadtmp'];
            $traslados = $actual['trasladotmp'];
            $retencion = $actual['retenciontmp'];

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
                        $iconT = "far fa-check-square mx-2";
                        $checkedT = "checked";
                        break;
                    } else {
                        $iconT = "far fa-square mx-2";
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
                        $iconR = "far fa-check-square mx-2";
                        $checkedR = "checked";
                        break;
                    } else {
                        $iconR = "far fa-square mx-2";
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

    public function checkConcepto($t) {
        $idtmp = $t->getIdtmp();
        $cant = $t->getCantidadtmp();
        $check = $this->checkProductoTMPAux($idtmp);
        foreach ($check as $actual) {
            $idproducto = $actual['id_productotmp'];
        }

        $chinv = 0;
        $cantidad = 0;

        $prod = $this->checkProductoAux($idproducto);
        foreach ($prod as $pactual) {
            $chinv = $pactual['chinventario'];
            $cantidad = $pactual['cantinv'];
        }

        $restante = $cantidad - $cant;
        if ($chinv == '1') {
            if ($restante < 0) {
                $datos = "0El inventario no es suficiente para agregar más producto.";
            } else {
                $datos = $this->actualizarConcepto($t);
            }
        } else if ($chinv == '0') {
            $datos = $this->actualizarConcepto($t);
        }
        return $datos;
    }

    public function actualizarConcepto($t) {
        $actualizado = false;
        $importe = $t->getImportetmp() - $t->getImpdescuento();
        $traslados = $this->reBuildArray2($importe, $t->getIdtraslados());
        $retenciones = $this->reBuildArray2($importe, $t->getIdretencion());

        $consulta = "UPDATE `tmpcotizacion` SET tmpnombre=:nombre, cantidad_tmp=:cantidad, precio_tmp=:precio, totunitario_tmp=:totuni, descuento_tmp=:descuento, impdescuento_tmp=:impdescuento, imptotal_tmp=:imptotal, observaciones_tmp=:observaciones, trasladotmp=:tras, retenciontmp=:ret, clvfiscaltmp=:cfiscal, clvunidadtmp=:cunidad WHERE idtmpcotizacion=:id;";
        $valores = array("id" => $t->getIdtmp(),
            "nombre" => $t->getDescripciontmp(),
            "cantidad" => $t->getCantidadtmp(),
            "precio" => $t->getPreciotmp(),
            "totuni" => $t->getImportetmp(),
            "descuento" => $t->getDescuento(),
            "impdescuento" => $t->getImpdescuento(),
            "imptotal" => $t->getImptotal(),
            "observaciones" => $t->getObservacionestmp(),
            "tras" => $traslados,
            "ret" => $retenciones,
            "cfiscal" => $t->getClvfiscal(),
            "cunidad" => $t->getClvunidad());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function verificarProductos($sessionId) {
        $consultado = false;
        $consulta = "SELECT * FROM tmpcotizacion WHERE session_id=:idsession;";
        $valores = array("idsession" => $sessionId);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function validarCotizacion($sid) {
        $validar = false;
        $prod = 0;
        $productos = $this->verificarProductos($sid);
        foreach ($productos as $actual) {
            $prod ++;
        }
        if ($prod == 0) {
            $validar = true;
            echo "0No se han agregado productos a la factura.";
        }
        return $validar;
    }

    public function nuevaCotizacion($c) {
        $insertado = false;
        $validar = $this->validarCotizacion($c->getSessionid());
        if (!$validar) {
            $insertado = $this->insertarCotizacion($c);
        }
        return $insertado;
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

    private function updateFolioConsecutivo($id) {
        $consultado = false;
        $consulta = "UPDATE folio SET consecutivo=(consecutivo+1) WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->execute($consulta, $val);
        return $consultado;
    }

    private function getFoliobyID($id) {
        $consultado = false;
        $consulta = "SELECT * FROM folio WHERE idfolio=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
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

    public function insertarCotizacion($f) {
        $insertado = false;
        $hoy = date("Y-m-d");
        $tag = $this->genTag();

        $folios = $this->getFolio($f->getFolio());
        $Fdiv = explode("</tr>", $folios);
        $serie = $Fdiv[0];
        $letra = $Fdiv[1];
        $nfolio = $Fdiv[2];

        $idusuario = $_SESSION[sha1("idusuario")];
        $documento = $this->getDocumento();

        $consulta = "INSERT INTO `datos_cotizacion` VALUES (:id, :fecha_creacion, :serie, :letra, :folio, :idcliente, :nombrecliente, :email, :email2, :email3, :idmetodopago, :idformapago, :idmoneda, :iduso, :tipocomprobante, :iddatos, :observaciones, :subtotal, :subiva, :subret, :totdescuentos, :total, :envio, :chfirmar, :iddocumento, :documento, :expcot, :tag, :sid, :fechaexportar, :horaexportar);";
        $valores = array("id" => null,
            "fecha_creacion" => $hoy,
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "idcliente" => $f->getIdcliente(),
            "nombrecliente" => $f->getNombrecliente(),
            "email" => $f->getEmailcliente(),
            "email2" => $f->getEmailcliente2(),
            "email3" => $f->getEmailcliente3(),
            "idmetodopago" => $f->getIdmetodopago(),
            "idformapago" => $f->getIdformapago(),
            "idmoneda" => $f->getIdmoneda(),
            "iduso" => $f->getIdusocfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "iddatos" => $f->getIddatosfacturacion(),
            "observaciones" => $f->getObservaciones(),
            "subtotal" => null,
            "subiva" => null,
            "subret" => null,
            "totdescuentos" => null,
            "total" => null,
            "envio" => '0',
            'chfirmar' => $f->getChfirmar(),
            "iddocumento" => $idusuario,
            "documento" => $documento,
            "expcot" => '0',
            "tag" => $tag,
            "sid" => null,
            "fechaexportar" => null,
            "horaexportar" => null
        );

        $insertado = $this->consultas->execute($consulta, $valores);
        $detalle = $this->detalleCotizacion($f->getSessionid(), $tag);
        $datos = '+' . $tag;
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
                $div = explode("<impuesto>", $traslados);
                foreach ($div as $d) {
                    $div2 = explode("-", $d);
                    if ($porcentaje == $div2[1] && $impuesto == $div2[2]) {
                        $Timp += $div2[0];
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

    public function modificarChIva($idtmp, $chiva, $chret) {
        $check = $this->checkProductoTMPAux($idtmp);
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $precio_tmp = $actual['precio_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
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

        $consulta = "UPDATE `tmpcotizacion` set trasladotmp=:chiva, retenciontmp=:chret, totunitario_tmp=:totun, impdescuento_tmp=:impdesc, imptotal_tmp=:total where idtmpcotizacion = :idtmp;";
        $val = array("chiva" => $traslados,
            "chret" => $retenciones,
            "totun" => $totalun,
            "impdesc" => $impdesc,
            "total" => $total,
            "idtmp" => $idtmp);
        $datos = $this->consultas->execute($consulta, $val);
        return $datos;
    }


    public function detalleCotizacion($idsession, $tag) {
        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $productos = $this->getTMP($idsession);
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmpcotizacion'];
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
            $consulta2 = "INSERT INTO `detalle_cotizacion` VALUES (:id,:cantidad,:precio, :subtotal, :descuento, :impdescuento, :totdescuento, :traslados, :retenciones, :observaciones, :idproducto, :nombre, :chinv, :clvfiscal, :clvunidad, :iddatoscot);";
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
                "nombre" => $nombre,
                "chinv" => $chinv,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "iddatoscot" => $tag);
            $insertado = $this->consultas->execute($consulta2, $valores2);
        }
        $totaltraslados = $this->checkArray($idsession, '1');
        $totalretencion = $this->checkArray($idsession, '2');
        $borrar = "DELETE FROM `tmpcotizacion` WHERE session_id=:id;";
        $valores3 = array("id" => $idsession);
        $eliminado = $this->consultas->execute($borrar, $valores3);

        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);
        $update = "UPDATE `datos_cotizacion` SET subtot=:subtotal, subiva=:iva, subret=:ret, totdesc=:totdesc, totalcotizacion=:total WHERE tagcotizacion=:tag;";
        $valores4 = array("tag" => $tag,
            "subtotal" => bcdiv($sumador_total, '1', 2),
            "iva" => $totaltraslados,
            "ret" => $totalretencion,
            "totdesc" => bcdiv($sumador_descuento, '1', 2),
            "total" => bcdiv($total_factura, '1', 2));
        $insertado = $this->consultas->execute($update, $valores4);
        return $insertado;
    }

    public function getCotizacionById($idcotizacion) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_cotizacion WHERE iddatos_cotizacion=:cid;";
        $val = array("cid" => $idcotizacion);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getDatosFacByIdAux($id) {
        $consultado = false;
        $consulta = "SELECT * FROM datos_facturacion WHERE id_datos=:id;";
        $val = array("id" => $id);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getDatosFacById($id) {
        $catalogo = $this->getDatosFacByIdAux($id);
        foreach ($catalogo as $actual) {
            $nombre = $actual['nombre_contribuyente'];
            $rfc = $actual['rfc'];
            $razonsocial = $actual['razon_social'];
            $cregimen = $actual['c_regimenfiscal'];
            $regimen = $actual['regimen_fiscal'];
            $cp = $actual['codigo_postal'];
            $datos = "$nombre</tr>$rfc</tr>$razonsocial</tr>$cregimen</tr>$regimen</tr>$cp";
        }
        return $datos;
    }

    public function getDatosCotizacion($idcotizacion) {
        $factura = $this->getCotizacionById($idcotizacion);
        $datos = "";
        foreach ($factura as $facturaactual) {
            $idcotizacion = $facturaactual['iddatos_cotizacion'];
            $fecha_creacion = $facturaactual['fecha_creacion'];
            $serie = $facturaactual['serie'];
            $letra = $facturaactual['letra'];
            $folio = $facturaactual['foliocotizacion'];
            $idcliente = $facturaactual['cot_idcliente'];
            $nombrecliente = $facturaactual['nombrecliente'];
            $emailcliente = $facturaactual['emailcot'];
            $emailcliente2 = $facturaactual['emailcot2'];
            $emailcliente3 = $facturaactual['emailcot3'];
            $idforma_pago = $facturaactual['id_forma_pago'];
            $idmetodo_pago = $facturaactual['id_metodo_pago'];
            $idmoneda = $facturaactual['id_moneda'];
            $iduso_cfdi = $facturaactual['id_uso_cfdi'];
            $idtipo_comprobante = $facturaactual['id_tipo_comprobante'];
            $iddatos = $facturaactual['iddatosfacturacion'];
            $facturacion = $this->getDatosFacById($iddatos);
            $observaciones = $facturaactual['observaciones'];
            $chfirmar = $facturaactual['chfirmar'];
            $documento = $facturaactual['documento'];
            $tag = $facturaactual['tagcotizacion'];

            $datos = "$idcotizacion</tr>$serie</tr>$letra</tr>$folio</tr>$fecha_creacion</tr>$idcliente</tr>$nombrecliente</tr>$emailcliente</tr>$emailcliente2</tr>$emailcliente3</tr>$idmetodo_pago</tr>$idmoneda</tr>$iduso_cfdi</tr>$idforma_pago</tr>$idtipo_comprobante</tr>$iddatos</tr>$facturacion</tr>$observaciones</tr>$chfirmar</tr>$documento</tr>$tag";
            break;
        }
        return $datos;
    }

    public function getDetalle($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detalle_cotizacion det WHERE tagdetalle=:tag";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function productosCotizacion($tag, $sessionid) {
        $insertado = false;
        $productos = $this->getDetalle($tag);
        foreach ($productos as $productoactual) {
            $idproducto = $productoactual["id_prodservicio"];
            $nombre = $productoactual["cotizacion_producto"];
            $cantidad = $productoactual["cantidad"];
            $precio = $productoactual["precio"];
            $totunitario = $productoactual["totunitario"];
            $descuento = $productoactual['descuento'];
            $impdescuento = $productoactual['impdescuento'];
            $totdescuento = $productoactual['totaldescuento'];
            $observaciones = $productoactual['observacionesp'];
            $traslados = $productoactual['traslados'];
            $retenciones = $productoactual['retenciones'];
            $chinv = $productoactual['chinv'];
            $clvfiscal = $productoactual['clvfiscal'];
            $clvunidad = $productoactual['clvunidad'];

            $consulta = "INSERT INTO `tmpcotizacion` VALUES (:id, :idproducto, :nombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :traslado, :retencion, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
            $valores = array("id" => null,
                "idproducto" => $idproducto,
                "nombre" => $nombre,
                "cantidad" => $cantidad,
                "precio" => $precio,
                "importe" => $totunitario,
                "descuento" => $descuento,
                "impdescuento" => $impdescuento,
                "imptotal" => $totdescuento,
                "traslado" => $traslados,
                "retencion" => $retenciones,
                "observaciones" => $observaciones,
                "chinv" => $chinv,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "session" => $sessionid);
            $insertado = $this->consultas   ->execute($consulta, $valores);
        }
        return $insertado;
    }

    public function actualizarCotizacion($f) {
        $actualizado = false;
        $updfolio = "";
        $serie = "";
        $letra = "";
        $nfolio = "";
        if ($f->getFolio() != '0') {
            $updfolio = "serie=:serie, letra=:letra, foliocotizacion=:folio,";
            $folios = $this->getFolio($f->getFolio());
            $Fdiv = explode("</tr>", $folios);
            $serie = $Fdiv[0];
            $letra = $Fdiv[1];
            $nfolio = $Fdiv[2];
        }

        $consulta = "UPDATE `datos_cotizacion` SET $updfolio cot_idcliente=:idcliente, nombrecliente=:nombrecliente, emailcot=:email, emailcot2=:email2, emailcot3=:email3, id_metodo_pago=:idmetodopago, id_forma_pago=:idformapago, id_moneda=:idmoneda, id_uso_cfdi=:iduso, id_tipo_comprobante=:tipocomprobante, iddatosfacturacion=:iddatos, observaciones=:observaciones, chfirmar=:chfirmar WHERE iddatos_cotizacion=:idcotizacion;";
        $valores = array("idcotizacion" => $f->getIddatos_cotizacion(),
            "serie" => $serie,
            "letra" => $letra,
            "folio" => $nfolio,
            "idcliente" => $f->getIdcliente(),
            "nombrecliente" => $f->getNombrecliente(),
            "email" => $f->getEmailcliente(),
            "email2" => $f->getEmailcliente2(),
            "email3" => $f->getEmailcliente3(),
            "idmetodopago" => $f->getIdmetodopago(),
            "idformapago" => $f->getIdformapago(),
            "idmoneda" => $f->getIdmoneda(),
            "iduso" => $f->getIdusocfdi(),
            "tipocomprobante" => $f->getTipocomprobante(),
            "iddatos" => $f->getIddatosfacturacion(),
            "observaciones" => $f->getObservaciones(),
            "chfirmar" => $f->getChfirmar());
        $actualizado = $this->consultas->execute($consulta, $valores);
        $datos = $this->actualizarDetalle($f->getSessionid(), $f->getTag());
        return '+' . $f->getTag() . '+';
    }

    private function eliminarCotizacionAux($tag) {
        $eliminado = false;
        $consulta = "DELETE FROM `detalle_cotizacion` WHERE tagdetalle=:tag;";
        $valores = array("tag" => $tag);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    public function actualizarDetalle($idsession, $tag) {
        $sumador_total = 0;
        $sumador_iva = 0;
        $sumador_ret = 0;
        $sumador_descuento = 0;
        $this->eliminarCotizacionAux($tag);

        $productos = $this->getTMP($idsession);
        foreach ($productos as $productoactual) {
            $id_tmp = $productoactual['idtmpcotizacion'];
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

            $importe = bcdiv($ptotal, '1', 2) - bcdiv($descuento, '1', 2);
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
            $consulta2 = "INSERT INTO `detalle_cotizacion` VALUES (:id,:cantidad,:precio, :subtotal, :descuento, :impdescuento, :totdescuento, :traslados, :retenciones, :observaciones, :idproducto, :nombre, :chinv, :clvfiscal, :clvunidad, :tag);";
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
                "nombre" => $nombre,
                "chinv" => $chinv,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "tag" => $tag);
            $insertado = $this->consultas->execute($consulta2, $valores2);
        }
        $totaltraslados = $this->checkArray($idsession, '1');
        $totalretencion = $this->checkArray($idsession, '2');
        $borrar2 = "DELETE FROM `tmpcotizacion` WHERE session_id=:id;";
        $valores3 = array("id" => $idsession);
        $eliminado = $this->consultas->execute($borrar2, $valores3);

        $total_factura = ((bcdiv($sumador_total, '1', 2) + bcdiv($sumador_iva, '1', 2)) - bcdiv($sumador_ret, '1', 2)) - bcdiv($sumador_descuento, '1', 2);
        $update = "UPDATE `datos_cotizacion` set subtot=:subtotal, subiva=:iva, subret=:ret, totdesc=:totdesc, totalcotizacion=:total WHERE tagcotizacion=:tag;";
        $valores4 = array("tag" => $tag,
            "subtotal" => bcdiv($sumador_total, '1', 2),
            "iva" => $totaltraslados,
            "ret" => $totalretencion,
            "totdesc" => bcdiv($sumador_descuento, '1', 2),
            "total" => bcdiv($total_factura, '1', 2));
        $insertado = $this->consultas->execute($update, $valores4);
        return $insertado;
    }

    public function getCotizaciones($idcotizacion) {
        $consultado = false;
        $consulta = "select * from datos_cotizacion where iddatos_cotizacion=:id;";
        $val = array("id" => $idcotizacion);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function getIMGProducto($pid) {
        $img = "";
        $datos = $this->getProductoById($pid);
        foreach ($datos as $actual) {
            $img = $actual['imagenprod'];
        }
        return $img;
    }

    public function eliminarCotizacion($idCotizacion) {
        $eliminado = false;
        $consulta = "DELETE FROM `datos_cotizacion` WHERE iddatos_cotizacion=:id;";
        $valores = array("id" => $idCotizacion);
        $eliminado = $this-> consultas->execute($consulta, $valores);
        $eliminado2 = $this->eliminarCotizacionAux($idCotizacion);
        return $eliminado;
    }

    public function getCorreo($cid) {
        $datos = "";
        $correos = $this->getCorreosAux($cid);
        foreach ($correos as $actual) {
            $correo1 = $actual['emailcot'];
            $correo2 = $actual['emailcot2'];
            $correo3 = $actual['emailcot3'];
            $datos .= "$correo1<corte>$correo2<corte>$correo3";
        }
        return $datos;
    }

    private function getCorreosAux($cid) {
        $consultado = false;
        $consulta = "SELECT nombrecliente, emailcot, emailcot2, emailcot3 FROM datos_cotizacion WHERE iddatos_cotizacion=:id;";
        $val = array("id" => $cid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getConfigMailAux() {
        $consultado = false;
        $consulta = "SELECT * FROM correoenvio WHERE chuso3=:id;";
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
        $body = $cc->getMailBody('3');
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

            $mail->Subject = iconv("utf-8", "windows-1252",$asunto);
            $mail->isHTML(true);
            $mail->Body = $this->bodyMail($asunto, $saludo, $sm->getRazonsocial(), $msg, $logo);
            if ($sm->getChmail1() == '1') {
                $mail->addAddress($sm->getMailalt1());
            }
            if ($sm->getChmail2() == "") {
                $mail->addAddress($sm->getMailalt2());
            }
            if ($sm->getChmail3() !== "") {
                $mail->addAddress($sm->getMailalt3());
            }

            $mail->addStringAttachment($sm->getPdfstring(), 'cotizacion' . $sm->getFolio() . '.pdf');
            if (!$mail->send()) {
                echo '0No se ha podido mandar el mensaje';
                echo '0Mailer Error: ' . $mail->ErrorInfo;
            } else {
                return '1Se envió la cotización.';
            }
        } else {
            return "0No se ha configurado un correo de envio para esta área.";
        }
    }

    private function bodyMail($asunto, $saludo, $nombre, $msg, $logo) {
        $archivo = $_SESSION[sha1("database")].".ini";
        $ajustes = parse_ini_file($archivo, true);
        if (!$ajustes) {
            throw new Exception("No se puede abrir el archivo " . $archivo);
        }
        $rfcfolder = $ajustes['cron']['rfcfolder'];

        $txt = str_replace("<corte>", "</p><p style='font-size:18px; text-align: justify;'>", $msg);
        $message = "<html>
                        <body>
                            <table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0' style='border-radius: 25px;'>
                                <tr><td>
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
                                    </td></tr>
                            </table>
                        </body>
                    </html>";
        return $message;
    }

    private function obtenerTagCotizacion($idcotizacion){
        $tag_coti = "";
        $consulta = "SELECT tagcotizacion FROM datos_cotizacion WHERE iddatos_cotizacion = :id";
        $val = array("id" => $idcotizacion);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach($stmt as $row){
            $tag_coti = $row['tagcotizacion'];
        }
        return $tag_coti;
    }

    public function cobrarCotizacion($idcotizacion, $sid){
        $n = 0;
        $tag_coti = $this->obtenerTagCotizacion($idcotizacion);
        $consulta = "SELECT dc.id_prodservicio, ps.codproducto, dc.clvfiscal, dc.clvunidad, dc.cotizacion_producto, dc.precio, dc.cantidad, dc.descuento, dc.impdescuento, dc.totunitario, dc.totaldescuento, dc.traslados, dc.retenciones
                    FROM detalle_cotizacion AS dc
                    INNER JOIN productos_servicios AS ps ON ps.idproser = dc.id_prodservicio
                    WHERE tagdetalle = :tagdet";
        $val = array("tagdet" => $tag_coti);
        $stmt = $this->consultas->getResults($consulta, $val);
        foreach($stmt AS $row){
            $id_prodservicio = $row['id_prodservicio'];
            $codproducto = $row['codproducto'];
            $clvfiscal = $row['clvfiscal'];
            $clvunidad = $row['clvunidad'];
            $cotizacion_producto = $row['cotizacion_producto'];
            $precio = $row['precio'];
            $cantidad = $row['cantidad'];
            $descuento = $row['descuento'];
            $impdescuento = $row['impdescuento'];
            $totunitario = $row['totunitario'];
            $totaldescuento = $row['totaldescuento'];
            $traslados = $row['traslados'];
            $retenciones = $row['retenciones'];

            $consulta = "INSERT INTO tmpticket (tmpidprod, tmpcod, tmpclvfiscal, tmpclvunidad, tmpprod, tmpprecio, tmpcant, descuento, impdescuento, tmpimporte, totaldescuento, tmptraslados, tmpretenciones, sid) 
                        VALUES (:tmpidprod, :tmpcod, :tmpclvfiscal, :tmpclvunidad, :tmpprod, :tmpprecio, :tmpcant, :descuento, :impdescuento, :tmpimporte, :totaldescuento, :tmptraslados, :tmpretenciones, :sid)";
            $values = array(
                'tmpidprod' => $id_prodservicio,
                'tmpcod' => $codproducto,
                'tmpclvfiscal' => $clvfiscal,
                'tmpclvunidad' => $clvunidad,
                'tmpprod' => $cotizacion_producto,
                'tmpprecio' => $precio,
                'tmpcant' => $cantidad,
                'descuento' =>  $descuento,
                'impdescuento' => $impdescuento,
                'tmpimporte' => $totunitario,
                'totaldescuento' => $totaldescuento,
                'tmptraslados' => $traslados,
                'tmpretenciones' => $retenciones,
                'sid' => $sid
            );    
            
            $insertado = $this->consultas->execute($consulta, $values);
            $n++;
        }
        return $sid;
    }

    private function getTagbyIDAux($id)
    {
        $datos = false;
        $consulta = "SELECT iddatos_venta, tagventa, formapago, tagfactura, tarjeta FROM datos_venta WHERE iddatos_venta=:id";
        $val = array("id" => $id);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    public function getFolioFacturaAux($tag) {
        $consultado = false;
        $consulta = "SELECT CONCAT(letra, folio_interno_fac) AS folio_interno FROM datos_factura WHERE tagfactura=:cid;";
        $val = array("cid" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getFolioFactura($tag) {
        $folio = "";
        $datos = $this->getFolioFacturaAux($tag);
        foreach ($datos as $actual) {
            $folio = $actual['folio_interno'];
        }
        return $folio;
    }

    public function getTagCotizacionByIdAux($tag) {
        $consultado = false;
        $consulta = "SELECT expfactura FROM datos_cotizacion WHERE iddatos_cotizacion=:cid;";
        $val = array("cid" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getTagCotizacionById($tag) {
        $folio = "";
        $datos = $this->getTagCotizacionByIdAux($tag);
        foreach ($datos as $actual) {
            $folio = $actual['expfactura'];
        }
        return $folio;
    }

    public function validarExistenciaFacturaCotizacion($idcot, $tagcot, $sid)
    {
        $datos = "";
        $tag = $this->getTagCotizacionById($idcot);
        $folio = $this->getFolioFactura($tag);
        if ($folio) {
            $datos = "0Ya existe una factura relacionada a esta cotización, con folio " . $folio . ".";
        } else {
            $datos = $this->exportarprodCotizacion($tagcot, $sid);
        }
        return $datos;
    }

    public function exportarprodCotizacion($tag, $sessionid) {
        $insertado = false;
        $productos = $this->getDetalle($tag);
        foreach ($productos as $productoactual) {
            $idproducto = $productoactual["id_prodservicio"];
            $nombre = $productoactual["cotizacion_producto"];
            $cantidad = $productoactual["cantidad"];
            $precio = $productoactual["precio"];
            $totunitario = $productoactual["totunitario"];
            $descuento = $productoactual['descuento'];
            $impdescuento = $productoactual['impdescuento'];
            $totdescuento = $productoactual['totaldescuento'];
            $traslados = $productoactual['traslados'];
            $retenciones = $productoactual['retenciones'];
            $observaciones = $productoactual['observacionesp'];
            $chinv = $productoactual['chinv'];
            $clvfiscal = $productoactual['clvfiscal'];
            $clvunidad = $productoactual['clvunidad'];

            $consulta = "INSERT INTO `tmp` VALUES (:id, :idproducto, :nombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :traslado, :ret, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
            $valores = array("id" => null,
                "idproducto" => $idproducto,
                "nombre" => $nombre,
                "cantidad" => $cantidad,
                "precio" => $precio,
                "importe" => $totunitario,
                "descuento" => $descuento,
                "impdescuento" => $impdescuento,
                "imptotal" => $totdescuento,
                "traslado" => $traslados,
                "ret" => $retenciones,
                "observaciones" => $observaciones,
                "chinv" => $chinv,
                "clvfiscal" => $clvfiscal,
                "clvunidad" => $clvunidad,
                "session" => $sessionid);
            $insertado = $this->consultas->execute($consulta, $valores);
            if ($chinv == '1') {
                $remover = $this->removerInventario($idproducto, $cantidad);
            }
        }
        return $insertado;
    }

    public function removerInventario($idproducto, $cantidad) {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv-:cantidad where idproser=:idproducto;";
        $valores = array("idproducto" => $idproducto, "cantidad" => $cantidad);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getTagbyID($idcotizacion) {
        $tag = "";
        $datos = $this->getCotizacionById($idcotizacion);
        foreach ($datos as $actual) {
            $tag = $actual['tagcotizacion'];
        }
        return $tag;
    }

    private function getDetalleActualizar($tag) {
        $consultado = false;
        $consulta = "SELECT * FROM detalle_cotizacion c INNER JOIN productos_servicios p ON (c.id_prodservicio=p.idproser) WHERE c.tagdetalle=:tag";
        $val = array("tag" => $tag);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function arrayImpuestos($impuestos, $importe) {
        $div = explode("<impuesto>", $impuestos);
        $row = array();
        $Timp = 0;
        foreach ($div as $d) {
            $div2 = explode("-", $d);
            $imp = $importe * $div2[1];
            $Timp += $imp;
            if ($imp > 0) {
                $row[] = bcdiv($imp, '1', 2) . '-' . $div2[1] . '-' . $div2[2];
            }
        }
        $traslados = implode("<impuesto>", $row);
        return "$traslados</tr>$Timp";
    }

    public function checkArrayPrecios($idcot, $idimpuesto) {
        $productos = $this->getDetalle($idcot);
        $imptraslados = $this->getImpuestos($idimpuesto);
        $row = array();
        foreach ($imptraslados as $tactual) {
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $Timp = 0;

            foreach ($productos as $productoactual) {
                if ($idimpuesto == '1') {
                    $traslados = $productoactual['traslados'];
                } else if ($idimpuesto == '2') {
                    $traslados = $productoactual['retenciones'];
                }
                $div = explode("<impuesto>", $traslados);
                foreach ($div as $d) {
                    $div2 = explode("-", $d);
                    if ($porcentaje == $div2[1] && $impuesto == $div2[2]) {
                        $Timp += $div2[0];
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

    public function actualizarPrecios($idcotizacion) {
        $tag = $this->getTagbyID($idcotizacion);
        $detalle = $this->getDetalleActualizar($tag);
        $subtotal = 0;
        $subtras = 0;
        $subret = 0;
        $totdesc = 0;
        foreach ($detalle as $actual) {
            $iddetalle = $actual['iddetalle_cotizacion'];
            $precio = $actual['precio_venta'];
            $cantidad = $actual['cantidad'];
            $descuento = $actual['descuento'];
            $traslados = $actual['traslados'];
            $retencion = $actual['retenciones'];
            $totun = bcdiv($precio, '1', 2) * $cantidad;
            $impdescuento = bcdiv($totun, '1', 2) * ($descuento / 100);
            $totaldescuento = bcdiv($totun, '1', 2) - bcdiv($impdescuento, '1', 2);

            $arrtraslados = $this->arrayImpuestos($traslados, $totaldescuento);
            $divT = explode("</tr>", $arrtraslados);
            $trasimp = $divT[0];
            $Timp = $divT[1];

            $arrretenciones = $this->arrayImpuestos($retencion, $totaldescuento);
            $divR = explode("</tr>", $arrretenciones);
            $retenciones = $divR[0];
            $Tret = $divR[1];

            $subtotal += $totun;
            $totdesc += $impdescuento;
            $subtras += $Timp;
            $subret += $Tret;

            $actualizado = false;
            $consulta = "UPDATE `detalle_cotizacion` SET precio=:precio, totunitario=:totunitario, impdescuento=:impdescuento, totaldescuento=:totaldescuento, traslados=:traslados, retenciones=:retenciones WHERE iddetalle_cotizacion=:id;";
            $valores = array("id" => $iddetalle,
                "precio" => bcdiv($precio, '1', 2),
                "totunitario" => bcdiv($totun, '1', 2),
                "impdescuento" => bcdiv($impdescuento, '1', 2),
                "totaldescuento" => bcdiv($totaldescuento, '1', 2),
                "traslados" => $trasimp,
                "retenciones" => $retenciones);
            $insertado = $this->consultas->execute($consulta, $valores);
        }

        $total = ((bcdiv($subtotal, '1', 2) + bcdiv($subtras, '1', 2)) - bcdiv($subret, '1', 2)) - bcdiv($totdesc, '1', 2);
        $totaltraslados = $this->checkArrayPrecios($idcotizacion, '1');
        $totalretencion = $this->checkArrayPrecios($idcotizacion, '2');

        $actualizado = false;
        $consulta = "UPDATE `datos_cotizacion` SET subtot=:subtot, totdesc=:totdesc, totalcotizacion=:totalcotizacion, subiva=:traslados, subret=:retenciones WHERE tagcotizacion=:tag;";
        $valores = array("tag" => $tag,
            "subtot" => bcdiv($subtotal, '1', 2),
            "traslados" => $totaltraslados,
            "retenciones" => $totalretencion,
            "totdesc" => bcdiv($totdesc, '1', 2),
            "totalcotizacion" => bcdiv($total, '1', 2));
        $insertado2 = $this->consultas->execute($consulta, $valores);
        return $insertado2;
    }

    private function getRestantesAux($idcotizacion) {
        $consultado = false;
        $consulta = "SELECT restante FROM anticipo where anticipo_idcotizacion=:id order by idanticipo desc limit 1;";
        $val = array("id" => $idcotizacion);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getRestanteAnticipo($cid, $totcot) {
        $check = false;
        $restante = 0;
        $anticipos = $this->getRestantesAux($cid);
        foreach ($anticipos as $actual) {
            $restante = $actual['restante'];
            $check = true;
        }

        if (!$check) {
            $restante = $totcot;
        }
        return $restante;
    }

    public function getCotizacionAnticipo($idcotizacion) {
        $datos = "";
        $cotizacion = $this->getCotizacionById($idcotizacion);
        foreach ($cotizacion as $actual) {
            $creacion = date('d/m/Y');
            $total = $this->getRestanteAnticipo($idcotizacion, $actual['totalcotizacion']);
            $nombrecliente = $actual['nombrecliente'];
            $folio = $actual['letra'] . $actual['foliocotizacion'];
            $anticipo = ($total / 2);
            $restante = $total - $anticipo;
            $letras = NumeroALetras::convertir(bcdiv($anticipo, '1', 2), 'pesos', 'centavos');
            $div = explode(".", bcdiv($anticipo, '1', 2));
            $mensaje = addslashes("Se recibio de $nombrecliente la cantidad de $ " . bcdiv($anticipo, 1, 2) . " ($letras $div[1]/100 M.N.) por concepto del 50% de anticipo por la cotizacion de servicios con folio $folio.");
            $datos = "$nombrecliente</tr>$creacion</tr>$total</tr>$anticipo</tr>$restante</tr>$folio</tr>$mensaje";
        }
        return $datos;
    }
    
    public function transcribirCantidad($idcot, $cantidad) {
        $mensaje = "";
        $cotizacion = $this->getCotizacionById($idcot);
        foreach ($cotizacion as $actual) {
            $total = $actual['totalcotizacion'];
            if ($cantidad > $total) {
                $mensaje = "0La cantidad ingresada es mayor que el total de la cotización.";
            } else {
                $porcentaje = ($cantidad / $total) * 100;
                $nombrecliente = $actual['nombrecliente'];
                $folio = $actual['letra'] . $actual['foliocotizacion'];
                $letras = NumeroALetras::convertir(bcdiv($cantidad, '1', 2), 'pesos', 'centavos');
                $div = explode(".", bcdiv($cantidad, '1', 2));
                $mensaje = addslashes("Se recibió de $nombrecliente la cantidad de $ " . bcdiv($cantidad, 1, 2) . " ($letras $div[1]/100 M.N.) por concepto del " . bcdiv($porcentaje, 1, 2) . "% de anticipo por la cotización de servicios con folio $folio.");
            }
        }
        return $mensaje;
    }

    public function nuevoAnticipo($a) {
        if ($a->getRestante() < 0) {
            $datos = "0El monto ingresado es mayor que el restante de la cotización";
        } else {
            $datos = $this->insertarAnticipo($a);
        }
        return $datos;
    }

    private function insertarAnticipo($a) {
        $hoy = date('Y-m-d');
        $insertado = false;
        $consulta = "INSERT INTO `anticipo` VALUES (:id, :fechacreacion, :idcot, :monto, :restante, :autorizacion, :fechaanticipo, :imganticipo, :texto, :emision);";
        $valores = array("id" => null,
            "fechacreacion" => $hoy,
            "idcot" => $a->getIdcotizacion(),
            "monto" => bcdiv($a->getMonto(), '1', 2),
            "restante" => bcdiv($a->getRestante(), '1', 2),
            "autorizacion" => $a->getAutorizacion(),
            "fechaanticipo" => $a->getFecha(),
            "imganticipo" => $a->getImg(),
            "texto" => $a->getMensaje(),
            "emision" => $a->getEmision());
        $insertado = $this->consultas->execute($consulta, $valores);
        if ($a->getImg() != "") {
            rename('../temporal/anticipos/' . $a->getImg(), '../img/anticipos/' . $a->getImg());
        }
        return $insertado;
    }

    private function getAnticipos($idcotizacion) {
        $consultado = false;
        $consulta = "SELECT a.*,d.letra,d.foliocotizacion,d.totalcotizacion FROM anticipo a inner join datos_cotizacion d on (a.anticipo_idcotizacion=d.iddatos_cotizacion) where a.anticipo_idcotizacion='$idcotizacion'";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function listaAnticipo($idcotizacion) {
        $datos = "<thead><tr>
                    <th class='text-center'>Fecha de Creación </th>
                    <th class='text-center'>Monto</th>
                    <th class='text-center'>Restante</th>
                    <th class='text-center'>No. Autorización</th>
                    <th class='text-center'>Fecha Transacción</th>
                    <th class='text-center'>Comprobante</th>
                    <th class='text-center'>Opción</th>
                  </tr></thead><tbody>";

        $anticipos = $this->getAnticipos($idcotizacion);
        foreach ($anticipos as $actual) {
            $idanticipo = $actual['idanticipo'];
            $fechacreacion = $actual['fechacreacion'];
            $monto = $actual['montoanticipo'];
            $autorizacion = $actual['autorizacion'];
            $fechaanticipo = $actual['fechaanticipo'];
            $img = $actual['imganticipo'];
            $folio = $actual['letra'] . $actual['foliocotizacion'];
            $total = $actual['totalcotizacion'];
            $restante = $actual['restante'];

            $divf = explode("-", $fechacreacion);
            $fechacreacion = "$divf[2]/$divf[1]/$divf[0]";

            $divf2 = explode("-", $fechaanticipo);
            $fechaanticipo = "$divf2[2]/$divf2[1]/$divf2[0]";

            if ($img != "") {
                $link = "<a class='fw-semibold text-decoration-none' onclick='verImagenAnticipo($idanticipo)'>Ver archivo <i class='fas fa-file'></i></a>";
            } else {
                $link = "No hay archivo";
            }

            $datos .= "
                    <tr>
                        <td class='text-center'>$fechacreacion</td>
                        <td class='text-center'>$" . bcdiv($monto, '1', 2) . "</td>
                        <td class='text-center'>$" . bcdiv($restante, '1', 2) . "</td>
                        <td class='text-center'>$autorizacion</td>
                        <td class='text-center'>$fechaanticipo</td>
                        <td class='text-center'>$link</td>
                        <td class='text-center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarAnticipo($idanticipo);'>Editar anticipo <span class='fas fa-edit small text-muted'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarAnticipo($idanticipo, $idcotizacion);'>Eliminar anticipo <span class='text-muted fas fa-times'></span></a></li>
                        <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick=\"imprimirAnticipo($idanticipo)\";'>Imprimir anticipo <span class='text-muted fas fa-list-alt'></span></a></li>
                        </ul>
                        </div></td>
                    </tr>
                     ";
        }
        $datos .= "</tbody>";
        $check = $this->checkAnticiposRestantes($idcotizacion);
        if ($check) {
            $datos .= "<tfoot><tr><th colspan='6'></th><th class='text-end'><button class='btn button-file text-uppercase fs-6' data-bs-toggle='modal' data-bs-target='#anticipos' onclick='cargarDatosAnticipo($idcotizacion);' id='btn-add-anticipo'><small>Agregar anticipo <span class='fas fa-plus'></span></small></button></th></tr></tfoot>";
        }
        return $datos;
    }

    private function checkAnticiposRestantes($idcotizacion) {
        $check = false;
        $datos = $this->getRestantesAux($idcotizacion);
        foreach ($datos as $actual) {
            $restante = $actual['restante'];
            if ($restante > 0) {
                $check = true;
            }
        }
        return $check;
    }

    public function getAnticipoById($idtmp) {
        $consultado = false;
        $consulta = "select a.*,d.letra,d.foliocotizacion,d.totalcotizacion,d.foliocotizacion, f.firma FROM anticipo a inner join datos_cotizacion d on (a.anticipo_idcotizacion=d.iddatos_cotizacion) inner join datos_facturacion f on (f.id_datos=d.iddatosfacturacion) where a.idanticipo=:id";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getRestantePrevioAux($cid, $aid) {
        $consultado = false;
        $consulta = "SELECT restante FROM anticipo where anticipo_idcotizacion=:cid and idanticipo <:aid order by idanticipo desc limit 1";
        $val = array("cid" => $cid,
            "aid" => $aid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getTotalCotizacion($cid) {
        $total = 0;
        $datos = $this->getCotizacionById($cid);
        foreach ($datos as $actual) {
            $total = $actual['totalcotizacion'];
        }
        return $total;
    }

    private function getRestantePrevio($cid, $aid) {
        $check = false;
        $restante = 0;
        $datos = $this->getRestantePrevioAux($cid, $aid);
        foreach ($datos as $actual) {
            $check = true;
            $restante = $actual['restante'];
        }
        if (!$check) {
            $restante = $this->getTotalCotizacion($cid);
        }
        return $restante;
    }

    public function getDatosAnticipo($idtmp) {
        $anticipo = $this->getAnticipoById($idtmp);
        $datos = "";
        foreach ($anticipo as $actual) {
            $idanticipo = $actual['idanticipo'];
            $fechacreacion = $actual['fechacreacion'];
            $idcotizacion = $actual['anticipo_idcotizacion'];
            $monto = $actual['montoanticipo'];
            $restante = $actual['restante'];
            $autorizacion = $actual['autorizacion'];
            $fechaanticipo = $actual['fechaanticipo'];
            $img = $actual['imganticipo'];
            $texto = addslashes($actual['texto']);
            $emision = addslashes($actual['emision']);
            $previo = $this->getRestantePrevio($idcotizacion, $idanticipo);
            $src = "../img/anticipos/$img";
            $type = "";
            $base64 = "";

            if ($img != "" && file_exists($src)) {
                $type = pathinfo($src, PATHINFO_EXTENSION);

                if ($type != 'pdf') {
                    $data = file_get_contents($src);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                } else {
                    copy($src, "../temporal/anticipos/$img");
                }
            }

            $datos .= "$idanticipo</tr>$fechacreacion</tr>$idcotizacion</tr>$monto</tr>$restante</tr>$previo</tr>$autorizacion</tr>$fechaanticipo</tr>$img</tr>$type</tr>$texto</tr>$emision</tr>$base64";
            break;
        }
        return $datos;
    }

    public function actualizarAnticipo($a) {
        $img = $a->getImg();
        if ($img == "") {
            $img = $a->getActualizarimg();
        } else if ($a->getImg() != $a->getActualizarimg()) {
            if ($a->getImg() != "") {
                rename('../temporal/anticipos/' . $a->getImg(), '../img/anticipos/' . $a->getImg());
                unlink("../img/anticipos/" . $a->getActualizarimg());
            }
        }

        $insertado = false;
        $consulta = "UPDATE `anticipo` SET montoanticipo=:monto, restante=:restante, autorizacion=:autorizacion, fechaanticipo=:fechaanticipo, imganticipo=:img, texto=:texto, emision=:emision where idanticipo=:id;";
        $valores = array("id" => $a->getIdanticipo(),
            "monto" => bcdiv($a->getMonto(), '1', 2),
            "restante" => bcdiv($a->getRestante(), '1', 2),
            "autorizacion" => $a->getAutorizacion(),
            "fechaanticipo" => $a->getFecha(),
            "img" => $img,
            "texto" => $a->getMensaje(),
            "emision" => $a->getEmision());
        $insertado = $this->consultas->execute($consulta, $valores);
        $update = $this->actualizarRegistros($a->getIdanticipo(), $a->getIdcotizacion(), $a->getRestante());
        return $insertado;
    }

    private function actualizarRegistros($aid, $cid, $restante) {
        $update = false;
        $datos = $this->getRegistrosAux($aid, $cid);
        foreach ($datos as $actual) {
            $idanticipo = $actual['idanticipo'];
            $montoanticipo = $actual['montoanticipo'];
            $restante = $restante - $montoanticipo;
            $update = $this->modificarRegistros($idanticipo, $restante);
        }
        return $update;
    }

    private function getRegistrosAux($aid, $cid) {
        $consultado = FALSE;
        $consulta = "SELECT * FROM anticipo WHERE anticipo_idcotizacion=:cid and idanticipo > :aid";
        $val = array("cid" => $cid,
            "aid" => $aid);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function modificarRegistros($aid, $restante) {
        $actualizado = false;
        $consulta = "UPDATE `anticipo` SET restante=:restante WHERE idanticipo=:id;";
        $valores = array("id" => $aid,
            "restante" => bcdiv($restante, '1', 2));
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function eliminarAnticipo($id, $cid) {
        $eliminado = false;
        $consulta = "DELETE FROM `anticipo` WHERE idanticipo=:id;";
        $valores = array("id" => $id);
        $eliminado = $this->consultas->execute($consulta, $valores);
        $this->actualizarEliminado($id, $cid);
        return $eliminado;
    }

    private function actualizarEliminado($aid, $cid) {
        $restante = $this->getRestantePrevio($cid, $aid);
        $update = $this->actualizarRegistros($aid, $cid, $restante);
    } 
}