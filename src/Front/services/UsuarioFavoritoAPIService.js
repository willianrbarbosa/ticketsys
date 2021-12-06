angular.module("ticket_sys").service("UsuarioFavoritoAPIService", function($http, config){

	this.getUserFavoritesById = function(ufvTk){
		return $http.get(config.urlBaseModel + "UsuarioFavorito.php?ufvTk=" + ufvTk);
	};

	this.getUserFavoritesByUserId = function(userTK){
		return $http.get(config.urlBaseModel + "UsuarioFavorito.php?userTK=" + userTK);
	};

	this.saveUserFavorite = function(userFavorite){
		return $http.post(config.urlBaseModel + "UsuarioFavorito.bd.php", userFavorite);
	};
 
	this.deleteUserFavorite = function(userFavorite){
		return $http.post(config.urlBaseModel + "UsuarioFavorito.bd.php", userFavorite);
	};

});