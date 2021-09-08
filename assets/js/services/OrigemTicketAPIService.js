angular.module("ticket_sys").service("OrigemTicketAPIService", function($http, config){

	this.loadOrigemTicket = function(cFiltro){
		return $http.get(config.urlBase + "/OrigemTicket.php?cFiltro=" + cFiltro);
	};

	this.loadOrigemTicketDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/OrigemTicket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getOrigemTicketByID = function(ortTK){
		return $http.get(config.urlBase + "/OrigemTicket.php?ortTK=" + ortTK);
	};

	this.salvaOrigemTicket = function(OrigemTicket){
		return $http.post(config.urlBase + "/OrigemTicket.bd.php", OrigemTicket);
	};

	this.deletaOrigemTicket = function(OrigemTicket){
		return $http.post(config.urlBase + "/OrigemTicket.bd.php", OrigemTicket);
	};

});
