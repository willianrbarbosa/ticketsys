angular.module("ticket_sys").controller("IndicadoresCtrl", function($scope, PerfilAcessoRotinaAPIService, IndicadoresAPIService, $location, $filter, $routeParams, growl, config){	
	$scope.aIndicadores = [];
	$scope.eInd = {};
	$scope.aTipoInd = [{type_id: "H", type_descr: "Hora"},{type_id: "D", type_descr: "Diário"},{type_id: "S", type_descr: "Semanal"},{type_id: "M", type_descr: "Mensal"}];
	$scope.aUserAccess = {};

	$scope.loadIndicadores = function() {
		delete $scope.aIndicadores;
		IndicadoresAPIService.loadIndicadores().then(function(response){
			$scope.aIndicadores = response.data;
			if ( $scope.aIndicadores.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum parâmetro cadastrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Parâmetros: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.newIndicador = function(ind) {
		ind.ctrlaction = 'new';
		IndicadoresAPIService.saveIndicador(ind).then(function(response){
			delete $scope.ind;
			$scope.newForm.$setPristine();
			$location.path("/indicators");
			$scope.alerta('success', 'Indicador criado com sucesso.');
		});
	};

	$scope.getIndicador = function(indKey) {
		IndicadoresAPIService.getIndicador(indKey).then(function(response){
			$scope.eInd = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Indicador: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.editIndicador = function(ind) {
		ind.ctrlaction = 'edit';
		IndicadoresAPIService.saveIndicador(ind).then(function(response){
			delete $scope.eInd;
			$scope.edtForm.$setPristine();
			$location.path("/indicators");
			$scope.alerta('success', 'Indicador alterado com sucesso.');
		});
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('indicators').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.indKey ) {
					$scope.getIndicador($routeParams.indKey);
				} else {
					$scope.loadIndicadores();
				}
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();

	$scope.cfgTipoInd = {
		create: false,
		valueField: 'type_id',
		labelField: 'type_descr',
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o tipo de Indicador',
		maxItems: 1,
	};
});	