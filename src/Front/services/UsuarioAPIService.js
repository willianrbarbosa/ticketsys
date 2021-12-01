angular.module("ticket_sys").service("UsuarioAPIService", function($http, config){

	this.loadUsuarios = function(){
		return $http.get(config.urlBaseModel + "usuario.php");
	};

	this.loadUsuariosInativos = function(){
		return $http.get(config.urlBaseModel + "usuario.php?inativos=true");
	};

	this.getUsuario = function(userTK){
		return $http.get(config.urlBaseModel + "usuario.php?userTK=" + userTK);
	};

	this.loadUsuarioPorTipo = function(userTipo){
		return $http.get(config.urlBaseModel + "usuario.php?userTipo=" + userTipo);
	};

	this.loadUsuarioRespTicket = function(userRespTicket){
		return $http.get(config.urlBaseModel + "usuario.php?userRespTicket=" + userRespTicket);
	};

	this.getUsersPrcFiles = function(fileStatus){
		return $http.get(config.urlBaseModel + "usuario.php?fileStatus=" + fileStatus);
	};

	this.loadUserClientes = function(){
		return $http.get(config.urlBaseModel + "cliente.php?foruser=true");
	};

	this.admLogin = function(userTK){
		return $http.post(config.urlBaseModel + "login_adm.php", userTK);
	};

	this.getUserAcessos = function(usaUserId){
		return $http.get(config.urlBaseModel + "usuario.php?usaUserId=" + usaUserId);
	};

	this.getUsersAcessos = function(){
		return $http.get(config.urlBaseModel + "usuario.php?allUsers=true");
	};

	this.getUsersAcessosByFilter = function(userTK,data_de,data_ate){
		return $http.get(config.urlBaseModel + "usuario.php?userTK="+userTK+"&data_de="+data_de+"&data_ate="+data_ate);
	};

	this.getUserEmail = function(userEM){
		return $http.get(config.urlBaseModel + "usuario.php?userEM=" + userEM);
	};

	this.getUsuarioProdPendDesempenho = function(){
		return $http.get(config.urlBaseModel + "usuario.php?prodpenddesempenho=true");
	};

	this.encerraClienteProdPendente = function(user){
		return $http.post(config.urlBaseModel + "usuario.bd.php", user);
	};

	this.saveUsuario = function(user){
		return $http.post(config.urlBaseModel + "usuario.bd.php", user);
	};
 
	this.deleteUsuario = function(user){
		return $http.post(config.urlBaseModel + "usuario.bd.php", user);
	};

	this.inativaUsuario = function(user){
		return $http.post(config.urlBaseModel + "usuario.bd.php", user);
	};

	this.ativaUsuario = function(user){
		return $http.post(config.urlBaseModel + "usuario.bd.php", user);
	};

});