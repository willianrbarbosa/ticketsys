var app = angular.module("ticket_sys");
app.controller("NotificacaoCtrl", function($scope, NotificacaoAPIService, PerfilAcessoRotinaAPIService, $location, $filter, $routeParams, growl, config){	
	$scope.aNotificacoes = [];
	$scope.eNotificacao = {};

	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 10;
	$scope.nPgTotal = 0;
	$scope.iptsearch = '';

	$scope.loadUserNotificacoes = function() {
		NotificacaoAPIService.loadUserNotificacoes('null').then(function(response){
			$scope.aNotificacoes = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;			
			$scope.nPgTotal = ($scope.aNotificacoes.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aNotificacoes.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma notificação.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Notifcações do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
    	return $filter('filter')($scope.aNotificacoes, $scope.iptsearch);
    }
    
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getData().length/$scope.numPerPage);                
    }

	$scope.ReadNotification = function(notifica, read, out) {
		notifica.ctrlaction = 'edit';
		notifica.ntf_lida = read;
		NotificacaoAPIService.saveNotificacao(notifica).then(function(response){
			delete $scope.notifica;
			delete $scope.aNotificacoes;
			if ( out ) {
				$location.path("/" + notifica.ntf_url);
			} else {
				$scope.loadUserNotificacoes();
			}
		});
	};

	$scope.loadUserNotificacoes();

	$scope.classe1 = 'selecionado';

	$scope.isSelected = function(notif) {
		if ( notif ) {
			return notif.some(function(respuser){
				return respuser.selecionado
			});
		}
	};

	$scope.selectAll = function() {
		for (var i = 0; i < $scope.aNotificacoes.length; i++) {
			$scope.aNotificacoes[i]['selecionado'] = $("#checkall").is(":checked");
		};

	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};	
});	

app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});