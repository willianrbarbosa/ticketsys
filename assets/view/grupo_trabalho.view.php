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
				<a href="#/grupotrabalho" class="kt-subheader__breadcrumbs-link bold">&nbsp;Grupos de Trabalho</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="#/novogrupotrabalho" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-sm btn-primary" title="Adicionar novo cadastro">
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
								<a href="" ng-click="deletaGrupoTrabalho(aGrupoTrabalho)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-trash ft-danger"></i> <span class="kt-nav__link-text">Excluir selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="deletaGrupoTrabalho(aGrupoTrabalho)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-window-restore ft-success"></i> <span class="kt-nav__link-text">Restaurar selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="!lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadGrupoTrabalhoDeletados(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-times ft-danger"></i> <span class="kt-nav__link-text">Mostrar Excluídos</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadGrupoTrabalho(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-check ft-success"></i> <span class="kt-nav__link-text">Mostrar Ativos</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Cadastro de Grupo de Trabalho', 'Cadastros', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getAjuda('grupotrabalho')" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__head"><strong>Exportar dados:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Grupo de Trabalho', 'tbGrupoTrabalho', 'EXCEL')">
									<i class="kt-nav__link-icon fa fa-file-excel-o ft-success"></i>
									<span class="kt-nav__link-text">EXCEL</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Grupo de Trabalho', 'tbGrupoTrabalho', 'PDF')">
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
			        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="(lDeleted ? loadGrupoTrabalhoDeletados(cFiltroManual) : loadGrupoTrabalho(cFiltroManual))"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
				    </div>
				    <span class="ft-danger" ng-if="lTemFiltro" style="float: right;">
				    	*** Há filtros personalizados aplicado na consulta.
				    	<button type="button" ng-if="lTemFiltro" class="btn btn-link btn-xs ft-danger" title="Remover filtro" ng-click="limpaFiltroManual(true)"><i class="fa fa-trash"></i></button>
				    </span>
				</div>
			</div>

			<div class="kt-portlet__body" >

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>
				
				<div class="alert alert-warning" ng-if="!aGrupoTrabalho.length">
					<span id="loading"><div class="loading-img"> Carregando listagem de Grupo de Trabalho. Aguarde...</div></span>
				</div>

				<div ng-if="aGrupoTrabalho.length && aUserAccess.pta_nivel">
					<div class="table-responsive">
						<table class="table table-striped display nowrap" id="tbGrupoTrabalho">
							<thead class="thead">
								<tr>
									<th export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="checkall" name="checkall" ng-click="selectAll()" />
											<div class="control__indicator"></div>
										</label>
									</th>
									<th class="center-justify"><a href="">Ações</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('grt_id')">Reg.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('grt_descricao')">Descrição</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('grt_incdate')">Data inc.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('grt_upddate')">Data alt.</a></th>
									<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('grt_deldate')">Data exc.</a></th>
									<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('grt_deluser')">Usuário exc.</a></th>
								</tr>
							</thead>	
							<tbody>	
								<tr ng-class="{'selecionado negrito': grupotrabalho.selecionado, 'text-danger': lDeleted}"  ng-repeat="grupotrabalho in aGrupoTrabalho | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
									<td export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="squaredFour{{grupotrabalho.grt_id}}" name="check{{grupotrabalho.grt_id}}" ng-model="grupotrabalho.selecionado" />
											<div class="control__indicator"></div>
										</label>
									</td>
									<td class="text-nowrap">
										<a href="#/editagrupotrabalho/{{grupotrabalho.grt_id}}" ng-if="aUserAccess.pta_nivel >= 2 && !lDeleted" class="btn btn-xs btn-link" title="Editar Grupo de Trabalho"><i class="fa fa-edit"></i></a>
									</td>
									<td class="text-nowrap">{{grupotrabalho.grt_id}}</td>
									<td class="text-nowrap">{{grupotrabalho.grt_descricao}}</td>
									<td class="text-nowrap">{{grupotrabalho.grt_incdate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap">{{grupotrabalho.grt_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{grupotrabalho.grt_deldate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{grupotrabalho.grt_deluser | date:'dd/MM/yyyy HH:mm:ss'}}</td>
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
							<option value="grt_id">Reg.</option>
							<option value="grt_descricao">Descrição</option>
							<option value="grt_incdate">Data inc.</option>
							<option value="grt_upddate">Data alt.</option>
							<option value="grt_deldate">Data exc.</option>
							<option value="grt_deluser">Usuário exc..</option>
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
						<input class="form-control my-control iptdata" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="filterFieldBD == 'grt_incdate' || filterFieldBD == 'grt_upddate' || filterFieldBD == 'grt_deldate'">
						<input class="form-control my-control" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="filterFieldBD != 'grt_incdate' && filterFieldBD != 'grt_upddate' && filterFieldBD != 'grt_deldate'">
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
							<a href="" class="btn-block btn btn-xs btn-primary" data-dismiss="modal" ng-click="(lDeleted ? loadGrupoTrabalhoDeletados(cFiltroManual) : loadGrupoTrabalho(cFiltroManual))">Aplicar filtro informado</a>
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
