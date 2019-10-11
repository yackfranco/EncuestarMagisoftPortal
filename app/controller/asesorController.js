angular.module('Calificadores').controller('asesorController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$interval', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $interval, $LocalStorage) {

    console.log("pruebasss");

   
    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "Asesor") {
            $state.go('asesor');
        } else {
            $state.go('index');
        }
    } else {
        $state.go('index');
    }
    
    

    var variablemando = false;
    var idpregunta = "";
    var conteo = 0;
    

    validarentradaasesor();
    function validarentradaasesor(){
        datos = { accion: "validarentradaasesor" };
        datos.idusuario = $LocalStorage.idusuarioguardado;
        datos.idempresa =  $LocalStorage.idempresaguardado;

        servicios.asesor(datos).then(function success(response) {
            console.log(response.data);
            if(response.data != 0){

                var objeto = {};
                
                ///UNA PRUBEA
                $scope.enviarcalifi = function()
                {
                    console.log("mmm");
                    encuestas();
                }
                ///BOTON QUE ACTIVA LA CALIFICACION EN CASO DE SER MANUAL
                $scope.calificar = function () {
                    variablemando = true;
                    console.log(variablemando);
                    $scope.esperandocalif = true;
                    $scope.enviadobotonhide = true;
                    $scope.enviadoboton = true;
                    $scope.esperandocalifmensaje = true;                    
                }


                listarencuestasopreguntas();
                //FUNCION PARA TRAER LAS PREGUNTOS O ENCUESTAS//
                function listarencuestasopreguntas() {
                    datos = { accion: "listarencuestapregunta" };
                    datos.idusuario = $LocalStorage.idusuarioguardado;
                    servicios.asesor(datos).then(function success(response) {
                        console.log(response.data.encuesta);
                        
                        metodo = response.data.trabajo[0]['metodo'];
                        console.log(metodo);
                        console.log(response.data.encuesta);
                        metodoasesor(metodo);
                        modo = response.data.trabajo[0]['modo'];
                        //modoasesor(modo);
                        if(response.data.encuesta != ""){
                            objeto = response.data.encuesta;
                        }else{
                            $scope.encuestas = "No hay encuesta"; 
                        }                        
                        encuestas(objeto);
                        console.log(objeto);         
                    });
                }

                ///FUNCION PARA VALIDAR LA CONFIGURACION DEL METODO DEL ASESOR
                function metodoasesor(metodo){
                    if(metodo == "Manual"){                        
                        $scope.esperandocalif = false;
                        $scope.enviadobotonhide = false;
                        $scope.enviadoboton = false;
                        $scope.esperandocalifmensaje = false;  
                        variablemando = false;                      
                    }else{
                        variablemando = true;
                        $scope.esperandocalif = true;
                        $scope.enviadobotonhide = true;
                        $scope.enviadoboton = true;
                        $scope.esperandocalifmensaje = true;
                    }
                }


                
            
                //FUNCION QUE REALIZA EL CAMBIO DE PREGUNTAS
                function encuestas(){

                    if (conteo < objeto.length) {
                        $scope.encuestas = objeto[conteo].pregunta;   
                        console.log("imprimir pregutna", $scope.encuestas);                    
                        idpregunta = objeto[conteo].idpregunta;   
                        console.log(idpregunta);
                        console.log("avanza a la siguiente pregunta"); 
                                       
                    }else{                      
                        console.log("llegue aqui");                  
                        conteo = 0;
                        variablemando = false;
                        $scope.esperandocalifmensaje1 = true;
                        $scope.esperandocalifmensaje = false;
                        $scope.enviadobotonhide = true;
                        $scope.enviadoboton = false;
                        $scope.enviadocalif= true;
                        $scope.esperandocalif = false;
                        $scope.encuestas = "Finalizo la calificación";
                        
                        var interval = $interval(function () {
                            
                            $interval.cancel(interval);

                            if (metodo == "Manual") {
                                encuestas();                                
                                metodoasesor(metodo);
                                variablemando = false;
                                $scope.enviadocalif= false;
                                $scope.esperandocalifmensaje1 = false;

                               // mandocalificar("1");
                            } else{
                                encuestas()
                                //metodoasesor(metodo);
                                variablemando = true;
                                $scope.esperandocalif = true;
                                $scope.enviadobotonhide = true;
                                $scope.enviadoboton = true;
                                $scope.enviadocalif= false;
                                $scope.esperandocalifmensaje = true;
                                $scope.esperandocalifmensaje1 = false;
                                ///location.reload();
                                
                                //mandocalificar(validarmando = true);
                            }
                        }, 5000);
                    }
                }

                console.log(variablemando);
               
                listardatosusuario();
                function listardatosusuario (){
                    datos = { accion: "listardatosusuario" };
                    datos.idusuario = $LocalStorage.idusuarioguardado;
                    datos.idempresa =  $LocalStorage.idempresaguardado;
                    servicios.asesor(datos).then(function success(response) {
                        console.log(response.data);
                        $scope.nombreasesor = response.data.usuario[0].nombre
                        $scope.usuarioasesor = response.data.usuario[0].nombreusuario;
                        $scope.apellidoasesor = response.data.usuario[0].apellido;
                        $scope.empresaasesor = response.data.empresa[0].nombreempresa;
                    });
                }
            }else{
                $scope.enviadobotonhide = true;
                $scope.encuestas = "No tiene encuestas configuradas";
            }
        });
    }
    


    $(document).bind('keydown', 'alt+m', function () {
        if(variablemando){
            subircalificacion(idpregunta,1);
            conteo += 1;
            console.log("conteo", conteo);
            document.getElementById("simularenviar").click();
        }
       
    });

    $(document).bind('keydown', 'alt+r', function () {
        if(variablemando){
            subircalificacion(idpregunta,2);
            conteo += 1;
            console.log("conteo", conteo);
            document.getElementById("simularenviar").click();
        }
    });

    $(document).bind('keydown', 'alt+b', function () {
        
        if(variablemando){
            subircalificacion(idpregunta,3);
            conteo += 1;
            console.log("conteo", conteo);
            //encuestas();
            document.getElementById("simularenviar").click();
            //enviarcalifi();
        }
    });

    $(document).bind('keydown', 'alt+w', function () {
        if(variablemando){
            subircalificacion(idpregunta,4);
            conteo += 1;
            console.log("conteo", conteo);
            document.getElementById("simularenviar").click();
        }
    });    


    function subircalificacion (idpregunta, calificacion){
        
        datos.accion = "subircalificacion";
        datos.idusuario = $LocalStorage.idusuarioguardado;
        datos.idempresa =  $LocalStorage.idempresaguardado;
        datos.idsede = $LocalStorage.idsedeguardado;
        datos.calificacion = calificacion;
        datos.idpregunta = idpregunta;
        console.log ("idusuario",$LocalStorage.idusuarioguardado, "idempresa",$LocalStorage.idempresaguardado, "idsede", $LocalStorage.idsedeguardado, "calificacion", calificacion,  "idpregunta", idpregunta );
        
        servicios.asesor(datos).then(function success(response) {
            console.log(response);            
        });
    }





} 
