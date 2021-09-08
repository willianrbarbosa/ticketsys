<?php
session_start();
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
				<a href="#/registrationlog" class="kt-subheader__breadcrumbs-link bold">&nbsp;LOG de cadastros</a>
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
								<a href="" ng-click="NewUserFavorite('LOG de cadastros', '9. Configurações', '#'+url)" class="kt-nav__link">
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
				<div class="col-xs-12">
					<div class="row form-group">
						<div class="col-sm-4" >
							<label>Usuário</label>
							<selectize name="filter_user" id="filter_user" config="cfgUser" options="aUsuarios" ng-model="filesFilter.filter_user"></selectize>
						</div>
						<div class="col-sm-2">
							<label>Ação</label>
							<selectize name="filter_action" id="filter_action" config="cfgAction" options="aAction" ng-model="filesFilter.filter_action" ></selectize>
						</div>
						<div class="col-sm-4">
							<label>Pesquisar</label>
							<div class="input-group input-group-info " >
								<input class="form-control my-control" type="text" placeholder="Digite para pesquisar..." ng-keypress="rePage()" ng-model="iptsearch" autofocus>
								<span class="input-group-addon my-control search-addon"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
							</div>
						</div>
						<div class="col-sm-2" style="position:absolute; top:20px; right: 10px; margin:0px; padding:0px;">
							<button type="button" ng-click="loadLogCadastro(filesFilter)" class="btn btn-block btn-sm btn-primary" >Consultar</button>
						</div>
					</div>
				</div>
			</div>

			<div class="kt-portlet__body" >	

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>

				<div class="alert alert-warning" id="div-loading">
					<span id="loading"><div class="loading-img"> </div></span>			
				</div>

				<div class="table-responsive" ng-if="aLog.length && aUserAccess" >
					<table class="table table-striped display nowrap" id="tbCliMoviments">
						<thead class="thead">
							<tr>
								<!-- <th class="text-nowrap"><a href="">Ações</a></th> -->
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('led_user_id')">Reg. Usuário</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_nome')">Usuário</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('user_email')">E-mail</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('led_rot_nome')">Rotina</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('led_key')">Reg.</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('led_action')">Ação</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('led_table')">Tabela</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('led_date_comp')">Data/Hora</a></th>
							</tr>
						</thead>			
						<tbody>		  				
							<tr ng-animate="'animate'" ng-class="{'selecionado negrito':log.selecionado}" ng-repeat="log in aLog | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
							<!-- <td class="text-nowrap">
								<button ng-click="getLogCadastro(log.led_id)" class="btn btn-xs btn-link" title="Editar esse Movimento" ng-if="aUserAccess.pta_nivel >= 2"><i class="fa fa-search-plus"></i></button>
							</td> -->
							<td class="text-nowrap">{{log.led_user_id}}</td>
							<td class="text-nowrap">{{log.user_nome}}</td>
							<td class="text-nowrap">{{log.user_email}}</td>
							<td class="text-nowrap">{{log.led_rot_nome}}</td>
							<td class="text-nowrap">{{log.led_key}}</td>
							<td class="text-nowrap">{{log.led_action}}</td>
							<td class="text-nowrap">{{log.led_table}}</td>
							<td class="text-nowrap">{{log.led_date_comp | date:'dd/MM/yyyy HH:mm:ss'}}</td>
						</tr>						
					</tbody> 
				</table>
				</div>
				<hr/>
				<div class="row">
					<div class="col-sm center-justify">
						<pagination ng-init="cPg=currentPage+1" ng-model="cPg" total-items="nPgTotal" ng-change="currentPage=cPg-1" max-size="maxSize" boundary-links="true"></pagination>
					</div>
				</div> 

			</div>
		</div>
	</div>
</div>
<br>