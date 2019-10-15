angular.module('Calificadores').controller('adminController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage', '$interval'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage, $interval) {
    $scope.botoncalif = "botonescontorno";
    $scope.botoncaliftxt = "botonestxt";
    $scope.botoncaliffa = "botonesfa";
    console.log("prueba");

    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "ADMINISTRADOR") {
            $state.go('admin');
        } else {
            $state.go('index');
        }
    } else {

        $state.go('index');
    }
    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;

    valorcalificaciones();
    fechacalifiacion();
    tablacalificaciones();
    totalcalificaciones();

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
    function tablacalificaciones() {
        datos = {accion: "tablacalificaciones", IdEmpresa: $LocalStorage.IdEmpresa};
        servicios.admin(datos).then(function success(response) {
            $scope.calificacion = response.data;
            console.log(response.data);
        });
    }

    VigilarLicencia();
    function VigilarLicencia() {
        datos = {accion: "AvisarLicencia", IdEmpresa: $LocalStorage.IdEmpresa};
        servicios.admin(datos).then(function success(response) {
            console.log(response.data);
            if (response.data == "No hay licencia") {
                delete $LocalStorage.usuarioguardado;
                delete $LocalStorage.nombrecompletoguardado;
                delete $LocalStorage.idusuarioguardado;
                delete $LocalStorage.cedulaguardado;
                delete $LocalStorage.rolguardado;
                delete $LocalStorage.IdEmpresa;
                $state.go('index');
                return;
            }
            if (response.data.Meses > 0 && response.data.Meses <= 3) {
                swal({
                    title: "Atención",
                    text: "Su licencia expirará en " + response.data.Meses + " meses",
                    type: "info",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                });
            } else {
                if (response.data.Meses == 0) {
                    swal({
                        title: "Atención",
                        text: "Su licencia expirará en " + response.data.Dias + " días",
                        type: "info",
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Aceptar",
                        closeOnConfirm: false
                    });
                }
            }
        });
    }

    function valorcalificaciones() {
        datos = {accion: "valorcalificaciones", IdEmpresa: $LocalStorage.IdEmpresa};
        datos.idempresa = $LocalStorage.idempresaguardado;
        servicios.admin(datos).then(function success(response) {
            $scope.valorcalif = response.data;
        });
    }

    function fechacalifiacion() {
        datos = {accion: "fechacalifiacion"};
        servicios.admin(datos).then(function success(response) {
            $scope.fechacalif = response;
        });
    }

    function totalcalificaciones() {
        datos = {accion: "totalcalificaciones", IdEmpresa: $LocalStorage.IdEmpresa};
        servicios.admin(datos).then(function success(response) {
            $scope.totalcalificacion = response.data;
        });
    }
}