<?php

require_once 'com.sine.modelo/Session.php';

class Enrutador
{

    public function cargarVista($vista)
    {
        Session::start();
        if (isset($_SESSION[sha1("usuario")])) {
            switch ($vista) {
                case "paginicio":
                    include_once "../com.sine.vista/Inicio/inicio.php";
                    break;
                case "notificacion":
                    include_once "../com.sine.vista/Inicio/listanotificaciones.php";
                    break;
                case "comprar":
                    include_once "../com.sine.vista/Inicio/comprar.php";
                    break;
                case "iniciosession":
                    include_once "login/formlogin.html";
                    break;
                case "nuevousuario":
                    include_once '../com.sine.vista/usuario/formusuario.html';
                    break;
                case 'listasuarioaltas':
                    include_once '../com.sine.vista/usuario/listausuarioaltas.html';
                    break;
                case 'asignarpermisos':
                    include_once '../com.sine.vista/usuario/asignar-permisos.html';
                    break;
                case "categoria":
                    include_once '../com.sine.vista/categoria/formcategoria.html';
                    break;
                case 'listacategoria':
                    include_once 'com.sine.vista/categoria/listacategoria.html';
                    break;
                case "nuevoproducto":
                    include_once '../com.sine.vista/producto/formproducto.html';
                    break;
                case 'listaproductoaltas':
                    include_once '../com.sine.vista/producto/listaproductos.php';
                    break;
                case "valrfc":
                    include_once '../com.sine.vista/cliente/valrfc.html';
                    break;
                case "nuevocliente":
                    include_once '../com.sine.vista/cliente/formcliente.html';
                    break;
                case 'listaclientealtas':
                    include_once '../com.sine.vista/cliente/listaclientes.html';
                    break;
                case "comunicado":
                    include_once '../com.sine.vista/Comunicado/formcomunicado.html';
                    break;
                case 'listacomunicado':
                    include_once '../com.sine.vista/Comunicado/listacomunicado.php';
                    break;
                case 'reportefactura':
                    include_once '../com.sine.vista/reporte/reportefactura.html';
                    break;
                case 'reportepago':
                    include_once '../com.sine.vista/reporte/reportepago.html';
                    break;
                case 'reportegrafica':
                    include_once '../com.sine.vista/reporte/reportegrafica.php';
                    break;
                case 'reportesat':
                    include_once '../com.sine.vista/reporte/reportesat.php';
                    break;
                case 'datosiva':
                    include_once '../com.sine.vista/reporte/listaiva.html';
                    break;
                case 'reporteventas':
                    include_once '../com.sine.vista/reporte/reporteventas.html';
                    break;
                case 'cfdi':
                    include_once '../com.sine.vista/CFDI/formcfdi.html';
                    break;
                case 'datosempresa':
                    include_once '../com.sine.vista/DatosDeLaEmpresa/formdatosempresa.html';
                    break;
                case 'nuevocontrato':
                    include_once '../com.sine.vista/Contratos/formcontrato.php';
                    break;
                case 'registrarpago':
                    include_once '../com.sine.vista/Factura/registrarpago.php';
                    break;
                case 'pago':
                    include_once '../com.sine.vista/pago/formpago.html';
                    break;
                case 'listapago':
                    include_once '../com.sine.vista/pago/listapagos.php';
                    break;
                case 'factura':
                    include_once '../com.sine.vista/Factura/formfactura.php';
                    break;
                case 'cotizacion':
                    include_once '../com.sine.vista/Cotizacion/formCotizacion.php';
                    break;
                case 'instalacion':
                    include_once '../com.sine.vista/Instalacion/forminstalacion.php';
                    break;
                case 'listainstalacion':
                    include_once '../com.sine.vista/Instalacion/listainstalacion.php';
                    break;
                case 'listafactura':
                    include_once '../com.sine.vista/Factura/listafactura.php';
                    break;
                case 'listacotizacion':
                    include_once '../com.sine.vista/Cotizacion/listacotizacion.php';
                    break;
                case 'listacontrato':
                    include_once '../com.sine.vista/Contratistas/listaspermisionarios.php';
                    break;
                case 'listacontratos':
                    include_once '../com.sine.vista/Contratos/listacontratos.php';
                    break;
                case 'listaempresa':
                    include_once '../com.sine.vista/DatosDeLaEmpresa/listasdatosempresa.php';
                    break;
                case 'listacfdi':
                    include_once '../com.sine.vista/CFDI/listacfdi.php';
                    break;
                case 'nuevoproveedor':
                    include_once '../com.sine.vista/proveedor/formproveedor.html';
                    break;
                case 'listaproveedor':
                    include_once '../com.sine.vista/proveedor/listaproveedor.html';
                    break;
                case 'forminventario':
                    include_once '../com.sine.vista/producto/forminventario.html';
                    break;
                case 'listainventario':
                    include_once '../com.sine.vista/producto/listainventario.html';
                    break;
                case 'impuesto':
                    include_once '../com.sine.vista/Impuestos/formimpuestos.html';
                    break;
                case 'listaimpuesto':
                    include_once '../com.sine.vista/Impuestos/listaimpuestos.php';
                    break;
                case 'config':
                    include_once '../com.sine.vista/Configuracion/configuracion.php';
                    break;
                case 'encabezado':
                    include_once '../com.sine.vista/Configuracion/formencabezados.html';
                    break;
                case 'correo':
                    include_once '../com.sine.vista/Configuracion/formcorreo.html';
                    break;
                case 'folio':
                    include_once '../com.sine.vista/Configuracion/formfolios.html';
                    break;
                case 'listafolio':
                    include_once '../com.sine.vista/Configuracion/listafolios.php';
                    break;
                case 'comision':
                    include_once '../com.sine.vista/Configuracion/formcomisiones.html';
                    break;
                case 'tablas':
                    include_once '../com.sine.vista/Configuracion/formtablas.html';
                    break;
                case 'listafiel':
                    include_once '../com.sine.vista/DescargaMasiva/listafiel.php';
                    break;
                case 'nuevafiel':
                    include_once '../com.sine.vista/DescargaMasiva/formfiel.php';
                    break;
                case 'listadescsolicitud':
                    include_once '../com.sine.vista/DescargaMasiva/listasolicitud.php';
                    break;
                case 'descsolicitud':
                    include_once '../com.sine.vista/DescargaMasiva/formsolicitud.php';
                    break;
                case 'empleado':
                    include_once '../com.sine.vista/Nomina/formempleado.php';
                    break;
                case 'listaempleado':
                    include_once '../com.sine.vista/Nomina/listaempleado.php';
                    break;
                case 'nomina':
                    include_once '../com.sine.vista/Nomina/formnomina.php';
                    break;
                case 'listanomina':
                    include_once '../com.sine.vista/Nomina/listanomina.php';
                    break;
                case 'direccion':
                    include_once '../com.sine.vista/direccion/formdireccion.php';
                    break;
                case 'listadireccion':
                    include_once '../com.sine.vista/direccion/listadireccion.php';
                    break;
                case 'transporte':
                    include_once '../com.sine.vista/transporte/formtransporte.php';
                    break;
                case 'listatransporte':
                    include_once '../com.sine.vista/transporte/listatransporte.php';
                    break;
                case 'remolque':
                    include_once '../com.sine.vista/transporte/formremolque.php';
                    break;
                case 'listaremolque':
                    include_once '../com.sine.vista/transporte/listaremolque.php';
                    break;
                case 'operador':
                    include_once '../com.sine.vista/operador/formoperador.html';
                    break;
                case 'listaoperador':
                    include_once '../com.sine.vista/operador/listaoperador.html';
                    break;
                case 'carta':
                    include_once '../com.sine.vista/CartaPorte/formcarta.php';
                    break;
                case 'listacarta':
                    include_once '../com.sine.vista/CartaPorte/listacarta.php';
                    break;
                case 'puntodeventa':
                    include_once '../com.sine.vista/ventas/formventa.php';
                    break;
                case 'listaticket':
                    include_once '../com.sine.vista/ventas/listaticket.php';
                    break;
                case 'cortecaja':
                    include_once '../com.sine.vista/ventas/cortecaja.php';
                    break;
                case 'listacortes':
                    include_once '../com.sine.vista/ventas/listacortecaja.php';
                    break;
                default:
                    echo "El recurso solicitado no esta disponible<br/>Error 404: $vista Not Found";
                    break;
            }
        } else {
            include_once '../com.sine.vista/Configuracion/redireccion.html';
        }
    }
}
