<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../src/Exception.php';
require '../../src/PHPMailer.php';
require '../../src/SMTP.php';

$accion = $_REQUEST["accion"];

//FUNCION PARA INGRESAR EN EL SISTEMA.
if ($accion == "entrar") {
    $usuario = $_REQUEST["usuario"];
    $contrasena = $_REQUEST["contrasena"];
    $contrasenamd5 = md5($contrasena);

    $validarlogin = DevolverUnDato("SELECT COUNT(*) FROM usuario WHERE NombreUsuario = '$usuario'");
    $validarlogincontra = DevolverUnDato("SELECT COUNT(*) FROM usuario WHERE NombreUsuario = '$usuario' AND Contrasena = '$contrasenamd5'");

    if ($validarlogin == 0) {
        $validar = "usuariomal";
    } else if ($validarlogincontra == 0) {
        $validar = "contrasenamal";
    } else {
        $datosUsuario = DevolverUnArreglo("SELECT * FROM usuario WHERE NombreUsuario = '$usuario' AND Contrasena ='$contrasenamd5'");
        $IdEmpresa = $datosUsuario[0]['IdEmpresa'];
        $fechaactual = DevolverUnDato("select CURDATE()");
        $FechaLicencia = DevolverUnDato("select FechaLicencia from datosempresa where IdEmpresa = $IdEmpresa");
        $estadoUsuario = DevolverUnDato("SELECT Estado FROM usuario WHERE IdUsuario = " . $datosUsuario[0]['IdUsuario']);

        if ($FechaLicencia < $fechaactual) {
            $validar = array('Estado' => 'Licencia');
        } else {
            if (count($datosUsuario) > 0) {
                $validar = array('Respuesta' => $datosUsuario, 'Estado' => 'Logeado', 'EstadoUsuario' => $estadoUsuario);
            } else {
                $validar = array('Estado' => 'NoLogeado');
            }
        }
    }
}

if ($accion == "ValidarRol") {
    $usuario = $_REQUEST["usuario"];
    $arreglo = DevolverUnDato("SELECT rol from usuario where NombreUsuario = '$usuario'");
    if ($arreglo != "") {
        $validar = $arreglo;
    } else {
        $validar = "El Usuario No Existe";
    }
}

if ($accion == "listarusuarios") {
    $consultaradmin = DevolverUnArreglo("SELECT * FROM usuario WHERE Rol ='ADMINISTRADOR' AND Estado ='ACTIVO'");
    $validar = $consultaradmin;
}

if ($accion == "traerusuario") {
    $idusuario = $_REQUEST["idusuario"];
    $cosultausuario = DevolverUnDato("SELECT NombreUsuario FROM usuario WHERE IdUsuario = '$idusuario'");
    $validar = $cosultausuario;
}

//FUNCION PARA GENERAR UNA CONTRASEÑA RANDOM
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

//FUNCION PARA RECUPERAR CIONTRASENA
$mail = new PHPMailer(true);
if ($accion == "RecuperarContrasenalogin") {
    include 'ConexionNubeCorreo.php';

    $usuario = $_REQUEST["usuario"];
   
    $usuario = DevolverUnArreglo("select * from usuario where NombreUsuario = '$usuario' and Rol = 'ADMINISTRADOR'");
    if (count($usuario)<=0) {
        $validar = array('respuesta' => "El Usuario no es ADMINISTRADOR");
        echo json_encode($validar, JSON_FORCE_OBJECT);
        exit();
    }
//    print_r($usuario);
//    exit();
    $idusuario = $usuario[0]["IdUsuario"];
    $contrarandom = generateRandomString();
    $correo = DevolverUnDato("select Correo from usuario where IdUsuario = $idusuario");

//    print_r($correo);

    $datosnube = DevolverUnArreglo3("select * from correo");

//    print_r($datosnube);

    foreach ($datosnube as $value) {
//        print_r($value);
        $CorreoNube = $value['Correo'];
        $ContraNube = $value['Contrasena'];
    }
echo "correo: ".$CorreoNube;
echo "Contrasena: ".$ContraNube;

    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
//    $mail->Host = 'ingetronik.com';  // Specify main and backup SMTP servers
    $mail->CharSet = 'UTF-8';
//        $mail->Host = "ingetronik.com";
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $CorreoNube;                 // SMTP username
//    $mail->Username
    $mail->Password = $ContraNube;                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    //Recipients 
    $mail->setFrom($CorreoNube, 'Ingetronik');
    $mail->addAddress($correo, '');     // Add a recipient
//  
    //Content
    $imagenIngetronik = "https://images.pastatic.com/Content/LogosAdvertisements/15715839/7-7137.jpg";
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'RECUPERACIÓN DE CONTRASEÑA';
    $mail->Body = "<html>" .
            "<head>" .
            "    <title>TODO supply a title</title>" .
            "  <meta charset=\"UTF-8\">" .
            "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">" .
            "</head>" .
            "<body style=\"font-family:'Century Gothic'\">" .
            "<div>" .
            "<center><h1>RECUPERACIÓN DE CONTRASEÑA</h1></center> " .
            "</div>" .
            "<div>" .
            "  <h4><b>Estimado usuario,</b></h4>" .
            "   Hemos generado una nueva contraseña" .
            "  <br>" .
            "  <br>" .
            "  Contraseña: <b>" . $contrarandom . "</b>" .
            " <h4>Gracias por usar nuestro Software Magisoft</h4>" .
            "</div>" .
            "  <div>" .
            "   <hr/>" .
            "  <center> <img src=\"" . $imagenIngetronik . "\"  width=\"130\" height=\"70\"> </center><br>" .
            "   <center> <a href=\"www.ingetronik.com\">www.ingetronik.com</a>  </center>" .
            " </div>" .
            " </body>" .
            "</html>";
    //$mail->AltBody = 'Digame ingeniero';
    $mail->send();
//    echo 'Message has been sent';

    try {
        $consulta = "update usuario set Contrasena = '" . md5($contrarandom) . "', EstadoContrasena = 'RECUPERAR' where IdUsuario = $idusuario ";
        hacerConsulta($consulta);
        $validar = array('respuesta' => "Editado Correctamente");
    } catch (Exception $ex) {
        $validar = array('respuesta' => 'Error al traer datos de Editar');
    }
}

if ($accion == "cambiarcontra") {
    $Contranueva = $_REQUEST["contranueva"];
    $NombreUsuario = $_REQUEST["nombreusuario"];
    $idusuario = DevolverUnDato("select IdUsuario from usuario where NombreUsuario = '$NombreUsuario'");

    $consulta = "update usuario set Contrasena = '" . md5($Contranueva) . "', EstadoContrasena = 'NORMAL' where IdUsuario = $idusuario";
    hacerConsulta($consulta);
    $validar = array('respuesta' => "Listo");
}

echo json_encode($validar, JSON_FORCE_OBJECT);
?>
