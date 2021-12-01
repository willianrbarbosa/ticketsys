angular.module("ticket_sys").directive("uiCpf", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatCpf = function(sCpf){
				sCpf = sCpf.replace(/[^0-9]+/g, "");
				if (sCpf.length > 3)
					sCpf = sCpf.substring(0,3) + "." + sCpf.substring(3);
				if (sCpf.length > 7)
					sCpf = sCpf.substring(0,3) + "." + sCpf.substring(4,7) + '.' + sCpf.substring(7);
				if (sCpf.length > 11)
					sCpf = sCpf.substring(0,3) + "." + sCpf.substring(4,7) + '.' + sCpf.substring(8,11) + '-' + sCpf.substring(11);
				return sCpf;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatCpf(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});

angular.module("ticket_sys").directive("uiRg", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatRg = function(sRg){
				sRg = sRg.replace(/[^0-9]+/g, "");
				if (sRg.length > 2)
					sRg = sRg.substring(0,2) + "." + sRg.substring(2);
				if (sRg.length > 6)
					sRg = sRg.substring(0,2) + "." + sRg.substring(3,6) + '.' + sRg.substring(6);
				if (sRg.length > 10)
					sRg = sRg.substring(0,2) + "." + sRg.substring(3,6) + '.' + sRg.substring(7,10) + '-' + sRg.substring(10);
				return sRg;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatRg(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});

angular.module("ticket_sys").directive("uiCnpj", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatCnpj = function(sCnpj){
				sCnpj = sCnpj.replace(/[^0-9]+/g, "");
				if (sCnpj.length > 2)
					sCnpj = sCnpj.substring(0,2) + "." + sCnpj.substring(2);
				if (sCnpj.length > 6)
					sCnpj = sCnpj.substring(0,2) + "." + sCnpj.substring(3,6) + '.' + sCnpj.substring(6);
				if (sCnpj.length > 10)
					sCnpj = sCnpj.substring(0,2) + "." + sCnpj.substring(3,6) + '.' + sCnpj.substring(7,10) + '/' + sCnpj.substring(10);
				if (sCnpj.length > 14)
					sCnpj = sCnpj.substring(0,2) + "." + sCnpj.substring(3,6) + '.' + sCnpj.substring(7,10) + '/' + sCnpj.substring(11,15) + '-' + sCnpj.substring(15);
				return sCnpj;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatCnpj(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});

angular.module("ticket_sys").directive("uiInsce", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatIE = function(sIE){
				sIE = sIE.replace(/[^0-9]+/g, "");
				if (sIE.length > 3)
					sIE = sIE.substring(0,3) + "." + sIE.substring(3);
				if (sIE.length > 7)
					sIE = sIE.substring(0,3) + "." + sIE.substring(4,7) + '.' + sIE.substring(7);
				if (sIE.length > 11)
					sIE = sIE.substring(0,3) + "." + sIE.substring(4,7) + '.' + sIE.substring(8,11) + '.' + sIE.substring(11);
				return sIE;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatIE(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});