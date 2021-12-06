angular.module("ticket_sys").service("AuthenticationService", function($http, config){
	
	this.validLogin = function(login){
		return $http.post(config.urlBaseModel + "Login.php", login);
	};

	this.validConnected = function(login){
		return $http.get(config.urlBaseModel + "Login.php");
	};

	this.verifySession = function(){
		return $http.get(config.urlBaseModel + "Session.php");
	};

	this.recPwd = function(recpasswd){
		return $http.post(config.urlBaseModel + "Recpasswd.php", recpasswd);
	};

});