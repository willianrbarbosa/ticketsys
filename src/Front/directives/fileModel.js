angular.module("ticket_sys").directive('fileModel', ['$parse', function($parse){
	return {
		restrict: 'A',
		link: function(scope, element, attrs){
			// element.bind('change', function()	{
			// 	$parse(attrs.fileModel)
			// 	.assign(scope, element[0].files)
			// 	scope.$apply()
			// })
			var model = $parse(attrs.fileModel);
	        var modelSetter = model.assign;

	        element.bind('change', function(){
	        	var aFiles = [];
		     	angular.forEach(element[0].files,function(file){
	            	aFiles.push(file);
				})
	            scope.$apply(function(){
	                modelSetter(scope, aFiles);
	            });
	        });
		}
	}
}])