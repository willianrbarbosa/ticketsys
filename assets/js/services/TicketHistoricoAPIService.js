angular.module("ticket_sys").service("TicketHistoricoAPIService", function($http, config){

	this.loadTicketHistorico = function(cFiltro){
		return $http.get(config.urlBase + "/TicketHistorico.php?cFiltro=" + cFiltro);
	};

	this.loadTicketHistoricoDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/TicketHistorico.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTicketHistoricoByID = function(tkhTK){
		return $http.get(config.urlBase + "/TicketHistorico.php?tkhTK=" + tkhTK);
	};

	this.getTicketHistoricoPorTicket = function(tkh_tkt_id){
		return $http.get(config.urlBase + "/TicketHistorico.php?tkh_tkt_id=" + tkh_tkt_id);
	};

	this.getTicketHistoricoPorUser_id = function(tkh_user_id){
		return $http.get(config.urlBase + "/TicketHistorico.php?tkh_user_id=" + tkh_user_id);
	};

	this.getTicketHistoricoPorData_hora = function(tkh_data_hora){
		return $http.get(config.urlBase + "/TicketHistorico.php?tkh_data_hora=" + tkh_data_hora);
	};

	this.salvaTicketHistorico = function(TicketHistorico){
		return $http.post(config.urlBase + "/TicketHistorico.bd.php", TicketHistorico);
	};

	this.deletaTicketHistorico = function(TicketHistorico){
		return $http.post(config.urlBase + "/TicketHistorico.bd.php", TicketHistorico);
	};

});
