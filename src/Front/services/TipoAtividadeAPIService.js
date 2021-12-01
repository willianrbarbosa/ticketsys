angular.module("ticket_sys").service("TipoAtividadeAPIService", function($http, config){

	this.loadTipoAtividade = function(cFiltro){
		return $http.get(config.urlBaseModel + "TipoAtividade.php?cFiltro=" + cFiltro);
	};

	this.loadTipoAtividadeDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "TipoAtividade.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getTipoAtividadeByID = function(tavTK){
		return $http.get(config.urlBaseModel + "TipoAtividade.php?tavTK=" + tavTK);
	};

	this.salvaTipoAtividade = function(TipoAtividade){
		return $http.post(config.urlBaseModel + "TipoAtividade.bd.php", TipoAtividade);
	};

	this.deletaTipoAtividade = function(TipoAtividade){
		return $http.post(config.urlBaseModel + "TipoAtividade.bd.php", TipoAtividade);
	};

});
