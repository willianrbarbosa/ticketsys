angular.module("ticket_sys").controller("ParametroCtrl", function($scope, PerfilAcessoRotinaAPIService, ParametroAPIService, $location, $filter, $routeParams, growl, config){	
	$scope.aTypes = [{type_id: "E", type_descr: "ENTRADAS"},{type_id: "S", type_descr: "SAÍDAS"},{type_id: "A", type_descr: "ENTRADAS E SAÍDAS"}];
	$scope.aParametros = [];
	$scope.eParam = {};
	$scope.aUserAccess = {};

	$scope.loadParametros = function() {
		delete $scope.aParametros;
		ParametroAPIService.loadParametros().then(function(response){
			$scope.aParametros = response.data;
			if ( $scope.aParametros.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum parâmetro cadastrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Parâmetros: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.newParametro = function(param) {
		param.ctrlaction = 'new';
		ParametroAPIService.saveParametro(param).then(function(response){
			delete $scope.param;
			$scope.newForm.$setPristine();
			$location.path("/parameters");
			$scope.alerta('success', 'Parametro criado com sucesso.');
		});
	};

	$scope.getParametro = function(parKey) {
		ParametroAPIService.getParametro(parKey).then(function(response){
			$scope.eParam = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Parametro: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.editParametro = function(param) {
		param.ctrlaction = 'edit';
		ParametroAPIService.saveParametro(param).then(function(response){
			delete $scope.eParam;
			$scope.edtForm.$setPristine();
			$location.path("/parameters");
			$scope.alerta('success', 'Parametro alterado com sucesso.');
		});
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('parameters').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.parKey ) {
					$scope.getParametro($routeParams.parKey);
				} else {
					$scope.loadParametros();
				}
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();

	$scope.cfgType = {
		create: false,
		valueField: 'type_id',
		labelField: 'type_descr',
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o tipo de Importação',
		maxItems: 1,
	};
});	