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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$colorTitulo = "AFCEEB";
$colorIntercalado = "DDEBF7";
//$sheet->setCellValue('A1', 'Hello World !');
//fechas
$fechaInicial = $_REQUEST["fechaInicial"];
$fechafinal = $_REQUEST["fechafinal"];
$idServicio = $_REQUEST["idservicio"];

$fechaInicial = date("Y-m-d", strtotime($fechaInicial));
$fechafinal = date("Y-m-d", strtotime($fechafinal));


//DATOS EMPRESA
$info = DevolverUnArreglo("select * from auditoria");
$datosEmpresa = DevolverUnArreglo("select * from datosempresa");
$nombreempresa = $datosEmpresa[0]['NombreEmpresa'];
$NIT = $datosEmpresa[0]['nit'];

$sheet->setCellValue("A1", $nombreempresa);
$sheet->setCellValue("A2", $NIT);

//unirCeldas
$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:F2');
//centrar celdas
$sheet->getStyle('A1:F2')->getAlignment()->setHorizontal('center');
//borde de celdas
$sheet->getStyle('A1:F2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
//Color de celda background
$sheet->getStyle('A1:F2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorTitulo);



//FECHAS DE FILTRO
$sheet->getStyle('A3:B3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
$sheet->getStyle('A4:B4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
$sheet->setCellValue("A3", "FECHA INICIAL");
$sheet->setCellValue("B3", $fechaInicial);

$sheet->setCellValue("A4", "FECHA FINAL");
$sheet->setCellValue("B4", $fechafinal);

//REPORTE DE TURNOS
$TurnosCount = DevolverUnDato("select count(*) from auditoria where Estado != 'NORMAL' and auditoria.FechaLlegada  and auditoria.IdServicio = $idServicio and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$TurnosAtendidosCount = DevolverUnDato("select count(*) from auditoria where Estado = 'TERMINADO' and Observacion = '' and  auditoria.IdServicio = $idServicio and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$TurnosAusentesCount = DevolverUnDato("select count(*) from auditoria where Estado = 'AUSENTE' and auditoria.IdServicio = $idServicio and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$TurnosTransferidosCount = DevolverUnDato("select count(*) from auditoria where Estado = 'TERMINADO' and Observacion = 'TRANSFERIDO' and auditoria.IdServicio = $idServicio and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");

$sheet->mergeCells('A6:B6');
$sheet->setCellValue("A6", "INFORMACION DE TURNOS");
$sheet->setCellValue("A7", "TURNOS TOTALES");
$sheet->setCellValue("B7", $TurnosCount);
$sheet->setCellValue("A8", "TURNOS ATENDIDOS");
$sheet->setCellValue("B8", $TurnosAtendidosCount);
$sheet->setCellValue("A9", "TURNOS AUSENTES");
$sheet->setCellValue("B9", $TurnosAusentesCount);
$sheet->setCellValue("A10", "TURNOS TRANSFERIDOS");
$sheet->setCellValue("B10", $TurnosTransferidosCount);
$sheet->getStyle('A6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorTitulo);
$sheet->getStyle('A7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
$sheet->getStyle('B7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
$sheet->getStyle('A9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
$sheet->getStyle('B9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
$sheet->getStyle('A6:B6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
$sheet->getStyle('A6:B6')->getAlignment()->setHorizontal('center');
$sheet->getStyle('B6:B10')->getAlignment()->setHorizontal('center');

$contar = 14;

//REPORTE DE LOS SERVICIOS
$sheet->mergeCells('A12:C12');
$sheet->setCellValue("A12", "INFORMACION DE LOS SERVICIOS");
$sheet->setCellValue("A13", "SERVICIO");
$sheet->setCellValue("B13", "TOTAL");
$sheet->setCellValue("C13", "PORCENTAJE");
$sheet->getStyle('A13:C13')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
$sheet->getStyle('A13:C13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
//..........
$servicios = DevolverUnArreglo("select servicio.Servicio, COUNT(auditoria.IdServicio) as Cantidad from auditoria JOIN servicio on (auditoria.IdServicio = servicio.IdServicio) where (Estado != 'NORMAL') and auditoria.IdServicio = $idServicio and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59' GROUP by auditoria.IdServicio");
$ServiciosCount = $TurnosCount;
//$ServiciosCount = DevolverUnDato("select count(*) from auditoria where (Estado = 'TERMINADO' or Estado = 'AUSENTE') and Observacion != 'TRANSFERIDO' and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$totalPorcentaje = 0;
foreach ($servicios as &$valor) {
//    print_r($valor);
    $porcentaje = 0;
//
    $sheet->setCellValue("A" . $contar, $valor['Servicio']);
    $sheet->setCellValue("B" . $contar, $valor['Cantidad']);
    $porcentaje = ($valor['Cantidad'] / $ServiciosCount) * 100;
//    echo round($porcentaje, 2);
    $totalPorcentaje = $totalPorcentaje + $porcentaje;
//
    $sheet->setCellValue("C" . $contar, round($porcentaje, 2) . "%");
    $sheet->getStyle('A1:F2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
    if ($contar % 2 == 1) {
        $sheet->getStyle('A' . $contar . ':C' . $contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
    }
    $contar = $contar + 1;
}

$sheet->mergeCells('A' . $contar . ':B' . $contar);
$sheet->setCellValue("A" . $contar, "TOTAL");
$sheet->setCellValue("C" . $contar, $totalPorcentaje . "%");

$sheet->getStyle('A1:F2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
if ($contar % 2 == 1) {
    $sheet->getStyle('A' . $contar . ':C' . $contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
}
$sheet->getStyle('A1:F2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));

$contar = $contar + 2;
$sheet->getStyle('A12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorTitulo);
$sheet->getStyle('A12:C12')->getAlignment()->setHorizontal('center');


//tiempo de atencion promedio
$CeldaTiempoPromedio = "B" . $contar;
$sheet->setCellValue("A" . $contar, "TIEMPO DE ATENCION PROMEDIO");

$sheet->getStyle("A" . $contar . ":B" . $contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
$sheet->getStyle("A" . $contar . ":B" . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
$sheet->getStyle("A" . $contar . ":B" . $contar)->getAlignment()->setHorizontal('center');

$contar = $contar + 2;

//DETALLE
$sheet->setCellValue("A" . $contar, "ID");
$sheet->setCellValue("B" . $contar, "SERVICIO");
$sheet->setCellValue("C" . $contar, "ASESOR");
$sheet->setCellValue("D" . $contar, "TURNO");
$sheet->setCellValue("E" . $contar, "ESTADO");
$sheet->setCellValue("F" . $contar, "TIEMPO DE ESPERA EN SALA [hh:mm:ss]");
$sheet->setCellValue("G" . $contar, "TIEMPO DE ATENCION [hh:mm:ss");
$sheet->setCellValue("H" . $contar, "TIEMPO TOTAL [hh:mm:ss");
$sheet->setCellValue("I" . $contar, "HORA Y FECHA DE SOLICITUD DE TURNO");
$sheet->setCellValue("J" . $contar, "HORA Y FECHA DE LLAMADO DE TURNO");
$sheet->setCellValue("K" . $contar, "HORA Y FECHA DE TERMINACION DE TURNO");
$sheet->setCellValue("L" . $contar, "NUMERO DE LLAMADOS");
$sheet->setCellValue("M" . $contar, "OBSERVACION");
$sheet->setAutoFilter('A' . $contar . ':M' . $contar);

$sheet->getStyle('A' . $contar . ':M' . $contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorTitulo);
$sheet->getStyle('A' . $contar . ':M' . $contar)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('00000000'));
$sheet->getStyle('A' . $contar . ':M' . $contar)->getAlignment()->setHorizontal('center');
$contar = $contar + 1;


$detalle = DevolverUnArreglo("select auditoria.* , servicio.Servicio,usuario.NombreCompleto from auditoria,servicio,usuario where (auditoria.IdServicio = servicio.IdServicio) and (auditoria.IdUsuario = usuario.IdUsuario) and (auditoria.Estado = 'TERMINADO' or auditoria.Estado = 'AUSENTE') and auditoria.IdServicio = $idServicio and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$tiempoPromedioEspera = 0;
foreach ($detalle as &$valor) {
    if ($contar % 2 == 1) {
        $sheet->getStyle('A' . $contar . ':M' . $contar)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($colorIntercalado);
    }
    $sheet->setCellValue("A" . $contar, $valor['IdAuditoria']);
    $sheet->setCellValue("B" . $contar, $valor['Servicio']);
    $sheet->setCellValue("C" . $contar, $valor['NombreCompleto']);
    $sheet->setCellValue("D" . $contar, $valor['Turno']);
    if ($valor['Observacion'] == "TRANSFERIDO") {
        $sheet->setCellValue("E" . $contar, "TRANSFERIDO");
    } else {
        $sheet->setCellValue("E" . $contar, $valor['Estado']);
    }
    $TiempoEspera = CalcularMinutos(new DateTime($valor['FechaLlegada']), new DateTime($valor['FechaLlamado']));
    $TiempoAtencion = CalcularMinutos(new DateTime($valor['FechaLlamado']), new DateTime($valor['Fechasalio']));
    $tiempoTotal = $TiempoEspera + $TiempoAtencion;
    if ($valor['Observacion'] != "AUSENTE") {
        $tiempoPromedioEspera = $tiempoPromedioEspera + $tiempoTotal;
    }
    $sheet->setCellValue("F" . $contar, conversorSegundosHoras($TiempoEspera));
    $sheet->setCellValue("G" . $contar, conversorSegundosHoras($TiempoAtencion));
    $sheet->setCellValue("H" . $contar, conversorSegundosHoras($tiempoTotal));
    $sheet->setCellValue("I" . $contar, $valor['FechaLlegada']);
    $sheet->setCellValue("J" . $contar, $valor['FechaLlamado']);
    $sheet->setCellValue("K" . $contar, $valor['Fechasalio']);
    $sheet->setCellValue("L" . $contar, $valor['NumeroLlamados']);
    $sheet->setCellValue("M" . $contar, $valor['Observacion']);
    $contar = $contar + 1;
}


//TIEMPO PROMEDIO DE ESPERA
if (!empty($detalle)) {
  $sheet->setCellValue($CeldaTiempoPromedio, conversorSegundosHoras(round(($tiempoPromedioEspera) / $TurnosCount), 0));
}else
$sheet->setCellValue($CeldaTiempoPromedio,'0');
foreach (range('A', 'M') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
} 
//GUARDAR ARCHIVO

$files = glob('reportes/*'); //obtenemos todos los nombres de los ficheros
foreach ($files as $file) {
    if (is_file($file))
        unlink($file); //elimino el fichero
}
$writer = new Xlsx($spreadsheet);
$writer->save('reportes/Reporte.xlsx');

$validar = array('respuesta' => 'Enviado Correctamente');
echo json_encode($validar, JSON_FORCE_OBJECT);

function CalcularMinutos($fecha1, $fecha2) {
    $interval = $fecha1->diff($fecha2);
    $hours = $interval->format('%h');
    $minutes = $interval->format('%i');
    $segundos = $interval->format('%s');
    return ((($hours * 60) * 60) + ($minutes * 60) + $segundos);
}

function CalcularMinutos2($fecha1, $fecha2) {
    $interval = $fecha1->diff($fecha2);
    $hours = $interval->format('%h');
    $minutes = $interval->format('%i');
    return ($hours * 60) + $minutes;
}

function busca_edad($fecha_nacimiento) {
    $dia = date("d");
    $mes = date("m");
    $ano = date("Y");
    $dianaz = date("d", strtotime($fecha_nacimiento));
    $mesnaz = date("m", strtotime($fecha_nacimiento));
    $anonaz = date("Y", strtotime($fecha_nacimiento));
    if (($mesnaz == $mes) && ($dianaz > $dia)) {
        $ano = ($ano - 1);
    }
    if ($mesnaz > $mes) {
        $ano = ($ano - 1);
    }
    $edad = ($ano - $anonaz);
    return $edad;
}

function conversorSegundosHoras($tiempo_en_segundos) {
    $horas = floor($tiempo_en_segundos / 3600);
    $minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
    $segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);

    return $horas . ':' . $minutos . ":" . $segundos;
}

?>
