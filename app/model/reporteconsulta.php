<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';


$accion = $_REQUEST["accion"];

if ($accion == "fechaactual") {
    $fechamysql = DevolverUnDato("SELECT NOW()");
    $respuesta = $fechamysql;
}

if ($accion == "listarciudades") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $ciudad = DevolverUnArreglo("SELECT * FROM ciudad WHERE Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa ORDER BY NombreCiudad ASC");
    $respuesta = $ciudad;
}

if ($accion == "listarsedes") {
    $idciudad = $_REQUEST["idciudad"];
    $sede = DevolverUnArreglo("SELECT * FROM sede WHERE IdCiudad = '$idciudad' AND Estado = 'ACTIVO' ORDER BY NombreSede ASC");
    $respuesta = $sede;
}

if ($accion == "listarusuarios") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $usuario = DevolverUnArreglo("SELECT * FROM usuario WHERE Estado = 'ACTIVO' and Rol = 'ASESOR' and IdEmpresa = $IdEmpresa ORDER BY NombreUsuario ASC");
    $respuesta = $usuario;
}

if ($accion == "listarpreguntas") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $preguntas = DevolverUnArreglo("SELECT * FROM pregunta WHERE Estado = 'ACTIVO' and IdEmpresa = $IdEmpresa ORDER BY Pregunta ASC");
    $respuesta = $preguntas;
}
echo json_encode($respuesta, JSON_FORCE_OBJECT);
?>