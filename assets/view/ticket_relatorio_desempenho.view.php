<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();	
?>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Tickets</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="fa fa-angle-double-right"></i>
				<h4 class="kt-subheader__breadcrumbs-link bold">&nbsp;Relatórios</h4>
			</div>
			<div class="kt-subheader__breadcrumbs">
				<i class="fa fa-angle-double-right"></i>
				<a href="#/relatoriodesempenho" class="kt-subheader__breadcrumbs-link bold">&nbsp;Relatório Desempenho da Equipe</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">

					<a href="" class="btn btn-md  btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações Relacionadas">
						<i class="fa fa-ellipsis-h"></i>
					</a>

					<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
						<!--begin::Nav-->
						<ul class="kt-nav">
							<li class="kt-nav__head"><strong>Ações relacionadas:</strong></li>
							<li class="kt-nav__separator"></li>							
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Relatório Gerencial', 'Tickets Relatórios', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" data-toggle="modal" data-target="#mdHelp" class="kt-nav__link">
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

			<div class="row kt-portlet__head kt-portlet__head--sm" style="padding-top: 15px;">
				<div class="col-xs-12">

					<form name="rptForm" role="form" ng-show="aUserAccess.pta_nivel >= 3">
						<div class="row form-group">
				        	<div class="col-sm-3">
					        	<label>Data de</label><span class="txt-obg">*</span>
						        <div ng-class="{ 'has-error': rptForm.tkt_rel_desemp_data_de.$dirty && rptForm.tkt_rel_desemp_data_de.$error.required }" >
						        	<div class="input-group">
						        		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
								    	<input type="text" name="tkt_rel_desemp_data_de" id="tkt_rel_desemp_data_de" class="form-control my-control iptdate" ng-model="reportFilter.tkt_rel_desemp_data_de" placeholder="DD/MM/YYY" required/>
								    </div>
								</div>
					        </div>
				        	<div class="col-sm-3">
					        	<label>Data até</label><span class="txt-obg">*</span>
						        <div ng-class="{ 'has-error': rptForm.tkt_rel_desemp_data_ate.$dirty && rptForm.tkt_rel_desemp_data_ate.$error.required }" >
						        	<div class="input-group">
						        		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
								    	<input type="text" name="tkt_rel_desemp_data_ate" id="tkt_rel_desemp_data_ate" class="form-control my-control iptdate" ng-model="reportFilter.tkt_rel_desemp_data_ate" placeholder="DD/MM/YYY" ng-blur="getCliRegTribByDate(reportFilter.data_ate);" required/>
								    </div>
								</div>
					        </div>
					        <div class="col-sm-3">
					        	<label>Tipo Relatório</label><span class="txt-obg">*</span>
								<selectize name="tipo_rel" id="tipo_rel" config="cfgTipoRel" options="aTipoRel" ng-model="reportFilter.tipo_rel" required></selectize>
							</div>
							<div class="col-sm-3">
								<label>Filtrar por Pasta de Trabalho</label>
								<selectize id="fil_tkt_pst_id" name="fil_tkt_pst_id" config="cfgPastaTrabalho" options="aPastaTrabalho" ng-model="reportFilter.fil_tkt_pst_id"></selectize>
							</div>
					    </div>
						
						<div class="form-group row">
							<div class="col-sm-3">
								<label>Filtrar por Origem</label>
								<selectize id="fil_tkt_ort_id" name="fil_tkt_ort_id" config="cfgOrigemTicket" options="aOrigemTicket" ng-model="reportFilter.fil_tkt_ort_id"></selectize>
							</div>
							<div class="col-sm-3">
								<label>Filtrar por Tipo de Atividade</label>
								<selectize id="fil_tkt_tav_id" name="fil_tkt_tav_id" config="cfgTipoAtividade" options="aTipoAtividade" ng-model="reportFilter.fil_tkt_tav_id"></selectize>
							</div>
							<div class="col-sm-3">
								<label>Filtrar por Prioridade</label>
								<selectize id="fil_tkt_prt_id" name="fil_tkt_prt_id" config="cfgPrioridadeTicket" options="aPrioridadeTicket" ng-model="reportFilter.fil_tkt_prt_id"></selectize>
							</div>
							<div class="col-sm-3">
								<label>Filtrar por Categoria</label>
								<selectize id="fil_tkt_cgt_id" name="fil_tkt_cgt_id" config="cfgCategoriaTicket" options="aCategoriaTicket" ng-model="reportFilter.fil_tkt_cgt_id"></selectize>
							</div>
					    </div>
						
						<div class="form-group row">
							<div class="col-sm-4">
								<label>Filtrar por Situação</label>
								<selectize id="fil_tkt_stt_id" name="fil_tkt_stt_id" config="cfgSituacaoTicket" options="aSituacaoTicket" ng-model="reportFilter.fil_tkt_stt_id"></selectize>
							</div>
							<div class="col-sm-4">
								<label>Filtrar por Solicitante</label>
								<selectize id="fil_tkt_solic_id" name="fil_tkt_solic_id" config="cfgUsuario" options="aUsuario" ng-model="reportFilter.fil_tkt_solic_id"></selectize>
							</div>
							<div class="col-sm-4">
								<label>Filtrar por Responsável</label>
								<selectize id="fil_tkt_resp_id" name="fil_tkt_resp_id" config="cfgUserResp" options="aUserResp" ng-model="reportFilter.fil_tkt_resp_id"></selectize>
							</div>
						</div>
					</form>
					<hr>
					<div class="row form-group">
				        <div class="col-sm-4">
				        	<button type="button" ng-click="exportaDados('Relatório Desempenho Tickets<br>' + aTipoRel[reportFilter.tipo_rel-1].tpr_descr, 'tbRelDesempenhoTickets', 'EXCEL')" ng-show="aRelTicketsDesempenho.HTMLFILE" class="btn btn-sm btn-block btn-success"><i class="fa fa-file-excel-o"></i> Gerar EXCEL</button>
					    </div>
				        <div class="col-sm-4">
				        	<button type="button" ng-click="exportaDados('Relatório Desempenho Tickets<br>' + aTipoRel[reportFilter.tipo_rel-1].tpr_descr, 'tbRelDesempenhoTickets', 'PDF')" ng-show="aRelTicketsGerencial.HTMLFILE" class="btn btn-sm btn-block btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
					    </div>
				        <div class="col-sm-4">
				        	<button type="button" ng-click="rptForm.$invalid || generateReport(reportFilter)" ng-disabled="rptForm.$invalid" class="btn btn-sm btn-block btn-primary">Gerar Relatório</button>
					    </div>
					</div>
					<div class="row form-group">
				        <div class="col-sm-12">	
							<div class="alert alert-warning" id="div-loading">
								<span id="loading"><div class="loading-img"></div></span>
							</div>
						 </div>
					</div>	
				</div>
			</div>

			<div class="kt-portlet__body" >		

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>
								
				<div class="table-responsive">
					<div id="tbRelDesempenhoTickets">
						<ng-include src="aRelTicketsGerencial.HTMLFILE"></ng-include>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>