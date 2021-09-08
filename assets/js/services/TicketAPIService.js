angular.module("ticket_sys").service("TicketAPIService", function($http, config){

	this.loadTicket = function(cFiltro){
		return $http.get(config.urlBase + "/Ticket.php?cFiltro=" + cFiltro);
	};

	this.loadTicketDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/Ticket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTicketByID = function(tktTK){
		return $http.get(config.urlBase + "/Ticket.php?tktTK=" + tktTK);
	};

	this.getTicketPorPst_id = function(tkt_pst_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_pst_id=" + tkt_pst_id);
	};

	this.getTicketPorTav_id = function(tkt_tav_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_tav_id=" + tkt_tav_id);
	};

	this.getTicketPorStt_id = function(tkt_stt_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_stt_id=" + tkt_stt_id);
	};

	this.getTicketPorAbertura_data = function(tkt_abertura_data){
		return $http.get(config.urlBase + "/Ticket.php?tkt_abertura_data=" + tkt_abertura_data);
	};

	this.getTicketPorAbertura_user_id = function(cFiltro, tkt_abertura_user_id){
		return $http.get(config.urlBase + "/Ticket.php?cFiltro=" + cFiltro + "&tkt_abertura_user_id=" + tkt_abertura_user_id);
	};

	this.getTicketPorAprovado = function(tkt_aprovado){
		return $http.get(config.urlBase + "/Ticket.php?tkt_aprovado=" + tkt_aprovado);
	};

	this.getTicketPorAprovado_data = function(tkt_aprovado_data){
		return $http.get(config.urlBase + "/Ticket.php?tkt_aprovado_data=" + tkt_aprovado_data);
	};

	this.getTicketPorAprovado_user_id = function(tkt_aprovado_user_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_aprovado_user_id=" + tkt_aprovado_user_id);
	};

	this.getTicketPorCgt_id = function(tkt_cgt_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_cgt_id=" + tkt_cgt_id);
	};

	this.getTicketPorPrt_id = function(tkt_prt_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_prt_id=" + tkt_prt_id);
	};

	this.getTicketPorOrt_id = function(tkt_ort_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_ort_id=" + tkt_ort_id);
	};

	this.getTicketPorArquivado_user_id = function(tkt_arquivado_user_id){
		return $http.get(config.urlBase + "/Ticket.php?tkt_arquivado_user_id=" + tkt_arquivado_user_id);
	};

	this.loadTicketTodosPorUsuario = function(cFiltro, tkt_todos_usuario_id){
		return $http.get(config.urlBase + "/Ticket.php?cFiltro=" + cFiltro + "&tkt_todos_usuario_id=" + tkt_todos_usuario_id);
	};

	this.loadTicketPorPendentesResponsavel = function(cFiltro, tkt_resp_user_id){
		return $http.get(config.urlBase + "/Ticket.php?cFiltro=" + cFiltro + "&tkt_resp_user_id=" + tkt_resp_user_id);
	};

	this.loadTicketsKanban = function(cFiltro){
		return $http.get(config.urlBase + "/Ticket.php?kanban=true&cFiltro=" + cFiltro);
	};

	this.loadTicketsAgenda = function(cFiltro, nUserRespTk){
		return $http.get(config.urlBase + "/Ticket.php?agenda=true&cFiltro=" + cFiltro + "&userRespTk=" + nUserRespTk);
	};

	this.loadTriagemTickets = function(cFiltro){
		return $http.get(config.urlBase + "/Ticket.php?lTriagem=true&cFiltro=" + cFiltro);
	};

	this.triagemTicket = function(Ticket){
		return $http.post(config.urlBase + "/TicketTriagem.bd.php", Ticket);
	};

	this.TicketCalculaEsforco = function(Ticket){
		return $http.post(config.urlBase + "/TicketCalculaEsforco.php", Ticket);
	};

	this.salvaTicket = function(Ticket){
		return $http.post(config.urlBase + "/Ticket.bd.php", Ticket);
	};

	this.deletaTicket = function(Ticket){
		return $http.post(config.urlBase + "/Ticket.bd.php", Ticket);
	};

	this.uploadFile = function(file){
		return $http.post(config.urlBase + "/uploadFile.bd.php", file, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined,'Process-Data': false}
		});
	};

});
