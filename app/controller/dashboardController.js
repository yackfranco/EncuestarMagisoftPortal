angular.module('MagisoftV1').controller('dashboardController', dashboardController);
dashboardController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$interval'];
function dashboardController($scope, $state, $sessionStorage, servicios, $interval) {
    if ($sessionStorage.idusuario === undefined) {
        $state.go('login');
    } else {
        if ($sessionStorage.rol == "ASESOR") {
            $state.go('Mando');
        }
    }
    $scope.TipoUsuario = $sessionStorage.rol;

    $scope.NombreUsuario = $sessionStorage.nombreUsuario;

CargarTablas();
    $interval(function () {
        CargarTablas();
//        llenarTabla();
//        llenarTablaservicios();
//        llenarTablaTipoPoblacion();
//        llenarTablaTipoUsuariocant();
    }, 5000);

    function CargarTablas() {
        datos = {accion: "cargarTabla"};
        servicios.Dashboard(datos).then(function success(response) {
            console.log(response);
            $scope.usuario = response.data.respuesta;
            $scope.servicio = response.data.respuestaServicios;
            $scope.ausentes = response.data.turnosausentes;
            $scope.ServiciosCola = response.data.turnosenCOla;
            $scope.usuariosatendidos = response.data.TERMINADOSUsu;
        });
    }

}
