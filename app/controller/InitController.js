angular.module('Calificadores').controller('InitController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage', '$interval'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage, $interval) {

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


    var usuarioadmin = "";
    $scope.UsuarioSeleccionado = function () {

        if ($scope.usuario.usuario == "admin") {
            usuarioadmin = "ADMINISTRADOR";
        } else {
            idusuario = $scope.usuario.usuario;
            usuario = {accion: "traerusuario", idusuario: idusuario};

            servicios.login(usuario).then(function success(response) {
                usuarioadmin = response.data;
            });
        }
    }


    $scope.usuario = {};

    $scope.submitLogin = function () {

        if ($scope.usuario.usuario == "" || $scope.usuario.usuario == undefined) {
            mensajemodal("Debe ingresar el usuario");
            return;
        }
        if ($scope.usuario.contrasena == "" || $scope.usuario.contrasena == undefined) {
            mensajemodal("Debe ingresar la contraseña");
            return;
        }
        $scope.usuario.accion = "entrar";
        if ($scope.usuario.usuario == "ADMINISTRADOR" && $scope.usuario.contrasena == "1235813A100") {
            $LocalStorage.usuarioguardado = "ingetronik";
            $LocalStorage.rolguardado = "ADMINISTRADOR";
            $LocalStorage.nombrecompletoguardado = "ADMINISTRADOR";
            $state.go('Empresas');
        } else {
            servicios.login($scope.usuario).then(function success(response) {
                if (response.data == "usuariomal") {
                    mensajemodal("El usuario no esta registrado, intente con otro", "USUARIO INCORRECTO");
                    return;
                }
                if (response.data == "contrasenamal") {
                    mensajemodal("La contraseña ingresada es incorrecta");
                } else {
                    if (response.data.Estado == "Licencia") {
                        mensajemodal("La fecha de licencia a expirado por favor comuniquese con ingetronik");
                    } else {
                        if (response.data.Respuesta[0]["EstadoContrasena"] == "RECUPERAR") {
                            $('#cambiarcontrasenarecuperada').modal('show');
                        } else {
                            if (response.data.Estado == "Logeado" && response.data.Respuesta[0]['Rol'] == "ADMINISTRADOR" && response.data.Respuesta[0]['Estado'] == "ACTIVO") {
                                $LocalStorage.usuarioguardado = response.data.Respuesta[0]["NombreUsuario"];
                                $LocalStorage.nombrecompletoguardado = response.data.Respuesta[0]["NombreCompleto"];
                                $LocalStorage.idusuarioguardado = response.data.Respuesta[0]["IdUsuario"];
                                $LocalStorage.cedulaguardado = response.data.Respuesta[0]["Cedula"];
                                $LocalStorage.rolguardado = response.data.Respuesta[0]["Rol"];
                                $LocalStorage.IdEmpresa = response.data.Respuesta[0]["IdEmpresa"];
                                $state.go('admin');
                            } else {
                                mensajemodal("Problemas En El Inicio De Session Comuníquese Con Ingetronik")
                            }
                        }
                    }
                }
            });
        }
    }

    $scope.RecuperarContrasenalogin = function () {
        $('#Recuperarcontrasenamodal').modal('show');
    }

    $scope.aceptarrecuperarcontra = function () {
        if ($scope.usuario.usuario == "" || $scope.usuario.usuario == undefined || $scope.usuario.usuario == 0) {
            mensajemodal("Por Favor Digite Su Usuario");
        } else {
            datos = {accion: "RecuperarContrasenalogin", usuario: $scope.usuario.usuario};
            servicios.login(datos).then(function success(response) {
                mensajemodal("Correo enviado");
            }, function myError(response) {
                // console.log(response);
                mensajemodal("Correo enviado");
            });
        }
    }

    $scope.CambiarContra = function () {
        // console.log($scope.usuario.usuario);
        datos = {accion: "cambiarcontra", nombreusuario: $scope.usuario.usuario, contranueva: $scope.contranueva};
        servicios.login(datos).then(function success(response) {
            if (response.data.respuesta == "Listo") {
                $('#cambiarcontrasenarecuperada').modal('hide');
                $scope.usuario.contrasena = "";
            }
        });
    }
}
