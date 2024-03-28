<?php

require_once '../com.sine.modelo/Reportes.php';
require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.controlador/ControladorReportes.php';
require '../pdf/fpdf/fpdf.php';
require_once '../vendor/numeroaletras/NumeroALetras.php';

class PDF extends FPDF {

    var $widths;
    var $aligns;
    var $styles;
    var $rowtextcolor;
    var $sizes;
    var $lineHeight;
    var $rowborder;
    var $borderfill;
    var $Tfolio;
    var $rfc;
    var $firma;
    var $nmfirma;
    var $chfirmar;
    var $isFinished;
    var $tipofactura;
    var $heightB = 0;
    var $ycliente;
    var $rgbfd0;
    var $rgbfd1;
    var $rgbfd2;

    function SetWidths($w) {
        $this->widths = $w;
    }

    function SetAligns($a) {
        $this->aligns = $a;
    }

    function SetStyles($s = '') {
        $this->styles = $s;
    }

    function setRowColorText($t = "#000000") {
        $this->rowtextcolor = $t;
    }

    function SetSizes($sz = 9) {
        $this->sizes = $sz;
    }

    function SetLineHeight($h) {
        $this->lineHeight = $h;
    }

    function SetRowBorder($b = 'NB', $f = 'D') {
        $this->rowborder = $b;
        $this->borderfill = $f;
    }

    function Row($data) {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
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
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : (isset($this->aligns[0]) ? $this->aligns[0] : 'L');
            $s = isset($this->styles[$i]) ? $this->styles[$i] : (isset($this->styles[0]) ? $this->styles[0] : '');
            $tc = isset($this->rowtextcolor[$i]) ? $this->rowtextcolor[$i] : (isset($this->rowtextcolor[0]) ? $this->rowtextcolor[0] : "#000000");
            $sz = isset($this->sizes[$i]) ? $this->sizes[$i] : 9;
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            if ($this->rowborder == 'B') {
                $this->Rect($x, $y, $w, $h, $this->borderfill);
            } else if ($this->rowborder == 'R') {
                $this->RoundedRect($x, $y, $w, $h, 2, $this->borderfill);
            }

            //Print the text
            $this->SetFont('Arial', $s, $sz);
            $rgb = explode("-", $cc->hex2rgb($tc));
            $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
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

    function RowNBC($data) {
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
            if ($i == 1 || $i == 3) {
                $this->SetFillColor(255, 255, 255);
            } else {
                $this->SetFillColor(216, 216, 216);
            }
            $this->RoundedRect($x, $y, $w, $h, 2, 'F');
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
        $encabezado = $cc->getDatosEncabezado('1');
        foreach ($encabezado as $actual) {
            $titulo = $actual['tituloencabezado'];
            $colortitulo = $actual['colortitulo'];
            $rgbt = explode("-", $cc->hex2rgb($colortitulo));
            $colorcuadro = $actual['colorceltitulo'];
            $rgbc = explode("-", $cc->hex2rgb($colorcuadro));
            $imglogo = $actual['imglogo'];
            $logo = explode("/", $imglogo);
        }
        if ($this->tipofactura == '2') {
            $titulo = "Nota de Remision";
        }

        $this->SetFont('Arial', '', 20);
        $this->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $this->SetTextColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $logo = "../img/logo/$logo[0]";
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
        $this->Write(8, $titulo);

        $this->SetX(160);
        $this->RoundedRect(160, $this->GetY(), 45, 8, 4, 'F');
        $this->SetX(173.5);
        $this->Write(8, 'Folio');

        $this->SetY(18);
        $this->SetX(160);
        $this->SetTextColor(255, 0, 0);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(45, 8, utf8_decode($this->Tfolio), 0, 0, 'C', false);

        $this->Ln(20);
    }

    function Footer() {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('1');
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
                $firma = "../temporal/" . $this->rfc . "/" . $this->firma;
                if (file_exists($firma)) {
                    $this->Image($firma, 75, ($this->GetY() - 25), 60);
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
} else {
    $est = '';
}

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('1');
foreach ($encabezado as $actual) {
    $colorcuadro = $actual['colorcuadro'];
    $rgbc = explode("-", $cc->hex2rgb($colorcuadro));
    $colorsubtitulos = $actual['colorsubtitulos'];
    $rgbs = explode("-", $cc->hex2rgb($colorsubtitulos));
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

$pdf = new PDF('P', 'mm', 'Letter');

if ($tipo == "" || $tipo == '1' || $tipo == '3') {
    $facturas = $cf->getReporteFactura($f);
    foreach ($facturas as $reporteactual) {

        $id_factura = $reporteactual['iddatos_factura'];
        $nombre_cliente = $reporteactual['factura_rzsocial'];

        $repfacturas = $cf->getFacturas($id_factura);
        foreach ($repfacturas as $facturaactual) {
            $idcliente = $facturaactual['idcliente'];
            $nombreCliente = $facturaactual['rzreceptor'];
            $rfccliente = $facturaactual['rfcreceptor'];
            $cpcliente = $facturaactual['cpreceptor'];
            $regcliente = $facturaactual['regfiscalreceptor'];
            $cuso = $facturaactual['id_uso_cfdi'];
            $descripcionuso = $facturaactual['uso_cfdi'];
            $folio = $facturaactual['letra'] . $facturaactual['folio_interno_fac'];
            $tipocomprobante = $facturaactual['id_tipo_comprobante'] . ' ' . $facturaactual['tipo_comprobante'];
            $nombre = $facturaactual['factura_rzsocial'];
            $rfc = $facturaactual['factura_rfcemisor'];
            $razonsocial = $facturaactual['factura_rzsocial'];
            $regimen = $facturaactual['factura_clvregimen'] . ' ' . $facturaactual['factura_regimen'];
            $cp = $facturaactual['factura_cpemisor'];
            $fecha_creacion = $facturaactual['fecha_creacion'];
            $cadenaoriginal = $facturaactual['cadenaoriginal'];
            $certSAT = $facturaactual['nocertificadosat'];
            $certcfdi = $facturaactual['nocertificadocfdi'];
            $uuid = $facturaactual['uuid'];
            $selloSat = $facturaactual['sellosat'];
            $sellocfdi = $facturaactual['sellocfdi'];
            $fechatimbrado = $facturaactual['fechatimbrado'];
            $qrcode = $facturaactual['qrcode'];
            $c_metodo = $facturaactual['id_metodo_pago'];
            $des_metodo = $facturaactual['metodo_pago'];
            //$c_pago = $facturaactual['c_pago'];
            $des_pago = $facturaactual['forma_pago'];
            $c_moneda = $facturaactual['moneda'];
            $subtotal = $facturaactual['subtotal'];
            $totdescuentos = $facturaactual['totaldescuentos'];
            $totalfactura = $facturaactual['totalfactura'];
            $estado = $facturaactual['status_pago'];
            $chfirmar = $facturaactual['chfirmar'];
            $iddatosfacturacion = $facturaactual['iddatosfacturacion'];
            $tipofactura = $facturaactual['tipofactura'];
            $cfdisrel = $facturaactual['cfdisrel'];
            $tagfactura = $facturaactual['tagfactura'];
            $divideF = explode("-", $fecha_creacion);
            $mes = $cf->translateMonth($divideF[1]);
            $fecha_creacion2 = $divideF[2] . '/' . $mes . '/' . $divideF[0];
        }
        $sine = $cf->getDatosFacturacionbyId($iddatosfacturacion);
        foreach ($sine as $sineactual) {
            $numcertificado = $sineactual['numcsd'];
            $firma = $sineactual['firma'];
        }

        //$cliente = $cf->getDatosCliente($idcliente);

        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('1');
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
        $pdf->Tfolio = $folio;

        $pdf->tipofactura = $tipofactura;


        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->chfirmar = $chfirmar;
        $pdf->rfc = $rfc;
        $pdf->firma = $firma;

        $pdf->SetFont('Arial', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Datos del Emisor"));

        $pdf->SetWidths(Array(15, 94.5, 25, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($razonsocial), '', utf8_decode($numcertificado)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($rfc), '', utf8_decode($regimen)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($fecha_creacion2), '', utf8_decode($tipocomprobante)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(32, 77.5, 25, 60));
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($cp), '', ''));

        $heightdatos = $pdf->heightB;

        $pdf->SetFillColor(216, 216, 216);
        $pdf->RoundedRect(10, 45, 195, $heightdatos, 2, 'F');
        $pdf->SetWidths(Array(15, 94.5, 25, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetY(45);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->Write(5, 'Nombre');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'No Certificado');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($razonsocial), '', utf8_decode($numcertificado)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->Write(5, 'RFC');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'Regimen Fiscal');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($rfc), '', utf8_decode($regimen)));


        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->Write(5, 'Fecha');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'T. Comprobante');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($fecha_creacion2), '', utf8_decode($tipocomprobante)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths(Array(32, 77.5, 25, 60));
        $pdf->SetTextColor(23, 23, 124);
        $pdf->Write(5, 'Lugar de Expedicion');
        $pdf->SetX(119.5);
        $pdf->Write(5, '');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($cp), '', ''));
        $pdf->Ln(0.5);

        $pdf->heightB = 0;

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Datos del Cliente"));
        $direccion = "CP. $cpcliente";
        if ($idcliente != '0') {
            $direccion = $cf->getDireccionCliente($idcliente, $id_factura);
        }

        $pdf->ycliente = $pdf->GetY();

        $pdf->SetWidths(Array(17, 94.5, 23, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($nombreCliente), '', utf8_decode($cuso . ' ' . $descripcionuso)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($rfccliente), '', ''));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(17, 174.5));
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($direccion)));

        $heightcliente = $pdf->heightB;

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, ($pdf->ycliente + 0.7), 195, $heightcliente, 2, 'F');

        $pdf->SetY(($pdf->ycliente + 0.7));
        $pdf->SetWidths(Array(17, 94.5, 23, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Nombre');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'Uso de CFDI');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($nombreCliente), '', utf8_decode($cuso . ' ' . $descripcionuso)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'RFC');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($rfccliente), '', ''));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Direccion');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetWidths(Array(17, 174.5));
        $pdf->SetLineHeight(4.5);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($direccion)));
        $pdf->Ln(1);

        $pdf->heightB = 0;

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
        $pdf->SetX(92.5);
        $pdf->Write(6, 'CONCEPTOS');
        $pdf->Ln(7);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(Array(15, 20, 25, 46, 45, 22, 22));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(15, 6, 'Cantidad', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Clave Fiscal', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Unidad', 1, 0, 'C', 1);
        $pdf->Cell(46, 6, 'Descripcion', 1, 0, 'C', 1);
        $pdf->Cell(45, 6, 'Observaciones', 1, 0, 'C', 1);
        $pdf->Cell(22, 6, 'Precio', 1, 0, 'C', 1);
        $pdf->Cell(22, 6, 'Importe', 1, 0, 'C', 1);
        $pdf->Ln(6);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetRowBorder('B');
        $pdf->SetAligns(array('C'));

        $detallefactura = $cf->getDetalle($tagfactura);
        $iva = 0;
        $ieps = 0;
        $retiva = 0;
        $retieps = 0;
        $isr = 0;
        foreach ($detallefactura as $detalleactual) {
            $pdf->SetFont('Arial', '', 9);
            $claveFiscal = $detalleactual['clvfiscal'];
            $precioV = $detalleactual['precio'];
            $cantidad = $detalleactual['cantidad'];
            $unidad = $detalleactual['clvunidad'];
            $descripcion = $detalleactual['factura_producto'];
            $totalu = $detalleactual['totalunitario'];
            $traslados = $detalleactual['traslados'];
            $retenciones = $detalleactual['retenciones'];
            $observacionesprod = $detalleactual['observacionesproducto'];
            $obser = str_replace("<ent>", "\n", $observacionesprod);

            $divcF = explode("-", $claveFiscal);
            $divU = explode("-", $unidad);

            if ($traslados != "") {
                $divT = explode("<impuesto>", $traslados);
                foreach ($divT as $tactual) {
                    $impuestos = $tactual;
                    $div = explode("-", $impuestos);
                    if ($div[2] == '2') {
                        $iva += (bcdiv($div[0], '1', 2));
                    } else if ($div[2] == '3') {
                        $ieps += (bcdiv($div[0], '1', 2));
                    }
                }
            }

            if ($retenciones != "") {
                $divR = explode("<impuesto>", $retenciones);
                foreach ($divR as $ractual) {
                    $impuestos = $ractual;
                    $div2 = explode("-", $impuestos);
                    if ($div2[2] == '1') {
                        $isr += (bcdiv($div2[0], '1', 2));
                    } else if ($div2[2] == '2') {
                        $retiva += (bcdiv($div2[0], '1', 2));
                    } else if ($div2[2] == '3') {
                        $retieps += (bcdiv($div2[0], '1', 2));
                    }
                }
            }

            $pdf->Row(Array($cantidad, utf8_decode($divcF[0]), utf8_decode($divU[0] . " " . $divU[1]), utf8_decode($descripcion), utf8_decode($obser), utf8_decode('$ ' . number_format($precioV, 2, '.', ',')), utf8_decode('$ ' . number_format($totalu, 2, '.', ','))));
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Moneda ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 8, utf8_decode($c_moneda), 0, 0, 'L', 0);
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Forma de Pago ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 8, utf8_decode($des_pago), 0, 0, 'L', 0);
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Metodo de pago ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 8, utf8_decode($c_metodo . ' ' . $des_metodo), 0, 0, 'L', 0);
        $pdf->Ln(10);

        $pdf->Ln(-26);
        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Subtotal ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, utf8_decode('$ ' . number_format($subtotal, 2, '.', ',')), 1, 0, 'C', 0);
        $pdf->Ln(8);

        if ($iva > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'IVA ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($iva, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
        }

        if ($ieps > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'IEPS ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($ieps, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
        }

        if ($isr > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'ISR ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($isr, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
        }

        if ($retiva > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'Retencion IVA ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($retiva, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
        }

        if ($retieps > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'Retencion IEPS ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($retieps, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
        }

        if ($totdescuentos > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'Descuentos ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($totdescuentos, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
        }

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Total ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($totalfactura, '1', 2)), 1, 0, 'C', 0);
        $pdf->Ln(13);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Importe con Letra ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $letras = NumeroALetras::convertir(bcdiv($totalfactura, '1', 2), 'pesos', 'centavos');
        $div = explode(".", bcdiv($totalfactura, '1', 2));
        $pdf->Cell(26, 8, utf8_decode("$letras $div[1]/100 M.N."), 0, 0, 'L', 0);
        $pdf->Ln(8);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');


        if ($cfdisrel == '1') {

            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
            $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
            $pdf->RoundedRect(10, $pdf->GetY(), 35, 5, 2, 'F');
            $pdf->SetX(12);
            $pdf->Write(5, utf8_decode("CFDIS Relacionados"), '');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
            $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
            $pdf->SetWidths(Array(52, 70));
            $pdf->SetLineHeight(4.5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(52, 6, 'Tipo de Relacion', 1, 0, 'C', 1);
            $pdf->Cell(70, 6, 'Folio Fiscal', 1, 0, 'C', 1);
            $pdf->Ln(6);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(0, 0, 0);
            $relacionados = $cf->getcfdisRelacionados($tagfactura);
            foreach ($relacionados as $relactual) {
                $tiporel = $relactual['tiporel'];
                $descripcion = $relactual['descripcion_tiporelacion'];
                $uuidrel = $relactual['uuid'];
                $pdf->Row(Array(utf8_decode($tiporel . '-' . $descripcion), $uuidrel));
            }
        }

        if ($estado != "3") {
            if ($uuid == "") {
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'BI', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(10, 3, '*Este documento no posee efectos fiscales ', 0, 0, 'L', 0);
            } else {
                $pdf->Ln(3.5);
                $pdf->SetFillColor(216, 216, 216);
                $pdf->RoundedRect(10, $pdf->GetY(), 95, 30, 2, 'F');
                $pdf->SetTextColor(0, 0, 0);
                if ($qrcode != "") {
                    $pic = 'data://text/plain;base64,' . $qrcode;
                    $pdf->Write(10, $pdf->Image($pic, $pdf->GetX(), $pdf->GetY(), 30, 0, 'png'), '');
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor(23, 23, 124);
                $pdf->Write(10, utf8_decode("Folio Fiscal (UUID): "), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Write(10, utf8_decode($uuid), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor(23, 23, 124);
                $pdf->Write(10, utf8_decode("N° Certificado SAT: "), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Write(10, utf8_decode($certSAT), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor(23, 23, 124);
                $pdf->Write(10, utf8_decode("Fecha de Certificacion: "), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Write(10, utf8_decode($fechatimbrado), '');
                $pdf->Ln(11);

                $pdf->SetFont('Arial', 'B', 9);

                $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
                $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
                $pdf->RoundedRect(10, $pdf->GetY(), 35, 5, 2, 'F');
                $pdf->SetX(12);
                $pdf->Write(5, utf8_decode("Sello CFDI"), '');

                $pdf->RoundedRect(75, $pdf->GetY(), 35, 5, 2, 'F');
                $pdf->SetX(77);
                $pdf->Write(5, utf8_decode("Sello SAT"), '');

                $pdf->RoundedRect(140, $pdf->GetY(), 35, 5, 2, 'F');
                $pdf->SetX(142);
                $pdf->Write(5, utf8_decode("Cadena Original"), '');
                $pdf->Ln(5);
                $pdf->SetWidths(Array(62.5, 2.5, 62.5, 2.5, 62.5));
                $pdf->SetLineHeight(2.5);
                $pdf->SetFont('Arial', '', 5);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->RowR(Array(utf8_decode($sellocfdi), "", utf8_decode($selloSat), "", utf8_decode($cadenaoriginal)));
                $pdf->SetFont('Arial', '', 9);
                $pdf->Write(8, utf8_decode("Este documento es una representacion impresa de un cfdi-."), '');
            }
        } else {
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'BI', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(10, 3, '*La Factura ' . $folio . ' ha sido oficialmente cancelada', 0, 0, 'L', 0);
        }
    }
}

if ($tipo == '2' || $tipo == '3') {
    $cartas = $cf->getReporteCarta($f);
    foreach ($cartas as $recartaactual) {

        $id_carta = $recartaactual['idfactura_carta'];
        $nombre_cliente = $recartaactual['factura_rzsocial'];
        $tagfactura = $recartaactual['tagfactura'];

        $repcartas = $cf->getCartas($id_carta);
        foreach ($repcartas as $facturaactual) {
            $rfc = $facturaactual['factura_rfcemisor'];
            $razonsocial = $facturaactual['factura_rzsocial'];
            $regimen = $facturaactual['factura_clvregimen'] . ' ' . $facturaactual['factura_regimen'];
            $cp = $facturaactual['factura_cpemisor'];
            $idcliente = $facturaactual['idcliente'];
            $nombreCliente = $facturaactual['rzreceptor'];
            $rfccliente = $facturaactual['rfcreceptor'];
            $cpcliente = $facturaactual['cpreceptor'];
            $regfiscalreceptor = $facturaactual['regfiscalreceptor'];
            $cuso = $facturaactual['c_usocfdi'];
            $descripcionuso = $facturaactual['descripcion_cfdi'];
            $folio = $facturaactual['letra'] . $facturaactual['foliocarta'];
            $tipocomprobante = $facturaactual['c_tipocomprobante'] . ' ' . $facturaactual['descripcion_comprobante'];
            $fecha_creacion = $facturaactual['fecha_creacion'];
            $cadenaoriginal = $facturaactual['cadenaoriginal'];
            $certSAT = $facturaactual['nocertificadosat'];
            $certcfdi = $facturaactual['nocertificadocfdi'];
            $uuid = $facturaactual['uuid'];
            $selloSat = $facturaactual['sellosat'];
            $sellocfdi = $facturaactual['sellocfdi'];
            $fechatimbrado = $facturaactual['fechatimbrado'];
            $qrcode = $facturaactual['qrcode'];
            $c_metodo = $facturaactual['c_metodopago'];
            $des_metodo = $facturaactual['descripcion_metodopago'];
            //$c_pago = $facturaactual['c_pago'];
            $des_pago = $facturaactual['descripcion_pago'];
            $idmoneda = $facturaactual['id_moneda'];
            $c_moneda = $facturaactual['c_moneda'];
            $subtotal = $facturaactual['subtotal'];
            $totdescuentos = $facturaactual['totaldescuentos'];
            $totalfactura = $facturaactual['totalfactura'];
            $estado = $facturaactual['status_pago'];
            $chfirmar = $facturaactual['chfirmar'];
            $iddatosfacturacion = $facturaactual['iddatosfacturacion'];
            $tag = $facturaactual['tagfactura'];
            $cfdistring = $facturaactual['cfdistring'];
            $divideF = explode("-", $fecha_creacion);
            $mes = $cf->translateMonth($divideF[1]);
            $fecha_creacion2 = $divideF[2] . '/' . $mes . '/' . $divideF[0];
        }

        $sine = $cf->getDatosFacturacionbyId($iddatosfacturacion);
        foreach ($sine as $sineactual) {
            $numcertificado = $sineactual['numcsd'];
            $firma = $sineactual['firma'];
        }

        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('1');
        foreach ($encabezado as $actual) {
            $colorcuadro = $actual['colorcuadro'];
            $rgbc = explode("-", $cc->hex2rgb($colorcuadro));
            $colorsubtitulos = $actual['colorsubtitulos'];
            $rgbs = explode("-", $cc->hex2rgb($colorsubtitulos));
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

        $pdf->Tfolio = $folio;
        $pdf->chfirmar = $chfirmar;
        $pdf->nmfirma = $razonsocial;
        $pdf->firma = $firma;

        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Datos del Emisor"));

        $pdf->SetWidths(Array(15, 94.5, 25, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($razonsocial), '', utf8_decode($numcertificado)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($rfc), '', utf8_decode($regimen)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($fecha_creacion2), '', utf8_decode($tipocomprobante)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(32, 77.5, 25, 60));
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($cp), '', ''));

        $heightdatos = $pdf->heightB;

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, 45, 195, $heightdatos, 2, 'F');
        $pdf->SetWidths(Array(15, 94.5, 25, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetY(45);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Nombre');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'No Certificado');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($razonsocial), '', utf8_decode($numcertificado)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'RFC');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'Regimen Fiscal');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($rfc), '', utf8_decode($regimen)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Fecha');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'T. Comprobante');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($fecha_creacion2), '', utf8_decode($tipocomprobante)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths(Array(32, 77.5, 25, 60));
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Lugar de Expedicion');
        $pdf->SetX(119.5);
        $pdf->Write(5, '');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($cp), '', ''));
        $pdf->Ln(0.5);

        $pdf->heightB = 0;

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Datos del Cliente"));
        $direccion = "CP. $cpcliente";
        if ($idcliente != '0') {
            $direccion = $cf->getDireccionClienteCarta($idcliente, $id_carta);
        }

        $pdf->ycliente = $pdf->GetY();

        $pdf->SetWidths(Array(17, 94.5, 23, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($nombreCliente), '', utf8_decode($cuso . ' ' . $descripcionuso)));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($rfccliente), '', ''));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(17, 174.5));
        $pdf->SetX(10);
        $pdf->RowNBCount(Array('', utf8_decode($direccion)));

        $heightcliente = $pdf->heightB;

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, ($pdf->ycliente + 0.7), 195, $heightcliente, 2, 'F');

        $pdf->SetY(($pdf->ycliente + 0.7));
        $pdf->SetWidths(Array(17, 94.5, 23, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Nombre');
        $pdf->SetX(119.5);
        $pdf->Write(5, 'Uso de CFDI');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($nombreCliente), '', utf8_decode($cuso . ' ' . $descripcionuso)));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'RFC');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($rfccliente), '', ''));

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Direccion');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetWidths(Array(17, 174.5));
        $pdf->SetLineHeight(4.5);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', utf8_decode($direccion)));
        $pdf->Ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
        $pdf->SetX(92.5);
        $pdf->Write(6, 'CONCEPTOS');
        $pdf->Ln(7);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(15, 6, 'Cantidad', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Clave Fiscal', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Unidad', 1, 0, 'C', 1);
        $pdf->Cell(46, 6, 'Descripcion', 1, 0, 'C', 1);
        $pdf->Cell(45, 6, 'Observaciones', 1, 0, 'C', 1);
        $pdf->Cell(22, 6, 'Precio', 1, 0, 'C', 1);
        $pdf->Cell(22, 6, 'Importe', 1, 0, 'C', 1);
        $pdf->Ln(6);
        $pdf->SetAligns(array('C'));
        $pdf->SetWidths(Array(15, 20, 25, 46, 45, 22, 22));
        $pdf->SetLineHeight(4.5);

        $pdf->SetRowBorder('B');

        $detallefactura = $cf->getDetalleCarta($tag);
        $iva = 0;
        $ieps = 0;
        $retiva = 0;
        $retieps = 0;
        $isr = 0;
        $pdf->setRowColorText();
        $pdf->SetStyles();
        foreach ($detallefactura as $detalleactual) {
            $pdf->SetFont('Arial', '', 9);
            $claveFiscal = $detalleactual['clvfiscal'];
            $precioV = $detalleactual['precio'];
            $cantidad = $detalleactual['cantidad'];
            $unidad = $detalleactual['clvunidad'];
            $descripcion = $detalleactual['carta_producto'];
            $totalu = $detalleactual['totalunitario'];
            $traslados = $detalleactual['traslados'];
            $retenciones = $detalleactual['retenciones'];
            $observacionesprod = $detalleactual['observacionesproducto'];
            $obser = str_replace("<ent>", "\n", $observacionesprod);
            $divcF = explode("-", $claveFiscal);
            $divU = explode("-", $unidad);

            if ($traslados != "") {
                $divT = explode("<impuesto>", $traslados);
                foreach ($divT as $tactual) {
                    $impuestos = $tactual;
                    $div = explode("-", $impuestos);
                    if ($div[2] == '2') {
                        $iva += (bcdiv($div[0], '1', 2));
                    } else if ($div[2] == '3') {
                        $ieps += (bcdiv($div[0], '1', 2));
                    }
                }
            }

            if ($retenciones != "") {
                $divR = explode("<impuesto>", $retenciones);
                foreach ($divR as $ractual) {
                    $impuestos = $ractual;
                    $div = explode("-", $impuestos);
                    if ($div[2] == '1') {
                        $isr += (bcdiv($div[0], '1', 2));
                    } else if ($div[2] == '2') {
                        $retiva += (bcdiv($div[0], '1', 2));
                    } else if ($div[2] == '3') {
                        $retieps += (bcdiv($div[0], '1', 2));
                    }
                }
            }

            $pdf->Row(Array($cantidad, utf8_decode($divcF[0]), utf8_decode($divU[0] . " " . $divU[1]), utf8_decode($descripcion), utf8_decode($obser), utf8_decode('$ ' . number_format($precioV, 2, '.', ',')), utf8_decode('$ ' . number_format($totalu, 2, '.', ','))));
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Moneda ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 8, utf8_decode($c_moneda), 0, 0, 'L', 0);
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Forma de Pago ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 8, utf8_decode($c_pago . ' ' . $des_pago), 0, 0, 'L', 0);
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Metodo de pago ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 8, utf8_decode($c_metodo . ' ' . $des_metodo), 0, 0, 'L', 0);
        $pdf->Ln(10);

        $pdf->Ln(-26);
        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Subtotal ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, utf8_decode('$ ' . number_format($subtotal, 2, '.', ',')), 1, 0, 'C', 0);
        $pdf->Ln(8);

        $lnJ = 15;

        if ($iva > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'IVA ', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($iva, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
            $lnJ = 10;
        }

        if ($ieps > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'IEPS', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($ieps, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
            $lnJ = 10;
        }

        if ($isr > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'ISR', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($isr, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
            $lnJ = 10;
        }

        if ($retiva > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'Retencion IVA', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($retiva, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
            $lnJ = 10;
        }

        if ($retieps > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'Retencion IEPS', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($retieps, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
            $lnJ = 10;
        }

        if ($totdescuentos > 0) {
            $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(22, 8, 'Descuentos', 1, 0, 'C', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($totdescuentos, '1', 2)), 1, 0, 'C', 0);
            $pdf->Ln(8);
            $lnJ = 10;
        }

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Total', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, utf8_decode('$ ' . bcdiv($totalfactura, '1', 2)), 1, 0, 'C', 0);
        $pdf->Ln($lnJ);

        $div = explode(".", bcdiv($totalfactura, '1', 2));
        switch ($idmoneda) {
            case '1':
                $letramoneda = "pesos";
                $letracent = "centavos";
                $mn = "$div[1]/100 M.N.";
                break;
            case '2':
                $letramoneda = "dolares";
                $letracent = "centavos";
                $mn = "";
                break;
            case '4':
                $letramoneda = "euros";
                $letracent = "centimos";
                $mn = "";
                break;
            default:
                $letramoneda = "pesos";
                $letracent = "centavos";
                $mn = "$div[1]/100 M.N.";
                break;
        }
        $pdf->SetWidths(Array(30, 165));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Write(4.5, 'Importe con Letra');
        $pdf->SetFont('Arial', '', 9);
        $letras = NumeroALetras::convertir(bcdiv($totalfactura, '1', 2), utf8_decode($letramoneda), utf8_decode($letracent));
        $pdf->SetX(10);
        $pdf->SetAligns(Array('L'));
        $pdf->RowNB(Array('', utf8_decode("$letras $mn")));
        $pdf->Ln(1);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');

        if ($estado != "3") {
            if ($uuid == "") {
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'BI', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(10, 3, '*Este documento no posee efectos fiscales ', 0, 0, 'L', 0);
            } else {
                $pdf->Ln(4.5);
                $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
                $pdf->RoundedRect(10, $pdf->GetY(), 95, 30, 2, 'F');
                $pdf->SetTextColor(0, 0, 0);
                if ($qrcode != "") {
                    $pic = 'data://text/plain;base64,' . $qrcode;
                    $pdf->Write(10, $pdf->Image($pic, $pdf->GetX(), $pdf->GetY(), 30, 0, 'png'), '');
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
                $pdf->Write(10, utf8_decode("Folio Fiscal (UUID): "), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->Write(10, utf8_decode($uuid), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
                $pdf->Write(10, utf8_decode("N° Certificado SAT: "), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->Write(10, utf8_decode($certSAT), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
                $pdf->Write(10, utf8_decode("Fecha de Certificacion: "), '');
                $pdf->Ln(4);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(40);
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->Write(10, utf8_decode($fechatimbrado), '');
                $pdf->Ln(11.5);

                $pdf->SetFont('Arial', 'B', 9);

                $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
                $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
                $pdf->RoundedRect(10, $pdf->GetY(), 35, 5, 2, 'F');
                $pdf->SetX(12);
                $pdf->Write(5, utf8_decode("Sello CFDI"), '');

                $pdf->RoundedRect(75, $pdf->GetY(), 35, 5, 2, 'F');
                $pdf->SetX(77);
                $pdf->Write(5, utf8_decode("Sello SAT"), '');

                $pdf->RoundedRect(140, $pdf->GetY(), 35, 5, 2, 'F');
                $pdf->SetX(142);
                $pdf->Write(5, utf8_decode("Cadena Original"), '');
                $pdf->Ln(5);
                $pdf->SetWidths(Array(62.5, 2.5, 62.5, 2.5, 65));
                $pdf->SetLineHeight(2.5);
                $pdf->SetFont('Arial', '', 5);
                $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
                $pdf->rgbfd0 = $rgbfd[0];
                $pdf->rgbfd1 = $rgbfd[1];
                $pdf->rgbfd2 = $rgbfd[2];
                $pdf->RowR(Array(utf8_decode($sellocfdi), "", utf8_decode($selloSat), "", utf8_decode($cadenaoriginal)));
                $pdf->SetFont('Arial', '', 9);
                $pdf->Write(8, utf8_decode("Este documento es una representacion impresa de un cfdi-."), '');
            }
        } else {
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'BI', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(10, 3, '*La Factura ' . $folio . ' ha sido oficialmente cancelada', 0, 0, 'L', 0);
        }

        $pdf->AddPage();

        $carta = $cf->getDatosCarta($tag);
        foreach ($carta as $cactual) {
            $tipomov = $cactual['tipomovimiento'];
            $numpermiso = $cactual['carta_numpermiso'];
            $tipopermiso = $cactual['carta_tipopermiso'];
            $tipotransporte = $cactual['carta_conftransporte'];
            $anhomod = $cactual['carta_anhomod'];
            $placavehiculo = $cactual['carta_placa'];
            $segurocivil = $cactual['carta_segurocivil'];
            $poliza = $cactual['carta_polizaseguro'];
            $tiporemolque1 = $cactual['carta_tiporemolque1'];
            $placaremolque1 = $cactual['carta_placaremolque1'];
            $tiporemolque2 = $cactual['carta_tiporemolque2'];
            $placaremolque2 = $cactual['carta_placaremolque2'];
            $tiporemolque3 = $cactual['carta_tiporemolque3'];
            $placaremolque3 = $cactual['carta_placaremolque3'];

            $movimiento = "";
            if ($tipomov == '1') {
                $movimiento = "Entrada";
            } else if ($tipomov == '2') {
                $movimiento = "Salida";
            }

            $div1 = explode("-", $tipopermiso);
            $permiso = $div1[0];

            $div2 = explode("-", $tipotransporte);
            $transporte = $div2[0];
        }

        $distanciatotal = $cf->getDistanciaTotal($tag);

        $pdf->heightB = 0;
        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetAligns(array('C'));
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Complemento Carta Porte"));

        $pdf->SetWidths(Array(15, 94.5, 25, 60));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, 45, 195, 12, 2, 'F');

        $pdf->SetY(46);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(40, 66, 46, 43));
        $pdf->SetLineHeight(4.5);
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetRowBorder();
        $pdf->SetAligns(array('L'));
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Version', utf8_decode('2.0'), 'Distancia Total Recorrida', utf8_decode($distanciatotal)));


        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetRowBorder();
        $pdf->SetAligns(array('L'));
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Transporte Internacional', utf8_decode('No'), 'Entrada/Salida de mercancia', utf8_decode($movimiento)));
        $pdf->Ln(2.5);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetAligns(array('C'));
        $pdf->RowT(Array("Mercancias"));
        $pdf->Ln(1);

        $pdf->setRowColorText();
        $detallemercancia = $cf->getMercancias($tag);
        $pdf->SetWidths(Array(30, 90, 20, 30, 25));
        $pdf->SetLineHeight(4.5);
        $pdf->SetAligns(array('C'));
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->setRowColorText(array($colortittabla));
        $pdf->SetRowBorder('B', 'FD');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetStyles('B');
        $pdf->Row(Array("Clave Fiscal", "Descripcion", "Cantidad", "Clave Unidad", "Peso en Kg"));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetRowBorder('B');
        $pdf->SetStyles();
        $pdf->setRowColorText();
        foreach ($detallemercancia as $mercactual) {
            $clavemercancia = $mercactual['clave_mercanca'];
            $descripcion = $mercactual['descripcion_mercancia'];
            $cantidad = $mercactual['cant_mercancia'];
            $unidad = $mercactual['unidad_mercancia'];
            $peso = $mercactual['peso_mercancia'];

            $div = explode('-', $clavemercancia);
            $clv = $div[0];

            $div2 = explode('-', $unidad);
            $unit = $div[0];

            $pdf->Row(Array(utf8_decode($clv), utf8_decode($descripcion), $cantidad, utf8_decode($unit), $peso));
        }
        $pdf->Ln(2);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Origenes"));

        $origenes = $cf->getUbicaciones($tag, '1');
        foreach ($origenes as $oractual) {
            $idubicacion = $oractual['ubicacion_id'];
            $nombreor = $oractual['ubicacion_nombre'];
            $rfcorigen = $oractual['ubicacion_rfc'];
            $idtipo = $oractual['ubicacion_tipo'];
            $idestado = $oractual['ubicacion_idestado'];
            $estado = $oractual['estado'];
            $codporigen = $oractual['ubicacion_codpostal'];
            $distancia = $oractual['ubicacion_distancia'];
            $fechasalida = $oractual['fechallegada'];
            $horasalida = $oractual['horallegada'];
            $ordireccion = $oractual['direccion'];
            $idmunicipio = $oractual['idmunicipio'];
            $municipio = $cf->getMunicipioAux($idmunicipio);

            $dirubicacion = "$ordireccion CP. $codporigen, $municipio $estado";

            $div = explode("-", $fechasalida);
            $fecha = "$div[2]/$div[1]/$div[0]";
            $tipo = "Origen";

            $pdf->SetWidths(Array(28, 66, 33, 68));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
            $pdf->SetStyles(array('B', '', 'B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();

            $pdf->heightB = 0;

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(10);
            $pdf->RowNBCount(Array("", utf8_decode($nombreor), "", utf8_decode($rfcorigen)));
            $pdf->RowNBCount(Array("", utf8_decode($tipo), "", utf8_decode($distancia)));
            $pdf->RowNBCount(Array("", utf8_decode($fecha . ' ' . $horasalida), "", ""));

            $pdf->SetWidths(Array(20, 167));
            $pdf->SetLineHeight(5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt));
            $pdf->SetStyles(array('B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->RowNBCount(Array("", utf8_decode($dirubicacion)));

            $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
            $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

            $pdf->SetY($pdf->GetY() - ($pdf->heightB));
            $pdf->SetWidths(Array(28, 66, 33, 68));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
            $pdf->SetStyles(array('B', '', 'B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->Row(Array("Remitente", utf8_decode($nombreor), "RFC Remitente", utf8_decode($rfcorigen)));
            $pdf->Row(Array("Tipo", utf8_decode($tipo), "Distancia Recorrida", utf8_decode($distancia)));
            $pdf->Row(Array("Fecha Salida", utf8_decode($fecha . ' ' . $horasalida), "", ""));

            $pdf->SetWidths(Array(20, 167));
            $pdf->SetLineHeight(5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt));
            $pdf->SetStyles(array('B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->Row(Array("Direccion", utf8_decode($dirubicacion)));

            $pdf->Ln(1.5);
        }

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Destinos"));

        $destinos = $cf->getUbicaciones($tag, '2');
        foreach ($destinos as $desactual) {
            $idubicacion = $desactual['ubicacion_id'];
            $nombredes = $desactual['ubicacion_nombre'];
            $rfcdestino = $desactual['ubicacion_rfc'];
            $idtipo = $desactual['ubicacion_tipo'];
            $idestado = $desactual['ubicacion_idestado'];
            $estado = $desactual['estado'];
            $coddestino = $desactual['ubicacion_codpostal'];
            $distancia = $desactual['ubicacion_distancia'];
            $fechallegada = $desactual['fechallegada'];
            $horallegada = $desactual['horallegada'];
            $desdireccion = $desactual['direccion'];
            $idmunicipio = $desactual['idmunicipio'];
            $municipio = $cf->getMunicipioAux($idmunicipio);

            $dirubicacion = "$desdireccion CP. $codporigen, $municipio $estado";

            $div = explode("-", $fechallegada);
            $fecha = "$div[2]/$div[1]/$div[0]";
            $tipo = "Destino";

            $pdf->SetWidths(Array(28, 66, 33, 68));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
            $pdf->SetStyles(array('B', '', 'B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();

            $pdf->heightB = 0;

            $pdf->SetFont('Arial', '', 9);
            $pdf->RowNBCount(Array("", utf8_decode($nombredes), "", utf8_decode($rfcdestino)));
            $pdf->RowNBCount(Array("", utf8_decode($tipo), "", utf8_decode($distancia)));
            $pdf->RowNBCount(Array("", utf8_decode($fecha . ' ' . $horallegada), "", ""));

            $pdf->SetWidths(Array(20, 167));
            $pdf->SetLineHeight(5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt));
            $pdf->SetStyles(array('B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->RowNBCount(Array("", utf8_decode($dirubicacion)));

            $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
            $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

            $pdf->SetY($pdf->GetY() - ($pdf->heightB));

            $pdf->SetWidths(Array(28, 66, 33, 68));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
            $pdf->SetStyles(array('B', '', 'B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->Row(Array("Destinatario", utf8_decode($nombredes), "RFC Destinatario", utf8_decode($rfcdestino)));
            $pdf->Row(Array("Tipo", utf8_decode($tipo), "Distancia Recorrida", utf8_decode($distancia)));
            $pdf->Row(Array("Fecha Llegada", utf8_decode($fecha . ' ' . $horallegada), "", ""));

            $pdf->SetWidths(Array(20, 167));
            $pdf->SetLineHeight(5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt));
            $pdf->SetStyles(array('B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->Row(Array("Direccion", utf8_decode($dirubicacion)));
            $pdf->Ln(1.5);
        }

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Autotransporte Federal"));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(30, 70, 27, 68));
        $pdf->SetLineHeight(4.5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->heightB = 0;
        $pdf->RowNBCount(Array("Num Permiso", utf8_decode($numpermiso), "Tipo permiso", utf8_decode($permiso)));
        $pdf->RowNBCount(Array(utf8_decode("Año Vehiculo"), utf8_decode($anhomod), "Placa Vehiculo", utf8_decode($placavehiculo)));
        $pdf->RowNBCount(Array(utf8_decode("Tipo Transporte"), utf8_decode($transporte), "", ''));
        $pdf->RowNBCount(Array(utf8_decode("Nombre Aseguradora"), utf8_decode($segurocivil), "Num Poliza", utf8_decode($poliza)));

        if ($tiporemolque1 != "") {
            $pdf->RowNBCount(Array(utf8_decode("Tipo Remolque #1"), utf8_decode($tiporemolque1), "Placa Remolque", utf8_decode($placaremolque1)));
        }
        if ($tiporemolque2 != "") {
            $pdf->RowNBCount(Array(utf8_decode("Tipo Remolque #2"), utf8_decode($tiporemolque2), "Placa Remolque", utf8_decode($placaremolque2)));
        }
        if ($tiporemolque3 != "") {
            $pdf->RowNBCount(Array(utf8_decode("Tipo Remolque #3"), utf8_decode($tiporemolque3), "Placa Remolque", utf8_decode($placaremolque3)));
        }

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

        $pdf->SetY($pdf->GetY() - ($pdf->heightB));
        $pdf->Row(Array("Num Permiso", utf8_decode($numpermiso), "Tipo permiso", utf8_decode($permiso)));
        $pdf->Row(Array(utf8_decode("Año Vehiculo"), utf8_decode($anhomod), "Placa Vehiculo", utf8_decode($placavehiculo)));
        $pdf->Row(Array(utf8_decode("Tipo Transporte"), utf8_decode($transporte), "", ''));
        $pdf->Row(Array(utf8_decode("Nombre Aseguradora"), utf8_decode($segurocivil), "Num Poliza", utf8_decode($poliza)));

        if ($tiporemolque1 != "") {
            $pdf->Row(Array(utf8_decode("Tipo Remolque #1"), utf8_decode($tiporemolque1), "Placa Remolque", utf8_decode($placaremolque1)));
        }
        if ($tiporemolque2 != "") {
            $pdf->Row(Array(utf8_decode("Tipo Remolque #2"), utf8_decode($tiporemolque2), "Placa Remolque", utf8_decode($placaremolque2)));
        }
        if ($tiporemolque3 != "") {
            $pdf->Row(Array(utf8_decode("Tipo Remolque #3"), utf8_decode($tiporemolque3), "Placa Remolque", utf8_decode($placaremolque3)));
        }
        $pdf->Ln(1.5);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Operadores"));

        $operadores = $cf->getOperadores($tag);
        foreach ($operadores as $opactual) {
            $idoperador = $opactual['operador_id'];
            $rfcoperador = $opactual['operador_rfc'];
            $numlicencia = $opactual['operador_numlic'];
            $nombreoperador = $opactual['operador_nombre'];
            if ($nombreoperador == "") {
                $nombreoperador = $cf->getNombreOperador($rfcoperador);
            }
            $idestado = $opactual['operador_idestado'];
            $estado = $opactual['estado'];
            $calle = $opactual['operador_calle'];
            $cpoperador = $opactual['operador_cp'];
            $idmunicipio = $opactual['operador_idmunicipio'];
            $municipio = $cf->getMunicipioAux($idmunicipio);

            $diroperador = $calle . " " . $cp . " " . $municipio . " " . $estado;

            $pdf->SetWidths(Array(25, 70, 25, 70));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
            $pdf->SetStyles(array('B', '', 'B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->heightB = 0;

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(10);
            $pdf->RowNBCount(Array("Nombre", utf8_decode($nombreoperador), "RFC", utf8_decode($rfcoperador)));
            $pdf->RowNBCount(Array("Num Licencia", utf8_decode($numlicencia), "", ""));

            $pdf->SetWidths(Array(25, 170));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt));
            $pdf->SetStyles(array('B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->RowNBCount(Array("Direccion", utf8_decode($diroperador)));

            $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
            $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

            $pdf->SetY($pdf->GetY() - ($pdf->heightB));

            $pdf->SetWidths(Array(25, 70, 25, 70));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
            $pdf->SetStyles(array('B', '', 'B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->Row(Array("Nombre", utf8_decode($nombreoperador), "RFC", utf8_decode($rfcoperador)));
            $pdf->Row(Array("Num Licencia", utf8_decode($numlicencia), "", ""));

            $pdf->SetWidths(Array(25, 170));
            $pdf->SetLineHeight(4.5);
            $pdf->setRowColorText(Array($txtbold, $clrtxt));
            $pdf->SetStyles(array('B', ''));
            $pdf->SetAligns(array('L'));
            $pdf->SetRowBorder();
            $pdf->Row(Array("Direccion", utf8_decode($diroperador)));
            $pdf->Ln(1.5);
        }
    }
}

$pdf->isFinished = true;

$nombrecliente = "";
if ($clienteB != "") {
    $nombrecliente = $nombre_cliente;
}
$pdf->Output('Reporte' . $fechainicio . '-' . $fechafin . '' . $nombrecliente . '' . $est . '.pdf', 'I');
