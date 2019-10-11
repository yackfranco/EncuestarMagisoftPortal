<?php

require __DIR__ . '/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

//recoger el contenido de otro fichero
ob_start();

$fechaInicial = $_REQUEST["fechaInicial"];
$fechafinal = $_REQUEST["fechafinal"];
$accion = $_REQUEST["accion"];

$fechaInicial = date("Y-m-d", strtotime($fechaInicial));
$fechafinal = date("Y-m-d", strtotime($fechafinal));

if ($accion == "general") {
    require_once 'reportePDFGeneral.php';
}
if ($accion == "SoloUsuario") {
    $idusuario = $_REQUEST["idusuario"];
    require_once 'reportePDFSoloUsuario.php';
}

if ($accion == "SoloServicio") {
    $idServicio = $_REQUEST["idServicio"];
    require_once 'reportePDFSoloServicio.php';
}
if ($accion == "ServicioYUsuario") {
    $idServicio = $_REQUEST["idServicio"];
    $idusuario = $_REQUEST["idusuario"];
    require_once 'reportePDFServicioYUsuario.php';
}
$html = ob_get_clean();

$html2pdf = new Html2Pdf('P', 'A4', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($html);

$html2pdf->output();
//$dompdf = new DOMPDF();
//$dompdf->load_html( file_get_contents( 'http://localhost/MagisoftV1/app/model/reportedomPDF.php' ) );
//$dompdf->render();
//$dompdf->stream("mi_archivo.pdf");
