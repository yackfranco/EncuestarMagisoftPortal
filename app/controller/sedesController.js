angular.module('Calificadores').controller('sedesController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage) {
    
    if($LocalStorage.usuarioguardado != undefined){
        if($LocalStorage.rolguardado == "ADMINISTRADOR"){
            $state.go('sedes');
        } else {
            $state.go('index');
        }
    } else{
        $state.go('index');
    }
    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;
    
    //FUNCION PARA VALIDAR CAMPOS VACIOS
    function comprobarempty(objeto) {
        if (objeto == "" || objeto == undefined) {
            return true;
        } else {
            return false;
        }
    }
    document.getE
    //FUNCION PARA LLAMAR A LA MODAL
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

    //rutina para validar que solo ingresen letras
    function validarSoloLetra(string) {
        if (/[Ã±Ã‘'Ã¡Ã©ÃÃ³ÃºÃÃ‰ÃÃ“ÃšÃ$#´Ã¨Ã¬Ã²Ã¹Ã€ÃˆÃŒÃ’Ã™Ã¢ÃªÃ®Ã´Ã»Ã‚ÃŠÃŽÃ”Ã›Ã‘Ã±Ã¤Ã«Ã¯Ã¶Ã¼Ã„Ã‹ÃÃ–Ãœ\s\t|]/.test(string))
        {
           return true;
            /*
            document.getElementById(campo).select();
            document.getElementById(campo).focus();*/
        }
    }


    listarciudades();

    function listarciudades() {
        datos = {accion: "listarciudades", IdEmpresa: $LocalStorage.IdEmpresa};        
        servicios.sedes(datos).then(function success(response) {
           $scope.listaciudad = response.data;
        });
    }
    
    
    listarciudadselect();
    function listarciudadselect() {
        datos = {accion: "listarciudadselect", IdEmpresa: $LocalStorage.IdEmpresa};        
        servicios.sedes(datos).then(function success(response) {
            //console.log(response);
           $scope.listarciudades = response.data;
        });
    }

    ///TOMA LOS DATOS DE DEL FORMULARIO SEDE
    $scope.ciudad = {};
    //FUNCION PARA INGRESAR LAS SEDES
    $scope.ingresarciudad = function(){
        if(comprobarempty($scope.ciudad["nombreciudad"])){
            mensajemodal("Debe De Llenar Los Campos");
        } else{
            if(validarSoloLetra($scope.ciudad["nombreciudad"])){
                mensajemodal("Los Campos no deben contener los siguientes caracteres $ ´ #");
            }else{
                $scope.ciudad.accion = "ingresarciudad";
                $scope.ciudad.IdEmpresa = $LocalStorage.IdEmpresa;
                servicios.sedes($scope.ciudad).then(function success(response) {
                console.log(response);
                if(response["data"] == "invalido"){
                    mensajemodal("La Ciudad: " + $scope.ciudad["nombreciudad"] +" Ya Existe");
                }else{
                    mensajemodal("La Ciudad: " + $scope.ciudad["nombreciudad"] +" Fue Registrada Con Éxito");
                    //LIMPIAR DATOS DE LOS INPUTS
                    $scope.ciudad = {};
                    listarciudades();
                    listarciudadselect();
                }
                });
            }    
        }
    }


    ///FUNCION PARA TRAER LA SEDE A EDITAR
    $scope.traerciudad = function(idciudad){
        datos = {accion: "traerciudad"};
        datos.idciudad = idciudad;
        console.log(datos);
    
        servicios.sedes(datos).then(function success(response) {       
            $scope.editarciudad = response.data[0];
        });
    }


    //FUNCION PARA EDITAR LA SEDE
    $scope.editarlaciudad = function(IdCiudad){
        if(comprobarempty($scope.editarciudad["NombreCiudad"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        }else{
            if(validarSoloLetra($scope.editarciudad["NombreCiudad"])){
                mensajemodal("Los Campos no deben contener los siguientes caracteres $ ´ #");
            }else{
                $scope.editarciudad.accion = "editarciudad";
                $scope.editarciudad.IdCiudad = IdCiudad;
                servicios.sedes($scope.editarciudad).then(function success(response) {
                if(response.data == "invalido"){
                    mensajemodal("La ciudad ya se ecuentra registrada");    
                }else{
                    console.log(response.data);
                    mensajemodal("Ciudad Editada Con Éxito");
                    listarciudades()
                }                
                });
            }
        }
    }


    //FUNCION PARA TOMAR EL ID DE LA SEDE A ELIMINAR
    var idEliminarciudad="";
    $scope.listarciudadeliminar = function (IdCiudad) {
        idEliminarciudad = IdCiudad;
        console.log(idEliminarciudad);
    }


    //FUNCION PARA ELIMINAR SEDE
    $scope.eliminarciudad = function () {
        datos = {accion: "eliminarciudad", idciudad: idEliminarciudad};
        console.log(idEliminarciudad);
        servicios.sedes(datos).then(function success(response) {
            console.log(response);
            if(response.data == "noeliminar"){
                mensajemodal("La Ciudad No Se Puede Eliminar Contiene Registros"); 
            }else{
                mensajemodal("La Ciudad Fue Eliminada Con Éxito"); 
                listarciudades()
            }
            
        });
    }
    var idciudadselec ="";
    $scope.elegirCiudad = function (IdCiudad) {
        idciudadselec = IdCiudad;
        console.log(idciudadselec);
        datos = {accion: "listarsedes", IdCiudad: idciudadselec, IdEmpresa: $LocalStorage.IdEmpresa};        
        servicios.sedes(datos).then(function success(response) {
            console.log(response);
            $scope.listarsedes = response.data;
        });
    }


    
    $scope.editarsede = function (IdSede) {
        datos = {accion: "editarsede", IdSede: IdSede};        
        servicios.sedes(datos).then(function success(response) {           
           $scope.editsede = response.data[0];
        });
    }

    $scope.editarlasede = function (IdSede) {    
        if(comprobarempty($scope.editsede["NombreSede"])){
            mensajemodal("Debe de Llenar El Campo");
        } else{
            if(validarSoloLetra($scope.editsede["NombreSede"])){
                mensajemodal("Los Campos no deben contener los siguientes caracteres $ ´ #");
            }else{
                datos.NombreSede = $scope.NombreSede; 
                datos = {accion: "editarlasede", IdSede: IdSede, NombreSede: $scope.editsede["NombreSede"]};        
                servicios.sedes(datos).then(function success(response) { 
                if(response.data == "invalido"){
                    mensajemodal("La Sede Ingresada Ya Existe");    
                }else{
                    mensajemodal("La Sede Fue Editada Con Exito");
                    $scope.elegirCiudad(response.data);    
                }        
                });
            }   
        }
    }

    $scope.ingresarsede = function () {
        if(comprobarempty(idciudadselec)){
            mensajemodal("Debe Seleccionar Primero La Ciudad");
        }else{

            if(comprobarempty($scope.sede["NombreSede"])){
                mensajemodal("Debe llenar el campo");
            }else{
                if(validarSoloLetra($scope.sede["NombreSede"])){
                    mensajemodal("Los Campos no deben contener los siguientes caracteres $ ´ #");
                }else{
                    datos = {accion: "ingresarsede", NombreSede	: $scope.sede["NombreSede"], IdCiudad: idciudadselec};
                    servicios.sedes(datos).then(function success(response) {
                    if(response.data == "invalido"){
                    mensajemodal("La Sede Ingresada Ya Existe");
                    }else{
                    mensajemodal("La Sede Fue Registrada Con Exito");
                    $scope.elegirCiudad(response.data);
                    $scope.sede.NombreSede = "";
                    }
                    });
                }
            }            
        }
    }

    var idsedeeliminar="";
    $scope.listarsedeeliminar = function (IdSede) {
        idsedeeliminar = IdSede;
        console.log(idsedeeliminar);
    }
    
    //FUNCION PARA ELIMINAR SEDE
    $scope.eliminarsede = function () {
        datos = {accion: "eliminarsede", IdSede: idsedeeliminar};
        console.log(idsedeeliminar);
        servicios.sedes(datos).then(function success(response) {
            console.log(response);
            if(response.data == "noeliminar"){
                mensajemodal("La Sede No Se Puede Eliminar Contiene Registros"); 
            }else{
                mensajemodal("La Sede Fue Eliminada Con Éxito"); 
                $scope.elegirCiudad(idciudadselec);
            }
        });
    }
}   
