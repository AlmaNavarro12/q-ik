<?php
require_once  '../../CATSAT/CATSAT/com.sine.controlador/controladorMunicipio.php';
require_once  '../../CATSAT/CATSAT/com.sine.controlador/controladorEstado.php';
require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';

class ControladorAuto {

    private $consultas;
    private $controladorMunicipio;
    private $controladorEstado;


    function __construct() {
        $this->consultas = new consultas(); 
        $this->controladorMunicipio = new ControladorMunicipio();
        $this->controladorEstado = new ControladorEstado();
    }

    public function getCoincidenciasBusquedaCliente($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM cliente WHERE (nombre_empresa LIKE '%$referencia%') OR (concat(nombre,' ',apaterno,' ',amaterno) LIKE '%$referencia%') OR (rfc LIKE '%$referencia%') OR (razon_social LIKE '%$referencia%') LIMIT 0,5;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $estado = $this->controladorEstado->getEstadoAux($resultado['idestado']);
            $municipio = $this->controladorMunicipio->getMunicipioAux($resultado['idmunicipio']);
            $int = "";
            if($resultado['numero_interior'] != ""){
                $int = " Int. ".$resultado['numero_interior'];
            }
            $datos[] = array("value" => $resultado['nombre'] . " " . $resultado['apaterno'] . " - " . $resultado['nombre_empresa'],
                "id" => $resultado["id_cliente"],
                "rfc" => $resultado['rfc'],
                "razon" => $resultado['razon_social'],
                "regfiscal" => $resultado['regimen_cliente'],
                "codpostal" => $resultado['codigo_postal'],
                "direccion" => $resultado['calle']." ".$resultado['numero_exterior'].$int." ".$resultado['localidad']." ".$municipio." ".$estado,
                "mailinfo" => $resultado['email_informacion'],
                "mailfacturas" => $resultado['email_facturacion'],
                "mailgerencia" => $resultado['email_gerencia']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasFacturas($referencia, $iddatos) {
        $datos = [];
    
        $consultaFactura = "SELECT * FROM datos_factura WHERE (concat(letra, folio_interno_fac) LIKE '%$referencia%') AND idcliente = '$iddatos' AND status_pago != '1' AND status_pago != '3' ORDER BY folio_interno_fac DESC LIMIT 15;";
        $resultadosFactura = $this->consultas->getResults($consultaFactura, null);
        foreach ($resultadosFactura as $resultado) {
            $datos[] = [
                "value" => "Factura - " . $resultado['letra'] . $resultado['folio_interno_fac'],
                "id" => $resultado["iddatos_factura"],
                "type" => 'f'
            ];
        }
    
        $consultaCarta = "SELECT * FROM factura_carta WHERE (concat(letra, foliocarta) LIKE '%$referencia%') AND idcliente = '$iddatos' AND status_pago != '1' AND status_pago != '3' ORDER BY foliocarta DESC LIMIT 15;";
        $resultadosCarta = $this->consultas->getResults($consultaCarta, null);
        foreach ($resultadosCarta as $resultado) {
            $datos[] = [
                "value" => "Carta Porte - " . $resultado['letra'] . $resultado['foliocarta'],
                "id" => $resultado["idfactura_carta"],
                "type" => 'c'
            ];
        }
    
        if (empty($datos)) {
            $datos[] = [
                "value" => "No se encontraron registros",
                "id" => "Ninguno"
            ];
        }
    
        return $datos;
    }

    public function getCoincidenciasBusquedaProducto($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM productos_servicios WHERE ((codproducto LIKE '%$referencia%') OR (nombre_producto LIKE '%$referencia%') OR (descripcion_producto LIKE '%$referencia%') OR (clave_fiscal LIKE '%$referencia%')) LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['codproducto']."-".$resultado['nombre_producto'],
                "id" => $resultado["idproser"],
                "codigo" => $resultado['codproducto'],
                "nombre" => $resultado['nombre_producto']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }
}