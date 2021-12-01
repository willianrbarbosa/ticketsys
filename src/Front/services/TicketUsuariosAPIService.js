angular.module("ticket_sys").service("TicketUsuariosAPIService", function($http, config){

	this.loadTicketUsuarios = function(cFiltro){
		return $http.get(config.urlBaseModel + "TicketUsuarios.php?cFiltro=" + cFiltro);
	};

	this.loadTicketUsuariosDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "TicketUsuarios.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTicketUsuariosByID = function(tkuTK){
		return $http.get(config.urlBaseModel + "TicketUsuarios.php?tkuTK=" + tkuTK);
	};

	this.getTicketUsuariosPorTicket = function(tku_tkt_id){
		return $http.get(config.urlBaseModel + "TicketUsuarios.php?tku_tkt_id=" + tku_tkt_id);
	};

	this.getTicketUsuariosPorUser_id = function(tku_user_id){
		return $http.get(config.urlBaseModel + "TicketUsuarios.php?tku_user_id=" + tku_user_id);
	};

	this.getTicketUsuariosPorTipo = function(tku_tipo){
		return $http.get(config.urlBaseModel + "TicketUsuarios.php?tku_tipo=" + tku_tipo);
	};

	this.salvaTicketUsuarios = function(TicketUsuarios){
		return $http.post(config.urlBaseModel + "TicketUsuarios.bd.php", TicketUsuarios);
	};

	this.deletaTicketUsuarios = function(TicketUsuarios){
		return $http.post(config.urlBaseModel + "TicketUsuarios.bd.php", TicketUsuarios);
	};

});
