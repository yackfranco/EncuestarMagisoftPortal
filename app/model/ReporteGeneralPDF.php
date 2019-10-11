<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include 'conexion.php';
//include 'PlantillaPDF.php';
//require 'conexion.php';
//  require('../../pdf/fpdf/diag.php');

require '../../pdf/fpdf/fpdf.php';


    $fechaactual = DevolverUnDato("SELECT NOW()");

    
    $fechainical = $_REQUEST["gfechainicial"];
    $fechafinal = $_REQUEST["gfechafinal"];
    $idciudad = $_REQUEST["gidciudad"];
    $IdUsuario = $_REQUEST["gIdUsuario"];
    $IdSede = $_REQUEST["gIdSede"];


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
//  $pdf = new PDF_Diag();
   $pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

    
    $consultainicial2 = "SELECT calificacion.*, usuario.NombreCompleto, sede.NombreSede, ciudad.NombreCiudad, pregunta.Pregunta,valorcalif.ValorCalif,COUNT(*) as Total FROM `calificacion` join usuario on (usuario.IdUsuario = calificacion.IdUsuario) join sede on (sede.IdSede = calificacion.IdSede) join ciudad on (ciudad.IdCiudad = calificacion.IdCiudad) join pregunta on (pregunta.IdPregunta = calificacion.IdPregunta) join valorcalif on (valorcalif.NumeroCalif = calificacion.NumeroCalif)";
    $consultainicial = "SELECT calificacion.*, usuario.NombreCompleto, sede.NombreSede, ciudad.NombreCiudad, pregunta.Pregunta,valorcalif.ValorCalif FROM `calificacion` join usuario on (usuario.IdUsuario = calificacion.IdUsuario)  join sede on (sede.IdSede = calificacion.IdSede) join ciudad on (ciudad.IdCiudad = calificacion.IdCiudad) join pregunta on (pregunta.IdPregunta = calificacion.IdPregunta) join valorcalif on (valorcalif.NumeroCalif = calificacion.NumeroCalif)";
   
    if($fechainical != null && $fechafinal != null){
     $concatF1F2 = " WHERE (calificacion.FechaCalif >= '$fechainical 00:00:00' AND calificacion.FechaCalif <= '$fechafinal 23:59:59')";
     $consultainicial = $consultainicial.$concatF1F2;
     $consultainicial2 = $consultainicial2.$concatF1F2;
   }
   if($fechainical != null && $fechafinal == null){
    $concatF1F2 = " WHERE (calificacion.FechaCalif >= '$fechainical 00:00:00' AND calificacion.FechaCalif <= '$fechainical 23:59:59')";
    $consultainicial = $consultainicial.$concatF1F2;
    $consultainicial2 = $consultainicial2.$concatF1F2;
   }
    if ($IdUsuario != null && $IdUsuario !=""){
        $concatusu=" AND usuario.IdUsuario = $IdUsuario";
        $consultainicial = $consultainicial.$concatusu;
        $consultainicial2 = $consultainicial2.$concatusu;
    }
    if($idciudad != null && $idciudad !=""){
        $concatCiudad = " AND ciudad.IdCiudad = $idciudad";
        $consultainicial = $consultainicial.$concatCiudad;
        $consultainicial2 = $consultainicial2.$concatCiudad;
    }
    if($IdSede != null && $IdSede !=""){
        $concatSede = " AND sede.IdSede = $IdSede";
        $consultainicial = $consultainicial.$concatSede;
        $consultainicial2 = $consultainicial2.$concatSede;
    }


    $valores = hacerConsulta($consultainicial);
    $SQLIdCiudad = hacerConsulta($consultainicial." GROUP BY ciudad.IdCiudad");
    foreach ($SQLIdCiudad as $valueCiudad) {
        $pdf->Cell(190, 10, utf8_decode($valueCiudad['NombreCiudad']), 0, 1, 'C');

        $SQLIdSede = hacerConsulta($consultainicial." GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =".$valueCiudad['IdCiudad']);
        foreach ($SQLIdSede as $valueSede) {

            $pdf->Cell(50, 10, utf8_decode($valueSede['NombreSede']), 0, 1, 'C');

            $SQLIdUsuario = hacerConsulta($consultainicial." GROUP BY usuario.IdUsuario, sede.IdSede HAVING IdSede =".$valueSede['IdSede']);
            foreach ($SQLIdUsuario as $valueUsuario) {

                $pdf->Cell(190, 10, utf8_decode($valueUsuario['NombreCompleto']), 0, 1, 'C');

                $SQLIdPregunta = hacerConsulta($consultainicial." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario']." AND IdSede =".$valueSede['IdSede']);
                //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                foreach ($SQLIdPregunta as $valuePregunta) {

                    $pdf->Cell(190, 10, utf8_decode($valuePregunta['Pregunta']), 0, 1, 'C');

                    $SQLTotal = hacerConsulta($consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta']);
                    // echo $consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta'];
                    //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                    foreach ($SQLTotal as $valueTotal) {

                        // $sheet->setCellValue("A".$contar, $valueTotal['ValorCalif']);
                        // $sheet->setCellValue("B".$contar, $valueTotal['Total']);     
                        // $sheet->getStyle('A'.$contar)->getAlignment()->setHorizontal('center');


                        

                    }
                }
            }
        }
    }
    /*
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

*/

// $pdf->Output('F','reportespdf/ReportePorDefecto.pdf');
$pdf->Output();
// $validar = array('respuesta' => 'Enviado Correctamente');
// echo json_encode($validar, JSON_FORCE_OBJECT);

?>