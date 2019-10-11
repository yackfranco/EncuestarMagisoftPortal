angular.module('Calificadores').controller('usuarioController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$interval', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $interval, $LocalStorage) {

    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "ADMINISTRADOR") {
            $state.go('usuario');
        } else {
            $state.go('index');
        }
    } else {
        $state.go('index');
    }

    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;

    ///TOMA LOS DATOS DE DEL FORMULARIO USUARIO
    $scope.usuarios = {};

    //FUNCION PARA LLENAR TABLA USUARIOS

    llenarTabla();
    function llenarTabla() {
        datos = {accion: "cargarTabla", IdEmpresa: $LocalStorage.IdEmpresa};
        servicios.usuario(datos).then(function success(response) {
            console.log(response.data);
            $scope.usuario = response.data;
        });
    }
 
    //rutina para validar que solo ingresen letras
    function validarSoloLetra(string) {
        if (string.includes("#") || string.includes("$") || string.includes("´"))
        {
            return true;
            /*
             document.getElementById(campo).select();
             document.getElementById(campo).focus();*/
        }
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

    //FUNCION PARA VALIDAR EL CORREO ELECTRONICO
    function validar_email(email) {
        var patron = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return patron.test(email) ? true : false;
    }


    //FUNCION PARA REGISTRAR UN USUARIO EN EL SISTEMA
    $scope.submitusuarios = function () {
        //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
        console.log($scope.usuarios);
        if (comprobarempty($scope.usuarios["nombre"])
                || comprobarempty($scope.usuarios["cedula"])
                || comprobarempty($scope.usuarios["correo"])
                || comprobarempty($scope.usuarios["nombreusuario"])
                || comprobarempty($scope.usuarios["rol"])
                || comprobarempty($scope.usuarios["contrasena"])
                || comprobarempty($scope.usuarios["contrasena1"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {

            if (validarSoloLetra($scope.usuarios["nombre"])
                    || validarSoloLetra($scope.usuarios["cedula"])
                    || validarSoloLetra($scope.usuarios["nombreusuario"])
                    || validarSoloLetra($scope.usuarios["rol"])
                    || validarSoloLetra($scope.usuarios["contrasena"])
                    || validarSoloLetra($scope.usuarios["contrasena1"])) {
                mensajemodal("Los Campos no deben contener los siguientes caracteres $ ´ #");
            } else {
                //VALIDA EL CORREO LLAMANDO LA FUNCION
                if ($scope.usuarios["contrasena"] == $scope.usuarios["contrasena1"]) {
                    if (validar_email($scope.usuarios["correo"])) {
                        //VALIDAR USUARIO
                        $scope.usuarios.IdEmpresa = $LocalStorage.IdEmpresa;
                        $scope.usuarios.accion = "ingresarusuario";
                        servicios.usuario($scope.usuarios).then(function success(response) {
                            console.log(response);

                            if (response["data"] == "Pinvalido") {
                                mensajemodal("Debe Crear Una Pregunta Antes De Crear Un Usuario");
                            } else {
                                if (response["data"] == "invalido") {
                                    mensajemodal("El Usuario: " + $scope.usuarios["nombreusuario"] + " Ya Existe");
                                } else {
                                    if (response["data"] == "Cinvalido") {
                                        mensajemodal("La Cedula: " + $scope.usuarios["cedula"] + " Ya Existe");
                                    } else {
                                        mensajemodal("El Usuario: " + $scope.usuarios["nombreusuario"] + " Fue Registrado Con Éxito");
                                        //LIMPIAR DATOS DE LOS INPUTS
                                        $scope.usuarios = {};
                                        llenarTabla();
                                    }
                                }
                            }
                        });
                    } else {
                        mensajemodal("El Correo Eléctronico Ingresado no es Válido");
                    }
                } else {
                    mensajemodal("Las Contraseñas Ingresadas No Coinciden");
                }

            }
        }
    }


    //FUNCION PARA ENLISTAR EL USUARIO A EDITAR
    $scope.listarusuario = function (idusuario) {
        datos = {accion: "listarusuario"};
        datos.idusuario = idusuario;

        servicios.usuario(datos).then(function success(response) {
            console.log(response.data[0]);
            $scope.editusuario = response.data[0];
            //$scope.listausuario = response.data;


        });
    }

    //FUNCION PARA EDITAR UN USUARIO EN EL SISTEMA
    $scope.editarusuario = function (idusuario) {
        //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
        idusuario = idusuario;
        if (comprobarempty($scope.editusuario["NombreCompleto"])
                || comprobarempty($scope.editusuario["Cedula"])
                || comprobarempty($scope.editusuario["Correo"])
                || comprobarempty($scope.editusuario["NombreUsuario"])
                || comprobarempty($scope.editusuario["Rol"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {
            console.log($scope.editusuario);
            if (validarSoloLetra($scope.editusuario["NombreCompleto"])
                    || validarSoloLetra($scope.editusuario["Cedula"])
                    || validarSoloLetra($scope.editusuario["NombreUsuario"])) {
                mensajemodal("Los Campos no deben contener los siguientes caracteres $ ´ #");
            } else {
                if (validar_email($scope.editusuario["Correo"])) {
                    $scope.editusuario.accion = "editarusuario";
                    $scope.editusuario.idusuario = idusuario;
                    console.log(idusuario);
                    servicios.usuario($scope.editusuario).then(function success(response) {
                        console.log(response);
                        if (response["data"] == "invalido") {
                            mensajemodal("El Usuario: " + $scope.editusuario["NombreUsuario"] + " Ya Existe");
                        } else {
                            if (response["data"] == "Cinvalido") {
                                mensajemodal("La Cedula: " + $scope.editusuario["Cedula"] + " Ya Existe");
                            } else {
                                mensajemodal("El Usuario Fue Editado Con Éxito");
                                llenarTabla();
                            }
                        }



                    });

                } else {
                    mensajemodal("El Correo Eléctronico Ingresado no es Válido");
                }
            }
        }
    }



    //FUNCION PARA TOMAR EL ID DEL USUARIO A ELIMINAR
    var idEliminar = "";
    $scope.listarusuarioeliminar = function (idusuario) {
        idEliminar = idusuario;
        console.log(idEliminar);
    }

    //FUNCION PARA ELIMINAR USUARIO
    $scope.eliminarusuario = function () {
        datos = {accion: "eliminarusuario", idusuario: idEliminar};
        console.log(idEliminar);
        servicios.usuario(datos).then(function success(response) {
            mensajemodal("El Usuario Fue Eliminado Con Éxito");
            llenarTabla();
        });
    }

    $scope.busquedausuario = function () {
        console.log($scope.busuario);
        datos = {accion: "busquedausuario", datousuario: $scope.busuario, IdEmpresa: $LocalStorage.IdEmpresa};
        console.log($scope.busuario);
        servicios.usuario(datos).then(function success(response) {
            $scope.usuario = response.data;
        });
    }

}
