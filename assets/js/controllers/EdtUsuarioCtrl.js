angular.module("ticket_sys").controller("EdtUsuarioCtrl", function($scope, PerfilAcessoRotinaAPIService,PerfilAcessoAPIService, UsuarioAPIService, PastaTrabalhoAPIService, $location, $filter, $routeParams, growl, config){
	$scope.aTypes = [{type_id: "1", type_descr: "Administrador"},{type_id: "2", type_descr: "Funcionário"},{type_id: "3", type_descr: "Cliente"}];
	$scope.aUserData = {};
	$scope.eUsuario = [];
	$scope.aUserAccess = {};

	$scope.aPerfisAcesso = [];
	$scope.aPerfilAcesso = {};

	$scope.getLoggedUser = function() {
		UsuarioAPIService.getUsuario('null').then(function(response){
			$scope.aUserData = response.data;
			$scope.getUserAccess();
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUsuario = function() {
		UsuarioAPIService.getUsuario($routeParams.userTK).then(function(response){
			$scope.eUsuario = response.data;
			$scope.eUsuario.user_passwd = '';
			if ( $scope.eUsuario.user_cli_id ) {
				$scope.eUsuario.user_cli_id = jQuery.parseJSON($scope.eUsuario.user_cli_id);
			}
			$scope.eUsuario.user_resp_ticket = ($scope.eUsuario.user_resp_ticket == 'S' ? true : false);
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadPerfisAcesso = function() {
		delete $scope.aPerfisAcesso;
		$scope.lDeleted = false;
		PerfilAcessoAPIService.loadPerfisAcesso().then(function(response){
			$scope.aPerfisAcesso = response.data;
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Perfis de Acesso: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadPerfisAcessoByCliPlano = function(cli_id) {
		delete $scope.aPerfilAcesso;
		$scope.lDeleted = false;
		PerfilAcessoAPIService.loadPerfisAcessoByCliPlano(cli_id).then(function(response){
			$scope.aPerfilAcesso = response.data;
			$scope.eUsuario.user_pfa_id = $scope.aPerfilAcesso.pfa_id;
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Perfis de Acesso: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.editUser = function(user) {
		user.ctrlaction = 'edit';
		UsuarioAPIService.saveUsuario(user).then(function(response){
			delete $scope.eUsuario;
			$scope.edtForm.$setPristine();
			$location.path("/users");
			$scope.alerta('success', 'Usuário alterado com sucesso.');
		});
	};

	$scope.loadPastaTrabalho  = function() {
		delete $scope.aPastaTrabalho ;
		PastaTrabalhoAPIService.loadPastaTrabalho('').then(function(response){
			$scope.aPastaTrabalho  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os PastaTrabalhos: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('users').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				$scope.getUsuario();
			} else {
				if ( $scope.aUserData.user_token == $routeParams.userTK ) {	
					$scope.aUserAccess.pta_rot_nome = 'users';
					$scope.aUserAccess.rtu_user_id = $scope.aUserData.user_id;
					$scope.aUserAccess.rtu_nivel = '2';				
					$scope.getUsuario();
				} else {
					$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
				}
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getLoggedUser();

	$scope.cfgPerfisAcesso = {
		create: false,
		valueField: 'pfa_id',
		labelField: 'pfa_id',
    	searchField: ['pfa_id', 'pfa_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o Perfil de Acesso',
		maxItems: 1,
		onInitialize: function(selectize){
			$scope.loadPerfisAcesso();
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="20%"><strong>' + escape(item.pfa_id) + '</strong>' + ' </td>'
                    +    '<td class="text-nowrap left-justify" width="80%">' + escape(item.pfa_descricao)  + ' </td>'
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.pfa_id) + ' | '
					+ '</strong>'
					+ escape(item.pfa_descricao)
					+ '</div>';
			}
		},
	};

	$scope.cfgType = {
		create: false,
		valueField: 'type_id',
		labelField: 'type_descr',
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o tipo do Usuário',
		maxItems: 1,
	};

	$scope.cfgModulos = {
		create: false,
		valueField: 'mod_id',
		labelField: 'mod_descr',
		delimiter: config.SelDelimiter,
		placeholder: 'Módulo do Auditor',
		maxItems: 1,
	};

	$scope.cfgPastaTrabalho = {
		create: false,
		valueField: 'pst_id',
    	searchField: ['pst_id','pst_descricao','grt_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um(a) PastaTrabalho',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadPastaTrabalho();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.pst_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.grt_descricao ? escape(item.grt_descricao) : '') + ' > ' + (item.pst_descricao ? escape(item.pst_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.pst_id) + ' | '
					+ '</strong>'
					+ (item.grt_descricao ? escape(item.grt_descricao) : '  ') + ' > '
					+ (item.pst_descricao ? escape(item.pst_descricao) : '  ')
					+ '</div>';
			}
		},
	};
});	