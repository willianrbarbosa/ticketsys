angular.module("ticket_sys").service("GraficosAPIService", function($http, config){


	this.getTotaisTicket = function(data_de, data_ate){
		return $http.get(config.urlBaseModel + "Grafico.php?chart=TT&data_de=" + data_de + "&data_ate=" + data_ate);
	};

	this.getTotaisHorasTicket = function(data_de, data_ate){
		return $http.get(config.urlBaseModel + "Grafico.php?chart=TTH&data_de=" + data_de + "&data_ate=" + data_ate);
	};

});