<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include 'conexion.php';

$accion = $_REQUEST["accion"];

if ($accion == "CargarServicios") {
    $arreglo = DevolverUnArreglo("SELECT * from servicio where Eliminado = 0 and EstadoSub = 'SERVICIO'");
    if ($arreglo == null) {
        http_response_code(401);
        $validar = [];
    } else {
//    $validar = array('id' => $arreglo);
        $validar = array('respuesta' => $arreglo);
    }
}

if ($accion == "SUBServicioRelacionados"){
    $idservicio = $_REQUEST["idservicio"];

    $arreglo = DevolverUnArreglo("select servicio.idservicio,servicio.Servicio as servicio,relacionservicios.idrelacionservicio from relacionservicios JOIN servicio on (servicio.IdServicio = relacionservicios.subservicio) where relacionservicios.servicio = $idservicio and relacionservicios.subservicio != $idservicio and relacionservicios.Eliminado = 0");
    if ($arreglo == null) {
        http_response_code(401);
        $validar = [];
    } else {
        $validar = array('respuesta' => $arreglo);
    }
}

if ($accion == "SUBServicioNoRelacionados") {
    $idservicio = $_REQUEST["idservicio"];
//SELECT servicio.Servicio from relacionservicios join servicio on (relacionservicios.subservicio = servicio.IdServicio) where relacionservicios.estado = 0 and relacionservicios.servicio = 27
    // where NOT EXISTS (select relacionservicios.subservicio from relacionservicios where relacionservicios.subservicio = 27)
    $arreglo = DevolverUnArreglo("SELECT servicio.idservicio,servicio.servicio from servicio where EstadoSub = 'SUBSERVICIO' AND IdServicio NOT IN (select relacionservicios.subservicio from relacionservicios where relacionservicios.subservicio != $idservicio  and relacionservicios.servicio = $idservicio and relacionservicios.Eliminado = 0)");
    if ($arreglo == null) {
        http_response_code(401);
        $validar = [];
    } else {
        $validar = array('respuesta' => $arreglo);
    }
}

if ($accion == "AgregarServicioRelacionado") {
    $idservicio = $_REQUEST["idservicio"];
    $idSubservicio = $_REQUEST["idSubservicio"];

    hacerConsulta("insert into relacionservicios (servicio, subservicio) values ($idservicio,$idSubservicio) ");
    $validar = array('respuesta' => "Relacionado Correctamente");
}

if ($accion == "EliminarServicioRelacionado") {
    $idservicio = $_REQUEST["idservicio"];
    $idSubservicio = $_REQUEST["idSubservicio"];
    $idrelacionServicio = $_REQUEST["idrelacionServicio"];

    $contaridrelacionAuditoria = DevolverUnDato("select count(*) from auditoria where idrelacionServicio = $idrelacionServicio");
    if ($contaridrelacionAuditoria <= 0) {
        hacerConsulta("delete from relacionservicios where idrelacionServicio = $idrelacionServicio");
    } else {
        hacerConsulta("update relacionservicios set Eliminado = 1 where idrelacionServicio = $idrelacionServicio");
    }
    $validar = array('respuesta' => "Eliminado Correctamente");
}
//
//if ($accion == "TraerTurnosEspera") {
//    $nombreServicio = $_REQUEST["IdServicio"];
//    $dato = DevolverUnDato("select count(*) from tablatemporal where idservicio = $nombreServicio");
//    $validar = array('respuesta' => $dato);
//}
//
//if ($accion == "EliminarTurnoEspera") {
//    $nombreServicio = $_REQUEST["IdServicio"];
//    hacerConsulta("delete from tablatemporal where idservicio = $nombreServicio");
//    $validar = array('respuesta' => "Eliminado");
//}
//
//if ($accion == "guardarServicio") {
//    $nombreServicio = $_REQUEST["nombreServicio"];
//    $Prefijo = $_REQUEST["Prefijo"];
//    $ConteoMinimo = $_REQUEST["ConteoMinimo"];
//    $ConteoMaximo = $_REQUEST["ConteoMaximo"];
//    $Secuencia = $_REQUEST["Secuencia"];
//    $prioridad = $_REQUEST["prioridad"];
//    $atril = $_REQUEST["atril"];
//    $tv = $_REQUEST["tv"];
//    $nivel = $_REQUEST["nivel"];
//
//
//    if (strlen($Prefijo) > 3) {
//        $validar = array('respuesta' => "El prefijo solo puede tener 3 caracteres como maximo");
//    }
////Pregunta si el numero a insertar es de tipo int
//    else if (is_numeric($ConteoMinimo) && is_numeric($ConteoMaximo) && is_numeric($prioridad) && is_numeric($Secuencia)) {
//        if (DevolverUnDato("select count(*) from servicio where servicio = '$nombreServicio'") > 0 || DevolverUnDato("select count(*) from servicio where prefijo = '$Prefijo'") > 0) {
//            $validar = array('respuesta' => "El Servicio o el Prefijo ya se encuentra en Base de Datos");
//        } else {
//            try {
//                $idServicio = InsertDevolviendoID("insert into servicio (Prefijo, Servicio,Cont_min, Cont_max, Secuencia, Prioridad, LLamadoTv, Color,ColorLetra,atril,EstadoSub) "
//                        . "values ('$Prefijo','$nombreServicio',$ConteoMinimo,$ConteoMaximo,$Secuencia,$prioridad,$tv,'','',$atril,'$nivel')");
//
//                if ($nivel == "SERVICIO") {
//                    hacerConsulta("insert into relacionservicios (servicio, subservicio) "
//                            . "values ($idServicio,$idServicio)");
//                }
//
//                $validar = array('respuesta' => "Registro Guardado Correctamente");
//            } catch (Exception $ex) {
//                
//            }
//        }
//    } else {
//        $validar = array('respuesta' => "El Dato ingresado debe de ser un Numero");
//    }
//}
//
//
//if ($accion == "EliminarServicio") {
//    $IdServicio = $_REQUEST["IdServicio"];
//    try {
//        hacerConsulta("update servicio set Eliminado = 1 where IdServicio = $IdServicio");
//        $validar = array('respuesta' => 'Eliminado');
//    } catch (Exception $ex) {
//        $validar = array('respuesta' => 'error al eliminar registro en la BD');
//    }
//}
//
//if ($accion == "TraerDatosEditar") {
//    $IdServicio = $_REQUEST["IdServicio"];
//    try {
//        $arreglo = DevolverUnArreglo("select * from servicio where IdServicio = $IdServicio");
//        $validar = array('respuesta' => $arreglo);
//    } catch (Exception $ex) {
//        $validar = array('respuesta' => 'Error al traer datos de Editar');
//    }
//}
//
//if ($accion == "editarServicio") {
//    $IdServicio = $_REQUEST["IdServicio"];
//    $nombreServicio = $_REQUEST["Servicio"];
//    $Prefijo = $_REQUEST["Prefijo"];
//    $Cmin = $_REQUEST["Cmin"];
//    $Cmax = $_REQUEST["Cmax"];
////    $Secuencia = $_REQUEST["Secuencia"];
//    $prioridad = $_REQUEST["prioridad"];
//    $tv = $_REQUEST["tv"];
//    $atril = $_REQUEST["atril"];
//    try {
//        hacerConsulta("update servicio set Prefijo='$Prefijo', Servicio='$nombreServicio',Cont_min=$Cmin, Cont_max=$Cmax,"
//                . " Prioridad=$prioridad, LLamadoTv=$tv, atril = $atril where IdServicio = $IdServicio ");
////                . " Secuencia=$Secuencia, Prioridad=$prioridad, LLamadoTv=$tv where IdServicio = $IdServicio ");
//        $validar = array('respuesta' => "Editado Correctamente");
//    } catch (Exception $ex) {
//        $validar = array('respuesta' => 'Error al traer datos de Editar');
//    }
//}



echo json_encode($validar, JSON_FORCE_OBJECT);
?>
