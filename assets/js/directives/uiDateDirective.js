angular.module("ticket_sys").directive("uiDate", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatData = function(sdate){
				sdate = sdate.replace(/[^0-9]+/g, "");
				if (sdate.length > 2)
					sdate = sdate.substring(0,2) + "/" + sdate.substring(2);
				if (sdate.length > 5)
					sdate = sdate.substring(0,5) + "/" + sdate.substring(5,9);
				return sdate;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatData(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});

angular.module("ticket_sys").directive('ngmdate', function (dateFilter) {
    return {
        require:'ngModel',
        link:function (scope, elm, attrs, ctrl) {

            var dateFormat = attrs['date'] || 'dd/MM/yyyy';
           
            ctrl.$formatters.unshift(function (modelValue) {
                return dateFilter(modelValue, dateFormat);
            });
        }
    };
});

angular.module("ticket_sys").directive("uiZipcode", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatZip = function(sZipCode){
				sZipCode = sZipCode.replace(/[^0-9]+/g, "");
				if (sZipCode.length > 2)
					sZipCode = sZipCode.substring(0,2) + "." + sZipCode.substring(2);
				if (sZipCode.length > 6)
					sZipCode = sZipCode.substring(0,2) + "." + sZipCode.substring(3,6) + '-' + sZipCode.substring(6);
				return sZipCode;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatZip(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});

angular.module("ticket_sys").directive("uiTime", function($filter){
	return {
		require: "ngModel",
		//4º parametro se refere ao require acima, fazendo alusão ao Controller principal do ngModel
		link: function(scope, element, attrs, ctrl){
			var _formatTime = function(stime){
				stime = stime.replace(/[^0-9]+/g, "");
				if (stime.length > 2) {
					if (parseInt(stime.substring(0,2)) > 23)
						stime = '23' + ":" + stime.substring(2,4);
					else
						stime = stime.substring(0,2) + ":" + stime.substring(2,4);
				}
				return stime;
			}

			element.bind("keyup", function(){
				ctrl.$setViewValue(_formatTime(ctrl.$viewValue));
				ctrl.$render();
			});
		}
	};
});