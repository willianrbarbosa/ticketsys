var app = angular.module("ticket_sys");
app.controller("CategoriaTicketCtrl", function($scope, PerfilAcessoRotinaAPIService, CategoriaTicketAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aCategoriaTicket = [];
	$scope.aUserAccess = {};

	$scope.nCategoriaTicket = {};
	$scope.eCategoriaTicket = {};

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
				$scope.loadCategoriaTicketDeletados($scope.cFiltroManual);
			} else {
				$scope.loadCategoriaTicket($scope.cFiltroManual);
			}
		}
	};

	$scope.loadCategoriaTicket  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aCategoriaTicket ;
		CategoriaTicketAPIService.loadCategoriaTicket(cFilManual).then(function(response){
			$scope.aCategoriaTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aCategoriaTicket.length > $scope.numPerPage ? ($scope.aCategoriaTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aCategoriaTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Categoria de Ticket encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Categoria de Ticket cadastrada.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Categorias de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadCategoriaTicketDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aCategoriaTicket ;
		CategoriaTicketAPIService.loadCategoriaTicketDeletados(cFilManual).then(function(response){
			$scope.aCategoriaTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aCategoriaTicket.length > $scope.numPerPage ? ($scope.aCategoriaTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aCategoriaTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Categoria de Ticket excluído foi encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Categoria de Ticket foi excluída.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Categorias de Atividade: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aCategoriaTicket, $scope.iptsearch);
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

	$scope.deletaCategoriaTicket = function(categoriaticket) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Categoria de Ticket',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' da(s) Categoria(s) de Ticket selecionado(s)?',
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
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'Categorias de Ticket, aguarde...');
	                setTimeout(function(){
						$scope.aCategoriaTicket = categoriaticket.filter(function(categoriaticket){
							if (categoriaticket.selecionado) {
								categoriaticket.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								CategoriaTicketAPIService.deletaCategoriaTicket(categoriaticket).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aCategoriaTicket;
									if ( $scope.lDeleted ) {
										$scope.loadCategoriaTicketDeletados($scope.cFiltroManual);
									} else {
										$scope.loadCategoriaTicket($scope.cFiltroManual);
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

	$scope.novoCategoriaTicket = function(categoriaticket, lOut) {
		categoriaticket.ctrlaction = 'new';
		CategoriaTicketAPIService.salvaCategoriaTicket(categoriaticket).then(function(response){			
			delete $scope.aCategoriaTicket;
			$scope.nCategoriaTicket = {};
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/categoriaticket"); 
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Categoria de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.getCategoriaTicket = function(cgtTK) {
		delete $scope.eCategoriaTicket;
		CategoriaTicketAPIService.getCategoriaTicketByID(cgtTK).then(function(response){
			$scope.eCategoriaTicket = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados da Categoria de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaCategoriaTicket = function(categoriaticket) {
		categoriaticket.ctrlaction = 'edit';
		CategoriaTicketAPIService.salvaCategoriaTicket(categoriaticket).then(function(response){			
			delete $scope.aCategoriaTicket;
			$scope.eCategoriaTicket = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$location.path("/categoriaticket");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Categoria de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
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
		for (var i = 0; i < $scope.aCategoriaTicket.length; i++) {
			$scope.aCategoriaTicket[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('categoria_ticket').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.cgtTK ) {
					$scope.getCategoriaTicket($routeParams.cgtTK);
				} else {
					if ( $scope.url != '/novocategoriaticket' ) {
						$scope.loadCategoriaTicket($scope.cFiltroManual);
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
