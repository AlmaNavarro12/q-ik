<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../com.sine.controlador/ControladorInstalacion.php';
require '../pdf/fpdf/fpdf.php';
setlocale(LC_MONETARY, 'es_MX.UTF-8');

class PDF extends FPDF
{
    var $widths;
    var $aligns;
    var $lineHeight;
    var $iddatos;
    var $chfirmar;
    var $isFinished;
    var $idorden;
    var $fechaservicio;
    var $title;
    var $firma;
    var $encargado;
    var $celdatitulo;
    var $colortitulo;
    var $imglogo;
    var $fondosubtitulo;
    var $colorsubtitulos;

    function setRowColorText($t = "#000000")
    {
        $this->rowtextcolor = $t;
    }

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function SetLineHeight($h)
    {
        $this->lineHeight = $h;
    }

    function Row($data)
    {
        // number of line
        $nb = 0;
        // loop each data to find out greatest line number in a row.
        for ($i = 0; $i < count($data); $i++) {
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }

        //multiply number of line with line height. This will be the height of current row
        $lh = $this->lineHeight;
        $h = $lh * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            $w = $this->widths[$i];
            // alignment of the current col. if unset, make it left.
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, $lh, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowT($data)
    {
        // number of line
        $nb = 0;
        // loop each data to find out greatest line number in a row.
        for ($i = 0; $i < count($data); $i++) {
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }

        //multiply number of line with line height. This will be the height of current row
        $h = $this->lineHeight * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            $w = $this->widths[$i];
            // alignment of the current col. if unset, make it left.
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->RoundedRect($x, $y, $w, $h, 4, 'F');
            //$this->Rect($x, $y, $w, $h, 'F');
            //Print the text
            $this->MultiCell($w, $h, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowNB($data)
    {
        // number of line
        $nb = 0;
        // loop each data to find out greatest line number in a row.
        for ($i = 0; $i < count($data); $i++) {
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }

        //multiply number of line with line height. This will be the height of current row
        $h = $this->lineHeight * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            $w = $this->widths[$i];
            // alignment of the current col. if unset, make it left.
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 4.5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }



    function RowNBC($data)
    {
        // number of line
        $nb = 0;
        // loop each data to find out greatest line number in a row.
        for ($i = 0; $i < count($data); $i++) {
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }

        //multiply number of line with line height. This will be the height of current row
        $h = $this->lineHeight * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            $w = $this->widths[$i];
            // alignment of the current col. if unset, make it left.
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 4.5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowR($data)
    {
        // number of line
        $nb = 0;
        // loop each data to find out greatest line number in a row.
        for ($i = 0; $i < count($data); $i++) {
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }

        //multiply number of line with line height. This will be the height of current row
        $h = $this->lineHeight * $nb;
        $h2 = $this->lineHeight;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            $w = $this->widths[$i];
            // alignment of the current col. if unset, make it left.
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x, $y, $w, $h);
            $this->SetFillColor(255, 255, 255);
            $this->RoundedRect($x, $y, $w, $h, 2, 'FD');
            //Print the text
            $this->MultiCell($w, $h2, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //calculate the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' or $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2f %.2f m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2f %.2f l', $xc * $k, ($hp - $y) * $k));
        if (strpos($angle, '2') === false)
            $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - $y) * $k));
        else
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($angle, '3') === false)
            $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2f %.2f l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($angle, '4') === false)
            $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($angle, '1') === false) {
            $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2f %.2f l', ($x + $r) * $k, ($hp - $y) * $k));
        } else
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1 * $this->k, ($h - $y1) * $this->k, $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    function Header()
    {
        $this->SetY(5);
        $this->SetFont('Arial', '', 20);
        $rgbc = explode("-", $this->celdatitulo);
        $rgbt = explode("-", $this->colortitulo);
        $this->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $this->SetTextColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $logo = "../img/logo/" . $this->imglogo;
        $dimensiones = getimagesize($logo);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $height = ($height * 20) / $width;
        if ($height > 25) {
            $height = 25;
        }
        $this->Cell(25, 5, $this->Image($logo, $this->GetX() + 2.5, $this->GetY(), 20, $height), 0, 0, 'C', false);
        $this->RoundedRect(35, $this->GetY(), 120, 8, 4, 'F');
        $this->SetX(38);
        $this->Write(8, iconv("utf-8", "windows-1252", $this->title));

        $this->SetY(15);
        $rgbcs = explode("-", $this->fondosubtitulo);
        $rgbts = explode("-", $this->colorsubtitulos);
        $this->SetFillColor($rgbcs[0], $rgbcs[1], $rgbcs[2]);
        $this->SetTextColor($rgbts[0], $rgbts[1], $rgbts[2]);
        $this->SetFont('Arial', 'B', 13);
        $this->RoundedRect(35, $this->GetY(), 120, 8, 4, 'F');
        $this->SetX(37);
        $this->Write(8, "Fecha de servicio: " . $this->fechaservicio);

        $this->SetY(5);
        $this->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $this->SetTextColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $this->SetX(160);
        $this->RoundedRect(160, $this->GetY(), 45, 8, 4, 'F');
        $this->SetX(173.5);
        $this->Write(8, 'Orden');

        $this->SetY(14);
        $this->SetX(160);
        $this->SetTextColor(255, 0, 0);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(45, 8, iconv("utf-8", "windows-1252", $this->idorden), 0, 0, 'C', false);
        $this->Ln(22);
    }

    function Footer()
    {
        if ($this->idorden >= 1558) {
            $this->SetY(-20);
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', '', 6.5);
            $this->Write(3, iconv('UTF-8', 'windows-1252', "El cliente acepta lo reflejado en este documento, y también acepta que la empresa que ofreció el servicio, así como sus empleados no se hacen responsables por daños causados antes, durante o después de la instalación. Para conservar la Garantía del Sistema de Rastreo Satelital GPS es muy importante evitar que éste sea manipulado o alterado por personal ajeno a que ofreció el servicio y esto incluye a técnicos de agencias de vehículos nuevos. La empresa no responderá por ningún tipo de daño si el dispositivo o su instalación fueron previamente intervenidos por terceros. La empresa no se hace responsable por la pérdida o robo de objetos de valor personales dentro del vehículo."));
            $this->Ln(1.7);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(65, 10, '', 0, 0, 'L');
            $this->Cell(65, 10, iconv('UTF-8', 'windows-1252', 'Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'C');
            $this->Cell(65, 10, '', 0, 0, 'R');
        } else {
            $this->SetY(-29);
            $this->SetFillColor(255, 255, 255);
            $this->RoundedRect(145, ($this->GetY() - 30), 60, 40, 3, 'D');
            if ($this->firma != "") {
                $this->Image($this->firma, 150, ($this->GetY() - 25), 60, 0, 'png');
            }
            $this->SetX(150);
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', 'B', 8);
            $this->Write(4, iconv("utf-8", "windows-1252", $this->encargado));
            $this->Ln();
            $this->SetX(150);
            $this->Write(4, "Firma encargado");
            $this->Ln();
            $this->SetFont('Arial', '', 6.5);
            $this->Ln(2.5);
            $this->Write(3, iconv("utf-8", "windows-1252", "El cliente acepta lo reflejado en este documento, y también acepta que la empresa que ofreció el servicio, así como sus empleados no se hacen responsables por daños causados antes, durante o después de la instalación. Para conservar la Garantía del Sistema de Rastreo Satelital GPS es muy importante evitar que éste sea manipulado o alterado por personal ajeno a que ofreció el servicio y esto incluye a técnicos de agencias de vehículos nuevos. La empresa no responderá por ningún tipo de daño si el dispositivo o su instalación fueron previamente intervenidos por terceros. La empresa no se hace responsable por la pérdida o robo de objetos de valor personales dentro del vehículo."));
            $this->Ln(1.7);
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(65, 10, '', 0, 0, 'L');
            $this->Cell(65, 10, iconv("utf-8", "windows-1252", 'Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'C');
            $this->Cell(65, 10, '', 0, 0, 'R');
        }
    }

    function myCell($w, $h, $x, $t)
    {
        $height = $h / 3;
        $first = $height + 2;
        $second = $height + $height + $height + 3;
        $len = strlen($t);
        if ($len > 15) {
            $txt = str_split($t, 14);
            $this->SetX($x);
            $this->Cell($w, $first, $txt[0], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $second, $txt[1], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $h, '', 'LTRB', 0, 'L', 0);
        } else {
            $this->SetX($x);
            $this->Cell($w, $h, $t, 'LTRB', 0, 'C', 0);
        }
    }

    function myCellD($w, $h, $x, $t)
    {
        $height = $h / 3;
        $first = $height + 2;
        $second = $height + $height + $height + 3;
        $len = strlen($t);
        if ($len > 30) {
            $txt = str_split($t, 30);
            $this->SetX($x);
            $this->Cell($w, $first, $txt[0], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $second, $txt[1], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $h, '', 'LTRB', 0, 'L', 0);
        } else {
            $this->SetX($x);
            $this->Cell($w, $h, $t, 'LTRB', 0, 'C', 0);
        }
    }

    function MultiCellBlt($w, $h, $blt, $txt, $border = 0, $align = 'J', $fill = false)
    {
        //Get bullet width including margins
        $blt_width = $this->GetStringWidth($blt) + $this->cMargin * 2;

        //Save x
        $bak_x = $this->x;

        //Output bullet
        $this->Cell($blt_width, $h, $blt, 0, '', $fill);

        //Output text
        $this->MultiCell($w - $blt_width, $h, $txt, $border, $align, $fill);

        //Restore x
        $this->x = $bak_x;
    }
}

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('16');
foreach ($encabezado as $actual) {
    $titulo = $actual['tituloencabezado'];
    $colortitulo = $cc->hex2rgb($actual['colortitulo']);
    $celdatitulo = $cc->hex2rgb($actual['colorceltitulo']);
    $imglogo = $actual['imglogo'];
    $pagina = $actual['pagina'];
    $correo = $actual['correo'];
    $telefono1 = $actual['telefono1'];
    $telefono2 = $actual['telefono2'];
    $chnum = $actual['numpag'];
    $colorpie = $cc->hex2rgb($actual['colorpie']);
    $colorcuadro = $actual['colorcuadro'];
    $rgbc = $cc->hex2rgb($colorcuadro);
    $colorsubtitulos = $actual['colorsubtitulos'];
    $rgbs = $cc->hex2rgb($colorsubtitulos);
    $clrfdatos = $actual['colorfdatos'];
    $rgbfd = explode("-", $cc->hex2rgb($clrfdatos));
    $txtbold = $actual['colorbold'];
    $rgbbld = explode("-", $cc->hex2rgb($txtbold));
    $clrtxt = $actual['colortexto'];
    $rgbtxt = explode("-", $cc->hex2rgb($clrtxt));
    $colorhtabla = $actual['colorhtabla'];
    $rgbt = explode("-", $cc->hex2rgb($colorhtabla));
    $colortittabla = $actual['colortittabla'];
    $rgbtt = explode("-", $cc->hex2rgb($colortittabla));
}

$ci = new ControladorInstalacion();
if (isset($_GET['s'])) {
    $idservicio = intval($_GET['s']);
} else if (isset($_POST['id'])) {
    $idservicio = intval($_POST['id']);
}

if (isset($idservicio)) {
    $datos = $ci->getOrdenbyId($idservicio);
    foreach ($datos as $actual) {
        $idorden = $actual['idorden'];
        $fechaservicio = $actual['fechaservicio'];
        $horaservicio = $actual['horaservicio'];
        $idcliente = $actual['idcliente'];
        $cliente = $actual['cliente'];
        $marca = $actual['marca'];
        $modelo = $actual['modelo'];
        $anho = $actual['anho'];
        $color = $actual['color'];
        $serie = $actual['serie'];
        $numeconomico = $actual['numeconomico'];
        $km = $actual['km'];
        $placas = $actual['placas'];
        $iddanhos = $actual['iddanhos'];
        $idmolduras = $actual['idmolduras'];
        $otrosmolduras = $actual['otrosmolduras'];
        $idtablero = $actual['idtablero'];
        $otrostablero = $actual['otrostablero'];
        $idcableado = $actual['idcableado'];
        $otroscableado = $actual['otroscableado'];
        $idccorriente = $actual['idccorriente'];
        $otroscorriente = $actual['otroscorriente'];
        $idtservicio = $actual['idtservicio'];
        $modeloanterior = $actual['modeloanterior'];
        $imeianterior = $actual['imeianterior'];
        $simanterior = $actual['simanterior'];
        $otrostservicio = $actual['otrostservicio'];
        $gpsvehiculo = $actual['gpsvehiculo'];
        $imei = $actual['imei'];
        $numtelefono = $actual['numtelefono'];
        $idinstalador = $actual['idinstalador'];
        $idaccesorio = $actual['idaccesorio'];
        $observaciones = $actual['observaciones'];
        $idinstalacion = $actual['idinstalacion'];
        $encargado = $actual['encargado'];
        $firma = $actual['firma'];
        $instalador = $actual['nombre_inst'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'];
        $folio = $actual['letra_folio'] . $actual['folioinstalacion'];
        $imgfrente = $actual['imgfrentevehiculo'];
        $frentebase = $actual['imgfrentebase'];
        $imgnserie = $actual['imgnserie'];
        $seriebase = $actual['imgseriebase'];
        $imgtabinicial = $actual['imgtabinicial'];
        $tableroinibase = $actual['imgtabinibase'];
        $imgtabfinal = $actual['imgtabfinal'];
        $tablerofinbase = $actual['imgtabfinbase'];
        $imgantesinst = $actual['imgantesinst'];
        $antesbase = $actual['imgantesbase'];
        $imgdespuesinst = $actual['imgdespuesinst'];
        $despuesbase = $actual['imgdespuesbase'];
        $observacion_general = $actual['observacion_general'];
        $ubicacion_boton_panico = $actual['ubicacion_boton_panico'];
        $tipo_corte = $actual['tipo_corte'];
        $tipounidad = $actual['tipounidad'];

        $divideF = explode("-", $fechaservicio);
        $fechaservicio2 = $divideF[2] . '/' . $divideF[1] . '/' . $divideF[0] . " a " . date("h:i A", strtotime($horaservicio));
    }

    $rfc = "";
    $telefono = "";
    $correo = "";
    $direccion = " ";
    $obswidth = 128;

    if ($idcliente != "0" && $idcliente != "") {
        $datoscliente = $ci->getDatosClientebyID($idcliente);
        foreach ($datoscliente as $clienteactual) {
            $rfc = $clienteactual['rfc'];
            $telefono = $clienteactual['telefono'];
            $correo = $clienteactual['email_gerencia'];
            $calle = $clienteactual['calle'];
            $numint = $clienteactual['numero_interior'];
            $numext = $clienteactual['numero_exterior'];
            $localidad = $clienteactual['localidad'];
            $idmunicipio = $clienteactual['idmunicipio'];
            $idestado = $clienteactual['idestado'];
            $pais = $clienteactual['pais'];
            $municipio = $clienteactual['nombre_municipio'];
            $estado = $clienteactual['nombre_estado'];
        }

        $direccion = "$calle #$numext, Col. $localidad, $municipio, $estado";
    }

    $noorden = $idorden;
    if ($noorden < 10 && $noorden != "") {
        $noorden = "00$idorden";
    } else if ($noorden >= 10 && $noorden < 100) {
        $noorden = "0$idorden";
    }

    //----------------------------------HOJA DE SERVICIO PARA UN VEHÍCULO
    if ($tipounidad == 1) {
        $pdf = new PDF('P', 'mm', 'Letter');

        $pdf->imglogo = $imglogo;
        $pdf->colortitulo = $colortitulo;
        $pdf->celdatitulo = $celdatitulo;
        $pdf->colorsubtitulos = $rgbs;
        $pdf->fondosubtitulo = $rgbc;
        $pdf->pagina = $pagina;
        $pdf->correo = $correo;
        $pdf->telefono1 = $telefono1;
        $pdf->telefono2 = $telefono2;
        $pdf->chnum = $chnum;
        $pdf->colorpie = $colorpie;

        $pdf->idorden = $noorden;
        $pdf->title = $titulo;
        $pdf->fechaservicio = $fechaservicio2;
        $pdf->firma = $firma;
        $pdf->encargado = $encargado;

        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos del cliente"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(120, 75));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Cliente:");
        $pdf->SetX(130);
        $pdf->Write(4.5, "RFC:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $cliente), "\n" . $rfc));

        $pdf->SetWidths(array(60, 60, 75));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Contacto:");
        $pdf->SetX(70);
        $pdf->Write(4.5, "Tel. Oficina:");
        $pdf->SetX(130);
        $pdf->Write(4.5, "E-mail");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $encargado), "\n" . iconv('UTF-8', 'windows-1252', $telefono), "\n" . iconv('UTF-8', 'windows-1252', $correo)));

        $pdf->SetWidths(array(195));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Direccion:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "\n$direccion")));

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos de la unidad"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(65, 65, 20, 45));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Marca:");
        $pdf->SetX(75);
        $pdf->Write(4.5, "Modelo:");
        $pdf->SetX(140);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Año"));
        $pdf->SetX(160);
        $pdf->Write(4.5, "Color");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $marca), "\n" . iconv('UTF-8', 'windows-1252', $modelo), "\n" . $anho, "\n" . iconv('UTF-8', 'windows-1252', $color)));

        $pdf->SetWidths(array(65, 65, 35, 30));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Serie:");
        $pdf->SetX(75);
        $pdf->Write(4.5, "Placas:");
        $pdf->SetX(140);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Eco:"));
        $pdf->SetX(175);
        $pdf->Write(4.5, "KM:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $serie), "\n" . iconv('UTF-8', 'windows-1252', $placas), "\n" . iconv('UTF-8', 'windows-1252', $numeconomico), "\n" . iconv('UTF-8', 'windows-1252', $km)));

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos del equipo"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(65, 65, 65));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Modelo:");
        $pdf->SetX(75);
        $pdf->Write(4.5, "IMEI:");
        $pdf->SetX(140);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "No. Telefónico"));
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $gpsvehiculo), "\n" . iconv('UTF-8', 'windows-1252', $imei), "\n" . $numtelefono));

        $pdf->SetWidths(array(195));
        $pdf->SetLineHeight(4.5);


        $have_btn_panic = 0;
        $have_tipo_corte = 0;
        $div_accesorios = explode('-', $idaccesorio);
        for ($i = 0; $i < sizeof($div_accesorios); $i++) {
            if ($div_accesorios[$i] == 1) {
                $have_btn_panic++;
            }
            if ($div_accesorios[$i] == 4) {
                $have_tipo_corte++;
            }
        }

        if ($idaccesorio != "") {
            $accesorios = iconv('UTF-8', 'windows-1252', $ci->listAccesorios($idaccesorio));
            //if(strpos($idaccesorio,'1-') !== false){
            if ($have_btn_panic > 0) {
                $accesorios .= "\n" . chr(149) . iconv('UTF-8', 'windows-1252', "Ubicación del botón de pánico: $ubicacion_boton_panico");
            }
            //if(strpos($idaccesorio,'4-') !== false){
            if ($have_tipo_corte > 0) {
                $accesorios .= "\n" . chr(149) . iconv('UTF-8', 'windows-1252', "Tipo de corte: $tipo_corte");
            }
            if ($observaciones != "") {
                $accesorios .= iconv('UTF-8', 'windows-1252', "\nObservaciones: $observaciones");
            }
        } else {
            $accesorios = " ";
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Accesorios:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . $accesorios));

        //if(strpos($idaccesorio,'1-') !== false){
        if ($have_btn_panic > 0) {
            $divn = explode("</tr>", $ci->getNameImgOtras(10, 11, $folio));
            $nam = $divn[0];
            $btnpanico = $nam;

            $divname = explode("</tr>", $ci->getNameImgOtras(10, 12, $folio));
            $name = $divname[0];
            $notibtnpanico = $name;

            $pdf->SetWidths(array(97.5, 97.5));
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetAligns(array('C', 'C'));
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Botón de Pánico"), iconv('UTF-8', 'windows-1252', "Notificación del Botón de Pánico")));
            $pdf->Row(Array("\n\n\n\n\n\n\n\n\n\n\n\n", ""));

            if ($btnpanico != "") {
                $image_info = explode(':', $btnpanico)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];
                $pdf->Cell(0, 0, '', 0, 0, 'C', 0);
                $pdf->Image($btnpanico, 35, ($pdf->GetY() - 52), 50, 50, $image_type);
            }

            if ($notibtnpanico != "") {
                $image_info = explode(':', $notibtnpanico)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];
                $pdf->Cell(0, 0, '', 0, 0, 'C', 0);
                $pdf->Image($notibtnpanico, 134, ($pdf->GetY() - 52), 50, 50, $image_type);
            }
        }
        /******************************* */
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos del servicio"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(195));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Servicio:");
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        if ($idtservicio != "") {
            $servicios = $ci->listServicios($idtservicio, $otrostservicio);
        } else {
            $servicios = " ";
        }
        $pdf->SetAligns('L');
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $servicios)));

        $showimei = false;
        $showsim = false;
        $divS = explode("-", $idtservicio);
        foreach ($divS as $Sactual) {
            if ($Sactual == '8') {
                $showimei = true;
            }
            if ($Sactual == '9') {
                $showsim = true;
            }
        }

        if ($showimei || $showsim) {
            $titimei = "";
            $tittel = "";
            $imeiant = "";
            $telant = "";
            if ($showimei) {
                $titimei = "Modelo e IMEI anterior:";
                $imeiant = $modeloanterior . " - " . $imeianterior;
            }
            if ($showsim) {
                $tittel = "Telefono anterior:";
                $telant = $simanterior;
            }
            $pdf->SetWidths(array(97.5, 97.5));
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(4.5, $titimei);
            $pdf->SetX(107.5);
            $pdf->Write(4.5, $tittel);
            $pdf->SetX(10);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $imeiant), "\n" . iconv('UTF-8', 'windows-1252', $telant)));
        }

        $pdf->SetWidths(array(195));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Instalador:");
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetLineHeight(4.5);
        $pdf->SetAligns('L');
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $instalador)));

        /************************************************************************************ */

        if ($otrosmolduras == '') {
            $otrosmolduras = "Otros (específica)";
        }

        if ($otrostablero == "") {
            $otrostablero = "Otros (específica)";
        }

        if ($otroscableado == "") {
            $otroscableado = "Otros (específica)";
        }

        if ($otroscorriente == "") {
            $otroscorriente = "Otros (específica)";
        }

        $pdf->title = "Recepción del vehículo";
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetWidths(array(97.5, 97.5));
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetAligns(array('C', 'C'));
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Frente del vehículo"), iconv('UTF-8', 'windows-1252', "Número de serie o VIN")));
        $pdf->Row(array("\n\n\n\n\n\n\n\n\n\n\n", ""));

        if ($frentebase != "") {
            $image_info = explode(':', $frentebase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Cell(48.75, 48.75, '', 0, 0, 'C', 0);
            $pdf->Image($frentebase, 35, ($pdf->GetY() - 48), 46, 46, $image_type);
            $ln = 52;
        }

        if ($seriebase != "") {
            $image_info = explode(':', $seriebase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Cell(48.75, 48.75, '', 0, 0, 'C', 0);
            $pdf->Image($seriebase, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
            $ln = 52;
        }
        $pdf->Ln(5);
        $pdf->SetAligns('L');
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(8, iconv('UTF-8', 'windows-1252', "Daños del vehículo"));
        $pdf->Ln(7);

        $pdf->SetWidths(array(97.5, 97.5));
        $divD = explode("-", $iddanhos);
        foreach ($divD as $damactual) {
            switch ($damactual) {
                case '1':
                    $danho = "Parachoques delantero";
                    break;
                case '2':
                    $danho = "Parachoques trasero";
                    break;
                case '3':
                    $danho = "Lateral izquierdo";
                    break;
                case '4':
                    $danho = "Lateral derecho";
                    break;
                case '5':
                    $danho = "Parabrisas";
                    break;
                case '6':
                    $danho = "Cajuela";
                    break;
                case '7':
                    $danho = "Cofre";
                    break;
                case '8':
                    $danho = "Techo";
                    break;
                case '9':
                    $danho = "Sin daños";
                    break;
                default:
                    $danho = "";
                    break;
            }

            $pdf->SetAligns(array('C', 'C'));
            if ($damactual != 9 && $damactual != "") {
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $divname = explode("</tr>", $ci->getNameImgOtras(2, $damactual, $folio));
                $name = $divname[0];
                $pdf->Row(array(chr(149) . " " . iconv('UTF-8', 'windows-1252', $danho), "\n\n\n\n\n\n\n\n\n\n\n"));
                $image_info = explode(':', $name)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];
                $pdf->Image($name, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
                $ln = 52;
            } else {
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->Row(array("\n" .chr(149) . " " . iconv('UTF-8', 'windows-1252', $danho), "\n" . chr(149) . " " . iconv('UTF-8', 'windows-1252', "No aplica imagen \n ")));
            }
        }

        $pdf->Ln(5);
        $pdf->SetAligns('L');
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(8, iconv('UTF-8', 'windows-1252', "Molduras del vehículo"));
        $pdf->Ln(7);

        $divM = explode("-", $idmolduras);
        foreach ($divM as $molactual) {
            switch ($molactual) {
                case '1':
                    $moldura = "Molduras dañadas";
                    break;
                case '2':
                    $moldura = "Tornillos, grapas o pijas";
                    break;
                case '3':
                    $moldura = "Sin observación";
                    break;
                case '4':
                    $moldura = $otrosmolduras;
                    break;
                default:
                    $moldura = "";
                    break;
            }
            $pdf->SetAligns(array('C', 'C'));
            if ($molactual != 3 && $molactual != "") {
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $divname = explode("</tr>", $ci->getNameImgOtras(3, $molactual, $folio));
                $name = $divname[0];
                $image_info = explode(':', $name)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];
                $pdf->Row(array(chr(149) . " " . iconv('UTF-8', 'windows-1252', $moldura), "\n\n\n\n\n\n\n\n\n\n\n"));
                $pdf->Image($name, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
            } else {
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->Row(array("\n" .chr(149) . " " . iconv('UTF-8', 'windows-1252', $moldura . "\n "), "\n" . chr(149) . " " . iconv('UTF-8', 'windows-1252', "No aplica imagen \n ")));
            }
        }

        $pdf->Ln(10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetWidths(array(97.5, 97.5));
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetAligns(array('C', 'C'));
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Tablero inicial"), iconv('UTF-8', 'windows-1252', "Tablero final")));

        $pdf->Row(array("\n\n\n\n\n\n\n\n\n\n\n", ""));

        if ($tableroinibase != "") {
            $image_info = explode(':', $tableroinibase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($tableroinibase, 35, ($pdf->GetY() - 48), 46, 46,  $image_type);
        }

        if ($tablerofinbase != "") {
            $image_info = explode(':', $tablerofinbase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($tablerofinbase, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
        }
        $pdf->Ln(5);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(8, iconv('UTF-8', 'windows-1252', "Condiciones del tablero"));
        $pdf->Ln(7);

        $pdf->SetWidths(array(97.5, 97.5));
        $divT = explode("-", $idtablero);
        foreach ($divT as $tabactual) {
            switch ($tabactual) {
                case '1':
                    $tablero = "Testigos encendidos";
                    break;
                case '2':
                    $tablero = "No enciende";
                    break;
                case '3':
                    $tablero = "No marca gasolina";
                    break;
                case '4':
                    $tablero = "Arnés o contra arnés dañado";
                    break;
                case '5':
                    $tablero = "Sin observación";
                    break;
                case '6':
                    $tablero = $otrostablero;
                    break;
                default:
                    $tablero = "";
                    break;
            }
            if ($tabactual  == 4 || $tabactual == 6) {
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $divname = explode("</tr>", $ci->getNameImgOtras(4, $tabactual, $folio));
                $name = $divname[0];
                $image_info = explode(':', $antesbase)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];
                $pdf->Row(array(chr(149) . " " . iconv('UTF-8', 'windows-1252', $tablero), "\n\n\n\n\n\n\n\n\n\n\n"));
                $pdf->Image($name, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
            } else {
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->Row(array("\n" . chr(149) . " " . iconv('UTF-8', 'windows-1252', $tablero) . "\n ", "\n" . chr(149) . " " . iconv('UTF-8', 'windows-1252', "No aplica imagen \n ")));
            }
        }
        $pdf->Ln(5);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Cableado interno del tablero:"));
        $pdf->Ln(7);


        $pdf->SetWidths(array(97.5, 97.5));
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetAligns(array('C', 'C'));
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Antes de instalación / revisión"), iconv('UTF-8', 'windows-1252', "Después de instalación / revisión")));
        $pdf->Row(array("\n\n\n\n\n\n\n\n\n\n\n", ""));

        if ($antesbase != "") {
            $image_info = explode(':', $antesbase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($antesbase, 35, ($pdf->GetY() - 48), 46, 46, $image_type);
        }

        if ($despuesbase != "") {
            $image_info = explode(':', $despuesbase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($despuesbase, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
            $valtablero = "\n\n\n\n\n\n\n\n\n\n\n";
        }

        $pdf->SetWidths(array(195));
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $divCab = explode("-", $idcableado);
        foreach ($divCab as $cabactual) {
            switch ($cabactual) {
                case '1':
                    $cableado = "Cables sueltos";
                    break;
                case '2':
                    $cableado = "Cables sin aislamiento";
                    break;
                case '3':
                    $cableado = "Empalme de cables excesivo";
                    break;
                case '4':
                    $cableado = "Sin observación";
                    break;
                case '5':
                    $cableado = $otroscableado;
                    break;
                default:
                    $cableado = "";
                    break;
            }
            $pdf->Row(array("\n" . chr(149) . " " . iconv('UTF-8', 'windows-1252', $cableado . "\n ")));
        }

        $pdf->Ln(5);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Sistema de corta corriente"));
        $pdf->Ln(5);


        $pdf->SetWidths(array(195));
        $divcorriente = explode("-", $idccorriente);
        foreach ($divcorriente as $corrienteactual) {
            switch ($corrienteactual) {
                case '1':
                    $ccorriente = "Alarma con corta corriente";
                    break;
                case '2':
                    $ccorriente = "GPS con corta corriente activo";
                    break;
                case '3':
                    $ccorriente = "Switch corta corriente";
                    break;
                case '4':
                    $ccorriente = "No cuenta";
                    break;
                case '5':
                    $ccorriente = $otroscorriente;
                    break;
                default:
                    $ccorriente = "";
                    break;
            }
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Row(array("\n" . chr(149) . " " . iconv('UTF-8', 'windows-1252', $ccorriente . "\n ")));
        }

        $pdf->AddPage();
        $otrasimg = 0;
        $imgs = $ci->getImgInsAux($folio);
        foreach ($imgs as $imgactual) {
            $otrasimg++;
        }

        if ($otrasimg > 0) {
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Otras evidencias"));
            $pdf->Ln(6);
            $pdf->SetWidths(array(97.5, 97.5));
            $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Descripción"), iconv('UTF-8', 'windows-1252', "Imagen")));
            foreach ($imgs as $imgactual) {
                $img = $imgactual['imginstalacion'];
                $titulo = $imgactual['titulo'];

                $image_info = explode(':', $img)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetAligns(array('C', ''));
                $pdf->Row(array("\n\n".chr(149) . " " . iconv('UTF-8', 'windows-1252', $titulo), "\n\n\n\n\n\n\n\n\n\n\n"));
                $pdf->Image($img, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
            }
        }

        if ($observacion_general != "") {
            $pdf->Ln(7);
            
            $pdf->SetLineHeight(4.5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(4.5, "Observaciones en general");
            $pdf->SetAligns(array('L'));
            $pdf->SetWidths(array(195));
            $pdf->SetX(10);
            $pdf->SetFont('Arial', '', 11);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $obser = str_replace("<corte>", "\n", $observacion_general);
            if (!isset($_GET['b'])) {
                $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', "\n" . $obser . "\n\n")));
            }
        }

        /*$pdf->Ln(50);
        $pdf->SetWidths(array(195));
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetAligns(array('C'));
        $pdf->RowNB(array("\n\n"));
        if ($pdf->firma != "") {
            $pdf->Image($pdf->firma, 77.5, ($pdf->GetY() - 35), 60, 0, 'png');
        }
        $pdf->RowNB(array(iconv('UTF-8', 'windows-1252', "___________________________________________________________\n Firma del encargado \n" . $pdf->encargado)));*/
        $pdf->isFinished = true;
    //----------------------------------HOJA DE SERVICIO PARA UNA CAJA
    } else if ($tipounidad == 2) {
        $pdf = new PDF('P', 'mm', 'Letter');

        $pdf->imglogo = $imglogo;
        $pdf->colortitulo = $colortitulo;
        $pdf->celdatitulo = $celdatitulo;
        $pdf->colorsubtitulos = $rgbs;
        $pdf->fondosubtitulo = $rgbc;
        $pdf->pagina = $pagina;
        $pdf->correo = $correo;
        $pdf->telefono1 = $telefono1;
        $pdf->telefono2 = $telefono2;
        $pdf->chnum = $chnum;
        $pdf->colorpie = $colorpie;
        $pdf->idorden = $noorden;
        $pdf->fechaservicio = $fechaservicio2;
        $pdf->title = $titulo;
        $pdf->firma = $firma;
        $pdf->encargado = $encargado;

        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos del cliente"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(125, 70));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Cliente:");
        $pdf->SetX(135);
        $pdf->Write(4.5, "RFC:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $cliente), "\n" . $rfc));

        $pdf->SetWidths(array(60, 65, 70));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Contacto:");
        $pdf->SetX(70);
        $pdf->Write(4.5, "Tel. Oficina:");
        $pdf->SetX(135);
        $pdf->Write(4.5, "E-mail");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', iconv("utf-8", "windows-1252", $encargado)), "\n" . iconv('UTF-8', 'windows-1252', $telefono), "\n" . iconv('UTF-8', 'windows-1252', $correo)));

        $pdf->SetWidths(array(195));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Dirección:"));
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "\n" . $direccion)));

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos de la unidad"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(65, 65, 20, 45));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Marca:");
        $pdf->SetX(75);
        $pdf->Write(4.5, "Modelo:");
        $pdf->SetX(140);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Año"));
        $pdf->SetX(160);
        $pdf->Write(4.5, "Color");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', ($marca == "") ? "---" : $marca), "\n" . iconv('UTF-8', 'windows-1252', ($modelo == "") ? "---" : $modelo), "\n" . ($anho == "") ? "\n---" : $anho, "\n" . iconv('UTF-8', 'windows-1252', ($color == "") ? "---" : $color)));

        $pdf->SetWidths(array(65, 65, 35, 30));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Serie:");
        $pdf->SetX(75);
        $pdf->Write(4.5, "Placas:");
        $pdf->SetX(140);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Eco:"));
        $pdf->SetX(175);
        $pdf->Write(4.5, "KM:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', ($serie == "") ? "---" : $serie), "\n" . iconv('UTF-8', 'windows-1252', ($placas == "") ? "---" : $placas), "\n" . iconv('UTF-8', 'windows-1252', ($numeconomico == "") ? "---" : $numeconomico), "\n" . iconv('UTF-8', 'windows-1252', $km)));

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos del equipo"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(65, 65, 65));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Modelo:");
        $pdf->SetX(75);
        $pdf->Write(4.5, "IMEI:");
        $pdf->SetX(140);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "No. Telefónico"));
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $gpsvehiculo), "\n" . iconv('UTF-8', 'windows-1252', $imei), "\n" . $numtelefono));

        $pdf->SetWidths(array(195));
        $pdf->SetLineHeight(4.5);

        if ($idaccesorio != "") {
            $accesorios = iconv('UTF-8', 'windows-1252', $ci->listAccesorios($idaccesorio));
            if (strpos($idaccesorio, '1-') !== false) {
                $accesorios .= "\n" . chr(149) . iconv('UTF-8', 'windows-1252', "Ubicacion del botón de pánico: $ubicacion_boton_panico");
            }
            if (strpos($idaccesorio, '4-') !== false) {
                $accesorios .= "\n" . chr(149) . iconv('UTF-8', 'windows-1252', "Tipo de corte: $tipo_corte");
            }
            if ($observaciones != "") {
                $accesorios .= iconv('UTF-8', 'windows-1252', "\nObservaciones: $observaciones");
            }
        } else {
            $accesorios = " ";
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Accesorios:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . $accesorios));

        if (strpos($idaccesorio, '1-') !== false) {
            $divn = explode("</tr>", $ci->getNameImgOtras(10, 11, $folio));
            $nam = $divn[0];
            $image_info = explode(':', $nam)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];

            $divname = explode("</tr>", $ci->getNameImgOtras(10, 12, $folio));
            $name = $divname[0];
            $image_info_o = explode(':', $name)[1];
            $image_extension_o = explode('/', $image_info_o)[1];
            $image_type_o = explode(';', $image_extension_o)[0];

            $pdf->SetWidths(array(97.5, 97.5));
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetAligns(array('C', 'C'));
            $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Botón de Pánico"), iconv('UTF-8', 'windows-1252', "Notificación del Botón de Pánico")));
            $pdf->Row(array("\n\n\n\n\n\n\n\n\n\n\n", ""));

            $pdf->Image($nam, 35, ($pdf->GetY() - 48), 46, 46, $imag_type);
            $pdf->Image($name, 134, ($pdf->GetY() - 48), 46, 46, $imag_type_o);
        }

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(array("Datos del servicio"));
        $pdf->Ln(1);

        $pdf->SetWidths(array(195));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Servicio:");
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        if ($idtservicio != "") {
            $servicios = $ci->listServicios($idtservicio, $otrostservicio);
        } else {
            $servicios = " ";
        }
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $servicios)));

        $showimei = false;
        $showsim = false;
        $divS = explode("-", $idtservicio);
        foreach ($divS as $Sactual) {
            if ($Sactual == '8') {
                $showimei = true;
            }
            if ($Sactual == '9') {
                $showsim = true;
            }
        }

        if ($showimei || $showsim) {
            $titimei = "";
            $tittel = "";
            $imeiant = "";
            $telant = "";
            if ($showimei) {
                $titimei = "Modelo e IMEI anterior:";
                $imeiant = $modeloanterior . " - " . $imeianterior;
            }
            if ($showsim) {
                $tittel = "Telefono anterior:";
                $telant = $simanterior;
            }
            $pdf->SetWidths(array(97.5, 97.5));
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(4.5, $titimei);
            $pdf->SetX(107.5);
            $pdf->Write(4.5, $tittel);
            $pdf->SetX(10);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $imeiant), "\n" . iconv('UTF-8', 'windows-1252', $telant)));
        }

        $pdf->SetWidths(array(195));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Instalador:");
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetLineHeight(4.5);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $instalador)));

        $pdf->title = "Recepción del vehículo";
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetWidths(array(97.5, 97.5));
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetAligns(array('C', 'C'));
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Placas del vehículo"), iconv('UTF-8', 'windows-1252', "Número de serie o VIN")));

        $valplacas = "\nNo existe imagen\n ";
        $valserie = "\nNo existe imagen\n ";
        if ($frentebase != "") {
            $image_info = explode(':', $frentebase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($frentebase, 35, ($pdf->GetY() + 2), 46, 46, $image_type);
            $valplacas = "\n\n\n\n\n\n\n\n\n\n\n";
        }

        if ($seriebase != "") {
            $image_info = explode(':', $seriebase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($seriebase, 134, ($pdf->GetY() + 2), 46, 46, $image_type);
            $valserie = "\n\n\n\n\n\n\n\n\n\n\n";
        }
        $pdf->Row(array($valplacas, $valserie));

        $pdf->Ln(2);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Cableado:"));
        $pdf->Ln(7);


        $pdf->SetWidths(array(97.5, 97.5));
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetAligns(array('C', 'C'));
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Antes de instalación / revisión"), iconv('UTF-8', 'windows-1252', "Después de instalación / revisión")));

        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n\n\n\n\n\n\n\n\n\n\n", ""));
        if ($antesbase != "") {
            $image_info = explode(':', $antesbase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($antesbase, 35, ($pdf->GetY() - 48), 46, 46, $image_type);
        }

        if ($despuesbase != "") {
            $image_info = explode(':', $despuesbase)[1];
            $image_extension = explode('/', $image_info)[1];
            $image_type = explode(';', $image_extension)[0];
            $pdf->Image($despuesbase, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
        }

        $pdf->SetWidths(array(195));
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $divCab = explode("-", $idcableado);
        foreach ($divCab as $cabactual) {
            switch ($cabactual) {
                case '1':
                    $cableado = "Cables sueltos";
                    break;
                case '2':
                    $cableado = "Cables sin aislamiento";
                    break;
                case '3':
                    $cableado = "Empalme de cables excesivo";
                    break;
                case '4':
                    $cableado = "Sin observación";
                    break;
                case '5':
                    $cableado = $otroscableado;
                    break;
                default:
                    $cableado = "";
                    break;
            }
            $pdf->Row(array( chr(149) . " " . iconv('UTF-8', 'windows-1252', $cableado )));
        }

        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(5);
        $otrasimg = 0;
        $imgs = $ci->getImgInsAux($folio);
        foreach ($imgs as $imgactual) {
            $otrasimg++;
        }

        if ($otrasimg > 0) {
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(4.5, iconv('UTF-8', 'windows-1252', "Otras evidencias"));
            $pdf->Ln(6);
            $pdf->SetWidths(array(97.5, 97.5));
            $pdf->Row(array(iconv('UTF-8', 'windows-1252', "Descripción"), iconv('UTF-8', 'windows-1252', "Imagen")));
            foreach ($imgs as $imgactual) {
                $img = $imgactual['imginstalacion'];
                $titulo = $imgactual['titulo'];

                $image_info = explode(':', $img)[1];
                $image_extension = explode('/', $image_info)[1];
                $image_type = explode(';', $image_extension)[0];

                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);

                $pdf->Row(array(chr(149) . " " . iconv('UTF-8', 'windows-1252', $titulo), "\n\n\n\n\n\n\n\n\n\n\n"));
                $pdf->Image($img, 134, ($pdf->GetY() - 48), 46, 46, $image_type);
            }
        }

        if ($observacion_general != "") {
            $pdf->Ln(7);
            $pdf->SetAligns(array('L'));
            $pdf->SetWidths(array(195));
            $pdf->SetLineHeight(4.5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(4.5, "Observaciones en general");
            $pdf->SetX(10);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $obser = str_replace("<corte>", "\n", $observacion_general);
            if (!isset($_GET['b'])) {
                $pdf->Row(array("\n" . iconv('UTF-8', 'windows-1252', $obser )));
            }
        }

        /*$pdf->Ln(50);
        $pdf->SetWidths(Array(195));
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetAligns(Array('C'));
        $pdf->RowNB(Array("\n\n"));
        if ($pdf->firma != "") {
            $pdf->Image($pdf->firma, 77.5, ($pdf->GetY() - 35), 60, 0, 'png');
        }
        $pdf->RowNB(Array(iconv('UTF-8', 'windows-1252',"___________________________________________________________\n Firma del encargado \n".$pdf->encargado)));*/
        $pdf->isFinished = true;
    }
} else {
    //----------------------------------HOJA DE SERVICIO EN BLANCO
    if (isset($_GET['b'])) {
        $idorden = "";
        $fechaservicio = "";
        $horaservicio = "";
        $idcliente = "";
        $cliente = " ";
        $marca = " ";
        $modelo = "";
        $anho = "";
        $color = "";
        $serie = " ";
        $numeconomico = "";
        $km = "";
        $placas = "";
        $iddanhos = "";
        $idmolduras = "1-2-3-4";
        $otrosmolduras = "Otros (específica):";
        $idtablero = "1-2-3-4-5-6";
        $otrostablero = "Otros (específica):";
        $idcableado = "1-2-3-4-5";
        $otroscableado = "Otros (específica):";
        $idccorriente = "1-2-3-4-5";
        $otroscorriente = "Otros (específica):";
        $idtservicio = "";
        $imeianterior = "";
        $simanterior = "";
        $otrostservicio = "";
        $gpsvehiculo = " ";
        $imei = "";
        $numtelefono = "";
        $idinstalador = "";
        $imgcheck = "../img/checknt.png";
        $idaccesorio = "";
        $observaciones = " \n\n\n\n\n\n\n";
        $obswidth = 195;
        $idinstalacion = "";
        $encargado = " ";
        $firma = "";
        $instalador = " ";
        $folio = "";
        $imgfrente = "";
        $frentebase = "";
        $imgnserie = "";
        $seriebase = "";
        $imgtabinicial = "";
        $tableroinibase = "";
        $imgtabfinal = "";
        $tablerofinbase = "";
        $fechaservicio2 = "";
        $idmunicipio = "";
        $idestado = "";
        $calle = "";
        $numint = "";
        $numext = "";
        $localidad = "";

        $imgantesinst = "";
        $antesbase = "";
        $imgdespuesinst = "";
        $despuesbase = "";
    } else {
        $datos = $ci->getOrdenbyId($idservicio);
        foreach ($datos as $actual) {
            $idorden = $actual['idorden'];
            $fechaservicio = $actual['fechaservicio'];
            $horaservicio = $actual['horaservicio'];
            $idcliente = $actual['idcliente'];
            $cliente = $actual['cliente'];
            $marca = $actual['marca'];
            $modelo = $actual['modelo'];
            $anho = $actual['anho'];
            $color = $actual['color'];
            $serie = $actual['serie'];
            $numeconomico = $actual['numeconomico'];
            $km = $actual['km'];
            $placas = $actual['placas'];
            $iddanhos = $actual['iddanhos'];
            $idmolduras = $actual['idmolduras'];
            $otrosmolduras = $actual['otrosmolduras'];
            $idtablero = $actual['idtablero'];
            $otrostablero = $actual['otrostablero'];
            $idcableado = $actual['idcableado'];
            $otroscableado = $actual['otroscableado'];
            $idccorriente = $actual['idccorriente'];
            $otroscorriente = $actual['otroscorriente'];
            $idtservicio = $actual['idtservicio'];
            $modeloanterior = $actual['modeloanterior'];
            $imeianterior = $actual['imeianterior'];
            $simanterior = $actual['simanterior'];
            $otrostservicio = $actual['otrostservicio'];
            $gpsvehiculo = $actual['gpsvehiculo'];
            $imei = $actual['imei'];
            $numtelefono = $actual['numtelefono'];
            $idinstalador = $actual['idinstalador'];
            $idaccesorio = $actual['idaccesorio'];
            $observaciones = $actual['observaciones'];
            $idinstalacion = $actual['idinstalacion'];
            $encargado = $actual['encargado'];
            $firma = $actual['firma'];
            $instalador = $actual['nombre_inst'] . " " . $actual['apellido_paterno'] . " " . $actual['apellido_materno'];
            $folio = $actual['letra_folio'] . $actual['folioinstalacion'];
            $imgfrente = $actual['imgfrentevehiculo'];
            $frentebase = $actual['imgfrentebase'];
            $imgnserie = $actual['imgnserie'];
            $seriebase = $actual['imgseriebase'];
            $imgtabinicial = $actual['imgtabinicial'];
            $tableroinibase = $actual['imgtabinibase'];
            $imgtabfinal = $actual['imgtabfinal'];
            $tablerofinbase = $actual['imgtabfinbase'];
            $imgantesinst = $actual['imgantesinst'];
            $antesbase = $actual['imgantesbase'];
            $imgdespuesinst = $actual['imgdespuesinst'];
            $despuesbase = $actual['imgdespuesbase'];

            $divideF = explode("-", $fechaservicio);
            $fechaservicio2 = $divideF[2] . '/' . $divideF[1] . '/' . $divideF[0] . " " . $horaservicio;
        }
        $imgcheck = "../img/check.png";
        $obswidth = 128;
    }

    $rfc = "";
    $telefono = "";
    $correo = "";
    $direccion = " ";

    if ($idcliente != "0" && $idcliente != "") {
        $datoscliente = $ci->getDatosClientebyID($idcliente);
        foreach ($datoscliente as $clienteactual) {
            $rfc = $clienteactual['rfc'];
            $telefono = $clienteactual['telefono'];
            $correo = $clienteactual['email_gerencia'];
            $calle = $clienteactual['calle'];
            $numint = $clienteactual['numero_interior'];
            $numext = $clienteactual['numero_exterior'];
            $localidad = $clienteactual['localidad'];
            $idmunicipio = $clienteactual['idmunicipio'];
            $idestado = $clienteactual['idestado'];
            $pais = $clienteactual['pais'];
            $municipio = $clienteactual['nombre_municipio'];
            $estado = $clienteactual['nombre_estado'];
        }

        $direccion = "$calle #$numext, Col. $localidad, $municipio, $estado";
    }

    $pdf = new PDF('P', 'mm', 'Letter');

    $noorden = $idorden;
    if ($noorden < 10 && $noorden != "") {
        $noorden = "00$idorden";
    } else if ($noorden >= 10 && $noorden < 100) {
        $noorden = "0$idorden";
    }

    $pdf->imglogo = $imglogo;
    $pdf->colortitulo = $colortitulo;
    $pdf->celdatitulo = $celdatitulo;
    $pdf->colorsubtitulos = $rgbs;
    $pdf->fondosubtitulo = $rgbc;
    $pdf->pagina = $pagina;
    $pdf->correo = $correo;
    $pdf->telefono1 = $telefono1;
    $pdf->telefono2 = $telefono2;
    $pdf->chnum = $chnum;
    $pdf->colorpie = $colorpie;

    $pdf->idorden = $noorden;
    $pdf->fechaservicio = $fechaservicio2;
    $pdf->title =  $titulo;
    $pdf->firma = $firma;
    $pdf->encargado = $encargado;

    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->SetWidths(array(80));
    $pdf->SetLineHeight(8);
    $pdf->RowT(array("Datos del cliente"));
    $pdf->Ln(1);

    $pdf->SetWidths(array(120, 75));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Cliente:");
    $pdf->SetX(130);
    $pdf->Write(4.5, "RFC:");
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . $cliente, "\n" . $rfc));

    $pdf->SetWidths(array(60, 60, 75));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Contacto:");
    $pdf->SetX(70);
    $pdf->Write(4.5, "Tel. Oficina:");
    $pdf->SetX(130);
    $pdf->Write(4.5, "E-mail");
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $encargado), "\n" . iconv("utf-8", "windows-1252", $telefono), "\n" . iconv("utf-8", "windows-1252", $correo)));

    $pdf->SetWidths(array(195));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Direccion:");
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array(iconv("utf-8", "windows-1252", "\n$direccion")));

    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->SetWidths(array(80));
    $pdf->SetLineHeight(8);
    $pdf->RowT(array("Datos de la unidad"));
    $pdf->Ln(1);

    $pdf->SetWidths(array(65, 65, 20, 45));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Marca:");
    $pdf->SetX(75);
    $pdf->Write(4.5, "Modelo:");
    $pdf->SetX(140);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Año"));
    $pdf->SetX(160);
    $pdf->Write(4.5, "Color");
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $marca), "\n" . iconv("utf-8", "windows-1252", $modelo), "\n" . $anho, "\n" . iconv("utf-8", "windows-1252", $color)));

    $pdf->SetWidths(array(65, 65, 35, 30));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Serie:");
    $pdf->SetX(75);
    $pdf->Write(4.5, "Placas:");
    $pdf->SetX(140);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Eco:"));
    $pdf->SetX(175);
    $pdf->Write(4.5, "KM:");
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $serie), "\n" . iconv("utf-8", "windows-1252", $placas), "\n" . iconv("utf-8", "windows-1252", $numeconomico), "\n" . iconv("utf-8", "windows-1252", $km)));

    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->SetWidths(array(80));
    $pdf->SetLineHeight(8);
    $pdf->RowT(array("Datos del equipo"));
    $pdf->Ln(1);

    $pdf->SetWidths(array(65, 65, 65));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Modelo:");
    $pdf->SetX(75);
    $pdf->Write(4.5, "IMEI:");
    $pdf->SetX(140);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "No. Telefónico"));
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $gpsvehiculo), "\n" . iconv("utf-8", "windows-1252", $imei), "\n" . $numtelefono));

    $pdf->SetWidths(array(195));
    $pdf->SetLineHeight(4.5);

    if ($idaccesorio != "") {
        $accesorios = $ci->listAccesorios($idaccesorio);
    } else {
        $accesorios = " ";
    }

    if (!isset($_GET['b'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Accesorios:");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $accesorios)));
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->SetWidths(array(80));
    $pdf->SetLineHeight(8);
    $pdf->RowT(array("Datos del servicio"));
    $pdf->Ln(1);

    $pdf->SetWidths(array(195));
    $pdf->SetLineHeight(4.5);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Servicio:");
    $pdf->SetX(10);
    $pdf->SetFont('Arial', '', 9);
    if ($idtservicio != "") {
        $servicios = $ci->listServicios($idtservicio, $otrostservicio);
    } else {
        $servicios = " ";
    }
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $servicios)));

    $showimei = false;
    $showsim = false;
    $divS = explode("-", $idtservicio);
    foreach ($divS as $Sactual) {
        if ($Sactual == '8') {
            $showimei = true;
        }
        if ($Sactual == '9') {
            $showsim = true;
        }
    }

    if ($showimei || $showsim) {
        $titimei = "";
        $tittel = "";
        $imeiant = "";
        $telant = "";
        if ($showimei) {
            $titimei = "Modelo e IMEI anterior:";
            $imeiant = $modeloanterior . " - " . $imeianterior;
        }
        if ($showsim) {
            $tittel = "Telefono anterior:";
            $telant = $simanterior;
        }
        $pdf->SetWidths(array(97.5, 97.5));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, $titimei);
        $pdf->SetX(107.5);
        $pdf->Write(4.5, $tittel);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $imeiant), "\n" . iconv("utf-8", "windows-1252", $telant)));
    }

    $pdf->SetWidths(array(195));
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, "Instalador:");
    $pdf->SetX(10);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
    $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $instalador)));

    $ref = "";

    if (isset($_GET['b'])) {
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Daños en el vehículo:"));
        $pdf->Ln(7);
        $pdf->Image("../img/carro1.png", 10, $pdf->GetY(), 100);
    }

    $pdf->title = "Recepción del vehículo";
    $pdf->AliasNbPages();
    $pdf->AddPage();

    if (isset($_GET['b'])) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Accesorios a instalar:"));
        $pdf->Ln(10);

        $pdf->SetWidths(array(9, 39.75, 9, 39.75, 9, 39.75, 9, 39.75));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->RowNB(array($pdf->Image($imgcheck, 12.5, $pdf->GetY() - 2, 8) . "\n", iconv("utf-8", "windows-1252", 'Botón de pánico'), $pdf->Image($imgcheck, 61, $pdf->GetY() - 2, 8) . "\n", 'Bocina', $pdf->Image($imgcheck, 109.75, $pdf->GetY() - 2, 8) . "\n", iconv("utf-8", "windows-1252", 'Micrófono'), $pdf->Image($imgcheck, 158.5, $pdf->GetY() - 2, 8) . "\n", 'Corte de corriente combustible'));
        $pdf->RowNB(array($pdf->Image($imgcheck, 12.5, $pdf->GetY() - 2, 8) . "\n\n", 'Sensor de gasolina', $pdf->Image($imgcheck, 61, $pdf->GetY() - 2, 8) . "\n", 'Sensores de puertas', $pdf->Image($imgcheck, 109.75, $pdf->GetY() - 2, 8) . "\n", 'Sensor de impacto', $pdf->Image($imgcheck, 158.5, $pdf->GetY() - 2, 8) . "\n", iconv("utf-8", "windows-1252", 'Cámara')));
        $pdf->RowNB(array($pdf->Image($imgcheck, 12.5, $pdf->GetY() - 2, 8) . "\n\n", iconv("utf-8", "windows-1252", 'Chapa magnética'), $pdf->Image($imgcheck, 61, $pdf->GetY() - 2, 8) . "\n", 'Solo GPS', $pdf->Image($imgcheck, 109.75, $pdf->GetY() - 2, 8) . "\n", iconv("utf-8", "windows-1252", 'Solo revisión'), "", ""));
    }

    $pdf->SetWidths(array(48.75, 48.75, 48.75, 48.75));
    $pdf->SetLineHeight(4);
    if (!isset($_GET['b'])) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Daños en el vehículo:"));
        $pdf->Ln(6);

        $pdf->SetFont('Arial', 'B', 9);
        $divD = explode("-", $iddanhos);
        $print = array_chunk($divD, 4);

        foreach ($print as $actual) {
            $row = array();
            $checkX = 12.5;
            foreach ($actual as $damactual) {
                switch ($damactual) {
                    case '1':
                        $danho = "Parachoques delantero";
                        break;
                    case '2':
                        $danho = "Parachoques trasero";
                        break;
                    case '3':
                        $danho = "Lateral izquierdo";
                        break;
                    case '4':
                        $danho = "Lateral derecho";
                        break;
                    case '5':
                        $danho = "Parabrisas";
                        break;
                    case '6':
                        $danho = "Cajuela";
                        break;
                    case '7':
                        $danho = "Cofre";
                        break;
                    case '8':
                        $danho = "Techo";
                        break;
                    case '9':
                        $danho = "Sin daños";
                        break;
                    default:
                        $danho = "";
                        break;
                }

                $row[] = ($pdf->Image($imgcheck, $checkX, $pdf->GetY() - 2, 8) . "          " . iconv("utf-8", "windows-1252", $danho) . "\n\n");
                $checkX += 48.75;
            }
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->RowNB($row);
        }

        $pdf->Ln(2);
    }

    if ($otrosmolduras == '') {
        $otrosmolduras = "Otros (específica)";
    }

    if ($otrostablero == "") {
        $otrostablero = "Otros (específica)";
    }

    if ($otroscableado == "") {
        $otroscableado = "Otros (específica)";
    }

    if ($otroscorriente == "") {
        $otroscorriente = "Otros (específica)";
    }

    $count = 1;
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Molduras del vehiculo:"));
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 9);
    $divM = explode("-", $idmolduras);
    $arrM = array_chunk($divM, 4);

    foreach ($arrM as $actual) {
        $rowM = array();
        $checkX = 12.5;
        foreach ($actual as $molactual) {
            switch ($molactual) {
                case '1':
                    $moldura = "Molduras dañadas";
                    break;
                case '2':
                    $moldura = "Tornillos, grapas o pijas";
                    break;
                case '3':
                    $moldura = "Sin observación";
                    break;
                case '4':
                    $moldura = $otrosmolduras;
                    break;
                default:
                    $moldura = "";
                    break;
            }

            $rowM[] = ($pdf->Image($imgcheck, $checkX, $pdf->GetY() - 2, 8) . "          " . iconv("utf-8", "windows-1252", $moldura) . "\n\n");
            $checkX += 48.75;
        }
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->RowNB($rowM);
    }

    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Tablero del vehículo:"));
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 9);
    $divT = explode("-", $idtablero);
    $arrT = array_chunk($divT, 4);

    foreach ($arrT as $actual) {
        $rowT = array();
        $checkX = 12.5;

        foreach ($actual as $tabactual) {
            switch ($tabactual) {
                case '1':
                    $tablero = "Testigos encendidos";
                    break;
                case '2':
                    $tablero = "No enciende";
                    break;
                case '3':
                    $tablero = "No marca gasolina";
                    break;
                case '4':
                    $tablero = "Arnés o contra arnés dañado";
                    break;
                case '5':
                    $tablero = "Sin observación";
                    break;
                case '6':
                    $tablero = $otrostablero;
                    break;
                default:
                    $tablero = "";
                    break;
            }
            $rowT[] = ($pdf->Image($imgcheck, $checkX, $pdf->GetY() - 2, 8) . "          " . iconv("utf-8", "windows-1252", $tablero) . "\n\n");
            $checkX += 48.75;
        }
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->RowNB($rowT);
    }

    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Cableado interno del tablero:"));
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 9);
    $divCab = explode("-", $idcableado);
    $arrCab = array_chunk($divCab, 4);

    foreach ($arrCab as $actual) {
        $rowCab = array();
        $checkX = 12.5;
        foreach ($actual as $cabactual) {
            switch ($cabactual) {
                case '1':
                    $cableado = "Cables sueltos";
                    break;
                case '2':
                    $cableado = "Cables sin aislamiento";
                    break;
                case '3':
                    $cableado = "Empalme de cables excesivo";
                    break;
                case '4':
                    $cableado = "Sin observación";
                    break;
                case '5':
                    $cableado = $otroscableado;
                    break;
                default:
                    $cableado = "";
                    break;
            }
            $rowCab[] = ($pdf->Image($imgcheck, $checkX, $pdf->GetY() - 2, 8) . "          " . iconv("utf-8", "windows-1252", $cableado) . "\n\n");
            $checkX += 48.75;
        }
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->RowNB($rowCab);
    }

    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
    $pdf->Write(4.5, iconv("utf-8", "windows-1252", "Sistema de corta corriente"));
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 9);
    $divcorriente = explode("-", $idccorriente);
    $arrCorr = array_chunk($divcorriente, 4);

    foreach ($arrCorr as $actual) {
        $rowCorr = array();
        $checkX = 12.5;
        foreach ($actual as $corrienteactual) {
            switch ($corrienteactual) {
                case '1':
                    $ccorriente = "Alarma con corta corriente";
                    break;
                case '2':
                    $ccorriente = "GPS con corta corriente activo";
                    break;
                case '3':
                    $ccorriente = "Switch corta corriente";
                    break;
                case '4':
                    $ccorriente = "No cuenta";
                    break;
                case '5':
                    $ccorriente = $otroscorriente;
                    break;
                default:
                    $ccorriente = "";
                    break;
            }

            $rowCorr[] = ($pdf->Image($imgcheck, $checkX, $pdf->GetY() - 2, 8) . "          " . iconv("utf-8", "windows-1252", $ccorriente) . "\n\n");
            $checkX += 48.75;
        }
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->RowNB($rowCorr);
    }

    $pdf->Ln(10);
    if ($observaciones != "") {
        $pdf->SetWidths(array($obswidth));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(4.5, "Observaciones");
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        $obser = str_replace("<corte>", "\n", $observaciones);
        if (!isset($_GET['b'])) {
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Row(array("\n" . iconv("utf-8", "windows-1252", $obser)));
        }
    }
}


if (isset($_GET['s'])) {
    $pdf->Output('Hoja_Servicio_' . $noorden . '.pdf', 'I');
} else if (isset($_POST['id'])) {
    require_once '../com.sine.modelo/SendMail.php';
    $m = new SendMail();
    $ch1 = $_POST['ch1'];
    $ch2 = $_POST['ch2'];
    $ch3 = $_POST['ch3'];
    $ch4 = $_POST['ch4'];
    $correoalt = $_POST['correoalt'];

    $m->setIdfactura($idorden);
    $m->setChmail1($ch1);
    $m->setChmail2($ch2);
    $m->setChmail3($ch3);
    $m->setChmail4($ch4);
    $m->setMailalt1($correoalt);
    $m->setPdfstring($pdf->Output('S'));
    $send = $ci->mail($m);
    echo $send;
} else if ($_GET['b']) {
    $pdf->Output('Hoja_Servicio.pdf', 'I');
}
