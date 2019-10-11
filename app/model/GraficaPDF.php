<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include 'conexion.php';
require('../../pdf/fpdf/diag.php');



class PDF extends FPDF {
    function Header() {
        $DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa");

        $this->Image('img/home.png',3,3,30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(30);
        $this->Cell(120, 10, utf8_decode('REPORTE DE CALIFICACIÓN'), 0, 1, 'C');
        $this->Cell(190, 10, utf8_decode($DatosEmpresa[0]['nit']), 0, 1, 'C');
        $this->Cell(190, 10, utf8_decode($DatosEmpresa[0]['Nombre']), 0, 1, 'C');
        $this->Cell(190, 10, utf8_decode($DatosEmpresa[0]['Slogan']), 0, 1, 'C');
        $this->Ln(20);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}
$pdf = new PDF_Diag();
$pdf->AddPage();
$data = array('Men' => 1510, 'Women' => 1610, 'Children' => 1400);

//Pie chart
$pdf->SetFont('Arial', 'BIU', 12);
$pdf->Cell(0, 5, '1 - Pie chart', 0, 1);
$pdf->Ln(8);

$pdf->SetFont('Arial', '', 10);
$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->Cell(30, 5, 'Number of men:');
$pdf->Cell(15, 5, $data['Men'], 0, 0, 'R');
$pdf->Ln();
$pdf->Cell(30, 5, 'Number of women:');
$pdf->Cell(15, 5, $data['Women'], 0, 0, 'R');
$pdf->Ln();
$pdf->Cell(30, 5, 'Number of children:');
$pdf->Cell(15, 5, $data['Children'], 0, 0, 'R');
$pdf->Ln();
$pdf->Ln(8);

$pdf->SetXY(90, $valY);
$col1=array(100,100,255);
$col2=array(255,100,100);
$col3=array(255,255,100);
$pdf->PieChart(100, 35, $data, '%l (%p)', array($col1,$col2,$col3));
$pdf->SetXY($valX, $valY + 40);

//Bar diagram
$pdf->SetFont('Arial', 'BIU', 12);
$pdf->Cell(0, 5, '2 - Bar diagram', 0, 1);
$pdf->Ln(8);
$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->BarDiagram(190, 70, $data, '%l : %v (%p)', array(255,175,100));
$pdf->SetXY($valX, $valY + 80);


$pdf->Cell(190, 10, utf8_decode("algo"), 0, 1, 'C');


$pdf->Output();
?>