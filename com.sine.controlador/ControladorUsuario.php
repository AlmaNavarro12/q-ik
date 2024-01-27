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
                    $img = "<img src=\"$base64\" width=\"200px\">";
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
        $existe = $this->validarExistenciaUsuario($u->getNombre() . "" . $u->getApellidoPaterno() . "" . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), 0);
        $insertado = false;
        if (!$existe) {
            $insertado = $this->insertarUsuario($u);
        }
        return $insertado;
    }

    private function insertarUsuario($u)
    {
        $registrado = false;
        $img = $u->getImg();
        $acceso = $this->getTAcceso();
        $div = explode("</tr>", $acceso);

        if ($img == '') {
            $img = $this->crearImg($u->getNombre());
        } else {
            rename('../temporal/tmp/' . $img, '../img/usuarios/' . $img);
        }
        $consulta = "INSERT INTO `usuario` VALUES (:id, :nombre, :apellidopaterno, :apellidomaterno, :usuario, :contrasena, :correo, :celular, :telefono, :estatus, :tipo, :acceso, :paq, :fecha, :img, :fts);";
        $valores = array(
            "id" => null,
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
        );
        $registrado = $this->consultas->execute($consulta, $valores);
        $idusuario = $this->getUserID($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo());
        $permisos = $this->insertarPermisos($idusuario);
        return $registrado;
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
                echo "0Ya existe un usuario con este mismo nombre y apellidos";
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            $usuarios = $this->getUsuarioByNombreUsuario($usuario);
            foreach ($usuarios as $usuarioactual) {
                $idusuarioactual = $usuarioactual['idusuario'];
                if ($idusuarioactual != $idusuario) {
                    echo "0Ya existe este nombre de usuario, intenta con otro";
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
                    echo "0Ya existe este correo, intenta con otro";
                    $existe = true;
                    break;
                }
            }
        }
        return $existe;
    }

    public function getUsuarioByNombreCompleto($nombrecompleto)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE concat(nombre,apellido_paterno,apellido_materno)=:nombrecompleto;";
        $valores = array("nombrecompleto" => $nombrecompleto);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getUsuarioByNombreUsuario($nombreusuario)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE usuario=:usuario;";
        $valores = array("usuario" => $nombreusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getUsuarioByCorreo($correo)
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

    private function crearImg($nombre)
    {
        $sn = substr($nombre, 0, 1);
        $f = getdate();
        $d = $f['mday'];
        $m = $f['mon'];
        $y = $f['year'];
        $h = $f['hours'];
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
        $hoy = $y . '-' . $m . '-' . $d . 'T' . $h . '.' . $mi . '.' . $s;
        header("Content-Type: image/png");
        $im = @imagecreate(20, 20) or die("Cannot Initialize new GD image stream");
        $color_fondo = imagecolorallocate($im, 9, 9, 107);
        $color_texto = imagecolorallocate($im, 255, 255, 255);
        imagestring($im, 5, 6, 2.8, "$sn", $color_texto);
        $imgname = "$sn$hoy.png";
        imagepng($im, '../img/usuarios/' . $imgname);
        imagedestroy($im);
        return $imgname;
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

    public function modificarUsuario($u) {
        $existe = $this->validarExistenciaUsuario($u->getNombre() . "" . $u->getApellidoPaterno() . "" . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), $u->getIdUsuario());
        $actualizado = false;
        if (!$existe) {
            $actualizado = $this->actualizarUsuario($u);
        }
        return $actualizado;
    }
    
    private function actualizarUsuario($u) {
        $actualizado = false;
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

        $consulta = "UPDATE `usuario` SET  nombre=:nombre, apellido_paterno=:apellidopaterno, apellido_materno=:apellidomaterno, usuario=:usuario, email=:correo,$pass celular=:celular, telefono_fijo=:telefono, tipo=:tipo, imgperfil=:img WHERE idusuario=:id;";
        $valores = array("nombre" => $u->getNombre(),
            "apellidopaterno" => $u->getApellidoPaterno(),
            "apellidomaterno" => $u->getApellidoMaterno(),
            "usuario" => $u->getUsuario(),
            "correo" => $u->getCorreo(),
            "celular" => $u->getCelular(),
            "contrasena" => $u->getContrasena(),
            "telefono" => $u->getTelefono(),
            "tipo" => $u->getTipo(),
            "img" => $img,
            "id" => $u->getIdUsuario());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function getTipoLogin() {
        session_start();
        $usuariologin = $_SESSION[sha1("idusuario")];
        $tipologin = $_SESSION[sha1("tipousuario")];
        return $tipologin;
    }

    
    public function actualizarImgPerfil($u) {
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
        $valores = array("img" => $img,
            "id" => $u->getIdUsuario());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }


    public function insertarPermisosList($u) {
        $actualizado = false;
        $consulta = "INSERT INTO `usuariopermiso` VALUES (:id, :idusuario, :facturas, :crearfactura, :editarfactura, :eliminarfactura, :listafactura, :pago, :crearpago, :editarpago, :eliminarpago, :listapago, :nomina, :listaempleado, :crearempleado, :editarempleado, :eliminarempleado, :listanomina, :crearnomina, :editarnomina, :eliminarnomina, :cartaporte, :listaubicacion, :crearubicacion, :editarubicacion, :eliminarubicacion, :listatransporte, :creartransporte, :editartransporte, :eliminartransporte, :listaremolque, :crearremolque, :editarremolque, :eliminarremolque, :listaoperador, :crearoperador, :editaroperador, :eliminaroperador, :listacarta, :crearcarta, :editarcarta, :eliminarcarta, :cotizacion, :crearcotizacion, :editarcotizacion, :eliminarcotizacion, :listacotizacion, :anticipo, :cliente, :crearcliente, :editarcliente, :eliminarcliente, :listacliente, :comunicado, :crearcomunicado, :editarcomunicado, :eliminarcomunicado, :listacomunicado, :producto, :crearproducto, :editarproducto, :eliminarproducto, :listaproducto, :proveedor, :crearproveedor, :editarproveedor, :eliminarproveedor, :listaproveedor, :impuesto, :crearimpuesto, :editarimpuesto, :eliminarimpuesto, :listaimpuesto, :datosfacturacion, :creardatos, :editardatos, :listadatos, :contrato, :crearcontrato, :editarcontrato, :eliminarcontrato, :listacontrato, :usuario, :crearusuario, :listausuario, :eliminarusuario, :asignarpermiso, :reporte, :reportefactura, :reportepago, :reportegrafica, :reporteiva, :datosiva, :reporteventa, :configuracion, :addfolio, :listafolio, :editarfolio, :eliminarfolio, :addcomision, :encabezados, :confcorreo, :importar);";
        $valores = array("id" => null,
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
            "importar" => $u->getImportar());
        $actualizado = $this->consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function checkAccion($u) {
        echo 'Hola ' . var_export($u, true);
        $datos = false;
        if ($u->getAccion() == '1') {
            $datos = $this->insertarPermisosList($u);
        } else if ($u->getAccion() == '0') {
            $datos = $this->actualizarPermisos($u);
        }
        return $datos;
    }
    

    private function actualizarPermisos($u) {
        
        $actualizado = false;
        $consulta = "UPDATE usuariopermiso SET  facturas=:facturas, crearfactura=:crearfactura, editarfactura=:editarfactura, eliminarfactura=:eliminarfactura, listafactura=:listafactura, pago=:pago, crearpago=:crearpago, editarpago=:editarpago, eliminarpago=:eliminarpago, listapago=:listapago, nomina=:nomina, listaempleado=:listaempleado, crearempleado=:crearempleado, editarempleado=:editarempleado, eliminarempleado=:eliminarempleado, listanomina=:listanomina, crearnomina=:crearnomina, editarnomina=:editarnomina, eliminarnomina=:eliminarnomina, cartaporte=:cartaporte, listaubicacion=:listaubicacion, crearubicacion=:crearubicacion, editarubicacion=:editarubicacion, eliminarubicacion=:eliminarubicacion, listatransporte=:listatransporte, creartransporte=:creartransporte, editartransporte=:editartransporte, eliminartransporte=:eliminartransporte, listaremolque=:listaremolque, crearremolque=:crearremolque, editarremolque=:editarremolque, eliminarremolque=:eliminarremolque, listaoperador=:listaoperador, crearoperador=:crearoperador, editaroperador=:editaroperador, eliminaroperador=:eliminaroperador, listacarta=:listacarta, crearcarta=:crearcarta, editarcarta=:editarcarta, eliminarcarta=:eliminarcarta, cotizacion=:cotizacion, crearcotizacion=:crearcotizacion, editarcotizacion=:editarcotizacion, eliminarcotizacion=:eliminarcotizacion, listacotizacion=:listacotizacion, anticipo=:anticipo, cliente=:cliente, crearcliente=:crearcliente, editarcliente=:editarcliente, eliminarcliente=:eliminarcliente, listacliente=:listacliente, comunicado=:comunicado, crearcomunicado=:crearcomunicado, editarcomunicado=:editarcomunicado, eliminarcomunicado=:eliminarcomunicado, listacomunicado=:listacomunicado, producto=:producto, crearproducto=:crearproducto, editarproducto=:editarproducto, eliminarproducto=:eliminarproducto, listaproducto=:listaproducto, proveedor=:proveedor, crearproveedor=:crearproveedor, editarproveedor=:editarproveedor, eliminarproveedor=:eliminarproveedor, listaproveedor=:listaproveedor, impuesto=:impuesto, crearimpuesto=:crearimpuesto, editarimpuesto=:editarimpuesto, eliminarimpuesto=:eliminarimpuesto, listaimpuesto=:listaimpuesto, datosfacturacion=:datosfacturacion, creardatos=:creardatos, editardatos=:editardatos, listadatos=:listadatos, contrato=:contrato, crearcontrato=:crearcontrato, editarcontrato=:editarcontrato, eliminarcontrato=:eliminarcontrato, listacontrato=:listacontrato, usuario=:usuario, crearusuario=:crearusuario, listausuario=:listausuario, eliminarusuario=:eliminarusuario, asignarpermiso=:asignarpermiso, reporte=:reporte, reportefactura=:reportefactura, reportepago=:reportepago, reportegrafica=:reportegrafica, reporteiva=:reporteiva, datosiva=:datosiva, reporteventa=:reporteventa, configuracion=:configuracion, addfolio=:addfolio, listafolio=:listafolio, editarfolio=:editarfolio, eliminarfolio=:eliminarfolio, addcomision=:addcomision, encabezados=:encabezados, confcorreo=:confcorreo, importar=:importar WHERE permiso_idusuario=:id;";
        $valores = array("facturas" => $u->getFacturas(),
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
            "importar" => $u->getImportar(),
            "id" => $u->getIdUsuario());
        $actualizado = $this->consultas->execute($consulta, $valores);
        echo $actualizado;
        return $actualizado;
    }

    
    public function checkPermisos($idusuario) {
        $datos = "";
        $check = $this->checkPermisosAux($idusuario);
        if ($check) {
            $datos = $this->getPermisosUsuario($idusuario);
        } else {
            $datos = $this->getInsertPermisos($idusuario);
        }
        return $datos;
    }

    private function checkPermisosAux($idusuario) {
        $existe = false;
        $get = $this->getPermisoById($idusuario);
        foreach ($get as $actual) {
            $existe = true;
        }
        return $existe;
    }

    private function getInsertPermisos($idusuario) {
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

    public function getPermisosUsuario($idusuario) {
        $usuario = $this->getPermisoById($idusuario);
        $datos = "";
        foreach ($usuario as $usuarioactual) {
            session_start();
            $usuariologin = $_SESSION[sha1("idusuario")];
            $idusuario = $usuarioactual['permiso_idusuario'];
            $nombreusuario = $usuarioactual['nombre'] . ' ' . $usuarioactual['apellido_paterno'] . ' ' . $usuarioactual['apellido_materno'];
            $facturas = $usuarioactual['facturas'];
            $crearfactura = $usuarioactual['crearfactura'];
            $editarfactura = $usuarioactual['editarfactura'];
            $eliminarfactura = $usuarioactual['eliminarfactura'];
            $listafactura = $usuarioactual['listafactura'];
            $pago = $usuarioactual['pago'];
            $crearpago = $usuarioactual['crearpago'];
            $editarpago = $usuarioactual['editarpago'];
            $eliminarpago = $usuarioactual['eliminarpago'];
            $listapago = $usuarioactual['listapago'];
            $nomina = $usuarioactual['nomina'];
            $listaempleado = $usuarioactual['listaempleado'];
            $crearempleado = $usuarioactual['crearempleado'];
            $editarempleado = $usuarioactual['editarempleado'];
            $eliminarempleado = $usuarioactual['eliminarempleado'];
            $listanomina = $usuarioactual['listanomina'];
            $crearnomina = $usuarioactual['crearnomina'];
            $editarnomina = $usuarioactual['editarnomina'];
            $eliminarnomina = $usuarioactual['eliminarnomina'];
            $cartaporte = $usuarioactual['cartaporte'];
            $listaubicacion = $usuarioactual['listaubicacion'];
            $crearubicacion = $usuarioactual['crearubicacion'];
            $editarubicacion = $usuarioactual['editarubicacion'];
            $eliminarubicacion = $usuarioactual['eliminarubicacion'];
            $listatransporte = $usuarioactual['listatransporte'];
            $creartransporte = $usuarioactual['creartransporte'];
            $editartransporte = $usuarioactual['editartransporte'];
            $eliminartransporte = $usuarioactual['eliminartransporte'];
            $listaremolque = $usuarioactual['listaremolque'];
            $crearremolque = $usuarioactual['crearremolque'];
            $editarremolque = $usuarioactual['editarremolque'];
            $eliminarremolque = $usuarioactual['eliminarremolque'];
            $listaoperador = $usuarioactual['listaoperador'];
            $crearoperador = $usuarioactual['crearoperador'];
            $editaroperador = $usuarioactual['editaroperador'];
            $eliminaroperador = $usuarioactual['eliminaroperador'];
            $listacarta = $usuarioactual['listacarta'];
            $crearcarta = $usuarioactual['crearcarta'];
            $editarcarta = $usuarioactual['editarcarta'];
            $eliminarcarta = $usuarioactual['eliminarcarta'];
            $cotizacion = $usuarioactual['cotizacion'];
            $crearcotizacion = $usuarioactual['crearcotizacion'];
            $editarcotizacion = $usuarioactual['editarcotizacion'];
            $eliminarcotizacion = $usuarioactual['eliminarcotizacion'];
            $listacotizacion = $usuarioactual['listacotizacion'];
            $anticipo = $usuarioactual['anticipo'];
            $cliente = $usuarioactual['cliente'];
            $crearcliente = $usuarioactual['crearcliente'];
            $editarcliente = $usuarioactual['editarcliente'];
            $eliminarcliente = $usuarioactual['eliminarcliente'];
            $listacliente = $usuarioactual['listacliente'];
            $comunicado = $usuarioactual['comunicado'];
            $crearcomunicado = $usuarioactual['crearcomunicado'];
            $editarcomunicado = $usuarioactual['editarcomunicado'];
            $eliminarcomunicado = $usuarioactual['eliminarcomunicado'];
            $listacomunicado = $usuarioactual['listacomunicado'];
            $producto = $usuarioactual['producto'];
            $crearproduto = $usuarioactual['crearproducto'];
            $editarproducto = $usuarioactual['editarproducto'];
            $eliminarproducto = $usuarioactual['eliminarproducto'];
            $listaproducto = $usuarioactual['listaproducto'];
            $proveedor = $usuarioactual['proveedor'];
            $crearproveedor = $usuarioactual['crearproveedor'];
            $editarproveedor = $usuarioactual['editarproveedor'];
            $eliminarproveedor = $usuarioactual['eliminarproveedor'];
            $listaproveedor = $usuarioactual['listaproveedor'];
            $impuesto = $usuarioactual['impuesto'];
            $crearimpuesto = $usuarioactual['crearimpuesto'];
            $editarimpuesto = $usuarioactual['editarimpuesto'];
            $eliminarimpuesto = $usuarioactual['eliminarimpuesto'];
            $listaimpuesto = $usuarioactual['listaimpuesto'];
            $datosfacturacion = $usuarioactual['datosfacturacion'];
            $creardatos = $usuarioactual['creardatos'];
            $editardatos = $usuarioactual['editardatos'];
            $listadatos = $usuarioactual['listadatos'];
            $contrato = $usuarioactual['contrato'];
            $crearcontrato = $usuarioactual['crearcontrato'];
            $editarcontrato = $usuarioactual['editarcontrato'];
            $eliminarcontrato = $usuarioactual['eliminarcontrato'];
            $listacontrato = $usuarioactual['listacontrato'];
            $usuarios = $usuarioactual['usuario'];
            $crearusuario = $usuarioactual['crearusuario'];
            $listausuario = $usuarioactual['listausuario'];
            $eliminarusuario = $usuarioactual['eliminarusuario'];
            $asignarpermiso = $usuarioactual['asignarpermiso'];
            $reporte = $usuarioactual['reporte'];
            $reportefactura = $usuarioactual['reportefactura'];
            $reportepago = $usuarioactual['reportepago'];
            $reportegrafica = $usuarioactual['reportegrafica'];
            $reporteiva = $usuarioactual['reporteiva'];
            $datosiva = $usuarioactual['datosiva'];
            $reporteventa = $usuarioactual['reporteventa'];
            $configuracion = $usuarioactual['configuracion'];
            $addfolio = $usuarioactual['addfolio'];
            $listafolio = $usuarioactual['listafolio'];
            $editarfolio = $usuarioactual['editarfolio'];
            $eliminarfolio = $usuarioactual['eliminarfolio'];
            $comision = $usuarioactual['addcomision'];
            $encabezados = $usuarioactual['encabezados'];
            $confcorreo = $usuarioactual['confcorreo'];
            $importar = $usuarioactual['importar'];

            $datos = "$idusuario</tr>$nombreusuario</tr>$facturas</tr>$crearfactura</tr>$editarfactura</tr>$eliminarfactura</tr>$listafactura</tr>$pago</tr>$crearpago</tr>$editarpago</tr>$eliminarpago</tr>$listapago</tr>$nomina</tr>$listaempleado</tr>$crearempleado</tr>$editarempleado</tr>$eliminarempleado</tr>$listanomina</tr>$crearnomina</tr>$editarnomina</tr>$eliminarnomina</tr>$cartaporte</tr>$listaubicacion</tr>$crearubicacion</tr>$editarubicacion</tr>$eliminarubicacion</tr>$listatransporte</tr>$creartransporte</tr>$editartransporte</tr>$eliminartransporte</tr>$listaremolque</tr>$crearremolque</tr>$editarremolque</tr>$eliminarremolque</tr>$listaoperador</tr>$crearoperador</tr>$editaroperador</tr>$eliminaroperador</tr>$listacarta</tr>$crearcarta</tr>$editarcarta</tr>$eliminarcarta</tr>$cotizacion</tr>$crearcotizacion</tr>$editarcotizacion</tr>$eliminarcotizacion</tr>$listacotizacion</tr>$anticipo</tr>$cliente</tr>$crearcliente</tr>$editarcliente</tr>$eliminarcliente</tr>$listacliente</tr>$comunicado</tr>$crearcomunicado</tr>$editarcomunicado</tr>$eliminarcomunicado</tr>$listacomunicado</tr>$producto</tr>$crearproduto</tr>$editarproducto</tr>$eliminarproducto</tr>$listaproducto</tr>$proveedor</tr>$crearproveedor</tr>$editarproveedor</tr>$eliminarproveedor</tr>$listaproveedor</tr>$impuesto</tr>$crearimpuesto</tr>$editarimpuesto</tr>$eliminarimpuesto</tr>$listaimpuesto</tr>$datosfacturacion</tr>$creardatos</tr>$editardatos</tr>$listadatos</tr>$contrato</tr>$crearcontrato</tr>$editarcontrato</tr>$eliminarcontrato</tr>$listacontrato</tr>$usuarios</tr>$crearusuario</tr>$listausuario</tr>$eliminarusuario</tr>$asignarpermiso</tr>$reporte</tr>$reportefactura</tr>$reportepago</tr>$reportegrafica</tr>$reporteiva</tr>$datosiva</tr>$reporteventa</tr>$configuracion</tr>$addfolio</tr>$listafolio</tr>$editarfolio</tr>$eliminarfolio</tr>$comision</tr>$encabezados</tr>$confcorreo</tr>$importar</tr>0</tr>$usuariologin";
            break;
        }
        return $datos;
    }
}
