angular.module("ticket_sys").service("TicketApontamentosAPIService", function($http, config){

	this.loadTicketApontamentos = function(cFiltro){
		return $http.get(config.urlBase + "/TicketApontamentos.php?cFiltro=" + cFiltro);
	};

	this.loadTicketApontamentosDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/TicketApontamentos.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTicketApontamentosByID = function(tkpTK){
		return $http.get(config.urlBase + "/TicketApontamentos.php?tkpTK=" + tkpTK);
	};

	this.getTicketApontamentosPorTicket = function(tkp_tkt_id){
		return $http.get(config.urlBase + "/TicketApontamentos.php?tkp_tkt_id=" + tkp_tkt_id);
	};

	this.getTicketApontamentosPorUser_id = function(tkp_user_id){
		return $http.get(config.urlBase + "/TicketApontamentos.php?tkp_user_id=" + tkp_user_id);
	};

	this.getTicketApontamentosPorData = function(tkp_data){
		return $http.get(config.urlBase + "/TicketApontamentos.php?tkp_data=" + tkp_data);
	};

	this.salvaTicketApontamentos = function(TicketApontamentos){
		return $http.post(config.urlBase + "/TicketApontamentos.bd.php", TicketApontamentos);
	};

	this.deletaTicketApontamentos = function(TicketApontamentos){
		return $http.post(config.urlBase + "/TicketApontamentos.bd.php", TicketApontamentos);
	};

});
