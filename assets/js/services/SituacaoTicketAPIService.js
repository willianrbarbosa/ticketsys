angular.module("ticket_sys").service("SituacaoTicketAPIService", function($http, config){

	this.loadSituacaoTicket = function(cFiltro){
		return $http.get(config.urlBase + "/SituacaoTicket.php?cFiltro=" + cFiltro);
	};

	this.loadSituacaoTicketKanban = function(cKanban, cFiltro){
		return $http.get(config.urlBase + "/SituacaoTicket.php?cKanban=" + cKanban + "&cFiltro=" + cFiltro);
	};

	this.loadSituacaoTicketDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/SituacaoTicket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getSituacaoTicketByID = function(sttTK){
		return $http.get(config.urlBase + "/SituacaoTicket.php?sttTK=" + sttTK);
	};

	this.salvaSituacaoTicket = function(SituacaoTicket){
		return $http.post(config.urlBase + "/SituacaoTicket.bd.php", SituacaoTicket);
	};

	this.deletaSituacaoTicket = function(SituacaoTicket){
		return $http.post(config.urlBase + "/SituacaoTicket.bd.php", SituacaoTicket);
	};

});
