var app = angular.module("ticket_sys");
app.controller("TipoAtividadeCtrl", function($scope, PerfilAcessoRotinaAPIService, TipoAtividadeAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aTipoAtividade = [];
	$scope.aUserAccess = {};

	$scope.nTipoAtividade = {};
	$scope.eTipoAtividade = {};

	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 5;
	$scope.nPgTotal = 0;
	$scope.crtPageAc = 0;
	$scope.cPgAc = 0;
	$scope.nPgTotalAc = 0;
	$scope.iptsearch = '';
	$scope.lDeleted= false;

	$scope.operatorfilterBD = '';
	$scope.filterFieldBD = '';
	$scope.sinalfilterBD = '';
	$scope.cfilterBD = '';
	$scope.cFiltroManual = '';
	$scope.lTemFiltro = false;

	// Editor options.
	$scope.ckEditorOptions = {
		textInput: 'pretext',
		options: {
			language: 'pt',
			allowedContent: true,
			entities: false
		}
	};

	$scope.addFiltroManual = function (cOprd, cCampo, cSinal, cConteudo) {
		$scope.cFiltroManual = $scope.cFiltroManual + " " + ($scope.cFiltroManual ? (cOprd ? cOprd : '') : '') + " " + cCampo + " " + cSinal;
		if ( (cSinal == 'LIKE') || (cSinal == 'NOT LIKE') ) {
			$scope.cFiltroManual = $scope.cFiltroManual + " " + "'*"+cConteudo+"*'";
		} else {
			$scope.cFiltroManual = $scope.cFiltroManual + " " + "'"+cConteudo+"'";
		}

		$scope.operatorfilterBD = '';
		$scope.filterFieldBD = '';
		$scope.sinalfilterBD = '';
		$scope.cfilterBD = '';
	};

	$scope.limpaFiltroManual = function (lReload) {
		$scope.cFiltroManual = ''
		$scope.operatorfilterBD = '';
		$scope.filterFieldBD = '';
		$scope.sinalfilterBD = '';
		$scope.cfilterBD = '';
		if ( lReload ) {
			if ( $scope.lDeleted ) {
				$scope.loadTipoAtividadeDeletados($scope.cFiltroManual);
			} else {
				$scope.loadTipoAtividade($scope.cFiltroManual);
			}
		}
	};

	$scope.loadTipoAtividade  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aTipoAtividade ;
		TipoAtividadeAPIService.loadTipoAtividade(cFilManual).then(function(response){
			$scope.aTipoAtividade  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aTipoAtividade.length > $scope.numPerPage ? ($scope.aTipoAtividade.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aTipoAtividade.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Tipo de Atividade encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Tipo de Atividade cadastrado.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Tipos de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTipoAtividadeDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aTipoAtividade ;
		TipoAtividadeAPIService.loadTipoAtividadeDeletados(cFilManual).then(function(response){
			$scope.aTipoAtividade  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aTipoAtividade.length > $scope.numPerPage ? ($scope.aTipoAtividade.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aTipoAtividade.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Tipo de Atividade excluído foi encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Tipo de Atividade foi excluído.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Tipos de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aTipoAtividade, $scope.iptsearch);
	}

	$scope.numberOfPages=function(){
		return Math.ceil($scope.getData().length/$scope.numPerPage);
	}

	$scope.range = function(min, max, step){
		step = step || 1;
		var input = [];
		for (var i = min; i <= max; i += step) input.push(i);
		return input;
	};

	$scope.changePage = function(nIdx) {
		$scope.cPg = nIdx;
		$scope.currentPage = nIdx-1;
	};

	$scope.deletaTipoAtividade = function(tipoatividade) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Tipo de Atividade',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' do(s) Tipo(s) de Atividade selecionado(s)?',
	        size: BootstrapDialog.SIZE_MEDIUM,
	        type: ($scope.lDeleted ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER),
	        closable: true,
	        draggable: true,
	        buttons: [{
	            id: 'btn-ok',
		        icon: 'fa' +  ($scope.lDeleted ? 'fa-window-restore' : 'fa-trash'),
		        label: ($scope.lDeleted ? 'Restaurar' : 'Excluir'),
		        cssClass: 'btn-xs ' +  ($scope.lDeleted ? 'btn-success' : 'btn-danger'), 
	            hotkey: 13, // Enter.
		        autospin: false,
		        action: function(dialog){
	                dialog.enableButtons(false);
	                dialog.setClosable(false);
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'Tipos de Atividade, aguarde...');
	                setTimeout(function(){
						$scope.aTipoAtividade = tipoatividade.filter(function(tipoatividade){
							if (tipoatividade.selecionado) {
								tipoatividade.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								TipoAtividadeAPIService.deletaTipoAtividade(tipoatividade).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aTipoAtividade;
									if ( $scope.lDeleted ) {
										$scope.loadTipoAtividadeDeletados($scope.cFiltroManual);
									} else {
										$scope.loadTipoAtividade($scope.cFiltroManual);
									}
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

	$scope.novoTipoAtividade = function(tipoatividade, lOut) {
		tipoatividade.ctrlaction = 'new';
		TipoAtividadeAPIService.salvaTipoAtividade(tipoatividade).then(function(response){			
			delete $scope.aTipoAtividade;
			$scope.nTipoAtividade = {};
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/tipoatividade"); 
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do Tipo de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.getTipoAtividade = function(tavTK) {
		delete $scope.eTipoAtividade;
		TipoAtividadeAPIService.getTipoAtividadeByID(tavTK).then(function(response){
			$scope.eTipoAtividade = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados do Tipo de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaTipoAtividade = function(tipoatividade) {
		tipoatividade.ctrlaction = 'edit';
		TipoAtividadeAPIService.salvaTipoAtividade(tipoatividade).then(function(response){			
			delete $scope.aTipoAtividade;
			$scope.eTipoAtividade = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$location.path("/tipoatividade");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do Tipo de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.isSelected = function(user) {
		if ( user ) {
			return user.some(function(respuser){
				return respuser.selecionado
			});
		}
	};

	$scope.selectAll = function() {
		for (var i = 0; i < $scope.aTipoAtividade.length; i++) {
			$scope.aTipoAtividade[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('tipo_atividade').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.tavTK ) {
					$scope.getTipoAtividade($routeParams.tavTK);
				} else {
					if ( $scope.url != '/novotipoatividade' ) {
						$scope.loadTipoAtividade($scope.cFiltroManual);
					}
				}
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Rotinas do Usuário: " + response.status + " - " + response.statusText);
		});
	};

	$scope.getUserAccess();


});	
app.filter('startFrom', function() {
	return function(input, start) {
		start = +start; //parse to int
		return input.slice(start);
	}
});
