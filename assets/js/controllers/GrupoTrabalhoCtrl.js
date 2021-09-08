var app = angular.module("ticket_sys");
app.controller("GrupoTrabalhoCtrl", function($scope, PerfilAcessoRotinaAPIService, GrupoTrabalhoAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aGrupoTrabalho = [];
	$scope.aUserAccess = {};

	$scope.nGrupoTrabalho = {};
	$scope.eGrupoTrabalho = {};

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
				$scope.loadGrupoTrabalhoDeletados($scope.cFiltroManual);
			} else {
				$scope.loadGrupoTrabalho($scope.cFiltroManual);
			}
		}
	};

	$scope.loadGrupoTrabalho  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aGrupoTrabalho ;
		GrupoTrabalhoAPIService.loadGrupoTrabalho(cFilManual).then(function(response){
			$scope.aGrupoTrabalho  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aGrupoTrabalho.length > $scope.numPerPage ? ($scope.aGrupoTrabalho.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aGrupoTrabalho.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Grupo de Trabalho encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Grupo de Trabalho cadastrado.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os grupotrabalhos: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadGrupoTrabalhoDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aGrupoTrabalho ;
		GrupoTrabalhoAPIService.loadGrupoTrabalhoDeletados(cFilManual).then(function(response){
			$scope.aGrupoTrabalho  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aGrupoTrabalho.length > $scope.numPerPage ? ($scope.aGrupoTrabalho.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aGrupoTrabalho.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Grupo de Trabalho excluído foi encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Grupo de Trabalho foi excluído.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Grupos de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aGrupoTrabalho, $scope.iptsearch);
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

	$scope.deletaGrupoTrabalho = function(grupotrabalho) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Grupo de Trabalho',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' do(s) Grupo(s) de Trabalho selecionado(s)?',
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
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'Grupos de Trabalho, aguarde...');
	                setTimeout(function(){
						$scope.aGrupoTrabalho = grupotrabalho.filter(function(grupotrabalho){
							if (grupotrabalho.selecionado) {
								grupotrabalho.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								GrupoTrabalhoAPIService.deletaGrupoTrabalho(grupotrabalho).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aGrupoTrabalho;
									if ( $scope.lDeleted ) {
										$scope.loadGrupoTrabalhoDeletados($scope.cFiltroManual);
									} else {
										$scope.loadGrupoTrabalho($scope.cFiltroManual);
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

	$scope.novoGrupoTrabalho = function(grupotrabalho, lOut) {
		grupotrabalho.ctrlaction = 'new';
		GrupoTrabalhoAPIService.salvaGrupoTrabalho(grupotrabalho).then(function(response){			
			delete $scope.aGrupoTrabalho;
			$scope.nGrupoTrabalho = {};
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/grupotrabalho"); 
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do Grupo de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.getGrupoTrabalho = function(grtTK) {
		delete $scope.eGrupoTrabalho;
		GrupoTrabalhoAPIService.getGrupoTrabalhoByID(grtTK).then(function(response){
			$scope.eGrupoTrabalho = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados do Grupo de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaGrupoTrabalho = function(grupotrabalho) {
		grupotrabalho.ctrlaction = 'edit';
		GrupoTrabalhoAPIService.salvaGrupoTrabalho(grupotrabalho).then(function(response){			
			delete $scope.aGrupoTrabalho;
			$scope.eGrupoTrabalho = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$location.path("/grupotrabalho");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do Grupo de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
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
		for (var i = 0; i < $scope.aGrupoTrabalho.length; i++) {
			$scope.aGrupoTrabalho[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('grupo_trabalho').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.grtTK ) {
					$scope.getGrupoTrabalho($routeParams.grtTK);
				} else {
					if ( $scope.url != '/novogrupotrabalho' ) {
						$scope.loadGrupoTrabalho($scope.cFiltroManual);
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
