angular.module("ticket_sys").service("PastaTrabalhoAPIService", function($http, config){

	this.loadPastaTrabalho = function(cFiltro){
		return $http.get(config.urlBaseModel + "PastaTrabalho.php?cFiltro=" + cFiltro);
	};

	this.loadPastaTrabalhoDeletados = function(cFiltro){
		return $http.get(config.urlBaseModel + "PastaTrabalho.php?delete=true&cFiltro=" + cFiltro);
	};

	this.getPastaTrabalhoByID = function(pstTK){
		return $http.get(config.urlBaseModel + "PastaTrabalho.php?pstTK=" + pstTK);
	};

	this.getPastaTrabalhoPorGrt_id = function(pst_grt_id){
		return $http.get(config.urlBaseModel + "PastaTrabalho.php?pst_grt_id=" + pst_grt_id);
	};

	this.salvaPastaTrabalho = function(PastaTrabalho){
		return $http.post(config.urlBaseModel + "PastaTrabalho.bd.php", PastaTrabalho);
	};

	this.deletaPastaTrabalho = function(PastaTrabalho){
		return $http.post(config.urlBaseModel + "PastaTrabalho.bd.php", PastaTrabalho);
	};

});
