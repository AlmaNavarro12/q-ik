<?php

require_once '../com.sine.modelo/TMP.php';
require_once '../vendor/numeroaletras/NumeroALetras.php';
require_once '../com.sine.controlador/ControladorFactura.php';
require '../pdf/fpdf/fpdf.php';


setlocale(LC_MONETARY, 'es_MX.UTF-8');

class PDF extends FPDF {

    var $titulopagina;
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
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x, $y, $w, $h);
            if ($i == 1 || $i == 3) {
                $this->SetFillColor(255, 255, 255);
            } else {
                $this->SetFillColor($this->rgbfd0, $this->rgbfd1, $this->rgbfd2);
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
        if ($this->GetY() + $h > $this->PageBreakTrigger){
            $this->AddPage($this->CurOrientation);
            $this->Ln($h);
        }
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
        $this->Write(8, $this->titulopagina);

        $this->SetX(160);
        $this->RoundedRect(160, $this->GetY(), 45, 8, 4, 'F');
        $this->SetX(173.5);
        $this->Write(8, 'Folio');

        $this->SetY(18);
        $this->SetX(160);
        $this->SetTextColor(255, 0, 0);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(45, 8, iconv("utf-8", "windows-1252",$this->Tfolio), 0, 0, 'C', false);

        $this->Ln(15);
    }

    function Footer() {
        $pagin = "";
        if ($this->chnum == '1') {
            $pagin = iconv("utf-8", "windows-1252",'Pagina ' . $this->PageNo() . ' de {nb}');
        }
        $this->SetY(-18);
        if ($this->isFinished) {
            if ($this->chfirmar == '1') {
                $this->Image($this->firma, 75, ($this->GetY() - 25), 60, 0, 'png');
                $this->SetFont('Arial', 'I', 9);
                $this->Cell(195, 4, $this->nmfirma, 0, 0, 'C');
                $this->Ln(4);
            }
        }
        $rgb = explode("-", $this->colorpie);
        $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(65, 4, $this->pagina, 0, 0, 'L');
        $phone = "Tel: " . $this->telefono1;
        if ($this->telefono2 != "") {
            $this->Cell(65, 4, '', 0, 0, 'C');
            $this->Cell(65, 4, "Tel: " . $this->telefono1, 0, 0, 'R');
            $phone = "Tel: " . $this->telefono2;
        }
        $this->Ln(4);
        $this->Cell(65, 4, $this->correo, 0, 0, 'L');
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

}

$cf = new ControladorFactura();
if (isset($_GET['factura'])) {
    $id_factura = intval($_GET['factura']);
} else if (isset($_POST['id'])) {
    $id_factura = intval($_POST['id']);
} else if (isset($_POST['mul'])) {
    $id_factura = intval($_POST['mul']);
}

$facturas = $cf->getFacturas($id_factura);
$dircliente = "";
foreach ($facturas as $facturaactual) {
    $idcliente = $facturaactual['idcliente'];
    $nombreCliente = $facturaactual['rzreceptor'];
    $rfccliente = $facturaactual['rfcreceptor'];
    $cpcliente = $facturaactual['cpreceptor'];
    $regcliente = $facturaactual['regfiscalreceptor'];
    //cfdi
    $cuso = $facturaactual['id_uso_cfdi'];
    //cfdi
    $descripcionuso = $facturaactual['uso_cfdi'];
    $rfc = $facturaactual['factura_rfcemisor'];
    $razonsocial = $facturaactual['factura_rzsocial'];
    $regimen = $facturaactual['factura_clvregimen'] . ' ' . $facturaactual['factura_regimen'];
    $dircliente = $facturaactual['dircliente']." ";
    $cp = $facturaactual['factura_cpemisor'];
    $folio = $facturaactual['letra'] . $facturaactual['folio_interno_fac'];
    //comrpobante
    $tipocomprobante = $facturaactual['id_tipo_comprobante'] . ' ' . $facturaactual['tipo_comprobante'];
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
    //ajuste1 se susituyo c_pago por:
    //$c_pago = $facturaactual['id_forma_pago'];
    //ajuste2 se  sustituyo descripcion_pago por:
    $des_pago = $facturaactual['forma_pago'];
    $idmoneda = $facturaactual['id_moneda'];
    $c_moneda = $facturaactual['moneda'];
    $subtotal = $facturaactual['subtotal'];
    $totdescuentos = $facturaactual['totaldescuentos'];
    $totalfactura = $facturaactual['totalfactura'];
    $estado = $facturaactual['status_pago'];
    $chfirmar = $facturaactual['chfirmar'];
    $iddatosfacturacion = $facturaactual['iddatosfacturacion'];
    $chperiodo = $facturaactual['chperiodo'];
    $periodocobro = $facturaactual['periodocobro'];
    $tipofactura = $facturaactual['tipofactura'];
    $cfdistring = $facturaactual['cfdistring'];
    $cfdisrel = $facturaactual['cfdisrel'];
    $periodoglobal = $facturaactual['periodoglobal'];
    $mesperiodo = $facturaactual['mesperiodo'];
    $anhoperiodo = $facturaactual['anhoperiodo'];
    $tag = $facturaactual['tagfactura'];
}


$divideF = explode("-", $fecha_creacion);
$mes = $cf->translateMonth($divideF[1]);
$fecha_creacion2 = $divideF[2] . '/' . $mes . '/' . $divideF[0];

$sine = $cf->getDatosFacturacionbyId($iddatosfacturacion);
foreach ($sine as $sineactual) {
	if ($uuid == "") {
        $rfc = $sineactual['rfc'];
        $razonsocial = $sineactual['razon_social'];
        $clvreg = $sineactual['c_regimenfiscal'];
        $regimen = $sineactual['regimen_fiscal'];
        $cp = $sineactual['codigo_postal'];
    }
    $nombre = $sineactual['nombre_contribuyente'];
    $firma = $sineactual['firma'];
}

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('1');
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
$pdf->titulopagina = $titulo;
$pdf->imglogo = $imglogo;
$pdf->colortitulo = $colortitulo;
$pdf->celdatitulo = $celdatitulo;
$pdf->pagina = $pagina;
$pdf->correo = $correo;
$pdf->telefono1 = $telefono1;
$pdf->telefono2 = $telefono2;
$pdf->chnum = $chnum;
$pdf->colorpie = $colorpie;
$pdf->Tfolio = $folio;
$pdf->chfirmar = $chfirmar;
$pdf->nmfirma = $nombre;
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
$pdf->RowNBCount(Array('Nombre', iconv("utf-8", "windows-1252",$razonsocial), 'No Certificado', iconv("utf-8", "windows-1252",$certcfdi)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('RFC', iconv("utf-8", "windows-1252",$rfc), 'Regimen Fiscal', iconv("utf-8", "windows-1252",$regimen)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('Fecha', iconv("utf-8", "windows-1252",$fecha_creacion2), 'T. Comprobante', iconv("utf-8", "windows-1252",$tipocomprobante)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(Array(32, 77.5));
$pdf->SetX(10);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$cp)));

$heightdatos = $pdf->heightB;

$pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
$pdf->RoundedRect(10, 45, 195, $heightdatos, 2, 'F');
$pdf->SetWidths(Array(15, 92.5, 27, 60));
$pdf->SetLineHeight(4.5);

$pdf->SetY(45);
$pdf->SetRowBorder('NB');
$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->Row(Array('Nombre', iconv("utf-8", "windows-1252",$razonsocial), 'No Certificado', iconv("utf-8", "windows-1252",$certcfdi)));

$pdf->SetRowBorder('NB');
$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->Row(Array('RFC', iconv("utf-8", "windows-1252",$rfc), 'Regimen Fiscal', iconv("utf-8", "windows-1252",$regimen)));

$pdf->SetRowBorder('NB');
$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->Row(Array('Fecha', iconv("utf-8", "windows-1252",$fecha_creacion2), 'T. Comprobante', iconv("utf-8", "windows-1252",$tipocomprobante)));

$pdf->SetWidths(Array(34, 77.5));
$pdf->SetRowBorder('NB');
$pdf->SetStyles(array('B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt));
$pdf->Row(Array('Lugar de Expedicion', iconv("utf-8", "windows-1252",$cp)));
$pdf->Ln(0.5);

$pdf->heightB = 0;

$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
$pdf->SetFont('Arial', '', 15);
$pdf->SetWidths(Array(80));
$pdf->SetLineHeight(8);
$pdf->RowT(Array("Datos del Cliente"));
$direccion = $dircliente . "CP. $cpcliente";

$pdf->ycliente = $pdf->GetY();

$pdf->SetWidths(Array(17, 94.5, 23, 60));
$pdf->SetLineHeight(4.5);

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('Nombre', iconv("utf-8", "windows-1252",$nombreCliente), 'RFC', iconv("utf-8", "windows-1252",$rfccliente)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('Regimen', iconv("utf-8", "windows-1252",$regcliente), 'Uso de CFDI', iconv("utf-8", "windows-1252",$cuso . ' ' . $descripcionuso)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(Array(17, 174.5));
$pdf->SetX(10);
$pdf->RowNBCount(Array('Direccion', iconv("utf-8", "windows-1252",$direccion)));

$heightcliente = $pdf->heightB;

$pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
$pdf->RoundedRect(10, ($pdf->ycliente + 0.7), 195, $heightcliente, 2, 'F');

$pdf->SetY(($pdf->ycliente + 0.7));
$pdf->SetWidths(Array(17, 94.5, 23, 60));
$pdf->SetLineHeight(4.5);

$pdf->SetRowBorder('NB');
$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->Row(Array('Nombre', iconv("utf-8", "windows-1252",$nombreCliente), 'RFC', iconv("utf-8", "windows-1252",$rfccliente)));

$pdf->Row(Array('Regimen', iconv("utf-8", "windows-1252",$regcliente), 'Uso de CFDI', iconv("utf-8", "windows-1252",$cuso . ' ' . $descripcionuso)));

$pdf->SetStyles(array('B', ''));
$pdf->SetWidths(Array(17, 174.5));
$pdf->setRowColorText(array($txtbold, $clrtxt));
$pdf->Row(Array('Direccion', iconv("utf-8", "windows-1252",$direccion)));
$pdf->Ln(1);

if ($rfccliente == 'XAXX010101000' && $periodoglobal != '') {
    $pdf->heightB = 0;
    $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
    $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
    $pdf->SetFont('Arial', '', 15);
    $pdf->SetWidths(Array(80));
    $pdf->SetLineHeight(8);
    $pdf->RowT(Array("Informacion Global"));

    $pdf->ycliente = $pdf->GetY();
    
    $infoglobal = $cf->getInfoGlobal($periodoglobal, $mesperiodo);
    $divP = explode("</tr>", $infoglobal);

    $pdf->SetWidths(Array(22, 45, 20, 44, 20, 44));
    $pdf->SetLineHeight(4.5);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->RowNBCount(Array('Periodicidad', iconv("utf-8", "windows-1252",$periodoglobal . '-' . $divP[0]), 'Mes', iconv("utf-8", "windows-1252",$mesperiodo . '-' . $divP[1]), 'Año', iconv("utf-8", "windows-1252",$anhoperiodo)));

    $heightglobal = $pdf->heightB;

    $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
    $pdf->RoundedRect(10, ($pdf->ycliente + 0.7), 195, $heightglobal, 2, 'F');

    $pdf->SetY(($pdf->ycliente + 0.7));

    $pdf->SetRowBorder('NB');
    $pdf->SetLineHeight(4.5);
    $pdf->SetStyles(array('B', '', 'B', '', 'B', ''));
    $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold, $clrtxt, $txtbold, $clrtxt));
    $pdf->Row(Array('Periodicidad', iconv("utf-8", "windows-1252",$periodoglobal . '-' . $divP[0]), 'Mes', iconv("utf-8", "windows-1252",$mesperiodo . '-' . $divP[1]), iconv("utf-8", "windows-1252",'Año'), iconv("utf-8", "windows-1252",$anhoperiodo)));
    $pdf->Ln(1);
}

$pdf->Ln(0.5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(23, 23, 124);
$pdf->RoundedRect(10, $pdf->GetY(), 195, 5.5, 2, 'D');
$pdf->SetX(92.5);
$pdf->Write(6, 'CONCEPTOS');
$pdf->Ln(7);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
$pdf->SetWidths(Array(15, 22, 25, 46, 45, 21, 21));
$pdf->SetRowBorder('B', 'FD');
$pdf->SetSizes(9);
$pdf->SetStyles('B');
$pdf->SetAligns(array('C'));
$pdf->SetLineHeight(6);
$pdf->setRowColorText(array($colortittabla));
$pdf->Row(Array('Cant', 'Clave Fiscal', 'Unidad', 'Descripcion', 'Observaciones', 'Precio', 'Importe'));

$detallefactura = $cf->getDetalle($tag);
$iva = 0;
$ieps = 0;
$retiva = 0;
$retieps = 0;
$isr = 0;

$pdf->SetRowBorder('B');
$pdf->SetLineHeight(4.5);
$pdf->SetStyles();
$pdf->setRowColorText(array('#000000'));
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
    $divclv = explode("-", $claveFiscal);

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

    $pdf->Row(Array($cantidad, $divclv[0], iconv("utf-8", "windows-1252",$unidad), iconv("utf-8", "windows-1252",$descripcion), iconv("utf-8", "windows-1252",$obser), iconv("utf-8", "windows-1252",'$ ' . number_format($precioV, 2, '.', ',')), iconv("utf-8", "windows-1252",'$ ' . number_format($totalu, 2, '.', ','))));
}

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Moneda ', 0, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, iconv("utf-8", "windows-1252",$c_moneda), 0, 0, 'L', 0);
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Forma de Pago ', 0, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, iconv("utf-8", "windows-1252",$des_pago), 0, 0, 'L', 0);
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Metodo de pago ', 0, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, iconv("utf-8", "windows-1252",$c_metodo . ' ' . $des_metodo), 0, 0, 'L', 0);
$pdf->Ln(10);

$pdf->Ln(-26);
$pdf->Cell(153, 8, '', 0, 0, 'C', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(21, 8, 'Subtotal ', 1, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format($subtotal, 2, '.', ',')), 1, 0, 'C', 0);
$pdf->Ln(8);

$lnJ = 15;

if ($iva > 0) {
    $pdf->Cell(153, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(21, 8, 'IVA ', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($iva, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($ieps > 0) {
    $pdf->Cell(153, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(21, 8, 'IEPS', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($ieps, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($isr > 0) {
    $pdf->Cell(153, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(21, 8, 'ISR', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($isr, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($retiva > 0) {
    $pdf->Cell(153, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(21, 8, 'Ret. IVA', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($retiva, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($retieps > 0) {
    $pdf->Cell(153, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(21, 8, 'Ret. IEPS', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($retieps, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($totdescuentos > 0) {
    $pdf->Cell(153, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(21, 8, 'Descuentos', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($totdescuentos, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

$pdf->Cell(153, 8, '', 0, 0, 'C', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(21, 8, 'Total', 1, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(21, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($totalfactura, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
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
$letras = NumeroALetras::convertir(bcdiv($totalfactura, '1', 2), iconv("utf-8", "windows-1252",$letramoneda), iconv("utf-8", "windows-1252",$letracent));
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252","$letras $mn")));
$pdf->Ln(1);

if ($chperiodo == '1') {
    $month = $divideF[1];
    $year = $divideF[0];
    $year2 = $year;
    $month2 = $month + $periodocobro;

    if ($month2 >= '13') {
        $month2 = $month2 - 12;
        $year2 = $year + 1;
    }

    switch ($periodocobro) {
        case '1':
            $periodo = "Mensual";
            break;
        case '2':
            $periodo = "Bimestral";
            break;
        case '3':
            $periodo = "Trimestral";
            break;
        case '4':
            $periodo = "Cuatrimestral";
            break;
        case '6':
            $periodo = "Semestral";
            break;
        case '12':
            $periodo = "Anual";
            break;
        default:
            break;
    }

    $mes = $cf->translateMonth($month);
    $mes2 = $cf->translateMonth($month2);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(30, 8, 'Periodo de Cobro ', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(26, 8, iconv("utf-8", "windows-1252","$periodo del " . $divideF[2] . " de " . $mes . " del $year al " . $divideF[2] . " de " . $mes2 . " del $year2"), 0, 0, 'L', 0);
    $pdf->Ln(8);
}

$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');

if ($cfdisrel == '1') {

    $pdf->Ln(4);
    $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
    $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
    $pdf->SetWidths(Array(35));
    $pdf->SetLineHeight(4.5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetRowBorder('R', 'F');
    $pdf->SetAligns(array('C'));
    $pdf->setRowColorText(Array($colorsubtitulos));
    $pdf->SetStyles('B');
    $pdf->Row(Array("CFDIS Relacionados"));

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
    $pdf->SetTextColor($rgbtt[0], $rgbtt[1], $rgbtt[2]);
    $pdf->SetWidths(Array(52, 70));
    $pdf->SetLineHeight(4.5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetRowBorder('R', 'F');
    $pdf->SetAligns(array('C'));
    $pdf->setRowColorText(Array($colortittabla, $colortittabla));
    $pdf->SetStyles('B');
    $pdf->Row(Array('Tipo de Relacion', 'Folio Fiscal'));

    $pdf->SetFont('Arial', '', 9);
    $pdf->setRowColorText();
    $pdf->SetStyles();
    $pdf->SetRowBorder('R');
    $relacionados = $cf->getcfdisRelacionados($tag);
    foreach ($relacionados as $relactual) {
        $tiporel = $relactual['tiporel'];
        $descripcion = $relactual['descripcion_tiporelacion'];
        $uuidrel = $relactual['uuid'];
        $pdf->Row(Array(iconv("utf-8", "windows-1252",$tiporel . '-' . $descripcion), $uuidrel));
    }
}

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
        $pdf->Write(10, iconv("utf-8", "windows-1252","Folio Fiscal (UUID): "), '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(40);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Write(10, iconv("utf-8", "windows-1252",$uuid), '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(40);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(10, iconv("utf-8", "windows-1252","N° Certificado SAT: "), '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(40);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Write(10, iconv("utf-8", "windows-1252",$certSAT), '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(40);
        $pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
        $pdf->Write(10, iconv("utf-8", "windows-1252","Fecha de Certificacion: "), '');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(40);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->Write(10, iconv("utf-8", "windows-1252",$fechatimbrado), '');
        $pdf->Ln(11.5);

        $pdf->SetFont('Arial', 'B', 9);

        $pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
        $pdf->RoundedRect(10, $pdf->GetY(), 35, 5, 2, 'F');
        $pdf->SetX(12);
        $pdf->Write(5, iconv("utf-8", "windows-1252","Sello CFDI"), '');

        $pdf->RoundedRect(75, $pdf->GetY(), 35, 5, 2, 'F');
        $pdf->SetX(77);
        $pdf->Write(5, iconv("utf-8", "windows-1252","Sello SAT"), '');

        $pdf->RoundedRect(140, $pdf->GetY(), 35, 5, 2, 'F');
        $pdf->SetX(142);
        $pdf->Write(5, iconv("utf-8", "windows-1252","Cadena Original"), '');
        $pdf->Ln(5);
        $pdf->SetWidths(Array(62.5, 2.5, 62.5, 2.5, 65));
        $pdf->SetLineHeight(2.5);
        $pdf->SetFont('Arial', '', 5);
        $pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
        $pdf->rgbfd0 = $rgbfd[0];
        $pdf->rgbfd1 = $rgbfd[1];
        $pdf->rgbfd2 = $rgbfd[2];
        $pdf->RowR(Array(iconv("utf-8", "windows-1252",$sellocfdi), "", iconv("utf-8", "windows-1252",$selloSat), "", iconv("utf-8", "windows-1252",$cadenaoriginal)));
        $pdf->SetFont('Arial', '', 9);
        $pdf->Write(8, iconv("utf-8", "windows-1252","Este documento es una representacion impresa de un cfdi-."), '');
    }
} else {
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'BI', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(10, 3, '*La Factura ' . $folio . ' ha sido oficialmente cancelada', 0, 0, 'L', 0);
}

$pdf->isFinished = true;
if (isset($_GET['factura'])) {
    $pdf->Output($folio . '_' . $rfc . '_' . $uuid . '.pdf', 'I');
} else if (isset($_POST['id'])) {
    require_once '../com.sine.modelo/SendMail.php';
    $sm = new SendMail();
    $sm->setIdcliente($idcliente);
    $sm->setRfcemisor($rfc);
    $sm->setRazonsocial($nombreCliente);
    $sm->setFolio($folio);
    $sm->setUuid($uuid);
    $sm->setChmail1($_POST['ch1']);
    $sm->setChmail2($_POST['ch2']);
    $sm->setChmail3($_POST['ch3']);
    $sm->setChmail4($_POST['ch4']);
    $sm->setChmail5($_POST['ch5']);
    $sm->setChmail6($_POST['ch6']);
    $sm->setMailalt1($_POST['mailalt1']);
    $sm->setMailalt2($_POST['mailalt2']);
    $sm->setMailalt3($_POST['mailalt3']);
    $sm->setMailalt4($_POST['mailalt4']);
    $sm->setMailalt5($_POST['mailalt5']);
    $sm->setMailalt6($_POST['mailalt6']);
    $sm->setPdfstring($pdf->Output('S'));
    $sm->setCfdistring($cfdistring);
    $send = $cf->mail($sm);
    echo $send;
} else if (isset($_POST['mul'])) {
    $file = "factura$folio.pdf";
    $cod = $_POST['cod'];
    $number = $_POST['wpnumber'];

    $pdf->Output("../file/$file", 'F');
    $wp = $cf->sendMSG($file, $cod, $number);
}