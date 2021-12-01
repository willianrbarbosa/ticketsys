var app = angular.module("ticket_sys");
app.controller("PastaTrabalhoCtrl", function($scope, PerfilAcessoRotinaAPIService, PastaTrabalhoAPIService, GrupoTrabalhoAPIService, $location, $filter, $routeParams, growl, config){

	$scope.aPastaTrabalho = [];
	$scope.aUserAccess = {};

	$scope.nPastaTrabalho = {};
	$scope.ePastaTrabalho = {};

	$scope.aGrupoTrabalho = [];

	$scope.filtANCM = [];
	$scope.currentPage = 0;
	$scope.cPg = 0;
	$scope.numPerPage = 30;
	$scope.maxSize = 5;
	$scope.nPgTotal = 0;
	$scope.crtPageAc = 0;
	$scope.cPgAc = 0;
	$scope.nPgTotalAc = 0;
	$scope.iptsearch = '';
	$scope.lDeleted= false;

	$scope.operatorfilterBD = '';
	$scope.filterFieldBD = '';
	$scope.sinalfilterBD = '';
	$scope.cfilterBD = '';
	$scope.cFiltroManual = '';
	$scope.lTemFiltro = false;

	// Editor options.
	$scope.ckEditorOptions = {
		textInput: 'pretext',
		options: {
			language: 'pt',
			allowedContent: true,
			entities: false
		}
	};

	$scope.addFiltroManual = function (cOprd, cCampo, cSinal, cConteudo) {
		$scope.cFiltroManual = $scope.cFiltroManual + " " + ($scope.cFiltroManual ? (cOprd ? cOprd : '') : '') + " " + cCampo + " " + cSinal;
		if ( (cSinal == 'LIKE') || (cSinal == 'NOT LIKE') ) {
			$scope.cFiltroManual = $scope.cFiltroManual + " " + "'*"+cConteudo+"*'";
		} else {
			$scope.cFiltroManual = $scope.cFiltroManual + " " + "'"+cConteudo+"'";
		}

		$scope.operatorfilterBD = '';
		$scope.filterFieldBD = '';
		$scope.sinalfilterBD = '';
		$scope.cfilterBD = '';
	};

	$scope.limpaFiltroManual = function (lReload) {
		$scope.cFiltroManual = ''
		$scope.operatorfilterBD = '';
		$scope.filterFieldBD = '';
		$scope.sinalfilterBD = '';
		$scope.cfilterBD = '';
		if ( lReload ) {
			if ( $scope.lDeleted ) {
				$scope.loadPastaTrabalhoDeletados($scope.cFiltroManual);
			} else {
				$scope.loadPastaTrabalho($scope.cFiltroManual);
			}
		}
	};

	$scope.loadPastaTrabalho  = function(cFilManual) {
		$scope.lDeleted = false;
		delete $scope.aPastaTrabalho ;
		PastaTrabalhoAPIService.loadPastaTrabalho(cFilManual).then(function(response){
			$scope.aPastaTrabalho  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aPastaTrabalho.length > $scope.numPerPage ? ($scope.aPastaTrabalho.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aPastaTrabalho.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Pasta de Trabalho encontrada para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhuma Pasta de Trabalho cadastrada.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Pastas de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.loadPastaTrabalhoDeletados  = function(cFilManual) {
		$scope.lDeleted = true;
		delete $scope.aPastaTrabalho ;
		PastaTrabalhoAPIService.loadPastaTrabalhoDeletados(cFilManual).then(function(response){
			$scope.aPastaTrabalho  = response.data;
			$scope.currentPage = 0;
			$scope.cPg = 1;
			$scope.nPgTotal = ($scope.aPastaTrabalho.length > $scope.numPerPage ? ($scope.aPastaTrabalho.length/$scope.numPerPage*$scope.maxSize).toFixed(0) : 1);
			$scope.getData();
			if ( $scope.aPastaTrabalho.length <= 0 ) {
				if ( cFilManual ) {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Pasta de Trabalho excluído foi encontrado para os filtros informados.');
				} else {
					$('#loading').html('<strong><i class="fa fa-lg fa-exclamation-triangle"></i></strong> Nenhum Pasta de Trabalho foi excluído.');
				}
			}
			if ( cFilManual ) {
				$scope.lTemFiltro = true;
			} else {
				$scope.lTemFiltro = false;
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Pastas de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.rePage = function() {
		$scope.cPg = 1;
		$scope.currentPage = 0;
	};

	$scope.getData = function () {
		return $filter('filter')($scope.aPastaTrabalho, $scope.iptsearch);
	}

	$scope.numberOfPages=function(){
		return Math.ceil($scope.getData().length/$scope.numPerPage);
	}

	$scope.range = function(min, max, step){
		step = step || 1;
		var input = [];
		for (var i = min; i <= max; i += step) input.push(i);
		return input;
	};

	$scope.changePage = function(nIdx) {
		$scope.cPg = nIdx;
		$scope.currentPage = nIdx-1;
	};

	$scope.deletaPastaTrabalho = function(pastatrabalho) {
		BootstrapDialog.show({
	        title: '<i class="fa fa-lg fa-warning"></i> ' + ($scope.lDeleted ? 'Restauração' : 'Exclusão') + ' de Pasta de Trabalho',
	        message: 'Confirma a ' +  ($scope.lDeleted ? 'restauração' : 'exclusão') + ' do(s) Pasta(s) de Trabalho selecionada(s)?',
	        size: BootstrapDialog.SIZE_MEDIUM,
	        type: ($scope.lDeleted ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER),
	        closable: true,
	        draggable: true,
	        buttons: [{
	            id: 'btn-ok',
		        icon: 'fa' +  ($scope.lDeleted ? 'fa-window-restore' : 'fa-trash'),
		        label: ($scope.lDeleted ? 'Restaurar' : 'Excluir'),
		        cssClass: 'btn-xs ' +  ($scope.lDeleted ? 'btn-success' : 'btn-danger'), 
	            hotkey: 13, // Enter.
		        autospin: false,
		        action: function(dialog){
	                dialog.enableButtons(false);
	                dialog.setClosable(false);
	                dialog.getModalBody().html('<div class="loading-img">' + ($scope.lDeleted ? ' Restaurando ' : ' Excluindo ') + 'Pastas de Trabalho, aguarde...');
	                setTimeout(function(){
						$scope.aPastaTrabalho = pastatrabalho.filter(function(pastatrabalho){
							if (pastatrabalho.selecionado) {
								pastatrabalho.ctrlaction = ($scope.lDeleted ? 'restore' : 'delete');
								PastaTrabalhoAPIService.deletaPastaTrabalho(pastatrabalho).then(function(response){
									if ( response.data.return ) {
										$scope.alerta('success', response.data.msg);
									} else {
										$scope.alerta('error', response.data.msg);
									}
									delete $scope.aPastaTrabalho;
									if ( $scope.lDeleted ) {
										$scope.loadPastaTrabalhoDeletados($scope.cFiltroManual);
									} else {
										$scope.loadPastaTrabalho($scope.cFiltroManual);
									}
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

	$scope.novoPastaTrabalho = function(pastatrabalho, lOut) {
		pastatrabalho.ctrlaction = 'new';
		PastaTrabalhoAPIService.salvaPastaTrabalho(pastatrabalho).then(function(response){			
			delete $scope.aPastaTrabalho;
			$scope.nPastaTrabalho = {};
			$scope.newForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			if ( lOut ) { 
				$location.path("/pastatrabalho"); 
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Pasta de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.loadGrupoTrabalho  = function() {
		delete $scope.aGrupoTrabalho ;
		GrupoTrabalhoAPIService.loadGrupoTrabalho('').then(function(response){
			$scope.aGrupoTrabalho  = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Pasta de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response.TypeError));
		});
	};

	$scope.getPastaTrabalho = function(pstTK) {
		delete $scope.ePastaTrabalho;
		PastaTrabalhoAPIService.getPastaTrabalhoByID(pstTK).then(function(response){
			$scope.ePastaTrabalho = response.data;
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar os dados da Pasta de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.editaPastaTrabalho = function(pastatrabalho) {
		pastatrabalho.ctrlaction = 'edit';
		PastaTrabalhoAPIService.salvaPastaTrabalho(pastatrabalho).then(function(response){			
			delete $scope.aPastaTrabalho;
			$scope.ePastaTrabalho = {};
			$scope.edtForm.$setPristine();
			if ( response.data.return ) {
				$scope.alerta('success', response.data.msg);
			} else {
				$scope.alerta('error', response.data.msg);
			}
			$location.path("/pastatrabalho");
		}).catch(function(response){
			$scope.alerta("error","Falha ao salvar os dados da Pasta de Trabalho: " + (response.status ? response.status + " - " : "") + (response.statusText ? response.statusText : response));
		});
	};

	$scope.isSelected = function(user) {
		if ( user ) {
			return user.some(function(respuser){
				return respuser.selecionado
			});
		}
	};

	$scope.selectAll = function() {
		for (var i = 0; i < $scope.aPastaTrabalho.length; i++) {
			$scope.aPastaTrabalho[i]['selecionado'] = $("#checkall").is(":checked");
		};
	};

	$scope.ordenarPor = function(sCampo) {
		$scope.criterioOrdenacao = sCampo;
		$scope.direcaoOrdenacao = !$scope.direcaoOrdenacao;
	};

	$scope.getUserAccess = function() {
		PerfilAcessoRotinaAPIService.getLoggedUsuariorRotina('pasta_trabalho').then(function(response){
			if ( response.data != 'false' ) {
				$scope.aUserAccess = response.data;
				if ( $routeParams.pstTK ) {
					$scope.getPastaTrabalho($routeParams.pstTK);
				} else {
					if ( $scope.url != '/novopastatrabalho' ) {
						$scope.loadPastaTrabalho($scope.cFiltroManual);
					}
				}
			} else {
				$('#loading').html('Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.');
			}
		}).catch(function(response){
			$scope.alerta("error","Falha ao carregar as Rotinas do Usuário: " + response.status + " - " + response.statusText);
		});
	};

	$scope.getUserAccess();

	$scope.cfgGrupoTrabalho = {
		create: false,
		valueField: 'grt_id',
    	searchField: ['grt_id','grt_descricao'],
		delimiter: config.SelDelimiter,
		placeholder: 'Selecione um Grupo de Trabalho',
		maxItems: 1,
		onInitialize: function(selectize){ 
			$scope.loadGrupoTrabalho();	
		},
		render: {
			option: function(item, escape) {
                return '<table class="table" style="margin-bottom: 0px !important;">'
                    +   '<tr>'
                    +    '<td class="text-nowrap left-justify" width="10%"><strong>' + escape(item.grt_id) + '</strong></td>'
                    +    '<td class="text-nowrap left-justify" width="90%">' + (item.grt_descricao ? escape(item.grt_descricao) : '') + '</td> '
                    +   '</tr>'
                    + '</table>';
			},
			item: function(item, escape){
				return '<div>'
					+ '<strong>'
					+ escape(item.grt_id) + ' | '
					+ '</strong>'
					+ (item.grt_descricao ? escape(item.grt_descricao) : '  ')
					+ '</div>';
			}
		},
	};

});	
app.filter('startFrom', function() {
	return function(input, start) {
		start = +start; //parse to int
		return input.slice(start);
	}
});
