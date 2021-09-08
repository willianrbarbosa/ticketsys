angular.module("ticket_sys").service("IndicadoresAPIService", function($http, config){

	this.loadIndicadores = function(){
		return $http.get(config.urlBase + "/indicadores.php");
	};

	this.getIndicador = function(indKey){
		return $http.get(config.urlBase + "/indicadores.php?indKey=" + indKey);
	};

	this.saveIndicador = function(param){
		return $http.post(config.urlBase + "/indicadores.bd.php", param);
	};

});