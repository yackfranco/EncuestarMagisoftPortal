angular.module('Calificadores').controller('configusuariosController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage) {
    
     $scope.botonconfig = "botonescontorno";
    $scope.botonconfigtxt = "botonestxt";
    $scope.botonconfigfa = "botonesfa";
    if($LocalStorage.usuarioguardado != undefined){
        if($LocalStorage.rolguardado == "ADMINISTRADOR"){
            $state.go('configusuarios');
        } else {
            $state.go('index');
        }
    } else{
        $state.go('index');
    }

    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;
    
   listarusuario()

    var idusuario1;
    var configpreencuesta1;
    var modo1;
    var metodo1;
    var ventana;

    function comprobarempty(objeto) {
        if (objeto == "" || objeto == undefined) {
            return true;
        } else {
            return false;
        }
    }


   function mensajemodal(mensaje, titulo = "ATENCIÓN") {
        swal({
            title: titulo,
            text: mensaje
        },
                function () {
                    swal.close();
                    $interval.cancel(interval);
                });
        var interval = $interval(function () {
            swal.close();
            $interval.cancel(interval);
        }, 3000);
    }


    function listarusuario(){
        datos = {accion: "listarusuario", IdEmpresa: $LocalStorage.IdEmpresa};
        console.log("listarusuario")        
        servicios.configusuarios(datos).then(function success(response) {
            console.log(response);
            $scope.usuarios = response.data;
        });
    }

 
    $scope.preguntaEncuesta = function(modo){
        modo1 = modo;
        console.log(modo1);

        if(modo1 == "Mono"){
            document.getElementById("PE").innerHTML = "Elegir Pregunta: ";
            $scope.OpcionSeleccion = "Pregunta";
            datos = {accion: "listarpreguntas", IdEmpresa: $LocalStorage.IdEmpresa};
            servicios.configusuarios(datos).then(function success(response) {
            console.log(response);
            $scope.preguntas = response.data;
            });

        }else{
            document.getElementById("PE").innerHTML = "Elegir Encuesta: ";
            $scope.OpcionSeleccion = "Encuesta";
            datos = {accion: "listarencuestas", IdEmpresa: $LocalStorage.IdEmpresa};
            servicios.configusuarios(datos).then(function success(response) {
            console.log(response);
            $scope.encuestas = response.data;
            });
        }
    }
    

    
    $scope.elegirUsuario = function(idusuario){
        
        idusuario1 = idusuario;
        console.log("idusuario",idusuario1);
    }

    $scope.preguntaEncuestaid = function(configpreencuesta){
        configpreencuesta1 = configpreencuesta;
        console.log("idpregunta o Encuesta",configpreencuesta1);
    }
    
    $scope.checkAll = function(checkValue){
        if(checkValue == true){
            ventana = 1;
        }else{
            ventana = 0;
        }
        
        console.log("ventanaminimizada",ventana);
    }

    $scope.elegirmetodo = function(metodo){
        metodo1 = metodo;
        console.log("metodo",metodo1);
    }

    $scope.submitconfigusuarios = function () {

        console.log(idusuario1, configpreencuesta1, modo1, metodo1);
        
        //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
       if(comprobarempty(idusuario1)
            || comprobarempty(modo1)
            || comprobarempty(metodo1)
            || comprobarempty(configpreencuesta1))
            {
             mensajemodal("Debe Llenar Todos Los Campos");
            }else{
                if(modo1 == "Multi"){
                    idencuesta = configpreencuesta1;
                    idpregunta = 0;
                }else{
                    idpregunta = configpreencuesta1;
                    idencuesta = 0;
                }
            datos = {accion: "subtmiconfiguser",IdEmpresa: $LocalStorage.IdEmpresa,idusuario: idusuario1, modo: modo1, metodo: metodo1, idpregunta: idpregunta, idencuesta: idencuesta, ventana: ventana};
            
            console.log(datos);

            servicios.configusuarios(datos).then(function success(response) {
            console.log(response);
            
            mensajemodal("Configuración Guardada Con Éxito");
            $scope.IdUsuario = ""; 
            $scope.modo = "";
            $scope.metodo = "";
            $scope.configpreencuesta = "";
            });
        }
    }
}
