angular.module("ticket_sys").config(function($routeProvider, config){	
	$routeProvider.when("/login", {
		templateUrl: config.urlBaseView + "login.view.html",
		controller: "TicketSysCtrl"
	});
	$routeProvider.when("/users", {
		templateUrl: config.urlBaseView + "usuarios.view.html",
		controller: "UsuarioCtrl"
	});

	$routeProvider.when("/newuser", {
		templateUrl: config.urlBaseView + "novousuario.view.html",
		controller: "UsuarioCtrl"
	});

	$routeProvider.when("/edituser/:userTK", {
		templateUrl: config.urlBaseView + "editusuario.view.html",
		controller: "EdtUsuarioCtrl"
	});

	$routeProvider.when("/viewuser/:userTK", {
		templateUrl: config.urlBaseView + "detalheusuario.view.html",
		controller: "EdtUsuarioCtrl"
	});

	$routeProvider.when("/myaccount/:userTK", {
		templateUrl: config.urlBaseView + "meus_dados.view.html",
		controller: "EdtUsuarioCtrl"
	});

	$routeProvider.when("/registrationlog", {
		templateUrl: config.urlBaseView + "log_cadastro.view.html",
		controller: "LogCadastroCtrl"
	});

	$routeProvider.when("/usersaccesslog", {
		templateUrl: config.urlBaseView + "log_usuario_acessos.view.html",
		controller: "LogUsuarioAcessosCtrl"
	});

	$routeProvider.when("/errorslog", {
		templateUrl: config.urlBaseView + "log_erros_sistema.view.html",
		controller: "LogErrosSistemaCtrl"
	});

	$routeProvider.when("/notifications", {
		templateUrl: config.urlBaseView + "notificacoes.view.html",
		controller: "NotificacaoCtrl"
	});

	$routeProvider.when("/stopproccessline", {
		templateUrl: config.urlBaseView + "pausa_fila_processam.view.html",
		controller: "CleanMovimentsCtrl"
	});

	$routeProvider.when("/parameters", {
		templateUrl: config.urlBaseView + "parametros.view.html",
		controller: "ParametroCtrl"
	});

	$routeProvider.when("/newparameter", {
		templateUrl: config.urlBaseView + "novo_parametro.view.html",
		controller: "ParametroCtrl"
	});

	$routeProvider.when("/editparameter/:parKey", {
		templateUrl: config.urlBaseView + "edit_parametro.view.html",
		controller: "ParametroCtrl"
	});

	$routeProvider.when("/indicators", {
		templateUrl: config.urlBaseView + "indicadores.view.html",
		controller: "IndicadoresCtrl"
	});

	$routeProvider.when("/newindicator", {
		templateUrl: config.urlBaseView + "novo_indicador.view.html",
		controller: "IndicadoresCtrl"
	});

	$routeProvider.when("/editindicator/:indKey", {
		templateUrl: config.urlBaseView + "edit_indicador.view.html",
		controller: "IndicadoresCtrl"
	});

	$routeProvider.when("/accessprofiles", {
		templateUrl: config.urlBaseView + "perfis_acesso.view.html",
		controller: "PerfilAcessoCtrl"
	});

	$routeProvider.when("/newaccessprofile", {
		templateUrl: config.urlBaseView + "novo_perfil_acesso.view.html",
		controller: "PerfilAcessoCtrl"
	});

	$routeProvider.when("/editaccessprofile/:pfaTK", {
		templateUrl: config.urlBaseView + "edit_perfil_acesso.view.html",
		controller: "EdtPerfilAcessoCtrl"
	});


	/*******************************************************************************\
	|***************************** INI SISTEMA TICKETS *****************************|
	\*******************************************************************************/
	$routeProvider.when("/categoriaticket", {
		templateUrl: config.urlBaseView + "categoria_ticket.view.html",
		controller: "CategoriaTicketCtrl"
	});

	$routeProvider.when("/novocategoriaticket", {
		templateUrl: config.urlBaseView + "novo_categoria_ticket.view.html",
		controller: "CategoriaTicketCtrl"
	});

	$routeProvider.when("/editacategoriaticket/:cgtTK", {
		templateUrl: config.urlBaseView + "edita_categoria_ticket.view.html",
		controller: "CategoriaTicketCtrl"
	});

	$routeProvider.when("/grupotrabalho", {
		templateUrl: config.urlBaseView + "grupo_trabalho.view.html",
		controller: "GrupoTrabalhoCtrl"
	});

	$routeProvider.when("/novogrupotrabalho", {
		templateUrl: config.urlBaseView + "novo_grupo_trabalho.view.html",
		controller: "GrupoTrabalhoCtrl"
	});

	$routeProvider.when("/editagrupotrabalho/:grtTK", {
		templateUrl: config.urlBaseView + "edita_grupo_trabalho.view.html",
		controller: "GrupoTrabalhoCtrl"
	});

	$routeProvider.when("/pastatrabalho", {
		templateUrl: config.urlBaseView + "pasta_trabalho.view.html",
		controller: "PastaTrabalhoCtrl"
	});

	$routeProvider.when("/novopastatrabalho", {
		templateUrl: config.urlBaseView + "novo_pasta_trabalho.view.html",
		controller: "PastaTrabalhoCtrl"
	});

	$routeProvider.when("/editapastatrabalho/:pstTK", {
		templateUrl: config.urlBaseView + "edita_pasta_trabalho.view.html",
		controller: "PastaTrabalhoCtrl"
	});

	$routeProvider.when("/origemticket", {
		templateUrl: config.urlBaseView + "origem_ticket.view.html",
		controller: "OrigemTicketCtrl"
	});

	$routeProvider.when("/novoorigemticket", {
		templateUrl: config.urlBaseView + "novo_origem_ticket.view.html",
		controller: "OrigemTicketCtrl"
	});

	$routeProvider.when("/editaorigemticket/:ortTK", {
		templateUrl: config.urlBaseView + "edita_origem_ticket.view.html",
		controller: "OrigemTicketCtrl"
	});

	$routeProvider.when("/prioridadeticket", {
		templateUrl: config.urlBaseView + "prioridade_ticket.view.html",
		controller: "PrioridadeTicketCtrl"
	});

	$routeProvider.when("/novoprioridadeticket", {
		templateUrl: config.urlBaseView + "novo_prioridade_ticket.view.html",
		controller: "PrioridadeTicketCtrl"
	});

	$routeProvider.when("/editaprioridadeticket/:prtTK", {
		templateUrl: config.urlBaseView + "edita_prioridade_ticket.view.html",
		controller: "PrioridadeTicketCtrl"
	});

	$routeProvider.when("/situacaoticket", {
		templateUrl: config.urlBaseView + "situacao_ticket.view.html",
		controller: "SituacaoTicketCtrl"
	});

	$routeProvider.when("/novosituacaoticket", {
		templateUrl: config.urlBaseView + "novo_situacao_ticket.view.html",
		controller: "SituacaoTicketCtrl"
	});

	$routeProvider.when("/editasituacaoticket/:sttTK", {
		templateUrl: config.urlBaseView + "edita_situacao_ticket.view.html",
		controller: "SituacaoTicketCtrl"
	});

	$routeProvider.when("/tipoatividade", {
		templateUrl: config.urlBaseView + "tipo_atividade.view.html",
		controller: "TipoAtividadeCtrl"
	});

	$routeProvider.when("/novotipoatividade", {
		templateUrl: config.urlBaseView + "novo_tipo_atividade.view.html",
		controller: "TipoAtividadeCtrl"
	});

	$routeProvider.when("/editatipoatividade/:tavTK", {
		templateUrl: config.urlBaseView + "edita_tipo_atividade.view.html",
		controller: "TipoAtividadeCtrl"
	});

	$routeProvider.when("/ticket", {
		templateUrl: config.urlBaseView + "ticket.view.html",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/triagemticket", {
		templateUrl: config.urlBaseView + "triagem_ticket.view.html",
		controller: "TriagemTicketCtrl"
	});

	$routeProvider.when("/detalheticket/:tktTK", {
		templateUrl: config.urlBaseView + "detalhe_ticket.view.html",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/meustickets", {
		templateUrl: config.urlBaseView + "meus_tickets.view.html",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/meustrabalhos", {
		templateUrl: config.urlBaseView + "meus_trabalhos.view.html",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/novoticket", {
		templateUrl: config.urlBaseView + "novo_ticket.view.html",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/ticketkanban", {
		templateUrl: config.urlBaseView + "ticket_kanban.view.html",
		controller: "TicketKanbanCtrl"
	});

	$routeProvider.when("/ticketagenda", {
		templateUrl: config.urlBaseView + "ticket_agenda.view.html",
		controller: "TicketAgendaCtrl"
	});

	$routeProvider.when("/ticketdashboard", {
		templateUrl: config.urlBaseView + "ticket_dashboard.view.html",
		controller: "TicketDashboardCtrl"
	});

	$routeProvider.when("/relatoriogerencial", {
		templateUrl: config.urlBaseView + "ticket_relatorio_gerencial.view.html",
		controller: "TicketRelGerencialCtrl"
	});

	$routeProvider.when("/relatoriodesempenho", {
		templateUrl: config.urlBaseView + "ticket_relatorio_desempenho.view.html",
		controller: "TicketRelDesempenhoCtrl"
	});

	/*******************************************************************************\
	|***************************** FIM SISTEMA TICKETS *****************************|
	\*******************************************************************************/

	$routeProvider.otherwise({
		redirectTo: "/login"
	});

});