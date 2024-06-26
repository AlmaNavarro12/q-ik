<?php
require_once '../com.sine.dao/Consultas.php';

class ControladorProducto
{

    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

    public function listaProductosHistorial($NOM, $pag, $numreg)
    {
        include '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead>
            <tr class='align-middle'>
                <th class='text-center col-auto'>C&oacute;digo</th>
                <th class='text-center col-auto'>Producto/Servicio </th>
                <th class='text-center col-auto'>Unidad </th>
                <th class='text-center col-auto'>P.Compra </th>
                <th class='text-center col-auto'>P.Venta</th>
                <th class='text-center col-auto'>C.Fiscal </th>
                <th class='text-center col-auto'>Proveedor</th>
                <th class='text-center col-auto'>Inventario</th>
                <th class='text-center col-auto'>Cantidad</th>
                <th class='text-center col-auto'>Opci&oacute;n</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($NOM == "") {
            $condicion = "ORDER BY p.codproducto";
        } else {
            $condicion = "WHERE (p.nombre_producto LIKE '%$NOM%') OR (p.codproducto LIKE '%$NOM%') ORDER BY p.codproducto";
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
            $productos = $this->getSevicios($con);
            $finales = 0;
            foreach ($productos as $productoactual) {
                $id_producto = $productoactual['idproser'];
                $codigo = $productoactual['codproducto'];
                $nombre = $productoactual['nombre_producto'];
                $unidad = $productoactual['desc_unidad'];
                $descripcion_producto = $productoactual['descripcion_producto'];
                $pcompra = $productoactual['precio_compra'];
                $pventa = $productoactual['precio_venta'];
                $tipo = $productoactual['tipo'];
                $clavefiscal = $productoactual['clave_fiscal'];
                $descripcion = $productoactual['desc_fiscal'];
                $chinventario = $productoactual['chinventario'];
                $cantidad = $productoactual['cantinv'];
                $idproveedor = $productoactual['idproveedor'];

                $estadoinv = "Inactivo";
                $color = "#ED495C";
                $title = "Activar Inventario";
                $function = "onclick='setCant($id_producto)'";
                $modal = "data-bs-toggle='modal' data-bs-target='#cambiarestado'";

                $title2 = "Activar Inventario";
                $function2 = "onclick='setCant($id_producto)'";
                $modal2 = "data-bs-toggle='modal' data-bs-target='#cambiarestado'";

                if ($chinventario == '1') {
                    $estadoinv = "Activo";
                    $color = "#34A853";
                    $title = "Desactivar Inventario";
                    $function = "onclick='desactivarInventario($id_producto)'";
                    $modal = "";

                    $title2 = "Cambiar Cantidad";
                    $function2 = "onclick='setCantInventario($id_producto,$cantidad)'";
                    $modal2 = "data-bs-toggle='modal' data-bs-target='#cambiarcantidad'";
                }

                if ($tipo == '2') {
                    $estadoinv = "<div class='small-tooltip icon tip'>
                                    <span class='text-danger'>Inactivo</span>
                                    <span class='tiptext text-center text-danger'> Un servicio no puede tener inventario</span>
                                </div>";
                    $color = "#ED495C";
                    $title = "";
                    $function = "";
                    $modal = "";

                    $title2 = "No Disponible";
                    $function2 = "";
                    $modal2 = "";
                }

                $proveedor = "No Disponible";
                if ($idproveedor != '0') {
                    $proveedor = $this->getProveedor($idproveedor);
                }

                $numero_compra = (float)$pcompra;
                $numero_venta = (float)$pventa;
                $datos .= "
                    <tr>
                        <td class='text-center'>$codigo</td>
                        <td class='text-center'>$nombre - $descripcion_producto</td>
                        <td class='text-center text-wreak'>$unidad</td>
                        <td class='text-center'>$" . number_format($numero_compra, 2, '.', ',') . "</td>
                        <td class='text-center'>$" . number_format($numero_venta, 2, '.', ',') . "</td>
                        <td class='text-center text-wreak'>$clavefiscal - $descripcion</td>
                        <td class='text-center'>$proveedor</td>
                        <td class='text-center'><a class='state-link fw-bold' style='color: $color;' $modal $function title='$title'><span>$estadoinv</span></a></td>
                        <td class='text-center'><a class='state-link fw-bold' $modal2 $function2 title='$title2'><span>$cantidad</span></a></td>
                        <td class='text-center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right z-1'>";

                if ($div[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarProducto($id_producto);'>Editar producto <span class='fas fa-edit text-muted small'></span></a></li>";
                }

                if ($div[2] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarProducto($id_producto);'>Eliminar producto <span class='fas fa-times text-muted'></span></a></li>";
                }

                if ($div[3] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='copiarProducto($id_producto);'>Copiar producto <span class='fas fa-copy text-muted small'></span></a></li>";
                }

                $datos .= "</ul>
                        </div></td>
                    </tr>
                     ";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarProducto";
            $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='7'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

            if ($finales == 0) {
                $datos .= "<tr><td colspan='12'>No se encontraron registros</td></tr>";
            }
        }
        return $datos;
    }

    private function getPermisoById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso p where permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario)
    {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $lista = $actual['listaproducto'];
            $crear = $actual['crearproducto'];
            $editar = $actual['editarproducto'];
            $eliminar = $actual['eliminarproducto'];

            $datos .= "$lista</tr>$editar</tr>$eliminar</tr>$crear";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion)
    {
        $consultado = false;
        $consulta = "SELECT COUNT(*) numrows FROM productos_servicios p $condicion;";
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

    private function getSevicios($condicion)
    {
        $consultado = false;
        $consulta = "SELECT p.* FROM productos_servicios p $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getProveedorAux($pid)
    {
        $datos = false;
        $consulta = "SELECT * FROM proveedor WHERE idproveedor=:pid";
        $val = array("pid" => $pid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getProveedor($pid)
    {
        $nombre = false;
        $datos = $this->getProveedorAux($pid);
        foreach ($datos as $actual) {
            $nombre = $actual['empresa'];
        }
        return $nombre;
    }

    public function listaProductosTaxes($taxes)
    {
        $bandera = 0;
        $impuestos = "";
        $inputs = "";
        $array = explode("<tr>", $taxes);
        $consultas = "SELECT idimpuesto,
                      CASE 
                          WHEN impuesto = 1 THEN 'ISR'
                          WHEN impuesto = 2 THEN 'IVA'
                          WHEN impuesto = 3 THEN 'IEPS'
                      END AS impuesto,
                      porcentaje,
                      tipoimpuesto
                      FROM impuesto";
        $stmt = $this->consultas->getResults($consultas, null);
        foreach ($stmt as $rs) {
            $bandera++;
            $id = $rs['idimpuesto'];
            $imp = $rs['impuesto'];
            $per = $rs['porcentaje'];
            $tipo = $rs['tipoimpuesto'];
            $value = $per . "-" . $imp . "-" . $id;
            $var_taxes = $per . "-" . $tipo;
            $strTipo = "Traslado";
            $var_check = "";
            $var_hidden = "style='display: none;'";

            if ($tipo == 2) {
                $strTipo = "Retención";
            }

            for ($i = 0; $i < sizeof($array); $i++) {
                if ($array[$i] == $var_taxes) {
                    $var_check = "checked='checked'";
                    $var_hidden = "";
                }
            }

            $impuestos .= "<div class='col-auto mt-2'><label class='radio-inline d-flex align-items-center' style='padding: 0px;'>
                                <input type='checkbox' class='input-check' id='imp$id' name='taxes' value='$var_taxes' onclick='habilitaImp(`$value`)' $var_check>
                                <label for='imp$id' class='label-form ms-2'>$imp</label>
                            </label></div>";

            $inputs .= "<div id='CalcImp$id' class='col-auto' $var_hidden>
                            <label class='label-form text-right' for='piva'><b>$imp " . ($per * 100) . "%</b><small>$strTipo</small></label>
                            <label class='mark-required text-danger fw-bold'></label>
                            <div class='form-group'>
                                <input class='form-control text-center input-form col-12' id='pimp$id' name='p$id' value='0' type='text' disabled/>
                            </div>
                        </div>";
        }

        $json = array();
        $json["bandera"] = $bandera;
        $json["impuestos"] = $impuestos;
        $json["inputs"] = $inputs;
        return $json;
    }

    public function validarCodigo($p)
    {
        $datos = "";
        $check = $this->validarCodigoAux($p->getCodproducto());
        if ($check) {
            $datos = "0Ya existe un producto con este código.";
        } else {
            $datos = $this->gestionarProducto($p);
        }
        return $datos;
    }

    private function validarCodigoAux($cod)
    {
        $existe = false;
        $validar = $this->getCodigobyCod($cod);
        foreach ($validar as $actual) {
            $existe = true;
        }
        return $existe;
    }

    private function getCodigobyCod($cod)
    {
        $consultado = false;
        $consulta = "SELECT codproducto FROM productos_servicios WHERE codproducto=:cod;";
        $val = array("cod" => $cod);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function valCodigoActualizarAux($codigo, $idproducto) {
        $consultado = false;
        $consulta = "SELECT * FROM productos_servicios WHERE codproducto = :codigo AND idproser != :idproducto;";
        $val = array("codigo" => $codigo, "idproducto" => $idproducto);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }
    

    public function valCodigoActualizar($p) {
        $cod = "";
        $datos = "";
        $validar = $this->valCodigoActualizarAux($p->getCodproducto(), $p->getIdProducto());
        foreach ($validar as $valactual) {
            $cod = $valactual['codproducto'];
        }
        if ($cod != "") {
            $datos = "0Ya existe un producto con este codigo.";
        } else {
            $datos = $this->gestionarProducto($p);
        }
        return $datos;
    }

    public function insertarInventario($p) {
        $insertado = false;
        $consulta = "UPDATE `productos_servicios` SET cantinv=:cantidad WHERE idproser=:id_producto;";
        $valores = array("id_producto" => $p->getIdProducto(), "cantidad" => $p->getCantidad());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function gestionarProducto($p)
    {
        $img = $p->getImagen();

        if ($p->getIdProducto() == 0) {
            if ($img != '') {
                rename('../temporal/productos/' . $p->getImagen(), '../img/productos/' . $p->getImagen());
            }
        } else {
            if ($img == '') {
                $img = $p->getImgactualizar();
            } else if ($img != $p->getNameImg()) {
                $nuevaRuta = '../img/productos/' . $img;
                $viejaRuta = '../img/productos/' . $p->getNameImg();
                rename('../temporal/productos/' . $img, $nuevaRuta);

                if (file_exists($viejaRuta)) {
                    unlink($viejaRuta);
                }
            }
        }
        $insertado = false;
        $consulta = $p->getIdProducto() != 0 ?
            "UPDATE `productos_servicios` SET codproducto=:codproducto, nombre_producto=:producto, clv_unidad=:clvunidad, desc_unidad=:unidad, desc_fiscal=:descfiscal, descripcion_producto=:descripcion, precio_compra=:pcompra, porcentaje=:porcentaje, ganancia=:ganancia, precio_venta=:pventa,tipo=:tipo, clave_fiscal=:clvfiscal,  idproveedor=:idproveedor, imagenprod=:imagen, chinventario=:chinventario, cantinv=:cantidad, impuestos_aplicables=:taxes WHERE idproser=:id_producto;" :
            "INSERT INTO `productos_servicios` VALUES (:id, :codproducto, :producto, :clvunidad, :unidad, :descfiscal, :descripcion, :pcompra, :porcentaje, :ganancia, :pventa, :tipo, :clvfiscal,  :idproveedor,:imagen,:chinventario,:cantidad, :impuestos_aplicables);";

        $valores = $p->getIdProducto() != 0 ?
            [
                "codproducto" => $p->getCodproducto(),
                "producto" => $p->getProducto(),
                "clvunidad" => $p->getClvunidad(),
                "unidad" => $p->getDescripcionunidad(),
                "descfiscal" => $p->getDescripcionfiscal(),
                "descripcion" => $p->getDescripcion(),
                "pcompra" => $p->getPrecio_compra(),
                "porcentaje" => $p->getPorcentaje(),
                "ganancia" => $p->getGanancia(),
                "pventa" => $p->getPrecio_venta(),
                "tipo" => $p->getTipo(),
                "clvfiscal" => $p->getClavefiscal(),
                "idproveedor" => $p->getIdproveedor(),
                "imagen" => $img,
                "chinventario" => $p->getChinventario(),
                "cantidad" => $p->getCantidad(),
                "taxes" => $p->getTaxes(),
                "id_producto" => $p->getIdProducto()
            ] :
            [
                "id" => null,
                "codproducto" => $p->getCodproducto(),
                "producto" => $p->getProducto(),
                "clvunidad" => $p->getClvunidad(),
                "unidad" => $p->getDescripcionunidad(),
                "descfiscal" => $p->getDescripcionfiscal(),
                "descripcion" => $p->getDescripcion(),
                "pcompra" => $p->getPrecio_compra(),
                "porcentaje" => $p->getPorcentaje(),
                "ganancia" => $p->getGanancia(),
                "pventa" => $p->getPrecio_venta(),
                "tipo" => $p->getTipo(),
                "clvfiscal" => $p->getClavefiscal(),
                "idproveedor" => $p->getIdproveedor(),
                "imagen" => $p->getImagen(),
                "chinventario" => $p->getChinventario(),
                "cantidad" => $p->getCantidad(),
                "impuestos_aplicables" => $p->getTaxes()
            ];

        $insertado = $this->consultas->execute($consulta, $valores);

        if ($p->getInsert() != "" && $p->getIdProducto() == 0) {
            session_start();
            $sid = session_id();
            $idproducto = $this->getIDProducto($p->getCodproducto());
            if ($p->getInsert() == 'f') {
                $agregar = $this->agregarProductoFactura($idproducto, $sid, $p);
            }
            if ($p->getInsert() == 'c' || $p->getInsert() == 'ct') {
                $agregar = $this->agregarProductoCotizacion($idproducto, $sid, $p);
            }
        }

        if ($p->getInsert() == 'f' && $p->getIdProducto() != 0) {
            $concepto = $this->actualizarConcepto2($p);
        } else if ($p->getInsert() == 'c' || $p->getInsert() == 'ct') {
            $concepto = $this->actualizarConceptoCot($p);
        }
        return $insertado;
    }

    private function checkProductoAux($idtmp) {
        $consultado = false;
        $consulta = "SELECT t.*,p.cantinv,p.chinventario FROM tmp t inner join productos_servicios p on (t.id_productotmp=p.idproser) where t.idtmp=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    public function reBuildArray($importe, $array) {
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
    
    private function actualizarConcepto2($t) {
        $check = $this->checkProductoAux($t->getIdtmp());
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
            $traslados = $actual['trasladotmp'];
            $retenciones = $actual['retenciontmp'];
            $idproducto = $actual['id_productotmp'];
        }
        $totuni = $canttmp * $t->getPrecio_venta();
        $impdescuento = $t->getPrecio_venta() * ($descuento_tmp / 100);
        $importe = bcdiv($totuni, '1', 2) - bcdiv($impdescuento, '1', 2);

        $rebuildT = $this->reBuildArray($importe, $traslados);
        $divT = explode("<cut>", $rebuildT);
        $traslados = $divT[0];
        $Timp = $divT[1];

        $rebuildR = $this->reBuildArray($importe, $retenciones);
        $divR = explode("<cut>", $rebuildR);
        $retenciones = $divR[0];
        $Tret = $divR[1];

        $total = (( bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2));

        $actualizado = false;
        $consulta = "UPDATE `tmp` SET tmpnombre=:nombre, precio_tmp=:precio, totunitario_tmp=:totunitario, impdescuento_tmp=:impdesc, imptotal_tmp=:total, trasladotmp=:traslado, retenciontmp=:retencion, clvfiscaltmp=:cfiscal, clvunidadtmp=:cunidad  WHERE idtmp=:id;";
        $valores = array("id" => $t->getIdtmp(),
            "nombre" => $t->getProducto(),
            "precio" => $t->getPrecio_venta(),
            "totunitario" => $totuni,
            "impdesc" => $impdescuento,
            "total" => $total,
            "traslado" => $traslados,
            "retencion" => $retenciones,
            "cfiscal" => $t->getClavefiscal() . "-" . $t->getDescripcionfiscal(),
            "cunidad" => $t->getClvunidad() . "-" . $t->getDescripcionunidad());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    private function actualizarConceptoCot($t) {
        $check = $this->checkProductoCotAux($t->getIdtmp());
        foreach ($check as $actual) {
            $canttmp = $actual['cantidad_tmp'];
            $descuento_tmp = $actual['descuento_tmp'];
            $traslados = $actual['trasladotmp'];
            $retenciones = $actual['retenciontmp'];
            $idproducto = $actual['id_productotmp'];
        }
        $totuni = $canttmp * $t->getPrecio_venta();
        $impdescuento = $t->getPrecio_venta() * ($descuento_tmp / 100);
        $importe = bcdiv($totuni, '1', 2) - bcdiv($impdescuento, '1', 2);

        $rebuildT = $this->reBuildArray($importe, $traslados);
        $divT = explode("<cut>", $rebuildT);
        $traslados = $divT[0];
        $Timp = $divT[1];

        $rebuildR = $this->reBuildArray($importe, $retenciones);
        $divR = explode("<cut>", $rebuildR);
        $retenciones = $divR[0];
        $Tret = $divR[1];

        $total = (bcdiv($importe, '1', 2) + bcdiv($Timp, '1', 2)) - bcdiv($Tret, '1', 2);

        $actualizado = false;
        $consulta = "UPDATE `tmpcotizacion` SET tmpnombre=:nombre, precio_tmp=:precio, totunitario_tmp=:totunitario, impdescuento_tmp=:impdesc, imptotal_tmp=:total, trasladotmp=:traslado, retenciontmp=:retencion, clvfiscaltmp=:cfiscal, clvunidadtmp=:cunidad WHERE idtmpcotizacion=:id;";
        $valores = array("id" => $t->getIdtmp(),
            "nombre" => $t->getProducto(),
            "precio" => $t->getPrecio_venta(),
            "totunitario" => $totuni,
            "impdesc" => $impdescuento,
            "total" => $total,
            "traslado" => $traslados,
            "retencion" => $retenciones,
            "cfiscal" => $t->getClavefiscal() . "-" . $t->getDescripcionfiscal(),
            "cunidad" => $t->getClvunidad() . "-" . $t->getDescripcionunidad());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    private function checkProductoCotAux($idtmp) {
        $consultado = false;
        $consulta = "SELECT t.*,p.cantinv,p.chinventario FROM tmpcotizacion t inner join productos_servicios p on (t.id_productotmp=p.idproser) where t.idtmpcotizacion=:id;";
        $val = array("id" => $idtmp);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function getIDProductoAux($datos)
    {
        $consultado = false;
        $consulta = "SELECT * FROM productos_servicios WHERE codproducto=:datos;";
        $valores = array("datos" => $datos);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getIDProducto($datos)
    {
        $idproducto = "";
        $producto = $this->getIDProductoAux($datos);
        foreach ($producto as $actual) {
            $idproducto = $actual['idproser'];
        }
        return $idproducto;
    }

    private function agregarProductoFactura($idproducto, $sessionid, $t)
    {
        $insertado = false;
        $traslados = $this->buildArray('1', $t->getPrecio_venta());
        $retenciones = $this->buildArray('2', $t->getPrecio_venta());
        $consulta = "INSERT INTO `tmp` VALUES (:id, :idproducto, :tmpnombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :traslados, :retenciones, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
        $valores = array(
            "id" => null,
            "idproducto" => $idproducto,
            "tmpnombre" => $t->getProducto(),
            "cantidad" => '1',
            "precio" => $t->getPrecio_venta(),
            "importe" => $t->getPrecio_venta(),
            "descuento" => '0',
            "impdescuento" => '0',
            "imptotal" => $t->getPrecio_venta(),
            "traslados" => $traslados,
            "retenciones" => $retenciones,
            "observaciones" => '',
            "chinv" => $t->getChinventario(),
            "clvfiscal" => $t->getClavefiscal() . "-" . $t->getDescripcionfiscal(),
            "clvunidad" => $t->getClvunidad() . "-" . $t->getDescripcionunidad(),
            "session" => $sessionid
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        if ($t->getChinventario() == '1') {
            $this->removerInventario($idproducto, '1');
        }
        return $insertado;
    }

    private function removerInventario($idproducto, $cantidad)
    {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv-:cantidad where idproser=:idproducto;";
        $valores = array(
            "idproducto" => $idproducto,
            "cantidad" => $cantidad
        );
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function buildArray($idimpuesto, $precio)
    {
        $imptraslados = $this->getImpuestos($idimpuesto);
        $row = array();
        foreach ($imptraslados as $tactual) {
            $impuesto = $tactual['impuesto'];
            $porcentaje = $tactual['porcentaje'];
            $chuso = $tactual['chuso'];
            if ($chuso == '1') {
                $imp = $precio * $porcentaje;
                if ($imp > 0) {
                    $row[] = bcdiv($imp, '1', 2) . '-' . $porcentaje . '-' . $impuesto;
                }
            }
        }

        $trasarray = implode("<impuesto>", $row);
        return $trasarray;
    }

    private function getImpuestos($tipo)
    {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto where tipoimpuesto=:tipo";
        $valores = array("tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function agregarProductoCotizacion($idproducto, $sessionid, $t)
    {
        $insertado = false;
        $traslados = $this->buildArray('1', $t->getPrecio_venta());
        $retenciones = $this->buildArray('2', $t->getPrecio_venta());
        $consulta = "INSERT INTO `tmpcotizacion` VALUES (:id, :idproducto, :tmpnombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :traslados, :retenciones, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
        $valores = array(
            "id" => null,
            "idproducto" => $idproducto,
            "tmpnombre" => $t->getProducto(),
            "cantidad" => '1',
            "precio" => $t->getPrecio_venta(),
            "importe" => $t->getPrecio_venta(),
            "descuento" => '0',
            "impdescuento" => '0',
            "imptotal" => $t->getPrecio_venta(),
            "traslados" => $traslados,
            "retenciones" => $retenciones,
            "observaciones" => '',
            "chinv" => $t->getChinventario(),
            "clvfiscal" => $t->getClavefiscal() . "-" . $t->getDescripcionfiscal(),
            "clvunidad" => $t->getClvunidad() . "-" . $t->getDescripcionunidad(),
            "session" => $sessionid
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminarImgTmp($imgtmp)
    {
        $viejaruta = '../temporal/productos/' . $imgtmp;
        if ($imgtmp != '') {
            if (file_exists($viejaruta)) {
                unlink($viejaruta);
            }
        }
        return true;
    }
    
    public function estadoInventario($e)
    {
        $insertado = false;
        $consulta = "UPDATE `productos_servicios` SET cantinv=:cantidad, chinventario=:chinventario WHERE idproser=:idproducto;";
        $valores = array(
            "idproducto" => $e->getIdProducto(),
            "cantidad" => $e->getCantidad(),
            "chinventario" => $e->getChinventario()
        );
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    private function getProductoById($idproducto)
    {
        $consultado = false;
        $consulta = "SELECT p.* FROM productos_servicios p WHERE p.idproser=:pid;";
        $valores = array("pid" => $idproducto);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function datosProductos($idproducto)
    {
        $datos = "";
        $productos = $this->getProductoById($idproducto);
        foreach ($productos as $productoactual) {
            $id_producto = $productoactual['idproser'];
            $codigo = $productoactual['codproducto'];
            $nombre = addslashes($productoactual['nombre_producto']);
            $clvunidad = $productoactual['clv_unidad'];
            $descripcion_unidad = $productoactual['desc_unidad'];
            $descripcion = $productoactual['desc_fiscal'];
            $descripcion_producto = $productoactual['descripcion_producto'];
            $pcompra = $productoactual['precio_compra'];
            $porcentaje = $productoactual['porcentaje'];
            $ganancia = $productoactual['ganancia'];
            $pventa = $productoactual['precio_venta'];
            $tipo = $productoactual['tipo'];
            $clavefiscal = $productoactual['clave_fiscal'];
            $idproveedor = $productoactual['idproveedor'];
            $imagen = $productoactual['imagenprod'];
            $chinventario = $productoactual['chinventario'];
            $cantidad = $productoactual['cantinv'];
            $taxes = $productoactual['impuestos_aplicables'];
            $img = "";
            if ($imagen != "") {
                $imgfile = "../img/productos/" . $imagen;
                $type = pathinfo($imgfile, PATHINFO_EXTENSION);
                $data = file_get_contents($imgfile);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $img = "<img src=\"$base64\" width=\"200px\">";
            }
            $datos .= "$id_producto</tr>$codigo</tr>$nombre</tr>$clvunidad</tr>$descripcion_unidad</tr>$descripcion_producto</tr>$pcompra</tr>$porcentaje</tr>$ganancia</tr>$pventa</tr>$tipo</tr>$clavefiscal</tr>$descripcion</tr>$idproveedor</tr>$imagen</tr>$chinventario</tr>$cantidad</tr>$img</tr>$taxes";
        }
        return $datos;
    }

    public function quitarProducto($idproducto) {
        $eliminado = false;
        $eliminado = $this->eliminarProducto($idproducto);
        return $eliminado;
    }

    public function eliminarProducto($idproducto) {
        $eliminado = false;
        $consulta = "DELETE FROM `productos_servicios` WHERE idproser=:id_producto;";
        $valores = array("id_producto" => $idproducto);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }
}
