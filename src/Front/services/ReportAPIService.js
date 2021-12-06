angular.module("ticket_sys").service("ReportAPIService", function($http, config){
	this.TicketGerencialReport = function(RptFilter){
		return $http.post(config.urlBaseModel + "TicketGerencialReport.php", RptFilter);
	};

	this.TicketDesempenhoReport = function(RptFilter){
		return $http.post(config.urlBaseModel + "TicketDesempenhoReport.php", RptFilter);
	};

});