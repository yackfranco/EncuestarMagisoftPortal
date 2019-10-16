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
        servicios.RecuperarEmpresas(datos).then(function success(response) {
            console.log(response.data);
            $scope.empresas = response.data;
        });
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

    $scope.busquedaempresasEliminadas = function () {
        console.log($scope.busuarioEli);
        datos = {accion: "busquedaempresas", datousuario: $scope.busuarioEli};
        console.log($scope.busuarioEli);
        servicios.RecuperarEmpresas(datos).then(function success(response) {
            $scope.empresas = response.data;
        });
    }

    $scope.RecuperarEmpresa = function (IdEmpresa) {
        swal({
            title: "Esta Seguro?",
            text: "Esta intentando de recuperar una empresa eliminada",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Recuperar",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: false
        },
                function (isConfirm) {
                    if (isConfirm) {
                        datos = {accion: "RecuperarEmpresa", idusuario: IdEmpresa};
                        console.log(IdEmpresa);
                        servicios.RecuperarEmpresas(datos).then(function success(response) {
                            llenarTabla();
                        });
                        swal("Correcto", "Empresa recuperada con exito", "success");
                    } else {
                        swal("Operación cancelada", "", "error");
                    }
                });
//        datos = {accion: "RecuperarEmpresa", idusuario: IdEmpresa};
//        console.log(IdEmpresa);
//        servicios.RecuperarEmpresas(datos).then(function success(response) {
//            llenarTabla();
//        });
    }
}
