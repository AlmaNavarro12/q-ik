<?php
require_once '../com.sine.modelo/Empresa.php';
require_once '../com.sine.controlador/ControladorEmpresa.php';

if (isset($_POST['transaccion'])) {
    $transaccion = $_POST['transaccion'];
    $cu = new ControladorEmpresa();

    switch ($transaccion) {
        case 'insertardatos':
            $rfc = $_POST['rfc'];
            $carpeta = '../temporal/' . $rfc. '/';
            $csd = $carpeta . 'csd.cer';
            $cerpem = $carpeta . 'csdPEM.pem';
            $key = $carpeta . 'key.key';
            $cerb64 = base64_encode(file_get_contents($csd));
            $keyB64 = base64_encode(file_get_contents($key));

            $shellConvertCSD = "openssl x509 -inform der -in $csd -out $cerpem";
            shell_exec($shellConvertCSD);
            
            $numcert = file_get_contents($carpeta . 'Serial.txt');
            $divide = explode("=", $numcert);
            $numcert2 = $divide[1];
            $par = str_split($numcert2);
            $result = implode(':', $par);
            $divide2 = explode(":", $result);
            $numcert = $divide2[1] . $divide2[3] . $divide2[5] . $divide2[7] . $divide2[9] . $divide2[11] . $divide2[13] . $divide2[15] . $divide2[17] . $divide2[19] . $divide2[21] . $divide2[23] . $divide2[25] . $divide2[27] . $divide2[29] . $divide2[31] . $divide2[33] . $divide2[35] . $divide2[37] . $divide2[39];

            $c = obtenerdatosEmpresa();
            $c->setCsd($cerb64);
            $c->setKeyB64($keyB64);
            $c->setNumcert($numcert);
            $actualizado = $cu->saveDatos($c);
            if ($actualizado != "") {
                echo $actualizado;
            } else {
                echo "0Error: no se insertó el registro";
            }
            break;
        case 'listaempresa':
            $nom = $_POST['nom'];
            $numreg = $_POST['numreg'];
            $pag = $_POST['pag'];
            $datos = $cu->listasEmpresa($nom, $numreg, $pag);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'editarempresa':
            $idempresa = $_POST['idempresa'];
            $datos = $cu->getDatosEmpresa($idempresa);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'actualizarempresa':
            $c = new Empresa();
            $idempresa = $_POST['idempresa'];
            $nombre = $_POST['nombre'];
            $rfc = $_POST['rfc'];
            $razon = $_POST['razon'];
            $color = $_POST['color'];
            $calle = $_POST['calle'];
            $numint = $_POST['interior'];
            $numext = $_POST['exterior'];
            $colonia = $_POST['colonia'];
            $municipio = $_POST['idmunicipio'];
            $estado = $_POST['idestado'];
            $nombrestado = $_POST['estado'];
            $nombremunicipio = $_POST['municipio'];
            $pais = $_POST['pais'];
            $cp = $_POST['cp'];
            $regfiscal = $_POST['regimen'];
            $divr = explode("-", $regfiscal);
            $folio = $divr[0];
            $regimen = $divr[1];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $certificado = $_POST['certificado'];
            $key = $_POST['key'];
            $passkey = $_POST['passkey'];
            $idbanco = $_POST['idbanco'];
            $sucursal = $_POST['sucursal'];
            $cuenta = $_POST['cuenta'];
            $clabe = $_POST['clabe'];
            $oxxo = $_POST['oxxo'];
            $idbanco1 = $_POST['idbanco1'];
            $sucursal1 = $_POST['sucursal1'];
            $cuenta1 = $_POST['cuenta1'];
            $clabe1 = $_POST['clabe1'];
            $oxxo1 = $_POST['oxxo1'];
            $idbanco2 = $_POST['idbanco2'];
            $sucursal2 = $_POST['sucursal2'];
            $cuenta2 = $_POST['cuenta2'];
            $clabe2 = $_POST['clabe2'];
            $oxxo2 = $_POST['oxxo2'];
            $idbanco3 = $_POST['idbanco3'];
            $sucursal3 = $_POST['sucursal3'];
            $cuenta3 = $_POST['cuenta3'];
            $clabe3 = $_POST['clabe3'];
            $oxxo3 = $_POST['oxxo3'];
            $firma = $_POST['firma'];
            $firmaactual = $_POST['firmaactual'];
            $carpeta = '../temporal/' . $rfc . '/';
            if (!is_dir($carpeta)) {
                mkdir($carpeta);
            }
            $cerb64 = "";
            $keyB64 = "";
            $numcert = "";

            if ($certificado != "") {
                $csd = $carpeta . 'csd.cer';
                $cerpem = $carpeta . 'csdPEM.pem';
                $shellConvertCSD = "openssl x509 -inform der -in $csd -out $cerpem";
                shell_exec($shellConvertCSD);

                $cerb64 = base64_encode(file_get_contents($csd));
                $numcert = file_get_contents($carpeta . 'Serial.txt');
                $divide = explode("=", $numcert);
                $numcert2 = $divide[1];

                $par = str_split($numcert2);
                $result = implode(':', $par);

                $divide2 = explode(":", $result);

                $numcert = $divide2[1] . $divide2[3] . $divide2[5] . $divide2[7] . $divide2[9] . $divide2[11] . $divide2[13] . $divide2[15] . $divide2[17] . $divide2[19] . $divide2[21] . $divide2[23] . $divide2[25] . $divide2[27] . $divide2[29] . $divide2[31] . $divide2[33] . $divide2[35] . $divide2[37] . $divide2[39];
            }
            if ($key != "") {
                $keyloc = $carpeta . 'key.key';
                $keyB64 = base64_encode(file_get_contents($keyloc));
                if ($passkey != "") {
                    $keypem = $carpeta . 'keyPEM.pem';
                    $shellConvertKey = "openssl pkcs8 -inform DER -in $keyloc -out $keypem -passin pass:$passkey";
                    shell_exec($shellConvertKey);
                }
            }

            $c->setIdempresa($idempresa);
            $c->setNombreEmpresa($nombre);
            $c->setRfc($rfc);
            $c->setRazonSocial($razon);
            $c->setColor($color);
            $c->setCalle($calle);
            $c->setNumint($numint);
            $c->setNumext($numext);
            $c->setColonia($colonia);
            $c->setMunicipio($municipio);
            $c->setEstado($estado);
            $c->setMunicipio($municipio);
            $c->setNombreEstado($nombrestado);
            $c->setNombreMunicipio($nombremunicipio);
            $c->setPais($pais);
            $c->setCp($cp);
            $c->setFolioFiscal($folio);
            $c->setRegimenFiscal($regimen);
            $c->setCorreo($correo);
            $c->setTelefono($telefono);
            $c->setIdbanco($idbanco);
            $c->setSucursal($sucursal);
            $c->setCuenta($cuenta);
            $c->setClabe($clabe);
            $c->setOxxo($oxxo);
            $c->setIdbanco1($idbanco1);
            $c->setSucursal1($sucursal1);
            $c->setCuenta1($cuenta1);
            $c->setClabe1($clabe1);
            $c->setOxxo1($oxxo1);
            $c->setIdbanco2($idbanco2);
            $c->setSucursal2($sucursal2);
            $c->setCuenta2($cuenta2);
            $c->setClabe2($clabe2);
            $c->setOxxo2($oxxo2);
            $c->setIdbanco3($idbanco3);
            $c->setSucursal3($sucursal3);
            $c->setCuenta3($cuenta3);
            $c->setClabe3($clabe3);
            $c->setOxxo3($oxxo3);
            $c->setKeyB64($keyB64);
            $c->setCsd($cerb64);
            $c->setPasscsd($passkey);
            $c->setNumcert($numcert);
            $c->setFirma($firma);
            $c->setFirmaanterior($firmaactual);
            $c->setNombreBanco1($_POST['nombrebanco1']);
            $c->setNombreBanco2($_POST['nombrebanco2']);
            $c->setNombreBanco3($_POST['nombrebanco3']);
            $c->setNombreBanco4($_POST['nombrebanco4']);
            $actualizado = $cu->actualizarDatos($c);
            if ($actualizado != "") {
                echo $actualizado;
            } else {
                echo "0Error: no guardo el registro ";
            }
            break;
        case 'execkey':
            $passkey = $_POST['passkey'];
            $datos = $cu->getError($passkey);
            if ($datos != "") {
                echo $datos;
            } else {
                echo "0No se han econtrado datos";
            }
            break;
        case 'eliminarempresa':
            $did = $_POST['did'];
            $eliminado = $cu->quitarEmpresa($did);
            if ($eliminado) {
                echo "Registro eliminado";
            } else {
                echo "no se elimino";
            }
            break;
        case 'validaPaquete':
            $datos = $cu->validaPaquete();
            echo $datos;
            break;
        default:
            break;
    }
}

function obtenerdatosEmpresa()
{
    $c = new Empresa();
    $c->setNombreEmpresa($_POST['nombre']);
    $c->setRfc($_POST['rfc']);
    $c->setRazonSocial($_POST['razon']);
    $c->setColor($_POST['color']);
    $c->setCalle($_POST['calle']);
    $c->setNumint($_POST['interior']);
    $c->setNumext($_POST['exterior']);
    $c->setColonia($_POST['colonia']);
    $c->setCorreo($_POST['correo']);
    $c->setTelefono($_POST['telefono']);
    $c->setMunicipio($_POST['idmunicipio']);
    $c->setEstado($_POST['idestado']);
    $c->setNombreEstado($_POST['estado']);
    $c->setNombreMunicipio($_POST['municipio']);
    $c->setPais($_POST['pais']);
    $c->setCp($_POST['cp']);
    $regimenFiscal = explode("-", $_POST['regimen']);
    $folioFiscal = $regimenFiscal[0];
    $regimen = $regimenFiscal[1];
    $c->setFolioFiscal($folioFiscal);
    $c->setRegimenFiscal($regimen);
    $c->setIdbanco($_POST['idbanco']);
    $c->setSucursal($_POST['sucursal']);
    $c->setCuenta($_POST['cuenta']);
    $c->setClabe($_POST['clabe']);
    $c->setOxxo($_POST['oxxo']);
    $c->setIdbanco1($_POST['idbanco1']);
    $c->setSucursal1($_POST['sucursal1']);
    $c->setCuenta1($_POST['cuenta1']);
    $c->setClabe1($_POST['clabe1']);
    $c->setOxxo1($_POST['oxxo1']);
    $c->setIdbanco2($_POST['idbanco2']);
    $c->setSucursal2($_POST['sucursal2']);
    $c->setCuenta2($_POST['cuenta2']);
    $c->setClabe2($_POST['clabe2']);
    $c->setOxxo2($_POST['oxxo2']);
    $c->setIdbanco3($_POST['idbanco3']);
    $c->setSucursal3($_POST['sucursal3']);
    $c->setCuenta3($_POST['cuenta3']);
    $c->setClabe3($_POST['clabe3']);
    $c->setOxxo3($_POST['oxxo3']);
    $c->setPasscsd($_POST['passkey']);
    $c->setFirma($_POST['firma']);
    $c->setNombreBanco1($_POST['nombrebanco1']);
    $c->setNombreBanco2($_POST['nombrebanco2']);
    $c->setNombreBanco3($_POST['nombrebanco3']);
    $c->setNombreBanco4($_POST['nombrebanco4']);
    return $c;
}
