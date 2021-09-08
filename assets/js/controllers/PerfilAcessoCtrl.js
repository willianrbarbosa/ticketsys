angular.module("ticket_sys").controller("PerfilAcessoCtrl", function($scope, PerfilAcessoRotinaAPIService, PerfilAcessoAPIService, $location, $filter, $routeParams, growl, config){	
	$scope.aPerfisAcesso = [];
	$scope.aRotinas = [];
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
    }

	$scope.loadPerfisAcesso = function(cFilManual) {
		delete $scope.aPerfisAcesso;
		$scope.lDeleted = false;
		PerfilAcessoAPIService.loadPerfisAcessoByCondicao(cFilManual).then(function(response){
			$scope.aPerfisAcesso = response.data;
			if ( $scope.aPerfisAcesso.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Perfil de Acesso cadastrado.');
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Perfis de Acesso: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadDeletedPerfisAcesso = function(cFilManual) {
		delete $scope.aPerfisAcesso;
		$scope.lDeleted = true;
		PerfilAcessoAPIService.loadDeletedPerfisAcessoByCondicao(cFilManual).then(function(response){
			$scope.aPerfisAcesso = response.data;
			if ( $scope.aPerfisAcesso.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Perfil de Acesso deletado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Perfis de Acesso: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
    	return $filter('filter')($scope.aPerfisAcesso, $scope.iptsearch);
    }
    
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getData().length/$scope.numPerPage);                
    }

	$scope.newPerfilAcesso = function(perfilAcesso, out) {
		perfilAcesso.ctrlaction = 'new';
		PerfilAcessoAPIService.savePerfilAcesso(perfilAcesso).then(function(response){
			delete $scope.perfilAcesso;
			delete $scope.aPerfisAcesso;
			$scope.newForm.$setPristine();
			$scope.alerta('success', 'Perfil de Acesso salvo com sucesso.');
			$("#mdNovoComando .close").click();
			if ( out ) { $location.path("/accessprofiles"); }
		});
	};

	$scope.deletePerfilAcesso = function(perfilAcesso) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' +  ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Perfil de Acesso',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' do(s) Perfil(s) de Acesso selecionado(s)?',
	        size: BootstrapDialog.SIZE_SMALL,
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
	                dialog.getModalBody().html('<span id="loading"><div class="loading-img"> ' +  ($scope.lDeleted ? 'restaurando' : 'excluindo') + ' Perfis de Acesso, aguarde...</div></span>');
	                setTimeout(function(){
						$scope.aPerfisAcesso = perfilAcesso.filter(function(perfilAcesso){
							if (perfilAcesso.selecionado) {
								if($scope.lDeleted){
									perfilAcesso.ctrlaction = 'restaura';
								}else{
									perfilAcesso.ctrlaction = 'delete';
								}
								PerfilAcessoAPIService.deletePerfilAcesso(perfilAcesso).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', 'Perfil(s) de Acesso ' +  ($scope.lDeleted ? 'restaurado' : 'excluído') + '(s) com sucesso.');
									} else {
										$scope.alerta('error', response.data.id);
									}
									delete $scope.aPerfisAcesso;
									if ( $scope.lDeleted ) {
										$scope.loadDeletedPerfisAcesso($scope.cFiltroManual);
									} else {
										$scope.loadPerfisAcesso($scope.cFiltroManual);
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

	$scope.getRotinas = function() {
		PerfilAcessoRotinaAPIService.loadRotinas().then(function(response){
			$scope.aRotinas = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
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
					$scope.aRotinas[i]['selecionado'] = $("#checkall2").is(":checked");
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
				if ( $routeParams.cmdTK ) {
					$scope.getPerfilAcesso($routeParams.cmdTK);
				} else {
					$scope.loadPerfisAcesso($scope.cFiltroManual);
					$scope.getRotinas();
				}
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();
});	