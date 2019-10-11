<?php 

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

require __DIR__ . 'vendor/phpoffice/phpspreadsheet/samples/Header.php';


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();



    $fechaactual = DevolverUnDato("SELECT NOW()");

    $DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa");

    $fechainical = $_REQUEST["gfechainicial"];
    $fechafinal = $_REQUEST["gfechafinal"];
    $idciudad = $_REQUEST["gidciudad"];
    $IdUsuario = $_REQUEST["gIdUsuario"];
    $IdSede = $_REQUEST["gIdSede"];

    $colorTitulo = "C8C8C8";
    
//ultima consulta  GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede = 1 AND IdUsuario = 1 AND IdPregunta = 25";
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
    //echo $consultainicial;
    //TITULO, UNIR CELDAS,CENTRADO
    $sheet->setCellValue("A5", 'REPORTE DE CALIFICACIÓN');
    $sheet->mergeCells('A5:I5');
    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("C7", $DatosEmpresa[0]['nit']);
    $sheet->mergeCells('C7:G7');
    $sheet->getStyle('C7')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("C8", $DatosEmpresa[0]['Nombre']);
    $sheet->mergeCells('C8:G8');
    $sheet->getStyle('C8')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("C9", $DatosEmpresa[0]['Slogan']);
    $sheet->mergeCells('C9:G9');
    $sheet->getStyle('C9')->getAlignment()->setHorizontal('center');

    $contar = 12;
    
    // echo $consultainicial;
    $valores = hacerConsulta($consultainicial);
    $SQLIdCiudad = hacerConsulta($consultainicial." GROUP BY ciudad.IdCiudad");
    foreach ($SQLIdCiudad as $valueCiudad) {
        $sheet->setCellValue("C".$contar, $valueCiudad['NombreCiudad']);
        $sheet->mergeCells('C'.$contar.':G'.$contar);
        $sheet->getStyle('C'.$contar)->getAlignment()->setHorizontal('center');

        $contar = $contar + 2;
        // print_r("Ciudad ".$valueCiudad['IdCiudad']);

        $SQLIdSede = hacerConsulta($consultainicial." GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =".$valueCiudad['IdCiudad']);
        foreach ($SQLIdSede as $valueSede) {

            $sheet->setCellValue("A".$contar, $valueSede['NombreSede']);
            $sheet->mergeCells('A'.$contar.':C'.$contar);
            $sheet->getStyle('A'.$contar)->getAlignment()->setHorizontal('center');
        
            $contar = $contar + 2;

            // print_r("Sede ".$valueSede['IdSede']);
            $SQLIdUsuario = hacerConsulta($consultainicial." GROUP BY usuario.IdUsuario, sede.IdSede HAVING IdSede =".$valueSede['IdSede']);
            foreach ($SQLIdUsuario as $valueUsuario) {

                $sheet->setCellValue("C".$contar, $valueUsuario['NombreCompleto']);
                $sheet->mergeCells('C'.$contar.':G'.$contar);
                $sheet->getStyle('C'.$contar)->getAlignment()->setHorizontal('center');
        
                $contar = $contar + 2;

                // print_r("Sede ".$valueSede['IdSede']);
                $SQLIdPregunta = hacerConsulta($consultainicial." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario']." AND IdSede =".$valueSede['IdSede']);
                //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                foreach ($SQLIdPregunta as $valuePregunta) {

                    $sheet->setCellValue("A".$contar, $valuePregunta['Pregunta']);
                    $sheet->mergeCells('A'.$contar.':I'.$contar);
                    $sheet->getStyle('A'.$contar)->getAlignment()->setHorizontal('center');
        
                    $contar = $contar + 2;

                    $SQLTotal = hacerConsulta($consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta']);
                    echo $consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta'];
                    //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                    foreach ($SQLTotal as $valueTotal) {

                        $sheet->setCellValue("A".$contar, $valueTotal['ValorCalif']);
                        $sheet->setCellValue("B".$contar, $valueTotal['Total']);     
                        $sheet->getStyle('A'.$contar)->getAlignment()->setHorizontal('center');
                        
                        $contar = $contar + 1;
                        ///GRAFICASASAS
                        $worksheet->fromArray(
                            [
                                    ['', 2010, 2011, 2012],
                                    ['Q1', 12, 15, 21],
                                    ['Q2', 56, 73, 86],
                                    ['Q3', 52, 61, 69],
                                    ['Q4', 30, 32, 0],
                                ]
                        );
                        //COLORES DE LOS CAMPOS
                        $colors = [
                            'cccccc', '00abb8', 'b8292f', 'eb8500',
                        ];
                        $dataSeriesLabels1 = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // 2011
                        ];
                        // Set the X-Axis Labels
                        //     Datatype
                        //     Cell reference for data
                        //     Format Code
                        //     Number of datapoints in series
                        //     Data values
                        //     Data Marker
                        $xAxisTickValues1 = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
                        ];
                        // Set the Data values for each data series we want to plot
                        //     Datatype
                        //     Cell reference for data
                        //     Format Code
                        //     Number of datapoints in series
                        //     Data values
                        //     Data Marker
                        //     Custom colors
                        $dataSeriesValues1 = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4, [], null, $colors),
                        ];
                        
                        // Build the dataseries
                        $series1 = new DataSeries(
                            DataSeries::TYPE_PIECHART, // plotType
                            null, // plotGrouping (Pie charts don't have any grouping)
                            range(0, count($dataSeriesValues1) - 1), // plotOrder
                            $dataSeriesLabels1, // plotLabel
                            $xAxisTickValues1, // plotCategory
                            $dataSeriesValues1          // plotValues
                        );
                        
                        // Set up a layout object for the Pie chart
                        $layout1 = new Layout();
                        $layout1->setShowVal(true);
                        $layout1->setShowPercent(true);
                        
                        // Set the series in the plot area
                        $plotArea1 = new PlotArea($layout1, [$series1]);
                        // Set the chart legend
                        $legend1 = new Legend(Legend::POSITION_RIGHT, null, false);
                        
                        $title1 = new Title('Test Pie Chart');
                        
                        // Create the chart
                        $chart1 = new Chart(
                            'chart1', // name
                            $title1, // title
                            $legend1, // legend
                            $plotArea1, // plotArea
                            true, // plotVisibleOnly
                            0, // displayBlanksAs
                            null, // xAxisLabel
                            null   // yAxisLabel - Pie charts don't have a Y-Axis
                        );
                        
                        // Set the position where the chart should appear in the worksheet
                        $chart1->setTopLeftPosition('A7');
                        $chart1->setBottomRightPosition('H20');
                        
                        // Add the chart to the worksheet
                        $worksheet->addChart($chart1);
                    }
                    $contar = $contar + 1;
                }
            }
        }
    }

   $writer = new Xlsx($spreadsheet);
    $writer->save('reportes/Reporte.xlsx'); 




echo json_encode($respuesta, JSON_FORCE_OBJECT);
?>