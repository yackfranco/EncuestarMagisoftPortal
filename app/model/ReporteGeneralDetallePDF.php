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
$IdEmpresa = $_REQUEST["IdEmpresa"];

class PDF extends FPDF {

    function Header() {
        $DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa");

        $this->Image('img/LOGO.png', 3, 3, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(30);
        $this->Cell(120, 10, utf8_decode('REPORTE DE CALIFICACIÓN'), 0, 1, 'C');
        $this->Cell(190, 10, utf8_decode($DatosEmpresa[0]['nit']), 0, 1, 'C');
        $this->Cell(190, 10, utf8_decode($DatosEmpresa[0]['Nombre']), 0, 1, 'C');
        $this->Cell(190, 10, utf8_decode($DatosEmpresa[0]['Slogan']), 0, 1, 'C');
        $this->Ln(10);
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

if ($fechainical != null && $fechafinal != null) {
    $concatF1F2 = " WHERE calificacion.IdEmpresa = $IdEmpresa and (calificacion.FechaCalif >= '$fechainical 00:00:00' AND calificacion.FechaCalif <= '$fechafinal 23:59:59')";
    $consultainicial = $consultainicial . $concatF1F2;
    $consultainicial2 = $consultainicial2 . $concatF1F2;
}
if ($fechainical != null && $fechafinal == null) {
    $concatF1F2 = " WHERE calificacion.IdEmpresa = $IdEmpresa and (calificacion.FechaCalif >= '$fechainical 00:00:00' AND calificacion.FechaCalif <= '$fechainical 23:59:59')";
    $consultainicial = $consultainicial . $concatF1F2;
    $consultainicial2 = $consultainicial2 . $concatF1F2;
}
if ($IdUsuario != null && $IdUsuario != "") {
    $concatusu = " AND usuario.IdUsuario = $IdUsuario";
    $consultainicial = $consultainicial . $concatusu;
    $consultainicial2 = $consultainicial2 . $concatusu;
}
if ($idciudad != null && $idciudad != "") {
    $concatCiudad = " AND ciudad.IdCiudad = $idciudad";
    $consultainicial = $consultainicial . $concatCiudad;
    $consultainicial2 = $consultainicial2 . $concatCiudad;
}
if ($IdSede != null && $IdSede != "") {
    $concatSede = " AND sede.IdSede = $IdSede";
    $consultainicial = $consultainicial . $concatSede;
    $consultainicial2 = $consultainicial2 . $concatSede;
}

$contartotal = 0;
// $valores = hacerConsulta($consultainicial);
$SQLIdCiudad = hacerConsulta($consultainicial . " GROUP BY ciudad.IdCiudad");
foreach ($SQLIdCiudad as $valueCiudad) {
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(190, 10, utf8_decode("CIUDAD: "), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 15);
    $pdf->Cell(190, 10, utf8_decode($valueCiudad['NombreCiudad']), 0, 1, 'C');
    $pdf->Ln(1);

    $SQLIdSede = hacerConsulta($consultainicial . " GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =" . $valueCiudad['IdCiudad']);
    foreach ($SQLIdSede as $valueSede) {

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(190, 10, utf8_decode("SEDE: "), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 15);
        $pdf->Cell(190, 10, utf8_decode($valueSede['NombreSede']), 0, 1, 'C');
        $pdf->Ln(1);

        // print_r("Sede ".$valueSede['IdSede']);
        $SQLIdUsuario = hacerConsulta($consultainicial . " GROUP BY usuario.IdUsuario, sede.IdSede HAVING IdSede =" . $valueSede['IdSede']);
        foreach ($SQLIdUsuario as $valueUsuario) {

            $pdf->SetFont('Arial', 'B', 15);
            $pdf->Cell(190, 10, utf8_decode($valueUsuario['NombreCompleto']), 0, 1, 'C');
            $pdf->Ln(2);

            // print_r("Sede ".$valueSede['IdSede']);
            $SQLIdPregunta = hacerConsulta($consultainicial . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdSede =" . $valueSede['IdSede']);
            //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
            foreach ($SQLIdPregunta as $valuePregunta) {
                $pdf->SetFillColor(232, 232, 232);

                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Cell(190, 10, utf8_decode($valuePregunta['Pregunta']), 0, 1, 'C');
                $pdf->Ln(5);

                $pdf->Cell(95, 10, utf8_decode("CALIFICACIÓN"), 1, 0, 'C', 1);
                $pdf->Cell(95, 10, utf8_decode("TOTAL"), 1, 1, 'C', 1);

                $SQLTotal = hacerConsulta($consultainicial2 . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =" . $valueSede['IdSede'] . " AND IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdPregunta =" . $valuePregunta['IdPregunta']);
                // echo $consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta'];
                //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                foreach ($SQLTotal as $valueTotal) {
                    $pdf->SetFont('Arial', '', 15);
                    $contartotal += $valueTotal['Total'];
                    $pdf->Cell(95, 10, utf8_decode($valueTotal['ValorCalif']), 1, 0, 'C');
                    $pdf->Cell(95, 10, utf8_decode($valueTotal['Total']), 1, 1, 'C');
                }
                $pdf->Cell(95, 10, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(95, 10, utf8_decode($contartotal), 1, 1, 'C');
                $contartotal = 0;
            }
        }
    }
}






// // $valores = hacerConsulta($consultainicial);
// $SQLIdCiudad = hacerConsulta($consultainicial." GROUP BY ciudad.IdCiudad");
// foreach ($SQLIdCiudad as $valueCiudad) {
//     $pdf->Cell(190, 10, utf8_decode("CIUDAD: ".$valueCiudad['NombreCiudad']), 0, 1, 'C');
//     $SQLIdSede = hacerConsulta($consultainicial." GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =".$valueCiudad['IdCiudad']);
//     foreach ($SQLIdSede as $valueSede) {
//         $pdf->SetFont('Arial', 'B', 15);
//         $pdf->Cell(190, 10, utf8_decode("SEDE: ".$valueSede['NombreSede']), 0, 1, 'C');
//         $pdf->Ln(5);
//         $pdf->SetFont('Arial', 'B', 10);
//         $pdf->Cell(47.5, 10, utf8_decode("PREGUNTA"), 1, 0, 'C');
//         $pdf->Cell(55, 10, utf8_decode("NOMBRE USUARIO"), 1, 0, 'C');
//         $pdf->Cell(47.5, 10, utf8_decode("CALIFICACIÓN"), 1, 0, 'C');
//         $pdf->Cell(47.5, 10, utf8_decode("FECHA DE CALIFICACIÓN"), 1, 1, 'C');
//         $SQLIdUsuario = hacerConsulta($consultainicial);
//         foreach ($SQLIdUsuario as $valueUsuario) {
//                 $cellWidth = 47.5;
//                 $cellHeight = 4;
//                 if ($pdf->GetStringWidth($valueUsuario['Pregunta']) < $cellWidth) {
//                     $line = 1;
//                 } else {
//                     $textLength = strlen($valueUsuario['Pregunta']);
//                     $errMargin = 10;
//                     $startChar = 0;
//                     $maxChar = 0;
//                     $textArray = array();
//                     $tmpString = "";
//                     while ($startChar < $textLength) {
//                         while (
//                         $pdf->GetStringWidth($tmpString) < ($cellWidth - $errMargin) &&
//                         ($startChar + $maxChar) < $textLength) {
//                             $maxChar++;
//                             $tmpString = substr($valueUsuario['Pregunta'], $startChar, $maxChar);
//                         }
//                         $startChar = $startChar + $maxChar;
//                         array_push($textArray, $tmpString);
//                         $maxChar = 0;
//                         $tmpString = '';
//                     }
//                     $line = count($textArray);
//                 }
//                 $xPos = $pdf->GetX();
//                 $yPos = $pdf->GetY();
//                 $pdf->SetFont('Arial', '', 10);
//                 $pdf->MultiCell($cellWidth, $cellHeight, utf8_decode($valueUsuario['Pregunta']), 1, 1,'',1);
//                 $pdf->SetXY($xPos + $cellWidth, $yPos);
//                 $pdf->Cell(55, ($line * $cellHeight), $valueUsuario['NombreCompleto'], 1, 0, 'C');
//                 $pdf->Cell(47.5, ($line * $cellHeight), $valueUsuario['ValorCalif'], 1, 0, 'C', 0);
//                 $pdf->Cell(47.5, ($line * $cellHeight), $valueUsuario['FechaCalif'], 1, 1, 'C', 0);
//         }
//         $pdf->Ln(10);
//     }
// }

$pdf->Output('F', 'reportespdf/ReporteGeneralDetalle.pdf');
$validar = array('respuesta' => 'Enviado Correctamente');
echo json_encode($validar, JSON_FORCE_OBJECT);
//  $pdf->Output();
?>