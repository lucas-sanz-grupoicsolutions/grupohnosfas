<?php

/*******************************************************************************
* Script :  PDF_Code128
* Version : 1.2
* Date :    2016-01-31
* Auteur :  Roland Gautier
*
* Version   Date        Detail
* 1.2       2016-01-31  Compatibility with FPDF 1.8
* 1.1       2015-04-10  128 control characters FNC1 to FNC4 accepted
* 1.0       2008-05-20  First release
*
* Code128($x, $y, $code, $w, $h)
*     $x,$y :     angle supérieur gauche du code à barre
*                 upper left corner of the barcode
*     $code :     le code à créer
*                 ascii text to convert to barcode
*     $w :        largeur hors tout du code dans l'unité courante
*                 (prévoir 5 à 15 mm de blanc à droite et à gauche)
*                 barcode total width (current unit)
*                 (keep 5 to 15 mm white on left and right sides)
*     $h :        hauteur hors tout du code dans l'unité courante
*                 barcode total height (current unit)
*
* Commutation des jeux ABC automatique et optimisée
* Automatic and optimized A/B/C sets selection and switching
*
*
*   128 barcode control characters
*   ASCII   Aset            Bset        [ne pas utiliser][do not use]
*   ---------------------------
*   200     FNC3            FNC3
*   201     FNC2            FNC2
*   202     ShiftA          ShiftB
*   203     [SwitchToCset]  [SwitchToCset]
*   204     [SwitchToBset]  FNC4
*   205     FNC4            [SwitchToAset]
*   206     FNC1            FNC1
*******************************************************************************/


namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Entities\Barcode;
use App\Libraries\Fpdf\FPDF;


class MyPDF extends FPDF {

    // Pie de página
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}
