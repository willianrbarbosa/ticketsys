angular.module("ticket_sys").service("PrioridadeTicketAPIService", function($http, config){

	this.loadPrioridadeTicket = function(cFiltro){
		return $http.get(config.urlBase + "/PrioridadeTicket.php?cFiltro=" + cFiltro);
	};

	this.loadPrioridadeTicketDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/PrioridadeTicket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getPrioridadeTicketByID = function(prtTK){
		return $http.get(config.urlBase + "/PrioridadeTicket.php?prtTK=" + prtTK);
	};

	this.salvaPrioridadeTicket = function(PrioridadeTicket){
		return $http.post(config.urlBase + "/PrioridadeTicket.bd.php", PrioridadeTicket);
	};

	this.deletaPrioridadeTicket = function(PrioridadeTicket){
		return $http.post(config.urlBase + "/PrioridadeTicket.bd.php", PrioridadeTicket);
	};

});
