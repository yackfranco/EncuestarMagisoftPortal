<?php // header('Content-Type: application/json');
//header("Access-Control-Allow-Origin: *");
//include 'conexion.php';
//
//$accion = $_REQUEST["accion"];

//if ($accion == "cargarDatos") {
//    $cont = DevolverUnDato("select count(*) from configtv");
//    if ($cont > 0) {
//        $resp = DevolverUnArreglo("SELECT * from configtv");
//    } else {
//        $resp = "sin datos";
//    }
//
//    $validar = array('respuesta' => $resp);
//}

//if ($accion == "guardarDatos") {
//    $MensajeR = $_REQUEST["MensajeR"];
//    $Nvideo = $_REQUEST["Nvideo"];
//    $Fuente = $_REQUEST["Fuente"];
//    $Imagen = addslashes(file_get_contents($_FILES['Imagen']['tmp_name']));
//
//
//    try {
//
//        $cont = DevolverUnDato("select count(*) from configtv");
//        if ($cont > 0) {
//            hacerConsulta("Update configtv set mensaje = '$MensajeR', video = '$Nvideo', TamanoLetra = '$Fuente', logo = '$Imagen'");
//        } else {
//            $consulta = "insert into configtv (mensaje,video,TamanoLetra,logo) values ('$MensajeR','$Nvideo','$Fuente','$Imagen')";
//            hacerConsulta($consulta);
//        }
//        $resp = "Guardado Correctamente";
//    } catch (Exception $ex) {
//        $resp = "Error al Guardar Configuracion de Los Turnos Perdidos";
//    }
//
//    $validar = array('respuesta' => $resp);
//}
//
//
//echo json_encode($validar, JSON_FORCE_OBJECT);
?>

<!DOCTYPE html> 
<html>
    <head>
        <script src="../../node_modules/push.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="container">
            <button id="btn">Lanzar</button>
        </div>
        <script type="text/javascript">
//            window.onload = function () {
//                Push.Permission.request(onGranted, onDenied);
//            };
            
                
                Push.create("Notificacion nigga", {
                    body: 'notificaciones vergas',
                    icon: '../../image/ActivarAlarma.png',
                    timeout: 4000,
                    vibrate: [100,100,100],
                    onClick: function(){
                        alert('Clic en la notificacion');
                    }

                });
            
        </script>
    </body>

</html>
