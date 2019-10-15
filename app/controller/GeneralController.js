angular.module('Calificadores').controller('GeneralController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage', '$interval', 'urlBase', '$http'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage, $interval, $urlBase, $http) {
    var datos = {};

    function ListarTablaVerUsuarios() {
        datos = {accion: "registroUsuarios", IdEmpresa: $LocalStorage.IdEmpresa};
        //console.log("listarusuario")        
        servicios.usuario(datos).then(function success(response) {
            console.log(response);
            $scope.usuariosEstado = response.data;
        });
    }
    var interval;


    $scope.VerUsuarios = function () {
        ListarTablaVerUsuarios();
        $('#ModalVerUsuarios').modal('show');
        interval = $interval(function () {
            ListarTablaVerUsuarios();
        }, 10000);
    }

    $scope.cerrarModalVerUsuarios = function () {
        $interval.cancel(interval);
    }



    $scope.mostrar = true;


    $scope.ClicItem = function (es) {
        console.log(es);
        switch (es) {
            case 'admin':
                $state.go('admin');
                break;
            case 'usuario':
                $state.go('usuario');
                break;
            case 'calificaciones':
                $state.go('admin');
                break;
            case 'configusuarios':
                $state.go('configusuarios');
                break;
            case 'sedes':
                $state.go('sedes');
                break;
            case 'paquete':
                $state.go('paquete');
                break;
            case 'reportes':
                $state.go('reportes');
                break;
            case 'Empresas':
                $state.go('Empresas');
                break;
            case 'RecuperarEmpresa':
                $state.go('RecuperarEmpresa');
                break;
        }
    }


    $scope.cerrarSesion = function (es) {
        delete $LocalStorage.usuarioguardado;
        delete $LocalStorage.nombrecompletoguardado;
        delete $LocalStorage.idusuarioguardado;
        delete $LocalStorage.cedulaguardado;
        delete $LocalStorage.rolguardado;
        delete $LocalStorage.IdEmpresa;
        $state.go('index');
    }

    $scope.ReporteMonitorearUsuario = function () {
        $http.get($urlBase + "reporteMonitorearUsuarios.php")
                .then(function (response) {
                    if (response.data.respuesta == "Enviado Correctamente") {
                        $('#modalDescarga').modal('show');
                        $scope.MostrarDescarga = true;
                        $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                        $scope.descargarReporteExcel = true;
                    }
                });
    }

    VigilarLicencia();
    function VigilarLicencia() {
        if($LocalStorage.IdEmpresa == "" || $LocalStorage.IdEmpresa== undefined){
            return;
        }
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




}
