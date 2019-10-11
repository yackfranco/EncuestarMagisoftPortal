<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

$year = date("Y");
$month = date("n");
$day = date("j");

# Obtenemos el numero de la semana
$semana = date("W", mktime(0, 0, 0, $month, $day, $year));

# Obtenemos el día de la semana de la fecha dada
$diaSemana = date("w", mktime(0, 0, 0, $month, $day, $year));

# el 0 equivale al domingo...
if ($diaSemana == 0)
    $diaSemana = 7;

# A la fecha recibida, le restamos el dia de la semana y obtendremos el lunes
$primerDia = date("Y-m-d", mktime(0, 0, 0, $month, $day - $diaSemana + 1, $year));

# A la fecha recibida, le sumamos el dia de la semana menos siete y obtendremos el domingo
$ultimoDia = date("Y-m-d", mktime(0, 0, 0, $month, $day + (7 - $diaSemana), $year));

$accion = $_REQUEST["accion"];

//$fecha = array('primerDia' => $primerDia, 'ultimoDia' => $ultimoDia);

if ($accion == "totalcalificaciones") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $validar = DevolverUnDato("SELECT COUNT(*) FROM calificacion WHERE IdEmpresa = $IdEmpresa AND FechaCalif >= '$primerDia 00:00:00' and FechaCalif <= '$ultimoDia 23:59:59'");
}

//FUNCION PARA LLENAR LA TABLA DE CALIFICACIONES
if ($accion == "tablacalificaciones") {
    $validar = array();
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $preguntas = DevolverUnArreglo("SELECT * from pregunta where Estado = 'ACTIVO' and IdPregunta IN (select IdPregunta from calificacion where IdEmpresa = $IdEmpresa and fechacalif BETWEEN '$primerDia 00:00:00' and '$ultimoDia 23:59:59')");
    $valorcalif = DevolverUnArreglo("SELECT * FROM valorcalif where IdEmpresa = $IdEmpresa");
    $conteovalor = DevolverUnDato("SELECT count(*) FROM valorcalif where IdEmpresa = $IdEmpresa");
    $arregloCompleto = array();

    $arregloCompleto = array();
    foreach ($preguntas as $value) {
        $arreglo = array();
        $conteo = 1;
        $consulta = "";
        $consulta = "select";
        foreach ($valorcalif as $valor) {
            $conteo += 1;
            if ($conteo <= $conteovalor) {
                $consulta = $consulta . "(select count(*) from calificacion where IdEmpresa = $IdEmpresa and NumeroCalif = " . $valor['NumeroCalif'] . " and IdPregunta = " . $value['IdPregunta'] . " and FechaCalif >= '$primerDia 00:00:00' and FechaCalif <= '$ultimoDia 23:59:59') as c" . $valor['NumeroCalif'] . ",";
            } else {
                $consulta = $consulta . "(select count(*) from calificacion where IdEmpresa = $IdEmpresa and NumeroCalif = " . $valor['NumeroCalif'] . " and IdPregunta = " . $value['IdPregunta'] . " and FechaCalif >= '$primerDia 00:00:00' and FechaCalif <= '$ultimoDia 23:59:59') as c" . $valor['NumeroCalif'] . "";
            }
        }
        $consulta = $consulta . " FROM calificacion WHERE IdEmpresa = $IdEmpresa and IdPregunta = " . $value['IdPregunta'];
        $respuesta = DevolverUnArreglo($consulta);

        $sumaarreglo = $respuesta[0];

        $a = array_sum($sumaarreglo);
        $sumatotalcalificaciones = $a;
        $a = str_split($a);

        array_push($validar, array("Pregunta" => $value['Pregunta'], "Calificaciones" => $sumaarreglo, "Total" => $a, 'totalponderado' => $sumatotalcalificaciones));

        $consulta = "";
    }
}

if ($accion == "valorcalificaciones") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $validar = DevolverUnArreglo("SELECT ValorCalif FROM valorcalif WHERE IdEmpresa = $IdEmpresa");
}

if ($accion == "fechacalifiacion") {
    $fecha = array('primerDia' => $primerDia, 'ultimoDia' => $ultimoDia);
    $validar = $fecha;
}

if ($accion == "AvisarLicencia") {

    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $inicio = DevolverUnDato("Select CURDATE()");
    $fin = DevolverUnDato("Select FechaLicencia from datosempresa where IdEmpresa = $IdEmpresa");

//    $inicio = "2014-01-01 00:00:00";
//    $fin = "2014-11-01 23:59:59";

    $datetime1 = new DateTime($inicio);
    $datetime2 = new DateTime($fin);

# obtenemos la diferencia entre las dos fechas
    $interval = $datetime2->diff($datetime1);

# obtenemos la diferencia en meses
    $intervalDias = $interval->format("%d");

# obtenemos la diferencia en meses
    $intervalMeses = $interval->format("%m");
# obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
    $intervalAnos = $interval->format("%y") * 12;

    $Meses = $intervalMeses + $intervalAnos;
//    echo "hay una diferencia de " . ($intervalMeses + $intervalAnos) . " meses";

    $TiempoLicencia = array('Meses' => $Meses, 'Dias' => $intervalDias);
    $validar = $TiempoLicencia;
}
//echo $totalc4;

echo json_encode($validar, JSON_UNESCAPED_UNICODE);
?>