angular.module('Calificadores').controller('empresaController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage) {
    
    console.log("empressaaaa");
    
    //OBETENER LA FECHA DE MYSQL
    fechamysql();
    function fechamysql(){
        datos = {accion: "fechamysql"};
        servicios.empresa(datos).then(function success(response) {
           
            $scope.fechaActual = response.data;
            
        });
    }

    //FUNCION PARA CONEVRTIR UN OBJETO FECHA
    function convertDatePickerTimeToMySQLTime(str) {
        var month, day, year, hours, minutes, seconds;
        var date = new Date(str),
                month = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
        hours = ("0" + date.getHours()).slice(-2);
        minutes = ("0" + date.getMinutes()).slice(-2);
        seconds = ("0" + date.getSeconds()).slice(-2);

        var mySQLDate = [date.getFullYear(), month, day].join("-");
        var mySQLTime = [hours, minutes, seconds].join(":");
        return [mySQLDate, mySQLTime].join(" ");
    }

    //FUNCION PARA VALIDAR EL CORREO ELECTRONICO
    function validar_email(email) {
        var patron = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return patron.test(email) ? true : false;
    }

    //FUNCION PARA VALIDAR CAMPOS VACIOS
    function comprobarempty(objeto) {
        if (objeto == "" || objeto == undefined) {
            return true;
        } else {
            return false;
        }
    }

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

    llenarTablaEmpresa();
    //FUNCION PARA LLENAR TABLA EMPRESA
    function llenarTablaEmpresa() {
        datos = {accion: "cargarTablaEmpresa"};
        servicios.empresa(datos).then(function success(response) {
            $scope.empresaslistas = response.data;
            console.log(response.data);
            
            
        });
    }

    ///TOMA LOS DATOS DE DEL FORMULARIO EMPRESA
    $scope.empresa = {};
    $scope.submitempresa = function () {
        //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
        if(comprobarempty($scope.empresa["nombreempresa"])
        || comprobarempty($scope.empresa["nit"])
        || comprobarempty($scope.empresa["slogan"])
        || comprobarempty($scope.empresa["licencia"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        }else{
            $fechalicencia = convertDatePickerTimeToMySQLTime($scope.empresa["licencia"]);
            $scope.empresa.accion = "ingresarempresa";
            $scope.empresa.fechalicencia = $fechalicencia;
            servicios.empresa($scope.empresa).then(function success(response) {   
            if(response["data"] == "invalido"){
                mensajemodal("La Empresa: " + $scope.empresa["nombreempresa"] +" Ya Existe");
            }else{
                mensajemodal("La Empresa: " + $scope.empresa["nombreempresa"] +" Fue Registrado Con Éxito");
                //LIMPIAR DATOS DE LOS INPUTS
                $scope.empresa = {};
                llenarTablaEmpresa();
                console.log("da");
                    }
                });
            }
    }

     //FUNCION PARA ENLISTAR LA EMPRESA A EDITAR
    $scope.listarempresa = function (idempresa) {
        datos = {accion: "listarempresa"};
        datos.idempresa = idempresa;
        servicios.empresa(datos).then(function success(response) {
            $scope.editempresa = response.data[0];
            console.log("licencia: " + response.data[0]['licencia']);
        });
    }

    //FUNCION PARA EDITAR UNA EMPRESA
    $scope.editarempresa = function (idempresa) {
        idempresa1 = idempresa;
        if(comprobarempty($scope.editempresa["nombreempresa"])
        || comprobarempty($scope.editempresa["nit"])
        || comprobarempty($scope.editempresa["slogan"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        }else{
            fechalicencia = "";
            if(comprobarempty($scope.editempresa.fachelicencia)){
                fechalicencia1 = $scope.editempresa.licencia;
            } else {
                fechalicencia1 = $scope.editempresa.fachelicencia;
            }
            $scope.editempresa.accion = "editarlaempresa";
            $scope.editempresa.fechalicencia = fechalicencia1;
            $scope.editempresa.idempresa = idempresa1;
            servicios.empresa($scope.editempresa).then(function success(response) { 
                llenarTablaEmpresa();
            });
        }
    }
    //FUNCION PARA PREPARAR LA EMPRESA A ELIMINAR
    var idEmpresaEliminar = "";
    $scope.listarempresaeliminar = function (idempresa){
        idEmpresaEliminar = idempresa;
        console.log(idEmpresaEliminar);
    }

    //FUNCION PARA ELIMINAR EMPRESA
    $scope.eliminarempresa = function (){
        datos = {accion: "eliminarempresa", idempresa: idEmpresaEliminar};
        servicios.empresa(datos).then(function success(response) {
            //console.log(response);
            mensajemodal("La Empresa Fue Eliminado Con Éxito"); 
            llenarTablaEmpresa();
        });
    }

    //FUNCION PARA LISTAR EL USUARIO ADMIN POR DEFECTO
    var idempresausuario = "";
    $scope.listarusuarioempresa = function (idempresa){
        $scope.usuarioempresa = "";
        idempresausuario = idempresa;
        datos = {accion: "listarusuarioempresa"};
        datos.idempresa = idempresa;
        servicios.empresa(datos).then(function success(response) {
            console.log(response.data);
            
            if(response.data == "crearusuario"){
                $scope.crearusuario = true;
                $scope.editarusuario= false;
            }else{
                $scope.usuarioempresa = response.data[0];
                $scope.crearusuario = false;
                $scope.editarusuario= true;
            }
            
        });
    }

    //FUNCION PARA CREAR USUARIO ADMIN POR DEFECTO EMPRESA
    $scope.crearuserempresa={};
    $scope.crearusuarioempresa = function (){

        if(comprobarempty($scope.crearuserempresa["nombre"])
        || comprobarempty($scope.crearuserempresa["apellido"])
        || comprobarempty($scope.crearuserempresa["cedula"])
        || comprobarempty($scope.crearuserempresa["correo"])
        || comprobarempty($scope.crearuserempresa["nombreusuario"])
        || comprobarempty($scope.crearuserempresa["contrasena"])
        || comprobarempty($scope.crearuserempresa["contrasena1"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        }else{
            if( validar_email($scope.crearuserempresa["correo"])){
                if($scope.crearuserempresa["contrasena"] == $scope.crearuserempresa["contrasena1"]){
                    $scope.crearuserempresa.accion = "ingresarusuarioadmin";
                    $scope.crearuserempresa.idempresa = idempresausuario;
                    servicios.empresa($scope.crearuserempresa).then(function success(response) {
                        if(response["data"] == "valido"){
                            mensajemodal("El Usuario " + $scope.crearuserempresa["nombreusuario"]+ " Fue Registrado con Éxito");
                            $('#crearusuarioadmin').modal('hide');
                            $scope.crearusuario = false;
                            $('#usuarioempresa').modal('hide');
                        }else{
                            mensajemodal("El Usuario " + $scope.crearuserempresa["nombreusuario"]+ " Ya Existe");
                        }
                    });
                }else{
                    mensajemodal("Las Contraseñas Ingresadas No Coinciden");
                }
            }else{
                mensajemodal("El correo electronico no es valido");
            }
        }
    }

    //FUNCION PARA ENLISTAR EL USUARIO A EDITAR DE LA
    $scope.listarusuarioeditarempresa = function (idusuario) {
        datos = {accion: "listarusuarioeditarempresa"};
        datos.idusuario = idusuario;
        servicios.empresa(datos).then(function success(response) {
            
            $scope.editusuarioadmin = response.data[0];
            console.log(response);
        });
        console.log(idusuario);
    }

    //FUNCION PARA EDITAR EL USUARIO ADMIN DE LA EMPRESAS
    $scope.editarusuarioempresa = function (idusuario){
        idadminempresa = idusuario;
        if(comprobarempty($scope.editusuarioadmin["nombre"])
        || comprobarempty($scope.editusuarioadmin["apellido"])
        || comprobarempty($scope.editusuarioadmin["cedula"])
        || comprobarempty($scope.editusuarioadmin["correo"])
        || comprobarempty($scope.editusuarioadmin["nombreusuario"])
        || comprobarempty($scope.editusuarioadmin["contrasena1"])
        || comprobarempty($scope.editusuarioadmin["contrasena2"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        }else{
            if(validar_email($scope.editusuarioadmin["correo"])){
                console.log("bien correo");
                
                if($scope.editusuarioadmin["contrasena1"] == $scope.editusuarioadmin["contrasena2"]){
                    console.log("bien contrasenas");
                    $scope.editusuarioadmin.accion = "editarusuarioadminempresa";
                    $scope.editusuarioadmin.idusuario = idadminempresa;
                    servicios.empresa($scope.editusuarioadmin).then(function success(response) {
                        console.log(response);
                        $('#editarusuarioadmin').modal('hide');
                    });
                }else{
                    mensajemodal("Las Contraseñas Ingresadas No Coinciden");
                }
            }else{
                mensajemodal("El correo electronico no es valido");
            }
        }
    }


    
}  

