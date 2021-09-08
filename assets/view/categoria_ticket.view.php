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
				<a href="#/categoriaticket" class="kt-subheader__breadcrumbs-link bold">&nbsp;Categorias de Ticket</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="#/novocategoriaticket" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-sm btn-primary" title="Adicionar novo cadastro">
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
								<a href="" ng-click="deletaCategoriaTicket(aCategoriaTicket)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-trash ft-danger"></i> <span class="kt-nav__link-text">Excluir selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="deletaCategoriaTicket(aCategoriaTicket)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-window-restore ft-success"></i> <span class="kt-nav__link-text">Restaurar selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="!lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadCategoriaTicketDeletados(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-times ft-danger"></i> <span class="kt-nav__link-text">Mostrar Excluídos</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadCategoriaTicket(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-check ft-success"></i> <span class="kt-nav__link-text">Mostrar Ativos</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Cadastro de Categoria de Ticket', 'Cadastros', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getAjuda('categoriaticket')" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__head"><strong>Exportar dados:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Categoria de Ticket', 'tbCategoriaTicket', 'EXCEL')">
									<i class="kt-nav__link-icon fa fa-file-excel-o ft-success"></i>
									<span class="kt-nav__link-text">EXCEL</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Categoria de Ticket', 'tbCategoriaTicket', 'PDF')">
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
			        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="(lDeleted ? loadCategoriaTicketDeletados(cFiltroManual) : loadCategoriaTicket(cFiltroManual))"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
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
				
				<div class="alert alert-warning" ng-if="!aCategoriaTicket.length">
					<span id="loading"><div class="loading-img"> Carregando listagem de Categoria de Ticket. Aguarde...</div></span>
				</div>

				<div ng-if="aCategoriaTicket.length && aUserAccess.pta_nivel">
					<div class="table-responsive">
						<table class="table table-striped display nowrap" id="tbCategoriaTicket">
							<thead class="thead">
								<tr>
									<th export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="checkall" name="checkall" ng-click="selectAll()" />
											<div class="control__indicator"></div>
										</label>
									</th>
									<th class="center-justify"><a href="">Ações</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('cgt_id')">Reg.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('cgt_descricao')">Descrição</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('cgt_incdate')">Data inc.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('cgt_upddate')">Data alt.</a></th>
									<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('cgt_deldate')">Data exc.</a></th>
									<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('cgt_deluser')">Usuário exc.</a></th>
								</tr>
							</thead>	
							<tbody>	
								<tr ng-class="{'selecionado negrito': categoriaticket.selecionado, 'text-danger': lDeleted}"  ng-repeat="categoriaticket in aCategoriaTicket | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
									<td export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="squaredFour{{categoriaticket.cgt_id}}" name="check{{categoriaticket.cgt_id}}" ng-model="categoriaticket.selecionado" />
											<div class="control__indicator"></div>
										</label>
									</td>
									<td class="text-nowrap">
										<a href="#/editacategoriaticket/{{categoriaticket.cgt_id}}" ng-if="aUserAccess.pta_nivel >= 2 && !lDeleted" class="btn btn-xs btn-link" title="Editar Categoria de Ticket"><i class="fa fa-edit"></i></a>
									</td>
									<td class="text-nowrap">{{categoriaticket.cgt_id}}</td>
									<td class="text-nowrap">{{categoriaticket.cgt_descricao}}</td>
									<td class="text-nowrap">{{categoriaticket.cgt_incdate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap">{{categoriaticket.cgt_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{categoriaticket.cgt_deldate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{categoriaticket.cgt_deluser | date:'dd/MM/yyyy HH:mm:ss'}}</td>
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
							<option value="cgt_id">Reg.</option>
							<option value="cgt_descricao">Descrição</option>
							<option value="cgt_incdate">Data inc.</option>
							<option value="cgt_upddate">Data alt.</option>
							<option value="cgt_deldate">Data exc.</option>
							<option value="cgt_deluser">Usuário exc..</option>
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
						<input class="form-control my-control iptdata" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="filterFieldBD == 'cgt_incdate' || filterFieldBD == 'cgt_upddate' || filterFieldBD == 'cgt_deldate'">
						<input class="form-control my-control" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD" ng-show="filterFieldBD != 'cgt_incdate' && filterFieldBD != 'cgt_upddate' && filterFieldBD != 'cgt_deldate'">
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
							<a href="" class="btn-block btn btn-xs btn-primary" data-dismiss="modal" ng-click="(lDeleted ? loadCategoriaTicketDeletados(cFiltroManual) : loadCategoriaTicket(cFiltroManual))">Aplicar filtro informado</a>
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
