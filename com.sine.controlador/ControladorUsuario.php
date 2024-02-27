<?php
require_once '../com.sine.dao/Consultas.php';

class ControladorUsuario
{

    private $consultas;

    private $permisos = [
        "facturas", "crearfactura", "editarfactura", "eliminarfactura", "listafactura", "timbrarfactura", //Timbrar
        "pago", "crearpago", "editarpago", "eliminarpago", "listapago", "timbrarpago", //Timbrar
        "nomina", "listaempleado", "crearempleado", "editarempleado", "eliminarempleado", "listanomina", "crearnomina", "editarnomina", "eliminarnomina", "timbrarnomina", //Timbrar
        "cartaporte", "listaubicacion", "crearubicacion", "editarubicacion", "eliminarubicacion", "listatransporte", "creartransporte", "editartransporte", "eliminartransporte", "listaremolque", "crearremolque", "editarremolque", "eliminarremolque", "listaoperador", "crearoperador", "editaroperador", "eliminaroperador", "listacarta", "crearcarta", "editarcarta", "eliminarcarta", "timbrarcarta", //Timbrar
        "cotizacion", "crearcotizacion", "editarcotizacion", "eliminarcotizacion", "listacotizacion","anticipo", 
        "cliente", "crearcliente", "editarcliente", "eliminarcliente", "listacliente",
        "comunicado", "crearcomunicado", "editarcomunicado", "eliminarcomunicado", "listacomunicado", "exportarventa",
        "producto", "crearproducto", "editarproducto", "eliminarproducto", "listaproducto",
        "proveedor", "crearproveedor", "editarproveedor", "eliminarproveedor", "listaproveedor",
        "impuesto", "crearimpuesto", "editarimpuesto", "eliminarimpuesto", "listaimpuesto",
        "datosfacturacion", "creardatos", "editardatos", "listadatos", "eliminardatos", "descargardatos", //Eliminar, descargar archivos
        "contrato", "crearcontrato", "editarcontrato", "eliminarcontrato", "listacontrato",
        "usuario", "crearusuario", "listausuario", "eliminarusuario", "asignarpermiso",
        "reporte", "reportefactura", "reportepago", "reportegrafica", "reporteiva", "datosiva", "reporteventa",
        "configuracion", "addfolio", "listafolio", "editarfolio", "eliminarfolio", "addcomision", "encabezados", "confcorreo", "importar", 
        "ventas", "crearventa", "cancelarventa"
    ];

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
        <tr class='align-middle'>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellido paterno</th>
            <th>Apellido materno</th>
            <th class='col-auto text-center'>Correo</th>
            <th class='col-auto text-center'>Celular</th>
            <th class='col-auto text-center'>Teléfono</th>
            <th class='col-auto text-center'>Opción</th>
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
                <td class='text-center'>$correo</td>
                <td class='text-center'>$celular</td>
                <td class='text-center'>$telefono</td>
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
                    $datos .= "<li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis text-nowrap' style='overflow: hidden; text-overflow: ellipsis;' onclick='asignarPermisos($id_usuario)'>Asignar permisos <span class='text-muted fas fa-sign-in-alt small'></span></a></li>";
                }
                $datos .= "</ul>
                    </div>
                </td> 
            </tr>";
            }
        }

        $function = "buscarUsuario";
        $datos .= "</tbody><tfoot><tr><th colspan='3' class='align-top'>Mostrando $inicios al $finales de $numrows registros</th>";
        $datos .= "<th colspan='5'>" . paginate($page, $total_pages, $adjacents, $function) . "</th></tr></tfoot>";

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
        $existe = $this->validarExistenciaUsuario($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), $u->getIdUsuario());
        $guardado = false;
        if (!$existe) {
            $guardado = $this->guardarUsuario($u);
        }
        return $guardado;
    }

    private function guardarUsuario($u)
    {
        $existe = $this->validarExistenciaUsuario($u->getNombre() . $u->getApellidoPaterno() . $u->getApellidoMaterno(), $u->getUsuario(), $u->getCorreo(), $u->getIdUsuario());
        $guardado = false;
        if (!$existe) {
            $pass = "";
            if ($u->getChpass() == '1') {
                $pass = " password=sha1(:contrasena),";
            }

            $img = $u->getImg();

            if ($u->getIdUsuario() == 0) {
                $img = ($img == '') ? $this->crearImg($u->getNombre()) : $img;
                if ($img != '') {
                    rename('../temporal/usuarios/' . $img, '../img/usuarios/' . $img);
                }
            } else {
                if ($img == '') {
                    $img = $u->getImgactualizar();
                } else if ($img != $u->getNameImg()) {
                    $nuevaRuta = '../img/usuarios/' . $img;
                    $viejaRuta = '../img/usuarios/' . $u->getNameImg();
                    rename('../temporal/usuarios/' . $img, $nuevaRuta);

                    if (file_exists($viejaRuta)) {
                        unlink($viejaRuta);
                    }
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

    private function crearImg($nombre)
    {
        $sn = substr($nombre, 0, 1);
        $hoy = date('Ymd\TH.i.s');

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

    private function insertarPermisos($idusuario)
    {
        $actualizado = false;

        $valores = ["id" => null, "idusuario" => $idusuario];

        foreach ($this->permisos as $permiso) {
            $valores[$permiso] = '0';
        }

        $consulta = "INSERT INTO `usuariopermiso` VALUES (:id, :idusuario,";
        $consulta .= implode(", :", $this->permisos) . ");";
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
        $consulta = "SELECT fecharegistro, acceso, paquete FROM usuario ORDER BY idusuario ASC limit 1;";
        $consultado = $this->consultas->getResults($consulta, null);
        return $consultado;
    }

    private function getUserIDAux($concat)
    {
        $consultado = false;
        $consulta = "SELECT * FROM usuario where concat(nombre,apellido_paterno,apellido_materno,usuario,email) = :concat;";
        $val = array("concat" => $concat);
        $consultado = $this->consultas->getResults($consulta, $val);
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
        $usuarioArray = $this->getUsuarioById($idusuario);

        if ($usuarioArray) {
            $usuario = $usuarioArray[0];
            $imgperfil = $usuario['imgperfil'];

            $eliminado = $this->eliminarUsuario($idusuario);

            if ($eliminado) {
                $rutaArchivo = '../img/usuarios/';
                $rutaCompleta = $rutaArchivo . $imgperfil;

                if (file_exists($rutaCompleta)) {
                    unlink($rutaCompleta);
                }
            } else {
                return $eliminado;
            }
        }
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
                rename('../temporal/usuarios/' . $img, '../img/usuarios/' . $img);
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

        $mapeoGet = array(
            "editarcotizacion" => "getEditarcot",
            "eliminarcotizacion" => "getEliminarcot",
            "crearnomina" => "getCrearnomina",
            "usuario" => "getUsuarios",
            "reporteventa" => "getReporteventas",
            "editarfolio" => "getEditfolio",
            "asignarpermiso" => "getAsignarpermisos",
        );

        $consulta = "INSERT INTO usuariopermiso VALUES (NULL, :idusuario, ";
        foreach ($this->permisos as $columna) {
            $getter = isset($mapeoGet[$columna]) ? $mapeoGet[$columna] : "get" . $columna;
            $consulta .= ":$columna, ";
            $valores[":$columna"] = method_exists($u, $getter) ? $u->$getter() : null;
        }
        $consulta .= "NULL);";

        $valores[":idusuario"] = $u->getIdUsuario();
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

        $consulta = "UPDATE usuariopermiso SET ";
        foreach ($this->permisos as $columna) {
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

            $datos = "$idusuario</tr>$nombreusuario</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>0</tr>1</tr>$usuariologin";
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

            $datos = "$idusuario</tr>$nombreusuario";
            foreach ($this->permisos as $permiso) {
                $valorPermiso = isset($usuarioactual[$permiso]) ? $usuarioactual[$permiso] : '';
                $datos .= "</tr>$valorPermiso";
            }
            $datos .= "</tr>0</tr>$usuariologin";
            break;
        }

        return $datos;
    }

    public function eliminarImgTmp($imgtmp){
        $viejaruta = '../temporal/usuarios/' . $imgtmp;
        if ($imgtmp != '') {
            if (file_exists($viejaruta)) {
                unlink($viejaruta);
            }
        }
        return true;
    }
}