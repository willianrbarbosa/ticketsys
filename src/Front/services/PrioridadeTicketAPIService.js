angular.module("ticket_sys").service("PrioridadeTicketAPIService", function($http, config){

	this.loadPrioridadeTicket = function(cFiltro){
		return $http.get(config.urlBaseModel + "PrioridadeTicket.php?cFiltro=" + cFiltro);
	};

	this.loadPrioridadeTicketDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "PrioridadeTicket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getPrioridadeTicketByID = function(prtTK){
		return $http.get(config.urlBaseModel + "PrioridadeTicket.php?prtTK=" + prtTK);
	};

	this.salvaPrioridadeTicket = function(PrioridadeTicket){
		return $http.post(config.urlBaseModel + "PrioridadeTicket.bd.php", PrioridadeTicket);
	};

	this.deletaPrioridadeTicket = function(PrioridadeTicket){
		return $http.post(config.urlBaseModel + "PrioridadeTicket.bd.php", PrioridadeTicket);
	};

});
