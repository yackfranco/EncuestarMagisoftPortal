<?php

// echo 'algo';

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');


include 'conexion.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

// // require 'vendor/phpoffice/phpspreadsheet/samples/Header.php';


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$fechaactual = DevolverUnDato("SELECT NOW()");

$DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa");

$fechainical = $_REQUEST["gfechainicial"];
$fechafinal = $_REQUEST["gfechafinal"];
$idciudad = $_REQUEST["gidciudad"];
$IdUsuario = $_REQUEST["gIdUsuario"];
$IdSede = $_REQUEST["gIdSede"];
$Pregunta = $_REQUEST["gPregunta"];
$IdEmpresa = $_REQUEST["IdEmpresa"];

$colorTitulo = "ffffff";
$colorUsuario = "FF5733";

// //ultima consulta  GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede = 1 AND IdUsuario = 1 AND IdPregunta = 25";
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
if ($Pregunta != null && $Pregunta != "") {
    $concatPregunta = " AND calificacion.IdPregunta = $Pregunta";
    $consultainicial = $consultainicial . $concatPregunta;
    $consultainicial2 = $consultainicial2 . $concatPregunta;
}
//     print_r($consultainicial);
//     exit();
//     ///INGRESAR UNA IMAGEN AL EXCEL
$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('LOGO');
$drawing->setDescription('camara de comercio');
$drawing->setPath('img/LOGO.png');
$drawing->setCoordinates('A1');
$drawing->setWidthAndHeight(190, 140);
$drawing->setResizeProportional(true);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

//TITULO, UNIR CELDAS,CENTRADO
$sheet->setCellValue("A5", 'REPORTE DE CALIFICACIÓN');
$sheet->getStyle("A5")->getFont()->setBold(true);
$sheet->getStyle("A5")->getFont()->setSize(15);
$sheet->getRowDimension('5')->setRowHeight(20);
$sheet->mergeCells('A5:D5');
$sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
//$sheet->getStyle('A5:I10')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);


$sheet->setCellValue("A7", $DatosEmpresa[0]['nit']);
$sheet->mergeCells('A7:D7');
$sheet->getStyle('A7')->getAlignment()->setHorizontal('center');

$sheet->setCellValue("A8", $DatosEmpresa[0]['Nombre']);
$sheet->getStyle("A8")->getFont()->setBold(true);
$sheet->getStyle("A8")->getFont()->setSize(13);
$sheet->getRowDimension('8')->setRowHeight(20);
$sheet->mergeCells('A8:D8');
$sheet->getStyle('A8')->getAlignment()->setHorizontal('center');

$sheet->setCellValue("A9", $DatosEmpresa[0]['Slogan']);
$sheet->mergeCells('A9:D9');
$sheet->getStyle('A9')->getAlignment()->setHorizontal('center');
//colorear
//$sheet->getStyle('A1:I10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorTitulo);

$contar = 12;


$ContarNeutro = 0;
$contartotal = 0;
// echo $consultainicial;
// $valores = hacerConsulta($consultainicial);
$SQLIdCiudad = hacerConsulta($consultainicial . " GROUP BY ciudad.IdCiudad");

foreach ($SQLIdCiudad as $valueCiudad) {
    $sheet->setCellValue("A" . $contar, $valueCiudad['NombreCiudad']);
    $sheet->getStyle("A" . $contar)->getFont()->setBold(true);
    $sheet->getStyle("A" . $contar)->getFont()->setSize(13);
    $sheet->getRowDimension($contar)->setRowHeight(20);
    $sheet->mergeCells('A' . $contar . ':D' . $contar);
    $sheet->getStyle('A' . $contar)->getAlignment()->setHorizontal('center');

    $contar = $contar + 2;

    // print_r("Ciudad ".$valueCiudad['IdCiudad']);

    $SQLIdSede = hacerConsulta($consultainicial . " GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =" . $valueCiudad['IdCiudad']);
    foreach ($SQLIdSede as $valueSede) {

        $sheet->setCellValue("A" . $contar, $valueSede['NombreSede']);
        $sheet->mergeCells('A' . $contar . ':D' . $contar);
        $sheet->getStyle('A' . $contar)->getAlignment()->setHorizontal('center');

        $contar = $contar + 2;

        //     // print_r("Sede ".$valueSede['IdSede']);
        $SQLIdUsuario = hacerConsulta($consultainicial . " GROUP BY usuario.IdUsuario, sede.IdSede HAVING IdSede =" . $valueSede['IdSede']);
        foreach ($SQLIdUsuario as $valueUsuario) {
            $sheet->setCellValue("A" . $contar, $valueUsuario['NombreCompleto']);
            //AQUI------------------------------------------------
            $sheet->getStyle("A" . $contar)->getFont()->setBold(true);
            $sheet->getStyle("A" . $contar)->getFont()->setSize(12);
            $sheet->getRowDimension($contar)->setRowHeight(20);
            $sheet->mergeCells('A' . $contar . ':D' . $contar);
            $sheet->getStyle('A' . $contar)->getAlignment()->setHorizontal('center');

            //colorear usuario
            //$sheet->getStyle('A'.$contar.':I'.$contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorUsuario);
            $contar = $contar + 2;

            // print_r("Sede ".$valueSede['IdSede']);
            $SQLIdPregunta = hacerConsulta($consultainicial . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdSede =" . $valueSede['IdSede']);
            //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
            foreach ($SQLIdPregunta as $valuePregunta) {

                $sheet->setCellValue("A" . $contar, $valuePregunta['Pregunta']);
                $sheet->mergeCells('A' . $contar . ':D' . $contar);
                $sheet->getStyle('A' . $contar)->getAlignment()->setHorizontal('center');

                $contar = $contar + 3;
                $sheet->setCellValue("A" . $contar, "CALIFICACION");
                $sheet->setCellValue("B" . $contar, "TOTAL");
                $sheet->getStyle("A" . $contar . ":B" . $contar)->getFont()->setBold(true);
                $sheet->getStyle("A" . $contar . ":B" . $contar)->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A" . $contar . ":B" . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
                $contar = $contar + 1;

                $SQLTotal = hacerConsulta($consultainicial2 . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =" . $valueSede['IdSede'] . " AND IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdPregunta =" . $valuePregunta['IdPregunta']);
                // echo $consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta'];
                //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                foreach ($SQLTotal as $valueTotal) {

                    $sheet->setCellValue("A" . $contar, $valueTotal['ValorCalif']);
                    $sheet->getStyle("A" . $contar)->getFont()->setBold(true);

                    $sheet->setCellValue("B" . $contar, $valueTotal['Total']);
                    $contartotal += $valueTotal['Total'];

                    $sheet->getStyle("A" . $contar . ":B" . $contar)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A" . $contar . ":B" . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));

                    $contar = $contar + 1;
                }
                $sheet->setCellValue("B" . $contar, $contartotal);
                $sheet->getStyle("B" . $contar)->getFont()->setBold(true);
                $sheet->getStyle("B" . $contar)->getAlignment()->setHorizontal('center');
                $sheet->getStyle("B" . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
                $contartotal = 0;
                $contar = $contar + 2;

                //colorea todo el reporte

                $ContarNeutro = 0;
            }
        }
    }
}
$contar = $contar + 3;


$ContarNeutro = 0;

// echo $consultainicial;
// $valores = hacerConsulta($consultainicial);
$SQLIdCiudad = hacerConsulta($consultainicial . " GROUP BY ciudad.IdCiudad");
foreach ($SQLIdCiudad as $valueCiudad) {
    $sheet->setCellValue("A" . ($contar - 1), "CIUDAD");
    $sheet->mergeCells('A' . ($contar - 1) . ':E' . ($contar - 1));
    $sheet->getStyle('A' . ($contar - 1))->getAlignment()->setHorizontal('center');
    $sheet->setCellValue("A" . $contar, $valueCiudad['NombreCiudad']);
    $sheet->getStyle("A" . $contar)->getFont()->setBold(true);
    $sheet->getStyle("A" . $contar)->getFont()->setSize(13);
    $sheet->getRowDimension($contar)->setRowHeight(20);
    $sheet->mergeCells('A' . $contar . ':E' . $contar);
    $sheet->getStyle('A' . $contar)->getAlignment()->setHorizontal('center');

    $contar = $contar + 3;
    // print_r("Ciudad ".$valueCiudad['IdCiudad']);

    $SQLIdSede = hacerConsulta($consultainicial . " GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =" . $valueCiudad['IdCiudad']);
    foreach ($SQLIdSede as $valueSede) {
        $sheet->setCellValue("A" . ($contar - 1), "SEDE");
        $sheet->mergeCells('A' . ($contar - 1) . ':E' . ($contar - 1));
        $sheet->getStyle('A' . ($contar - 1))->getAlignment()->setHorizontal('center');
        $sheet->setCellValue("A" . $contar, $valueSede['NombreSede']);
        $sheet->getStyle("A" . $contar)->getFont()->setBold(true);
        $sheet->getStyle("A" . $contar)->getFont()->setSize(12);
        $sheet->getRowDimension($contar)->setRowHeight(20);
        $sheet->mergeCells('A' . $contar . ':E' . $contar);
        $sheet->getStyle('A' . $contar)->getAlignment()->setHorizontal('center');

        $contar = $contar + 2;
        // $ContarNeutro = $contar;
        $sheet->setAutoFilter("A" . $contar . ":D" . $contar);
        $sheet->setCellValue("A" . $contar, "NOMBRE USUARIO");
        $sheet->setCellValue("B" . $contar, "PREGUNTA");
        $sheet->setCellValue("C" . $contar, "CALIFICACIÓN");
        $sheet->setCellValue("D" . $contar, "FECHA DE CALIFICACIÓN");
        $sheet->getStyle("A" . $contar . ":D" . $contar)->getFont()->setBold(true);
        $sheet->getStyle("A" . $contar . ":D" . $contar)->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A" . $contar . ":D" . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
        $contar = $contar + 1;
        // print_r("Sede ".$valueSede['IdSede']);

        $SQLIdUsuario = hacerConsulta($consultainicial);

        foreach ($SQLIdUsuario as $valueUsuario) {

            $sheet->setCellValue("A" . $contar, $valueUsuario['NombreCompleto']);
            $sheet->setCellValue("B" . $contar, $valueUsuario['Pregunta']);
            $sheet->setCellValue("C" . $contar, $valueUsuario['ValorCalif']);
            $sheet->setCellValue("D" . $contar, $valueUsuario['FechaCalif']);

            $sheet->getStyle("A" . $contar . ":D" . $contar)->getAlignment()->setHorizontal('center');
            $sheet->getStyle("A" . $contar . ":D" . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));

            $contar = $contar + 1;
        }
        $contar = $contar + 2;
        // $sheet->setAutoFilter("A".$ContarNeutro.":D".$ContarNeutro);
        $sheet->getStyle('A1:E' . $contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorTitulo);
    }
}


////stilo columnas
foreach (range('A', 'Z') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}
// $files = glob('reportes/*'); //obtenemos todos los nombres de los ficheros
// foreach ($files as $file) {
//     if (is_file($file))
//         unlink($file); //elimino el fichero
// }
// Add the chart to the worksheet
$writer = new Xlsx($spreadsheet);
$writer->save('reportes/ReporteGeneralDetalle.xlsx');

$validar = array('respuesta' => 'Enviado Correctamente');
echo json_encode($validar, JSON_FORCE_OBJECT);
?>