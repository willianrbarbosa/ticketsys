angular.module("ticket_sys").service("ParametroAPIService", function($http, config){

	this.loadParametros = function(){
		return $http.get(config.urlBaseModel + "Parametro.php");
	};

	this.getParametro = function(parKey){
		return $http.get(config.urlBaseModel + "Parametro.php?parKey=" + parKey);
	};

	this.saveParametro = function(param){
		return $http.post(config.urlBaseModel + "Parametro.bd.php", param);
	};

});