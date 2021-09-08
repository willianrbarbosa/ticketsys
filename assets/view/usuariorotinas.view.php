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
				<a href="#/userroutines" class="kt-subheader__breadcrumbs-link bold">&nbsp;Rotinas de Usuários</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="#/newcsosn" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-sm btn-primary" title="Adicionar novo cadastro">
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
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Rotinas de Usuários', '9. Configurações', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__head"><strong>Exportar dados:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-file-excel-o ft-success"></i>
									<span class="kt-nav__link-text">EXCEL</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link">
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
			        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="loadUsuarios()"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
				    </div>
				</div>
			</div>

			<div class="kt-portlet__body" >

		<div ng-show="error" class="alert alert-warning">
			{{error}}
		</div>
		
		<div class="alert alert-warning" ng-if="!aUsuarios.length">
			<span id="loading"><div class="loading-img"></div></span>
		</div>

		<div ng-if="aUsuarios.length && aUserAccess">
			<div class="table-responsive">
				<table class="table table-striped display nowrap">
		  			<thead class="thead">
						<tr>
							<th class="text-nowrap"><a href="">Ações</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_id')">Reg.</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_nome')">Nome</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_email')">E-mail</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_tipo')">Tipo</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('cli_razao_social')">Cliente</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_incdate')">Data inc.</a></th>
							<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_upddate')">Data alt.</a></th>
						</tr>
					</thead>			
		  			<tbody>		  				
						<tr ng-animate="'animate'" ng-class="{selecionado: usuario.selecionado, negrito: usuario.selecionado}" ng-repeat="usuario in aUsuarios | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">						
							<td class="text-nowrap">
								<a href="#/edituserroutine/{{usuario.user_token}}" ng-if="aUserAccess.pta_nivel >= 2" class="btn btn-xs btn-link" title="Configurar Rotinas de Acesso"><i class="fa fa-cog"></i></a>
								<a href="#/edituser/{{usuario.user_token}}" ng-if="aUserAccess.pta_nivel >= 2" class="btn btn-xs btn-link" title="Editar os Dados desse usuário"><i class="fa fa-edit"></i></a>
							</td>
							<td>#{{usuario.user_id}}</td>
							<td class="text-nowrap">{{usuario.user_nome}}</td>
							<td class="text-nowrap">{{usuario.user_email}}</td>
							<td class="text-nowrap">{{(usuario.user_tipo == 1 ? 'Administrador' : (usuario.user_tipo == 2 ? 'Normal' : 'Cliente'))}}</td>
							<td class="text-nowrap">{{usuario.cli_razao_social}}</td>
							<td class="text-nowrap">{{usuario.user_incdate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
							<td class="text-nowrap">{{usuario.user_upddate | date:'dd/MM/yyyy HH:mm:ss'}}</td>
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