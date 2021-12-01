angular.module("ticket_sys").service("SituacaoTicketAPIService", function($http, config){

	this.loadSituacaoTicket = function(cFiltro){
		return $http.get(config.urlBaseModel + "SituacaoTicket.php?cFiltro=" + cFiltro);
	};

	this.loadSituacaoTicketKanban = function(cKanban, cFiltro){
		return $http.get(config.urlBaseModel + "SituacaoTicket.php?cKanban=" + cKanban + "&cFiltro=" + cFiltro);
	};

	this.loadSituacaoTicketDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "SituacaoTicket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getSituacaoTicketByID = function(sttTK){
		return $http.get(config.urlBaseModel + "SituacaoTicket.php?sttTK=" + sttTK);
	};

	this.salvaSituacaoTicket = function(SituacaoTicket){
		return $http.post(config.urlBaseModel + "SituacaoTicket.bd.php", SituacaoTicket);
	};

	this.deletaSituacaoTicket = function(SituacaoTicket){
		return $http.post(config.urlBaseModel + "SituacaoTicket.bd.php", SituacaoTicket);
	};

});
