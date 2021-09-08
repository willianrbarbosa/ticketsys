var app = angular.module("ticket_sys");
app.controller("TicketRelDesempenhoCtrl", function($scope, $sce, PerfilAcessoRotinaAPIService, TicketAPIService, CategoriaTicketAPIService, OrigemTicketAPIService, PastaTrabalhoAPIService, PrioridadeTicketAPIService, SituacaoTicketAPIService, TipoAtividadeAPIService, UsuarioAPIService, ReportAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aRelTicketsGerencial = [];

	$scope.aUserAccess = {};

	$scope.aCategoriaTicket = [];
	$scope.aOrigemTicket = [];
	$scope.aPastaTrabalho = [];
	$scope.aPrioridadeTicket = [];
	$scope.aSituacaoTicket = [];
	$scope.aTipoAtividade = [];

	$scope.aUsuario = [];
	$scope.aUserResp = [];

	$scope.reportFilter = {};

	$scope.$on('$viewContentLoaded', function() {
		var date = new Date();
    	setTimeout(function(){			
			var primeiroDia = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 7);
			var ultimoDia = new Date(date.getFullYear(), date.getMonth(), date.getDate());

		    $scope.reportFilter.tkt_rel_desemp_data_de = moment(new Date(primeiroDia), "Y-m-d").format("DD/MM/YYYY");
		    $scope.reportFilter.tkt_rel_desemp_data_ate = moment(new Date(ultimoDia), "Y-m-d").format("DD/MM/YYYY");
		
		    $("#tkt_rel_desemp_data_de").datepicker('setDate', $scope.reportFilter.tkt_rel_desemp_data_de);
		    $("#tkt_rel_desemp_data_ate").datepicker('setDate', $scope.reportFilter.tkt_rel_desemp_data_ate);

			$scope.getUserAccess();
        }, 500);
	});

	$scope.generateReport  = function(RptFilter) {
		delete $scope.aRelTicketsGerencial ;
		$('#div-loading').show("slow");
		$("#loading").html('<div class="loading-img"> Gerando relatório. Aguarde...</div>');
		ReportAPIService.TicketDesempenhoReport(RptFilter).then(function(response){
			$scope.aRelTicketsGerencial  = response.data;
			if ( $scope.aRelTicketsGerencial.HTMLFILE ) {
				$('#div-loading').hide("slow");
				
				$('#pdf_data_de').val(RptFilter.data_de);
				$('#pdf_data_ate').val(RptFilter.data_ate);

				$('#pdf_data_de_ex').val(RptFilter.data_de);
				$('#pdf_data_ate_ex').val(RptFilter.data_ate);				
			} else {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum ticket encontrado para os filtros informados.');
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
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
			$scope.alerta("error","Falha ao carregar os Responsáveis de Tickets: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};  

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('ticket_rel_desemp').then(function(response){
			$('#div-loading').hide();
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Rotinas do Usuário: " + response.status + " - " + response.statusText);
		});
	};


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
                    +    '<td class="text-nowrap left-justify" width="70%">' + (item.user_nome ? escape(item.user_nome) : '') + '</td> '
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
					+ '<img class="img-usuario-selectize col-radius" src="assets/img/sys_images/' + (item.user_photo ? item.user_photo : 'user_default.png') + '">'
					+ '<strong>'
					+ escape(item.user_id) + ' | '
					+ '</strong>'
					+ (item.user_nome ? escape(item.user_nome) : '  ')
					+ '</div>';
			}
		},
	};

    $scope.aTipoRel = [
    	{tpr_id: "1", tpr_descr: "Assertividade Prazo por Responsável"},
    	{tpr_id: "2", tpr_descr: "Assertividade Prazo por Dia encerramento"},
    	{tpr_id: "3", tpr_descr: "Índice EXR por Responsável"},
    	{tpr_id: "4", tpr_descr: "Índice EXR por Dia"}
	];

    $scope.cfgTipoRel = {
		create: false,
		valueField: 'tpr_id',
		labelField: 'tpr_id',
    	searchField: ['tpr_id', 'tpr_descr'],
		delimiter: config.SelDelimiter,
		placeholder: 'Tipo Relatório',
		maxItems: 1,
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="5%"><strong>' + escape(item.tpr_id)+ '</strong>' + ' </td>'
                    +    '<td class="text-nowrap left-justify" width="89%">' + (item.tpr_descr ? escape(item.tpr_descr) : '') + '</td> '  
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.tpr_id) + ' | ' 
					+ '</strong>' 
					+ (item.tpr_descr ? escape(item.tpr_descr) : '  ')  + ' ' 
					+ '</div>';
			}
		},
	};

	$('#tkt_rel_desemp_data_de').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.reportFilter.tkt_rel_desemp_data_de = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});

	$('#tkt_rel_desemp_data_ate').datepicker({
		format: 'dd/mm/yyyy',
		language: 'pt-BR',
		locale: 'pt',
		todayBtn: true,
		todayHighlight: true,
		autoclose: true,
		orientation: 'top left',
	}).on('changeDate', function(e) {
		$scope.reportFilter.tkt_rel_desemp_data_ate = moment(new Date(e.date), "Y-m-d").format("DD/MM/YYYY");
	});
});	
app.filter('startFrom', function() {
	return function(input, start) {
		start = +start; //parse to int
		return input.slice(start);
	}
});
