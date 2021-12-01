var app = angular.module("ticket_sys");
app.controller("TicketCtrl", function($scope, $sce, PerfilAcessoRotinaAPIService, TicketAPIService, CategoriaTicketAPIService, OrigemTicketAPIService, PastaTrabalhoAPIService, PrioridadeTicketAPIService, SituacaoTicketAPIService, TipoAtividadeAPIService, UsuarioAPIService, UploadFileAPIService, TicketArquivosAPIService, TicketUsuariosAPIService, TicketComentariosAPIService, TicketHistoricoAPIService, TicketApontamentosAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aTicket = [];
	$scope.aTicketArquivos = [];
	$scope.aTicketUsuarios = [];
	$scope.aTicketsPai = [];
	$scope.aUserAccess = {};
	$scope.nTicketArquivo = {};
	$scope.nTicketComentario = {};
	$scope.nTicketApontamento = {};

	$scope.nTicket = {};
	$scope.nTicket.aSolicitante = {};
	$scope.nTicket.aObservadores = [];
	$scope.nTicket.aResponsavel = {};

	$scope.eTicket = {};
	$scope.eTicket.aSolicitante = {};
	$scope.eTicket.aObservadores = [];
	$scope.eTicket.aResponsavel = {};

	$scope.aCategoriaTicket = [];

	$scope.aOrigemTicket = [];

	$scope.aPastaTrabalho = [];

	$scope.aPrioridadeTicket = [];

	$scope.aSituacaoTicket = [];

	$scope.aTipoAtividade = [];

	$scope.aUsuario = [];
	$scope.aUserResp = [];

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

	$scope.lDisableFields = false;
	$scope.lDisableButtons = false;

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
				$scope.loadTicketDeletados($scope.cFiltroManual);
			} else {
				$scope.loadTicket($scope.cFiltroManual);
			}
		}
	};

	$scope.loadTicket  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aTicket ;
		TicketAPIService.loadTicket(cFilManual).then(function(response){
			$scope.aTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aTicket.length > $scope.numPerPage ? ($scope.aTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket cadastrado.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aTicket ;
		TicketAPIService.loadTicketDeletados(cFilManual).then(function(response){
			$scope.aTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aTicket.length > $scope.numPerPage ? ($scope.aTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket excluído foi encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket foi excluído.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets Pai: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketsPai  = function() {
		delete $scope.aTicketsPai;
		TicketAPIService.loadTicketTodosPorUsuario('', 'null').then(function(response){
			$scope.aTicketsPai  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketsUsuarios  = function(tktTK) {
		delete $scope.aTicketUsuarios;
		TicketUsuariosAPIService.getTicketUsuariosPorTicket(tktTK).then(function(response){
			$scope.aTicketUsuarios  = response.data;
			$scope.eTicket.aObservadores = [];
			if ( $scope.aTicketUsuarios.length > 0 ) {
				var nIdxTktUser = null;
				for (var u = 0; u < $scope.aTicketUsuarios.length; u++) {
					$scope.aTicketUsuarios[u].tku_notif_email 		= ($scope.aTicketUsuarios[u].tku_notif_email == 'S' ? true : false);
					$scope.aTicketUsuarios[u].tku_notif_sistema 	= ($scope.aTicketUsuarios[u].tku_notif_sistema == 'S' ? true : false);
					if ( $scope.aTicketUsuarios[u].tku_tipo == 'S' ) {
						nIdxTktUser = u;
					}
					if ( $scope.aTicketUsuarios[u].tku_tipo == 'O' ) {
						$scope.eTicket.aObservadores.push($scope.aTicketUsuarios[u]);
					}
				}
				if ( nIdxTktUser >= 0 ) {
					$scope.eTicket.aSolicitante 					= $scope.aTicketUsuarios[nIdxTktUser];
				} else {
					$scope.eTicket.aSolicitante 					= {};
					$scope.eTicket.aSolicitante.tku_user_id 		= $scope.eTicket.abert_user_id;
					$scope.eTicket.aSolicitante.tku_tipo 			= 'S';
					$scope.eTicket.aSolicitante.tku_notif_email 	= false;
					$scope.eTicket.aSolicitante.tku_notif_sistema 	= true;
				}
				nIdxTktUser = null;
				for (var u = 0; u < $scope.aTicketUsuarios.length; u++) {
					if ( $scope.aTicketUsuarios[u].tku_tipo == 'R' ) {
						nIdxTktUser = u;
					}
				}
				if ( nIdxTktUser >= 0 ) {
					$scope.eTicket.aResponsavel 					= $scope.aTicketUsuarios[nIdxTktUser];
				} else {
					$scope.eTicket.aResponsavel 					= {};
					$scope.eTicket.aResponsavel.tku_tipo 			= 'R';
					$scope.eTicket.aResponsavel.tku_notif_email 	= false;
					$scope.eTicket.aResponsavel.tku_notif_sistema 	= true;
				}
			} else {
				$scope.eTicket.aSolicitante 					= {};
				$scope.eTicket.aSolicitante.tku_user_id 		= $scope.eTicket.abert_user_id;
				$scope.eTicket.aSolicitante.tku_tipo 			= 'S';
				$scope.eTicket.aSolicitante.tku_notif_email 	= true;
				$scope.eTicket.aSolicitante.tku_notif_sistema 	= true;

				$scope.eTicket.aResponsavel 					= {};
				$scope.eTicket.aResponsavel.tku_tipo 			= 'R';
				$scope.eTicket.aResponsavel.tku_notif_email 	= true;
				$scope.eTicket.aResponsavel.tku_notif_sistema 	= true;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Usuários do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketsArquivos  = function(tktTK) {
		TicketArquivosAPIService.getTicketArquivosPorTicket(tktTK).then(function(response){
			$scope.eTicket.aArquivos  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Arquivos do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketHistorico  = function(tktTK) {
		TicketHistoricoAPIService.getTicketHistoricoPorTicket(tktTK).then(function(response){
			$scope.eTicket.aHistorico  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Históricos do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketApontamentos  = function(tktTK) {
		TicketApontamentosAPIService.getTicketApontamentosPorTicket(tktTK).then(function(response){
			$scope.eTicket.aApontamentos  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Apontamentos do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketsComentarios  = function(tktTK) {
		TicketComentariosAPIService.getTicketComentariosPorTicket(tktTK).then(function(response){
			$scope.eTicket.aComentarios  = response.data;
			for (var i = 0; i < $scope.eTicket.aComentarios.length; i++) {
				$scope.eTicket.aComentarios[i].descricao_comentario =  $sce.trustAsHtml($scope.eTicket.aComentarios[i].tkc_descricao);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Comentários do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadMeusTickets  = function(cFilManual) {
		delete $scope.aTicket ;
		TicketAPIService.loadTicketTodosPorUsuario(cFilManual, 'null').then(function(response){
			$scope.aTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aTicket.length > $scope.numPerPage ? ($scope.aTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket cadastrado.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadMeusTrabalhos  = function(cFilManual) {
		delete $scope.aTicket ;
		TicketAPIService.loadTicketPorPendentesResponsavel(cFilManual, 'null').then(function(response){
			$scope.aTicket  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aTicket.length > $scope.numPerPage ? ($scope.aTicket.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aTicket.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket cadastrado.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aTicket, $scope.iptsearch);
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

	$scope.deletaTicket = function(ticket) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de ticket',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' do(s) ticket(s) selecionado(s)?',
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
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'tickets, aguarde...');
	                setTimeout(function(){
						$scope.aTicket = ticket.filter(function(ticket){
							if (ticket.selecionado) {
								ticket.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								TicketAPIService.deletaTicket(ticket).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aTicket;
									if ( $scope.lDeleted ) {
										$scope.loadTicketDeletados($scope.cFiltroManual);
									} else {
										$scope.loadTicket($scope.cFiltroManual);
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

	$scope.novoTicket = function(ticket, lOut, lTicket = false) {
		ticket.ctrlaction = 'new';
		TicketAPIService.salvaTicket(ticket).then(function(response){			
			delete $scope.aTicket;
			$scope.nTicket = {};
			$scope.nTicket.aSolicitante = {};
			$scope.nTicket.aObservadores = [];
			$scope.nTicket.aResponsavel = {};
			$scope.nTicket.tkt_abertura_data = moment(new Date(), "Y-m-d").format("DD/MM/YYYY");
			$("#nov_tkt_abertura_data").datepicker('setDate', $scope.nTicket.tkt_abertura_data);
			setTimeout(function(){
				$scope.nTicket.tkt_pst_id = $scope.aUserData.user_pst_id;
				$scope.nTicket.tkt_abertura_user_id = $scope.aUserData.user_id;
				$scope.nTicket.tkt_tav_id = $scope.aTipoAtividade[0].tav_id;
				$scope.nTicket.aResponsavel.tku_user_id = $scope.aUserData.user_id;
            }, 1000);
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/ticket"); 
			} else {
				if ( lTicket ) { 
					$location.path("/detalheticket/" + response.data.id); 
				}
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.loadCategoriaTicket  = function() {
		delete $scope.aCategoriaTicket ;
		CategoriaTicketAPIService.loadCategoriaTicket('').then(function(response){
			$scope.aCategoriaTicket  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os CategoriaTickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadOrigemTicket  = function() {
		delete $scope.aOrigemTicket ;
		OrigemTicketAPIService.loadOrigemTicket('').then(function(response){
			$scope.aOrigemTicket  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os OrigemTickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadPastaTrabalho  = function() {
		delete $scope.aPastaTrabalho ;
		PastaTrabalhoAPIService.loadPastaTrabalho('').then(function(response){
			$scope.aPastaTrabalho  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os PastaTrabalhos: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadPrioridadeTicket  = function() {
		delete $scope.aPrioridadeTicket ;
		PrioridadeTicketAPIService.loadPrioridadeTicket('').then(function(response){
			$scope.aPrioridadeTicket  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os PrioridadeTickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadSituacaoTicket  = function() {
		delete $scope.aSituacaoTicket ;
		SituacaoTicketAPIService.loadSituacaoTicket('').then(function(response){
			$scope.aSituacaoTicket  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os SituacaoTickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTipoAtividade  = function() {
		delete $scope.aTipoAtividade ;
		TipoAtividadeAPIService.loadTipoAtividade('').then(function(response){
			$scope.aTipoAtividade  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os TipoAtividades: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadUsuario  = function() {
		$scope.aUsuario = [];
		UsuarioAPIService.loadUsuarios().then(function(response){
			$scope.aUsuario  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Usuarios: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadUsuarioRespTicket  = function() {
		$scope.aUserResp = [];
		UsuarioAPIService.loadUsuarioRespTicket('S').then(function(response){
			$scope.aUserResp  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Usuarios: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.getTicket = function(tktTK) {
		delete $scope.eTicket;
		TicketAPIService.getTicketByID(tktTK).then(function(response){
			$scope.eTicket = response.data;
			$scope.eTicket.tkt_abertura_data = moment(new Date($scope.eTicket.tkt_abertura_data)).format("DD/MM/YYYY HH:mm:ss");
			
    		setTimeout(function(){	
				if ( $scope.eTicket.tkt_data_ini_estim ) {
					$scope.eTicket.tkt_data_ini_estim = moment(new Date($scope.eTicket.tkt_data_ini_estim + ' 00:00:00')).format("DD/MM/YYYY");
					$("#edt_tkt_data_ini_estim").datepicker('setDate', $scope.eTicket.tkt_data_ini_estim);
				}	
				if ( $scope.eTicket.tkt_data_fim_estim ) {
					$scope.eTicket.tkt_data_fim_estim = moment(new Date($scope.eTicket.tkt_data_fim_estim + ' 00:00:00')).format("DD/MM/YYYY");
					$("#edt_tkt_data_fim_estim").datepicker('setDate', $scope.eTicket.tkt_data_fim_estim);
				}	
				if ( $scope.eTicket.tkt_data_ini_real ) {
					$scope.eTicket.tkt_data_ini_real = moment(new Date($scope.eTicket.tkt_data_ini_real + ' 00:00:00')).format("DD/MM/YYYY");
					$("#edt_tkt_data_ini_real").datepicker('setDate', $scope.eTicket.tkt_data_ini_real);
				}	
				if ( $scope.eTicket.tkt_data_fim_real ) {
					$scope.eTicket.tkt_data_fim_real = moment(new Date($scope.eTicket.tkt_data_fim_real + ' 00:00:00')).format("DD/MM/YYYY");
					$("#edt_tkt_data_fim_real").datepicker('setDate', $scope.eTicket.tkt_data_fim_real);
				}	

				// $scope.eTicket.tkt_aprovado = ($scope.eTicket.tkt_aprovado == 'S' ? true : false);
				if ( $scope.eTicket.tkt_aprovado_data ) {
					$scope.eTicket.tkt_aprovado_data = moment(new Date($scope.eTicket.tkt_aprovado_data + ' 00:00:00')).format("DD/MM/YYYY");
					$("#edt_tkt_aprovado_data").datepicker('setDate', $scope.eTicket.tkt_aprovado_data);
				}	

				$scope.eTicket.tkt_arquivado = ($scope.eTicket.tkt_arquivado == 'S' ? true : false);
				if ( $scope.eTicket.tkt_arquivado_data ) {
					$scope.eTicket.tkt_arquivado_data = moment(new Date($scope.eTicket.tkt_arquivado_data + ' 00:00:00')).format("DD/MM/YYYY");
					$("#edt_tkt_arquivado_data").datepicker('setDate', $scope.eTicket.tkt_arquivado_data);
				}
        	}, 500);

			if ( $scope.eTicket.tkt_encerrado == 'S' ) {
				$scope.lDisableFields = true;
				$scope.lDisableButtons = true;
			}

			$scope.eTicket.descricao_ticket = $sce.trustAsHtml($scope.eTicket.tkt_descricao);
			$scope.loadTicketsUsuarios($scope.eTicket.tkt_id);
			$scope.loadTicketsArquivos($scope.eTicket.tkt_id);
			$scope.loadTicketsComentarios($scope.eTicket.tkt_id);
			$scope.loadTicketApontamentos($scope.eTicket.tkt_id);
			$scope.loadTicketHistorico($scope.eTicket.tkt_id);

		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados do ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.calculaEsforcoEstimado = function() {
		if ( !$scope.lDisableFields ) {
			var aCalcEsforo = {};
			aCalcEsforo.data_inicial	= $scope.eTicket.tkt_data_ini_estim;
			aCalcEsforo.hora_inicial	= $scope.eTicket.tkt_hora_ini_estim;
			aCalcEsforo.data_final		= $scope.eTicket.tkt_data_fim_estim;
			aCalcEsforo.hora_final		= $scope.eTicket.tkt_hora_fim_estim;
			TicketAPIService.TicketCalculaEsforco(aCalcEsforo).then(function(response){
				if ( response.data.return ) {
					$scope.eTicket.tkt_total_hora_estim = response.data.esforco_hora;
				} else {
					$scope.alerta("error", response.data.msg)
				}
			}).catch(function(response){
				$scope.alerta("error","Falha ao calcular o esforço estimado: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
			});
		}
	};

	$scope.calculaNovoEsforcoEstimado = function() {
		var aCalcEsforo = {};
		aCalcEsforo.data_inicial	= $scope.nTicket.tkt_data_ini_estim;
		aCalcEsforo.hora_inicial	= $scope.nTicket.tkt_hora_ini_estim;
		aCalcEsforo.data_final		= $scope.nTicket.tkt_data_fim_estim;
		aCalcEsforo.hora_final		= $scope.nTicket.tkt_hora_fim_estim;
		TicketAPIService.TicketCalculaEsforco(aCalcEsforo).then(function(response){
			if ( response.data.return ) {
				$scope.nTicket.tkt_total_hora_estim = response.data.esforco_hora;
			} else {
				$scope.alerta("error", response.data.msg)
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao calcular o esforço estimado: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.$watch("nTicket.ticketFile",function(newValue,oldValue){
		if( $scope.nTicket.ticketFile ){
			$scope.uploadNovoTicketFile();
		}
	});
	
	$scope.uploadNovoTicketFile = function() {
		$('#div-novo-upload-img').show("slow");
		$('#novo-uploading-img').html('<div class="loading-img"> Aguarde, fazendo upload do arquivo selecionado...</div>');
		var cFileName = '';
		if ( $scope.nTicket.ticketFile[0] ) {
			cFileName = $scope.nTicket.ticketFile[0].name;
			var fd = new FormData();
			fd.append('file', $scope.nTicket.ticketFile[0]);
			fd.append('origem', 'T');
			UploadFileAPIService.uploadFile(fd).then(function(updfilereturn){
				if ( updfilereturn.data.return == true ) {
					$scope.nTicket.ticket_file = cFileName;
					$('#novo-uploading-img').html('<strong><i class="fa fa-check text-success"></i></strong> ' + updfilereturn.data.retmsg);				
				} else if ( updfilereturn.data.return == false ) {
					$('#novo-uploading-img').html('<strong><i class="fa fa-warning text-danger"></i></strong> ' + updfilereturn.data.retmsg);
				} else {
					$('#novo-uploading-img').html('<strong><i class="fa fa-warning text-danger"></i></strong> Tamanho do arquivo é maior do que o permitido pelo servidor. Verifique!!!');
				}
			});
		}
	};
	
	$scope.uploadEditaTicketFile = function() {
		$('#det_div-upload-img').show("slow");
		$('#det_uploading-img').html('<div class="loading-img"> Aguarde, fazendo upload do arquivo selecionado...</div>');
		var cFileName = '';
		if ( $scope.nTicketArquivo.ticketFile[0] ) {
			cFileName = $scope.nTicketArquivo.ticketFile[0].name;
			var fd = new FormData();
			fd.append('file', $scope.nTicketArquivo.ticketFile[0]);
			fd.append('origem', 'T');
			UploadFileAPIService.uploadFile(fd).then(function(updfilereturn){
				if ( updfilereturn.data.return == true ) {
					$('#det_uploading-img').html('<strong><i class="fa fa-check text-success"></i></strong> ' + updfilereturn.data.retmsg);

					$scope.nTicketArquivo.ctrlaction = 'new';
					$scope.nTicketArquivo.tka_tkt_id = $scope.eTicket.tkt_id;
					$scope.nTicketArquivo.tka_arquivo_nome = cFileName;
					TicketArquivosAPIService.salvaTicketArquivos($scope.nTicketArquivo).then(function(response){	
						if ( response.data.return ) {
							$scope.alerta('success', $('#det_uploading-img').html() + response.data.msg);
							$scope.limpaAnexaArquivo();
							$scope.loadTicketsArquivos($scope.eTicket.tkt_id);
							$scope.loadTicketHistorico($scope.eTicket.tkt_id);
						} else {
							$scope.alerta('error', response.data.msg);
						}
					}).catch(function(response){
						$scope.alerta("error","Falha ao anexar o arquivo selecionado ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
					});
				} else if ( updfilereturn.data.return == false ) {
					$('#det_uploading-img').html('<strong><i class="fa fa-warning text-danger"></i></strong> ' + updfilereturn.data.retmsg);
				} else {
					$('#det_uploading-img').html('<strong><i class="fa fa-warning text-danger"></i></strong> Tamanho do arquivo é maior do que o permitido pelo servidor. Verifique!!!');
				}
			});
		}
	};

	$scope.limpaAnexaArquivo = function() {
		delete $scope.nTicketArquivo.ticketFile;
		$scope.nTicketArquivo = {};
		$scope.formEdtNovoArqTicket.$setPristine();
		$('#det_file_ticket').show("slow");
		$('#det_div-upload-img').hide("slow");
		$('#det_div-selected-files').hide("slow");
		$('#det_selected-files').html();
		$("#mdArquivo").modal("hide");
	};

	$scope.deletaEditaArquivoTicket = function(ticketArquivo) {
		ticketArquivo.ctrlaction = 'delete';
		TicketArquivosAPIService.deletaTicketArquivos(ticketArquivo).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.loadTicketsArquivos($scope.eTicket.tkt_id);
				$scope.loadTicketHistorico($scope.eTicket.tkt_id);
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao excluir o arquivo do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.novoEditaComentarioTicket = function(ticketComentario) {
		ticketComentario.ctrlaction = 'new';
		ticketComentario.tkc_tkt_id = $scope.eTicket.tkt_id;
		TicketComentariosAPIService.salvaTicketComentarios(ticketComentario).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.formEdtNovoArqTicket.$setPristine();
				$scope.nTicketComentario = {};
				$("#mdComentario").modal("hide");
				$scope.loadTicketsComentarios($scope.eTicket.tkt_id);
				$scope.loadTicketHistorico($scope.eTicket.tkt_id);
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar o comentário ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.deletaEditaComentarioTicket = function(ticketComentario) {
		ticketComentario.ctrlaction = 'delete';
		TicketComentariosAPIService.deletaTicketComentarios(ticketComentario).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.loadTicketsComentarios($scope.eTicket.tkt_id);
				$scope.loadTicketHistorico($scope.eTicket.tkt_id);
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao excluir o comentário do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.novoEditaApontamentoTicket = function(ticketApontamento, lOut) {
		ticketApontamento.ctrlaction = 'new';
		ticketApontamento.tkp_tkt_id = $scope.eTicket.tkt_id;
		TicketApontamentosAPIService.salvaTicketApontamentos(ticketApontamento).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.formEdtNovAptmt.$setPristine();
				$scope.nTicketApontamento = {};
				if ( lOut ) { $("#mdApontamento").modal("hide"); }
				$scope.getTicket($scope.eTicket.tkt_id);
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar o apontamento ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.encerraEditaApontamentoTicket = function(ticketApontamento, userTK) {
		$scope.eTicket.apontamento_pendente = ticketApontamento.filter(function(responseTkt){
			if (responseTkt.tkp_user_id == userTK) {				
				responseTkt.ctrlaction = 'stop';
				responseTkt.tkp_tkt_id = $scope.eTicket.tkt_id;
				TicketApontamentosAPIService.salvaTicketApontamentos(responseTkt).then(function(response){	
					if ( response.data.return ) {
						$scope.alerta('success', response.data.msg);
						$scope.eTicket.apontamento_pendente = {};
						$scope.getTicket($scope.eTicket.tkt_id);
					} else {
						$scope.alerta('error', response.data.msg);
					}
				}).catch(function(response){
					$scope.alerta("error","Falha ao encerrar o apontamento ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
				});
			}
		});
	};

	$scope.deletaEditaApontamentoTicket = function(ticketApontamento) {
		ticketApontamento.ctrlaction = 'delete';
		TicketApontamentosAPIService.deletaTicketApontamentos(ticketApontamento).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.getTicket($scope.eTicket.tkt_id);
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao excluir o apontamento do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaTicket = function(ticket, lOut) {
		ticket.ctrlaction = 'edit';
		TicketAPIService.salvaTicket(ticket).then(function(response){			
			delete $scope.aTicket;
			$scope.eTicket = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/ticket"); 
			} else {
				if ( $routeParams.tktTK ) {
					$scope.getTicket($routeParams.tktTK);
				}
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
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
		for (var i = 0; i < $scope.aTicket.length; i++) {
			$scope.aTicket[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.NovoAddObservador = function() {
		$scope.nTicket.aObservadores.push({});
	};

	$scope.EditaAddObservador = function() {
		$scope.eTicket.aObservadores.push({});
	};

	$scope.isUsuarioApontamentoExecucao = function(tktApmto, userTK) {
		if ( tktApmto ) {
			return tktApmto.some(function(responseTkt){
				return responseTkt.tkp_user_id == userTK;
			});
		}
	};    

    var timer = setInterval(function(){
    	if (  $scope.url.indexOf('detalheticket') != -1 ) {
	    	if ( $scope.eTicket.apontamento_pendente ) {
		    	for (var p = 0; p < $scope.eTicket.apontamento_pendente.length; p++) {
		    		var t = new Date($scope.eTicket.apontamento_pendente[p].tkp_data + ' ' + $scope.eTicket.apontamento_pendente[p].tempo_execucao);
					var d = new Date(t.getTime() + 1000);
		    		$scope.eTicket.apontamento_pendente[p].tempo_execucao = ('0'+d.getHours()).substr(-2) + ':' + ('0'+d.getMinutes()).substr(-2) + ':' + ('0'+d.getSeconds()).substr(-2);
		    	}
		        $scope.$apply();
		    }
		}
    }, 1000);  

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('ticket').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;

				if ( $scope.aUserAccess.pta_nivel < 3 ) {
					$scope.lDisableFields = true;
				} else {
					$scope.lDisableFields = false;
				}

				if ( $routeParams.tktTK ) {
					$scope.getTicket($routeParams.tktTK);
				} else {
					if ( $scope.url == '/novoticket' ) {
						$scope.nTicket.tkt_abertura_data = moment(new Date(), "Y-m-d").format("DD/MM/YYYY");
						$("#nov_tkt_abertura_data").datepicker('setDate', $scope.nTicket.tkt_abertura_data);
						setTimeout(function(){
							$scope.nTicket.tkt_pst_id = $scope.aUserData.user_pst_id;
							$scope.nTicket.tkt_abertura_user_id = $scope.aUserData.user_id;
							$scope.nTicket.tkt_tav_id = $scope.aTipoAtividade[0].tav_id;
		                	$scope.nTicket.aResponsavel.tku_user_id = $scope.aUserData.user_id;
		                }, 1000);
					} else if ( $scope.url == '/meustickets' ) {
						$scope.loadMeusTickets($scope.cFiltroManual);
					} else if ( $scope.url == '/meustrabalhos' ) {
						$scope.loadMeusTrabalhos($scope.cFiltroManual);
					} else if ( $scope.url == '/triagemticket' ) {
						$scope.loadTriagemTickets($scope.cFiltroManual);
					} else {
						$scope.loadTicket($scope.cFiltroManual);
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

	$scope.cfgCategoriaTicket = {
		create: false,
		valueField: 'cgt_id',
    	searchField: ['cgt_id','cgt_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) CategoriaTicket',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadCategoriaTicket();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.cgt_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.cgt_descricao ? escape(item.cgt_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.cgt_id) + ' | '
					+ '</strong>'
					+ (item.cgt_descricao ? escape(item.cgt_descricao) : '  ')
					+ '</div>';
			}
		},
	};
	$scope.cfgOrigemTicket = {
		create: false,
		valueField: 'ort_id',
    	searchField: ['ort_id','ort_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) OrigemTicket',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadOrigemTicket();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.ort_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.ort_descricao ? escape(item.ort_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.ort_id) + ' | '
					+ '</strong>'
					+ (item.ort_descricao ? escape(item.ort_descricao) : '  ')
					+ '</div>';
			}
		},
	};
	$scope.cfgPastaTrabalho = {
		create: false,
		valueField: 'pst_id',
    	searchField: ['pst_id','pst_descricao','grt_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) PastaTrabalho',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadPastaTrabalho();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.pst_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.grt_descricao ? escape(item.grt_descricao) : '') + ' > ' + (item.pst_descricao ? escape(item.pst_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.pst_id) + ' | '
					+ '</strong>'
					+ (item.grt_descricao ? escape(item.grt_descricao) : '  ') + ' > '
					+ (item.pst_descricao ? escape(item.pst_descricao) : '  ')
					+ '</div>';
			}
		},
	};
	$scope.cfgPrioridadeTicket = {
		create: false,
		valueField: 'prt_id',
    	searchField: ['prt_id','prt_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) Prioridade de Ticket',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadPrioridadeTicket();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong><i class="fa fa-lg fa-square" style=" color: ' + item.prt_cor + '"></i></strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.prt_descricao ? escape(item.prt_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ '<i class="fa fa-lg fa-square" style=" color: ' + item.prt_cor + '"></i>  '
					+ '</strong>'
					+ (item.prt_descricao ? escape(item.prt_descricao) : '  ')
					+ '</div>';
			}
		},
	};
	$scope.cfgSituacaoTicket = {
		create: false,
		valueField: 'stt_id',
    	searchField: ['stt_id','stt_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) Situações de Ticket',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadSituacaoTicket();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.stt_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.stt_descricao ? escape(item.stt_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.stt_id) + ' | '
					+ '</strong>'
					+ (item.stt_descricao ? escape(item.stt_descricao) : '  ')
					+ '</div>';
			}
		},
	};
	$scope.cfgTipoAtividade = {
		create: false,
		valueField: 'tav_id',
    	searchField: ['tav_id','tav_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) Tipo de Atividade',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadTipoAtividade();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.tav_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.tav_descricao ? escape(item.tav_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.tav_id) + ' | '
					+ '</strong>'
					+ (item.tav_descricao ? escape(item.tav_descricao) : '  ')
					+ '</div>';
			}
		},
	};

	$scope.cfgUsuario = {
		create: false,
		valueField: 'user_id',
    	searchField: ['user_id','user_nome','user_email'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um Usuario',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadUsuario();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="20%"><img class="img-usuario-selectize col-radius" src="src/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '"></td>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.user_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="70%">' + (item.user_nome ? escape(item.user_nome) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<img class="img-usuario-selectize col-radius" src="src/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '">'
					+ '<strong>'
					+ escape(item.user_id) + ' | '
					+ '</strong>'
					+ (item.user_nome ? escape(item.user_nome) : '  ')
					+ '</div>';
			}
		},
	};

	$scope.cfgUserResp = {
		create: false,
		valueField: 'user_id',
    	searchField: ['user_id','user_nome','user_email'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um Usuario',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadUsuarioRespTicket();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="20%"><img class="img-usuario-selectize col-radius" src="src/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '"></td>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.user_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="80%">' + (item.user_nome ? escape(item.user_nome) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<img class="img-usuario-selectize col-radius" src="src/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '">'
					+ '<strong>'
					+ escape(item.user_id) + ' | '
					+ '</strong>'
					+ (item.user_nome ? escape(item.user_nome) : '  ')
					+ '</div>';
			}
		},
	};

	$scope.cfgTicketPai = {
		create: false,
		valueField: 'tkt_id',
    	searchField: ['tkt_id','tkt_titulo'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o Ticket Pai',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadTicketsPai();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.tkt_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.tkt_titulo ? escape(item.tkt_titulo) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.tkt_id) + ' | '
					+ '</strong>'
					+ (item.tkt_titulo ? escape(item.tkt_titulo) : '  ')
					+ '</div>';
			}
		},
	};

	$('#nov_tkt_abertura_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_abertura_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_abertura_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_abertura_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});
	$('#nov_tkt_data_ini_estim').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_data_ini_estim = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_data_ini_estim').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_data_ini_estim = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#nov_tkt_data_ini_real').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_data_ini_real = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_data_ini_real').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_data_ini_real = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#nov_tkt_data_fim_estim').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_data_fim_estim = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_data_fim_estim').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_data_fim_estim = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#nov_tkt_data_fim_real').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_data_fim_real = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_data_fim_real').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_data_fim_real = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#nov_tkt_aprovado_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_aprovado_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_aprovado_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_aprovado_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});
	$('#nov_tkt_arquivado_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicket.tkt_arquivado_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#edt_tkt_arquivado_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.eTicket.tkt_arquivado_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#tkp_data').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.nTicketApontamento.tkp_data = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});
});	
app.filter('startFrom', function() {
	return function(input, start) {
		start = +start; //parse to int
		return input.slice(start);
	}
});
