<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// require '../../src/Exception.php';
// require '../../src/PHPMailer.php';
// require '../../src/SMTP.php';

$accion = $_REQUEST["accion"];


//INGRESAR USUARIOS AL SISTEMA
if ($accion == "ingresarusuario") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $nombre = strtoupper($_REQUEST["nombre"]);
    $cedula = $_REQUEST["cedula"];
    $correo = $_REQUEST["correo"];
    $nombreusuario = strtoupper($_REQUEST["nombreusuario"]);
    $rol = $_REQUEST["rol"];
    $contrasena = $_REQUEST["contrasena"];
    $consultarusuario = DevolverUnDato("SELECT count(NombreUsuario) from usuario where NombreUsuario = '$nombreusuario' and idempresa = $IdEmpresa");
    $validarcontraseña = DevolverUnDato("SELECT COUNT(Cedula) FROM usuario WHERE Cedula = '$cedula' and idempresa = $IdEmpresa");
    $validarpregunta = DevolverUnDato("SELECT count(*) FROM pregunta where idempresa = $IdEmpresa ");
    $pregunta = DevolverUnDato("SELECT IdPregunta FROM pregunta where idempresa = $IdEmpresa LIMIT 1");

    if ($validarpregunta == "0") {
        $validar = "Pinvalido";
    } else {
        if ($consultarusuario != "0") {
            $validar = "invalido";
        } else {
            if ($validarcontraseña != "0") {
                $validar = "Cinvalido";
            } else {
                $sql = InsertDevolviendoID("INSERT INTO usuario (Cedula, NombreCompleto, Correo, Rol, Estado, NombreUsuario, Contrasena, EstadoContrasena, IdEmpresa) 
                VALUES ('$cedula', '$nombre', '$correo', '$rol', 'ACTIVO', '$nombreusuario', '" . md5($contrasena) . "', 'NORMAL', $IdEmpresa)");
                if ($rol == "ASESOR") {
                    $califica = hacerConsulta("INSERT INTO estilocalificacion (IdUsuario, Modo, Estilo, Metodo, MinimizarCalif, IdEmpresa) VALUES "
                            . "('$sql', 'Mono', '$pregunta', 'Automatico',1,$IdEmpresa)");
                    $validar = "valido";
                } else {
                    $validar = "valido";
                }
            }
        }
    }
}


//EDITAR UN USUARIO
$contrasena = "";
if ($accion == "editarusuario") {
    $nombre = strtoupper($_REQUEST["NombreCompleto"]);
    $cedula = $_REQUEST["Cedula"];
    $correo = $_REQUEST["Correo"];
    $nombreusuario = strtoupper($_REQUEST["NombreUsuario"]);
    $rol = $_REQUEST["Rol"];
    $idusuario = $_REQUEST["idusuario"];
    $consultarusuario = DevolverUnDato("SELECT count(NombreUsuario) from usuario where NombreUsuario = '$nombreusuario' AND IdUsuario != '$idusuario'");

    $validarcontraseña = DevolverUnDato("SELECT COUNT(Cedula) FROM usuario WHERE Cedula = '$cedula' AND IdUsuario != '$idusuario'");

    if ($consultarusuario != "0") {
        $validar = "invalido";
    } else {
        if ($validarcontraseña != "0") {
            $validar = "Cinvalido";
        } else {
            if (empty($_REQUEST["contrasena"]) || $_REQUEST["contrasena"] == null) {
                $sql = hacerConsulta("UPDATE usuario SET Cedula = '$cedula', NombreCompleto = '$nombre', Correo = '$correo', Rol = '$rol', NombreUsuario = '$nombreusuario' WHERE IdUsuario ='$idusuario'");
                $validar = "sin contra";
            } else {
                $contrasena = $_REQUEST["contrasena"];
                $sql = hacerConsulta("UPDATE usuario SET Cedula = '$cedula', NombreCompleto = '$nombre', Correo = '$correo', Rol = '$rol', NombreUsuario = '$nombreusuario', Contrasena = md5($contrasena) WHERE IdUsuario ='$idusuario'");
                $validar = "con contra";
            }$validar = "con contra";
        }
    }
}


//ENLISTAR USUARIOS
if ($accion == "cargarTabla") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $datosUsuario = DevolverUnArreglo("SELECT * FROM usuario WHERE Estado = 'ACTIVO' AND IdEmpresa = $IdEmpresa ORDER BY NombreCompleto ASC");
    $validar = $datosUsuario;
}

//listar usuario a editar
if ($accion == "listarusuario") {
    $idusuario = $_REQUEST["idusuario"];
    $datoUsuario = DevolverUnArreglo("SELECT * FROM usuario WHERE IdUsuario = '$idusuario'");
    $validar = $datoUsuario;
}


//ELIMINAR USUARIO FORMA LOGICA
if ($accion == "eliminarusuario") {
    $idusuario = $_REQUEST["idusuario"];
    $datoUsuario = hacerConsulta("UPDATE usuario SET Estado = 'ELIMINADO' WHERE IdUsuario = '$idusuario'");
    $validar = $datoUsuario;
}

if ($accion == "busquedausuario") {
    $datousuario = $_REQUEST["datousuario"];
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $datoUsuario = DevolverUnArreglo("SELECT * FROM usuario WHERE IdEmpresa = $IdEmpresa and (NombreCompleto LIKE '%" . "$datousuario" . "%' OR Cedula LIKE '%" . "$datousuario" . "%' OR NombreUsuario LIKE '%" . "$datousuario" . "%')");
    //echo "SELECT * FROM usuario WHERE NombreCompleto LIKE '%"."$datousuario"."%'";
    $validar = $datoUsuario;
    //print_r($datoUsuario);    
}
if ($accion == "registroUsuarios") {
    $idempresa = $_REQUEST["IdEmpresa"];
    $Datos = DevolverUnArreglo("SELECT registrousuario.fecharegistro,ciudad.NombreCiudad,sede.NombreSede, usuario.NombreUsuario,(SELECT if (registrousuario.fecharegistro> (SELECT NOW() - INTERVAL 2 MINUTE), 'YES', 'NO')) as estado FROM `registrousuario` join ciudad on (ciudad.IdCiudad = registrousuario.idciudad) join sede on (sede.IdSede = registrousuario.idsede) join usuario on (usuario.IdUsuario = registrousuario.idusuario) where usuario.estado != 'ELIMINADO' and registrousuario.idempresa = $idempresa order by estado desc,fecharegistro desc");
    $validar = $Datos;
}

//ECHO PARA MANDAR DATOS POR JSON A LA VISTA
echo json_encode($validar, JSON_FORCE_OBJECT);
?>