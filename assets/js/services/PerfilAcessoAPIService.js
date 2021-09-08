angular.module("ticket_sys").service("PerfilAcessoAPIService", function($http, config){

	this.loadPerfisAcesso = function(){
		return $http.get(config.urlBase + "/perfilAcesso.php");
	};

	this.loadPerfisAcessoByCliPlano = function(cli_id){
		return $http.get(config.urlBase + "/perfilAcesso.php?cli_id="+cli_id);
	};

	this.loadDeletedPerfisAcessoByCondicao = function(cFiltro){
		return $http.get(config.urlBase + "/perfilAcesso.php?deleted=true&cFiltro=" + cFiltro);
	};

	this.loadPerfisAcessoByCondicao = function(cFiltro){
		return $http.get(config.urlBase + "/perfilAcesso.php?cFiltro=" + cFiltro);
	};

	this.getPerfilAcesso = function(pfaTk){
		return $http.get(config.urlBase + "/perfilAcesso.php?pfaTk=" + pfaTk);
	};

	this.savePerfilAcesso = function(perfilAcesso){
		return $http.post(config.urlBase + "/perfilAcesso.bd.php", perfilAcesso);
	};
 
	this.deletePerfilAcesso = function(perfilAcesso){
		return $http.post(config.urlBase + "/perfilAcesso.bd.php", perfilAcesso);
	};

});