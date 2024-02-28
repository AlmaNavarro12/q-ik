<?php

require_once '../com.sine.dao/Consultas.php';

class ControladorButton{

    private $consultas;

    function __construct()
    {
        $this->consultas = new Consultas();
    }

    public function getPermisoById()
    {
        session_start();
        $idusuario = $_SESSION[sha1("idusuario")];
        $consultado = false;
        $consulta = "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.imgperfil FROM usuariopermiso p inner join usuario u on (p.permiso_idusuario=u.idusuario) where permiso_idusuario=:idusuario;";
        $valores = array("idusuario" => $idusuario);
        $consultado = $this->consultas->getResults($consulta, $valores);
        return $consultado;
    }

    public function loadButton($view)
    {
        $permisos = $this->getPermisoById();
        //
        $botones = [
            'factura' => ['crearfactura', 'factura', 'Crear Factura'],
            'pago' => ['crearpago', 'pago', 'Crear Pago'],
            'cotizacion' => ['crearcotizacion', 'cotizacion', 'Crear Cotizacion'],
            'cliente' => ['crearcliente', 'nuevocliente', 'Nuevo Cliente'],
            'comunicado' => ['crearcomunicado', 'comunicado', 'Crear Comunicado'],
            'producto' => ['crearproducto', 'nuevoproducto', 'Crear producto'],
            'proveedor' => ['crearproveedor', 'nuevoproveedor', 'Nuevo Proveedor'],
            'impuesto' => ['crearimpuesto', 'impuesto', 'Crear impuesto'],
            'datos' => ['creardatos', 'datosempresa', 'Alta Datos'],
            'contrato' => ['crearcontrato', 'nuevocontrato', 'Crear Factura'],
            'usuario' => ['crearusuario', 'nuevousuario', 'Crear usuario'],
            'config' => [
                ['addfolio', 'folio', 'Crear Folio'],
                ['addcomision', 'comisión', 'Agregar Comisión'],
                ['encabezados', 'encabezados', 'Configurar Encabezados'],
                ['confcorreo', 'correo', 'Configurar Correo'],
                ['importar', 'importar', 'Importar Datos']
            ],
            'folio' => ['addfolio', 'folio', 'Crear Folio'],
            'empleado' => ['crearempleado', 'empleado', 'Registrar Empleado'],
            'nomina' => ['crearnomina', 'nomina', 'Crear Nomina'],
            'destino' => ['crearubicacion', 'direccion', 'Crear Ubicacion'],
            'transporte' => ['creartransporte', 'transporte', 'Crear Transporte'],
            'remolque' => ['crearremolque', 'remolque', 'Crear Remolque'],
            'operador' => ['crearoperador', 'operador', 'Crear Operador'],
            'carta' => ['crearcarta', 'carta', 'Crear Carta'],
            'ventas' => ['crearventa', 'puntodeventa', 'Nueva venta'],
        ];
    
        $btn = "";
        if (array_key_exists($view, $botones)) {
            list($permiso, $accion, $texto) = $botones[$view];
            if ($view == 'config') {
                $configBotones = '';
                foreach ($botones['config'] as $configActual) {
                    list($configPermiso) = $configActual;
                    foreach ($permisos as $usuarioactual) {
                        if (isset($usuarioactual[$configPermiso])) {
                            $valorPermiso = $usuarioactual[$configPermiso];
                            $configBotones .= "$valorPermiso</tr>";
                            break;
                        }
                    }
                }
                $btn = $configBotones;            
            } else {
                if ($accion == 'folio') {
                    foreach ($permisos as $usuarioactual) {
                        if ($usuarioactual[$permiso] == '1') {
                            $btn = "<button class='button-create text-uppercase' onclick=\"loadViewConfig('$accion');\">$texto <span class='lnr lnr-plus-circle icon-size'></span></button>";
                            break;
                        }
                    }
                } else {
                    foreach ($permisos as $usuarioactual) {
                        if ($usuarioactual[$permiso] == '1') {
                            $btn = "<button class='button-create text-uppercase' onclick=\"loadView('$accion');\">$texto <span class='lnr lnr-plus-circle icon-size'></span></button>";
                            break;
                        }
                    }
                }
            }
        }
        return $btn;
    }
}