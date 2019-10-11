angular.module('Calificadores').controller('GeneralController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage) {


    
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
        $state.go('index');
    }


}
