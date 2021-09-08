var app = angular.module("ticket_sys");
app.controller("TipoMvaPautaCtrl", function($scope, PerfilAcessoRotinaAPIService, TipoMvaPautaAPIService, $location, $timeout, $filter, $routeParams, growl, config){
	$scope.eTipoMvaPauta = [];
	$scope.aTipoMvaPauta = [];
	$scope.aUserAccess = {};
	$scope.url = $location.path();

	$scope.$on('$viewContentLoaded', function() {
		$scope.url = $location.path();
		$scope.getUserAccess();
	});
	
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 10;
	$scope.nPgTotal = 0;
	$scope.iptsearch = '';

	$scope.loadTipoMvaPauta = function() {
		delete $scope.aTipoMvaPauta;
		TipoMvaPautaAPIService.loadTipoMvaPauta().then(function(response){
			$scope.aTipoMvaPauta = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;			
			$scope.nPgTotal = ($scope.aTipoMvaPauta.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aTipoMvaPauta.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Tipo MVA/Pauta cadastrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Tipo MVA/Pauta: " + response.status + " - " + response.statusText;
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aTipoMvaPauta, $scope.iptsearch);
	}

	$scope.numberOfPages=function(){
		return Math.ceil($scope.getData().length/$scope.numPerPage);
	}

	$scope.newTipoMvaPauta = function(tva, out) {
		tva.ctrlaction = 'new';
		TipoMvaPautaAPIService.saveTipoMvaPauta(tva).then(function(response){
			delete $scope.tva;
			delete $scope.aTipoMvaPauta;
			$scope.newForm.$setPristine();
			if ( out ) { $location.path("/typemvapauta"); }
			$scope.alerta('success', 'Tipo MVA/Pauta salvo com sucesso.');
		});
	};

	$scope.deleteTipoMvaPauta = function(tva) {
		BootstrapDialog.show({
			title: '<i class="fa fa-lg fa-warning"></i> Exclusão de Tipo MVA/Pauta',
			message: 'Confirma a exclusão do(s) Tipo MVA/Pauta(s) selecionado(s)?',
			size: BootstrapDialog.SIZE_SMALL,
			type: BootstrapDialog.TYPE_DANGER,
			closable: true,
			draggable: true,
			buttons: [{
				id: 'btn-ok',
				icon: 'fa fa-trash',
				label: 'Excluir',
				cssClass: 'btn-xs btn-danger',
				hotkey: 13, // Enter.
				autospin: false,
				action: function(dialog){
					dialog.enableButtons(false);
					dialog.setClosable(false);
					dialog.getModalBody().html('<i class="fa fa-spinner fa-spin fa-sm fa-fw ft-danger"></i> Excluindo Tipo MVA/Pauta, aguarde...');
					setTimeout(function(){
						$scope.aTipoMvaPauta = tva.filter(function(tva){
							if (tva.selecionado) {
								tva.ctrlaction = 'delete';
								TipoMvaPautaAPIService.deleteTipoMvaPauta(tva).then(function(response){
									$scope.alerta('error', 'Tipo MVA/Pauta(s) excluído(s) com sucesso.');
									delete $scope.aTipoMvaPauta;
									$scope.loadTipoMvaPauta();
								});
							}
						});
						dialog.close();
					}, 1500);
				}
			}, {
				id: 'btn-cancel',
				icon: 'fa fa-ban',
				label: 'Fechar',
				autospin: true,
				hotkey: 65,
				cssClass: 'btn btn-xs btn-default',
				action: function(dialog) {
					dialog.close();
				}
			}]
		});
	};

	$scope.getTipoMvaPauta = function(tvaTK) {
		TipoMvaPautaAPIService.getTipoMvaPauta(tvaTK).then(function(response){
			$scope.eTipoMvaPauta = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Tipo MVA/Pauta: " + response.status + " - " + response.statusText;
		});
	};

	$scope.editTipoMvaPauta = function(tva) {
		tva.ctrlaction = 'edit';
		TipoMvaPautaAPIService.saveTipoMvaPauta(tva).then(function(response){
			delete $scope.eTipoMvaPauta;
			$scope.edtForm.$setPristine();
			$location.path("/typemvapauta");
			$scope.alerta('success', 'Tipo MVA/Pauta alterado com sucesso.');
		});
	};

	$scope.classe1 = 'selecionado';

	$scope.isSelecionado = function(tva) {
		if ( tva ) {
			return tva.some(function(respuser){
				return respuser.selecionado
			});
		}
	};

	$scope.selectAll = function() {
		for (var i = 0; i < $scope.aTipoMvaPauta.length; i++) {
			$scope.aTipoMvaPauta[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.OrderBy = function(sField) {
		$scope.OrderCriterion = sField;
		$scope.directionOrder = !$scope.directionOrder;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('typemvapauta').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				
				if ( $routeParams.tvaTK )
					$scope.getTipoMvaPauta($routeParams.tvaTK);
				else
					$scope.loadTipoMvaPauta();
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Tipo MVA/Pauta: " + response.status + " - " + response.statusText;
		});
	};
});	

app.filter('startFrom', function() {
	return function(input, start) {
		if (!input || !input.length) { return; }
		start = +start; //parse to int
		return input.slice(start);
	}
});

