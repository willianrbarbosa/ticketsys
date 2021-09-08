<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();	
?>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Configurações</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/accessprofiles" class="kt-subheader__breadcrumbs-link bold">&nbsp;Perfis de Acesso</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="#/newaccessprofile" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-sm btn-primary" title="Adicionar novo perfil">
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
								<a href="" ng-click="deletePerfilAcesso(aPerfisAcesso)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-trash ft-danger"></i> <span class="kt-nav__link-text">Excluir selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="deletePerfilAcesso(aPerfisAcesso)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-window-restore ft-success"></i> <span class="kt-nav__link-text">Restaurar selecionados</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="!lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadDeletedPerfisAcesso(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-times ft-danger"></i> <span class="kt-nav__link-text">Mostrar Excluídos</span>
								</a>
							</li>
							<li class="kt-nav__item" ng-if="lDeleted && aUserAccess.pta_nivel >= 3">
								<a href="" ng-click="loadPerfisAcesso(cFiltroManual)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-check ft-success"></i> <span class="kt-nav__link-text">Mostrar Ativos</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Cadastro de Perfis de Acesso', 'Configurações', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getHelp('perfis_acesso')" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__head"><strong>Exportar dados:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Prefis de Acesso', 'tbPrefilAcesso', 'EXCEL')">
									<i class="kt-nav__link-icon fa fa-file-excel-o ft-success"></i>
									<span class="kt-nav__link-text">EXCEL</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link" ng-click="exportaDados('Prefis de Acesso', 'tbPrefilAcesso', 'PDF')">
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
				<div class="col-xs-offset-6 col-xs-6">
				    <div class="input-group input-group-info right-justify" ng-show="aUserAccess.pta_nivel">
			        	<input class="form-control my-control" type="text" placeholder="Digite para pesquisar..." ng-keypress="rePage()" ng-model="iptsearch" autofocus>
			        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="(lDeleted ? loadDeletedPerfisAcesso() : loadPerfisAcesso())"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
				    </div>
				</div>
			</div>

			<div class="kt-portlet__body" >	

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>
				
				<div class="alert alert-warning" ng-if="!aPerfisAcesso.length">
					<span id="loading"><div class="loading-img"> Carregando listagem de perfis de acesso. Aguarde...</div></span>
				</div>

				<div ng-if="aPerfisAcesso.length && aUserAccess">
					<div class="table-responsive">
						<table class="table table-striped display nowrap" id="tbPrefilAcesso">
				  			<thead class="thead">
								<tr>
									<th export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="checkall" name="checkall" ng-click="selectAll()" />
											<div class="control__indicator"></div>
										</label>
									</th>
									<th class="text-nowrap" export-no-show><a href="">Ações</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('pf_aid')">Reg.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('pfa_descricao')">Descrição</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('pfa_incdate')">Data inc.</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('pfa_upddate')">Data alt.</a></th>
									<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('pfa_deldate')">Data exc.</a></th>
									<th class="text-nowrap" ng-if="lDeleted"><a href="" ng-click="ordenarPor('pfa_deluser')">Usuário exc.</a></th>
								</tr>
							</thead>			
				  			<tbody>		  				
								<tr ng-animate="'animate'" ng-class="{selecionado: perfilacesso.selecionado, negrito: perfilacesso.selecionado, 'ft-danger': lDeleted}" ng-repeat="perfilacesso in aPerfisAcesso | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
									<td export-no-show>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="squaredFour{{perfilacesso.pfa_id}}" name="check{{perfilacesso.pfa_id}}" ng-model="perfilacesso.selecionado" />
											<div class="control__indicator"></div>
										</label>
									</td>
									<td class="text-nowrap" export-no-show>
										<a href="#/editaccessprofile/{{perfilacesso.pfa_id}}" ng-if="aUserAccess.pta_nivel >= 2 && !lDeleted" class="btn btn-xs btn-link" title="Editar"><i class="fa fa-edit"></i></a>
									</td>
									<td>#{{perfilacesso.pfa_id}}</td>
									<td class="text-nowrap">{{perfilacesso.pfa_descricao}}</td>
									<td class="text-nowrap">{{perfilacesso.pfa_incdate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap">{{perfilacesso.pfa_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{perfilacesso.pfa_deldate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
									<td class="text-nowrap" ng-if="lDeleted">{{perfilacesso.pfa_deluser}}</td>
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

<div id="mdFiltroManual" tabindex='-1' class="modal fade" style="overflow: auto;">				
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
							<option value="pfa_id">Reg.</option>
							<option value="pfa_descricao">Descrição</option>
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
						<input class="form-control my-control" type="text" placeholder="Digite o conteúdo do filtro..." ng-model="cfilterBD">
					</div>
					<div class="col-xs-1" ng-show="cFiltroManual.length < 3">
						<button type="button" class="btn btn-block btn-sm btn-primary" title="Adicionar filtro" ng-click="!filterFieldBD || !sinalfilterBD || addFiltroManual(operatorfilterBD, filterFieldBD, sinalfilterBD, cfilterBD)" ng-disabled="!filterFieldBD || !sinalfilterBD">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
					</div>
					<div class="col-xs-1" ng-show="cFiltroManual.length >= 3">
						<button type="button" class="btn btn-block btn-sm btn-primary" title="Adicionar filtro" ng-click="!filterFieldBD || !sinalfilterBD || !operatorfilterBD || addFiltroManual(operatorfilterBD, filterFieldBD, sinalfilterBD, cfilterBD)" ng-disabled="!filterFieldBD || !sinalfilterBD || !operatorfilterBD">&nbsp;<i class="fa fa-check"></i>&nbsp;</button>
					</div>
					<div class="col-xs-1">
						<button type="button" class="btn btn-block btn-sm btn-warning" title="Limpar filtro" ng-click="cFiltroManual = ''">&nbsp;<i class="fa fa-eraser"></i>&nbsp;</button>
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
							<a href="" class="btn-block btn btn-xs btn-primary" data-dismiss="modal" ng-click="loadPerfisAcesso(cFiltroManual)">Aplicar filtro informado</a>
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