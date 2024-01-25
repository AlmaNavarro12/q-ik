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
                            <li class='notification-link py-1'><a class='text-decoration-none text-secondary-emphasis' onclick='editarUsuario($id_usuario)'>Editar usuario <span class='text-muted fas fa-edit small'></span></a></li>";
            if ($permisos[0] == '1') {
                $datos .= "<li class='notification-link py-1'><a class='text-decoration-none text-secondary-emphasis' onclick='eliminarUsuario($id_usuario)'>Eliminar usuario <span class='text-muted fas fa-times'></span></a></li>";
            }

            if ($permisos[1] == '1') {
                $datos .= "<li class='notification-link py-1'><a class='text-decoration-none text-secondary-emphasis' onclick='asignarPermisos($id_usuario)'>Asignar permisos <span class='text-muted fas fa-sign-in-alt small'></span></a></li>";
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

    public function getDatosUsuario($idusuario) {
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

    private function getUsuarioById($idusuario) {
        $consultado = false;
        $consulta = "SELECT u.* FROM usuario AS u WHERE u.idusuario=:idusuario;";
        $c = new Consultas();
        $valores = array("idusuario" => $idusuario);
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    
    public function nuevoUsuario($u) {
        $existe = $this->validarExistenciaUsuario($u->getNombre() . "" . $u->getApellidoPaterno() . "" . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), 0);
        $insertado = false;
        if (!$existe) {
            $insertado = $this->insertarUsuario($u);
        }
        return $existe;
    }

    private function insertarUsuario($u) {
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
        $valores = array("id" => null,
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
            "fts" => '0');
        $consultas = new Consultas();
        $registrado = $consultas->execute($consulta, $valores);
        $idusuario = $this->getUserID($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo());
        $permisos = $this->insertarPermisos($idusuario);
        return $registrado;
    }

    private function insertarPermisos($idusuario) {
        $actualizado = false;
        $consultas = new Consultas();
        $consulta = "INSERT INTO `usuariopermiso` VALUES (:id, :idusuario, :facturas, :crearfactura, :editarfactura, :eliminarfactura, :listafactura, :pago, :crearpago, :editarpago, :eliminarpago, :listapago, :nomina, :listaempleado, :crearempleado, :editarempleado, :eliminarempleado, :listanomina, :crearnomina, :editarnomina, :eliminarnomina, :cartaporte, :listaubicacion, :crearubicacion, :editarubicacion, :eliminarubicacion, :listatransporte, :creartransporte, :editartransporte, :eliminartransporte, :listaremolque, :crearremolque, :editarremolque, :eliminarremolque, :listaoperador, :crearoperador, :editaroperador, :eliminaroperador, :listacarta, :crearcarta, :editarcarta, :eliminarcarta, :cotizacion, :crearcotizacion, :editarcotizacion, :eliminarcotizacion, :listacotizacion, :anticipo, :cliente, :crearcliente, :editarcliente, :eliminarcliente, :listacliente, :comunicado, :crearcomunicado, :editarcomunicado, :eliminarcomunicado, :listacomunicado, :producto, :crearproducto, :editarproducto, :eliminarproducto, :listaproducto, :proveedor, :crearproveedor, :editarproveedor, :eliminarproveedor, :listaproveedor, :impuesto, :crearimpuesto, :editarimpuesto, :eliminarimpuesto, :listaimpuesto, :datosfacturacion, :creardatos, :editardatos, :listadatos, :contrato, :crearcontrato, :editarcontrato, :eliminarcontrato, :listacontrato, :usuario, :crearusuario, :listausuario, :eliminarusuario, :asignarpermiso, :reporte, :reportefactura, :reportepago, :reportegrafica, :reporteiva, :datosiva, :reporteventa, :configuracion, :addfolio, :listafolio, :editarfolio, :eliminarfolio, :addcomision, :encabezados, :confcorreo, :importar);";
        $valores = array("id" => null,
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
            "importar" => '0');
        $actualizado = $consultas->execute($consulta, $valores);
        return $actualizado;
    }

    public function validarExistenciaUsuario($nombrecompleto, $correo, $usuario, $idusuario) {
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

    public function getUsuarioByNombreCompleto($nombrecompleto) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE concat(nombre,apellido_paterno,apellido_materno)=:nombrecompleto;";
        $consultas = new Consultas();
        $valores = array("nombrecompleto" => $nombrecompleto);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getUsuarioByNombreUsuario($nombreusuario) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE usuario=:usuario;";
        $consultas = new Consultas();
        $valores = array("usuario" => $nombreusuario);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function getUsuarioByCorreo($correo) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario WHERE email=:correo;";
        $consultas = new Consultas();
        $valores = array("correo" => $correo);
        $consultado = $consultas->getResults($consulta, $valores);
        return $consultado;
    }

    private function getFechaReg() {
        $fechareg = "";
        $user = $this->getFechaAccAux();
        foreach ($user as $actual) {
            $fechareg = $actual['fecharegistro'];
        }
        return $fechareg;
    }

    private function getTAcceso() {
        $tacceso = "";
        $datos = $this->getFechaAccAux();
        foreach ($datos as $actual) {
            $tacceso = $actual['acceso'] . "</tr>" . $actual['paquete'];
        }
        return $tacceso;
    }

    private function getFechaAccAux() {
        $consultado = false;
        $c = new Consultas();
        $consulta = "SELECT fecharegistro, acceso, paquete FROM usuario ORDER BY idusuario ASC limit 1;";
        $consultado = $c->getResults($consulta, null);
        return $consultado;
    }

    
    private function crearImg($nombre) {
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
        $im = @imagecreate(20, 20)or die("Cannot Initialize new GD image stream");
        $color_fondo = imagecolorallocate($im, 9, 9, 107);
        $color_texto = imagecolorallocate($im, 255, 255, 255);
        imagestring($im, 5, 6, 2.8, "$sn", $color_texto);
        $imgname = "$sn$hoy.png";
        imagepng($im, '../img/usuarios/' . $imgname);
        imagedestroy($im);
        return $imgname;
    }

    private function getUserIDAux($concat) {
        $consultado = false;
        $consulta = "SELECT * FROM usuario where concat(nombre,apellido_paterno,apellido_materno,usuario,email) = :concat;";
        $c = new Consultas();
        $val = array("concat" => $concat);
        $consultado = $c->getResults($consulta, $val);
        return $consultado;
    }

    private function getUserID($nombre, $usuario, $correo) {
        $concat = $nombre . $usuario . $correo;
        $iduser = "0";
        $user = $this->getUserIDAux($concat);
        foreach ($user as $actual) {
            $iduser = $actual['idusuario'];
        }
        return $iduser;
    }

    public function quitarUsuario($idusuario) {
        $eliminado = $this->eliminarUsuario($idusuario);
        return $eliminado;
    }

    private function eliminarUsuario($idusuario) {
        $eliminado = false;
        $consulta = "DELETE FROM `usuario` WHERE idusuario=:id;";
        $valores = array("id" => $idusuario);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        $permisos = $this->eliminarPermisos($idusuario);
        return $eliminado;
    }

    private function eliminarPermisos($idusuario) {
        $eliminado = false;
        $consulta = "DELETE FROM `usuariopermiso` WHERE permiso_idusuario=:id;";
        $valores = array("id" => $idusuario);
        $consultas = new Consultas();
        $eliminado = $consultas->execute($consulta, $valores);
        return $eliminado;
    }
}