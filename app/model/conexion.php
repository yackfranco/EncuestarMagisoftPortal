<?php
/*
$usuario = "root";
$contrasena = "";  // en mi caso tengo contraseña pero en casa caso introducidla aquí.
$servidor = "localhost";
$basededatos = "calificadoresadmin";
*/
$usuario = "clientes";
$contrasena = "ingetronik";  // en mi caso tengo contraseña pero en casa caso introducidla aquí.
$servidor = "157.230.11.245";
$basededatos = "calificadorv2";

$conexion = mysqli_connect($servidor, $usuario, $contrasena) or die("No se ha podido conectar al servidor de Base de datos");
$db = mysqli_select_db($conexion, $basededatos)or die("Upps! Pues va a ser que no se ha podido conectar a la base de datos");
$conn = new mysqli($servidor, $usuario, $contrasena, $basededatos);
mysqli_set_charset($conexion, 'utf8');


function DevolverUnDato($query) {
    global $conexion;
    $resultado = mysqli_query($conexion, $query)or die("Algo ha ido mal en la consulta a la base de datos");
    $arreglo = mysqli_fetch_array($resultado);
    return $arreglo[0];
}

function DevolverUnArreglo($query) {
    global $conexion;

    $resultado = mysqli_query($conexion, $query)or die("Algo ha ido mal en la consulta a la base de datos");

//    array('IdCiudad' => '', 'NombreCiudad' => 'Seleccione una ciudad')
    $rawdata = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $rawdata[] = $row;
    }
    return $rawdata;
}
function InsertDevolviendoID($query){
    global $conexion;
    mysqli_query($conexion, $query);
    $resultado = mysqli_insert_id($conexion)or die("Algo ha ido mal en la consulta a la base de datos");
//    $arreglo = mysqli_fetch_array($resultado);
    return $resultado;
}
function hacerConsulta($query) {
    global $conexion;
    $otra = mysqli_query($conexion, $query);
    //mysqli_close($conexion);
    return $otra;
}
