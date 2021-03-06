angular.module("ticket_sys").service("LogAPIService", function($http, config){

	this.loadLogCadastro = function(Flfilter){
		return $http.post(config.urlBaseModel + "Log_cadastro.php", Flfilter);
	};

	this.getLogCadastro = function(ledTk){
		return $http.get(config.urlBaseModel + "Log_cadastro.php?ledTk=" + ledTk);
	};

	this.loadLogConsultaCliente = function(){
		return $http.get(config.urlBaseModel + "Log_consulta_cliente.php");
	};

	this.getLogConsultaCliente = function(lccTk){
		return $http.get(config.urlBaseModel + "Log_consulta_cliente.php?lccTk=" + lccTk);
	};

	this.loadLogConsultaClienteUser = function(userTK,data_de,data_ate){
		return $http.get(config.urlBaseModel + "Log_consulta_cliente.php?userTK="+userTK+"&data_de="+data_de+"&data_ate="+data_ate);
	};

	this.loadErrosSistemas = function(action,file){
		return $http.get(config.urlBaseModel + "Log_erros_sistema.php?action="+action+"&file="+file);
	};

	this.validaConfiguracoesServidor = function(action){
		return $http.get(config.urlBaseModel + "Verifica_configuracao_server.php?action="+action);
	};

});