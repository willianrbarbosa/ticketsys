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
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/ticket" class="kt-subheader__breadcrumbs-link bold">&nbsp;Meus Ticket</a>
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
								<a href="" data-toggle="modal" data-target="#mdFiltroManual" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-filter ft-primary"></i> <span class="kt-nav__link-text">Filtro personalizado</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Meus Tickets', 'Tickets', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__head"><strong>Exportar dados:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Ticket', 'tbTicket', 'EXCEL')">
									<i class="kt-nav__link-icon fa fa-file-excel-o ft-success"></i>
									<span class="kt-nav__link-text">EXCEL</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Ticket', 'tbTicket', 'PDF')">
									<i class="kt-nav__link-icon fa fa-file-pdf-o ft-danger"></i>
									<span class="kt-nav__link-text">PDF</span>
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
				<div class="col-xs-12" ng-show="aUserAccess.pta_nivel">
					<div class="row">
						<div class="col-xs-offset-9 col-xs-2">
							<a href="" data-toggle="modal" data-target="#mdNewTicket" class="btn btn-block btn-sm btn-block btn-primary" title="Adicionar novo cadastro">
								<i class="fa fa-plus"></i> Abrir novo Ticket
							</a>
						</div>
						<div class="col-xs-1" ng-show="aUserAccess.pta_nivel">
						    <a href="" class="btn btn-block btn-sm btn-block btn-default" ng-click="loadMeusTickets(cFiltroManual)"><i class="fa fa-refresh"></i>&nbsp;</a>
						</div>
					</div>
					<div class="row" ng-show="aUserAccess.pta_nivel">
						<div class="col-xs-11 right-justify">
						    <span class="ft-danger" ng-if="lTemFiltro" style="float: right;">
						      	*** Há filtros personalizados aplicado na consulta.</span>
						    </span>
						</div>
						<div class="col-xs-1 left-justify" ng-show="aUserAccess.pta_nivel">
						    <button type="button" ng-if="lTemFiltro" class="btn btn-link btn-xs ft-danger" title="Remover filtro" ng-click="limpaFiltroManual(true)"><i class="fa fa-trash"></i></button>
						</div>
					</div>
				</div>
			</div>

			<div class="kt-portlet__body" >

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>
				
				<div class="alert alert-warning" ng-if="!aTicket.length">
					<span id="loading"><div class="loading-img"> Carregando listagem de Ticket. Aguarde...</div></span>
				</div>


				<ul class="nav nav-pills nav-tabs-line-primary" role="tablist" ng-init="option_tab = 1">
					<li class="nav-item" ng-class="{'bg-primary-dark active' : option_tab == 1}">
						<a href="" class="nav-link bold" ng-class="{'active bg-primary-dark ft-white' : option_tab == 1}" data-toggle="tab" ng-click="option_tab = 1" role="tab">Pendentes</a>
					</li>
					<li class="nav-item" ng-class="{'bg-primary-dark active' : option_tab == 2}">
						<a href="" class="nav-link bold" ng-class="{'active bg-primary-dark ft-white' : option_tab == 2}" data-toggle="tab" ng-click="option_tab = 2" role="tab">Encerrados</a>
					</li>
				</ul>
				<div class="tabs-content">
					<br/>
					<div class="tab-pane" ng-show="option_tab == 1" style="border: none !important;">
						<div ng-if="aTicket.length && aUserAccess.pta_nivel">
							<div class="table-responsive">
								<table class="table table-striped display nowrap" id="tbTicket">
									<thead class="thead">
										<tr>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_id')">Nº.</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('pst_descricao')">Pasta</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_titulo')">Título</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_abertura_data')">Data Abertura</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('stt_descricao')">Situação</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('prt_descricao')">Prioridade</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('solic_user_nome')">Solicitante</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('resp_user_nome')">Responsável</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_per_concluido')">% EXR</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('PRAZO')">Prazo</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('AGENDA')">Agenda</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_aprovado')">Aprovado?</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_aprovado_data')">Data Aprovação</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('aprov_user_nome')">Usuário Aprovação</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_ticket_pai')">Ticket Pai</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_upddate')">Última alt.</a></th>
											<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('tkt_deldate')">Data exc.</a></th>
											<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('tkt_deluser')">Usuário exc.</a></th>
										</tr>
									</thead>	
									<tbody>	
										<tr ng-class="{'text-danger': lDeleted, 'text-primary bold': ticket.tkt_aprovado == 'S'}"  ng-repeat="ticket in aTicket | filter: {tkt_encerrado: 'N'} | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
											<td class="text-nowrap"><a href="#/detalheticket/{{ticket.tkt_id}}">#{{ticket.tkt_id}}</a></td>
											<td class="">{{ticket.grt_descricao}} > {{ticket.pst_descricao}}</td>
											<td class="text-nowrap">{{ticket.tkt_titulo}}</td>
											<td class="text-nowrap">{{ticket.tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="">{{ticket.stt_descricao}}</td>
											<td class="text-nowrap">
												<div class="kt-ribbon__target ft-white center-justify" style="padding: 3px !important; background-color: {{ticket.prt_cor}} !important;" >
													{{ticket.prt_descricao}}
												</div>
											</td>
											<td class="">{{ticket.solic_user_nome}}</td>
											<td class="">{{ticket.resp_user_nome}}</td>
											<td class="text-nowrap">
												<div class="progress progress-striped" style="width: 120px; height: 20px; margin-bottom: 0 !important;">
													<div class="progress-bar progress-bar-success animate slideInLeft wow" role="progressbar" style="width: {{ticket.tkt_per_concluido}}%" 
														aria-valuemin="0" 
														aria-valuenow="{{ticket.tkt_per_concluido | number: 2}}" 
														aria-valuemax="100"><span class="x-small bold ft-black">{{ticket.tkt_per_concluido | number: 2}} %</span></div>
												</div>
											</td>
											<td class="text-nowrap">
												<i class="fa fa-lg" 
													ng-class="{'fa-circle-o-notch ft-gray-dk': ticket.PRAZO == 'B', 'fa-square text-danger': ticket.PRAZO == 'N', 'fa-exclamation-triangle ft-yellow': ticket.PRAZO == 'D', 'fa-circle text-success': ticket.PRAZO == 'S'}" 
													title="{{(ticket.PRAZO == 'B' ? 'Data de término estimado não informado' : (ticket.PRAZO == 'N' ? 'Fora do prazo' : (ticket.PRAZO == 'D' ? 'Encerra HOJE' : 'No prazo')))}}">												
												</i>
											</td>
											<td class="text-nowrap">
												<i class="fa fa-lg" 
													ng-class="{'fa-circle-o-notch ft-gray-dk': ticket.AGENDA == 'B', 'fa-square text-danger': ticket.AGENDA == 'N', 'fa-exclamation-triangle ft-yellow': ticket.AGENDA == 'P', 'fa-circle text-success': ticket.AGENDA == 'S'}" 
													title="{{(ticket.AGENDA == 'B' ? 'Data de início estimado não informado' : (ticket.AGENDA == 'N' ? 'Iniciado depois do agendado' : (ticket.AGENDA == 'P' ? 'Iniciado antes do agendado' : 'No início agendado')))}}">
												</i>
											</td>
											<td class="">{{ticket.tkt_aprovado}}</td>
											<td class="">{{ticket.tkt_aprovado_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="">{{ticket.aprov_user_nome}}</td>
											<td class="text-nowrap">{{ticket.tkt_ticket_pai}}</td>
											<td class="text-nowrap">{{ticket.tkt_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="text-nowrap" ng-if="lDeleted">{{ticket.tkt_deldate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="text-nowrap" ng-if="lDeleted">{{ticket.tkt_deluser | date:'dd/MM/yyyy HH:mm:ss'}}</td>
										</tr>
									</tbody> 
								</table>
							</div> 
						</div>
					</div>
					<div class="tab-pane" ng-show="option_tab == 2" style="border: none !important;">
						<div ng-if="aTicket.length && aUserAccess.pta_nivel">
							<div class="table-responsive">
								<table class="table table-striped display nowrap" id="tbTicket">
									<thead class="thead">
										<tr>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_id')">Nº.</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('pst_descricao')">Pasta</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_titulo')">Título</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_abertura_data')">Data Abertura</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('stt_descricao')">Situação</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('prt_descricao')">Prioridade</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('solic_user_nome')">Solicitante</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('resp_user_nome')">Responsável</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_per_concluido')">% EXR</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('PRAZO')">Prazo</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('AGENDA')">Agenda</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_aprovado')">Aprovado?</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_aprovado_data')">Data Aprovação</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('aprov_user_nome')">Usuário Aprovação</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_encerrado')">Encerrado?</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_encerrado_data')">Data Encerram.</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('enc_user_nome')">Usuário Encerram.</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_ticket_pai')">Ticket Pai</a></th>
											<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_upddate')">Última alt.</a></th>
											<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('tkt_deldate')">Data exc.</a></th>
											<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('tkt_deluser')">Usuário exc.</a></th>
										</tr>
									</thead>	
									<tbody>	
										<tr ng-class="{'text-danger': lDeleted, 'text-success bold': ticket.tkt_encerrado == 'S'}"  ng-repeat="ticket in aTicket | filter: {tkt_encerrado: 'S'} | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
											<td class="text-nowrap"><a href="#/detalheticket/{{ticket.tkt_id}}">#{{ticket.tkt_id}}</a></td>
											<td class="">{{ticket.grt_descricao}} > {{ticket.pst_descricao}}</td>
											<td class="text-nowrap">{{ticket.tkt_titulo}}</td>
											<td class="text-nowrap">{{ticket.tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="">{{ticket.stt_descricao}}</td>
											<td class="text-nowrap">
												<div class="kt-ribbon__target ft-white center-justify" style="padding: 3px !important; background-color: {{ticket.prt_cor}} !important;" >
													{{ticket.prt_descricao}}
												</div>
											</td>
											<td class="">{{ticket.solic_user_nome}}</td>
											<td class="">{{ticket.resp_user_nome}}</td>
											<td class="text-nowrap">
												<div class="progress progress-striped" style="width: 120px; height: 20px; margin-bottom: 0 !important;">
													<div class="progress-bar progress-bar-success animate slideInLeft wow" role="progressbar" style="width: {{ticket.tkt_per_concluido}}%" 
														aria-valuemin="0" 
														aria-valuenow="{{ticket.tkt_per_concluido | number: 2}}" 
														aria-valuemax="100"><span class="x-small bold ft-black">{{ticket.tkt_per_concluido | number: 2}} %</span></div>
												</div>
											</td>
											<td class="text-nowrap">
												<i class="fa fa-lg" 
													ng-class="{'fa-circle-o-notch ft-gray-dk': ticket.PRAZO == 'B', 'fa-square text-danger': ticket.PRAZO == 'N', 'fa-exclamation-triangle ft-yellow': ticket.PRAZO == 'D', 'fa-circle text-success': ticket.PRAZO == 'S'}" 
													title="{{(ticket.PRAZO == 'B' ? 'Data de término estimado não informado' : (ticket.PRAZO == 'N' ? 'Fora do prazo' : (ticket.PRAZO == 'D' ? 'Encerra HOJE' : 'No prazo')))}}">												
												</i>
											</td>
											<td class="text-nowrap">
												<i class="fa fa-lg" 
													ng-class="{'fa-circle-o-notch ft-gray-dk': ticket.AGENDA == 'B', 'fa-square text-danger': ticket.AGENDA == 'N', 'fa-exclamation-triangle ft-yellow': ticket.AGENDA == 'P', 'fa-circle text-success': ticket.AGENDA == 'S'}" 
													title="{{(ticket.AGENDA == 'B' ? 'Data de início estimado não informado' : (ticket.AGENDA == 'N' ? 'Iniciado depois do agendado' : (ticket.AGENDA == 'P' ? 'Iniciado antes do agendado' : 'No início agendado')))}}">
												</i>
											</td>
											<td class="">{{ticket.tkt_aprovado}}</td>
											<td class="">{{ticket.tkt_aprovado_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="">{{ticket.aprov_user_nome}}</td>
											<td class="">{{ticket.tkt_encerrado}}</td>
											<td class="">{{ticket.tkt_encerrado_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="">{{ticket.enc_user_nome}}</td>
											<td class="text-nowrap">{{ticket.tkt_ticket_pai}}</td>
											<td class="text-nowrap">{{ticket.tkt_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="text-nowrap" ng-if="lDeleted">{{ticket.tkt_deldate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
											<td class="text-nowrap" ng-if="lDeleted">{{ticket.tkt_deluser | date:'dd/MM/yyyy HH:mm:ss'}}</td>
										</tr>
									</tbody> 
								</table>
							</div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-sm-12 center-justify">
						<pagination ng-init="cPg=currentPage+1" ng-model="cPg" total-items="nPgTotal" ng-change="currentPage=cPg-1" max-size="maxSize" boundary-links="true"></pagination>
					</div>
				</div>
				<hr/>
			</div>

		</div>
	</div>
</div>


<div id="mdFiltroManual" tabindex="-1" class="modal fade" style="overflow: auto;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">

			<div class="modal-header">
		        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h5 class="modal-title"><strong><i class="fa fa-md fa-filter"></i> Filtro personalizado</strong></h5>
			</div>

			<div class="modal-body" style="text-align: justify;">
				<div class="row form-group" ng-show="aUserAccess.pta_nivel">
					<div class="col-sm-1">
						<select  class="form-control my-control-sel" ng-model="operatorfilterBD" ng-show="cFiltroManual && cFiltroManual != '('">
							<option value=""></option>
							<option value="AND">E</option>
							<option value="OR">OU</option>
						</select>
					</div>
					<div class="col-sm-1" >
						<button type="button" class="btn btn-block btn-sm btn-default" ng-click="cFiltroManual = cFiltroManual+'('">&nbsp;(&nbsp;</button>
					</div>
					<div class="col-sm-1" >
						<button type="button" class="btn btn-block btn-sm btn-default" ng-click="cFiltroManual = cFiltroManual+')'">&nbsp;)&nbsp;</button>
					</div>
					<div class="col-sm-9" >
						<select  class="form-control my-control-sel" ng-model="filterFieldBD">
							<option value=""></option>
							<option value="tkt_id">Reg.</option>
							<option value="tkt_pst_id">Pasta de Trabalho</option>
							<option value="tkt_titulo">Título</option>
							<option value="tkt_tav_id">Tipo de Atividade</option>
							<option value="tkt_abertura_data">Data de Abertura</option>
							<option value="tkt_stt_id">Situação</option>
							<option value="tkt_cgt_id">Categoria </option>
							<option value="tkt_prt_id">Prioridade</option>
							<option value="tkt_ort_id">Origem</option>
							<option value="tkt_per_concluido">% Concluído</option>
							<option value="tkt_aprovado">Aprovado?</option>
							<option value="tkt_aprovado_data">Data Aprovação</option>
							<option value="tkt_ticket_pai">Ticket Pai</option>
							<option value="tkt_arquivado">Arquivado?</option>
							<option value="tkt_arquivado_data">Data Arquiv.</option>
							<option value="tkt_incdate">Data inc.</option>
							<option value="tkt_upddate">Data alt.</option>
							<option value="tkt_deldate">Data exc.</option>
							<option value="tkt_deluser">Usuário exc.</option>
						</select>
					</div>
				</div>

				<div class="row form-group" ng-show="aUserAccess.pta_nivel">
					<div class="col-sm-4" >
						<select  class="form-control my-control-sel" ng-model="sinalfilterBD">
							<option value="=">igual</option>
							<option value="<>">diferente</option>
							<option value="LIKE">contém</option>
							<option value="NOT LIKE">não contém</option>
							<option value=">">maior</option>
							<option value=">=">maior ou igual</option>
							<option value="<">menor</option>
							<option value="<=">menor ou igual</option>
						</select>
					</div>
					<div class="col-sm-6" >
						<div ng-show="filterFieldBD == 'tkt_pst_id'"><selectize config="cfgPastaTrabalho" options="aPastaTrabalho" ng-model="cfilterBD"></selectize></div>
						<div ng-show="filterFieldBD == 'tkt_tav_id'"><selectize config="cfgTipoAtividade" options="aTipoAtividade" ng-model="cfilterBD"></selectize></div>
						<div ng-show="filterFieldBD == 'tkt_stt_id'"><selectize config="cfgSituacaoTicket" options="aSituacaoTicket" ng-model="cfilterBD"></selectize></div>
						<div ng-show="filterFieldBD == 'tkt_cgt_id'"><selectize config="cfgCategoriaTicket" options="aCategoriaTicket" ng-model="cfilterBD"></selectize></div>
						<div ng-show="filterFieldBD == 'tkt_prt_id'"><selectize config="cfgPrioridadeTicket" options="aPrioridadeTicket" ng-model="cfilterBD"></selectize></div>
						<div ng-show="filterFieldBD == 'tkt_ort_id'"><selectize config="cfgOrigemTicket" options="aOrigemTicket" ng-model="cfilterBD"></selectize></div>
						<input class="form-control my-control iptdate" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="(filterFieldBD == 'tkt_abertura_data' || filterFieldBD == 'tkt_data_ini_estim' || filterFieldBD == 'tkt_hora_ini_estim' || filterFieldBD == 'tkt_data_ini_real' || filterFieldBD == 'tkt_hora_ini_real' || filterFieldBD == 'tkt_data_fim_estim' || filterFieldBD == 'tkt_hora_fim_estim' || filterFieldBD == 'tkt_data_fim_real' || filterFieldBD == 'tkt_hora_fim_real' || filterFieldBD == 'tkt_aprovado_data' || filterFieldBD == 'tkt_arquivado_data' || filterFieldBD == 'tkt_incdate' || filterFieldBD == 'tkt_upddate' || filterFieldBD == 'tkt_deldate') && (filterFieldBD != 'tkt_pst_id' && filterFieldBD != 'tkt_tav_id' && filterFieldBD != 'tkt_abertura_user_id' && filterFieldBD != 'tkt_stt_id' && filterFieldBD != 'tkt_cgt_id' && filterFieldBD != 'tkt_prt_id' && filterFieldBD != 'tkt_ort_id' && filterFieldBD != 'tkt_aprovado_user_id' && filterFieldBD != 'tkt_arquivado_user_id')">
						<input class="form-control my-control iptcur" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="(filterFieldBD == 'tkt_total_hora_estim' || filterFieldBD == 'tkt_total_hora_real' || filterFieldBD == 'tkt_per_concluido') && (filterFieldBD != 'tkt_pst_id' && filterFieldBD != 'tkt_tav_id' && filterFieldBD != 'tkt_abertura_user_id' && filterFieldBD != 'tkt_stt_id' && filterFieldBD != 'tkt_cgt_id' && filterFieldBD != 'tkt_prt_id' && filterFieldBD != 'tkt_ort_id' && filterFieldBD != 'tkt_aprovado_user_id' && filterFieldBD != 'tkt_arquivado_user_id')">
						<input class="form-control my-control" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="filterFieldBD != 'tkt_pst_id' && filterFieldBD != 'tkt_tav_id' && filterFieldBD != 'tkt_abertura_user_id' && filterFieldBD != 'tkt_stt_id' && filterFieldBD != 'tkt_cgt_id' && filterFieldBD != 'tkt_prt_id' && filterFieldBD != 'tkt_ort_id' && filterFieldBD != 'tkt_aprovado_user_id' && filterFieldBD != 'tkt_arquivado_user_id' && filterFieldBD != 'tkt_abertura_data' && filterFieldBD != 'tkt_data_ini_estim' && filterFieldBD != 'tkt_hora_ini_estim' && filterFieldBD != 'tkt_data_ini_real' && filterFieldBD != 'tkt_hora_ini_real' && filterFieldBD != 'tkt_data_fim_estim' && filterFieldBD != 'tkt_hora_fim_estim' && filterFieldBD != 'tkt_data_fim_real' && filterFieldBD != 'tkt_hora_fim_real' && filterFieldBD != 'tkt_total_hora_estim' && filterFieldBD != 'tkt_total_hora_real' && filterFieldBD != 'tkt_per_concluido' && filterFieldBD != 'tkt_aprovado_data' && filterFieldBD != 'tkt_arquivado_data' && filterFieldBD != 'tkt_incdate' && filterFieldBD != 'tkt_upddate' && filterFieldBD != 'tkt_deldate'">
					</div>
					<div class="col-xs-1" ng-show="cFiltroManual.length < 3">
						<button type="button" class="btn btn-block btn-sm btn-primary" title="Adicionar filtro" ng-click="!filterFieldBD || !sinalfilterBD || addFiltroManual(operatorfilterBD, filterFieldBD, sinalfilterBD, cfilterBD)"
						 ng-disabled="!filterFieldBD || !sinalfilterBD">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
					</div>
					<div class="col-xs-1" ng-show="cFiltroManual.length >= 3">
						<button type="button" class="btn btn-block btn-sm btn-primary" title="Adicionar filtro" ng-click="!filterFieldBD || !sinalfilterBD || !operatorfilterBD || addFiltroManual(operatorfilterBD, filterFieldBD, sinalfilterBD, cfilterBD)"
						 ng-disabled="!filterFieldBD || !sinalfilterBD || !operatorfilterBD">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
					</div>
					<div class="col-xs-1">
						<button type="button" class="btn btn-block btn-sm btn-warning" title="Limpar filtro" ng-click="limpaFiltroManual(true)">&nbsp;<i class="fa fa-eraser"></i>&nbsp;</button>
					</div>
				</div>
				<hr/>

				<div class="row form-group" ng-show="aUserAccess.pta_nivel">
					<h5>{{cFiltroManual}}</h5>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row">
			      	<div class="col-sm-12 right-justify">
			      		<div class="col-sm-4 col-sm-offset-6" style="margin-right: -20px;">
							<a href="" class="btn-block btn btn-xs btn-primary" data-dismiss="modal" ng-click="loadMeusTickets(cFiltroManual)">Aplicar filtro informado</a>
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
