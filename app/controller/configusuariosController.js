angular.module('Calificadores').controller('configusuariosController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$interval', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $interval, $LocalStorage) {

    $scope.botonconfig = "botonescontorno";
    $scope.botonconfigtxt = "botonestxt";
    $scope.botonconfigfa = "botonesfa";
    $scope.HabilitarMetodo = true;

    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "ADMINISTRADOR") {
            $state.go('configusuarios');
        } else {
            $state.go('index');
        }
    } else {
        $state.go('index');
    }

    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;

    listarusuario()

    var idusuario1;
    var configpreencuesta1;
    var modo1;
    var metodo1;
    var ventana;
    var TodosAsesores;

    function comprobarempty(objeto) {
        if (objeto == "" || objeto == undefined) {
            return true;
        } else {
            return false;
        }
    }

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

    function listarusuario() {
        datos = {accion: "listarusuario", IdEmpresa: $LocalStorage.IdEmpresa};
        console.log("listarusuario")
        servicios.configusuarios(datos).then(function success(response) {
            console.log(response);
            $scope.usuarios = response.data;
        });
    }
    $scope.bloquearCkeck = false;
    $scope.preguntaEncuesta = function (modo) {
        modo1 = modo;
//        $scope.confiusuarios.Metodo = "";
        if (modo1 == "Mono") {
            $scope.bloquearCkeck = false;
            document.getElementById("PE").innerHTML = "Elegir Pregunta: ";
            $scope.OpcionSeleccion = "Pregunta";
            datos = {accion: "listarpreguntas", IdEmpresa: $LocalStorage.IdEmpresa};
            $scope.Manual = true;
            $scope.Automatico = true;
            $scope.Seleccionable = false;
            if (metodo1 == "Seleccionable") {
                metodo1 = "Manual";
            }
            servicios.configusuarios(datos).then(function success(response) {
                //console.log(response);
                $scope.preguntas = response.data;
            });
        } else {
            $scope.bloquearCkeck = true;
            $scope.checkValue = false;
            $scope.Manual = true;
            $scope.Automatico = true;
            $scope.Seleccionable = true;
            document.getElementById("PE").innerHTML = "Elegir Encuesta: ";
            $scope.OpcionSeleccion = "Encuesta";
            datos = {accion: "listarencuestas", IdEmpresa: $LocalStorage.IdEmpresa};
            servicios.configusuarios(datos).then(function success(response) {
                //console.log(response);
                $scope.encuestas = response.data;
            });
        }
        $scope.confiusuarios.Encuesta = idencupre;
        $scope.HabilitarMetodo = false;
        console.log(modo1);
    }
    var datos = {};
    var idencupre = 0;
    $scope.elegirUsuario = function (idusuario) {
        if (idusuario == "Todos los usuarios") {
            $scope.confiusuarios = {};
            $scope.checkValue = false;
            $scope.HabilitarMetodo = true;
            return;
        }
        datos = {accion: "listarconfiguracion", idusuario: idusuario};
        idusuario1 = idusuario;
        servicios.configusuarios(datos).then(function success(response) {
            console.log(response);
            if (response.data[0][0]["MinimizarCalif"] == 1)
                $scope.checkValue = true;
            else
                $scope.checkValue = false;


            $scope.confiusuarios = response.data[0][0];
            if ($scope.confiusuarios.Modo == "Mono") {
                idencupre = response.data[1][0]['IdPregunta'];
//                console.log("ajsjfad ",response.data[1][0]['IdPaquete']);
            } else {
                idencupre = response.data[1][0]['IdPaquete'];
            }

            //$scope.confiusuarios.Encuesta = response.data[0][0];
            //  console.log(response);
            $scope.elegirmetodo(response.data[0][0]['Metodo']);

            if (response.data[0][0]['Modo'] == "Multi") {
                $scope.confiusuarios.Modousuario = "Multi-Pregunta";
                $scope.preguntaEncuesta(response.data[0][0]['Modo']);
                //$scope.confiusuarios.Encuesta = response.data[1][0]['Paquete'];
                //console.log("Encuestaaaaa",   $scope.confiusuarios.Encuesta);
            } else {
                $scope.confiusuarios.Modousuario = "Mono-Pregunta";
                $scope.preguntaEncuesta(response.data[0][0]['Modo']);
                //$scope.confiusuarios.Encuesta = response.data[1][0]['Pregunta'];
                //console.log("Encuestaaaaa",   $scope.confiusuarios.Encuesta);
            }
            if (response.data[0][0]['Metodo'] == "Manual")
                $scope.bloquearCkeck = true;
            else
                $scope.bloquearCkeck = false;
        });
    }

    $scope.preguntaEncuestaid = function (configpreencuesta) {
        configpreencuesta1 = configpreencuesta;
        //console.log("idpregunta o Encuesta",configpreencuesta1);
    }

    $scope.checkAll = function (checkValue) {
        if (checkValue == true) {
            ventana = 1;
        } else {
            ventana = 0;
        }


        //console.log("ventanaminimizada",ventana);
    }

    $scope.checkAllAsesores = function (checkValue) {
        if (checkValue == true) {
            TodosAsesores = 1;
        } else {
            TodosAsesores = 0;
        }
        //console.log("Todoslosusuarios",TodosAsesores);
    }

    $scope.elegirmetodo = function (metodo) {
        if ($scope.confiusuarios.Modo == "Mono" && metodo == "Automatico") {
            $scope.bloquearCkeck = false;
        } else {
            $scope.bloquearCkeck = true;
            $scope.checkValue = false;
        }
        metodo1 = metodo;
        console.log("metodo", metodo1);
    }

    $scope.submitconfigusuarios = function () {
        if ($scope.idusuario == "Todos los usuarios") {

            if (comprobarempty($scope.confiusuarios.Modo)
                    || comprobarempty($scope.confiusuarios.Metodo)
                    || comprobarempty($scope.confiusuarios.Encuesta))
            {
                mensajemodal("Debe Llenar Todos Los Campos");
            } else {
                if (modo1 == "Multi") {
                    idencuesta = configpreencuesta1;
                    idpregunta = 0;
                } else {
                    idpregunta = configpreencuesta1;
                    idencuesta = 0;
                }
                datos = {accion: "subtmiconfiguser", IdEmpresa: $LocalStorage.IdEmpresa, TodosAsesores: 1, modo: modo1, metodo: metodo1, estilo: $scope.confiusuarios.Encuesta, ventana: ventana};
                servicios.configusuarios(datos).then(function success(response) {
                    // console.log(response);                    
                    mensajemodal("Todos los Asesores Fueron Configurados Con Éxito");
                    $scope.IdUsuario = "";
                    $scope.modo = "";
                    $scope.metodo = "";
                    $scope.configpreencuesta = "";
                });
            }
        } else {
            //VALIDA LOS CAMPOS LLAMANDO LA FUNCION
            if (comprobarempty(idusuario1)
                    || comprobarempty($scope.confiusuarios.Modo)
                    || comprobarempty($scope.confiusuarios.Metodo)
                    || comprobarempty($scope.confiusuarios.Encuesta))
            {
                mensajemodal("Debe Llenar Todos Los Campos");
            } else {
                if (modo1 == "Multi") {
                    idencuesta = configpreencuesta1;
                    idpregunta = 0;
                } else {
                    idpregunta = configpreencuesta1;
                    idencuesta = 0;
                }

                datos = {accion: "subtmiconfiguser", IdEmpresa: $LocalStorage.IdEmpresa, TodosAsesores: 0, idusuario: idusuario1, modo: $scope.confiusuarios.Modo, metodo: $scope.confiusuarios.Metodo, estilo: $scope.confiusuarios.Encuesta, ventana: ventana};

                //console.log(datos);
                servicios.configusuarios(datos).then(function success(response) {
                    //console.log(response);
                    mensajemodal("Configuración Guardada Con Éxito");
                    $scope.IdUsuario = "";
                    $scope.modo = "";
                    $scope.metodo = "";
                    $scope.configpreencuesta = "";
                });
            }
        }
    }

}
