var app = angular.module("ticket_sys");
app.controller("TicketDashboardCtrl", function($scope, $sce, PerfilAcessoRotinaAPIService, GraficosAPIService, TicketAPIService, UsuarioAPIService, ParametroAPIService, $location, $filter, $compile, $routeParams, growl, config){

	$scope.aTicketsHorasTotais = [];
	$scope.aTicketsTotais = [];

	$scope.ADMINTKT		= null;

	$scope.cDataTKTDe	= null;
	$scope.cDataTKTAte	= null;

	$scope.lDisableFields = false;

	$scope.$on('$viewContentLoaded', function() {
    	var date = new Date();
        setTimeout(function(){	
        	// var primeiroDia = new Date('2020', '01', '01');
			var primeiroDia = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 7);
			var ultimoDia = new Date(date.getFullYear(), date.getMonth(), date.getDate());

		    $scope.cDataTKTDe = moment(new Date(primeiroDia), "Y-m-d").format("DD/MM/YYYY");
		    $scope.cDataTKTAte = moment(new Date(ultimoDia), "Y-m-d").format("DD/MM/YYYY");

		    $("#iptDataTKTDe").datepicker('setDate', $scope.cDataTKTDe);
		    $("#iptDataTKTAte").datepicker('setDate', $scope.cDataTKTAte);

		    $scope.getParametro('ADMINTKT');
		    $scope.loadGraficos($scope.cDataTKTDe, $scope.cDataTKTAte);
        }, 500);
	});
    
    function toggleChart(item) {
        if(item.isVisible()) {
            item.hide();
        } else { 
            item.show();
        }
    }

	$scope.getTotaisTicket = function(cDataDe, cDataAte) {
		delete $scope.aTicketsTotais;
		GraficosAPIService.getTotaisTicket(cDataDe, cDataAte).then(function(response){
			$scope.aTicketsTotais  = response.data;	
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Totais dos Tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.getTotaisHorasTicket = function(cDataDe, cDataAte) {
		delete $scope.aTicketsHorasTotais;
		GraficosAPIService.getTotaisHorasTicket(cDataDe, cDataAte).then(function(response){
			$scope.aTicketsHorasTotais  = response.data;	
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Totais dos Tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.getParametro  = function(param) {
		ParametroAPIService.getParametro(param).then(function(response){
			$scope.ADMINTKT  = response.data.par_conteudo;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar o Parâmetro: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

    var lAutoReload = setInterval(function(){
    	if ( $scope.url == '/ticketdashboard' ) {
    		$scope.getTotaisTicket($scope.cDataTKTDe, $scope.cDataTKTAte);
    		$scope.getTotaisHorasTicket($scope.cDataTKTDe, $scope.cDataTKTAte);
    	}
    }, 300000);

	$scope.loadGraficos = function(cDataDe, cDataAte) {
		$scope.getTotaisTicket(cDataDe, cDataAte);
		$scope.getTotaisHorasTicket(cDataDe, cDataAte);
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('ticket').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;

				if ( $scope.aUserAccess.pta_nivel < 3 ) {
					$scope.lDisableFields = true;
				} else {
					$scope.lDisableFields = false;
				}
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Rotinas do Usuário: " + response.status + " - " + response.statusText);
		});
	};

	$scope.getUserAccess();

    $scope.optChartTotalHorasDia = {
        palette: 'material',
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.HORAS_DIA",
            series: "aTicketsHorasTotais.USERS_RESP",
        },
        title: {
        	text: "Total de horas apontadas por dia/Responsável",
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
        	type: "line",
            argumentField: "dia",
            hoverMode: "none",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                font: {
                    size: 12,
                    weight: 500,
                },
            	wordWrap: "none" ,
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    if ( arg.valueText != '0' ) {
                        return text + 'h';
                    } else {
                        return '';
                    }
                }
            },
        	resolveLabelOverlapping: "shift",
        },
        commonAxisSettings: {
            grid: {
                visible: true,
                color: "#DCDCDC"
            }
        },
        margin: {
            bottom: 5
        },
        argumentAxis: {
            valueMarginsEnabled: false,
            discreteAxisDivisionMode: "crossLabels",
            grid: {
                visible: false,
                color: "#DCDCDC"
            }
        },
        crosshair: {
            enabled: true,
            color: "#33b5e5",
            width: 2,
            dashStyle: "dot",
            label: {
                visible: true,
                backgroundColor: "#33b5e5",                
                font: {
                  color: "#fff",
                  size: 12,
                }
            }                          
        },
        legend: {
            visible: true,
            orientation: "horizontal",
            itemTextPosition: "right",
            horizontalAlignment: "center",
            verticalAlignment: "bottom",
            columnCount: 4,
            font: {
                size: 12
            },
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		},
        tooltip: {
            enabled: true,
            customizeTooltip: function (arg) {
                return {
                    fontColor: '#000',
                    borderColor: '#FFF',
                    text: arg.seriesName + '<br/>' + arg.argumentText + ' (' + arg.valueText + 'h)'
                };
            }
        },
        onLegendClick: function (e) {
            var series = e.target;
            if (series.isVisible()) {
                series.hide();
            } else {
                series.show();
            }
        }
    };

    $scope.optChartTotalHorasEsforcoXReal = {
        palette: ['#595959','#1266F1', '#39C0ED'],
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.EXR",
        },
        title: {
        	text: "Horas de Esforço Estimado X Apontamento Real (EXR) por Responsável",
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "resp_nome",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 2
                },
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    if ( arg.valueText != '0' ) {
                        return text + 'h';
                    } else {
                        return '';
                    }
                }
            }
        },
        series: [
            { valueField: "TOT_ESFORCO", name: "Esforço Estimado" },
            { valueField: "TOT_HORA", name: "Apontamento Real" },
            { valueField: "EXR", name: "% EXR", 
                label: {
                    customizeText: function (arg) {
                        var text = arg.valueText;
                        text = text.replaceAll(",","_");
                        text = text.replaceAll(".",",");
                        text = text.replaceAll("_",".");
                        if ( arg.valueText != '0' ) {
                            return text + ' %';
                        } else {
                            return '';
                        }
                    }
                } 
            }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		},
    };

	$scope.optChartTotaisResp = {
        palette: ['#FFA900','#27bcfd','#00d27a'],
        bindingOptions: {
            dataSource: "aTicketsTotais.RESP",
        },
        title: {
        	text: "Qtde. de Novos Tickts por Responsável",        
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "resp_nome",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 0
                }
            }
        },
        series: [
            { valueField: "qtde_pend", name: "Pendente" },
            { valueField: "qtde_aprov", name: "Aprovado" },
            { valueField: "qtde_enc", name: "Encerrado" }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		},
    };

	$scope.optChartTotalHorasResp = {
        palette: ['#FFA900','#00d27a','#9E9E9E'],
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.RESP",
        },
        title: {
        	text: "Total de horas trabalhadas por Responsável",        
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "resp_nome",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 2
                },
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    if ( arg.valueText != '0' ) {
                        return text + 'h';
                    } else {
                        return '';
                    }
                }
            }
        },
        series: [
            { valueField: "tot_hora_pend", name: "Pendente" },
            { valueField: "tot_hora_enc", name: "Encerrado" },
            { valueField: "tot_hora_total", name: "Total" }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		}
    };

	$scope.optChartTotaisSolic = {
        palette: 'ocean',
        bindingOptions: {
            dataSource: "aTicketsTotais.SOLIC",
        },
        title: {
        	text: "Qtde. de Novos Tickts por Solicitante", 
        	font: {
                size: 18,
                weight: 1000
            }
        },
        legend: {
            visible: true,
            orientation: "horizontal",
            itemTextPosition: "right",
            horizontalAlignment: "center",
            verticalAlignment: "bottom",
            font: {
                size: 12
            },
        },
        series: [{
            argumentField: "solic_nome",
            valueField: "qtde_total",
            label: {
                visible: true,
                font: {
                    size: 12
                },
                connector: {
                    visible: true,
                    width: 2
                },
                position: "columns",
                format: {
                    percentPrecision: 2 // the precision of percentage values (12.3456 % --> 12.34 %)
                },
                customizeText: function (arg) {
                    var porcentagem = arg.percentText;
                    porcentagem = porcentagem.replace(".",",");
                    // arg.argumentText + ":<br/> " + 
                    if ( arg.valueText != '0' ) {
                        return arg.valueText + " (" + porcentagem + ")";
                    } else {
                        return '';
                    }
                }
            }
        }],
        resolveLabelOverlapping: "shift",
		"export": {
			enabled: false
		},
        onPointClick: function (e) {
            var point = e.target;    
            toggleChart(point);
        },
        onLegendClick: function (e) {
            var arg = e.target;    
            toggleChart(e.component.getAllSeries()[0].getPointsByArg(arg)[0]);
        }
    };

	$scope.optChartTotalHorasSolic = {
        palette: ['#FFA900','#00d27a','#9E9E9E'],
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.SOLIC",
        },
        title: {
        	text: "Total de horas trabalhadas por Solicitante",        
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "solic_nome",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 2
                },
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    if ( arg.valueText != '0' ) {
                        return text + 'h';
                    } else {
                        return '';
                    }
                }
            }
        },
        series: [
            { valueField: "tot_hora_pend", name: "Pendente" },
            { valueField: "tot_hora_enc", name: "Encerrado" },
            { valueField: "tot_hora_total", name: "Total" }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		}
    };

	$scope.optChartTotaisCateg = {
        palette: 'pastel',
        bindingOptions: {
            dataSource: "aTicketsTotais.CATEG",
        },
        title: {
        	text: "Qtde. de Novos Tickts por Categoria",
        	font: {
                size: 18,
                weight: 1000
            }
        },
        legend: {
            visible: true,
            orientation: "horizontal",
            itemTextPosition: "right",
            horizontalAlignment: "center",
            verticalAlignment: "bottom",
            font: {
                size: 12
            },
        },
        series: [{
            argumentField: "cgt_descricao",
            valueField: "qtde_total",
            label: {
                visible: true,
                font: {
                    size: 12
                },
                connector: {
                    visible: true,
                    width: 2
                },
                position: "columns",
                format: {
                    percentPrecision: 2 // the precision of percentage values (12.3456 % --> 12.34 %)
                },
                customizeText: function (arg) {
                    var porcentagem = arg.percentText;
                    porcentagem = porcentagem.replace(".",",");
                    // arg.argumentText + ":<br/> " + 
                    if ( arg.valueText != '0' ) {
                        return arg.valueText + " (" + porcentagem + ")";
                    } else {
                        return '';
                    }
                }
            }
        }],
        resolveLabelOverlapping: "shift",
		"export": {
			enabled: false
		},
        onPointClick: function (e) {
            var point = e.target;    
            toggleChart(point);
        },
        onLegendClick: function (e) {
            var arg = e.target;    
            toggleChart(e.component.getAllSeries()[0].getPointsByArg(arg)[0]);
        }
    };

	$scope.optChartTotalHorasCateg = {
        palette: ['#FFA900','#00d27a','#9E9E9E'],
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.CATEG",
        },
        title: {
        	text: "Total de horas trabalhadas por Categoria",        
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "cgt_descricao",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 2
                },
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    if ( arg.valueText != '0' ) {
                        return text + 'h';
                    } else {
                        return '';
                    }
                }
            }
        },
        series: [
            { valueField: "tot_hora_pend", name: "Pendente" },
            { valueField: "tot_hora_enc", name: "Encerrado" },
            { valueField: "tot_hora_total", name: "Total" }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		}
    };

	$scope.optChartTotaisPasta = {
        palette: ['#FFA900','#27bcfd','#00d27a'],
        bindingOptions: {
            dataSource: "aTicketsTotais.PASTA",
        },
        title: {
        	text: "Qtde. de Novos Tickts por Pasta de trabalho",        
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "pst_descricao",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 0
                }
            }
        },
        series: [
            { valueField: "qtde_pend", name: "Pendente" },
            { valueField: "qtde_aprov", name: "Aprovado" },
            { valueField: "qtde_enc", name: "Encerrado" }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		},
    };

	$scope.optChartTotalHorasPasta = {
        palette: ['#FFA900','#00d27a','#9E9E9E'],
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.PASTA",
        },
        title: {
        	text: "Total de horas trabalhadas por Pasta de Trabalho",        
        	font: {
                size: 18,
                weight: 1000
            }
        },
        commonSeriesSettings: {
            argumentField: "pst_descricao",
            type: "bar",
            hoverMode: "allArgumentPoints",
            selectionMode: "allArgumentPoints",
            label: {
                visible: true,
                format: {
                    type: "fixedPoint",
                    precision: 2
                },
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    if ( arg.valueText != '0' ) {
                        return text + 'h';
                    } else {
                        return '';
                    }
                }
            }
        },
        series: [
            { valueField: "tot_hora_pend", name: "Pendente" },
            { valueField: "tot_hora_enc", name: "Encerrado" },
            { valueField: "tot_hora_total", name: "Total" }
        ],
        margin: {
            bottom: 5
        },
        legend: {
            verticalAlignment: "bottom",
            horizontalAlignment: "center"
        },
        valueAxis: {
            showZero: false
        },
		"export": {
			enabled: false
		}
    };

	$scope.optChartTotaisSituacao = {
		palette: ['#1266F1','#00B74A','#B23CFD','#FFA900','#39C0ED'],
        bindingOptions: {
            dataSource: "aTicketsTotais.SITUACAO",
        },
        title: {
        	text: "Qtde. de Novos Tickts por Situação",
        	font: {
                size: 18,
                weight: 1000
            }
        },
        legend: {
            visible: true,
            orientation: "horizontal",
            itemTextPosition: "right",
            horizontalAlignment: "center",
            verticalAlignment: "bottom",
            font: {
                size: 12
            },
        },
        series: [{
            argumentField: "stt_descricao",
            valueField: "qtde_total",
            label: {
                visible: true,
                font: {
                    size: 12
                },
                connector: {
                    visible: true,
                    width: 2
                },
                position: "columns",
                format: {
                    percentPrecision: 2 // the precision of percentage values (12.3456 % --> 12.34 %)
                },
                customizeText: function (arg) {
                    var porcentagem = arg.percentText;
                    porcentagem = porcentagem.replace(".",",");
                    // arg.argumentText + ":<br/> " + 
                    if ( arg.valueText != '0' ) {
                        return arg.valueText + " (" + porcentagem + ")";
                    } else {
                        return '';
                    }
                }
            }
        }],
        resolveLabelOverlapping: "shift",
		"export": {
			enabled: false
		},
        onPointClick: function (e) {
            var point = e.target;    
            toggleChart(point);
        },
        onLegendClick: function (e) {
            var arg = e.target;    
            toggleChart(e.component.getAllSeries()[0].getPointsByArg(arg)[0]);
        }
    };

	$scope.optChartTotalHorasSituacao = {
		palette: ['#1266F1','#00B74A','#B23CFD','#FFA900','#39C0ED'],
        bindingOptions: {
            dataSource: "aTicketsHorasTotais.SITUACAO",
        },
        title: {
        	text: "Total de horas trabalhadas por Situação",
        	font: {
                size: 18,
                weight: 1000
            }
        },
        legend: {
            visible: true,
            orientation: "horizontal",
            itemTextPosition: "right",
            horizontalAlignment: "center",
            verticalAlignment: "bottom",
            font: {
                size: 12
            },
        },
        series: [{
            argumentField: "stt_descricao",
            valueField: "tot_hora_total",
            label: {
                visible: true,
                font: {
                    size: 12
                },
                connector: {
                    visible: true,
                    width: 2
                },
                position: "columns",
                format: {
                    percentPrecision: 2 // the precision of percentage values (12.3456 % --> 12.34 %)
                },
                customizeText: function (arg) {
                    var text = arg.valueText;
                    text = text.replaceAll(",",":");
                    text = text.replaceAll(".",":");
                    var porcentagem = arg.percentText;
                    porcentagem = porcentagem.replace(".",",");
                    // arg.argumentText + ":<br/> " + 
                    if ( arg.valueText != '0' ) {
                        return text + 'h' + " (" + porcentagem + ")";
                    } else {
                        return '';
                    }
                }
            }
        }],
        resolveLabelOverlapping: "shift",
		"export": {
			enabled: false
		},
        onPointClick: function (e) {
            var point = e.target;    
            toggleChart(point);
        },
        onLegendClick: function (e) {
            var arg = e.target;    
            toggleChart(e.component.getAllSeries()[0].getPointsByArg(arg)[0]);
        }
    };

	$('#iptDataTKTDe').mask('00/00/0000');
    $('#iptDataTKTDe').datepicker({
        format: 'dd/mm/yyyy',
    	language: "pt-BR",
    	locale: "pt",
        todayBtn: true,
    	todayHighlight: true,
    	autoclose: true,
        orientation: 'top left',
    }).on('changeDate', function(e) {
        $scope.cDataTKTDe  = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
    });
	
	$('#iptDataTKTAte').mask('00/00/0000');
    $('#iptDataTKTAte').datepicker({
        format: 'dd/mm/yyyy',
    	language: "pt-BR",
    	locale: "pt",
        todayBtn: true,
    	todayHighlight: true,
    	autoclose: true,
        orientation: 'top right',
    }).on('changeDate', function(e) {
        $scope.cDataTKTAte  = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
    });
});	
app.filter('startFrom', function() {
	return function(input, start) {
		start = +start; //parse to int
		return input.slice(start);
	}
});
