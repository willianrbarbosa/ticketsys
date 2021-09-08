<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();
?>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Cadastros</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/ticket" class="kt-subheader__breadcrumbs-link bold">&nbsp;Ticket</a>
			</div>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<span class="kt-subheader__breadcrumbs">&nbsp;Editar</span>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="newForm.$invalid || novoTicket(nTicket, true)" ng-disabled="newForm.$invalid">
						Salvar
					</a>
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-success btn-salvar" ng-click="newForm.$invalid || novoTicket(nTicket, false)" ng-disabled="newForm.$invalid">
						Salvar e Cad. Novo
					</a>

					<a href="" class="btn btn-sm  btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações Relacionadas">
						<i class="fa fa-ellipsis-h"></i>
					</a>

					<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
						<!--begin::Nav-->
						<ul class="kt-nav">
							<li class="kt-nav__head"><strong>Ações relacionadas:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="#/ticket" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Novo Ticket', 'Cadastros', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getAjuda('ticket')" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
						</ul>
						<!--end::Nav-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="row">
		<div class="col-xs-12 kt-portlet" >	

			<div class="row kt-portlet__head kt-portlet__head--sm" style="padding-top: 15px;" ng-if="aUserAccess.pta_nivel >= 3">
				<div class="col-lg-12">				      	
					<button href="" class="btn btn-md btn-default" title="Anexar Arquivos">
						<i class="fa fa-paperclip"></i>
					</button>				
					<button href="" class="btn btn-md btn-default" title="Apontamento de horas">
						<i class="fa fa-clock"></i>
					</button>	
					<button href="" class="btn btn-md btn-default" title="Associar Pessoas">
						<i class="fa fa-users"></i>
					</button>
					<button href="" class="btn btn-md btn-default" title="Inserir comentário">
						<i class="fa fa-comments-o"></i>
					</button>
				</div>
			</div>

			<div class="kt-portlet__body">

				<div class="alert alert-warning" ng-if="!aUserAccess.pta_nivel || aUserAccess.pta_nivel < 1">
					Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.
					<a href="#/ticket" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>

				<form name="newForm" role="form" ng-show="aUserAccess.pta_nivel >= 1">
					<div class="form-group row">
				      	<div class="col-sm-2">
				        	<label>Nº. Ticket</label>
				          	<input type="text" name="nov_tkt_id" id="nov_tkt_id" ng-maxlength="11" class="form-control my-control" ng-model="nTicket.tkt_id" readonly />
				       	</div>
						<div class="col-sm-3" ng-class="{ 'has-error': (newForm.nov_tkt_abertura_data.$dirty && newForm.nov_tkt_abertura_data.$error.required) || newForm.nov_tkt_abertura_data.$error.maxlength }">
							<label>Data de Abertura</label><span class="txt-obg">*</span>
							<div class="input-group">
							  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
							  	<input type="text" name="nov_tkt_abertura_data" id="nov_tkt_abertura_data"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="nTicket.tkt_abertura_data" placeholder="DD/MM/YYY"  required/>
						 	</div>
						</div>
						<div class="col-sm-3" ng-class="{ 'has-error': newForm.nov_tkt_pst_id.$dirty && newForm.nov_tkt_pst_id.$error.required}">
							<label>Pasta de Trabalho</label><span class="txt-obg">*</span>
							<selectize id="nov_tkt_pst_id" name="nov_tkt_pst_id" config="cfgPastaTrabalho" options="aPastaTrabalho" ng-model="nTicket.tkt_pst_id" required></selectize>
						</div>
						<div class="col-sm-4" ng-class="{ 'has-error': newForm.nov_tkt_solicitante_user_id.$dirty && newForm.nov_tkt_solicitante_user_id.$error.required}">
							<label>Solicitante</label><span class="txt-obg">*</span>
							<selectize id="nov_tkt_solicitante_user_id" name="nov_tkt_solicitante_user_id" config="cfgUsuario" options="aUsuario" ng-model="nTicket.tkt_solicitante_user_id" required></selectize>
						</div>
					</div>
					<hr/>
					<div class="form-group row">
				      	<div class="col-sm-6">
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': newForm.nov_tkt_ort_id.$dirty && newForm.nov_tkt_ort_id.$error.required}">
									<label>Origem</label><span class="txt-obg">*</span>
									<selectize id="nov_tkt_ort_id" name="nov_tkt_ort_id" config="cfgOrigemTicket" options="aOrigemTicket" ng-model="nTicket.tkt_ort_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': newForm.nov_tkt_tav_id.$dirty && newForm.nov_tkt_tav_id.$error.required}">
									<label>Tipo de Atividade</label><span class="txt-obg">*</span>
									<selectize id="nov_tkt_tav_id" name="nov_tkt_tav_id" config="cfgTipoAtividade" options="aTipoAtividade" ng-model="nTicket.tkt_tav_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': newForm.nov_tkt_prt_id.$dirty && newForm.nov_tkt_prt_id.$error.required}">
									<label>Prioridade</label><span class="txt-obg">*</span>
									<selectize id="nov_tkt_prt_id" name="nov_tkt_prt_id" config="cfgPrioridadeTicket" options="aPrioridadeTicket" ng-model="nTicket.tkt_prt_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': newForm.nov_tkt_cgt_id.$dirty && newForm.nov_tkt_cgt_id.$error.required}">
									<label>Categoria</label><span class="txt-obg">*</span>
									<selectize id="nov_tkt_cgt_id" name="nov_tkt_cgt_id" config="cfgCategoriaTicket" options="aCategoriaTicket" ng-model="nTicket.tkt_cgt_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': newForm.nov_tkt_ticket_pai.$error.maxlength }">
									<label>Ticket Pai</label>
									<input type="text" name="nov_tkt_ticket_pai" id="nov_tkt_ticket_pai"  ng-maxlength="11" maxlength="11" class="form-control my-control iptint" ng-model="nTicket.tkt_ticket_pai" />
								</div>
							</div>
						</div>
				      	<div class="col-sm-6 bg-gray">
							<div class="form-group row">
					      		<h4><i class="fa fa-calendar"></i> Prazo Estimado</h4>
								<div class="form-group row">
									<div class="col-sm-12">
										<label>Início</label><span class="txt-obg">*</span>
									</div>
									<div class="col-sm-6" ng-class="{ 'has-error': (newForm.nov_tkt_data_ini_estim.$dirty && newForm.nov_tkt_data_ini_estim.$error.required) || newForm.nov_tkt_data_ini_estim.$error.maxlength }">
										<div class="input-group">
										  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
										  	<input type="text" name="nov_tkt_data_ini_estim" id="nov_tkt_data_ini_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="nTicket.tkt_data_ini_estim" placeholder="DD/MM/YYY" required/>
									 	</div>
									</div>
									<div class="col-sm-6" ng-class="{ 'has-error': (newForm.nov_tkt_hora_ini_estim.$dirty && newForm.nov_tkt_hora_ini_estim.$error.required) || newForm.nov_tkt_hora_ini_estim.$error.maxlength }">
										<div class="input-group">
										  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
										  	<input type="text" name="nov_tkt_hora_ini_estim" id="nov_tkt_hora_ini_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="nTicket.tkt_hora_ini_estim" placeholder="HH:mm" required/>
									 	</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<label>Término</label><span class="txt-obg">*</span>
									</div>
									<div class="col-sm-6" ng-class="{ 'has-error': (newForm.nov_tkt_data_fim_estim.$dirty && newForm.nov_tkt_data_fim_estim.$error.required) || newForm.nov_tkt_data_fim_estim.$error.maxlength }">
										<div class="input-group">
									  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
									  		<input type="text" name="nov_tkt_data_fim_estim" id="nov_tkt_data_fim_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="nTicket.tkt_data_fim_estim" placeholder="DD/MM/YYY" required/>
									 	</div>
									</div>
									<div class="col-sm-6" ng-class="{ 'has-error': (newForm.nov_tkt_hora_fim_estim.$dirty && newForm.nov_tkt_hora_fim_estim.$error.required) || newForm.nov_tkt_hora_fim_estim.$error.maxlength }">
										<div class="input-group">
									  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
									  		<input type="text" name="nov_tkt_hora_fim_estim" id="nov_tkt_hora_fim_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="nTicket.tkt_hora_fim_estim" placeholder="HH:mm" required/>
									 	</div>
									</div>
								</div>
							</div>
							<hr/>
				      		<div class="form-group row">
					      		<h4><i class="fa fa-clock"></i> Esforço Estimado</h4>
								<div class="form-group row">
									<div class="col-sm-12" ng-class="{ 'has-error': newForm.nov_tkt_total_hora_estim.$error.maxlength }">
										<div class="input-group">
									  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calculator"></i></span>
											<input type="text" name="nov_tkt_total_hora_estim" id="nov_tkt_total_hora_estim"  ng-maxlength="12" maxlength="12" class="form-control my-control iptcur" ng-model="nTicket.tkt_total_hora_estim" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group row">
						<div class="col-sm-12" ng-class="{ 'has-error': (newForm.nov_tkt_titulo.$dirty && newForm.nov_tkt_titulo.$error.required) || newForm.nov_tkt_titulo.$error.maxlength }">
							<label>Título</label><span class="txt-obg">*</span>
							<input type="text" name="nov_tkt_titulo" id="nov_tkt_titulo"  ng-maxlength="120" maxlength="120" class="form-control my-control" ng-model="nTicket.tkt_titulo"  required/>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-12" ng-class="{ 'has-error': (newForm.nov_tkt_titulo.$dirty && newForm.nov_tkt_titulo.$error.required) || newForm.nov_tkt_descricao.$error.maxlength }">
							<label>Descrição</label><span class="txt-obg">*</span>
							<ng-quill-editor ng-model="nTicket.tkt_descricao">
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
							<!-- <div text-angular ng-model="nTicket.tkt_descricao" required></div> -->
						</div>
					</div>
              	</form>

          	</div>
       	</div>
    </div>
</div>
