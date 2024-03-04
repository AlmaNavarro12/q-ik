<?php

require_once '../vendor/autoload.php';
require '../pdf/fpdf/fpdf.php';
require_once '../com.sine.controlador/ControladorConfiguracion.php';
require_once '../com.sine.controlador/ControladorVenta.php';

setlocale(LC_MONETARY, 'es_MX.UTF-8');

class PDF extends FPDF {

    var $titulopagina;
    var $idencabezado;
    var $imglogo;
    var $celdatitulo;
    var $colortitulo;
    var $pagina;
    var $correo;
    var $telefono1;
    var $telefono2;
    var $chnum;
    var $colorpie;
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
    var $titulo;
    var $clrtitulo;
    var $colorcelda;
    var $tel1;
    var $tel2;
    var $clrpie;
    var $margen;
    var $body;

    var $chkdata = 0;
    var $nombreempresa = '';
    var $razonsocial = '';
    var $direccion = '';
    var $RFC = '';

    var $piecentro;
    var $pieizquierdo;
    var $piederecho;

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
            $sz = isset($this->sizes[$i]) ? $this->sizes[$i] : (isset($this->sizes[0]) ? $this->sizes[0] : 9);
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
            $this->RoundedRect($x, $y, $w, $h, 2, 'D');
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
        $this->SetFont('Arial', '', 19);
        $rgbc = explode("-", $this->celdatitulo);
        $rgbt = explode("-", $this->colortitulo);
        $this->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $this->SetTextColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $logo = "../temporal/tmp/$this->imglogo";
        if (!file_exists($logo)) {
            $logo = "../img/logo/$this->imglogo";
        }
        $dimensiones = getimagesize($logo);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $height = ($height * 20) / $width;
        
        if ($height > 25) {
            $height = 25;
        }

        if($this->chkdata == 1) {
                
            $this->SetRowBorder('NB');
            $this->setRowColorText(array($this->clrtitulo));
            $this->SetY(3);
            $this->SetX($this->margen);
            $this->SetWidths(Array(bcdiv(($this->body * 0.33),'1',0), bcdiv(($this->body * 0.66),'1',0)+1));
            if($this->body >= 76){
                $this->SetSizes(array(1, 10));
                $this->SetLineHeight(5);
            }else {
                $this->SetSizes(array(1, 7));
                $this->SetLineHeight(4);
            }                
            
            $this->SetStyles(array('', 'B'));
            $this->SetAligns('C', 'C');
            if($this->body >= 76){
                $this->Row(Array($this->Image($logo,($this->margen+5), 3, 20, $height), iconv("utf-8","windows-1252",$this->nombreempresa)));
            }else{
                $this->Row(Array($this->Image($logo,($this->margen), 3, bcdiv(($this->body * 0.33),'1',0), $height), iconv("utf-8","windows-1252",$this->nombreempresa)));
            }
            
            $this->SetX($this->margen);
            $this->Row(Array('', iconv("utf-8","windows-1252",$this->razonsocial)));
            $this->SetX($this->margen);
            $this->Row(Array('', iconv("utf-8","windows-1252",$this->direccion)));
            $this->SetX($this->margen);
            $this->Row(Array('', iconv("utf-8","windows-1252",$this->RFC)));
            $this->SetX($this->margen);
            $this->RoundedRect($this->margen, 30, $this->body, 2, 1, 'F');

        } else if($this->chkdata == 2) {
            
            $this->SetRowBorder('NB');
            $this->SetY(3);
            $this->SetX($this->margen);
            $this->SetWidths(Array($this->body));
            $this->SetSizes(array(10));
            $this->SetLineHeight(25);
            $this->SetAligns('C');
            $this->Row(Array($this->Image($logo,(bcdiv(($this->body * 0.33),'1',0)+2), 3, 20, $height)));
            $this->RoundedRect($this->margen, 30, $this->body, 2, 1, 'F');

        } else if($this->chkdata == 3){

            $this->SetRowBorder('NB');
            $this->setRowColorText(array($this->clrtitulo));
            $this->SetY(3);
            $this->SetX($this->margen);
            $this->SetWidths(Array($this->body));
            if($this->body >= 76){
                $this->SetSizes(array(12));
            }else{
                $this->SetSizes(array(9));
            }                
            $this->SetLineHeight(5);
            $this->SetStyles(array('B'));
            $this->SetAligns('C');
            $this->Row(Array(iconv("utf-8","windows-1252",$this->nombreempresa)));
            $this->SetX($this->margen);
            $this->Row(Array(iconv("utf-8","windows-1252",$this->razonsocial)));
            $this->SetX($this->margen);
            $this->Row(Array(iconv("utf-8","windows-1252",$this->direccion)));
            $this->SetX($this->margen);
            $this->Row(Array(iconv("utf-8","windows-1252",$this->RFC)));
            $this->SetX($this->margen);
            $this->RoundedRect($this->margen, 30, $this->body, 2, 1, 'F');

        }    
    }
/*
    function Footer() {
        $this->SetX($this->margen);
        $this->SetWidths(Array(76));
        $this->SetStyles('B');
        $this->SetAligns(array('C'));
        $this->Row(array($this->piecentro));
        $this->Ln(5);

        $this->SetX($this->margen);
        $this->SetSizes(array(8,8));
        $this->SetWidths(Array(38, 38));
        $this->SetStyles('B');
        $this->SetAligns(array('L', 'R'));
        $this->Row(array($this->pieizquierdo, $this->piederecho));
    }*/

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

$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('12');
foreach ($encabezado as $actual) {
    $titulo = $actual['tituloencabezado'];
    $datos_empresa = $actual['titulocarta'];
    $colortit = $actual['colortitulo'];
    $colorlinea = $actual['colorceltitulo'];
    $colortext = $actual['colortexto'];
    $pieizquierdo = $actual['pagina'];
    $piederecho = $actual['correo'];
    $anchoticket = $actual['numpag'];
    $imgticket = $actual['imglogo'];
    $piecentral = $actual['observacionescot'];
}

$datacorp = explode('</>', $datos_empresa);
$rgbc = explode("-", $cc->hex2rgb($colortit));
$rgbs = explode("-", $cc->hex2rgb($colorlinea));

$cv = new ControladorVenta();
if (isset($_GET['t'])) {
    $id_ticket = $_GET['t'];
    $sello = $_GET['imagen'];
} else if (isset($_POST['id'])) {
    $id_ticket = $_POST['id'];
    $sello = "";
}

$tickets = $cv->getDatosTicket($id_ticket);
foreach ($tickets as $actual) {
    $folio = $actual['folio'];
    $fechaventa = $actual['fecha_venta'];
    $horaventa = $actual['hora_venta'];
    $totalventa = $actual['totalventa'];
    $fmpago = $actual['formapago'];
    $monto = $actual['montopagado'];
    $cambio = $actual['cambio'];
    $referencia = $actual['ref_venta'];
    $descuento = $actual['descuento'];
}

$divideF = explode("-", $fechaventa);
$mes = $cv->translateMonth($divideF[1]);
$fecha_creacion2 = $divideF[2] . '/' . $mes . '/' . $divideF[0];

$fecha = getdate();
$d = $fecha['mday'];
$m = $fecha['mon'];
$y = $fecha['year'];
$h = $fecha['hours'];
$mi = $fecha['minutes'];

if ($d < 10) {
    $d = "0$d";
}
if ($m < 10) {
    $m = "0$m";
}
if ($h < 10) {
    $h = "0$h";
}
if ($mi < 10) {
    $mi = "0$mi";
}
$mes2 = $cv->translateMonth($m);
$hoy = "$d/$mes2/$y";

$alto_prod = $cv->ObtenerLargoTicket($id_ticket);
//$base = 113;
$base = ($anchoticket >= 80)? 113 : 120;
$largo_ticket = $base + $alto_prod;

$pdf = new PDF('P', 'mm', array($anchoticket, $largo_ticket));

if($anchoticket >= 80){
    $pdf->margen = bcdiv(($anchoticket - 76) / 2, '1', 1);
    $pdf->body = 76;
}else{
    $pdf->margen = 5;
    $pdf->body = ($anchoticket - 10);
}

$pdf->chkdata = $datacorp[0];
$pdf->nombreempresa = $datacorp[1];
$pdf->razonsocial = $datacorp[2];
$pdf->direccion = $datacorp[3];
$pdf->RFC = $datacorp[4];
$pdf->clrtitulo = $colortit;
$pdf->colorcelda = $colorlinea;
$pdf->colortitulo = $cc->hex2rgb($colortit);
$pdf->celdatitulo = $cc->hex2rgb($colorlinea);
$pdf->imglogo = $imgticket;

$pdf->piecentro = $piecentral;
$pdf->pieizquierdo = $pieizquierdo;
$pdf->piederecho = $piederecho;

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);
$pdf->SetTopMargin(3);
$pdf->SetAutoPageBreak(true,3);

$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

$pdf->SetY(34.3);
$pdf->SetX($pdf->margen);
$pdf->SetWidths(Array($pdf->body));
$pdf->SetRowBorder('NB');
$pdf->SetStyles(array(''));
$pdf->setRowColorText(array($colortit));
if($pdf->body >= 76){
    $pdf->SetSizes(array(15));
    $pdf->SetLineHeight(7);
}else{
    $pdf->SetSizes(array(13));
    $pdf->SetLineHeight(4);
}
$pdf->SetAligns('C');
$pdf->Row(Array(iconv("utf-8","windows-1252",$titulo)));
$pdf->Ln(3);

$pdf->SetX($pdf->margen);
$pdf->SetWidths(Array($pdf->body));
$pdf->setRowColorText(array($colortext));
if($pdf->body >= 76){
    $pdf->SetSizes(array(10));
    $pdf->SetLineHeight(5);
}else{
    $pdf->SetSizes(array(6));
    $pdf->SetLineHeight(4);
}

$pdf->SetAligns('L');
$pdf->Row(Array(iconv("utf-8","windows-1252",'VENTA: '.$folio)));
$pdf->SetX($pdf->margen);
$pdf->SetWidths(array(bcdiv(($pdf->body / 2),'1',1), bcdiv(($pdf->body / 2),'1',1)));
$pdf->SetSizes(array(1, 1));
$pdf->SetLineHeight(1);
$pdf->Row(array('', ''));

$pdf->SetX($pdf->margen);
$pdf->SetStyles(array('B', ''));
$pdf->SetAligns(array('L', 'L'));
if($pdf->body >= 76){
    $pdf->SetSizes(array(8, 8));
    $pdf->SetLineHeight(5);
}else{
    $pdf->SetSizes(array(6, 6));
    $pdf->SetLineHeight(4);
}
$pdf->Row(array('FECHA VENTA', $fecha_creacion2 . ' ' . $horaventa));

$pdf->SetX($pdf->margen);
$pdf->SetStyles(array('B', ''));
$pdf->SetAligns(array('L', 'L'));
$pdf->Row(Array('FECHA IMPRESION',  $hoy . ' ' . $h.':'.$mi));
$pdf->Ln(1);
$pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
$pdf->Ln(1);

$pdf->SetX($pdf->margen);
//$pdf->SetWidths(Array(32, 16, 12, 16));
$pdf->SetWidths(Array(bcdiv(($pdf->body * 0.42),'1',1), bcdiv(($pdf->body * 0.20),'1',1), bcdiv(($pdf->body * 0.15),'1',1), bcdiv(($pdf->body * 0.23),'1',1)));
$pdf->SetStyles('');
$pdf->setRowColorText(array($colortext));
if($pdf->body >= 76){
    $pdf->SetSizes(array(8, 8, 8, 8));
    $pdf->SetLineHeight(5);
}else{
    $pdf->SetSizes(array(5.6, 5.6, 5, 5.6));
    $pdf->SetLineHeight(2);
} 
$pdf->SetAligns(array('L', 'L', 'C', 'L'));
$pdf->Row(Array(iconv("utf-8","windows-1252",'PRODUCTO'), iconv("utf-8","windows-1252",'UNIT'), iconv("utf-8","windows-1252",'CANT'), iconv("utf-8","windows-1252",'IMP')));
$pdf->Ln(1);

if($pdf->body >= 76){
    $pdf->SetSizes(array(8, 8, 8, 8));
}else{
    $pdf->SetSizes(array(5.6, 5.6, 5.6, 5.6));
} 

$tottraslados = 0;
$totretencion = 0;
$totprod = 0;
$subtotal = 0;
$detalle = $cv->getDetalleTicket($id_ticket);
foreach ($detalle as $actual) {
    $cod = $actual['venta_codprod'];
    $prod =  strtoupper($actual['venta_producto']);
    $unit = $actual['venta_precio'];
    $cant = $actual['venta_cant'];
    $importe = $actual['venta_importe'];
    $traslados = $actual['venta_traslados'];
    $retenciones = $actual['venta_retencion'];

    //sacamos el iva del precio unitario ya que no quieren tener desglosado el ticket para el cliente
    /*$iva_unit = round(($unit * 0.16), 2);
    $unit = $unit + $iva_unit;*/
    $tot_iva_unit = 0;
    if ($traslados != "") {
        $divtras = explode("<impuesto>", $traslados);
        foreach ($divtras as $tras) {
            $div = explode("-", $tras);
            $iva_unit = round(($unit * $div[1]),2);
            $tot_iva_unit += $iva_unit;
            //$tottraslados += bcdiv($div[0], '1', 2);
            $iva_prod = bcdiv($div[0], '1', 2);
            //$tottraslados += $iva_prod;
            //Al importe le sumamos el IVA tambien para que el subtotal venga ya con impuestos
            $importe += $iva_prod;
        }
    }

    $tot_ret_unit = 0;
    if ($retenciones != "") {
        $divret = explode("<impuesto>", $retenciones);
        foreach ($divret as $ret) {
            $div2 = explode("-", $ret);
            $ret_unit = round(($unit * $div2[1]),2);
            $tot_ret_unit += $ret_unit;
            //$totretencion += bcdiv($div2[0], '1', 2);
            $ret_prod = bcdiv($div2[0], '1', 2);
            $importe -= $ret_prod;
        }
    }

    $unit = round((($unit + $tot_iva_unit) - $tot_ret_unit),2);

    $subtotal += $importe;

    //redondeo a 1 cuando alican el precio a productos como cheetos o productos a granel
    if($cant > 0 && $cant < 1){
        $cant = 1;
    }

    $totprod = $totprod + $cant;
    $pdf->SetX($pdf->margen);
    $pdf->SetLineHeight(4);
    $pdf->Row(Array(iconv("utf-8","windows-1252",$cod . ' ' . $prod), iconv("utf-8","windows-1252",'$' . number_format($unit, 2, '.', ',')), iconv("utf-8","windows-1252",$cant), iconv("utf-8","windows-1252",'$' . number_format($importe, 2, '.', ','))));
    //$pdf->Row(Array(iconv("utf-8","windows-1252",$cod . ' ' . $prod), iconv("utf-8","windows-1252",number_format($unit, 2, '.', ',')), iconv("utf-8","windows-1252",$cant), iconv("utf-8","windows-1252",number_format($importe, 2, '.', ','))));
    $pdf->Ln(1);
}

$pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
$pdf->Ln(1);

$pdf->SetX($pdf->margen);
$pdf->SetWidths(Array(bcdiv(($pdf->body * 0.62),'1',1), bcdiv(($pdf->body * 0.38),'1',1)));
$pdf->SetStyles('B');
$pdf->SetAligns(array('R', 'L'));
$pdf->Row(array('CANT PRODUCTOS:', '   '.$totprod));
$pdf->Ln(1);

$pdf->SetX($pdf->margen);
$pdf->Row(array('SUBTOTAL:', iconv("utf-8","windows-1252",'   $ ' . number_format($subtotal, 2, '.', ','))));
$pdf->Ln(1);

$pdf->SetX($pdf->margen);
$pdf->Row(array('DESCUENTO:', iconv("utf-8","windows-1252",'   $ ' . number_format($descuento, 2, '.', ','))));
$pdf->Ln(1);

/*if ($tottraslados > 0) {
    $pdf->SetX($pdf->margen);
    $pdf->Row(array('IMPUESTOS:', iconv("utf-8","windows-1252",'$ ' . number_format($tottraslados, 2, '.', ','))));
    $pdf->Ln(1);
}*/

/*if ($totretencion > 0) {
    $pdf->SetX($pdf->margen);
    $pdf->Row(array('RETENCIONES:', iconv("utf-8","windows-1252",'$ ' . number_format($totretencion, 2, '.', ','))));
    $pdf->Ln(1);
}*/

$pdf->SetX($pdf->margen);
$pdf->Row(array('TOTAL VENTA:', iconv("utf-8","windows-1252",'   $ ' . number_format($totalventa, 2, '.', ','))));
$pdf->Ln(1.5);
$pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
$pdf->Ln(1);

$pdf->SetX($pdf->margen);
$pdf->SetStyles('B');
$pdf->SetWidths(array(bcdiv(($pdf->body / 2),'1',1), bcdiv(($pdf->body / 2),'1',1)));
$pdf->SetAligns(array('L', 'L'));

if ($fmpago == 'cash') {
    $pdf->Row(array('FORMA DE PAGO:', 'Efectivo'));

    $pdf->SetX($pdf->margen);
    $pdf->Row(array('EFECTIVO:', iconv("utf-8","windows-1252",'$ ' . number_format($monto, 2, '.', ','))));

    $pdf->SetX($pdf->margen);
    $pdf->Row(array('CAMBIO:', iconv("utf-8","windows-1252",'$ ' . number_format($cambio, 2, '.', ','))));
    $pdf->Ln(1);
    $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
    $pdf->Ln(1);
} else if ($fmpago == 'card') {
    $pdf->Row(array('FORMA DE PAGO:', 'Tarjeta'));
    
    $pdf->SetX($pdf->margen);
    $pdf->Row(array('REFERENCIA:', $referencia));
    $pdf->Ln(1);
    $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
    $pdf->Ln(1);
} else if ($fmpago == 'val') {
    $pdf->Row(array('FORMA DE PAGO:', 'Vales de despensa'));
    
    $pdf->SetX($pdf->margen);
    $pdf->Row(array('REFERENCIA:', $referencia));
    $pdf->Ln(1);
    $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
    $pdf->Ln(1);
}

$posicionInicialY = $pdf->GetY();
if (!empty($sello)) {
    $posicionY = 80; 
    $pdf->SetY($posicionY);
    $pdf->SetX($pdf->margen);
    $pdf->Image($sello, $pdf->GetX(), $posicionY, 50); 
    $posicionY += 60; 
    $pdf->SetY($posicionY);
    $pdf->SetX($pdf->margen);
    $pdf->SetY($posicionInicialY);
}


$pdf->SetX($pdf->margen);
$pdf->SetWidths(Array($pdf->body));
$pdf->SetStyles('B');
$pdf->SetAligns(array('C'));
$pdf->Row(array($piecentral));
$pdf->Ln(3);

$pdf->SetX($pdf->margen);
$pdf->SetWidths(array(bcdiv(($pdf->body / 2),'1',1), bcdiv(($pdf->body / 2),'1',1)));
$pdf->SetStyles('B');
$pdf->SetAligns(array('L', 'R'));
$pdf->Row(array($pieizquierdo, $piederecho));

$pdf->isFinished = true;
$pdf->Output();
?>