angular.module('Calificadores').controller('reportesController', InitController);
InitController.$inject = ['$scope', '$state', '$sessionStorage', 'servicios', '$localStorage', '$http', 'urlBase'];
function InitController($scope, $state, $sessionStorage, servicios, $LocalStorage, $http, $urlBase) {

    if ($LocalStorage.usuarioguardado != undefined) {
        if ($LocalStorage.rolguardado == "ADMINISTRADOR") {
            $state.go('reportes');
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

    //FUNCION PARA CONEVRTIR UN OBJETO FECHA
    function convertDatePickerTimeToMySQLTime(str) {
        var month, day, year, hours, minutes, seconds;
        var date = new Date(str),
            month = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);
        // hours = ("0" + date.getHours()).slice(-2);
        // minutes = ("0" + date.getMinutes()).slice(-2);
        // seconds = ("0" + date.getSeconds()).slice(-2);

        var mySQLDate = [date.getFullYear(), month, day].join("-");
        // var mySQLTime = [hours, minutes, seconds].join(":");
        // return [mySQLDate, mySQLTime].join(" ");
        return [mySQLDate].join(" ");
    }

    /*TRAER DATOS PARA REPORTES*/
    listarciudades();
    function listarciudades() {
        datos = { accion: "listarciudades", IdEmpresa: $LocalStorage.IdEmpresa };
        servicios.reporte(datos).then(function success(response) {
            console.log(response);
            $scope.listaciudad = response.data;
        });
    }


    function listarsedes(idciudad) {

        datos = { accion: "listarsedes", idciudad: idciudad };
        servicios.reporte(datos).then(function success(response) {
            console.log(response);
            $scope.listasede = response.data;
        });
    }

    function listarsedesd(idciudad) {
        datos = { accion: "listarsedes", idciudad: idciudad };
        servicios.reporte(datos).then(function success(response) {
            console.log(response);
            $scope.listaseded = response.data;
        });
    }

    listarusuarios();
    function listarusuarios() {
        datos = { accion: "listarusuarios", IdEmpresa: $LocalStorage.IdEmpresa };
        servicios.reporte(datos).then(function success(response) {
            console.log(response);
            $scope.listausuario = response.data;
        });
    }

    listarpreguntas();
    function listarpreguntas() {
        datos = { accion: "listarpreguntas" , IdEmpresa: $LocalStorage.IdEmpresa };
        servicios.reporte(datos).then(function success(response) {
            console.log(response);
            $scope.listarpregunta = response.data;
        });
    }

    var fechamysql = "";
    fechaactual();
    function fechaactual() {
        datos = { accion: "fechaactual" };
        servicios.reporte(datos).then(function success(response) {
            fechamysql = response.data;
        });
    }





    ///DATOS REPORTE GENERAL///
    var gfechainicial = "";
    var gfechafinal = "";
    var gidciudad = "";
    var gIdSede = "";
    var gIdUsuario = "";
    /*FECHA INICAL*/
    $scope.gfechainicial = function (generalfechainicial) {
        gfechainicial = convertDatePickerTimeToMySQLTime(generalfechainicial);
        console.log(gfechainicial);
        if (gfechainicial == "1969-12-31") {
            gfechainicial = "";
            console.log(gfechainicial);
        }
        console.log(gfechainicial);
    }
    /*FEHCA FINAL*/
    $scope.gfechafinal = function (generalfechafinal) {
        gfechafinal = convertDatePickerTimeToMySQLTime(generalfechafinal);
        console.log(gfechafinal);
        if (gfechafinal == "1969-12-31") {
            gfechafinal = "";
            console.log(gfechafinal);
        }
        console.log(gfechafinal);
    }
    /*CIUDAD*/
    $scope.gciudad = function (idciudad) {
        gidciudad = idciudad;

        console.log(gidciudad);
        listarsedes(gidciudad);
    }

    /*SEDE*/
    $scope.gsede = function (IdSede) {
        gIdSede = IdSede;
        if (IdSede == null)
            gIdSede = "";

        console.log(gIdSede);
    }
    /*USUARIO*/
    $scope.gusuario = function (IdUsuario) {
        gIdUsuario = IdUsuario;
        console.log(gIdUsuario);
    }

    var fechafinalgeneral = "";
    $scope.reporteexcelgeneral = function () {
        if (gfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (gfechafinal == "") {

                $scope.reporteexcelgeneral = function () {
                    $http.get($urlBase + "reportegeneral.php?gfechainicial=" + gfechainicial + "& gfechafinal=" + gfechafinal + "& gidciudad=" + gidciudad + "& gIdSede=" + gIdSede + "& gIdUsuario=" + gIdUsuario+ "& IdEmpresa"+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            /*
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                                $scope.descargarReporteExcel = true;
                            }*/
                        });
                };
            } else {
                if (gfechainicial > gfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reporteexcelgeneral = function () {
                        $http.get($urlBase + "reportegeneral.php?gfechainicial=" + gfechainicial + "& gfechafinal=" + gfechafinal + "& gidciudad=" + gidciudad + "& gIdSede=" + gIdSede + "& gIdUsuario=" + gIdUsuario+ "& IdEmpresa"+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                /*
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                                    $scope.descargarReporteExcel = true;
                                }*/
                            });
                    };
                }
            }
        }
    }



    $scope.reportePDFgeneral = function () {
        if (gfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (gfechafinal == "") {

                $scope.reportePDFgeneral = function () {
                    $http.get($urlBase + "ReporteGeneralPDF.php?gfechainicial=" + gfechainicial + "& gfechafinal=" + gfechafinal + "& gidciudad=" + gidciudad + "& gIdSede=" + gIdSede + "& gIdUsuario=" + gIdUsuario+ "& IdEmpresa"+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            /*
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                                $scope.descargarReporteExcel = true;
                            }*/
                        });
                };
            } else {
                if (gfechainicial > gfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reportePDFgeneral = function () {
                        $http.get($urlBase + "ReporteGeneralPDF.php?gfechainicial=" + gfechainicial + "& gfechafinal=" + gfechafinal + "& gidciudad=" + gidciudad + "& gIdSede=" + gIdSede + "& gIdUsuario=" + gIdUsuario+ "& IdEmpresa"+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                /*
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                                    $scope.descargarReporteExcel = true;
                                }*/
                            });
                    };
                }
            }
        }
    }








    $scope.reporteexcelgeneralDetallado = function () {
        if (gfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (gfechafinal == "") {

                $scope.reporteexcelgeneralDetallado = function () {
                    $http.get($urlBase + "reportegeneral.php?gfechainicial=" + gfechainicial + "& gfechafinal=" + gfechafinal + "& gidciudad=" + gidciudad + "& gIdSede=" + gIdSede + "& gIdUsuario=" + gIdUsuario + "& IdEmpresa"+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            /*
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                                $scope.descargarReporteExcel = true;
                            }*/
                        });
                };
            } else {
                if (gfechainicial > gfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reporteexcelgeneralDetallado = function () {
                        $http.get($urlBase + "reportegeneral.php?gfechainicial=" + gfechainicial + "& gfechafinal=" + gfechafinal + "& gidciudad=" + gidciudad + "& gIdSede=" + gIdSede + "& gIdUsuario=" + gIdUsuario + "& IdEmpresa"+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                /*
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reportes/Reporte.xlsx";
                                    $scope.descargarReporteExcel = true;
                                }*/
                            });
                    };
                }
            }
        }
    }







    ///DATOS REPORTE DETALLADO///
    var dfechainicial = "";
    var dfechafinal = "";
    var didciudad = "";
    var dIdSede = "";
    var dIdUsuario = "";
    var dIdPregunta = "";

    /*FECHA INICAL*/
    $scope.dfechainicial = function (detallefechainicial) {
        dfechainicial = convertDatePickerTimeToMySQLTime(detallefechainicial);
        console.log(dfechainicial);
    }
    /*FEHCA FINAL*/
    $scope.dfechafinal = function (detallefechafinal) {
        dfechafinal = convertDatePickerTimeToMySQLTime(detallefechafinal);
        if (dfechafinal == "1969-12-31") {
            dfechafinal = "";
            console.log(gfechafinal);
        }
        console.log(dfechafinal);
    }
    /*CIUDAD*/
    $scope.dciudad = function (deidciudad) {
        console.log(deidciudad);
        didciudad = deidciudad;
        listarsedesd(didciudad);
    }

    /*SEDE*/
    $scope.dsede = function (deIdSede) {
        dIdSede = deIdSede;
        if (dIdSede == null)
            dIdSede = "";
    }
    /*USUARIO*/
    $scope.dusuario = function (deIdUsuario) {
        dIdUsuario = deIdUsuario;
        console.log(dIdUsuario);
    }

    $scope.dpregunta = function (deIdPregunta) {
        dIdPregunta = deIdPregunta;
        console.log(dIdPregunta);
    }


    $scope.reporteexcelgeneralDetallado = function () {
        if (dfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (dfechafinal == "") {

                $scope.reporteexcelgeneralDetallado = function () {
                    $http.get($urlBase + "reportegeneralDetalle.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reportes/ReporteGeneralDetalle.xlsx";
                                $scope.descargarReporteExcel = true;
                            }
                        });
                };
            } else {
                if (dfechainicial > dfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reporteexcelgeneralDetallado = function () {
                        $http.get($urlBase + "reportegeneralDetalle.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reportes/ReporteGeneralDetalle.xlsx";
                                    $scope.descargarReporteExcel = true;
                                }
                            });
                    };
                }
            }
        }
    }




    $scope.reporteexcelgeneralDetalladoPDF = function () {
        if (dfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (dfechafinal == "") {

                $scope.reporteexcelgeneralDetalladoPDF = function () {
                    $http.get($urlBase + "ReporteGeneralDetallePDF.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reportespdf/ReporteGeneralDetalle.pdf";
                                $scope.descargarReporteExcel = true;
                            }
                        });
                };
            } else {
                if (dfechainicial > dfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reporteexcelgeneralDetalladoPDF = function () {
                        $http.get($urlBase + "ReporteGeneralDetallePDF.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reportespdf/ReporteGeneralDetalle.pdf";
                                    $scope.descargarReporteExcel = true;
                                }
                            });
                    };
                }
            }
        }
    }
    //************************************************** */
    $scope.reportegeneralDetalladoCSV = function () {
        if (dfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (dfechafinal == "") {

                $scope.reportegeneralDetalladoCSV = function () {
                    $http.get($urlBase + "ReporteDetalleCSV.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reportescsv/fichero.csv";
                                $scope.descargarReporteExcel = true;
                            }
                        });
                };
            } else {
                if (dfechainicial > dfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reportegeneralDetalladoCSV = function () {
                        $http.get($urlBase + "ReporteDetalleCSV.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reportescsv/fichero.csv";
                                    $scope.descargarReporteExcel = true;
                                }
                            });
                    };
                }
            }
        }
    }


    $scope.reportegeneralDetalladoHTML = function () {
        if (dfechainicial == "") {
            mensajemodal("Debe de Seleccionar la fecha inicial del reporte");
        } else {
            if (dfechafinal == "") {

                $scope.reportegeneralDetalladoHTML = function () {
                    $http.get($urlBase + "ReporteDetalladoHTML.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                        .then(function (response) {
                            if (response.data.respuesta == "Enviado Correctamente") {
                                $('#modalDescarga').modal('show');
                                $scope.MostrarDescarga = true;
                                $scope.linkdescarga = $urlBase + "reporteshtml/Reporte.html";
                                $scope.descargarReporteExcel = true;
                            }
                        });
                };
            } else {
                if (dfechainicial > dfechafinal) {
                    mensajemodal("El Rango De Fechas No Es Valido");
                } else {
                    $scope.reportegeneralDetalladoHTML = function () {
                        $http.get($urlBase + "ReporteDetalladoHTML.php?gfechainicial=" + dfechainicial + "& gfechafinal=" + dfechafinal + "& gidciudad=" + didciudad + "& gIdSede=" + dIdSede + "& gIdUsuario=" + dIdUsuario + "& gPregunta=" + dIdPregunta + "& IdEmpresa="+$LocalStorage.IdEmpresa)
                            .then(function (response) {
                                if (response.data.respuesta == "Enviado Correctamente") {
                                    $('#modalDescarga').modal('show');
                                    $scope.MostrarDescarga = true;
                                    $scope.linkdescarga = $urlBase + "reporteshtml/Reporte.html";
                                    $scope.descargarReporteExcel = true;
                                }
                            });
                    };
                }
            }
        }
    }
}