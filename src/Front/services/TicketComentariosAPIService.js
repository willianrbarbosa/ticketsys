angular.module("ticket_sys").service("TicketComentariosAPIService", function($http, config){

	this.loadTicketComentarios = function(cFiltro){
		return $http.get(config.urlBaseModel + "TicketComentarios.php?cFiltro=" + cFiltro);
	};

	this.loadTicketComentariosDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "TicketComentarios.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTicketComentariosByID = function(tkcTK){
		return $http.get(config.urlBaseModel + "TicketComentarios.php?tkcTK=" + tkcTK);
	};

	this.getTicketComentariosPorTicket = function(tkc_tkt_id){
		return $http.get(config.urlBaseModel + "TicketComentarios.php?tkc_tkt_id=" + tkc_tkt_id);
	};

	this.getTicketComentariosPorUser_id = function(tkc_user_id){
		return $http.get(config.urlBaseModel + "TicketComentarios.php?tkc_user_id=" + tkc_user_id);
	};

	this.getTicketComentariosPorData_hora = function(tkc_data_hora){
		return $http.get(config.urlBaseModel + "TicketComentarios.php?tkc_data_hora=" + tkc_data_hora);
	};

	this.salvaTicketComentarios = function(TicketComentarios){
		return $http.post(config.urlBaseModel + "TicketComentarios.bd.php", TicketComentarios);
	};

	this.deletaTicketComentarios = function(TicketComentarios){
		return $http.post(config.urlBaseModel + "TicketComentarios.bd.php", TicketComentarios);
	};

});
