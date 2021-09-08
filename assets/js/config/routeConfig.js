angular.module("ticket_sys").config(function($routeProvider){	
	$routeProvider.when("/login", {
		templateUrl: "assets/view/login.view.php",
		controller: "TicketSysCtrl"
	});
	$routeProvider.when("/users", {
		templateUrl: "assets/view/usuarios.view.php",
		controller: "UsuarioCtrl"
	});

	$routeProvider.when("/newuser", {
		templateUrl: "assets/view/novousuario.view.php",
		controller: "UsuarioCtrl"
	});

	$routeProvider.when("/edituser/:userTK", {
		templateUrl: "assets/view/editusuario.view.php",
		controller: "EdtUsuarioCtrl"
	});

	$routeProvider.when("/viewuser/:userTK", {
		templateUrl: "assets/view/detalheusuario.view.php",
		controller: "EdtUsuarioCtrl"
	});

	$routeProvider.when("/myaccount/:userTK", {
		templateUrl: "assets/view/meus_dados.view.php",
		controller: "EdtUsuarioCtrl"
	});

	$routeProvider.when("/registrationlog", {
		templateUrl: "assets/view/log_cadastro.view.php",
		controller: "LogCadastroCtrl"
	});

	$routeProvider.when("/usersaccesslog", {
		templateUrl: "assets/view/log_usuario_acessos.view.php",
		controller: "LogUsuarioAcessosCtrl"
	});

	$routeProvider.when("/errorslog", {
		templateUrl: "assets/view/log_erros_sistema.view.php",
		controller: "LogErrosSistemaCtrl"
	});

	$routeProvider.when("/notifications", {
		templateUrl: "assets/view/notificacoes.view.php",
		controller: "NotificacaoCtrl"
	});

	$routeProvider.when("/stopproccessline", {
		templateUrl: "assets/view/pausa_fila_processam.view.php",
		controller: "CleanMovimentsCtrl"
	});

	$routeProvider.when("/parameters", {
		templateUrl: "assets/view/parametros.view.php",
		controller: "ParametroCtrl"
	});

	$routeProvider.when("/newparameter", {
		templateUrl: "assets/view/novo_parametro.view.php",
		controller: "ParametroCtrl"
	});

	$routeProvider.when("/editparameter/:parKey", {
		templateUrl: "assets/view/edit_parametro.view.php",
		controller: "ParametroCtrl"
	});

	$routeProvider.when("/indicators", {
		templateUrl: "assets/view/indicadores.view.php",
		controller: "IndicadoresCtrl"
	});

	$routeProvider.when("/newindicator", {
		templateUrl: "assets/view/novo_indicador.view.php",
		controller: "IndicadoresCtrl"
	});

	$routeProvider.when("/editindicator/:indKey", {
		templateUrl: "assets/view/edit_indicador.view.php",
		controller: "IndicadoresCtrl"
	});

	$routeProvider.when("/accessprofiles", {
		templateUrl: "assets/view/perfis_acesso.view.php",
		controller: "PerfilAcessoCtrl"
	});

	$routeProvider.when("/newaccessprofile", {
		templateUrl: "assets/view/novo_perfil_acesso.view.php",
		controller: "PerfilAcessoCtrl"
	});

	$routeProvider.when("/editaccessprofile/:pfaTK", {
		templateUrl: "assets/view/edit_perfil_acesso.view.php",
		controller: "EdtPerfilAcessoCtrl"
	});


	/*******************************************************************************\
	|***************************** INI SISTEMA TICKETS *****************************|
	\*******************************************************************************/
	$routeProvider.when("/categoriaticket", {
		templateUrl: "assets/view/categoria_ticket.view.php",
		controller: "CategoriaTicketCtrl"
	});

	$routeProvider.when("/novocategoriaticket", {
		templateUrl: "assets/view/novo_categoria_ticket.view.php",
		controller: "CategoriaTicketCtrl"
	});

	$routeProvider.when("/editacategoriaticket/:cgtTK", {
		templateUrl: "assets/view/edita_categoria_ticket.view.php",
		controller: "CategoriaTicketCtrl"
	});

	$routeProvider.when("/grupotrabalho", {
		templateUrl: "assets/view/grupo_trabalho.view.php",
		controller: "GrupoTrabalhoCtrl"
	});

	$routeProvider.when("/novogrupotrabalho", {
		templateUrl: "assets/view/novo_grupo_trabalho.view.php",
		controller: "GrupoTrabalhoCtrl"
	});

	$routeProvider.when("/editagrupotrabalho/:grtTK", {
		templateUrl: "assets/view/edita_grupo_trabalho.view.php",
		controller: "GrupoTrabalhoCtrl"
	});

	$routeProvider.when("/pastatrabalho", {
		templateUrl: "assets/view/pasta_trabalho.view.php",
		controller: "PastaTrabalhoCtrl"
	});

	$routeProvider.when("/novopastatrabalho", {
		templateUrl: "assets/view/novo_pasta_trabalho.view.php",
		controller: "PastaTrabalhoCtrl"
	});

	$routeProvider.when("/editapastatrabalho/:pstTK", {
		templateUrl: "assets/view/edita_pasta_trabalho.view.php",
		controller: "PastaTrabalhoCtrl"
	});

	$routeProvider.when("/origemticket", {
		templateUrl: "assets/view/origem_ticket.view.php",
		controller: "OrigemTicketCtrl"
	});

	$routeProvider.when("/novoorigemticket", {
		templateUrl: "assets/view/novo_origem_ticket.view.php",
		controller: "OrigemTicketCtrl"
	});

	$routeProvider.when("/editaorigemticket/:ortTK", {
		templateUrl: "assets/view/edita_origem_ticket.view.php",
		controller: "OrigemTicketCtrl"
	});

	$routeProvider.when("/prioridadeticket", {
		templateUrl: "assets/view/prioridade_ticket.view.php",
		controller: "PrioridadeTicketCtrl"
	});

	$routeProvider.when("/novoprioridadeticket", {
		templateUrl: "assets/view/novo_prioridade_ticket.view.php",
		controller: "PrioridadeTicketCtrl"
	});

	$routeProvider.when("/editaprioridadeticket/:prtTK", {
		templateUrl: "assets/view/edita_prioridade_ticket.view.php",
		controller: "PrioridadeTicketCtrl"
	});

	$routeProvider.when("/situacaoticket", {
		templateUrl: "assets/view/situacao_ticket.view.php",
		controller: "SituacaoTicketCtrl"
	});

	$routeProvider.when("/novosituacaoticket", {
		templateUrl: "assets/view/novo_situacao_ticket.view.php",
		controller: "SituacaoTicketCtrl"
	});

	$routeProvider.when("/editasituacaoticket/:sttTK", {
		templateUrl: "assets/view/edita_situacao_ticket.view.php",
		controller: "SituacaoTicketCtrl"
	});

	$routeProvider.when("/tipoatividade", {
		templateUrl: "assets/view/tipo_atividade.view.php",
		controller: "TipoAtividadeCtrl"
	});

	$routeProvider.when("/novotipoatividade", {
		templateUrl: "assets/view/novo_tipo_atividade.view.php",
		controller: "TipoAtividadeCtrl"
	});

	$routeProvider.when("/editatipoatividade/:tavTK", {
		templateUrl: "assets/view/edita_tipo_atividade.view.php",
		controller: "TipoAtividadeCtrl"
	});

	$routeProvider.when("/ticket", {
		templateUrl: "assets/view/ticket.view.php",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/triagemticket", {
		templateUrl: "assets/view/triagem_ticket.view.php",
		controller: "TriagemTicketCtrl"
	});

	$routeProvider.when("/detalheticket/:tktTK", {
		templateUrl: "assets/view/detalhe_ticket.view.php",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/meustickets", {
		templateUrl: "assets/view/meus_tickets.view.php",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/meustrabalhos", {
		templateUrl: "assets/view/meus_trabalhos.view.php",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/novoticket", {
		templateUrl: "assets/view/novo_ticket.view.php",
		controller: "TicketCtrl"
	});

	$routeProvider.when("/ticketkanban", {
		templateUrl: "assets/view/ticket_kanban.view.php",
		controller: "TicketKanbanCtrl"
	});

	$routeProvider.when("/ticketagenda", {
		templateUrl: "assets/view/ticket_agenda.view.php",
		controller: "TicketAgendaCtrl"
	});

	$routeProvider.when("/ticketdashboard", {
		templateUrl: "assets/view/ticket_dashboard.view.php",
		controller: "TicketDashboardCtrl"
	});

	$routeProvider.when("/relatoriogerencial", {
		templateUrl: "assets/view/ticket_relatorio_gerencial.view.php",
		controller: "TicketRelGerencialCtrl"
	});

	$routeProvider.when("/relatoriodesempenho", {
		templateUrl: "assets/view/ticket_relatorio_desempenho.view.php",
		controller: "TicketRelDesempenhoCtrl"
	});

	/*******************************************************************************\
	|***************************** FIM SISTEMA TICKETS *****************************|
	\*******************************************************************************/

	$routeProvider.otherwise({
		redirectTo: "/login"
	});

});