var app = angular.module("ticket_sys");
app.controller("LogUsuarioAcessosCtrl", function($scope, UsuarioAPIService, PerfilAcessoRotinaAPIService, $location, $timeout, $filter, $routeParams, growl, config){	
	$scope.aLog = [];
	$scope.aDetLog = {};
	$scope.aLogFiltered = [];
	$scope.aUserAccess = {};
	$scope.aUsuarios = [];
	$scope.nUserTk = 0;
	$scope.reportFilter = {};
	
	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 10;
	$scope.nPgTotal = 0;
	$scope.iptsearch = '';

	$scope.loadUsuarios = function() {
		$('#div-loading').show("slow");
		$('#loading').html('<div class="loading-img"> Carregando listagem dos usuários. Aguarde...</div>');
		delete $scope.error;
		UsuarioAPIService.loadUsuarios().then(function(response){
			$scope.aUsuarios = response.data;
			if ( $scope.aUsuarios.length > 0 ) {
				$('#div-loading').hide("slow");
			} else {
				$scope.nUserTk = 0;
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum usuário encontrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Clientes: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadLogAcessoUsuarios = function() {
		$('#div-loading').show("slow");
		$('#loading').html('<div class="loading-img"> Carregando os Logs. Aguarde...</div>');
		UsuarioAPIService.getUsersAcessos().then(function(response){
			$scope.aLog = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aLog.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aLog.length > 0 ) {
				$('#div-loading').hide("slow");
			} else {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum acesso feito pelo usuário.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Logs: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadLogAcessoUsuariosFilter = function(reportFilter) {
		$('#div-loading').show("slow");
		$('#loading').html('<div class="loading-img"> Carregando os Logs. Aguarde...</div>');

		var data_de = '';
		var data_ate = '';

		if(reportFilter.data_de){
			data_de = reportFilter.data_de.substr(6,4) + '-' + reportFilter.data_de.substr(3,2) + '-' + reportFilter.data_de.substr(0,2);
		}

		if(reportFilter.data_ate){
			data_ate = reportFilter.data_ate.substr(6,4) + '-' + reportFilter.data_ate.substr(3,2) + '-' + reportFilter.data_ate.substr(0,2);	
		}
		
		UsuarioAPIService.getUsersAcessosByFilter(reportFilter.userTk,data_de,data_ate).then(function(response){
			$scope.aLog = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aLog.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aLog.length > 0 ) {
				$('#div-loading').hide("slow");
			} else {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum acesso feito pelo usuário.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Logs: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
    	return $filter('filter')($scope.aLog, $scope.iptsearch);
    }
    
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getData().length/$scope.numPerPage);                
    }

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('usersaccesslog').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				$scope.loadLogAcessoUsuarios();
			} else {				
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
				$location.path("/customerpanel");
          		// window.location.reload();	
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();	

	$('#data_de').mask('00/00/0000');
    $('#data_de').datepicker({
        format: 'dd/mm/yyyy',
    	language: "pt-BR",
    	locale: "pt",
        todayBtn: true,
    	todayHighlight: true,
    	autoclose: true,
        orientation: 'top left',
    }).on('changeDate', function(e) {
        $scope.reportFilter.data_de  = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
    });
	
	$('#data_ate').mask('00/00/0000');
    $('#data_ate').datepicker({
        format: 'dd/mm/yyyy',
    	language: "pt-BR",
    	locale: "pt",
        todayBtn: true,
    	todayHighlight: true,
    	autoclose: true,
        orientation: 'top right',
    }).on('changeDate', function(e) {
        $scope.reportFilter.data_ate  = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
    });

	$scope.cfgUser = {
		create: false,
		valueField: 'user_id',
		labelField: 'user_nome',
    	searchField: ['user_id','user_nome','user_email'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o usuário',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadUsuarios();	
		},
		onChange: function(selectize){
			if(selectize){
				$scope.nUserTk 	  = '';
				$scope.nUserTk = selectize;
				
				$scope.$apply();
			}
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="5%"><strong>' + escape(item.user_id)+ '</strong>' + ' </td>'
                    +    '<td class="text-nowrap left-justify" width="80%"> ' + escape(item.user_nome) + ' </td> '  
                    +    '<td class="text-nowrap left-justify" width="15%"><strong>' + escape(item.user_email) + '</strong></td>'
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.user_id)
					+ '</strong>' + ' | ' 
					+ escape(item.user_nome) 
					+ '</div>';
			}
		},
	};
});	

app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});