angular.module("ticket_sys").service("IndicadoresAPIService", function($http, config){

	this.loadIndicadores = function(){
		return $http.get(config.urlBaseModel + "Indicadores.php");
	};

	this.getIndicador = function(indKey){
		return $http.get(config.urlBaseModel + "Indicadores.php?indKey=" + indKey);
	};

	this.saveIndicador = function(param){
		return $http.post(config.urlBaseModel + "Indicadores.bd.php", param);
	};

});