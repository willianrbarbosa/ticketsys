angular.module("ticket_sys").service("NotificacaoAPIService", function($http, config){

	this.loadNotificacoes = function(){
		return $http.get(config.urlBaseModel + "Notificacao.php");
	};

	this.loadUserNotificacoes = function(userTK){
		return $http.get(config.urlBaseModel + "Notificacao.php?userTK=" + userTK);
	};

	this.loadUserNotificacoesNaoLidas = function(userTK){
		return $http.get(config.urlBaseModel + "Notificacao.php?user_nao_lida=" + userTK);
	};

	this.getNotificacao = function(ntfTk){
		return $http.get(config.urlBaseModel + "Notificacao.php?ntfTk=" + ntfTk);
	};

	this.saveNotificacao = function(notif){
		return $http.post(config.urlBaseModel + "Notificacao.bd.php", notif);
	};
 
	this.deleteNotificacao = function(notif){
		return $http.post(config.urlBaseModel + "Notificacao.bd.php", notif);
	};

});