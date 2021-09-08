var app = angular.module("ticket_sys");
app.controller("TicketSysCtrl", function($scope, $rootScope, $location, config, $anchorScroll, $sce, PerfilAcessoRotinaAPIService, UsuarioAPIService, AuthenticationService, NotificacaoAPIService, UsuarioFavoritoAPIService, TicketAPIService, UploadFileAPIService, ParametroAPIService, growl){
	$scope.app = "ticket_sys";
	$scope.url = $location.path();
	$scope.aUserData = {};
	$scope.aNotificacoes = [];
	$scope.aProdAuditor = [];
	$scope.aTriagemTickets = [];
	$scope.aUserAccess = [];
	$scope.nUnRead = 0;
	$scope.login_error = '';
	$scope.nUserFavorite = {};
	$scope.aUserFavorites = {};
	$scope.diasVctoPlano = {};
	$scope.msgVctoPlano = '';

	$scope.newUserTicket = {};

	$scope.$on('$viewContentLoaded', function() {
		$scope.url = $location.path();

		if ( $scope.url == '/login' ) {
			$scope.validConnected();
		} else {
			$scope.getUsuario();
			$scope.loadUserNotificacoes();
		}
	});

	$scope.getUsuario = function(loginValido = false) {
		delete $scope.aUserData;
		UsuarioAPIService.getUsuario('null').then(function(response){
			$scope.aUserData = response.data;
			if ( $scope.aUserData.user_id ) {
				config.lDark = ($scope.aUserData.user_tema == 'D' ? false : true);
				$scope.setDarkTheme();
				$scope.loadUserNotificacoes();	
				$scope.getUserAccess();
				$scope.getUserFavoritesByUserId();
				if(loginValido){
					$location.path("/pendingproducts");
					window.location.reload();
				}
				setTimeout(function(){
					$scope.getParametroADMINTKT();	
				}, 500);
			} else {
				$("#mdNaoLogado").modal("show");
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar o Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadUserNotificacoes = function() {
		NotificacaoAPIService.loadUserNotificacoes('index').then(function(response){
			$scope.aNotificacoes = response.data;
			$scope.nUnRead = 0;
			for (var i = $scope.aNotificacoes.length - 1; i >= 0; i--) {
				if ( $scope.aNotificacoes[i].ntf_lida == 'N' ) {
					$scope.nUnRead ++;
				}
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Notifcações do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.loadTriagemTickets  = function() {
		delete $scope.aTriagemTickets ;
		TicketAPIService.loadTriagemTickets('').then(function(response){
			$scope.aTriagemTickets  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os tickets para triagem: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.getParametroADMINTKT  = function() {
		ParametroAPIService.getParametro('ADMINTKT').then(function(response){
			var ADMINTKT  = response.data.par_conteudo;
			if ( ADMINTKT == $scope.aUserData.user_id ) {
				$scope.loadTriagemTickets();
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os Arquivos do Ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.ReadNotification = function(notifica, read) {
		notifica.ctrlaction = 'edit';
		notifica.ntf_lida = read;
		NotificacaoAPIService.saveNotificacao(notifica).then(function(response){
			delete $scope.aNotificacoes;
			$scope.loadUserNotificacoes();
			$location.path("/" + notifica.ntf_url);
			delete $scope.notifica;
		});
	};
	
	$scope.validLogin = function(login) {
		delete $scope.login_error;
		AuthenticationService.validLogin(login).then(function(response){
            $rootScope.userLogged = false;
            if ( response.data.return ) {
				delete $scope.login;	
				$scope.loginForm.$setPristine();
              	$rootScope.userLogged = true;     
				$scope.alerta('success', response.data.msg); 
				$scope.getUsuario(true);
				window.scrollTo(0,0);
			} else if ( !response.data.return ) {
				$scope.alerta('error', response.data.msg);
			}
		});
	};
	
	$scope.validConnected = function() {
		AuthenticationService.validConnected().then(function(response){
            $rootScope.userLogged = false;
            if ( response.data.return) {
				delete $scope.login;	
				$scope.loginForm.$setPristine();
              	$rootScope.userLogged = true;     
				$scope.getUsuario(true);
				window.scrollTo(0,0);
			} else if ( !response.data.return ) { 	
				$scope.alerta('error', response.data.msg);
			}
		});
	};

	$scope.getUserFavoritesByUserId = function() {
		UsuarioFavoritoAPIService.getUserFavoritesByUserId('null').then(function(response){
			$scope.aUserFavorites = response.data;
		}).catch(function(response){
			$scope.alerta("error", "Falha ao carregar os favoritos: " + response.status + ' - ' + response.statusText);
		});
	};

	$scope.NewUserFavorite = function(cDescricao, cCategoria, cURL) {
		$scope.nUserFavorite.ufv_descricao = cDescricao.substring(0, 40);
		$scope.nUserFavorite.ufv_categoria = cCategoria.substring(0, 40);
		$scope.nUserFavorite.ufv_url = cURL;
		$("#mdNewFavorite").modal("show");
	};

	$scope.saveUserFavorite = function(userFavorite) {
		userFavorite.ctrlaction = 'new';
		UsuarioFavoritoAPIService.saveUserFavorite(userFavorite).then(function(response){
			$scope.nUserFavorite = {};
			$scope.formNewFavorite.$setPristine();
			$("#mdNewFavorite").modal("hide");
			$scope.alerta('success', 'Favorito salvo com sucesso.');
			delete $scope.aUserFavorites;
			$scope.getUserFavoritesByUserId();
		}).catch(function(response){
			$scope.alerta("error", "Falha ao carregar os favoritos: " + response.status + ' - ' + response.statusText);
		});
	};

	$scope.$watch("newUserTicket.ticketFile",function(newValue,oldValue){
		if( $scope.newUserTicket.ticketFile ){
			$scope.uploadTicketFile();
		}
	});
	
	$scope.uploadTicketFile = function() {
		$('#div-upload-img').show("slow");
		$('#uploading-img').html('<div class="loading-img"> Aguarde, fazendo upload do arquivo selecionado...</div>');
		var cFileName = '';
		if ( $scope.newUserTicket.ticketFile[0] ) {
			cFileName = $scope.newUserTicket.ticketFile[0].name;
			var fd = new FormData();
			fd.append('file', $scope.newUserTicket.ticketFile[0]);
			fd.append('origem', 'T');
			UploadFileAPIService.uploadFile(fd).then(function(updfilereturn){
				if ( updfilereturn.data.return == true ) {
					$scope.newUserTicket.ticket_file = cFileName;
					$('#uploading-img').html('<strong><i class="fa fa-check text-success"></i></strong> ' + updfilereturn.data.retmsg);				
				} else if ( updfilereturn.data.return == false ) {
					$('#uploading-img').html('<strong><i class="fa fa-warning text-danger"></i></strong> ' + updfilereturn.data.retmsg);
				} else {
					$('#uploading-img').html('<strong><i class="fa fa-warning text-danger"></i></strong> Tamanho do arquivo é maior do que o permitido pelo servidor. Verifique!!!');
				}
			});
		}
	};

	$scope.LimpaTicket = function() {
		$scope.newUserTicket = {};
		$('#file_ticket').show("slow");
		$('#div-upload-img').hide("slow");
		$('#div-selected-files').hide("slow");
		$('#selected-files').html();
	}

	$scope.novoTicket = function(ticket) {
		ticket.ctrlaction = 'new_user_ticket';
		TicketAPIService.salvaTicket(ticket).then(function(response){			
			delete $scope.aTicket;
			$scope.newUserTicket = {};
			$scope.formNewTicket.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$scope.LimpaTicket();
			$("#mdNewTicket").modal("hide");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados do ticket: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.deleteUserFavorite = function(userFavorite) {
		userFavorite.ctrlaction = 'delete';
		UsuarioFavoritoAPIService.deleteUserFavorite(userFavorite).then(function(response){
			$scope.alerta('error', 'Favorito excluído com sucesso.');
			delete $scope.aUserFavorites;
			$scope.getUserFavoritesByUserId();
		}).catch(function(response){
			$scope.alerta("error", "Falha ao carregar os favoritos: " + response.status + ' - ' + response.statusText);
		});
	};

	$scope.getHelp = function(view) {
		// UsuarioAPIService.getUsuario('null').then(function(response){
		// 	$scope.aUserData = response.data;
		// 	$scope.loadUserNotificacoes();	
		// 	$scope.getUserAccess($scope.aUserData.user_id);
		// 	$scope.getUserFavoritesByUserId();
		// }).catch(function(response){
		// 	$scope.error = "Falha ao carregar o Usuário: " + response.status + ' - ' + response.statusText;
		// });
		$("#mdHelp").modal("show");
	};

	$scope.exportaDados = function(tabelaName, tabelaID, cTipoExporta) {	
		var $clonedTable = $("#"+tabelaID).clone();
		$clonedTable.find('[export-no-show]').remove();
		var HTMLtable = $("<div>").append( $clonedTable.eq(0).clone()).html();// $("#"+tabelaID).html();
		$("#nome_tabela").val(tabelaName);
		$("#tabela_html").val(HTMLtable);
		$("#exporta_tipo").val(cTipoExporta);
		$("#formExportaDados").submit();
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getPerfilRotinaLogin().then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	
	$scope.setDarkTheme = function(lSaveTheme = false) {
		if ( config.lDark ) {
			$(".kt-header-mobile").removeClass("dark-theme-header");
			$(".kt-header__topbar").removeClass("dark-theme-header");

			$(".kt-aside").removeClass("dark-kt-aside");
			 
			$(".kt-subheader").removeClass("dark-kt-subheader");
			$(".kt-portlet").removeClass("dark-theme-portlet");
			$(".kt-grid--root").removeClass("dark-theme-body");
			$(".kt-header").removeClass("dark-theme-header");
			$(".kt-menu__nav").removeClass("dark-kt-menu__nav");
			
			$(".kt-header__topbar-item").removeClass("dark-kt-header__topbar-item");
			$(".kt-header__topbar-user").removeClass("dark-kt-header__topbar-user");			 
			 
			$(".dropdown-menu").removeClass("dark-dropdown-menu");
			$(".kt-quick-panel").removeClass("dark-kt-quick-panel");
			$(".kt-quick-panel__content").removeClass("dark-kt-quick-panel__content");

			$(".kt-quick-panel__content").removeClass("dark-kt-quick-panel__content");

			$(".modal").removeClass("dark-modal");

			config.lDark = false;
		} else {			
			$(".kt-header-mobile").addClass("dark-theme-header");
			$(".kt-header__topbar").addClass("dark-theme-header");

			$(".kt-aside").addClass("dark-kt-aside");
			
			$(".kt-subheader").addClass("dark-kt-subheader");
			$(".kt-portlet").addClass("dark-theme-portlet");
			$(".kt-grid--root").addClass("dark-theme-body");
			$(".kt-header").addClass("dark-theme-header");
			$(".kt-menu__nav").addClass("dark-kt-menu__nav");
			 
			$(".kt-header__topbar-item").addClass("dark-kt-header__topbar-item");
			$(".kt-header__topbar-user").addClass("dark-kt-header__topbar-user");			 
			 
			$(".dropdown-menu").addClass("dark-dropdown-menu");
			$(".kt-quick-panel").addClass("dark-kt-quick-panel");
			$(".kt-quick-panel__content").addClass("dark-kt-quick-panel__content");

			$(".modal").addClass("dark-modal");
			
			config.lDark = true;
		}
		if ( lSaveTheme ) {
			var aUserTheme = {};
			aUserTheme.nUserTK = 'null';
			aUserTheme.theme = (config.lDark ? 'D' : 'C');
			aUserTheme.ctrlaction = 'theme';
			UsuarioAPIService.saveUsuario(aUserTheme).then(function(response){
				$scope.alerta('success', 'Tema selecionado com sucesso.');
			}).catch(function(response){
				$scope.alerta('error', 'Falha ao salvar o tema selecionado.' + response.status + ' - ' + response.statusText);
			});
		}
	};

	$scope.loadUserClientes = function() {
		delete $scope.aUserClientes;
		UsuarioAPIService.loadUserClientes().then(function(response){
			$scope.aUserClientes = response.data;
			if ( $scope.aUserClientes.length > 1 ) {
				$("#mdUserClientes").modal("show");
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Empresas: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.userChangeEmp = function(ncliTK) {
		var aUserEmp = {};
		aUserEmp.nCliTK = ncliTK;
		aUserEmp.ctrlaction = 'changeEmp';
		UsuarioAPIService.saveUsuario(aUserEmp).then(function(response){
			$scope.alerta('success', 'Empresa alterada com sucesso.<br/> Você será redirecionado para a Dashboard.');
			$("#mdUserClientes .close").click();
			$scope.getUserAccess();
			$location.path("/dashboard");
			window.location.reload();
		}).catch(function(response){
			$scope.error = "Falha ao mudar a empresa: " + response.status + ' - ' + response.statusText;
		});
	};


	$scope.AccessRotFilter = function(rotina) {
		for (var i = 0; i < $scope.aUserAccess.length; i++) {
			if ( $scope.aUserAccess[i].pta_rot_nome == rotina )
				return true;
		};
		return false;
	};


	$scope.AccessRotNivelFilter = function(rotina, nNivel) {
		for (var i = 0; i < $scope.aUserAccess.length; i++) {
			if ( $scope.aUserAccess[i].pta_rot_nome == rotina ) {
				if ( $scope.aUserAccess[i].pta_nivel >= nNivel ) {
					return true;
				} else {
					return false;
				}
			}
		};
		return false;
	};

	$scope.recPasswd = function(recpasswd) {
		AuthenticationService.recPwd(recpasswd).then(function(response){
			delete $scope.recpasswd;
			$scope.recemailform.$setPristine();
			if (response.data == 1) {
				$scope.alerta('success', 'Senha alterada com êxito. Em instantes você receberá um e-mail com a nova senha.' + response.data);	
			}
			else {				
				$scope.alerta('error', 'E-mail não encontrado em nossa base de dados. ' + response.data);	
			}
		});
	};

	$scope.alerta = function(type, msg, cTitle = ''){
	    var config = {
	      title: cTitle,
	      disableCountDown: true
	    };
	    if ( type == 'success') 
	    	growl.success(msg, config);
	    if ( type == 'warning') 
	    	growl.warning(msg, config);
	    if ( type == 'error') 
	    	growl.error(msg, config);
	    if ( type == 'info') 
	    	growl.info(msg, config);
	}

});

app.filter("groupBy",["$parse","$filter",function($parse,$filter){
	if ( $filter ) {
		return function(array,groupByField){
			var result	= [];
			var prev_item = null;
			var groupKey = false;
			var filteredData = $filter('orderBy')(array,groupByField);
			for(var i=0;i<filteredData.length;i++){
				groupKey = false;
				if(prev_item !== null){
					if(prev_item[groupByField] !== filteredData[i][groupByField]){
						groupKey = true;
					}
				} else {
					groupKey = true;  
				}
				if(groupKey){
					filteredData[i]['group_by_key'] =true;  
				} else {
					filteredData[i]['group_by_key'] =false;  
				}
				result.push(filteredData[i]);
				prev_item = filteredData[i];
			}
			return result;
		}
	}
}])