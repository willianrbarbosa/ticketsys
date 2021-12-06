angular.module("ticket_sys").service("PerfilAcessoRotinaAPIService", function($http, config){

	this.loadPerfilRotinas = function(){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php");
	};

	this.loadRotinas = function(){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php?rot=null");
	};

	this.loadRotinasSelecionadasPerfil = function(ptapfaTK){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php?ptapfaTK=" + ptapfaTK);
	};

	this.getPerfilRotinaByPerfil = function(pfaTK){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php?pfaTK=" + pfaTK);
	};

	this.getPerfilRotinaLogin = function(){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php?verifTipo=true");
	};

	this.getPerfilRotinaByRotina = function(rtuTK){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php?rtuTK=" + rtuTK);
	};

	this.getLoggedUsuariorRotina = function(lgdrTK){
		return $http.get(config.urlBaseModel + "PerfilAcessoRotina.php?lgdrTK=" + lgdrTK);
	};

	this.savePerfiloRotina = function(perfilRot){
		return $http.post(config.urlBaseModel + "PerfilAcessoRotina.bd.php", perfilRot);
	};
 
	this.deletePerfilRotina = function(perfilRot){
		return $http.post(config.urlBaseModel + "PerfilAcessoRotina.bd.php", perfilRot);
	};

});