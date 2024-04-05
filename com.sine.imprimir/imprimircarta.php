<?php

require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.controlador/ControladorCarta.php';
require_once '../vendor/numeroaletras/NumeroALetras.php';
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
        $this->Ln(26);
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

$cf = new ControladorCarta();
if (isset($_GET['carta'])) {
    $id_factura = intval($_GET['carta']);
} else if (isset($_POST['id'])) {
    $id_factura = intval($_POST['id']);
} else if (isset($_POST['idwp'])) {
    $id_factura = intval($_POST['idwp']);
}

$facturas = $cf->getFacturas($id_factura);
foreach ($facturas as $facturaactual) {
    $rfc = $facturaactual['factura_rfcemisor'];
    $razonsocial = $facturaactual['factura_rzsocial'];
    $regimen = $facturaactual['factura_clvregimen'] . ' ' . $facturaactual['factura_regimen'];
    $cp = $facturaactual['factura_cpemisor'];
    $idcliente = $facturaactual['idcliente'];
    $nombreCliente = $facturaactual['rzreceptor'];
    $rfccliente = $facturaactual['rfcreceptor'];
    $dircliente = $facturaactual['dircliente'] . " ";
    $cpcliente = $facturaactual['cpreceptor'];
    $regfiscalreceptor = $facturaactual['regfiscalreceptor'];

    $cfdi = $facturaactual['nombre_uso_cfdi'];
    list($cuso, $descripcionuso) = explode('-', $cfdi, 2);

    $folio = $facturaactual['letra'] . $facturaactual['foliocarta'];

    $comprobante = $facturaactual['nombre_comprobante'];
    list($ctipocomp, $tipocomprobante) = explode('-', $comprobante, 2);

    $fecha_creacion = $facturaactual['fecha_creacion'];
    $cadenaoriginal = $facturaactual['cadenaoriginal'];
    $certSAT = $facturaactual['nocertificadosat'];
    $certcfdi = $facturaactual['nocertificadocfdi'];
    $uuid = $facturaactual['uuid'];
    $selloSat = $facturaactual['sellosat'];
    $sellocfdi = $facturaactual['sellocfdi'];
    $fechatimbrado = $facturaactual['fechatimbrado'];
    $qrcode = $facturaactual['qrcode'];

    $metodo = $facturaactual['nombre_metodo_pago'];
    list($c_metodo, $des_metodo) = explode('-', $metodo, 2);

    $forma = $facturaactual['nombre_forma_pago'];
    list($c_pago, $des_pago) = explode('-', $forma, 2);

    $idmoneda = $facturaactual['id_moneda'];

    $moneda = $facturaactual['nombre_moneda'];
    list($c_moneda, $des_moneda) = explode('-', $moneda, 2);

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
	if ($uuid == "") {
        $rfc = $sineactual['rfc'];
        $razonsocial = $sineactual['razon_social'];
        $clvreg = $sineactual['c_regimenfiscal'];
        $regimen = $sineactual['regimen_fiscal'];
        $cp = $sineactual['codigo_postal'];
    }
    $numcertificado = $sineactual['numcsd'];
    $firma = $sineactual['firma'];
}

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('11');
foreach ($encabezado as $actual) {
    $titulo = $actual['tituloencabezado'];
    $colortitulo = $cc->hex2rgb($actual['colortitulo']);
    $celdatitulo = $cc->hex2rgb($actual['colorceltitulo']);
    $imglogo = $actual['imglogo'];
    $titulocarta = $actual['titulocarta'];
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
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$razonsocial), '', iconv("utf-8", "windows-1252",$numcertificado)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$rfc), '', iconv("utf-8", "windows-1252",$regimen)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$fecha_creacion2), '', iconv("utf-8", "windows-1252",$ctipocomp . ' ' . $tipocomprobante)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(Array(32, 77.5, 25, 60));
$pdf->SetX(10);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$cp), '', ''));

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
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$razonsocial), '', iconv("utf-8", "windows-1252",$numcertificado)));

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
$pdf->Write(5, 'RFC');
$pdf->SetX(119.5);
$pdf->Write(5, 'Regimen Fiscal');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$rfc), '', iconv("utf-8", "windows-1252",$regimen)));

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
$pdf->Write(5, 'Fecha');
$pdf->SetX(119.5);
$pdf->Write(5, 'T. Comprobante');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$fecha_creacion2), '', iconv("utf-8", "windows-1252",$ctipocomp . ' ' . $tipocomprobante)));

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetWidths(Array(32, 77.5, 25, 60));
$pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
$pdf->Write(5, 'Lugar de Expedicion');
$pdf->SetX(119.5);
$pdf->Write(5, '');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$cp), '', ''));
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
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$nombreCliente), '', iconv("utf-8", "windows-1252",$rfccliente)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(10);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$regfiscalreceptor), '', iconv("utf-8", "windows-1252",$cuso . ' ' . $descripcionuso)));

$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(Array(17, 174.5));
$pdf->SetX(10);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$direccion)));

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
$pdf->Write(5, 'RFC');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$nombreCliente), '', iconv("utf-8", "windows-1252",$rfccliente)));

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
$pdf->Write(5, 'Regimen');
$pdf->SetX(119.5);
$pdf->Write(5, 'Uso de CFDI');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$regfiscalreceptor), '', iconv("utf-8", "windows-1252",$cuso . ' ' . $descripcionuso)));

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
$pdf->Write(5, 'Direccion');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetWidths(Array(17, 174.5));
$pdf->SetLineHeight(4.5);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$direccion)));
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
$pdf->SetTextColor(0, 0, 0);

$detallefactura = $cf->getDetalle($tag);
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
    $unidad = iconv("utf-8", "windows-1252",$detalleactual['clvunidad']);
    $descripcion = $detalleactual['carta_producto'];
    $totalu = $detalleactual['totalunitario'];
    $traslados = $detalleactual['traslados'];
    $retenciones = $detalleactual['retenciones'];
    $divclv = explode("-", $claveFiscal);
    $claveFiscal = $divclv[0];
    $observacionesprod = $detalleactual['observacionesproducto'];
    $obser = str_replace("<ent>", "\n", $observacionesprod);

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

    $pdf->Row(Array($cantidad, $claveFiscal, $unidad, iconv("utf-8", "windows-1252",$descripcion), iconv("utf-8", "windows-1252",$obser), iconv("utf-8", "windows-1252",'$ ' . number_format($precioV, 2, '.', ',')), iconv("utf-8", "windows-1252",'$ ' . number_format($totalu, 2, '.', ','))));
}

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Moneda ', 0, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, iconv("utf-8", "windows-1252",$c_moneda), 0, 0, 'L', 0);
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Forma de Pago ', 0, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, iconv("utf-8", "windows-1252",$c_pago . ' ' . $des_pago), 0, 0, 'L', 0);
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Metodo de pago ', 0, 0, 'C', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, iconv("utf-8", "windows-1252",$c_metodo . ' ' . $des_metodo), 0, 0, 'L', 0);
$pdf->Ln(10);

if ($ctipocomp == 'T') {
    $subtotal = '0';
    $iva = '0';
    $ieps = '0';
    $isr = '0';
    $retiva = '0';
    $retieps = '0';
    $totdescuentos = '0';
    $totalfactura = '0';
}

$pdf->Ln(-26);
$pdf->Cell(151, 8, '', 0, 0, 'C', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(22, 8, 'Subtotal ', 1, 0, 'C', 0);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format($subtotal, 2, '.', ',')), 1, 0, 'C', 0);
$pdf->Ln(8);

$lnJ = 15;

if ($iva > 0) {
    $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(22, 8, 'IVA ', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($iva, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($ieps > 0) {
    $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(22, 8, 'IEPS', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($ieps, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($isr > 0) {
    $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(22, 8, 'ISR', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($isr, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($retiva > 0) {
    $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(22, 8, 'RET IVA', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($retiva, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($retieps > 0) {
    $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(22, 8, 'RET IEPS', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($retieps, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

if ($totdescuentos > 0) {
    $pdf->Cell(151, 8, '', 0, 0, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(22, 8, 'Descuentos', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($totdescuentos, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->Ln(8);
    $lnJ = 10;
}

$pdf->Cell(151, 8, '', 0, 0, 'C', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(22, 8, 'Total', 1, 0, 'C', 0);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(22, 8, iconv("utf-8", "windows-1252",'$ ' . number_format(bcdiv($totalfactura, '1', 2), 2, '.', ',')), 1, 0, 'C', 0);
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
$pdf->SetAligns(Array('L'));
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252","$letras $mn")));
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

$pdf->titulopagina = $titulocarta;
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
    $seguroambiente = $cactual['carta_seguroambiente'];
    $polizaambiente = $cactual['carta_polizaambiente'];
    $tiporemolque1 = $cactual['carta_tiporemolque1'];
    $placaremolque1 = $cactual['carta_placaremolque1'];
    $tiporemolque2 = $cactual['carta_tiporemolque2'];
    $placaremolque2 = $cactual['carta_placaremolque2'];
    $tiporemolque3 = $cactual['carta_tiporemolque3'];
    $placaremolque3 = $cactual['carta_placaremolque3'];
    $obsercarta = $cactual['carta_observaciones'];
    $obsercarta = str_replace("<ent>", "\n", $obsercarta);

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
$pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
$pdf->RoundedRect(10, 36, 195, 10, 2, 'F');

$pdf->SetY(36.3);
$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(Array(40, 66, 46, 43));
$pdf->SetLineHeight(4.5);
$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->SetRowBorder();
$pdf->SetAligns(array('L'));
$pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->Row(Array('Version', iconv("utf-8", "windows-1252",'2.0'), 'Distancia Total Recorrida', iconv("utf-8", "windows-1252",$distanciatotal)));

$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->SetRowBorder();
$pdf->SetAligns(array('L'));
$pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->Row(Array('Transporte Internacional', iconv("utf-8", "windows-1252",'No'), 'Entrada/Salida de mercancia', iconv("utf-8", "windows-1252",$movimiento)));
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
$detallemercancia = $cf->getMercancias($tag);
$pdf->SetWidths(Array(20, 45, 15, 25, 25, 30, 35));
$pdf->SetLineHeight(4);
$pdf->SetAligns(array('C'));
$pdf->SetFillColor($rgbt[0], $rgbt[1], $rgbt[2]);
$pdf->setRowColorText(array($colortittabla));
$pdf->SetRowBorder('B', 'FD');
$pdf->SetFont('Arial', '', 9);
$pdf->SetStyles('B');
$pdf->Row(Array("Clave Fiscal", "Descripcion", "Cant", "Unidad", "Peso en Kg", "Material Peligroso", "Embalaje"));

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
    $condicion = $mercactual['condicion'];
    $cpeligro = $mercactual['peligro'];
    $clvmaterial = $mercactual['clvmaterial'];
    $embalaje = $mercactual['embalaje'];

    $div = explode('-', $clavemercancia);
    $clv = $div[0];

    $div2 = explode('-', $unidad);
    $unit = $div2[1];

    if ($cpeligro == '0') {
        $peligro = "No " . $clvmaterial;
    } else if ($cpeligro == '1') {
        $peligro = $clvmaterial;
    } else {
        $peligro = "No";
    }

    $pdf->Row(Array(iconv("utf-8", "windows-1252",$clv), iconv("utf-8", "windows-1252",$descripcion), $cantidad, iconv("utf-8", "windows-1252",$unit), $peso, iconv("utf-8", "windows-1252",$peligro), iconv("utf-8", "windows-1252",$embalaje)));
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
    $estado = $oractual['nombre_estado'];
    $codporigen = $oractual['ubicacion_codpostal'];
    $distancia = $oractual['ubicacion_distancia'];
    $fechasalida = $oractual['fechallegada'];
    $horasalida = $oractual['horallegada'];
    $ordireccion = $oractual['direccion'];
    $municipio = $oractual['nombre_municipio'];

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
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$nombreor), "", iconv("utf-8", "windows-1252",$rfcorigen)));
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$tipo), "", iconv("utf-8", "windows-1252",$distancia)));
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$fecha . ' ' . $horasalida), "", ""));

    $pdf->SetWidths(Array(20, 167));
    $pdf->SetLineHeight(5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt));
    $pdf->SetStyles(array('B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$dirubicacion)));

    $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
    $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

    $pdf->SetY($pdf->GetY() - ($pdf->heightB));
    $pdf->SetWidths(Array(28, 66, 33, 68));
    $pdf->SetLineHeight(4.5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
    $pdf->SetStyles(array('B', '', 'B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->Row(Array("Remitente", iconv("utf-8", "windows-1252",$nombreor), "RFC Remitente", iconv("utf-8", "windows-1252",$rfcorigen)));
    $pdf->Row(Array("Tipo", iconv("utf-8", "windows-1252",$tipo), "Distancia Recorrida", iconv("utf-8", "windows-1252",$distancia)));
    $pdf->Row(Array("Fecha Salida", iconv("utf-8", "windows-1252",$fecha . ' ' . $horasalida), "", ""));

    $pdf->SetWidths(Array(20, 167));
    $pdf->SetLineHeight(5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt));
    $pdf->SetStyles(array('B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->Row(Array("Direccion", iconv("utf-8", "windows-1252",$dirubicacion)));

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
    $estado = $desactual['nombre_estado'];
    $coddestino = $desactual['ubicacion_codpostal'];
    $distancia = $desactual['ubicacion_distancia'];
    $fechallegada = $desactual['fechallegada'];
    $horallegada = $desactual['horallegada'];
    $desdireccion = $desactual['direccion'];
    $municipio = $desactual['nombre_municipio'];

    $dirubicacion = "$desdireccion CP. $coddestino, $municipio $estado";

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
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$nombredes), "", iconv("utf-8", "windows-1252",$rfcdestino)));
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$tipo), "", iconv("utf-8", "windows-1252",$distancia)));
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$fecha . ' ' . $horallegada), "", ""));

    $pdf->SetWidths(Array(20, 167));
    $pdf->SetLineHeight(5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt));
    $pdf->SetStyles(array('B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->RowNBCount(Array("", iconv("utf-8", "windows-1252",$dirubicacion)));

    $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
    $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

    $pdf->SetY($pdf->GetY() - ($pdf->heightB));

    $pdf->SetWidths(Array(28, 66, 33, 68));
    $pdf->SetLineHeight(4.5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
    $pdf->SetStyles(array('B', '', 'B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->Row(Array("Destinatario", iconv("utf-8", "windows-1252",$nombredes), "RFC Destinatario", iconv("utf-8", "windows-1252",$rfcdestino)));
    $pdf->Row(Array("Tipo", iconv("utf-8", "windows-1252",$tipo), "Distancia Recorrida", iconv("utf-8", "windows-1252",$distancia)));
    $pdf->Row(Array("Fecha Llegada", iconv("utf-8", "windows-1252",$fecha . ' ' . $horallegada), "", ""));

    $pdf->SetWidths(Array(20, 167));
    $pdf->SetLineHeight(5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt));
    $pdf->SetStyles(array('B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->Row(Array("Direccion", iconv("utf-8", "windows-1252",$dirubicacion)));
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
$pdf->SetLineHeight(5);
$pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
$pdf->SetStyles(array('B', '', 'B', ''));
$pdf->SetAligns(array('L'));
$pdf->SetRowBorder();
$pdf->heightB = 0;
$pdf->RowNBCount(Array("Num Permiso", iconv("utf-8", "windows-1252",$numpermiso), "Tipo permiso", iconv("utf-8", "windows-1252",$permiso)));
$pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Año Vehiculo"), iconv("utf-8", "windows-1252",$anhomod), "Placa Vehiculo", iconv("utf-8", "windows-1252",$placavehiculo)));
$pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Tipo Transporte"), iconv("utf-8", "windows-1252",$transporte), "", ''));
$pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Aseguradora"), iconv("utf-8", "windows-1252",$segurocivil), "Num Poliza", iconv("utf-8", "windows-1252",$poliza)));
if ($seguroambiente != "") {
    $pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Aseguradora Medio Ambiente"), iconv("utf-8", "windows-1252",$seguroambiente), "Num Poliza", iconv("utf-8", "windows-1252",$polizaambiente)));
}

if ($tiporemolque1 != "") {
    $pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Tipo Remolque #1"), iconv("utf-8", "windows-1252",$tiporemolque1), "Placa Remolque", iconv("utf-8", "windows-1252",$placaremolque1)));
}
if ($tiporemolque2 != "") {
    $pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Tipo Remolque #2"), iconv("utf-8", "windows-1252",$tiporemolque2), "Placa Remolque", iconv("utf-8", "windows-1252",$placaremolque2)));
}
if ($tiporemolque3 != "") {
    $pdf->RowNBCount(Array(iconv("utf-8", "windows-1252","Tipo Remolque #3"), iconv("utf-8", "windows-1252",$tiporemolque3), "Placa Remolque", iconv("utf-8", "windows-1252",$placaremolque3)));
}

$pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
$pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

$pdf->SetY($pdf->GetY() - ($pdf->heightB));
$pdf->Row(Array("Num Permiso", iconv("utf-8", "windows-1252",$numpermiso), "Tipo permiso", iconv("utf-8", "windows-1252",$permiso)));
$pdf->Row(Array(iconv("utf-8", "windows-1252","Año Vehiculo"), iconv("utf-8", "windows-1252",$anhomod), "Placa Vehiculo", iconv("utf-8", "windows-1252",$placavehiculo)));
$pdf->Row(Array(iconv("utf-8", "windows-1252","Tipo Transporte"), iconv("utf-8", "windows-1252",$transporte), "", ''));
$pdf->Row(Array(iconv("utf-8", "windows-1252","Aseguradora"), iconv("utf-8", "windows-1252",$segurocivil), "Num Poliza", iconv("utf-8", "windows-1252",$poliza)));
if ($seguroambiente != "") {
    $pdf->Row(Array(iconv("utf-8", "windows-1252","Aseguradora Medio Ambiente"), iconv("utf-8", "windows-1252",$seguroambiente), "Num Poliza", iconv("utf-8", "windows-1252",$polizaambiente)));
}

if ($tiporemolque1 != "") {
    $pdf->Row(Array(iconv("utf-8", "windows-1252","Tipo Remolque #1"), iconv("utf-8", "windows-1252",$tiporemolque1), "Placa Remolque", iconv("utf-8", "windows-1252",$placaremolque1)));
}
if ($tiporemolque2 != "") {
    $pdf->Row(Array(iconv("utf-8", "windows-1252","Tipo Remolque #2"), iconv("utf-8", "windows-1252",$tiporemolque2), "Placa Remolque", iconv("utf-8", "windows-1252",$placaremolque2)));
}
if ($tiporemolque3 != "") {
    $pdf->Row(Array(iconv("utf-8", "windows-1252","Tipo Remolque #3"), iconv("utf-8", "windows-1252",$tiporemolque3), "Placa Remolque", iconv("utf-8", "windows-1252",$placaremolque3)));
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
    $estado = $opactual['nombre_estado'];
    $calle = $opactual['operador_calle'];
    $cpoperador = $opactual['operador_cp'];
    $municipio = $opactual['nombre_municipio'];

    $diroperador = $calle . " " . $cpoperador . " " . $municipio . " " . $estado;

    $pdf->SetWidths(Array(25, 70, 25, 70));
    $pdf->SetLineHeight(4.5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
    $pdf->SetStyles(array('B', '', 'B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->heightB = 0;

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(10);
    $pdf->RowNBCount(Array("Nombre", iconv("utf-8", "windows-1252",$nombreoperador), "RFC", iconv("utf-8", "windows-1252",$rfcoperador)));
    $pdf->RowNBCount(Array("Num Licencia", iconv("utf-8", "windows-1252",$numlicencia), "", ""));

    $pdf->SetWidths(Array(25, 170));
    $pdf->SetLineHeight(4.5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt));
    $pdf->SetStyles(array('B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->RowNBCount(Array("Direccion", iconv("utf-8", "windows-1252",$diroperador)));

    $pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
    $pdf->RoundedRect(10, $pdf->GetY() - ($pdf->heightB), 195, $pdf->heightB, 2, 'F');

    $pdf->SetY($pdf->GetY() - ($pdf->heightB));

    $pdf->SetWidths(Array(25, 70, 25, 70));
    $pdf->SetLineHeight(4.5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt, $txtbold, $clrtxt));
    $pdf->SetStyles(array('B', '', 'B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->Row(Array("Nombre", iconv("utf-8", "windows-1252",$nombreoperador), "RFC", iconv("utf-8", "windows-1252",$rfcoperador)));
    $pdf->Row(Array("Num Licencia", iconv("utf-8", "windows-1252",$numlicencia), "", ""));

    $pdf->SetWidths(Array(25, 170));
    $pdf->SetLineHeight(4.5);
    $pdf->setRowColorText(Array($txtbold, $clrtxt));
    $pdf->SetStyles(array('B', ''));
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder();
    $pdf->Row(Array("Direccion", iconv("utf-8", "windows-1252",$diroperador)));
    $pdf->Ln(1.5);
}

if ($obsercarta != "") {
    $pdf->SetWidths(Array(80));
    $pdf->SetSizes(array(12));
    $pdf->SetStyles('B');
    $pdf->SetAligns(array('L'));
    $pdf->SetRowBorder('NB');
    $pdf->setRowColorText();
    $pdf->Row(Array("Observaciones"));

    $pdf->SetRowBorder('R');
    $pdf->SetSizes(Array(10));
    $pdf->SetWidths(Array(100));
    $pdf->SetAligns(array('L'));
    $pdf->SetLineHeight(4.5);
    $pdf->SetStyles();
    $pdf->SetSizes();
    $pdf->Row(Array(iconv("utf-8", "windows-1252",$obsercarta)));
}

$pdf->isFinished = true;
if (isset($_GET['carta'])) {
    $pdf->Output($folio . '_' . $rfc . '_' . $uuid . '.pdf', 'I');
} else if (isset($_POST['id'])) {
    require_once '../com.sine.modelo/SendMail.php';
    $sm = new SendMail();
    $sm->setIdcliente($idcliente);
    $sm->setRfcemisor($rfc);
    $sm->setRazonsocial($razonsocial);
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
} else if (isset($_POST['idwp'])) {
    $file = "factura$folio.pdf";
    $cod = $_POST['cod'];
    $number = $_POST['wpnumber'];

    $pdf->Output("../file/$file", 'F');
    $wp = $cf->sendMSG($file, $cod, $number);
}

