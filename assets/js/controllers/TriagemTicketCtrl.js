var app = angular.module("ticket_sys");
app.controller("TriagemTicketCtrl", function($scope, $sce, PerfilAcessoRotinaAPIService, TicketAPIService, CategoriaTicketAPIService, OrigemTicketAPIService, PastaTrabalhoAPIService, PrioridadeTicketAPIService, SituacaoTicketAPIService, TipoAtividadeAPIService, UsuarioAPIService, UsuarioAPIService, UsuarioAPIService, UploadFileAPIService, TicketArquivosAPIService, TicketUsuariosAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aTicket = [];
	$scope.aTicketArquivos = [];
	$scope.aTicketUsuarios = [];
	$scope.aTicketsPai = [];
	$scope.aUserAccess = {};

	$scope.aCategoriaTicket = [];

	$scope.aOrigemTicket = [];

	$scope.aPastaTrabalho = [];

	$scope.aPrioridadeTicket = [];

	$scope.aSituacaoTicket = [];

	$scope.aTipoAtividade = [];

	$scope.aUsuario = [];
	$scope.aUserResp = [];

	$scope.lCarregandoTicket  = false;
	$scope.nWorkTicket = null;

	$scope.loadTriagemTickets  = function() {
    	$scope.lCarregandoTicket = true;
		$("#ico-first").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-prev").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-save-next").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-next").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-last").addClass("fa-spinner fa-spin fa-sm fa-fw");

		delete $scope.aTicket ;
		TicketAPIService.loadTriagemTickets('').then(function(response){
			$scope.aTicket  = response.data;
			
			if ( $scope.aTicket.length <= 0 ) {
				$('#loading').html('<span class="ft-success"><strong><i class="fa fa-3x fa-thumbs-up"></i></strong><br/> Nenhum Ticket pendente de Triagem.</span>');
			} else {
				$scope.setTicketAtual(0);
			}

			// $("#tri_tkt_abertura_user_id").selectize()[0].selectize.disable();

    		$scope.lCarregandoTicket = false;
			$("#ico-first").removeClass("fa-spinner fa-spin fa-sm fa-fw");
			$("#ico-prev").removeClass("fa-spinner fa-spin fa-sm fa-fw");
			$("#ico-save-next").removeClass("fa-spinner fa-spin fa-sm fa-fw");
			$("#ico-next").removeClass("fa-spinner fa-spin fa-sm fa-fw");
			$("#ico-last").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.setTicketAtual = function(nAction) {
		$scope.lCarregandoTicket = true;
		$("#ico-first").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-prev").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-save-next").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-next").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-last").addClass("fa-spinner fa-spin fa-sm fa-fw");

		$scope.nWorkTicket = nAction;
		$scope.nWorkTicketAux = nAction + 1;

		$scope.aTicket[$scope.nWorkTicket].tkt_abertura_data = moment(new Date($scope.aTicket[$scope.nWorkTicket].tkt_abertura_data)).format("DD/MM/YYYY HH:mm:ss");
		// $("#tri_tkt_abertura_data").datepicker('setDate', $scope.aTicket[$scope.nWorkTicket].tkt_abertura_data);
		if ( $scope.aTicket[$scope.nWorkTicket].tkt_data_ini_estim ) {
			$scope.aTicket[$scope.nWorkTicket].tkt_data_ini_estim = moment(new Date($scope.aTicket[$scope.nWorkTicket].tkt_data_ini_estim + ' 00:00:00')).format("DD/MM/YYYY");
			$("#tri_tkt_data_ini_estim").datepicker('setDate', $scope.aTicket[$scope.nWorkTicket].tkt_data_ini_estim);
		}	
		if ( $scope.aTicket[$scope.nWorkTicket].tkt_data_fim_estim ) {
			$scope.aTicket[$scope.nWorkTicket].tkt_data_fim_estim = moment(new Date($scope.aTicket[$scope.nWorkTicket].tkt_data_fim_estim + ' 00:00:00')).format("DD/MM/YYYY");
			$("#tri_tkt_data_fim_estim").datepicker('setDate', $scope.aTicket[$scope.nWorkTicket].tkt_data_fim_estim);
		}	

		$scope.aTicket[$scope.nWorkTicket].descricao_ticket = $sce.trustAsHtml($scope.aTicket[$scope.nWorkTicket].tkt_descricao);
		$scope.aTicket[$scope.nWorkTicket].tkt_tav_id = 1;			
		$("#tri_tkt_tav_id").selectize()[0].selectize.setValue(1);

		$scope.loadTicketsUsuarios($scope.aTicket[$scope.nWorkTicket].tkt_id);
		$scope.loadTicketsArquivos($scope.aTicket[$scope.nWorkTicket].tkt_id);

		$scope.lCarregandoTicket = false;
		$("#ico-first").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-prev").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-save-next").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-next").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-last").removeClass("fa-spinner fa-spin fa-sm fa-fw");
	};

	$scope.calculaEsforcoEstimado = function() {
		var aCalcEsforo = {};
		aCalcEsforo.data_inicial	= $scope.aTicket[$scope.nWorkTicket].tkt_data_ini_estim;
		aCalcEsforo.hora_inicial	= $scope.aTicket[$scope.nWorkTicket].tkt_hora_ini_estim;
		aCalcEsforo.data_final		= $scope.aTicket[$scope.nWorkTicket].tkt_data_fim_estim;
		aCalcEsforo.hora_final		= $scope.aTicket[$scope.nWorkTicket].tkt_hora_fim_estim;
		TicketAPIService.TicketCalculaEsforco(aCalcEsforo).then(function(response){
			if ( response.data.return ) {
				$scope.aTicket[$scope.nWorkTicket].tkt_total_hora_estim = response.data.esforco_hora;
			} else {
				$scope.alerta("error", response.data.msg)
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao calcular o esforço estimado: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
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
			$scope.aTicket[$scope.nWorkTicket].aObservadores = [];
			if ( $scope.aTicketUsuarios.length > 0 ) {
				var nIdxTktUser = null;
				for (var u = 0; u < $scope.aTicketUsuarios.length; u++) {
					$scope.aTicketUsuarios[u].tku_notif_email 		= ($scope.aTicketUsuarios[u].tku_notif_email == 'S' ? true : false);
					$scope.aTicketUsuarios[u].tku_notif_sistema 	= ($scope.aTicketUsuarios[u].tku_notif_sistema == 'S' ? true : false);
					if ( $scope.aTicketUsuarios[u].tku_tipo == 'S' ) {
						nIdxTktUser = u;
					}
					if ( $scope.aTicketUsuarios[u].tku_tipo == 'O' ) {
						$scope.aTicket[$scope.nWorkTicket].aObservadores.push($scope.aTicketUsuarios[u]);
					}
				}
				if ( nIdxTktUser >= 0 ) {
					$scope.aTicket[$scope.nWorkTicket].aSolicitante 					= $scope.aTicketUsuarios[nIdxTktUser];
				} else {
					$scope.aTicket[$scope.nWorkTicket].aSolicitante 					= {};
					$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_user_id 		= $scope.aTicket[$scope.nWorkTicket].abert_user_id;
					$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_tipo 			= 'S';
					$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_notif_email 	= true;
					$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_notif_sistema 	= true;
				}
				nIdxTktUser = null;
				for (var u = 0; u < $scope.aTicketUsuarios.length; u++) {
					if ( $scope.aTicketUsuarios[u].tku_tipo == 'R' ) {
						nIdxTktUser = u;
					}
				}
				if ( nIdxTktUser >= 0 ) {
					$scope.aTicket[$scope.nWorkTicket].aResponsavel 					= $scope.aTicketUsuarios[nIdxTktUser];
				} else {
					$scope.aTicket[$scope.nWorkTicket].aResponsavel 					= {};
					$scope.aTicket[$scope.nWorkTicket].aResponsavel.tku_tipo 			= 'R';
					$scope.aTicket[$scope.nWorkTicket].aResponsavel.tku_notif_email 	= true;
					$scope.aTicket[$scope.nWorkTicket].aResponsavel.tku_notif_sistema 	= true;
				}
			} else {
				$scope.aTicket[$scope.nWorkTicket].aSolicitante 					= {};
				$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_user_id 		= $scope.aTicket[$scope.nWorkTicket].abert_user_id;
				$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_tipo 			= 'S';
				$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_notif_email 	= true;
				$scope.aTicket[$scope.nWorkTicket].aSolicitante.tku_notif_sistema 	= true;

				$scope.aTicket[$scope.nWorkTicket].aResponsavel 					= {};
				$scope.aTicket[$scope.nWorkTicket].aResponsavel.tku_tipo 			= 'R';
				$scope.aTicket[$scope.nWorkTicket].aResponsavel.tku_notif_email 	= true;
				$scope.aTicket[$scope.nWorkTicket].aResponsavel.tku_notif_sistema 	= true;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Usuários do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadTicketsArquivos  = function(tktTK) {
		delete $scope.aTicketArquivos;
		TicketArquivosAPIService.getTicketArquivosPorTicket(tktTK).then(function(response){
			$scope.aTicket[$scope.nWorkTicket].aArquivos  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Usuários do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
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

	$scope.triagemTicket = function(ticket) {
		$scope.lCarregandoTicket = true;
		$("#ico-first").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-prev").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-save-next").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-next").addClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-last").addClass("fa-spinner fa-spin fa-sm fa-fw");

		TicketAPIService.triagemTicket(ticket).then(function(response){
			$scope.triagemForm.$setPristine();
			if ( response.data.return ) {
				$scope.loadTriagemTickets();
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
		$scope.lCarregandoTicket = false;
		$("#ico-first").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-prev").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-save-next").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-next").removeClass("fa-spinner fa-spin fa-sm fa-fw");
		$("#ico-last").removeClass("fa-spinner fa-spin fa-sm fa-fw");
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.TriagemAddObservador = function() {
		$scope.aTicket[$scope.nWorkTicket].aObservadores.push({});
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('triagemTicket').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				$scope.loadTriagemTickets();
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
                    +    '<td class="text-nowrap left-justify" width="20%"><img class="img-usuario-selectize col-radius" src="assets/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '"></td>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.user_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="80%">' + (item.user_nome ? escape(item.user_nome) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<img class="img-usuario-selectize col-radius" src="assets/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '">'
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
                    +    '<td class="text-nowrap left-justify" width="20%"><img class="img-usuario-selectize col-radius" src="assets/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '"></td>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.user_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="80%">' + (item.user_nome ? escape(item.user_nome) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.user_id) + ' | '
					+ '</strong>'
					+ (item.user_nome ? escape(item.user_nome) : '  ')
					+ '<img class="img-usuario-selectize col-radius" src="assets/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '">'
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

	$('#tri_tkt_data_ini_estim').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.aTicket[$scope.nWorkTicket].tkt_data_ini_estim = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#tri_tkt_data_fim_estim').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.aTicket[$scope.nWorkTicket].tkt_data_fim_estim = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});
});	
app.filter('startFrom', function() {
	return function(input, start) {
		start = +start; //parse to int
		return input.slice(start);
	}
});
