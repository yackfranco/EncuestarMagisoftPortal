angular.module('Calificadores').controller('paqueteController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage) {

    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "ADMINISTRADOR") {
            $state.go('paquete');
        } else {
            $state.go('index');
        }
    } else {
        $state.go('index');
    }
    $scope.nombrecompletoadmin = $LocalStorage.nombrecompletoguardado;

    //FUNCION PARA VALIDAR CAMPOS VACIOS
    function comprobarempty(objeto) {
        if (objeto == "" || objeto == undefined) {
            return true;
        } else {
            return false;
        }
    }

    //FUNCION PARA LLAMAR A LA MODAL
    function mensajemodal(mensaje) {
        //PARA ACTIVAR UNA MODAL
//        $('#btnformulariosedes').modal('show');
        swal({
            title: "ATENCIÓN",
            text: mensaje,
            width: '800px'
        });
        //MENASAJE QUE SE ENVIA POR PARAMETRO
//        $scope.mensaje = mensaje;
        //PARA DESACTIVAR LA MODAL DESPUES DE X SEGUNDOS
        /* var interval = $interval(function () {
         $('#btnformulariosedes').modal('hide');
         $interval.cancel(interval);
         
         }, 3000);*/
    }

    //*******************ENCUESTAS**************************///
    listarencuesta();
    //FUNCION PARA LISTAR LAS ENCUESTAS
    function listarencuesta() {
        datos = {accion: "listarencuesta", IdEmpresa: $LocalStorage.IdEmpresa};
        servicios.paquete(datos).then(function success(response) {
            $scope.listapaquete = response.data;
        });
    }

    listarencuestaselect();
    //FUNCION PARA LISTAR LAS ENCUESTAS EN ETIQUETA SELECT
    function listarencuestaselect() {
        datos = {accion: "listarencuestaselect", IdEmpresa: $LocalStorage.IdEmpresa};
        servicios.paquete(datos).then(function success(response) {
            //console.log(response);
            $scope.listarencuestas = response.data;
        });
    }

    function validarSoloLetra(string) {
        if (string.includes("#") || string.includes("$") || string.includes("´") || string.includes("`") || string.includes(","))
        {
            return true;
        }
        return false;
    }
    ///TOMA LOS DATOS DE DEL FORMULARIO ENCUESTA
    $scope.paquetes = {};
    //FUNCION PARA INGRESAR LAS ENCUESTAS
    $scope.ingresarpaquete = function () {
        if (comprobarempty($scope.paquetes["nombrepaquete"])) {
            mensajemodal("Debe De Llenar Los Campos");
            return;
        }

        if (validarSoloLetra($scope.paquetes.nombrepaquete)) {
            mensajemodal("La encuesta NO debe contener los siguientes caracteres: $ # ´ ` , ");
            return;
        }

        $scope.paquetes.IdEmpresa = $LocalStorage.IdEmpresa;
        $scope.paquetes.accion = "ingresarpaquete";
        servicios.paquete($scope.paquetes).then(function success(response) {
            console.log(response);
            if (response["data"] == "invalido") {
                mensajemodal("La Encuesta: " + $scope.paquetes["nombrepaquete"] + " Ya Existe");
            } else {
                mensajemodal("La Encuesta: " + $scope.paquetes["nombrepaquete"] + " Fue Registrada Con Éxito");
                $scope.paquetes = {};
                listarencuesta();
                listarencuestaselect();
            }
        });

    }


    ///FUNCION PARA TRAER LA ENCUESTA A EDITAR
    $scope.traerencuesta = function (IdPaquete) {
        datos = {accion: "traerencuesta"};
        datos.IdPaquete = IdPaquete;
        console.log(datos);
        servicios.paquete(datos).then(function success(response) {
            $scope.editarpaquete = response.data[0];
        });
    }


    //FUNCION PARA EDITAR LA ENCUESTA
    $scope.editarelpaquete = function (IdPaquete) {
        if (comprobarempty($scope.editarpaquete["Paquete"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {
            $scope.editarpaquete.accion = "editarpaquete";
            $scope.editarpaquete.IdCiudad = IdPaquete;
            servicios.paquete($scope.editarpaquete).then(function success(response) {
                if (response.data == "invalido") {
                    mensajemodal("La Encuesta Ya Se Encuentra Registrada");
                } else {
                    console.log(response.data);
                    mensajemodal("Encuesta Editada Con Éxito");
                    listarencuesta();
                    listarencuestaselect();
                }
            });
        }
    }


    //FUNCION PARA TOMAR EL ID DE LA ENCUESTA A ELIMINAR
    var idEliminarencuesta = "";
    $scope.listarencuestaeliminar = function (IdPaquete) {
        idEliminarencuesta = IdPaquete;
        console.log(idEliminarencuesta);
    }


    //FUNCION PARA ELIMINAR ENCUESTA
    $scope.eliminarencuesta = function () {
        datos = {accion: "eliminarencuesta", IdPaquete: idEliminarencuesta};
        console.log(idEliminarencuesta);
        servicios.paquete(datos).then(function success(response) {
            console.log(response);

            if (response.data == "noeliminar") {
                mensajemodal("La Encuesta No Se Puede Eliminar Contiene Registros");
            } else {
                mensajemodal("La Encuesta Fue Eliminada Con Éxito");
                listarencuesta();
                listarencuestaselect();
            }

        });
    }
///******************************************************************************************///

///*************************************PREGUTNAS********************************************///

    //FUNCION PARA ELIGIR ENCUESTA Y LISTAR LAS PREGUNTAS
    var IdPaquetePregunta = "";
    $scope.elegirpaquete = function (IdPaquete) {
        IdPaquetePregunta = IdPaquete;
        datos = {accion: "elegirpaquete", IdPaquete: IdPaquete};
        servicios.paquete(datos).then(function success(response) {
            $scope.preguntaspaquete = response.data;
        });
    }

    //FUNCION PARA INGRESAR PREGUNTAS A ENCUESTA
    $scope.pregunta = {};

    $scope.ingersarpregunta = function () {
        console.log(IdPaquetePregunta);

        if (comprobarempty(IdPaquetePregunta)) {

            mensajemodal("Debe Seleccionar Una Encuesta Primero");
            return;
        }
        if (comprobarempty($scope.pregunta.Nombrepregunta)) {
            mensajemodal("Debe llenar el campo de pregunta");
            return;
        }
        if (validarSoloLetra($scope.pregunta.Nombrepregunta)) {
            mensajemodal("La pregunta NO debe contener los siguientes caracteres: $ # ´ ` , ");
            return;
        }
        $scope.pregunta.IdEmpresa = $LocalStorage.IdEmpresa;
        $scope.pregunta.accion = "ingersarpregunta";
        $scope.pregunta.IdPaquete = IdPaquetePregunta;
        console.log($scope.pregunta);
        servicios.paquete($scope.pregunta).then(function success(response) {
            if (response.data == "invalido") {
                mensajemodal("La Pregunta Ingresada Ya Se Encuentra Registrada En La Encuesta");
            } else {
                mensajemodal("La Pregunta fue Registrada Con Exito");
                $scope.pregunta.Nombrepregunta = "";
                $scope.elegirpaquete(IdPaquetePregunta);
            }
        });


    }

    ///FUNCION PARA TRAER LA PREGUNTA A EDITAR
    $scope.traerpregunta = function (IdPregunta) {
        datos = {accion: "traerpregunta"};
        datos.IdPregunta = IdPregunta;
        servicios.paquete(datos).then(function success(response) {
            $scope.editarpregunta = response.data[0];
            console.log(response.data[0]);
        });
    }

    //FUNCION PARA EDITAR LA PREGUNTA
    $scope.editarlapregunta = function (IdPregunta) {
        if (comprobarempty($scope.editarpregunta["Pregunta"]))
        {
            mensajemodal("Debe de Llenar Todos Los Campos");
        } else {
            $scope.editarpregunta.accion = "editarpregunta";
            $scope.editarpregunta.IdPregunta = IdPregunta;
            $scope.editarpregunta.IdPaquete = IdPaquetePregunta;
            servicios.paquete($scope.editarpregunta).then(function success(response) {
                if (response.data == "invalido") {
                    mensajemodal("La Pregunta Ingresada Ya Se Encuentra Registrada En La Encuesta");
                } else {
                    console.log(response.data);
                    mensajemodal("La Pregunta Fue Editada Con Éxito");
                    $scope.elegirpaquete(IdPaquetePregunta);
                }
            });
        }
    }

    //FUNCION PARA TOMAR EL ID DE LA PREGUNTA A ELIMINAR
    var idEliminarpregunta = "";
    $scope.listarpreguntaeliminar = function (IdPregunta) {
        idEliminarpregunta = IdPregunta;
        console.log(idEliminarpregunta);
    }

    //FUNCION PARA ELIMINAR PREGUNTA
    $scope.eliminarpregunta = function () {
        datos = {accion: "eliminarpregunta", IdPregunta: idEliminarpregunta};
        console.log(idEliminarpregunta);
        servicios.paquete(datos).then(function success(response) {
            console.log(response);
            if (response.data == "noeliminar") {
                mensajemodal("La Pregunta No Se Puede Eliminar Contiene Registros");
            } else {
                mensajemodal("La Pregunta Fue Eliminada Con Éxito");
                $scope.elegirpaquete(IdPaquetePregunta);
            }
        });
    }


}
