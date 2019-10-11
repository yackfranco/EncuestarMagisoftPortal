<?php

$usuario3 = "clientes";
$contrasena3 = "ingetronik";  // en mi caso tengo contraseña pero en casa caso introducidla aquí.
$servidor3 = "157.230.11.245";
$basededatos3 = "Licencia";
$conexion3 = mysqli_connect($servidor3, $usuario3, $contrasena3) or die("No se ha podido conectar al servidor de Base de datos");
$db2 = mysqli_select_db($conexion3, $basededatos3)or die("Upps! Pues va a ser que no se ha podido conectar a la base de datos");
$conn2 = new mysqli($servidor3, $usuario3, $contrasena3, $basededatos3);
mysqli_set_charset($conexion3, 'utf8');

//function DevolverUnDato($query) {
//    global $conexion;
//    $resultado = mysqli_query($conexion, $query)or die("Algo ha ido mal en la consulta a la base de datos");
//    $arreglo = mysqli_fetch_array($resultado);
//    return $arreglo[0];
//}
//include 'conexion.php';


function DevolverUnArreglo3($query) {
    global $conexion3;

    $resultado = mysqli_query($conexion3, $query)or die("Algo ha ido mal en la consulta a la base de datos");

//    array('IdCiudad' => '', 'NombreCiudad' => 'Seleccione una ciudad')
    $rawdata = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $rawdata[] = $row;
    }

    return $rawdata;
}


function hacerConsulta3($query) {
    global $conexion3;
    $otra = mysqli_query($conexion3, $query);
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
       $validar = array('respuesta' => "Insertado Correctamente");
       
    } catch (Exception $ex) {
        
    }
}

//function ValidarUrl($url) {
//    $validar = @fsockopen($url, 80, $errno, $errstr, 15);
//    if ($validar) {
//        fclose($validar);
//        return true;
//    } else
//        return false;
//}
