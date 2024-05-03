<?php

require_once '../com.sine.modelo/TMP.php';
require_once '../com.sine.controlador/ControladorReportes.php';
require '../pdf/fpdf/fpdf.php';
setlocale(LC_MONETARY, 'es_MX.UTF-8');

class PDF extends FPDF
{

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
    var $cse;
    var $cnombreu;
    var $usuario;
    var $uid;
    var $supervisor;
    var $fecha;
    var $hora;
    var $horafin;

    var $total;
    var $ganancias;
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

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function SetStyles($s = '')
    {
        $this->styles = $s;
    }

    function setRowColorText($t = "#000000")
    {
        $this->rowtextcolor = $t;
    }

    function SetSizes($sz = 9)
    {
        $this->sizes = $sz;
    }

    function SetLineHeight($h)
    {
        $this->lineHeight = $h;
    }

    function SetRowBorder($b = 'NB', $f = 'D')
    {
        $this->rowborder = $b;
        $this->borderfill = $f;
    }

    function Row($data)
    {
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

    function RowC($data)
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

    function RowNBCount($data)
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

    function RowNBTitle($data)
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

    public function getPageBreakTrigger()
    {
        return $this->PageBreakTrigger;
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

    function RowRTitle($data)
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
            $this->RoundedRect($x, $y, $w, $h, 4, 'F');
            //Print the text
            $this->MultiCell($w, $h2, $data[$i], 0, $a);
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
        $this->RoundedRect(35, $this->GetY(), 170, 8, 4, 'F');
        $this->SetX(38);
        $this->Write(8, $this->titulopagina);

        $this->SetFont('Helvetica', 'B', 14);
        $this->SetFillColor($rgbc[0], $rgbc[1], $rgbc[2]);
        $this->SetTextColor($rgbt[0], $rgbt[1], $rgbt[2]);
        $this->SetWidths(array(155));
        $this->SetLineHeight(8);
        $this->SetY(36.3);
        $this->RowT(array("Fecha de corte: " . $this->fecha . " entre " . $this->hora . " a ". $this->horafin ."."));
        $this->Ln(26);

        if ($this->uid != '0') {
            $this->SetY(46);
            $this->SetX(10);
            $this->SetTextColor($this->cse[0], $this->cse[1], $this->cse[2]);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, "Datos de usuario: ", 0, 0, 'L', false);
            $this->SetTextColor($this->cnombreu[0], $this->cnombreu[1], $this->cnombreu[2]);
            $this->SetY(46);
            $this->SetX(60);
            $this->Cell(0, 8, iconv("utf-8", "windows-1252", $this->usuario), 0, 0, 'L', false);
            $this->SetY(51);
            $this->SetX(10);
            $this->Ln(15);
        }

        $this->SetY(69);
        $this->SetX(27);
        $this->SetTextColor($this->cse[0], $this->cse[1], $this->cse[2]);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, "Ventas totales:", 0, 0, 'L', false);
        $this->SetTextColor($this->cnombreu[0], $this->cnombreu[1], $this->cnombreu[2]);
        $this->SetY(69);
        $this->SetX(67);
        $this->Cell(45, 8, "$" . number_format($this->total, 2, '.', ','), 0, 0, 'L', false);
        $this->SetX(125);
        $this->SetTextColor($this->cse[0], $this->cse[1], $this->cse[2]);
        $this->Cell(0, 8, "Ganancias totales:", 0, 0, 'L', false);
        $this->SetTextColor($this->cnombreu[0], $this->cnombreu[1], $this->cnombreu[2]);
        $this->SetX(172);
        $this->Cell(45, 8, "$" . number_format($this->ganancias, 2, '.', ','), 0, 0, 'L', false);
        $this->Ln(18);
    }

    function Footer()
    {
        $pagin = "";
        if ($this->chnum == '1') {
            $pagin =  iconv("utf-8", "windows-1252", 'Pagina ' . $this->PageNo() . ' de {nb}');
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
}

$cr = new ControladorReportes();

$uid = $_GET['u'];
$fecha = $_GET['f'];
$horainicio = $_GET['hi'];
$horafin = $_GET['hf'];
$pago = $_GET['p'];

if (!$fecha) {
    $date = getdate();
    $d = $date['mday'];
    $m = $date['mon'];
    $y = $date['year'];

    if ($d < 10) {
        $d = "0$d";
    }
    if ($m < 10) {
        $m = "0$m";
    }
    $fecha = "$y-$m-$d";
}

$divF = explode("-", $fecha);
$mon = $cr->translateMonth($divF[1]);
$dateformat = $divF[2] . "/" . $mon . "/" . $divF[0];
$usuario = $cr->getUserbyID($uid);

$datos = $cr->getCorteCaja($uid, $pago, $fecha, $horainicio, $horafin);
$div = explode("<cut>", $datos);
$totventas = $div[0];
$totganancia = $div[1];

require_once '../com.sine.controlador/ControladorConfiguracion.php';
$cc = new ControladorConfiguracion();
$encabezado = $cc->getDatosEncabezado('13');
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
$pdf->titulopagina = "Datos de caja";
$pdf->imglogo = $imglogo;
$pdf->colortitulo = $colortitulo;
$pdf->celdatitulo = $celdatitulo;
$pdf->pagina = $pagina;
$pdf->correo = $correo;
$pdf->telefono1 = $telefono1;
$pdf->telefono2 = $telefono2;
$pdf->chnum = $chnum;
$pdf->colorpie = $colorpie;
$pdf->Tfolio = 0;
$pdf->cse = $rgbbld;
$pdf->cnombreu = $rgbtxt;
$pdf->fecha = $dateformat;
$pdf->hora = date('h:i A', strtotime($horainicio));
$pdf->horafin = date('h:i A', strtotime($horafin));
$pdf->usuario = $cr->getUserbyID($uid);
$pdf->uid = $cr->getUserbyID($uid);
$pdf->total = $totventas;
$pdf->ganancias = $totganancia;

$pdf->AliasNbPages();

//-----------------------------------PAGINA 1
$pdf->AddPage();
$pdf->SetAligns('C');
$pdf->SetRowBorder('NB');
$pdf->SetLineHeight(6);
$pdf->SetSizes(array(13, 13, 13));
$pdf->SetWidths(array(95, 5, 95));
$pdf->SetStyles(array('B', '', 'B'));
$pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold));
$pdf->Row(array('Entradas de efectivo', '', 'Dinero en caja'));
$y = $pdf->GetY();

$fondo = 0;
$total = 0;
$datf = $cr->getFondoCaja($uid, $fecha, $horainicio, $horafin);
foreach ($datf as $actual) {
    $fondo += $actual['fondo'];
    $total += $actual['fondo'];
}
$pdf->SetAligns(array('L', 'R'));
$pdf->SetSizes(array(9, 9));
$pdf->SetWidths(array(40, 55));
$pdf->SetStyles(array('B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt));
$pdf->SetLineHeight(0.1);
$pdf->SetRowBorder('NB');
$pdf->Row(array('', ''));

$pdf->SetRowBorder('B');
$pdf->SetLineHeight(4.5);
$pdf->Row(array('Dinero inicial en caja:', "$" . number_format($fondo, 2, '.', ',')));

$entradas = $cr->getMovEfectivo('1', $fecha, $uid, $horainicio, $horafin);
foreach ($entradas as $actual) {
    $concepto =  iconv("utf-8", "windows-1252", $actual['conceptomov']);
    $monto = $actual['montomov'];
    $total += $actual['montomov'];
    $pdf->Row(array(iconv("utf-8", "windows-1252", $concepto) . ":", "$" . number_format($monto, 2, '.', ',')));
}

$pdf->Row(array('Total:', "$" . number_format($total, 2, '.', ',')));
$y1 = $pdf->GetY();

$total = 0;
$efectivo = 0;
$tarjeta = 0;
$vales = 0;
$entradas = 0;
$salidas = 0;
$datf = $cr->getVentasByTipo($fecha, 'cash', $uid,  $horainicio, $horafin);
foreach ($datf as $actual) {
    $total += $actual['totalventa'];
    $efectivo += $actual['totalventa'];
}

$pdf->SetY($y);
$pdf->SetX(110);
$pdf->Row(array(iconv("utf-8", "windows-1252", 'Ventas en efectivo:'), "$" . number_format($efectivo, 2, '.', ',')));

$datcd = $cr->getVentasByTipo($fecha, 'card', $uid,  $horainicio, $horafin);
foreach ($datcd as $actual) {
    $total += $actual['totalventa'];
    $tarjeta += $actual['totalventa'];
}

if ($tarjeta > 0) {
    $pdf->SetX(110);
    $pdf->Row(array(iconv("utf-8", "windows-1252", 'Ventas con tarjeta:'), "$" . number_format($tarjeta, 2, '.', ',')));
}

$datvl = $cr->getVentasByTipo($fecha, 'val', $uid, $horainicio, $horafin);
foreach ($datvl as $actual) {
    $total += $actual['totalventa'];
    $vales += $actual['totalventa'];
}

if ($vales > 0) {
    $pdf->SetX(110);
    $pdf->Row(array(iconv("utf-8", "windows-1252", 'Ventas con vales:'), "$" . number_format($vales, 2, '.', ',')));
}


$datf = $cr->getFondoCaja($uid, $fecha, $horainicio, $horafin);
foreach ($datf as $actual) {
    $total += $actual['fondo'];
    $entradas += $actual['fondo'];
}

$ent = $cr->getMovEfectivoByTag('1',  $uid, $fecha, $horainicio, $horafin);
foreach ($ent as $actual) {
    $entradas += $actual['montomov'];
    $total += $actual['montomov'];
}

if ($entradas > 0) {
    $pdf->SetX(110);
    $pdf->setRowColorText(array("#48AD2C"));
    $pdf->Row(array(iconv("utf-8", "windows-1252", 'Entradas:'), "$" . number_format($entradas, 2, '.', ',')));
    $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold));
}

$out = $cr->getMovEfectivoByTag('2',  $uid, $fecha, $horainicio, $horafin);
foreach ($out as $actual) {
    $salidas += $actual['montomov'];
    $total -= $actual['montomov'];
}

if ($salidas > 0) {
    $pdf->SetX(110);
    $pdf->setRowColorText(array("#FF0000"));
    $pdf->Row(array(iconv("utf-8", "windows-1252", 'Salidas:'), "$" . number_format($salidas, 2, '.', ',')));
    $pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold));
}

$pdf->SetX(110);
$pdf->Row(array(iconv("utf-8", "windows-1252", 'Total:'), "$" . number_format($total, 2, '.', ',')));
$y2 = $pdf->GetY();

$ylast = $y2;
if ($y1 > $y2) {
    $ylast = $y1;
}

$pdf->SetY($ylast);
$pdf->Ln(3);
$pdf->SetAligns('C');
$pdf->SetRowBorder('NB');
$pdf->SetLineHeight(6);
$pdf->SetSizes(array(13, 13, 13));
$pdf->SetWidths(array(95, 5, 95));
$pdf->SetStyles(array('B', '', 'B'));
$pdf->setRowColorText(array($txtbold, $clrtxt, $txtbold));
$pdf->Row(array('Salidas de efectivo', '', 'Cancelaciones'));

$y = $pdf->GetY();
$pdf->SetAligns(array('L', 'R'));
$pdf->SetSizes(array(9, 9));
$pdf->SetWidths(array(40, 55));
$pdf->SetStyles(array('B', ''));
$pdf->setRowColorText(array($txtbold, $clrtxt));
$pdf->SetLineHeight(0.1);
$pdf->SetRowBorder('NB');
$pdf->Row(array('', ''));

$salidas = 0;
$pdf->SetRowBorder('B');
$pdf->SetLineHeight(4.5);
$out =  $cr->getMovEfectivoByTag('2',  $uid, $fecha, $horainicio, $horafin);
foreach ($out as $actual) {
    $concepto = $actual['conceptomov'];
    $monto = $actual['montomov'];
    $salidas += $actual['montomov'];

    $pdf->Row(array(iconv("utf-8", "windows-1252", $concepto . ":"), "$" . number_format($monto, 2, '.', ',')));
}
$pdf->Row(array(iconv("utf-8", "windows-1252", "Total:"), "$" . number_format($salidas, 2, '.', ',')));

$y1 = $pdf->GetY();

$cancelaciones = 0;
$pdf->SetY($y);
$can = $cr->getCancelacionesCorte($uid, $fecha, $horainicio, $horafin);
foreach ($can as $actual) {
    $pdf->SetX(110);
    $concepto = $actual['conceptomov'];
    $monto = $actual['montomov'];
    $cancelaciones += $actual['montomov'];
    $pdf->Row(array(iconv("utf-8", "windows-1252", $concepto . ":"), "$" . number_format($monto, 2, '.', ',')));
}
$pdf->SetX(110);
$pdf->Row(array(iconv("utf-8", "windows-1252", "Total:"), "$" . number_format($cancelaciones, 2, '.', ',')));
$y2 = $pdf->GetY();

$ylast = $y2;
if ($y1 > $y2) {
    $ylast = $y1;
}

//------------------------------------------PAGINA 2
$pdf->AddPage();
$fondo = $cr->getFondoCaja($uid, $fecha, $horainicio, $horafin);
if (!empty($fondo)) {
    $pdf->Ln(3);
    $pdf->SetRowBorder('NB');
    $pdf->SetLineHeight(4.5);
    $pdf->SetSizes(array(13));
    $pdf->SetStyles(array('B'));
    $pdf->setRowColorText(array($txtbold));
    $pdf->SetWidths(array(150));
    $pdf->Row(array('Fondo de incio en caja:'));
    $pdf->Ln(1);

    $pdf->SetAligns(array('L', 'C',));
    $pdf->SetWidths(array(145, 45));
    $pdf->SetSizes(array(10, 10));
    $pdf->SetStyles(array('', '',));
    $pdf->setRowColorText(array($txtbold, $txtbold));
    $pdf->SetLineHeight(6.5);
    $pdf->SetRowBorder('B');
    foreach ($fondo as $actual) {
        $monto = $actual['fondo'];
        $hora = $actual['horaingreso'];
        $pdf->Row(array(iconv("utf-8", "windows-1252", "Hora de ingreso: " . date('h:i A', strtotime($hora))), "$" . number_format($monto, 2, '.', ',')));
    }
}

$entrada =  $cr->getMovEfectivo('1', $fecha, $uid, $horainicio, $horafin);
if (!empty($entrada)) {
    $total = 0;
    $pdf->Ln(8);
    $pdf->SetRowBorder('NB');
    $pdf->SetLineHeight(4.5);
    $pdf->SetSizes(array(13));
    $pdf->SetStyles(array('B'));
    $pdf->setRowColorText(array($txtbold));
    $pdf->SetWidths(array(150));
    $pdf->Row(array('Detalles de entrada de efectivo en la caja:'));
    $pdf->Ln(1);

    $pdf->SetAligns(array('L', 'C', 'C',));
    $pdf->SetWidths(array(100, 45, 45));
    $pdf->SetSizes(array(10, 10, 10));
    $pdf->SetStyles(array('', '', ''));
    $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold));
    $pdf->SetLineHeight(6.5);
    $pdf->SetRowBorder('B');
    foreach ($entrada as $actual) {
        $concepto =  iconv("utf-8", "windows-1252", $actual['conceptomov']);
        $monto = $actual['montomov'];
        $horam = $actual['horamov'];
        $total += $actual['montomov'];
        $pdf->Row(array(iconv("utf-8", "windows-1252", $concepto), iconv("utf-8", "windows-1252", date('h:i A', strtotime($horam))), "$" . number_format($monto, 2, '.', ',')));
    }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(145, 6.5, iconv("utf-8", "windows-1252", ""), 0, 0, 'R');
    $pdf->Cell(45, 6.5, "$" . number_format($total, 2, '.', ','), 1, 1, 'C');
}

$salida =  $cr->getMovEfectivoByTag('2',  $uid, $fecha, $horainicio, $horafin);
if (!empty($salida)) {
    $total = 0;
    $pdf->Ln(8);
    $pdf->SetRowBorder('NB');
    $pdf->SetLineHeight(4.5);
    $pdf->SetSizes(array(13));
    $pdf->SetStyles(array('B'));
    $pdf->setRowColorText(array($txtbold));
    $pdf->SetWidths(array(150));
    $pdf->Row(array('Detalles de salida de efectivo en la caja:'));
    $pdf->Ln(1);

    $pdf->SetAligns(array('L', 'C', 'C',));
    $pdf->SetWidths(array(100, 45, 45));
    $pdf->SetSizes(array(10, 10, 10));
    $pdf->SetStyles(array('', '', ''));
    $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold));
    $pdf->SetLineHeight(6.5);
    $pdf->SetRowBorder('B');
    foreach ($salida as $actual) {
        $concepto =  iconv("utf-8", "windows-1252", $actual['conceptomov']);
        $monto = $actual['montomov'];
        $horam = $actual['horamov'];
        $total += $actual['montomov'];
        $pdf->Row(array(iconv("utf-8", "windows-1252", $concepto), iconv("utf-8", "windows-1252", date('h:i A', strtotime($horam))), "$" . number_format($monto, 2, '.', ',')));
    }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(145, 6.5, iconv("utf-8", "windows-1252", ""), 0, 0, 'L');
    $pdf->Cell(45, 6.5, "$" . number_format($total, 2, '.', ','), 1, 1, 'C');
}

$pdf->Ln(8);
$pdf->SetRowBorder('NB');
$pdf->SetLineHeight(4.5);
$pdf->SetSizes(array(13));
$pdf->SetStyles(array('B'));
$pdf->setRowColorText(array($txtbold));
$pdf->SetWidths(array(150));
$pdf->SetAligns(array('L',));
$pdf->Row(array('Detalle de ventas:'));
$pdf->Ln(1);

$pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
$pdf->SetWidths(array(20, 20, 20, 20, 20, 25, 20, 25, 20));
$pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9, 9));
$pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'));
$pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
$pdf->SetLineHeight(6);
$pdf->SetRowBorder('B');

$ventas = $cr->obtenerDetallesVentas($uid, $fecha, $horainicio, $horafin);
if (empty($ventas)) {
    $pdf->SetAligns(array('C'));
    $pdf->SetWidths(array(190));
    $pdf->Row(array(iconv("utf-8", "windows-1252", "No hay ventas registradas.")));
} else {
    $totalVentas = 0;
    $sumaTotalTras = 0;
    $sumaTotalRet = 0;
    $totalSin = 0;
    $descuento = 0;
    $totalDes = 0;
    $pdf->SetWidths(array(20, 20, 20, 20, 20, 25, 20, 25, 20));
    $pdf->SetRowBorder('B');
    $pdf->Row(array(
        iconv("utf-8", "windows-1252", "Folio"),
        iconv("utf-8", "windows-1252", "Hora"),
        iconv("utf-8", "windows-1252", "Productos"),
        iconv("utf-8", "windows-1252", "Forma"),
        iconv("utf-8", "windows-1252", "Subtotal"),
        iconv("utf-8", "windows-1252", "Descuento"),
        iconv("utf-8", "windows-1252", "Traslado"),
        iconv("utf-8", "windows-1252", "Retención"),
        iconv("utf-8", "windows-1252", "Total")
    ));
    $pdf->SetStyles(array('', '', '', '', '', '', '', '', ''));
    foreach ($ventas as $detalles) {
        $folio = $detalles["letra"] . $detalles["folio"];
        $horav = $detalles["hora_venta"];
        $formapago = $detalles["formapago"];
        $descuento = $detalles["descuento"];
        switch ($formapago) {
            case "cash":
                $formapago = "Efectivo";
                break;
            case "card":
                $formapago = "Tarjeta";
                break;
            case "val":
                $formapago = "Vales";
                break;
            default:
                $formapago = "Otro";
        }
        $tags = $detalles["tagventa"];
        $total = $detalles["totalventa"];
        $descuento = $detalles["descuento"];
        $totalVentas += $total;
        $totalDes += $descuento;

        $detalles_venta = $cr->obtenerDetallesVentaPorTag($tags);
        $cantidad = 0;
        $subtotal = 0;
        $sumatras = 0;
        $sumaret = 0;

        foreach ($detalles_venta as $resultado) {
            $cantidad += $resultado['venta_cant'];
            $subtotal += $resultado['venta_precio'];
            $traslados = $resultado['venta_traslados'];
            $partes = explode('<impuesto>', $traslados);
            foreach ($partes as $parte) {
                $valores = explode('-', $parte);
                $tras = floatval($valores[0]);
                $sumatras += $tras;
            }

            $retencion = $resultado['venta_retencion'];
            $partesret = explode('<impuesto>', $retencion);
            foreach ($partesret as $parteret) {
                $valores = explode('-', $parteret);
                $ret = floatval($valores[0]);
                $sumaret += $ret;
            }
        }

        $totalSin += $subtotal;
        $sumaTotalTras += $sumatras;
        $sumaTotalRet += $sumaret;

        if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
            $pdf->AddPage();
        }

        $pdf->SetWidths(array(20, 20, 20, 20, 20, 25, 20, 25, 20));
        $pdf->Row(array(
            iconv("utf-8", "windows-1252", $folio),
            iconv("utf-8", "windows-1252", date('h:i A', strtotime($horav))),
            $cantidad,
            iconv("utf-8", "windows-1252", $formapago),
            "$" . number_format($subtotal, 2, '.', ','),
            "$" . number_format($descuento, 2, '.', ','),
            "$" . number_format($sumatras, 2, '.', ','),
            "$" . number_format($sumaret, 2, '.', ','),
            "$" . number_format($total, 2, '.', ',')
        ));
    }
    $pdf->SetFont('Arial', 'B', 9);
    $anchoCelda1 = 80;
    $anchoCelda2 = 20;
    $anchoTotal = $anchoCelda1 + $anchoCelda2 * 3;
    $pdf->Cell($anchoCelda1, 4.5, iconv("utf-8", "windows-1252", "Total"), 1, 0, 'L');
    $pdf->Cell($anchoCelda2, 4.5, "$" . number_format($totalSin, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(25, 4.5, "$" . number_format($totalDes, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(20, 4.5, "$" . number_format($sumaTotalTras, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(25, 4.5, "$" . number_format($sumaTotalRet, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell($anchoCelda2, 4.5, "$" . number_format($totalVentas, 2, '.', ','), 1, 1, 'C');
    $pdf->Cell($anchoTotal, 2, '', 0, 1, 'C');
}

$pdf->Ln(8);
$pdf->SetRowBorder('NB');
$pdf->SetLineHeight(4.5);
$pdf->SetSizes(array(13));
$pdf->SetStyles(array('B'));
$pdf->setRowColorText(array($txtbold));
$pdf->SetWidths(array(150));
$pdf->SetAligns(array('L',));
if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
    $pdf->AddPage();
}
$pdf->Row(array('Detalle de ventas canceladas:'));
$pdf->Ln(1);
$pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
$pdf->SetWidths(array(20, 20, 20, 20, 20, 25, 20, 25, 20));
$pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9, 9));
$pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'));
$pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
$pdf->SetLineHeight(5.5);
$pdf->SetRowBorder('B');

$ventas = $cr->obtenerDetallesVentasCanceladas($fecha, $horainicio, $horafin, $uid);
if (empty($ventas)) {
    $pdf->SetAligns(array('C'));
    $pdf->SetWidths(array(190));
    if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
        $pdf->AddPage();
        $pdf->SetAligns(array('C'));
        $pdf->SetWidths(array(190));
    }
    $pdf->Row(array(iconv("utf-8", "windows-1252", "No hay ventas canceladas.")));
} else {
    $totalVentas = 0;
    $sumaTotalTras = 0;
    $sumaTotalRet = 0;
    $totalSin = 0;
    $totalFinDes = 0;
    $desc = 0;
    if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
        $pdf->AddPage();
    }
    $pdf->Row(array(
        iconv("utf-8", "windows-1252", "Folio"),
        iconv("utf-8", "windows-1252", "Hora"),
        iconv("utf-8", "windows-1252", "Productos"),
        iconv("utf-8", "windows-1252", "Forma"),
        iconv("utf-8", "windows-1252", "Subtotal"),
        iconv("utf-8", "windows-1252", "Descuento"),
        iconv("utf-8", "windows-1252", "Traslado"),
        iconv("utf-8", "windows-1252", "Retención"),
        iconv("utf-8", "windows-1252", "Total")
    ));
    $pdf->SetStyles(array('', '', '', '', '', '', ''));
    foreach ($ventas as $detalles) {
        $folio = $detalles["letra"] . $detalles["folio"];
        $hora = $detalles["hora_cancelada"];
        $formapago = $detalles["formapago"];
        $desc = $detalles["descuento"];
        switch ($formapago) {
            case "cash":
                $formapago = "Efectivo";
                break;
            case "card":
                $formapago = "Tarjeta";
                break;
            case "val":
                $formapago = "Vales";
                break;
            default:
                $formapago = "Otro";
        }
        $tags = $detalles["tagventa"];
        $total = $detalles["totalventa"];
        $totalVentas += $total;
        $totalFinDes += $desc;

        $detalles_venta = $cr->obtenerDetallesVentaPorTag($tags);
        $cantidad = 0;
        $subtotal = 0;
        $sumatras = 0;
        $sumaret = 0;

        foreach ($detalles_venta as $resultado) {
            $cantidad += $resultado['venta_cant'];
            $subtotal += $resultado['venta_precio'];
            $traslados = $resultado['venta_traslados'];
            $partes = explode('<impuesto>', $traslados);
            foreach ($partes as $parte) {
                $valores = explode('-', $parte);
                $tras = floatval($valores[0]);
                $sumatras += $tras;
            }

            $retencion = $resultado['venta_retencion'];
            $partesret = explode('<impuesto>', $retencion);
            foreach ($partesret as $parteret) {
                $valores = explode('-', $parteret);
                $ret = floatval($valores[0]);
                $sumaret += $ret;
            }
        }

        $totalSin += $subtotal;
        $sumaTotalTras += $sumatras;
        $sumaTotalRet += $sumaret;

        if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
            $pdf->AddPage();
            $pdf->Ln(1);
            $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
            $pdf->SetWidths(array(20, 20, 20, 20, 20, 25, 20, 25, 20));
            $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9, 9));
            $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'));
            $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
            $pdf->SetLineHeight(5.5);
            $pdf->SetRowBorder('B');
        }
        $pdf->Row(array(
            iconv("utf-8", "windows-1252", $folio),
            iconv("utf-8", "windows-1252", date('h:i A', strtotime($hora))),
            $cantidad,
            iconv("utf-8", "windows-1252", $formapago),
            "$" . number_format($subtotal, 2, '.', ','),
            "$" . number_format($desc, 2, '.', ','),
            "$" . number_format($sumatras, 2, '.', ','),
            "$" . number_format($sumaret, 2, '.', ','),
            "$" . number_format($total, 2, '.', ',')
        ));
    }
    $pdf->SetFont('Arial', 'B', 9);
    $anchoCelda1 = 80;
    $anchoCelda2 = 20;
    $anchoTotal = $anchoCelda1 + $anchoCelda2 * 3;
    $pdf->Cell($anchoCelda1, 4.5, iconv("utf-8", "windows-1252", "Total"), 1, 0, 'L');
    $pdf->Cell($anchoCelda2, 4.5, "$" . number_format($totalSin, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(25, 4.5, "$" . number_format($totalFinDes, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(20, 4.5, "$" . number_format($sumaTotalTras, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(25, 4.5, "$" . number_format($sumaTotalRet, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell($anchoCelda2, 4.5, "$" . number_format($totalVentas, 2, '.', ','), 1, 1, 'C');
    $pdf->Cell($anchoTotal, 2, '', 0, 1, 'C');
}

$pdf->Ln(8);
$pdf->SetRowBorder('NB');
$pdf->SetLineHeight(4.5);
$pdf->SetSizes(array(13));
$pdf->SetStyles(array('B'));
$pdf->setRowColorText(array($txtbold));
$pdf->SetWidths(array(150));
$pdf->SetAligns(array('L',));
$pdf->Row(array('Detalle pagos de facturas:'));
$pdf->Ln(8);
$detallesPagosPorForma  = $cr->obtenerDetallesPagosPorForma($uid, $fecha, $horainicio, $horafin);
if (empty($detallesPagosPorForma)) {
    if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
        $pdf->AddPage();
        $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
        $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
        $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
        $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
        $pdf->SetLineHeight(5.5);
        $pdf->SetRowBorder('B');
        $pdf->SetAligns(array('C'));
        $pdf->SetWidths(array(190));
    }
    $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
    $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
    $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
    $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
    $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
    $pdf->SetLineHeight(5.5);
    $pdf->SetRowBorder('B');
    $pdf->SetAligns(array('C'));
    $pdf->SetWidths(array(190));
    $pdf->Row(array(iconv("utf-8", "windows-1252", "No hay pagos de facturas registrados.")));
} else {
    foreach ($detallesPagosPorForma as $formaPago => $detalles) {
        if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
            $pdf->AddPage();
            $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
            $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
            $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
            $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
            $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
            $pdf->SetLineHeight(6.5);
            $pdf->SetRowBorder('B');
            $pdf->SetAligns(array('L'));
            $pdf->SetWidths(array(190));
        }
        $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
        $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
        $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
        $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
        $pdf->SetLineHeight(6.5);
        $pdf->SetRowBorder('B');
        $pdf->SetAligns(array('L'));
        $pdf->SetWidths(array(190));
        $pdf->Row(array(iconv("utf-8", "windows-1252", "Forma de pago: " . $formaPago)));

        $sumaTotalPagos = 0;
        $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
        $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
        $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
        $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
        $pdf->SetLineHeight(2.9);
        $pdf->SetRowBorder('B');

        if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
            $pdf->AddPage();
            $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
            $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
            $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
            $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
            $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
            $pdf->SetLineHeight(2.9);
            $pdf->SetRowBorder('B');
        }
        $pdf->Row(array(
            iconv("utf-8", "windows-1252", "\nFolio"),
            iconv("utf-8", "windows-1252", "\nHora"),
            iconv("utf-8", "windows-1252", "\nEmisor"),
            iconv("utf-8", "windows-1252", "\nReceptor"),
            iconv("utf-8", "windows-1252", "\nMoneda"),
            iconv("utf-8", "windows-1252", "\nNo. de complemento\n "),
            iconv("utf-8", "windows-1252", "\nTotal")
        ));

        foreach ($detalles as $detalle) {
            $folio = $detalle["letra"] . $detalle["foliopago"];
            $horapago = $detalle["hora_creacion"];
            $emisor = $detalle["razonemisor"];
            $receptor = $detalle["razonreceptor"];
            $moneda = $detalle["nombre_moneda"];
            $orden = $detalle["orden"];
            $total = $detalle["total"];
            $sumaTotalPagos += $total;
            $emisor_capitalize = !empty($emisor) ? ucwords(mb_strtolower($emisor, 'UTF-8')) : "No disponible";
            $receptor_capitalize = !empty($receptor) ? ucwords(mb_strtolower($receptor, 'UTF-8')) : "No disponible";

            $pdf->SetLineHeight(3.5);
            if ($pdf->GetY() + 10 > $pdf->getPageBreakTrigger()) {
                $pdf->AddPage();
                $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $pdf->SetWidths(array(20, 20, 40, 45, 20, 25, 20));
                $pdf->SetSizes(array(9, 9, 9, 9, 9, 9, 9, 9));
                $pdf->SetStyles(array('B', 'B', 'B', 'B', 'B', 'B'));
                $pdf->setRowColorText(array($txtbold, $txtbold, $txtbold, $txtbold, $txtbold, $txtbold));
                $pdf->SetLineHeight(2.9);
                $pdf->SetRowBorder('B');
            }
            $pdf->Row(array(
                iconv("utf-8", "windows-1252", "\n" . $folio),
                iconv("utf-8", "windows-1252", "\n" . date('h:i A', strtotime($horapago))),
                iconv("utf-8", "windows-1252", "\n" . $emisor_capitalize . "\n "),
                iconv("utf-8", "windows-1252", "\n" . $receptor_capitalize . "\n "),
                iconv("utf-8", "windows-1252", "\n" . $moneda),
                iconv("utf-8", "windows-1252", "\n" . $orden),
                iconv("utf-8", "windows-1252", "\n" . "$" . number_format($total, 2, '.', ',')),

            ));
        }
        $pdf->SetLineHeight(6.5);
        $pdf->SetFont('Arial', 'B', 9);
        $anchoCelda1 = 170;
        $anchoCelda2 = 20;
        $anchoTotal = $anchoCelda1 + $anchoCelda2 * 3;
        $pdf->Cell($anchoCelda1, 6.5, iconv("utf-8", "windows-1252", ""), 0, 0, 'L');
        $pdf->Cell($anchoCelda2, 6.5, "$" . number_format($sumaTotalPagos, 2, '.', ','), 1, 1, 'C');
        $pdf->Cell($anchoTotal, 2, '', 0, 1, 'C');
    }
}

$pdf->isFinished = true;
$nm = str_replace(" ", "_", $usuario);
$pdf->Output('corte_' . $fecha . '_' . $nm . '.pdf', 'I');