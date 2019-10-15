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
//   print_r($_REQUEST);
//   exit();
    $metodo = $_REQUEST["metodo"];
    $modo = $_REQUEST["modo"];
//    $idpregunta = $_REQUEST["idpregunta"];
//    $idencuesta = $_REQUEST["idencuesta"];
    $ventana = $_REQUEST["ventana"];
    $Todosusuarios = $_REQUEST["TodosAsesores"];
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $estilo = $_REQUEST["estilo"];

    if ($Todosusuarios == 1) {
        $respuesta = "configuracion para todos los usuarios";
//        echo "UPDATE estilocalificacion SET Modo='$modo', Estilo='$idpregunta', Metodo='$metodo', MinimizarCalif='$ventana' where idempresa = $IdEmpresa";
//        exit();

        $sql = hacerConsulta("UPDATE estilocalificacion SET Modo='$modo', Estilo='$estilo', Metodo='$metodo', MinimizarCalif='$ventana' where idempresa = $IdEmpresa");
        $respuesta = "update";
    } else {
        $idusuario = $_REQUEST["idusuario"];
        $configuracion = DevolverUnDato("SELECT count(*) FROM estilocalificacion WHERE IdUsuario = $idusuario");
        if ($configuracion != "0") {
            $sql = hacerConsulta("UPDATE estilocalificacion SET Modo='$modo', Estilo='$estilo', Metodo='$metodo', MinimizarCalif='$ventana' WHERE IdUsuario = '$idusuario'");
            $respuesta = "update";
        } else {
            $consulta = hacerConsulta("INSERT INTO estilocalificacion (IdUsuario,idempresa, Modo, Estilo, Metodo, MinimizarCalif) VALUES ('$idusuario',$IdEmpresa, '$modo', '$estilo', '$metodo','$ventana')");
            $respuesta = "insert";
        }
    }
}

//if ($accion == "subtmiconfiguser") {
//    $idusuario = $_REQUEST["idusuario"];
//    $metodo = $_REQUEST["metodo"];
//    $modo = $_REQUEST["modo"];
//    $idpregunta = $_REQUEST["idpregunta"];
//    $idencuesta = $_REQUEST["idencuesta"];
//    $ventana = $_REQUEST["ventana"];
//    $IdEmpresa = $_REQUEST["IdEmpresa"];
//    $configuracion = DevolverUnDato("SELECT count(*) FROM estilocalificacion WHERE IdUsuario = $idusuario");
//
//
//    if ($configuracion != "0") {
//        if ($idpregunta != "0") {
//            $sql = hacerConsulta("UPDATE estilocalificacion SET Modo='$modo', Estilo='$idpregunta', Metodo='$metodo', MinimizarCalif='$ventana', IdEmpresa = $IdEmpresa WHERE IdUsuario = '$idusuario'");
//        } else {
//            $sql = hacerConsulta("UPDATE estilocalificacion SET Modo='$modo', Estilo='$idencuesta', Metodo='$metodo', MinimizarCalif='$ventana', IdEmpresa = $IdEmpresa WHERE IdUsuario = '$idusuario'");
//        }
//        $respuesta = "update";
//    } else {
//        if ($idpregunta != "0") {
//            $consulta = hacerConsulta("INSERT INTO estilocalificacion (IdEmpresa,IdUsuario, Modo, Estilo, Metodo, MinimizarCalif) VALUES ($IdEmpresa,'$idusuario', '$modo', '$idpregunta', '$metodo','$ventana')");
//        } else {
//            $consulta = hacerConsulta("INSERT INTO estilocalificacion (IdEmpresa,IdUsuario, Modo, Estilo, Metodo, MinimizarCalif) VALUES ($IdEmpresa,'$idusuario', '$modo', '$idencuesta', '$metodo','$ventana')");
//        }
//        $respuesta = "insert";
//    }
//}


if ($accion == "listarconfiguracion") {
    $IdUsuario = $_REQUEST["idusuario"];
    $configusuario = DevolverUnArreglo("SELECT * FROM estilocalificacion WHERE IdUsuario = '$IdUsuario'");
    if ($configusuario[0]['Modo'] == "Multi") {
        $encuespregun = DevolverUnArreglo("SELECT * FROM paquete WHERE Estado = 'ACTIVO' AND  IdPaquete = " . $configusuario[0]['Estilo']);
        $encuesta = $encuespregun;
    } else {
        $encuespregun = DevolverUnArreglo("SELECT * FROM pregunta WHERE Estado = 'ACTIVO' AND IdPregunta = " . $configusuario[0]['Estilo']);
        $encuesta = $encuespregun;
    }
    $respuesta = array($configusuario, $encuesta);
}



echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
?>