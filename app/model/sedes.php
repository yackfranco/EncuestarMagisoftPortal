<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';


$accion = $_REQUEST["accion"];



if ($accion == "listarciudades") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $ciudad = DevolverUnArreglo("SELECT * FROM ciudad WHERE Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa");
    $respuesta = $ciudad;
}

if ($accion == "listarciudadselect") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $ciudad = DevolverUnArreglo("SELECT * FROM ciudad WHERE Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa");
    $respuesta = $ciudad;
}

if ($accion == "traerciudad") {
    $idciudad = $_REQUEST["idciudad"];
    $idciudad = DevolverUnArreglo("SELECT * FROM ciudad WHERE IdCiudad = '$idciudad' AND Estado = 'ACTIVO'");
    $respuesta = $idciudad;
}



if ($accion == "editarciudad") {
    $IdCiudad = $_REQUEST["IdCiudad"];
    $NombreCiudad = strtoupper($_REQUEST["NombreCiudad"]);
    $consultaciudad = DevolverUnDato("SELECT count(NombreCiudad) from ciudad where NombreCiudad = '$NombreCiudad' AND Estado = 'ACTIVO'");

    if ($consultaciudad != "0") {
        $respuesta = "invalido";
    } else {
        $sql = hacerConsulta("UPDATE ciudad SET NombreCiudad = '$NombreCiudad' WHERE IdCiudad ='$IdCiudad'");
        $respuesta = $sql;
    }
}


if ($accion == "eliminarciudad") {
    $idciudad = $_REQUEST["idciudad"];
    //$validarciudad =  DevolverUnDato ("SELECT COUNT(IdCiudad) FROM calificacion WHERE IdCiudad = '$idciudad'");
    //if($validarciudad != 1){
    $elimiciudad = hacerConsulta("UPDATE ciudad SET Estado = 'ELIMINADO' WHERE IdCiudad = '$idciudad'");
    $respuesta = "eliminado";
    /* }else{
      $respuesta = "noeliminar";
      } */
}


if ($accion == "ingresarciudad") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];

    $nombreciudad = strtoupper($_REQUEST['nombreciudad']);
    $consultaciudad = DevolverUnDato("SELECT count(NombreCiudad) from ciudad where NombreCiudad = '$nombreciudad' AND Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa");

    if ($consultaciudad != "0") {
        $respuesta = "invalido";
    } else {
        $consultarciudadeliminada = DevolverUnDato("SELECT count(NombreCiudad) from ciudad where NombreCiudad = '$nombreciudad' AND Estado = 'ELIMINADO' and IdEmpresa = $IdEmpresa");
        if ($consultarciudadeliminada != "0") {
            $sql = hacerConsulta("UPDATE ciudad SET NombreCiudad = '$nombreciudad', Estado = 'ACTIVO' where NombreCiudad = '$nombreciudad'");
            $respuesta = "valido";
        } else {
            $sql = hacerConsulta("INSERT INTO ciudad (NombreCiudad,Estado,IdEmpresa) VALUES ('$nombreciudad','ACTIVO',$IdEmpresa)");
            $respuesta = "valido";
        }
        /*
          $sql = hacerConsulta("INSERT INTO ciudad (NombreCiudad,Estado) VALUES ('$nombreciudad','ACTIVO')");
          $respuesta = "valido"; */
    }
}

if ($accion == "listarsedes") {
    $IdCiudad = $_REQUEST['IdCiudad'];
    $ciudad = DevolverUnArreglo("SELECT * FROM sede WHERE IdCiudad = '$IdCiudad' AND Estado = 'ACTIVO'");
    $respuesta = $ciudad;
}

if ($accion == "editarsede") {
    $IdSede = $_REQUEST['IdSede'];
    $sedes = DevolverUnArreglo("SELECT * FROM sede WHERE IdSede = '$IdSede' AND Estado = 'ACTIVO'");
    $respuesta = $sedes;
}


if ($accion == "editarlasede") {
    $IdSede = $_REQUEST['IdSede'];
    $NombreSede = strtoupper($_REQUEST['NombreSede']);
    $IdCiudad = DevolverUnDato("SELECT IdCiudad FROM sede WHERE IdSede ='$IdSede'");
    $valdiarsede = DevolverUnDato("SELECT COUNT(NombreSede) FROM sede WHERE NombreSede = '$NombreSede' AND Estado = 'ACTIVO'");
    if ($valdiarsede != 0) {
        $respuesta = "invalido";
    } else {
        $sede = hacerConsulta("UPDATE sede SET NombreSede = '$NombreSede' WHERE IdSede ='$IdSede'");
        $respuesta = $IdCiudad;
    }
}

if ($accion == "ingresarsede") {
    //print_r($_REQUEST);

    $IdCiudad = $_REQUEST['IdCiudad'];
    $NombreSede = strtoupper($_REQUEST['NombreSede']);

    $validarsede = DevolverUnDato("SELECT COUNT(NombreSede) FROM sede WHERE NombreSede = '$NombreSede' AND IdCiudad = '$IdCiudad' AND Estado = 'ACTIVO'");


    if ($validarsede != "0") {
        $respuesta = "invalido";
    } else {
        $validarsedeeliminada = DevolverUnDato("SELECT COUNT(NombreSede) FROM sede WHERE NombreSede = '$NombreSede' AND IdCiudad = '$IdCiudad' AND Estado = 'ELIMINADO'");
        //echo $validarsedeeliminada;        
        if ($validarsedeeliminada != "0") {
            $sede = hacerConsulta("UPDATE sede SET IdCiudad = '$IdCiudad', NombreSede = '$NombreSede', Estado = 'ACTIVO' WHERE NombreSede = '$NombreSede' AND IdCiudad = '$IdCiudad'");
            $respuesta = $IdCiudad;
        } else {
            $sede = hacerConsulta("INSERT INTO sede (IdCiudad, NombreSede,Estado) VALUES ('$IdCiudad','$NombreSede','ACTIVO')");
            $respuesta = $IdCiudad;
        }
    }
}



if ($accion == "eliminarsede") {
    $IdSede = $_REQUEST['IdSede'];
    $valdiarsede = DevolverUnDato("SELECT COUNT(IdSede) FROM calificacion WHERE IdSede = '$IdSede'");
    //if($valdiarsede !=0){
    //    $respuesta = "noeliminar";
    //}else{
    $sede = hacerConsulta("UPDATE sede SET Estado = 'ELIMINADO' WHERE IdSede = '$IdSede'");
    $respuesta = "eliminar";
    //}
}




echo json_encode($respuesta, JSON_FORCE_OBJECT);
?>

