<?php
require_once '../com.sine.dao/Consultas.php';
require_once '../com.sine.controlador/ControladorSat.php';
require_once '../vendor/autoload.php';

class ControladorAuto {

    private $consultas;
    private $controladorSat;

    function __construct() {
        $this->consultas = new consultas(); 
        $this->controladorSat = new ControladorSat();
    }

    public function getCoincidenciasBusquedaCliente($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM cliente WHERE (nombre_empresa LIKE '%$referencia%') OR (concat(nombre,' ',apaterno,' ',amaterno) LIKE '%$referencia%') OR (rfc LIKE '%$referencia%') OR (razon_social LIKE '%$referencia%') LIMIT 0,5;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $estado =  $resultado["nombre_estado"];
            $municipio =  $resultado["nombre_municipio"];
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
    
        $consultaFactura = "SELECT * FROM datos_factura WHERE (concat(letra, folio_interno_fac) LIKE '%$referencia%') AND idcliente = :datos AND status_pago != '1' AND status_pago != '3' ORDER BY folio_interno_fac DESC LIMIT 15;";
        $valores = array("datos" => $iddatos);
        $resultadosFactura = $this->consultas->getResults($consultaFactura, $valores);
        foreach ($resultadosFactura as $resultado) {
            $datos[] = [
                "value" => "Factura-" . $resultado['letra'] . $resultado['folio_interno_fac'],
                "id" => $resultado["iddatos_factura"],
                "type" => 'f'
            ];
        }
    
        $consultaCarta = "SELECT * FROM factura_carta WHERE (concat(letra, foliocarta) LIKE '%$referencia%') AND idcliente = '$iddatos' AND status_pago != '1' AND status_pago != '3' ORDER BY foliocarta DESC LIMIT 15;";
        $resultadosCarta = $this->consultas->getResults($consultaCarta, null);
        foreach ($resultadosCarta as $resultado) {
            $datos[] = [
                "value" => "Carta Porte-" . $resultado['letra'] . $resultado['foliocarta'],
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

    public function getCoincidenciasFacturasTimbradas($referencia, $iddatos) {
        $datos = [];
    
        $consultaFactura = "SELECT * FROM datos_factura WHERE (concat(letra, folio_interno_fac) LIKE '%$referencia%') AND idcliente = :datos ORDER BY folio_interno_fac DESC LIMIT 15;";
        $valores = array("datos" => $iddatos);
        $resultadosFactura = $this->consultas->getResults($consultaFactura, $valores);
        foreach ($resultadosFactura as $resultado) {
            $datos[] = [
                "value" => "Factura-" . $resultado['letra'] . $resultado['folio_interno_fac'],
                "id" => $resultado["iddatos_factura"],
                "type" => 'f'
            ];
        }
    
        $consultaCarta = "SELECT * FROM factura_carta WHERE (concat(letra, foliocarta) LIKE '%$referencia%') AND idcliente = '$iddatos' ORDER BY foliocarta DESC LIMIT 15;";
        $resultadosCarta = $this->consultas->getResults($consultaCarta, null);
        foreach ($resultadosCarta as $resultado) {
            $datos[] = [
                "value" => "Carta Porte-" . $resultado['letra'] . $resultado['foliocarta'],
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
        $consulta = "SELECT * FROM productos_servicios WHERE ((codproducto LIKE '%$referencia%') OR (nombre_producto LIKE '%$referencia%') OR (descripcion_producto LIKE '%$referencia%') OR (clave_fiscal LIKE '%$referencia%')) AND cantinv > 0 LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['codproducto']."|".$resultado['nombre_producto'],
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

    public function getCoincidenciasLocalidad($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM localidadenvio WHERE (c_Localidad LIKE '%$referencia%' OR Descripcion LIKE '%$referencia%') LIMIT 15;";
        $c = new Consultas();
        $resultados = $c->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['c_Localidad']."-".$resultado['Descripcion'],
                "id" => $resultado["id_localidad"],
                "nombre" => $resultado['Descripcion']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasEmpleado($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM empleado WHERE (nombreempleado LIKE '%$referencia%' OR rfcempleado LIKE '%$referencia%' OR curpempleado LIKE '%$referencia%') LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['nombreempleado'],
                "id" => $resultado["idempleado"],
                "nombre" => $resultado['nombreempleado'],
                "rfc" => $resultado['rfcempleado']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasProducto($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM productos_servicios WHERE ((codproducto LIKE '%$referencia%') OR (nombre_producto LIKE '%$referencia%') OR (descripcion_producto LIKE '%$referencia%') OR (clave_fiscal LIKE '%$referencia%')) LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['clave_fiscal']."-".$resultado['nombre_producto'],
                "id" => $resultado["idproser"],
                "nombre" => $resultado['nombre_producto'],
                "peligro" => $this->controladorSat->getPeligroByCFiscal($resultado['clave_fiscal']));
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasVehiculo($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM transporte WHERE ((nombrevehiculo LIKE '%$referencia%') OR (placavehiculo LIKE '%$referencia%')) and status ='1' LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['nombrevehiculo'],
                "id" => $resultado["idtransporte"],
                "numpermiso" => $resultado["numeropermiso"],
                "tipopermiso" => $resultado["tipopermiso"],
                "conftransporte" => $resultado['conftransporte'],
                "anhomodelo" => $resultado['anhomodelo'],
                "placavehiculo" => $resultado['placavehiculo'],
                "segurocivil" => $resultado['seguroCivil'],
                "polizaCivil" => $resultado['polizaCivil'],
                "seguroambiente" => $resultado['seguroAmbiente'],
                "polizaambiente" => $resultado['polizaambiente']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasRemolque($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM remolque WHERE ((nombreremolque LIKE '%$referencia%') OR (placaremolque LIKE '%$referencia%')) and status ='1' LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['nombreremolque'],
                "id" => $resultado['idremolque'],
                "tiporemolque" => $resultado["tiporemolque"],
                "placaremolque" => $resultado["placaremolque"]);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasUbicacion($referencia, $tipo) {
        $datos = array();
        $consulta = "SELECT u.* FROM ubicacion u WHERE ((u.nombre LIKE '%$referencia%') OR (u.rfcubicacion LIKE '%$referencia%') OR (u.nombre_estado LIKE '%$referencia%')) AND u.status ='1'  AND u.tipoubicacion='$tipo' LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['nombre'],
                "id" => $resultado['idubicacion'],
                "rfc" => $resultado['rfcubicacion'],
                "tipo" => $resultado["tipoubicacion"],
                "calle" => $resultado['calle'],
                "numext" => $resultado['numext'],
                "numint" => $resultado['numint'],
                "colonia" => $resultado['colonia'],
                "idestado" => $resultado["ubicacion_idestado"],
                "idmunicipio" => $resultado['ubicacion_idmunicipio'],
                "cp" => $resultado['codpostal']);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }

    public function getCoincidenciasOperador($referencia) {
        $datos = array();
        $consulta = "SELECT * FROM operador o WHERE ((concat(o.nombreoperador,' ',o.apaternooperador,' ',o.amaternooperador) LIKE '%$referencia%') OR (o.rfcoperador LIKE '%$referencia%')) and opstatus ='1' LIMIT 15;";
        $resultados = $this->consultas->getResults($consulta, null);
        $contador = 0;
        foreach ($resultados as $resultado) {
            $datos[] = array("value" => $resultado['nombreoperador'].' '.$resultado['apaternooperador'].' '.$resultado['amaternooperador'],
                "id" => $resultado['idoperador'],
                "rfc" => $resultado['rfcoperador'],
                "licencia" => $resultado['numlicencia'],
                "idestado" => $resultado["operador_idestado"],
                "idmunicipio" => $resultado['operador_idmunicipio'],
                "calle" => $resultado["calle"],
                "codpostal" => $resultado["cpoperador"]);
            $contador++;
        }
        if ($contador == 0) {
            $datos [] = array("value" => "No se encontraron registros", "id" => "Ninguno");
        }
        return $datos;
    }
}