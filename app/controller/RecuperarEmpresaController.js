angular.module('Calificadores').controller('RecuperarEmpresaController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$interval', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $interval, $LocalStorage) {
    $scope.botonRecuperarEmpresa = "botonescontorno";
    $scope.botonRecuperarEmpresatxt = "botonestxt";
    $scope.botonRecuperarEmpresafa = "botonesfa";
    
    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "ADMINISTRADOR") {
            $state.go('RecuperarEmpresa');
        } else {
            $state.go('index');
        }
    } else {
        $state.go('index');
    }

    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;

    ///TOMA LOS DATOS DE DEL FORMULARIO USUARIO
    $scope.empresas = {};

    //FUNCION PARA LLENAR TABLA USUARIOS

    llenarTabla();
    function llenarTabla() {
        datos = {accion: "cargarTablaempresas"};
        servicios.Empresas(datos).then(function success(response) {
            console.log(response.data);
            $scope.empresas = response.data;
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

    //FUNCION PARA CONEVRTIR UN OBJETO FECHA
    function convertDatePickerTimeToMySQLTime(str) {
        var month, day, year, hours, minutes, seconds;
        var date = new Date(str),
                month = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
        // hours = ("0" + date.getHours()).slice(-2);
        // minutes = ("0" + date.getMinutes()).slice(-2);
        // seconds = ("0" + date.getSeconds()).slice(-2);

        var mySQLDate = [date.getFullYear(), month, day].join("-");
        // var mySQLTime = [hours, minutes, seconds].join(":");
        // return [mySQLDate, mySQLTime].join(" ");
        return [mySQLDate].join(" ");
    }

    var EMfechainicial = "";
    $scope.EMfechainicial = function (FechaLicencia) {
        EMfechainicial = convertDatePickerTimeToMySQLTime(FechaLicencia);
        console.log(EMfechainicial);
    }

    var UPfechainicial = "";
    $scope.UPfechainicial = function (FechaLicencia2) {
        console.log(FechaLicencia2);
        UPfechainicial = convertDatePickerTimeToMySQLTime(FechaLicencia2);
        console.log(UPfechainicial);
    }

    var IdEmpresaGlobal;
    $scope.listarCalificaciones = function (IdEmpresa) {
        IdEmpresaGlobal = IdEmpresa;
        datos = {accion: "ValidarCalificacionesEmpresa", IdEmpresa: IdEmpresa};
        servicios.Empresas(datos).then(function success(response) {
            console.log(response.data);
            if (response.data == 0) {
                $scope.Calificaron = true;
            } else {
                $scope.Calificaron = false;
            }
        });
        datos = {accion: "cargarTablaCalificaciones", IdEmpresa: IdEmpresa};
        servicios.Empresas(datos).then(function success(response) {
            console.log(response.data);
            $scope.calificaciones = response.data;
        });
    }

    $scope.GuardarCalif = function () {
        if (comprobarempty($scope.Nombre) || comprobarempty($scope.Valor)) {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {
            datos = {accion: "GuardarCalif", IdEmpresa: IdEmpresaGlobal, Nombre: $scope.Nombre.toUpperCase(), Valor: $scope.Valor};
            servicios.Empresas(datos).then(function success(response) {
                if (response["data"] == "invalido") {
                    alert("No se permite repetir el valor");
                } else {
                    if (response["data"] == "Cinvalido") {
                        alert("No se permite repetir el nombre");
                    } else {
                        $scope.listarCalificaciones(IdEmpresaGlobal);
                        $scope.Nombre = '';
                        $scope.Valor = '';
                    }
                }
            });
        }
    }

    //FUNCION PARA REGISTRAR UN USUARIO EN EL SISTEMA
    $scope.submitEmpresa = function () {
        //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
        if (comprobarempty($scope.empresas["nombre"])
                || comprobarempty($scope.empresas["nit"])
                || comprobarempty($scope.empresas["slogan"])
                || comprobarempty(EMfechainicial))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {
            console.log($scope.usuarios);
            $scope.empresas.accion = "ingresarempresas";
            $scope.empresas.FechaLicencia = EMfechainicial;
            servicios.Empresas($scope.empresas).then(function success(response) {
                console.log(response);
                if (response["data"] == "invalido") {
                    mensajemodal("El Usuario: " + $scope.empresas["nombre"] + " Ya Existe");
                } else {
                    if (response["data"] == "Cinvalido") {
                        mensajemodal("El Nit: " + $scope.empresas["nit"] + " Ya Existe");
                    } else {
                        mensajemodal("La Empresa: " + $scope.empresas["nombre"] + " Fue Registrada Con Éxito");
                        //LIMPIAR DATOS DE LOS INPUTS
                        $scope.empresas = {};
                        llenarTabla();
                    }
                }
            });
        }
    }

    //FUNCION PARA ENLISTAR EL USUARIO A EDITAR
    $scope.listarEmpresa = function (idusuario) {
        datos = {accion: "listarempresas"};
        datos.idusuario = idusuario;
        servicios.Empresas(datos).then(function success(response) {
            console.log(response.data[0]);
            $scope.editEmpresa = response.data[0];
            UPfechainicial = $scope.editEmpresa.FechaLicencia;
        });
    }

    //FUNCION PARA EDITAR UN USUARIO EN EL SISTEMA
    $scope.editarEmpresas = function (idusuario) {
        //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
        console.log($scope.editEmpresa);
        if (comprobarempty($scope.editEmpresa["Nombre"])
                || comprobarempty($scope.editEmpresa["nit"])
                || comprobarempty($scope.editEmpresa["Slogan"])
                || comprobarempty(UPfechainicial))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {
            $scope.editEmpresa.FechaLicencia = UPfechainicial;
            $scope.editEmpresa.accion = "editarempresas";
            $scope.editEmpresa.idusuario = idusuario;
            servicios.Empresas($scope.editEmpresa).then(function success(response) {
                console.log(response);
                if (response["data"] == "invalido") {
                    mensajemodal("El Nombre: " + $scope.editEmpresa["NombreUsuario"] + " Ya Existe");
                } else {
                    if (response["data"] == "Cinvalido") {
                        mensajemodal("La Nit: " + $scope.editEmpresa["Cedula"] + " Ya Existe");
                    } else {
                        mensajemodal("La Empresa Fue Editada Con Éxito");
                        llenarTabla();
                    }
                }
            });
        }
    }

    //FUNCION PARA TOMAR EL ID DEL USUARIO A ELIMINAR
    var idEliminar = "";
    $scope.listarEmpresaeliminar = function (idusuario) {
        idEliminar = idusuario;
        console.log(idEliminar);
    }

    var idEliminarEmpresaFisico = "";
    $scope.listarEmpresaeliminarFisico = function (idusuario) {
        idEliminarEmpresaFisico = idusuario;
        console.log(idEliminarEmpresaFisico);
    }

    //FUNCION PARA ELIMINAR USUARIO
    $scope.eliminarEmpresa = function () {
        datos = {accion: "eliminarEmpresa", idusuario: idEliminar};
        console.log(idEliminar);
        servicios.Empresas(datos).then(function success(response) {
            mensajemodal("La Empresa Fue Eliminada Con Éxito");
            llenarTabla();
        });
    }

    $scope.EliminadoFisico = function (IdEmpresa) {
        datos = {accion: "eliminarEmpresaFisica", IdEmpresa: idEliminarEmpresaFisico};
        console.log(idEliminar);
        servicios.Empresas(datos).then(function success(response) {
            mensajemodal("La Empresa Fue Eliminada Con Éxito");
            llenarTabla();
        });
    }

    //FUNCION PARA ELIMINAR CALIFICACION
    $scope.listarCalifeliminar = function (IdValor) {
        console.log(idEliminar);
        datos = {accion: "eliminarCalificacion", IdValor: IdValor};
        servicios.Empresas(datos).then(function success(response) {
            $scope.listarCalificaciones(IdEmpresaGlobal);
        });
    }

    $scope.busquedaempresas = function () {
        console.log($scope.busuario);
        datos = {accion: "busquedaempresas", datousuario: $scope.busuario};
        console.log($scope.busuario);
        servicios.Empresas(datos).then(function success(response) {
            $scope.empresas = response.data;
        });
    }
}