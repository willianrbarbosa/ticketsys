<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();
?>  
	<script type="text/javascript">
		$('#novo_file_ticket').on('click', function() {
			$('#novo_ticket_files').trigger('click');
		});
		$('#novo_ticket_files').on('change', function() {
			$('#novo_file_ticket').hide("slow");
			$('#div-novo-selected-files').show("slow");
			$('#novo-selected-files').html($(this)[0].files[0].name);
		});
	</script>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Tickets</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/ticket" class="kt-subheader__breadcrumbs-link bold">&nbsp;Triagem de Ticket</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">

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
								<a href="" ng-click="NewUserFavorite('Triagem de Tickets', 'Tickets', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getAjuda('triagem_ticket')" class="kt-nav__link">
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
					<div class="form-group row" ng-show="aTicket.length > 0">					
						<div class="col-sm-2 col-sm-offset-1 col-xs-3">
							<button type="button" class="btn btn-block btn-sm btn-default" title="Voltar para o primeiro produto" ng-disabled="lCarregandoTicket" ng-click="lCarregandoTicket || setTicketAtual(0)"> &nbsp;<i id="ico-first" class="fa fa-angle-double-left bold"></i>&nbsp; </button>
						</div>
						<div class="col-sm-2 col-xs-2">
							<button type="button" class="btn btn-block btn-sm btn-default" title="Voltar para o produto anterior" ng-disabled="lCarregandoTicket" ng-click="lCarregandoTicket || setTicketAtual((nWorkTicket > 0 ? nWorkTicket - 1 : 0))"> &nbsp;<i id="ico-prev" class="fa fa-angle-left bold"></i>&nbsp; </button>
						</div>
						<div class="col-sm-2 col-xs-2" ng-if="aUserAccess.pta_nivel >= 2">
							<button type="button" class="btn btn-block btn-sm btn-primary" title="Salvar produto" ng-click="triagemForm.$invalid || lCarregandoTicket || triagemTicket(aTicket[nWorkTicket])" ng-disabled="triagemForm.$invalid"> &nbsp;<i id="ico-save-next" class="fa fa-save bold"></i>&nbsp; </button>
						</div>
						<div class="col-sm-2 col-xs-2">
							<button type="button" class="btn btn-block btn-sm btn-default" title="Pular esse produto" ng-disabled="lCarregandoTicket" ng-click="lCarregandoTicket || setTicketAtual((nWorkTicket < (aTicket.length-1) ? nWorkTicket + 1 : (aTicket.length-1)))"> &nbsp;<i id="ico-next" class="fa fa-angle-right bold"></i>&nbsp; </button>
						</div>
						<div class="col-sm-2 col-xs-3">
							<button type="button" class="btn btn-block btn-sm btn-default" title="Ir para o último produto" ng-disabled="lCarregandoTicket" ng-click="lCarregandoTicket || setTicketAtual(aTicket.length-1)"> &nbsp;<i id="ico-last" class="fa fa-angle-double-right bold"></i>&nbsp; </button>
						</div>
					</div>
				</div>
			</div>

			<div class="kt-portlet__body">

				<div class="alert alert-warning" ng-if="!aUserAccess.pta_nivel || aUserAccess.pta_nivel < 1">
					Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.
					<a href="#/ticket" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>
				
				<div class="alert alert-warning" ng-if="!aTicket.length">
					<span id="loading"><div class="loading-img"> Carregando Tickets. Aguarde...</div></span>
				</div>

				<form name="triagemForm" role="form" ng-show="aUserAccess.pta_nivel >= 3 && aTicket.length">
					<div class="form-group row">
				      	<div class="col-sm-12 center-justify">
				      		<h4 class="bold ft-primary">Nº. Ticket: #{{aTicket[nWorkTicket].tkt_id}} ({{aTicket[nWorkTicket].grt_descricao}} > {{aTicket[nWorkTicket].pst_descricao}})</h4>
				      	</div>
					</div>
					<hr/>

					<div class="form-group row" ng-if="aTicket[nWorkTicket].solic_user_id">						
						<div class="col-sm-2 center-justify">
							<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(aTicket[nWorkTicket].solic_user_photo ? aTicket[nWorkTicket].solic_user_photo : 'user_default.png')}}"><br/>
							<label class="ft-primary bold">{{aTicket[nWorkTicket].solic_user_nome}}</label><br/>
							<label class="ft-primary bold">{{aTicket[nWorkTicket].tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
						</div>
						<div class="col-sm-10">
							<h5 class="bold">{{aTicket[nWorkTicket].tkt_titulo}}</h5>
							<pre ng-bind-html="aTicket[nWorkTicket].descricao_ticket"></pre>
						</div>
					</div>
					<hr ng-if="aTicket[nWorkTicket].solic_user_id"/>

					<div class="form-group row" ng-if="!aTicket[nWorkTicket].solic_user_id">						
						<div class="col-sm-2 center-justify">
							<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(aTicket[nWorkTicket].abert_user_photo ? aTicket[nWorkTicket].abert_user_photo : 'user_default.png')}}"><br/>
							<label class="ft-primary bold">{{aTicket[nWorkTicket].abert_user_nome}}</label><br/>
							<label class="ft-primary bold">{{aTicket[nWorkTicket].tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
						</div>
						<div class="col-sm-10">
							<h5 class="bold">{{aTicket[nWorkTicket].tkt_titulo}}</h5>
							<pre ng-bind-html="aTicket[nWorkTicket].descricao_ticket"></pre>
						</div>
					</div>
					<hr ng-if="!aTicket[nWorkTicket].solic_user_id"/>

					<div class="form-group row">
				      	<div class="col-sm-6">
							<div class="form-group row">
								<div class="col-sm-6" ng-class="{ 'has-error': triagemForm.tri_tkt_pst_id.$dirty && triagemForm.tri_tkt_pst_id.$error.required}">
									<label>Pasta de Trabalho</label><span class="txt-obg">*</span>
									<selectize id="tri_tkt_pst_id" name="tri_tkt_pst_id" config="cfgPastaTrabalho" options="aPastaTrabalho" ng-model="aTicket[nWorkTicket].tkt_pst_id" required></selectize>
								</div>
								<div class="col-sm-6" ng-class="{ 'has-error': triagemForm.tri_tkt_ort_id.$dirty && triagemForm.tri_tkt_ort_id.$error.required}">
									<label>Origem</label><span class="txt-obg">*</span>
									<selectize id="tri_tkt_ort_id" name="tri_tkt_ort_id" config="cfgOrigemTicket" options="aOrigemTicket" ng-model="aTicket[nWorkTicket].tkt_ort_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': triagemForm.tri_tkt_tav_id.$dirty && triagemForm.tri_tkt_tav_id.$error.required}">
									<label>Tipo de Atividade</label><span class="txt-obg">*</span>
									<selectize id="tri_tkt_tav_id" name="tri_tkt_tav_id" config="cfgTipoAtividade" options="aTipoAtividade" ng-model="aTicket[nWorkTicket].tkt_tav_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': triagemForm.tri_tkt_prt_id.$dirty && triagemForm.tri_tkt_prt_id.$error.required}">
									<label>Prioridade</label><span class="txt-obg">*</span>
									<selectize id="tri_tkt_prt_id" name="tri_tkt_prt_id" config="cfgPrioridadeTicket" options="aPrioridadeTicket" ng-model="aTicket[nWorkTicket].tkt_prt_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': triagemForm.tri_tkt_cgt_id.$dirty && triagemForm.tri_tkt_cgt_id.$error.required}">
									<label>Categoria</label><span class="txt-obg">*</span>
									<selectize id="tri_tkt_cgt_id" name="tri_tkt_cgt_id" config="cfgCategoriaTicket" options="aCategoriaTicket" ng-model="aTicket[nWorkTicket].tkt_cgt_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<label>Ticket Pai</label>
									<selectize id="tri_tkt_ticket_pai" name="tri_tkt_ticket_pai" config="cfgTicketPai" options="aTicketsPai" ng-model="aTicket[nWorkTicket].tkt_ticket_pai"></selectize>
								</div>
							</div>
						</div>
				      	<div class="col-sm-6 bg-gray">
							<div class="form-group row">
					      		<h4><i class="fa fa-calendar"></i> Prazo Estimado</h4>
								<div class="form-group row">
									<div class="col-sm-12">
										<label>Início</label>
									</div>
									<div class="col-sm-6" >
										<div class="input-group">
										  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
										  	<input type="text" name="tri_tkt_data_ini_estim" id="tri_tkt_data_ini_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="aTicket[nWorkTicket].tkt_data_ini_estim" placeholder="DD/MM/YYY"/>
									 	</div>
									</div>
									<div class="col-sm-6" >
										<div class="input-group">
										  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
										  	<input type="text" name="tri_tkt_hora_ini_estim" id="tri_tkt_hora_ini_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="aTicket[nWorkTicket].tkt_hora_ini_estim" placeholder="HH:mm"/>
									 	</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<label>Término</label>
									</div>
									<div class="col-sm-6" >
										<div class="input-group">
									  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
									  		<input type="text" name="tri_tkt_data_fim_estim" id="tri_tkt_data_fim_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="aTicket[nWorkTicket].tkt_data_fim_estim" placeholder="DD/MM/YYY"/>
									 	</div>
									</div>
									<div class="col-sm-6" >
										<div class="input-group">
									  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
									  		<input type="text" name="tri_tkt_hora_fim_estim" id="tri_tkt_hora_fim_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="aTicket[nWorkTicket].tkt_hora_fim_estim" placeholder="HH:mm"/>
									 	</div>
									</div>
								</div>
							</div>
							<hr/>
				      		<div class="form-group row">
					      		<h4><i class="fa fa-clock"></i> Esforço Estimado</h4>
								<div class="form-group row">
									<div class="col-sm-12" >
										<div class="input-group">
									  		<span class="input-group-addon" style="font-size: 11px;">
									  			<a href="" ng-click="calculaEsforcoEstimado();"><i class="fa fa-lg fa-calculator"></i></a>
									  		</span>
											<input type="text" name="tri_tkt_total_hora_estim" id="tri_tkt_total_hora_estim"  ng-maxlength="12" maxlength="12" class="form-control my-control iptcur" ng-model="aTicket[nWorkTicket].tkt_total_hora_estim" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr/>

					<div class="form-group row">
						<div class="col-sm-4" style="border-right: 1px #000 solid;">
					      	<h4 class="text-danger">
					      		<i class="fa fa-user"></i> Solicitante<span class="txt-obg">*</span>
					      	</h4>
							<div class="form-group row">
								<div class="col-sm-12">
									<selectize id="tri_sol_tku_user_id" name="tri_sol_tku_user_id" config="cfgUsuario" options="aUsuario" ng-model="aTicket[nWorkTicket].aSolicitante.tku_user_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica E-mail?
										<input type="checkbox" value="None" id="tri_sol_tku_notif_email" name="tri_sol_tku_notif_email" ng-model="aTicket[nWorkTicket].aSolicitante.tku_notif_email" />
										<div class="control__indicator"></div>
									</label>
								</div>
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica Sistema?
										<input type="checkbox" value="None" id="tri_sol_tku_notif_sistema" name="tri_sol_tku_notif_sistema" ng-model="aTicket[nWorkTicket].aSolicitante.tku_notif_sistema" />
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
					      	<h4 class="text-warning">
					      		<i class="fa fa-users"></i> Observadores
					      		<button type="button" class="btn btn-xs btn-link" ng-click="TriagemAddObservador();" style="float: right;"><i class="fa fa-plus"></i></button>
					      	</h4>
							<div ng-repeat="observador in aTicket[nWorkTicket].aObservadores">
								<div class="form-group row">
									<div class="col-sm-12">
										<selectize id="{{$index}}tri_obs_tku_user_id" name="{{$index}}tri_obs_tku_user_id" config="cfgUsuario" options="aUsuario" ng-model="observador.tku_user_id"></selectize>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-6">
										<label class="control control--checkbox small">Notifica E-mail?
											<input type="checkbox" value="None" id="{{$index}}tri_obs_tku_notif_email" name="{{$index}}tri_obs_tku_notif_email" ng-model="observador.tku_notif_email" />
											<div class="control__indicator"></div>
										</label>
									</div>
									<div class="col-sm-6">
										<label class="control control--checkbox small">Notifica Sistema?
											<input type="checkbox" value="None" id="{{$index}}tri_obs_tku_notif_sistema" name="{{$index}}tri_obs_tku_notif_sistema" ng-model="observador.tku_notif_sistema" />
											<div class="control__indicator"></div>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-4" style="border-left: 1px #000 solid;">
					      	<h4 class="text-success">
					      		<i class="fa fa-user"></i> Responsável<span class="txt-obg">*</span>
					      	</h4>
							<div class="form-group row">
								<div class="col-sm-12">
									<selectize id="tri_resp_tku_user_id" name="tri_resp_tku_user_id" config="cfgUserResp" options="aUserResp" ng-model="aTicket[nWorkTicket].aResponsavel.tku_user_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica E-mail?
										<input type="checkbox" value="None" id="tri_resp_tku_notif_email" name="tri_resp_tku_notif_email" ng-model="aTicket[nWorkTicket].aResponsavel.tku_notif_email" />
										<div class="control__indicator"></div>
									</label>
								</div>
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica Sistema?
										<input type="checkbox" value="None" id="tri_resp_tku_notif_sistema" name="tri_resp_tku_notif_sistema" ng-model="aTicket[nWorkTicket].aResponsavel.tku_notif_sistema" />
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
						</div>
					</div>
					<hr/>
					
					<div ng-repeat="arquivo in aTicket[nWorkTicket].aArquivos">
						<div class="row form-group">
							<div class="col-sm-2 center-justify">
								<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(arquivo.user_photo ? arquivo.user_photo : 'user_default.png')}}"><br/>
								<label class="ft-primary bold">{{arquivo.user_nome}}</label><br/>
								<label class="ft-primary bold">{{arquivo.tka_data_hora_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
							</div>
							<div class="col-sm-4" ng-if="arquivo.tka_arquivo_tipo == 'png' || arquivo.tka_arquivo_tipo == 'jpg' || arquivo.tka_arquivo_tipo == 'jpeg' || arquivo.tka_arquivo_tipo == 'gif' || arquivo.tka_arquivo_tipo == 'bmp'">
								<a href="{{arquivo.tka_arquivo_local + arquivo.tka_arquivo_nome}}" data-toggle="lightbox">
									<img class="img-usuario" src="{{arquivo.tka_arquivo_local + arquivo.tka_arquivo_nome}}" >
								</a>
							</div>
							<div class="col-sm-6" ng-if="arquivo.tka_arquivo_tipo == 'png' || arquivo.tka_arquivo_tipo == 'jpg' || arquivo.tka_arquivo_tipo == 'jpeg' || arquivo.tka_arquivo_tipo == 'gif' || arquivo.tka_arquivo_tipo == 'bmp'">
								<label class="bold">{{arquivo.tka_arquivo_nome}}</label><br/>
								<label class="bold">({{arquivo.tka_arquivo_tipo}})</label><br/>
							</div>
							<div class="col-sm-10" ng-if="arquivo.tka_arquivo_tipo != 'png' && arquivo.tka_arquivo_tipo != 'jpg' && arquivo.tka_arquivo_tipo != 'jpeg' && arquivo.tka_arquivo_tipo != 'gif' && arquivo.tka_arquivo_tipo != 'bmp'">
								<a href="{{arquivo.tka_arquivo_local + arquivo.tka_arquivo_nome}}" target="_Blank" download>
									<i class="fa fa-3x"
										ng-class="{
											'fa-file-excel-o ft-success': arquivo.tka_arquivo_tipo == 'xls' || arquivo.tka_arquivo_tipo == 'xlsx',
											'fa-file-word-o ft-primary': arquivo.tka_arquivo_tipo == 'doc' || arquivo.tka_arquivo_tipo == 'docx',
											'fa-file-text-o ft-gray-dk': arquivo.tka_arquivo_tipo == 'txt',
											'fa-code ft-purple': arquivo.tka_arquivo_tipo == 'xml',
											'fa-file-pdf-o ft-danger': arquivo.tka_arquivo_tipo == 'pdf',
											'fa-file-archive-o ft-danger': arquivo.tka_arquivo_tipo == 'zip' || arquivo.tka_arquivo_tipo == 'rar',
											'fa-file-video-o ft-info': arquivo.tka_arquivo_tipo == 'mp4' || arquivo.tka_arquivo_tipo == 'avi'
										}"></i>
								</a><br/>
								<label class="bold">{{arquivo.tka_arquivo_nome}}</label><br/>
								<label class="bold">({{arquivo.tka_arquivo_tipo}})</label><br/>
							</div>
						</div>
						<hr/>
					</div>
              	</form>

          	</div>
       	</div>
    </div>
</div>
