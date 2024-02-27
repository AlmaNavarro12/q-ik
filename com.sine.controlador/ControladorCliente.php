<?php
require_once '../com.sine.dao/Consultas.php';

class ControladorCliente {
    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function insertarCliente($c) {
        $registrado = false;
        $consulta = "INSERT INTO cliente VALUES (:id, :nombre, :apaterno, :amaterno, :nombre_empresa, :email_informacion, :email_facturacion, :email_gerencia, :telefono, :idbanco, :cuenta, :clabe, :idbanco1, :cuenta1, :clabe1, :idbanco2, :cuenta2, :clabe2, :idbanco3, :cuenta3, :clabe3, :rfc, :razon_social, :regimenfiscal, :calle, :numero_interior, :numero_exterior, :localidad, :idmunicipio, :idestado, :pais, :codigo_postal, :correoalt1, :correoalt2, :correoalt3);";
        $valores = $this->getValores($c);
        $registrado = $this->consultas->execute($consulta, $valores);
        return $registrado;
    }

    private function getValores($c){
        return array(
            "id" => $c->getIdCliente(),
            "nombre" => $c->getNombre(),
            "apaterno" => $c->getApellidoPaterno(),
            "amaterno" => $c->getApellidoMaterno(),
            "nombre_empresa" => $c->getNombre_empresa(),
            "email_informacion" => $c->getCorreoinfo(),
            "email_facturacion" => $c->getCorreo_fact(),
            "email_gerencia" => $c->getCorreo_gerencia(),
            "telefono" => $c->getTelefono(),
            "idbanco" => $c->getIdbanco(),
            "cuenta" => $c->getCuenta(),
            "clabe" => $c->getClabe(),
            "idbanco1" => $c->getIdbanco1(),
            "cuenta1" => $c->getCuenta1(),
            "clabe1" => $c->getClabe1(),
            "idbanco2" => $c->getIdbanco2(),
            "cuenta2" => $c->getCuenta2(),
            "clabe2" => $c->getClabe2(),
            "idbanco3" => $c->getIdbanco3(),
            "cuenta3" => $c->getCuenta3(),
            "clabe3" => $c->getClabe3(),
            "rfc" => $c->getRfc(),
            "razon_social" => $c->getRazon(),
            "regimenfiscal" => $c->getRegimen(),
            "calle" => $c->getCalle(),
            "numero_interior" => $c->getNum_interior(),
            "numero_exterior" => $c->getNum_exterior(),
            "localidad" => $c->getLocalidad(),
            "idmunicipio" => $c->getMunicipio(),
            "idestado" => $c->getEstado(),
            "pais" => $c->getPais(),
            "codigo_postal" => $c->getCodigo_postal(),
            "correoalt1" => $c->getCorreoalt1(),
            "correoalt2" => $c->getCorreoalt2(),
            "correoalt3" => $c->getCorreoalt3()
        );
    }

    public function getFiltrado($condicion){
        $consultado = false;
        $consulta = "SELECT * FROM cliente c $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getClienteById($idcliente){
        $consultado = false;
        $consulta = "SELECT * FROM cliente AS c where c.id_cliente=:idcliente;";
        $valores = array("idcliente" => $idcliente);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getClienteByRfc($rfc){
        $consultado = false;
        if ($rfc != null) {
            $consulta = "SELECT * FROM cliente WHERE rfc=:rfc;";
            $valores = array("rfc" => $rfc);
            $consultado = $this->consultas->getResults($consulta, $valores);
            return $consultado;
        } else {
            $consultado = false;
        }
    }

    public function nuevoCliente($c){
        $existe = $this->validarExistenciaCliente($c->getRfc(), $c->getIdCliente());
        $insertado = false;
        if (!$existe) {
            $insertado = $this->insertarCliente($c);
        }
        return $insertado;
    }

    public function validarExistenciaCliente($rfc, $idcliente){
        $existe = false;
        $clientes = $this->getClienteByRfc($rfc);
        foreach ($clientes as $clienteactual) {
            $idclienteactual = $clienteactual['id_cliente'];
            if ($idclienteactual != $idcliente) {
                echo "0Este RFC ya esta registrado como cliente";
                $existe = true;
            } else if ($idcliente == $idclienteactual) {
                $existe = false;
            }
        }
        return $existe;
    }

    public function getDatosCliente($idcliente){
        $cliente = $this->getClienteById($idcliente);
        $datos = "";
        foreach ($cliente as $clienteactual) {
            $idcliente = $clienteactual['id_cliente'];
            $nombre = $clienteactual['nombre'];
            $apaterno = $clienteactual['apaterno'];
            $amaterno = $clienteactual['amaterno'];
            $empresa = $clienteactual['nombre_empresa'];
            $correo_info = $clienteactual['email_informacion'];
            $correo_fact = $clienteactual['email_facturacion'];
            $correo_gerencia = $clienteactual['email_gerencia'];
            $telefono = $clienteactual['telefono'];
            $idbanco = $clienteactual['idbanco'];
            $cuenta = $clienteactual['cuenta'];
            $clabe = $clienteactual['clabe'];
            $idbanco1 = $clienteactual['idbanco1'];
            $cuenta1 = $clienteactual['cuenta1'];
            $clabe1 = $clienteactual['clabe1'];
            $idbanco2 = $clienteactual['idbanco2'];
            $cuenta2 = $clienteactual['cuenta2'];
            $clabe2 = $clienteactual['clabe2'];
            $idbanco3 = $clienteactual['idbanco3'];
            $cuenta3 = $clienteactual['cuenta3'];
            $clabe3 = $clienteactual['clabe3'];
            $rfc = $clienteactual['rfc'];
            $razon = $clienteactual['razon_social'];
            $regimen = $clienteactual['regimen_cliente'];
            $calle = $clienteactual['calle'];
            $interior = $clienteactual['numero_interior'];
            $exterior = $clienteactual['numero_exterior'];
            $idestado = $clienteactual['idestado'];
            $idmunicipio = $clienteactual['idmunicipio'];
            $localidad = $clienteactual['localidad'];
            $codigo_postal = $clienteactual['codigo_postal'];
            $correoalt1 = $clienteactual['correoalt1'];
            $correoalt2 = $clienteactual['correoalt2'];
            $correoalt3 = $clienteactual['correoalt3'];

            $datos = "$idcliente</tr>$nombre</tr>$apaterno</tr>$amaterno</tr>$empresa</tr>$correo_info</tr>$correo_fact</tr>$correo_gerencia</tr>$telefono</tr>$rfc</tr>$razon</tr>$regimen</tr>$calle</tr>$interior</tr>$exterior</tr>$idestado</tr>$idmunicipio</tr>$localidad</tr>$codigo_postal</tr>$idbanco</tr>$cuenta</tr>$clabe</tr>$idbanco1</tr>$cuenta1</tr>$clabe1</tr>$idbanco2</tr>$cuenta2</tr>$clabe2</tr>$idbanco3</tr>$cuenta3</tr>$clabe3</tr>$correoalt1</tr>$correoalt2</tr>$correoalt3";
            break;
        }
        return $datos;
    }


    public function modificarCliente($c){
        $existe = $this->validarExistenciaCliente($c->getRfc(), $c->getIdCliente());
        $actualizado = false;
        if (!$existe) {
            $actualizado = $this->actualizarCliente($c);
        }
        return $actualizado;
    }

    private function actualizarCliente($c) {
        $actualizado = false;
        $consulta = "UPDATE cliente SET  nombre=:nombre, apaterno=:apaterno, amaterno=:amaterno, nombre_empresa=:nombre_empresa, email_informacion=:email_informacion, email_facturacion=:email_facturacion, email_gerencia=:email_gerencia, telefono=:telefono, idbanco=:idbanco, cuenta=:cuenta, clabe=:clabe, idbanco1=:idbanco1, cuenta1=:cuenta1, clabe1=:clabe1, idbanco2=:idbanco2, cuenta2=:cuenta2, clabe2=:clabe2, idbanco3=:idbanco3, cuenta3=:cuenta3, clabe3=:clabe3, rfc=:rfc, razon_social=:razon_social, regimen_cliente=:regimenfiscal, calle=:calle, numero_interior=:numero_interior, numero_exterior=:numero_exterior, localidad=:localidad, idmunicipio=:idmunicipio, idestado=:idestado, pais=:pais, codigo_postal=:codigo_postal, correoalt1=:correoalt1, correoalt2=:correoalt2, correoalt3=:correoalt3 WHERE id_cliente=:id;";
        $valores = $this->getValores($c);
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function quitarCliente($c){
        $eliminado = $this->eliminarCliente($c);
        return $eliminado;
    }

    private function eliminarCliente($c){
        $eliminado = false;
        $consulta = "DELETE FROM cliente WHERE id_cliente=:id;";
        $valores = array("id" => $c);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }

    private function getNumrowsAux($condicion){
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM cliente AS c $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getNumrows($condicion){
        $numrows = 0;
        $rows = $this->getNumrowsAux($condicion);
        foreach ($rows as $actual) {
            $numrows = $actual['numrows'];
        }
        return $numrows;
    }

    private function getPermisoById($idusuario){
        $consultado = false;
        $consulta = "SELECT * FROM usuariopermiso c where permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getPermisos($idusuario){
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $editar = $actual['editarcliente'];
            $eliminar = $actual['eliminarcliente'];
            $datos .= "$editar</tr>$eliminar";
        }
        return $datos;
    }

    public function listaClientesHistorial($REF, $pag, $numreg){
        include '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $datos = "<thead>
            <tr class='align-middle'>
                <th>Nombre</th>
                <th class='text-center'>Empresa</th>
                <th class='text-center'>Correo Información</th>
                <th class='text-center'>Correo Facturación</th>
                <th class='text-center'>Correo Gerencia</th>
                <th class='text-center'>Teléfono</th>
                <th class='text-center'>RFC</th>
                <th class='text-center'>Razón Social</th>
                <th class='text-center'>Opci&oacute;n</th>
            </tr>
        </thead>
        <tbody>";

        $condicion = "";
        if ($REF == "") {
            $condicion = " ORDER BY c.nombre";
        } else {
            $condicion = "WHERE (c.nombre LIKE '%$REF%' OR c.apaterno LIKE '%$REF%' OR c.amaterno LIKE '%$REF%' OR c.rfc LIKE '%$REF%') ORDER BY c.nombre";
        }

        $numrows = $this->getNumrows($condicion);
        $page = (isset($pag) && !empty($pag)) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $clientes = $this->getFiltrado($con);
        $finales = 0;
        $permisos = $this->getPermisos($idlogin);
        $div = explode("</tr>", $permisos);

        foreach ($clientes as $clienteactual) {
            $idcliente = $clienteactual['id_cliente'];
            $nombre = $clienteactual['nombre'];
            $apaterno = $clienteactual['apaterno'];
            $amaterno = $clienteactual['amaterno'];
            $nombre_empresa = $clienteactual['nombre_empresa'];
            $correo_informacion = $clienteactual['email_informacion'];
            $correo_facturacion = $clienteactual['email_facturacion'];
            $correo_gerencia = $clienteactual['email_gerencia'];
            $telefono = $clienteactual['telefono'];
            $rfc = $clienteactual['rfc'];
            $razon_social = $clienteactual['razon_social'];

            $editar = '';
            if ($div[0] == '1' || $div[1] == '1') {
                $editar .= '<div class="dropdown">
                                <button class="button-list dropdown-toggle" title="Opciones" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="fas fa-ellipsis-v text-muted"></span>
                                <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">';

                if ($div[0] == '1') {
                    $editar .= '<li class="notification-link py-1 ps-3"><a class="text-decoration-none text-secondary-emphasis" onclick="editarCliente(' . $idcliente . ')">Editar cliente <span class="text-muted fas fa-edit small"></span></a></li>';
                }

                if ($div[1] == '1') {
                    $editar .= '<li class="notification-link py-1 ps-3"><a class="text-decoration-none text-secondary-emphasis" onclick="eliminarCliente(' . $idcliente . ')">Eliminar cliente <span class="text-muted fas fa-times"></span></a></li>';
                }

                $editar .= '</div>
                </div>';
            }


            $datos .= "<tr>
                         <td>$nombre $apaterno $amaterno </td>
                         <td class='text-center'>$nombre_empresa</td>
                         <td class='text-break text-center'>$correo_informacion</td>
                         <td class='text-break text-center'>$correo_facturacion</td>
                         <td class='text-break text-center'>$correo_gerencia</td>
                         <td>$telefono</td>
                         <td class='text-center'>$rfc</td>
                         <td class='text-center'>$razon_social</td>
                         <td class='text-center'>$editar</td>
                    </tr>";
            $finales++;
        }

        $inicios = $offset + 1;
        $finales += $inicios - 1;
        $function = "buscarCliente";
        if ($finales == 0) {
            $datos .= "<tr><td class='text-center' colspan='12'>No se encontraron registros</td></tr>";
        }
        $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='6'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

        return $datos;
    }
}
