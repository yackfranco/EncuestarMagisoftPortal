<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

$accion = $_REQUEST["accion"];

if ($accion == "cargarTablaempresas") {
    $datosEmpresa = DevolverUnArreglo("SELECT * FROM datosempresa WHERE Estado = 'ELIMINADO' ORDER BY FechaLicencia  ASC");
    $validar = $datosEmpresa;
}

if ($accion == "busquedaempresas") {
    $datousuario = $_REQUEST["datousuario"];
    $datoUsuario = DevolverUnArreglo("SELECT * FROM datosempresa WHERE Estado = 'ELIMINADO' AND (Nombre LIKE '%" . "$datousuario" . "%' OR nit LIKE '%" . "$datousuario" . "%' OR Nombre LIKE '%" . "$datousuario" . "%')");
    //echo "SELECT * FROM usuario WHERE NombreCompleto LIKE '%"."$datousuario"."%'";
    $validar = $datoUsuario;
}

if ($accion == "RecuperarEmpresa") {
    $idusuario = $_REQUEST["idusuario"];
    $datoUsuario = hacerConsulta("UPDATE datosempresa SET Estado = 'ACTIVO' WHERE IdEmpresa = '$idusuario'");
    hacerConsulta("UPDATE usuario SET Estado = 'ACTIVO' WHERE IdEmpresa = '$idusuario'");
    $validar = $datoUsuario;
}

//ECHO PARA MANDAR DATOS POR JSON A LA VISTA
echo json_encode($validar, JSON_FORCE_OBJECT);
//echo json_encode($respuesta, JSON_FORCE_OBJECT);
?>
