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
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="#/novoticket" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-sm btn-primary" title="Adicionar novo cadastro">
						<i class="fa fa-plus"></i>
					</a>

					<a href="" class="btn btn-sm  btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações Relacionadas">
						<i class="fa fa-ellipsis-h"></i>
					</a>

					<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
						<!--begin::Nav-->
						<ul class="kt-nav">
							<li class="kt-nav__head"><strong>Ações relacionadas:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item" ng-if="aUserAccess.pta_nivel >= 3">
								<a href="" data-toggle="modal" data-target="#mdFiltroManual" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-filter ft-primary"></i> <span class="kt-nav__link-text">Filtro personalizado</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="!lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="deletaTicket(aTicket)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-trash ft-danger"></i> <span class="kt-nav__link-text">Excluir selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="deletaTicket(aTicket)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-window-restore ft-success"></i> <span class="kt-nav__link-text">Restaurar selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="!lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadTicketDeletados(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-times ft-danger"></i> <span class="kt-nav__link-text">Mostrar Excluídos</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadTicket(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-check ft-success"></i> <span class="kt-nav__link-text">Mostrar Ativos</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Cadastro de Ticket', 'Cadastros', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getAjuda('ticket')" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
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
				<div class="col-xs-offset-6 col-xs-6" ng-show="aUserAccess.pta_nivel">
				    <div class="input-group input-group-info right-justify" ng-show="aUserAccess.pta_nivel">
			        	<input class="form-control my-control" type="text" placeholder="Digite para pesquisar..." ng-model="iptsearch">
			        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="(lDeleted ? loadTicketDeletados(cFiltroManual) : loadTicket(cFiltroManual))"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
				    </div>
				    <span class="ft-danger" ng-if="lTemFiltro" style="float: right;">
				      	*** Há filtros personalizados aplicado na consulta.</span>
				      	<button type="button" ng-if="lTemFiltro" class="btn btn-link btn-xs ft-danger" title="Remover filtro" ng-click="limpaFiltroManual(true)"><i class="fa fa-trash"></i></button>
				    </span>
				</div>
			</div>

			<div class="kt-portlet__body" >

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>
				
				<div class="alert alert-warning" ng-if="!aTicket.length">
					<span id="loading"><div class="loading-img"> Carregando listagem de Ticket. Aguarde...</div></span>
				</div>

				<div ng-if="aTicket.length && aUserAccess.pta_nivel">
					<div class="table-responsive">
						<table class="table table-striped display nowrap" id="tbTicket">
							<thead class="thead">
								<tr>
									<th export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="checkall" name="checkall" ng-click="selectAll()" />
											<div class="control__indicator"></div>
										</label>
									</th>
									<th class="center-justify"><a href="">Ações</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_id')">Nº.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('pst_descricao')">Pasta de Trabalho</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_titulo')">Título</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('tkt_abertura_data')">Data Abertura</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('stt_descricao')">Situação</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('prt_descricao')">Prioridade</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('solic_user_nome')">Solicitante</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('resp_user_nome')">Responsável</a></th>
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
								<tr ng-class="{'selecionado negrito': ticket.selecionado, 'text-danger': lDeleted}"  ng-repeat="ticket in aTicket | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
									<td export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="squaredFour{{ticket.tkt_id}}" name="check{{ticket.tkt_id}}" ng-model="ticket.selecionado" />
											<div class="control__indicator"></div>
										</label>
									</td>
									<td class="text-nowrap">
										<a href="#/detalheticket/{{ticket.tkt_id}}" ng-if="aUserAccess.pta_nivel >= 2 && !lDeleted" class="btn btn-xs btn-link" title="Editar Ticket"><i class="fa fa-edit"></i></a>
									</td>
									<td class="text-nowrap">{{ticket.tkt_id}}</td>
									<td class="text-nowrap">{{ticket.grt_descricao}} > {{ticket.pst_descricao}}</td>
									<td class="text-nowrap">{{ticket.tkt_titulo}}</td>
									<td class="text-nowrap">{{ticket.tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap">{{ticket.stt_descricao}}</td>
									<td class="text-nowrap">
										<div class="kt-ribbon__target ft-white center-justify" style="padding: 3px !important; background-color: {{ticket.prt_cor}} !important;" >
											{{ticket.prt_descricao}}
										</div>
									</td>
									<td class="text-nowrap">{{ticket.solic_user_nome}}</td>
									<td class="text-nowrap">{{ticket.resp_user_nome}}</td>
									<td class="text-nowrap">{{ticket.tkt_aprovado}}</td>
									<td class="text-nowrap">{{ticket.tkt_aprovado_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap">{{ticket.aprov_user_nome}}</td>
									<td class="text-nowrap">{{ticket.tkt_ticket_pai}}</td>
									<td class="text-nowrap">{{ticket.tkt_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{ticket.tkt_deldate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{ticket.tkt_deluser | date:'dd/MM/yyyy HH:mm:ss'}}</td>
								</tr>
							</tbody> 
						</table>
					</div>
					<hr/>
					<div class="row">
						<div class="col-sm-12 center-justify">
							<pagination ng-init="cPg=currentPage+1" ng-model="cPg" total-items="nPgTotal" ng-change="currentPage=cPg-1" max-size="maxSize" boundary-links="true"></pagination>
						</div>
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
							<option value="tkt_per_concluido">% EXR</option>
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
							<a href="" class="btn-block btn btn-xs btn-primary" data-dismiss="modal" ng-click="(lDeleted ? loadTicketDeletados(cFiltroManual) : loadTicket(cFiltroManual))">Aplicar filtro informado</a>
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
