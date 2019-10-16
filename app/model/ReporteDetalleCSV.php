<?php

ini_set("auto_detect_line_endings", true);
include 'conexion.php';

$fechainical = $_REQUEST["gfechainicial"];
$fechafinal = $_REQUEST["gfechafinal"];
$idciudad = $_REQUEST["gidciudad"];
$IdUsuario = $_REQUEST["gIdUsuario"];
$IdSede = $_REQUEST["gIdSede"];
$Pregunta = $_REQUEST["gPregunta"];
$IdEmpresa = $_REQUEST["IdEmpresa"];

$DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa where IdEmpresa = $IdEmpresa");

$consultainicial = "SELECT calificacion.*, usuario.NombreCompleto, sede.NombreSede, ciudad.NombreCiudad, pregunta.Pregunta, valorcalif.ValorCalif from calificacion, sede, ciudad, usuario, pregunta, (SELECT * from valorcalif WHERE valorcalif.IdEmpresa = $IdEmpresa) as valorcalif WHERE (valorcalif.NumeroCalif = calificacion.NumeroCalif) AND (usuario.IdUsuario = calificacion.IdUsuario) AND (calificacion.IdSede = sede.IdSede) AND (ciudad.IdCiudad = calificacion.IdCiudad) AND (pregunta.IdPregunta = calificacion.IdPregunta) AND calificacion.IdEmpresa = $IdEmpresa ";
$consultainicial2 = "SELECT calificacion.*, usuario.NombreCompleto, sede.NombreSede, ciudad.NombreCiudad, pregunta.Pregunta, valorcalif.ValorCalif,COUNT(*) as Total from calificacion, sede, ciudad, usuario, pregunta, (SELECT * from valorcalif WHERE valorcalif.IdEmpresa = $IdEmpresa) as valorcalif WHERE (valorcalif.NumeroCalif = calificacion.NumeroCalif) AND (usuario.IdUsuario = calificacion.IdUsuario) AND (calificacion.IdSede = sede.IdSede) AND (ciudad.IdCiudad = calificacion.IdCiudad) AND (pregunta.IdPregunta = calificacion.IdPregunta) AND calificacion.IdEmpresa = $IdEmpresa ";
if ($fechainical != null && $fechafinal != null) {
    $concatF1F2 = "  and (calificacion.FechaCalif >= '$fechainical 00:00:00' AND calificacion.FechaCalif <= '$fechafinal 23:59:59')";
    $consultainicial = $consultainicial . $concatF1F2;
    $consultainicial2 = $consultainicial2 . $concatF1F2;
}
if ($fechainical != null && $fechafinal == null) {
    $concatF1F2 = " and (calificacion.FechaCalif >= '$fechainical 00:00:00' AND calificacion.FechaCalif <= '$fechainical 23:59:59')";
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

$lista = array(
    array('REPORTE DE CALIFICACIÓN'),
    array($DatosEmpresa[0]['nit']),
    array($DatosEmpresa[0]['Nombre']),
    array($DatosEmpresa[0]['Slogan'])
);

$fp = fopen('reportescsv/fichero.csv', 'w');

foreach ($lista as $campos) {
    fputcsv($fp, $campos);
}
/* * ********************* */
$contartotal = 0;
$SQLIdCiudad = hacerConsulta($consultainicial . " GROUP BY ciudad.IdCiudad");
foreach ($SQLIdCiudad as $valueCiudad) {
    $Ciudad = array(
        array('CIUDAD'),
        array($valueCiudad['NombreCiudad'])
    );
    foreach ($Ciudad as $campos) {
        fputcsv($fp, $campos);
    }
    $SQLIdSede = hacerConsulta($consultainicial . " GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =" . $valueCiudad['IdCiudad']);
    foreach ($SQLIdSede as $valueSede) {

        $Sede = array(
            array('SEDE'),
            array($valueSede['NombreSede'])
        );

        foreach ($Sede as $campos) {
            fputcsv($fp, $campos);
        }

        $SQLIdUsuario = hacerConsulta($consultainicial . " GROUP BY usuario.IdUsuario, sede.IdSede HAVING IdSede =" . $valueSede['IdSede']);
        foreach ($SQLIdUsuario as $valueUsuario) {

            $Usuario = array($valueUsuario['NombreCompleto']);
            fputcsv($fp, $Usuario);
            $SQLIdPregunta = hacerConsulta($consultainicial . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdSede =" . $valueSede['IdSede']);
            foreach ($SQLIdPregunta as $valuePregunta) {
                $Preguntacsv = array($valuePregunta['Pregunta']);
                fputcsv($fp, $Preguntacsv);

                $Valorestotal = array('CALIFICACIÓN', 'TOTAL');
                fputcsv($fp, $Valorestotal);

                $SQLTotal = hacerConsulta($consultainicial2 . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =" . $valueSede['IdSede'] . " AND IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdPregunta =" . $valuePregunta['IdPregunta']);
                foreach ($SQLTotal as $valueTotal) {
                    $contartotal += $valueTotal['Total'];
                    $Valorestotalcalif = array($valueTotal['ValorCalif'], $valueTotal['Total']);
                    fputcsv($fp, $Valorestotalcalif);
                }
                $Valorestotalcontar = array('', $contartotal);
                fputcsv($fp, $Valorestotalcontar);
                $contartotal = 0;
            }
        }
    }
}

/* * *************** */

$SQLIdCiudad = hacerConsulta($consultainicial . " GROUP BY ciudad.IdCiudad");
foreach ($SQLIdCiudad as $valueCiudad) {
    $ciudades = array('CIUDAD : ' . $valueCiudad['NombreCiudad']);
    fputcsv($fp, $ciudades);

    $SQLIdSede = hacerConsulta($consultainicial . " GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =" . $valueCiudad['IdCiudad']);
    foreach ($SQLIdSede as $valueSede) {
        $sedes = array('SEDE: ' . $valueSede['NombreSede']);
        fputcsv($fp, $sedes);

        $encabezado = array('NOMBRE USUARIO', 'PREGUNTA', 'CALIFICACIÓN', 'FECHA DE CALIFICACIÓN');
        fputcsv($fp, $encabezado);

        $SQLIdUsuario = hacerConsulta($consultainicial . " AND sede.IdSede =" . $valueSede['IdSede']);
        foreach ($SQLIdUsuario as $valueUsuario) {
            $Datos = array($valueUsuario['NombreCompleto'], $valueUsuario['Pregunta'], $valueUsuario['ValorCalif'], $valueUsuario['FechaCalif']);
            fputcsv($fp, $Datos);
        }
    }
}
$validar = array('respuesta' => 'Enviado Correctamente');
echo json_encode($validar, JSON_FORCE_OBJECT);
fclose($fp);
?>
