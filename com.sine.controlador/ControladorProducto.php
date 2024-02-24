<?php
require_once '../com.sine.dao/Consultas.php';

class ControladorProducto{

    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

    public function listaProductosHistorial($NOM, $pag, $numreg) {
        include '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead>
            <tr>
                <th class='text-center col-auto'>C&oacute;digo</th>
                <th class='text-center col-auto'>Producto/Servicio </th>
                <th class='text-center col-auto'>Unidad </th>
                <th class='text-center col-auto'>P.Compra </th>
                <th class='text-center col-auto'>P.Venta</th>
                <th class='text-center col-auto'>Clave Fiscal </th>
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
                $unidad = $productoactual['clv_unidad'];
                $descripcion_producto = $productoactual['descripcion_producto'];
                $pcompra = $productoactual['precio_compra'];
                $pventa = $productoactual['precio_venta'];
                $tipo = $productoactual['tipo'];
                $clavefiscal = $productoactual['clave_fiscal'];
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
                    $estadoinv = "Inactivo";
                    $color = "#ED495C";
                    $title = "Un servicio no puede tener inventario";
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
                        <td class='text-center'>$unidad</td>
                        <td class='text-center'>$ " . number_format($numero_compra, 2, '.', ',') . "</td>
                        <td class='text-center'>$ " . number_format($numero_venta, 2, '.', ',') . "</td>
                        <td class='text-center'>$clavefiscal</td>
                        <td class='text-center'>$proveedor</td>
                        <td class='text-center'><a class='state-link fw-bold' style='color: $color;' $modal $function title='$title'><span>$estadoinv</span></a></td>
                        <td class='text-center'><a class='state-link fw-bold' $modal2 $function2 title='$title2'><span>$cantidad</span></a></td>
                        <td align='center'><div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v text-muted'></span>
                        <span class='caret'></span></button>
                        <ul class='dropdown-menu dropdown-menu-right'>";
                
                if ($div[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarProducto($id_producto);'>Editar Producto <span class='glyphicon glyphicon-edit'></span></a></li>";
                }
                
                if ($div[2] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarProducto($id_producto);'>Eliminar Producto <span class='glyphicon glyphicon-remove'></span></a></li>";
                }
                
                if ($div[3] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='copiarProducto($id_producto);'>Copiar Producto <span class='glyphicon glyphicon-copy'></span></a></li>";
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
            $datos .= "</tbody><tfoot><tr><th colspan='7' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='3'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

            if ($finales == 0) {
                $datos .= "<tr><td colspan='12'>No se encontraron registros</td></tr>";
            }
        }
        return $datos;
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
            $lista = $actual['listaproducto'];
            $crear = $actual['crearproducto'];
            $editar = $actual['editarproducto'];
            $eliminar = $actual['eliminarproducto'];

            $datos .= "$lista</tr>$editar</tr>$eliminar</tr>$crear";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT COUNT(*) numrows FROM productos_servicios p $condicion;";
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

    private function getSevicios($condicion) {
        $consultado = false;
        $consulta = "SELECT p.* FROM productos_servicios p $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getProveedorAux($pid) {
        $datos = false;
        $consulta = "SELECT * FROM proveedor WHERE idproveedor=:pid";
        $val = array("pid" => $pid);
        $datos = $this->consultas->getResults($consulta, $val);
        return $datos;
    }

    private function getProveedor($pid) {
        $nombre = false;
        $datos = $this->getProveedorAux($pid);
        foreach ($datos as $actual) {
            $nombre = $actual['empresa'];
        }
        return $nombre;
    }

    public function listaProductosTaxes($taxes) {
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
        foreach($stmt AS $rs){
            $bandera++;
            $id = $rs['idimpuesto'];
            $imp = $rs['impuesto'];
            $per = $rs['porcentaje'];
            $tipo = $rs['tipoimpuesto'];
            $value = $per."-".$imp."-".$id;
            $var_taxes = $per."-".$tipo;
            $strTipo = "Traslado";
            $var_check = "";
            $var_hidden = "style='display: none;'";

            if($tipo == 2){
                $strTipo = "Retención";
            }

            for($i = 0; $i < sizeof($array); $i++){
                if($array[$i] == $var_taxes){
                    $var_check = "checked='checked'";
                    $var_hidden = "";
                }
            }

            $impuestos .= "<div class='col-auto mt-2'><label class='radio-inline d-flex align-items-center' style='padding: 0px;'>
                                <input type='checkbox' class='input-check' id='imp$imp' name='taxes' value='$var_taxes' onclick='habilitaImp(`$value`)' $var_check>
                                <label for='imp$imp' class='label-form ms-2'>$imp</label>
                            </label></div>";

            $inputs .= "<div id='CalcImp$imp' class='col-auto' $var_hidden>
                            <label class='label-form text-right' for='piva'><b>$imp ".($per*100)."%</b><small>$strTipo</small></label>
                            <label class='mark-required text-danger fw-bold'></label>
                            <div class='form-group'>
                                <input class='form-control text-center input-form' id='pimp$imp' name='p$imp' value='0' type='text' disabled/>
                            </div>
                        </div>";
        }

        $json = array();
        $json["bandera"] = $bandera;
        $json["impuestos"] = $impuestos;
        $json["inputs"] = $inputs;
        return $json;
    }

    public function validarCodigo($p) {
        $datos = "";
        $check = $this->validarCodigoAux($p->getCodproducto());
        if ($check) {
            $datos = "0Ya existe un producto con este código.";
        } else {
            $datos = $this->gestionarProducto($p);
        }
        return $datos;
    }

    private function validarCodigoAux($cod) {
        $existe = false;
        $validar = $this->getCodigobyCod($cod);
        foreach ($validar as $actual) {
            $existe = true;
        }
        return $existe;
    }

    private function getCodigobyCod($cod) {
        $consultado = false;
        $consulta = "SELECT codproducto FROM productos_servicios WHERE codproducto=:cod;";
        $val = array("cod" => $cod);
        $consultado = $this->consultas->getResults($consulta, $val);
        return $consultado;
    }

    private function gestionarProducto($p) {
        $existe = $this->validarCodigoAux($p->getCodproducto());

        if(!$existe) {
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
        }
        $insertado = false;
        $consulta = $p->getIdProducto() != 0 ?
        "UPDATE `productos_servicios` SET codproducto=:codproducto, nombre_producto=:producto, clv_unidad=:clvunidad,  descripcion_producto=:descripcion, precio_compra=:pcompra, porcentaje=:porcentaje, ganancia=:ganancia, precio_venta=:pventa, tipo=:tipo, clave_fiscal=:clvfiscal, idproveedor=:idproveedor, imagenprod=:imagen, chinventario=:chinventario, cantinv=:cantidad, impuestos_aplicables=:taxes WHERE idproser=:id_producto;" :
        "INSERT INTO `productos_servicios` VALUES (:id, :codproducto, :producto, :clvunidad,  :descripcion, :pcompra, :porcentaje, :ganancia, :pventa, :tipo, :clvfiscal, :idproveedor,:imagen,:chinventario,:cantidad, :taxes);";

        $valores = $p->getIdProducto() != 0 ?
            [
                "codproducto" => $p->getCodproducto(),
                "producto" => $p->getProducto(),
                "clvunidad" => $p->getClvunidad(),
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
            "taxes" => $p->getTaxes()
                ];

            $insertado = $this->consultas->execute($consulta, $valores);
        
        if ($p->getInsert() != "") {
            session_start();
            $sid = session_id();
            $idproducto = $this->getIDProducto($p->getCodproducto());
            if ($p->getInsert() == 'f') {
                $agregar = $this->agregarProductoFactura($idproducto, $sid, $p);
            }if ($p->getInsert() == 'c' || $p->getInsert() == 'ct') {
                $agregar = $this->agregarProductoCotizacion($idproducto, $sid, $p);
            }
        }
        return $insertado;
    }

    private function getIDProductoAux($datos) {
        $consultado = false;
        $consulta = "SELECT * FROM productos_servicios WHERE codproducto=:datos;";
        $valores = array("datos" => $datos);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getIDProducto($datos) {
        $idproducto = "";
        $producto = $this->getIDProductoAux($datos);
        foreach ($producto as $actual) {
            $idproducto = $actual['idproser'];
        }
        return $idproducto;
    }

    private function agregarProductoFactura($idproducto, $sessionid, $t) {
        $insertado = false;
        $traslados = $this->buildArray('1', $t->getPrecio_venta());
        $retenciones = $this->buildArray('2', $t->getPrecio_venta());
        $consulta = "INSERT INTO `tmp` VALUES (:id, :idproducto, :tmpnombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :traslados, :retenciones, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
        $valores = array("id" => null,
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
            "session" => $sessionid);
        $insertado = $this->consultas->execute($consulta, $valores);
        if ($t->getChinventario() == '1') {
            $this->removerInventario($idproducto, '1');
        }
        return $insertado;
    }

    private function removerInventario($idproducto, $cantidad) {
        $consultado = false;
        $consulta = "UPDATE `productos_servicios` set cantinv=cantinv-:cantidad where idproser=:idproducto;";
        $valores = array("idproducto" => $idproducto,
            "cantidad" => $cantidad);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function buildArray($idimpuesto, $precio) {
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

    private function getImpuestos($tipo) {
        $consultado = false;
        $consulta = "SELECT * FROM impuesto where tipoimpuesto=:tipo";
        $valores = array("tipo" => $tipo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function agregarProductoCotizacion($idproducto, $sessionid, $t) {
        $insertado = false;
        $traslados = $this->buildArray('1', $t->getPrecio_venta());
        $retenciones = $this->buildArray('2', $t->getPrecio_venta());
        $consulta = "INSERT INTO `tmpcotizacion` VALUES (:id, :idproducto, :tmpnombre, :cantidad, :precio, :importe, :descuento, :impdescuento, :imptotal, :traslados, :retenciones, :observaciones, :chinv, :clvfiscal, :clvunidad, :session);";
        $valores = array("id" => null,
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
            "session" => $sessionid);
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }

    public function eliminarImgTmp($imgtmp){
        $viejaruta = '../temporal/productos/' . $imgtmp;
        if ($imgtmp != '') {
            if (file_exists($viejaruta)) {
                unlink($viejaruta);
            }
        }
        return true;
    }

    public function estadoInventario($e) {
        $insertado = false;
        $consulta = "UPDATE `productos_servicios` SET cantinv=:cantidad, chinventario=:chinventario WHERE idproser=:idproducto;";
        $valores = array("idproducto" => $e->getIdProducto(),
            "cantidad" => $e->getCantidad(),
            "chinventario" => $e->getChinventario());
        $insertado = $this->consultas->execute($consulta, $valores);
        return $insertado;
    }
}