angular.module("ticket_sys").service("AuthenticationService", function($http, config){
	
	this.validLogin = function(login){
		return $http.post(config.urlBase + "/login.php", login);
	};

	this.validConnected = function(login){
		return $http.get(config.urlBase + "/login.php");
	};

	this.verifySession = function(){
		return $http.get(config.urlBase + "/session.php");
	};

	this.recPwd = function(recpasswd){
		return $http.post(config.urlBase + "/recpasswd.php", recpasswd);
	};

	this.setViewURL = function(cProd){
		return $http.post(config.urlBase + "/visualizador_link.php", cProd);
	};

});