angular.module("ticket_sys").service("CategoriaTicketAPIService", function($http, config){

	this.loadCategoriaTicket = function(cFiltro){
		return $http.get(config.urlBase + "/CategoriaTicket.php?cFiltro=" + cFiltro);
	};

	this.loadCategoriaTicketDeletados = function(cFiltro){
		return $http.get(config.urlBase + "/CategoriaTicket.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getCategoriaTicketByID = function(cgtTK){
		return $http.get(config.urlBase + "/CategoriaTicket.php?cgtTK=" + cgtTK);
	};

	this.salvaCategoriaTicket = function(CategoriaTicket){
		return $http.post(config.urlBase + "/CategoriaTicket.bd.php", CategoriaTicket);
	};

	this.deletaCategoriaTicket = function(CategoriaTicket){
		return $http.post(config.urlBase + "/CategoriaTicket.bd.php", CategoriaTicket);
	};

});
