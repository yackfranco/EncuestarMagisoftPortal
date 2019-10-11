angular.module('MagisoftV1').controller('SeguridadController', InitController);
SeguridadController.$inject = ['$scope', '$state', '$sessionStorage', '$localStorage'];
function SeguridadController($scope, $state, $sessionStorage, $LocalStorage) {

    if ($LocalStorage.idLote === undefined) {
        $state.go('dashboard');
    } else {
        $state.go('MenuCalificar');
    }
}

