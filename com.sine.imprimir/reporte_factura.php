<?php

require_once '../com.sine.modelo/Reportes.php';
require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.controlador/ControladorReportes.php';
require '../pdf/fpdf/fpdf.php';

class PDF extends FPDF {

    var $widths;
    var $aligns;
    var $lineHeight;
    var $Tfolio;
    var $iddatos;
    var $chfirmar;
    var $isFinished;
    var $tipofactura;
    var $heightB = 0;
    var $ycliente;

    function SetWidths($w) {
        $this->widths = $w;
    }

    function SetAligns($a) {
        $this->aligns = $a;
    }

    function SetLineHeight($h) {
        $this->lineHeight = $h;
    }

    function Row($data) {
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
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->SetTextColor(0, 0, 0);
            $fill = 'D';
            if ($i == 6) {
                $fill = 'FD';
                $this->SetTextColor(255, 255, 255);
            }
            $this->Rect($x, $y, $w, $h, $fill);
            //Print the text

            $this->MultiCell($w, $lh, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowT($data) {
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

    function RowC($data) {
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
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h, 'F');
            //Print the text
            $this->MultiCell($w, $lh, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowNBCount($data) {
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
            //$this->RoundedRect($x, $y, $w, $h, 2, 'F');
            //$this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 4.5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
        $this->heightB += $h;
    }

    function RowNBTitle($data) {
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
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->RoundedRect($x, $y, $w, $h, 2, 'F');
            //$this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, $lh, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowNB($data) {
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

    function RowNBC($data) {
        // number of line
        $nb = 0;
        // loop each data to find out greatest line number in a row.
        for ($i = 0; $i < count($data); $i++) {
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i])); //liena 258
        }

        //multiply number of line with line height. This will be the height of current row
        $h = $this->lineHeight * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            $w = $this->widths[$i];//linea 268
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

    function RowRTitle($data) {
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
            $this->RoundedRect($x, $y, $w, $h, 4, 'F');
            //Print the text
            $this->MultiCell($w, $h2, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowR($data) {
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
            //$this->SetFillColor(255, 255, 255);
            $this->RoundedRect($x, $y, $w, $h, 2, 'FD');
            //Print the text
            $this->MultiCell($w, $h2, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
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

    function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234') {
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

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1 * $this->k, ($h - $y1) * $this->k, $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    function Header() {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('6');
        foreach ($encabezado as $actual) {
            $titulo = $actual['tituloencabezado'];
            $colortitulo = $actual['colortitulo'];
            $rgbt = explode("-", $cc->hex2rgb($colortitulo));
            $colorcuadro = $actual['colorceltitulo'];
            $rgbc = explode("-", $cc->hex2rgb($colorcuadro));
            $imglogo = $actual['imglogo'];
        }

        $this->SetFont('Arial', '', 19);
        $this->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $this->SetTextColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $logo = "../img/logo/$imglogo";
        $dimensiones = getimagesize($logo);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $height = ($height * 20) / $width;
        if ($height > 25) {
            $height = 25;
        }
        $this->Cell(25, 5, $this->Image($logo, $this->GetX() + 2.5, $this->GetY(), 20, $height), 0, 0, 'C', false);
        $this->RoundedRect(35, $this->GetY(), 170, 8, 4, 'F');
        $this->SetX(38);
        $this->Write(8, $titulo);

        $this->Ln(26);
    }

    function Footer() {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('6');
        foreach ($encabezado as $actual) {
            $pagina = $actual['pagina'];
            $correo = $actual['correo'];
            $telefono1 = $actual['telefono1'];
            $telefono2 = $actual['telefono2'];
            $chnum = $actual['numpag'];
            $colorpie = $actual['colorpie'];
            $rgb = explode("-", $cc->hex2rgb($colorpie));
        }
        $pagin = "";
        if ($chnum == '1') {
            $pagin = utf8_decode('Pagina ' . $this->PageNo() . ' de {nb}');
        }
        $this->SetY(-18);
        if ($this->isFinished) {
            $chfirmar = $this->chfirmar;
            if ($chfirmar == '1') {
                $datos = $cc->getFirma($this->iddatos);
                $div = explode("</tr>", $datos);
                $rfc = $div[0];
                $firma = $div[1];
                if (file_exists("../temporal/$rfc/$firma")) {
                    $this->Image("../temporal/$rfc/$firma", 75, ($this->GetY() - 25), 60);
                }
            }
        }
        $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(65, 4, $pagina, 0, 0, 'L');
        $phone = "Tel: " . $telefono1;
        if ($telefono2 != "") {
            $this->Cell(65, 4, '', 0, 0, 'C');
            $this->Cell(65, 4, "Tel: " . $telefono1, 0, 0, 'R');
            $phone = "Tel: " . $telefono2;
        }
        $this->Ln(4);
        $this->Cell(65, 4, $correo, 0, 0, 'L');
        $this->Cell(65, 4, $pagin, 0, 0, 'C');
        $this->Cell(65, 4, $phone, 0, 0, 'R');
    }

    function myCell($w, $h, $x, $t) {
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

    function myCellD($w, $h, $x, $t) {
        $height = $h / 3;
        $first = $height + 2;
        $second = $height + $height + $height + 3;
        $len = strlen($t);
        if ($len > 27) {
            $txt = str_split($t, 27);
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

}

setlocale(LC_MONETARY, 'es_MX.UTF-8');

$cf = new ControladorReportes();
$f = new Reportes();
$fechainicio = $_GET['fechainicio'];
$fechafin = $_GET['fechafin'];
$clienteB = $_GET['cliente'];
$estadoB = $_GET['estado'];
$datos = $_GET['datos'];
$tipo = $_GET['tipo'];
$monedaR = $_GET['moneda'];

$idmonedaC = 1;
if ($monedaR != "") {
    $idmonedaC = $monedaR;
}

function getNombreMoneda($idMoneda)
{
    switch ($idMoneda) {
        case 1:
            return "MXN"; // Peso Mexicano
        case 2:
            return "USD"; // Dólar Estadounidense
        case 3:
            return "EUR"; // Euro
        default:
            return "Desconocido";
    }
}
$cmonedaT = getNombreMoneda($idmonedaC);

$f->setFechainicio($fechainicio);
$f->setFechafin($fechafin);
$f->setIdcliente($clienteB);
$f->setEstado($estadoB);
$f->setDatos($datos);
$f->setTipo($tipo);

if ($estadoB == '1') {
    $est = 'Pagadas';
} else if ($estadoB == '2') {
    $est = 'Pendientes';
} else if ($estadoB == '3') {
    $est = 'Canceladas';
} else if ($estadoB == '4') {
    $est = 'Parciales';
} else {
    $est = '';
}
$divFI = explode("-", $fechainicio);
$divFF = explode("-", $fechafin);
$mI = $cf->translateMonth($divFI[1]);
$mF = $cf->translateMonth($divFF[1]);
$fechainicio = $divFI[2] . '/' . $mI . '/' . $divFI[0];
$fechafin = $divFF[2] . '/' . $mF . '/' . $divFF[0];

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('6');
foreach ($encabezado as $actual) {
    $colorcuadro = $actual['colorcuadro'];
    $rgbc = explode("-", $cc->hex2rgb($colorcuadro));
    $colorsubtitulos = $actual['colorsubtitulos'];
    $rgbs = explode("-", $cc->hex2rgb($colorsubtitulos));
    $colorhtabla = $actual['colorhtabla'];
    $rgbt = explode("-", $cc->hex2rgb($colorhtabla));
    $colortittabla = $actual['colortittabla'];
    $rgbtt = explode("-", $cc->hex2rgb($colortittabla));
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', '', 15);
$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
$pdf->SetWidths(Array(195));
$pdf->SetLineHeight(8);
$pdf->RowT(Array('Facturas generadas en el periodo:'));
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(23, 23, 124);
$pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
$pdf->SetX(80);
$pdf->Write(6, 'Del ' . $fechainicio . ' al ' . $fechafin);
$pdf->Ln(8);


$pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
$pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
$pdf->SetFont('Arial', 'B', 9); 
$pdf->SetWidths(Array(12, 20, 20, 25, 25, 16, 16, 18, 20, 20)); 
$pdf->SetLineHeight(5);
$pdf->RoundedRect(10, $pdf->GetY(), 190, 9, 4, 'FD');
$pdf->RowNBC(Array("Folio", "Fecha creacion", "Emisor", "Cliente", "RFC", "Tipo", iconv("utf-8", "windows-1252", "Estado"), "Met.pago", "For.pago","Total"));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9); 



$ivaperiodo = 0;
$retperiodo = 0;
$descperiodo = 0;
$totalPeriodo = 0;
$totalPendientes = 0;
$totalCanceladas = 0;
$totalPagadas = 0;

$nombre_cliente = "";

if ($tipo == "" || $tipo == '1' || $tipo == '3') {
    $facturas = $cf->getReporteFactura($f);
    foreach ($facturas as $reporteactual) {
        $fid = $reporteactual['iddatos_factura'];
        $folio = $reporteactual['letra'] . $reporteactual['folio_interno_fac'];
        $fecha = $reporteactual['fecha_creacion'];
        $emisor = $reporteactual['factura_rzsocial'];
        $nombre_cliente = $reporteactual['rzreceptor'];
        $rfc = $reporteactual['rfcreceptor'];
        $estado = $reporteactual['status_pago'];
        $subtotal = $reporteactual['subtotal'];
        $iva = $reporteactual['subtotaliva'];
        $ret = $reporteactual['subtotalret'];
        $totaldescuentos = $reporteactual['totaldescuentos'];
        $total = $reporteactual['totalfactura'];
        $tipofactura = $reporteactual['tipofactura'];
        $tcambio = $reporteactual['tcambio'];
        $monedaF = $reporteactual['id_moneda'];
        $cmoneda = $reporteactual['moneda'];
        //n
        $metodopago = $reporteactual['metodo_pago'];
        $formapago = $reporteactual['forma_pago'];

        $divideF = explode("-", $fecha);
        $mes = $cf->translateMonth($divideF[1]);
        $fecha = $divideF[2] . '/' . $mes . '/' . $divideF[0];

        if ($estado == '1') {
            $estadofac = 'Pagada';
            $fillcolor = $pdf->SetFillColor(19, 202, 1);
            $totalPagadas += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        } else if ($estado == '2') {
            $estadofac = 'Pendiente';
            $fillcolor = $pdf->SetFillColor(255, 0, 0);
            $totalPendientes += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        } else if ($estado == '3') {
            $estadofac = 'Cancelada';
            $fillcolor = $pdf->SetFillColor(255, 181, 67);
            $totalCanceladas += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        } else if ($estado == '4') {
            $estadofac = 'Pago Parcial';
            $fillcolor = $pdf->SetFillColor(2, 231, 239);
            $total = $cf->restanteParcial($fid, $total,'f');
            $totalPendientes += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        }

        if ($tipofactura == '1') {
            $type = "Factura";
        } else if ($tipofactura == '2') {
            $type = "Recibo";
        }

        $diviva = explode("<impuesto>", $iva);
        foreach ($diviva as $ivan) {
            $traslados = $ivan;
            $divt = explode("-", $traslados);
            $ivaperiodo += $cf->totalDivisa($divt[0], $tcambio, $idmonedaC, $monedaF);
        }

        $divret = explode("<impuesto>", $ret);
        foreach ($divret as $retn) {
            $retenciones = $retn;
            $divr = explode("-", $retenciones);
            $retperiodo += $cf->totalDivisa($divr[0], $tcambio, $idmonedaC, $monedaF);
        }
        $descperiodo += $cf->totalDivisa($totaldescuentos, $tcambio, $idmonedaC, $monedaF);
        $totalPeriodo += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);

        //$pdf->Row(Array(utf8_decode($folio), utf8_decode($fecha), utf8_decode($emisor), utf8_decode($nombre_cliente), utf8_decode($rfc), utf8_decode($type), utf8_decode($estadofac), utf8_decode('$ ' . number_format($total, 2, '.', ',') . ' ' . $cmoneda, iconv("utf-8", "windows-1252", $metodopago), iconv("utf-8", "windows-1252", $formapago))));
        $pdf->Row(Array(
            iconv("utf-8", "windows-1252", $folio),
            iconv("utf-8", "windows-1252", $fecha),
            iconv("utf-8", "windows-1252", $emisor),
            iconv("utf-8", "windows-1252", $nombre_cliente),
            iconv("utf-8", "windows-1252", $rfc),
            iconv("utf-8", "windows-1252", $type),
            iconv("utf-8", "windows-1252", $estadofac),
            iconv("utf-8", "windows-1252", $metodopago),
            iconv("utf-8", "windows-1252", $formapago),
            iconv("utf-8", "windows-1252", '$ ' . number_format($total, 2, '.', ',') . ' ' . $cmoneda),
            
        ));
        
    }
}

if ($tipo == '2' || $tipo == '3') {
    $facturas = $cf->getReporteCarta($f);
    foreach ($facturas as $reporteactual) {
        $fid = $reporteactual['idfactura_carta'];
        $folio = $reporteactual['letra'] . $reporteactual['foliocarta'];
        $fecha = $reporteactual['fecha_creacion'];
        $emisor = $reporteactual['factura_rzsocial'];
        $nombre_cliente = $reporteactual['rzreceptor'];
        $rfc = $reporteactual['rfcreceptor'];
        $estado = $reporteactual['status_pago'];
        $subtotal = $reporteactual['subtotal'];
        $iva = $reporteactual['subtotaliva'];
        $ret = $reporteactual['subtotalret'];
        $totaldescuentos = $reporteactual['totaldescuentos'];
        $total = $reporteactual['totalfactura'];
        $tcambio = $reporteactual['tcambio'];
        $monedaF = $reporteactual['id_moneda'];
        $cmoneda = $reporteactual['c_moneda'];

        $divideF = explode("-", $fecha);
        $mes = $cf->translateMonth($divideF[1]);
        $fecha = $divideF[2] . '/' . $mes . '/' . $divideF[0];

        if ($estado == '1') {
            $estadofac = 'Pagada';
            $fillcolor = $pdf->SetFillColor(19, 202, 1);
            $totalPagadas += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        } else if ($estado == '2') {
            $estadofac = 'Pendiente';
            $fillcolor = $pdf->SetFillColor(255, 0, 0);
            $totalPendientes += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        } else if ($estado == '3') {
            $estadofac = 'Cancelada';
            $fillcolor = $pdf->SetFillColor(255, 181, 67);
            $totalCanceladas += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        } else if ($estado == '4') {
            $estadofac = 'Pago Parcial';
            $fillcolor = $pdf->SetFillColor(2, 231, 239);
            $total = $cf->restanteParcial($fid, $total,'c');
            $totalPendientes += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);
        }

        $diviva = explode("<impuesto>", $iva);
        foreach ($diviva as $ivan) {
            $traslados = $ivan;
            $divt = explode("-", $traslados);
            $ivaperiodo += $cf->totalDivisa($divt[0], $tcambio, $idmonedaC, $monedaF);
        }

        $divret = explode("<impuesto>", $ret);
        foreach ($divret as $retn) {
            $retenciones = $retn;
            $divr = explode("-", $retenciones);
            $retperiodo += $cf->totalDivisa($divr[0], $tcambio, $idmonedaC, $monedaF);
        }
        $descperiodo += $cf->totalDivisa($totaldescuentos, $tcambio, $idmonedaC, $monedaF);
        $totalPeriodo += $cf->totalDivisa($total, $tcambio, $idmonedaC, $monedaF);

        $pdf->Row(Array(utf8_decode($folio), utf8_decode($fecha), utf8_decode($emisor), utf8_decode($nombre_cliente), utf8_decode($rfc), utf8_decode('Carta'), utf8_decode($estadofac), utf8_decode('$ ' . number_format($total, 2, '.', ',') . ' ' . $cmoneda)));
        //$pdf->Row(Array(utf8_decode($folio), utf8_decode($fecha), utf8_decode($emisor), utf8_decode($nombre_cliente), utf8_decode($rfc), utf8_decode($type), utf8_decode($metodo_pago), utf8_decode($forma_pago), utf8_decode($estadofac), utf8_decode('$ ' . number_format($total, 2, '.', ',') . ' ' . $cmoneda)));

    }
}

$pdf->Ln(3);
$pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
$pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
$pdf->SetWidths(Array(45, 45, 45));
$pdf->SetLineHeight(6);
$pdf->SetX(70);
$pdf->RowR(Array('Total Facturas Pagadas', 'Total Facturas Pendientes', 'Total Facturas Canceladas'));

$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(70);
$pdf->RowR(Array(utf8_decode('$ ' . number_format($totalPagadas, 2, '.', ',') . ' ' . $cmonedaT), utf8_decode('$ ' . number_format($totalPendientes, 2, '.', ',') . ' ' . $cmonedaT), utf8_decode('$ ' . number_format($totalCanceladas, 2, '.', ',') . ' ' . $cmonedaT)));

if ($ivaperiodo > 0) {
    $pdf->Ln(8);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
    $pdf->Cell(40, 8, 'Impuestos Trasladados', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 8, utf8_decode('$ ' . number_format($ivaperiodo, 2, '.', ',')), 1, 0, 'C', 0);
}
if ($retperiodo > 0) {
    $pdf->Ln(8);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
    $pdf->Cell(40, 8, 'Impuestos Retenidos', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 8, utf8_decode('$ ' . number_format($retperiodo, 2, '.', ',')), 1, 0, 'C', 0);
}
if ($descperiodo > 0) {
    $pdf->Ln(8);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
    $pdf->Cell(40, 8, 'Total Descuentos', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 8, utf8_decode('$ ' . number_format($descperiodo, 2, '.', ',')), 1, 0, 'C', 0);
}

$pdf->Ln(8);
$pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
$pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
$pdf->Cell(120, 8, '', 0, 0, 'C', 0);
$pdf->Cell(40, 8, 'Total del Periodo en ' . $cmonedaT, 1, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(35, 8, utf8_decode('$ ' . number_format($totalPeriodo, 2, '.', ',')), 1, 0, 'C', 0);

$cliente = "";
if ($clienteB != "") {
    $cliente = $nombre_cliente;
}
$pdf->Output('Reporte' . $fechainicio . '-' . $fechafin . '' . $cliente . '' . $est . '.pdf', 'I');
