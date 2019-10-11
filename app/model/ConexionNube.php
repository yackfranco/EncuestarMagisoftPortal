<?php

$usuario2 = "clientes";
$contrasena2 = "ingetronik";  // en mi caso tengo contraseña pero en casa caso introducidla aquí.
$servidor2 = "157.230.11.245";
$basededatos2 = "coomperqr";
$conexion2 = mysqli_connect($servidor2, $usuario2, $contrasena2) or die("No se ha podido conectar al servidor de Base de datos");
$db2 = mysqli_select_db($conexion2, $basededatos2)or die("Upps! Pues va a ser que no se ha podido conectar a la base de datos");
$conn2 = new mysqli($servidor2, $usuario2, $contrasena2, $basededatos2);
mysqli_set_charset($conexion2, 'utf8');

//function DevolverUnDato($query) {
//    global $conexion;
//    $resultado = mysqli_query($conexion, $query)or die("Algo ha ido mal en la consulta a la base de datos");
//    $arreglo = mysqli_fetch_array($resultado);
//    return $arreglo[0];
//}
include 'conexion.php';


function DevolverUnArreglo2($query) {
    global $conexion2;

    $resultado = mysqli_query($conexion2, $query)or die("Algo ha ido mal en la consulta a la base de datos");

//    array('IdCiudad' => '', 'NombreCiudad' => 'Seleccione una ciudad')
    $rawdata = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $rawdata[] = $row;
    }

    return $rawdata;
}


function hacerConsulta2($query) {
    global $conexion2;
    $otra = mysqli_query($conexion2, $query);
    //mysqli_close($conexion);
    return $otra;
}



$accion = $_REQUEST["accion"];

if ($accion == "TraerDatosLocal") {
//    echo "HOLA";
//       exit();
    try {
        if (ValidarUrl("www.google.com"))
          $valores = DevolverUnArreglo("SELECT * FROM `auditoria` WHERE IdUsuario != 0 AND FechaLlamado != '0000-00-00 00:00:00' AND EstadoNube = 0");
        else
            return;
        
        foreach ($valores as $dato ){
//            $dato['IdAuditoria'];
            $datos = DevolverUnDato("select servicio from servicio where idservicio = ".$dato['IdServicio']);
            
            hacerConsulta2("INSERT INTO `auditoria`(`IdAuditoria`, `IdServicio`, `IdUsuario`, `Turno`, `IdPersona`, `IdEncuesta`, `Estado`, `FechaLlegada`, `FechaLlamado`, `Fechasalio`, `NumeroLlamados`, `Observacion`,NombreServicio) VALUES "
                    . "(".$dato['IdAuditoria'].",".$dato['IdServicio'].",".$dato['IdUsuario'].",'".$dato['Turno']."',".$dato['IdPersona'].",".$dato['IdEncuesta'].",'".$dato['Estado']."','".$dato['FechaLlegada']."','".$dato['FechaLlamado']."','".$dato['Fechasalio']."',".$dato['NumeroLlamados'].",'".$dato['Observacion']."','$datos')");            
//            echo "INSERT INTO `auditoria`(`IdAuditoria`, `IdServicio`, `IdUsuario`, `Turno`, `IdPersona`, `IdEncuesta`, `Estado`, `FechaLlegada`, `FechaLlamado`, `Fechasalio`, `NumeroLlamados`, `Observacion`) VALUES "
//                    . "(".$dato['IdAuditoria'].",".$dato['IdServicio'].",".$dato['IdUsuario'].",'".$dato['Turno']."',".$dato['IdPersona'].",".$dato['IdEncuesta'].",'".$dato['Estado']."','".$dato['FechaLlegada']."','".$dato['FechaLlamado']."','".$dato['Fechasalio']."',".$dato['NumeroLlamados'].",'".$dato['Observacion']."')";
//            exit();
            hacerConsulta("update auditoria set EstadoNube = 1 where idauditoria = ".$dato['IdAuditoria']."");
        }
        hacerConsulta2("DELETE FROM auditoria where FechaLlegada<=(SELECT (NOW() - INTERVAL 1 DAY))");
       $validar = array('respuesta' => "Insertado Correctamente");
       
    } catch (Exception $ex) {
        
    }
}

function ValidarUrl($url) {
    $validar = @fsockopen($url, 80, $errno, $errstr, 15);
    if ($validar) {
        fclose($validar);
        return true;
    } else
        return false;
}
