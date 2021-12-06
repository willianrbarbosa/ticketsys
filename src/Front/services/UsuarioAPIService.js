angular.module("ticket_sys").service("UsuarioAPIService", function($http, config){

	this.loadUsuarios = function(){
		return $http.get(config.urlBaseModel + "Usuario.php");
	};

	this.loadUsuariosInativos = function(){
		return $http.get(config.urlBaseModel + "Usuario.php?inativos=true");
	};

	this.getUsuario = function(userTK){
		return $http.get(config.urlBaseModel + "Usuario.php?userTK=" + userTK);
	};

	this.loadUsuarioPorTipo = function(userTipo){
		return $http.get(config.urlBaseModel + "Usuario.php?userTipo=" + userTipo);
	};

	this.loadUsuarioRespTicket = function(userRespTicket){
		return $http.get(config.urlBaseModel + "Usuario.php?userRespTicket=" + userRespTicket);
	};

	// this.loadUserClientes = function(){
	// 	return $http.get(config.urlBaseModel + "Cliente.php?foruser=true");
	// };

	this.admLogin = function(userTK){
		return $http.post(config.urlBaseModel + "Login_adm.php", userTK);
	};

	this.getUserAcessos = function(usaUserId){
		return $http.get(config.urlBaseModel + "Usuario.php?usaUserId=" + usaUserId);
	};

	this.getUsersAcessos = function(){
		return $http.get(config.urlBaseModel + "Usuario.php?allUsers=true");
	};

	this.getUsersAcessosByFilter = function(userTK,data_de,data_ate){
		return $http.get(config.urlBaseModel + "Usuario.php?userTK="+userTK+"&data_de="+data_de+"&data_ate="+data_ate);
	};

	this.getUserEmail = function(userEM){
		return $http.get(config.urlBaseModel + "Usuario.php?userEM=" + userEM);
	};

	this.saveUsuario = function(user){
		return $http.post(config.urlBaseModel + "Usuario.bd.php", user);
	};
 
	this.deleteUsuario = function(user){
		return $http.post(config.urlBaseModel + "Usuario.bd.php", user);
	};

	this.inativaUsuario = function(user){
		return $http.post(config.urlBaseModel + "Usuario.bd.php", user);
	};

	this.ativaUsuario = function(user){
		return $http.post(config.urlBaseModel + "Usuario.bd.php", user);
	};

});