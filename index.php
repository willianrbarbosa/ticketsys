<?php
	ob_start();
	session_start();

	ini_set('default_charset','UTF-8');
	include('assets/model/class/security.class.php');
	$security = new Security();
	define( 'WP_MAX_MEMORY_LIMIT' , '4096M' );
?>  
<html ng-app="ticket_sys" ng-init="tab=1">
<head>
	<meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?php echo $security->base_patch; ?>/assets/img/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $security->base_patch; ?>/assets/img/favicon.png" type="image/x-icon"/>
	<title>Ticket SYS</title>

    <meta name="author" content="Willian Barbosa">
	<meta name="description" content="Sistema de Tickets e controle de hroas para equipe técnica."> 
    
    <?php 	include('includes.html');	?>

</head>
<body app="ticket_sys" ng-cloak ng-controller="TicketSysCtrl" class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-aside--minimize">

	<?php 	include('assets/view/header.html');   ?>
	<div growl></div>
	
	<form action="<?php echo $security->base_patch; ?>/assets/model/exportar_dados.php" method="post" target="_blank" id="formExportaDados">
		<input type="hidden" id="nome_tabela" name="nome_tabela" />
		<input type="hidden" id="tabela_html" name="tabela_html" />
		<input type="hidden" id="exporta_tipo" name="exporta_tipo" />
	</form>

	<div id="mdNewFavorite" tabindex='-1' class="modal fade" style="overflow: auto;">				
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">				
			        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
					<h5 class="modal-title"><strong><i class="fa fa-md fa-star ft-yellow"></i> Novo Favorito</strong></h5>
				</div>

				<div class="modal-body" style="text-align: justify;">
					<form name="formNewFavorite" role="form">
						<div class="row form-group">
					        <div class="col-sm-6">
						        <div ng-class="{ 'has-error': formNewFavorite.ufv_descricao.$dirty && (formNewFavorite.ufv_descricao.$error.required || formNewFavorite.cli_nome_fantasia.$error.maxlength) }" >
						        	<label>Descrição</label><span class="txt-obg">*</span>
					        		<input type="text" name="ufv_descricao" id="ufv_descricao" ng-maxlength="40" maxlength="40" class="form-control my-control" placeholder="Nome de exibição" ng-model="nUserFavorite.ufv_descricao" required />
								</div>       
					        </div>
					        <div class="col-sm-6">
						        <div ng-class="{ 'has-error': formNewFavorite.ufv_categoria.$dirty && (formNewFavorite.ufv_categoria.$error.required || formNewFavorite.cli_nome_fantasia.$error.maxlength) }" >
						        	<label>Categoria</label><span class="txt-obg">*</span>
					        		<input type="text" name="ufv_categoria" id="ufv_categoria" ng-maxlength="40" maxlength="40" class="form-control my-control" ng-model="nUserFavorite.ufv_categoria" required disabled readonly/>
								</div>
					        </div>
					    </div>
						<div class="row form-group">
					        <div class="col-sm-12">
						        <div ng-class="{ 'has-error': formNewFavorite.ufv_url.$dirty && (formNewFavorite.ufv_url.$error.required || formNewFavorite.cli_nome_fantasia.$error.maxlength) }" >
						        	<label>URL</label><span class="txt-obg">*</span>
					        		<input type="text" name="ufv_url" id="ufv_url" ng-maxlength="120" maxlength="120" class="form-control my-control" ng-model="nUserFavorite.ufv_url" required disabled readonly/>
								</div>
					        </div>
					    </div>
					</form>
				</div>

				<div class="modal-footer">
					<div class="row">
				      	<div class="col-sm-12 right-justify">
				      		<div class="col-sm-4 col-sm-offset-6" style="margin-right: -20px;">
								<a href="" class="btn-block btn btn-xs btn-primary" ng-click="formNewFavorite.$invalid || saveUserFavorite(nUserFavorite)" ng-disabled="formNewFavorite.$invalid">Salvar favorito</a>
							</div>		
							<div class="col-sm-2">
								<button type="button" class="btn-block btn btn-xs btn-link" data-dismiss="modal"><span class="ft-black">ou</span> Fechar</button>
							</div>
				      	</div>
				    </div>
				</div>

			</div>
		</div>
	</div>

	<div id="mdNaoLogado" tabindex='-1' class="modal fade" style="overflow: auto;">				
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">

				<div class="modal-header bg-danger-dark">
					<br/>
					<div class="row form-group">
						<div class="col-sm-12 center-justify ft-white">
							<h2 class="modal-title"><strong> :( Sua sessão expirou!</strong></h2>
							<h5>Faça login novamente para continuar usando o sistema.</h5>
						</div>
					</div>
				</div>

				<div class="modal-body bg-danger2">
					<div class="row form-group">
						<div class="col-sm-12 ft-white">
							<h4>
								Sua sessão expirou!!!<br/><br/>
								Pode ter sido por muito tempo de inativatidade ou por ter feito login com seu usuário em outro computador/navegador.<br/><br/>								
							</h4>
						</div>
					</div>
					<br/><br/>

					<div class="row form-group">
						<div class="col-sm-4 col-sm-offset-4">
							<a href="assets/model/logout.php" class="btn btn-lg btn-block btn-danger bg-danger-dark wow fadeInDown animated"><i class="fa fa-sign-in"></i> Acessar novamente</a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div id="mdNewTicket" tabindex='-1' class="modal fade" style="overflow: auto;">				
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">

				<div class="modal-header">				
			        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
					<h5 class="modal-title"><strong><i class="fa fa-md fa-address-card fa-plus text-warning"></i> Abrir novo Ticket</strong></h5>
				</div>

				<div class="modal-body" style="text-align: justify;">
					<form name="formNewTicket" role="form">
						<div class="row form-group">
					        <div class="col-sm-12">
						        <div ng-class="{ 'has-error': formNewTicket.tkt_titulo.$dirty && (formNewTicket.tkt_titulo.$error.required || formNewTicket.tkt_titulo.$error.maxlength) }" >
						        	<label>Título</label><span class="txt-obg">*</span>
					        		<input type="text" name="tkt_titulo" id="tkt_titulo" ng-maxlength="120" maxlength="120" class="form-control my-control" placeholder="Informe o Título" ng-model="newUserTicket.tkt_titulo" required />
								</div>       
					        </div>
					    </div>
						<div class="form-group row">
							<div class="col-sm-12" ng-class="{ 'has-error': formNewTicket.tkt_descricao.$dirty && newForm.tkt_descricao.$error.required }">
								<label>Descrição</label><span class="txt-obg">*</span>
								<ng-quill-editor ng-model="newUserTicket.tkt_descricao">
									<ng-quill-toolbar class="ql-container-60">
									<div>
									<span class="ql-formats">
										<select class="ql-size">
											<option value="small"></option>
											<option selected></option>
											<option value="large"></option>
											<option value="huge"></option>
										</select>
									</span>
									<span class="ql-formats">
										<button class="ql-bold"></button>
										<button class="ql-italic"></button>
										<button class="ql-underline"></button>
										<button class="ql-strike"></button>
									</span>
									<span class="ql-formats">
										<select class="ql-color"></select>
										<select class="ql-background"></select>
									</span>
									<span class="ql-formats">
										<button class="ql-list" value="ordered"></button>
										<button class="ql-list" value="bullet"></button>
										<select class="ql-align">
											<option selected></option>
											<option value="center"></option>
											<option value="right"></option>
											<option value="justify"></option>
										</select>
									</span>
										<span class="ql-formats">
										    <button class="ql-blockquote"></button>
										    <button class="ql-code-block"></button>
											<button class="ql-link"></button>
											<button class="ql-image"></button>
										</span>
									</div>
									</ng-quill-toolbar>
								</ng-quill-editor>
								<!-- <div text-angular ng-model="newUserTicket.tkt_descricao" required></div> -->
							</div>
						</div>

						<div class="row form-group">
							<div class="col-sm-12">
								<div class="row form-group bg-gray">
									<div class="col-sm-10 col-sm-offset-1 center-justify">
										<div id="file_ticket">
											<span class="bigger-150 bolder">Anexar Arquivo ao Ticket</span><br>
											<span class="smaller-100 text-grey">(clique no ícone para selecionar o arquivo)</span> <br> 				
											<a href=""><i class="upload-icon ace-icon fa fa-cloud-upload text-blue fa-3x"></i></a><br/>
										</div>
										<div id="div-selected-files"style="display: none;">
											<h5 id="selected-files" class="ft-primary bold"></h5>
										</div>
									</div>
						            <input type="file" name="ticket_files" id="ticket_files" class="form-control my-control" file-model="newUserTicket.ticketFile" style="display: none;"/>
						        </div>
								<div class="row">
									<div id="div-upload-img" class="alert alert-warning" style="display: none">
										<div id="uploading-img" ></div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<div class="modal-footer">
					<div class="row">
				      	<div class="col-sm-12 right-justify">
				      		<div class="col-sm-4 col-sm-offset-4" style="margin-right: -20px;">
								<a href="" class="btn-block btn btn-xs btn-default" ng-click="LimpaTicket()">Limpar</a>
							</div>
				      		<div class="col-sm-4" style="margin-right: -20px;">
								<a href="" class="btn-block btn btn-xs btn-primary" ng-click="formNewTicket.$invalid || novoTicket(newUserTicket)" ng-disabled="formNewTicket.$invalid">Abrir Ticket</a>
							</div>
				      	</div>
				    </div>
				</div>

			</div>
		</div>
	</div>

</body>
</html>