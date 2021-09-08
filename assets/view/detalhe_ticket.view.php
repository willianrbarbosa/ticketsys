<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();
?>       
<script type="text/javascript">
	$('#det_file_ticket').on('click', function() {
		$('#det_ticket_files').trigger('click');
	});
		$('#det_ticket_files').on('change', function() {
		$('#det_file_ticket').hide("slow");
		$('#det_div-selected-files').show("slow");
		$('#det_selected-files').html($(this)[0].files[0].name);
	});
</script>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid " style="background-color: {{(eTicket.tkt_encerrado == 'N' ? eTicket.prt_cor : '#FFF')}}; !important;">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Tickets</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/detalheticket/{{eTicket.tkt_id}}" class="kt-subheader__breadcrumbs-link bold" ng-class="{'ft-white': eTicket.tkt_encerrado == 'N'}">&nbsp;Nº. Ticket: #{{eTicket.tkt_id}} ({{eTicket.grt_descricao}} > {{eTicket.pst_descricao}})</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">

					<a href="" class="btn btn-xs btn-default btn-salvar bold" ng-click="edtForm.$invalid || editaTicket(eTicket, false)" ng-disabled="edtForm.$invalid" ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields">
						Salvar
					</a>
					<a href="" class="btn btn-sm btn-default" data-toggle="modal" data-target="#mdArquivo" title="Anexar Arquivos" ng-if="aUserAccess.pta_nivel >= 1 && !lDisableFields">
						<i class="fa fa-paperclip"></i>
					</a>
					<a href="" class="btn btn-sm btn-default" data-toggle="modal" data-target="#mdApontamento" title="Apontamento de horas" ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields">
						<i class="fa fa-clock"></i>
					</a>
					<a href="" class="btn btn-sm btn-default" title="Iniciar execução de atividade" 
						ng-click="novoEditaApontamentoTicket(nTicketApontamento, false);" 
						ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields && !isUsuarioApontamentoExecucao(eTicket.apontamento_pendente, aUserData.user_id)">
						<i class="fa fa-play"></i>
					</a>
					<a href="" class="btn btn-sm btn-default" title="Pausar execução de atividade" 
						ng-click="encerraEditaApontamentoTicket(eTicket.apontamento_pendente, aUserData.user_id);" 
						ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields && isUsuarioApontamentoExecucao(eTicket.apontamento_pendente, aUserData.user_id)">
						<i class="fa fa-pause"></i>
					</a>
					<a href="" class="btn btn-sm btn-default" data-toggle="modal" data-target="#mdComentario" title="Inserir comentário" ng-if="aUserAccess.pta_nivel >= 1 && !lDisableFields">
						<i class="fa fa-comments-o"></i>
					</a>

					<a href="" class="btn btn-sm  btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações Relacionadas">
						<i class="fa fa-ellipsis-h"></i>
					</a>

					<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
						<!--begin::Nav-->
						<ul class="kt-nav">
							<li class="kt-nav__head"><strong>Ações relacionadas:</strong></li>
							<li class="kt-nav__separator"></li>

							<li class="kt-nav__item" ng-if="aUserAccess.pta_nivel < 3">
								<a href="" data-toggle="modal" data-target="#mdNewTicket" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-plus ft-primary"></i> <span class="kt-nav__link-text">Abrir novo Ticket</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="aUserAccess.pta_nivel < 3">
								<a href="#/meustickets" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>

							<li class="kt-nav__item" ng-if="aUserAccess.pta_nivel >= 3">
								<a href="#/novoticket" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-plus ft-primary"></i> <span class="kt-nav__link-text">Abrir novo Ticket</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="aUserAccess.pta_nivel >= 3">
								<a href="#/meustrabalhos" class="kt-nav__link">
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

			<div class="kt-portlet__body">

				<div class="alert alert-warning" ng-if="!aUserAccess.pta_nivel">
					Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.
					<a href="#/ticket" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>
				
				<div class="alert alert-warning" ng-if="!eTicket">
					<span id="loading"><div class="loading-img"> Carregando Ticket. Aguarde...</div></span>
				</div>


				<div ng-if="aUserAccess.pta_nivel >= 1 && !lDisableFields && eTicket.apontamento_pendente.length">
					<div class="form-group row">
						<div class="col-sm-6" ng-repeat="apmtoPendente in eTicket.apontamento_pendente">
							<div class="form-group row">
								<div class="col-sm-2 center-justify">
									<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(apmtoPendente.user_photo ? apmtoPendente.user_photo : 'user_default.png')}}"><br/>
								</div>
								<div class="col-sm-10">
									<label class="bold">{{apmtoPendente.user_nome}}</label><br/>
									<span class="text-primary"><i class="fa fa-play ft-gray-dk"></i> {{apmtoPendente.tkt_titulo}}</span><br/>
									<span class="ft-gray-dk">{{apmtoPendente.tempo_execucao}}</span>
								</div>
							</div>
						</div>
					</div>
					<hr/>
				</div>

				<form name="edtForm" role="form" ng-show="aUserAccess.pta_nivel && eTicket">

					<div class="form-group row">
				      	<div class="col-sm-6" ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields">
							<div class="form-group row">
								<div class="col-sm-6" ng-class="{ 'has-error': edtForm.edt_tkt_pst_id.$dirty && edtForm.edt_tkt_pst_id.$error.required}">
									<label>Pasta de Trabalho</label><span class="txt-obg">*</span>
									<selectize id="edt_tkt_pst_id" name="edt_tkt_pst_id" config="cfgPastaTrabalho" options="aPastaTrabalho" ng-model="eTicket.tkt_pst_id" required></selectize>
								</div>
								<div class="col-sm-6" ng-class="{ 'has-error': edtForm.edt_tkt_ort_id.$dirty && edtForm.edt_tkt_ort_id.$error.required}">
									<label>Origem</label><span class="txt-obg">*</span>
									<selectize id="edt_tkt_ort_id" name="edt_tkt_ort_id" config="cfgOrigemTicket" options="aOrigemTicket" ng-model="eTicket.tkt_ort_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': edtForm.edt_tkt_tav_id.$dirty && edtForm.edt_tkt_tav_id.$error.required}">
									<label>Tipo de Atividade</label><span class="txt-obg">*</span>
									<selectize id="edt_tkt_tav_id" name="edt_tkt_tav_id" config="cfgTipoAtividade" options="aTipoAtividade" ng-model="eTicket.tkt_tav_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': edtForm.edt_tkt_prt_id.$dirty && edtForm.edt_tkt_prt_id.$error.required}">
									<label>Prioridade</label><span class="txt-obg">*</span>
									<selectize id="edt_tkt_prt_id" name="edt_tkt_prt_id" config="cfgPrioridadeTicket" options="aPrioridadeTicket" ng-model="eTicket.tkt_prt_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12" ng-class="{ 'has-error': edtForm.edt_tkt_cgt_id.$dirty && edtForm.edt_tkt_cgt_id.$error.required}">
									<label>Categoria</label><span class="txt-obg">*</span>
									<selectize id="edt_tkt_cgt_id" name="edt_tkt_cgt_id" config="cfgCategoriaTicket" options="aCategoriaTicket" ng-model="eTicket.tkt_cgt_id" required></selectize>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<label>Ticket Pai</label>
									<selectize id="edt_tkt_ticket_pai" name="edt_tkt_ticket_pai" config="cfgTicketPai" options="aTicketsPai" ng-model="eTicket.tkt_ticket_pai"></selectize>
								</div>
							</div>
						</div>
				      	<div class="col-sm-6" ng-if="aUserAccess.pta_nivel < 3 || lDisableFields">
							<div class="form-group row">
								<div class="col-sm-6">
									<h5 class="bold">Pasta de Trabalho</h5><br/>
									<label class="nobold">{{eTicket.pst_descricao}}</label>
								</div>
								<div class="col-sm-6">
									<h5 class="bold">Origem</h5><br/>
									<label class="nobold">{{eTicket.ort_descricao}}</label>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<h5 class="bold">Tipo de Atividade</h5><br/>
									<label class="nobold">{{eTicket.tav_descricao}}</label>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<h5 class="bold">Prioridade</h5><br/>
									<label class="nobold"><i class="fa fa-2x fa-square" style="color: {{eTicket.prt_cor}}"></i> {{eTicket.prt_descricao}}</label>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<h5 class="bold">Categoria</h5><br/>
									<label class="nobold">{{eTicket.cgt_descricao}}</label>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<h5 class="bold">Ticket Pai</h5><br/>
									<label class="nobold">{{eTicket.tkt_ticket_pai}}</label>
								</div>
							</div>
				      	</div>

				      	<div class="col-sm-6">
							<div class="form-group row bg-gray">
								<div class="col-sm-12">
					      			<h4><i class="fa fa-calendar"></i> Prazo</h4>
									<div class="form-group row">
										<div class="col-sm-6" >
											<label>Início Estimado</label>
											<div class="input-group">
											  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
											  	<input type="text" name="edt_tkt_data_ini_estim" id="edt_tkt_data_ini_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="eTicket.tkt_data_ini_estim" placeholder="DD/MM/YYY" ng-disabled="lDisableFields"/>
										 	</div>
											<div class="input-group">
											  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
											  	<input type="text" name="edt_tkt_hora_ini_estim" id="edt_tkt_hora_ini_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="eTicket.tkt_hora_ini_estim" placeholder="HH:mm" ng-disabled="lDisableFields"/>
										 	</div>
										</div>
										<div class="col-sm-6" >
											<label>Término Estimado</label>
											<div class="input-group">
										  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
										  		<input type="text" name="edt_tkt_data_fim_estim" id="edt_tkt_data_fim_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="eTicket.tkt_data_fim_estim" placeholder="DD/MM/YYY" ng-disabled="lDisableFields"/>
										 	</div>
											<div class="input-group">
										  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
										  		<input type="text" name="edt_tkt_hora_fim_estim" id="edt_tkt_hora_fim_estim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="eTicket.tkt_hora_fim_estim" placeholder="HH:mm" ng-disabled="lDisableFields"/>
										 	</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6" >
											<label>Início Real</label>
											<div class="input-group">
											  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
											  	<input type="text" name="edt_tkt_data_ini_real" id="edt_tkt_data_ini_real"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="eTicket.tkt_data_ini_real" placeholder="DD/MM/YYY" readonly disabled  ng-disabled="lDisableFields"/>
										 	</div>
											<div class="input-group">
											  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
											  	<input type="text" name="edt_tkt_hora_ini_real" id="edt_tkt_hora_ini_real"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="eTicket.tkt_hora_ini_real" placeholder="HH:mm" readonly disabled  ng-disabled="lDisableFields"/>
										 	</div>
										</div>
										<div class="col-sm-6" >
											<label>Término Real</label>
											<div class="input-group">
										  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
										  		<input type="text" name="edt_tkt_data_fim_real" id="edt_tkt_data_fim_real"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="eTicket.tkt_data_fim_real" placeholder="DD/MM/YYY" readonly disabled  ng-disabled="lDisableFields"/>
										 	</div>
											<div class="input-group">
										  		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
										  		<input type="text" name="edt_tkt_hora_fim_real" id="edt_tkt_hora_fim_real"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="eTicket.tkt_hora_fim_real" placeholder="HH:mm" readonly disabled  ng-disabled="lDisableFields"/>
										 	</div>
										</div>
									</div>
									<div class="form-group row" ng-if="eTicket.dias_atraso != 0 && eTicket.tkt_encerrado == 'N'">
										<div class="col-sm-12">
											<h5 class="bold" ng-if="eTicket.dias_atraso > 0"><span class="text-danger">{{eTicket.dias_atraso}}</span> dias de atraso.</h5>
											<h5 class="bold" ng-if="eTicket.dias_atraso < 0"><span class="text-success">{{eTicket.dias_atraso}}</span> dias antecipado.</h5>
										</div>
									</div>
									<div class="form-group row" ng-if="eTicket.dias_desvio_prazo != 0 && eTicket.tkt_encerrado == 'S'">
										<div class="col-sm-12">
											<h5 class="bold" ng-if="eTicket.dias_desvio_prazo > 0"><span class="text-danger">{{eTicket.dias_desvio_prazo}}</span> dias de desvio de prazo.</h5>
											<h5 class="bold" ng-if="eTicket.dias_desvio_prazo < 0"><span class="text-success">{{eTicket.dias_desvio_prazo}}</span> dias de desvio de prazo.</h5>
										</div>
									</div>
								</div>
							</div>

				      		<div class="form-group row bg-gray">
								<div class="col-sm-12">
						      		<h4><i class="fa fa-clock"></i> Esforço em horas</h4>
									<div class="row">
										<div class="col-sm-6" >
											<label>Estimado</label>
											<div class="input-group">
										  		<span class="input-group-addon" style="font-size: 11px;">
										  			<a href="" ng-click="calculaEsforcoEstimado();"><i class="fa fa-lg fa-calculator"></i></a>
										  		</span>
												<input type="text" name="edt_tkt_total_hora_estim" id="edt_tkt_total_hora_estim"  ng-maxlength="12" maxlength="12" class="form-control my-control iptcur" ng-model="eTicket.tkt_total_hora_estim"  ng-disabled="lDisableFields"/>
											</div>
										</div>
										<div class="col-sm-6" >
											<label class="bold">Real</label><br/>
										  	<input type="text" name="edt_tkt_total_hora_real" id="edt_tkt_total_hora_real"  ng-maxlength="12" maxlength="12" class="form-control my-control iptcur" ng-model="eTicket.tkt_total_hora_real" disabled readonly  ng-disabled="lDisableFields"/>
										</div>
									</div>
								</div>

								<div class="col-sm-12">
									<div class="row">
										<div class="col-sm-12" >
								      		<h4 class="bold">EXR: {{eTicket.tkt_per_concluido | number: 2}} %</h4>
											<div class="progress progress-striped" style="height: 10px;">
												<div class="progress-bar progress-bar-success animate slideInLeft wow" role="progressbar" style="width: {{eTicket.tkt_per_concluido}}%" 
													aria-valuemin="0" 
													aria-valuenow="{{eTicket.tkt_per_concluido | number: 2}}" 
													aria-valuemax="100"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr/>

					<div class="form-group row">
						<div class="col-sm-4" style="border-right: 1px #000 solid;">
					      	<h4 class="text-danger bold">
					      		<i class="fa fa-user"></i> Solicitante<span class="txt-obg">*</span>
					      	</h4>
							<div class="form-group row" ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields">
								<div class="col-sm-12">
									<selectize id="edt_sol_tku_user_id" name="edt_sol_tku_user_id" config="cfgUsuario" options="aUsuario" ng-model="eTicket.aSolicitante.tku_user_id" required></selectize>
								</div>
							</div>
							<div class="form-group row" ng-if="aUserAccess.pta_nivel < 3 || lDisableFields">
								<div class="col-sm-12">
									<h5 class="bold">
										<img class="img-min-grid-lg col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(eTicket.aSolicitante.user_photo ? eTicket.aSolicitante.user_photo : 'user_default.png')}}">
										{{eTicket.aSolicitante.user_nome}}
									</h5>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica E-mail?
										<input type="checkbox" value="None" id="edt_sol_tku_notif_email" name="edt_sol_tku_notif_email" ng-model="eTicket.aSolicitante.tku_notif_email"  ng-disabled="lDisableFields"/>
										<div class="control__indicator"></div>
									</label>
								</div>
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica Sistema?
										<input type="checkbox" value="None" id="edt_sol_tku_notif_sistema" name="edt_sol_tku_notif_sistema" ng-model="eTicket.aSolicitante.tku_notif_sistema"  ng-disabled="lDisableFields"/>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
					      	<h4 class="bold">
					      		<i class="fa fa-users"></i> Observadores
					      		<button type="button" class="btn btn-xs btn-link" ng-click="EditaAddObservador();" style="float: right;" ng-if="aUserAccess.pta_nivel >= 3"><i class="fa fa-plus"></i></button>
					      	</h4>
							<div ng-repeat="observador in eTicket.aObservadores">
								<div class="form-group row" ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields">
									<div class="col-sm-12">
										<selectize id="{{$index}}edt_obs_tku_user_id" name="{{$index}}edt_obs_tku_user_id" config="cfgUsuario" options="aUsuario" ng-model="observador.tku_user_id"></selectize>
									</div>
								</div>
								<div class="form-group row" ng-if="aUserAccess.pta_nivel < 3 || lDisableFields">
									<div class="col-sm-12">
										<h5 class="bold">
											<img class="img-min-grid-lg col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(observador.user_photo ? observador.user_photo : 'user_default.png')}}">
											{{observador.user_nome}}
										</h5>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-6">
										<label class="control control--checkbox small">Notifica E-mail?
											<input type="checkbox" value="None" id="{{$index}}edt_obs_tku_notif_email" name="{{$index}}edt_obs_tku_notif_email" ng-model="observador.tku_notif_email"  ng-disabled="lDisableFields"/>
											<div class="control__indicator"></div>
										</label>
									</div>
									<div class="col-sm-6">
										<label class="control control--checkbox small">Notifica Sistema?
											<input type="checkbox" value="None" id="{{$index}}edt_obs_tku_notif_sistema" name="{{$index}}edt_obs_tku_notif_sistema" ng-model="observador.tku_notif_sistema"  ng-disabled="lDisableFields"/>
											<div class="control__indicator"></div>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-4" style="border-left: 1px #000 solid;">
					      	<h4 class="text-primary bold">
					      		<i class="fa fa-user"></i> Responsável<span class="txt-obg">*</span>
					      	</h4>
							<div class="form-group row" ng-if="aUserAccess.pta_nivel >= 3 && !lDisableFields">
								<div class="col-sm-12">
									<selectize id="edt_resp_tku_user_id" name="edt_resp_tku_user_id" config="cfgUserResp" options="aUserResp" ng-model="eTicket.aResponsavel.tku_user_id" required></selectize>
								</div>
							</div>
							<div class="form-group row" ng-if="aUserAccess.pta_nivel < 3 || lDisableFields">
								<div class="col-sm-12">
									<h5 class="bold">
										<img class="img-min-grid-lg col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(eTicket.aResponsavel.user_photo ? eTicket.aResponsavel.user_photo : 'user_default.png')}}">
										{{eTicket.aResponsavel.user_nome}}
									</h5>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica E-mail?
										<input type="checkbox" value="None" id="edt_resp_tku_notif_email" name="edt_resp_tku_notif_email" ng-model="eTicket.aResponsavel.tku_notif_email"  ng-disabled="lDisableFields"/>
										<div class="control__indicator"></div>
									</label>
								</div>
								<div class="col-sm-6">
									<label class="control control--checkbox small">Notifica Sistema?
										<input type="checkbox" value="None" id="edt_resp_tku_notif_sistema" name="edt_resp_tku_notif_sistema" ng-model="eTicket.aResponsavel.tku_notif_sistema"  ng-disabled="lDisableFields"/>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
						</div>
					</div>
					<hr/>

					<div class="form-group row">						
						<div class="col-sm-2 center-justify">
							<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(eTicket.abert_user_photo ? eTicket.abert_user_photo : 'user_default.png')}}"><br/>
							<label class="ft-primary bold">{{eTicket.abert_user_nome}}</label><br/>
							<label class="ft-primary bold">{{eTicket.tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
						</div>
						<div class="col-sm-10">
							<h5 class="bold">{{eTicket.tkt_titulo}}</h5>
							<pre ng-bind-html="eTicket.descricao_ticket" class="word-wrap"></pre>
						</div>
					</div>
					<hr/>
					<div ng-repeat="comentario in eTicket.aComentarios">
						<div class="row form-group" ng-if="comentario.tkc_tipo == 'R'">
							<div class="col-sm-10">
								<pre ng-bind-html="comentario.descricao_comentario" class="bg-warning word-wrap"></pre>
							</div>
							<div class="col-sm-2 center-justify">
								<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(comentario.user_photo ? comentario.user_photo : 'user_default.png')}}"><br/>
								<label class="ft-primary bold">{{comentario.user_nome}}</label><br/>
								<label class="ft-primary bold">{{comentario.tkc_data_hora_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
								<button type="button" class="btn btn-xs btn-danger" title="Excluir Comentário" ng-click="deletaEditaComentarioTicket(comentario);" ng-if="(aUserAccess.pta_nivel >= 3 && !lDisableFields) || (comentario.user_id == aUserData.user_id && !lDisableButtons)"><i class="fa fa-trash"></i> Excluir</button>
							</div>
						</div>
						<div class="row form-group" ng-if="comentario.tkc_tipo != 'R'">
							<div class="col-sm-2 center-justify">
								<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(comentario.user_photo ? comentario.user_photo : 'user_default.png')}}"><br/>
								<label class="ft-primary bold">{{comentario.user_nome}}</label><br/>
								<label class="ft-primary bold">{{comentario.tkc_data_hora_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
								<button type="button" class="btn btn-xs btn-danger" title="Excluir Comentário" ng-click="deletaEditaComentarioTicket(comentario);" ng-if="(aUserAccess.pta_nivel >= 3 && !lDisableFields) || (comentario.user_id == aUserData.user_id && !lDisableButtons)"><i class="fa fa-trash"></i> Excluir</button>
							</div>
							<div class="col-sm-10">
								<pre ng-bind-html="comentario.descricao_comentario" class="bg-success word-wrap"></pre>
							</div>
						</div>
						<hr/>
					</div>

					<div ng-if="eTicket.tkt_aprovado == 'S'">
						<div class="row form-group">
							<div class="col-sm-2 center-justify">
								<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(eTicket.aprov_user_photo ? eTicket.aprov_user_photo : 'user_default.png')}}"><br/>
								<label class="ft-primary bold">{{eTicket.aprov_user_nome}}</label><br/>
								<label class="ft-primary bold">{{eTicket.tkt_aprovado_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
							</div>
							<div class="col-sm-10">
								<pre class="bg-info left-justify word-wrap">
									<i class="fa fa-3x fa-thumbs-o-up text-primary" style="float: left !important;"></i>
									<h5 class="bold text-primary">Ticket aprovado.</h5>
								</pre>
							</div>
						</div>
						<hr/>
					</div>

					<div ng-if="eTicket.tkt_encerrado == 'S'">
						<div class="row form-group">
							<div class="col-sm-10">
								<pre class="bg-success right-justify word-wrap">
									<i class="fa fa-3x fa-check text-success" style="float: right !important;"></i>
									<h5 class="bold text-success">Ticket encerrado.</h5>
								</pre>
							</div>
							<div class="col-sm-2 center-justify">
								<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(eTicket.enc_user_photo ? eTicket.enc_user_photo : 'user_default.png')}}"><br/>
								<label class="ft-primary bold">{{eTicket.enc_user_nome}}</label><br/>
								<label class="ft-primary bold">{{eTicket.tkt_encerrado_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
							</div>
						</div>
						<hr/>
					</div>

					<ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist" ng-init="option_tab = 1">
						<li class="nav-item mr-3">
							<a class="nav-link" ng-class="{'active' : option_tab == 1}" ng-click="option_tab = 1" data-toggle="tab" href="">
								<span class="nav-icon mr-2">
									<span class="svg-icon mr-3">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24"/>
												<path d="M14,16 L12,16 L12,12.5 C12,11.6715729 11.3284271,11 10.5,11 C9.67157288,11 9,11.6715729 9,12.5 L9,17.5 C9,19.4329966 10.5670034,21 12.5,21 C14.4329966,21 16,19.4329966 16,17.5 L16,7.5 C16,5.56700338 14.4329966,4 12.5,4 L12,4 C10.3431458,4 9,5.34314575 9,7 L7,7 C7,4.23857625 9.23857625,2 12,2 L12.5,2 C15.5375661,2 18,4.46243388 18,7.5 L18,17.5 C18,20.5375661 15.5375661,23 12.5,23 C9.46243388,23 7,20.5375661 7,17.5 L7,12.5 C7,10.5670034 8.56700338,9 10.5,9 C12.4329966,9 14,10.5670034 14,12.5 L14,16 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.500000, 12.500000) rotate(-315.000000) translate(-12.500000, -12.500000) "/>
											</g>
										</svg>
									</span>
								</span>
								<span class="nav-text font-weight-bold">Arquivos anexados</span>
							</a>
						</li>
						<li class="nav-item mr-3">
							<a class="nav-link" ng-class="{'active' : option_tab == 2}" ng-click="option_tab = 2" data-toggle="tab" href="">
								<span class="nav-icon mr-2">
									<span class="svg-icon mr-3">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										        <rect x="0" y="0" width="24" height="24"/>
										        <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z" fill="#000000" opacity="0.3"/>
										        <path d="M13,5.06189375 C12.6724058,5.02104333 12.3386603,5 12,5 C11.6613397,5 11.3275942,5.02104333 11,5.06189375 L11,4 L10,4 C9.44771525,4 9,3.55228475 9,3 C9,2.44771525 9.44771525,2 10,2 L14,2 C14.5522847,2 15,2.44771525 15,3 C15,3.55228475 14.5522847,4 14,4 L13,4 L13,5.06189375 Z" fill="#000000"/>
										        <path d="M16.7099142,6.53272645 L17.5355339,5.70710678 C17.9260582,5.31658249 18.5592232,5.31658249 18.9497475,5.70710678 C19.3402718,6.09763107 19.3402718,6.73079605 18.9497475,7.12132034 L18.1671361,7.90393167 C17.7407802,7.38854954 17.251061,6.92750259 16.7099142,6.53272645 Z" fill="#000000"/>
										        <path d="M11.9630156,7.5 L12.0369844,7.5 C12.2982526,7.5 12.5154733,7.70115317 12.5355117,7.96165175 L12.9585886,13.4616518 C12.9797677,13.7369807 12.7737386,13.9773481 12.4984096,13.9985272 C12.4856504,13.9995087 12.4728582,14 12.4600614,14 L11.5399386,14 C11.2637963,14 11.0399386,13.7761424 11.0399386,13.5 C11.0399386,13.4872031 11.0404299,13.4744109 11.0414114,13.4616518 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
										    </g>
										</svg>
									</span>
								</span>
								<span class="nav-text font-weight-bold">Apontamentos</span>
							</a>
						</li>
						<li class="nav-item mr-3">
							<a class="nav-link" ng-class="{'active' : option_tab == 3}" ng-click="option_tab = 3" data-toggle="tab" href="">
								<span class="nav-icon mr-2">
									<span class="svg-icon mr-3">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24"/>
												<path d="M7.38979581,2.8349582 C8.65216735,2.29743306 10.0413491,2 11.5,2 C17.2989899,2 22,6.70101013 22,12.5 C22,18.2989899 17.2989899,23 11.5,23 C5.70101013,23 1,18.2989899 1,12.5 C1,11.5151324 1.13559454,10.5619345 1.38913364,9.65805651 L3.31481075,10.1982117 C3.10672013,10.940064 3,11.7119264 3,12.5 C3,17.1944204 6.80557963,21 11.5,21 C16.1944204,21 20,17.1944204 20,12.5 C20,7.80557963 16.1944204,4 11.5,4 C10.54876,4 9.62236069,4.15592757 8.74872191,4.45446326 L9.93948308,5.87355717 C10.0088058,5.95617272 10.0495583,6.05898805 10.05566,6.16666224 C10.0712834,6.4423623 9.86044965,6.67852665 9.5847496,6.69415008 L4.71777931,6.96995273 C4.66931162,6.97269931 4.62070229,6.96837279 4.57348157,6.95710938 C4.30487471,6.89303938 4.13906482,6.62335149 4.20313482,6.35474463 L5.33163823,1.62361064 C5.35654118,1.51920756 5.41437908,1.4255891 5.49660017,1.35659741 C5.7081375,1.17909652 6.0235153,1.2066885 6.2010162,1.41822583 L7.38979581,2.8349582 Z" fill="#000000" opacity="0.3"/>
												<path d="M14.5,11 C15.0522847,11 15.5,11.4477153 15.5,12 L15.5,15 C15.5,15.5522847 15.0522847,16 14.5,16 L9.5,16 C8.94771525,16 8.5,15.5522847 8.5,15 L8.5,12 C8.5,11.4477153 8.94771525,11 9.5,11 L9.5,10.5 C9.5,9.11928813 10.6192881,8 12,8 C13.3807119,8 14.5,9.11928813 14.5,10.5 L14.5,11 Z M12,9 C11.1715729,9 10.5,9.67157288 10.5,10.5 L10.5,11 L13.5,11 L13.5,10.5 C13.5,9.67157288 12.8284271,9 12,9 Z" fill="#000000"/>
											</g>
										</svg>
									</span>
								</span>
								<span class="nav-text font-weight-bold">Histórico</span>
							</a>
						</li>
					</ul>
					<div class="tabs-content">
						<div class="tab-pane" ng-show="option_tab == 1" style="border: none !important;">
							<div ng-repeat="arquivo in eTicket.aArquivos" ng-if="eTicket.aArquivos.length">
								<div class="row form-group">
									<div class="col-sm-2 center-justify">
										<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(arquivo.user_photo ? arquivo.user_photo : 'user_default.png')}}"><br/>
										<label class="ft-primary bold">{{arquivo.user_nome}}</label><br/>
										<label class="ft-primary bold">{{arquivo.tka_data_hora_comp | date:'dd/MM/yyyy HH:mm:ss'}}</label>
										<button type="button" class="btn btn-xs btn-danger" title="Excluir Arquivo" ng-click="deletaEditaArquivoTicket(arquivo);" ng-if="(aUserAccess.pta_nivel >= 3 && !lDisableFields) || (comentario.user_id == aUserData.user_id && !lDisableButtons)"><i class="fa fa-trash"></i> Excluir</button>
									</div>
									<div class="col-sm-4" ng-if="arquivo.tka_arquivo_tipo == 'png' || arquivo.tka_arquivo_tipo == 'jpg' || arquivo.tka_arquivo_tipo == 'jpeg' || arquivo.tka_arquivo_tipo == 'gif' || arquivo.tka_arquivo_tipo == 'bmp'">
										<a href="{{arquivo.tka_arquivo_local + arquivo.tka_arquivo_nome}}" data-toggle="lightbox">
											<img class="img-usuario" src="{{arquivo.tka_arquivo_local + arquivo.tka_arquivo_nome}}" >
										</a>
									</div>
									<div class="col-sm-6" ng-if="arquivo.tka_arquivo_tipo == 'png' || arquivo.tka_arquivo_tipo == 'jpg' || arquivo.tka_arquivo_tipo == 'jpeg' || arquivo.tka_arquivo_tipo == 'gif' || arquivo.tka_arquivo_tipo == 'bmp'">
										<label class="bold">{{arquivo.tka_arquivo_nome}}</label><br/>
										<label class="bold">({{arquivo.tka_arquivo_tipo}})</label>
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
										<label class="bold">({{arquivo.tka_arquivo_tipo}})</label>
									</div>
								</div>
								<hr/>
							</div>
							<div class="alert alert-warning" ng-if="!eTicket.aArquivos.length">
								<h5>Nenhum arquivo foi anexado nesse ticket ainda...</h5>
							</div>
						</div>
						<div class="tab-pane" ng-show="option_tab == 2" style="border: none !important;">
							<div class="table-responsive" ng-if="eTicket.aApontamentos.length">
								<table class="table table-striped display nowrap" id="tbClientes">
						  			<thead class="thead">
										<tr>
											<th class="text-nowrap center-justify" colspan="2"><a href="" ng-click="ordenarPor('user_nome')">Usuário</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkp_data')">Data</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkp_hora_exec_ini')">Hora de início</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkp_hora_exec_fim')">Hora de fim</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkp_horas_total_comp')">Total de horas</a></th>
											<th class="text-nowrap">#</th>
										</tr>
									</thead>			
						  			<tbody>		  				
										<tr ng-animate="'animate'" ng-repeat="apontamento in eTicket.aApontamentos">
											<td class="text-nowrap right-justify">
												<img class="img-usuario-min-grid col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(apontamento.user_photo ? apontamento.user_photo : 'user_default.png')}}">
											</td>
											<td class="text-nowrap">{{apontamento.user_nome}}</td>
											<td class="text-nowrap">{{apontamento.tkp_data | date:'dd/MM/yyyy'}}</td>
											<td class="text-nowrap">{{apontamento.tkp_hora_exec_ini}}</td>
											<td class="text-nowrap">{{apontamento.tkp_hora_exec_fim}}</td>
											<td class="text-nowrap">{{apontamento.tkp_horas_total_comp}}</td>
											<td class="text-nowrap">
												<button type="button" class="btn btn-xs btn-link" title="Excluir Apontamento" ng-click="deletaEditaApontamentoTicket(apontamento);" ng-if="(aUserAccess.pta_nivel >= 3 && !lDisableFields) || (comentario.user_id == aUserData.user_id && !lDisableButtons)"><i class="fa fa-trash text-danger"></i></button>
											</td>
										</tr>
						  			</tbody> 
								</table>
							</div>							
							<div class="alert alert-warning" ng-if="!eTicket.aApontamentos.length">
								<h5>Nenhum apontamento realizado para esse ticket ainda...</h5>
							</div>
						</div>
						<div class="tab-pane" ng-show="option_tab == 3" style="border: none !important;">
							<div ng-repeat="historico in eTicket.aHistorico" ng-if="eTicket.aHistorico.length">
								<div class="row form-group">
									<div class="col-sm-1 center-justify">
										<img class="img-usuario-min col-radius" src="<?php echo $security->base_patch.'/assets/img/sys_images/'; ?>{{(historico.user_photo ? historico.user_photo : 'user_default.png')}}"><br/>
									</div>
									<div class="col-sm-10">
										<label class="bold">{{historico.user_nome}} em {{historico.tkh_data_hora_comp | date:'dd/MM/yyyy'}} ás {{historico.tkh_data_hora_comp | date:'HH:mm:ss'}}</label>
										<p>{{historico.tkh_descricao}}</p>
									</div>
								</div>
							</div>
							<div class="alert alert-warning" ng-if="!eTicket.aHistorico.length">
								<h5>Não existe nenhum histórico para esse ticket ainda...</h5>
							</div>
						</div>
					</div>
              	</form>

          	</div>
       	</div>
    </div>
</div>


<div id="mdArquivo" tabindex='-1' class="modal fade" style="overflow: auto;">				
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">				
		        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h5 class="modal-title"><strong><i class="fa fa-md fa-paperclip fa-plus text-warning"></i> Adicionar arquivo ao Ticket</strong></h5>
			</div>

			<div class="modal-body">
				<form name="formEdtNovoArqTicket" role="form">

					<div class="row form-group jumbotron">
						<div class="col-sm-12">
							<div class="row form-group">
								<div class="col-sm-10 col-sm-offset-1 center-justify">
									<div id="det_file_ticket">
										<h5 class="bold">Anexar Arquivo ao Ticket</h5>
										<h5 class="ft-gray-dk">(clique no ícone para selecionar o arquivo)</h5>
										<a href=""><i class="upload-icon ace-icon fa fa-cloud-upload ft-blue fa-3x"></i></a><br/>
									</div>
									<div id="det_div-selected-files"style="display: none;">
										<h5 id="det_selected-files" class="ft-primary bold"></h5>
									</div>
								</div>
					            <input type="file" name="det_ticket_files" id="det_ticket_files" class="form-control my-control" file-model="nTicketArquivo.ticketFile" style="display: none;" required/>
					        </div>
							<div class="row">
								<div id="det_div-upload-img" class="alert alert-warning" style="display: none">
									<div id="det_uploading-img" ></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" ng-if="nTicketArquivo.ticketFile">
						<div class="col-sm-6 col-sm-offset-3 center-justify">
							<a href="" class="btn btn-block btn-md btn-primary" ng-click="uploadEditaTicketFile(nTicketArquivo.ticketFile)"><i class="fa fa-paper-plane"></i> Enviar</a>
						</div>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<div class="row">
			      	<div class="col-sm-12 right-justify">
			      		<div class="col-sm-4 col-sm-offset-8" style="margin-right: -20px;">
							<a href="" class="btn-block btn btn-xs btn-default" ng-click="limpaAnexaArquivo()">Cancelar</a>
						</div>
			      	</div>
			    </div>
			</div>

		</div>
	</div>
</div>

<div id="mdComentario" tabindex='-1' class="modal fade" style="overflow: auto;">				
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">				
		        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h5 class="modal-title"><strong><i class="fa fa-md fa-comments-o fa-plus text-warning"></i> Adicionar comentário ao Ticket</strong></h5>
			</div>

			<div class="modal-body">
				<form name="formEdtNovoComentarioTicket" role="form">
					<div class="form-group row">
						<div class="col-sm-12" ng-class="{ 'has-error': formEdtNovoComentarioTicket.tkc_descricao.$dirty && edtForm.tkc_descricao.$error.required }">
							<ng-quill-editor ng-model="nTicketComentario.tkc_descricao">
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
							<!-- <div text-angular ng-model="nTicketComentario.tkc_descricao" required></div> -->
						</div>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<div class="row">
			      	<div class="col-sm-12 right-justify">
			      		<div class="col-sm-4 col-sm-offset-4" style="margin-right: -20px;">
							<a href="" class="btn-block btn btn-xs btn-default" data-dismiss="modal" aria-label="Close">Cancelar</a>
						</div>
			      		<div class="col-sm-4" style="margin-right: -20px;">
							<a href="" class="btn-block btn btn-xs btn-primary" ng-click="formEdtNovoComentarioTicket.$invalid || novoEditaComentarioTicket(nTicketComentario)" ng-disabled="formEdtNovoComentarioTicket.$invalid"><i class="fa fa-paper-plane"></i> Enviar</a>
						</div>
			      	</div>
			    </div>
			</div>

		</div>
	</div>
</div>

<div id="mdApontamento" tabindex='-1' class="modal fade" style="overflow: auto;">				
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">				
		        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h5 class="modal-title"><strong><i class="fa fa-md fa-clock text-warning"></i> Apontamento o Ticket</strong></h5>
			</div>

			<div class="modal-body">
				<form name="formEdtNovAptmt" role="form">
					<div class="form-group row">
						<div class="col-sm-12" ng-class="{ 'has-error': formEdtNovAptmt.tkp_data.$dirty && formEdtNovAptmt.tkp_data.$error.required }">							
							<label>Data do apontamento</label><span class="txt-obg">*</span>
							<div class="input-group">
							  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
							  	<input type="text" name="tkp_data" id="tkp_data"  ng-maxlength="10" maxlength="10" class="form-control my-control iptdate" ng-model="nTicketApontamento.tkp_data" placeholder="DD/MM/YYY" required/>
						 	</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-6" ng-class="{ 'has-error': formEdtNovAptmt.tkp_hora_exec_ini.$dirty && formEdtNovAptmt.tkp_hora_exec_ini.$error.required }">
							<label>Hora de início</label><span class="txt-obg">*</span>
							<div class="input-group">
							  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
							  	<input type="text" name="tkp_hora_exec_ini" id="tkp_hora_exec_ini"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="nTicketApontamento.tkp_hora_exec_ini" placeholder="HH:mm" required/>
						 	</div>
						</div>
						<div class="col-sm-6" ng-class="{ 'has-error': formEdtNovAptmt.tkc_descricao.$dirty && formEdtNovAptmt.tkc_descricao.$error.required }">
							<label>Hora de fim</label><span class="txt-obg">*</span>
							<div class="input-group">
							  	<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-clock"></i></span>
							  	<input type="text" name="tkp_hora_exec_fim" id="tkp_hora_exec_fim"  ng-maxlength="10" maxlength="10" class="form-control my-control ipthora" ng-model="nTicketApontamento.tkp_hora_exec_fim" placeholder="HH:mm" required/>
						 	</div>
						</div>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<div class="row">
			      	<div class="col-sm-12 right-justify">
			      		<div class="col-sm-4 col-sm-offset-4" style="margin-right: -20px;">
							<a href="" class="btn-block btn btn-xs btn-primary" ng-click="formEdtNovAptmt.$invalid || novoEditaApontamentoTicket(nTicketApontamento, true)" ng-disabled="formEdtNovAptmt.$invalid">Confirmar e sair</a>
						</div>
			      		<div class="col-sm-4" style="margin-right: -20px;">
							<a href="" class="btn-block btn btn-xs btn-success" ng-click="formEdtNovAptmt.$invalid || novoEditaApontamentoTicket(nTicketApontamento, false)" ng-disabled="formEdtNovAptmt.$invalid">Confirmar e continuar</a>
						</div>
			      	</div>
			    </div>
			</div>

		</div>
	</div>
</div>