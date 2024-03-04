<?php


require_once '../vendor/autoload.php';
require_once '../pdf/fpdf/fpdf.php';
require_once '../pdf/fpdf/NumeroALetras.php';
require_once '../com.sine.controlador/ControladorConfiguracion.php';

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

    function RowWithColor($data, $colors, $estadoIndex) {
        $x = $this->GetX();
        $y = $this->GetY();
        $estado = $data[$estadoIndex];
        $color = isset($colors[$estado]) ? $colors[$estado] : array(0, 0, 0); // Color por defecto: negro
        foreach ($data as $i => $value) {
        if ($i === $estadoIndex) {
        $this->SetFillColor($color[0], $color[1], $color[2]);
        } else {
        $this->SetFillColor(255, 255, 255); 
        }
        $this->SetTextColor(0, 0, 0); 
        $this->Cell($this->widths[$i], 7, $value, 1, 0, 'C', true);
        }
        $this->SetXY($x, $y + 7);
    }
   
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
            //$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
            $nb = max($nb, $this->NbLines($this->widths[$i] ?? 0, $data[$i] ?? ''));
        }

        //multiply number of line with line height. This will be the height of current row
        $lh = $this->lineHeight;
        $h = $lh * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of current row
        for ($i = 0; $i < count($data); $i++) {
            // width of the current col
            //$w = $this->widths[$i];
            $w = $this->widths[$i] ?? 0;
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
        if ($this->idencabezado == '12') {
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
                $this->SetAligns('C');
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
        } else {
            require_once '../com.sine.controlador/ControladorConfiguracion.php';
            $cc = new ControladorConfiguracion();

            $rgbt = explode("-", $cc->hex2rgb($this->clrtitulo));
            $rgbc = explode("-", $cc->hex2rgb($this->colorcelda));

            $this->SetFont('Arial', '', 19);
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
            $this->Cell(25, 5, $this->Image($logo, $this->GetX() + 2.5, $this->GetY(), 20, $height), 0, 0, 'C', false);
            $this->RoundedRect(35, $this->GetY(), 120, 8, 4, 'F');
            $this->SetX(38);
            $this->Write(8, $this->titulo);

            $this->SetX(160);
            $this->RoundedRect(160, $this->GetY(), 45, 8, 4, 'F');
            $this->SetX(173.5);
            $this->Write(8, 'Folio');

            $this->SetY(18);
            $this->SetX(160);
            $this->SetTextColor(255, 0, 0);
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(45, 8, iconv("utf-8","windows-1252",'001'), 0, 0, 'C', false);

            $this->Ln(26);
        }
    }

    function Footer() {
        if ($this->idencabezado == '12') {
            
        } else {
            require_once '../com.sine.controlador/ControladorConfiguracion.php';
            $cc = new ControladorConfiguracion();
            $rgb = explode("-", $cc->hex2rgb($this->clrpie));
            $pagin = "";
            if ($this->chnum == '1') {
                $pagin = iconv("utf-8","windows-1252",'Pagina ' . $this->PageNo() . ' de {nb}');
            }

            $this->SetY(-18);
            if ($this->isFinished) {
                $chfirmar = $this->chfirmar;
                if ($chfirmar == '1') {
                    $firma = $cc->getFirma($this->iddatos);
                    $this->Image($firma, 75, ($this->GetY() - 25), 60, 0, 'png');
                }
            }
            $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(65, 4, $this->pagina, 0, 0, 'L');
            $phone = "Tel: " . $this->tel1;
            if ($this->tel2 != "") {
                $this->Cell(65, 4, '', 0, 0, 'C');
                $this->Cell(65, 4, "Tel: " . $this->tel1, 0, 0, 'R');
                $phone = "Tel: " . $this->tel2;
            }
            $this->Ln(4);
            $this->Cell(65, 4, $this->correo, 0, 0, 'L');
            $this->Cell(65, 4, $pagin, 0, 0, 'C');
            $this->Cell(65, 4, $phone, 0, 0, 'R');
        }
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

$idencabezado = $_POST['idencabezado'];
$titulo = $_POST['titulo'];
$clrtitulo = $_POST['clrtitulo'];
$colorcelda = $_POST['colorcelda'];
$clrcuadro = $_POST['clrcuadro'];
$clrsub = $_POST['clrsub'];
$clrfdatos = $_POST['clrfdatos'];
$txtbold = $_POST['txtbold'];
$clrtxt = $_POST['clrtxt'];
$colorhtabla = $_POST['colorhtabla'];
$tittabla = $_POST['tittabla'];
$pagina = $_POST['pagina'];
$correo = $_POST['correo'];
$tel1 = $_POST['tel1'];
$tel2 = $_POST['tel2'];
$clrpie = $_POST['clrpie'];
$imagen = $_POST['imagen'];
$chnum = $_POST['chnum'];
$chlogo = $_POST['chlogo'];
$observaciones = $_POST['observaciones'];
$anchoTicket = $_POST['widthticket'];
$titulo2 = $_POST['titulo2'];
$rfcempresa = $_POST['rfcempresa'];
$direccion = $_POST['direccion'];
$razonsocial = $_POST['razonsocial'];
$nombreempresa = $_POST['nombreempresa'];
$chkdata = $_POST['chkdata'];


$rgbc = explode("-", $cc->hex2rgb($clrcuadro));
$rgbs = explode("-", $cc->hex2rgb($clrsub));
$rgbfd = explode("-", $cc->hex2rgb($clrfdatos));
$rgbbld = explode("-", $cc->hex2rgb($txtbold));
$rgbtxt = explode("-", $cc->hex2rgb($clrtxt));
$rgbt = explode("-", $cc->hex2rgb($colorhtabla));
$rgbtt = explode("-", $cc->hex2rgb($tittabla));

if( $idencabezado == 12){
    $pdf = new PDF('P', 'mm', array($anchoTicket, 150));
    if($anchoTicket >= 80){
        $pdf->margen = bcdiv(($anchoTicket - 76) / 2, '1', 1);
        $pdf->body = 76;
    }else{
        $pdf->margen = 5;
        $pdf->body = ($anchoTicket - 10);
    }
    
    $pdf->chkdata = $chkdata;
    $pdf->nombreempresa = $nombreempresa;
    $pdf->razonsocial = $razonsocial;
    $pdf->direccion = $direccion;
    $pdf->RFC = $rfcempresa;
} else {
    $pdf = new PDF('P', 'mm', 'Letter');
}

$pdf->idencabezado = $idencabezado;
$pdf->imglogo = $imagen;
$pdf->titulo = $titulo;
$pdf->clrtitulo = $clrtitulo;
$pdf->colorcelda = $colorcelda;
$pdf->colortitulo = $cc->hex2rgb($clrtitulo);
$pdf->celdatitulo = $cc->hex2rgb($colorcelda);

$pdf->pagina = $pagina;
$pdf->correo = $correo;
$pdf->tel1 = $tel1;
$pdf->tel2 = $tel2;
$pdf->clrpie = $clrpie;
$pdf->chnum = $chnum;

$pdf->chfirmar = '';
$pdf->iddatos = '';
$pdf->tipofactura = '';

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

switch ($idencabezado) {
    case 1:
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Datos del Emisor"));
        $pdf->Ln(1);

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, 45.3, 195, 19, 2, 'F');

        $pdf->SetLineHeight(4.5);

        $pdf->SetY(45.3);

        $pdf->SetX(10);
        $pdf->SetWidths(Array(15, 94.5, 30, 55));
        $pdf->SetRowBorder('');
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetLineHeight(0.1);
        $pdf->Row(Array('', '', '', ''));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetLineHeight(4.5);
        $pdf->Row(Array('Nombre', iconv("utf-8","windows-1252",'Contribuyente'), 'No Certificado', iconv("utf-8","windows-1252",'007')));

        $pdf->SetX(10);
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->Row(Array('RFC', iconv("utf-8","windows-1252",'XAXX010101000'), 'Regimen Fiscal', iconv("utf-8","windows-1252",'01 Regimen')));

        $pdf->SetX(10);
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Fecha', iconv("utf-8","windows-1252",'01/01/2021'), 'T. Comprobante', iconv("utf-8","windows-1252",'I Ingreso')));

        $pdf->SetWidths(Array(35, 77.5, 25, 58));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Lugar de Expedicion', iconv("utf-8","windows-1252",'76800'), '', ''));
        $pdf->Ln(1);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Datos del Cliente"));

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, ($pdf->GetY() + 0.7), 195, 14, 2, 'F');

        $pdf->SetY(($pdf->GetY() + 0.7));
        $pdf->SetWidths(Array(17, 94.5, 23, 60));
        $pdf->SetLineHeight(0.1);
        $pdf->Row(Array('', '', '', ''));
        $pdf->SetLineHeight(4.5);
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Nombre', 'Receptor', 'Uso de CFDI', 'P01 Por Definir'));

        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('RFC', 'XAXX010101000', '', ''));

        $pdf->SetWidths(Array(17, 174.5));
        $pdf->SetLineHeight(4.5);
        $pdf->SetStyles(array('B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt));
        $pdf->Row(Array('Direccion', iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));
        $pdf->Ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
        $pdf->SetX(92.5);
        $pdf->Write(6, 'CONCEPTOS');
        $pdf->Ln(7);

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
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetWidths(Array(15, 20, 25, 46, 45, 22, 22));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetStyles('');
        $pdf->setRowColorText();
        $pdf->SetRowBorder('B');
        $pdf->SetAligns('C');
        $pdf->Row(Array('1', '01', 'H87 Pieza', 'Descripcion de Producto', '', '$ 100.00', '$ 100.00'));

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Subtotal ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, '$ 100.00', 1, 0, 'C', 0);
        $pdf->Ln(8);

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'IVA ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, '$ 16.00', 1, 0, 'C', 0);
        $pdf->Ln(8);

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Total ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, '$ 116.00', 1, 0, 'C', 0);
        $pdf->Ln(15);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Importe con Letra ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $letras = NumeroALetras::convertir(bcdiv(116.00, '1', 2), 'pesos', 'centavos');
        $pdf->Cell(26, 8, iconv("utf-8","windows-1252","$letras 00/100 M.N."), 0, 0, 'L', 0);
        $pdf->Ln(8);


        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');
        break;
    case 2:
         
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Datos del Emisor"));
        $pdf->Ln(1);

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, 45.3, 195, 15, 2, 'F');
        $pdf->SetWidths(Array(24, 113, 22, 35.5));
        $pdf->SetLineHeight(4.5);

        $pdf->SetY(45);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Razon Social');
        $pdf->SetX(147.5);
        $pdf->Write(5, 'RFC Emisor');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'Contribuyente'), '', iconv("utf-8","windows-1252",'XAXX010101000')));

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Regimen Fiscal');
        $pdf->SetX(147.5);
        $pdf->Write(5, 'Fecha Emision');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'01-Regimen'), '', iconv("utf-8","windows-1252",'01-01-2021')));

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetWidths(Array(24, 170.5));
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Direccion');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));

        $pdf->Ln(1);

        $pdf->SetFont('Arial', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Datos del Cliente"));

        $pdf->Ln(0.5);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, ($pdf->GetY() + 0.4), 195, 25, 2, 'F');

        $pdf->SetY(($pdf->GetY() + 0.4));
        $pdf->SetWidths(Array(30, 110, 23, 31.5));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Nombre');
        $pdf->SetX(151.5);
        $pdf->Write(5, 'RFC');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'Cliente'), '', iconv("utf-8","windows-1252",'XAXX010101000')));

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Uso del CFDI');
        $pdf->SetX(151.5);
        $pdf->Write(5, 'Fecha y Hora');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'P01-Por Definir'), '', iconv("utf-8","windows-1252",'01-01-2021')));

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Metodo, Forma y');
        $pdf->SetX(151.5);
        $pdf->Write(5, 'Moneda');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', 'XAXX010101000', '', iconv("utf-8","windows-1252",'MXN')));

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Condiciones de pago');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'PPD-Pago en parcialidades o diferido '), '', ''));

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(5, 'Direccion');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetWidths(Array(30, 177.8));
        $pdf->SetLineHeight(4.5);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->SetX(10);
        $pdf->RowNB(Array('', iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));
        $pdf->Ln(1.2);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'FD');
        $pdf->Cell(195, 6, 'CFDIS RELACIONADOS', 0, 0, 'C');
        $pdf->Ln(7);
        $pdf->SetWidths(Array(30, 24, 20, 23, 17, 16.7, 25, 20, 20));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 9, 2, 'FD');
        $pdf->RowNBC(Array(iconv("utf-8","windows-1252",'UUID'), iconv("utf-8","windows-1252",'FOLIO'), iconv("utf-8","windows-1252",'METODO PAGO'), iconv("utf-8","windows-1252",'TOTAL FACTURA'), iconv("utf-8","windows-1252",'MONEDA/CAMBIO'), iconv("utf-8","windows-1252",'PARC.'), iconv("utf-8","windows-1252",'ANTERIOR'), iconv("utf-8","windows-1252",'PAGADO'), iconv("utf-8","windows-1252",'RESTANTE')));
        //cambios nuevos 
        $pdf->setRowColorText($clrtxt);
        $pdf->SetStyles('');
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('B');
        $pdf->Row(Array('123e4567-e89b-12d3-a456-42661417','ABC123DEF456GHI789','targeta Debito','12,34.00','MXN','P1','12,34.00','12,00.00','34.00'));
        $pdf->Row(Array('123e4567-e89b-12d3-a456-42661417','ABC123DEF456GHI789','targeta Debito','34.00','MXN','P2','34.00','33.00','10.00'));
       
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX(140.7);
        $pdf->SetWidths(Array(25, 20, 20));
        $pdf->SetLineHeight(4.5);
        $pdf->SetAligns(array('R', 'C', 'C'));
        $pdf->Row(Array(iconv("utf-8","windows-1252","Total Pagado: "), iconv("utf-8","windows-1252","$ " . number_format('1234.00', 2, '.', ',')), iconv("utf-8","windows-1252",'MXN')));
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'BI', 8);
            $pdf->Write(10, iconv("utf-8","windows-1252","*Este documento no posee efectos fiscales"), '');
       
            $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
            $pdf->RoundedRect(10, $pdf->GetY(), 95, 30, 2, 'F');
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(40);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(10, iconv("utf-8","windows-1252","Folio Fiscal (UUID): "), '');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(40);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Write(10, iconv("utf-8","windows-1252",'123e4567-e89b-12d3-a456-42661417'), '');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(40);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(10, iconv("utf-8","windows-1252","N° Certificado SAT: "), '');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(40);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Write(10, iconv("utf-8","windows-1252",'ABC123DEF456GHI789'), '');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(40);
            $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
            $pdf->Write(10, iconv("utf-8","windows-1252","Fecha de Certificacion: "), '');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(40);
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->Write(10, iconv("utf-8","windows-1252",'2024-02-10'), '');
            $pdf->Ln(11.5);

            $pdf->SetFont('Arial', 'B', 9);

            $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
            $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
            $pdf->RoundedRect(10, $pdf->GetY(), 35, 5, 2, 'F');
            $pdf->SetX(12);
            $pdf->Write(5, iconv("utf-8","windows-1252","Sello CFDI"), '');

            $pdf->RoundedRect(75, $pdf->GetY(), 35, 5, 2, 'F');
            $pdf->SetX(77);
            $pdf->Write(5, iconv("utf-8","windows-1252","Sello SAT"), '');

            $pdf->RoundedRect(140, $pdf->GetY(), 35, 5, 2, 'F');
            $pdf->SetX(142);
            $pdf->Write(5, iconv("utf-8","windows-1252","Cadena Original"), '');
            $pdf->Ln(5);
            $pdf->SetWidths(Array(62.5, 2.5, 62.5, 2.5, 65));
            $pdf->SetLineHeight(2.5);
            $pdf->SetFont('Arial', '', 5);
            $pdf->rgbfd0 = $rgbfd[0];
            $pdf->rgbfd1 = $rgbfd[1];
            $pdf->rgbfd2 = $rgbfd[2];
            $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
            $pdf->RowR(Array(iconv("utf-8","windows-1252",'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), "",
                             iconv("utf-8","windows-1252",'0987654321ZYXWVUTSRQPONMLKJIHGFEDCBA'), "",
                             iconv("utf-8","windows-1252",'1234567890ABCDEFGHI')));
            $pdf->SetFont('Arial', '', 9);
            $pdf->Write(8, iconv("utf-8","windows-1252","Este documento es una representacion impresa de un cfdi-."), '');
        
            $pdf->SetFont('Arial', 'BI', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(10, 3, '*El pago ' , '$foliopago' , ' ha sido oficialmente cancelado', 0, 0, 'L');
        
            $pdf->isFinished = true;
       

        break;
    case 3:
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetWidths(Array(40));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("FECHA: 01/01/2021"));

        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Datos de la Cotizacion"));

        $pdf->SetWidths(array(15, 72, 32, 75.5));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', '', 9);
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 10, 2, 'F');
        $pdf->Row(Array("Nombre", iconv("utf-8","windows-1252",'Cliente cotizacion'), "Lugar de Expedicion", iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: Imaginaria, Municipio, Estado')));

        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
        $pdf->SetX(92.5);
        $pdf->Write(6, 'CONCEPTOS');
        $pdf->Ln(6.7);

        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths(Array(10, 22, 25, 19, 44, 35, 20, 20));
        $pdf->SetLineHeight(4.5);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 4.5, 2, 'FD');
        $pdf->RowNBC(Array("Cant", "Codigo", "Imagen", "Unidad", "Descripcion", "Observaciones", "Precio", "Importe"));
        $pdf->SetTextColor(0, 0, 0);

        $file = '../img/monitor.jpg';
        $dimensiones = getimagesize($file);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $height = ($height * 25) / $width;
        if ($height > 22.1) {
            $height = 22.1;
        }

        $nj = $height / 4.42;
        $j = "";
        for ($i = 1; $i <= number_format($nj); $i++) {
            $j .= "\n";
        }

        $pdf->Cell(194.5, 24, '', 0, 0, 'C', 0);
        $img = $pdf->Image($file, 42, $pdf->GetY(), 25, $height) . $j;
        $pdf->SetX(-205.9);

        $pdf->setRowColorText($clrtxt);
        $pdf->SetStyles('');
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('B');
        $pdf->Row(Array('1', iconv("utf-8","windows-1252",'01'), $img, 'E48', iconv("utf-8","windows-1252",'Producto'), iconv("utf-8","windows-1252",''), iconv("utf-8","windows-1252",'$ ' . number_format('100', 2, '.', ',')), iconv("utf-8","windows-1252",'$ ' . number_format('100', 2, '.', ','))));

        $pdf->SetWidths(Array(20, 20));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX(165);
        $pdf->Row(Array('Subtotal', iconv("utf-8","windows-1252",'$ ' . number_format('100', 2, '.', ''))));

        $pdf->SetX(165);
        $pdf->Row(Array('IVA', iconv("utf-8","windows-1252",'$ ' . number_format('16', 2, '.', ''))));

        $pdf->SetX(165);
        $pdf->Row(Array('Ret IVA', iconv("utf-8","windows-1252",'$ ' . number_format('4', 2, '.', ''))));

        $pdf->SetX(165);
        $pdf->Row(Array('Total', iconv("utf-8","windows-1252",'$ ' . number_format('112', 2, '.', ''))));
        $pdf->Ln(2);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');

        $pdf->Ln(5);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $obser = str_replace("<ent>", "\n", $observaciones);
        $pdf->Write(3, iconv("utf-8","windows-1252",'Observaciones: ' . $obser));

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->SetWidths(Array(85, 85));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->RowR(Array('Datos Fiscales:', 'Datos Transferencia Bancaria: '));

        $pdf->SetWidths(Array(85, 85));
        $pdf->SetLineHeight(4.5);
        $pdf->RowR(Array(iconv("utf-8","windows-1252","RFC: XAXX010101000 \nRazon Social: Publico en General \nCalle: Calle Falsa #123 Col. Imaginaria \nLocalidad: Municipio \nEstado: Estado \nCodigo Postal: 76800 \nCorreo: ejemplo@ejemplo \nTel: 4271234567"), iconv("utf-8","windows-1252","Banco: Banco Olmeca    Sucursal: 123 \nBeneficiario: Yo \nCuenta: 7...8 \nClabe:0987654321  \nN° Tarjeta: Olmecard")));
        break;
    case 4:
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths(Array(65));
        $pdf->SetLineHeight(8);
        $pdf->SetX(140);
        $pdf->RowT(Array(iconv("utf-8","windows-1252",'BUENO POR: $' . bcdiv('100', 1, 2))));

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths(Array(125));
        $pdf->SetLineHeight(8);
        $pdf->SetX(80);
        $pdf->RowT(Array(iconv("utf-8","windows-1252",'San Juan del Rio, Queretaro a 01 de Enero de 2021')));

        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Write(8, iconv("utf-8","windows-1252",'Se recibio de Rodrigo Antonio Moran-Clarion la cantidad de $ 555.61 (QUINIENTOS CINCUENTA Y CINCO  PESOS CON SESENTA Y UN  CENTAVOS 61/100 M.N.) por concepto del 50% de anticipo por la cotizacion de servicios con folio COT0008.'));

        $pdf->Ln(4);
        $pdf->SetFont('courier', 'B', 18);
        $pdf->SetTextColor(50, 50, 50);

        $pdf->TextWithRotation(25.5, 143, '01 01 2021', 25.5, 0);
        $pdf->Image('../img/SelloSine2.png', $pdf->GetX(), 100, 70);
        break;
    case 5:
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths(Array(125));
        $pdf->SetLineHeight(6);
        $pdf->SetY(36.3);
        $pdf->SetX(80);
        $pdf->RowT(Array(iconv("utf-8","windows-1252",'San Juan del Rio, Queretaro a 01 de Ene de 2021')));

        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetWidths(Array(195));
        $pdf->RowNBC(Array(iconv("utf-8","windows-1252",'Comunicado')));

        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        //ANGELGM $espL
        $pdf->Write(0 , iconv("utf-8","windows-1252",'Por medio de este comunicado le proporcionamos la siguiente informacion.'));
        $pdf->Ln(5);

        $pdf->Cell(195, 80, '', 0, 0, 'C', 0);
        $pdf->Image('../img/exampleimg.jpg', 10, $pdf->GetY(), 0, 80);
        $pdf->TextWithRotation(25.5, 223, '01 01 2021', 25.5, 0);
        $pdf->Image('../img/SelloSine2.png', 10, 180, 70);
        break;
    case 6:     
        
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
        $pdf->Write(6, 'Del 2024-01-12 al 2024-02-15'); //elimino de variable $fechainicio, $fechafin
        $pdf->Ln(8);

        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths(Array(20, 26, 30, 30, 28, 18, 18, 25));
        $pdf->SetLineHeight(5);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5, 2, 'FD');
        $pdf->RowNBC(Array("Folio", "Fecha creacion", "Emisor", "Cliente", "RFC", "Tipo", "Estado", "Total"));
        $pdf->SetTextColor(0, 0, 0);

        $pdf->RowWithColor(
            array('ABC1234567','26/Dic/2023',' Ficticia S.A. de C.V.','INOV S.A. de C.V.','GEMA841210ABC ','Factura',"Pendiente",'$ 4,755.86 MXN '), 
            array(
                'Pendiente' => array(255, 255, 0), // Amarillo
                'Cancelada' => array(255, 0, 0), // Rojo
                'Pagada' => array(0, 128, 0) // Verde
            ), 
            6 // Índice de la columna "Estado"
        );
        
        $pdf->RowWithColor(
            array('DEF4567890','18/Dic/2023',' Ficticia S.A. de C.V.','INOV S.A. de C.V.','VEMA841210ABC','Factura',"Cancelada",'$ 1,456.00 MXN '), 
            array(
                'Pendiente' => array(255, 255, 0), // Amarillo
                'Cancelada' => array(255, 0, 0), // Rojo
                'Pagada' => array(0, 128, 0) // Verde
            ), 
            6 // Índice de la columna "Estado"
        );
        
        $pdf->RowWithColor(
            array('GHI7890123','15/Dic/2023',' Ficticia S.A. de C.V.','INOV S.A. de C.V.','XEMA841210ABC','Factura',"Pagada",'$ 2,560.75 MXN '), 
            array(
                'Pendiente' => array(255, 255, 0), // Amarillo
                'Cancelada' => array(255, 0, 0), // Rojo
                'Pagada' => array(0, 128, 0) // Verde
            ), 
            6 // Índice de la columna "Estado"
        );
            
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->SetWidths(Array(45, 45, 45));
        $pdf->SetLineHeight(6);
        $pdf->SetX(70);
        $pdf->RowR(Array('Total Facturas Pagadas', 'Total Facturas Pendientes', 'Total Facturas Canceladas'));

        //$pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(70);
        $pdf->RowR(Array(
            iconv("utf-8","windows-1252",'$ ' . number_format(22222, 2, '.', ',') . ' ' . '120'), 
            iconv("utf-8","windows-1252",'$ ' . number_format(44444, 2, '.', ',') . ' ' . '130'), 
            iconv("utf-8","windows-1252",'$ ' . number_format(55555, 2, '.', ',') . ' ' . '140')));

        
            $pdf->Ln(8);
            $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
            $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
            $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
            $pdf->Cell(40, 8, 'Impuestos Trasladados', 1, 0, 'C', 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(35, 8, iconv("utf-8","windows-1252",'$ ' . number_format(1234.56, 2, '.', ',')), 1, 0, 'C', 0);
        
            $pdf->Ln(8);
            $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
            $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
            $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
            $pdf->Cell(40, 8, 'Impuestos Retenidos', 1, 0, 'C', 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(35, 8, iconv("utf-8","windows-1252",'$ ' . number_format(1234.56, 2, '.', ',')), 1, 0, 'C', 0);
        
            $pdf->Ln(8);
            $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
            $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
            $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
            $pdf->Cell(40, 8, 'Total Descuentos', 1, 0, 'C', 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(35, 8, iconv("utf-8","windows-1252",'$ ' . number_format(1234.56, 2, '.', ',')), 1, 0, 'C', 0);
        
            $pdf->Ln(8);
            $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
            $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
            $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
            $pdf->Cell(40, 8, 'Total del Periodo en ' . 'MXN', 1, 0, 'C', 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(35, 8, iconv("utf-8","windows-1252",'$ ' . number_format(1234.56, 2, '.', ',')), 1, 0, 'C', 0);
    
        break;
    case 7:
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetY(36.3);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(195));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array('Pagos generados en el periodo:'));
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
        $pdf->SetX(80);
        $pdf->Write(6, 'Del 2024-01-12 al 2024-02-15'); //variable no definida intercambio
        $pdf->Ln(8);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths(Array(20, 30, 32, 32, 30, 25, 26));
        $pdf->SetLineHeight(5);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5, 2, 'FD');
        $pdf->RowNBC(Array(iconv("utf-8","windows-1252","N° de Folio") ,"Facturas Pagadas","Emisor","Cliente", "RFC Cliente", "Fecha de Pago", "Total Pago"));

        $pdf->setRowColorText($clrtxt);
        $pdf->SetStyles('');
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('B');
        $pdf->Row(Array('ABC12', 'FCT-12345 - FCT-67890 - FCT-24680 - FCT-13579 - FCT-ABCDE ', 'Comercializadora Ficticia S.A.', 'Inversiones del Norte S.A. de C.V.', 'EMIF920518ABC', '20/Dic/2023 00:00', '$1000.00 MXN'));
        $pdf->Row(Array('ABC12', 'FCT-67890 - FCT-24680 - FCT-13579 - FCT-ABCDE ', 'Comercializadora Ficticia S.A.', 'Inversiones del Norte S.A. de C.V.', 'EMIF920518ABC', '23/Ene/2023 00:00', '$1089.00 MXN'));
        $pdf->Row(Array('ABC12', 'FCT-12345 - FCT-67890 - FCT-ABCDE ', 'Comercializadora Ficticia S.A.', 'Inversiones del Norte S.A. de C.V.', 'EMIF920518ABC', '26/Ago/2023 00:00', '$1560.00 MXN'));

        $pdf->Ln(8);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->Cell(120, 8, '', 0, 0, 'C', 0);
        $pdf->Cell(40, 8, 'Total del Periodo en MXN:', 1, 0, 'C', 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(35, 8, iconv("utf-8","windows-1252",'$ ' . number_format(3649.00, 2, '.', ',')), 1, 0, 'C', 0);
        //$pdf->Output('Reporte' . $fechainicio . '-' . $fechafin . '' . $nombrecliente . '.pdf', 'I');

        break;
    case 8:
        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

        $pdf->SetWidths(Array(195));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Reporte Bimestral de Impuestos facturados y de recargo"));
        $pdf->Ln(1);

        $type = 2; //añad la varibale erronea
        $pic = "../img/graficaa.jpeg";

        if ($type == "1" ) {
            $pdf->Image($pic, 8, 45, 96, 0, 'png');
            $pdf->Image($pic2, 110, 45, 96, 0, 'png');
            $pdf->Ln(76);
            $pdf->Image($pic3, 60, $pdf->GetY(), 96, 0, 'png');
        } else if ($type == "2") {
            $pdf->Image($pic, 10, 45, 195, 0, 'jpeg');
        }
        break;
    case 9:

        $pdf->SetFont('Arial', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetY(36.3);
        $pdf->SetWidths(Array(195));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array('Reporte de IVA facturado y de recargo'));
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->SetLineHeight(6);
        $pdf->RowR(Array('Periodo: ' .  "Diciembre/2024" ));
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(Array(40, 30, 40, 30, 35, 20));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 6, 2, 'FD');
        $pdf->RowNBC(Array(iconv("utf-8","windows-1252",'UUID'), iconv("utf-8","windows-1252",'RFC Emisor'), iconv("utf-8","windows-1252",'Nombre Emisor'), iconv("utf-8","windows-1252",'RFC Receptor'), iconv("utf-8","windows-1252",'Fecha Emision'), iconv("utf-8","windows-1252",'Monto')));
        $pdf->Ln(1.5);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->setRowColorText($clrtxt);
        $pdf->SetStyles('');
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('B');
        $pdf->Row(Array('XXXCXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX','ABC000000DEF','XYZ BANCO INTERNACIONALS.A.INSTITUCION DE BANCA MULTIPLEGRUPO FINANCIERO XYZ','JALJ020500ABC','2022-12-31 03:28:55','$ 3.0'));
        $pdf->Row(Array('XXXCXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX','ABC000000DEF','XYZ BANCO INTERNACIONALS.A.INSTITUCION DE BANCA MULTIPLEGRUPO FINANCIERO XYZ','JALJ020500ABC','2022-12-31 03:28:55','$ 2.0'));
        $pdf->Row(Array('XXXCXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX','ABC000000DEF','XYZ BANCO INTERNACIONALS.A.INSTITUCION DE BANCA MULTIPLEGRUPO FINANCIERO XYZ','JALJ020500ABC','2022-12-31 03:28:55','$ 5.0'));
        $pdf->Row(Array('XXXCXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX','ABC000000DEF','XYZ BANCO INTERNACIONALS.A.INSTITUCION DE BANCA MULTIPLEGRUPO FINANCIERO XYZ','JALJ020500ABC','2022-12-31 03:28:55','$ 8.0'));
        break;  
    case 10:

        $pdf->SetFont('Arial', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(195));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Ventas generadas en el periodo:"));
        $pdf->SetTextColor(23, 23, 124);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineHeight(6);
        $pdf->RowR(Array('Del 01/12/2023 al 31/12/2023')); 
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(Array(25, 30, 30, 30, 40, 40, 40));
        $pdf->SetLineHeight(4.5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 6, 2, 'FD');
        $pdf->RowNBC(Array(iconv("utf-8","windows-1252",'Folio'),  iconv("utf-8","windows-1252",'Fecha creacion'), iconv("utf-8","windows-1252",'Realizo'), iconv("utf-8","windows-1252",'Total Cot'), iconv("utf-8","windows-1252",'Folio Factura'), iconv("utf-8","windows-1252",'Cliente')));
        $pdf->Ln(1.5);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        //$pdf->RowNBC(Array(iconv("utf-8","windows-1252",'Folio'),  iconv("utf-8","windows-1252",'Fecha creacion'), iconv("utf-8","windows-1252",'Realizo'), iconv("utf-8","windows-1252",'Total Cot'), iconv("utf-8","windows-1252",'Folio Factura'), iconv("utf-8","windows-1252",'Cliente'), iconv("utf-8","windows-1252",'Total Factura')));


        $pdf->setRowColorText($clrtxt);
        $pdf->SetStyles('');
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('B');
        $pdf->Row(Array('ABCD1234', '05/12/2023', 'Sofia Garcia', '$ 3,255.72', 'FACT-123456', 'Ana Martinez'));
        $pdf->Row(Array('EFGH5678', '08/12/2023', 'Maria Rodriguez', '$ 1,158.84', 'INV-789012', 'Innovacorp S.A. de C.V.'));
        $pdf->Row(Array('JKLM1234', '14/12/2023', 'Alejandro Perez', '$ 8,886.96', 'FCT-345678', 'Carlos Lopez'));
        $pdf->Row(Array('PQRS9012', '26/12/2023', 'Andrea Lopez', '$ 40,114.95', 'REC-901234', 'carolina adviento'));
        $pdf->Row(Array('XYWZ5678', '08/12/2023', 'Daniel Hernandez', '$ 1,158.84', 'FAC-567890', 'patitosgood S.A de C.V'));
        $pdf->Row(Array('XYXZ5678', '18/12/2023', 'Juan Martinez', '$ 3,428.67', 'NTF-123ABC', 'Laura Rodriguez'));

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
        $pdf->SetWidths(Array(195)); 
        $pdf->SetLineHeight(4.5);
        $pdf->SetTextColor(23, 23, 124);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineHeight(6);
        $pdf->RowR(Array('Ventas Individuales')); 
        $pdf->Ln(4);

        //$pdf->Output('Reporte' . $fechainicio . '-' . $fechafin . '.pdf', 'I');
        break;
    case 11:
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $pdf->RowT(Array("Datos del Emisor"));
        $pdf->Ln(1);

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, 45.3, 195, 19, 2, 'F');

        $pdf->SetLineHeight(4.5);

        $pdf->SetY(45.3);

        $pdf->SetX(10);
        $pdf->SetWidths(Array(15, 94.5, 30, 55));
        $pdf->SetRowBorder('');
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetLineHeight(0.1);
        $pdf->Row(Array('', '', '', ''));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetLineHeight(4.5);
        $pdf->Row(Array('Nombre', iconv("utf-8","windows-1252",'Contribuyente'), 'No Certificado', iconv("utf-8","windows-1252",'007')));

        $pdf->SetX(10);
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->Row(Array('RFC', iconv("utf-8","windows-1252",'XAXX010101000'), 'Regimen Fiscal', iconv("utf-8","windows-1252",'01 Regimen')));

        $pdf->SetX(10);
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Fecha', iconv("utf-8","windows-1252",'01/01/2021'), 'T. Comprobante', iconv("utf-8","windows-1252",'I Ingreso')));

        $pdf->SetWidths(Array(35, 77.5, 25, 58));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Lugar de Expedicion', iconv("utf-8","windows-1252",'76800'), '', ''));
        $pdf->Ln(1);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetFont('Arial', '', 15);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Datos del Cliente"));

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, ($pdf->GetY() + 0.7), 195, 14, 2, 'F');

        $pdf->SetY(($pdf->GetY() + 0.7));
        $pdf->SetWidths(Array(17, 94.5, 23, 60));
        $pdf->SetLineHeight(0.1);
        $pdf->Row(Array('', '', '', ''));
        $pdf->SetLineHeight(4.5);
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Nombre', 'Receptor', 'Uso de CFDI', 'P01 Por Definir'));

        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(array('RFC', 'XAXX010101000', '', ''));

        $pdf->SetWidths(Array(17, 174.5));
        $pdf->SetLineHeight(4.5);
        $pdf->SetStyles(array('B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt));
        $pdf->Row(Array('Direccion', iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));
        $pdf->Ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
        $pdf->SetX(92.5);
        $pdf->Write(6, 'CONCEPTOS');
        $pdf->Ln(7);

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
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetWidths(Array(15, 20, 25, 46, 45, 22, 22));
        $pdf->SetLineHeight(4.5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetStyles('');
        $pdf->setRowColorText();
        $pdf->SetRowBorder('B');
        $pdf->SetAligns('C');
        $pdf->Row(Array('1', '01', 'H87 Pieza', 'Descripcion de Producto', '', '$ 100.00', '$ 100.00'));

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Subtotal ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, '$ 100.00', 1, 0, 'C', 0);
        $pdf->Ln(8);

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'IVA ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, '$ 16.00', 1, 0, 'C', 0);
        $pdf->Ln(8);

        $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(22, 8, 'Total ', 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(22, 8, '$ 116.00', 1, 0, 'C', 0);
        $pdf->Ln(15);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 8, 'Importe con Letra ', 0, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $letras = NumeroALetras::convertir(bcdiv(116.00, '1', 2), 'pesos', 'centavos');
        $pdf->Cell(26, 8, iconv("utf-8","windows-1252","$letras 00/100 M.N."), 0, 0, 'L', 0);
        $pdf->Ln(8);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');

        $pdf->titulo = $titulo2;
        $pdf->AddPage();

        $pdf->heightB = 0;
        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, 36, 195, 10, 2, 'F');

        $pdf->SetY(36.3);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(40, 66, 46, 43));
        $pdf->SetLineHeight(4.5);
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetRowBorder();
        $pdf->SetAligns(array('L'));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Version', iconv("utf-8","windows-1252",'2.0'), 'Distancia Total Recorrida', iconv("utf-8","windows-1252",'100')));

        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetRowBorder();
        $pdf->SetAligns(array('L'));
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->Row(Array('Transporte Internacional', iconv("utf-8","windows-1252",'No'), 'Entrada/Salida de mercancia', iconv("utf-8","windows-1252",'Entrada')));
        $pdf->Ln(2);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetAligns(array('C'));
        $pdf->RowT(Array("Mercancias"));
        $pdf->Ln(1);

        $pdf->setRowColorText();
        $pdf->SetWidths(Array(20, 45, 15, 25, 25, 30, 35));
        $pdf->SetLineHeight(4);
        $pdf->SetAligns(array('C'));
        $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $pdf->setRowColorText(array($tittabla));
        $pdf->SetRowBorder('B', 'FD');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetStyles('B');
        $pdf->Row(Array("Clave Fiscal", "Descripcion", "Cant", "Unidad", "Peso en Kg", "Material Peligroso", "Embalaje"));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetRowBorder('B');
        $pdf->SetStyles();
        $pdf->setRowColorText();
        $pdf->Row(Array(iconv("utf-8","windows-1252",'01'), iconv("utf-8","windows-1252",'Mercancia'), '1', iconv("utf-8","windows-1252",'Pieza'), '10', iconv("utf-8","windows-1252",'No'), iconv("utf-8","windows-1252",'')));
        $pdf->Ln(2);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Origenes"));

        $pdf->SetWidths(Array(28, 66, 33, 68));
        $pdf->SetLineHeight(4.5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 20, 2, 'F');
        $pdf->Row(Array("Remitente", iconv("utf-8","windows-1252",'Nombre Remitente'), "RFC Remitente", iconv("utf-8","windows-1252",'XAXX010101000')));
        $pdf->Row(Array("Tipo", iconv("utf-8","windows-1252",'Origen'), "Distancia Recorrida", iconv("utf-8","windows-1252",'0')));
        $pdf->Row(Array("Fecha Salida", iconv("utf-8","windows-1252",'01/01/2021 12:00'), "", ""));

        $pdf->SetWidths(Array(20, 167));
        $pdf->SetLineHeight(5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt));
        $pdf->SetStyles(array('B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->Row(Array("Direccion", iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));
        $pdf->Ln(1.5);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Destinos"));

        $pdf->SetWidths(Array(28, 66, 33, 68));
        $pdf->SetLineHeight(4.5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 20, 2, 'F');
        $pdf->Row(Array("Destino", iconv("utf-8","windows-1252",'Nombre Remitente'), "RFC Destinatario", iconv("utf-8","windows-1252",'XAXX010101000')));
        $pdf->Row(Array("Tipo", iconv("utf-8","windows-1252",'Destino'), "Distancia Recorrida", iconv("utf-8","windows-1252",'100')));
        $pdf->Row(Array("Fecha Llegada", iconv("utf-8","windows-1252",'02/01/2021 12:00'), "", ""));

        $pdf->SetWidths(Array(20, 167));
        $pdf->SetLineHeight(5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt));
        $pdf->SetStyles(array('B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->Row(Array("Direccion", iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));
        $pdf->Ln(1.5);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Autotransporte Federal"));

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetWidths(Array(30, 70, 27, 68));
        $pdf->SetLineHeight(5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 21, 2, 'F');

        $pdf->SetY($pdf->GetY());
        $pdf->Row(Array("Num Permiso", iconv("utf-8","windows-1252",'1234567'), "Tipo permiso", iconv("utf-8","windows-1252",'01')));
        $pdf->Row(Array(iconv("utf-8","windows-1252","Año Vehiculo"), iconv("utf-8","windows-1252",'2000'), "Placa Vehiculo", iconv("utf-8","windows-1252",'XYZ321')));
        $pdf->Row(Array(iconv("utf-8","windows-1252","Tipo Transporte"), iconv("utf-8","windows-1252",'Camion'), "", ''));
        $pdf->Row(Array(iconv("utf-8","windows-1252","Aseguradora"), iconv("utf-8","windows-1252",'Seguros Polilla'), "Num Poliza", iconv("utf-8","windows-1252",'012421954')));

        $pdf->Ln(1.5);

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->SetWidths(Array(80));
        $pdf->SetAligns(array('C'));
        $pdf->SetLineHeight(8);
        $pdf->RowT(Array("Operadores"));

        $pdf->SetWidths(Array(25, 70, 25, 70));
        $pdf->SetLineHeight(4.5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 195, 15, 2, 'F');

        $pdf->SetY($pdf->GetY());

        $pdf->SetWidths(array(25, 70, 25, 70));
        $pdf->SetLineHeight(4.5);
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
        $pdf->SetStyles(array('B', '', 'B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->Row(Array("Nombre", iconv("utf-8","windows-1252",'Ramiro Perez Martinez'), "RFC", iconv("utf-8","windows-1252",'XAXX010101000')));
        $pdf->Row(Array("Num Licencia", iconv("utf-8","windows-1252",'1234567'), "", ""));

        $pdf->SetWidths(Array(25, 170));
        $pdf->SetLineHeight(4.5);
        $pdf->setRowColorText(Array($txtbold, $clrtxt));
        $pdf->SetStyles(array('B', ''));
        $pdf->SetAligns(array('L'));
        $pdf->SetRowBorder();
        $pdf->Row(array("Direccion", iconv("utf-8","windows-1252",'Calle Falsa #123, Colonia: desconocida, Municipio, Estado')));
        $pdf->Ln(1.5);
        break;
    
    case 12:
        $pdf->SetY(34.3);
        $pdf->SetX($pdf->margen);
        $pdf->SetWidths(Array($pdf->body));
        $pdf->SetRowBorder('NB');
        $pdf->SetStyles(array(''));
        $pdf->setRowColorText(array($clrtitulo));
        if($pdf->body >= 76){
            $pdf->SetSizes(array(15));
            $pdf->SetLineHeight(7);
        }else{
            $pdf->SetSizes(array(13));
            $pdf->SetLineHeight(4);
        }
        $pdf->SetAligns('C');
        $pdf->Row(Array(iconv("utf-8","windows-1252", $titulo)));
        $pdf->Ln(3);

        $pdf->SetX($pdf->margen);
        $pdf->SetWidths(Array($pdf->body));
        $pdf->setRowColorText(array($clrtxt));
        if($pdf->body >= 76){
            $pdf->SetSizes(array(10));
            $pdf->SetLineHeight(5);
        }else{
            $pdf->SetSizes(array(6));
            $pdf->SetLineHeight(4);
        }
        $pdf->SetAligns('L');
        $pdf->Row(Array(iconv("utf-8","windows-1252",'VENTA: 0001')));
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
        $pdf->Row(array('FECHA VENTA', '01/01/2023  00:00'));

        
        $pdf->SetX($pdf->margen);
        $pdf->SetStyles(array('B', ''));
        $pdf->SetAligns(array('L', 'L'));
        $pdf->Row(Array('FECHA IMPRESION',  '01/01/2023  00:00'));
        $pdf->Ln(1);
        $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        //$pdf->SetWidths(Array(32, 16, 12, 16));
        $pdf->SetWidths(Array(bcdiv(($pdf->body * 0.42),'1',1), bcdiv(($pdf->body * 0.20),'1',1), bcdiv(($pdf->body * 0.15),'1',1), bcdiv(($pdf->body * 0.23),'1',1)));
        $pdf->SetStyles('');
        $pdf->setRowColorText(array($clrtxt));
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
        $pdf->SetX($pdf->margen);
        $pdf->SetLineHeight(4);
        $pdf->Row(Array(iconv("utf-8","windows-1252",'010101010101 Nombre Producto'), iconv("utf-8","windows-1252",'$275.00'), iconv("utf-8","windows-1252",'2'), iconv("utf-8","windows-1252",'$550.00')));
        $pdf->Ln(1);        

        $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        $pdf->SetWidths(Array(bcdiv(($pdf->body * 0.62),'1',1), bcdiv(($pdf->body * 0.38),'1',1)));
        $pdf->SetStyles('B');
        $pdf->SetAligns(array('R', 'L'));
        $pdf->Row(array('CANT :', '   2'));
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        $pdf->Row(array('SUBTOTAL:', iconv("utf-8","windows-1252",'   $ 550.00')));
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        $pdf->Row(array('DESCUENTO:', iconv("utf-8","windows-1252",'   $ 0.00')));
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        $pdf->Row(array('TOTAL VENTA:', iconv("utf-8","windows-1252",'   $ s550.00')));
        $pdf->Ln(1.5);
        $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        $pdf->SetStyles('B');
        $pdf->SetWidths(array(bcdiv(($pdf->body / 2),'1',1), bcdiv(($pdf->body / 2),'1',1)));
        $pdf->SetAligns(array('L', 'L'));

        
        $pdf->Row(array('FORMA DE PAGO:', 'Efectivo'));
        $pdf->SetX($pdf->margen);
        $pdf->Row(array('EFECTIVO:', iconv("utf-8","windows-1252",'$ 600.00')));
        $pdf->SetX($pdf->margen);
        $pdf->Row(array('CAMBIO:', iconv("utf-8","windows-1252",'$ 50.00')));
        $pdf->Ln(1);
        $pdf->Rect($pdf->margen, $pdf->GetY(), $pdf->body, 0.2);
        $pdf->Ln(1);

        $pdf->SetX($pdf->margen);
        $pdf->SetWidths(Array($pdf->body));
        $pdf->SetStyles('B');
        $pdf->SetAligns(array('C'));
        $pdf->Row(array($observaciones));
        $pdf->Ln(3);

        $pdf->SetX($pdf->margen);
        $pdf->SetWidths(array(bcdiv(($pdf->body / 2),'1',1), bcdiv(($pdf->body / 2),'1',1)));
        $pdf->SetStyles('B');
        $pdf->SetAligns(array('L', 'R'));
        $pdf->Row(array($pdf->pagina, $pdf->correo));

      
        break;
    case 13:

        $pdf->SetFont('Helvetica', '', 15);
        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);

        $pdf->SetWidths(Array(80));
        $pdf->SetLineHeight(8);
        $pdf->SetY(36.3);
        $dateformat='12/23/2002';
        $pdf->RowT(Array("Fecha de Corte " . $dateformat));
        $pdf->SetY(48);
           
           
        $pdf->SetWidths(Array(22, 173));
        $pdf->SetRowBorder('NB');
        $pdf->SetLineHeight(4.5);
        $pdf->SetSizes(array(13, 13));
        $pdf->SetStyles(array('B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt));
        $pdf->Row(Array('Usuario', iconv("utf-8","windows-1252",'Maximiliano')));//sustituir
        $pdf->Ln(2);
        

        $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
        $pdf->SetWidths(Array(45, 50, 5, 45, 50));
        $pdf->SetLineHeight(0.1);
        $pdf->Row(Array('', '','', '', ''));

        $pdf->SetRowBorder('NB');
        $pdf->SetLineHeight(4.5);
        $pdf->SetSizes(array(13, 13, 13, 13, 13));
        $pdf->SetStyles(array('B', '','', 'B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt, '', $txtbold, $clrtxt));
        $totventas = 34242; //valor
        $totganancia = 4212; //valor
        $pdf->Row(Array('Ventas totales:', "$ " . number_format($totventas, 2, '.', ','),'', 'Ganancias:', "$ " . number_format($totganancia, 2, '.', ',')));

        $pdf->Ln(3);
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('NB');
        $pdf->SetLineHeight(6);
        $pdf->SetSizes(array(13, 13, 13));
        $pdf->SetWidths(Array(95, 5, 95));
        $pdf->SetStyles(array('B', '', 'B'));
        $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold));
        $pdf->Row(Array('Entradas de efectivo', '', 'Dinero en caja'));
        $y_entradas = $pdf->GetY();

        $pdf->SetAligns(array('L', 'C'));
        $pdf->SetSizes(array(9, 9));
        $pdf->SetWidths(Array(40, 55));
        $pdf->SetStyles(array('B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt));
        $pdf->SetLineHeight(0.1);
        $pdf->SetRowBorder('NB');
        $pdf->Row(Array('', ''));

        $pdf->SetRowBorder('B');
        $pdf->SetLineHeight(4.5);
        $pdf->Row(Array('Dinero inicial en caja:', '$ 2,000.00'));
        $pdf->Row(Array('Efectivo:', '$ 540.50'));
        $pdf->Row(Array('PEDIDO DON MAXI:', '$ 767.00'));
        $pdf->Row(Array('Total:', '$ 3,307.50'));
        $y_salidas = $pdf->GetY();

        $pdf->SetY($y_entradas);
        $pdf->SetX(110);
        $pdf->Row(Array(iconv("utf-8","windows-1252",'Ventas en efectivo:'), '$ 34,242.00'));
        $pdf->SetX(110);
        $pdf->Row(Array(iconv("utf-8","windows-1252",'Entradas:'), '$ 3,307.50'));
        $pdf->SetX(110);
        $pdf->Row(Array(iconv("utf-8","windows-1252",'Salidas:'), '$ 18,270.00'));
        $pdf->SetX(110);
        $pdf->Row(Array(iconv("utf-8","windows-1252",'Total:'), '$ 19,279.50'));
        $pdf->SetY($y_salidas);


        $pdf->Ln(3);
        $pdf->SetAligns('C');
        $pdf->SetRowBorder('NB');
        $pdf->SetLineHeight(6);
        $pdf->SetSizes(array(13));
        $pdf->SetWidths(Array(95));
        $pdf->SetStyles(array('B', '', 'B'));
        $pdf->setRowColorText(array($txtbold));
        $pdf->Row(Array('Salidas de efectivo'));


        $pdf->SetAligns(array('L', 'C'));
        $pdf->SetSizes(array(9, 9));
        $pdf->SetWidths(Array(40, 55));
        $pdf->SetStyles(array('B', ''));
        $pdf->setRowColorText(array($txtbold, $clrtxt));
        $pdf->SetLineHeight(0.1);
        $pdf->SetRowBorder('NB');
        $pdf->Row(Array('', ''));

        $salidas = 0;
        $pdf->SetRowBorder('B');
        $pdf->SetLineHeight(4.5);
        $pdf->Row(Array('trapitos:', '$ 2,250.00'));
        $pdf->Row(Array('acuarelas:', '$ 1,000.00'));
        $pdf->Row(Array('plumon:', '$ 20.00'));
        $pdf->Row(Array('salsas valentina:', '$ 15,000.00'));
        $pdf->Row(Array('Total:', '$ 18,270.00'));
        $pdf->isFinished = true;
        break;
}




 $pdf->isFinished = true;
$pdf->Output('./ejemplo.pdf', 'F');