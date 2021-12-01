var app = angular.module("ticket_sys");
app.controller("sbLogErrosSistemaCtrl", function($scope, LogAPIService,PerfilAcessoRotinaAPIService, $location, $timeout, $filter, $routeParams, growl, config){	
	$scope.aUserAccess = {};
	$scope.aErrorLog = [];
	$scope.eLog = {};

	$scope.aErrorLog = [];
	$scope.aScheduler = [];
	$scope.aTables = [];
	$scope.aMail = [];
	$scope.aAuditorSemExec = [];
	$scope.aAuditorSemNFe = [];
	$scope.aAuditorSemExecSaidas = [];
	$scope.aAuditorSemNFeSaidas = [];
	
	$scope.loadErrosSistemas = function(action) {
		$('#div-loading').show("slow");
		$('#loading').html('<div class="loading-img"> Carregando listagem de Logs. Aguarde...</div>');
		delete $scope.error;

		LogAPIService.loadErrosSistemas(action,null).then(function(response){
			$scope.aErrorLog = response.data;
			if ( $scope.aErrorLog.length > 0 ) {
				$('#div-loading').hide("slow");
			} else {
				$scope.nUserTk = 0;
				$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Log encontrado.');
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar os Logs: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.deleteLog = function(action,file) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> Exclusão de LOG',
	        message: 'Confirma a exclusão do Log selecionado?',
	        size: BootstrapDialog.SIZE_SMALL,
	        type: ($scope.lDeleted ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER),
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
	                dialog.getModalBody().html('<div class="loading-img"> Excluindo Log, aguarde...');
	                setTimeout(function(){
						$scope.file = $scope.aErrorLog[file].log_name;

						LogAPIService.loadErrosSistemas(action,$scope.file).then(function(response){
							$scope.loadErrosSistemas('search');
						}).catch(function(response){
							$scope.error = "Falha ao carregar os Logs: " + response.status + ' - ' + response.statusText;
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
	
	$scope.preValidaConfiguracoesServidor = function() {
		$scope.aErrorLog = [];
		$scope.aScheduler = [];
		$scope.aTables = [];
		$scope.aMail = [];
		$scope.aAuditorSemExec = [];
		$scope.aAuditorSemNFe = [];
		$scope.validaConfiguracoesServidor('error_log');
		$scope.validaConfiguracoesServidor('scheduler');
		$scope.validaConfiguracoesServidor('queue');
		$scope.validaConfiguracoesServidor('tables');
		$scope.validaConfiguracoesServidor('mail');
		$scope.validaConfiguracoesServidor('exec_auditor');
		$scope.validaConfiguracoesServidor('nfe_auditor');
		$scope.validaConfiguracoesServidor('exec_auditor_saida');
		$scope.validaConfiguracoesServidor('nfe_auditor_saida');
		$scope.validaConfiguracoesServidor('group_by');
	};
	
	$scope.validaConfiguracoesServidor = function(action) {
		$('#div-loading'+action).show("slow");
		$('#loading'+action).html('<div class="loading-img-min"></div>');
		LogAPIService.validaConfiguracoesServidor(action).then(function(response){
			if ( action == 'error_log' ) {
				$scope.aErrorLog	= response.data;
				if ( $scope.aErrorLog.length > 0 ) {
					$('#loadingerror_log').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingerror_log').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'scheduler' ) {
				$scope.aScheduler	= response.data;
				if ( $scope.aScheduler.length > 0 ) {
					$('#loadingscheduler').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingscheduler').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'queue' ) {
				$scope.aQueue	= response.data;
				
				if ( $scope.aQueue.length > 0) {
					$('#loadingqueue').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingqueue').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'tables' ) {
				$scope.aTables		= response.data;
				if ( $scope.aTables.length > 0 ) {
					$('#loadingtables').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingtables').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'mail' ) {
				$scope.aMail			= response.data;
				if ( $scope.aMail.lMail ) {
					$('#loadingmail').html('<i class="fa fa-lg fa-check ft-success"></i>');
				} else {
					$('#loadingmail').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				}
			}
			if ( action == 'exec_auditor' ) {
				$scope.aAuditorSemExec		= response.data;
				if ( $scope.aAuditorSemExec.length > 0 ) {
					$('#loadingexec_auditor').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingexec_auditor').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'nfe_auditor' ) {
				$scope.aAuditorSemNFe		= response.data;
				if ( $scope.aAuditorSemNFe.length > 0 ) {
					$('#loadingnfe_auditor').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingnfe_auditor').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'exec_auditor_saida' ) {
				$scope.aAuditorSemExecSaidas		= response.data;
				if ( $scope.aAuditorSemExecSaidas.length > 0 ) {
					$('#loadingexec_auditor_saida').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingexec_auditor_saida').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}
			if ( action == 'nfe_auditor_saida' ) {
				$scope.aAuditorSemNFeSaidas		= response.data;
				if ( $scope.aAuditorSemNFeSaidas.length > 0 ) {
					$('#loadingnfe_auditor_saida').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadingnfe_auditor_saida').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}

			if ( action == 'group_by' ) {
				$scope.aResultGB		= response.data;
				if ( $scope.aResultGB.return) {
					$('#loadinggroup_by').html('<i class="fa fa-lg fa-ban ft-danger"></i>');
				} else {
					$('#loadinggroup_by').html('<i class="fa fa-lg fa-check ft-success"></i>');
				}
			}

			$('#return'+action).html();		
		}).catch(function(response){
			$('#loading'+action).html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Falha ao executar a verificação para ' + action + '.');
		});
	};

	$scope.showLog = function(index){
		$scope.eLog = $scope.aErrorLog[index];
		$("#mdShowLog").modal("show");
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('errorslog').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $scope.url == '/errorslog' ) {
					$scope.loadErrosSistemas('search');
				}
			} else {				
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
				$location.path("/customerpanel");
          		// window.location.reload();	
			}
		}).catch(function(response){
			$scope.error = "Falha ao carregar as Rotinas do Usuário: " + response.status + ' - ' + response.statusText;
		});
	};

	$scope.getUserAccess();	
});	

app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});