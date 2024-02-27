<?php
require_once '../com.sine.dao/Consultas.php';

class ControladorProveedor {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    public function listaServiciosHistorial($REF, $pag, $numreg) {
        include '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead>
            <tr class='align-middle'>
                <th class='col-auto text-center'>Empresa</th>
                <th class='col-auto text-center'>Representante</th>
                <th class='col-auto text-center'>Contacto</th>
                <th class='col-auto text-center'>Banco</th>
                <th class='col-auto text-center'>Sucursal</th>
                <th class='col-auto text-center'>No. de Cuenta</th>
                <th class='col-auto text-center'>Clabe Interbancaria</th>
                <th class='col-auto text-center'>Opci&oacute;n</th>
            </tr>
        </thead>
        <tbody>";
        $condicion = "";
        if ($REF == "") {
            $condicion = " ORDER BY p.empresa";
        } else {
            $condicion = "WHERE ((p.empresa LIKE '%$REF%') OR (p.representante LIKE '%$REF%')) ORDER BY p.empresa";
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
            $proveedores = $this->getFiltrado($con);
            $finales = 0;
            foreach ($proveedores as $proveedoractual) {
                $id_proveedor = $proveedoractual['idproveedor'];
                $empresa = $proveedoractual['empresa'];
                $representante = $proveedoractual['representante'];
                $telefono = $proveedoractual['telefono'];
                $email = $proveedoractual['email'];
                $num_cuenta = $proveedoractual['num_cuenta'];
                $clabe = $proveedoractual['clabe_interban'];
                $banco = "No Disponible";
                if ($proveedoractual['idbanco'] != '0') {
                    $banco = $proveedoractual['nombre_banco'];
                }
                $sucursal = $proveedoractual['nsucursal'];

                $datos .= "<tr>
                         <td class='text-center'>$empresa</td>
                         <td class='text-center text-break'>$representante</td>
                         <td class='text-center lh-base'> $email <br> $telefono</td>
                         <td class='text-center'>$banco</td>
                         <td class='text-center'>$sucursal</td>
                         <td class='text-center'>$num_cuenta</td>
                         <td class='text-center'>$clabe</td>
                         <td class='text-center'><div class='dropdown'>
                            <button class='button-list dropdown-toggle' title='Opciones'  type='button' data-bs-toggle='dropdown'><span class='fas fa-ellipsis-v'></span>
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu dropdown-menu-right'>";

                if ($div[0] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarProveedor($id_proveedor)'>Editar proveedor <span class='fas fa-edit'></span></a></li>";
                }

                if ($div[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarProveedor($id_proveedor)'>Eliminar proveedor <span class='fas fa-times text-muted'></span></a></li>";
                }
                $datos .= "</ul>
                        </div></td>
                    </tr>";
                $finales++;
            }
            $inicios = $offset + 1;
            $finales += $inicios - 1;
            $function = "buscarProveedor";
            if ($finales == 0) {
                $datos .= "<tr><td colspan='10'>No se encontraron registros</td></tr>";
            }
            $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
            $datos .= "<th colspan='6'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";
        }
        return $datos;
    }

    private function getNumrowsAux($condicion) {
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM proveedor AS p $condicion;";
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
            $editar = $actual['editarproveedor'];
            $eliminar = $actual['eliminarproveedor'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    public function getFiltrado($condicion) {
        $consultado = false;
        $consulta = "SELECT * FROM proveedor p $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getProveedorById($idproveedor) {
        $consultado = false;
        $consulta = "SELECT * FROM proveedor AS p where p.idproveedor=:idproveedor;";
        $valores = array("idproveedor" => $idproveedor);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getDatosProveedor($idproveedor) {
        $proveedor = $this->getProveedorById($idproveedor);
        $datos = "";
        foreach ($proveedor as $proveedoractual) {
            $idproveedor = $proveedoractual['idproveedor'];
            $empresa = $proveedoractual['empresa'];
            $representante = $proveedoractual['representante'];
            $telefono = $proveedoractual['telefono'];
            $correo = $proveedoractual['email'];
            $cuenta = $proveedoractual['num_cuenta'];
            $clabe = $proveedoractual['clabe_interban'];
            $idbanco = $proveedoractual['idbanco'];
            $banco = $proveedoractual['nombre_banco'];
            $sucursal = $proveedoractual['nsucursal'];
            $rfc = $proveedoractual['rfc'];
            $razon = $proveedoractual['razon_social'];
            $datos = "$idproveedor</tr>$empresa</tr>$representante</tr>$telefono</tr>$correo</tr>$cuenta</tr>$clabe</tr>$idbanco</tr>$sucursal</tr>$rfc</tr>$razon</tr>$banco";
            break;
        }
        return $datos;
    }

    public function checarProveedor($p) {
        $existe = $this->validarExistenciaProveedor($p->getEmpresa(), $p->getNum_cuenta(), $p->getClave_interbancaria(), $p->getId_proveedor());
        $insertado = false;
        if (!$existe) {
            $insertado = $this->gestionarProveedor($p);
        }
        return $insertado;
    }

    public function getProveedorByEmpresa($empresa) {
        $consultado = false;
        $consulta = "SELECT * FROM proveedor WHERE empresa=:empresa;";
        $valores = array("empresa" => $empresa);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getProveedorByCuenta($cuenta) {
        $consultado = false;
        $consulta = "SELECT * FROM proveedor WHERE num_cuenta=:cuenta;";
        $valores = array("cuenta" => $cuenta);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getProveedorByClabe($clabe) {
        $consultado = false;
        $consulta = "SELECT * FROM proveedor WHERE rfc=:clabe;";
        $valores = array("clabe" => $clabe);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function validarExistenciaProveedor($empresa, $cuenta, $clabe, $idproveedor) {
        $existe = false;
        $proveedores = $this->getProveedorByEmpresa($empresa);
        foreach ($proveedores as $proveedoractual) {
            $idproveedoractual = $proveedoractual['idproveedor'];
            if ($idproveedoractual != $idproveedor) {
                echo "0Esta empresa ya esta registrada como proveedor.";
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            $proveedores = $this->getProveedorByCuenta($cuenta);
            foreach ($proveedores as $proveedoractual) {
                $idproveedoractual = $proveedoractual['idproveedor'];
                if ($idproveedoractual != $idproveedor) {
                    echo "0Ya existe un proveedor con este número de cuenta.";
                    $existe = true;
                    break;
                }
            }
        }
        if (!$existe) {
            $proveedores = $this->getProveedorByClabe($clabe);
            foreach ($proveedores as $proveedoractual) {
                $idproveedoractual = $proveedoractual['idproveedor'];
                if ($idproveedoractual != $idproveedor) {
                    echo "0Ya existe un proveedor con esta clabe interbancaria.";
                    $existe = true;
                    break;
                }
            }
        }
        return $existe;
    }

    private function gestionarProveedor($p) {
        $consulta =  $p->getId_proveedor() != 0 
            ? "UPDATE `proveedor` SET empresa=:empresa, representante=:representante, telefono=:telefono, email=:email, num_cuenta=:cuenta, clabe_interban=:clabe, idbanco=:idbanco, nombre_banco=:nombre_banco, nsucursal=:sucursal, rfc=:rfc, razon_social=:razon WHERE idproveedor=:id;"
            : "INSERT INTO `proveedor` VALUES (NULL, :empresa, :representante, :telefono, :email, :cuenta, :clabe, :idbanco, :nombre_banco, :sucursal, :rfc, :razon);";
        
        $valores = array(
            "empresa" => $p->getEmpresa(),
            "representante" => $p->getRepresentante(),
            "telefono" => $p->getTelefono(),
            "email" => $p->getEmail(),
            "cuenta" => $p->getNum_cuenta(),
            "clabe" => $p->getClave_interbancaria(),
            "idbanco" => $p->getId_banco(),
            "nombre_banco" => $p->getNombre_banco(),
            "sucursal" => $p->getSucursal(),
            "rfc" => $p->getRfc(),
            "razon" => $p->getRazon(),
            "id" => $p->getId_proveedor()
        );
    
        $registrado = $this->consultas->execute($consulta, $valores);
        return $registrado;
    }
    
    public function quitarProveedor($idproveedor) {
        $eliminado = $this->eliminarProveedor($idproveedor);
        return $eliminado;
    }

    private function eliminarProveedor($idproveedor) {
        $eliminado = false;
        $consulta = "DELETE FROM `proveedor` WHERE idproveedor=:id;";
        $valores = array("id" => $idproveedor);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }
}