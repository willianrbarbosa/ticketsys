angular.module("ticket_sys").service("ReportAPIService", function($http, config){
	this.TicketGerencialReport = function(RptFilter){
		return $http.post(config.urlBase + "/ticketGerencialReport.php", RptFilter);
	};

	this.TicketDesempenhoReport = function(RptFilter){
		return $http.post(config.urlBase + "/ticketDesempenhoReport.php", RptFilter);
	};

});