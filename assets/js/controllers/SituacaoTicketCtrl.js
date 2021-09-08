var app = angular.module("ticket_sys");
app.controller("SituacaoTicketCtrl", function($scope, PerfilAcessoRotinaAPIService, SituacaoTicketAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aSituacaoTicket = [];
	$scope.aUserAccess = {};

	$scope.nSituacaoTicket = {};
	$scope.eSituacaoTicket = {};

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
				$scope.loadSituacaoTicketDeletados($scope.cFiltroManual);
			} else {
				$scope.loadSituacaoTicket($scope.cFiltroManual);
			}
		}
	};

	$scope.loadSituacaoTicket  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aSituacaoTicket ;
		SituacaoTicketAPIService.loadSituacaoTicket(cFilManual).then(function(response){
			$scope.aSituacaoTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aSituacaoTicket.length > $scope.numPerPage ? ($scope.aSituacaoTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aSituacaoTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Situação de Ticket encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Situação de Ticket cadastrada.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Situações de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadSituacaoTicketDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aSituacaoTicket ;
		SituacaoTicketAPIService.loadSituacaoTicketDeletados(cFilManual).then(function(response){
			$scope.aSituacaoTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aSituacaoTicket.length > $scope.numPerPage ? ($scope.aSituacaoTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aSituacaoTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Situação de Ticket excluído foi encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Situação de Ticket foi excluída.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Situações de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aSituacaoTicket, $scope.iptsearch);
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

	$scope.deletaSituacaoTicket = function(situacaoticket) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Situação de Ticket',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' do(s) Situação(ões) de Ticket selecionado(s)?',
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
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'Situações de Ticket, aguarde...');
	                setTimeout(function(){
						$scope.aSituacaoTicket = situacaoticket.filter(function(situacaoticket){
							if (situacaoticket.selecionado) {
								situacaoticket.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								SituacaoTicketAPIService.deletaSituacaoTicket(situacaoticket).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aSituacaoTicket;
									if ( $scope.lDeleted ) {
										$scope.loadSituacaoTicketDeletados($scope.cFiltroManual);
									} else {
										$scope.loadSituacaoTicket($scope.cFiltroManual);
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

	$scope.novoSituacaoTicket = function(situacaoticket, lOut) {
		situacaoticket.ctrlaction = 'new';
		SituacaoTicketAPIService.salvaSituacaoTicket(situacaoticket).then(function(response){			
			delete $scope.aSituacaoTicket;
			$scope.nSituacaoTicket = {};
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/situacaoticket"); 
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Situação de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.getSituacaoTicket = function(sttTK) {
		delete $scope.eSituacaoTicket;
		SituacaoTicketAPIService.getSituacaoTicketByID(sttTK).then(function(response){
			$scope.eSituacaoTicket = response.data;
			$scope.eSituacaoTicket.stt_aprova_ticket = ($scope.eSituacaoTicket.stt_aprova_ticket == 'S' ? true : false);
			$scope.eSituacaoTicket.stt_encerra_ticket = ($scope.eSituacaoTicket.stt_encerra_ticket == 'S' ? true : false);
			$scope.eSituacaoTicket.stt_kanban = ($scope.eSituacaoTicket.stt_kanban == 'S' ? true : false);
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados da Situação de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaSituacaoTicket = function(situacaoticket) {
		situacaoticket.ctrlaction = 'edit';
		SituacaoTicketAPIService.salvaSituacaoTicket(situacaoticket).then(function(response){			
			delete $scope.aSituacaoTicket;
			$scope.eSituacaoTicket = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$location.path("/situacaoticket");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Situação de Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
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
		for (var i = 0; i < $scope.aSituacaoTicket.length; i++) {
			$scope.aSituacaoTicket[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('situacao_ticket').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.sttTK ) {
					$scope.getSituacaoTicket($routeParams.sttTK);
				} else {
					if ( $scope.url != '/novosituacaoticket' ) {
						$scope.loadSituacaoTicket($scope.cFiltroManual);
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
