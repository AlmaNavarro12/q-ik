<?php

require_once '../com.sine.modelo/Cotizacion.php';
require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.controlador/ControladorCotizacion.php';
require '../pdf/fpdf/fpdf.php';
setlocale(LC_MONETARY, 'es_MX.UTF-8');
date_default_timezone_set("America/Mexico_City");

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
    var $Tfolio;
    var $lineHeight;
    var $iddatos;
    var $chfirmar;
    var $firma;
    var $nmfirma;
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
            $this->Rect($x, $y, $w, $h);
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

}

$cf = new ControladorCotizacion;
$f = new Cotizacion();
if (isset($_GET['cot'])) {
    $id_cotizacion = intval($_GET['cot']);
} else if (isset($_POST['idcotizacion'])) {
    $id_cotizacion = intval($_POST['idcotizacion']);
}

$facturas = $cf->getCotizaciones($id_cotizacion);
foreach ($facturas as $facturaactual) {
    $nombrecliente = $facturaactual['nombrecliente'];
    $folio = $facturaactual['letra'] . $facturaactual['foliocotizacion'];
    $fecha_creacion = $facturaactual['fecha_creacion'];
    $divideF = explode("-", $fecha_creacion);
    $fechacreacion = $divideF[2] . '/' . $divideF[1] . '/' . $divideF[0];
    $observaciones = $facturaactual['observaciones'];
    $iddatosfacturacion = $facturaactual['iddatosfacturacion'];
    $subtotal = $facturaactual['subtot'];
    $subtotaliva = $facturaactual['subiva'];
    $subtotalret = $facturaactual['subret'];
    $totdescuentos = $facturaactual['totdesc'];
    $totcotizacion = $facturaactual['totalcotizacion'];
    $chfirmar = $facturaactual['chfirmar'];
    $tag = $facturaactual['tagcotizacion'];
}

$sine = $cf->getDatosFacturacionbyId($iddatosfacturacion);
foreach ($sine as $sineactual) {
    $nombreempresa = $sineactual['nombre_contribuyente'];
    $rfc = $sineactual['rfc'];
    $razonsocial = $sineactual['razon_social'];
    $callesine = $sineactual['calle'];
    $numexteriorsine = $sineactual['numero_exterior'];
    $colonia = $sineactual['colonia'];
    $estado = $sineactual['estado'];
    $municipio = $sineactual['municipio'];
    $cpostal = $sineactual['codigo_postal'];
    $correodatos = $sineactual['correodatos'];
    $telefonodatos = $sineactual['telefono'];
    $idbanco = $sineactual['idbanco'];
    $nombrebanco = $sineactual['nombre_banco'];
    $sucursal = $sineactual['sucursal'];
    $cuenta = $sineactual['cuenta'];
    $clabe = $sineactual['clabe'];
    $oxxo = $sineactual['tarjetaoxxo'];
    $idbanco1 = $sineactual['idbanco1'];
    $nombrebanco1 = $sineactual['nombre_banco1'];

    $sucursal1 = $sineactual['sucursal1'];
    $cuenta1 = $sineactual['cuenta1'];
    $clabe1 = $sineactual['clabe1'];
    $oxxo1 = $sineactual['tarjetaoxxo1'];
    $idbanco2 = $sineactual['idbanco2'];
    $nombrebanco2 = $sineactual['nombre_banco2'];

    $sucursal2 = $sineactual['sucursal2'];
    $cuenta2 = $sineactual['cuenta2'];
    $clabe2 = $sineactual['clabe2'];
    $oxxo2 = $sineactual['tarjetaoxxo2'];
    $idbanco3 = $sineactual['idbanco3'];
    $nombrebanco3 = $sineactual['nombre_banco3'];

    $sucursal3 = $sineactual['sucursal3'];
    $cuenta3 = $sineactual['cuenta3'];
    $clabe3 = $sineactual['clabe3'];
    $oxxo3 = $sineactual['tarjetaoxxo3'];
    $firma = $sineactual['firma'];
}

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('3');
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
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetWidths(Array(40));
$pdf->SetLineHeight(8);
$pdf->SetY(36.3);
$pdf->RowT(Array("FECHA: $fechacreacion"));

$pdf->SetTextColor($rgbs[0], $rgbs[1], $rgbs[2]);
$pdf->SetFont('Arial', '', 15);
$pdf->SetWidths(Array(80));
$pdf->SetLineHeight(8);
$pdf->RowT(Array("Datos de la Cotizacion"));

$pdf->chfirmar = $chfirmar;
$pdf->iddatos = $iddatosfacturacion;
$pdf->nmfirma = $nombreempresa;
$pdf->firma = $firma;

$pdf->SetWidths(Array(15, 72, 32, 75.5));
$pdf->SetLineHeight(4.5);
$pdf->SetFont('Arial', '', 9);
$pdf->RowNBCount(Array('', iconv("utf-8", "windows-1252",$nombrecliente), '', iconv("utf-8", "windows-1252",'Calle: ' . $callesine . ' #' . $numexteriorsine . ' Col. ' . $colonia . ', ' . $municipio . ', ' . $estado)));


$pdf->SetFillColor($rgbfd[0], $rgbfd[1], $rgbfd[2]);
$pdf->RoundedRect(10, 52.3, 195, $pdf->heightB, 2, 'F');

$pdf->SetY(52.3);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($rgbbld[0], $rgbbld[1], $rgbbld[2]);
$pdf->Write(4.5, 'Nombre');
$pdf->SetX(96.5);
$pdf->Write(4.5, 'Lugar de Expedicion');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($rgbtxt[0], $rgbtxt[1], $rgbtxt[2]);
$pdf->SetX(10);
$pdf->RowNB(Array('', iconv("utf-8", "windows-1252",$nombrecliente), '', iconv("utf-8", "windows-1252",'Calle: ' . $callesine . ' #' . $numexteriorsine . ' Col. ' . $colonia . ', ' . $municipio . ', ' . $estado)));
$pdf->heightB = 0;

$pdf->Ln(1);
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
$pdf->RowNBC(Array("Cant", "Codigo", "Imagen", "Unidad", iconv("utf-8", "windows-1252","Descripción"), "Observaciones", "Precio", "Importe"));
$pdf->SetTextColor(0, 0, 0);
$detallefactura = $cf->getDetalle($tag);
$iva = 0;
$ieps = 0;
$retiva = 0;
$retieps = 0;
$isr = 0;

foreach ($detallefactura as $detalleactual) {
    $pdf->SetFont('Arial', '', 9);
    $idprod = $detalleactual['id_prodservicio'];
    $claveFiscal = $detalleactual['clvfiscal'];
    $precioV = $detalleactual['precio'];
    $cantidad = $detalleactual['cantidad'];
    $unidad = $detalleactual['clvunidad'];
    $descripcion = $detalleactual['cotizacion_producto'];
    $totalu = $detalleactual['totunitario'];
    $traslados = $detalleactual['traslados'];
    $retenciones = $detalleactual['retenciones'];
    $obser = $detalleactual['observacionesp'];
    $observacionesp = str_replace("<ent>", "\n", $obser);
    $divclv = explode("-", $claveFiscal);
    $cfiscal = $divclv[0];

    $imagen = $cf->getIMGProducto($idprod);


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


    if ($imagen != "") {
        $file = '../img/productos/' . $imagen;
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
    } else {
        $img = "No disponible";
    }
    $pdf->Row(Array($cantidad, iconv("utf-8", "windows-1252",$cfiscal), $img, $unidad, iconv("utf-8", "windows-1252",$descripcion), iconv("utf-8", "windows-1252",$observacionesp), iconv("utf-8", "windows-1252",'$ ' . number_format($precioV, 2, '.', ',')), iconv("utf-8", "windows-1252",'$ ' . number_format($totalu, 2, '.', ','))));
}

$pdf->SetWidths(Array(20, 20));
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(165);
$pdf->Row(Array('Subtotal', iconv("utf-8", "windows-1252",'$ ' . number_format($subtotal, 2, '.', ''))));

if ($iva > 0) {
    $pdf->SetX(165);
    $pdf->Row(Array('IVA', iconv("utf-8", "windows-1252",'$ ' . number_format($iva, 2, '.', ''))));
}

if ($ieps > 0) {
    $pdf->SetX(165);
    $pdf->Row(Array('IEPS', iconv("utf-8", "windows-1252",'$ ' . number_format($ieps, 2, '.', ''))));
}

if ($retiva > 0) {
    $pdf->SetX(165);
    $pdf->Row(Array('Ret IVA', iconv("utf-8", "windows-1252",'$ ' . number_format($retiva, 2, '.', ''))));
}

if ($retieps > 0) {
    $pdf->SetX(165);
    $pdf->Row(Array('Ret IEPS', iconv("utf-8", "windows-1252",'$ ' . number_format($retieps, 2, '.', ''))));
}

if ($isr > 0) {
    $pdf->SetX(165);
    $pdf->Row(Array('ISR', iconv("utf-8", "windows-1252",'$ ' . number_format($isr, 2, '.', ''))));
}

if ($totdescuentos > 0) {
    $pdf->SetX(165);
    $pdf->Row(Array('Descuentos', iconv("utf-8", "windows-1252",'$ ' . number_format($totdescuentos, 2, '.', ''))));
}

$pdf->SetX(165);
$pdf->Row(Array('Total', iconv("utf-8", "windows-1252",'$ ' . number_format($totcotizacion, 2, '.', ''))));
$pdf->Ln(2);

$pdf->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
$pdf->RoundedRect(10, $pdf->GetY(), 195, 3, 1.5, 'F');

$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$w2 = 150;
$h2 = 8;
if ($observaciones != "") {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $obser = str_replace("<ent>", "\n", $observaciones);
    $pdf->Write(3.5, iconv("utf-8", "windows-1252",'Observaciones: ' . $obser));
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'I', 9);
$pdf->SetWidths(Array(85, 85));
$pdf->SetLineHeight(4.5);
$pdf->SetFillColor(255, 255, 255);
$pdf->RowR(Array('Datos Fiscales:', 'Datos Transferencia Bancaria: '));

$pdf->SetWidths(Array(85, 85));
$pdf->SetLineHeight(4.5);
$pdf->RowR(Array(iconv("utf-8", "windows-1252","RFC: $rfc \nRazon Social: $razonsocial \nCalle: $callesine #$numexteriorsine Col. $colonia \nLocalidad: $municipio \nEstado: $estado \nCodigo Postal: $cpostal \nCorreo: $correodatos \nTel: $telefonodatos"), iconv("utf-8", "windows-1252","Banco: $nombrebanco    Sucursal: $sucursal \nBeneficiario: $razonsocial \nCuenta: $cuenta \nClabe: $clabe \nNo. Tarjeta: $oxxo")));

if ($idbanco1 != '0') {
    $pdf->SetWidths(Array(85));
    $pdf->SetLineHeight(4.5);
    $pdf->ycliente = $pdf->GetY();
    $pdf->RowR(Array(iconv("utf-8", "windows-1252","Datos de Transferencia Bancaria: ")));
    $pdf->RowR(Array(iconv("utf-8", "windows-1252","Banco: $nombrebanco1    Sucursal: $sucursal1 \nBeneficiario: $razonsocial \nCuenta: $cuenta1 \nClabe: $clabe1 \nNo. Tarjeta: $oxxo1")));
}

if ($idbanco2 != '0') {
    if ($pdf->ycliente != "") {
        $pdf->SetY($pdf->ycliente);
        $pdf->SetX(95);
    }
    $pdf->SetWidths(Array(85));
    $pdf->SetLineHeight(4.5);
    $pdf->RowR(Array(iconv("utf-8", "windows-1252","Datos de Transferencia Bancaria: ")));
    if ($pdf->ycliente != "") {
        $pdf->SetX(95);
    }
    $pdf->RowR(Array(iconv("utf-8", "windows-1252","Banco: $nombrebanco2    Sucursal: $sucursal2 \nBeneficiario: $razonsocial \nCuenta: $cuenta2 \nClabe: $clabe2 \nNo. Tarjeta: $oxxo2")));
}

if ($idbanco3 != '0') {
    $pdf->SetWidths(Array(85));
    $pdf->SetLineHeight(4.5);
    $pdf->RowR(Array(iconv("utf-8", "windows-1252","Datos de Transferencia Bancaria: ")));
    $pdf->RowR(Array(iconv("utf-8", "windows-1252","Banco: $nombrebanco3    Sucursal: $sucursal3 \nBeneficiario: $razonsocial \nCuenta: $cuenta3 \nClabe: $clabe3 \nNo. Tarjeta: $oxxo3")));
}


$pdf->isFinished = true;
if (isset($_GET['cot'])) {
    $cliente = str_replace(" ", "_", $nombrecliente);
    $pdf->Output('cotizacion' . $folio . '_' . iconv("utf-8", "windows-1252",$cliente).'_'.$fecha_creacion . '.pdf', 'I');
} else if (isset($_POST['idcotizacion'])) {
    require_once '../com.sine.modelo/SendMail.php';
    $sm = new SendMail();
    $sm->setRazonsocial($nombrecliente);
    $sm->setFolio($folio);
    $sm->setChmail1($_POST['ch1']);
    $sm->setChmail2($_POST['ch2']);
    $sm->setChmail3($_POST['ch3']);
    $sm->setMailalt1($_POST['correo1']);
    $sm->setMailalt2($_POST['correo2']);
    $sm->setMailalt3($_POST['correo3']);
    $sm->setPdfstring($pdf->Output('S'));
    echo $cf->mail($sm);
}

