angular.module("ticket_sys").service("TicketArquivosAPIService", function($http, config){

	this.loadTicketArquivos = function(cFiltro){
		return $http.get(config.urlBaseModel + "TicketArquivos.php?cFiltro=" + cFiltro);
	};

	this.loadTicketArquivosDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "TicketArquivos.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTicketArquivosByID = function(tkaTK){
		return $http.get(config.urlBaseModel + "TicketArquivos.php?tkaTK=" + tkaTK);
	};

	this.getTicketArquivosPorTicket = function(tka_tkt_id){
		return $http.get(config.urlBaseModel + "TicketArquivos.php?tka_tkt_id=" + tka_tkt_id);
	};

	this.getTicketArquivosPorUser_id = function(tka_user_id){
		return $http.get(config.urlBaseModel + "TicketArquivos.php?tka_user_id=" + tka_user_id);
	};

	this.getTicketArquivosPorData_hora = function(tka_data_hora){
		return $http.get(config.urlBaseModel + "TicketArquivos.php?tka_data_hora=" + tka_data_hora);
	};

	this.salvaTicketArquivos = function(TicketArquivos){
		return $http.post(config.urlBaseModel + "TicketArquivos.bd.php", TicketArquivos);
	};

	this.deletaTicketArquivos = function(TicketArquivos){
		return $http.post(config.urlBaseModel + "TicketArquivos.bd.php", TicketArquivos);
	};

});
