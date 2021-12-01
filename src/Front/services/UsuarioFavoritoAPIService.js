angular.module("ticket_sys").service("UsuarioFavoritoAPIService", function($http, config){

	this.getUserFavoritesById = function(ufvTk){
		return $http.get(config.urlBaseModel + "usuarioFavorito.php?ufvTk=" + ufvTk);
	};

	this.getUserFavoritesByUserId = function(userTK){
		return $http.get(config.urlBaseModel + "usuarioFavorito.php?userTK=" + userTK);
	};

	this.saveUserFavorite = function(userFavorite){
		return $http.post(config.urlBaseModel + "usuarioFavorito.bd.php", userFavorite);
	};
 
	this.deleteUserFavorite = function(userFavorite){
		return $http.post(config.urlBaseModel + "usuarioFavorito.bd.php", userFavorite);
	};

});