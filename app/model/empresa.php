<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

$accion = $_REQUEST["accion"];

if($accion == "fechamysql"){
    $fechamysql = DevolverUnDato("SELECT NOW()");
    $respuesta = $fechamysql;
}


if($accion == "cargarTablaEmpresa"){
    $datosEmpresa = DevolverUnArreglo("SELECT * FROM empresa WHERE estado = 1  ORDER BY licencia ASC");
    $respuesta = $datosEmpresa;
}

//INGRESAR USUARIOS AL SISTEMA
if($accion =="ingresarempresa"){
    $nombreempresa = $_REQUEST["nombreempresa"];
    $nit = $_REQUEST["nit"];
    $slogan = $_REQUEST["slogan"];
    $licencia = $_REQUEST["fechalicencia"];
    $consultarempresa = DevolverUnDato("SELECT count(*) FROM empresa where nombreempresa = '$nombreempresa'");
    if($consultarempresa!="0"){
        $respuesta = "invalido";
    } else{
        $sql = hacerConsulta("INSERT INTO empresa (nombreempresa, nit, slogan, licencia, estado) VALUES ('$nombreempresa', '$nit', '$slogan', '$licencia', 1)");
        $respuesta = "valido";
    }

}


if($accion == "listarempresa"){
    $idempresa = $_REQUEST["idempresa"];
    $datoEmpresa = DevolverUnArreglo("SELECT idempresa, nombreempresa, nit, slogan, licencia FROM empresa WHERE idempresa = $idempresa");
    $respuesta = $datoEmpresa;
}

if($accion == "editarlaempresa"){
    $idempresa = $_REQUEST["idempresa"];
    $nombreempresa = $_REQUEST["nombreempresa"];
    $nit = $_REQUEST["nit"];
    $slogan = $_REQUEST["slogan"];
    $fechalicencia = $_REQUEST["fechalicencia"];
    $sqlEmpresa = hacerConsulta("UPDATE empresa SET nombreempresa = '$nombreempresa', nit= '$nit', slogan= '$slogan', licencia = '$fechalicencia' WHERE idempresa = '$idempresa'");
    $respuesta = "bien";
}

if ($accion == "eliminarempresa"){
    $idempresa = $_REQUEST["idempresa"];
    $sqlEmpresa = hacerConsulta("UPDATE empresa SET estado = 0 WHERE idempresa = '$idempresa'");
    $respuesta = "eliminado";
}

if ($accion == "listarusuarioempresa"){
    $idempresa = $_REQUEST["idempresa"];
    $consultarusuarioempresa = DevolverUnDato("SELECT count(*) FROM usuario where idempresa = $idempresa and estado = 2");
    if($consultarusuarioempresa!="0"){
        $sqlEmpresa = DevolverUnArreglo("SELECT * FROM usuario WHERE idempresa = '$idempresa' AND estado = 2");
        $respuesta = $sqlEmpresa;
    } else{
        $respuesta = "crearusuario";
    }
}

if ($accion == "ingresarusuarioadmin"){
    
    $idempresa = $_REQUEST["idempresa"];
    $nombre = $_REQUEST["nombre"];
    $apellido = $_REQUEST["apellido"];
    $cedula = $_REQUEST["cedula"];
    $correo = $_REQUEST["correo"];
    $nombreusuario = $_REQUEST["nombreusuario"];
    $contrasena = $_REQUEST["contrasena"];

    $consultarusuarioempresa = DevolverUnDato("SELECT count(*) FROM usuario where nombreusuario = '$nombreusuario'");
    
    if($consultarusuarioempresa =="0"){
        $datosusuarioadmin = hacerConsulta("INSERT INTO usuario (idempresa, nombre, apellido, cedula, correo, rol, nombreusuario, contrasena, estado) VALUES ('$idempresa', '$nombre', '$apellido', '$cedula', '$correo', 'Administrador', '$nombreusuario', md5($contrasena), 2)");
        $respuesta = "valido";
    }else{
        $respuesta = "invalido";
    }
}

if ($accion == "listarusuarioeditarempresa"){
    $idusuario = $_REQUEST["idusuario"];
    $datousuarioEmpresa = DevolverUnArreglo("SELECT * FROM usuario WHERE idusuario = '$idusuario'");
    $respuesta =  $datousuarioEmpresa;
}

if($accion == "editarusuarioadminempresa"){
    $idusuario = $_REQUEST["idusuario"];
    $nombre = $_REQUEST["nombre"];
    $apellido = $_REQUEST["apellido"];
    $cedula = $_REQUEST["cedula"];
    $correo = $_REQUEST["correo"];
    $nombreusuario = $_REQUEST["nombreusuario"];
    $contrasena = $_REQUEST["contrasena1"];
    $sql = hacerConsulta("UPDATE usuario SET nombre ='$nombre', apellido ='$apellido', cedula ='$cedula', correo =' $correo', nombreusuario = '$nombreusuario', contrasena =md5($contrasena) WHERE idusuario = '$idusuario'");
    $respuesta = "bien";

}
//ECHO PARA MANDAR DATOS POR JSON A LA VISTA
echo json_encode($respuesta, JSON_FORCE_OBJECT);

?>