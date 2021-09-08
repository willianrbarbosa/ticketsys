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
				<a href="#/parameters" class="kt-subheader__breadcrumbs-link bold">&nbsp;Parâmetros</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="#/newparameter" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-sm btn-primary" title="Adicionar novo cadastro">
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
								<a href="" ng-click="NewUserFavorite('Parâmetros', '9. Configurações', '#'+url)" class="kt-nav__link">
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
			        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="loadParametros()"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
				    </div>
				</div>
			</div>

			<div class="kt-portlet__body" >

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>
				
				<div class="alert alert-warning" ng-if="!aParametros.length">
					<span id="loading"><div class="loading-img"> Carregando listagem de parâmetros. Aguarde...</div></span>
				</div>

				<div ng-if="aParametros.length && aUserAccess">
					<div class="table-responsive">
						<table class="table table-striped display nowrap">
				  			<thead class="thead">
								<tr>
									<th class="text-nowrap">Ações</th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('pla_id')">Chave</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('par_conteudo')">Conteúdo</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('par_descricao')">Descrição</a></th>
								</tr>
							</thead>			
				  			<tbody>		  				
								<tr ng-class="{selecionado: param.selecionado, negrito: param.selecionado}" ng-repeat="param in aParametros | filter: iptsearch | orderBy: criterioOrdenacao: direcaoOrdenacao">
									<td class="text-nowrap"><a href="#/editparameter/{{param.par_key}}" ng-if="aUserAccess.pta_nivel >= 3" class="btn btn-xs btn-link" title="Clique para editar"><i class="fa fa-edit"></i></a></td>
									<td><strong>{{param.par_key}}</strong></td>
									<td class="text-nowrap">{{param.par_conteudo}}</td>
									<td class="text-nowrap">{{param.par_descricao}}</td>
								</tr>						
				  			</tbody> 
						</table>
					</div>
				</div> 

			</div>
		</div>
	</div>
</div>
<br>