angular.module("ticket_sys").directive("uiCelphone", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formarPhone = function(sphone){
				sphone = sphone.replace(/[^0-9]+/g, "");
				sphone = '(' + sphone;
				if (sphone.length > 2)
					sphone = sphone.substring(0,3) + ')' +  sphone.substring(3);
				if (sphone.length > 9)
					sphone = sphone.substring(0,9) + '-' +  sphone.substring(9);
				if (sphone.length > 14)
					sphone = sphone.substring(0,14);

				return sphone;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formarPhone(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});
angular.module("ticket_sys").directive("uiPhone", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formarPhone = function(sphone){
				sphone = sphone.replace(/[^0-9]+/g, "");
				sphone = '(' + sphone;
				if (sphone.length > 2)
					sphone = sphone.substring(0,3) + ')' +  sphone.substring(3);
				if (sphone.length > 9)
					sphone = sphone.substring(0,8) + '-' +  sphone.substring(8);
				if (sphone.length > 14)
					sphone = sphone.substring(0,14);

				return sphone;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formarPhone(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});