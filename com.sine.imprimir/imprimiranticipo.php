<?php

require_once '../com.sine.modelo/Anticipo.php';
require_once '../com.sine.controlador/ControladorCotizacion.php';
require '../pdf/fpdf/fpdf.php';

setlocale(LC_MONETARY, 'es_MX.UTF-8');
date_default_timezone_set("America/Mexico_City");

class PDF extends FPDF {

    var $widths;
    var $aligns;
    var $Tfolio;
    var $lineHeight;
    var $iddatos;
    var $chfirmar;
    var $firma;
    var $isFinished;
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
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 4.5, $data[$i], 0, $a);
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
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x, $y, $w, $h);
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

    function Header() {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('4');
        foreach ($encabezado as $actual) {
            $titulo = $actual['tituloencabezado'];
            $colortitulo = $actual['colortitulo'];
            $rgbt = explode("-", $cc->hex2rgb($colortitulo));
            $colorcuadro = $actual['colorcuadro'];
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
        $this->RoundedRect(35, $this->GetY(), 120, 8, 4, 'F');
        $this->SetX(38);
        $this->Write(8, $titulo);

        $this->SetX(160);
        $this->RoundedRect(160, $this->GetY(), 45, 8, 4, 'F');
        $this->SetX(165);
        $this->Write(8, 'Cotizacion');

        $this->SetY(18);
        $this->SetX(160);
        $this->SetTextColor(255, 0, 0);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(45, 8, utf8_decode($this->Tfolio), 0, 0, 'C', false);
        $this->Ln(26);
    }

    function Footer() {
        require_once '../com.sine.controlador/ControladorConfiguracion.php';
        $cc = new ControladorConfiguracion();
        $encabezado = $cc->getDatosEncabezado('4');
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
            if ($this->chfirmar == '1') {
                $this->Image($this->firma, 75, ($this->GetY() - 25), 60, 0, 'png');
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
        if ($len > 14) {
            $txt = str_split($t, 13);
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

    function myCellO($w, $h, $x, $t) {
        $height = $h / 3;
        $first = $height + 2;
        $second = $height + $height + $height + 3;

        $len = strlen($t);
        if ($len > 101) {
            $txt = str_split($t, 100);
            $this->SetX($x);
            $this->Cell($w, $first, $txt[0], 0, '', 'L');
            $this->SetX($x);
            $this->Cell($w, $second, $txt[1], 0, '', 'L');
            $this->SetX($x);
            $this->Cell($w, $h, '', 0, 0, 'L', 0);
        } else {
            $this->SetX($x);
            $this->Cell($w, $h, $t, 0, 0, 'L', 0);
        }
    }

    function myCellD($w, $h, $x, $t) {
        $height = $h / 3;
        $first = $height + 2;
        $second = $height + $height + $height + 1;
        $third = $height + $height + $height + 1;
        $len = strlen($t);
        if ($len > 27) {
            $txt = str_split($t, 27);
            $this->SetX($x);
            $this->Cell($w, $first, $txt[0], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $second, $txt[1], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $third, $txt[2], '', '', 'C');
            $this->SetX($x);
            $this->Cell($w, $h, '', 'LTRB', 0, 'L', 0);
        } else {
            $this->SetX($x);
            $this->Cell($w, $h, $t, 'LTRB', 0, 'C', 0);
        }
    }

    function TextWithDirection($x, $y, $txt, $direction = 'R') {
        if ($direction == 'R')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 1, 0, 0, 1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'L')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', -1, 0, 0, -1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'U')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, 1, -1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'D')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, -1, 1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        else
            $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->ColorFlag)
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        $this->_out($s);
    }

    function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle = 0) {
        $font_angle += 90 + $txt_angle;
        $txt_angle *= M_PI / 180;
        $font_angle *= M_PI / 180;

        $txt_dx = cos($txt_angle);
        $txt_dy = sin($txt_angle);
        $font_dx = cos($font_angle);
        $font_dy = sin($font_angle);

        $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $txt_dx, $txt_dy, $font_dx, $font_dy, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->ColorFlag)
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        $this->_out($s);
    }

}

$cf = new ControladorCotizacion;
$f = new Cotizacion();
$idanticipo = intval($_GET['anticipo']);
$anticipo = $cf->getAnticipoById($idanticipo);

foreach ($anticipo as $actual) {
    $idanticipo = $actual['idanticipo'];
    $fechacreacion = $actual['fechacreacion'];
    $idcotizacion = $actual['anticipo_idcotizacion'];
    $monto = $actual['montoanticipo'];
    $autorizacion = $actual['autorizacion'];
    $fechaanticipo = $actual['fechaanticipo'];
    $texto = $actual['texto'];
    $foliocotizacion = $actual['letra'] . $actual['foliocotizacion'];
    $emision = $actual['emision'];
    $firma = $actual['firma'];
}

$divf = explode("-", $fechacreacion);

switch ($divf[1]) {
    case '01':
        $mes = 'Enero';
        break;
    case '02':
        $mes = 'Febrero';
        break;
    case '03':
        $mes = "Marzo";
        break;
    case '04':
        $mes = 'Abril';
        break;
    case '05':
        $mes = 'Mayo';
        break;
    case '06':
        $mes = 'Junio';
        break;
    case '07':
        $mes = 'Julio';
        break;
    case '08':
        $mes = 'Agosto';
        break;
    case '09':
        $mes = 'Septiembre';
        break;
    case '10':
        $mes = 'Octubre';
        break;
    case '11':
        $mes = 'Noviembre';
        break;
    case '12':
        $mes = 'Diciembre';
        break;
}

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('4');
foreach ($encabezado as $actual) {
    $colorcuadro = $actual['colorcuadro'];
    $rgbc = explode("-", $cc->hex2rgb($colorcuadro));
    $colorsubtitulos = $actual['colorsubtitulos'];
    $rgbs = explode("-", $cc->hex2rgb($colorsubtitulos));
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->Tfolio = $foliocotizacion;
$pdf->firma = $firma;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetY(36.3);
$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetWidths(Array(65));
$pdf->SetLineHeight(8);
$pdf->SetX(140);
$pdf->RowT(Array(utf8_decode('BUENO POR: $' . bcdiv($monto, 1, 2))));

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetWidths(Array(125));
$pdf->SetLineHeight(8);
$pdf->SetX(80);
$pdf->RowT(Array(utf8_decode($emision . ' a ' . $divf[2] . ' de ' . $mes . ' de ' . $divf[0])));

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 15);
$pdf->SetTextColor(0, 0, 0);
$pdf->Write(8, utf8_decode($texto));

$pdf->Ln(4);
$pdf->SetFont('courier', 'B', 18);
$pdf->SetTextColor(50, 50, 50);
switch ($divf[1]) {
    case '01':
        $msello = 'ENE.';
        break;
    case '02':
        $msello = 'FEB.';
        break;
    case '03':
        $msello = "MAR.";
        break;
    case '04':
        $msello = 'ABR.';
        break;
    case '05':
        $msello = 'MAY.';
        break;
    case '06':
        $msello = 'JUN.';
        break;
    case '07':
        $msello = 'JUL';
        break;
    case '08':
        $msello = 'AGO.';
        break;
    case '09':
        $msello = 'SEP.';
        break;
    case '10':
        $msello = 'OCT.';
        break;
    case '11':
        $msello = 'NOV.';
        break;
    case '12':
        $msello = 'DIC.';
        break;
    default :
        $msello = 'ENE.';
        break;
}
$pdf->TextWithRotation(25.5, 143, $divf[2] . ' ' . $msello . ' ' . $divf[0], 25.5, 0);
$pdf->Image('../img/SelloSine2.png', $pdf->GetX(), 100, 70);


$pdf->Output("anticipo$foliocotizacion.pdf", 'I');
