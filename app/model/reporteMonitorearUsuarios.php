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

//require __DIR__ . 'vendor/phpoffice/phpspreadsheet/samples/Header.php';


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();



$fechaactual = DevolverUnDato("SELECT NOW()");

$DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa");


$colorTitulo = "C8C8C8";
$colorFilas = "F2F2F2";
//$colorRojo = "E59A9A";
$colorRojo = "D53737";
//$colorVerde = "B8EABA";
$colorVerde = "45A236";

$sheet->mergeCells('A1:E1');
$fechaActual = DevolverUnDato("select now()");
$sheet->setCellValue("A1", "Reporte generado: ".$fechaActual );


//TITULO, UNIR CELDAS,CENTRADO
$sheet->mergeCells('A3:E3');
$sheet->setCellValue("A3", 'MONITOREO DE USUARIOS');
$sheet->getStyle("A3")->getFont()->setBold(true);
$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

//$sheet->getStyle($colorFilas)
$sheet->setCellValue("A4", 'CIUDAD');
$sheet->setCellValue("B4", 'SEDE');
$sheet->setCellValue("C4", 'USUARIO');
$sheet->setCellValue("D4", 'FECHA');
$sheet->setCellValue("E4", 'ESTADO');
$sheet->getStyle("A4:E4")->getFont()->setBold(true);
$sheet->getStyle('A4:E4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorTitulo);

$Datos = DevolverUnArreglo("SELECT registrousuario.fecharegistro,ciudad.NombreCiudad,sede.NombreSede, usuario.NombreUsuario,(SELECT if (registrousuario.fecharegistro> (SELECT NOW() - INTERVAL 2 MINUTE), 'CONECTADO', 'DESCONECTADO')) as estado FROM `registrousuario` join ciudad on (ciudad.IdCiudad = registrousuario.idciudad) join sede on (sede.IdSede = registrousuario.idsede) join usuario on (usuario.IdUsuario = registrousuario.idusuario) where usuario.estado != 'ELIMINADO' order by estado desc,fecharegistro desc");
$Datos = array_reverse($Datos);
$conteoFilas = 5;
foreach ($Datos as $value) {
    $sheet->setCellValue("A$conteoFilas", $value["NombreCiudad"]);
    $sheet->setCellValue("B$conteoFilas", $value["NombreSede"]);
    $sheet->setCellValue("C$conteoFilas", $value["NombreUsuario"]);
    $sheet->setCellValue("D$conteoFilas", $value["fecharegistro"]);
    $sheet->setCellValue("E$conteoFilas", $value["estado"]);
    if ($conteoFilas % 2 == 0) {
        $sheet->getStyle("A$conteoFilas:E$conteoFilas")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorFilas);
    }
    if ($value["estado"] == "CONECTADO") {
        $sheet->getStyle("E$conteoFilas")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorVerde);
    } else {
        $sheet->getStyle("E$conteoFilas")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorRojo);
    }
    $conteoFilas = $conteoFilas + 1;
}
$sheet->getStyle("A4:E" . ($conteoFilas - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save('reportes/Reporte.xlsx');


$validar = array('respuesta' => 'Enviado Correctamente');
echo json_encode($validar, JSON_FORCE_OBJECT);
?>