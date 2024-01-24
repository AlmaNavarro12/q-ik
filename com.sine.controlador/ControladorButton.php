<?php

require_once '../com.sine.dao/Consultas.php';

class ControladorButton {

    function __construct() {
        
    }

    public function getPermisoById() {
        session_start();
        $idusuario = $_SESSION[sha1("idusuario")];
        $consultado = false;
        $consulta = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.imgperfil FROM usuariopermiso p inner join usuario u on (p.permiso_idusuario=u.idusuario) where permiso_idusuario=:idusuario;";
        $c = new Consultas();
        $valores = array("idusuario" => $idusuario);
        $consultado = $c->getResults($consulta, $valores);
        return $consultado;
    }

    public function loadButton($view) {
        $permisos = $this->getPermisoById();
        foreach ($permisos as $usuarioactual) {
            $crearfactura = $usuarioactual['crearfactura'];
            $crearpago = $usuarioactual['crearpago'];
            $crearempleado = $usuarioactual['crearempleado'];
            $crearnomina = $usuarioactual['crearnomina'];
            $crearubicacion = $usuarioactual['crearubicacion'];
            $creartransporte = $usuarioactual['creartransporte'];
            $crearremolque = $usuarioactual['crearremolque'];
            $crearoperador = $usuarioactual['crearoperador'];
            $crearcarta = $usuarioactual['crearcarta'];
            $crearcotizacion = $usuarioactual['crearcotizacion'];
            $crearcliente = $usuarioactual['crearcliente'];
            $crearcomunicado = $usuarioactual['crearcomunicado'];
            $crearproducto = $usuarioactual['crearproducto'];
            $crearproveedor = $usuarioactual['crearproveedor'];
            $crearimpuesto = $usuarioactual['crearimpuesto'];
            $creardatos = $usuarioactual['creardatos'];
            $crearcontrato = $usuarioactual['crearcontrato'];
            $crearusuario = $usuarioactual['crearusuario'];
            $listafolio = $usuarioactual['listafolio'];
            $crearfolio = $usuarioactual['addfolio'];
            $comision = $usuarioactual['addcomision'];
            $encabezados = $usuarioactual['encabezados'];
            $confcorreo = $usuarioactual['confcorreo'];
            $importar = $usuarioactual['importar'];
        }

        $btn = "";

        switch ($view) {
            case "factura":
                if ($crearfactura == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('factura');\" >Crear Factura <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case "pago":
                if ($crearpago == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('pago');\">Crear Pago <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'cotizacion':
                if ($crearcotizacion == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('cotizacion');\">Crear Cotizacion <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'cliente':
                if ($crearcliente == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('nuevocliente');\">Nuevo Cliente <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case "comunicado":
                if ($crearcomunicado == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('comunicado');\">Crear Comunicado <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'producto':
                if ($crearproducto == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('nuevoproducto');\">Crear producto <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case "proveedor":
                if ($crearproveedor == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('nuevoproveedor');\">Nuevo Proveedor <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'impuesto':
                if ($crearimpuesto == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('impuesto');\">Crear impuesto <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case "datos":
                if ($creardatos == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('datosempresa');\">Alta Datos <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'contrato':
                if ($crearcontrato == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('nuevocontrato');\">Crear Factura <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'usuario':
                if ($crearusuario == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('nuevousuario');\">Crear usuario <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'config':
                $btn = "$listafolio</tr>$comision</tr>$encabezados</tr>$confcorreo</tr>$importar";
                break;
            case 'folio':
                if ($crearfolio == '1') {
                    $btn = "<button class='button-agregar' onclick=\"loadViewConfig('folio');\">Crear Folio <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'empleado':
                if ($crearempleado == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('empleado');\">Registrar Empleado <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'nomina':
                if ($crearnomina == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('nomina');\">Crear Nomina <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'destino':
                if ($crearubicacion == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('direccion');\">Crear Ubicacion <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'transporte':
                if ($creartransporte == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('transporte');\">Crear Transporte <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'remolque':
                if ($crearremolque == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('remolque');\">Crear Remolque <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'operador':
                if ($crearoperador == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('operador');\">Crear Operador <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            case 'carta':
                if ($crearcarta == '1') {
                    $btn = "<button class='button-create' onclick=\"loadView('carta');\">Crear Carta <span class='lnr lnr-plus-circle icon-size'></span></button>";
                }
                break;
            default:
                echo "";
                break;
        }
        return $btn;
    }

}
