var app = angular.module("ticket_sys");
app.controller("OrigemTicketCtrl", function($scope, PerfilAcessoRotinaAPIService, OrigemTicketAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aOrigemTicket = [];
	$scope.aUserAccess = {};

	$scope.nOrigemTicket = {};
	$scope.eOrigemTicket = {};

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
				$scope.loadOrigemTicketDeletados($scope.cFiltroManual);
			} else {
				$scope.loadOrigemTicket($scope.cFiltroManual);
			}
		}
	};

	$scope.loadOrigemTicket  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aOrigemTicket ;
		OrigemTicketAPIService.loadOrigemTicket(cFilManual).then(function(response){
			$scope.aOrigemTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aOrigemTicket.length > $scope.numPerPage ? ($scope.aOrigemTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aOrigemTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Origem de Ticket encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Origem de Ticket cadastrada.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Origens de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadOrigemTicketDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aOrigemTicket ;
		OrigemTicketAPIService.loadOrigemTicketDeletados(cFilManual).then(function(response){
			$scope.aOrigemTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aOrigemTicket.length > $scope.numPerPage ? ($scope.aOrigemTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aOrigemTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Origem de Ticket excluído foi encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Origem de Ticket foi excluída.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Origens de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aOrigemTicket, $scope.iptsearch);
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

	$scope.deletaOrigemTicket = function(origemticket) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Origem de Ticket',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' da(s) Origem(ns) de Ticket selecionado(s)?',
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
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'Origens de Ticket, aguarde...');
	                setTimeout(function(){
						$scope.aOrigemTicket = origemticket.filter(function(origemticket){
							if (origemticket.selecionado) {
								origemticket.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								OrigemTicketAPIService.deletaOrigemTicket(origemticket).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aOrigemTicket;
									if ( $scope.lDeleted ) {
										$scope.loadOrigemTicketDeletados($scope.cFiltroManual);
									} else {
										$scope.loadOrigemTicket($scope.cFiltroManual);
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

	$scope.novoOrigemTicket = function(origemticket, lOut) {
		origemticket.ctrlaction = 'new';
		OrigemTicketAPIService.salvaOrigemTicket(origemticket).then(function(response){			
			delete $scope.aOrigemTicket;
			$scope.nOrigemTicket = {};
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/origemticket"); 
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Origem de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.getOrigemTicket = function(ortTK) {
		delete $scope.eOrigemTicket;
		OrigemTicketAPIService.getOrigemTicketByID(ortTK).then(function(response){
			$scope.eOrigemTicket = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados da Origem de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaOrigemTicket = function(origemticket) {
		origemticket.ctrlaction = 'edit';
		OrigemTicketAPIService.salvaOrigemTicket(origemticket).then(function(response){			
			delete $scope.aOrigemTicket;
			$scope.eOrigemTicket = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$location.path("/origemticket");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Origem de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
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
		for (var i = 0; i < $scope.aOrigemTicket.length; i++) {
			$scope.aOrigemTicket[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('origem_ticket').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.ortTK ) {
					$scope.getOrigemTicket($routeParams.ortTK);
				} else {
					if ( $scope.url != '/novoorigemticket' ) {
						$scope.loadOrigemTicket($scope.cFiltroManual);
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
