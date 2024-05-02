<?php

class Permiso {

    private $idUsuario;
    private $facturas;
    private $crearfactura;
    private $editarfactura;
    private $eliminarfactura;
    private $listafactura;
    private $pago;
    private $crearpago;
    private $editarpago;
    private $eliminarpago;
    private $listapago;
    private $nomina;
    private $listaempleado;
    private $crearempleado;
    private $editarempleado;
    private $eliminarempleado;
    private $listanomina;
    private $crearnomina;
    private $editarnomina;
    private $eliminarnomina;
    private $cartaporte;
    private $listaubicacion;
    private $crearubicacion;
    private $editarubicacion;
    private $eliminarubicacion;
    private $listatransporte;
    private $creartransporte;
    private $editartransporte;
    private $eliminartransporte;
    private $listaremolque;
    private $crearremolque;
    private $editarremolque;
    private $eliminarremolque;
    private $listaoperador;
    private $crearoperador;
    private $editaroperador;
    private $eliminaroperador;
    private $listacarta;
    private $crearcarta;
    private $editarcarta;
    private $eliminarcarta;
    private $cotizacion;
    private $crearcotizacion;
    private $editarcot;
    private $eliminarcot;
    private $listacotizacion;
    private $anticipo;
    private $cliente;
    private $crearcliente;
    private $editarcliente;
    private $eliminarcliente;
    private $listacliente;
    private $comunicado;
    private $crearcomunicado;
    private $editarcomunicado;
    private $eliminarcomunicado;
    private $listacomunicado;
    private $producto;
    private $crearproducto;
    private $editarproducto;
    private $eliminarproducto;
    private $listaproducto;
    private $proveedor;
    private $crearproveedor;
    private $editarproveedor;
    private $eliminarproveedor;
    private $listaproveedor;
    private $impuesto;
    private $crearimpuesto;
    private $editarimpuesto;
    private $eliminarimpuesto;
    private $listaimpuesto;
    private $datosfacturacion;
    private $creardatos;
    private $editardatos;
    private $listadatos;
    private $contrato;
    private $crearcontrato;
    private $editarcontrato;
    private $eliminarcontrato;
    private $listacontrato;
    private $usuarios;
    private $crearusuario;
    private $eliminarusuario;
    private $asignarpermisos;
    private $listausuario;
    private $reporte;
    private $reportefactura;
    private $reportepago;
    private $reportegrafica;
    private $reporteiva;
    private $datosiva;
    private $reporteventas;
    private $configuracion;
    private $addfolio;
    private $listafolio;
    private $editfolio;
    private $eliminarfolio;
    private $addcomision;
    private $encabezados;
    private $confcorreo;
    private $accion;
    private $importar;
    private $ventas;
    private $crearventa;
    private $cancelarventa;
    private $exportarventa;
    private $timbrarfactura;
    private $timbrarpago;
    private $timbrarnomina;
    private $timbrarcarta;
    private $eliminardatos;
    private $descargardatos;
    private $listaventa;
    private $registrarentrada;
    private $registrarsalida;
    private $cortedecaja;
    private $reporteinventario;
    private $reportepuntoventa;
    private $exportarfactura;


    //CAMBIOS EN RELACION A GPS 22 de abril
    private $instalaciongps;
    private $creargps;
    private $editargps;
    private $eliminargps;
    private $listagps;
    private $crearinstalacion;
    private $editarinicio;
    private $editarpasos;
    private $cancelarinstalacion;
    private $eliminarinstalacion; 
    private $listainstalacion; 
    private $reportecorte; 

    function __construct() {
        
    }
    
    //GETTERS EN GPS
    function getInstalacionGps() {
        return $this->instalaciongps;
    }
    function getCrearGps() {
        return $this->creargps;
    }

    function getEditarGps() {
        return $this->editargps;
    }

    function getEliminarGps() {
        return $this->eliminargps;
    }

    function getListaGps() {
        return $this->listagps;
    }

    function getCrearInstalacion() {
        return $this->crearinstalacion;
    }

    function getEditarInicio() {
        return $this->editarinicio;
    }

    function getEditarPasos() {
        return $this->editarpasos;
    }

    function getCancelarInstalacion() {
        return $this->cancelarinstalacion;
    }

    function getEliminarInstalacion() {
        return $this->eliminarinstalacion;
    }

    function getListaInstalacion() {
        return $this->listainstalacion;
    }

    //SETTTER EN GPS
    function setInstalacionGps($instalaciongps) {
        $this->instalaciongps = $instalaciongps;
    }

    function setCrearGps($creargps) {
        $this->creargps = $creargps;
    }

    function setEditarGps($editargps) {
        $this->editargps = $editargps;
    }

    function setEliminarGps($eliminargps) {
        $this->eliminargps = $eliminargps;
    }

    function setListaGps($listagps) {
        $this->listagps = $listagps;
    }

    function setCrearInstalacion($crearinstalacion) {
        $this->crearinstalacion = $crearinstalacion;
    }

    function setEditarInicio($editarinicio) {
        $this->editarinicio = $editarinicio;
    }

    function setEditarPasos($editarpasos) {
        $this->editarpasos = $editarpasos;
    }

    function setCancelarInstalacion($cancelarinstalacion) {
        $this->cancelarinstalacion = $cancelarinstalacion;
    }

    function setEliminarInstalacion($eliminarinstalacion) {
        $this->eliminarinstalacion = $eliminarinstalacion;
    }

    function setListaInstalacion($listainstalacion) {
        $this->listainstalacion = $listainstalacion;
    }

    //FIN DE CAMBIOS
    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getFacturas() {
        return $this->facturas;
    }

    function getCrearfactura() {
        return $this->crearfactura;
    }

    function getEditarfactura() {
        return $this->editarfactura;
    }

    function getEliminarfactura() {
        return $this->eliminarfactura;
    }

    function getListafactura() {
        return $this->listafactura;
    }

    function getPago() {
        return $this->pago;
    }

    function getCrearpago() {
        return $this->crearpago;
    }

    function getEditarpago() {
        return $this->editarpago;
    }

    function getEliminarpago() {
        return $this->eliminarpago;
    }

    function getListapago() {
        return $this->listapago;
    }

    function getNomina() {
        return $this->nomina;
    }

    function getListaempleado() {
        return $this->listaempleado;
    }

    function getCrearempleado() {
        return $this->crearempleado;
    }

    function getTimbrarFactura() {
        return $this->timbrarfactura;
    }

    function getTimbrarPago() {
        return $this->timbrarpago;
    }

    function getTimbrarNomina() {
        return $this->timbrarnomina;
    }

    function getTimbrarCarta() {
        return $this->timbrarcarta;
    }

    function getListaVenta() {
        return $this->listaventa;
    }

    function getEliminarDatos() {
        return $this->eliminardatos;
    }

    function getDescargarDatos() {
        return $this->descargardatos;
    }

    function getEditarempleado() {
        return $this->editarempleado;
    }

    function getEliminarempleado() {
        return $this->eliminarempleado;
    }

    function getListanomina() {
        return $this->listanomina;
    }

    function getCrearnomina() {
        return $this->crearnomina;
    }

    function getEditarnomina() {
        return $this->editarnomina;
    }

    function getEliminarnomina() {
        return $this->eliminarnomina;
    }

    function getCartaporte() {
        return $this->cartaporte;
    }

    function getListaubicacion() {
        return $this->listaubicacion;
    }

    function getCrearubicacion() {
        return $this->crearubicacion;
    }

    function getEditarubicacion() {
        return $this->editarubicacion;
    }

    function getEliminarubicacion() {
        return $this->eliminarubicacion;
    }

    function getListatransporte() {
        return $this->listatransporte;
    }

    function getCreartransporte() {
        return $this->creartransporte;
    }

    function getEditartransporte() {
        return $this->editartransporte;
    }

    function getEliminartransporte() {
        return $this->eliminartransporte;
    }

    function getListaremolque() {
        return $this->listaremolque;
    }

    function getCrearremolque() {
        return $this->crearremolque;
    }

    function getEditarremolque() {
        return $this->editarremolque;
    }

    function getEliminarremolque() {
        return $this->eliminarremolque;
    }

    function getListaoperador() {
        return $this->listaoperador;
    }

    function getCrearoperador() {
        return $this->crearoperador;
    }

    function getRegistrarEntrada() {
        return $this->registrarentrada;
    }

    function getRegistrarSalida() {
        return $this->registrarsalida;
    }

    function getCortedeCaja() {
        return $this->cortedecaja;
    }

    function getEditaroperador() {
        return $this->editaroperador;
    }

    function getEliminaroperador() {
        return $this->eliminaroperador;
    }

    function getListacarta() {
        return $this->listacarta;
    }

    function getExportarFactura() {
        return $this->exportarfactura;
    }


    function getCrearcarta() {
        return $this->crearcarta;
    }

    function getEditarcarta() {
        return $this->editarcarta;
    }

    function getEliminarcarta() {
        return $this->eliminarcarta;
    }

    function getCotizacion() {
        return $this->cotizacion;
    }

    function getCrearcotizacion() {
        return $this->crearcotizacion;
    }

    function getEditarcot() {
        return $this->editarcot;
    }

    function getEliminarcot() {
        return $this->eliminarcot;
    }

    function getListacotizacion() {
        return $this->listacotizacion;
    }

    function getAnticipo() {
        return $this->anticipo;
    }

    function getCliente() {
        return $this->cliente;
    }

    function getCrearcliente() {
        return $this->crearcliente;
    }

    function getEditarcliente() {
        return $this->editarcliente;
    }

    function getEliminarcliente() {
        return $this->eliminarcliente;
    }

    function getListacliente() {
        return $this->listacliente;
    }

    function getComunicado() {
        return $this->comunicado;
    }

    function getCrearcomunicado() {
        return $this->crearcomunicado;
    }

    function getEditarcomunicado() {
        return $this->editarcomunicado;
    }

    function getEliminarcomunicado() {
        return $this->eliminarcomunicado;
    }

    function getListacomunicado() {
        return $this->listacomunicado;
    }

    function getProducto() {
        return $this->producto;
    }

    function getCrearproducto() {
        return $this->crearproducto;
    }

    function getEditarproducto() {
        return $this->editarproducto;
    }

    function getEliminarproducto() {
        return $this->eliminarproducto;
    }

    function getListaproducto() {
        return $this->listaproducto;
    }

    function getProveedor() {
        return $this->proveedor;
    }

    function getCrearproveedor() {
        return $this->crearproveedor;
    }

    function getEditarproveedor() {
        return $this->editarproveedor;
    }

    function getEliminarproveedor() {
        return $this->eliminarproveedor;
    }

    function getListaproveedor() {
        return $this->listaproveedor;
    }

    function getImpuesto() {
        return $this->impuesto;
    }

    function getCrearimpuesto() {
        return $this->crearimpuesto;
    }

    function getEditarimpuesto() {
        return $this->editarimpuesto;
    }

    function getEliminarimpuesto() {
        return $this->eliminarimpuesto;
    }

    function getListaimpuesto() {
        return $this->listaimpuesto;
    }

    function getDatosfacturacion() {
        return $this->datosfacturacion;
    }

    function getCreardatos() {
        return $this->creardatos;
    }

    function getEditardatos() {
        return $this->editardatos;
    }

    function getListadatos() {
        return $this->listadatos;
    }

    function getContrato() {
        return $this->contrato;
    }

    function getCrearcontrato() {
        return $this->crearcontrato;
    }

    function getEditarcontrato() {
        return $this->editarcontrato;
    }

    function getEliminarcontrato() {
        return $this->eliminarcontrato;
    }

    function getListacontrato() {
        return $this->listacontrato;
    }

    function getUsuarios() {
        return $this->usuarios;
    }

    function getCrearusuario() {
        return $this->crearusuario;
    }

    function getEliminarusuario() {
        return $this->eliminarusuario;
    }

    function getAsignarpermisos() {
        return $this->asignarpermisos;
    }

    function getListausuario() {
        return $this->listausuario;
    }

    function getReporte() {
        return $this->reporte;
    }

    function getReportefactura() {
        return $this->reportefactura;
    }

    function getReportepago() {
        return $this->reportepago;
    }

    function getReportegrafica() {
        return $this->reportegrafica;
    }

    function getReporteiva() {
        return $this->reporteiva;
    }

    function getDatosiva() {
        return $this->datosiva;
    }

    function getReporteventas() {
        return $this->reporteventas;
    }

    function getReporteInventario() {
        return $this->reporteinventario;
    }

    function getReportePuntoVenta() {
        return $this->reportepuntoventa;
    }

    function getReporteCorte() {
        return $this->creartransporte;
    }

    function getConfiguracion() {
        return $this->configuracion;
    }

    function getAddfolio() {
        return $this->addfolio;
    }

    function getListafolio() {
        return $this->listafolio;
    }

    function getEditfolio() {
        return $this->editfolio;
    }

    function getEliminarfolio() {
        return $this->eliminarfolio;
    }

    function getAddcomision() {
        return $this->addcomision;
    }

    function getEncabezados() {
        return $this->encabezados;
    }

    function getConfcorreo() {
        return $this->confcorreo;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setFacturas($facturas) {
        $this->facturas = $facturas;
    }

    function setCrearfactura($crearfactura) {
        $this->crearfactura = $crearfactura;
    }

    function setEditarfactura($editarfactura) {
        $this->editarfactura = $editarfactura;
    }

    function setEliminarfactura($eliminarfactura) {
        $this->eliminarfactura = $eliminarfactura;
    }

    function setListafactura($listafactura) {
        $this->listafactura = $listafactura;
    }

    function setPago($pago) {
        $this->pago = $pago;
    }

    function setCrearPago($crearpago) {
        $this->crearpago = $crearpago;
    }

    function setTimbrarFactura($timbrarfactura) {
        $this->timbrarfactura = $timbrarfactura;
    }

    function setTimbrarNomina($timbrarnomina) {
        $this->timbrarnomina = $timbrarnomina;
    }
    
    function setTimbrarPago($timbrarpago) {
        $this->timbrarpago = $timbrarpago;
    }
    
    function setTimbrarCarta($timbrarcarta) {
        $this->timbrarcarta = $timbrarcarta;
    }
    
    function setEliminarDatos($eliminardatos) {
        $this->eliminardatos = $eliminardatos;
    }

    function setExportarFactura($exportarfactura) {
        $this->exportarfactura = $exportarfactura;
    }

    function setDescargarDatos($descargardatos) {
        $this->descargardatos = $descargardatos;
    }

    function setEditarpago($editarpago) {
        $this->editarpago = $editarpago;
    }

    function setEliminarpago($eliminarpago) {
        $this->eliminarpago = $eliminarpago;
    }

    function setListapago($listapago) {
        $this->listapago = $listapago;
    }

    function setNomina($nomina) {
        $this->nomina = $nomina;
    }

    function setListaempleado($listaempleado) {
        $this->listaempleado = $listaempleado;
    }

    function setCrearempleado($crearempleado) {
        $this->crearempleado = $crearempleado;
    }

    function setEditarempleado($editarempleado) {
        $this->editarempleado = $editarempleado;
    }

    function setEliminarempleado($eliminarempleado) {
        $this->eliminarempleado = $eliminarempleado;
    }

    function setListanomina($listanomina) {
        $this->listanomina = $listanomina;
    }

    function setCrearnomina($crearnomina) {
        $this->crearnomina = $crearnomina;
    }

    function setEditarnomina($editarnomina) {
        $this->editarnomina = $editarnomina;
    }

    function setEliminarnomina($eliminarnomina) {
        $this->eliminarnomina = $eliminarnomina;
    }

    function setReporteInventario($reporteinventario) {
        $this->reporteinventario = $reporteinventario;
    }

    function setReportePuntoVenta($reportepuntoventa) {
        $this->reportepuntoventa = $reportepuntoventa;
    }

    function setReporteCorte($reportecorte) {
        $this->reportecorte = $reportecorte;
    }

    function setCartaporte($cartaporte) {
        $this->cartaporte = $cartaporte;
    }

    function setListaubicacion($listaubicacion) {
        $this->listaubicacion = $listaubicacion;
    }

    function setCrearubicacion($crearubicacion) {
        $this->crearubicacion = $crearubicacion;
    }

    function setEditarubicacion($editarubicacion) {
        $this->editarubicacion = $editarubicacion;
    }

    function setEliminarubicacion($eliminarubicacion) {
        $this->eliminarubicacion = $eliminarubicacion;
    }

    function setListatransporte($listatransporte) {
        $this->listatransporte = $listatransporte;
    }

    function setCreartransporte($creartransporte) {
        $this->creartransporte = $creartransporte;
    }

    function setEditartransporte($editartransporte) {
        $this->editartransporte = $editartransporte;
    }

    function setEliminartransporte($eliminartransporte) {
        $this->eliminartransporte = $eliminartransporte;
    }

    function setListaremolque($listaremolque) {
        $this->listaremolque = $listaremolque;
    }

    function setCrearremolque($crearremolque) {
        $this->crearremolque = $crearremolque;
    }

    function setEditarremolque($editarremolque) {
        $this->editarremolque = $editarremolque;
    }

    function setEliminarremolque($eliminarremolque) {
        $this->eliminarremolque = $eliminarremolque;
    }

    function setListaoperador($listaoperador) {
        $this->listaoperador = $listaoperador;
    }

    function setCrearoperador($crearoperador) {
        $this->crearoperador = $crearoperador;
    }

    function setEditaroperador($editaroperador) {
        $this->editaroperador = $editaroperador;
    }

    function setEliminaroperador($eliminaroperador) {
        $this->eliminaroperador = $eliminaroperador;
    }

    function setListacarta($listacarta) {
        $this->listacarta = $listacarta;
    }

    function setCrearcarta($crearcarta) {
        $this->crearcarta = $crearcarta;
    }

    function setListaVenta($listaventa) {
        $this->listaventa = $listaventa;
    }

    function setEditarcarta($editarcarta) {
        $this->editarcarta = $editarcarta;
    }

    function setEliminarcarta($eliminarcarta) {
        $this->eliminarcarta = $eliminarcarta;
    }

    function setCotizacion($cotizacion) {
        $this->cotizacion = $cotizacion;
    }

    function setCrearcotizacion($crearcotizacion) {
        $this->crearcotizacion = $crearcotizacion;
    }

    function setEditarcot($editarcot) {
        $this->editarcot = $editarcot;
    }

    function setEliminarcot($eliminarcot) {
        $this->eliminarcot = $eliminarcot;
    }

    function setListacotizacion($listacotizacion) {
        $this->listacotizacion = $listacotizacion;
    }

    function setAnticipo($anticipo) {
        $this->anticipo = $anticipo;
    }

    function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    function setCrearcliente($crearcliente) {
        $this->crearcliente = $crearcliente;
    }

    function setEditarcliente($editarcliente) {
        $this->editarcliente = $editarcliente;
    }

    function setEliminarcliente($eliminarcliente) {
        $this->eliminarcliente = $eliminarcliente;
    }

    function setListacliente($listacliente) {
        $this->listacliente = $listacliente;
    }

    function setComunicado($comunicado) {
        $this->comunicado = $comunicado;
    }

    function setCrearcomunicado($crearcomunicado) {
        $this->crearcomunicado = $crearcomunicado;
    }

    function setEditarcomunicado($editarcomunicado) {
        $this->editarcomunicado = $editarcomunicado;
    }

    function setEliminarcomunicado($eliminarcomunicado) {
        $this->eliminarcomunicado = $eliminarcomunicado;
    }

    function setListacomunicado($listacomunicado) {
        $this->listacomunicado = $listacomunicado;
    }

    function setProducto($producto) {
        $this->producto = $producto;
    }

    function setCrearproducto($crearproducto) {
        $this->crearproducto = $crearproducto;
    }

    function setEditarproducto($editarproducto) {
        $this->editarproducto = $editarproducto;
    }

    function setEliminarproducto($eliminarproducto) {
        $this->eliminarproducto = $eliminarproducto;
    }

    function setListaproducto($listaproducto) {
        $this->listaproducto = $listaproducto;
    }

    function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
    }

    function setCrearproveedor($crearproveedor) {
        $this->crearproveedor = $crearproveedor;
    }

    function setEditarproveedor($editarproveedor) {
        $this->editarproveedor = $editarproveedor;
    }

    function setEliminarproveedor($eliminarproveedor) {
        $this->eliminarproveedor = $eliminarproveedor;
    }

    function setListaproveedor($listaproveedor) {
        $this->listaproveedor = $listaproveedor;
    }

    function setImpuesto($impuesto) {
        $this->impuesto = $impuesto;
    }

    function setCrearimpuesto($crearimpuesto) {
        $this->crearimpuesto = $crearimpuesto;
    }

    function setEditarimpuesto($editarimpuesto) {
        $this->editarimpuesto = $editarimpuesto;
    }

    function setEliminarimpuesto($eliminarimpuesto) {
        $this->eliminarimpuesto = $eliminarimpuesto;
    }

    function setListaimpuesto($listaimpuesto) {
        $this->listaimpuesto = $listaimpuesto;
    }

    function setDatosfacturacion($datosfacturacion) {
        $this->datosfacturacion = $datosfacturacion;
    }

    function setCreardatos($creardatos) {
        $this->creardatos = $creardatos;
    }

    function setEditardatos($editardatos) {
        $this->editardatos = $editardatos;
    }

    function setListadatos($listadatos) {
        $this->listadatos = $listadatos;
    }

    function setContrato($contrato) {
        $this->contrato = $contrato;
    }

    function setCrearcontrato($crearcontrato) {
        $this->crearcontrato = $crearcontrato;
    }

    function setEditarcontrato($editarcontrato) {
        $this->editarcontrato = $editarcontrato;
    }

    function setEliminarcontrato($eliminarcontrato) {
        $this->eliminarcontrato = $eliminarcontrato;
    }

    function setListacontrato($listacontrato) {
        $this->listacontrato = $listacontrato;
    }

    function setUsuarios($usuarios) {
        $this->usuarios = $usuarios;
    }

    function setCrearusuario($crearusuario) {
        $this->crearusuario = $crearusuario;
    }

    function setEliminarusuario($eliminarusuario) {
        $this->eliminarusuario = $eliminarusuario;
    }

    function setAsignarpermisos($asignarpermisos) {
        $this->asignarpermisos = $asignarpermisos;
    }

    function setListausuario($listausuario) {
        $this->listausuario = $listausuario;
    }

    function setReporte($reporte) {
        $this->reporte = $reporte;
    }

    function setReportefactura($reportefactura) {
        $this->reportefactura = $reportefactura;
    }

    function setReportepago($reportepago) {
        $this->reportepago = $reportepago;
    }

    function setReportegrafica($reportegrafica) {
        $this->reportegrafica = $reportegrafica;
    }

    function setReporteiva($reporteiva) {
        $this->reporteiva = $reporteiva;
    }

    function setDatosiva($datosiva) {
        $this->datosiva = $datosiva;
    }

    function setReporteventas($reporteventas) {
        $this->reporteventas = $reporteventas;
    }

    function setConfiguracion($configuracion) {
        $this->configuracion = $configuracion;
    }

    function setAddfolio($addfolio) {
        $this->addfolio = $addfolio;
    }

    function setListafolio($listafolio) {
        $this->listafolio = $listafolio;
    }

    function setEditfolio($editfolio) {
        $this->editfolio = $editfolio;
    }

    function setEliminarfolio($eliminarfolio) {
        $this->eliminarfolio = $eliminarfolio;
    }

    function setAddcomision($addcomision) {
        $this->addcomision = $addcomision;
    }

    function setEncabezados($encabezados) {
        $this->encabezados = $encabezados;
    }

    function setConfcorreo($confcorreo) {
        $this->confcorreo = $confcorreo;
    }
    
    function getAccion() {
        return $this->accion;
    }

    function getImportar() {
        return $this->importar;
    }

    function setAccion($accion) {
        $this->accion = $accion;
    }

    function setImportar($importar) {
        $this->importar = $importar;
    }

    function getVentas() {
        return $this->ventas;
    }

    function setVentas($ventas) {
        $this->ventas = $ventas;
    }

    function getCrearVenta() {
        return $this->crearventa;
    }

    function setCrearVenta($crearventa) {
        $this->crearventa = $crearventa;
    }

    function getCancelarVenta() {
        return $this->cancelarventa;
    }

    function setCancelarVenta($cancelarventa) {
        $this->cancelarventa = $cancelarventa;
    }

    function getExportarVenta() {
        return $this->exportarventa;
    }

    function setExportarVenta($exportarventa) {
        $this->exportarventa = $exportarventa;
    }

    function setRegistrarEntrada($registrarentrada) {
        $this->registrarentrada = $registrarentrada;
    }

    function setRegistrarSalida($registrarsalida) {
        $this->registrarsalida = $registrarsalida;
    }

    function setCortedeCaja($cortedecaja) {
        $this->cortedecaja = $cortedecaja;
    }

}
