angular.module("ticket_sys").service("ParametroAPIService", function($http, config){

	this.loadParametros = function(){
		return $http.get(config.urlBase + "/parametro.php");
	};

	this.getParametro = function(parKey){
		return $http.get(config.urlBase + "/parametro.php?parKey=" + parKey);
	};

	this.saveParametro = function(param){
		return $http.post(config.urlBase + "/parametro.bd.php", param);
	};

});