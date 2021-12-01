angular.module("ticket_sys").service("GrupoTrabalhoAPIService", function($http, config){

	this.loadGrupoTrabalho = function(cFiltro){
		return $http.get(config.urlBaseModel + "GrupoTrabalho.php?cFiltro=" + cFiltro);
	};

	this.loadGrupoTrabalhoDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "GrupoTrabalho.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getGrupoTrabalhoByID = function(grtTK){
		return $http.get(config.urlBaseModel + "GrupoTrabalho.php?grtTK=" + grtTK);
	};

	this.salvaGrupoTrabalho = function(GrupoTrabalho){
		return $http.post(config.urlBaseModel + "GrupoTrabalho.bd.php", GrupoTrabalho);
	};

	this.deletaGrupoTrabalho = function(GrupoTrabalho){
		return $http.post(config.urlBaseModel + "GrupoTrabalho.bd.php", GrupoTrabalho);
	};

});
