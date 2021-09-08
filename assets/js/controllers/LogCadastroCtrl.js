var app = angular.module("ticket_sys");
app.controller("LogCadastroCtrl", function($scope, LogAPIService, UsuarioAPIService, PerfilAcessoRotinaAPIService, $location, $timeout, $filter, $routeParams, growl, config){	
	$scope.aAction = [{act_id: "new", act_descr: "Novo Cadastro"},{act_id: "edit", act_descr: "Edição"},{act_id: "delete", act_descr: "Exclusão"},{act_id: "cad", act_descr: "Vínculo"}];
	$scope.aLog = [];
	$scope.aDetLog = {};
	$scope.aLogFiltered = [];
	$scope.aUserAccess = {};
	$scope.aUsuarios = [];
	$scope.filesFilter = {};

	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 10;
	$scope.nPgTotal = 0;
	$scope.iptsearch = '';

	$scope.loadLogCadastro = function(Flfilter) {
		$('#div-loading').show("slow");
		$('#loading').html('<div class="loading-img"> Carregando listagem dos arquivos. Aguarde...</div>');
		delete $scope.aLog;
		LogAPIService.loadLogCadastro(Flfilter).then(function(response){
			$scope.aLog = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aLog.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aLog.length > 0 ) {
				$('#div-loading').hide("slow");
			} else {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Log de Cadastro encontrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Logs: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getLogCadastro = function(ledTK) {
		LogAPIService.getLogCadastro(ledTK).then(function(response){
			$scope.aDetLog = response.data;			
			$("#mdgetLogCadastro").modal("show");
		}).catch(function(response){
			$scope.error = "Falha ao carregar os detalhes do LOG: " + response.status + ' - ' + response.statusText;
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
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('registrationlog').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				$('#div-loading').hide("slow");
			} else {				
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
				$location.path("/customerpanel");
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadUsuarios = function() {
		delete $scope.aUsuarios;
		UsuarioAPIService.loadUsuarios().then(function(response){
			$scope.aUsuarios = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Usuários: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.cfgUser = {
		create: false,
		valueField: 'user_id',
		labelField: 'user_nome',
		searchField: ['user_id','user_nome'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um usuário para filtrar',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadUsuarios();	
		},
		render: {
			option: function(item, escape) {
	            return '<table class="table" style="margin-bottom: 0px !important;">'
	                +   '<tr>'
	                +    '<td class="text-nowrap left-justify" width="5%"><strong>' + escape(item.user_id)+ '</strong>' + ' </td>'
	                +    '<td class="text-nowrap left-justify" width="95%"> ' + escape(item.user_nome) + ' </td> '  
	                +   '</tr>'
	                + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.user_id) + ' | ' 
					+ '</strong>' 
					+ escape(item.user_nome)
					+ '</div>';
			}
		},
	};

	$scope.cfgAction = {
		create: false,
		valueField: 'act_id',
		labelField: 'act_id',
		searchField: ['act_id','act_descr'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione uma ação',
		maxItems: 1,
		render: {
			option: function(item, escape) {
		        return '<table class="table" style="margin-bottom: 0px !important;">'
		            +   '<tr>'
		            +   '<td class="text-nowrap left-justify" width="5%">' + (item.act_descr ? escape(item.act_descr) : '') + '</td> '  
		            +   '</tr>'
		            + '</table>';
			},
			item: function(item, escape) {
				return '<div>'
					+ escape(item.act_descr)
					+ '</div>';
			}
		}
	};

	$scope.getUserAccess();
});	

app.filter('startFrom', function() {
return function(input, start) {
    start = +start; //parse to int
    return input.slice(start);
}
});

