<?php

include 'conexion.php';

// $prueba = "<h3 style='margin:1px;'>$nombre</h3><h4 style='margin:1px;'>$slogan</h4>";

$fechainical = $_REQUEST["gfechainicial"];
$fechafinal = $_REQUEST["gfechafinal"];
$idciudad = $_REQUEST["gidciudad"];
$IdUsuario = $_REQUEST["gIdUsuario"];
$IdSede = $_REQUEST["gIdSede"];
$Pregunta = $_REQUEST["gPregunta"];
$IdEmpresa = $_REQUEST["IdEmpresa"];

$DatosEmpresa = DevolverUnArreglo("SELECT * from datosempresa where IdEmpresa = $IdEmpresa");
$nit = $DatosEmpresa[0]['nit'];
$nombre = $DatosEmpresa[0]['Nombre'];
$slogan = $DatosEmpresa[0]['Slogan'];

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


$ConcatciudadSedes = "";
$concatdatos = "";
$contartotal = 0;

$SQLIdCiudad = hacerConsulta($consultainicial . " GROUP BY ciudad.IdCiudad");
foreach ($SQLIdCiudad as $valueCiudad) {
    $varciudad = $valueCiudad['NombreCiudad'];
    $ciudades = "<div style='
    display: flex;
    flex-direction: column;
    justify-content: center;
    justify-items: center;
    align-items: center;
    width: 80%;
'>
    <h3 style='margin-bottom: 1px'>Ciudad: $varciudad</h3>
   
</div>";
    $ConcatciudadSedes = $ConcatciudadSedes . $ciudades;

    $SQLIdSede = hacerConsulta($consultainicial . " GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =" . $valueCiudad['IdCiudad']);
    foreach ($SQLIdSede as $valueSede) {
        $varsedes = $valueSede['NombreSede'];

        $sedes = "<div style='
        display: flex;
        flex-direction: column;
        justify-content: center;
        justify-items: center;
        align-items: center;
        width: 80%;
    '>
        <h3>Sede: $varsedes</h3>
    </div>";

        $ConcatciudadSedes = $ConcatciudadSedes . $sedes;

        $SQLIdUsuario = hacerConsulta($consultainicial . " GROUP BY usuario.IdUsuario, sede.IdSede HAVING IdSede =" . $valueSede['IdSede']);
        foreach ($SQLIdUsuario as $valueUsuario) {

            $varusu = $valueUsuario['NombreCompleto'];

            $usuario = "<div style='
            display: flex;
            flex-direction: column;
            justify-content: center;
            justify-items: center;
            align-items: center;
            width: 80%;
        '>
            <h3>Usuario: $varusu</h3>
        </div>";

            $ConcatciudadSedes = $ConcatciudadSedes . $usuario;

            $SQLIdPregunta = hacerConsulta($consultainicial . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, sede.IdSede HAVING IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdSede =" . $valueSede['IdSede']);
            foreach ($SQLIdPregunta as $valuePregunta) {

                $varpre = $valuePregunta['Pregunta'];

                $pregunta = "<div style='
              display: flex;
              flex-direction: column;
              justify-content: center;
              justify-items: center;
              align-items: center;
              width: 80%;
          '>
              <h3>$varpre</h3>
          </div>";

                $ConcatciudadSedes = $ConcatciudadSedes . $pregunta;


                $encabezadotabla = " <table>
          <thead style='background: rgba(199, 187, 174, 0.3); border: 1px solid rgba(201, 198, 195, 0.3);'>
              <tr>    
                  <th style='text-align: center; padding: 5px'>CALIFICACIÓN</th>
                  <th style='text-align: center; padding: 5px'>TOTAL</th>
              </tr>
          </thead>
          <tbody >
              <tr>";

                $ConcatciudadSedes = $ConcatciudadSedes . $encabezadotabla;


                $SQLTotal = hacerConsulta($consultainicial2 . " GROUP BY pregunta.IdPregunta, usuario.IdUsuario, calificacion.NumeroCalif, ciudad.IdCiudad HAVING IdSede =" . $valueSede['IdSede'] . " AND IdUsuario =" . $valueUsuario['IdUsuario'] . " AND IdPregunta =" . $valuePregunta['IdPregunta']);
                foreach ($SQLTotal as $valueTotal) {
                    $Valorestotalcalif = array($valueTotal['ValorCalif'], $valueTotal['Total']);
                    $calificaciontabla = $valueTotal['ValorCalif'];
                    $totaltabla = $valueTotal['Total'];
                    $contartotal += $totaltabla;

                    $datoscompletos = "<td style='text-align: center'>$calificaciontabla</td>
                    <td style='text-align: center'>$totaltabla</td>
                    </tr>
                    ";
                    $concatdatos = $concatdatos . $datoscompletos;
                }
                $contarconcat = "<td style='text-align: center'></td>
                <td style='text-align: center'>$contartotal</td>
                </tr>
                ";
                $concatdatos = $concatdatos . $contarconcat;
                $ConcatciudadSedes = $ConcatciudadSedes . $concatdatos . "</tbody> </table>";
                $concatdatos = "";
                $contartotal = 0;
            }
        }
    }
}








/*
  $ConcatciudadSedes = "";
  $concatdatos = "";
  $SQLIdCiudad = hacerConsulta($consultainicial." GROUP BY ciudad.IdCiudad");
  foreach ($SQLIdCiudad as $valueCiudad) {
  $varciudad = $valueCiudad['NombreCiudad'];
  $ciudades = "<div style='
  display: flex;
  flex-direction: column;
  justify-content: center;
  justify-items: center;
  align-items: center;
  width: 80%;
  '>
  <h3 style='margin-bottom: 1px'>Ciudad: $varciudad</h3>

  </div>";
  $ConcatciudadSedes = $ConcatciudadSedes.$ciudades;
  //   $ciudades = array('CIUDAD : '.$valueCiudad['NombreCiudad']);
  //   fputcsv($fp, $ciudades);

  $SQLIdSede = hacerConsulta($consultainicial." GROUP BY sede.IdSede, ciudad.IdCiudad HAVING IdCiudad =".$valueCiudad['IdCiudad']);
  foreach ($SQLIdSede as $valueSede) {

  $varsedes = $valueSede['NombreSede'];

  $sedes = "<div style='
  display: flex;
  flex-direction: column;
  justify-content: center;
  justify-items: center;
  align-items: center;
  width: 80%;
  '>
  <h3>Sede: $varsedes</h3>
  </div>";

  $ConcatciudadSedes = $ConcatciudadSedes.$sedes;
  /////////////
  $encabezadotabla = " <table>
  <thead style='background: rgba(199, 187, 174, 0.3); border: 1px solid rgba(201, 198, 195, 0.3);'>
  <tr>
  <th style='text-align: center; padding: 5px'>NOMBRE</th>
  <th style='text-align: center; padding: 5px'>PRGUNTA</th>
  <th style='text-align: center; padding: 5px'>CALIFICACIÓN</th>
  <th style='text-align: center; padding: 5px'>FECHA DE CALIFICACIÓN</th>
  </tr>
  </thead>
  <tbody >
  <tr>";

  $ConcatciudadSedes = $ConcatciudadSedes.$encabezadotabla;

  $SQLIdUsuario = hacerConsulta($consultainicial." AND sede.IdSede =".$valueSede['IdSede']);
  foreach ($SQLIdUsuario as $valueUsuario) {
  $varnombre = $valueUsuario['NombreCompleto'];
  $varpregunta = $valueUsuario['Pregunta'];
  $varvalor = $valueUsuario['ValorCalif'];
  $varFecha = $valueUsuario['FechaCalif'];

  $datoscompletos = "   <td style='text-align: center'>$varnombre</td>
  <td style='text-align: center'>$varpregunta</td>
  <td style='text-align: center'>$varvalor</td>
  <td style='text-align: center'>$varFecha</td>
  </tr>
  ";
  $concatdatos = $concatdatos.$datoscompletos;

  }
  $ConcatciudadSedes = $ConcatciudadSedes.$concatdatos."</tbody> </table>";

  }

  }
 */
$fichero_salida = "reporteshtml/Reporte.html";
$tabla = "<!DOCTYPE html>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <title>Reporte Calificaciones</title>
</head>
<body style='
        display: flex;
        justify-content: center;
        justify-items: center;
        height: 100%;
    '>

    <div style='
        display: flex;
        flex-direction: column;
        justify-content: center;
        justify-items: center;
        align-items: center;
        width: 80%;
        
    '>

    <div style='
        display: flex;
        flex-direction: column;
        justify-content: center;
        justify-items: center;
        align-items: center;
        width: 80%;
        margin:0;
        
    '>
        <h2 style='margin:1px;'>REPORTE DE CALIFICACIÓN</h2>
        <h4 style='margin:1px;  margin-top: 15px'>$nit</h4>
        <h3 style='margin:1px;'>$nombre</h3>
        <h4 style='margin:1px;'>$slogan</h4>
       
    </div>
    $ConcatciudadSedes
    </div>
</body>
</html>";
$fp = fopen($fichero_salida, 'w');
fwrite($fp, $tabla);
fclose($fp);
$validar = array('respuesta' => 'Enviado Correctamente');
echo json_encode($validar, JSON_FORCE_OBJECT);
?>
