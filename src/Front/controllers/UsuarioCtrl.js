var app = angular.module("ticket_sys");
app.controller("UsuarioCtrl", function($scope, PerfilAcessoRotinaAPIService, UsuarioAPIService, PerfilAcessoAPIService, PastaTrabalhoAPIService, $location, $filter, $routeParams, growl, config){	
	$scope.aTypes = [{type_id: "1", type_descr: "Administrador"},{type_id: "2", type_descr: "Desenvolvedor"},{type_id: "3", type_descr: "Solicitante"}];
	$scope.aUsuarios = [];
	$scope.aAcessos = [];
	$scope.aUserAccess = {};
	$scope.aPerfisAcesso = [];
	$scope.aPerfilAcesso = {};
	$scope.usuario = {};
	
	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 10;
	$scope.nPgTotal = 0;
	$scope.crtPageAc = 0;
	$scope.cPgAc = 0;
	$scope.nPgTotalAc = 0;
	$scope.iptsearch = '';
	$scope.lDeleted = false;

	$scope.loadUsuarios = function() {
		$scope.lDeleted = false;
		delete $scope.aUsuarios;
		UsuarioAPIService.loadUsuarios().then(function(response){
			$scope.aUsuarios = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;			
			$scope.nPgTotal = ($scope.aUsuarios.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aUsuarios.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Usuário cadastrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Usuários: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadUsuariosInativos = function() {
		$scope.lDeleted = true;
		delete $scope.aUsuarios;
		UsuarioAPIService.loadUsuariosInativos().then(function(response){
			$scope.aUsuarios = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;			
			$scope.nPgTotal = ($scope.aUsuarios.length/$scope.numPerPage*$scope.maxSize).toFixed(0);
			$scope.getData();
			if ( $scope.aUsuarios.length <= 0 ) {
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Usuário inativo.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Usuários: " + response.status + ' - ' + response.statusText;
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
			$scope.usuario.user_pfa_id = $scope.aPerfilAcesso.pfa_id;
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Perfis de Acesso: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
    	return $filter('filter')($scope.aUsuarios, $scope.iptsearch);
    }
    
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getData().length/$scope.numPerPage);                
    }

	$scope.getUserEmail = function(user) {
		UsuarioAPIService.getUserEmail(user.user_email).then(function(response){
			if ( response.data != 'false' ) {
				if ( Object.keys(response.data).length > 0 ) {
					$scope.alerta('warning', 'ATENÇÃO!!! E-mail já cadastrado.');
					user.user_email = '';
					$('#user_email').focus();
				}
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Usuários: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAcessos = function(usrTK) {
		delete $scope.aAcessos;
		UsuarioAPIService.getUserAcessos(usrTK).then(function(response){
			$scope.aAcessos = response.data;			
			$("#mdUserAcessos").modal("show");	
			$scope.crtPageAc = 0;
			$scope.cPgAc = 1;			
			$scope.nPgTotalAc = ($scope.aAcessos.length/15*$scope.maxSize).toFixed(0);
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Acessos do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.newUser = function(out) {
		$scope.usuario.ctrlaction = 'new';
		UsuarioAPIService.saveUsuario($scope.usuario).then(function(response){
			delete $scope.usuario;
			delete $scope.aUsuarios;
			$scope.newForm.$setPristine();
			if ( out ) { $location.path("/users"); }
			$scope.alerta('success', 'Usuário salvo com sucesso.');
		});
	};

	$scope.admLogin = function(userTK) {
		UsuarioAPIService.admLogin(userTK).then(function(response){
			$scope.alerta('success', 'Logando com o Usuário selecionado. Aguarde...');			
			$location.path("/pendingproducts");
			window.location.reload();
			window.scrollTo(0,0);
		}).catch(function(response){
			$scope.error = "Falha ao tentar logar com esse usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.deleteUsuario = function(user) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> Exclusão de Usuário',
	        message: 'Confirma a exclusão do(s) Usuário(s) selecionado(s)?',
	        size: BootstrapDialog.SIZE_SMALL,
	        type: BootstrapDialog.TYPE_DANGER,
	        closable: true,
	        draggable: true,
	        buttons: [{
	            id: 'btn-ok',   
		        icon: 'fa fa-trash',       
		        label: 'Excluir',
		        cssClass: 'btn-xs btn-danger', 
	            hotkey: 13, // Enter.
		        autospin: false,
		        action: function(dialog){    
	                dialog.enableButtons(false);
	                dialog.setClosable(false);
	                dialog.getModalBody().html('<i class="fa fa-spinner fa-spin fa-sm fa-fw ft-danger"></i> Excluindo Usuários, aguarde...');
	                setTimeout(function(){
						$scope.aUsuarios = user.filter(function(user){
							if (user.selecionado) {
								user.ctrlaction = 'delete';
								UsuarioAPIService.deleteUsuario(user).then(function(response){
									$scope.alerta('error', 'Usuário(s) excluído(s) com sucesso.');
									delete $scope.aUsuarios;
									$scope.loadUsuarios();
								});
							}
						});
	                    dialog.close();
	                }, 1500);
		        }
	        }, {
	            id: 'btn-cancel',   
	            icon: 'fa fa-ban',       
		        label: 'Fechar',
		        autospin: true,
		        hotkey: 65,
		        cssClass: 'btn btn-xs btn-default', 
	            action: function(dialog) {
					dialog.close();
	            }
	        }]
	    });
	};

	$scope.inativaUsuario = function(user) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> Exclusão de Usuário',
	        message: 'Confirma a inativação do(s) Usuário(s) selecionado(s)?',
	        size: BootstrapDialog.SIZE_SMALL,
	        type: BootstrapDialog.TYPE_DANGER,
	        closable: true,
	        draggable: true,
	        buttons: [{
	            id: 'btn-ok',   
		        icon: 'fa fa-ban',       
		        label: 'Inativar',
		        cssClass: 'btn-xs btn-danger', 
	            hotkey: 13, // Enter.
		        autospin: false,
		        action: function(dialog){    
	                dialog.enableButtons(false);
	                dialog.setClosable(false);
	                dialog.getModalBody().html('<i class="fa fa-spinner fa-spin fa-sm fa-fw ft-danger"></i> Inativando Usuários, aguarde...');
	                setTimeout(function(){
						$scope.aUsuarios = user.filter(function(user){
							if (user.selecionado) {
								user.ctrlaction = 'inativa';
								UsuarioAPIService.deleteUsuario(user).then(function(response){
									$scope.alerta('error', 'Usuário(s) inativado(s) com sucesso.');
									delete $scope.aUsuarios;
									$scope.loadUsuarios();
								});
							}
						});
	                    dialog.close();
	                }, 1500);
		        }
	        }, {
	            id: 'btn-cancel',   
	            icon: 'fa fa-ban',       
		        label: 'Fechar',
		        autospin: true,
		        hotkey: 65,
		        cssClass: 'btn btn-xs btn-default', 
	            action: function(dialog) {
					dialog.close();
	            }
	        }]
	    });
	};

	$scope.ativaUsuario = function(user) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-success"></i> Ativação de Usuário',
	        message: 'Confirma a ativação do(s) Usuário(s) selecionado(s)?',
	        size: BootstrapDialog.SIZE_SMALL,
	        type: BootstrapDialog.TYPE_SUCCESS,
	        closable: true,
	        draggable: true,
	        buttons: [{
	            id: 'btn-ok',   
		        icon: 'fa fa-user-plus',       
		        label: 'Ativar',
		        cssClass: 'btn-xs btn-success', 
	            hotkey: 13, // Enter.
		        autospin: false,
		        action: function(dialog){    
	                dialog.enableButtons(false);
	                dialog.setClosable(false);
	                dialog.getModalBody().html('<i class="fa fa-spinner fa-spin fa-sm fa-fw ft-success"></i> Ativando Usuários, aguarde...');
	                setTimeout(function(){
						$scope.aUsuarios = user.filter(function(user){
							if (user.selecionado) {
								user.ctrlaction = 'ativa';
								UsuarioAPIService.ativaUsuario(user).then(function(response){
									$scope.alerta('success', 'Usuário(s) ativado(s) com sucesso.');
									delete $scope.aUsuarios;
									$scope.loadUsuarios();
								});
							}
						});
	                    dialog.close();
	                }, 1500);
		        }
	        }, {
	            id: 'btn-cancel',   
	            icon: 'fa fa-ban',       
		        label: 'Fechar',
		        autospin: true,
		        hotkey: 65,
		        cssClass: 'btn btn-xs btn-default', 
	            action: function(dialog) {
					dialog.close();
	            }
	        }]
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

	$scope.classe1 = 'selecionado';

	$scope.isSelected = function(user) {
		if ( user ) {
			return user.some(function(respuser){
				return respuser.selecionado
			});
		}
	};

	$scope.selectAll = function() {
		for (var i = 0; i < $scope.aUsuarios.length; i++) {
			$scope.aUsuarios[i]['selecionado'] = $("#checkall").is(":checked");
		};

	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('users').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				$scope.loadUsuarios();
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();


	$scope.cfgType = {
		create: false,
		valueField: 'type_id',
		labelField: 'type_descr',
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione o tipo do Usuário',
		maxItems: 1,
	};
	
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

	$scope.cfgModulos = {
		create: false,
		valueField: 'mod_id',
		labelField: 'mod_descr',
		delimiter: config.SelDelimiter,
		placeholder: 'Módulo do Auditor',
		maxItems: 1,
	};
});	

app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});