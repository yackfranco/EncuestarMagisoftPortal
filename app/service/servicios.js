angular.module('Calificadores').service('servicios', servicios);
servicios.$inject = ['$http', 'urlBase', '$httpParamSerializerJQLike'];

function servicios($http, urlBase, $httpParamSerializerJQLike) {

    this.login = (data) => {
        return $http.post(urlBase + 'login.php', $httpParamSerializerJQLike(data));
    };

    this.usuario = (data) => {
        return $http.post(urlBase + 'usuario.php', $httpParamSerializerJQLike(data));
    };

    this.admin = (data) => {
        return $http.post(urlBase + 'admin.php', $httpParamSerializerJQLike(data));
    };

    this.configusuarios = (data) => {
        return $http.post(urlBase + 'configusuarios.php', $httpParamSerializerJQLike(data));
    };

    this.sedes = (data) => {
        return $http.post(urlBase + 'sedes.php', $httpParamSerializerJQLike(data));
    };
    this.paquete = (data) => {
        return $http.post(urlBase + 'paquete.php', $httpParamSerializerJQLike(data));
    };

    this.empresa = (data) => {
        return $http.post(urlBase + 'empresa.php', $httpParamSerializerJQLike(data));
    };
    this.asesor = (data) => {
        return $http.post(urlBase + 'asesor.php', $httpParamSerializerJQLike(data));
    };
    this.reporte = (data) => {
        return $http.post(urlBase + 'reporteconsulta.php', $httpParamSerializerJQLike(data));
    };
    this.reportegeneral = (data) => {
        return $http.post(urlBase + 'reportegeneral.php', $httpParamSerializerJQLike(data));
    };
    this.Empresas = (data) => {
        return $http.post(urlBase + 'Empresas.php', $httpParamSerializerJQLike(data));
    };
    this.RecuperarEmpresas = (data) => {
        return $http.post(urlBase + 'RecuperarEmpresa.php', $httpParamSerializerJQLike(data));
    };
}
