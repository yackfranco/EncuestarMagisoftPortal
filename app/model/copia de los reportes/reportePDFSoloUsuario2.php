<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include 'conexion.php';


//datos Empresa
$datosEmpresa = DevolverUnArreglo("select * from datosempresa");
$nombreempresa = $datosEmpresa[0]['NombreEmpresa'];
$NIT = $datosEmpresa[0]['nit'];


//Reporte Turnos
//echo "select count(*) from auditoria where Estado != 'NORMAL' and auditoria.FechaLlegada  and IdUsuario = $idusuario >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'";
//exit();
$TurnosCount = DevolverUnDato("select count(*) from auditoria where Estado != 'NORMAL' and auditoria.FechaLlegada  and IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$TurnosAtendidosCount = DevolverUnDato("select count(*) from auditoria where Estado = 'TERMINADO' and Observacion = '' and IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$TurnosAusentesCount = DevolverUnDato("select count(*) from auditoria where Estado = 'AUSENTE' and IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$TurnosTransferidosCount = DevolverUnDato("select count(*) from auditoria where Estado = 'TERMINADO' and Observacion = 'TRANSFERIDO' and IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");


//servicios
$servicios = DevolverUnArreglo("select servicio.Servicio, COUNT(auditoria.IdServicio) as Cantidad from auditoria JOIN servicio on (auditoria.IdServicio = servicio.IdServicio) where (Estado != 'NORMAL') and auditoria.IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59' GROUP by auditoria.IdServicio");
$ServiciosCount = $TurnosCount;
//$ServiciosCount = DevolverUnDato("select count(*) from auditoria where (Estado = 'TERMINADO' or Estado = 'AUSENTE') and Observacion != 'TRANSFERIDO' and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");
$totalPorcentaje = 0;


//detalle
//echo "select auditoria.* , servicio.Servicio,usuario.NombreCompleto from auditoria,servicio,usuario where (auditoria.IdServicio = servicio.IdServicio) and (auditoria.IdUsuario = usuario.IdUsuario) and (auditoria.Estado = 'TERMINADO' or auditoria.Estado = 'AUSENTE') and IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'";
//exit();
$detalle = DevolverUnArreglo("select auditoria.* , servicio.Servicio,usuario.NombreCompleto from auditoria,servicio,usuario where (auditoria.IdServicio = servicio.IdServicio) and (auditoria.IdUsuario = usuario.IdUsuario) and (auditoria.Estado = 'TERMINADO' or auditoria.Estado = 'AUSENTE') and auditoria.IdUsuario = $idusuario and auditoria.FechaLlegada >= '$fechaInicial 00:00:00' and auditoria.FechaLlegada<='$fechafinal 23:59:59'");

//datos del usuario o servicio 
$NombreUsuario1 = DevolverUnDato("select NombreCompleto from usuario where idusuario =$idusuario ");
//FUNCIONES PHP

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

<!--<!DOCTYPE HTML>-->
<html lang="es">
    <head>
        <title>Reporte</title>
        <link href="../../css/ReportePDF.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="cuerpo">
        <div class="infoEmpresaRe">
            <span class="nombreempresa"><?php echo $nombreempresa ?></span>
            <br>
            <span class="nombreempresa"><?php echo $NIT ?></span>
        </div>
        <DIV class="BORDESOTROS">
            <div style="margin-top: 15px;">
                <div>Reporte generado del Usuario: <?php echo $NombreUsuario1 ?></div>
            </div>
            <div class="divfechas">
                <div>Fecha Inicial: <?php echo $fechaInicial ?></div>
                <div>Fecha Final: <?php echo $fechafinal ?></div>
            </div>
            <div class="infoTurnos">
                <span class="tituloTablaturnos">INFORMACION DE TURNOS</span>
                <table class="table">

                    <tbody>
                        <tr class="pintarLineaIntercalada">
                            <td>TURNOS TOTALES</td>
                            <td class="reducirTamañoColumnaInfoTurnos"><?php echo $TurnosCount ?></td>
                        </tr>
                        <tr>
                            <td>TURNOS ATENDIDOS</td>
                            <td class="reducirTamañoColumnaInfoTurnos"><?php echo $TurnosAtendidosCount ?></td>
                        </tr>
                        <tr class="pintarLineaIntercalada">
                            <td>TURNOS AUSENTES</td>
                            <td class="reducirTamañoColumnaInfoTurnos"><?php echo $TurnosAusentesCount ?></td>
                        </tr>
                        <tr>
                            <td>TURNOS TRANSFERIDOS</td>
                            <td class="reducirTamañoColumnaInfoTurnos"><?php echo $TurnosTransferidosCount ?></td>
                        </tr>

                    </tbody>
                </table>

            </div>

            <div class="InfoServicios">
                <span>INFORMACION SERVICIOS</span>
                <table  border="1">

                     <tbody>
                        <tr class="pintarTitutlosTabla">
                            <td>SERVICIOS</td>
                            <td>TOTAL</td>
                            <td>PORCENTAJE</td>
                        </tr>

                        <?php
                        $contar = 1;
                        foreach ($servicios as &$valor) {
                            $porcentaje = 0;
                            if ($contar % 2 == 0) {
                                echo '<tr class="pintarLineaIntercalada">';
                            } else {
                                echo "<tr>";
                            }
                            echo "<td>" . $valor['servicio'] . "</td>";
                            echo "<td>" . $valor['cantidad'] . "</td>";
                            $porcentaje = ($valor['cantidad'] / $ServiciosCount) * 100;
                            $totalPorcentaje = $totalPorcentaje + $porcentaje;
                            echo "<td>" . round($porcentaje, 2) . "%</td>";
                            echo "</tr>";
                            $contar = $contar + 1;
                        }
                        ?>

                        <tr>
                            <td>TOTAL PORCENTAJE</td>
                            <td><?php echo $ServiciosCount ?></td>
                            <td><?php echo $totalPorcentaje ?>%</td>
                        </tr>
                    </tbody>
                </table>

            </div>


            <div class="detalle">
                <span>DETALLE</span>
                <table border="1">
                    <tbody>
                        <tr class="pintarTitutlosTabla">
                            <td>SERVICIO</td>
                            <td class='reducirTamañoColumna'>ASESOR</td>
                            <td>TURNO</td>
                            <td>ESTADO</td>
                            <td class='reducirTamañoColumna'>TIEMPO ESPERA</td>
                            <td class='reducirTamañoColumna'>TIEMPO ATENCION</td>
                            <td class='reducirTamañoColumna'>TIEMPO TOTAL</td>
    <!--                        <th></th>
                            <th></th>
                            <th></th>-->
                        </tr>
                        <tr>
                            <?php
                            $contar = 1;
                            foreach ($detalle as &$valor) {
                                if ($contar % 2 == 0) {
                                    echo '<tr class="pintarLineaIntercalada">';
                                } else {
                                    echo "<tr>";
                                }
                                echo "<td>" . $valor['servicio'] . "</td>";
                                echo "<td class='reducirTamañoColumna'>" . $valor['nombrecompleto'] . "</td>";
                                echo "<td>" . $valor['Turno'] . "</td>";
                                if ($valor['Observacion'] == "TRANSFERIDO") {
                                    echo "<td>TRANSFERIDO</td>";
                                } else {
                                    echo "<td>" . $valor['Estado'] . "</td>";
                                }

                                $TiempoEspera = CalcularMinutos(new DateTime($valor['FechaLlegada']), new DateTime($valor['FechaLlamado']));
                                $TiempoAtencion = CalcularMinutos(new DateTime($valor['FechaLlamado']), new DateTime($valor['Fechasalio']));
                                $tiempoTotal = $TiempoEspera + $TiempoAtencion;
//                            if ($valor['Observacion'] != "AUSENTE") {
//                                $tiempoPromedioEspera = $tiempoPromedioEspera + $tiempoTotal;
//                            }
                                echo "<td class='reducirTamañoColumna'>" . conversorSegundosHoras($TiempoEspera) . "</td>";
                                echo "<td class='reducirTamañoColumna'>" . conversorSegundosHoras($TiempoAtencion) . "</td>";
                                echo "<td class='reducirTamañoColumna'>" . conversorSegundosHoras($tiempoTotal) . "</td>";
                                echo "</tr>";
                                $contar = $contar + 1;
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>

            </div>
        </DIV>


    </body>
</html>
