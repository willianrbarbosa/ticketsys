angular.module("ticket_sys").service("PerfilAcessoRotinaAPIService", function($http, config){

	this.loadPerfilRotinas = function(){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php");
	};

	this.loadRotinas = function(){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php?rot=null");
	};

	this.loadRotinasSelecionadasPerfil = function(ptapfaTK){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php?ptapfaTK=" + ptapfaTK);
	};

	this.getPerfilRotinaByPerfil = function(pfaTK){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php?pfaTK=" + pfaTK);
	};

	this.getPerfilRotinaLogin = function(){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php?verifTipo=true");
	};

	this.getPerfilRotinaByRotina = function(rtuTK){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php?rtuTK=" + rtuTK);
	};

	this.getLoggedUsuariorRotina = function(lgdrTK){
		return $http.get(config.urlBase + "/perfilAcessoRotina.php?lgdrTK=" + lgdrTK);
	};

	this.savePerfiloRotina = function(perfilRot){
		return $http.post(config.urlBase + "/perfilAcessoRotina.bd.php", perfilRot);
	};
 
	this.deletePerfilRotina = function(perfilRot){
		return $http.post(config.urlBase + "/perfilAcessoRotina.bd.php", perfilRot);
	};

});