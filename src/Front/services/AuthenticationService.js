angular.module("ticket_sys").service("AuthenticationService", function($http, config){
	
	this.validLogin = function(login){
		return $http.post(config.urlBaseModel + "login.php", login);
	};

	this.validConnected = function(login){
		return $http.get(config.urlBaseModel + "login.php");
	};

	this.verifySession = function(){
		return $http.get(config.urlBaseModel + "session.php");
	};

	this.recPwd = function(recpasswd){
		return $http.post(config.urlBaseModel + "recpasswd.php", recpasswd);
	};

	this.setViewURL = function(cProd){
		return $http.post(config.urlBaseModel + "visualizador_link.php", cProd);
	};

});