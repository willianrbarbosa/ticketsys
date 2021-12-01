angular.module("ticket_sys").controller("EdtPerfilAcessoCtrl", function($scope, PerfilAcessoRotinaAPIService, PerfilAcessoRotinaAPIService, PerfilAcessoAPIService, $location, $filter, $routeParams, growl, config){	
	$scope.ePerfilAcesso	= {};
	$scope.aUserAccess = {};
	$scope.lDeleted = false;

	$scope.operatorfilterBD = '';
	$scope.filterFieldBD = '';
	$scope.sinalfilterBD = '';
	$scope.cfilterBD = '';
	$scope.cFiltroManual = '';
	$scope.lTemFiltro = false;
	
	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 10;
	$scope.nPgTotal = 0;
	$scope.iptsearch = '';

	$scope.aRotinas = [];

	$scope.getPerfilAcesso = function(pfaTK) {
		PerfilAcessoAPIService.getPerfilAcesso(pfaTK).then(function(response){
			$scope.ePerfilAcesso = response.data;	
			$scope.loadRotinaSelecionadasUser(pfaTK);
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Perfil de Acesso: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.editPerfilAcesso = function(perfilAcesso) {
		perfilAcesso.ctrlaction = 'edit';
		PerfilAcessoAPIService.savePerfilAcesso(perfilAcesso).then(function(response){
			$scope.savePerfilRotina();
			$scope.alerta('success', 'Perfil de Acesso alterado com sucesso.');
		});
	};

	$scope.savePerfilRotina = function() {
		$scope.aRotinas[0].perfilID = $scope.ePerfilAcesso.pfa_id;
		$scope.aRotinas[0].ctrlaction = 'new';
		PerfilAcessoRotinaAPIService.savePerfiloRotina($scope.aRotinas).then(function(response){
			$scope.alerta('success', 'Rotinas de acesso atribuidas ao perfil ' + $scope.ePerfilAcesso.pfa_descricao + ' com sucesso.');
			delete $scope.aRotinas;
			delete $scope.ePerfilAcesso;
			$location.path("/accessprofiles");
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
    	return $filter('filter')($scope.aPerfisAcesso, $scope.iptsearch);
    };
    
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getData().length/$scope.numPerPage);                
    };


	$scope.loadRotinaSelecionadasUser = function(rotUserTK) {
		PerfilAcessoRotinaAPIService.loadRotinasSelecionadasPerfil(rotUserTK).then(function(response){
			$scope.aRotinas = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.classe1 = 'selecionado';

	$scope.isSelected = function(perfilAcesso) {
		if ( perfilAcesso ) {
			return perfilAcesso.some(function(respuser){
				return respuser.selecionado;
			});
		}
	};

	$scope.selectAll = function() {	
		$scope.aPerfisAcessoFiltered = $filter('filter')($scope.aPerfisAcesso, $scope.iptsearch);
		for (var i = 0; i < $scope.aPerfisAcesso.length; i++) {
			for (var f = 0; f < $scope.aPerfisAcessoFiltered.length; f++) {
				if ( $scope.aPerfisAcesso[i]['pfa_id'] == $scope.aPerfisAcessoFiltered[f]['pfa_id'] ) {
					$scope.aPerfisAcesso[i]['selecionado'] = $("#checkall").is(":checked");
					break;
				}
			}
		};
	};

	$scope.selectAllRoutines = function() {	
		$scope.aRotinasFiltered = $filter('filter')($scope.aRotinas, $scope.iptsearch);
		for (var i = 0; i < $scope.aRotinas.length; i++) {
			for (var f = 0; f < $scope.aRotinasFiltered.length; f++) {
				if ( $scope.aRotinas[i]['rot_nome'] == $scope.aRotinasFiltered[f]['rot_nome'] ) {
					$scope.aRotinas[i]['selecionado'] = ($("#checkall2").is(":checked") ? 'true' :'false');
					break;
				}
			}
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('accessprofiles').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				$scope.getPerfilAcesso($routeParams.pfaTK);
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();
});	