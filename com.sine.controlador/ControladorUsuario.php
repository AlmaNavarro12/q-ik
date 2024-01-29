<?php
require_once '../com.sine.dao/Consultas.php';

class ControladorUsuario
{

    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

    public function listaServiciosHistorial($US, $numreg, $pag)
    {
        require_once '../com.sine.common/pagination.php';
        session_start();
        $idlogin = $_SESSION[sha1("idusuario")];
        $permisos = explode("</tr>", $this->getPermisosUsuarioAsig($idlogin));

        $datos = "<thead class='p-0'>
        <tr>
            <th class='col-auto'>Usuario</th>
            <th class='col-auto'>Nombre</th>
            <th class='col-auto'>Apellido paterno</th>
            <th class='col-auto'>Apellido materno</th>
            <th class='col-auto'>Correo</th>
            <th class='col-auto'>Celular</th>
            <th class='col-auto'>Teléfono</th>
            <th class='col-auto'>Opción</th>
        </tr>
    </thead>
    <tbody>";

        $condicion = empty($US) ? " ORDER BY u.usuario" : "WHERE (u.usuario LIKE '%$US%') OR (concat(nombre,' ',apellido_paterno,' ',apellido_materno) LIKE '%$US%') ORDER BY u.usuario";

        $numrows = $this->getNumrows($condicion);
        $page = isset($pag) && !empty($pag) ? $pag : 1;
        $per_page = $numreg;
        $adjacents = 4;
        $offset = ($page - 1) * $per_page;
        $total_pages = ceil($numrows / $per_page);
        $con = $condicion . " LIMIT $offset,$per_page ";
        $usuarios = $this->getSevicios($con);

        $inicios = $offset + 1;
        $finales = $inicios + count($usuarios) - 1;

        if (empty($usuarios)) {
            $datos .= "<tr><td colspan='8'>No se encontraron registros.</td></tr>";
        } else {
            foreach ($usuarios as $usuarioactual) {
                $id_usuario = $usuarioactual['idusuario'];
                $nombre = $usuarioactual['nombre'];
                $apellido_paterno = $usuarioactual['apellido_paterno'];
                $apellido_materno = $usuarioactual['apellido_materno'];
                $usuario = $usuarioactual['usuario'];
                $correo = $usuarioactual['email'];
                $celular = $usuarioactual['celular'];
                $telefono = $usuarioactual['telefono_fijo'];
                $datos .= "<tr>
                <td>$usuario</td>
                <td>$nombre</td>
                <td>$apellido_paterno</td>
                <td>$apellido_materno</td>
                <td>$correo</td>
                <td>$celular</td>
                <td>$telefono</td>
                <td class='text-center'>
                    <div class='dropdown'>
                        <button class='button-list dropdown-toggle' title='Opciones' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                            <span class='fas fa-ellipsis-v text-muted'></span>
                            <span class='caret'></span>
                        </button>
                        <ul class='dropdown-menu dropdown-menu-right'>
                            <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='editarUsuario($id_usuario)'>Editar usuario <span class='text-muted fas fa-edit small'></span></a></li>";
                if ($permisos[0] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarUsuario($id_usuario)'>Eliminar usuario <span class='text-muted fas fa-times'></span></a></li>";
                }

                if ($permisos[1] == '1') {
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick='asignarPermisos($id_usuario)'>Asignar permisos <span class='text-muted fas fa-sign-in-alt small'></span></a></li>";
                }
                $datos .= "</ul>
                    </div>
                </td> 
            </tr>";
            }
        }

        $function = "buscarUsuario";
        $datos .= "</tbody><tfoot><tr><th colspan='5' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='3'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

        return $datos;
    }


    private function getPermisosUsuarioAsig($idusuario)
    {
        $datos = "";
        $permisos = $this->getPermisoById($idusuario);
        foreach ($permisos as $actual) {
            $eliminar = $actual['eliminarusuario'];
            $asignarp = $actual['asignarpermiso'];
            $datos .= "$eliminar</tr>$asignarp";
        }
        return $datos;
    }

    private function getPermisoById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno FROM usuariopermiso p inner join usuario u on (p.permiso_idusuario=u.idusuario) where permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }


    private function getNumrowsAux($condicion)
    {
        $consultado = false;
        $consulta = "SELECT count(*) numrows FROM usuario AS u $condicion;";
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

    public function getSevicios($condicion)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario AS u $condicion;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    public function getDatosUsuario($idusuario)
    {
        $usuario = $this->getUsuarioById($idusuario);
        $datos = "";
        foreach ($usuario as $usuarioactual) {
            session_start();
            $usuariologin = $_SESSION[sha1("idusuario")];
            $tipologin = $_SESSION[sha1("tipousuario")];
            $idusuario = $usuarioactual['idusuario'];
            $nombre = $usuarioactual['nombre'];
            $apellidopaterno = $usuarioactual['apellido_paterno'];
            $apellidomaterno = $usuarioactual['apellido_materno'];
            $usuario = $usuarioactual['usuario'];
            $correo = $usuarioactual['email'];
            $celular = $usuarioactual['celular'];
            $telefono = $usuarioactual['telefono_fijo'];
            $estatus = $usuarioactual['estatus'];
            $contraseña = $usuarioactual['password'];
            $tipo = $usuarioactual['tipo'];
            $imgnm = $usuarioactual['imgperfil'];

            $img = "";
            if ($imgnm != "") {
                $imgfile = "../img/usuarios/" . $imgnm;
                if (file_exists($imgfile)) {
                    $type = pathinfo($imgfile, PATHINFO_EXTENSION);
                    $data = file_get_contents($imgfile);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $img = "<img src=\"$base64\" class=\"rounded-circle border border-secondary shadow-sm\" width=\"200px\">";
                }
            }

            $datos = "$idusuario</tr>$nombre</tr>$apellidopaterno</tr>$apellidomaterno</tr>$usuario</tr>$correo</tr>$celular</tr>$telefono</tr>$estatus</tr>$contraseña</tr>$tipo</tr>$usuariologin</tr>$tipologin</tr>$imgnm</tr>$img";
            break;
        }
        return $datos;
    }

    private function getUsuarioById($idusuario)
    {
        $consultado = false;
        $consulta = "SELECT u.* FROM usuario AS u WHERE u.idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function nuevoUsuario($u)
    {
        $existe = $u->getIdUsuario() != 0 ?
            $this->validarExistenciaUsuario($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), $u->getIdUsuario()) :
            $this->validarExistenciaUsuario($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), 0);

        $guardado = false;
        if (!$existe) {
            $guardado = $this->guardarUsuario($u);
        }
        return $guardado;
    }

    public function guardarUsuario($u)
    {
        $existe = $u->getIdUsuario() != 0 ?
            $this->validarExistenciaUsuario($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), $u->getIdUsuario()) :
            $this->validarExistenciaUsuario($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), 0);

        $guardado = false;

        if (!$existe) {
            $pass = "";
            if ($u->getChpass() == '1') {
                $pass = " password=sha1(:contrasena),";
            }

            $img = $u->getImg();
            if ($img == '') {
                $img = $u->getImgactualizar();
            } else if ($img != $u->getImgactualizar()) {
                if ($img != "") {
                    rename('../temporal/tmp/' . $img, '../img/usuarios/' . $img);
                    unlink("../img/usuarios/" . $u->getImgactualizar());
                }
            }

            $acceso = $u->getIdUsuario() != 0 ? '' : $this->getTAcceso();
            $div = explode("</tr>", $acceso);

            $consulta = $u->getIdUsuario() != 0 ?
                "UPDATE `usuario` SET  nombre=:nombre, apellido_paterno=:apellidopaterno, apellido_materno=:apellidomaterno, usuario=:usuario, email=:correo,$pass celular=:celular, telefono_fijo=:telefono, tipo=:tipo, imgperfil=:img WHERE idusuario=:id;" :
                "INSERT INTO `usuario` VALUES (null, :nombre, :apellidopaterno, :apellidomaterno, :usuario, :contrasena, :correo, :celular, :telefono, :estatus, :tipo, :acceso, :paq, :fecha, :img, :fts);";

            $valores = $u->getIdUsuario() != 0 ?
                [
                    "nombre" => $u->getNombre(),
                    "apellidopaterno" => $u->getApellidoPaterno(),
                    "apellidomaterno" => $u->getApellidoMaterno(),
                    "usuario" => $u->getUsuario(),
                    "correo" => $u->getCorreo(),
                    "celular" => $u->getCelular(),
                    "contrasena" => $u->getContrasena(),
                    "telefono" => $u->getTelefono(),
                    "tipo" => $u->getTipo(),
                    "img" => $img,
                    "id" => $u->getIdUsuario()
                ] :
                [
                    "nombre" => $u->getNombre(),
                    "apellidopaterno" => $u->getApellidoPaterno(),
                    "apellidomaterno" => $u->getApellidoMaterno(),
                    "usuario" => $u->getUsuario(),
                    "contrasena" => $u->getContrasena(),
                    "correo" => $u->getCorreo(),
                    "celular" => $u->getCelular(),
                    "telefono" => $u->getTelefono(),
                    "estatus" => $u->getEstatus(),
                    "tipo" => $u->getTipo(),
                    "acceso" => $div[0],
                    "paq" => $div[1],
                    "fecha" => $this->getFechaReg(),
                    "img" => $img,
                    "fts" => '0'
                ];

            $guardado = $this->consultas->execute($consulta, $valores);

            if ($u->getIdUsuario() == 0) {
                $idusuario = $this->getUserID($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo());
                $permisos = $this->insertarPermisos($idusuario);
            }
        }

        return $guardado;
    }

    private function insertarPermisos($idusuario)
    {
        $actualizado = false;
        $consulta = "INSERT INTO `usuariopermiso` VALUES (:id, :idusuario, :facturas, :crearfactura, :editarfactura, :eliminarfactura, :listafactura, :pago, :crearpago, :editarpago, :eliminarpago, :listapago, :nomina, :listaempleado, :crearempleado, :editarempleado, :eliminarempleado, :listanomina, :crearnomina, :editarnomina, :eliminarnomina, :cartaporte, :listaubicacion, :crearubicacion, :editarubicacion, :eliminarubicacion, :listatransporte, :creartransporte, :editartransporte, :eliminartransporte, :listaremolque, :crearremolque, :editarremolque, :eliminarremolque, :listaoperador, :crearoperador, :editaroperador, :eliminaroperador, :listacarta, :crearcarta, :editarcarta, :eliminarcarta, :cotizacion, :crearcotizacion, :editarcotizacion, :eliminarcotizacion, :listacotizacion, :anticipo, :cliente, :crearcliente, :editarcliente, :eliminarcliente, :listacliente, :comunicado, :crearcomunicado, :editarcomunicado, :eliminarcomunicado, :listacomunicado, :producto, :crearproducto, :editarproducto, :eliminarproducto, :listaproducto, :proveedor, :crearproveedor, :editarproveedor, :eliminarproveedor, :listaproveedor, :impuesto, :crearimpuesto, :editarimpuesto, :eliminarimpuesto, :listaimpuesto, :datosfacturacion, :creardatos, :editardatos, :listadatos, :contrato, :crearcontrato, :editarcontrato, :eliminarcontrato, :listacontrato, :usuario, :crearusuario, :listausuario, :eliminarusuario, :asignarpermiso, :reporte, :reportefactura, :reportepago, :reportegrafica, :reporteiva, :datosiva, :reporteventa, :configuracion, :addfolio, :listafolio, :editarfolio, :eliminarfolio, :addcomision, :encabezados, :confcorreo, :importar);";
        $valores = array(
            "id" => null,
            "idusuario" => $idusuario,
            "facturas" => '0',
            "crearfactura" => '0',
            "editarfactura" => '0',
            "eliminarfactura" => '0',
            "listafactura" => '0',
            "pago" => '0',
            "crearpago" => '0',
            "editarpago" => '0',
            "eliminarpago" => '0',
            "listapago" => '0',
            "nomina" => '0',
            "listaempleado" => '0',
            "crearempleado" => '0',
            "editarempleado" => '0',
            "eliminarempleado" => '0',
            "listanomina" => '0',
            "crearnomina" => '0',
            "editarnomina" => '0',
            "eliminarnomina" => '0',
            "cartaporte" => '0',
            "listaubicacion" => '0',
            "crearubicacion" => '0',
            "editarubicacion" => '0',
            "eliminarubicacion" => '0',
            "listatransporte" => '0',
            "creartransporte" => '0',
            "editartransporte" => '0',
            "eliminartransporte" => '0',
            "listaremolque" => '0',
            "crearremolque" => '0',
            "editarremolque" => '0',
            "eliminarremolque" => '0',
            "listaoperador" => '0',
            "crearoperador" => '0',
            "editaroperador" => '0',
            "eliminaroperador" => '0',
            "listacarta" => '0',
            "crearcarta" => '0',
            "editarcarta" => '0',
            "eliminarcarta" => '0',
            "cotizacion" => '0',
            "crearcotizacion" => '0',
            "editarcotizacion" => '0',
            "eliminarcotizacion" => '0',
            "listacotizacion" => '0',
            "anticipo" => '0',
            "cliente" => '0',
            "crearcliente" => '0',
            "editarcliente" => '0',
            "eliminarcliente" => '0',
            "listacliente" => '0',
            "comunicado" => '0',
            "crearcomunicado" => '0',
            "editarcomunicado" => '0',
            "eliminarcomunicado" => '0',
            "listacomunicado" => '0',
            "producto" => '0',
            "crearproducto" => '0',
            "editarproducto" => '0',
            "eliminarproducto" => '0',
            "listaproducto" => '0',
            "proveedor" => '0',
            "crearproveedor" => '0',
            "editarproveedor" => '0',
            "eliminarproveedor" => '0',
            "listaproveedor" => '0',
            "impuesto" => '0',
            "crearimpuesto" => '0',
            "editarimpuesto" => '0',
            "eliminarimpuesto" => '0',
            "listaimpuesto" => '0',
            "datosfacturacion" => '0',
            "creardatos" => '0',
            "editardatos" => '0',
            "listadatos" => '0',
            "contrato" => '0',
            "crearcontrato" => '0',
            "editarcontrato" => '0',
            "eliminarcontrato" => '0',
            "listacontrato" => '0',
            "usuario" => '0',
            "crearusuario" => '0',
            "listausuario" => '0',
            "eliminarusuario" => '0',
            "asignarpermiso" => '0',
            "reporte" => '0',
            "reportefactura" => '0',
            "reportepago" => '0',
            "reportegrafica" => '0',
            "reporteiva" => '0',
            "datosiva" => '0',
            "reporteventa" => '0',
            "configuracion" => '0',
            "addfolio" => '0',
            "listafolio" => '0',
            "editarfolio" => '0',
            "eliminarfolio" => '0',
            "addcomision" => '0',
            "encabezados" => '0',
            "confcorreo" => '0',
            "importar" => '0'
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function validarExistenciaUsuario($nombrecompleto, $correo, $usuario, $idusuario)
    {
        $existe = false;
        $usuarios = $this->getUsuarioByNombreCompleto($nombrecompleto);
        foreach ($usuarios as $usuarioactual) {
            $idusuarioactual = $usuarioactual['idusuario'];
            if ($idusuarioactual != $idusuario) {
                echo "0Ya existe un usuario con este mismo nombre y apellidos.";
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            $usuarios = $this->getUsuarioByNombreUsuario($usuario);
            foreach ($usuarios as $usuarioactual) {
                $idusuarioactual = $usuarioactual['idusuario'];
                if ($idusuarioactual != $idusuario) {
                    echo "0Ya existe este nombre de usuario, intenta con otro.";
                    $existe = true;
                    break;
                }
            }
        }
        if (!$existe) {
            $usuarios = $this->getUsuarioByCorreo($correo);
            foreach ($usuarios as $usuarioactual) {
                $idusuarioactual = $usuarioactual['idusuario'];
                if ($idusuarioactual != $idusuario) {
                    echo "0Ya existe este correo, intenta con otro.";
                    $existe = true;
                    break;
                }
            }
        }
        return $existe;
    }

    private function getUsuarioByNombreCompleto($nombrecompleto)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE concat(nombre,apellido_paterno,apellido_materno)=:nombrecompleto;";
        $valores = array("nombrecompleto" => $nombrecompleto);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getUsuarioByNombreUsuario($nombreusuario)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE usuario=:usuario;";
        $valores = array("usuario" => $nombreusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getUsuarioByCorreo($correo)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE email=:correo;";
        $valores = array("correo" => $correo);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getFechaReg()
    {
        $fechareg = "";
        $user = $this->getFechaAccAux();
        foreach ($user as $actual) {
            $fechareg = $actual['fecharegistro'];
        }
        return $fechareg;
    }

    private function getTAcceso()
    {
        $tacceso = "";
        $datos = $this->getFechaAccAux();
        foreach ($datos as $actual) {
            $tacceso = $actual['acceso'] . "</tr>" . $actual['paquete'];
        }
        return $tacceso;
    }

    private function getFechaAccAux()
    {
        $consultado = false;
        $c = new Consultas();
        $consulta = "SELECT fecharegistro, acceso, paquete FROM usuario ORDER BY idusuario ASC limit 1;";
        $consultado = $c->getResults($consulta, null);
        return $consultado;
    }

    private function getUserIDAux($concat)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario where concat(nombre,apellido_paterno,apellido_materno,usuario,email) = :concat;";
        $c = new Consultas();
        $val = array("concat" => $concat);
        $consultado = $c->getResults($consulta, $val);
        return $consultado;
    }

    private function getUserID($nombre, $usuario, $correo)
    {
        $concat = $nombre . $usuario . $correo;
        $iduser = "0";
        $user = $this->getUserIDAux($concat);
        foreach ($user as $actual) {
            $iduser = $actual['idusuario'];
        }
        return $iduser;
    }

    public function quitarUsuario($idusuario)
    {
        $eliminado = $this->eliminarUsuario($idusuario);
        return $eliminado;
    }

    private function eliminarUsuario($idusuario)
    {
        $eliminado = false;
        $consulta = "DELETE FROM `usuario` WHERE idusuario=:id;";
        $valores = array("id" => $idusuario);
        $eliminado = $this->consultas->execute($consulta, $valores);
        $permisos = $this->eliminarPermisos($idusuario);
        return $eliminado;
    }

    private function eliminarPermisos($idusuario)
    {
        $eliminado = false;
        $consulta = "DELETE FROM `usuariopermiso` WHERE permiso_idusuario=:id;";
        $valores = array("id" => $idusuario);
        $eliminado = $this->consultas->execute($consulta, $valores);
        return $eliminado;
    }
    public function getTipoLogin()
    {
        session_start();
        $usuariologin = $_SESSION[sha1("idusuario")];
        $tipologin = $_SESSION[sha1("tipousuario")];
        return $tipologin;
    }


    public function actualizarImgPerfil($u)
    {
        $actualizado = false;
        $img = $u->getImg();
        if ($img == '') {
            $img = $u->getImgactualizar();
        } else if ($img != $u->getImgactualizar()) {
            if ($img != "") {
                rename('../temporal/tmp/' . $img, '../img/usuarios/' . $img);
                unlink("../img/usuarios/" . $u->getImgactualizar());
            }
        }
        $consulta = "UPDATE `usuario` SET imgperfil=:img WHERE idusuario=:id;";
        $valores = array(
            "img" => $img,
            "id" => $u->getIdUsuario()
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }


    public function insertarPermisosList($u)
    {
        $actualizado = false;
        $consulta = "INSERT INTO `usuariopermiso` VALUES (:id, :idusuario, :facturas, :crearfactura, :editarfactura, :eliminarfactura, :listafactura, :pago, :crearpago, :editarpago, :eliminarpago, :listapago, :nomina, :listaempleado, :crearempleado, :editarempleado, :eliminarempleado, :listanomina, :crearnomina, :editarnomina, :eliminarnomina, :cartaporte, :listaubicacion, :crearubicacion, :editarubicacion, :eliminarubicacion, :listatransporte, :creartransporte, :editartransporte, :eliminartransporte, :listaremolque, :crearremolque, :editarremolque, :eliminarremolque, :listaoperador, :crearoperador, :editaroperador, :eliminaroperador, :listacarta, :crearcarta, :editarcarta, :eliminarcarta, :cotizacion, :crearcotizacion, :editarcotizacion, :eliminarcotizacion, :listacotizacion, :anticipo, :cliente, :crearcliente, :editarcliente, :eliminarcliente, :listacliente, :comunicado, :crearcomunicado, :editarcomunicado, :eliminarcomunicado, :listacomunicado, :producto, :crearproducto, :editarproducto, :eliminarproducto, :listaproducto, :proveedor, :crearproveedor, :editarproveedor, :eliminarproveedor, :listaproveedor, :impuesto, :crearimpuesto, :editarimpuesto, :eliminarimpuesto, :listaimpuesto, :datosfacturacion, :creardatos, :editardatos, :listadatos, :contrato, :crearcontrato, :editarcontrato, :eliminarcontrato, :listacontrato, :usuario, :crearusuario, :listausuario, :eliminarusuario, :asignarpermiso, :reporte, :reportefactura, :reportepago, :reportegrafica, :reporteiva, :datosiva, :reporteventa, :configuracion, :addfolio, :listafolio, :editarfolio, :eliminarfolio, :addcomision, :encabezados, :confcorreo, :importar);";
        $valores = array(
            "id" => null,
            "idusuario" => $u->getIdUsuario(),
            "facturas" => $u->getFacturas(),
            "crearfactura" => $u->getCrearfactura(),
            "editarfactura" => $u->getEditarfactura(),
            "eliminarfactura" => $u->getEliminarfactura(),
            "listafactura" => $u->getListafactura(),
            "pago" => $u->getPago(),
            "crearpago" => $u->getCrearpago(),
            "editarpago" => $u->getEditarpago(),
            "eliminarpago" => $u->getEliminarpago(),
            "listapago" => $u->getListapago(),
            "nomina" => $u->getNomina(),
            "listaempleado" => $u->getListaempleado(),
            "crearempleado" => $u->getCrearempleado(),
            "editarempleado" => $u->getEditarempleado(),
            "eliminarempleado" => $u->getEliminarempleado(),
            "listanomina" => $u->getListanomina(),
            "crearnomina" => $u->getCrearnomina(),
            "editarnomina" => $u->getEditarnomina(),
            "eliminarnomina" => $u->getEliminarnomina(),
            "cartaporte" => $u->getCartaporte(),
            "listaubicacion" => $u->getListaubicacion(),
            "crearubicacion" => $u->getCrearubicacion(),
            "editarubicacion" => $u->getEditarubicacion(),
            "eliminarubicacion" => $u->getEliminarubicacion(),
            "listatransporte" => $u->getListatransporte(),
            "creartransporte" => $u->getCreartransporte(),
            "editartransporte" => $u->getEditartransporte(),
            "eliminartransporte" => $u->getEliminartransporte(),
            "listaremolque" => $u->getListaremolque(),
            "crearremolque" => $u->getCrearremolque(),
            "editarremolque" => $u->getEditarremolque(),
            "eliminarremolque" => $u->getEliminarremolque(),
            "listaoperador" => $u->getListaoperador(),
            "crearoperador" => $u->getCrearoperador(),
            "editaroperador" => $u->getEditaroperador(),
            "eliminaroperador" => $u->getEliminaroperador(),
            "listacarta" => $u->getListacarta(),
            "crearcarta" => $u->getCrearcarta(),
            "editarcarta" => $u->getEditarcarta(),
            "eliminarcarta" => $u->getEliminarcarta(),
            "cotizacion" => $u->getCotizacion(),
            "crearcotizacion" => $u->getCrearcotizacion(),
            "editarcotizacion" => $u->getEditarcot(),
            "eliminarcotizacion" => $u->getEliminarcot(),
            "listacotizacion" => $u->getListacotizacion(),
            "anticipo" => $u->getAnticipo(),
            "cliente" => $u->getCliente(),
            "crearcliente" => $u->getCrearcliente(),
            "editarcliente" => $u->getEditarcliente(),
            "eliminarcliente" => $u->getEliminarcliente(),
            "listacliente" => $u->getListacliente(),
            "comunicado" => $u->getComunicado(),
            "crearcomunicado" => $u->getCrearcomunicado(),
            "editarcomunicado" => $u->getEditarcomunicado(),
            "eliminarcomunicado" => $u->getEliminarcomunicado(),
            "listacomunicado" => $u->getListacomunicado(),
            "producto" => $u->getProducto(),
            "crearproducto" => $u->getCrearproducto(),
            "editarproducto" => $u->getEditarproducto(),
            "eliminarproducto" => $u->getEliminarproducto(),
            "listaproducto" => $u->getListaproducto(),
            "proveedor" => $u->getProveedor(),
            "crearproveedor" => $u->getCrearproveedor(),
            "editarproveedor" => $u->getEditarproveedor(),
            "eliminarproveedor" => $u->getEliminarproveedor(),
            "listaproveedor" => $u->getListaproveedor(),
            "impuesto" => $u->getImpuesto(),
            "crearimpuesto" => $u->getCrearimpuesto(),
            "editarimpuesto" => $u->getEditarimpuesto(),
            "eliminarimpuesto" => $u->getEliminarimpuesto(),
            "listaimpuesto" => $u->getListaimpuesto(),
            "datosfacturacion" => $u->getDatosfacturacion(),
            "creardatos" => $u->getCreardatos(),
            "editardatos" => $u->getEditardatos(),
            "listadatos" => $u->getListadatos(),
            "contrato" => $u->getContrato(),
            "crearcontrato" => $u->getCrearcontrato(),
            "editarcontrato" => $u->getEditarcontrato(),
            "eliminarcontrato" => $u->getEliminarcontrato(),
            "listacontrato" => $u->getListacontrato(),
            "usuario" => $u->getUsuarios(),
            "crearusuario" => $u->getCrearusuario(),
            "listausuario" => $u->getListausuario(),
            "eliminarusuario" => $u->getEliminarusuario(),
            "asignarpermiso" => $u->getAsignarpermisos(),
            "reporte" => $u->getReporte(),
            "reportefactura" => $u->getReportefactura(),
            "reportepago" => $u->getReportepago(),
            "reportegrafica" => $u->getReportegrafica(),
            "reporteiva" => $u->getReporteiva(),
            "datosiva" => $u->getDatosiva(),
            "reporteventa" => $u->getReporteventas(),
            "configuracion" => $u->getConfiguracion(),
            "addfolio" => $u->getAddfolio(),
            "listafolio" => $u->getListafolio(),
            "editarfolio" => $u->getEditfolio(),
            "eliminarfolio" => $u->getEliminarfolio(),
            "addcomision" => $u->getAddcomision(),
            "encabezados" => $u->getEncabezados(),
            "confcorreo" => $u->getConfcorreo(),
            "importar" => $u->getImportar()
        );
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function checkAccion($u)
    {
        $datos = false;
        if ($u->getAccion() == '1') {
            $datos = $this->insertarPermisosList($u);
        } else if ($u->getAccion() == '0') {
            $datos = $this->actualizarPermisos($u);
        }
        return $datos;
    }

    private function actualizarPermisos($u)
    {
        $actualizado = false;

        $mapeoGet = array(
            "editarcotizacion" => "getEditarcot",
            "eliminarcotizacion" => "getEliminarcot",
            "crearnomina" => "getCrearnomina",
            "usuario" => "getUsuarios",
            "reporteventa" => "getReporteventas",
            "editarfolio" => "getEditfolio",
            "asignarpermiso" => "getAsignarpermisos",
        );

        $columnas = array(
            "facturas", "crearfactura", "editarfactura", "eliminarfactura", "listafactura",
            "pago", "crearpago", "editarpago", "eliminarpago", "listapago",
            "nomina", "listaempleado", "crearempleado", "editarempleado", "eliminarempleado", "listanomina", "crearnomina", "editarnomina", "eliminarnomina",
            "cartaporte", "listaubicacion", "crearubicacion", "editarubicacion", "eliminarubicacion", "listatransporte", "creartransporte", "editartransporte", "eliminartransporte", "listaremolque", "crearremolque", "editarremolque", "eliminarremolque", "listaoperador", "crearoperador", "editaroperador", "eliminaroperador", "listacarta", "crearcarta", "editarcarta", "eliminarcarta",
            "cotizacion", "crearcotizacion", "editarcotizacion", "eliminarcotizacion", "listacotizacion",
            "anticipo", "cliente", "crearcliente", "editarcliente", "eliminarcliente", "listacliente",
            "comunicado", "crearcomunicado", "editarcomunicado", "eliminarcomunicado", "listacomunicado",
            "producto", "crearproducto", "editarproducto", "eliminarproducto", "listaproducto",
            "proveedor", "crearproveedor", "editarproveedor", "eliminarproveedor", "listaproveedor",
            "impuesto", "crearimpuesto", "editarimpuesto", "eliminarimpuesto", "listaimpuesto",
            "datosfacturacion", "creardatos", "editardatos", "listadatos",
            "contrato", "crearcontrato", "editarcontrato", "eliminarcontrato", "listacontrato",
            "usuario", "crearusuario", "listausuario", "eliminarusuario", "asignarpermiso",
            "reporte", "reportefactura", "reportepago", "reportegrafica", "reporteiva", "datosiva", "reporteventa",
            "configuracion", "addfolio", "listafolio", "editarfolio", "eliminarfolio", "addcomision", "encabezados", "confcorreo", "importar"
        );

        $consulta = "UPDATE usuariopermiso SET ";
        foreach ($columnas as $columna) {
            $getter = isset($mapeoGet[$columna]) ? $mapeoGet[$columna] : "get" . $columna;
            $consulta .= "$columna=:$columna, ";
            $valores[$columna] = method_exists($u, $getter) ? $u->$getter() : null;
        }
        //quitar la ultima coma de las columnas
        $consulta = rtrim($consulta, ", ") . " WHERE permiso_idusuario=:id;";

        $valores["id"] = $u->getIdUsuario();
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function checkPermisos($idusuario)
    {
        $datos = "";
        $check = $this->checkPermisosAux($idusuario);
        if ($check) {
            $datos = $this->getPermisosUsuario($idusuario);
        } else {
            $datos = $this->getInsertPermisos($idusuario);
        }
        return $datos;
    }

    private function checkPermisosAux($idusuario)
    {
        $existe = false;
        $get = $this->getPermisoById($idusuario);
        foreach ($get as $actual) {
            $existe = true;
        }
        return $existe;
    }

    private function getInsertPermisos($idusuario)
    {
        $usuario = $this->getUsuarioById($idusuario);
        $datos = "";
        foreach ($usuario as $usuarioactual) {
            session_start();
            $usuariologin = $_SESSION[sha1("idusuario")];
            $idusuario = $usuarioactual['idusuario'];
            $nombreusuario = $usuarioactual['nombre'] . ' ' . $usuarioactual['apellido_paterno'] . ' ' . $usuarioactual['apellido_materno'];

            $datos = "$idusuario</tr>$nombreusuario</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>1</tr>$usuariologin";
            break;
        }
        return $datos;
    }

    public function getPermisosUsuario($idusuario)
    {
        $usuario = $this->getPermisoById($idusuario);
        $datos = "";

        foreach ($usuario as $usuarioactual) {
            session_start();
            $usuariologin = $_SESSION[sha1("idusuario")];

            $idusuario = $usuarioactual['permiso_idusuario'];
            $nombreusuario = $usuarioactual['nombre'] . ' ' . $usuarioactual['apellido_paterno'] . ' ' . $usuarioactual['apellido_materno'];

            $columnas = array_keys($usuarioactual);
            $datos = "$idusuario</tr>$nombreusuario";
            foreach ($columnas as $columna) {
                $datos .= "</tr>{$usuarioactual[$columna]}";
            }
            $datos .= "</tr>0</tr>$usuariologin";
            break;
        }

        return $datos;
    }
}
