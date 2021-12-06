angular.module("ticket_sys").service("UploadFileAPIService", function($http, config){
 
	this.uploadFile = function(file){
		return $http.post(config.urlBaseModel + "UploadFile.bd.php", file, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined,'Process-Data': false}
		});
	};

});