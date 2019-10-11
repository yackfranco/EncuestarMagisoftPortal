<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';


$accion = $_REQUEST["accion"];

if($accion == "validarentradaasesor"){
    $idusuario = $_REQUEST['idusuario'];
    $validacionasesor = DevolverUnDato("SELECT count(*) FROM estilocalific WHERE idusuario = $idusuario");
    $validar = $validacionasesor;
}


if($accion == "listarencuestapregunta"){
    $idusuario = $_REQUEST['idusuario'];
    $datoscalific = DevolverUnArreglo("SELECT * FROM estilocalific WHERE idusuario = '$idusuario'");

    if($datoscalific[0]["idpregunta"] != "0"){
        $idpregunta = $datoscalific[0]["idpregunta"];
        $datospregunta = DevolverUnArreglo("SELECT * FROM pregunta WHERE idpregunta = '$idpregunta' AND estado = 1");   
        $datos= array('trabajo' => $datoscalific, 'encuesta' => $datospregunta);
        $validar =  $datos;
    }else{
        $idencuesta = $datoscalific[0]["idencuesta"];        
        $datosencuesta = DevolverUnArreglo("SELECT * FROM pregunta WHERE idencuesta = '$idencuesta' AND estado = 1");
        $datos= array('trabajo' => $datoscalific, 'encuesta' => $datosencuesta);
        $validar =  $datos;
    }
}


if($accion == "listardatosusuario"){
    $idusuario = $_REQUEST['idusuario'];
    $idempresa = $_REQUEST['idempresa'];
    $datosuasuario = DevolverUnArreglo("SELECT * FROM usuario WHERE idusuario = '$idusuario'");
    $datosusuarioempresa = DevolverUnArreglo("SELECT * FROM empresa WHERE idempresa = '$idempresa'");
    $datos= array('usuario' => $datosuasuario, 'empresa' => $datosusuarioempresa);
    $validar = $datos;
}


if($accion == "subircalificacion"){

    $idusuario = $_REQUEST['idusuario'];
    $idempresa = $_REQUEST['idempresa'];
    $idsede = $_REQUEST['idsede'];
    $numcalif = $_REQUEST['calificacion'];
    $idpregunta = $_REQUEST['idpregunta'];
    
    $fecha = DevolverUnDato("SELECT NOW()");
    
    $sql = hacerConsulta("INSERT INTO calificacion (idsede, idempresa, idusuario, idpregunta, numcalif, fechacalif) VALUES ('$idsede',$idempresa,$idusuario,$idpregunta,$numcalif,'$fecha')");
    //echo "INSERT INTO calificacion (idsede, idempresa, idusuario, idpregunta, numcalif, fechacalif) VALUES ($idsede,$idempresa,$idusuario,$idpregunta,$numcalif,'$fecha'";
    $validar = "Calificacion enviada con exito";
    
}



echo json_encode($validar, JSON_UNESCAPED_UNICODE);
?>

