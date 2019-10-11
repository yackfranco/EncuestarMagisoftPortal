<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';



$accion = $_REQUEST["accion"];


if ($accion == "listarusuario") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $usuarios = DevolverUnArreglo("SELECT NombreUsuario, IdUsuario, Rol FROM usuario WHERE rol = 'ASESOR' AND Estado = 'ACTIVO' AND IdEmpresa = $IdEmpresa");
    $respuesta = $usuarios;
}

if ($accion == "listarpreguntas") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $preguntas = DevolverUnArreglo("SELECT * FROM pregunta INNER JOIN paquete ON pregunta.IdPaquete = paquete.IdPaquete WHERE pregunta.IdEmpresa = $IdEmpresa");
    $respuesta = $preguntas;
}

if ($accion == "listarencuestas") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $encuestas = DevolverUnArreglo("SELECT * FROM paquete WHERE IdEmpresa = $IdEmpresa");
    $respuesta = $encuestas;
}

if ($accion == "subtmiconfiguser") {
    $idusuario = $_REQUEST["idusuario"];
    $metodo = $_REQUEST["metodo"];
    $modo = $_REQUEST["modo"];
    $idpregunta = $_REQUEST["idpregunta"];
    $idencuesta = $_REQUEST["idencuesta"];
    $ventana = $_REQUEST["ventana"];
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $configuracion = DevolverUnDato("SELECT count(*) FROM estilocalificacion WHERE IdUsuario = $idusuario");


    if ($configuracion != "0") {
        if ($idpregunta != "0") {
            $sql = hacerConsulta("UPDATE estilocalificacion SET Modo='$modo', Estilo='$idpregunta', Metodo='$metodo', MinimizarCalif='$ventana', IdEmpresa = $IdEmpresa WHERE IdUsuario = '$idusuario'");
        } else {
            $sql = hacerConsulta("UPDATE estilocalificacion SET Modo='$modo', Estilo='$idencuesta', Metodo='$metodo', MinimizarCalif='$ventana', IdEmpresa = $IdEmpresa WHERE IdUsuario = '$idusuario'");
        }
        $respuesta = "update";
    } else {
        if ($idpregunta != "0") {
            $consulta = hacerConsulta("INSERT INTO estilocalificacion (IdEmpresa,IdUsuario, Modo, Estilo, Metodo, MinimizarCalif) VALUES ($IdEmpresa,'$idusuario', '$modo', '$idpregunta', '$metodo','$ventana')");
        } else {
            $consulta = hacerConsulta("INSERT INTO estilocalificacion (IdEmpresa,IdUsuario, Modo, Estilo, Metodo, MinimizarCalif) VALUES ($IdEmpresa,'$idusuario', '$modo', '$idencuesta', '$metodo','$ventana')");
        }
        $respuesta = "insert";
    }
}



echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
?>