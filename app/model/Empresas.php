<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

$accion = $_REQUEST["accion"];

//INGRESAR USUARIOS AL SISTEMA
if ($accion == "ingresarempresas") {
    $nombre = strtoupper($_REQUEST["nombre"]);
    $nit = $_REQUEST["nit"];
    $slogan = $_REQUEST["slogan"];
    $FechaLicencia = $_REQUEST["FechaLicencia"];

    $consultarusuario = DevolverUnDato("SELECT count(Nombre) from datosempresa where Estado = 'ACTIVO' AND Nombre = '$nombre'");
    $validarcontraseña = DevolverUnDato("SELECT COUNT(nit) FROM datosempresa WHERE Estado = 'ACTIVO' AND nit = '$nit'");
    if ($consultarusuario != "0") {
        $validar = "invalido";
    } else {
        if ($validarcontraseña != "0") {
            $validar = "Cinvalido";
        } else {
            $keyrandom = generateRandomString();
            $IdEmpresa = InsertDevolviendoID("INSERT INTO datosempresa (Nombre, nit, Slogan, FechaLicencia, KeyEmpresa, Estado) 
                VALUES ('$nombre', '$nit', '$slogan', '$FechaLicencia', '$keyrandom','ACTIVO')");

                hacerConsulta("INSERT INTO usuario (Cedula, NombreCompleto, Rol, Estado, NombreUsuario, Contrasena, EstadoContrasena, IdEmpresa) 
                VALUES ('$nit', '$nombre', 'ADMINISTRADOR', 'ACTIVO', '$nit','".md5($nit)."', 'NORMAL', $IdEmpresa)");
               
               $IdPaquete = InsertDevolviendoID("INSERT INTO paquete (IdEmpresa, Paquete, Estado) 
                VALUES ($IdEmpresa, 'SERVICIO', 'ACTIVO')");

               hacerConsulta("INSERT INTO pregunta (IdEmpresa, IdPaquete, Pregunta, Estado) 
                VALUES ($IdEmpresa, $IdPaquete, '¿ COMO LE PARECIO EL SERVICIO ?', 'ACTIVO')");
               
            hacerConsulta("INSERT INTO valorcalif (IdEmpresa,NumeroCalif,valorcalif) VALUES ($IdEmpresa,1,'MALO'),($IdEmpresa,2,'REGULAR'),($IdEmpresa,3,'BUENO'),($IdEmpresa,4,'EXCELENTE')");
            $validar = "valido";
        }
    }
}

//EDITAR UN USUARIO
if ($accion == "editarempresas") {
    $nombre = strtoupper($_REQUEST["Nombre"]);
    $nit = $_REQUEST["nit"];
    $slogan = $_REQUEST["Slogan"];
    $FechaLicencia = $_REQUEST["FechaLicencia"];
    $idusuario = $_REQUEST["idusuario"];

    $consultarusuario = DevolverUnDato("SELECT count(Nombre) from datosempresa where Nombre = '$nombre' AND IdEmpresa != '$idusuario'");

    $validarcontraseña = DevolverUnDato("SELECT COUNT(Nit) FROM datosempresa WHERE Nit = '$nit' AND IdEmpresa != '$idusuario'");

    if ($consultarusuario != "0") {
        $validar = "invalido";
    } else {
        if ($validarcontraseña != "0") {
            $validar = "Cinvalido";
        } else {
            $sql = hacerConsulta("UPDATE datosempresa SET Nombre = '$nombre', Nit = '$nit', Slogan = '$slogan', FechaLicencia = '$FechaLicencia' WHERE IdEmpresa ='$idusuario'");
            $validar = "Listo";
        }
    }
}

//ENLISTAR USUARIOS
if ($accion == "cargarTablaempresas") {
    $datosUsuario = DevolverUnArreglo("SELECT * FROM datosempresa WHERE Estado = 'ACTIVO' ORDER BY Nombre ASC");
    $validar = $datosUsuario;
}
if ($accion == "cargarTablaCalificaciones") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $datosUsuario = DevolverUnArreglo("SELECT * FROM valorcalif WHERE IdEmpresa = $IdEmpresa ORDER BY NumeroCalif ASC");
    $validar = $datosUsuario;
}
if ($accion == "GuardarCalif") {
    $IdEmpresa = $_REQUEST["IdEmpresa"];
    $Nombre = $_REQUEST["Nombre"];
    $Valor = $_REQUEST["Valor"];

    $consultarusuario = DevolverUnDato("SELECT COUNT(NumeroCalif) FROM valorcalif WHERE NumeroCalif = $Valor AND IdEmpresa = $IdEmpresa");
    $validarcontraseña = DevolverUnDato("SELECT COUNT(valorcalif) FROM valorcalif WHERE valorcalif = '$Nombre' AND IdEmpresa = $IdEmpresa");

    if ($consultarusuario != "0") {
        $validar = "invalido";
    } else {
        if ($validarcontraseña != "0") {
            $validar = "Cinvalido";
        } else {
            hacerConsulta("INSERT INTO valorcalif (IdEmpresa,NumeroCalif,valorcalif) VALUES ($IdEmpresa,$Valor,'$Nombre')");
            $validar = "valido";
        }
    }
}
//listar usuario a editar
    if ($accion == "listarempresas") {
        $idusuario = $_REQUEST["idusuario"];
        $datoUsuario = DevolverUnArreglo("SELECT * FROM datosempresa WHERE IdEmpresa = $idusuario and Estado = 'ACTIVO'");
        $validar = $datoUsuario;
    }

//ELIMINAR USUARIO FORMA LOGICA
    if ($accion == "eliminarEmpresa") {
        $idusuario = $_REQUEST["idusuario"];
        $datoUsuario = hacerConsulta("UPDATE datosempresa SET Estado = 'ELIMINADO' WHERE IdEmpresa = '$idusuario'");
        hacerConsulta("UPDATE usuario SET Estado = 'ELIMINADO' WHERE IdEmpresa = '$idusuario'");
        $validar = $datoUsuario;
    }
//ELIMINAR USUARIO FORMA FISICA
    if ($accion == "eliminarEmpresaFisica") {
        $IdEmpresa = $_REQUEST["IdEmpresa"];
        hacerConsulta("Delete from estilocalificacion where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from sede where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from usuario where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from valorcalif where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from pregunta where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from paquete where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from datosempresa where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from ciudad where IdEmpresa = $IdEmpresa");
        hacerConsulta("Delete from calificacion where IdEmpresa = $IdEmpresa");
        $validar = 'Listo';
    }
    if ($accion == "eliminarCalificacion") {
        $IdValor = $_REQUEST["IdValor"];
        hacerConsulta("DELETE FROM valorcalif WHERE IdValor = $IdValor");
        $validar = "Listo";
    }

    if ($accion == "busquedaempresas") {
        $datousuario = $_REQUEST["datousuario"];
        $datoUsuario = DevolverUnArreglo("SELECT * FROM datosempresa WHERE Estado = 'ACTIVO' AND (Nombre LIKE '%" . "$datousuario" . "%' OR nit LIKE '%" . "$datousuario" . "%' OR Nombre LIKE '%" . "$datousuario" . "%')");
        //echo "SELECT * FROM usuario WHERE NombreCompleto LIKE '%"."$datousuario"."%'";
        $validar = $datoUsuario;
    }
    
    if ($accion == "ValidarCalificacionesEmpresa") {
        $IdEmpresa = $_REQUEST["IdEmpresa"];
        $datoUsuario = DevolverUnDato("select count(*) from calificacion where IdEmpresa = $IdEmpresa ");
        //echo "SELECT * FROM usuario WHERE NombreCompleto LIKE '%"."$datousuario"."%'";
        $validar = $datoUsuario;
    }

//FUNCION PARA GENERAR UNA CONTRASEÑA RANDOM
    function generateRandomString($length = 20) {
        $val = true;
        $val2 = "";
        while ($val) {
            $val2 = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
            $algo = DevolverUnDato("select count(*) from datosempresa where KeyEmpresa = '$val2' ");
            if ($algo <= 0) {
                $val = false;
            }
        }
        return $val2;
//    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

//ECHO PARA MANDAR DATOS POR JSON A LA VISTA
    echo json_encode($validar, JSON_FORCE_OBJECT);
?>
