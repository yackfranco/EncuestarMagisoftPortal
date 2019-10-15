angular.module('Calificadores').config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$httpProvider',
    function ($stateProvider, $urlRouterProvider, $locationProvider, $httpProvider) {

        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';

        $locationProvider.hashPrefix('');
        $urlRouterProvider.otherwise('/');

        $stateProvider.state('index', {
            url: '/',
            templateUrl: 'app/template/login.html',
            controller: 'InitController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/InitController.js'
                                ]
                            }
                        ]);
                    }]
            }

        }).state('admin', {
            url: '/admin',
            templateUrl: 'app/template/admin.html',
            controller: 'adminController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/adminController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('usuario', {
            url: '/usuario',
            templateUrl: 'app/template/usuario.html',
            controller: 'usuarioController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/usuarioController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('configusuarios', {
            url: '/configusuarios',
            templateUrl: 'app/template/configusuarios.html',
            controller: 'configusuariosController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/configusuariosController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('sedes', {
            url: '/sedes',
            templateUrl: 'app/template/sedes.html',
            controller: 'sedesController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/sedesController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('reportes', {
            url: '/reportes',
            templateUrl: 'app/template/reportes.html',
            controller: 'reportesController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/reportesController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('empresa', {
            url: '/empresa',
            templateUrl: 'app/template/empresa.html',
            controller: 'empresaController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/empresaController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('paquete', {
            url: '/paquete',
            templateUrl: 'app/template/paquete.html',
            controller: 'paqueteController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/paqueteController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('Empresas', {
            url: '/Empresas',
            templateUrl: 'app/template/Empresas.html',
            controller: 'EmpresasController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/EmpresasController.js'
                                ]
                            }
                        ]);
                    }]
            }
        }).state('RecuperarEmpresa', {
            url: '/RecuperarEmpresa',
            templateUrl: 'app/template/RecuperarEmpresa.html',
            controller: 'RecuperarEmpresaController',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: [
                                    'app/controller/RecuperarEmpresaController.js'
                                ]
                            }
                        ]);
                    }]
            }
        })
    }]);