var app = angular.module("ticket_sys");
app.controller("TicketAgendaCtrl", function($scope, $sce, PerfilAcessoRotinaAPIService, TicketAPIService, CategoriaTicketAPIService, OrigemTicketAPIService, PastaTrabalhoAPIService, PrioridadeTicketAPIService, TipoAtividadeAPIService, UsuarioAPIService, UploadFileAPIService, TicketArquivosAPIService, TicketUsuariosAPIService, TicketComentariosAPIService, TicketHistoricoAPIService, TicketApontamentosAPIService, ParametroAPIService, uiCalendarConfig, $location, $filter, $compile, $routeParams, growl, config){

	$scope.aTicketsAgenda = [];
	$scope.aTicketArquivos = [];
	$scope.aTicketUsuarios = [];
	$scope.aTicketsPai = [];
	$scope.aUserAccess = {};
	$scope.nTicketArquivo = {};
	$scope.nTicketComentario = {};
	$scope.nTicketApontamento = {};
	$scope.aUserData = {};

	$scope.eTicket = {};
	$scope.eTicket.aSolicitante = {};
	$scope.eTicket.aObservadores = [];
	$scope.eTicket.aResponsavel = {};

	$scope.aCategoriaTicket = [];

	$scope.aOrigemTicket = [];

	$scope.aPastaTrabalho = [];

	$scope.aPrioridadeTicket = [];

	$scope.aTipoAtividade = [];
	$scope.filter_resp_id = null;
	$scope.ADMINTKT		= null;

	$scope.aUsuario = [];
	$scope.aUserResp = [];

	$scope.events = [];
	$scope.ticketAltData = new Object();
	$scope.DateFilter;
	$scope.newCalendar;

	$scope.lDisableFields = false;

	$scope.loadTicketsAgenda = function() {
		delete $scope.aTicketsAgenda ;
		$scope.events.length = 0;
		TicketAPIService.loadTicketsAgenda('', $scope.filter_resp_id).then(function(response){
			$scope.aTicketsAgenda  = response.data;		
			var color = '';
			var cTitAux = '';
			for (var i = 0; i < $scope.aTicketsAgenda.length; i++) {
				cTitAux = '';
				if ( $scope.aTicketsAgenda[i].tkt_stt_id == 5 ) {
					cTitAux = '***** EM APROVAÇÃO *****\n';
				} else {
					if ( $scope.aTicketsAgenda[i].PRAZO == 'N' ) {
						cTitAux = '***** ATRASADO *****\n';
					}
				}
				$scope.events.push({
					color: $scope.aTicketsAgenda[i].prt_cor,
					textColor: '#000',
					id: $scope.aTicketsAgenda[i].tkt_id,
					title: cTitAux + '#' + $scope.aTicketsAgenda[i].tkt_id + ' - ' + $scope.aTicketsAgenda[i].tkt_titulo + '\n' + $scope.aTicketsAgenda[i].grt_descricao + ' > ' + $scope.aTicketsAgenda[i].pst_descricao,
					start: $scope.aTicketsAgenda[i].tkt_data_ini_estim+'T'+$scope.aTicketsAgenda[i].tkt_hora_ini_estim,
					end: $scope.aTicketsAgenda[i].tkt_data_fim_estim+'T'+$scope.aTicketsAgenda[i].tkt_hora_fim_estim,
					allDay: false,
				});
			};	
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
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
				$scope.eTicket.aSolicitante.tku_notif_email 	= false;
				$scope.eTicket.aSolicitante.tku_notif_sistema 	= true;

				$scope.eTicket.aResponsavel 					= {};
				$scope.eTicket.aResponsavel.tku_tipo 			= 'R';
				$scope.eTicket.aResponsavel.tku_notif_email 	= false;
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

	$scope.getParametro  = function(param) {
		ParametroAPIService.getParametro(param).then(function(response){
			$scope.ADMINTKT  = response.data.par_conteudo;
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
			
			if ( $scope.eTicket.tkt_data_ini_estim ) {
				$scope.eTicket.tkt_data_ini_estim = moment(new Date($scope.eTicket.tkt_data_ini_estim + ' 00:00:00')).format("DD/MM/YYYY");
				$("#agd_tkt_data_ini_estim").datepicker('setDate', $scope.eTicket.tkt_data_ini_estim);
			}	
			if ( $scope.eTicket.tkt_data_fim_estim ) {
				$scope.eTicket.tkt_data_fim_estim = moment(new Date($scope.eTicket.tkt_data_fim_estim + ' 00:00:00')).format("DD/MM/YYYY");
				$("#agd_tkt_data_fim_estim").datepicker('setDate', $scope.eTicket.tkt_data_fim_estim);
			}	
			if ( $scope.eTicket.tkt_data_ini_real ) {
				$scope.eTicket.tkt_data_ini_real = moment(new Date($scope.eTicket.tkt_data_ini_real + ' 00:00:00')).format("DD/MM/YYYY");
				$("#agd_tkt_data_ini_real").datepicker('setDate', $scope.eTicket.tkt_data_ini_real);
			}	
			if ( $scope.eTicket.tkt_data_fim_real ) {
				$scope.eTicket.tkt_data_fim_real = moment(new Date($scope.eTicket.tkt_data_fim_real + ' 00:00:00')).format("DD/MM/YYYY");
				$("#agd_tkt_data_fim_real").datepicker('setDate', $scope.eTicket.tkt_data_fim_real);
			}	

			// $scope.eTicket.tkt_aprovado = ($scope.eTicket.tkt_aprovado == 'S' ? true : false);
			if ( $scope.eTicket.tkt_aprovado_data ) {
				$scope.eTicket.tkt_aprovado_data = moment(new Date($scope.eTicket.tkt_aprovado_data + ' 00:00:00')).format("DD/MM/YYYY");
				$("#agd_tkt_aprovado_data").datepicker('setDate', $scope.eTicket.tkt_aprovado_data);
			}	

			$scope.eTicket.tkt_arquivado = ($scope.eTicket.tkt_arquivado == 'S' ? true : false);
			if ( $scope.eTicket.tkt_arquivado_data ) {
				$scope.eTicket.tkt_arquivado_data = moment(new Date($scope.eTicket.tkt_arquivado_data + ' 00:00:00')).format("DD/MM/YYYY");
				$("#agd_tkt_arquivado_data").datepicker('setDate', $scope.eTicket.tkt_arquivado_data);
			}

			if ( $scope.eTicket.tkt_encerrado == 'S' ) {
				$scope.lDisableFields = true;
			}

			$scope.eTicket.descricao_ticket = $sce.trustAsHtml($scope.eTicket.tkt_descricao);
			$scope.loadTicketsUsuarios($scope.eTicket.tkt_id);
			$scope.loadTicketsArquivos($scope.eTicket.tkt_id);
			$scope.loadTicketsComentarios($scope.eTicket.tkt_id);
			$scope.loadTicketApontamentos($scope.eTicket.tkt_id);
			$scope.loadTicketHistorico($scope.eTicket.tkt_id);

			$("#mdTicket").modal("show");
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

	$scope.getTicketArquivo = function(ticket) {
		$scope.nTicketArquivo.tka_tkt_id = ticket.tkt_id;
		$scope.nTicketArquivo.tkt_titulo = ticket.tkt_titulo;
		$("#mdArquivo").modal("show");
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
					$scope.nTicketArquivo.tka_arquivo_nome = cFileName;
					TicketArquivosAPIService.salvaTicketArquivos($scope.nTicketArquivo).then(function(response){	
						if ( response.data.return ) {
							$scope.alerta('success', $('#det_uploading-img').html() + response.data.msg);
							$scope.loadTicketsAgenda();
							$scope.limpaAnexaArquivo();
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

	$scope.getTicketComentario = function(ticket) {
		$scope.nTicketComentario.tkc_tkt_id = ticket.tkt_id;
		$scope.nTicketComentario.tkt_titulo = ticket.tkt_titulo;
		$("#mdComentario").modal("show");
	};

	$scope.novoEditaComentarioTicket = function(ticketComentario) {
		ticketComentario.ctrlaction = 'new';
		TicketComentariosAPIService.salvaTicketComentarios(ticketComentario).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.formEdtNovoArqTicket.$setPristine();
				$scope.nTicketComentario = {};
				$("#mdComentario").modal("hide");
				$scope.loadTicketsAgenda();
				if ( $scope.eTicket.tkt_id ) {
					$scope.getTicket($scope.eTicket.tkt_id);
				}
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar o comentário ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.novoKanbanApontamentoTicket = function(ticket, ticketApontamento) {
		ticketApontamento.ctrlaction = 'new';
		ticketApontamento.tkp_tkt_id = ticket.tkt_id;
		TicketApontamentosAPIService.salvaTicketApontamentos(ticketApontamento).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.nTicketApontamento = {};
				$scope.loadTicketsAgenda();
				if ( $scope.eTicket.tkt_id ) {
					$scope.getTicket($scope.eTicket.tkt_id);
				}
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar o apontamento ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.encerraKanbanApontamentoTicket = function(ticket, ticketApontamento, userTK) {
		$scope.eTicket.apontamento_pendente = ticketApontamento.filter(function(responseTkt){
			if (responseTkt.tkp_user_id == userTK) {				
				responseTkt.ctrlaction = 'stop';
				responseTkt.tkp_tkt_id = ticket.tkt_id;
				TicketApontamentosAPIService.salvaTicketApontamentos(responseTkt).then(function(response){	
					if ( response.data.return ) {
						$scope.alerta('success', response.data.msg);
						ticket.apontamento_pendente = {};
						$scope.loadTicketsAgenda();
					} else {
						$scope.alerta('error', response.data.msg);
					}
				}).catch(function(response){
					$scope.alerta("error","Falha ao encerrar o apontamento ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
				});
			}
		});
	};

	$scope.getTicketApontamento = function(ticket) {
		$scope.nTicketApontamento.tkp_tkt_id = ticket.tkt_id;
		$scope.nTicketApontamento.tkt_titulo = ticket.tkt_titulo;
		$("#mdApontamento").modal("show");
	};

	$scope.novoEditaApontamentoTicket = function(ticketApontamento) {
		ticketApontamento.ctrlaction = 'new';
		TicketApontamentosAPIService.salvaTicketApontamentos(ticketApontamento).then(function(response){	
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
				$scope.formEdtNovAptmt.$setPristine();
				$scope.nTicketApontamento = {};
				$("#mdApontamento").modal("hide");
				$scope.loadTicketsAgenda();
				if ( $scope.eTicket.tkt_id ) {
					$scope.getTicket($scope.eTicket.tkt_id);
				}
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar o apontamento ao Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.isUsuarioApontamentoExecucao = function(tktApmto, userTK) {
		if ( tktApmto ) {
			return tktApmto.some(function(responseTkt){
				return responseTkt.tkp_user_id == userTK;
			});
		}
	};

    var lAutoReload = setInterval(function(){
    	if ( $scope.url == '/ticketagenda' ) {
    		$scope.loadTicketsAgenda();
    	}
    }, 180000);


	$scope.getUsuario = function() {
		delete $scope.aUserData;
		UsuarioAPIService.getUsuario('null').then(function(response){
			$scope.aUserData = response.data;
			if ( ($scope.aUserData.user_tipo == '1') ) {
				$scope.filter_resp_id = $scope.aUserData.user_id;
				$scope.getParametro('ADMINTKT');
				$scope.loadTicketsAgenda();
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	/* alert on eventClick */
	$scope.alertOnEventClick = function( data, jsEvent, view){
		$scope.getTicket(data.id);
	}

	/* alert on Drop */
	$scope.alertOnDrop = function(ticket, delta, revertFunc, jsEvent, ui, view){
		$scope.ticketAltData.ctrlaction = 'alt_data';
		$scope.ticketAltData.tktId = ticket.id;
		$scope.ticketAltData.tktDays = delta._days;	
		$scope.ticketAltData.tktTime = delta._data.hours+':'+delta._data.minutes+':'+delta._data.seconds;
		$scope.ticketAltData.tktAltIni = 'S';
		TicketAPIService.salvaTicket($scope.ticketAltData).then(function(response){
			$scope.alerta('success', response.data.msg);
			$scope.loadCalendar();
			uiCalendarConfig.calendars['aptmCalendar'].fullCalendar('refetchEvents');
		});
	};
	/* alert on Resize */
	$scope.alertOnResize = function(ticket, delta, revertFunc, jsEvent, ui, view ){
		$scope.ticketAltData.ctrlaction = 'alt_data';
		$scope.ticketAltData.tktId = ticket.id;
		$scope.ticketAltData.tktDays = delta._days;	
		$scope.ticketAltData.tktTime = delta._data.hours+':'+delta._data.minutes+':'+delta._data.seconds;
		$scope.ticketAltData.tktAltIni = 'N';
		TicketAPIService.salvaTicket($scope.ticketAltData).then(function(response){
			$scope.alerta('success', response.data.msg);
			$scope.loadCalendar();
			uiCalendarConfig.calendars['aptmCalendar'].fullCalendar('refetchEvents');
		});
	};
	/* add and removes an event source of choice */
	$scope.addRemoveEventSource = function(sources,source) {
		var canAdd = 0;
		angular.forEach(sources,function(value, key){
			if(sources[key] === source){
				sources.splice(key,1);
				canAdd = 1;
			}
		});
		if(canAdd === 0){
			sources.push(source);
		}
	};
	/* add custom event*/
	$scope.newEvent = function(date, jsEvent, view) {
		$("#mdNewTicket").modal("show");
	};

	/* remove event */
	$scope.remove = function(index) {
		$scope.events.splice(index,1);
	};
	/* Change View */
	$scope.changeView = function(view,calendar) {
		uiCalendarConfig.calendars[calendar].fullCalendar('changeView',view);
	};
	/* Change View */
	$scope.renderCalender = function(calendar) {
		if(uiCalendarConfig.calendars[calendar]){
			uiCalendarConfig.calendars[calendar].fullCalendar('render');
		}
	};
	/* Render Tooltip */
	$scope.eventRender = function( event, element, view ) { 
		element.attr({'tooltip': event.title, 'tooltip-append-to-body': true});
		$compile(element)($scope);
	};
	/* config object */
	$scope.uiConfig = {
		calendar:{
			height: 650,
			editable: true,
			header:{
				left: 'title',
				center: '',
				right: 'month agendaWeek agendaDay today prev, next '
			},
			allDaySlot: false,
	    	timeFormat: 'HH:mm',
	    	axisFormat: 'HH:mm',
	    	slotLabelFormat: 'HH:mm',
	    	defaultView: 'agendaWeek',
	    	minTime: "07:00:00",
  			maxTime: "18:00:00",
	    	allDayText: 'Dia todo',
	    	dayClick: $scope.newEvent,
			eventClick: $scope.alertOnEventClick,
			eventDrop: $scope.alertOnDrop,
			eventResize: $scope.alertOnResize,
			eventRender: $scope.eventRender
		}
	};

	$scope.changeLang = function() {
		if ( $scope.changeTo === 'PT-BR' ){
			$scope.uiConfig.calendar.dayNames = ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"];
			$scope.uiConfig.calendar.dayNamesShort = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];
			$scope.changeTo = 'EN';
		} else {
			$scope.uiConfig.calendar.dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
			$scope.uiConfig.calendar.dayNamesShort = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
			$scope.changeTo = 'PT-BR';
		}
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('ticketkanban').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;

				if ( $scope.aUserAccess.pta_nivel < 3 ) {
					$scope.lDisableFields = true;
				} else {
					$scope.lDisableFields = false;
				}

				$scope.getUsuario();
				
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
		placeholder: 'Selecione um(a) Pasta de Trabalho para filtrar',
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

	$scope.TicketsAgenda = [$scope.events];

	$('#agd_tkt_abertura_data').datepicker({
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

	$('#agd_tkt_data_ini_estim').datepicker({
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

	$('#agd_tkt_data_ini_real').datepicker({
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

	$('#agd_tkt_data_fim_estim').datepicker({
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

	$('#agd_tkt_data_fim_real').datepicker({
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

	$('#agd_tkt_aprovado_data').datepicker({
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

	$('#agd_tkt_arquivado_data').datepicker({
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
