<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';


$accion = $_REQUEST["accion"];


if ($accion == "listarencuesta") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $encuesta = DevolverUnArreglo("SELECT * FROM paquete WHERE Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa ORDER BY Paquete ASC");
    $respuesta = $encuesta;
}

if ($accion == "listarencuestaselect") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $encuesta = DevolverUnArreglo("SELECT * FROM paquete WHERE Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa ORDER BY Paquete ASC");
    $respuesta = $encuesta;
}


if ($accion == "ingresarpaquete") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];

    $nombrepaquete = strtoupper($_REQUEST['nombrepaquete']);
    $consultapaquete = DevolverUnDato("SELECT count(Paquete) from paquete where Paquete = '$nombrepaquete' and IdEmpresa = $IdEmpresa");

    if ($consultapaquete != "0") {
        $respuesta = "invalido";
    } else {
        $sql = hacerConsulta("INSERT INTO paquete (Paquete,Estado,IdEmpresa) VALUES ('$nombrepaquete','ACTIVO',$IdEmpresa)");
        $respuesta = "valido";
    }
}

if ($accion == "traerencuesta") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $IdPaquete = $_REQUEST["IdPaquete"];
    $paquete = DevolverUnArreglo("SELECT * FROM paquete WHERE IdPaquete = '$IdPaquete' AND Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa ORDER BY Paquete ASC");
    $respuesta = $paquete;
}



if ($accion == "editarpaquete") {
    $IdPaquete = $_REQUEST["IdPaquete"];
    $Paquete = strtoupper($_REQUEST["Paquete"]);

    $consultapaquete = DevolverUnDato("SELECT count(Paquete) from paquete where Paquete = '$Paquete'");

    if ($consultapaquete != "0") {
        $respuesta = "invalido";
    } else {
        $sql = hacerConsulta("UPDATE paquete SET Paquete = '$Paquete' WHERE IdPaquete ='$IdPaquete'");
        $respuesta = $sql;
    }
}


if ($accion == "eliminarencuesta") {
    $IdPaquete = $_REQUEST["IdPaquete"];
    //$validarencuesta =  DevolverUnDato ("SELECT count(*) FROM calificacion where IdPregunta IN (SELECT IdPregunta from pregunta where pregunta.IdPaquete = '$IdPaquete')");
    //if($validarencuesta != 1){UPDATE paquete SET Paquete = '$Paquete' WHERE IdPaquete ='$IdPaquete'
    $elimiencuesta = hacerConsulta("UPDATE paquete SET Estado = 'ELIMINADO' WHERE IdPaquete = '$IdPaquete'");
    $eliminarpreguntasencuesta = hacerConsulta("UPDATE pregunta SET Estado = 'ELIMINADO' WHERE IdPaquete = '$IdPaquete'");
    $respuesta = "eliminado";
    //}else{
    // $respuesta = "noeliminar";
    //}
}

if ($accion == "elegirpaquete") {
    $IdPaquete = $_REQUEST["IdPaquete"];
    $preguntas = DevolverUnArreglo("SELECT * FROM pregunta WHERE IdPaquete = '$IdPaquete' AND Estado = 'ACTIVO' ORDER BY Pregunta ASC");
    $respuesta = $preguntas;
}

if ($accion == "ingersarpregunta") {
    $IdPaquete = $_REQUEST["IdPaquete"];
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $pregunta = strtoupper($_REQUEST["Nombrepregunta"]);
    $validarpregunta = DevolverUnDato("SELECT COUNT(*) FROM pregunta WHERE Pregunta = '$pregunta' AND IdPaquete = '$IdPaquete' AND IdEmpresa = $IdEmpresa");
    if ($validarpregunta != 0) {
        $respuesta = "invalido";
    } else {
        $sql = hacerConsulta("INSERT INTO pregunta (IdPaquete,Pregunta,Estado,IdEmpresa) VALUES ('$IdPaquete','$pregunta','ACTIVO',$IdEmpresa)");
        $respuesta = "valido";
    }
}

if ($accion == "traerpregunta") {
    $IdPregunta = $_REQUEST["IdPregunta"];
    $paquete = DevolverUnArreglo("SELECT * FROM pregunta WHERE IdPregunta = '$IdPregunta' AND Estado = 'ACTIVO'");
    $respuesta = $paquete;
}


if ($accion == "editarpregunta") {
    $IdPregunta = $_REQUEST["IdPregunta"];
    $Pregunta = strtoupper($_REQUEST["Pregunta"]);
    $IdPaquete = $_REQUEST["IdPaquete"];
    $validarpregunta = DevolverUnDato("SELECT COUNT(*) FROM pregunta WHERE Pregunta = '$Pregunta' AND IdPaquete = '$IdPaquete'");
    if ($validarpregunta != 0) {
        $respuesta = "invalido";
    } else {
        $sql = hacerConsulta("UPDATE pregunta SET Pregunta = '$Pregunta' WHERE IdPregunta ='$IdPregunta'");
        $respuesta = $sql;
    }
}

if ($accion == "eliminarpregunta") {
    $IdPregunta = $_REQUEST["IdPregunta"];
    //$validarpregunta =  DevolverUnDato ("SELECT count(*) FROM calificacion where IdPregunta = '$IdPregunta'");
    //if($validarpregunta != 1){
    $elimiencuesta = hacerConsulta("UPDATE pregunta SET Estado = 'ELIMINADO' WHERE IdPregunta = '$IdPregunta'");
    $respuesta = "eliminado";
    // }else{
    //    $respuesta = "noeliminar";
    //}
}

echo json_encode($respuesta, JSON_FORCE_OBJECT);
?>

