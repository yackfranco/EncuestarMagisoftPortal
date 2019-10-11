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

require 'vendor/phpoffice/phpspreadsheet/samples/Header.php';


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();



    $fechaactual = DevolverUnDato("SELECT NOW()");

    $DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa");

    $fechainical = $_REQUEST["gfechainicial"];
    $fechafinal = $_REQUEST["gfechafinal"];
    $idciudad = $_REQUEST["gidciudad"];
    $IdUsuario = $_REQUEST["gIdUsuario"];
    $IdSede = $_REQUEST["gIdSede"];

    $colorTitulo = "ffffff";
    $colorUsuario = "FF5733";
    
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
    
    ///INGRESAR UNA IMAGEN AL EXCEL
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('LOGO');
    $drawing->setDescription('camara de comercio');
    $drawing->setPath('img/LOGO.jpg');
    $drawing->setCoordinates('A1');
    $drawing->setWidthAndHeight(190, 140);
    $drawing->setResizeProportional(true);
    $drawing->setWorksheet($spreadsheet->getActiveSheet());

    //TITULO, UNIR CELDAS,CENTRADO
    $sheet->setCellValue("A5", 'REPORTE DE CALIFICACIÓN');
    $sheet->getStyle("A5")->getFont()->setBold(true);   
    $sheet->getStyle("A5")->getFont()->setSize(15);  
    $sheet->getRowDimension('5')->setRowHeight(20);
    $sheet->mergeCells('A5:I5');
    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
    //$sheet->getStyle('A5:I10')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
    

    $sheet->setCellValue("A7", $DatosEmpresa[0]['nit']);
    $sheet->mergeCells('A7:I7');
    $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A8", $DatosEmpresa[0]['Nombre']);
    $sheet->getStyle("A8")->getFont()->setBold(true);
    $sheet->getStyle("A8")->getFont()->setSize(13);  
    $sheet->getRowDimension('8')->setRowHeight(20);
    $sheet->mergeCells('A8:I8');
    $sheet->getStyle('A8')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A9", $DatosEmpresa[0]['Slogan']);
    $sheet->mergeCells('A9:I9');
    $sheet->getStyle('A9')->getAlignment()->setHorizontal('center');
    //colorear
    //$sheet->getStyle('A1:I10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorTitulo);

    $contar = 12;

    $ContarNeutro = 0;
    
    // echo $consultainicial;
    $valores = hacerConsulta($consultainicial);
    $SQLIdCiudad = hacerConsulta($consultainicial." GROUP BY ciudad.IdCiudad");
    foreach ($SQLIdCiudad as $valueCiudad) {
        $sheet->setCellValue("A".$contar, $valueCiudad['NombreCiudad']);
        $sheet->getStyle("A".$contar)->getFont()->setBold(true);
        $sheet->getStyle("A".$contar)->getFont()->setSize(13);  
        $sheet->getRowDimension($contar)->setRowHeight(20);
        $sheet->mergeCells('A'.$contar.':I'.$contar);
        $sheet->getStyle('A'.$contar)->getAlignment()->setHorizontal('center');

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
                $sheet->getStyle("C".$contar,)->getFont()->setBold(true);
                $sheet->getStyle("C".$contar)->getFont()->setSize(12);  
                $sheet->getRowDimension($contar)->setRowHeight(20);
                $sheet->mergeCells('C'.$contar.':G'.$contar);
                $sheet->getStyle('C'.$contar)->getAlignment()->setHorizontal('center');
                
                //colorear usuario
                //$sheet->getStyle('A'.$contar.':I'.$contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorUsuario);
                $contar = $contar + 2;

                // print_r("Sede ".$valueSede['IdSede']);
                $SQLIdPregunta = hacerConsulta($consultainicial." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario']." AND IdSede =".$valueSede['IdSede']);
                //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                foreach ($SQLIdPregunta as $valuePregunta) {

                    $sheet->setCellValue("A".$contar, $valuePregunta['Pregunta']);
                    $sheet->mergeCells('A'.$contar.':I'.$contar);
                    $sheet->getStyle('A'.$contar)->getAlignment()->setHorizontal('center');
                    
                   

                    $contar = $contar + 3;

                    $SQLTotal = hacerConsulta($consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta']);
                    echo $consultainicial2." GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =".$valueSede['IdSede']." AND IdUsuario =".$valueUsuario['IdUsuario']." AND IdPregunta =".$valuePregunta['IdPregunta'];
                    //echo $consultainicial."GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =".$valueUsuario['IdUsuario'];
                    foreach ($SQLTotal as $valueTotal) {

                        
                        $sheet->setCellValue("A".$contar, $valueTotal['ValorCalif']);
                        $sheet->getStyle("A".$contar,)->getFont()->setBold(true);                        
                        $sheet->setCellValue("B".$contar, $valueTotal['Total']);
                        $sheet->getStyle("A".$contar.":B".$contar)->getAlignment()->setHorizontal('center');
                        $sheet->getStyle("A".$contar.":B".$contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
                        


                            if($ContarNeutro == 0)
                            {
                                $ContarNeutro=$contar;
                                $contadorVueltas = 0;
                            }
                        /************************************************ */
                        ///GRAFICASASAS
                        // $sheet->fromArray(
                        //    $SQLTotal;
                        // );
                        // $sheet->fromArray(
                        //     [
                        //             ['', 2010, 2011, 2012],
                        //             ['Q1', 12, 15, 21],
                        //             ['Q2', 56, 73, 86],
                        //             ['Q3', 52, 61, 69],
                        //             ['Q4', 30, 32, 0],
                        //         ]
                        // );
                        //COLORES DE LOS CAMPOS
                        $colors = [
                            'FF5733', '00abb8', 'b8292f', 'eb8500',
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
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$'.$ContarNeutro.':$A$'.$contar, null, 4), // Q1 to Q4 'Worksheet!$A$2:$A$5'
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
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$'.$ContarNeutro.':$B$'.$contar, null, 4, [], null, $colors),
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
                        
                        $title1 = new Title($valuePregunta['Pregunta']);
                        
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
                        $chart1->setTopLeftPosition('D'.$ContarNeutro);
                        // echo 'L'.$ContarNeutro;
                        // exit;
                        $contadorVueltas = $contadorVueltas+1;
                        $algo12 = ($ContarNeutro+5+$contadorVueltas);
                        $chart1->setBottomRightPosition('H'.$algo12);
                        $contar = $contar + 1;
                    }
                    $contar = $contar + 7;
                    $sheet->addChart($chart1);

                    //colorea todo el reporte
                    $sheet->getStyle('A1:I'.$contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorTitulo);
                    $ContarNeutro = 0;
                }
                
            }           
        }
    }

    ////stilo columnas
    $sheet->getColumnDimension('A')->setAutoSize(true);

    // Add the chart to the worksheet
    
    $filename = $helper->getFilename(__FILE__);
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->setIncludeCharts(true);
    $callStartTime = microtime(true);
    $writer->save($filename);
    $helper->logWrite($writer, $filename, $callStartTime);

   
    


    /*
    $writer->setIncludeCharts(true);
    $callStartTime = microtime(true);
   $writer = new Xlsx($spreadsheet);
   $writer->save('reportes/Reporte.xlsx'); */

   


echo json_encode($respuesta, JSON_FORCE_OBJECT);
?>