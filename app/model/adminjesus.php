<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include 'conexion.php';

$year=date("Y");
$month=date("n");
$day= date("j");
 
# Obtenemos el numero de la semana
$semana=date("W",mktime(0,0,0,$month,$day,$year));
 
# Obtenemos el día de la semana de la fecha dada
$diaSemana=date("w",mktime(0,0,0,$month,$day,$year));
 
# el 0 equivale al domingo...
if($diaSemana==0)
    $diaSemana=7;
 
# A la fecha recibida, le restamos el dia de la semana y obtendremos el lunes
$primerDia=date("Y-m-d",mktime(0,0,0,$month,$day-$diaSemana+1,$year));
 
# A la fecha recibida, le sumamos el dia de la semana menos siete y obtendremos el domingo
$ultimoDia=date("Y-m-d",mktime(0,0,0,$month,$day+(7-$diaSemana),$year));

$accion = $_REQUEST["accion"];





if($accion == "tablacalificaciones"){
    $preguntas = DevolverUnArreglo("SELECT * from pregunta where Estado = 'ACTIVO' and IdPregunta IN (select IdPregunta from calificacion where FechaCalif BETWEEN '$primerDia 00:00:00' and '$ultimoDia 23:59:59')");
    $valorcalif = DevolverUnArreglo("SELECT * FROM valorcalif");
    //print_r($valorcalif);
    
    
    $conteoArrayCalif = count($valorcalif);
    $arregloCompleto = array(); 
    foreach ($preguntas as $value) {
        $conteoCalif = 0;
        $consulta =  "select";
        foreach($valorcalif as $vcalif){
            $conteoCalif+=1;
            if($conteoCalif>= $conteoArrayCalif){
                $consulta = $consulta ." (select count(*) from calificacion where NumeroCalif = ".$vcalif['NumeroCalif']." and IdPregunta = ".$value['IdPregunta']." and FechaCalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c".$vcalif['NumeroCalif'];

            }else{
                $consulta = $consulta . " (select count(*) from calificacion where NumeroCalif = ".$vcalif['NumeroCalif']." and IdPregunta = ".$value['IdPregunta']." and FechaCalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c".$vcalif['NumeroCalif'].",";

            }
        }
    
        $consulta = $consulta ." FROM calificacion WHERE IdPregunta = ".$value['IdPregunta'];
        $arreglo = array();
         echo $consulta;
        // exit();
        // print_r($consulta);
        $respuestas = DevolverUnArreglo($consulta);
         print_r($respuestas);
        //exit();
        $consulta = "";
        
         $total = 0;
         if(count($respuestas) > 0){
            $contres = 0;
             foreach($respuestas as $res){
                 $contres = $contres +1;
                 $total =$total + $respuestas[0]['c'.$contres];
             }
             $arreglo2;
             $arreglo2 = array("Pregunta"=>$value['Pregunta']);
             $contres = 0;
            
             foreach($respuestas as $res){
                 $contres = $contres +1;
                
                 array_push($arreglo2, ["c$contres"=>$respuestas[0]['c'.$contres]]);
             }
             array_push($arreglo,$arreglo2);
             array_push($arregloCompleto,$arreglo);
             $validar = $arregloCompleto;
         }
    }
}








//$fecha = array('primerDia' => $primerDia, 'ultimoDia' => $ultimoDia);


/*
if($accion == "tablacalificaciones"){
    //$idempresa = $_REQUEST['idempresa'];
    $preguntas = DevolverUnArreglo("SELECT * from pregunta where estado = 1 and idpregunta IN (select idpregunta from calificacion where idempresa = $idempresa and fechacalif BETWEEN '$primerDia 00:00:00' and '$ultimoDia 23:59:59')");
    // $preguntas = DevolverUnArreglo("SELECT * FROM pregunta join encuesta on (pregunta.idencuesta = encuesta.idencuesta) WHERE encuesta.idempresa = $idempresa");
    //select * from pregunta where idpregunta IN (select idpregunta from calificacion where idempresa = 1 and fechacalif BETWEEN '2019-07-15 00:00:00' and '2019-07-15 23:59:59')
    
    $arregloCompleto = array(); 
    foreach ($preguntas as $value) {

        $arreglo = array();
        $respuestas = DevolverUnArreglo("select 
        (select count(*) from calificacion where numcalif = 4 and idempresa= $idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c4,
        (select count(*) from calificacion where numcalif = 3 and idempresa=$idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c3,
        (select count(*) from calificacion where numcalif = 2 and idempresa=$idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c2 , 
        (select count(*) from calificacion where numcalif = 1 and idempresa=$idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c1 
        FROM calificacion WHERE idempresa=$idempresa and idpregunta = ".$value['idpregunta']);

        if(count($respuestas) > 0){
            $total = $respuestas[0]['c4'] + $respuestas[0]['c3'] + $respuestas[0]['c2'] +$respuestas[0]['c1'];
            
        
            array_push($arreglo, array("Pregunta"=>$value['pregunta'], "c4"=>$respuestas[0]['c4'],"c3"=>$respuestas[0]['c3'],"c2"=>$respuestas[0]['c2'],"c1"=>$respuestas[0]['c1'],"Total"=>$total));
            array_push($arregloCompleto,$arreglo);
            $validar = $arregloCompleto;
        }
    }
}*/










/*
//FUNCION PARA LLENAR LA TABLA DE CALIFICACIONES
if($accion == "tablacalificaciones"){
    $idempresa = $_REQUEST['idempresa'];
    $preguntas = DevolverUnArreglo("SELECT * from pregunta where estado = 1 and idpregunta IN (select idpregunta from calificacion where idempresa = $idempresa and fechacalif BETWEEN '$primerDia 00:00:00' and '$ultimoDia 23:59:59')");
    // $preguntas = DevolverUnArreglo("SELECT * FROM pregunta join encuesta on (pregunta.idencuesta = encuesta.idencuesta) WHERE encuesta.idempresa = $idempresa");
    //select * from pregunta where idpregunta IN (select idpregunta from calificacion where idempresa = 1 and fechacalif BETWEEN '2019-07-15 00:00:00' and '2019-07-15 23:59:59')
    
    $arregloCompleto = array(); 
    foreach ($preguntas as $value) {

        $arreglo = array();
        $respuestas = DevolverUnArreglo("select 
        (select count(*) from calificacion where numcalif = 4 and idempresa= $idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c4,
        (select count(*) from calificacion where numcalif = 3 and idempresa=$idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c3,
        (select count(*) from calificacion where numcalif = 2 and idempresa=$idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c2 , 
        (select count(*) from calificacion where numcalif = 1 and idempresa=$idempresa  and idpregunta = ".$value['idpregunta']." and fechacalif >= '$primerDia 00:00:00' and fechacalif <= '$ultimoDia 23:59:59') as c1 
        FROM calificacion WHERE idempresa=$idempresa and idpregunta = ".$value['idpregunta']);

        if(count($respuestas) > 0){
            $total = $respuestas[0]['c4'] + $respuestas[0]['c3'] + $respuestas[0]['c2'] +$respuestas[0]['c1'];
            
        
            array_push($arreglo, array("Pregunta"=>$value['pregunta'], "c4"=>$respuestas[0]['c4'],"c3"=>$respuestas[0]['c3'],"c2"=>$respuestas[0]['c2'],"c1"=>$respuestas[0]['c1'],"Total"=>$total));
            array_push($arregloCompleto,$arreglo);
            $validar = $arregloCompleto;
        }
    }
}


if($accion == "valorcalificaciones"){
    $idempresa = $_REQUEST['idempresa'];
    $validar = DevolverUnArreglo("SELECT valor FROM `valorcalif` WHERE idempresa = '$idempresa'");

}

if($accion == "fechacalifiacion"){
    $fecha = array('primerDia' => $primerDia, 'ultimoDia' => $ultimoDia);
    $validar = $fecha;

}

//echo $totalc4;



*/
if($accion == "valorcalificaciones"){
    
    $validar = DevolverUnArreglo("SELECT ValorCalif FROM valorcalif");

}

if($accion == "fechacalifiacion"){
    $fecha = array('primerDia' => $primerDia, 'ultimoDia' => $ultimoDia);
    $validar = $fecha;

}

 echo json_encode($validar, JSON_UNESCAPED_UNICODE);

?>